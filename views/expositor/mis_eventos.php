<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);

$titulo_pagina = 'Mis Eventos — UAB DIE';
$seccion_activa = 'mis_eventos';
$eventos = $eventos ?? [];

$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
$formatearFecha = static function (?string $fecha): string {
    $marcaTiempo = $fecha ? strtotime($fecha) : false;
    return $marcaTiempo !== false ? date('d/m/Y', $marcaTiempo) : 'Por definir';
};
$estados = [
    'BORRADOR' => ['Borrador', 'bg-secondary'],
    'PUBLICADO' => ['Publicado', 'bg-success'],
    'EN_CURSO' => ['En curso', 'bg-primary'],
    'FINALIZADO' => ['Finalizado', 'bg-dark'],
    'CANCELADO' => ['Cancelado', 'bg-danger'],
];

require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <section aria-labelledby="titulo-mis-eventos">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
          <h1 id="titulo-mis-eventos" class="h3 fw-bold text-uab-azul mb-1">Mis eventos</h1>
          <p class="small text-muted mb-0">Eventos en los que tiene asignada una función como expositor.</p>
        </div>
        <a class="btn btn-uab-azul btn-sm" href="index.php?action=expositor_asistencia"><i class="bi bi-clipboard-check me-1"></i> Tomar asistencia</a>
      </div>

      <?php if (empty($eventos)): ?>
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center py-5 px-4">
            <i class="bi bi-calendar2-plus display-5 text-muted" aria-hidden="true"></i>
            <h2 class="h5 mt-3">Todavía no tienes eventos asignados</h2>
            <p class="text-muted mb-0">
              Cuando la administración te asigne a un evento, aparecerá aquí con sus fechas y tu función.
            </p>
          </div>
        </div>
      <?php else: ?>
        <div class="row g-3">
          <?php foreach ($eventos as $eventoAsignado): ?>
            <?php
            [$etiquetaEstado, $claseEstado] = $estados[$eventoAsignado['estado']] ?? ['Sin estado', 'bg-secondary'];
            $modalidad = match ($eventoAsignado['modalidad']) {
                'PRESENCIAL' => 'Presencial',
                'VIRTUAL' => 'Virtual',
                'HIBRIDA' => 'Híbrida',
                default => 'Por definir',
            };
            ?>
            <div class="col-12 col-xl-6">
              <article class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column p-4">
                  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <span class="small text-muted"><?= $escapar($eventoAsignado['tipo_evento_nombre']) ?></span>
                    <span class="badge <?= $claseEstado ?>"><?= $etiquetaEstado ?></span>
                  </div>
                  <h2 class="h5 fw-bold text-uab-azul"><?= $escapar($eventoAsignado['titulo']) ?></h2>
                  <p class="small text-muted mb-3">Código: <?= $escapar($eventoAsignado['codigo']) ?></p>
                  <dl class="row small mb-3">
                    <dt class="col-sm-4">Tu función</dt>
                    <dd class="col-sm-8"><?= $escapar($eventoAsignado['rol_expositor'] ?: 'Expositor') ?></dd>
                    <dt class="col-sm-4">Fechas</dt>
                    <dd class="col-sm-8">
                      <?= $escapar($formatearFecha($eventoAsignado['fecha_inicio'])) ?>
                      — <?= $escapar($formatearFecha($eventoAsignado['fecha_fin'])) ?>
                    </dd>
                    <dt class="col-sm-4">Modalidad</dt>
                    <dd class="col-sm-8"><?= $modalidad ?></dd>
                    <?php if (!empty($eventoAsignado['lugar'])): ?>
                      <dt class="col-sm-4">Lugar</dt>
                      <dd class="col-sm-8"><?= $escapar($eventoAsignado['lugar']) ?></dd>
                    <?php endif; ?>
                  </dl>
                  <div class="mt-auto">
                    <a class="btn btn-sm btn-uab-azul" href="index.php?action=expositor_asistencia&amp;id_evento=<?= (int)$eventoAsignado['id_evento'] ?>">
                      <i class="bi bi-clipboard-check me-1" aria-hidden="true"></i> Tomar asistencia
                    </a>
                    <a class="btn btn-sm btn-outline-primary" href="index.php?action=detalle_evento&amp;id=<?= (int)$eventoAsignado['id_evento'] ?>">
                      <i class="bi bi-eye me-1" aria-hidden="true"></i> Ver detalle del evento
                    </a>
                  </div>
                </div>
              </article>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
  </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
