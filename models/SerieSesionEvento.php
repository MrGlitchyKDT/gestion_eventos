<?php
// models/SerieSesionEvento.php
require_once __DIR__ . '/../config/Database.php';

/** Gestiona patrones recurrentes y las sesiones individuales que producen. */
class SerieSesionEvento {
    private const MAX_SESIONES_GENERADAS = 366;
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function listarAgrupadasPorEventos(array $idsEventos): array {
        $idsEventos = array_values(array_unique(array_filter(array_map('intval', $idsEventos))));
        if ($idsEventos === []) return [];
        $marcadores = implode(',', array_fill(0, count($idsEventos), '?'));
        $sql = "SELECT ss.*, (SELECT GROUP_CONCAT(sd.dia_semana ORDER BY sd.dia_semana SEPARATOR ',')
                              FROM serie_sesiones_dias sd WHERE sd.id_serie = ss.id_serie) AS dias_semana
                FROM series_sesiones_evento ss
                WHERE ss.id_evento IN ({$marcadores})
                ORDER BY ss.fecha_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($idsEventos);
        $resultado = [];
        foreach ($stmt->fetchAll() as $serie) $resultado[(int)$serie['id_evento']][] = $serie;
        return $resultado;
    }

    public function obtenerPorId(int $idSerie): ?array {
        $stmt = $this->db->prepare("SELECT ss.*, (SELECT GROUP_CONCAT(sd.dia_semana ORDER BY sd.dia_semana SEPARATOR ',')
            FROM serie_sesiones_dias sd WHERE sd.id_serie = ss.id_serie) AS dias_semana
            FROM series_sesiones_evento ss WHERE ss.id_serie = :serie LIMIT 1");
        $stmt->execute([':serie' => $idSerie]);
        return $stmt->fetch() ?: null;
    }

    /** Crea la definición y todas sus sesiones futuras en una transacción. */
    public function crear(array $datos, int $idUsuario): array {
        $fechas = $this->generarFechas($datos);
        $this->db->beginTransaction();
        try {
            $insertarSerie = $this->db->prepare("INSERT INTO series_sesiones_evento
                (id_evento, titulo, fecha_inicio, fecha_fin, hora_inicio, hora_fin, lugar_especifico,
                 frecuencia, intervalo_recurrencia, estado, id_usuario_creador)
                VALUES (:evento, :titulo, :inicio, :fin, :hora_inicio, :hora_fin, :lugar,
                        :frecuencia, :intervalo, 'ACTIVA', :usuario)");
            $insertarSerie->execute($this->parametrosSerie($datos, $idUsuario));
            $idSerie = (int)$this->db->lastInsertId();
            $this->guardarDias($idSerie, $datos['dias_semana']);
            $cantidad = $this->crearSesiones($idSerie, $datos, $fechas);
            $this->db->commit();
            return ['id_serie' => $idSerie, 'cantidad' => $cantidad];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    /** Actualiza el patrón y reconstruye sólo sus sesiones futuras sin excepciones. */
    public function actualizar(int $idSerie, array $datos): array {
        $fechas = $this->generarFechas($datos);
        $this->db->beginTransaction();
        try {
            $serie = $this->obtenerPorIdBloqueado($idSerie);
            if (!$serie || $serie['estado'] !== 'ACTIVA') throw new InvalidArgumentException('La serie no existe o no está activa.');
            if ((int)$serie['id_evento'] !== (int)$datos['id_evento']) throw new InvalidArgumentException('La serie no pertenece al evento seleccionado.');

            $bloqueadas = $this->db->prepare("SELECT COUNT(*) FROM sesiones_evento se
                WHERE se.id_serie = :serie AND se.estado = 'PROGRAMADA' AND se.es_excepcion = 0
                  AND se.fecha >= CURDATE()
                  AND EXISTS (SELECT 1 FROM asistencias a WHERE a.id_sesion = se.id_sesion)");
            $bloqueadas->execute([':serie' => $idSerie]);
            if ((int)$bloqueadas->fetchColumn() > 0) throw new RuntimeException('Hay sesiones futuras con registros de asistencia y no pueden regenerarse.');

            $actualizar = $this->db->prepare("UPDATE series_sesiones_evento SET
                titulo = :titulo, fecha_inicio = :inicio, fecha_fin = :fin,
                hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar_especifico = :lugar,
                frecuencia = :frecuencia, intervalo_recurrencia = :intervalo
                WHERE id_serie = :serie");
            $parametros = $this->parametrosSerie($datos, 0);
            unset($parametros[':evento'], $parametros[':usuario']);
            $parametros[':serie'] = $idSerie;
            $actualizar->execute($parametros);

            $this->db->prepare('DELETE FROM serie_sesiones_dias WHERE id_serie = :serie')->execute([':serie' => $idSerie]);
            $this->guardarDias($idSerie, $datos['dias_semana']);
            $this->db->prepare("DELETE FROM sesiones_evento
                WHERE id_serie = :serie AND estado = 'PROGRAMADA' AND es_excepcion = 0 AND fecha >= CURDATE()")
                ->execute([':serie' => $idSerie]);
            $cantidad = $this->crearSesiones($idSerie, $datos, $fechas, true);
            $this->db->commit();
            return ['id_serie' => $idSerie, 'cantidad' => $cantidad];
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }

    private function obtenerPorIdBloqueado(int $idSerie): ?array {
        $stmt = $this->db->prepare('SELECT * FROM series_sesiones_evento WHERE id_serie = :serie FOR UPDATE');
        $stmt->execute([':serie' => $idSerie]);
        return $stmt->fetch() ?: null;
    }

    private function parametrosSerie(array $datos, int $idUsuario): array {
        return [
            ':evento' => (int)$datos['id_evento'], ':titulo' => $datos['titulo'],
            ':inicio' => $datos['fecha_inicio'], ':fin' => $datos['fecha_fin'],
            ':hora_inicio' => $datos['hora_inicio'], ':hora_fin' => $datos['hora_fin'],
            ':lugar' => $datos['lugar_especifico'] ?: null, ':frecuencia' => $datos['frecuencia'],
            ':intervalo' => (int)$datos['intervalo_recurrencia'], ':usuario' => $idUsuario,
        ];
    }

    private function guardarDias(int $idSerie, array $dias): void {
        if ($dias === []) return;
        $stmt = $this->db->prepare('INSERT INTO serie_sesiones_dias (id_serie, dia_semana) VALUES (:serie, :dia)');
        foreach ($dias as $dia) $stmt->execute([':serie' => $idSerie, ':dia' => $dia]);
    }

    private function crearSesiones(int $idSerie, array $datos, array $fechas, bool $soloFuturas = false): int {
        $insertar = $this->db->prepare("INSERT INTO sesiones_evento
            (id_evento, id_serie, titulo, fecha, hora_inicio, hora_fin, lugar_especifico, estado, es_excepcion)
            VALUES (:evento, :serie, :titulo, :fecha, :hora_inicio, :hora_fin, :lugar, 'PROGRAMADA', 0)");
        $cantidad = 0;
        foreach ($fechas as $fecha) {
            if ($soloFuturas && $fecha < date('Y-m-d')) continue;
            $this->validarSinSolapamiento($datos['id_evento'], $fecha, $datos['hora_inicio'], $datos['hora_fin']);
            $insertar->execute([
                ':evento' => $datos['id_evento'], ':serie' => $idSerie, ':titulo' => $datos['titulo'],
                ':fecha' => $fecha, ':hora_inicio' => $datos['hora_inicio'], ':hora_fin' => $datos['hora_fin'],
                ':lugar' => $datos['lugar_especifico'] ?: null,
            ]);
            $cantidad++;
        }
        return $cantidad;
    }

    private function validarSinSolapamiento(int $idEvento, string $fecha, string $horaInicio, string $horaFin): void {
        $stmt = $this->db->prepare("SELECT 1 FROM sesiones_evento
            WHERE id_evento = :evento AND fecha = :fecha AND estado <> 'CANCELADA'
              AND hora_inicio < :fin AND hora_fin > :inicio
            LIMIT 1 FOR UPDATE");
        $stmt->execute([':evento' => $idEvento, ':fecha' => $fecha, ':inicio' => $horaInicio, ':fin' => $horaFin]);
        if ($stmt->fetchColumn()) throw new RuntimeException("Existe una sesión que se cruza con el horario del {$fecha}.");
    }

    private function generarFechas(array $datos): array {
        $inicio = new DateTimeImmutable($datos['fecha_inicio']);
        $fin = new DateTimeImmutable($datos['fecha_fin']);
        $fechas = [];
        for ($fecha = $inicio; $fecha <= $fin; $fecha = $fecha->modify('+1 day')) {
            $diasDesdeInicio = (int)$inicio->diff($fecha)->format('%a');
            $corresponde = $datos['frecuencia'] === 'DIARIA'
                ? $diasDesdeInicio % $datos['intervalo_recurrencia'] === 0
                : in_array((int)$fecha->format('N'), $datos['dias_semana'], true)
                    && (int)floor($diasDesdeInicio / 7) % $datos['intervalo_recurrencia'] === 0;
            if ($corresponde) $fechas[] = $fecha->format('Y-m-d');
            if (count($fechas) > self::MAX_SESIONES_GENERADAS) throw new InvalidArgumentException('La serie excede el máximo de 366 sesiones. Reduzca el periodo o ajuste la frecuencia.');
        }
        if ($fechas === []) throw new InvalidArgumentException('La recurrencia elegida no genera sesiones dentro del periodo indicado.');
        return $fechas;
    }
}
