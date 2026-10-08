<?php
$titulo_pagina = 'Reportes y analítica — CERT-DIE';
$seccion_activa = 'reportes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';

$totalEventos = array_sum(array_map(static fn(array $fila): int => (int)$fila['total_eventos'], $resumenAreas));
$colores = ['#214a91', '#008f63', '#e69b19', '#d84845', '#6f42c1', '#0b7285', '#6c757d'];
$tramos = [];
$inicioTramo = 0.0;
foreach ($resumenAreas as $indice => $fila) {
    if ($totalEventos < 1 || !(int)$fila['total_eventos']) continue;
    $finTramo = $inicioTramo + ((int)$fila['total_eventos'] / $totalEventos * 100);
    $tramos[] = $colores[$indice % count($colores)] . ' ' . number_format($inicioTramo, 3, '.', '') . '% ' . number_format($finTramo, 3, '.', '') . '%';
    $inicioTramo = $finTramo;
}
$estiloPie = $tramos ? 'conic-gradient(' . implode(', ', $tramos) . ')' : '#e9ecef';

$totalAreasActivas = count(array_filter($resumenAreas, static fn(array $fila): bool => (int)$fila['total_eventos'] > 0));
$tramosTipos = [];
$inicioTramo = 0.0;
foreach ($resumenTipos as $indice => $fila) {
    if (!$totalEventos || !(int)$fila['total_eventos']) continue;
    $finTramo = $inicioTramo + ((int)$fila['total_eventos'] / $totalEventos * 100);
    $tramosTipos[] = $colores[$indice % count($colores)] . ' ' . number_format($inicioTramo, 3, '.', '') . '% ' . number_format($finTramo, 3, '.', '') . '%';
    $inicioTramo = $finTramo;
}
$estiloPieTipos = $tramosTipos ? 'conic-gradient(' . implode(', ', $tramosTipos) . ')' : '#e9ecef';

// La analítica mensual y el listado por evento conservan parámetros totalmente independientes.
$parametrosResumen = ['resumen_mes' => $filtrosResumen['mes']];
if ($filtrosResumen['areas_seleccionadas'] !== null) {
    $parametrosResumen['resumen_areas_present'] = 1;
    $parametrosResumen['resumen_areas'] = $filtrosResumen['areas_seleccionadas'];
}
if ($filtrosResumen['tipos_seleccionados'] !== null) {
    $parametrosResumen['resumen_tipos_present'] = 1;
    $parametrosResumen['resumen_tipos'] = $filtrosResumen['tipos_seleccionados'];
}
$consultaResumen = http_build_query($parametrosResumen);
$parametrosEventos = [];
if ($filtrosReporte['id_categoria']) $parametrosEventos['id_categoria'] = $filtrosReporte['id_categoria'];
if ($filtrosReporte['id_tipo_evento']) $parametrosEventos['id_tipo_evento'] = $filtrosReporte['id_tipo_evento'];
if ($filtrosReporte['habilitado']) $parametrosEventos['habilitado'] = $filtrosReporte['habilitado'];
if ($filtrosReporte['buscar'] !== '') $parametrosEventos['buscar'] = $filtrosReporte['buscar'];
if ($filtrosReporte['fecha_desde']) $parametrosEventos['fecha_desde'] = $filtrosReporte['fecha_desde'];
if ($filtrosReporte['fecha_hasta']) $parametrosEventos['fecha_hasta'] = $filtrosReporte['fecha_hasta'];
$consultaEventos = http_build_query($parametrosEventos);
$parametrosDatos = [];
if ($filtrosDatos['id_categoria']) $parametrosDatos['datos_id_categoria'] = $filtrosDatos['id_categoria'];
if ($filtrosDatos['id_tipo_evento']) $parametrosDatos['datos_id_tipo_evento'] = $filtrosDatos['id_tipo_evento'];
if ($filtrosDatos['habilitado']) $parametrosDatos['datos_habilitado'] = $filtrosDatos['habilitado'];
if ($filtrosDatos['buscar'] !== '') $parametrosDatos['datos_buscar'] = $filtrosDatos['buscar'];
if ($filtrosDatos['fecha_desde']) $parametrosDatos['datos_fecha_desde'] = $filtrosDatos['fecha_desde'];
if ($filtrosDatos['fecha_hasta']) $parametrosDatos['datos_fecha_hasta'] = $filtrosDatos['fecha_hasta'];
$consultaSinDatos = http_build_query(array_merge($parametrosResumen, $parametrosEventos));
$areasTodasSeleccionadas = $filtrosResumen['areas_seleccionadas'] === null
    || count($filtrosResumen['areas_seleccionadas']) === count($areasReporte);
$tiposTodosSeleccionados = $filtrosResumen['tipos_seleccionados'] === null
    || count($filtrosResumen['tipos_seleccionados']) === count($tiposReporte);
$mesesResumen = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
$anioResumen = (int)substr($filtrosResumen['mes'], 0, 4);
$numeroMesResumen = (int)substr($filtrosResumen['mes'], 5, 2);
$aniosResumen = range((int)date('Y') - 5, (int)date('Y') + 5);
if (!in_array($anioResumen, $aniosResumen, true)) $aniosResumen[] = $anioResumen;
sort($aniosResumen);
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h1 class="h3 fw-bold text-uab-azul mb-1">Reportes y analítica</h1>
      <p class="text-muted mb-0">Indicadores mensuales, asistencia por sesión y certificados por plantilla.</p>
    </div>
    <button class="btn btn-outline-secondary no-print" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir vista</button>
  </div>

  <form method="get" class="card border-0 shadow-sm mb-4 no-print">
    <input type="hidden" name="action" value="admin_reportes">
    <?php foreach (['id_categoria', 'id_tipo_evento', 'habilitado', 'buscar', 'fecha_desde', 'fecha_hasta'] as $campo): if ($filtrosReporte[$campo] !== null && $filtrosReporte[$campo] !== ''): ?>
      <input type="hidden" name="<?= htmlspecialchars($campo) ?>" value="<?= htmlspecialchars((string)$filtrosReporte[$campo]) ?>">
    <?php endif; endforeach; ?>
    <input type="hidden" name="resumen_areas_present" value="1">
    <input type="hidden" name="resumen_tipos_present" value="1">
    <div class="card-body">
      <div class="row g-3 align-items-end mb-3">
        <div class="col-12 col-md-6 col-xl-4">
        <label class="form-label fw-semibold" for="resumen-mes">Mes de análisis</label>
        <div class="row g-2">
          <div class="col-7"><select class="form-select" id="resumen-mes" name="resumen_mes_num" aria-label="Mes de análisis"><?php foreach ($mesesResumen as $numeroMes => $nombreMes): ?><option value="<?= $numeroMes ?>" <?= $numeroMesResumen === $numeroMes ? 'selected' : '' ?>><?= htmlspecialchars($nombreMes) ?></option><?php endforeach; ?></select></div>
          <div class="col-5"><select class="form-select" name="resumen_anio" aria-label="Año de análisis"><?php foreach ($aniosResumen as $anio): ?><option value="<?= $anio ?>" <?= $anioResumen === $anio ? 'selected' : '' ?>><?= $anio ?></option><?php endforeach; ?></select></div>
        </div>
        </div>
      </div>
      <div class="row g-4">
        <fieldset class="col-12 col-lg-6">
          <legend class="fs-6 fw-semibold mb-2">Áreas incluidas</legend>
          <div class="form-check mb-2"><input class="form-check-input resumen-seleccionar-todo" data-grupo="resumen-area" type="checkbox" id="todas-areas" <?= $areasTodasSeleccionadas ? 'checked' : '' ?>><label class="form-check-label fw-semibold" for="todas-areas">Todas las áreas</label></div>
          <div class="row row-cols-1 row-cols-sm-2 g-2">
            <?php foreach ($areasReporte as $area): $seleccionada = $filtrosResumen['areas_seleccionadas'] === null || in_array((int)$area['id_categoria'], $filtrosResumen['areas_seleccionadas'], true); ?>
              <div class="col"><div class="form-check"><input class="form-check-input resumen-area" type="checkbox" name="resumen_areas[]" value="<?= (int)$area['id_categoria'] ?>" id="resumen-area-<?= (int)$area['id_categoria'] ?>" <?= $seleccionada ? 'checked' : '' ?>><label class="form-check-label" for="resumen-area-<?= (int)$area['id_categoria'] ?>"><?= htmlspecialchars($area['nombre']) ?></label></div></div>
            <?php endforeach; ?>
          </div>
        </fieldset>
        <fieldset class="col-12 col-lg-6">
          <legend class="fs-6 fw-semibold mb-2">Tipos de evento incluidos</legend>
          <div class="form-check mb-2"><input class="form-check-input resumen-seleccionar-todo" data-grupo="resumen-tipo" type="checkbox" id="todos-tipos" <?= $tiposTodosSeleccionados ? 'checked' : '' ?>><label class="form-check-label fw-semibold" for="todos-tipos">Todos los tipos de evento</label></div>
          <div class="row row-cols-1 row-cols-sm-2 g-2">
            <?php foreach ($tiposReporte as $tipo): $seleccionado = $filtrosResumen['tipos_seleccionados'] === null || in_array((int)$tipo['id_tipo_evento'], $filtrosResumen['tipos_seleccionados'], true); ?>
              <div class="col"><div class="form-check"><input class="form-check-input resumen-tipo" type="checkbox" name="resumen_tipos[]" value="<?= (int)$tipo['id_tipo_evento'] ?>" id="resumen-tipo-<?= (int)$tipo['id_tipo_evento'] ?>" <?= $seleccionado ? 'checked' : '' ?>><label class="form-check-label" for="resumen-tipo-<?= (int)$tipo['id_tipo_evento'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></label></div></div>
            <?php endforeach; ?>
          </div>
        </fieldset>
      </div>
      <div class="mt-3 d-flex justify-content-end"><button class="btn btn-uab-azul"><i class="bi bi-funnel me-1"></i>Aplicar filtros al resumen</button></div>
    </div>
  </form>

  <section class="mb-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div>
        <h2 class="h4 fw-bold text-uab-azul mb-0">Resumen general</h2>
        <p class="text-muted mb-0 small">Actividades que se cruzan con <?= htmlspecialchars(date('m/Y', strtotime($filtrosResumen['desde']))) ?>.</p>
      </div>
      <a class="btn btn-sm btn-outline-success no-print" href="index.php?action=admin_exportar_reporte&amp;tipo=areas&amp;<?= htmlspecialchars($consultaResumen) ?>"><i class="bi bi-filetype-csv me-1"></i>CSV por áreas</a>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-12 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Eventos en el período</div><div class="display-6 fw-bold text-uab-azul"><?= $totalEventos ?></div></div></div></div>
      <div class="col-12 col-md-6"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small text-uppercase fw-semibold">Cursos habilitados en el mes</div><div class="display-6 fw-bold text-success"><?= $cursosHabilitados ?></div><small class="text-muted">Cursos publicados con inscripciones abiertas en el período.</small></div></div></div>
    </div>
    <div class="row g-3">
      <div class="col-12 col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="row align-items-center g-4">
        <div class="col-12 col-md-5 d-flex justify-content-center"><div class="rounded-circle shadow-sm" role="img" aria-label="Distribución de eventos por área" style="width:min(220px, 65vw);aspect-ratio:1;background:<?= htmlspecialchars($estiloPie) ?>;position:relative"><div class="bg-white rounded-circle position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center" style="width:58%;height:58%"><strong class="h3 mb-0"><?= $totalEventos ?></strong><span class="small text-muted">eventos</span></div></div></div>
        <div class="col-12 col-md-7"><h3 class="h5 fw-bold mb-2">Eventos por área</h3><p class="small text-muted mb-3"><?= $totalAreasActivas ?> área<?= $totalAreasActivas === 1 ? '' : 's' ?> con actividad.</p><?php if (!$totalEventos): ?><p class="text-muted mb-0">No existen eventos con estas casillas.</p><?php endif; ?><div class="row g-2"><?php foreach ($resumenAreas as $indice => $fila): if (!(int)$fila['total_eventos']) continue; ?><div class="col-12"><div class="d-flex align-items-center gap-2"><span class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:<?= $colores[$indice % count($colores)] ?>"></span><span class="small flex-grow-1"><?= htmlspecialchars($fila['area']) ?></span><strong><?= (int)$fila['total_eventos'] ?></strong></div></div><?php endforeach; ?></div></div>
      </div></div></div></div>
      <div class="col-12 col-xl-6"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="row align-items-center g-4">
        <div class="col-12 col-md-5 d-flex justify-content-center"><div class="rounded-circle shadow-sm" role="img" aria-label="Distribución de eventos por tipo" style="width:min(220px, 65vw);aspect-ratio:1;background:<?= htmlspecialchars($estiloPieTipos) ?>;position:relative"><div class="bg-white rounded-circle position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center" style="width:58%;height:58%"><strong class="h3 mb-0"><?= $totalEventos ?></strong><span class="small text-muted">eventos</span></div></div></div>
        <div class="col-12 col-md-7"><h3 class="h5 fw-bold mb-2">Eventos por tipo</h3><p class="small text-muted mb-3">Distribución de talleres, cursos, seminarios y demás tipos.</p><?php if (!$totalEventos): ?><p class="text-muted mb-0">No existen eventos con estas casillas.</p><?php endif; ?><div class="row g-2"><?php foreach ($resumenTipos as $indice => $fila): if (!(int)$fila['total_eventos']) continue; ?><div class="col-12"><div class="d-flex align-items-center gap-2"><span class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:<?= $colores[$indice % count($colores)] ?>"></span><span class="small flex-grow-1"><?= htmlspecialchars($fila['tipo']) ?></span><strong><?= (int)$fila['total_eventos'] ?></strong></div></div><?php endforeach; ?></div></div>
      </div></div></div></div>
    </div>
  </section>

  <section class="mb-5" id="datos-eventos">
    <div class="mb-3">
      <h2 class="h4 fw-bold text-uab-azul mb-0">Datos de eventos</h2>
      <p class="text-muted mb-0 small">Seleccione un evento para conocer el perfil de sus personas inscritas activamente.</p>
    </div>

    <div class="alert alert-light border d-flex align-items-center gap-2 mb-3" id="datos-evento-estado" role="status">
      <i class="bi bi-bar-chart-fill text-uab-azul"></i><span>Seleccione un evento de la lista para cargar sus datos.</span>
    </div>

    <div class="row g-3 mb-4" id="datos-evento-graficos">
      <div class="col-12 col-lg-4"><article class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h3 class="h5 fw-bold text-uab-azul mb-3"><i class="bi bi-gender-ambiguous me-2"></i>Género</h3><div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3"><div class="rounded-circle shadow-sm flex-shrink-0" data-pie="genero" role="img" aria-label="Distribución por género" style="width:150px;aspect-ratio:1;background:#e9ecef;position:relative"><div class="bg-white rounded-circle position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center text-center" style="width:58%;height:58%"><strong class="h4 mb-0" data-pie-total>—</strong><span class="small text-muted">personas</span></div></div><div class="small flex-grow-1 w-100" data-pie-leyenda="genero"><span class="text-muted">Sin datos.</span></div></div></div></article></div>
      <div class="col-12 col-lg-4"><article class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h3 class="h5 fw-bold text-uab-azul mb-3"><i class="bi bi-mortarboard me-2"></i>Condición académica</h3><div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3"><div class="rounded-circle shadow-sm flex-shrink-0" data-pie="condicion_academica" role="img" aria-label="Distribución por condición académica" style="width:150px;aspect-ratio:1;background:#e9ecef;position:relative"><div class="bg-white rounded-circle position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center text-center" style="width:58%;height:58%"><strong class="h4 mb-0" data-pie-total>—</strong><span class="small text-muted">personas</span></div></div><div class="small flex-grow-1 w-100" data-pie-leyenda="condicion_academica"><span class="text-muted">Sin datos.</span></div></div></div></article></div>
      <div class="col-12 col-lg-4"><article class="card border-0 shadow-sm h-100"><div class="card-body p-4"><h3 class="h5 fw-bold text-uab-azul mb-3"><i class="bi bi-calendar-range me-2"></i>Rango de edad</h3><div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3"><div class="rounded-circle shadow-sm flex-shrink-0" data-pie="edad" role="img" aria-label="Distribución por rangos de edad" style="width:150px;aspect-ratio:1;background:#e9ecef;position:relative"><div class="bg-white rounded-circle position-absolute top-50 start-50 translate-middle d-flex flex-column justify-content-center align-items-center text-center" style="width:58%;height:58%"><strong class="h4 mb-0" data-pie-total>—</strong><span class="small text-muted">personas</span></div></div><div class="small flex-grow-1 w-100" data-pie-leyenda="edad"><span class="text-muted">Sin datos.</span></div></div></div></article></div>
    </div>

    <form method="get" class="card border-0 shadow-sm mb-3 no-print">
      <input type="hidden" name="action" value="admin_reportes">
      <?php foreach ([$parametrosResumen, $parametrosEventos] as $parametrosExternos): foreach ($parametrosExternos as $clave => $valor): ?>
        <?php if (is_array($valor)): foreach ($valor as $item): ?><input type="hidden" name="<?= htmlspecialchars($clave) ?>[]" value="<?= htmlspecialchars((string)$item) ?>"><?php endforeach; ?>
        <?php else: ?><input type="hidden" name="<?= htmlspecialchars($clave) ?>" value="<?= htmlspecialchars((string)$valor) ?>"><?php endif; ?>
      <?php endforeach; endforeach; ?>
      <div class="card-body row g-3 align-items-end">
        <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="datos-buscar">Buscar por nombre</label><input class="form-control" id="datos-buscar" name="datos_buscar" type="search" value="<?= htmlspecialchars($filtrosDatos['buscar']) ?>" placeholder="Nombre del evento"></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-tipo">Tipo de evento</label><select class="form-select" id="datos-tipo" name="datos_id_tipo_evento"><option value="">Todos los tipos</option><?php foreach ($tiposReporte as $tipo): ?><option value="<?= (int)$tipo['id_tipo_evento'] ?>" <?= $filtrosDatos['id_tipo_evento'] === (int)$tipo['id_tipo_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-area">Área</label><select class="form-select" id="datos-area" name="datos_id_categoria"><option value="">Todas las áreas</option><?php foreach ($areasReporte as $area): ?><option value="<?= (int)$area['id_categoria'] ?>" <?= $filtrosDatos['id_categoria'] === (int)$area['id_categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-habilitado">Habilitado</label><select class="form-select" id="datos-habilitado" name="datos_habilitado"><option value="">Todos</option><option value="SI" <?= $filtrosDatos['habilitado'] === 'SI' ? 'selected' : '' ?>>Habilitado</option><option value="NO" <?= $filtrosDatos['habilitado'] === 'NO' ? 'selected' : '' ?>>No habilitado</option></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-desde">Desde</label><input class="form-control" id="datos-desde" name="datos_fecha_desde" type="date" value="<?= htmlspecialchars($filtrosDatos['fecha_desde'] ?? '') ?>"></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-hasta">Hasta</label><input class="form-control" id="datos-hasta" name="datos_fecha_hasta" type="date" value="<?= htmlspecialchars($filtrosDatos['fecha_hasta'] ?? '') ?>"></div>
        <div class="col-12 col-xl-1 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1" title="Filtrar"><i class="bi bi-funnel"></i><span class="visually-hidden">Filtrar</span></button><a class="btn btn-outline-secondary" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaSinDatos) ?>#datos-eventos" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
      </div>
    </form>

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3"><h3 class="h5 mb-0 text-uab-azul"><i class="bi bi-calendar-event me-2"></i>Lista de eventos</h3></div>
      <div class="list-group list-group-flush" id="lista-datos-eventos">
        <?php if (!$eventosDatos): ?><div class="text-center text-muted py-4">No existen eventos registrados.</div><?php endif; ?>
        <?php foreach ($eventosDatos as $eventoDatos): ?>
          <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 datos-evento-selector" data-id-evento="<?= (int)$eventoDatos['id_evento'] ?>">
            <span><strong><?= htmlspecialchars($eventoDatos['titulo']) ?></strong><span class="d-block small text-muted"><code><?= htmlspecialchars($eventoDatos['codigo']) ?></code> · <?= htmlspecialchars($eventoDatos['tipo_evento']) ?> · <?= htmlspecialchars($eventoDatos['area']) ?></span></span>
            <span class="badge bg-light text-dark border text-nowrap"><?= (int)$eventoDatos['total_inscritos'] ?> inscritos</span>
          </button>
        <?php endforeach; ?>
      </div>
      <?php if ($hayMasEventosDatos): ?><div class="card-footer bg-white text-center py-3 no-print"><a class="btn btn-outline-primary btn-sm" href="index.php?action=admin_datos_eventos&amp;<?= htmlspecialchars(http_build_query($parametrosDatos)) ?>"><i class="bi bi-list-ul me-1"></i>Ver todos los eventos</a></div><?php endif; ?>
    </div>
  </section>

  <section class="mb-5" id="reportes-evento">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div>
        <h2 class="h4 fw-bold text-uab-azul mb-0">Reportes por evento</h2>
        <p class="text-muted mb-0 small">Filtre los eventos para acceder a sesiones, asistencia y certificados.</p>
      </div>
      <a class="btn btn-sm btn-outline-success no-print" href="index.php?action=admin_exportar_reporte&amp;tipo=eventos&amp;<?= htmlspecialchars($consultaEventos) ?>"><i class="bi bi-filetype-csv me-1"></i>CSV de eventos</a>
    </div>

    <form method="get" class="card border-0 shadow-sm mb-3 no-print">
      <input type="hidden" name="action" value="admin_reportes">
      <?php foreach ($parametrosResumen as $clave => $valor): if ($clave === 'mes') continue; ?>
        <?php if (is_array($valor)): foreach ($valor as $item): ?><input type="hidden" name="<?= htmlspecialchars($clave) ?>[]" value="<?= htmlspecialchars((string)$item) ?>"><?php endforeach; ?>
        <?php else: ?><input type="hidden" name="<?= htmlspecialchars($clave) ?>" value="<?= htmlspecialchars((string)$valor) ?>"><?php endif; ?>
      <?php endforeach; ?>
      <div class="card-body row g-3 align-items-end">
        <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="buscar-reporte">Buscar por nombre</label><input class="form-control" id="buscar-reporte" name="buscar" type="search" value="<?= htmlspecialchars($filtrosReporte['buscar']) ?>" placeholder="Nombre del evento"></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="tipo-reporte">Tipo de evento</label><select class="form-select" id="tipo-reporte" name="id_tipo_evento"><option value="">Todos los tipos</option><?php foreach ($tiposReporte as $tipo): ?><option value="<?= (int)$tipo['id_tipo_evento'] ?>" <?= $filtrosReporte['id_tipo_evento'] === (int)$tipo['id_tipo_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option><?php endforeach; ?></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="id_categoria">Área</label><select class="form-select" id="id_categoria" name="id_categoria"><option value="">Todas las áreas</option><?php foreach ($areasReporte as $area): ?><option value="<?= (int)$area['id_categoria'] ?>" <?= $filtrosReporte['id_categoria'] === (int)$area['id_categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?><?= !(int)$area['activo'] ? ' (inactiva)' : '' ?></option><?php endforeach; ?></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="habilitado-reporte">Habilitado</label><select class="form-select" id="habilitado-reporte" name="habilitado"><option value="">Todos</option><option value="SI" <?= $filtrosReporte['habilitado'] === 'SI' ? 'selected' : '' ?>>Habilitado</option><option value="NO" <?= $filtrosReporte['habilitado'] === 'NO' ? 'selected' : '' ?>>No habilitado</option></select></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="fecha-desde-reporte">Desde</label><input class="form-control" id="fecha-desde-reporte" name="fecha_desde" type="date" value="<?= htmlspecialchars($filtrosReporte['fecha_desde'] ?? '') ?>"></div>
        <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="fecha-hasta-reporte">Hasta</label><input class="form-control" id="fecha-hasta-reporte" name="fecha_hasta" type="date" value="<?= htmlspecialchars($filtrosReporte['fecha_hasta'] ?? '') ?>"></div>
        <div class="col-12 col-xl-1 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1" title="Filtrar"><i class="bi bi-funnel"></i><span class="visually-hidden">Filtrar</span></button><a class="btn btn-outline-secondary" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaResumen) ?>#reportes-evento" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
      </div>
    </form>

    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Evento</th><th>Área</th><th>Fechas</th><th class="text-center">Sesiones</th><th class="text-center">Certificados</th><th class="text-end no-print">Detalle</th></tr></thead><tbody>
      <?php if (!$eventosReporte): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay eventos para el período y los filtros seleccionados.</td></tr><?php endif; ?>
      <?php foreach ($eventosReporte as $evento): ?><tr><td><strong><?= htmlspecialchars($evento['titulo']) ?></strong><div class="small text-muted"><code><?= htmlspecialchars($evento['codigo']) ?></code> · <?= htmlspecialchars($evento['tipo_evento']) ?> · <span class="badge bg-light text-dark border"><?= htmlspecialchars($evento['estado']) ?></span></div></td><td><?= htmlspecialchars($evento['area']) ?></td><td class="small text-nowrap"><?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br><?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?></td><td class="text-center"><?= (int)$evento['total_sesiones'] ?></td><td class="text-center"><?= (int)$evento['certificados_emitidos'] ?></td><td class="text-end no-print"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaEventos) ?>&amp;id_evento=<?= (int)$evento['id_evento'] ?>"><i class="bi bi-folder2-open me-1"></i>Abrir</a></td></tr><?php endforeach; ?>
    </tbody></table></div><?php if ($hayMasEventosReporte): ?><div class="card-footer bg-white text-center py-3 no-print"><a class="btn btn-outline-primary btn-sm" href="index.php?action=admin_reporte_eventos&amp;<?= htmlspecialchars($consultaEventos) ?>"><i class="bi bi-list-ul me-1"></i>Ver todos los eventos</a></div><?php endif; ?></div>
  </section>

  <section class="mb-5"><div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3"><div><h2 class="h4 fw-bold text-uab-azul mb-0">Detalle por áreas</h2><p class="text-muted mb-0 small">Consolidado de actividades del mes seleccionado.</p></div><a class="btn btn-sm btn-outline-success no-print" href="index.php?action=admin_exportar_reporte&amp;tipo=areas&amp;<?= htmlspecialchars($consultaResumen) ?>"><i class="bi bi-filetype-csv me-1"></i>Descargar CSV</a></div><div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Área</th><th class="text-center">Eventos</th><th class="text-center">Publicados</th><th class="text-center">Concluidos</th><th class="text-center">Cursos habilitados</th></tr></thead><tbody><?php foreach ($resumenAreas as $fila): ?><tr><td><strong><?= htmlspecialchars($fila['area']) ?></strong></td><td class="text-center"><?= (int)$fila['total_eventos'] ?></td><td class="text-center"><?= (int)$fila['publicados'] ?></td><td class="text-center"><?= (int)$fila['concluidos'] ?></td><td class="text-center"><span class="badge bg-success"><?= (int)$fila['cursos_habilitados'] ?></span></td></tr><?php endforeach; ?></tbody></table></div></div></section>

  <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h2 class="h6 fw-bold mb-0 text-uab-azul"><i class="bi bi-shield-check me-2"></i>Actividad administrativa reciente</h2></div><div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0"><thead class="table-light"><tr><th>Fecha y hora</th><th>Administrador</th><th>Módulo</th><th>Acción</th><th>Detalles</th></tr></thead><tbody><?php if (!$ultimasAuditorias): ?><tr><td colspan="5" class="text-center text-muted py-3">Sin registros recientes.</td></tr><?php endif; ?><?php foreach ($ultimasAuditorias as $auditoria): ?><tr><td class="small text-nowrap"><?= date('d/m/Y H:i', strtotime($auditoria['fecha_registro'])) ?></td><td class="small fw-semibold"><?= htmlspecialchars($auditoria['nombres'] . ' ' . $auditoria['apellidos']) ?></td><td><span class="badge bg-light text-dark border"><?= htmlspecialchars($auditoria['modulo']) ?></span></td><td><code class="small"><?= htmlspecialchars($auditoria['accion']) ?></code></td><td class="small text-muted"><?= htmlspecialchars($auditoria['detalles'] ?? '—') ?></td></tr><?php endforeach; ?></tbody></table></div><div class="card-footer bg-white text-center no-print"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_auditoria"><i class="bi bi-list-ul me-1"></i>Ver todas las actividades</a></div></section>
</main>
<script>
document.querySelectorAll('.resumen-seleccionar-todo').forEach((seleccionarTodo) => {
  const casillas = Array.from(document.querySelectorAll('.' + seleccionarTodo.dataset.grupo));
  const actualizarEstado = () => {
    const seleccionadas = casillas.filter((casilla) => casilla.checked).length;
    seleccionarTodo.checked = seleccionadas === casillas.length;
    seleccionarTodo.indeterminate = seleccionadas > 0 && seleccionadas < casillas.length;
  };
  seleccionarTodo.addEventListener('change', () => {
    casillas.forEach((casilla) => { casilla.checked = seleccionarTodo.checked; });
    seleccionarTodo.indeterminate = false;
  });
  casillas.forEach((casilla) => casilla.addEventListener('change', actualizarEstado));
  actualizarEstado();
});

const coloresDatosEvento = ['#214a91', '#008f63', '#e69b19', '#d84845', '#6f42c1', '#0b7285', '#6c757d'];

function renderizarPieDatosEvento(tipo, filas) {
  const grafico = document.querySelector(`[data-pie="${tipo}"]`);
  const leyenda = document.querySelector(`[data-pie-leyenda="${tipo}"]`);
  if (!grafico || !leyenda) return;

  const datos = Array.isArray(filas) ? filas.filter((fila) => Number(fila.total) > 0) : [];
  const total = datos.reduce((acumulado, fila) => acumulado + Number(fila.total), 0);
  const centro = grafico.querySelector('[data-pie-total]');
  if (centro) centro.textContent = String(total);
  grafico.style.background = '#e9ecef';
  grafico.setAttribute('aria-label', total ? `Distribución de ${tipo}: ${total} personas` : 'Sin datos registrados');
  leyenda.replaceChildren();

  if (!total) {
    const sinDatos = document.createElement('span');
    sinDatos.className = 'text-muted';
    sinDatos.textContent = 'No hay datos registrados para este evento.';
    leyenda.append(sinDatos);
    return;
  }

  let inicio = 0;
  const segmentos = datos.map((fila, indice) => {
    const fin = inicio + (Number(fila.total) / total * 100);
    const tramo = `${coloresDatosEvento[indice % coloresDatosEvento.length]} ${inicio.toFixed(3)}% ${fin.toFixed(3)}%`;
    inicio = fin;
    return tramo;
  });
  grafico.style.background = `conic-gradient(${segmentos.join(', ')})`;

  datos.forEach((fila, indice) => {
    const item = document.createElement('div');
    item.className = 'd-flex align-items-center gap-2 mb-1';
    const color = document.createElement('span');
    color.className = 'rounded-circle flex-shrink-0';
    color.style.cssText = `width:10px;height:10px;background:${coloresDatosEvento[indice % coloresDatosEvento.length]}`;
    const etiqueta = document.createElement('span');
    etiqueta.className = 'flex-grow-1';
    etiqueta.textContent = fila.etiqueta;
    const cantidad = document.createElement('strong');
    cantidad.textContent = String(fila.total);
    item.append(color, etiqueta, cantidad);
    leyenda.append(item);
  });
}

async function cargarDatosEvento(boton) {
  const idEvento = Number(boton.dataset.idEvento);
  if (!idEvento) return;
  const estado = document.getElementById('datos-evento-estado');
  document.querySelectorAll('.datos-evento-selector').forEach((elemento) => elemento.classList.toggle('active', elemento === boton));
  estado.className = 'alert alert-light border d-flex align-items-center gap-2 mb-3';
  estado.innerHTML = '<span class="spinner-border spinner-border-sm text-uab-azul" aria-hidden="true"></span><span>Cargando datos del evento…</span>';

  try {
    const respuesta = await fetch(`index.php?action=admin_datos_evento&id_evento=${encodeURIComponent(idEvento)}`, { headers: { Accept: 'application/json' } });
    const contenido = await respuesta.json();
    if (!respuesta.ok || !contenido.ok) throw new Error(contenido.mensaje || 'No fue posible cargar los datos.');
    renderizarPieDatosEvento('genero', contenido.datos.genero);
    renderizarPieDatosEvento('condicion_academica', contenido.datos.condicion_academica);
    renderizarPieDatosEvento('edad', contenido.datos.edad);
    estado.className = 'alert alert-info border d-flex align-items-center gap-2 mb-3';
    estado.innerHTML = '';
    const icono = document.createElement('i');
    icono.className = 'bi bi-info-circle-fill';
    const texto = document.createElement('span');
    texto.textContent = `${contenido.evento.titulo}: ${contenido.datos.total} persona(s) inscrita(s).`;
    estado.append(icono, texto);
  } catch (error) {
    estado.className = 'alert alert-danger d-flex align-items-center gap-2 mb-3';
    estado.innerHTML = '';
    const icono = document.createElement('i');
    icono.className = 'bi bi-exclamation-triangle-fill';
    const texto = document.createElement('span');
    texto.textContent = error.message || 'No fue posible cargar los datos del evento.';
    estado.append(icono, texto);
    ['genero', 'condicion_academica', 'edad'].forEach((tipo) => renderizarPieDatosEvento(tipo, []));
  }
}

document.querySelectorAll('.datos-evento-selector').forEach((boton) => {
  boton.addEventListener('click', () => cargarDatosEvento(boton));
});
const idEventoDatosSeleccionado = <?= (int)($idEventoDatosSeleccionado ?? 0) ?>;
const botonDatosInicial = idEventoDatosSeleccionado
  ? document.querySelector(`.datos-evento-selector[data-id-evento="${idEventoDatosSeleccionado}"]`)
  : document.querySelector('.datos-evento-selector');
if (botonDatosInicial) {
  botonDatosInicial.click();
} else if (idEventoDatosSeleccionado) {
  cargarDatosEvento({ dataset: { idEvento: String(idEventoDatosSeleccionado) } });
}
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
