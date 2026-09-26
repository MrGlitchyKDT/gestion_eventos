<?php
// models/SesionEvento.php
require_once __DIR__ . '/../config/Database.php';

class SesionEvento {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * RF-24: Registra una nueva sesión o clase para el evento
     */
    public function crear(array $datos): int {
        $sql = "INSERT INTO sesiones_evento (
                    id_evento, titulo, fecha, hora_inicio, hora_fin, lugar_especifico, estado
                ) VALUES (
                    :id_evento, :titulo, :fecha, :hora_inicio, :hora_fin, :lugar_especifico, 'PROGRAMADA'
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_evento'        => (int)$datos['id_evento'],
            ':titulo'           => trim($datos['titulo']),
            ':fecha'            => $datos['fecha'],
            ':hora_inicio'      => $datos['hora_inicio'],
            ':hora_fin'         => $datos['hora_fin'],
            ':lugar_especifico' => !empty($datos['lugar_especifico']) ? trim($datos['lugar_especifico']) : null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Modifica una sesión existente mientras esté programada
     */
    public function actualizar(int $id_sesion, array $datos): bool {
        $sql = "UPDATE sesiones_evento SET
                    titulo = :titulo,
                    fecha = :fecha,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    lugar_especifico = :lugar_especifico,
                    es_excepcion = CASE WHEN id_serie IS NOT NULL THEN 1 ELSE es_excepcion END
                WHERE id_sesion = :id_sesion AND estado = 'PROGRAMADA'";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo'           => trim($datos['titulo']),
            ':fecha'            => $datos['fecha'],
            ':hora_inicio'      => $datos['hora_inicio'],
            ':hora_fin'         => $datos['hora_fin'],
            ':lugar_especifico' => !empty($datos['lugar_especifico']) ? trim($datos['lugar_especifico']) : null,
            ':id_sesion'        => $id_sesion
        ]);
    }

    /**
     * Lista cronológica de sesiones de un evento
     */
    public function listarPorEvento(int $id_evento): array {
        $sql = "SELECT * FROM sesiones_evento 
                WHERE id_evento = :id_evento 
                ORDER BY fecha ASC, hora_inicio ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_evento' => $id_evento]);
        return $stmt->fetchAll();
    }

    /** Lista sesiones de varios eventos, agrupadas para la gestión administrativa. */
    public function listarAgrupadasPorEventos(array $idsEventos): array {
        $idsEventos = array_values(array_unique(array_filter(array_map('intval', $idsEventos))));
        if ($idsEventos === []) {
            return [];
        }

        $marcadores = implode(',', array_fill(0, count($idsEventos), '?'));
        $stmt = $this->db->prepare("SELECT * FROM sesiones_evento WHERE id_evento IN ({$marcadores}) ORDER BY fecha ASC, hora_inicio ASC");
        $stmt->execute($idsEventos);
        $resultado = [];
        foreach ($stmt->fetchAll() as $sesion) {
            $resultado[(int)$sesion['id_evento']][] = $sesion;
        }
        return $resultado;
    }

    public function obtenerPorId(int $id_sesion): ?array {
        $sql = "SELECT se.*, e.titulo AS evento_titulo, e.estado AS evento_estado
                FROM sesiones_evento se
                INNER JOIN eventos e ON se.id_evento = e.id_evento
                WHERE se.id_sesion = :id_sesion 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_sesion' => $id_sesion]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Cambia el estado de la sesión ('PROGRAMADA', 'EN_CURSO', 'CONCLUIDA', 'CANCELADA')
     */
    public function cambiarEstado(int $id_sesion, string $nuevoEstado): bool {
        $estadosPermitidos = ['PROGRAMADA', 'EN_CURSO', 'CONCLUIDA', 'CANCELADA'];
        if (!in_array($nuevoEstado, $estadosPermitidos, true)) {
            throw new InvalidArgumentException("Estado de sesión no válido: $nuevoEstado");
        }

        $sql = "UPDATE sesiones_evento SET estado = :estado WHERE id_sesion = :id_sesion";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado'    => $nuevoEstado,
            ':id_sesion' => $id_sesion
        ]);
    }

    /** Abre una ventana temporal de confirmación para los participantes. */
    public function abrirAsistencia(int $idSesion, int $idDocente, int $minutos): bool {
        if ($minutos < 5 || $minutos > 240) throw new InvalidArgumentException('La ventana debe durar entre 5 y 240 minutos.');
        $stmt = $this->db->prepare("UPDATE sesiones_evento
            SET estado = 'EN_CURSO', id_usuario_apertura = :docente,
                fecha_apertura_asistencia = NOW(),
                fecha_cierre_programada = DATE_ADD(NOW(), INTERVAL :minutos MINUTE),
                id_usuario_cierre = NULL, fecha_cierre_asistencia = NULL
            WHERE id_sesion = :sesion AND estado = 'PROGRAMADA'");
        $stmt->bindValue(':docente', $idDocente, PDO::PARAM_INT);
        $stmt->bindValue(':minutos', $minutos, PDO::PARAM_INT);
        $stmt->bindValue(':sesion', $idSesion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    /** Cierra la ventana y deja la sesión lista para el cálculo definitivo. */
    public function cerrarAsistencia(int $idSesion, int $idDocente): bool {
        $stmt = $this->db->prepare("UPDATE sesiones_evento
            SET estado = 'CONCLUIDA', id_usuario_cierre = :docente, fecha_cierre_asistencia = NOW()
            WHERE id_sesion = :sesion AND estado = 'EN_CURSO'");
        $stmt->execute([':docente' => $idDocente, ':sesion' => $idSesion]);
        return $stmt->rowCount() === 1;
    }

    public function eliminar(int $id_sesion): bool {
        $sql = "DELETE FROM sesiones_evento WHERE id_sesion = :id_sesion";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_sesion' => $id_sesion]);
    }

    /**
     * RF-51: Conteo de sesiones válidas para el cálculo de porcentaje de asistencia
     */
    public function contarSesionesValidas(int $id_evento): int {
        $sql = "SELECT COUNT(*) FROM sesiones_evento 
                WHERE id_evento = :id_evento AND estado = 'CONCLUIDA'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_evento' => $id_evento]);
        return (int)$stmt->fetchColumn();
    }
}
