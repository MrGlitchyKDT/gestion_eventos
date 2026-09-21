<?php 
// views/admin/dashboard.php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['ADMINISTRADOR']);
$titulo_pagina = "Panel Administrativo — UAB Eventos";
$seccion_activa = "dashboard";
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php'; 

?>

<main class="container-fluid px-3 px-md-4 py-4">
  <div class="row g-4">
    <!-- Resumen administrativo -->
    <section class="col-12">
      <h1 class="h3 fw-bold text-uab-azul mb-4">Resumen General</h1>

      <!-- Tarjetas de Métricas Rápidas (KPIs) -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
          <a class="text-decoration-none" href="index.php?action=admin_usuarios">
            <div class="card stat-card border-0 shadow-sm h-100">
              <div class="card-body">
                <i class="bi bi-people text-uab-azul fs-4"></i>
                <div class="stat-num"><?= $totalUsuarios ?? 0 ?></div>
                <div class="small text-muted">Usuarios activos</div>
              </div>
            </div>
          </a>
        </div>

        <div class="col-12 col-md-4">
          <a class="text-decoration-none" href="index.php?action=admin_reportes">
            <div class="card stat-card stat-green border-0 shadow-sm h-100">
              <div class="card-body">
                <i class="bi bi-journal-check text-uab-verde fs-4"></i>
                <div class="stat-num"><?= $totalInscripciones ?? 0 ?></div>
                <div class="small text-muted">Inscripciones activas</div>
              </div>
            </div>
          </a>
        </div>

        <div class="col-12 col-md-4">
          <a class="text-decoration-none" href="index.php?action=admin_reportes">
            <div class="card stat-card stat-red border-0 shadow-sm h-100">
              <div class="card-body">
                <i class="bi bi-award text-danger fs-4"></i>
                <div class="stat-num"><?= $totalCertificados ?? 0 ?></div>
                <div class="small text-muted">Certificados emitidos</div>
              </div>
            </div>
          </a>
        </div>
      </div>

      <!-- Alerta de solicitudes pendientes de reimpresión (RF-60) -->
      <?php if (($solicitudesPend ?? 0) > 0): ?>
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill fs-4 text-warning"></i>
            <div>
              <strong><?= $solicitudesPend ?> solicitud(es) de reimpresión pendiente(s)</strong> de revisión.
            </div>
          </div>
          <a href="index.php?action=admin_reportes" class="btn btn-sm btn-dark">Revisar solicitudes</a>
        </div>
      <?php endif; ?>

      <!-- Bitácora de Auditoría Administrativa Reciente (RF-70) -->
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
          <h2 class="h6 fw-bold mb-0 text-uab-azul">
            <i class="bi bi-shield-check me-2"></i>Actividad Administrativa Reciente (Auditoría)
          </h2>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Fecha y Hora</th>
                <th>Administrador</th>
                <th>Módulo</th>
                <th>Acción Realizada</th>
                <th>Detalles</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($ultimasAuditorias)): ?>
                <tr>
                  <td colspan="5" class="text-center py-4 text-muted">Sin registros recientes de auditoría.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($ultimasAuditorias as $aud): ?>
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
      </div>
    </section>
  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
