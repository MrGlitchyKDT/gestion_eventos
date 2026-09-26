<?php 
$titulo_pagina = "Registro de Participante — UAB DIE";
$esAuth = true;
$esLogin = true;
require_once __DIR__ . '/../layouts/header.php'; 
?>

<main class="registro-page">
  <div class="container">

    <div class="registro-container">

      <div class="registro-header text-center">
        <h1>Crear Cuenta</h1>
      </div>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger py-2 small mb-4">
          <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <form action="index.php?action=do_registro" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">

        <div class="row g-3">

          <div class="col-md-6">
            <label for="ci" class="form-label">
              Cédula de Identidad (CI) *
            </label>
            <input
              type="text"
              name="ci"
              id="ci"
              maxlength="25"
              class="form-control"
              required
              placeholder="Ej: 100003"
            >
          </div>

          <div class="col-md-6">
            <label for="telefono" class="form-label">
              Teléfono / WhatsApp
            </label>
            <input
              type="tel"
              name="telefono"
              id="telefono"
              maxlength="30"
              class="form-control"
              placeholder="+591 ..."
            >
          </div>

          <div class="col-md-6">
            <label for="nombres" class="form-label">
              Nombres *
            </label>
            <input
              type="text"
              name="nombres"
              id="nombres"
              maxlength="100"
              class="form-control"
              required
              placeholder="Nombres"
            >
          </div>

          <div class="col-md-6">
            <label for="apellidos" class="form-label">
              Apellidos *
            </label>
            <input
              type="text"
              name="apellidos"
              id="apellidos"
              maxlength="100"
              class="form-control"
              required
              placeholder="Apellidos"
            >
          </div>

          <div class="col-12">
            <label for="correo" class="form-label">
              Correo Institucional / Personal *
            </label>
            <input
              type="email"
              name="correo"
              id="correo"
              maxlength="150"
              class="form-control"
              required
              placeholder="correo@ejemplo.edu"
            >
          </div>

          <div class="col-md-6">
            <label for="password" class="form-label">
              Contraseña *
            </label>
            <input
              type="password"
              name="password"
              id="password"
              class="form-control"
              required
              minlength="6"
              placeholder="Mínimo 6 caracteres"
            >
          </div>

          <div class="col-md-6">
            <label for="password_confirm" class="form-label">
              Confirmar Contraseña *
            </label>
            <input
              type="password"
              name="password_confirm"
              id="password_confirm"
              class="form-control"
              required
              minlength="6"
              placeholder="Repita contraseña"
            >
          </div>

        </div>

        <button
          type="submit"
          class="btn btn-uab-azul w-100 py-2 mt-4 mb-3"
        >
          <i class="bi bi-person-check-fill me-1"></i>
          Confirmar Registro
        </button>

        <div class="text-center small registro-login">
          ¿Ya posee cuenta registrada?
          <a href="index.php?action=login">
            Iniciar sesión
          </a>
        </div>

      </form>

    </div>

  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
