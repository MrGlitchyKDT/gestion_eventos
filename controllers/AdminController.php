<?php
// controllers/AdminController.php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Certificado.php';
require_once __DIR__ . '/../models/SolicitudReimpresion.php';
require_once __DIR__ . '/../models/SolicitudRecuperacionPassword.php';
require_once __DIR__ . '/../models/Auditoria.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class AdminController {
    private PDO $db;
    private Usuario $usuarioModel;
    private Evento $eventoModel;
    private Inscripcion $inscripcionModel;
    private Asistencia $asistenciaModel;
    private Certificado $certificadoModel;
    private SolicitudReimpresion $reimpresionModel;
    private SolicitudRecuperacionPassword $solicitudRecuperacionModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->usuarioModel = new Usuario();
        $this->eventoModel = new Evento();
        $this->inscripcionModel = new Inscripcion();
        $this->asistenciaModel = new Asistencia();
        $this->certificadoModel = new Certificado();
        $this->reimpresionModel = new SolicitudReimpresion();
        $this->solicitudRecuperacionModel = new SolicitudRecuperacionPassword();
    }

    /** Inicio administrativo con acceso a los módulos de gestión. */
    public function dashboard(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $reimpresionesPendientes = $this->reimpresionModel->conteoPorEstado()['PENDIENTE'] ?? 0;
        $recuperacionesPendientes = $this->solicitudRecuperacionModel->pendientes();
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /** Buzón administrativo de solicitudes públicas de recuperación de contraseña. */
    public function solicitudesRecuperacion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $estado = strtoupper(trim((string)($_GET['estado'] ?? '')));
        if (!in_array($estado, ['', 'PENDIENTE', 'APROBADA', 'RECHAZADA'], true)) $estado = '';
        $solicitudesRecuperacion = $this->solicitudRecuperacionModel->listar($estado);
        $recuperacionesPendientes = $this->solicitudRecuperacionModel->pendientes();
        $enlaceRecuperacionBuzon = $_SESSION['enlace_recuperacion_buzon'] ?? null;
        unset($_SESSION['enlace_recuperacion_buzon']);
        require_once __DIR__ . '/../views/admin/recuperaciones/index.php';
    }

    /** Aprueba una petición y genera un enlace de un solo uso, o la rechaza con un motivo. */
    public function resolverSolicitudRecuperacion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'La solicitud de recuperación no es válida.';
            header('Location: index.php?action=admin_solicitudes_recuperacion', true, 303);
            exit();
        }

        $idSolicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $decision = strtoupper(trim((string)($_POST['decision'] ?? '')));
        $motivo = trim((string)($_POST['motivo_rechazo'] ?? ''));
        $admin = AuthHelper::obtenerUsuario();

        try {
            if (!$idSolicitud || !in_array($decision, ['APROBADA', 'RECHAZADA'], true)) throw new RuntimeException('Decisión inválida.');
            if ($decision === 'RECHAZADA' && $motivo === '') throw new RuntimeException('Indique el motivo del rechazo.');
            if (mb_strlen($motivo) > 1000) throw new RuntimeException('El motivo de rechazo no puede superar 1.000 caracteres.');
            $solicitud = $this->solicitudRecuperacionModel->obtenerPendiente($idSolicitud);
            if (!$solicitud || !(int)$solicitud['activo']) throw new RuntimeException('La solicitud ya fue atendida o la cuenta no está activa.');

            if ($decision === 'APROBADA') {
                $recuperacion = $this->usuarioModel->crearTokenRecuperacion((int)$solicitud['id_usuario']);
                if (!$recuperacion) throw new RuntimeException('No fue posible crear el enlace temporal para esta cuenta.');
                if (!$this->solicitudRecuperacionModel->resolver($idSolicitud, (int)$admin['id_usuario'], 'APROBADA')) throw new RuntimeException('La solicitud ya fue atendida.');
                $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
                $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
                $_SESSION['enlace_recuperacion_buzon'] = [
                    'nombre' => trim($solicitud['nombres'] . ' ' . $solicitud['apellidos']),
                    'correo' => $solicitud['correo'],
                    'url' => $protocolo . '://' . $host . $baseUrl . '/index.php?action=reestablecer&token=' . rawurlencode($recuperacion['token']),
                    'expiracion' => $recuperacion['expiracion'],
                ];
                $_SESSION['success'] = 'Solicitud aprobada. Verifique la identidad antes de entregar el enlace temporal.';
                Auditoria::registrar((int)$admin['id_usuario'], 'APROBAR_SOLICITUD_RECUPERACION', 'USUARIOS', "Solicitud de recuperación #{$idSolicitud} aprobada para la cuenta #{$solicitud['id_usuario']}");
            } else {
                if (!$this->solicitudRecuperacionModel->resolver($idSolicitud, (int)$admin['id_usuario'], 'RECHAZADA', $motivo)) throw new RuntimeException('La solicitud ya fue atendida.');
                $_SESSION['success'] = 'Solicitud de recuperación rechazada.';
                Auditoria::registrar((int)$admin['id_usuario'], 'RECHAZAR_SOLICITUD_RECUPERACION', 'USUARIOS', "Solicitud de recuperación #{$idSolicitud} rechazada. Motivo: {$motivo}");
            }
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: index.php?action=admin_solicitudes_recuperacion', true, 303);
        exit();
    }

    /** Reportes, exportaciones y últimos movimientos administrativos. */
    public function reportes(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        require_once __DIR__ . '/ReporteController.php';
        $reporteController = new ReporteController();
        $filtrosReporte = $reporteController->normalizarFiltros($_GET);
        // Los filtros del resumen son independientes de los del listado
        // "Reportes por evento".
        $filtrosResumen = $reporteController->normalizarFiltrosResumen($_GET);
        $idEventoSolicitado = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        if ($idEventoSolicitado) {
            $destino = 'index.php?action=' . (filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT) ? 'admin_reporte_asistencia_sesion' : 'admin_reporte_evento');
            $destino .= '&id_evento=' . (int)$idEventoSolicitado;
            if ($idSesion = filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT)) $destino .= '&id_sesion=' . (int)$idSesion;
            $filtrosEnlace = [];
            if ($filtrosReporte['id_categoria']) $filtrosEnlace['id_categoria'] = $filtrosReporte['id_categoria'];
            if ($filtrosReporte['id_tipo_evento']) $filtrosEnlace['id_tipo_evento'] = $filtrosReporte['id_tipo_evento'];
            if ($filtrosReporte['habilitado']) $filtrosEnlace['habilitado'] = $filtrosReporte['habilitado'];
            if ($filtrosReporte['buscar'] !== '') $filtrosEnlace['buscar'] = $filtrosReporte['buscar'];
            if ($filtrosReporte['fecha_desde']) $filtrosEnlace['fecha_desde'] = $filtrosReporte['fecha_desde'];
            if ($filtrosReporte['fecha_hasta']) $filtrosEnlace['fecha_hasta'] = $filtrosReporte['fecha_hasta'];
            if ($filtrosEnlace) $destino .= '&' . http_build_query($filtrosEnlace);
            header('Location: ' . $destino);
            exit();
        }
        $areasReporte = $reporteController->listarAreas();
        $tiposReporte = $reporteController->listarTipos();
        $resumenAreas = $reporteController->resumenPorAreas($filtrosResumen);
        $resumenTipos = $reporteController->resumenPorTipos($filtrosResumen);
        $filtrosDatos = $reporteController->normalizarFiltrosDatos($_GET);
        $eventosDatos = $reporteController->eventosParaDatos($filtrosDatos, 11);
        $hayMasEventosDatos = count($eventosDatos) > 10;
        $eventosDatos = array_slice($eventosDatos, 0, 10);
        $idEventoDatosSeleccionado = filter_input(INPUT_GET, 'datos_evento_id', FILTER_VALIDATE_INT) ?: null;
        // El panel sólo muestra los diez eventos más próximos. La lista completa
        // se consulta en una pantalla independiente para evitar que crezca sin límite.
        $eventosReporte = $reporteController->eventosDelPeriodo($filtrosReporte, 11);
        $hayMasEventosReporte = count($eventosReporte) > 10;
        $eventosReporte = array_slice($eventosReporte, 0, 10);
        $cursosHabilitados = array_sum(array_map(static fn(array $fila): int => (int)$fila['cursos_habilitados'], $resumenAreas));

        // Los detalles viven en rutas independientes para no sobrecargar el resumen.
        $eventoDetalle = null;
        $sesionesReporte = [];
        $certificadosReporte = [];
        $sesionDetalle = null;
        $asistenciaSesionReporte = [];
        $ultimasAuditorias = (new Auditoria())->listar(10, 0);
        require_once __DIR__ . '/../views/admin/reportes/index.php';
    }

    /** Bitácora completa de acciones administrativas, paginada. */
    public function auditoria(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT);
        $pagina = $pagina && $pagina > 0 ? $pagina : 1;
        $porPagina = 50;
        $auditoriaModel = new Auditoria();
        $totalAuditorias = $auditoriaModel->contar();
        $totalPaginas = max(1, (int)ceil($totalAuditorias / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $auditorias = $auditoriaModel->listar($porPagina, ($pagina - 1) * $porPagina);

        require_once __DIR__ . '/../views/admin/auditoria/index.php';
    }

    /**
     * RF-67: Listado general de usuarios con filtros por rol y estado
     */
    public function usuarios(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        $rolFiltro = filter_input(INPUT_GET, 'rol', FILTER_VALIDATE_INT);
        $buscarUsuario = mb_substr(trim((string)($_GET['buscar'] ?? '')), 0, 150);
        $ciUsuario = mb_substr(trim((string)($_GET['ci'] ?? '')), 0, 25);
        $estadoUsuario = strtoupper(trim((string)($_GET['estado'] ?? '')));
        if (!in_array($estadoUsuario, ['ACTIVO', 'INACTIVO'], true)) $estadoUsuario = '';
        $sql = "SELECT u.id_usuario, u.ci, u.nombres, u.apellidos, u.correo, u.telefono, u.activo, u.fecha_registro, r.id_rol, r.nombre AS rol_nombre
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol";
        $where = [];
        $params = [];
        if ($rolFiltro) {
            $where[] = 'u.id_rol = :rol';
            $params[':rol'] = $rolFiltro;
        }
        if ($buscarUsuario !== '') {
            // Con prepares nativos de MySQL cada marcador debe ser único.
            $where[] = '(u.nombres LIKE :buscar_nombres OR u.apellidos LIKE :buscar_apellidos)';
            $params[':buscar_nombres'] = '%' . $buscarUsuario . '%';
            $params[':buscar_apellidos'] = '%' . $buscarUsuario . '%';
        }
        if ($ciUsuario !== '') {
            $where[] = 'u.ci LIKE :ci';
            $params[':ci'] = '%' . $ciUsuario . '%';
        }
        if ($estadoUsuario !== '') {
            $where[] = 'u.activo = :activo';
            $params[':activo'] = $estadoUsuario === 'ACTIVO' ? 1 : 0;
        }
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= " ORDER BY u.fecha_registro DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $usuarios = $stmt->fetchAll();
        $roles = $this->db->query("SELECT * FROM roles ORDER BY id_rol ASC")->fetchAll();
        $filtrosUsuarios = ['rol' => $rolFiltro ?: null, 'buscar' => $buscarUsuario, 'ci' => $ciUsuario, 'estado' => $estadoUsuario];
        $enlaceRecuperacion = $_SESSION['enlace_recuperacion_asistida'] ?? null;
        unset($_SESSION['enlace_recuperacion_asistida']);

        require_once __DIR__ . '/../views/admin/usuarios/index.php';
    }

    /**
     * RF-68: Asignación y cambio de rol de usuario
     */
    public function cambiarRolUsuario(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_usuarios');
            exit();
        }

        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $nuevo_rol  = filter_input(INPUT_POST, 'id_rol', FILTER_VALIDATE_INT);
        $admin      = AuthHelper::obtenerUsuario();

        if ($id_usuario && $nuevo_rol) {
            // Impedir que un administrador se degrade a sí mismo
            if ($id_usuario === (int)$admin['id_usuario']) {
                $_SESSION['error'] = "No puede modificar su propio nivel de privilegios.";
            } else {
                $stmt = $this->db->prepare("UPDATE usuarios SET id_rol = :id_rol WHERE id_usuario = :id_usuario");
                $stmt->execute([':id_rol' => $nuevo_rol, ':id_usuario' => $id_usuario]);

                Auditoria::registrar(
                    (int)$admin['id_usuario'],
                    'CAMBIAR_ROL',
                    'USUARIOS',
                    "Se asignó el rol ID #{$nuevo_rol} al usuario ID #{$id_usuario}"
                );
                $_SESSION['success'] = "Rol de usuario actualizado exitosamente.";
            }
        }
        header('Location: index.php?action=admin_usuarios');
        exit();
    }

    /**
     * RF-69: Activar o desactivar cuentas de usuario
     */
    public function toggleEstadoUsuario(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_usuarios');
            exit();
        }

        $id_usuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $admin      = AuthHelper::obtenerUsuario();

        if ($id_usuario === (int)$admin['id_usuario']) {
            $_SESSION['error'] = "No puede suspender su propia cuenta de acceso.";
            header('Location: index.php?action=admin_usuarios');
            exit();
        }

        $stmtActual = $this->db->prepare("SELECT activo, correo FROM usuarios WHERE id_usuario = ?");
        $stmtActual->execute([$id_usuario]);
        $usuario = $stmtActual->fetch();

        if ($usuario) {
            $nuevoEstado = $usuario['activo'] ? 0 : 1;
            $stmtUpdate = $this->db->prepare("UPDATE usuarios SET activo = ? WHERE id_usuario = ?");
            $stmtUpdate->execute([$nuevoEstado, $id_usuario]);

            $accionTxt = $nuevoEstado ? "ACTIVAR_CUENTA" : "DESACTIVAR_CUENTA";
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                $accionTxt,
                'USUARIOS',
                "Cuenta {$usuario['correo']} (ID #{$id_usuario}) establecida en estado activo = {$nuevoEstado}"
            );

            $_SESSION['success'] = "El estado de la cuenta fue modificado correctamente.";
        }

        header('Location: index.php?action=admin_usuarios');
        exit();
    }

    /** Genera un enlace temporal que el administrador entrega tras verificar identidad. */
    public function generarEnlaceRecuperacion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la solicitud de recuperación.';
            header('Location: index.php?action=admin_usuarios', true, 303);
            exit();
        }

        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $admin = AuthHelper::obtenerUsuario();
        $usuario = $idUsuario ? $this->usuarioModel->obtenerPorId($idUsuario) : null;

        if (!$usuario || (int)$usuario['activo'] !== 1) {
            $_SESSION['error'] = 'Sólo puede generar enlaces para cuentas activas.';
            header('Location: index.php?action=admin_usuarios', true, 303);
            exit();
        }

        try {
            $recuperacion = $this->usuarioModel->crearTokenRecuperacion((int)$usuario['id_usuario']);
            if (!$recuperacion) {
                throw new RuntimeException('No fue posible crear el enlace de recuperación.');
            }

            $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
            $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $enlace = $protocolo . '://' . $host . $baseUrl
                . '/index.php?action=reestablecer&token=' . rawurlencode($recuperacion['token']);

            $_SESSION['enlace_recuperacion_asistida'] = [
                'nombre' => trim($usuario['nombres'] . ' ' . $usuario['apellidos']),
                'correo' => $usuario['correo'],
                'url' => $enlace,
                'expiracion' => $recuperacion['expiracion'],
            ];
            $_SESSION['success'] = 'Enlace de recuperación generado. Entréguelo sólo después de verificar la identidad del usuario.';

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'GENERAR_ENLACE_RECUPERACION',
                'USUARIOS',
                "Se generó un enlace temporal de recuperación para la cuenta ID #{$usuario['id_usuario']}"
            );
        } catch (Throwable $e) {
            error_log('Error al generar enlace de recuperación: ' . $e->getMessage());
            $_SESSION['error'] = 'No fue posible generar el enlace de recuperación. Intente nuevamente.';
        }

        header('Location: index.php?action=admin_usuarios', true, 303);
        exit();
    }

    /**
     * RF-38 y RF-39: Gestión de Tipos de Eventos
     */
    public function tiposEventos(): void {
        $this->configuracionEventos();
    }

    /** Gestiona tipos y áreas disponibles para los formularios de eventos. */
    public function configuracionEventos(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $tipos = $this->db->query("SELECT te.*, (SELECT COUNT(*) FROM eventos e WHERE e.id_tipo_evento = te.id_tipo_evento) AS eventos_asociados FROM tipos_evento te ORDER BY te.activo DESC, te.nombre ASC")->fetchAll();
        $categorias = $this->db->query("SELECT c.*, (SELECT COUNT(*) FROM eventos e WHERE e.id_categoria = c.id_categoria) AS eventos_asociados FROM categorias c ORDER BY c.activo DESC, c.nombre ASC")->fetchAll();
        require_once __DIR__ . '/../views/admin/configuracion_eventos/index.php';
    }

    public function guardarCatalogoEvento(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el registro de la configuración.';
            header('Location: index.php?action=admin_configuracion_eventos');
            exit();
        }

        $catalogo = $_POST['catalogo'] ?? '';
        $definiciones = [
            'tipo' => ['tabla' => 'tipos_evento', 'clave' => 'id_tipo_evento', 'etiqueta' => 'Tipo de evento'],
            'categoria' => ['tabla' => 'categorias', 'clave' => 'id_categoria', 'etiqueta' => 'Área'],
        ];
        $definicion = $definiciones[$catalogo] ?? null;
        $nombre = mb_substr(trim($_POST['nombre'] ?? ''), 0, 100);
        $abreviatura = strtoupper(mb_substr(trim($_POST['abreviatura'] ?? ''), 0, 8));
        $admin  = AuthHelper::obtenerUsuario();

        try {
            if (!$definicion || $nombre === '') {
                throw new InvalidArgumentException('Ingrese un nombre y seleccione una configuración válida.');
            }
            if (!preg_match('/^[A-Z0-9]{2,8}$/', $abreviatura)) {
                throw new InvalidArgumentException('La abreviatura debe tener entre 2 y 8 letras o números en mayúscula.');
            }

            $existente = $this->db->prepare("SELECT {$definicion['clave']} AS id, nombre, abreviatura FROM {$definicion['tabla']} WHERE nombre = :nombre OR abreviatura = :abreviatura LIMIT 1");
            $existente->execute([':nombre' => $nombre, ':abreviatura' => $abreviatura]);
            $registro = $existente->fetch();
            if ($registro && $registro['nombre'] !== $nombre) {
                throw new InvalidArgumentException("La abreviatura {$abreviatura} ya está asignada a {$registro['nombre']}.");
            }
            if ($registro && $registro['abreviatura'] !== $abreviatura) {
                throw new InvalidArgumentException("{$definicion['etiqueta']} {$nombre} ya existe con la abreviatura {$registro['abreviatura']}.");
            }
            if ($registro) {
                $activar = $this->db->prepare("UPDATE {$definicion['tabla']} SET activo = 1 WHERE {$definicion['clave']} = :id");
                $activar->execute([':id' => $registro['id']]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$definicion['tabla']} (nombre, abreviatura, activo) VALUES (:nombre, :abreviatura, 1)");
                $stmt->execute([':nombre' => $nombre, ':abreviatura' => $abreviatura]);
            }

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'GUARDAR_CATALOGO_EVENTO',
                'EVENTOS',
                "{$definicion['etiqueta']} guardado: {$nombre} ({$abreviatura})"
            );
            $_SESSION['success'] = "{$definicion['etiqueta']} guardado con éxito.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: index.php?action=admin_configuracion_eventos');
        exit();
    }

    /** Elimina catálogos sin uso o los desactiva cuando ya tienen eventos asociados. */
    public function eliminarCatalogoEvento(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la eliminación de la configuración.';
            header('Location: index.php?action=admin_configuracion_eventos');
            exit();
        }

        $catalogo = $_POST['catalogo'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $definiciones = [
            'tipo' => ['tabla' => 'tipos_evento', 'clave' => 'id_tipo_evento', 'campo_evento' => 'id_tipo_evento', 'etiqueta' => 'Tipo de evento'],
            'categoria' => ['tabla' => 'categorias', 'clave' => 'id_categoria', 'campo_evento' => 'id_categoria', 'etiqueta' => 'Área'],
        ];
        $definicion = $definiciones[$catalogo] ?? null;

        try {
            if (!$definicion || !$id) {
                throw new InvalidArgumentException('La configuración seleccionada no es válida.');
            }
            $stmtNombre = $this->db->prepare("SELECT nombre FROM {$definicion['tabla']} WHERE {$definicion['clave']} = :id LIMIT 1");
            $stmtNombre->execute([':id' => $id]);
            $nombre = $stmtNombre->fetchColumn();
            if ($nombre === false) {
                throw new InvalidArgumentException('La configuración ya no existe.');
            }
            $stmtUso = $this->db->prepare("SELECT COUNT(*) FROM eventos WHERE {$definicion['campo_evento']} = :id");
            $stmtUso->execute([':id' => $id]);
            $enUso = (int)$stmtUso->fetchColumn();
            if ($enUso > 0) {
                $stmt = $this->db->prepare("UPDATE {$definicion['tabla']} SET activo = 0 WHERE {$definicion['clave']} = :id");
                $stmt->execute([':id' => $id]);
                $_SESSION['success'] = "{$definicion['etiqueta']} desactivado: tiene {$enUso} evento(s) asociado(s).";
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$definicion['tabla']} WHERE {$definicion['clave']} = :id");
                $stmt->execute([':id' => $id]);
                $_SESSION['success'] = "{$definicion['etiqueta']} eliminado correctamente.";
            }
            $admin = AuthHelper::obtenerUsuario();
            Auditoria::registrar((int)$admin['id_usuario'], 'ELIMINAR_CATALOGO_EVENTO', 'EVENTOS', "{$definicion['etiqueta']}: {$nombre}");
        } catch (Exception $e) {
            $_SESSION['error'] = 'No se pudo actualizar la configuración: ' . $e->getMessage();
        }
        header('Location: index.php?action=admin_configuracion_eventos');
        exit();
    }

    /**
     * RF-45: Inscripción administrativa manual por parte del administrador
     */
    public function inscribirManual(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar la inscripción manual.';
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_evento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idsUsuarios = $_POST['id_usuarios'] ?? ($_POST['id_usuario'] ?? []);
        if (!is_array($idsUsuarios)) {
            $idsUsuarios = [$idsUsuarios];
        }
        $admin      = AuthHelper::obtenerUsuario();

        try {
            if (!$id_evento) {
                throw new InvalidArgumentException('El evento seleccionado no es válido.');
            }
            $idsInscripcion = $this->inscripcionModel->registrarLoteAdministrativo((int)$id_evento, $idsUsuarios);
            $cantidad = count($idsInscripcion);

            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'INSCRIPCION_MANUAL_LOTE',
                'INSCRIPCIONES',
                "Inscripción manual de {$cantidad} persona(s) en el evento #{$id_evento} (inscripciones: " . implode(', ', $idsInscripcion) . ')'
            );

            $_SESSION['success'] = $cantidad === 1
                ? 'La inscripción manual se realizó correctamente.'
                : "Se realizaron correctamente {$cantidad} inscripciones manuales.";
        } catch (Throwable $e) {
            $_SESSION['error'] = "Error en inscripción manual: " . $e->getMessage();
        }

        header('Location: index.php?action=admin_eventos', true, 303);
        exit();
    }

    /**
     * RF-47: Modificar estado de inscripción administrativamente
     */
    public function cambiarEstadoInscripcion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_inscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $nuevo_estado   = trim($_POST['estado'] ?? '');
        $motivo         = trim($_POST['motivo'] ?? 'Resolución administrativa');
        $admin          = AuthHelper::obtenerUsuario();

        if ($this->inscripcionModel->actualizarEstadoAdministrativo($id_inscripcion, $nuevo_estado, $motivo)) {
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'MODIFICAR_INSCRIPCION',
                'INSCRIPCIONES',
                "Inscripción #{$id_inscripcion} cambiada a estado {$nuevo_estado}. Motivo: {$motivo}"
            );
            $_SESSION['success'] = "Estado de inscripción actualizado.";
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado de la inscripción.";
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?action=admin_dashboard';
        header("Location: {$referer}");
        exit();
    }

    /**
     * RF-49 y RF-50: Asistencia registrada o corregida administrativamente
     */
    public function corregirAsistencia(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=admin_eventos');
            exit();
        }

        $id_sesion      = filter_input(INPUT_POST, 'id_sesion', FILTER_VALIDATE_INT);
        $id_inscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $estado         = trim($_POST['estado'] ?? 'PRESENTE');
        $observacion    = trim($_POST['observacion'] ?? 'Corrección administrativa directa');
        $admin          = AuthHelper::obtenerUsuario();

        if ($this->asistenciaModel->registrarOActualizar($id_sesion, $id_inscripcion, $estado, (int)$admin['id_usuario'], $observacion)) {
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'CORRECCION_ASISTENCIA',
                'ASISTENCIA',
                "Asistencia modificada en Sesión #{$id_sesion} para Inscripción #{$id_inscripcion} a estado {$estado}"
            );
            $_SESSION['success'] = "Asistencia corregida y porcentaje recalculado.";
        } else {
            $_SESSION['error'] = "Error al corregir asistencia.";
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?action=admin_dashboard';
        header("Location: {$referer}");
        exit();
    }
}
