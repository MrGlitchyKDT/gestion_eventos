<?php
// gestion_eventos/public/index.php
require_once __DIR__ . '/../helpers/AuthHelper.php';
AuthHelper::initSession();
AuthHelper::verificarSesionActual();

$action = is_string($_GET['action'] ?? null) ? $_GET['action'] : 'catalogo';

// Una sesión vigente siempre entra a su módulo desde la raíz o las pantallas de acceso.
if (AuthHelper::estaAutenticado() && (!isset($_GET['action'])
    || in_array($action, ['login', 'do_login', 'registro', 'do_registro', 'recuperar', 'do_recuperar'], true))) {
    AuthHelper::redirigirInicio();
}

// 1. Rutas públicas (sin requerir inicio de sesión)
$rutas_publicas = [
    'catalogo',
    'detalle_evento',
    'login',
    'do_login',
    'registro',
    'do_registro',
    'recuperar',
    'do_recuperar',
    'reestablecer',
    'do_reestablecer',
    'verificar_certificado',
    'generar_qr'
];

// 2. Control de acceso perimetral
if (!in_array($action, $rutas_publicas, true) && !AuthHelper::estaAutenticado()) {
    header('Location: index.php?action=login');
    exit();
}

// 3. Despachador de acciones (Lazy Loading de controladores)
switch ($action) {

    // ==========================================
    // PORTAL PÚBLICO Y CATÁLOGO (RF-07 a RF-09)
    // ==========================================
    case 'catalogo':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->catalogo();
        break;

    case 'detalle_evento':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->detalle();
        break;

    case 'verificar_certificado':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->verificarPublico();
        break;

    case 'generar_qr':
        require_once __DIR__ . '/../helpers/QrHelper.php';
        try {
            $contenidoQr = (string)($_GET['contenido'] ?? '');
            $svgQr = QrHelper::generarSvg($contenidoQr);
            header('Content-Type: image/svg+xml; charset=UTF-8');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
            echo $svgQr;
        } catch (Throwable $e) {
            http_response_code(400);
        }
        exit();

    case 'imprimir_certificado':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->imprimir();
        break;

    // ==========================================
    // AUTENTICACIÓN Y CUENTAS (RF-01 a RF-06)
    // ==========================================
    case 'login':
        require_once __DIR__ . '/../views/auth/login.php';
        break;

    case 'do_login':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->doLogin();
        break;

    case 'registro':
        require_once __DIR__ . '/../views/auth/registro.php';
        break;

    case 'do_registro':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->doRegistro();
        break;

    case 'recuperar':
        require_once __DIR__ . '/../views/auth/recuperar.php';
        break;

    case 'do_recuperar':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->doRecuperar();
        break;

    case 'reestablecer':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->reestablecer();
        break;

    case 'do_reestablecer':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->doReestablecer();
        break;

    case 'logout':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'perfil':
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR', 'PARTICIPANTE']);
        require_once __DIR__ . '/../views/usuario/perfil.php';
        break;

    case 'actualizar_perfil':
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR', 'PARTICIPANTE']);
        $usr = AuthHelper::obtenerUsuario();
        require_once __DIR__ . '/../models/Usuario.php';
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('No se pudo validar la actualización del perfil.');
            }
            (new Usuario())->actualizarPerfil((int)$usr['id_usuario'], $_POST);
            $_SESSION['success'] = 'Datos personales actualizados correctamente.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: index.php?action=perfil');
        exit();

    case 'actualizar_foto_perfil':
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR', 'PARTICIPANTE']);
        $usr = AuthHelper::obtenerUsuario();
        require_once __DIR__ . '/../models/Usuario.php';
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
                throw new InvalidArgumentException('No se pudo validar la actualización de la foto.');
            }
            (new Usuario())->actualizarFotoPerfil((int)$usr['id_usuario'], $_FILES['foto_perfil'] ?? []);
            $_SESSION['success'] = 'La foto de perfil fue actualizada correctamente.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: index.php?action=perfil');
        exit();

    case 'ver_foto_perfil':
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR', 'PARTICIPANTE']);
        $usr = AuthHelper::obtenerUsuario();
        require_once __DIR__ . '/../models/Usuario.php';
        $rutaFoto = (new Usuario())->rutaFotoPerfil((int)$usr['id_usuario']);
        if (!$rutaFoto) {
            http_response_code(404);
            exit();
        }
        $mimeFoto = (new finfo(FILEINFO_MIME_TYPE))->file($rutaFoto);
        if (!in_array($mimeFoto, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            http_response_code(404);
            exit();
        }
        header('Content-Type: ' . $mimeFoto);
        header('Content-Length: ' . filesize($rutaFoto));
        header('Cache-Control: private, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        readfile($rutaFoto);
        exit();

    case 'actualizar_password':
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR', 'PARTICIPANTE']);
        $usr = AuthHelper::obtenerUsuario();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'No se pudo validar el cambio de contraseña.';
            header('Location: index.php?action=perfil');
            exit();
        }
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = ?");
        $stmt->execute([(int)$usr['id_usuario']]);
        $hashActual = $stmt->fetchColumn();

        require_once __DIR__ . '/../models/Usuario.php';
        if (password_verify($_POST['password_actual'] ?? '', $hashActual)) {
            (new Usuario())->reestablecerPassword((int)$usr['id_usuario'], $_POST['password_nueva'] ?? '');
            $_SESSION['success'] = "Contraseña actualizada con éxito.";
        } else {
            $_SESSION['error'] = "La contraseña actual no es correcta.";
        }
        header('Location: index.php?action=perfil');
        exit();

    // ==========================================
    // PARTICIPANTE: INSCRIPCIONES Y CERTIFICADOS (RF-10 a RF-19)
    // ==========================================
    case 'inscribirse_evento':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->inscribirse();
        break;

    case 'cancelar_inscripcion':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->cancelarInscripcion();
        break;

    case 'mis_inscripciones':
        AuthHelper::requerirRol(['PARTICIPANTE', 'EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();
        require_once __DIR__ . '/../models/Inscripcion.php';
        $inscripciones = (new Inscripcion())->listarPorUsuario((int)$usuario['id_usuario']);
        require_once __DIR__ . '/../views/participante/mis_inscripciones.php';
        break;

    case 'mi_asistencia':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->miAsistencia();
        break;

    case 'confirmar_asistencia':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->confirmarAsistencia();
        break;

    case 'mis_certificados':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->misCertificados();
        break;

    case 'solicitar_reimpresion':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->procesarSolicitudReimpresion();
        break;

    case 'descargar_certificado':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->descargarPdf();
        break;

    // ==========================================
    // EXPOSITOR: GESTIÓN Y ASISTENCIA (RF-20 a RF-26)
    // ==========================================
    case 'expositor_eventos':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->misEventos();
        break;

    case 'expositor_subir_material':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->subirMateriales();
        break;

    case 'expositor_sesiones':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->sesiones();
        break;

    case 'expositor_asistencia':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->planillaAsistencia();
        break;

    case 'api_guardar_asistencia':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->guardarAsistenciaApi();
        break;

    case 'expositor_abrir_asistencia':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->abrirAsistencia();
        break;

    case 'expositor_cerrar_asistencia':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->cerrarAsistencia();
        break;

    case 'expositor_exportar_asistencia':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->exportarAsistencia();
        break;

    case 'expositor_descargar_asistencia_pdf':
        require_once __DIR__ . '/../controllers/ExpositorController.php';
        (new ExpositorController())->descargarAsistenciaPdf();
        break;

    // ==========================================
    // ADMINISTRACIÓN CENTRAL (RF-29 a RF-70)
    // ==========================================
    case 'admin_dashboard':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->dashboard();
        break;

    case 'admin_eventos':
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        require_once __DIR__ . '/../models/Evento.php';
        $eventoModel = new Evento();
        $filtrosEventos = [
            'id_tipo_evento' => filter_input(INPUT_GET, 'id_tipo_evento', FILTER_VALIDATE_INT) ?: null,
            'id_categoria' => filter_input(INPUT_GET, 'id_categoria', FILTER_VALIDATE_INT) ?: null,
            'habilitado' => in_array(strtoupper((string)($_GET['habilitado'] ?? '')), ['SI', 'NO'], true)
                ? strtoupper((string)$_GET['habilitado']) : '',
            'buscar' => mb_substr(trim((string)($_GET['buscar'] ?? '')), 0, 150),
        ];
        $eventos = $eventoModel->listarParaAdministracion($filtrosEventos);
        $expositoresPorEvento = $eventoModel->listarExpositoresAgrupadosPorEventos(array_column($eventos, 'id_evento'));
        require_once __DIR__ . '/../models/SesionEvento.php';
        $sesionesPorEvento = (new SesionEvento())->listarAgrupadasPorEventos(array_column($eventos, 'id_evento'));
        require_once __DIR__ . '/../models/SerieSesionEvento.php';
        $seriesPorEvento = (new SerieSesionEvento())->listarAgrupadasPorEventos(array_column($eventos, 'id_evento'));
        require_once __DIR__ . '/../models/Material.php';
        $materialesPorEvento = (new Material())->listarAgrupadosPorEventos(array_column($eventos, 'id_evento'));
        $db = Database::getConnection();
        $tipos = $db->query("SELECT * FROM tipos_evento WHERE activo = 1")->fetchAll();
        $categorias = $db->query("SELECT * FROM categorias WHERE activo = 1")->fetchAll();
        $usuarios_participantes = $db->query("SELECT u.id_usuario, u.nombres, u.apellidos, u.ci, UPPER(TRIM(r.nombre)) AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON r.id_rol = u.id_rol
            WHERE u.activo = 1 AND UPPER(TRIM(r.nombre)) IN ('PARTICIPANTE', 'EXPOSITOR')
            ORDER BY u.apellidos ASC, u.nombres ASC")->fetchAll();
        $usuarios_expositores = $db->query("SELECT u.id_usuario, u.nombres, u.apellidos, u.ci, u.correo
            FROM usuarios u
            INNER JOIN roles r ON r.id_rol = u.id_rol
            WHERE u.activo = 1 AND UPPER(TRIM(r.nombre)) = 'EXPOSITOR'
            ORDER BY u.apellidos ASC, u.nombres ASC")->fetchAll();
        require_once __DIR__ . '/../views/admin/eventos/index.php';
        break;

    case 'admin_certificados':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminGestion();
        break;

    case 'admin_plantilla_certificado_guardar':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminSubirPlantilla();
        break;

    case 'admin_plantilla_certificado_archivar':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminArchivarPlantilla();
        break;

    case 'admin_plantilla_certificado_diseno':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminGuardarDiseno();
        break;

    case 'admin_plantilla_certificado_ver':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->verPlantilla();
        break;

    case 'admin_plantilla_certificado_previsualizar':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminPrevisualizar();
        break;

    case 'admin_certificado_emitir_pdf':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminEmitirPdf();
        break;

    case 'admin_certificado_emitir_expositor_pdf':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminEmitirExpositorPdf();
        break;

    case 'admin_certificado_regenerar_pdf':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminRegenerarPdf();
        break;

    case 'admin_certificado_anular':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminAnular();
        break;

    case 'admin_reimpresiones':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminReimpresiones();
        break;

    case 'admin_reimpresion_comprobante':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminVerComprobanteReimpresion();
        break;

    case 'admin_reimpresion_resolver':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminResolverReimpresion();
        break;

    case 'admin_reimpresion_entregar':
        require_once __DIR__ . '/../controllers/CertificadoController.php';
        (new CertificadoController())->adminEntregarReimpresion();
        break;

    case 'admin_eventos_guardar':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminGuardarEvento();
        break;

    case 'admin_evento_cambiar_estado':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminCambiarEstado();
        break;

    case 'admin_evento_actualizar':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminActualizarEvento();
        break;

    case 'admin_evento_asignar_expositor':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminAsignarExpositor();
        break;

    case 'admin_evento_desasignar_expositor':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminDesasignarExpositor();
        break;

    case 'admin_evento_crear_sesion':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminCrearSesion();
        break;

    case 'admin_evento_guardar_serie_sesiones':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminGuardarSerieSesiones();
        break;

    case 'admin_evento_eliminar_sesion':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminEliminarSesion();
        break;

    case 'descargar_material':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->descargarMaterial();
        break;

    case 'admin_material_eliminar':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->adminEliminarMaterial();
        break;

    case 'admin_evento_editar':
        header('Location: index.php?action=admin_eventos', true, 303);
        exit();

    case 'admin_inscribir_manual':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->inscribirManual();
        break;

    case 'admin_usuarios':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->usuarios();
        break;

    case 'admin_usuario_cambiar_rol':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->cambiarRolUsuario();
        break;

    case 'admin_usuario_toggle_estado':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->toggleEstadoUsuario();
        break;

    case 'admin_usuario_generar_recuperacion':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->generarEnlaceRecuperacion();
        break;

    case 'admin_solicitudes_recuperacion':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->solicitudesRecuperacion();
        break;

    case 'admin_solicitud_recuperacion_resolver':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->resolverSolicitudRecuperacion();
        break;

    case 'admin_tipos_eventos':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->tiposEventos();
        break;

    case 'admin_tipo_evento_guardar':
        header('Location: index.php?action=admin_configuracion_eventos', true, 303);
        exit();

    case 'admin_configuracion_eventos':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->configuracionEventos();
        break;

    case 'admin_catalogo_evento_guardar':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->guardarCatalogoEvento();
        break;

    case 'admin_catalogo_evento_eliminar':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->eliminarCatalogoEvento();
        break;

    // ==========================================
    // REPORTES Y EXPORTACIONES (RF-71 a RF-76)
    // ==========================================
    case 'admin_reportes':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->reportes();
        break;

    case 'admin_datos_evento':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->datosDemograficosEventoJson();
        break;

    case 'admin_datos_eventos':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->listaEventosDatos();
        break;

    case 'admin_reporte_eventos':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->listaEventos();
        break;

    case 'admin_reporte_evento':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->detalleEvento();
        break;

    case 'admin_reporte_asistencia_sesion':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->detalleAsistenciaSesion();
        break;

    case 'admin_reporte_asistencia_pdf':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->descargarAsistenciaPdf();
        break;

    case 'admin_reporte_certificados_pdf':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->descargarCertificadosPdf();
        break;

    case 'admin_auditoria':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->auditoria();
        break;

    case 'admin_exportar_reporte':
        require_once __DIR__ . '/../controllers/ReporteController.php';
        (new ReporteController())->exportar();
        break;

    default:
        http_response_code(404);
        require_once __DIR__ . '/../views/layouts/header.php';
        echo '<div class="container py-5 text-center">
                <h1 class="display-4 fw-bold text-uab-azul">404</h1>
                <p class="lead text-muted">La ruta solicitada no existe o fue movida.</p>
                <a href="index.php?action=catalogo" class="btn btn-uab-azul mt-3">Volver al inicio</a>
              </div>';
        require_once __DIR__ . '/../views/layouts/footer.php';
        break;

    case 'participante_dashboard':
        require_once __DIR__ . '/../controllers/EventoController.php';
        (new EventoController())->participanteDashboard();
        break;
}
