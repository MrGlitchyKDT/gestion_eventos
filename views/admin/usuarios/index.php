<?php 
$titulo_pagina = "Gestión de Usuarios — Admin UAB";
$seccion_activa = "usuarios";
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php'; 
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <div class="row g-4">
    <section class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
          <h1 class="h3 fw-bold text-uab-azul mb-0">Gestión de Usuarios</h1>
          <p class="text-muted small mb-0">Control de acceso institucional, roles y activación de cuentas</p>
        </div>
      </div>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success py-2 small mb-3"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <?php if (!empty($enlaceRecuperacion)): ?>
        <div class="alert alert-warning shadow-sm mb-3" role="alert">
          <div class="fw-bold mb-1"><i class="bi bi-key me-1"></i> Enlace de recuperación listo</div>
          <p class="mb-2 small">Entréguelo únicamente a <?= htmlspecialchars($enlaceRecuperacion['nombre']) ?> (<?= htmlspecialchars($enlaceRecuperacion['correo']) ?>) después de verificar su identidad. Vence el <?= htmlspecialchars(date('d/m/Y H:i', strtotime($enlaceRecuperacion['expiracion']))) ?>.</p>
          <div class="input-group input-group-sm">
            <input id="enlaceRecuperacion" type="text" class="form-control" value="<?= htmlspecialchars($enlaceRecuperacion['url']) ?>" readonly>
            <button type="button" class="btn btn-outline-dark" onclick="copiarEnlaceRecuperacion()"><i class="bi bi-copy me-1"></i> Copiar</button>
          </div>
        </div>
      <?php endif; ?>

      <!-- Filtros de usuarios -->
      <form action="index.php" method="GET" class="card border-0 shadow-sm p-3 mb-3 bg-white">
        <input type="hidden" name="action" value="admin_usuarios">
        <div class="row g-3 align-items-end">
          <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="buscar-usuario">Buscar por nombre</label><input class="form-control" id="buscar-usuario" type="search" name="buscar" value="<?= htmlspecialchars($filtrosUsuarios['buscar']) ?>" placeholder="Nombre o apellido"></div>
          <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="ci-usuario-filtro">CI</label><input class="form-control" id="ci-usuario-filtro" type="search" name="ci" value="<?= htmlspecialchars($filtrosUsuarios['ci']) ?>" placeholder="CI"></div>
          <div class="col-12 col-md-4 col-xl-2"><label class="form-label fw-semibold" for="rol-usuario-filtro">Rol</label>
            <select id="rol-usuario-filtro" name="rol" class="form-select">
              <option value="">Todos los roles</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_rol'] ?>" <?= $filtrosUsuarios['rol'] === (int)$r['id_rol'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 col-md-4 col-xl-2"><label class="form-label fw-semibold" for="estado-usuario-filtro">Estado</label><select id="estado-usuario-filtro" name="estado" class="form-select"><option value="">Todos los estados</option><option value="ACTIVO" <?= $filtrosUsuarios['estado'] === 'ACTIVO' ? 'selected' : '' ?>>Activo</option><option value="INACTIVO" <?= $filtrosUsuarios['estado'] === 'INACTIVO' ? 'selected' : '' ?>>Inactivo</option></select></div>
          <div class="col-12 col-md-4 col-xl-3 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button><a class="btn btn-outline-secondary" href="index.php?action=admin_usuarios" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
        </div>
      </form>

      <div class="table-responsive card border-0 shadow-sm">
        <table class="table table-uab table-hover align-middle mb-0">
          <thead>
            <tr>
              <th scope="col">Usuario</th>
              <th scope="col">Cédula (CI)</th>
              <th scope="col">Correo Electrónico</th>
              <th scope="col">Rol Asignado</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$usuarios): ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron usuarios con los filtros seleccionados.</td></tr>
            <?php endif; ?>
            <?php foreach ($usuarios as $u): 
              $esPropio = ((int)$u['id_usuario'] === (int)$usuarioSesion['id_usuario']);
            ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($u['apellidos'] . ', ' . $u['nombres']) ?></strong>
                  <?php if ($esPropio): ?>
                    <span class="badge bg-uab-azul ms-1">Usted</span>
                  <?php endif; ?>
                </td>
                <td><code><?= htmlspecialchars($u['ci']) ?></code></td>
                <td class="small"><?= htmlspecialchars($u['correo']) ?></td>
                <td>
                  <span class="badge <?= $u['rol_nombre'] === 'ADMINISTRADOR' ? 'bg-dark' : ($u['rol_nombre'] === 'EXPOSITOR' ? 'bg-uab-verde' : 'bg-secondary') ?>">
                    <?= htmlspecialchars($u['rol_nombre']) ?>
                  </span>
                </td>
                <td>
                  <span class="badge <?= $u['activo'] ? 'bg-success' : 'bg-danger' ?>">
                    <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="text-end text-nowrap">
                  <?php if (!$esPropio): ?>
                    <?php if ($u['activo']): ?>
                      <?php $nombreRecuperacion = json_encode($u['nombres'] . ' ' . $u['apellidos'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG); ?>
                      <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalRecuperacion" onclick='prepararRecuperacion(<?= (int)$u['id_usuario'] ?>, <?= $nombreRecuperacion ?>)' title="Generar enlace de recuperación">
                        <i class="bi bi-key"></i>
                      </button>
                    <?php endif; ?>

                    <!-- Cambiar Rol -->
                    <button class="btn btn-sm btn-outline-uab-azul" data-bs-toggle="modal" data-bs-target="#modalCambiarRol" onclick="prepararCambioRol(<?= $u['id_usuario'] ?>, '<?= htmlspecialchars(addslashes($u['nombres'] . ' ' . $u['apellidos'])) ?>', <?= $u['id_rol'] ?>)">
                      <i class="bi bi-shield-lock" title="Cambiar rol"></i>
                    </button>

                    <!-- Toggle Activar/Desactivar -->
                    <form action="index.php?action=admin_usuario_toggle_estado" method="POST" class="d-inline">
                      <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                      <button type="submit" class="btn btn-sm <?= $u['activo'] ? 'btn-outline-danger' : 'btn-outline-success' ?>" title="<?= $u['activo'] ? 'Desactivar cuenta' : 'Activar cuenta' ?>">
                        <i class="bi <?= $u['activo'] ? 'bi-person-slash' : 'bi-person-check' ?>"></i>
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small">—</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>

<!-- Modal: Cambiar Rol -->
<div class="modal fade" id="modalCambiarRol" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="index.php?action=admin_usuario_cambiar_rol" method="POST">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title"><i class="bi bi-shield-lock me-1"></i> Asignar Rol de Usuario</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="id_usuario" id="rol_id_usuario">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Usuario</label>
            <input type="text" id="rol_usuario_nombre" class="form-control bg-light" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nuevo Rol *</label>
            <select name="id_rol" id="rol_select" class="form-select" required>
              <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id_rol'] ?>"><?= htmlspecialchars($r['nombre']) ?> — <?= htmlspecialchars($r['descripcion']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm">Actualizar Privilegios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Recuperación asistida -->
<div class="modal fade" id="modalRecuperacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="index.php?action=admin_usuario_generar_recuperacion" method="POST">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title"><i class="bi bi-key me-1"></i> Generar enlace de recuperación</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
          <input type="hidden" name="id_usuario" id="recuperacion_id_usuario">
          <p class="mb-3">Genere un enlace temporal para <strong id="recuperacion_usuario_nombre"></strong>.</p>
          <div class="alert alert-warning small mb-0">
            Verifique la identidad del usuario antes de compartir el enlace. Será válido por una hora y sólo podrá usarse una vez.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-key me-1"></i> Generar enlace</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function prepararCambioRol(idUsuario, nombre, idRolActual) {
  document.getElementById('rol_id_usuario').value = idUsuario;
  document.getElementById('rol_usuario_nombre').value = nombre;
  document.getElementById('rol_select').value = idRolActual;
}

function prepararRecuperacion(idUsuario, nombre) {
  document.getElementById('recuperacion_id_usuario').value = idUsuario;
  document.getElementById('recuperacion_usuario_nombre').textContent = nombre;
}

function copiarEnlaceRecuperacion() {
  const campo = document.getElementById('enlaceRecuperacion');
  if (!campo) return;
  campo.select();
  navigator.clipboard?.writeText(campo.value).catch(() => document.execCommand('copy'));
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
