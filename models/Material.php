<?php
// models/Material.php
require_once __DIR__ . '/../config/Database.php';

class Material {
    private PDO $db;
    private const TAMANO_MAXIMO = 20971520; // 20 MB
    private const TIPOS_PERMITIDOS = [
        'pdf'  => ['application/pdf'],
        'zip'  => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
        'rar'  => ['application/vnd.rar', 'application/x-rar-compressed', 'application/octet-stream'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'txt'  => ['text/plain'],
    ];

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * RF-28: Procesa la subida física del archivo y guarda su referencia
     */
    public function subir(int $id_evento, int $id_usuario, string $titulo, ?string $descripcion, array $archivo): int {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al cargar el archivo en el servidor (Código: {$archivo['error']}).");
        }

        // Límite de 20 MB por archivo
        if ($archivo['size'] < 1 || $archivo['size'] > self::TAMANO_MAXIMO) {
            throw new Exception("El archivo excede el tamaño máximo permitido de 20MB.");
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!isset(self::TIPOS_PERMITIDOS[$extension])) {
            throw new Exception("Tipo de archivo no permitido. Solo se aceptan: PDF, ZIP, RAR, PPTX, DOCX, XLSX y TXT.");
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if ($mime === false || !in_array($mime, self::TIPOS_PERMITIDOS[$extension], true)) {
            throw new Exception('El contenido del archivo no coincide con el formato indicado.');
        }

        // Los archivos se almacenan fuera del directorio público y se sirven mediante una ruta autorizada.
        $directorioDestino = $this->directorioAlmacenamiento();
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $nombreGuardado = bin2hex(random_bytes(20)) . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreGuardado;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            throw new Exception("No se pudo mover el archivo al directorio de almacenamiento.");
        }

        $sql = "INSERT INTO materiales_evento (
                    id_evento, id_usuario_subio, titulo, descripcion, ruta_archivo, tipo_archivo, tamano_bytes, fecha_publicacion
                ) VALUES (
                    :id_evento, :id_usuario, :titulo, :descripcion, :ruta, :tipo, :tamano, NOW()
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_evento'    => $id_evento,
            ':id_usuario'   => $id_usuario,
            ':titulo'       => mb_substr(trim($titulo), 0, 150),
            ':descripcion' => !empty($descripcion) ? trim($descripcion) : null,
            ':ruta'         => $nombreGuardado,
            ':tipo'         => strtoupper($extension),
            ':tamano'       => (int)$archivo['size']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /** Recibe el campo HTML materiales[] y registra cada archivo seleccionado. */
    public function subirMultiples(int $idEvento, int $idUsuario, array $archivos): int {
        if (!isset($archivos['name'])) {
            return 0;
        }

        $nombres = is_array($archivos['name']) ? $archivos['name'] : [$archivos['name']];
        $total = 0;
        foreach ($nombres as $indice => $nombre) {
            $archivo = [
                'name' => $nombre,
                'type' => is_array($archivos['type'] ?? null) ? ($archivos['type'][$indice] ?? '') : ($archivos['type'] ?? ''),
                'tmp_name' => is_array($archivos['tmp_name'] ?? null) ? ($archivos['tmp_name'][$indice] ?? '') : ($archivos['tmp_name'] ?? ''),
                'error' => is_array($archivos['error'] ?? null) ? ($archivos['error'][$indice] ?? UPLOAD_ERR_NO_FILE) : ($archivos['error'] ?? UPLOAD_ERR_NO_FILE),
                'size' => is_array($archivos['size'] ?? null) ? ($archivos['size'][$indice] ?? 0) : ($archivos['size'] ?? 0),
            ];
            if ($archivo['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $titulo = pathinfo(basename((string)$archivo['name']), PATHINFO_FILENAME) ?: 'Material del evento';
            $this->subir($idEvento, $idUsuario, $titulo, null, $archivo);
            $total++;
        }
        return $total;
    }

    /**
     * RF-28: Consulta de materiales disponibles para un evento
     */
    public function listarPorEvento(int $id_evento): array {
        $sql = "SELECT 
                    m.*,
                    u.nombres, u.apellidos
                FROM materiales_evento m
                INNER JOIN usuarios u ON m.id_usuario_subio = u.id_usuario
                WHERE m.id_evento = :id_evento
                ORDER BY m.fecha_publicacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_evento' => $id_evento]);
        return $stmt->fetchAll();
    }

    /** Lista materiales para varios eventos y los agrupa por id_evento. */
    public function listarAgrupadosPorEventos(array $idsEventos): array {
        $idsEventos = array_values(array_unique(array_filter(array_map('intval', $idsEventos))));
        if ($idsEventos === []) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($idsEventos), '?'));
        $stmt = $this->db->prepare("SELECT id_material, id_evento, titulo, tipo_archivo, tamano_bytes FROM materiales_evento WHERE id_evento IN ({$marcadores}) ORDER BY fecha_publicacion DESC");
        $stmt->execute($idsEventos);
        $resultado = [];
        foreach ($stmt->fetchAll() as $material) {
            $resultado[(int)$material['id_evento']][] = $material;
        }
        return $resultado;
    }

    public function obtenerPorId(int $idMaterial): ?array {
        $stmt = $this->db->prepare('SELECT * FROM materiales_evento WHERE id_material = :id LIMIT 1');
        $stmt->execute([':id' => $idMaterial]);
        return $stmt->fetch() ?: null;
    }

    public function usuarioPuedeDescargar(int $idEvento, int $idUsuario): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM inscripciones WHERE id_evento = :evento AND id_usuario = :usuario AND estado IN ('INSCRITO', 'ASISTIO', 'APROBADO', 'REPROBADO') LIMIT 1");
        $stmt->execute([':evento' => $idEvento, ':usuario' => $idUsuario]);
        return (bool)$stmt->fetchColumn();
    }

    public function rutaFisica(array $material): string {
        return $this->directorioAlmacenamiento() . basename((string)$material['ruta_archivo']);
    }

    public function eliminar(int $id_material): bool {
        $sql = "SELECT ruta_archivo FROM materiales_evento WHERE id_material = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_material]);
        $material = $stmt->fetch();

        if ($material) {
            $archivoFisico = $this->rutaFisica($material);
            if (file_exists($archivoFisico)) {
                unlink($archivoFisico);
            }

            $sqlDel = "DELETE FROM materiales_evento WHERE id_material = :id";
            $stmtDel = $this->db->prepare($sqlDel);
            return $stmtDel->execute([':id' => $id_material]);
        }
        return false;
    }

    private function directorioAlmacenamiento(): string {
        return __DIR__ . '/../storage/materiales/';
    }
}
