<?php
// controllers/EventoController.php
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/SesionEvento.php';
require_once __DIR__ . '/../models/SerieSesionEvento.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Material.php';
require_once __DIR__ . '/../models/Auditoria.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class EventoController {
    private Evento $eventoModel;
    private SesionEvento $sesionModel;
    private SerieSesionEvento $serieSesionModel;
    private Inscripcion $inscripcionModel;
    private Asistencia $asistenciaModel;
    private Material $materialModel;

    public function __construct() {
        $this->eventoModel = new Evento();
        $this->sesionModel = new SesionEvento();
        $this->serieSesionModel = new SerieSesionEvento();
        $this->inscripcionModel = new Inscripcion();
        $this->asistenciaModel = new Asistencia();
        $this->materialModel = new Material();
    }

    /**
     * RF-07 y RF-08: Muestra el catálogo con filtros de búsqueda
     */
    public function catalogo(): void {
        AuthHelper::initSession();

        $filtros = [
            'buscar'         => $_GET['buscar'] ?? '',
            'id_tipo_evento' => $_GET['id_tipo_evento'] ?? '',
            'id_categoria'   => $_GET['id_categoria'] ?? '',
            'estado'         => $_GET['estado'] ?? '',
            'fecha'          => $_GET['fecha'] ?? ''
        ];

        $eventos = $this->eventoModel->listarPublicos($filtros);

        // Catálogos para los selects del filtro
        $db = Database::getConnection();
        $tipos = $db->query("SELECT * FROM tipos_evento WHERE activo = 1")->fetchAll();
        $categorias = $db->query("SELECT * FROM categorias WHERE activo = 1")->fetchAll();

        require_once __DIR__ . '/../views/publico/catalogo.php';
    }

    /**
     * RF-09: Ficha técnica detallada del evento
     */
    public function detalle(): void {
        AuthHelper::initSession();

        $id_evento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id_evento) {
            header('Location: index.php?action=catalogo');
            exit();
        }

        $evento = $this->eventoModel->obtenerDetalle($id_evento);
        if (!$evento) {
            http_response_code(404);
            die("Evento no encontrado.");
        }

        $sesiones = $this->sesionModel->listarPorEvento($id_evento);
        $materiales = $this->materialModel->listarPorEvento($id_evento);
        $disponibilidad = $this->eventoModel->verificarDisponibilidadInscripcion($id_evento);

        // Verificar si el usuario en sesión ya se encuentra inscrito
        $estaInscrito = false;
        if (AuthHelper::estaAutenticado()) {
            $usuario = AuthHelper::obtenerUsuario();
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_evento = ? AND id_usuario = ? AND estado = 'INSCRITO'");
            $stmt->execute([$id_evento, $usuario['id_usuario']]);
            $estaInscrito = (bool)$stmt->fetch();
        }

        $puedeDescargarMateriales = false;
        if (AuthHelper::estaAutenticado()) {
            $usuario = AuthHelper::obtenerUsuario();
            $puedeDescargarMateriales = strtoupper((string)$usuario['rol_nombre']) === 'ADMINISTRADOR'
                || $this->materialModel->usuarioPuedeDescargar($id_evento, (int)$usuario['id_usuario']);
        }

        require_once __DIR__ . '/../views/publico/detalle_evento.php';
    }

    /**
     * RF-10 y RF-11: Procesa la inscripción del participante
     */
    public function inscribirse(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=catalogo');
            exit();
        }

        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $usuario = AuthHelper::obtenerUsuario();

        if (!$id_evento) {
            $_SESSION['error'] = "Identificador de evento inválido.";
            header('Location: index.php?action=catalogo');
            exit();
        }

        try {
            $idInscripcion = $this->inscripcionModel->registrar($id_evento, (int)$usuario['id_usuario'], 'WEB_PARTICIPANTE');
            $_SESSION['success'] = "¡Inscripción confirmada exitosamente!";
            header("Location: index.php?action=mis_inscripciones");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?action=detalle_evento&id={$id_evento}");
            exit();
        }
    }

    /**
     * RF-12: Procesa la cancelación voluntaria
     */
    public function cancelarInscripcion(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=mis_inscripciones');
            exit();
        }

        $id_inscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo'] ?? 'Cancelado voluntariamente por el usuario');
        $usuario = AuthHelper::obtenerUsuario();

        if ($this->inscripcionModel->cancelar($id_inscripcion, (int)$usuario['id_usuario'], $motivo)) {
            $_SESSION['success'] = "Inscripción cancelada correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo cancelar la inscripción solicitada.";
        }

        header('Location: index.php?action=mis_inscripciones');
        exit();
    }

    /**
     * RF-29: Guardado de nuevo evento (Administrador)
     */
    public function adminGuardarEvento(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        if (!AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el registro del evento. Intente nuevamente.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $admin = AuthHelper::obtenerUsuario();
        $idEvento = null;

        try {
            $datos = [
                'codigo'                        => trim($_POST['codigo']),
                'titulo'                        => trim($_POST['titulo']),
                'descripcion'                  => trim($_POST['descripcion']),
                'id_tipo_evento'                => (int)$_POST['id_tipo_evento'],
                'id_categoria'                  => (int)$_POST['id_categoria'],
                'modalidad'                     => $_POST['modalidad'],
                'lugar'                         => $_POST['lugar'] ?? null,
                'enlace_virtual'                => $_POST['enlace_virtual'] ?? null,
                'cupo_maximo'                   => (int)($_POST['cupo_maximo'] ?? 0),
                'fecha_inicio_inscripcion'      => $_POST['fecha_inicio_inscripcion'],
                'fecha_fin_inscripcion'        => $_POST['fecha_fin_inscripcion'],
                'fecha_inicio'                  => $_POST['fecha_inicio'],
                'fecha_fin'                     => $_POST['fecha_fin'],
                'emite_certificado'             => isset($_POST['emite_certificado']) ? 1 : 0,
                'horas_academicas'              => (int)($_POST['horas_academicas'] ?? 0),
                'porcentaje_asistencia_minimo' => (float)($_POST['porcentaje_asistencia_minimo'] ?? 80.00),
                'nota_minima_aprobacion'        => (float)($_POST['nota_minima_aprobacion'] ?? 0.00),
                'id_usuario_creador'            => (int)$admin['id_usuario']
            ];

            $idEvento = $this->eventoModel->crear($datos);
            $cantidadMateriales = $this->materialModel->subirMultiples(
                $idEvento,
                (int)$admin['id_usuario'],
                $_FILES['materiales'] ?? []
            );

            // Registro de Auditoría (RF-70)
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'CREAR_EVENTO',
                'EVENTOS',
                "Se creó el evento ID #{$idEvento} con código {$datos['codigo']}"
            );

            $_SESSION['success'] = "Evento registrado exitosamente en estado BORRADOR."
                . ($cantidadMateriales ? " Se adjuntaron {$cantidadMateriales} archivo(s)." : '');
            header('Location: index.php?action=admin_eventos');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $idEvento
                ? 'El evento fue registrado, pero no se pudieron adjuntar los archivos: ' . $e->getMessage() . ' Puede agregarlos al editarlo.'
                : 'Error al crear el evento: ' . $e->getMessage();
            header('Location: index.php?action=admin_eventos');
            exit();
        }
    }

    /** Muestra las sesiones cuya autoasistencia está abierta para el participante. */
    public function miAsistencia(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT) ?: null;
        $sesionesAbiertas = $this->asistenciaModel->sesionesAbiertasPorUsuario((int)$usuario['id_usuario'], $idEvento);
        require_once __DIR__ . '/../views/participante/asistencia.php';
    }

    /** Registra la presencia que el propio participante confirma durante la ventana autorizada. */
    public function confirmarAsistencia(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idSesion = filter_input(INPUT_POST, 'id_sesion', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idSesion) {
                throw new InvalidArgumentException('La solicitud de asistencia no es válida.');
            }
            $usuario = AuthHelper::obtenerUsuario();
            $this->asistenciaModel->confirmarParticipante($idSesion, (int)$usuario['id_usuario']);
            $_SESSION['success'] = 'Tu asistencia fue confirmada correctamente.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $destino = 'index.php?action=mi_asistencia';
        if ($idEvento) $destino .= '&id_evento=' . (int)$idEvento;
        header('Location: ' . $destino, true, 303);
        exit();
    }

    /** RF-30: Actualiza un evento desde la ventana de edición administrativa. */
    public function adminActualizarEvento(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        if (!$idEvento || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la edición del evento. Intente nuevamente.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $admin = AuthHelper::obtenerUsuario();
        $eventoActual = $this->eventoModel->obtenerDetalle($idEvento);
        if (!$eventoActual) {
            $_SESSION['error'] = 'El evento que desea editar ya no existe.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $eventoActualizado = false;
        try {
            $modalidad = strtoupper(trim($_POST['modalidad'] ?? ''));
            if (!in_array($modalidad, ['PRESENCIAL', 'VIRTUAL', 'HIBRIDA'], true)) {
                throw new InvalidArgumentException('La modalidad del evento no es válida.');
            }

            $datos = [
                'titulo' => trim($_POST['titulo'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'id_tipo_evento' => (int)($_POST['id_tipo_evento'] ?? 0),
                'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
                'modalidad' => $modalidad,
                'lugar' => trim($_POST['lugar'] ?? ''),
                'enlace_virtual' => trim($_POST['enlace_virtual'] ?? ''),
                'cupo_maximo' => (int)($_POST['cupo_maximo'] ?? -1),
                'fecha_inicio_inscripcion' => str_replace('T', ' ', trim($_POST['fecha_inicio_inscripcion'] ?? '')),
                'fecha_fin_inscripcion' => str_replace('T', ' ', trim($_POST['fecha_fin_inscripcion'] ?? '')),
                'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
                'fecha_fin' => trim($_POST['fecha_fin'] ?? ''),
                'emite_certificado' => isset($_POST['emite_certificado']) ? 1 : 0,
                'horas_academicas' => (int)($_POST['horas_academicas'] ?? 0),
                'porcentaje_asistencia_minimo' => (float)($_POST['porcentaje_asistencia_minimo'] ?? -1),
                'nota_minima_aprobacion' => (float)$eventoActual['nota_minima_aprobacion'],
            ];

            if ($datos['titulo'] === '' || $datos['descripcion'] === '' || $datos['id_tipo_evento'] < 1
                || $datos['id_categoria'] < 1 || $datos['cupo_maximo'] < 0 || $datos['horas_academicas'] < 1
                || $datos['porcentaje_asistencia_minimo'] < 0 || $datos['porcentaje_asistencia_minimo'] > 100
                || !$datos['fecha_inicio_inscripcion'] || !$datos['fecha_fin_inscripcion']
                || !$datos['fecha_inicio'] || !$datos['fecha_fin']) {
                throw new InvalidArgumentException('Revise los campos obligatorios y los valores numéricos.');
            }

            $this->eventoModel->actualizar($idEvento, $datos);
            $eventoActualizado = true;
            $cantidadMateriales = $this->materialModel->subirMultiples(
                $idEvento,
                (int)$admin['id_usuario'],
                $_FILES['materiales'] ?? []
            );
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ACTUALIZAR_EVENTO',
                'EVENTOS',
                "Se actualizó el evento ID #{$idEvento} con código {$eventoActual['codigo']}"
            );
            $_SESSION['success'] = 'Evento actualizado correctamente.'
                . ($cantidadMateriales ? " Se adjuntaron {$cantidadMateriales} archivo(s)." : '');
        } catch (Exception $e) {
            $_SESSION['error'] = $eventoActualizado
                ? 'El evento se actualizó, pero no se pudieron adjuntar los archivos: ' . $e->getMessage() . ' Puede intentarlo nuevamente.'
                : 'No se pudo actualizar el evento: ' . $e->getMessage();
        }

        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /** Asigna o actualiza la función de un expositor activo en un evento. */
    public function adminAsignarExpositor(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la asignación del expositor.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $rolExpositor = mb_substr(trim($_POST['rol_expositor'] ?? 'Expositor Principal'), 0, 100);
        $admin = AuthHelper::obtenerUsuario();

        try {
            if (!$idEvento || !$idUsuario || $rolExpositor === '' || !$this->eventoModel->obtenerDetalle($idEvento)) {
                throw new InvalidArgumentException('Revise el evento, expositor y función seleccionados.');
            }

            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT u.nombres, u.apellidos FROM usuarios u INNER JOIN roles r ON r.id_rol = u.id_rol WHERE u.id_usuario = :usuario AND u.activo = 1 AND r.nombre = 'EXPOSITOR' LIMIT 1");
            $stmt->execute([':usuario' => $idUsuario]);
            $expositor = $stmt->fetch();
            if (!$expositor) {
                throw new InvalidArgumentException('El usuario seleccionado no es un expositor activo.');
            }

            $this->eventoModel->asignarExpositor($idEvento, $idUsuario, $rolExpositor);
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ASIGNAR_EXPOSITOR_EVENTO',
                'EVENTO_EXPOSITORES',
                "Se asignó a {$expositor['nombres']} {$expositor['apellidos']} al evento #{$idEvento} como {$rolExpositor}"
            );
            $_SESSION['success'] = 'Expositor asignado correctamente.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'No se pudo asignar el expositor: ' . $e->getMessage();
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /** Retira la asignación de un expositor de un evento. */
    public function adminDesasignarExpositor(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idEvento || !$idUsuario || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el retiro del expositor.';
        } elseif ($this->eventoModel->desasignarExpositor($idEvento, $idUsuario)) {
            $admin = AuthHelper::obtenerUsuario();
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'DESASIGNAR_EXPOSITOR_EVENTO',
                'EVENTO_EXPOSITORES',
                "Se retiró al usuario #{$idUsuario} del evento #{$idEvento}"
            );
            $_SESSION['success'] = 'Expositor retirado del evento correctamente.';
        } else {
            $_SESSION['error'] = 'No se encontró la asignación solicitada.';
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /** Registra una sesión en el cronograma de un evento. */
    public function adminCrearSesion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el registro de la sesión.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $datos = [
            'id_evento' => $idEvento,
            'titulo' => mb_substr(trim($_POST['titulo'] ?? ''), 0, 150),
            'fecha' => trim($_POST['fecha'] ?? ''),
            'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
            'hora_fin' => trim($_POST['hora_fin'] ?? ''),
            'lugar_especifico' => mb_substr(trim($_POST['lugar_especifico'] ?? ''), 0, 200),
        ];

        try {
            $evento = $idEvento ? $this->eventoModel->obtenerDetalle($idEvento) : null;
            if (!$evento || $datos['titulo'] === ''
                || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos['fecha'])
                || !preg_match('/^\d{2}:\d{2}$/', $datos['hora_inicio'])
                || !preg_match('/^\d{2}:\d{2}$/', $datos['hora_fin'])
                || $datos['hora_inicio'] >= $datos['hora_fin']) {
                throw new InvalidArgumentException('Revise título, fecha y horarios de la sesión.');
            }
            if ($datos['fecha'] < $evento['fecha_inicio'] || $datos['fecha'] > $evento['fecha_fin']) {
                throw new InvalidArgumentException('La fecha de la sesión debe estar dentro del periodo del evento.');
            }

            $idSesion = $this->sesionModel->crear($datos);
            $admin = AuthHelper::obtenerUsuario();
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'CREAR_SESION_EVENTO',
                'SESIONES_EVENTO',
                "Se registró la sesión #{$idSesion} para el evento #{$idEvento}"
            );
            $_SESSION['success'] = 'Sesión agregada al cronograma correctamente.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'No se pudo registrar la sesión: ' . $e->getMessage();
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /** Crea o edita una serie recurrente y genera sus sesiones individuales. */
    public function adminGuardarSerieSesiones(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el registro de la serie.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $idSerie = filter_input(INPUT_POST, 'id_serie', FILTER_VALIDATE_INT);
        try {
            $datos = $this->obtenerDatosSerieSolicitud();
            $evento = $this->eventoModel->obtenerDetalle((int)$datos['id_evento']);
            if (!$evento) throw new InvalidArgumentException('El evento seleccionado no existe.');
            if ($datos['fecha_inicio'] < $evento['fecha_inicio'] || $datos['fecha_fin'] > $evento['fecha_fin']) {
                throw new InvalidArgumentException('El periodo de la serie debe estar dentro de las fechas del evento.');
            }

            $admin = AuthHelper::obtenerUsuario();
            if ($idSerie) {
                $resultado = $this->serieSesionModel->actualizar((int)$idSerie, $datos);
                $accion = 'ACTUALIZAR_SERIE_SESIONES';
                $detalle = "Se actualizó la serie #{$idSerie} y se regeneraron {$resultado['cantidad']} sesiones futuras.";
                $_SESSION['success'] = "Serie actualizada. Se regeneraron {$resultado['cantidad']} sesiones futuras.";
            } else {
                $resultado = $this->serieSesionModel->crear($datos, (int)$admin['id_usuario']);
                $accion = 'CREAR_SERIE_SESIONES';
                $detalle = "Se creó la serie #{$resultado['id_serie']} con {$resultado['cantidad']} sesiones.";
                $_SESSION['success'] = "Serie creada con {$resultado['cantidad']} sesiones programadas.";
            }
            Auditoria::registrar((int)$admin['id_usuario'], $accion, 'SERIES_SESIONES_EVENTO', $detalle);
        } catch (Throwable $e) {
            $_SESSION['error'] = 'No se pudo guardar la serie: ' . $e->getMessage();
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /** Elimina una sesión que no cuente con dependencias que impidan retirarla. */
    public function adminEliminarSesion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idSesion = filter_input(INPUT_POST, 'id_sesion', FILTER_VALIDATE_INT);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$idSesion || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la eliminación de la sesión.';
        } else {
            try {
                $sesion = $this->sesionModel->obtenerPorId($idSesion);
                if (!$sesion) {
                    throw new InvalidArgumentException('La sesión ya no existe.');
                }
                $this->sesionModel->eliminar($idSesion);
                $admin = AuthHelper::obtenerUsuario();
                Auditoria::registrar(
                    (int)$admin['id_usuario'],
                    'ELIMINAR_SESION_EVENTO',
                    'SESIONES_EVENTO',
                    "Se eliminó la sesión #{$idSesion} del evento #{$sesion['id_evento']}"
                );
                $_SESSION['success'] = 'Sesión eliminada correctamente.';
            } catch (Exception $e) {
                $_SESSION['error'] = 'No se pudo eliminar la sesión: ' . $e->getMessage();
            }
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    private function obtenerDatosSerieSolicitud(): array {
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $titulo = mb_substr(trim($_POST['titulo'] ?? ''), 0, 150);
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
        $fechaFin = trim($_POST['fecha_fin'] ?? '');
        $horaInicio = trim($_POST['hora_inicio'] ?? '');
        $horaFin = trim($_POST['hora_fin'] ?? '');
        $frecuencia = strtoupper(trim($_POST['frecuencia'] ?? ''));
        $intervalo = filter_var($_POST['intervalo_recurrencia'] ?? null, FILTER_VALIDATE_INT);
        $diasRecibidos = is_array($_POST['dias_semana'] ?? null) ? $_POST['dias_semana'] : [];
        $dias = array_values(array_unique(array_map('intval', $diasRecibidos)));
        sort($dias);

        if (!$idEvento || $titulo === ''
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio)
            || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)
            || $fechaInicio > $fechaFin
            || !preg_match('/^\d{2}:\d{2}$/', $horaInicio)
            || !preg_match('/^\d{2}:\d{2}$/', $horaFin)
            || $horaInicio >= $horaFin
            || !in_array($frecuencia, ['DIARIA', 'SEMANAL'], true)
            || !$intervalo || $intervalo < 1 || $intervalo > 30
            || array_diff($dias, [1, 2, 3, 4, 5, 6, 7])) {
            throw new InvalidArgumentException('Revise título, periodo, horarios y frecuencia de la serie.');
        }
        if ($frecuencia === 'SEMANAL' && $dias === []) {
            throw new InvalidArgumentException('Seleccione por lo menos un día para la recurrencia semanal.');
        }

        return [
            'id_evento' => (int)$idEvento,
            'titulo' => $titulo,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'lugar_especifico' => mb_substr(trim($_POST['lugar_especifico'] ?? ''), 0, 200),
            'frecuencia' => $frecuencia,
            'intervalo_recurrencia' => (int)$intervalo,
            'dias_semana' => $frecuencia === 'SEMANAL' ? $dias : [],
        ];
    }

    /** Descarga protegida para administración y participantes inscritos al evento. */
    public function descargarMaterial(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR', 'PARTICIPANTE']);
        $idMaterial = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $material = $idMaterial ? $this->materialModel->obtenerPorId($idMaterial) : null;
        $usuario = AuthHelper::obtenerUsuario();
        $esAdmin = strtoupper((string)($usuario['rol_nombre'] ?? '')) === 'ADMINISTRADOR';

        if (!$material || (!$esAdmin && !$this->materialModel->usuarioPuedeDescargar((int)$material['id_evento'], (int)$usuario['id_usuario']))) {
            http_response_code(403);
            exit('No tiene acceso a este material.');
        }

        $ruta = $this->materialModel->rutaFisica($material);
        if (!is_file($ruta)) {
            http_response_code(404);
            exit('El archivo solicitado ya no está disponible.');
        }

        $nombre = preg_replace('/[\\\\\"\\r\\n]/', '_', $material['titulo']) . '.' . strtolower($material['tipo_archivo']);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($ruta));
        header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($nombre));
        header('X-Content-Type-Options: nosniff');
        readfile($ruta);
        exit();
    }

    /** Elimina un archivo previamente adjuntado desde la edición administrativa. */
    public function adminEliminarMaterial(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idMaterial = filter_input(INPUT_POST, 'id_material', FILTER_VALIDATE_INT);
        $material = $idMaterial ? $this->materialModel->obtenerPorId($idMaterial) : null;
        if (!$idMaterial || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la eliminación del archivo.';
        } elseif ($material && $this->materialModel->eliminar($idMaterial)) {
            $admin = AuthHelper::obtenerUsuario();
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ELIMINAR_MATERIAL_EVENTO',
                'MATERIALES_EVENTO',
                "Se eliminó el material #{$idMaterial} del evento #{$material['id_evento']}"
            );
            $_SESSION['success'] = 'Archivo eliminado correctamente.';
        } else {
            $_SESSION['error'] = 'El archivo ya no existe o no pudo eliminarse.';
        }
        header('Location: index.php?action=admin_eventos');
        exit();
    }

    /**
     * RF-31 a RF-34: Transiciones de estado administrativas
     */
    public function adminCambiarEstado(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_evento    = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $nuevo_estado = trim($_POST['estado'] ?? '');
        $admin        = AuthHelper::obtenerUsuario();

        try {
            $this->eventoModel->cambiarEstado($id_evento, $nuevo_estado);

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'CAMBIO_ESTADO_EVENTO',
                'EVENTOS',
                "El evento ID #{$id_evento} cambió su estado a {$nuevo_estado}"
            );

            $_SESSION['success'] = "Estado del evento actualizado a {$nuevo_estado}.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error al actualizar estado: " . $e->getMessage();
        }

        header("Location: index.php?action=admin_eventos");
        exit();
    }


    /**
     * RF-07, RF-08, RF-10: Dashboard principal del participante con eventos abiertos
     */
    public function participanteDashboard(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();
        $db = Database::getConnection();

        // 1. Obtener eventos PUBLICADOS con inscripciones abiertas en fecha y hora actual
        $sqlAbiertos = "SELECT 
                            e.*,
                            te.nombre AS tipo_evento_nombre,
                            c.nombre AS categoria_nombre,
                            (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento AND i.estado = 'INSCRITO') AS total_inscritos
                        FROM eventos e
                        INNER JOIN tipos_evento te ON e.id_tipo_evento = te.id_tipo_evento
                        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
                        WHERE e.estado = 'PUBLICADO'
                          AND NOW() BETWEEN e.fecha_inicio_inscripcion AND e.fecha_fin_inscripcion
                        ORDER BY e.fecha_inicio ASC";
        $stmtAbiertos = $db->query($sqlAbiertos);
        $eventosAbiertos = $stmtAbiertos->fetchAll();

        // 2. Obtener IDs de eventos en los que el usuario ya está registrado
        $stmtMisEvt = $db->prepare("SELECT id_evento FROM inscripciones WHERE id_usuario = :id AND estado != 'CANCELADO'");
        $stmtMisEvt->execute([':id' => $usuario['id_usuario']]);
        $misEventosIds = $stmtMisEvt->fetchAll(PDO::FETCH_COLUMN);

        // 3. Métricas rápidas del usuario para el encabezado
        $stmtTotalIns = $db->prepare("SELECT COUNT(*) FROM inscripciones WHERE id_usuario = :id AND estado = 'INSCRITO'");
        $stmtTotalIns->execute([':id' => $usuario['id_usuario']]);
        $totalMisInscripciones = (int)$stmtTotalIns->fetchColumn();

        $stmtTotalCert = $db->prepare("SELECT COUNT(*) FROM certificados WHERE id_usuario = :id AND estado = 'EMITIDO'");
        $stmtTotalCert->execute([':id' => $usuario['id_usuario']]);
        $totalMisCertificados = (int)$stmtTotalCert->fetchColumn();

        // 4. Catálogos para filtros rápidos
        $tipos = $db->query("SELECT * FROM tipos_evento WHERE activo = 1")->fetchAll();

        require_once __DIR__ . '/../views/participante/dashboard.php';
    }
}
