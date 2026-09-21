<?php
$titulo_pagina = 'Actividad administrativa — Admin UAB';
$seccion_activa = 'reportes';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h1 class="h3 fw-bold text-uab-azul mb-0">Actividad administrativa</h1>
      <p class="text-muted small mb-0">Historial completo de operaciones realizadas por administradores.</p>
    </div>
    <a href="index.php?action=admin_reportes" class="btn btn-outline-secondary btn-sm no-print">
      <i class="bi bi-arrow-left me-1"></i> Volver a reportes
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h2 class="h6 fw-bold mb-0 text-uab-azul"><i class="bi bi-shield-check me-2"></i>Bitácora de auditoría</h2>
      <span class="small text-muted"><?= $totalAuditorias ?> registro(s)</span>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Fecha y hora</th><th>Administrador</th><th>Módulo</th><th>Acción realizada</th><th>Detalles</th></tr></thead>
        <tbody>
          <?php if (empty($auditorias)): ?>
            <tr><td colspan="5" class="text-center py-4 text-muted">No hay registros de auditoría.</td></tr>
          <?php else: ?>
            <?php foreach ($auditorias as $aud): ?>
              <tr>
                <td class="small text-muted text-nowrap"><?= date('d/m/Y H:i', strtotime($aud['fecha_registro'])) ?></td>
                <td><strong class="small"><?= htmlspecialchars($aud['nombres'] . ' ' . $aud['apellidos']) ?></strong></td>
                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($aud['modulo']) ?></span></td>
                <td><code class="small"><?= htmlspecialchars($aud['accion']) ?></code></td>
                <td class="small text-secondary"><?= htmlspecialchars($aud['detalles'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php if ($totalPaginas > 1): ?>
      <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center no-print">
        <?php if ($pagina > 1): ?>
          <a class="btn btn-outline-secondary btn-sm" href="index.php?action=admin_auditoria&pagina=<?= $pagina - 1 ?>"><i class="bi bi-chevron-left"></i> Anterior</a>
        <?php else: ?><span></span><?php endif; ?>
        <span class="small text-muted">Página <?= $pagina ?> de <?= $totalPaginas ?></span>
        <?php if ($pagina < $totalPaginas): ?>
          <a class="btn btn-outline-secondary btn-sm" href="index.php?action=admin_auditoria&pagina=<?= $pagina + 1 ?>">Siguiente <i class="bi bi-chevron-right"></i></a>
        <?php else: ?><span></span><?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
