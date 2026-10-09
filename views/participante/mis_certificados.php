<?php
$titulo_pagina = 'Mis certificados — DIE-CERT';
$seccion_activa = 'mis_certificados';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$solicitudEnProcesoPorCertificado = [];
foreach ($solicitudes as $solicitud) {
    if (in_array(($solicitud['estado'] ?? ''), ['PENDIENTE', 'APROBADA'], true)) {
        $solicitudEnProcesoPorCertificado[(int)$solicitud['id_certificado']] = $solicitud['estado'];
    }
}
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
      <h1 class="h3 fw-bold text-uab-azul mb-1"><i class="bi bi-award me-2"></i>Mis certificados</h1>
      <p class="text-muted mb-0">
        <?= !empty($requiereEntregaFisica) ? 'Consulte sus certificados emitidos y solicite una reimpresión si la necesita.' : 'Abra o imprima sus certificados oficiales emitidos.' ?>
      </p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
      <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalInstruccionesReimpresion">
        <i class="bi bi-question-circle me-1"></i>¿Cómo solicitar mi reimpresión?
      </button>
      <span class="badge bg-uab-azul fs-6 rounded-pill px-3 py-2"><?= count($certificados) ?> emitido<?= count($certificados) === 1 ? '' : 's' ?></span>
    </div>
  </div>

  <?php foreach (['success' => 'success', 'error' => 'danger'] as $clave => $tipo): ?>
    <?php if (!empty($_SESSION[$clave])): ?>
      <div class="alert alert-<?= $tipo ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION[$clave]); unset($_SESSION[$clave]); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if (empty($certificados)): ?>
    <section class="card border-0 shadow-sm text-center py-5">
      <div class="card-body">
        <i class="bi bi-patch-check text-muted" style="font-size: 3rem;"></i>
        <h2 class="h5 mt-3">Aún no tiene certificados emitidos</h2>
        <p class="text-muted mb-0">Los certificados aparecerán aquí cuando la administración los emita.</p>
      </div>
    </section>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($certificados as $certificado): ?>
        <article class="col-12 col-md-6 col-xl-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex flex-column p-4">
              <div class="d-flex justify-content-between gap-2 align-items-start mb-3">
                <span class="badge <?= $certificado['tipo_participacion'] === 'EXPOSITOR' ? 'bg-info text-dark' : 'bg-success' ?>">
                  <i class="bi <?= $certificado['tipo_participacion'] === 'EXPOSITOR' ? 'bi-person-video3' : 'bi-person-check' ?> me-1"></i>
                  <?= htmlspecialchars($certificado['tipo_participacion'] === 'EXPOSITOR' ? 'Expositor' : 'Participante') ?>
                </span>
                <code class="small text-end"><?= htmlspecialchars($certificado['codigo_unico']) ?></code>
              </div>
              <h2 class="h5 fw-bold mb-2"><?= htmlspecialchars($certificado['evento_titulo']) ?></h2>
              <p class="small text-muted mb-1"><i class="bi bi-file-earmark-text me-1"></i><?= htmlspecialchars($certificado['nombre_plantilla'] ?: 'Certificado oficial') ?></p>
              <p class="small text-muted mb-4"><i class="bi bi-calendar3 me-1"></i>Emitido el <?= date('d/m/Y', strtotime($certificado['fecha_emision'])) ?></p>
              <?php if (!empty($requiereEntregaFisica)): ?>
                <p class="small text-muted border-top pt-3 mb-3"><i class="bi bi-building me-1"></i>La entrega de certificados es presencial en la Dirección de Extensión.</p>
              <?php endif; ?>
              <div class="mt-auto d-flex flex-wrap gap-2">
                <?php if (empty($requiereEntregaFisica)): ?>
                  <a class="btn btn-uab-azul" target="_blank" href="index.php?action=descargar_certificado&id=<?= (int)$certificado['id_certificado'] ?>"><i class="bi bi-file-earmark-pdf me-1"></i>Abrir PDF</a>
                <?php endif; ?>
                <?php if (!empty($requiereEntregaFisica) && !isset($solicitudEnProcesoPorCertificado[(int)$certificado['id_certificado']])): ?>
                  <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalReimpresion" data-certificado="<?= (int)$certificado['id_certificado'] ?>" data-evento="<?= htmlspecialchars($certificado['evento_titulo'], ENT_QUOTES, 'UTF-8') ?>">
                    <i class="bi bi-arrow-repeat me-1"></i>Reimpresión
                  </button>
                <?php elseif (!empty($requiereEntregaFisica)): ?>
                  <?php $estadoSolicitud = $solicitudEnProcesoPorCertificado[(int)$certificado['id_certificado']]; ?>
                  <span class="btn btn-outline-secondary disabled"><i class="bi <?= $estadoSolicitud === 'APROBADA' ? 'bi-box-seam' : 'bi-hourglass-split' ?> me-1"></i><?= $estadoSolicitud === 'APROBADA' ? 'Lista para recoger' : 'En revisión' ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($solicitudes)): ?>
    <section class="card border-0 shadow-sm mt-4">
      <div class="card-header bg-white py-3"><h2 class="h5 mb-0 text-uab-azul"><i class="bi bi-clock-history me-2"></i>Solicitudes de reimpresión</h2></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Certificado</th><th>Evento</th><th>Fecha</th><th>Estado</th><th>Información</th></tr></thead><tbody>
          <?php foreach ($solicitudes as $solicitud): ?>
            <tr><td><code><?= htmlspecialchars($solicitud['codigo_unico']) ?></code></td><td><?= htmlspecialchars($solicitud['evento_titulo']) ?></td><td><?= date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])) ?></td><td><span class="badge <?= $solicitud['estado'] === 'PENDIENTE' ? 'bg-warning text-dark' : ($solicitud['estado'] === 'RECHAZADA' ? 'bg-danger' : 'bg-success') ?>"><?= htmlspecialchars($solicitud['estado']) ?></span></td><td class="small text-muted"><?php if ($solicitud['estado'] === 'APROBADA'): ?>Puede recoger la reimpresión en la Dirección de Extensión.<?php elseif ($solicitud['estado'] === 'ENTREGADA'): ?>Reimpresión entregada.<?php elseif ($solicitud['estado'] === 'RECHAZADA'): ?><?= htmlspecialchars($solicitud['motivo_rechazo'] ?: 'La solicitud fue rechazada.') ?><?php else: ?>Comprobante en revisión.<?php endif; ?></td></tr>
          <?php endforeach; ?>
        </tbody></table>
      </div>
    </section>
  <?php endif; ?>
</main>

<div class="modal fade" id="modalInstruccionesReimpresion" tabindex="-1" aria-labelledby="tituloInstruccionesReimpresion" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header bg-uab-azul text-white">
      <h2 class="modal-title h5" id="tituloInstruccionesReimpresion"><i class="bi bi-info-circle me-2"></i>Solicitud de reimpresión</h2>
      <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button>
    </div>
    <div class="modal-body p-4">
      <p class="mb-3">Siga estos pasos para solicitar la reimpresión de su certificado:</p>
      <ol class="mb-0 ps-3">
        <li class="mb-3">Solicite su ficha en la caja del Edificio Central de la Universidad Autónoma del Beni, ubicado en Av. 6 de Agosto 55.</li>
        <li class="mb-3">Adjunte su boleta de pago en la solicitud de reimpresión e indique las razones de la reimpresión.</li>
        <li>Espere a que se apruebe su solicitud y luego recoja su certificado en la Dirección de Investigación y Extensión de Postgrado.</li>
      </ol>
    </div>
    <div class="modal-footer"><button class="btn btn-uab-azul" type="button" data-bs-dismiss="modal">Entendido</button></div>
  </div></div>
</div>

<div class="modal fade" id="modalReimpresion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <form method="post" enctype="multipart/form-data" action="index.php?action=solicitar_reimpresion">
      <div class="modal-header"><h2 class="modal-title h5">Solicitar reimpresión</h2><button class="btn-close" type="button" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>"><input id="reimpresion-certificado" type="hidden" name="id_certificado"><p class="small text-muted">Certificado de <strong id="reimpresion-evento"></strong></p><label class="form-label" for="reimpresion-motivo">Motivo de la solicitud</label><textarea id="reimpresion-motivo" class="form-control" name="motivo" maxlength="1000" rows="3" required></textarea><label class="form-label mt-3" for="reimpresion-comprobante">Comprobante de pago</label><input id="reimpresion-comprobante" class="form-control" type="file" name="comprobante" accept="application/pdf,image/jpeg,image/png" required><div class="form-text">PDF, JPG o PNG. Máximo 5 MB.</div></div>
      <div class="modal-footer"><button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-uab-azul">Enviar solicitud</button></div>
    </form>
  </div></div>
</div>
<script>
document.getElementById('modalReimpresion').addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  document.getElementById('reimpresion-certificado').value = button.dataset.certificado;
  document.getElementById('reimpresion-evento').textContent = button.dataset.evento;
});
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
