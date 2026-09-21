<?php
$titulo_pagina = 'Configuración de Eventos — Admin UAB';
$seccion_activa = 'configuracion_eventos';
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php';
$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <section class="mx-auto" style="max-width: 1200px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
      <div>
        <h1 class="h3 fw-bold text-uab-azul mb-1">Configuración de eventos</h1>
        <p class="text-muted small mb-0">Administre los tipos y categorías disponibles al registrar eventos.</p>
      </div>
      <a href="index.php?action=admin_eventos" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Gestión de eventos</a>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="alert alert-success py-2 small mb-3"><?= $escapar($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="alert alert-danger py-2 small mb-3"><?= $escapar($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row g-4">
      <?php foreach (['tipo' => ['titulo' => 'Tipos de evento', 'icono' => 'bi-calendar-event', 'datos' => $tipos], 'categoria' => ['titulo' => 'Categorías de evento', 'icono' => 'bi-tags', 'datos' => $categorias]] as $clave => $catalogo): ?>
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <h2 class="h5 fw-bold text-uab-azul mb-3"><i class="bi <?= $catalogo['icono'] ?> me-2"></i><?= $catalogo['titulo'] ?></h2>
              <form action="index.php?action=admin_catalogo_evento_guardar" method="POST" class="row g-2 mb-4">
                <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
                <input type="hidden" name="catalogo" value="<?= $clave ?>">
                <div class="col-sm-8">
                  <label class="visually-hidden" for="nombre_<?= $clave ?>">Nombre</label>
                  <input type="text" class="form-control" id="nombre_<?= $clave ?>" name="nombre" maxlength="100" required placeholder="Nuevo <?= $clave === 'tipo' ? 'tipo de evento' : 'categoría' ?>">
                </div>
                <div class="col-sm-4 d-grid">
                  <button class="btn btn-uab-azul" type="submit"><i class="bi bi-plus-lg me-1"></i> Agregar</button>
                </div>
              </form>

              <div class="list-group list-group-flush border rounded overflow-hidden">
                <?php if (empty($catalogo['datos'])): ?>
                  <div class="list-group-item text-muted small">No hay registros configurados.</div>
                <?php else: ?>
                  <?php foreach ($catalogo['datos'] as $item): ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between gap-2">
                      <div class="text-truncate">
                        <strong><?= $escapar($item['nombre']) ?></strong>
                        <span class="badge <?= (int)$item['activo'] === 1 ? 'bg-success' : 'bg-secondary' ?> ms-1"><?= (int)$item['activo'] === 1 ? 'Activo' : 'Inactivo' ?></span>
                        <?php if ((int)$item['eventos_asociados'] > 0): ?>
                          <div class="small text-muted"><?= (int)$item['eventos_asociados'] ?> evento(s) asociado(s)</div>
                        <?php endif; ?>
                      </div>
                      <form action="index.php?action=admin_catalogo_evento_eliminar" method="POST" class="m-0" onsubmit="return confirm('¿Desea eliminar esta configuración? Si tiene eventos asociados, se desactivará para preservar su historial.');">
                        <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
                        <input type="hidden" name="catalogo" value="<?= $clave ?>">
                        <input type="hidden" name="id" value="<?= (int)$item[$clave === 'tipo' ? 'id_tipo_evento' : 'id_categoria'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar o desactivar"><i class="bi bi-trash"></i></button>
                      </form>
                    </div>
                  <?php endforeach; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
