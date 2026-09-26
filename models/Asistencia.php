<?php
// models/Asistencia.php
require_once __DIR__ . '/../config/Database.php';

class Asistencia {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * RF-23, RF-24 y RF-25: Asienta o modifica la asistencia individual por sesión
     */
    public function registrarOActualizar(int $id_sesion, int $id_inscripcion, string $estado, int $id_usuario_registro, ?string $observacion = null, string $origen = 'DOCENTE'): bool {
        $estadosPermitidos = ['PRESENTE', 'FALTA', 'ATRASO', 'JUSTIFICADO'];
        if (!in_array($estado, $estadosPermitidos, true)) {
            throw new InvalidArgumentException("Estado de asistencia no válido: $estado");
        }

        $sql = "INSERT INTO asistencias (
                    id_sesion, id_inscripcion, estado, id_usuario_registro, origen_registro, observacion, fecha_registro
                ) VALUES (
                    :id_sesion, :id_inscripcion, :estado, :id_usuario_registro, :origen, :observacion, NOW()
                )
                ON DUPLICATE KEY UPDATE 
                    estado = :estado_up,
                    id_usuario_registro = :id_usuario_up,
                    origen_registro = :origen_up,
                    observacion = :observacion_up,
                    fecha_registro = NOW()";

        $stmt = $this->db->prepare($sql);
        $ejecutado = $stmt->execute([
            ':id_sesion'             => $id_sesion,
            ':id_inscripcion'        => $id_inscripcion,
            ':estado'                => $estado,
            ':id_usuario_registro'   => $id_usuario_registro,
            ':origen'                 => $origen,
            ':observacion'           => $observacion,
            ':estado_up'             => $estado,
            ':id_usuario_up'         => $id_usuario_registro,
            ':origen_up'             => $origen,
            ':observacion_up'        => $observacion
        ]);

        if ($ejecutado) {
            // Obtener el ID del evento asociado para recalcular los porcentajes
            $sqlEvento = "SELECT id_evento FROM inscripciones WHERE id_inscripcion = :id_inscripcion LIMIT 1";
            $stmtEvt = $this->db->prepare($sqlEvento);
            $stmtEvt->execute([':id_inscripcion' => $id_inscripcion]);
            $idEvento = (int)$stmtEvt->fetchColumn();

            if ($idEvento > 0) {
                $this->calcularYActualizarPorcentaje($id_inscripcion, $idEvento);
            }
        }

        return $ejecutado;
    }

    /** Confirma presencia propia durante una ventana abierta por el docente. */
    public function confirmarParticipante(int $idSesion, int $idUsuario): void {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT se.id_sesion, se.id_evento, i.id_inscripcion
                FROM sesiones_evento se
                INNER JOIN inscripciones i ON i.id_evento = se.id_evento
                WHERE se.id_sesion = :sesion AND i.id_usuario = :usuario
                  AND i.estado = 'INSCRITO' AND se.estado = 'EN_CURSO'
                  AND se.fecha_apertura_asistencia IS NOT NULL
                  AND se.fecha_cierre_programada IS NOT NULL
                  AND NOW() BETWEEN se.fecha_apertura_asistencia AND se.fecha_cierre_programada
                FOR UPDATE");
            $stmt->execute([':sesion'=>$idSesion, ':usuario'=>$idUsuario]);
            $registro = $stmt->fetch();
            if (!$registro) throw new Exception('La asistencia no está abierta o no tiene una inscripción activa para esta sesión.');
            $insertar = $this->db->prepare("INSERT INTO asistencias
                (id_sesion,id_inscripcion,estado,id_usuario_registro,origen_registro,fecha_confirmacion_participante,fecha_registro)
                VALUES (:sesion,:inscripcion,'PRESENTE',:usuario,'PARTICIPANTE',NOW(),NOW())");
            try { $insertar->execute([':sesion'=>$idSesion, ':inscripcion'=>$registro['id_inscripcion'], ':usuario'=>$idUsuario]); }
            catch (PDOException $e) { if ((string)$e->getCode() === '23000') throw new Exception('Ya confirmó su asistencia para esta sesión.'); throw $e; }
            $this->calcularYActualizarPorcentaje((int)$registro['id_inscripcion'], (int)$registro['id_evento']);
            $this->db->commit();
        } catch (Throwable $e) { if ($this->db->inTransaction()) $this->db->rollBack(); throw $e; }
    }

    public function sesionesAbiertasPorUsuario(int $idUsuario, ?int $idEvento = null): array {
        $sql = "SELECT se.id_sesion, se.id_evento, se.titulo AS sesion_titulo, se.fecha, se.hora_inicio, se.hora_fin,
                       se.fecha_cierre_programada, e.codigo AS evento_codigo, e.titulo AS evento_titulo,
                       i.id_inscripcion, a.id_asistencia
                FROM sesiones_evento se
                INNER JOIN eventos e ON e.id_evento = se.id_evento
                INNER JOIN inscripciones i ON i.id_evento = se.id_evento AND i.id_usuario = :usuario AND i.estado = 'INSCRITO'
                LEFT JOIN asistencias a ON a.id_sesion = se.id_sesion AND a.id_inscripcion = i.id_inscripcion
                WHERE se.estado = 'EN_CURSO' AND se.fecha_apertura_asistencia <= NOW() AND se.fecha_cierre_programada >= NOW()";
        $params = [':usuario'=>$idUsuario];
        if ($idEvento) { $sql .= ' AND se.id_evento = :evento'; $params[':evento']=$idEvento; }
        $sql .= ' ORDER BY se.fecha_cierre_programada ASC';
        $stmt=$this->db->prepare($sql); $stmt->execute($params); return $stmt->fetchAll();
    }

    public function recalcularEvento(int $idEvento): void {
        $stmt = $this->db->prepare("SELECT id_inscripcion FROM inscripciones WHERE id_evento = :evento AND estado IN ('INSCRITO','ASISTIO','APROBADO')");
        $stmt->execute([':evento'=>$idEvento]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $idInscripcion) $this->calcularYActualizarPorcentaje((int)$idInscripcion, $idEvento);
    }

    /**
     * RF-51 y RF-52: Calcula el porcentaje de asistencia acumulado y habilita para certificación
     */
    public function calcularYActualizarPorcentaje(int $id_inscripcion, int $id_evento): array {
        // 1. Conteo de sesiones válidas (no canceladas)
        $sqlTotal = "SELECT COUNT(*) FROM sesiones_evento 
                     WHERE id_evento = :id_evento AND estado = 'CONCLUIDA'";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute([':id_evento' => $id_evento]);
        $totalSesiones = (int)$stmtTotal->fetchColumn();

        if ($totalSesiones === 0) {
            return ['porcentaje' => 0.00, 'habilitado' => false];
        }

        // 2. Conteo de asistencias válidas del participante (PRESENTE o JUSTIFICADO cuentan para cumplimiento)
        $sqlAsistidas = "SELECT COUNT(*) 
                         FROM asistencias a
                         INNER JOIN sesiones_evento se ON a.id_sesion = se.id_sesion
                         WHERE a.id_inscripcion = :id_inscripcion 
                           AND se.id_evento = :id_evento 
                           AND se.estado = 'CONCLUIDA'
                           AND a.estado IN ('PRESENTE', 'JUSTIFICADO')";
        $stmtAsistidas = $this->db->prepare($sqlAsistidas);
        $stmtAsistidas->execute([
            ':id_inscripcion' => $id_inscripcion,
            ':id_evento'      => $id_evento
        ]);
        $sesionesAsistidas = (int)$stmtAsistidas->fetchColumn();

        $porcentaje = round(($sesionesAsistidas / $totalSesiones) * 100, 2);

        // 3. Obtener regla de aprobación del evento
        $sqlRegla = "SELECT porcentaje_asistencia_minimo, emite_certificado, nota_minima_aprobacion 
                     FROM eventos WHERE id_evento = :id_evento LIMIT 1";
        $stmtRegla = $this->db->prepare($sqlRegla);
        $stmtRegla->execute([':id_evento' => $id_evento]);
        $regla = $stmtRegla->fetch();

        $minimoRequerido = (float)($regla['porcentaje_asistencia_minimo'] ?? 80.00);
        $habilitado = ($porcentaje >= $minimoRequerido) && ((int)$regla['emite_certificado'] === 1);

        // 4. Actualizar tabla de inscripciones
        $sqlActualizar = "UPDATE inscripciones 
                          SET porcentaje_asistencia = :porcentaje,
                              habilitado_certificado = :habilitado
                          WHERE id_inscripcion = :id_inscripcion";
        $stmtAct = $this->db->prepare($sqlActualizar);
        $stmtAct->execute([
            ':porcentaje'     => $porcentaje,
            ':habilitado'     => $habilitado ? 1 : 0,
            ':id_inscripcion' => $id_inscripcion
        ]);

        return [
            'total_sesiones'     => $totalSesiones,
            'sesiones_asistidas' => $sesionesAsistidas,
            'porcentaje'         => $porcentaje,
            'habilitado'         => $habilitado
        ];
    }

    /**
     * RF-22 y RF-48: Lista de asistencia para una sesión específica
     */
    public function listarPorSesion(int $id_sesion, int $id_evento): array {
        $sql = "SELECT 
                    i.id_inscripcion,
                    i.codigo_inscripcion,
                    u.ci,
                    u.nombres,
                    u.apellidos,
                    i.porcentaje_asistencia,
                    a.id_asistencia,
                    COALESCE(a.estado, 'SIN_REGISTRO') AS estado_asistencia,
                    a.observacion,
                    a.fecha_registro
                FROM inscripciones i
                INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
                LEFT JOIN asistencias a ON a.id_inscripcion = i.id_inscripcion AND a.id_sesion = :id_sesion
                WHERE i.id_evento = :id_evento AND i.estado = 'INSCRITO'
                ORDER BY u.apellidos ASC, u.nombres ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_sesion' => $id_sesion,
            ':id_evento' => $id_evento
        ]);
        return $stmt->fetchAll();
    }

    /** Informe detallado de todas las sesiones de un evento para exportación. */
    public function listarInformePorEvento(int $idEvento): array {
        $stmt = $this->db->prepare(
            "SELECT se.titulo AS sesion_titulo, se.fecha, se.hora_inicio, se.hora_fin,
                    u.ci, u.nombres, u.apellidos, u.correo,
                    COALESCE(a.estado, 'SIN_REGISTRO') AS estado_asistencia, a.observacion,
                    i.porcentaje_asistencia
             FROM sesiones_evento se
             INNER JOIN inscripciones i ON i.id_evento = se.id_evento AND i.estado = 'INSCRITO'
             INNER JOIN usuarios u ON u.id_usuario = i.id_usuario
             LEFT JOIN asistencias a ON a.id_sesion = se.id_sesion AND a.id_inscripcion = i.id_inscripcion
             WHERE se.id_evento = :evento
             ORDER BY se.fecha ASC, se.hora_inicio ASC, u.apellidos ASC, u.nombres ASC"
        );
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetchAll();
    }
}
