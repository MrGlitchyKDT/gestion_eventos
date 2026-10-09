<?php
$titulo_pagina = 'Asistencia de sesión — DIE-CERT';
$seccion_activa = 'reportes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
$parametrosFiltro = [];
if ($filtrosReporte['id_categoria']) $parametrosFiltro['id_categoria'] = $filtrosReporte['id_categoria'];
if ($filtrosReporte['id_tipo_evento']) $parametrosFiltro['id_tipo_evento'] = $filtrosReporte['id_tipo_evento'];
if ($filtrosReporte['habilitado']) $parametrosFiltro['habilitado'] = $filtrosReporte['habilitado'];
if ($filtrosReporte['buscar'] !== '') $parametrosFiltro['buscar'] = $filtrosReporte['buscar'];
if ($filtrosReporte['fecha_desde']) $parametrosFiltro['fecha_desde'] = $filtrosReporte['fecha_desde'];
if ($filtrosReporte['fecha_hasta']) $parametrosFiltro['fecha_hasta'] = $filtrosReporte['fecha_hasta'];
$consultaBase = http_build_query($parametrosFiltro);
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <a class="small text-decoration-none" href="index.php?action=admin_reporte_evento&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;<?= htmlspecialchars($consultaBase) ?>"><i class="bi bi-arrow-left me-1"></i>Volver a sesiones del evento</a>
      <h1 class="h3 fw-bold text-uab-azul mt-2 mb-1">Asistencia: <?= htmlspecialchars($sesionReporte['titulo']) ?></h1>
      <p class="text-muted mb-0"><?= htmlspecialchars($eventoReporte['titulo']) ?> · <?= date('d/m/Y', strtotime($sesionReporte['fecha'])) ?> · <?= substr($sesionReporte['hora_inicio'], 0, 5) ?>–<?= substr($sesionReporte['hora_fin'], 0, 5) ?></p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-danger" href="index.php?action=admin_reporte_asistencia_pdf&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesionReporte['id_sesion'] ?>"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
      <a class="btn btn-outline-success" href="index.php?action=admin_exportar_reporte&amp;tipo=asistencia_sesion&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesionReporte['id_sesion'] ?>"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
      <a class="btn btn-outline-success" href="index.php?action=admin_exportar_reporte&amp;tipo=asistencia_sesion&amp;formato=xlsx&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesionReporte['id_sesion'] ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    </div>
  </div>
  <section class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Participante</th><th>CI</th><th>Estado de inscripción</th><th>Asistencia</th><th>Observación</th><th>Registro</th></tr></thead><tbody><?php if (!$asistenciaSesionReporte): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay participantes para esta sesión.</td></tr><?php endif; ?><?php foreach ($asistenciaSesionReporte as $registro): ?><tr><td><strong><?= htmlspecialchars($registro['nombres'] . ' ' . $registro['apellidos']) ?></strong><div class="small text-muted"><?= htmlspecialchars($registro['correo']) ?></div></td><td><?= htmlspecialchars($registro['ci']) ?></td><td><span class="badge bg-light text-dark border"><?= htmlspecialchars($registro['estado_inscripcion']) ?></span></td><td><span class="badge <?= in_array($registro['estado_asistencia'], ['PRESENTE', 'JUSTIFICADO'], true) ? 'bg-success' : ($registro['estado_asistencia'] === 'FALTA' ? 'bg-danger' : 'bg-secondary') ?>"><?= htmlspecialchars($registro['estado_asistencia']) ?></span></td><td class="small"><?= htmlspecialchars($registro['observacion'] ?: '—') ?></td><td class="small text-nowrap"><?= $registro['fecha_registro'] ? date('d/m/Y H:i', strtotime($registro['fecha_registro'])) : '—' ?></td></tr><?php endforeach; ?></tbody></table></div></section>
</main>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
