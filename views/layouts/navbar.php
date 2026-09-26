<?php
// views/layouts/navbar.php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::initSession();
$usuarioNav = AuthHelper::obtenerUsuario();
$rolNav = AuthHelper::obtenerRol();
$inicioNav = $usuarioNav ? (AuthHelper::obtenerRutaInicio() ?? 'catalogo') : 'catalogo';
$inicialNav = $usuarioNav
    ? mb_strtoupper(mb_substr(trim($usuarioNav['nombres'] ?? 'U'), 0, 1))
    : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar shadow-sm sticky-top" style="background-color: #1a428a !important;">
  <div class="container-xl">
    <!-- Logotipo institucional -->
    <a class="navbar-brand app-navbar-brand d-flex align-items-center gap-3 m-0 text-white" href="index.php?action=<?= htmlspecialchars($inicioNav) ?>" aria-label="Ir al inicio">
      <img src="assets/img/logo.png" class="app-brand-logo" alt="UAB DIE">
      <div class="lh-sm">
        <span class="app-navbar-title d-block">UAB DIE</span>
        <small class="app-navbar-subtitle d-block">Universidad Autónoma del Beni</small>
      </div>
    </a>

    <!-- Botón hamburguesa para móvil y tablet -->
    <button class="navbar-toggler border-0 shadow-none text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Abrir menú de navegación">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navegación principal y perfil -->
    <div class="offcanvas-lg offcanvas-end app-navbar-menu" tabindex="-1" id="navbarMain" aria-labelledby="navbarMenuTitle">
      <div class="offcanvas-header d-lg-none border-bottom border-light border-opacity-25">
        <h2 class="offcanvas-title text-white fs-6" id="navbarMenuTitle">Navegación</h2>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#navbarMain" aria-label="Cerrar menú"></button>
      </div>
      <div class="offcanvas-body d-lg-flex align-items-lg-center p-0">
      <ul class="navbar-nav ms-auto align-items-stretch align-items-lg-center gap-1 gap-lg-2 mt-0">
        <?php if ($usuarioNav): ?>
        <?php if ($rolNav === 'ADMINISTRADOR'): ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=admin_dashboard">
            <i class="bi bi-house-door me-1 text-info"></i> Inicio
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=admin_eventos">
            <i class="bi bi-calendar-event me-1 text-info"></i> Gestión de Eventos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=admin_usuarios">
            <i class="bi bi-people me-1 text-info"></i> Gestión de Usuarios
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=admin_reportes">
            <i class="bi bi-bar-chart me-1 text-info"></i> Reportes
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=admin_configuracion_eventos">
            <i class="bi bi-gear me-1 text-info"></i> Configuración
          </a>
        </li>
        <?php else: ?>
          <?php if ($rolNav !== 'EXPOSITOR'): ?>
            <li class="nav-item">
              <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=<?= htmlspecialchars($inicioNav) ?>">
                <i class="bi bi-house-door me-1 text-info"></i> Inicio
              </a>
            </li>
          <?php endif; ?>
        <?php if ($rolNav === 'EXPOSITOR'): ?>
          <li class="nav-item">
            <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=expositor_eventos">
              <i class="bi bi-calendar-event me-1 text-info"></i> Mis eventos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=expositor_asistencia">
              <i class="bi bi-clipboard-check me-1 text-info"></i> Asistencia
            </a>
          </li>
        <?php elseif ($rolNav === 'PARTICIPANTE'): ?>
          <li class="nav-item dropdown">
            <button class="btn nav-link text-white fw-semibold px-2 py-1 rounded dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-mortarboard me-1 text-info"></i> Seminarios
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
              <li><a class="dropdown-item" href="index.php?action=catalogo"><i class="bi bi-calendar-event me-2"></i> Todos los seminarios</a></li>
              <li><a class="dropdown-item" href="index.php?action=mis_inscripciones"><i class="bi bi-journal-bookmark me-2"></i> Mis seminarios</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <button class="btn nav-link text-white fw-semibold px-2 py-1 rounded dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-award me-1 text-info"></i> Certificados
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
              <li><a class="dropdown-item" href="index.php?action=mis_certificados"><i class="bi bi-award me-2"></i> Mis certificados</a></li>
              <li><a class="dropdown-item" href="index.php?action=verificar_certificado"><i class="bi bi-patch-check me-2"></i> Verificar certificado</a></li>
            </ul>
          </li>
        <?php endif; ?>
        <?php endif; ?>
        <li class="nav-item dropdown">
          <button class="btn app-profile-toggle dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="app-profile-avatar" aria-hidden="true"><?= htmlspecialchars($inicialNav) ?></span>
            <span class="app-profile-name"><?= htmlspecialchars(trim(($usuarioNav['nombres'] ?? '') . ' ' . ($usuarioNav['apellidos'] ?? ''))) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end app-profile-menu shadow-sm">
            <li class="px-3 py-2 border-bottom">
              <span class="d-block small text-muted">Sesión activa</span>
              <strong><?= htmlspecialchars($rolNav ?? '') ?></strong>
            </li>
            <li>
              <a class="dropdown-item" href="index.php?action=perfil">
                <i class="bi bi-person me-2"></i> Mi perfil
              </a>
            </li>
            <li>
              <form action="index.php?action=logout" method="POST" class="m-0">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
                <button type="submit" class="dropdown-item text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                </button>
              </form>
            </li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=login">
            <i class="bi bi-box-arrow-in-right me-1 text-info"></i> Iniciar sesión
          </a>
        </li>
        <?php endif; ?>
        <?php if (!$usuarioNav): ?>
        <li class="nav-item">
          <a class="nav-link text-white fw-semibold px-2 py-1 rounded" href="index.php?action=verificar_certificado">
            <i class="bi bi-patch-check me-1 text-info"></i> Verificar Certificado
          </a>
        </li>
        <?php endif; ?>
      </ul>
      </div>
    </div>
  </div>
</nav>
