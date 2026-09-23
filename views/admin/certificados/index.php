<?php
$titulo_pagina = 'Emisión de certificados — Admin UAB';
$seccion_activa = 'eventos';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
$configuracion = $plantilla ? (json_decode($plantilla['configuracion_campos'], true) ?: []) : [];
?>
<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div><h1 class="h3 fw-bold text-uab-azul mb-0">Emisión de certificados</h1><p class="text-muted small mb-0">Plantilla PDF, participantes habilitados y documentos emitidos.</p></div>
    <a href="index.php?action=admin_eventos" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Eventos</a>
  </div>
  <?php foreach (['success'=>'success','error'=>'danger'] as $clave=>$tipo): if (!empty($_SESSION[$clave])): ?>
    <div class="alert alert-<?= $tipo ?>"><?= htmlspecialchars($_SESSION[$clave]); unset($_SESSION[$clave]); ?></div>
  <?php endif; endforeach; ?>
  <form class="card border-0 shadow-sm mb-4" method="get">
    <input type="hidden" name="action" value="admin_certificados"><div class="card-body d-flex gap-3 align-items-end flex-wrap">
      <div class="flex-grow-1"><label class="form-label fw-semibold">Evento</label><select class="form-select" name="id_evento" onchange="this.form.submit()"><option value="">Seleccione un evento</option><?php foreach ($eventos as $fila): ?><option value="<?= $fila['id_evento'] ?>" <?= $idEvento === (int)$fila['id_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($fila['codigo'] . ' — ' . $fila['titulo']) ?></option><?php endforeach; ?></select></div>
    </div>
  </form>
  <?php if ($evento): ?>
    <?php if (!(int)$evento['emite_certificado']): ?><div class="alert alert-warning">Este evento está configurado sin emisión de certificados.</div><?php endif; ?>
    <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-file-earmark-pdf me-2"></i>Plantilla PDF del evento</h2></div><div class="card-body">
      <?php if ($plantilla): ?><div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3"><p class="mb-0"><i class="bi bi-check-circle-fill text-success me-1"></i>Plantilla activa: <strong><?= htmlspecialchars($plantilla['nombre_archivo']) ?></strong>, página <?= (int)$plantilla['pagina'] ?>.</p><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditorCertificado"><i class="bi bi-arrows-move me-1"></i> Editar posiciones</button></div><?php else: ?><p class="text-muted">Aún no existe una plantilla. No se podrán emitir certificados hasta cargarla.</p><?php endif; ?>
      <form action="index.php?action=admin_plantilla_certificado_guardar" method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_evento" value="<?= $idEvento ?>">
        <div class="col-md-7"><label class="form-label">Plantilla PDF (máximo 10 MB)</label><input class="form-control" type="file" name="plantilla" accept="application/pdf" required></div><div class="col-md-2"><label class="form-label">Página</label><input class="form-control" type="number" min="1" name="pagina" value="<?= $plantilla['pagina'] ?? 1 ?>"></div><div class="col-md-3 d-flex align-items-end"><button class="btn btn-uab-azul w-100"><i class="bi bi-upload me-1"></i><?= $plantilla ? 'Reemplazar' : 'Cargar plantilla' ?></button></div>
        <div class="col-12"><p class="small text-muted mb-0">Las posiciones iniciales están preparadas para una hoja A4 horizontal. Podrán ajustarse en una siguiente mejora mediante vista previa visual.</p></div>
      </form>
    </div></section>
    <section class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-person-check me-2"></i>Participantes habilitados (<?= count($habilitados) ?>)</h2></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Participante</th><th>CI</th><th>Asistencia</th><th class="text-end">Emisión</th></tr></thead><tbody>
      <?php if (!$habilitados): ?><tr><td colspan="4" class="text-center text-muted py-4">No hay participantes habilitados pendientes.</td></tr><?php endif; ?>
      <?php foreach ($habilitados as $persona): ?><tr><td><strong><?= htmlspecialchars($persona['nombres'].' '.$persona['apellidos']) ?></strong><div class="small text-muted"><?= htmlspecialchars($persona['correo']) ?></div></td><td><?= htmlspecialchars($persona['ci']) ?></td><td><?= number_format((float)$persona['porcentaje_asistencia'], 2) ?>%</td><td class="text-end"><form method="post" action="index.php?action=admin_certificado_emitir_pdf"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_evento" value="<?= $idEvento ?>"><input type="hidden" name="id_inscripcion" value="<?= $persona['id_inscripcion'] ?>"><button class="btn btn-sm btn-uab-azul" <?= !$plantilla ? 'disabled title="Cargue una plantilla PDF primero"' : '' ?>><i class="bi bi-award me-1"></i> Emitir PDF</button></form></td></tr><?php endforeach; ?>
    </tbody></table></div></section>
    <section class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-award me-2"></i>Certificados emitidos (<?= count($emitidos) ?>)</h2></div><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Folio</th><th>Titular</th><th>Fecha</th><th>Estado</th><th class="text-end">Archivo</th></tr></thead><tbody>
      <?php if (!$emitidos): ?><tr><td colspan="5" class="text-center text-muted py-4">Aún no se emitieron certificados.</td></tr><?php endif; ?>
      <?php foreach ($emitidos as $cert): ?><tr><td><code><?= htmlspecialchars($cert['codigo_unico']) ?></code></td><td><?= htmlspecialchars($cert['nombres'].' '.$cert['apellidos']) ?></td><td><?= date('d/m/Y H:i', strtotime($cert['fecha_emision'])) ?></td><td><span class="badge <?= $cert['estado'] === 'EMITIDO' ? 'bg-success' : 'bg-danger' ?>"><?= htmlspecialchars($cert['estado']) ?></span></td><td class="text-end"><?php if ($cert['estado'] === 'EMITIDO'): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="index.php?action=descargar_certificado&id=<?= $cert['id_certificado'] ?>"><i class="bi bi-printer me-1"></i> Abrir PDF</a> <form class="d-inline" method="post" action="index.php?action=admin_certificado_regenerar_pdf"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_evento" value="<?= $idEvento ?>"><input type="hidden" name="id_certificado" value="<?= $cert['id_certificado'] ?>"><button class="btn btn-sm btn-outline-secondary" title="Regenerar con el diseño actual"><i class="bi bi-arrow-repeat"></i></button></form><?php endif; ?></td></tr><?php endforeach; ?>
    </tbody></table></div></section>
  <?php endif; ?>
</main>
<?php if ($plantilla): ?>
<div class="modal fade" id="modalEditorCertificado" tabindex="-1" aria-labelledby="tituloEditorCertificado" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
    <form method="post" action="index.php?action=admin_plantilla_certificado_diseno">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_evento" value="<?= $idEvento ?>"><input type="hidden" id="configuracion_campos" name="configuracion_campos">
      <div class="modal-header bg-uab-azul text-white"><h2 class="modal-title h5" id="tituloEditorCertificado"><i class="bi bi-arrows-move me-2"></i>Editor visual de posiciones</h2><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><p class="small text-muted">Arrastre cada etiqueta hasta su posición en la plantilla. Las guías corresponden a milímetros de un certificado horizontal; los cambios se aplican a futuras emisiones.</p>
        <div id="editor-certificado" class="cert-editor-canvas" style="aspect-ratio: <?= (float)$lienzo['ancho'] ?> / <?= (float)$lienzo['alto'] ?>;">
          <object data="index.php?action=admin_plantilla_certificado_ver&id_evento=<?= $idEvento ?>#page=<?= (int)$plantilla['pagina'] ?>&zoom=page-width" type="application/pdf" class="cert-editor-pdf"><span>Su navegador no puede mostrar la plantilla PDF.</span></object>
          <?php $etiquetas=['nombre'=>'Nombre completo','ci'=>'C.I.','evento'=>'Nombre del evento','horas'=>'Carga horaria','fechas'=>'Fechas','codigo'=>'Folio','validacion'=>'URL de validación','qr'=>'Código QR']; foreach ($etiquetas as $clave=>$texto): $campo=$configuracion[$clave] ?? []; ?>
            <button type="button" class="cert-editor-field <?= empty($campo['activo']) ? 'd-none' : '' ?>" data-campo="<?= $clave ?>" data-x="<?= (float)($campo['x'] ?? 20) ?>" data-y="<?= (float)($campo['y'] ?? 20) ?>" title="Arrastre para mover"><?= htmlspecialchars($texto) ?></button>
          <?php endforeach; ?>
        </div>
        <div class="row g-2 mt-3" id="editor-controles"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-uab-azul"><i class="bi bi-save me-1"></i> Guardar posiciones</button></div>
    </form>
  </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('editor-certificado'); if (!canvas) return;
  const config = <?= json_encode($configuracion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
  const ancho = <?= json_encode((float)$lienzo['ancho']) ?>, alto = <?= json_encode((float)$lienzo['alto']) ?>;
  const campos = [...canvas.querySelectorAll('.cert-editor-field')];
  const ubicar = (el) => { el.style.left = (Number(el.dataset.x) / ancho * 100) + '%'; el.style.top = (Number(el.dataset.y) / alto * 100) + '%'; };
  campos.forEach(el => { const campo=config[el.dataset.campo] || {}; if (campo.ancla !== 'centro') { const w=Number(campo.ancho || 0); if (campo.alineacion === 'C') el.dataset.x=Number(el.dataset.x)+w/2; else if (campo.alineacion === 'R') el.dataset.x=Number(el.dataset.x)+w; } });
  campos.forEach(ubicar);
  let activo = null;
  canvas.addEventListener('pointerdown', (e) => { const etiqueta=e.target.closest('.cert-editor-field'); if (!etiqueta) return; activo=etiqueta; etiqueta.setPointerCapture(e.pointerId); e.preventDefault(); });
  canvas.addEventListener('pointermove', (e) => { if (!activo) return; const r=canvas.getBoundingClientRect(); activo.dataset.x=Math.max(0,Math.min(ancho,(e.clientX-r.left)/r.width*ancho)); activo.dataset.y=Math.max(0,Math.min(alto,(e.clientY-r.top)/r.height*alto)); ubicar(activo); });
  canvas.addEventListener('pointerup', () => activo=null);
  canvas.closest('form').addEventListener('submit', () => { campos.forEach(el => { const k=el.dataset.campo; config[k] = Object.assign({activo:true,x:20,y:20,ancho:k==='qr'?28:140,tamano:k==='nombre'?22:10,alineacion:'C',negrita:k==='nombre'||k==='evento'}, config[k] || {}); config[k].x=Number(el.dataset.x); config[k].y=Number(el.dataset.y); config[k].activo=!el.classList.contains('d-none'); config[k].ancla='centro'; }); config._lienzo={ancho,alto}; document.getElementById('configuracion_campos').value=JSON.stringify(config); });
});
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
