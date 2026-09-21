<?php
// controllers/ExpositorController.php
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/SesionEvento.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Auditoria.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class ExpositorController {
    private Evento $eventoModel;
    private SesionEvento $sesionModel;
    private Inscripcion $inscripcionModel;
    private Asistencia $asistenciaModel;

    public function __construct() {
        $this->eventoModel = new Evento();
        $this->sesionModel = new SesionEvento();
        $this->inscripcionModel = new Inscripcion();
        $this->asistenciaModel = new Asistencia();
    }

    /**
     * RF-20 y RF-26: Lista de eventos asignados al expositor en sesión
     */
    public function misEventos(): void {
        AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();

        $eventos = $this->eventoModel->listarPorExpositor((int)$usuario['id_usuario']);

        // Vista de eventos asignados
        require_once __DIR__ . '/../views/expositor/mis_eventos.php';
    }

    /**
     * RF-21: Consulta de sesiones programadas para un evento asignado
     */
    public function sesiones(): void {
        AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();

        $id_evento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        if (!$id_evento) {
            header('Location: index.php?action=expositor_eventos');
            exit();
        }

        $evento = $this->eventoModel->obtenerDetalle($id_evento);
        if (!$evento) {
            die("Evento no encontrado.");
        }

        // Seguridad: Verificar que el evento realmente esté asignado al expositor (si no es admin)
        if ($usuario['rol_nombre'] === 'EXPOSITOR') {
            $expositores = array_column($evento['expositores'], 'id_usuario');
            if (!in_array((int)$usuario['id_usuario'], $expositores, true)) {
                http_response_code(403);
                die("No tiene permisos para gestionar este evento.");
            }
        }

        $sesiones = $this->sesionModel->listarPorEvento($id_evento);
        require_once __DIR__ . '/../views/expositor/sesiones.php';
    }

    /**
     * RF-22, RF-23 y RF-24: Vista de toma de lista para una sesión específica
     */
    public function planillaAsistencia(): void {
        AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();
        $eventos = $this->eventosAutorizados($usuario);
        if (empty($eventos)) {
            $idEvento = 0;
            $evento = null;
            $sesiones = [];
            $sesion = null;
            $participantes = [];
            require_once __DIR__ . '/../views/expositor/asistencia.php';
            return;
        }
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        if (!$idEvento && !empty($eventos)) {
            $idEvento = (int)$eventos[0]['id_evento'];
        }

        $idsEventos = array_map(static fn($evento) => (int)$evento['id_evento'], $eventos);
        if (!$idEvento || !in_array($idEvento, $idsEventos, true)) {
            http_response_code(403);
            die('No tiene un evento asignado para registrar asistencia.');
        }

        $evento = $this->eventoModel->obtenerDetalle($idEvento);
        $sesiones = $this->sesionModel->listarPorEvento($idEvento);
        $idSesion = filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT);
        if (!$idSesion && !empty($sesiones)) {
            $idSesion = (int)$sesiones[0]['id_sesion'];
        }

        $sesion = $idSesion ? $this->sesionModel->obtenerPorId($idSesion) : null;
        if ($sesion && (int)$sesion['id_evento'] !== $idEvento) {
            http_response_code(403);
            die('La sesión no pertenece al evento seleccionado.');
        }

        $participantes = $sesion ? $this->asistenciaModel->listarPorSesion($idSesion, $idEvento) : [];
        require_once __DIR__ . '/../views/expositor/asistencia.php';
    }

    /**
     * RF-23, RF-24, RF-25 y RF-51: Endpoint API para registrar/corregir asistencia con Fetch API
     * Responde exclusivamente en formato JSON
     */
    public function guardarAsistenciaApi(): void {
        AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'mensaje' => 'Método no permitido.']);
            exit();
        }

        $usuario = AuthHelper::obtenerUsuario();

        // Soporte tanto para application/json como para FormData estándar
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $id_sesion      = filter_var($input['id_sesion'] ?? null, FILTER_VALIDATE_INT);
        $id_inscripcion = filter_var($input['id_inscripcion'] ?? null, FILTER_VALIDATE_INT);
        $estado         = strtoupper(trim($input['estado'] ?? ''));
        $observacion    = !empty($input['observacion']) ? trim($input['observacion']) : null;

        if (!AuthHelper::validarCsrf($input['csrf_token'] ?? null)) {
            http_response_code(419);
            echo json_encode(['success' => false, 'mensaje' => 'La sesión de seguridad expiró. Recargue la página.']);
            exit();
        }

        if (!$id_sesion || !$id_inscripcion || empty($estado)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'mensaje' => 'Parámetros obligatorios incompletos.']);
            exit();
        }

        try {
            // RF-25: Verificar que la sesión no esté concluida o cancelada
            $sesion = $this->sesionModel->obtenerPorId($id_sesion);
            if (!$sesion || in_array($sesion['estado'], ['CANCELADA'], true)) {
                throw new Exception("No se puede registrar asistencia en una sesión cancelada.");
            }

            $idsPermitidos = array_column($this->eventosAutorizados($usuario), 'id_evento');
            if (!in_array((int)$sesion['id_evento'], array_map('intval', $idsPermitidos), true)) {
                throw new Exception('No tiene autorización para registrar asistencia en esta sesión.');
            }

            $db = Database::getConnection();
            $stmtInscripcion = $db->prepare('SELECT 1 FROM inscripciones WHERE id_inscripcion = :inscripcion AND id_evento = :evento AND estado = \'INSCRITO\' LIMIT 1');
            $stmtInscripcion->execute([':inscripcion' => $id_inscripcion, ':evento' => $sesion['id_evento']]);
            if (!$stmtInscripcion->fetchColumn()) {
                throw new Exception('El participante no pertenece al evento de esta sesión.');
            }

            // Registrar y recalcular en la base de datos
            $guardado = $this->asistenciaModel->registrarOActualizar(
                $id_sesion,
                $id_inscripcion,
                $estado,
                (int)$usuario['id_usuario'],
                $observacion
            );

            if ($guardado) {
                // Obtener datos recalculados para actualizar el front-end en caliente
                $stmt = $db->prepare("SELECT porcentaje_asistencia, habilitado_certificado FROM inscripciones WHERE id_inscripcion = ?");
                $stmt->execute([$id_inscripcion]);
                $progreso = $stmt->fetch();

                echo json_encode([
                    'success'               => true,
                    'mensaje'               => 'Asistencia actualizada correctamente.',
                    'id_inscripcion'        => $id_inscripcion,
                    'estado'                => $estado,
                    'porcentaje_asistencia' => (float)$progreso['porcentaje_asistencia'],
                    'habilitado'            => (bool)$progreso['habilitado_certificado']
                ]);
            } else {
                throw new Exception("No se pudo guardar el registro de asistencia.");
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
        }
        exit();
    }

    /** Exporta el informe de asistencia del evento seleccionado a CSV. */
    public function exportarAsistencia(): void {
        AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $idsPermitidos = array_map('intval', array_column($this->eventosAutorizados($usuario), 'id_evento'));
        if (!$idEvento || !in_array($idEvento, $idsPermitidos, true)) {
            http_response_code(403);
            exit('No tiene permiso para exportar este informe.');
        }

        $evento = $this->eventoModel->obtenerDetalle($idEvento);
        $archivo = 'asistencia_' . preg_replace('/[^a-z0-9_-]/i', '_', $evento['codigo'] ?? (string)$idEvento) . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        echo "\xEF\xBB\xBF";
        $salida = fopen('php://output', 'w');
        fputcsv($salida, ['Sesión', 'Fecha', 'Horario', 'CI', 'Participante', 'Correo', 'Estado', 'Observación', '% acumulado']);
        foreach ($this->asistenciaModel->listarInformePorEvento($idEvento) as $fila) {
            fputcsv($salida, [
                $fila['sesion_titulo'], $fila['fecha'], substr($fila['hora_inicio'], 0, 5) . ' - ' . substr($fila['hora_fin'], 0, 5),
                $fila['ci'], trim($fila['nombres'] . ' ' . $fila['apellidos']), $fila['correo'],
                $fila['estado_asistencia'], $fila['observacion'], $fila['porcentaje_asistencia'] . '%'
            ]);
        }
        fclose($salida);
        exit();
    }

    private function eventosAutorizados(array $usuario): array {
        if (strtoupper((string)$usuario['rol_nombre']) === 'ADMINISTRADOR') {
            $db = Database::getConnection();
            return $db->query('SELECT id_evento, codigo, titulo FROM eventos ORDER BY fecha_inicio DESC')->fetchAll();
        }
        return $this->eventoModel->listarPorExpositor((int)$usuario['id_usuario']);
    }
}
