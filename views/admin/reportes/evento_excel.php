<?php
$titulo_pagina = 'Detalle de evento — Reportes CERT-DIE';
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
$consultaCertificado = $consultaBase . ($filtroCertificado !== '' ? '&certificado=' . urlencode((string)$filtroCertificado) : '');
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <a class="small text-decoration-none" href="index.php?action=admin_reportes&amp;<?= htmlspecialchars($consultaBase) ?>"><i class="bi bi-arrow-left me-1"></i>Volver a reportes</a>
      <h1 class="h3 fw-bold text-uab-azul mt-2 mb-1"><?= htmlspecialchars($eventoReporte['titulo']) ?></h1>
      <p class="text-muted mb-0"><code><?= htmlspecialchars($eventoReporte['codigo']) ?></code> · <?= htmlspecialchars($eventoReporte['tipo_evento']) ?> · <?= htmlspecialchars($eventoReporte['area']) ?></p>
    </div>
    <span class="badge bg-uab-azul fs-6"><?= htmlspecialchars($eventoReporte['estado']) ?></span>
  </div>

  <section class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-calendar-week me-2"></i>Sesiones del evento</h2></div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Sesión</th><th>Fecha y horario</th><th>Estado</th><th class="text-center">Registros</th><th class="text-center">Presentes / justificados</th><th class="text-end">Descargas</th></tr></thead>
      <tbody>
        <?php if (!$sesionesReporte): ?><tr><td colspan="6" class="text-center text-muted py-4">Este evento no tiene sesiones registradas.</td></tr><?php endif; ?>
        <?php foreach ($sesionesReporte as $sesion): ?>
          <tr>
            <td><strong><?= htmlspecialchars($sesion['titulo']) ?></strong></td>
            <td class="small text-nowrap"><?= date('d/m/Y', strtotime($sesion['fecha'])) ?><br><?= substr($sesion['hora_inicio'], 0, 5) ?>–<?= substr($sesion['hora_fin'], 0, 5) ?></td>
            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($sesion['estado']) ?></span></td>
            <td class="text-center"><?= (int)$sesion['registros'] ?></td>
            <td class="text-center"><?= (int)$sesion['asistencias_validas'] ?></td>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="index.php?action=admin_reporte_asistencia_sesion&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesion['id_sesion'] ?>&amp;<?= htmlspecialchars($consultaBase) ?>"><i class="bi bi-list-ul me-1"></i>Ver</a>
              <a class="btn btn-sm btn-outline-danger" title="Descargar PDF" href="index.php?action=admin_reporte_asistencia_pdf&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesion['id_sesion'] ?>"><i class="bi bi-filetype-pdf"></i></a>
              <a class="btn btn-sm btn-outline-success" title="Descargar CSV" href="index.php?action=admin_exportar_reporte&amp;tipo=asistencia_sesion&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesion['id_sesion'] ?>"><i class="bi bi-filetype-csv"></i></a>
              <a class="btn btn-sm btn-outline-success" title="Descargar Excel" href="index.php?action=admin_exportar_reporte&amp;tipo=asistencia_sesion&amp;formato=xlsx&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;id_sesion=<?= (int)$sesion['id_sesion'] ?>"><i class="bi bi-file-earmark-excel"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>

  <section class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-award me-2"></i>Certificaciones emitidas</h2>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_reporte_certificados_pdf&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;<?= htmlspecialchars($consultaCertificado) ?>"><i class="bi bi-filetype-pdf me-1"></i>Imprimir PDF</a>
          <a class="btn btn-sm btn-outline-success" href="index.php?action=admin_exportar_reporte&amp;tipo=certificados_evento&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;<?= htmlspecialchars($consultaCertificado) ?>"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
          <a class="btn btn-sm btn-outline-success" href="index.php?action=admin_exportar_reporte&amp;tipo=certificados_evento&amp;formato=xlsx&amp;id_evento=<?= (int)$eventoReporte['id_evento'] ?>&amp;<?= htmlspecialchars($consultaCertificado) ?>"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        </div>
      </div>
      <form class="row g-2 align-items-end mt-2" method="get">
        <input type="hidden" name="action" value="admin_reporte_evento">
        <input type="hidden" name="id_evento" value="<?= (int)$eventoReporte['id_evento'] ?>">
        <?php foreach ($parametrosFiltro as $clave => $valor): ?><input type="hidden" name="<?= htmlspecialchars($clave) ?>" value="<?= htmlspecialchars((string)$valor) ?>"><?php endforeach; ?>
        <div class="col-12 col-md-6"><label class="form-label small mb-1" for="certificado">Certificado para listar o imprimir</label><select class="form-select form-select-sm" id="certificado" name="certificado"><option value="">Todos los certificados</option><?php foreach ($tiposCertificado as $tipo): $valor = $tipo['id_plantilla'] === null ? 'historico' : (string)$tipo['id_plantilla']; ?><option value="<?= htmlspecialchars($valor) ?>" <?= (string)$filtroCertificado === $valor ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre_certificado']) ?> (<?= (int)$tipo['total'] ?>)</option><?php endforeach; ?></select></div>
        <div class="col-12 col-md-auto"><button class="btn btn-sm btn-uab-azul"><i class="bi bi-funnel me-1"></i>Filtrar</button></div>
      </form>
    </div>
    <div class="table-responsive"><table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Certificado</th><th>Titular</th><th>Tipo</th><th>Folio</th><th>Emisión</th><th>Estado</th></tr></thead>
      <tbody>
        <?php if (!$certificadosReporte): ?><tr><td colspan="6" class="text-center text-muted py-4">No hay certificaciones para el filtro seleccionado.</td></tr><?php endif; ?>
        <?php foreach ($certificadosReporte as $certificado): ?><tr><td><strong><?= htmlspecialchars($certificado['nombre_plantilla']) ?></strong><div class="small text-muted"><?= htmlspecialchars($certificado['destinatario_plantilla']) ?></div></td><td><?= htmlspecialchars($certificado['nombres'] . ' ' . $certificado['apellidos']) ?><div class="small text-muted"><?= htmlspecialchars($certificado['ci']) ?></div></td><td><span class="badge <?= $certificado['tipo_participacion'] === 'EXPOSITOR' ? 'bg-info text-dark' : 'bg-success' ?>"><?= htmlspecialchars($certificado['tipo_participacion']) ?></span></td><td><code><?= htmlspecialchars($certificado['codigo_unico']) ?></code></td><td class="small text-nowrap"><?= date('d/m/Y H:i', strtotime($certificado['fecha_emision'])) ?></td><td><span class="badge <?= $certificado['estado'] === 'EMITIDO' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($certificado['estado']) ?></span></td></tr><?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
</main>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
