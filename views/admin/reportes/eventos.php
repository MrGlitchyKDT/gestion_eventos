<?php
$titulo_pagina = 'Eventos para reportes — DIE-CERT';
$seccion_activa = 'reportes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';

$parametrosEventos = [];
if ($filtrosReporte['id_categoria']) $parametrosEventos['id_categoria'] = $filtrosReporte['id_categoria'];
if ($filtrosReporte['id_tipo_evento']) $parametrosEventos['id_tipo_evento'] = $filtrosReporte['id_tipo_evento'];
if ($filtrosReporte['habilitado']) $parametrosEventos['habilitado'] = $filtrosReporte['habilitado'];
if ($filtrosReporte['buscar'] !== '') $parametrosEventos['buscar'] = $filtrosReporte['buscar'];
if ($filtrosReporte['fecha_desde']) $parametrosEventos['fecha_desde'] = $filtrosReporte['fecha_desde'];
if ($filtrosReporte['fecha_hasta']) $parametrosEventos['fecha_hasta'] = $filtrosReporte['fecha_hasta'];
$consultaEventos = http_build_query($parametrosEventos);
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <a class="small text-decoration-none" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaEventos) ?>#reportes-evento"><i class="bi bi-arrow-left me-1"></i>Volver a reportes</a>
      <h1 class="h3 fw-bold text-uab-azul mt-2 mb-1">Todos los eventos para reportes</h1>
      <p class="text-muted mb-0">Abra un evento para consultar sus sesiones, asistencia y certificados.</p>
    </div>
    <a class="btn btn-outline-success no-print" href="index.php?action=admin_exportar_reporte&amp;tipo=eventos&amp;<?= htmlspecialchars($consultaEventos) ?>"><i class="bi bi-filetype-csv me-1"></i>CSV de eventos</a>
  </div>

  <form method="get" class="card border-0 shadow-sm mb-3 no-print">
    <input type="hidden" name="action" value="admin_reporte_eventos">
    <div class="card-body row g-3 align-items-end">
      <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="buscar-reporte-eventos">Buscar por nombre</label><input class="form-control" id="buscar-reporte-eventos" name="buscar" type="search" value="<?= htmlspecialchars($filtrosReporte['buscar']) ?>" placeholder="Nombre del evento"></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="tipo-reporte-eventos">Tipo de evento</label><select class="form-select" id="tipo-reporte-eventos" name="id_tipo_evento"><option value="">Todos los tipos</option><?php foreach ($tiposReporte as $tipo): ?><option value="<?= (int)$tipo['id_tipo_evento'] ?>" <?= $filtrosReporte['id_tipo_evento'] === (int)$tipo['id_tipo_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option><?php endforeach; ?></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="area-reporte-eventos">Área</label><select class="form-select" id="area-reporte-eventos" name="id_categoria"><option value="">Todas las áreas</option><?php foreach ($areasReporte as $area): ?><option value="<?= (int)$area['id_categoria'] ?>" <?= $filtrosReporte['id_categoria'] === (int)$area['id_categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($area['nombre']) ?><?= !(int)$area['activo'] ? ' (inactiva)' : '' ?></option><?php endforeach; ?></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="habilitado-reporte-eventos">Habilitado</label><select class="form-select" id="habilitado-reporte-eventos" name="habilitado"><option value="">Todos</option><option value="SI" <?= $filtrosReporte['habilitado'] === 'SI' ? 'selected' : '' ?>>Habilitado</option><option value="NO" <?= $filtrosReporte['habilitado'] === 'NO' ? 'selected' : '' ?>>No habilitado</option></select></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="fecha-desde-reporte-eventos">Desde</label><input class="form-control" id="fecha-desde-reporte-eventos" name="fecha_desde" type="date" value="<?= htmlspecialchars($filtrosReporte['fecha_desde'] ?? '') ?>"></div>
      <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="fecha-hasta-reporte-eventos">Hasta</label><input class="form-control" id="fecha-hasta-reporte-eventos" name="fecha_hasta" type="date" value="<?= htmlspecialchars($filtrosReporte['fecha_hasta'] ?? '') ?>"></div>
      <div class="col-12 col-xl-1 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1" title="Filtrar"><i class="bi bi-funnel"></i><span class="visually-hidden">Filtrar</span></button><a class="btn btn-outline-secondary" href="index.php?action=admin_reporte_eventos" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
    </div>
  </form>

  <section class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><strong class="text-uab-azul"><?= count($eventosReporte) ?> evento<?= count($eventosReporte) === 1 ? '' : 's' ?> encontrado<?= count($eventosReporte) === 1 ? '' : 's' ?></strong></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Evento</th><th>Área</th><th>Fechas</th><th class="text-center">Sesiones</th><th class="text-center">Certificados</th><th class="text-end">Detalle</th></tr></thead><tbody>
      <?php if (!$eventosReporte): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay eventos para los filtros seleccionados.</td></tr><?php endif; ?>
      <?php foreach ($eventosReporte as $evento): ?><tr><td><strong><?= htmlspecialchars($evento['titulo']) ?></strong><div class="small text-muted"><code><?= htmlspecialchars($evento['codigo']) ?></code> · <?= htmlspecialchars($evento['tipo_evento']) ?> · <span class="badge bg-light text-dark border"><?= htmlspecialchars($evento['estado']) ?></span></div></td><td><?= htmlspecialchars($evento['area']) ?></td><td class="small text-nowrap"><?= date('d/m/Y', strtotime($evento['fecha_inicio'])) ?><br><?= date('d/m/Y', strtotime($evento['fecha_fin'])) ?></td><td class="text-center"><?= (int)$evento['total_sesiones'] ?></td><td class="text-center"><?= (int)$evento['certificados_emitidos'] ?></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_reporte_evento&amp;<?= htmlspecialchars($consultaEventos) ?>&amp;id_evento=<?= (int)$evento['id_evento'] ?>"><i class="bi bi-folder2-open me-1"></i>Abrir</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
  </section>
</main>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
