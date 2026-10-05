<?php
$titulo_pagina = 'Nueva contraseña — CERT-DIE';
$esAuth = true;
$esLogin = true;
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="auth-main py-5">
  <div class="container">
    <section class="card shadow-sm border-0 mx-auto" style="max-width: 560px;">
      <div class="card-body p-4 p-md-5">
        <span class="auth-form-eyebrow">Enlace verificado</span>
        <h1 class="h3 fw-bold text-uab-azul mt-2">Crear nueva contraseña</h1>
        <p class="text-muted mb-4">Elija una contraseña de entre 8 y 72 caracteres. Este enlace dejará de funcionar al completar el cambio.</p>

        <form action="index.php?action=do_reestablecer" method="POST">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
          <input type="hidden" name="token" value="<?= htmlspecialchars($tokenRecuperacion) ?>">

          <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Nueva contraseña</label>
            <input type="password" id="password" name="password" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
          </div>

          <div class="mb-4">
            <label for="password_confirm" class="form-label fw-semibold">Confirmar nueva contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" class="form-control" minlength="8" maxlength="72" autocomplete="new-password" required>
          </div>

          <button type="submit" class="btn btn-uab-azul w-100">
            <i class="bi bi-shield-lock me-1"></i> Guardar nueva contraseña
          </button>
        </form>
      </div>
    </section>
  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
