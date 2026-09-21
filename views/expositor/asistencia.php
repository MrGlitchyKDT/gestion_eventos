<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);

$titulo_pagina = 'Control de asistencia — UAB Eventos';
$seccion_activa = 'asistencia';
$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
$estadoActual = static fn($estado): string => $estado === 'SIN_REGISTRO' ? '' : $estado;
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4" id="planillaAsistencia">
  <section class="mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
      <h1 class="h2 fw-bold text-uab-azul mb-2">Control de asistencia</h1>
      <p class="text-muted mb-0">Registre la asistencia por sesión. Presente y Justificado cuentan para el porcentaje de certificación.</p>
    </div>

    <?php if (empty($eventos)): ?>
      <div class="alert alert-info">Aún no tiene eventos asignados para registrar asistencia.</div>
    <?php else: ?>
      <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body p-3">
          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label for="filtro_evento" class="form-label small fw-semibold">Evento</label>
              <select id="filtro_evento" class="form-select" onchange="cambiarEvento(this.value)">
                <?php foreach ($eventos as $eventoOpcion): ?>
                  <option value="<?= (int)$eventoOpcion['id_evento'] ?>" <?= (int)$eventoOpcion['id_evento'] === (int)$idEvento ? 'selected' : '' ?>>
                    <?= $escapar($eventoOpcion['codigo'] . ' — ' . $eventoOpcion['titulo']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="filtro_sesion" class="form-label small fw-semibold">Sesión</label>
              <select id="filtro_sesion" class="form-select" onchange="cambiarSesion(this.value)" <?= empty($sesiones) ? 'disabled' : '' ?>>
                <?php if (empty($sesiones)): ?>
                  <option>El evento no tiene sesiones</option>
                <?php else: ?>
                  <?php foreach ($sesiones as $sesionOpcion): ?>
                    <option value="<?= (int)$sesionOpcion['id_sesion'] ?>" <?= (int)$sesionOpcion['id_sesion'] === (int)($sesion['id_sesion'] ?? 0) ? 'selected' : '' ?>>
                      <?= $escapar(date('d/m/Y', strtotime($sesionOpcion['fecha'])) . ' · ' . $sesionOpcion['titulo']) ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="col-md-2 d-grid gap-2">
              <a class="btn btn-outline-success" href="index.php?action=expositor_exportar_asistencia&id_evento=<?= (int)$idEvento ?>"><i class="bi bi-filetype-csv me-1"></i> Exportar</a>
              <button class="btn btn-outline-secondary" type="button" onclick="window.print()"><i class="bi bi-printer me-1"></i> Imprimir</button>
            </div>
          </div>
        </div>
      </div>

      <?php if (!$sesion): ?>
        <div class="alert alert-light border">Registre sesiones en el evento para habilitar la toma de asistencia.</div>
      <?php else: ?>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
          <div>
            <h2 class="h5 fw-bold mb-1"><?= $escapar($sesion['titulo']) ?></h2>
            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($sesion['fecha'])) ?> · <?= substr($sesion['hora_inicio'], 0, 5) ?>–<?= substr($sesion['hora_fin'], 0, 5) ?> · <?= $escapar($sesion['lugar_especifico'] ?: 'Lugar por definir') ?></span>
          </div>
          <span id="estado_guardado" class="small text-muted" aria-live="polite"></span>
        </div>

        <div class="table-responsive card border-0 shadow-sm">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>Participante</th><th>CI</th><th class="text-center">Estado de asistencia</th><th class="text-end">Porcentaje acumulado</th></tr>
            </thead>
            <tbody>
              <?php if (empty($participantes)): ?>
                <tr><td colspan="4" class="text-center py-4 text-muted">No hay participantes inscritos para este evento.</td></tr>
              <?php else: ?>
                <?php foreach ($participantes as $participante): ?>
                  <tr data-inscripcion="<?= (int)$participante['id_inscripcion'] ?>">
                    <td><strong><?= $escapar($participante['apellidos'] . ', ' . $participante['nombres']) ?></strong><div class="small text-muted"><?= $escapar($participante['codigo_inscripcion']) ?></div></td>
                    <td class="small"><?= $escapar($participante['ci']) ?></td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm asistencia-opciones" role="group" aria-label="Asistencia de <?= $escapar($participante['nombres']) ?>">
                        <?php foreach (['PRESENTE' => 'Presente', 'JUSTIFICADO' => 'Justificado', 'FALTA' => 'Falta'] as $valor => $etiqueta): ?>
                          <button type="button" class="btn <?= $estadoActual($participante['estado_asistencia']) === $valor ? 'btn-uab-azul active' : 'btn-outline-secondary' ?>" data-estado="<?= $valor ?>"><?= $etiqueta ?></button>
                        <?php endforeach; ?>
                      </div>
                    </td>
                    <td class="text-end"><span class="badge bg-light text-dark border porcentaje-asistencia"><?= number_format((float)$participante['porcentaje_asistencia'], 0) ?>%</span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>

<script>
const idEventoActual = <?= (int)($idEvento ?? 0) ?>;
const idSesionActual = <?= (int)($sesion['id_sesion'] ?? 0) ?>;
const csrfToken = <?= json_encode(AuthHelper::tokenCsrf(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
function cambiarEvento(idEvento) { window.location.href = `index.php?action=expositor_asistencia&id_evento=${encodeURIComponent(idEvento)}`; }
function cambiarSesion(idSesion) { window.location.href = `index.php?action=expositor_asistencia&id_evento=${encodeURIComponent(idEventoActual)}&id_sesion=${encodeURIComponent(idSesion)}`; }

document.querySelectorAll('.asistencia-opciones button').forEach((boton) => {
  boton.addEventListener('click', async () => {
    const fila = boton.closest('tr');
    const botones = fila.querySelectorAll('.asistencia-opciones button');
    botones.forEach((item) => item.disabled = true);
    document.getElementById('estado_guardado').textContent = 'Guardando asistencia…';
    try {
      const respuesta = await fetch('index.php?action=api_guardar_asistencia', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({ id_sesion: idSesionActual, id_inscripcion: Number(fila.dataset.inscripcion), estado: boton.dataset.estado, csrf_token: csrfToken })
      });
      const datos = await respuesta.json();
      if (!respuesta.ok || !datos.success) throw new Error(datos.mensaje || 'No se pudo guardar la asistencia.');
      botones.forEach((item) => {
        const activo = item === boton;
        item.className = `btn ${activo ? 'btn-uab-azul active' : 'btn-outline-secondary'}`;
      });
      fila.querySelector('.porcentaje-asistencia').textContent = `${Number(datos.porcentaje_asistencia).toFixed(0)}%`;
      document.getElementById('estado_guardado').textContent = 'Asistencia guardada.';
    } catch (error) {
      document.getElementById('estado_guardado').textContent = error.message;
    } finally {
      botones.forEach((item) => item.disabled = false);
    }
  });
});
</script>

<style>
@media print {
  .app-navbar, .no-print, footer { display: none !important; }
  #planillaAsistencia { padding: 0 !important; }
  .card { box-shadow: none !important; border: 1px solid #bbb !important; }
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
