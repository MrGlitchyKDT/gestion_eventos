<?php
// controllers/ReporteController.php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class ReporteController {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** Filtros exclusivos del listado "Reportes por evento". */
    public function normalizarFiltros(array $entrada): array {
        $normalizarFecha = static function (mixed $valor): ?string {
            $valor = trim((string)$valor);
            if ($valor === '') return null;
            $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
            return $fecha && $fecha->format('Y-m-d') === $valor ? $valor : null;
        };
        $idArea = filter_var($entrada['id_categoria'] ?? null, FILTER_VALIDATE_INT);
        $idTipo = filter_var($entrada['id_tipo_evento'] ?? null, FILTER_VALIDATE_INT);
        $habilitado = strtoupper(trim((string)($entrada['habilitado'] ?? '')));
        $buscar = mb_substr(trim((string)($entrada['buscar'] ?? '')), 0, 150);
        return [
            'fecha_desde' => $normalizarFecha($entrada['fecha_desde'] ?? null),
            'fecha_hasta' => $normalizarFecha($entrada['fecha_hasta'] ?? null),
            'id_categoria' => $idArea && $idArea > 0 ? (int)$idArea : null,
            'id_tipo_evento' => $idTipo && $idTipo > 0 ? (int)$idTipo : null,
            'habilitado' => in_array($habilitado, ['SI', 'NO'], true) ? $habilitado : '',
            'buscar' => $buscar,
        ];
    }

    /** Filtros independientes para el bloque demográfico Datos de eventos. */
    public function normalizarFiltrosDatos(array $entrada): array {
        return $this->normalizarFiltros([
            'fecha_desde' => $entrada['datos_fecha_desde'] ?? null,
            'fecha_hasta' => $entrada['datos_fecha_hasta'] ?? null,
            'id_categoria' => $entrada['datos_id_categoria'] ?? null,
            'id_tipo_evento' => $entrada['datos_id_tipo_evento'] ?? null,
            'habilitado' => $entrada['datos_habilitado'] ?? null,
            'buscar' => $entrada['datos_buscar'] ?? null,
        ]);
    }

    /** Filtros exclusivos de la analítica mensual y sus casillas. */
    public function normalizarFiltrosResumen(array $entrada): array {
        $anioSeleccionado = filter_var($entrada['resumen_anio'] ?? null, FILTER_VALIDATE_INT);
        $numeroMesSeleccionado = filter_var($entrada['resumen_mes_num'] ?? null, FILTER_VALIDATE_INT);
        if ($anioSeleccionado && $anioSeleccionado >= 2000 && $anioSeleccionado <= 2100
            && $numeroMesSeleccionado && $numeroMesSeleccionado >= 1 && $numeroMesSeleccionado <= 12) {
            $mes = sprintf('%04d-%02d', $anioSeleccionado, $numeroMesSeleccionado);
        } else {
            $mes = trim((string)($entrada['resumen_mes'] ?? $entrada['mes'] ?? date('Y-m')));
        }
        // El selector visual usa una fecha para disponer de calendario en todos los navegadores.
        // Cualquier día seleccionado representa el mes completo de la analítica.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $mes)) $mes = substr($mes, 0, 7);
        $fecha = DateTimeImmutable::createFromFormat('!Y-m', $mes);
        if (!$fecha || $fecha->format('Y-m') !== $mes) $fecha = new DateTimeImmutable('first day of this month');
        $filtros = [
            'mes' => $fecha->format('Y-m'),
            'desde' => $fecha->format('Y-m-01'),
            'hasta' => $fecha->modify('last day of this month')->format('Y-m-d'),
        ];
        $normalizarIds = static function (mixed $valores): array {
            $ids = [];
            foreach (is_array($valores) ? $valores : [$valores] as $valor) {
                $id = filter_var($valor, FILTER_VALIDATE_INT);
                if ($id && $id > 0) $ids[$id] = (int)$id;
            }
            return array_values($ids);
        };

        // null significa que no se aplicó el formulario y, por tanto, se incluyen todos.
        // Un arreglo vacío representa que se desmarcaron todas las casillas.
        $filtros['areas_seleccionadas'] = isset($entrada['resumen_areas_present'])
            ? $normalizarIds($entrada['resumen_areas'] ?? [])
            : null;
        $filtros['tipos_seleccionados'] = isset($entrada['resumen_tipos_present'])
            ? $normalizarIds($entrada['resumen_tipos'] ?? [])
            : null;
        return $filtros;
    }

    public function listarAreas(): array {
        return $this->db->query('SELECT id_categoria, nombre, activo FROM categorias ORDER BY nombre ASC')->fetchAll();
    }

    public function listarTipos(): array {
        return $this->db->query('SELECT id_tipo_evento, nombre, activo FROM tipos_evento ORDER BY nombre ASC')->fetchAll();
    }

    /** Lista eventos; el rango opcional incluye aquellos que se cruzan con las fechas indicadas. */
    public function eventosDelPeriodo(array $filtros, ?int $limite = null): array {
        $filtros = $this->normalizarFiltros($filtros);
        $where = [];
        $params = [];
        if ($filtros['fecha_hasta']) {
            $where[] = 'e.fecha_inicio <= :hasta';
            $params[':hasta'] = $filtros['fecha_hasta'];
        }
        if ($filtros['fecha_desde']) {
            $where[] = 'e.fecha_fin >= :desde';
            $params[':desde'] = $filtros['fecha_desde'];
        }
        if ($filtros['id_categoria']) {
            $where[] = 'e.id_categoria = :area';
            $params[':area'] = $filtros['id_categoria'];
        }
        if ($filtros['id_tipo_evento']) {
            $where[] = 'e.id_tipo_evento = :tipo';
            $params[':tipo'] = $filtros['id_tipo_evento'];
        }
        if ($filtros['habilitado'] === 'SI') {
            $where[] = "e.estado = 'PUBLICADO'";
        } elseif ($filtros['habilitado'] === 'NO') {
            $where[] = "e.estado <> 'PUBLICADO'";
        }
        if ($filtros['buscar'] !== '') {
            $where[] = 'e.titulo LIKE :buscar';
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        $sql = "SELECT e.id_evento, e.codigo, e.titulo, e.modalidad, e.estado, e.fecha_inicio, e.fecha_fin,
                       e.fecha_inicio_inscripcion, e.fecha_fin_inscripcion, e.cupo_maximo,
                       te.nombre AS tipo_evento, c.nombre AS area,
                       (SELECT COUNT(*) FROM inscripciones i WHERE i.id_evento = e.id_evento AND i.estado <> 'CANCELADO') AS inscritos_actuales,
                       (SELECT COUNT(*) FROM sesiones_evento se WHERE se.id_evento = e.id_evento) AS total_sesiones,
                       (SELECT COUNT(*) FROM certificados cert WHERE cert.id_evento = e.id_evento AND cert.estado = 'EMITIDO') AS certificados_emitidos
                FROM eventos e
                INNER JOIN tipos_evento te ON te.id_tipo_evento = e.id_tipo_evento
                INNER JOIN categorias c ON c.id_categoria = e.id_categoria
                " . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . "
                ORDER BY e.fecha_inicio ASC, e.titulo ASC";
        if ($limite !== null) {
            $sql .= ' LIMIT ' . max(1, min(1000, (int)$limite));
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Eventos disponibles para el análisis demográfico, sin limitar por período. */
    public function eventosParaDatos(array $filtros = [], ?int $limite = null): array {
        $filtros = $this->normalizarFiltros($filtros);
        $where = [];
        $params = [];
        if ($filtros['fecha_hasta']) {
            $where[] = 'e.fecha_inicio <= :hasta';
            $params[':hasta'] = $filtros['fecha_hasta'];
        }
        if ($filtros['fecha_desde']) {
            $where[] = 'e.fecha_fin >= :desde';
            $params[':desde'] = $filtros['fecha_desde'];
        }
        if ($filtros['id_categoria']) {
            $where[] = 'e.id_categoria = :area';
            $params[':area'] = $filtros['id_categoria'];
        }
        if ($filtros['id_tipo_evento']) {
            $where[] = 'e.id_tipo_evento = :tipo';
            $params[':tipo'] = $filtros['id_tipo_evento'];
        }
        if ($filtros['habilitado'] === 'SI') {
            $where[] = "e.estado = 'PUBLICADO'";
        } elseif ($filtros['habilitado'] === 'NO') {
            $where[] = "e.estado <> 'PUBLICADO'";
        }
        if ($filtros['buscar'] !== '') {
            $where[] = 'e.titulo LIKE :buscar';
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }
        $sql = "SELECT e.id_evento, e.codigo, e.titulo, e.fecha_inicio, e.fecha_fin,
                       te.nombre AS tipo_evento, c.nombre AS area,
                       COUNT(i.id_inscripcion) AS total_inscritos
                FROM eventos e
                INNER JOIN tipos_evento te ON te.id_tipo_evento = e.id_tipo_evento
                INNER JOIN categorias c ON c.id_categoria = e.id_categoria
                LEFT JOIN inscripciones i ON i.id_evento = e.id_evento AND i.estado <> 'CANCELADO'
                " . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . "
                GROUP BY e.id_evento, e.codigo, e.titulo, e.fecha_inicio, e.fecha_fin, te.nombre, c.nombre
                ORDER BY e.fecha_inicio DESC, e.titulo ASC";
        if ($limite !== null) {
            $sql .= ' LIMIT ' . max(1, min(1000, (int)$limite));
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Distribuciones demográficas de las personas inscritas activamente en un evento. */
    public function datosDemograficosPorEvento(int $idEvento): array {
        $consultarDistribucion = function (string $campo) use ($idEvento): array {
            $stmt = $this->db->prepare(
                "SELECT COALESCE(NULLIF(u.{$campo}, ''), 'SIN_REGISTRO') AS clave,
                        COUNT(DISTINCT i.id_usuario) AS total
                 FROM inscripciones i
                 INNER JOIN usuarios u ON u.id_usuario = i.id_usuario
                 WHERE i.id_evento = :evento AND i.estado <> 'CANCELADO'
                 GROUP BY COALESCE(NULLIF(u.{$campo}, ''), 'SIN_REGISTRO')"
            );
            $stmt->execute([':evento' => $idEvento]);
            return $stmt->fetchAll();
        };

        $formatear = static function (array $filas, array $etiquetas): array {
            $porClave = [];
            foreach ($filas as $fila) $porClave[(string)$fila['clave']] = (int)$fila['total'];
            $resultado = [];
            foreach ($etiquetas as $clave => $etiqueta) {
                if (isset($porClave[$clave]) && $porClave[$clave] > 0) {
                    $resultado[] = ['etiqueta' => $etiqueta, 'total' => $porClave[$clave]];
                    unset($porClave[$clave]);
                }
            }
            foreach ($porClave as $clave => $total) {
                if ($total > 0) $resultado[] = ['etiqueta' => ucwords(mb_strtolower(str_replace('_', ' ', $clave))), 'total' => $total];
            }
            return $resultado;
        };

        $genero = $formatear($consultarDistribucion('genero'), [
            'FEMENINO' => 'Femenino',
            'MASCULINO' => 'Masculino',
            'NO_BINARIO' => 'No binario',
            'PREFIERO_NO_INDICAR' => 'Prefiere no indicar',
            'SIN_REGISTRO' => 'Sin registro',
        ]);
        $condicionAcademica = $formatear($consultarDistribucion('condicion_academica'), [
            'ESTUDIANTE' => 'Estudiante',
            'PROFESIONAL' => 'Profesional',
            'SIN_REGISTRO' => 'Sin registro',
        ]);

        $edadesStmt = $this->db->prepare(
            "SELECT TIMESTAMPDIFF(YEAR, u.fecha_nacimiento, CURDATE()) AS edad
             FROM inscripciones i
             INNER JOIN usuarios u ON u.id_usuario = i.id_usuario
             WHERE i.id_evento = :evento AND i.estado <> 'CANCELADO'"
        );
        $edadesStmt->execute([':evento' => $idEvento]);
        $rangosEdad = [];
        foreach ($edadesStmt->fetchAll(PDO::FETCH_COLUMN) as $edad) {
            $edad = filter_var($edad, FILTER_VALIDATE_INT);
            $etiqueta = ($edad === false || $edad < 0 || $edad > 120)
                ? 'Sin registro'
                : (intdiv((int)$edad, 10) * 10) . '–' . (intdiv((int)$edad, 10) * 10 + 9) . ' años';
            $rangosEdad[$etiqueta] = ($rangosEdad[$etiqueta] ?? 0) + 1;
        }
        uksort($rangosEdad, static function (string $a, string $b): int {
            if ($a === 'Sin registro') return 1;
            if ($b === 'Sin registro') return -1;
            return (int)$a <=> (int)$b;
        });
        $edad = array_map(static fn(string $etiqueta, int $total): array => ['etiqueta' => $etiqueta, 'total' => $total], array_keys($rangosEdad), array_values($rangosEdad));

        return [
            'total' => array_sum(array_column($genero, 'total')),
            'genero' => $genero,
            'condicion_academica' => $condicionAcademica,
            'edad' => $edad,
        ];
    }

    /** Respuesta JSON para actualizar los gráficos del bloque Datos de eventos. */
    public function datosDemograficosEventoJson(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $evento = $idEvento ? $this->obtenerEvento($idEvento) : null;
        header('Content-Type: application/json; charset=UTF-8');
        if (!$evento) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'mensaje' => 'El evento solicitado no existe.'], JSON_UNESCAPED_UNICODE);
            exit();
        }
        echo json_encode([
            'ok' => true,
            'evento' => ['id' => (int)$evento['id_evento'], 'titulo' => $evento['titulo']],
            'datos' => $this->datosDemograficosPorEvento((int)$idEvento),
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    /** Lista completa de eventos para el módulo de reportes. */
    public function listaEventos(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $filtrosReporte = $this->normalizarFiltros($_GET);
        $areasReporte = $this->listarAreas();
        $tiposReporte = $this->listarTipos();
        $eventosReporte = $this->eventosDelPeriodo($filtrosReporte);
        require __DIR__ . '/../views/admin/reportes/eventos.php';
    }

    /** Lista completa exclusiva del bloque Datos de eventos. */
    public function listaEventosDatos(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $filtrosDatos = $this->normalizarFiltrosDatos($_GET);
        $areasReporte = $this->listarAreas();
        $tiposReporte = $this->listarTipos();
        $eventosDatos = $this->eventosParaDatos($filtrosDatos);
        require __DIR__ . '/../views/admin/reportes/datos_eventos.php';
    }

    /** Consolidado mensual para el gráfico circular y el detalle de áreas. */
    public function resumenPorAreas(array $filtros): array {
        $filtros = array_key_exists('areas_seleccionadas', $filtros)
            ? $filtros
            : $this->normalizarFiltrosResumen($filtros);
        $where = [];
        $condicionesEvento = ['e.id_categoria = c.id_categoria', 'e.fecha_inicio <= :hasta', 'e.fecha_fin >= :desde'];
        $params = [
            ':desde' => $filtros['desde'], ':hasta' => $filtros['hasta'],
            ':desde_inscripcion' => $filtros['desde'], ':hasta_inscripcion' => $filtros['hasta'],
        ];
        if ($filtros['areas_seleccionadas'] !== null) {
            if (!$filtros['areas_seleccionadas']) {
                $where[] = '1 = 0';
            } else {
                $marcadores = [];
                foreach ($filtros['areas_seleccionadas'] as $indice => $id) {
                    $marcador = ':resumen_area_' . $indice;
                    $marcadores[] = $marcador;
                    $params[$marcador] = $id;
                }
                $where[] = 'c.id_categoria IN (' . implode(', ', $marcadores) . ')';
            }
        }
        if ($filtros['tipos_seleccionados'] !== null) {
            if (!$filtros['tipos_seleccionados']) {
                $condicionesEvento[] = '1 = 0';
            } else {
                $marcadores = [];
                foreach ($filtros['tipos_seleccionados'] as $indice => $id) {
                    $marcador = ':resumen_tipo_' . $indice;
                    $marcadores[] = $marcador;
                    $params[$marcador] = $id;
                }
                $condicionesEvento[] = 'e.id_tipo_evento IN (' . implode(', ', $marcadores) . ')';
            }
        }
        $sql = "SELECT c.id_categoria, c.nombre AS area,
                       COUNT(e.id_evento) AS total_eventos,
                       COALESCE(SUM(e.estado = 'PUBLICADO'), 0) AS publicados,
                       COALESCE(SUM(e.estado = 'FINALIZADO'), 0) AS concluidos,
                       COALESCE(SUM(CASE WHEN UPPER(te.nombre) = 'CURSO'
                                            AND e.estado = 'PUBLICADO'
                                            AND e.fecha_inicio_inscripcion <= :hasta_inscripcion
                                            AND e.fecha_fin_inscripcion >= :desde_inscripcion
                                         THEN 1 ELSE 0 END), 0) AS cursos_habilitados
                FROM categorias c
                LEFT JOIN eventos e ON " . implode(' AND ', $condicionesEvento) . "
                LEFT JOIN tipos_evento te ON te.id_tipo_evento = e.id_tipo_evento
                " . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . "
                GROUP BY c.id_categoria, c.nombre
                ORDER BY total_eventos DESC, c.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Distribución mensual de eventos por tipo, con las mismas casillas del resumen. */
    public function resumenPorTipos(array $filtros): array {
        $filtros = array_key_exists('tipos_seleccionados', $filtros)
            ? $filtros
            : $this->normalizarFiltrosResumen($filtros);
        $where = [];
        $condicionesEvento = ['e.id_tipo_evento = te.id_tipo_evento', 'e.fecha_inicio <= :hasta', 'e.fecha_fin >= :desde'];
        $params = [':desde' => $filtros['desde'], ':hasta' => $filtros['hasta']];

        if ($filtros['areas_seleccionadas'] !== null) {
            if (!$filtros['areas_seleccionadas']) {
                $condicionesEvento[] = '1 = 0';
            } else {
                $marcadores = [];
                foreach ($filtros['areas_seleccionadas'] as $indice => $id) {
                    $marcador = ':tipo_area_' . $indice;
                    $marcadores[] = $marcador;
                    $params[$marcador] = $id;
                }
                $condicionesEvento[] = 'e.id_categoria IN (' . implode(', ', $marcadores) . ')';
            }
        }
        if ($filtros['tipos_seleccionados'] !== null) {
            if (!$filtros['tipos_seleccionados']) {
                $where[] = '1 = 0';
            } else {
                $marcadores = [];
                foreach ($filtros['tipos_seleccionados'] as $indice => $id) {
                    $marcador = ':tipo_seleccionado_' . $indice;
                    $marcadores[] = $marcador;
                    $params[$marcador] = $id;
                }
                $where[] = 'te.id_tipo_evento IN (' . implode(', ', $marcadores) . ')';
            }
        }

        $sql = "SELECT te.id_tipo_evento, te.nombre AS tipo,
                       COUNT(e.id_evento) AS total_eventos,
                       COUNT(DISTINCT e.id_categoria) AS total_areas
                FROM tipos_evento te
                LEFT JOIN eventos e ON " . implode(' AND ', $condicionesEvento) . "
                " . ($where ? 'WHERE ' . implode(' AND ', $where) : '') . "
                GROUP BY te.id_tipo_evento, te.nombre
                ORDER BY total_eventos DESC, te.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function sesionesConResumen(int $idEvento): array {
        $sql = "SELECT se.id_sesion, se.titulo, se.fecha, se.hora_inicio, se.hora_fin, se.estado,
                       COUNT(a.id_asistencia) AS registros,
                       COALESCE(SUM(a.estado IN ('PRESENTE', 'JUSTIFICADO')), 0) AS asistencias_validas,
                       COALESCE(SUM(a.estado = 'FALTA'), 0) AS faltas
                FROM sesiones_evento se
                LEFT JOIN asistencias a ON a.id_sesion = se.id_sesion
                WHERE se.id_evento = :evento
                GROUP BY se.id_sesion, se.titulo, se.fecha, se.hora_inicio, se.hora_fin, se.estado
                ORDER BY se.fecha ASC, se.hora_inicio ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetchAll();
    }

    /** Lista descargable de una sesión, conservando todos los inscritos del evento. */
    public function reporteAsistenciaSesion(int $idSesion, int $idEvento): array {
        $sql = "SELECT se.titulo AS sesion_titulo, se.fecha, se.hora_inicio, se.hora_fin, se.estado AS estado_sesion,
                       i.codigo_inscripcion, i.estado AS estado_inscripcion, i.porcentaje_asistencia,
                       u.ci, u.nombres, u.apellidos, u.correo,
                       COALESCE(a.estado, 'SIN_REGISTRO') AS estado_asistencia, a.observacion, a.fecha_registro
                FROM sesiones_evento se
                INNER JOIN inscripciones i ON i.id_evento = se.id_evento
                INNER JOIN usuarios u ON u.id_usuario = i.id_usuario
                LEFT JOIN asistencias a ON a.id_sesion = se.id_sesion AND a.id_inscripcion = i.id_inscripcion
                WHERE se.id_sesion = :sesion AND se.id_evento = :evento
                ORDER BY u.apellidos ASC, u.nombres ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':sesion' => $idSesion, ':evento' => $idEvento]);
        return $stmt->fetchAll();
    }

    public function tiposCertificadoPorEvento(int $idEvento): array {
        $stmt = $this->db->prepare("SELECT c.id_plantilla, COALESCE(p.nombre_plantilla, 'Certificado histórico') AS nombre_certificado,
                                           COALESCE(p.destinatario, GROUP_CONCAT(DISTINCT c.tipo_participacion SEPARATOR ', ')) AS destinatario, COUNT(*) AS total
                                    FROM certificados c
                                    LEFT JOIN plantillas_certificado p ON p.id_plantilla = c.id_plantilla
                                    WHERE c.id_evento = :evento
                                    GROUP BY c.id_plantilla, p.nombre_plantilla, p.destinatario
                                    ORDER BY nombre_certificado ASC");
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetchAll();
    }

    public function certificadosPorEvento(int $idEvento, int|string|null $filtroCertificado = null): array {
        $where = ['c.id_evento = :evento'];
        $params = [':evento' => $idEvento];
        if ($filtroCertificado === 'historico') {
            $where[] = 'c.id_plantilla IS NULL';
        } elseif (is_numeric($filtroCertificado) && (int)$filtroCertificado > 0) {
            $where[] = 'c.id_plantilla = :plantilla';
            $params[':plantilla'] = (int)$filtroCertificado;
        }
        $sql = "SELECT c.id_certificado, c.codigo_unico, c.tipo_participacion, c.fecha_emision, c.estado,
                       c.id_plantilla,
                       u.ci, u.nombres, u.apellidos, u.correo,
                       COALESCE(p.nombre_plantilla, 'Certificado histórico') AS nombre_plantilla,
                       COALESCE(p.destinatario, c.tipo_participacion) AS destinatario_plantilla
                FROM certificados c
                INNER JOIN usuarios u ON u.id_usuario = c.id_usuario
                LEFT JOIN plantillas_certificado p ON p.id_plantilla = c.id_plantilla
                WHERE " . implode(' AND ', $where) . "
                ORDER BY nombre_plantilla ASC, c.tipo_participacion ASC, u.apellidos ASC, u.nombres ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerEvento(int $idEvento): ?array {
        $stmt = $this->db->prepare("SELECT e.id_evento, e.codigo, e.titulo, e.modalidad, e.estado, e.fecha_inicio, e.fecha_fin,
                                           te.nombre AS tipo_evento, c.nombre AS area
                                    FROM eventos e
                                    INNER JOIN tipos_evento te ON te.id_tipo_evento = e.id_tipo_evento
                                    INNER JOIN categorias c ON c.id_categoria = e.id_categoria
                                    WHERE e.id_evento = :evento LIMIT 1");
        $stmt->execute([':evento' => $idEvento]);
        return $stmt->fetch() ?: null;
    }

    /** Página independiente con sesiones y certificados de un evento. */
    public function detalleEvento(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $eventoReporte = $idEvento ? $this->obtenerEvento($idEvento) : null;
        if (!$eventoReporte) {
            http_response_code(404);
            exit('El evento solicitado no existe.');
        }
        $sesionesReporte = $this->sesionesConResumen($idEvento);
        $tiposCertificado = $this->tiposCertificadoPorEvento($idEvento);
        $filtroCertificado = $_GET['certificado'] ?? '';
        if ($filtroCertificado !== 'historico' && !(is_numeric($filtroCertificado) && (int)$filtroCertificado > 0)) $filtroCertificado = '';
        $certificadosReporte = $this->certificadosPorEvento($idEvento, $filtroCertificado ?: null);
        $filtrosReporte = $this->normalizarFiltros($_GET);
        require __DIR__ . '/../views/admin/reportes/evento_excel.php';
    }

    /** Página independiente con la lista de asistencia de una sola sesión. */
    public function detalleAsistenciaSesion(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $idSesion = filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT);
        $eventoReporte = $idEvento ? $this->obtenerEvento($idEvento) : null;
        $sesionStmt = $this->db->prepare('SELECT id_sesion, titulo, fecha, hora_inicio, hora_fin, estado FROM sesiones_evento WHERE id_sesion = :sesion AND id_evento = :evento');
        $sesionStmt->execute([':sesion' => $idSesion, ':evento' => $idEvento]);
        $sesionReporte = $sesionStmt->fetch();
        if (!$eventoReporte || !$sesionReporte) {
            http_response_code(404);
            exit('La sesión solicitada no pertenece al evento.');
        }
        $asistenciaSesionReporte = $this->reporteAsistenciaSesion($idSesion, $idEvento);
        $filtrosReporte = $this->normalizarFiltros($_GET);
        require __DIR__ . '/../views/admin/reportes/asistencia_excel.php';
    }

    public function descargarAsistenciaPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $idSesion = filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT);
        $evento = $idEvento ? $this->obtenerEvento($idEvento) : null;
        $stmt = $this->db->prepare('SELECT id_sesion, titulo, fecha, hora_inicio, hora_fin FROM sesiones_evento WHERE id_sesion = :sesion AND id_evento = :evento');
        $stmt->execute([':sesion' => $idSesion, ':evento' => $idEvento]);
        $sesion = $stmt->fetch();
        if (!$evento || !$sesion) { http_response_code(404); exit('La sesión solicitada no pertenece al evento.'); }
        require_once __DIR__ . '/../helpers/ReportePdfService.php';
        (new ReportePdfService())->descargarAsistencia($evento, $sesion, $this->reporteAsistenciaSesion($idSesion, $idEvento));
    }

    public function descargarCertificadosPdf(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);
        $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
        $evento = $idEvento ? $this->obtenerEvento($idEvento) : null;
        if (!$evento) { http_response_code(404); exit('El evento solicitado no existe.'); }
        $filtroCertificado = $_GET['certificado'] ?? '';
        if ($filtroCertificado !== 'historico' && !(is_numeric($filtroCertificado) && (int)$filtroCertificado > 0)) $filtroCertificado = '';
        $tipos = $this->tiposCertificadoPorEvento($idEvento);
        $nombre = 'Todos los certificados';
        foreach ($tipos as $tipo) {
            if (($filtroCertificado === 'historico' && $tipo['id_plantilla'] === null) || (is_numeric($filtroCertificado) && (int)$tipo['id_plantilla'] === (int)$filtroCertificado)) {
                $nombre = $tipo['nombre_certificado'];
                break;
            }
        }
        require_once __DIR__ . '/../helpers/ReportePdfService.php';
        (new ReportePdfService())->descargarCertificados($evento, $this->certificadosPorEvento($idEvento, $filtroCertificado ?: null), $nombre);
    }

    /**
     * RF-71: Reporte consolidado de eventos según filtros
     */
    public function reporteEventos(array $filtros = []): array {
        return $this->eventosDelPeriodo($filtros);
    }

    /**
     * RF-72: Reporte detallado de participantes por evento
     */
    public function reporteParticipantesPorEvento(int $id_evento): array {
        $sql = "SELECT 
                    i.codigo_inscripcion,
                    u.ci,
                    u.nombres,
                    u.apellidos,
                    u.correo,
                    u.telefono,
                    i.estado AS estado_inscripcion,
                    i.origen,
                    i.porcentaje_asistencia,
                    i.habilitado_certificado,
                    i.fecha_inscripcion
                FROM inscripciones i
                INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
                WHERE i.id_evento = :id_evento
                ORDER BY u.apellidos ASC, u.nombres ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_evento' => $id_evento]);
        return $stmt->fetchAll();
    }

    /**
     * RF-73: Reporte de asistencia y sesiones de un evento
     */
    public function reporteAsistencia(int $id_evento): array {
        $sql = "SELECT 
                    se.titulo AS sesion_titulo,
                    se.fecha,
                    se.hora_inicio,
                    u.ci,
                    u.nombres,
                    u.apellidos,
                    COALESCE(a.estado, 'SIN_REGISTRO') AS estado_asistencia,
                    a.observacion,
                    a.fecha_registro
                FROM sesiones_evento se
                INNER JOIN inscripciones i ON se.id_evento = i.id_evento AND i.estado = 'INSCRITO'
                INNER JOIN usuarios u ON i.id_usuario = u.id_usuario
                LEFT JOIN asistencias a ON a.id_sesion = se.id_sesion AND a.id_inscripcion = i.id_inscripcion
                WHERE se.id_evento = :id_evento
                ORDER BY se.fecha ASC, u.apellidos ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_evento' => $id_evento]);
        return $stmt->fetchAll();
    }

    /**
     * RF-74: Reporte de certificados emitidos
     */
    public function reporteCertificados(array $filtros = []): array {
        $where = ["1 = 1"];
        $params = [];

        if (!empty($filtros['id_evento'])) {
            $where[] = "c.id_evento = :id_evento";
            $params[':id_evento'] = (int)$filtros['id_evento'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = "c.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        $sql = "SELECT 
                    c.codigo_unico,
                    e.codigo AS codigo_evento,
                    e.titulo AS evento_titulo,
                    u.ci,
                    u.nombres,
                    u.apellidos,
                    u.correo,
                    c.tipo_participacion,
                    COALESCE(p.nombre_plantilla, 'Plantilla histórica') AS nombre_plantilla,
                    c.fecha_emision,
                    c.estado,
                    c.motivo_anulacion
                FROM certificados c
                INNER JOIN eventos e ON c.id_evento = e.id_evento
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                LEFT JOIN plantillas_certificado p ON p.id_plantilla = c.id_plantilla
                WHERE " . implode(' AND ', $where) . "
                ORDER BY c.fecha_emision DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * RF-75: Reporte de solicitudes y reimpresiones
     */
    public function reporteReimpresiones(): array {
        $sql = "SELECT 
                    sr.id_solicitud,
                    c.codigo_unico,
                    e.titulo AS evento_titulo,
                    u.ci AS solicitante_ci,
                    CONCAT(u.nombres, ' ', u.apellidos) AS solicitante_nombre,
                    sr.motivo,
                    sr.estado,
                    sr.fecha_solicitud,
                    sr.fecha_resolucion,
                    CONCAT(admin.nombres, ' ', admin.apellidos) AS resolutor_nombre,
                    sr.motivo_rechazo,
                    sr.fecha_entrega
                FROM solicitudes_reimpresion sr
                INNER JOIN certificados c ON sr.id_certificado = c.id_certificado
                INNER JOIN eventos e ON c.id_evento = e.id_evento
                INNER JOIN usuarios u ON sr.id_usuario_solicitante = u.id_usuario
                LEFT JOIN usuarios admin ON sr.id_usuario_resolucion = admin.id_usuario
                ORDER BY sr.fecha_solicitud DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * RF-76: Reporte de expositores y actividades impartidas
     */
    public function reporteExpositores(): array {
        $sql = "SELECT 
                    u.ci,
                    u.nombres,
                    u.apellidos,
                    u.correo,
                    u.telefono,
                    ee.rol_expositor,
                    e.codigo AS evento_codigo,
                    e.titulo AS evento_titulo,
                    e.modalidad,
                    e.fecha_inicio,
                    e.fecha_fin,
                    e.estado AS evento_estado
                FROM usuarios u
                INNER JOIN evento_expositores ee ON u.id_usuario = ee.id_usuario
                INNER JOIN eventos e ON ee.id_evento = e.id_evento
                ORDER BY u.apellidos ASC, e.fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Utilidad para forzar la descarga de datos en formato CSV compatible con Excel
     */
    public function exportarCsv(string $nombreArchivo, array $encabezados, array $datos): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '_' . date('Ymd_His') . '.csv"');

        $salida = fopen('php://output', 'w');
        // BOM UTF-8 para que Microsoft Excel reconozca tildes y caracteres especiales
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($salida, $encabezados);

        foreach ($datos as $fila) {
            fputcsv($salida, array_values($fila));
        }

        fclose($salida);
        exit();
    }

    /** Descarga un libro XLSX para listas de asistencia o certificaciones. */
    public function exportarExcel(string $nombreArchivo, string $nombreHoja, array $encabezados, array $datos): void {
        require_once __DIR__ . '/../helpers/ReporteExcelService.php';
        ReporteExcelService::descargar($nombreArchivo, $nombreHoja, $encabezados, $datos);
    }

    /**
     * Endpoint para exportar el reporte seleccionado a CSV
     */
    public function exportar(): void {
        AuthHelper::requerirRol(['ADMINISTRADOR']);

        $tipo = trim($_GET['tipo'] ?? '');
        $formato = strtolower(trim((string)($_GET['formato'] ?? 'csv')));
        $filtros = $this->normalizarFiltros($_GET);
        if ($tipo === 'areas') {
            $filtros = $this->normalizarFiltrosResumen($_GET);
        }

        if ($tipo === 'asistencia_sesion') {
            $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
            $idSesion = filter_input(INPUT_GET, 'id_sesion', FILTER_VALIDATE_INT);
            $validar = $this->db->prepare('SELECT titulo FROM sesiones_evento WHERE id_sesion = :sesion AND id_evento = :evento');
            $validar->execute([':sesion' => $idSesion, ':evento' => $idEvento]);
            if (!$idEvento || !$idSesion || !$validar->fetchColumn()) {
                http_response_code(404);
                exit('La sesión solicitada no pertenece al evento.');
            }
            $encabezados = ['Sesión', 'Fecha', 'Hora inicio', 'Hora fin', 'Estado sesión', 'Código inscripción', 'Estado inscripción', '% asistencia', 'CI', 'Nombres', 'Apellidos', 'Correo', 'Asistencia', 'Observación', 'Fecha registro'];
            $datos = $this->reporteAsistenciaSesion($idSesion, $idEvento);
            if ($formato === 'xlsx') {
                $this->exportarExcel('asistencia_sesion_' . $idSesion, 'Asistencia', $encabezados, $datos);
            }
            $this->exportarCsv('asistencia_sesion_' . $idSesion, $encabezados, $datos);
        }

        if ($tipo === 'certificados_evento') {
            $idEvento = filter_input(INPUT_GET, 'id_evento', FILTER_VALIDATE_INT);
            $filtroCertificado = $_GET['certificado'] ?? '';
            if ($filtroCertificado !== 'historico' && !(is_numeric($filtroCertificado) && (int)$filtroCertificado > 0)) $filtroCertificado = '';
            $encabezados = ['Folio', 'Tipo de participación', 'Fecha emisión', 'Estado', 'CI', 'Nombres', 'Apellidos', 'Correo', 'Certificado', 'Destinatario del certificado'];
            $datos = $idEvento ? $this->certificadosPorEvento($idEvento, $filtroCertificado ?: null) : [];
            if ($formato === 'xlsx') {
                $this->exportarExcel('certificados_evento_' . (int)$idEvento, 'Certificados', $encabezados, $datos);
            }
            $this->exportarCsv('certificados_evento_' . (int)$idEvento, $encabezados, $datos);
        }

        match ($tipo) {
            'eventos' => $this->exportarCsv(
                'reporte_eventos',
                ['ID', 'Código', 'Título', 'Modalidad', 'Estado', 'Fecha Inicio', 'Fecha Fin', 'Inicio inscripciones', 'Fin inscripciones', 'Cupo Máx.', 'Tipo', 'Área', 'Inscritos', 'Sesiones', 'Certificados'],
                $this->reporteEventos($filtros)
            ),
            'participantes' => $this->exportarCsv(
                'reporte_participantes',
                ['Folio', 'CI', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'Estado', 'Origen', '% Asistencia', 'Habilitado Cert.', 'Fecha Inscripción'],
                $this->reporteParticipantesPorEvento((int)($_GET['id_evento'] ?? 0))
            ),
            'certificados' => $this->exportarCsv(
                'reporte_certificados',
                ['Código Certificado', 'Código Evento', 'Evento', 'CI', 'Nombres', 'Apellidos', 'Correo', 'Rol', 'Certificado', 'Fecha Emisión', 'Estado', 'Motivo Anulación'],
                $this->reporteCertificados()
            ),
            'areas' => $this->exportarCsv(
                'reporte_areas_' . $filtros['mes'],
                ['ID Área', 'Área', 'Eventos en el período', 'Publicados', 'Concluidos', 'Cursos habilitados'],
                $this->resumenPorAreas($filtros)
            ),
            'reimpresiones' => $this->exportarCsv(
                'reporte_reimpresiones',
                ['ID', 'Código Certificado', 'Evento', 'CI Solicitante', 'Nombre Solicitante', 'Motivo', 'Estado', 'Fecha Solicitud', 'Fecha Resolución', 'Resolutor', 'Motivo Rechazo', 'Fecha Entrega'],
                $this->reporteReimpresiones()
            ),
            'expositores' => $this->exportarCsv(
                'reporte_expositores',
                ['CI', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'Rol Expositor', 'Código Evento', 'Evento', 'Modalidad', 'Fecha Inicio', 'Fecha Fin', 'Estado Evento'],
                $this->reporteExpositores()
            ),
            default => die("Tipo de reporte no válido para exportación.")
        };
    }
}
