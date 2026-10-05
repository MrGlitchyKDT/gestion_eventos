<?php
$titulo_pagina = 'Buzón de reimpresiones — CERT-DIE';
$seccion_activa = 'admin_reimpresiones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
$etiquetas = ['' => 'Todas', 'PENDIENTE' => 'Pendientes', 'APROBADA' => 'Por entregar', 'RECHAZADA' => 'Rechazadas', 'ENTREGADA' => 'Entregadas'];
$clasesEstado = ['PENDIENTE' => 'bg-warning text-dark', 'APROBADA' => 'bg-primary', 'RECHAZADA' => 'bg-danger', 'ENTREGADA' => 'bg-success'];
?>

<main class="container-xl px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div>
      <h1 class="h3 fw-bold text-uab-azul mb-1"><i class="bi bi-inbox me-2"></i>Buzón de reimpresiones</h1>
      <p class="text-muted mb-0">Revise comprobantes, apruebe la reimpresión y registre la entrega presencial.</p>
    </div>
    <a class="btn btn-outline-primary" href="index.php?action=admin_certificados"><i class="bi bi-printer me-1"></i>Gestionar certificados</a>
  </div>

  <?php foreach (['success' => 'success', 'error' => 'danger'] as $clave => $tipo): ?>
    <?php if (!empty($_SESSION[$clave])): ?>
      <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION[$clave]); unset($_SESSION[$clave]); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <section class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
      <?php foreach ($etiquetas as $valor => $etiqueta): ?>
        <?php $activo = $estado === $valor; $cantidad = $valor === '' ? array_sum($conteoEstados) : ($conteoEstados[$valor] ?? 0); ?>
        <a href="index.php?action=admin_reimpresiones<?= $valor !== '' ? '&estado=' . urlencode($valor) : '' ?>" class="btn <?= $activo ? 'btn-uab-azul' : 'btn-outline-secondary' ?>">
          <?= htmlspecialchars($etiqueta) ?> <span class="badge <?= $activo ? 'bg-white text-uab-azul' : 'bg-secondary' ?> ms-1"><?= $cantidad ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex align-items-center gap-2"><i class="bi bi-list-check text-uab-azul"></i><h2 class="h5 mb-0">Solicitudes <?= $estado ? strtolower($etiquetas[$estado]) : 'registradas' ?></h2></div>
    <?php if (empty($solicitudes)): ?>
      <div class="card-body text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay solicitudes en este estado.</div>
    <?php else: ?>
      <div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Participante</th><th>Certificado / evento</th><th>Solicitud</th><th>Comprobante</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
        <tbody><?php foreach ($solicitudes as $solicitud): ?><tr>
          <td><strong><?= htmlspecialchars(trim($solicitud['nombres'] . ' ' . $solicitud['apellidos'])) ?></strong><div class="small text-muted">CI <?= htmlspecialchars($solicitud['ci']) ?> · <?= htmlspecialchars($solicitud['correo']) ?></div></td>
          <td><code><?= htmlspecialchars($solicitud['codigo_unico']) ?></code><div class="small text-muted mt-1"><?= htmlspecialchars($solicitud['evento_titulo']) ?></div></td>
          <td class="small"><div><?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></div><div class="text-muted text-break mt-1"><?= nl2br(htmlspecialchars($solicitud['motivo'])) ?></div></td>
          <td><?php if (!empty($solicitud['ruta_comprobante'])): ?><a target="_blank" class="btn btn-sm btn-outline-primary" href="index.php?action=admin_reimpresion_comprobante&id=<?= (int)$solicitud['id_solicitud'] ?>"><i class="bi bi-paperclip me-1"></i>Ver</a><div class="small text-muted mt-1"><?= htmlspecialchars($solicitud['nombre_comprobante'] ?: 'Archivo adjunto') ?></div><?php else: ?><span class="text-muted small">No disponible</span><?php endif; ?></td>
          <td><span class="badge <?= $clasesEstado[$solicitud['estado']] ?? 'bg-secondary' ?>"><?= htmlspecialchars($solicitud['estado']) ?></span><?php if ($solicitud['estado'] === 'RECHAZADA' && !empty($solicitud['motivo_rechazo'])): ?><div class="small text-danger mt-1"><?= htmlspecialchars($solicitud['motivo_rechazo']) ?></div><?php endif; ?></td>
          <td class="text-end"><?php if ($solicitud['estado'] === 'PENDIENTE'): ?><form method="post" class="d-inline" action="index.php?action=admin_reimpresion_resolver"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_solicitud" value="<?= (int)$solicitud['id_solicitud'] ?>"><input type="hidden" name="decision" value="APROBADA"><button class="btn btn-sm btn-success" title="Aprobar"><i class="bi bi-check-lg"></i><span class="visually-hidden">Aprobar</span></button></form><button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#modalRechazar" data-id="<?= (int)$solicitud['id_solicitud'] ?>" data-participante="<?= htmlspecialchars(trim($solicitud['nombres'] . ' ' . $solicitud['apellidos']), ENT_QUOTES, 'UTF-8') ?>" title="Rechazar"><i class="bi bi-x-lg"></i><span class="visually-hidden">Rechazar</span></button><?php elseif ($solicitud['estado'] === 'APROBADA'): ?><a class="btn btn-sm btn-outline-primary" target="_blank" href="index.php?action=descargar_certificado&id=<?= (int)$solicitud['id_certificado'] ?>"><i class="bi bi-printer me-1"></i>Imprimir</a><form method="post" class="d-inline" action="index.php?action=admin_reimpresion_entregar"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_solicitud" value="<?= (int)$solicitud['id_solicitud'] ?>"><button class="btn btn-sm btn-success mt-1" title="Registrar entrega"><i class="bi bi-box-seam me-1"></i>Entregada</button></form><?php else: ?><span class="small text-muted">Sin acciones pendientes</span><?php endif; ?></td>
        </tr><?php endforeach; ?></tbody>
      </table></div>
    <?php endif; ?>
  </section>
</main>

<div class="modal fade" id="modalRechazar" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="post" action="index.php?action=admin_reimpresion_resolver"><div class="modal-header"><h2 class="modal-title h5">Rechazar solicitud</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input id="rechazo-id" type="hidden" name="id_solicitud"><input type="hidden" name="decision" value="RECHAZADA"><p class="small text-muted">Informe a <strong id="rechazo-participante"></strong> el motivo del rechazo.</p><label class="form-label" for="rechazo-motivo">Motivo</label><textarea id="rechazo-motivo" class="form-control" name="motivo_rechazo" maxlength="1000" rows="4" required></textarea></div><div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger">Rechazar solicitud</button></div></form></div></div>
<script>
document.getElementById('modalRechazar').addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  document.getElementById('rechazo-id').value = button.dataset.id;
  document.getElementById('rechazo-participante').textContent = button.dataset.participante;
  document.getElementById('rechazo-motivo').value = '';
});
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
