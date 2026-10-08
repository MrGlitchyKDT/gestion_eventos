<?php
$titulo_pagina = 'Datos de eventos — CERT-DIE';
$seccion_activa = 'reportes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';

$parametrosDatos = [];
if ($filtrosDatos['id_categoria']) $parametrosDatos['datos_id_categoria'] = $filtrosDatos['id_categoria'];
if ($filtrosDatos['id_tipo_evento']) $parametrosDatos['datos_id_tipo_evento'] = $filtrosDatos['id_tipo_evento'];
if ($filtrosDatos['habilitado']) $parametrosDatos['datos_habilitado'] = $filtrosDatos['habilitado'];
if ($filtrosDatos['buscar'] !== '') $parametrosDatos['datos_buscar'] = $filtrosDatos['buscar'];
if ($filtrosDatos['fecha_desde']) $parametrosDatos['datos_fecha_desde'] = $filtrosDatos['fecha_desde'];
if ($filtrosDatos['fecha_hasta']) $parametrosDatos['datos_fecha_hasta'] = $filtrosDatos['fecha_hasta'];
$consultaDatos = http_build_query($parametrosDatos);
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <a class="small text-decoration-none" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaDatos) ?>#datos-eventos"><i class="bi bi-arrow-left me-1"></i>Volver a reportes</a>
      <h1 class="h3 fw-bold text-uab-azul mt-2 mb-1">Todos los eventos para datos</h1>
      <p class="text-muted mb-0">Seleccione un evento para analizar el género, condición académica y rango de edad de sus inscritos.</p>
    </div>
  </div>

  <form method="get" class="card border-0 shadow-sm mb-3 no-print">
    <input type="hidden" name="action" value="admin_datos_eventos">
    <div class="card-body row g-3 align-items-end">
      <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="datos-lista-buscar">Buscar por nombre</label><input class="form-control" id="datos-lista-buscar" name="datos_buscar" type="search" value="<?= htmlspecialchars($filtrosDatos['buscar']) ?>" placeholder="Nombre del evento"></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-lista-tipo">Tipo de evento</label><select class="form-select" id="datos-lista-tipo" name="datos_id_tipo_evento"><option value="">Todos los tipos</option><?php foreach ($tiposReporte as $tipo): ?><option value="<?= (int)$tipo['id_tipo_evento'] ?>" <?= $filtrosDatos['id_tipo_evento'] === (int)$tipo['id_tipo_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option><?php endforeach; ?></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-lista-area">Área</label><select class="form-select" id="datos-lista-area" name="datos_id_categoria"><option value="">Todas las áreas</option><?php foreach ($areasReporte as $area): ?><option value="<?= (int)$area['id_categoria'] ?>" <?= $filtrosDatos['id_categoria'] === (int)$area['id_categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?></option><?php endforeach; ?></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-lista-habilitado">Habilitado</label><select class="form-select" id="datos-lista-habilitado" name="datos_habilitado"><option value="">Todos</option><option value="SI" <?= $filtrosDatos['habilitado'] === 'SI' ? 'selected' : '' ?>>Habilitado</option><option value="NO" <?= $filtrosDatos['habilitado'] === 'NO' ? 'selected' : '' ?>>No habilitado</option></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-lista-desde">Desde</label><input class="form-control" id="datos-lista-desde" name="datos_fecha_desde" type="date" value="<?= htmlspecialchars($filtrosDatos['fecha_desde'] ?? '') ?>"></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="datos-lista-hasta">Hasta</label><input class="form-control" id="datos-lista-hasta" name="datos_fecha_hasta" type="date" value="<?= htmlspecialchars($filtrosDatos['fecha_hasta'] ?? '') ?>"></div>
      <div class="col-12 col-xl-1 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1" title="Filtrar"><i class="bi bi-funnel"></i><span class="visually-hidden">Filtrar</span></button><a class="btn btn-outline-secondary" href="index.php?action=admin_datos_eventos" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
    </div>
  </form>

  <section class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><strong class="text-uab-azul"><?= count($eventosDatos) ?> evento<?= count($eventosDatos) === 1 ? '' : 's' ?> encontrado<?= count($eventosDatos) === 1 ? '' : 's' ?></strong></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Evento</th><th>Área</th><th>Fechas</th><th class="text-center">Inscritos</th><th class="text-end">Datos</th></tr></thead><tbody>
      <?php if (!$eventosDatos): ?><tr><td colspan="5" class="text-center text-muted py-4">No hay eventos para los filtros seleccionados.</td></tr><?php endif; ?>
      <?php foreach ($eventosDatos as $evento): ?><tr><td><strong><?= htmlspecialchars($evento['titulo']) ?></strong><div class="small text-muted"><code><?= htmlspecialchars($evento['codigo']) ?></code> · <?= htmlspecialchars($evento['tipo_evento']) ?></div></td><td><?= htmlspecialchars($evento['area']) ?></td><td class="small text-nowrap"><?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br><?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?></td><td class="text-center"><?= (int)$evento['total_inscritos'] ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaDatos) ?>&amp;datos_evento_id=<?= (int)$evento['id_evento'] ?>#datos-eventos"><i class="bi bi-bar-chart-fill me-1"></i>Analizar</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </section>
</main>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
