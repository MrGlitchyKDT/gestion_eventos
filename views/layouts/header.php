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
    <?= htmlspecialchars($titulo_pagina ?? 'UAB DIE — Sistema de Gestión') ?>
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

        <img
          src="<?= htmlspecialchars($baseUrl) ?>/assets/img/logo.png"
          class="app-brand-logo"
          alt="UAB DIE"
        >

        <div>

          <div class="auth-header-title">
            UAB DIE
          </div>

          <div class="auth-header-subtitle">
            Universidad Autónoma del Beni
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

<?php endif; ?>
