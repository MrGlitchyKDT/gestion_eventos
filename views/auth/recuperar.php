<?php
$titulo_pagina = 'Recuperar contraseña — CERT-DIE';
$esAuth = true;
$esLogin = true;
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="auth-main py-5">
  <div class="container">
    <section class="card shadow-sm border-0 mx-auto" style="max-width: 620px;">
      <div class="card-body p-4 p-md-5">
        <span class="auth-form-eyebrow">Recuperación de acceso</span>
        <h1 class="h3 fw-bold text-uab-azul mt-2">¿Olvidó su contraseña?</h1>
        <p class="text-muted mb-4">Por seguridad, la recuperación se realiza con verificación de identidad por parte de administración.</p>

        <?php if (!empty($_SESSION['success'])): ?>
          <div class="alert alert-success" role="alert"><?= htmlspecialchars($_SESSION['success']) ?></div>
          <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
          <div class="alert alert-danger" role="alert"><?= htmlspecialchars($_SESSION['error']) ?></div>
          <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="post" action="index.php?action=do_recuperar">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold" for="identificador">Correo institucional o CI</label>
            <input class="form-control" id="identificador" name="identificador" maxlength="150" autocomplete="username" required>
            <div class="form-text">La solicitud será revisada por administración antes de generar un enlace temporal.</div>
          </div>
          <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-uab-azul"><i class="bi bi-send me-1"></i>Enviar solicitud</button>
          <a href="index.php?action=login" class="btn btn-outline-secondary">Volver al inicio de sesión</a>
          </div>
        </form>
      </div>
    </section>
  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
