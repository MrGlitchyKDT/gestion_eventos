<?php
require_once __DIR__ . '/../config/Database.php';

class PlantillaCertificado {
    private PDO $db;
    private const TAMANO_MAXIMO = 10485760;

    public function __construct() { $this->db = Database::getConnection(); }

    /** Compatibilidad para certificados históricos sin plantilla asociada. */
    public function obtenerPorEvento(int $idEvento): ?array {
        $stmt = $this->db->prepare("SELECT * FROM plantillas_certificado WHERE id_evento = :evento AND activa = 1 AND destinatario IN ('PARTICIPANTE', 'AMBOS') ORDER BY id_plantilla ASC LIMIT 1");
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetch() ?: null;
    }

    public function obtenerPorId(int $idPlantilla, bool $soloActiva = false): ?array {
        $sql = 'SELECT * FROM plantillas_certificado WHERE id_plantilla = :plantilla';
        if ($soloActiva) $sql .= ' AND activa = 1';
        $sql .= ' LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':plantilla' => $idPlantilla]);
        return $stmt->fetch() ?: null;
    }

    public function listarPorEvento(int $idEvento, bool $incluirArchivadas = true): array {
        $sql = 'SELECT * FROM plantillas_certificado WHERE id_evento = :evento';
        if (!$incluirArchivadas) $sql .= ' AND activa = 1';
        $sql .= ' ORDER BY activa DESC, fecha_registro DESC, id_plantilla DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetchAll();
    }

    public function guardar(int $idEvento, int $idUsuario, array $archivo, int $pagina, array $configuracion, string $nombrePlantilla, string $destinatario): int {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) throw new Exception('Seleccione una plantilla PDF válida.');
        if (($archivo['size'] ?? 0) < 1 || $archivo['size'] > self::TAMANO_MAXIMO) throw new Exception('La plantilla no puede superar 10 MB.');
        if (strtolower(pathinfo($archivo['name'] ?? '', PATHINFO_EXTENSION)) !== 'pdf' || (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']) !== 'application/pdf') throw new Exception('Solo se permiten archivos PDF.');
        $nombrePlantilla = mb_substr(trim($nombrePlantilla), 0, 150);
        if ($nombrePlantilla === '') throw new Exception('Indique un nombre para identificar la plantilla.');
        $destinatario = strtoupper(trim($destinatario));
        if (!in_array($destinatario, ['PARTICIPANTE', 'EXPOSITOR', 'AMBOS'], true)) throw new Exception('El destinatario de la plantilla no es válido.');
        $dir = $this->directorio(); if (!is_dir($dir)) mkdir($dir, 0755, true);
        $ruta = bin2hex(random_bytes(20)) . '.pdf';
        if (!move_uploaded_file($archivo['tmp_name'], $dir . $ruta)) throw new Exception('No se pudo guardar la plantilla PDF.');
        try {
            $sql = 'INSERT INTO plantillas_certificado (id_evento,nombre_plantilla,destinatario,ruta_archivo_pdf,nombre_archivo,pagina,configuracion_campos,id_usuario_subio,activa) VALUES (:evento,:titulo,:destinatario,:ruta,:nombre,:pagina,:config,:usuario,1)';
            $this->db->prepare($sql)->execute([':evento'=>$idEvento, ':titulo'=>$nombrePlantilla, ':destinatario'=>$destinatario, ':ruta'=>$ruta, ':nombre'=>mb_substr(basename($archivo['name']),0,255), ':pagina'=>$pagina, ':config'=>json_encode($configuracion, JSON_UNESCAPED_UNICODE), ':usuario'=>$idUsuario]);
            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) { @unlink($dir . $ruta); throw $e; }
    }

    public function rutaFisica(array $plantilla): string { return $this->directorio() . basename($plantilla['ruta_archivo_pdf']); }
    public function actualizarConfiguracion(int $idPlantilla, array $configuracion): void {
        $stmt = $this->db->prepare('UPDATE plantillas_certificado SET configuracion_campos = :config WHERE id_plantilla = :plantilla AND activa = 1');
        $stmt->execute([':config' => json_encode($configuracion, JSON_UNESCAPED_UNICODE), ':plantilla' => $idPlantilla]);
        if ($stmt->rowCount() < 1) throw new Exception('No se encontró una plantilla activa para este evento.');
    }

    public function archivar(int $idPlantilla): bool {
        $stmt = $this->db->prepare('UPDATE plantillas_certificado SET activa = 0 WHERE id_plantilla = :plantilla AND activa = 1');
        $stmt->execute([':plantilla' => $idPlantilla]);
        return $stmt->rowCount() === 1;
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
