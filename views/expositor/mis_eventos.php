<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);

$titulo_pagina = 'Mis Eventos — UAB Eventos';
$seccion_activa = 'mis_eventos';
$eventos = $eventos ?? [];
$usuario = AuthHelper::obtenerUsuario();
$eventosEnCurso = 0;
$eventosFinalizados = 0;

foreach ($eventos as $eventoAsignado) {
    if ($eventoAsignado['estado'] === 'EN_CURSO') {
        $eventosEnCurso++;
    } elseif ($eventoAsignado['estado'] === 'FINALIZADO') {
        $eventosFinalizados++;
    }
}

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

<main class="container-fluid px-3 px-md-4 py-3">
  <div class="row g-3">
    <aside class="col-12 col-lg-3 col-xl-2">
      <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    </aside>

    <section class="col-12 col-lg-9 col-xl-10" aria-labelledby="titulo-mis-eventos">
      <div class="hero-uab rounded-3 p-4 p-md-5 mb-4 shadow-sm">
        <span class="badge bg-white text-uab-azul mb-3">Panel de expositor</span>
        <h1 id="titulo-mis-eventos" class="h3 fw-bold mb-2">Mis eventos asignados</h1>
        <p class="mb-0">
          Bienvenido/a, <?= $escapar($usuario['nombres'] . ' ' . $usuario['apellidos']) ?>.
          Consulta las fechas y los detalles de los eventos en los que participas como expositor.
        </p>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
          <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
              <i class="bi bi-calendar-event text-uab-azul fs-4" aria-hidden="true"></i>
              <div class="stat-num"><?= count($eventos) ?></div>
              <div class="small text-muted">Eventos asignados</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4">
          <div class="card stat-card border-0 shadow-sm h-100">
            <div class="card-body">
              <i class="bi bi-play-circle text-uab-azul fs-4" aria-hidden="true"></i>
              <div class="stat-num"><?= $eventosEnCurso ?></div>
              <div class="small text-muted">En curso</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-sm-4">
          <div class="card stat-card stat-green border-0 shadow-sm h-100">
            <div class="card-body">
              <i class="bi bi-check-circle text-uab-verde fs-4" aria-hidden="true"></i>
              <div class="stat-num"><?= $eventosFinalizados ?></div>
              <div class="small text-muted">Finalizados</div>
            </div>
          </div>
        </div>
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
  </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
