<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';

AuthHelper::initSession();
$usuarioSesion = AuthHelper::obtenerUsuario();

$esAuth = $esAuth ?? false;

/*
 * Calcula la carpeta desde la que se está ejecutando index.php.
 * Ejemplo:
 * /gestion_eventos/public/index.php
 * -> /gestion_eventos/public
 */
$baseUrl = rtrim(
    str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])),
    '/'
);
$stylesVersion = @filemtime(__DIR__ . '/../../public/assets/css/styles.css') ?: time();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>
    <?= htmlspecialchars($titulo_pagina ?? 'DIE-CERT — Sistema de Gestión') ?>
  </title>

  <!-- Dependencias locales: permiten operar sin CDN. -->
  <link href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/fonts/google-fonts.css" rel="stylesheet">
  <link href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= htmlspecialchars($baseUrl) ?>/assets/vendor/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">

  <!-- CSS del proyecto -->
  <link
    href="<?= htmlspecialchars($baseUrl) ?>/assets/css/styles.css?v=<?= (int)$stylesVersion ?>"
    rel="stylesheet"
  >
</head>

<body class="<?= $esAuth ? 'auth-body' : '' ?>">

<?php if ($esAuth): ?>

<header class="auth-header <?= !empty($esLogin) ? 'auth-header-login' : '' ?>">

  <div class="container-xl">

    <div class="d-flex align-items-center justify-content-between">

      <a
        href="index.php"
        class="auth-header-brand text-decoration-none"
      >

        <span class="app-brand-identity" aria-hidden="true">
          <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png" class="app-brand-logo app-brand-logo-uab" alt="">
          <img src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo_DIE.png" class="app-brand-logo app-brand-logo-die" alt="">
        </span>

        <div class="app-brand-copy">

          <div class="auth-header-title" data-brand-title>
            UABJB
          </div>

          <div class="auth-header-subtitle" data-brand-subtitle>
            Universidad Autonoma del Beni &quot;Jose Ballivian&quot;
          </div>

        </div>

      </a>


      <?php if (empty($esLogin)): ?>

        <a
          href="index.php?action=verificar_certificado"
          class="auth-header-link d-none d-sm-flex"
        >
          <i class="bi bi-patch-check me-2"></i>
          Verificar certificado
        </a>

      <?php endif; ?>

    </div>

  </div>

</header>

<script>
(() => {
  const marcas = document.querySelectorAll('.auth-header-brand');
  const datos = {
    uab: { titulo: 'UABJB', subtitulo: 'Universidad Autonoma del Beni "Jose Ballivian"' },
    die: { titulo: 'DIE-CERT', subtitulo: 'Direccion de Investigación y Extensión de Pregrado' }
  };
  marcas.forEach((marca) => {
    let esDie = false;
    window.setInterval(() => {
      esDie = !esDie;
      const siguiente = esDie ? datos.die : datos.uab;
      const copia = marca.querySelector('.app-brand-copy');
      copia.classList.add('app-brand-copy-changing');
      window.setTimeout(() => {
        marca.classList.toggle('app-brand-die-activo', esDie);
        marca.querySelector('[data-brand-title]').textContent = siguiente.titulo;
        marca.querySelector('[data-brand-subtitle]').textContent = siguiente.subtitulo;
        copia.classList.remove('app-brand-copy-changing');
      }, 180);
    }, 6000);
  });
})();
</script>

<?php endif; ?>
