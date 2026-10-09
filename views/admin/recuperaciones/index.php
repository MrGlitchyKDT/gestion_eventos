<?php
$titulo_pagina = 'Buzón de recuperación de acceso — DIE-CERT';
$seccion_activa = 'recuperaciones';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
$etiquetasEstado = ['' => 'Todas', 'PENDIENTE' => 'Pendientes', 'APROBADA' => 'Aprobadas', 'RECHAZADA' => 'Rechazadas'];
$clasesEstado = ['PENDIENTE' => 'bg-warning text-dark', 'APROBADA' => 'bg-success', 'RECHAZADA' => 'bg-danger'];
?>
<main class="container-xl px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h1 class="h3 fw-bold text-uab-azul mb-1"><i class="bi bi-shield-lock me-2"></i>Buzón de recuperación de acceso</h1>
      <p class="text-muted mb-0">Verifique la identidad del solicitante antes de generar un enlace temporal.</p>
    </div>
    <a href="index.php?action=admin_usuarios" class="btn btn-outline-primary"><i class="bi bi-people me-1"></i>Gestión de usuarios</a>
  </div>

  <?php foreach (['success' => 'success', 'error' => 'danger'] as $clave => $tipo): ?>
    <?php if (!empty($_SESSION[$clave])): ?><div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert"><?= htmlspecialchars($_SESSION[$clave]); unset($_SESSION[$clave]); ?><button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Cerrar"></button></div><?php endif; ?>
  <?php endforeach; ?>

  <?php if (!empty($enlaceRecuperacionBuzon)): ?>
    <section class="alert alert-warning shadow-sm" role="alert">
      <h2 class="h6 fw-bold"><i class="bi bi-key me-1"></i>Enlace temporal listo</h2>
      <p class="small mb-2">Entréguelo únicamente a <?= htmlspecialchars($enlaceRecuperacionBuzon['nombre']) ?> (<?= htmlspecialchars($enlaceRecuperacionBuzon['correo']) ?>) después de verificar su documento. Vence el <?= date('d/m/Y H:i', strtotime($enlaceRecuperacionBuzon['expiracion'])) ?>.</p>
      <div class="input-group"><input id="enlace-recuperacion-buzon" class="form-control" value="<?= htmlspecialchars($enlaceRecuperacionBuzon['url']) ?>" readonly><button type="button" class="btn btn-outline-dark" onclick="navigator.clipboard.writeText(document.getElementById('enlace-recuperacion-buzon').value)"><i class="bi bi-copy me-1"></i>Copiar</button></div>
    </section>
  <?php endif; ?>

  <section class="card border-0 shadow-sm mb-4"><div class="card-body d-flex gap-2 flex-wrap">
    <?php foreach ($etiquetasEstado as $valor => $etiqueta): ?><a class="btn <?= $estado === $valor ? 'btn-uab-azul' : 'btn-outline-secondary' ?>" href="index.php?action=admin_solicitudes_recuperacion<?= $valor !== '' ? '&estado=' . urlencode($valor) : '' ?>"><?= htmlspecialchars($etiqueta) ?><?= $valor === 'PENDIENTE' ? ' (' . (int)$recuperacionesPendientes . ')' : '' ?></a><?php endforeach; ?>
  </div></section>

  <section class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-inbox me-2"></i>Solicitudes <?= $estado ? strtolower($etiquetasEstado[$estado]) : 'registradas' ?></h2></div>
    <?php if (!$solicitudesRecuperacion): ?><div class="card-body py-5 text-center text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay solicitudes en este estado.</div>
    <?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Solicitante</th><th>Cuenta</th><th>Solicitud</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead><tbody>
      <?php foreach ($solicitudesRecuperacion as $solicitud): ?><tr>
        <td><strong><?= htmlspecialchars($solicitud['nombres'] . ' ' . $solicitud['apellidos']) ?></strong><div class="small text-muted">CI <?= htmlspecialchars($solicitud['ci']) ?> · <?= htmlspecialchars($solicitud['telefono'] ?: 'Sin teléfono') ?></div></td>
        <td><div><?= htmlspecialchars($solicitud['correo']) ?></div><span class="small text-muted"><?= htmlspecialchars($solicitud['rol_nombre']) ?></span></td>
        <td class="small text-nowrap"><?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></td>
        <td><span class="badge <?= $clasesEstado[$solicitud['estado']] ?? 'bg-secondary' ?>"><?= htmlspecialchars($solicitud['estado']) ?></span><?php if ($solicitud['estado'] === 'RECHAZADA' && !empty($solicitud['motivo_rechazo'])): ?><div class="small text-danger mt-1"><?= htmlspecialchars($solicitud['motivo_rechazo']) ?></div><?php endif; ?></td>
        <td class="text-end"><?php if ($solicitud['estado'] === 'PENDIENTE'): ?><form class="d-inline" method="post" action="index.php?action=admin_solicitud_recuperacion_resolver"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input type="hidden" name="id_solicitud" value="<?= (int)$solicitud['id_solicitud'] ?>"><input type="hidden" name="decision" value="APROBADA"><button class="btn btn-sm btn-success" title="Aprobar y generar enlace"><i class="bi bi-check-lg me-1"></i>Aprobar</button></form><button class="btn btn-sm btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#modal-rechazar-recuperacion" data-id="<?= (int)$solicitud['id_solicitud'] ?>" data-nombre="<?= htmlspecialchars($solicitud['nombres'] . ' ' . $solicitud['apellidos'], ENT_QUOTES, 'UTF-8') ?>"><i class="bi bi-x-lg me-1"></i>Rechazar</button><?php else: ?><span class="small text-muted">Sin acciones pendientes</span><?php endif; ?></td>
      </tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?>
  </section>
</main>

<div class="modal fade" id="modal-rechazar-recuperacion" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="post" action="index.php?action=admin_solicitud_recuperacion_resolver"><div class="modal-header"><h2 class="modal-title h5">Rechazar solicitud</h2><button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input id="recuperacion-solicitud-id" type="hidden" name="id_solicitud"><input type="hidden" name="decision" value="RECHAZADA"><p class="small text-muted">Explique a <strong id="recuperacion-solicitud-nombre"></strong> por qué no se aprobó la solicitud.</p><label class="form-label" for="motivo-rechazo-recuperacion">Motivo</label><textarea id="motivo-rechazo-recuperacion" class="form-control" name="motivo_rechazo" maxlength="1000" rows="4" required></textarea></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger">Rechazar</button></div></form></div></div>
<script>
document.getElementById('modal-rechazar-recuperacion').addEventListener('show.bs.modal', function (event) {
  document.getElementById('recuperacion-solicitud-id').value = event.relatedTarget.dataset.id;
  document.getElementById('recuperacion-solicitud-nombre').textContent = event.relatedTarget.dataset.nombre;
  document.getElementById('motivo-rechazo-recuperacion').value = '';
});
</script>
<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
