<?php
require_once __DIR__ . '/../config/Database.php';

class PlantillaCertificado {
    private PDO $db;
    private const TAMANO_MAXIMO = 10485760;

    public function __construct() { $this->db = Database::getConnection(); }

    public function obtenerPorEvento(int $idEvento): ?array {
        $stmt = $this->db->prepare('SELECT * FROM plantillas_certificado WHERE id_evento = :evento AND activa = 1 LIMIT 1');
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetch() ?: null;
    }

    public function guardar(int $idEvento, int $idUsuario, array $archivo, int $pagina, array $configuracion): void {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new Exception('Seleccione una plantilla PDF válida.');
        if (($archivo['size'] ?? 0) < 1 || $archivo['size'] > self::TAMANO_MAXIMO) throw new Exception('La plantilla no puede superar 10 MB.');
        if (strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION)) !== 'pdf' || (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']) !== 'application/pdf') throw new Exception('Solo se permiten archivos PDF.');
        $dir = $this->directorio(); if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ruta = bin2hex(random_bytes(20)) . '.pdf';
        if (!move_uploaded_file($archivo['tmp_name'], $dir . $ruta)) throw new Exception('No se pudo guardar la plantilla PDF.');
        $anterior = $this->obtenerPorEvento($idEvento);
        try {
            $sql = 'INSERT INTO plantillas_certificado (id_evento,ruta_archivo_pdf,nombre_archivo,pagina,configuracion_campos,id_usuario_subio,activa) VALUES (:evento,:ruta,:nombre,:pagina,:config,:usuario,1) ON DUPLICATE KEY UPDATE ruta_archivo_pdf=VALUES(ruta_archivo_pdf), nombre_archivo=VALUES(nombre_archivo), pagina=VALUES(pagina), configuracion_campos=VALUES(configuracion_campos), id_usuario_subio=VALUES(id_usuario_subio), activa=1';
            $this->db->prepare($sql)->execute([':evento'=>$idEvento, ':ruta'=>$ruta, ':nombre'=>mb_substr(basename($archivo['name']),0,255), ':pagina'=>$pagina, ':config'=>json_encode($configuracion, JSON_UNESCAPED_UNICODE), ':usuario'=>$idUsuario]);
            if ($anterior && is_file($this->directorio() . basename($anterior['ruta_archivo_pdf']))) unlink($this->directorio() . basename($anterior['ruta_archivo_pdf']));
        } catch (Throwable $e) { @unlink($dir . $ruta); throw $e; }
    }

    public function rutaFisica(array $plantilla): string { return $this->directorio() . basename($plantilla['ruta_archivo_pdf']); }
    public function actualizarConfiguracion(int $idEvento, array $configuracion): void {
        $stmt = $this->db->prepare('UPDATE plantillas_certificado SET configuracion_campos = :config WHERE id_evento = :evento AND activa = 1');
        $stmt->execute([':config' => json_encode($configuracion, JSON_UNESCAPED_UNICODE), ':evento' => $idEvento]);
        if ($stmt->rowCount() < 1) throw new Exception('No se encontró una plantilla activa para este evento.');
    }
    public function dimensionesPagina(array $plantilla): array {
        require_once __DIR__ . '/../vendor/autoload.php';
        $ruta = $this->rutaFisica($plantilla);
        if (!is_file($ruta)) throw new Exception('No se encontró el archivo de plantilla.');
        $pdf = new \setasign\Fpdi\Fpdi();
        $total = $pdf->setSourceFile($ruta);
        $pagina = min(max(1, (int)$plantilla['pagina']), $total);
        $plantillaPdf = $pdf->importPage($pagina);
        $medida = $pdf->getTemplateSize($plantillaPdf);
        return ['ancho' => (float)$medida['width'], 'alto' => (float)$medida['height']];
    }
    private function directorio(): string { return __DIR__ . '/../storage/plantillas_certificado/'; }
}
