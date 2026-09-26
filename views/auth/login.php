<?php

$titulo_pagina = "Iniciar Sesión — UAB DIE";
$esAuth = true;
$esLogin = true;
require_once __DIR__ . '/../layouts/header.php';

?>

<main class="auth-main">

  <div class="container-xl">

    <div class="auth-layout">

      <!-- =====================================================
          LADO IZQUIERDO — BIENVENIDA
          ===================================================== -->

      <section class="auth-welcome">

        <div class="auth-welcome-content">
          <img
            src="<?= htmlspecialchars($baseUrl) ?>/assets/img/Logo_DIE_UABJB_blanco.png"
            alt="Dirección de Investigación y Extensión UAB-JB"
            class="auth-die-logo"
          >

          <h1 class="auth-welcome-title">
            Bienvenido
          </h1>

          <!-- <div class="auth-welcome-divider"></div> -->



          <h2 class="auth-welcome-subtitle">
            Plataforma de Eventos y Certificaciones
          </h2>


        </div>

      </section>

      <!-- =====================================================
           LADO DERECHO — LOGIN
           ===================================================== -->
      <section class="auth-login">

        <div class="auth-login-container">

          <div class="mb-4">

            <span class="auth-form-eyebrow">
              Acceso institucional
            </span>

            <h2 class="auth-login-title">
              Iniciar sesión
            </h2>

            <p class="auth-login-description">
              Ingrese con sus credenciales registradas.
            </p>

          </div>


          <!-- MENSAJE DE ERROR -->
          <?php if (!empty($_SESSION['error'])): ?>

            <div
              class="alert alert-danger py-2 small d-flex align-items-center mb-3"
              role="alert"
            >
              <i class="bi bi-exclamation-triangle-fill me-2"></i>

              <div>
                <?= htmlspecialchars($_SESSION['error']) ?>
              </div>
            </div>

            <?php unset($_SESSION['error']); ?>

          <?php endif; ?>


          <!-- MENSAJE DE ÉXITO -->
          <?php if (!empty($_SESSION['success'])): ?>

            <div
              class="alert alert-success py-2 small d-flex align-items-center mb-3"
              role="alert"
            >
              <i class="bi bi-check-circle-fill me-2"></i>

              <div>
                <?= htmlspecialchars($_SESSION['success']) ?>
              </div>
            </div>

            <?php unset($_SESSION['success']); ?>

          <?php endif; ?>


          <form action="index.php?action=do_login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">

            <!-- CORREO -->
            <div class="mb-3">

              <label
                for="correo"
                class="form-label small fw-semibold"
              >
                Correo electrónico
              </label>

              <div class="input-group auth-input">

                <span class="input-group-text">
                  <i class="bi bi-envelope"></i>
                </span>

                <input
                  type="email"
                  name="correo"
                  id="correo"
                  class="form-control"
                  placeholder="ejemplo@uab.edu.bo"
                  autocomplete="username"
                  maxlength="150"
                  value="<?= htmlspecialchars($_SESSION['login_correo'] ?? '') ?>"
                  required
                >

              </div>

            </div>


            <!-- CONTRASEÑA -->
            <div class="mb-3">

              <div class="d-flex justify-content-between align-items-center">

                <label
                  for="password"
                  class="form-label small fw-semibold"
                >
                  Contraseña
                </label>

                <a
                  href="index.php?action=recuperar"
                  class="auth-forgot-link"
                >
                  ¿Olvidó su contraseña?
                </a>

              </div>

              <div class="input-group auth-input">

                <span class="input-group-text">
                  <i class="bi bi-lock"></i>
                </span>

                <input
                  type="password"
                  name="password"
                  id="password"
                  class="form-control"
                  placeholder="••••••••"
                  autocomplete="current-password"
                  required
                >

                <button
                  type="button"
                  class="btn auth-password-toggle"
                  id="togglePassword"
                  aria-label="Mostrar contraseña"
                  aria-pressed="false"
                >
                  <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>

              </div>

            </div>


            <!-- BOTÓN -->
            <button
              type="submit"
              class="btn btn-uab-azul auth-submit w-100 mt-3"
            >
              Ingresar
              <i class="bi bi-arrow-right ms-2"></i>
            </button>

          </form>


          <!-- REGISTRO -->
          <div class="auth-register text-center">

            <span>
              ¿Aún no tiene una cuenta?
            </span>

            <a href="index.php?action=registro">
              Crear cuenta
            </a>

          </div>

        </div>

      </section>

    </div>

  </div>

</main>
<script>
document.addEventListener('DOMContentLoaded', function () {

  const passwordInput = document.getElementById('password');
  const toggleButton = document.getElementById('togglePassword');
  const toggleIcon = document.getElementById('togglePasswordIcon');

  if (!passwordInput || !toggleButton || !toggleIcon) {
    return;
  }

  toggleButton.addEventListener('click', function () {

    const passwordVisible = passwordInput.type === 'text';

    passwordInput.type = passwordVisible ? 'password' : 'text';

    toggleIcon.classList.toggle('bi-eye', passwordVisible);
    toggleIcon.classList.toggle('bi-eye-slash', !passwordVisible);

    toggleButton.setAttribute(
      'aria-label',
      passwordVisible ? 'Mostrar contraseña' : 'Ocultar contraseña'
    );

    toggleButton.setAttribute(
      'aria-pressed',
      passwordVisible ? 'false' : 'true'
    );

  });

});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
