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
        $plantillas = $idEvento ? $plantillaModel->listarPorEvento($idEvento) : [];
        $idPlantilla = filter_input(INPUT_GET, 'id_plantilla', FILTER_VALIDATE_INT) ?: 0;
        $plantilla = null;
        foreach ($plantillas as $fila) {
            if ((int)$fila['id_plantilla'] === $idPlantilla) { $plantilla = $fila; break; }
        }
        if (!$plantilla) {
            foreach ($plantillas as $fila) {
                if ((int)$fila['activa'] === 1) { $plantilla = $fila; $idPlantilla = (int)$fila['id_plantilla']; break; }
            }
        }
        try { $lienzo = $plantilla ? $plantillaModel->dimensionesPagina($plantilla) : ['ancho'=>297.0, 'alto'=>210.0]; }
        catch (Throwable $e) { $lienzo = ['ancho'=>297.0, 'alto'=>210.0]; }
        $puedeEmitirParticipantes = $plantilla && (int)$plantilla['activa'] === 1 && in_array($plantilla['destinatario'], ['PARTICIPANTE', 'AMBOS'], true);
        $puedeEmitirExpositores = $plantilla && (int)$plantilla['activa'] === 1 && in_array($plantilla['destinatario'], ['EXPOSITOR', 'AMBOS'], true);
        $habilitados = $puedeEmitirParticipantes ? $this->certificadoModel->listarHabilitadosPorEvento($idEvento, $idPlantilla) : [];
        $expositoresPendientes = $puedeEmitirExpositores ? $this->certificadoModel->listarExpositoresPendientesPorEvento($idEvento, $idPlantilla) : [];
        $emitidos = $idEvento ? $this->certificadoModel->listarPorEvento($idEvento) : [];
        require_once __DIR__ . '/../views/admin/certificados/index.php';
    }

    public function adminSubirPlantilla(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento) throw new Exception('No se pudo validar la plantilla.');
            $pagina = max(1, (int)($_POST['pagina'] ?? 1));
            $idPlantilla = (new PlantillaCertificado())->guardar(
                $idEvento,
                (int)AuthHelper::obtenerUsuario()['id_usuario'],
                $_FILES['plantilla'] ?? [],
                $pagina,
                $this->configuracionCampos($_POST),
                $_POST['nombre_plantilla'] ?? '',
                $_POST['destinatario'] ?? 'PARTICIPANTE'
            );
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'CARGAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', "Plantilla #{$idPlantilla} configurada para el evento #{$idEvento}");
            $_SESSION['success'] = 'Plantilla PDF guardada. Selecciónela para configurar posiciones y emitir certificados.';
        } catch (Exception $e) { $_SESSION['error'] = $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento . (!empty($idPlantilla) ? '&id_plantilla=' . (int)$idPlantilla : '')); exit();
    }

    /** Archiva una plantilla sin eliminarla ni afectar certificados históricos. */
    public function adminArchivarPlantilla(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento || !$idPlantilla) throw new Exception('Solicitud de archivo no válida.');
            $plantilla = (new PlantillaCertificado())->obtenerPorId($idPlantilla, true);
            if (!$plantilla || (int)$plantilla['id_evento'] !== (int)$idEvento) throw new Exception('La plantilla seleccionada no pertenece al evento.');
            if (!(new PlantillaCertificado())->archivar($idPlantilla)) throw new Exception('La plantilla ya estaba archivada.');
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'ARCHIVAR_PLANTILLA_CERTIFICADO', 'CERTIFICADOS', "Plantilla #{$idPlantilla} archivada del evento #{$idEvento}");
            $_SESSION['success'] = 'Plantilla archivada. Los certificados emitidos conservan su documento original.';
        } catch (Throwable $e) { $_SESSION['error'] = $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento); exit();
    }

    /** Guarda posiciones configuradas visualmente sobre la plantilla. */
    public function adminGuardarDiseno(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento || !$idPlantilla) throw new Exception('No se pudo validar el diseño.');
            $plantilla = (new PlantillaCertificado())->obtenerPorId($idPlantilla, true);
            if (!$plantilla || (int)$plantilla['id_evento'] !== (int)$idEvento) throw new Exception('La plantilla seleccionada no pertenece al evento.');
            $campos = json_decode($_POST['configuracion_campos'] ?? '', true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($campos)) throw new Exception('El diseño no contiene una configuración válida.');
            $resultado = $this->normalizarDiseno($campos);
            (new PlantillaCertificado())->actualizarConfiguracion($idPlantilla, $resultado);
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'EDITAR_DISENO_CERTIFICADO', 'CERTIFICADOS', "Posiciones actualizadas para la plantilla #{$idPlantilla} del evento #{$idEvento}");
            $_SESSION['success'] = 'Posiciones del certificado guardadas correctamente.';
        } catch (Throwable $e) { $_SESSION['error'] = 'No se pudo guardar el diseño: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento . '&id_plantilla=' . (int)$idPlantilla); exit();
    }

    /** Sirve la plantilla únicamente al administrador para el editor visual. */
    public function verPlantilla(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idPlantilla = filter_input(INPUT_GET, 'id_plantilla', FILTER_VALIDATE_INT);
        $plantilla = $idPlantilla ? (new PlantillaCertificado())->obtenerPorId($idPlantilla) : null;
        $ruta = $plantilla ? (new PlantillaCertificado())->rutaFisica($plantilla) : '';
        if (!is_file($ruta)) { http_response_code(404); exit('Plantilla no disponible.'); }
        header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="plantilla-certificado.pdf"'); header('Content-Length: ' . filesize($ruta)); readfile($ruta); exit();
    }

    /** Vista previa con datos de muestra. No inserta ni altera certificados. */
    public function adminPrevisualizar(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        try {
            $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
            $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT);
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento || !$idPlantilla) {
                throw new Exception('No se pudo validar la vista previa.');
            }
            $plantilla = (new PlantillaCertificado())->obtenerPorId($idPlantilla, true);
            if (!$plantilla || (int)$plantilla['id_evento'] !== (int)$idEvento) {
                throw new Exception('La plantilla seleccionada no pertenece a este evento.');
            }
            $campos = json_decode($_POST['configuracion_campos'] ?? '', true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($campos)) throw new Exception('El diseño no contiene una configuración válida.');
            $plantilla['configuracion_campos'] = json_encode($this->normalizarDiseno($campos), JSON_UNESCAPED_UNICODE);

            $stmt = Database::getConnection()->prepare('SELECT titulo AS evento_titulo, horas_academicas, fecha_inicio, fecha_fin FROM eventos WHERE id_evento = :evento LIMIT 1');
            $stmt->execute([':evento' => $idEvento]);
            $evento = $stmt->fetch();
            if (!$evento) throw new Exception('No se encontró el evento seleccionado.');

            $datosMuestra = array_merge($evento, [
                'nombres' => 'Nombre',
                'apellidos' => 'del participante',
                'ci' => '12345678',
                'rol_expositor' => $plantilla['destinatario'] === 'EXPOSITOR' ? 'Expositor/a' : '',
                'codigo_unico' => 'VISTA-PREVIA',
                'codigo_qr' => QrHelper::generarUrlVerificacion('VISTA-PREVIA'),
            ]);
            $contenido = (new CertificadoPdfService())->generarVistaPrevia($plantilla, $datosMuestra);
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="vista-previa-certificado.pdf"');
            header('Content-Length: ' . strlen($contenido));
            echo $contenido;
        } catch (Throwable $e) {
            http_response_code(422);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'No se pudo generar la vista previa: ' . $e->getMessage();
        }
        exit();
    }

    public function adminEmitirPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idInscripcion = filter_input(INPUT_POST, 'id_inscripcion', FILTER_VALIDATE_INT);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idInscripcion || !$idPlantilla) throw new Exception('Solicitud de emisión no válida.');
            $certificado = $this->emitirDocumento($idInscripcion, (int)AuthHelper::obtenerUsuario()['id_usuario'], $idPlantilla);
            $_SESSION['success'] = 'Certificado emitido: ' . $certificado['codigo_unico'];
        } catch (Exception $e) { $_SESSION['error'] = 'No se pudo emitir el certificado: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento . '&id_plantilla=' . (int)$idPlantilla); exit();
    }

    /** Emite un certificado exclusivo para un expositor asignado. */
    public function adminEmitirExpositorPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idUsuario = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT);
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idEvento || !$idUsuario || !$idPlantilla) throw new Exception('Solicitud de emisión no válida.');
            $certificado = $this->emitirDocumentoExpositor($idEvento, $idUsuario, (int)AuthHelper::obtenerUsuario()['id_usuario'], $idPlantilla);
            $_SESSION['success'] = 'Certificado de expositor emitido: ' . $certificado['codigo_unico'];
        } catch (Throwable $e) { $_SESSION['error'] = 'No se pudo emitir el certificado del expositor: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento . '&id_plantilla=' . (int)$idPlantilla); exit();
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
            $plantilla = !empty($certificado['id_plantilla'])
                ? (new PlantillaCertificado())->obtenerPorId((int)$certificado['id_plantilla'])
                : (new PlantillaCertificado())->obtenerPorEvento((int)$certificado['id_evento']);
            if (!$plantilla) throw new Exception('El evento no tiene una plantilla activa.');
            $certificado['codigo_qr'] = $certificado['codigo_qr'] ?: QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
            $ruta = (new CertificadoPdfService())->generar($plantilla, $certificado);
            $this->certificadoModel->actualizarDocumento($idCertificado, $ruta, $certificado['codigo_qr']);
            Auditoria::registrar((int)AuthHelper::obtenerUsuario()['id_usuario'], 'REGENERAR_CERTIFICADO_PDF', 'CERTIFICADOS', "Certificado {$certificado['codigo_unico']} regenerado con el diseño actual.");
            $_SESSION['success'] = 'PDF regenerado con las posiciones actuales.';
        } catch (Throwable $e) { $_SESSION['error'] = 'No se pudo regenerar el PDF: ' . $e->getMessage(); }
        header('Location: index.php?action=admin_certificados&id_evento=' . (int)$idEvento . (!empty($certificado['id_plantilla']) ? '&id_plantilla=' . (int)$certificado['id_plantilla'] : '')); exit();
    }

    public function descargarPdf(): void {
        // Los certificados de participantes se entregan únicamente de forma física.
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR']);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $certificado = $id ? $this->certificadoModel->obtenerPorId($id) : null;
        $usuario = AuthHelper::obtenerUsuario();
        if (!$certificado || $certificado['estado'] !== 'EMITIDO' || ($usuario['rol_nombre'] !== 'ADMINISTRADOR' && (int)$certificado['id_usuario'] !== (int)$usuario['id_usuario'])) { http_response_code(404); exit('Certificado no disponible.'); }
        $ruta = __DIR__ . '/../storage/certificados/' . basename((string)$certificado['ruta_archivo_pdf']);
        if (!is_file($ruta)) { header('Location: index.php?action=imprimir_certificado&codigo=' . urlencode($certificado['codigo_unico'])); exit(); }
        header('Content-Type: application/pdf'); header('Content-Disposition: inline; filename="' . basename($certificado['codigo_unico']) . '.pdf"'); header('Content-Length: ' . filesize($ruta)); readfile($ruta); exit();
    }

    private function emitirDocumento(int $idInscripcion, int $idAdmin, int $idPlantilla): array {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT id_evento FROM inscripciones WHERE id_inscripcion=:id'); $stmt->execute([':id'=>$idInscripcion]);
        $idEvento = (int)$stmt->fetchColumn();
        $plantilla = $idPlantilla ? (new PlantillaCertificado())->obtenerPorId($idPlantilla, true) : null;
        if (!$plantilla || (int)$plantilla['id_evento'] !== $idEvento || !in_array($plantilla['destinatario'], ['PARTICIPANTE', 'AMBOS'], true)) throw new Exception('Seleccione una plantilla activa para participantes de este evento.');
        $idCertificado = $this->certificadoModel->generarIndividual($idInscripcion, $idAdmin, $idPlantilla);
        try {
            $certificado = $this->certificadoModel->obtenerPorId($idCertificado);
            $certificado['codigo_qr'] = QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
            $ruta = (new CertificadoPdfService())->generar($plantilla, $certificado);
            $this->certificadoModel->actualizarDocumento($idCertificado, $ruta, $certificado['codigo_qr']);
            Auditoria::registrar($idAdmin, 'EMITIR_CERTIFICADO_PDF', 'CERTIFICADOS', "Certificado {$certificado['codigo_unico']} emitido para la inscripción #{$idInscripcion}");
            return $certificado;
        } catch (Throwable $e) { $this->certificadoModel->eliminarRecienEmitido($idCertificado); throw $e; }
    }

    private function emitirDocumentoExpositor(int $idEvento, int $idUsuario, int $idAdmin, int $idPlantilla): array {
        $plantilla = (new PlantillaCertificado())->obtenerPorId($idPlantilla, true);
        if (!$plantilla || (int)$plantilla['id_evento'] !== $idEvento || !in_array($plantilla['destinatario'], ['EXPOSITOR', 'AMBOS'], true)) throw new Exception('Seleccione una plantilla activa para expositores de este evento.');
        $idCertificado = $this->certificadoModel->generarParaExpositor($idEvento, $idUsuario, $idAdmin, $idPlantilla);
        try {
            $certificado = $this->certificadoModel->obtenerPorId($idCertificado);
            $certificado['codigo_qr'] = QrHelper::generarUrlVerificacion($certificado['codigo_unico']);
            $ruta = (new CertificadoPdfService())->generar($plantilla, $certificado);
            $this->certificadoModel->actualizarDocumento($idCertificado, $ruta, $certificado['codigo_qr']);
            Auditoria::registrar($idAdmin, 'EMITIR_CERTIFICADO_EXPOSITOR_PDF', 'CERTIFICADOS', "Certificado {$certificado['codigo_unico']} emitido para el expositor #{$idUsuario}");
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
            'rol_expositor'=>['activo'=>false,'x'=>148.5,'y'=>148,'ancho'=>257,'tamano'=>11,'alineacion'=>'C','negrita'=>false],
            'codigo'=>['activo'=>true,'x'=>18,'y'=>190,'ancho'=>100,'tamano'=>8,'alineacion'=>'L','negrita'=>false],
            'validacion'=>['activo'=>false,'x'=>18,'y'=>196,'ancho'=>170,'tamano'=>6,'alineacion'=>'L','negrita'=>false],
            'qr'=>['activo'=>true,'x'=>256,'y'=>180,'tamano'=>28],
        ];
        foreach ($defecto as $clave => &$campo) foreach (['x','y','ancho','tamano'] as $propiedad) if (isset($entrada[$clave . '_' . $propiedad]) && is_numeric($entrada[$clave . '_' . $propiedad])) $campo[$propiedad] = (float)$entrada[$clave . '_' . $propiedad];
        return $defecto;
    }

    /** Limita los valores del editor antes de guardarlos o renderizar una vista previa. */
    private function normalizarDiseno(array $campos): array {
        $lienzo = $campos['_lienzo'] ?? [];
        $anchoLienzo = max(50, min(500, (float)($lienzo['ancho'] ?? 297)));
        $altoLienzo = max(50, min(500, (float)($lienzo['alto'] ?? 210)));
        $permitidos = ['nombre','ci','evento','horas','fechas','rol_expositor','codigo','validacion','qr'];
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
        $resultado['_lienzo'] = ['ancho' => $anchoLienzo, 'alto' => $altoLienzo];
        return $resultado;
    }

    /**
     * RF-15 y RF-16: Panel del participante para ver sus certificados obtenidos
     */
    public function misCertificados(): void {
        AuthHelper::requerirRol(['PARTICIPANTE', 'EXPOSITOR', 'ADMINISTRADOR']);
        $usuario = AuthHelper::obtenerUsuario();

        $certificados = $this->certificadoModel->listarPorUsuario((int)$usuario['id_usuario']);
        $solicitudes = $this->reimpresionModel->listarPorUsuario((int)$usuario['id_usuario']);
        $esParticipante = ($usuario['rol_nombre'] ?? '') === 'PARTICIPANTE';

        require_once __DIR__ . '/../views/participante/mis_certificados.php';
    }

    /**
     * RF-17 y RF-18: El participante solicita la reimpresión indicando motivo
     */
    public function procesarSolicitudReimpresion(): void {
        AuthHelper::requerirRol(['PARTICIPANTE']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') $_SESSION['error'] = 'La solicitud expiró. Inténtelo de nuevo.';
            header('Location: index.php?action=mis_certificados');
            exit();
        }

        $usuario = AuthHelper::obtenerUsuario();
        $id_certificado = filter_input(INPUT_POST, 'id_certificado', FILTER_VALIDATE_INT);
        $motivo = trim($_POST['motivo'] ?? '');

        if (!$id_certificado || $motivo === '') {
            $_SESSION['error'] = 'Debe indicar un motivo para la reimpresión.';
            header('Location: index.php?action=mis_certificados');
            exit();
        }
        if (mb_strlen($motivo) > 1000) {
            $_SESSION['error'] = 'El motivo no puede superar los 1.000 caracteres.';
            header('Location: index.php?action=mis_certificados');
            exit();
        }

        $archivo = $_FILES['comprobante'] ?? null;
        $rutaComprobante = null;
        try {
            if (!is_array($archivo) || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                throw new Exception('Adjunte el comprobante de pago en PDF, JPG o PNG.');
            }
            $tamano = (int)($archivo['size'] ?? 0);
            if ($tamano < 1 || $tamano > 5 * 1024 * 1024) {
                throw new Exception('El comprobante debe pesar como máximo 5 MB.');
            }
            if (!is_uploaded_file((string)($archivo['tmp_name'] ?? ''))) {
                throw new Exception('No se recibió un archivo válido.');
            }
            $tipo = (new finfo(FILEINFO_MIME_TYPE))->file((string)$archivo['tmp_name']);
            $extensiones = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
            if (!isset($extensiones[$tipo])) {
                throw new Exception('El comprobante debe ser un archivo PDF, JPG o PNG.');
            }
            $directorio = __DIR__ . '/../storage/comprobantes_reimpresion';
            if (!is_dir($directorio) && !mkdir($directorio, 0775, true) && !is_dir($directorio)) {
                throw new Exception('No se pudo preparar el almacenamiento del comprobante.');
            }
            $nombreSeguro = 'reimpresion_' . bin2hex(random_bytes(16)) . '.' . $extensiones[$tipo];
            $rutaComprobante = $directorio . '/' . $nombreSeguro;
            if (!move_uploaded_file((string)$archivo['tmp_name'], $rutaComprobante)) {
                throw new Exception('No se pudo guardar el comprobante adjunto.');
            }
            $this->reimpresionModel->registrarSolicitud($id_certificado, (int)$usuario['id_usuario'], $motivo, [
                'ruta' => $nombreSeguro,
                'nombre' => mb_substr(basename((string)$archivo['name']), 0, 255),
                'tipo' => $tipo,
                'tamano' => $tamano,
            ]);
            $_SESSION['success'] = 'Solicitud enviada. El administrador revisará el comprobante y, si se aprueba, podrá recoger el certificado en la Dirección de Extensión.';
        } catch (Throwable $e) {
            if ($rutaComprobante && is_file($rutaComprobante)) @unlink($rutaComprobante);
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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
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
        $idCertificado = filter_input(INPUT_POST, 'id_certificado', FILTER_VALIDATE_INT);
        $idEvento = filter_input(INPUT_POST, 'id_evento', FILTER_VALIDATE_INT);
        $idPlantilla = filter_input(INPUT_POST, 'id_plantilla', FILTER_VALIDATE_INT) ?: 0;
        $destino = 'index.php?action=admin_certificados' . ($idEvento ? '&id_evento=' . (int)$idEvento : '')
            . ($idPlantilla ? '&id_plantilla=' . (int)$idPlantilla : '');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null) || !$idCertificado || !$idEvento) {
                throw new Exception('La solicitud de anulación no es válida.');
            }

            $certificado = $this->certificadoModel->obtenerPorId($idCertificado);
            if (!$certificado || (int)$certificado['id_evento'] !== (int)$idEvento) {
                throw new Exception('El certificado seleccionado no pertenece a este evento.');
            }
            if ($certificado['estado'] !== 'EMITIDO') {
                throw new Exception('Este certificado ya no se encuentra vigente.');
            }

            $motivo = trim((string)($_POST['motivo_anulacion'] ?? ''));
            if (mb_strlen($motivo) > 2000) {
                throw new Exception('El motivo de anulación no puede superar los 2.000 caracteres.');
            }
            $motivo = $motivo === '' ? null : $motivo;
            $admin = AuthHelper::obtenerUsuario();
            if (!$this->certificadoModel->anular($idCertificado, (int)$admin['id_usuario'], $motivo)) {
                throw new Exception('No fue posible anular el certificado seleccionado.');
            }

            $detalleMotivo = $motivo ? ' Motivo: ' . $motivo : ' Sin motivo registrado.';
            Auditoria::registrar(
                (int)$admin['id_usuario'],
                'ANULAR_CERTIFICADO',
                'CERTIFICADOS',
                "Certificado {$certificado['codigo_unico']} anulado." . $detalleMotivo
            );
            $_SESSION['success'] = 'El certificado fue anulado. La verificación pública indicará que ya no tiene validez.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . $destino);
        exit();
    }

    /**
     * RF-57, RF-58 y RF-64: Vista formal imprimible / guardado en PDF
     */
    public function imprimir(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR', 'EXPOSITOR']);
        $codigo = trim($_GET['codigo'] ?? '');
        if (empty($codigo)) {
            die("Código de certificado no proporcionado.");
        }

        $certificado = $this->certificadoModel->obtenerPorCodigo($codigo);
        if (!$certificado) {
            http_response_code(404);
            die("El certificado solicitado no existe en el sistema.");
        }
        $usuario = AuthHelper::obtenerUsuario();
        if (($usuario['rol_nombre'] ?? '') !== 'ADMINISTRADOR' && (int)$certificado['id_usuario'] !== (int)$usuario['id_usuario']) {
            http_response_code(404);
            exit('Certificado no disponible.');
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
        $codigo = strtoupper(trim($_GET['codigo'] ?? ''));
        $certificado = null;
        $consultaRealizada = $codigo !== '';

        if ($consultaRealizada) {
            $certificado = $this->certificadoModel->obtenerPorCodigo($codigo);
        }

        require_once __DIR__ . '/../views/publico/verificar.php';
    }

    /** Buzón de solicitudes de reimpresión para la Dirección de Extensión. */
    public function adminReimpresiones(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $estado = strtoupper(trim((string)($_GET['estado'] ?? '')));
        if (!in_array($estado, ['', 'PENDIENTE', 'APROBADA', 'RECHAZADA', 'ENTREGADA'], true)) $estado = '';
        $solicitudes = $this->reimpresionModel->listarParaAdministracion($estado);
        $conteoEstados = $this->reimpresionModel->conteoPorEstado();
        require_once __DIR__ . '/../views/admin/reimpresiones/index.php';
    }

    /** El comprobante se entrega solo tras comprobar que el administrador inició sesión. */
    public function adminVerComprobanteReimpresion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idSolicitud = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $solicitud = $idSolicitud ? $this->reimpresionModel->obtenerPorId($idSolicitud) : null;
        $tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!$solicitud || empty($solicitud['ruta_comprobante']) || !in_array($solicitud['tipo_comprobante'], $tiposPermitidos, true)) {
            http_response_code(404);
            exit('Comprobante no disponible.');
        }
        $ruta = __DIR__ . '/../storage/comprobantes_reimpresion/' . basename((string)$solicitud['ruta_comprobante']);
        if (!is_file($ruta)) {
            http_response_code(404);
            exit('El archivo comprobante no fue encontrado.');
        }
        $nombre = preg_replace('/[^A-Za-z0-9._-]/', '_', (string)($solicitud['nombre_comprobante'] ?: 'comprobante'));
        header('X-Content-Type-Options: nosniff');
        header('Content-Type: ' . $solicitud['tipo_comprobante']);
        header('Content-Disposition: inline; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit();
    }

    /**
     * RF-60, RF-61, RF-62 y RF-63: Resolver solicitudes de reimpresión (Admin)
     */
    public function adminResolverReimpresion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?action=admin_reimpresiones');
            exit();
        }

        $id_solicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $decision = trim($_POST['decision'] ?? ''); // 'APROBADA' o 'RECHAZADA'
        $motivo_rechazo = trim($_POST['motivo_rechazo'] ?? '');
        $admin = AuthHelper::obtenerUsuario();

        try {
            if (!$id_solicitud || !in_array($decision, ['APROBADA', 'RECHAZADA'], true)) throw new Exception('La decisión indicada no es válida.');
            if ($decision === 'RECHAZADA' && $motivo_rechazo === '') throw new Exception('Indique el motivo del rechazo para el participante.');
            if (mb_strlen($motivo_rechazo) > 1000) throw new Exception('El motivo de rechazo no puede superar los 1.000 caracteres.');
            $solicitud = $this->reimpresionModel->obtenerPorId($id_solicitud);
            if (!$solicitud || $solicitud['estado'] !== 'PENDIENTE') throw new Exception('La solicitud ya fue atendida o no existe.');
            if ($decision === 'APROBADA') {
                $rutaComprobante = __DIR__ . '/../storage/comprobantes_reimpresion/' . basename((string)($solicitud['ruta_comprobante'] ?? ''));
                if (empty($solicitud['ruta_comprobante']) || !is_file($rutaComprobante)) throw new Exception('No es posible aprobar una solicitud sin comprobante de pago disponible.');
            }
            if (!$this->reimpresionModel->resolver($id_solicitud, (int)$admin['id_usuario'], $decision, $motivo_rechazo)) throw new Exception('La solicitud ya fue atendida o no existe.');

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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !AuthHelper::validarCsrf($_POST['csrf_token'] ?? null)) {
            header('Location: index.php?action=admin_reimpresiones');
            exit();
        }

        $id_solicitud = filter_input(INPUT_POST, 'id_solicitud', FILTER_VALIDATE_INT);
        $admin = AuthHelper::obtenerUsuario();

        if ($id_solicitud && $this->reimpresionModel->registrarEntrega($id_solicitud, (int)$admin['id_usuario'])) {
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
