<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['ADMINISTRADOR']);
$titulo_pagina = 'Inicio administrativo — UAB Eventos';
$seccion_activa = 'dashboard';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <section class="card border-0 shadow-sm">
    <div class="card-body p-4 p-md-5">
      <span class="badge bg-uab-azul mb-3">Administración</span>
      <h1 class="h3 fw-bold text-uab-azul">Inicio administrativo</h1>
      <p class="text-muted mb-4">Seleccione un módulo para administrar eventos, usuarios, reportes o la configuración del catálogo.</p>
      <div class="row g-3">
        <div class="col-12 col-md-6 col-xl-3">
          <a href="index.php?action=admin_eventos" class="btn btn-outline-primary w-100 py-3">
            <i class="bi bi-calendar-event me-2"></i>Gestión de eventos
          </a>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <a href="index.php?action=admin_usuarios" class="btn btn-outline-primary w-100 py-3">
            <i class="bi bi-people me-2"></i>Gestión de usuarios
          </a>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <a href="index.php?action=admin_reportes" class="btn btn-outline-primary w-100 py-3">
            <i class="bi bi-bar-chart-line me-2"></i>Reportes
          </a>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
          <a href="index.php?action=admin_configuracion_eventos" class="btn btn-outline-primary w-100 py-3">
            <i class="bi bi-sliders me-2"></i>Configuración
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
