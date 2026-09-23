<?php
// controllers/CertificadoController.php
require_once __DIR__ . '/../models/Certificado.php';
require_once __DIR__ . '/../models/SolicitudReimpresion.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Auditoria.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/QrHelper.php';
require_once __DIR__ . '/../models/PlantillaCertificado.php';
require_once __DIR__ . '/../helpers/CertificadoPdfService.php';

class CertificadoController {
    private Certificado $certificadoModel;
    private SolicitudReimpresion $reimpresionModel;
    private Inscripcion $inscripcionModel;
    private Evento $eventoModel;

    public function __construct() {
        $this->certificadoModel = new Certificado();
        $this->reimpresionModel = new SolicitudReimpresion();
        $this->inscripcionModel = new Inscripcion();
        $this->eventoModel = new Evento();
    }

    /** Administración de plantilla, participantes habilitados y certificados por evento. */
    public function adminGestion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $db = Database::getConnection();
        $eventos = $db->query("SELECT id_evento, codigo, titulo, emite_certificado FROM eventos WHERE estado <> 'CANCELADO' ORDER BY fecha_inicio DESC, titulo ASC")->fetchAll();
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT) ?: (int)($eventos[0]['id_evento'] ?? 0);
        $evento = null;
        foreach ($eventos as $fila) if ((int)$fila['id_evento'] === $idEvento) { $evento = $fila; break; }
        if (!$evento) { $idEvento = 0; }
        $plantillaModel = new PlantillaCertificado();
        $plantilla = $idEvento ? $plantillaModel->obtenerPorEvento($idEvento) : null;
        try { $lienzo = $plantilla ? $plantillaModel->dimensionesPagina($plantilla) : ['ancho'=>297.0, 'alto'=>210.0]; }
        catch (Throwable $e) { $lienzo = ['ancho'=>297.0, 'alto'=>210.0]; }
        $habilitados = $idEvento ? $this->certificadoModel->listarHabilitadosPorEvento($idEvento) : [];
        $emitidos = $idEvento ? $this->certificadoModel->listarPorEvento($idEvento) : [];
        require_once __DIR__ . '/../views/admin/certificados/index.php';
    }

    public function adminSubirPlantilla(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento) throw new Exception('No se pudo validar la plantilla.');
            $pagina = max(1, (int)($_POST['pagina'] ?? 1));
            (new PlantillaCertificado())->guardar($idEvento, (int)AuthHelper::obtenerUsuario()['id_usuario'], $_FILES['plantilla'] ?? [], $pagina, $this->configuracionCampos($_POST));
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', "Plantilla configurada para el evento #{$idEvento}");
            $_SESSION['success'] = 'Plantilla PDF guardada. Puede emitir certificados para los participantes habilitados.';
        } catch (Exception $e) { $_SESSION['error'] = $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento); exit();
    }

    /** Guarda posiciones configuradas visualmente sobre la plantilla. */
    public function adminGuardarDiseno(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento) throw new Exception('No se pudo validar el diseño.');
            $campos = json_decode($_POST['configuracion_campos'] ?? '', true, 512, JSON_THROW_ON_ERROR);
            $lienzo = $campos['_lienzo'] ?? [];
            $anchoLienzo = max(50, min(500, (float)($lienzo['ancho'] ?? 297)));
            $altoLienzo = max(50, min(500, (float)($lienzo['alto'] ?? 210)));
            $permitidos = ['nombre','ci','evento','horas','fechas','codigo','validacion','qr'];
            $resultado = [];
            foreach ($permitidos as $clave) {
                if (!isset($campos[$clave]) || !is_array($campos[$clave])) continue;
                $campo = $campos[$clave];
                $resultado[$clave] = [
                    'activo' => !empty($campo['activo']),
                    'x' => max(0, min($anchoLienzo, (float)($campo['x'] ?? 0))),
                    'y' => max(0, min($altoLienzo, (float)($campo['y'] ?? 0))),
                    'ancho' => max(1, min($anchoLienzo, (float)($campo['ancho'] ?? 80))),
                    'tamano' => max(6, min(36, (float)($campo['tamano'] ?? 10))),
                    'alineacion' => in_array($campo['alineacion'] ?? 'C', ['L','C','R'], true) ? $campo['alineacion'] : 'C',
                    'negrita' => !empty($campo['negrita']),
                    'ancla' => 'centro',
                ];
            }
            if ($resultado === []) throw new Exception('El diseño no contiene campos válidos.');
            $resultado['_lienzo'] = [
                'ancho' => $anchoLienzo,
                'alto' => $altoLienzo,
            ];
            (new PlantillaCertificado())->actualizarConfiguracion($idEvento, $resultado);
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', "Posiciones actualizadas para el evento #{$idEvento}");
            $_SESSION['success'] = 'Posiciones del certificado guardadas correctamente.';
        } catch (Throwable $e) { $_SESSION['error'] = 'No se pudo guardar el diseño: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento); exit();
    }

    /** Sirve la plantilla únicamente al administrador para el editor visual. */
    public function verPlantilla(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $plantilla = $idEvento ? (new PlantillaCertificado())->obtenerPorEvento($idEvento) : null;
        $ruta = $plantilla ? (new PlantillaCertificado())->rutaFisica($plantilla) : '';
        if (!is_file($ruta)) { http_response_code(404); exit('Plantilla no disponible.'); }
        header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="plantilla-certificado.pdf"'); header('Content-Length: ' . filesize($ruta)); readfile($ruta); exit();
    }

    public function adminEmitirPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idInscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idInscripcion) throw new Exception('Solicitud de emisión no válida.');
            $certificado = $this->emitirDocumento($idInscripcion, (int)AuthHelper::obtenerUsuario()['id_usuario']);
            $_SESSION['success'] = 'Certificado emitido: ' . $certificado['codigo_unico'];
        } catch (Exception $e) { $_SESSION['error'] = 'No se pudo emitir el certificado: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento); exit();
    }

    /** Regenera un documento ya emitido usando el diseño actual de su evento. */
    public function adminRegenerarPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idCertificado = filter_input(INPUT_POST, 'id_certificado', FILTER_VALIDATE_INT);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idCertificado) throw new Exception('Solicitud no válida.');
            $certificado = $this->certificadoModel->obtenerPorId($idCertificado);
            if (!$certificado || $certificado['estado'] !== 'EMITIDO') throw new Exception('El certificado no está disponible para regeneración.');
            $plantilla = (new PlantillaCertificado())->obtenerPorEvento((int)$certificado['id_evento']);
            if (!$plantilla) throw new Exception('El evento no tiene una plantilla activa.');
            $certificado['codigo_qr'] = $certificado['codigo_qr'] ?: QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
            $ruta = (new CertificadoPdfService())->generar($plantilla, $certificado);
            $this->certificadoModel->actualizarDocumento($idCertificado, $ruta, $certificado['codigo_qr']);
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', "Certificado {$certificado['codigo_unico']} regenerado con el diseño actual.");
            $_SESSION['success'] = 'PDF regenerado con las posiciones actuales.';
        } catch (Throwable $e) { $_SESSION['error'] = 'No se pudo regenerar el PDF: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento); exit();
    }

    public function descargarPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR', 'PARTICIPANTE']);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $certificado = $id ? $this->certificadoModel->obtenerPorId($id) : null;
        $usuario = AuthHelper::obtenerUsuario();
        if (!$certificado || $certificado['estado'] !== 'EMITIDO' || ($usuario['rol_nombre'] !== 'ADMINISTRADOR' && (int)$certificado['id_usuario'] !== (int)$usuario['id_usuario'])) { http_response_code(404); exit('Certificado no disponible.'); }
        $ruta = __DIR__ . '/../storage/certificados/' . basename((string)$certificado['ruta_archivo_pdf']);
        if (!is_file($ruta)) { header('Location: index.php?action=imprimir_certificado&codigo=' . urlencode($certificado['codigo_unico'])); exit(); }
        header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="' . basename($certificado['codigo_unico']) . '.pdf"'); header('Content-Length: ' . filesize($ruta)); readfile($ruta); exit();
    }

    private function emitirDocumento(int $idInscripcion, int $idAdmin): array {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT id_evento FROM inscripciones WHERE id_inscripcion=:id'); $stmt->execute([':id'=>$idInscripcion]);
        $idEvento = (int)$stmt->fetchColumn();
        $plantilla = $idEvento ? (new PlantillaCertificado())->obtenerPorEvento($idEvento) : null;
        if (!$plantilla) throw new Exception('El evento no cuenta con una plantilla PDF activa.');
        $idCertificado = $this->certificadoModel->generarIndividual($idInscripcion, $idAdmin);
        try {
            $certificado = $this->certificadoModel->obtenerPorId($idCertificado);
            $certificado['codigo_qr'] = QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
            $ruta = (new CertificadoPdfService())->generar($plantilla, $certificado);
            $this->certificadoModel->actualizarDocumento($idCertificado, $ruta, $certificado['codigo_qr']);
            Auditoria::registrar($idAdmin, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', "Certificado {$certificado['codigo_unico']} emitido para la inscripción #{$idInscripcion}");
            return $certificado;
        } catch (Throwable $e) { $this->certificadoModel->eliminarRecienEmitido($idCertificado); throw $e; }
    }

    private function configuracionCampos(array $entrada): array {
        $defecto = [
            'nombre'=>['activo'=>true,'x'=>148.5,'y'=>82,'ancho'=>257,'tamano'=>22,'alineacion'=>'C','negrita'=>true],
            'ci'=>['activo'=>true,'x'=>148.5,'y'=>98,'ancho'=>257,'tamano'=>11,'alineacion'=>'C','negrita'=>false],
            'evento'=>['activo'=>true,'x'=>148.5,'y'=>116,'ancho'=>247,'tamano'=>15,'alineacion'=>'C','negrita'=>true],
            'horas'=>['activo'=>true,'x'=>148.5,'y'=>132,'ancho'=>257,'tamano'=>11,'alineacion'=>'C','negrita'=>false],
            'fechas'=>['activo'=>true,'x'=>148.5,'y'=>140,'ancho'=>257,'tamano'=>10,'alineacion'=>'C','negrita'=>false],
            'codigo'=>['activo'=>true,'x'=>18,'y'=>190,'ancho'=>100,'tamano'=>8,'alineacion'=>'L','negrita'=>false],
            'validacion'=>['activo'=>false,'x'=>18,'y'=>196,'ancho'=>170,'tamano'=>6,'alineacion'=>'L','negrita'=>false],
            'qr'=>['activo'=>true,'x'=>256,'y'=>180,'tamano'=>28],
        ];
        foreach ($defecto as $clave => &$campo) foreach (['x','y','ancho','tamano'] as $propiedad) if (isset($entrada[$clave . '_' . $propiedad]) && is_numeric($entrada[$clave . '_' . $propiedad])) $campo[$propiedad] = (float)$entrada[$clave . '_' . $propiedad];
        return $defecto;
    }

    /**
     * RF-15 y RF-16: Panel del participante para ver sus certificados obtenidos
     */
    public function misCertificados(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();

        $certificados = $this->certificadoModel->listarPorUsuario((int)$usuario['id_usuario']);
        $solicitudes = $this->reimpresionModel->listarPorUsuario((int)$usuario['id_usuario']);

        require_once __DIR__ . '/../views/participante/mis_certificados.php';
    }

    /**
     * RF-17 y RF-18: El participante solicita la reimpresión indicando motivo
     */
    public function procesarSolicitudReimpresion(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=mis_certificados');
            exit();
        }

        $usuario = AuthHelper::obtenerUsuario();
        $id_certificado = filter_input(INPUT_POST, 'id_certificado', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo'] ?? '');

        if (!$id_certificado || empty($motivo)) {
            $_SESSION['error'] = "Debe indicar un motivo justificado para la reimpresión.";
            header('Location: index.php?action=mis_certificados');
            exit();
        }

        try {
            $this->reimpresionModel->registrarSolicitud($id_certificado, (int)$usuario['id_usuario'], $motivo);
            $_SESSION['success'] = "Solicitud de reimpresión enviada a revisión administrativa.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: index.php?action=mis_certificados');
        exit();
    }

    /**
     * RF-52 y RF-53: Emisión individual por parte del Administrador
     */
    public function adminEmitirIndividual(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_inscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $admin = AuthHelper::obtenerUsuario();

        try {
            $idCert = $this->certificadoModel->generarIndividual($id_inscripcion, (int)$admin['id_usuario']);

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'EMITIR_CERTIFICADO',
                'CERTIFICADOS',
                "Se generó el certificado ID #{$idCert} para la inscripción #{$id_inscripcion}"
            );

            $_SESSION['success'] = "Certificado emitido exitosamente.";
        } catch (Exception $e) {
            $_SESSION['error'] = "No se pudo emitir el certificado: " . $e->getMessage();
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?action=admin_eventos';
        header("Location: {$referer}");
        exit();
    }

    /**
     * RF-55: Emisión masiva para todos los alumnos aprobados del evento
     */
    public function adminEmitirMasivo(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $admin = AuthHelper::obtenerUsuario();

        try {
            $resultado = $this->certificadoModel->generarMasivo($id_evento, (int)$admin['id_usuario']);

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'EMISION_MASIVA_CERTIFICADOS',
                'CERTIFICADOS',
                "Emisión masiva en evento #{$id_evento}: {$resultado['total_emitidos']} emitidos de {$resultado['total_procesados']} procesados."
            );

            $_SESSION['success'] = "Proceso concluido: {$resultado['total_emitidos']} certificado(s) generado(s).";
        } catch (Exception $e) {
            $_SESSION['error'] = "Fallo en la emisión masiva: " . $e->getMessage();
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?action=admin_eventos';
        header("Location: {$referer}");
        exit();
    }

    /**
     * RF-59: Anulación administrativa de un certificado
     */
    public function adminAnular(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_certificados');
            exit();
        }

        $id_certificado = filter_input(INPUT_POST, 'id_certificado', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo_anulacion'] ?? '');
        $admin = AuthHelper::obtenerUsuario();

        if (!$id_certificado || empty($motivo)) {
            $_SESSION['error'] = "Debe indicar el motivo formal de la anulación.";
            header('Location: index.php?action=admin_certificados');
            exit();
        }

        if ($this->certificadoModel->anular($id_certificado, (int)$admin['id_usuario'], $motivo)) {
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ANULAR_CERTIFICADO',
                'CERTIFICADOS',
                "Certificado ID #{$id_certificado} anulado. Motivo: {$motivo}"
            );
            $_SESSION['success'] = "El certificado fue marcado como ANULADO en el historial.";
        } else {
            $_SESSION['error'] = "No fue posible anular el certificado seleccionado.";
        }

        header('Location: index.php?action=admin_certificados');
        exit();
    }

    /**
     * RF-57, RF-58 y RF-64: Vista formal imprimible / guardado en PDF
     */
    public function imprimir(): void {
        $codigo = trim($_GET['codigo'] ?? '');
        if (empty($codigo)) {
            die("Código de certificado no proporcionado.");
        }

        $certificado = $this->certificadoModel->obtenerPorCodigo($codigo);
        if (!$certificado) {
            http_response_code(404);
            die("El certificado solicitado no existe en el sistema.");
        }

        // Obtener configuración institucional
        $db = Database::getConnection();
        $config = $db->query("SELECT * FROM configuracion_institucion WHERE id_configuracion = 1 LIMIT 1")->fetch();

        $urlValidacion = QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
        $qrImagen = QrHelper::generarImagenQr($urlValidacion, 160);

        // Renderizado del formato diploma apaisado
        require_once __DIR__ . '/../views/publico/imprimir_certificado.php';
    }

    /**
     * RF-16 y Validación Pública por QR (Acceso público sin sesión)
     */
    public function verificarPublico(): void {
        $codigo = trim($_GET['codigo'] ?? '');
        $certificado = null;

        if (!empty($codigo)) {
            $certificado = $this->certificadoModel->obtenerPorCodigo($codigo);
        }

        require_once __DIR__ . '/../views/publico/verificar.php';
    }

    /**
     * RF-60, RF-61, RF-62 y RF-63: Resolver solicitudes de reimpresión (Admin)
     */
    public function adminResolverReimpresion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_reimpresiones');
            exit();
        }

        $id_solicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $decision = trim($_POST['decision'] ?? ''); // 'APROBADA' o 'RECHAZADA'
        $motivo_rechazo = trim($_POST['motivo_rechazo'] ?? '');
        $admin = AuthHelper::obtenerUsuario();

        try {
            $this->reimpresionModel->resolver($id_solicitud, (int)$admin['id_usuario'], $decision, $motivo_rechazo);

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'RESOLVER_REIMPRESION',
                'CERTIFICADOS',
                "Solicitud #{$id_solicitud} resuelta como {$decision}."
            );

            $_SESSION['success'] = "Solicitud marcada como {$decision}.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al resolver solicitud: " . $e->getMessage();
        }

        header('Location: index.php?action=admin_reimpresiones');
        exit();
    }

    /**
     * RF-66: Registrar la entrega física del certificado reimpreso (Admin)
     */
    public function adminEntregarReimpresion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_reimpresiones');
            exit();
        }

        $id_solicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $admin = AuthHelper::obtenerUsuario();

        if ($this->reimpresionModel->registrarEntrega($id_solicitud, (int)$admin['id_usuario'])) {
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ENTREGA_REIMPRESION',
                'CERTIFICADOS',
                "Se registró la entrega material de la reimpresión #{$id_solicitud}."
            );
            $_SESSION['success'] = "Acta de entrega registrada con éxito.";
        } else {
            $_SESSION['error'] = "No se pudo asentar la entrega del documento.";
        }

        header('Location: index.php?action=admin_reimpresiones');
        exit();
    }
}
