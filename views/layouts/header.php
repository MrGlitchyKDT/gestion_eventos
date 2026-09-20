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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>
    <?= htmlspecialchars($titulo_pagina ?? 'UAB Eventos — Sistema de Gestión') ?>
  </title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap"
    rel="stylesheet"
  >

  <!-- Bootstrap -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    rel="stylesheet"
  >

  <!-- CSS del proyecto -->
  <link
    href="<?= htmlspecialchars($baseUrl) ?>/assets/css/styles.css"
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

        <span class="uab-logo">
          UAB
        </span>

        <div>

          <div class="auth-header-title">
            UAB Eventos
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
