<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);

$titulo_pagina = 'Mis Eventos — CERT-DIE';
$seccion_activa = 'mis_eventos';
$eventos = $eventos ?? [];
$materialesPorEvento = $materialesPorEvento ?? [];

$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
$formatearFecha = static function (?string $fecha): string {
    $marcaTiempo = $fecha ? strtotime($fecha) : false;
    return $marcaTiempo !== false ? date('d/m/Y', $marcaTiempo) : 'Por definir';
};
$formatearTamano = static function (int $bytes): string {
    if ($bytes < 1048576) return max(1, (int)ceil($bytes / 1024)) . ' KB';
    return number_format($bytes / 1048576, 1) . ' MB';
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

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success" role="alert"><?= $escapar($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger" role="alert"><?= $escapar($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

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
            $materialesEvento = $materialesPorEvento[(int)$eventoAsignado['id_evento']] ?? [];
            $modalidad = match ($eventoAsignado['modalidad']) {
                'PRESENCIAL' => 'Presencial',
                'VIRTUAL' => 'Virtual',
                'HIBRIDA' => 'Híbrida',
                default => 'Por definir',
            };
            ?>
            <div class="col-12 col-xl-6">
              <article id="evento-<?= (int)$eventoAsignado['id_evento'] ?>" class="card border-0 shadow-sm h-100">
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
                  <div class="border-top pt-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                      <h3 class="h6 fw-bold mb-0"><i class="bi bi-folder2-open text-uab-azul me-1"></i> Materiales para participantes</h3>
                      <span class="badge bg-light text-dark border"><?= count($materialesEvento) ?></span>
                    </div>
                    <?php if (empty($materialesEvento)): ?>
                      <p class="small text-muted mb-0">Aún no se publicaron materiales.</p>
                    <?php else: ?>
                      <ul class="list-unstyled small mb-0">
                        <?php foreach ($materialesEvento as $material): ?>
                          <li class="d-flex align-items-center gap-2 py-1">
                            <i class="bi bi-file-earmark-arrow-down text-uab-azul"></i>
                            <a class="text-truncate" href="index.php?action=descargar_material&amp;id=<?= (int)$material['id_material'] ?>" title="Descargar <?= $escapar($material['titulo']) ?>"><?= $escapar($material['titulo']) ?></a>
                            <span class="text-muted ms-auto text-nowrap"><?= $escapar($material['tipo_archivo']) ?> · <?= $escapar($formatearTamano((int)$material['tamano_bytes'])) ?></span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php endif; ?>
                  </div>
                  <div class="mt-auto">
                    <button type="button" class="btn btn-sm btn-outline-uab-azul" data-bs-toggle="modal" data-bs-target="#modalSubirMaterial" data-evento-id="<?= (int)$eventoAsignado['id_evento'] ?>" data-evento-titulo="<?= $escapar($eventoAsignado['titulo']) ?>">
                      <i class="bi bi-upload me-1" aria-hidden="true"></i> Subir materiales
                    </button>
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

<div class="modal fade" id="modalSubirMaterial" tabindex="-1" aria-labelledby="modalSubirMaterialTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="index.php?action=expositor_subir_material" method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-uab-azul text-white">
          <h2 class="modal-title h5" id="modalSubirMaterialTitulo"><i class="bi bi-upload me-1"></i> Publicar materiales</h2>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
          <input type="hidden" name="id_evento" id="material_id_evento">
          <p class="mb-3">Los archivos se publicarán para los participantes inscritos en <strong id="material_evento_titulo"></strong>.</p>
          <label for="materiales_expositor" class="form-label fw-semibold">Archivos académicos</label>
          <input class="form-control" type="file" id="materiales_expositor" name="materiales[]" multiple required accept=".pdf,.zip,.rar,.pptx,.docx,.xlsx,.txt">
          <div class="form-text">Puede seleccionar varios archivos. Formatos: PDF, ZIP, RAR, PPTX, DOCX, XLSX o TXT. Máximo 20 MB por archivo.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-cloud-arrow-up me-1"></i> Publicar archivos</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('modalSubirMaterial')?.addEventListener('show.bs.modal', function (evento) {
  const boton = evento.relatedTarget;
  document.getElementById('material_id_evento').value = boton?.dataset.eventoId || '';
  document.getElementById('material_evento_titulo').textContent = boton?.dataset.eventoTitulo || 'este evento';
  document.getElementById('materiales_expositor').value = '';
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
