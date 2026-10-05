<?php
require_once __DIR__ . '/../config/Database.php';

class SolicitudRecuperacionPassword {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Registra una petición sólo si el identificador corresponde a una cuenta activa. */
    public function solicitar(string $identificador): void {
        $identificador = trim($identificador);
        $stmtUsuario = $this->db->prepare('SELECT id_usuario FROM usuarios WHERE activo = 1 AND (LOWER(correo) = :correo OR ci = :ci) LIMIT 1');
        $stmtUsuario->execute([':correo' => mb_strtolower($identificador), ':ci' => $identificador]);
        $idUsuario = (int)$stmtUsuario->fetchColumn();
        if (!$idUsuario) return; // Evita revelar qué cuentas existen.

        $pendiente = $this->db->prepare("SELECT id_solicitud FROM solicitudes_recuperacion_password WHERE id_usuario = :usuario AND estado = 'PENDIENTE' LIMIT 1");
        $pendiente->execute([':usuario' => $idUsuario]);
        if ($pendiente->fetchColumn()) return;

        $insertar = $this->db->prepare("INSERT INTO solicitudes_recuperacion_password (id_usuario, estado, fecha_solicitud) VALUES (:usuario, 'PENDIENTE', NOW())");
        $insertar->execute([':usuario' => $idUsuario]);
    }

    public function listar(string $estado = ''): array {
        $sql = "SELECT sr.*, u.ci, u.nombres, u.apellidos, u.correo, u.telefono, r.nombre AS rol_nombre
                FROM solicitudes_recuperacion_password sr
                INNER JOIN usuarios u ON u.id_usuario = sr.id_usuario
                INNER JOIN roles r ON r.id_rol = u.id_rol
                WHERE (:estado_vacio = '' OR sr.estado = :estado)
                ORDER BY CASE sr.estado WHEN 'PENDIENTE' THEN 0 ELSE 1 END, sr.fecha_solicitud DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':estado_vacio' => $estado, ':estado' => $estado]);
        return $stmt->fetchAll();
    }

    public function obtenerPendiente(int $idSolicitud): ?array {
        $stmt = $this->db->prepare("SELECT sr.*, u.id_usuario, u.ci, u.nombres, u.apellidos, u.correo, u.activo
                                    FROM solicitudes_recuperacion_password sr
                                    INNER JOIN usuarios u ON u.id_usuario = sr.id_usuario
                                    WHERE sr.id_solicitud = :solicitud AND sr.estado = 'PENDIENTE' LIMIT 1");
        $stmt->execute([':solicitud' => $idSolicitud]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public function resolver(int $idSolicitud, int $idAdmin, string $estado, ?string $motivoRechazo = null): bool {
        if (!in_array($estado, ['APROBADA', 'RECHAZADA'], true)) throw new InvalidArgumentException('Estado de resolución inválido.');
        $stmt = $this->db->prepare("UPDATE solicitudes_recuperacion_password
                                    SET estado = :estado, motivo_rechazo = :motivo, id_usuario_resolucion = :admin, fecha_resolucion = NOW()
                                    WHERE id_solicitud = :solicitud AND estado = 'PENDIENTE'");
        $stmt->execute([
            ':estado' => $estado,
            ':motivo' => $estado === 'RECHAZADA' ? trim((string)$motivoRechazo) : null,
            ':admin' => $idAdmin,
            ':solicitud' => $idSolicitud,
        ]);
        return $stmt->rowCount() === 1;
    }

    public function pendientes(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM solicitudes_recuperacion_password WHERE estado = 'PENDIENTE'")->fetchColumn();
    }
}
