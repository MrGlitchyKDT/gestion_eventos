<?php
$titulo_pagina = 'Mi asistencia — UAB DIE';
$seccion_activa = 'mis_inscripciones';
$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <section class="mx-auto" style="max-width: 1050px;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h1 class="h2 fw-bold text-uab-azul mb-1">Confirmar asistencia</h1>
        <p class="text-muted mb-0">Podrás confirmar tu presencia una vez por cada sesión mientras el docente mantenga abierta la asistencia.</p>
      </div>
      <a href="index.php?action=mis_inscripciones" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Mis inscripciones</a>
    </div>

    <?php foreach (['success' => 'success', 'error' => 'danger'] as $clave => $tipo): ?>
      <?php if (!empty($_SESSION[$clave])): ?>
        <div class="alert alert-<?= $tipo ?> d-flex align-items-center gap-2" role="alert">
          <i class="bi bi-<?= $tipo === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
          <span><?= $escapar($_SESSION[$clave]) ?></span>
        </div>
        <?php unset($_SESSION[$clave]); ?>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php if (empty($sesionesAbiertas)): ?>
      <div class="card border-0 shadow-sm text-center p-5">
        <i class="bi bi-clock-history display-5 text-uab-azul mb-3"></i>
        <h2 class="h5 fw-bold">No hay asistencias abiertas</h2>
        <p class="text-muted mb-0">Cuando tu docente habilite una sesión de tus eventos inscritos, aparecerá aquí.</p>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($sesionesAbiertas as $sesion): ?>
          <div class="col-12">
            <article class="card border-0 shadow-sm">
              <div class="card-body p-3 p-md-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2"><?= $escapar($sesion['evento_codigo']) ?></span>
                  <h2 class="h5 fw-bold mb-1"><?= $escapar($sesion['evento_titulo']) ?></h2>
                  <h3 class="h6 text-uab-azul mb-2"><i class="bi bi-calendar-event me-1"></i><?= $escapar($sesion['sesion_titulo']) ?></h3>
                  <p class="small text-muted mb-0">
                    <?= date('d/m/Y', strtotime($sesion['fecha'])) ?> · <?= substr($sesion['hora_inicio'], 0, 5) ?>–<?= substr($sesion['hora_fin'], 0, 5) ?>
                    <span class="ms-2"><i class="bi bi-hourglass-split me-1"></i>Disponible hasta <?= date('H:i', strtotime($sesion['fecha_cierre_programada'])) ?></span>
                  </p>
                </div>
                <div class="text-lg-end">
                  <?php if ($sesion['id_asistencia']): ?>
                    <span class="badge bg-success px-3 py-2"><i class="bi bi-check2-circle me-1"></i> Asistencia confirmada</span>
                  <?php else: ?>
                    <form method="post" action="index.php?action=confirmar_asistencia">
                      <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
                      <input type="hidden" name="id_sesion" value="<?= (int)$sesion['id_sesion'] ?>">
                      <input type="hidden" name="id_evento" value="<?= (int)$sesion['id_evento'] ?>">
                      <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-square me-1"></i> Marcar presente</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
