<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
AuthHelper::requerirRol(['EXPOSITOR', 'ADMINISTRADOR']);

$titulo_pagina = 'Control de asistencia — UAB DIE';
$seccion_activa = 'asistencia';
$escapar = static fn($valor): string => htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
$estadoActual = static fn($estado): string => $estado === 'SIN_REGISTRO' ? '' : $estado;
$estadoSesion = $sesion['estado'] ?? null;
$ventanaVencida = $estadoSesion === 'EN_CURSO'
    && !empty($sesion['fecha_cierre_programada'])
    && strtotime($sesion['fecha_cierre_programada']) < time();
$puedeCorregir = $estadoSesion === 'CONCLUIDA';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<main class="container-fluid px-3 px-md-4 py-4" id="planillaAsistencia">
  <section class="mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
      <h1 class="h2 fw-bold text-uab-azul mb-2">Control de asistencia</h1>
      <p class="text-muted mb-0">Abra la asistencia para que los participantes inscritos confirmen su presencia desde su cuenta.</p>
    </div>

    <?php foreach (['success' => 'success', 'error' => 'danger'] as $clave => $tipo): ?>
      <?php if (!empty($_SESSION[$clave])): ?>
        <div class="alert alert-<?= $tipo ?> py-2 mb-3"><?= $escapar($_SESSION[$clave]) ?></div>
        <?php unset($_SESSION[$clave]); ?>
      <?php endif; ?>
    <?php endforeach; ?>

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

        <div class="card border-0 shadow-sm mb-3 no-print">
          <div class="card-body p-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
              <?php if ($estadoSesion === 'PROGRAMADA'): ?>
                <h3 class="h6 fw-bold mb-1"><i class="bi bi-lock me-1"></i> Asistencia cerrada</h3>
                <p class="small text-muted mb-0">Elija cuánto tiempo podrán confirmar los participantes y abra la sesión.</p>
              <?php elseif ($estadoSesion === 'EN_CURSO'): ?>
                <h3 class="h6 fw-bold text-success mb-1"><i class="bi bi-broadcast-pin me-1"></i> Asistencia abierta<?= $ventanaVencida ? ' (plazo vencido)' : '' ?></h3>
                <p class="small text-muted mb-0"><?= $ventanaVencida ? 'El plazo terminó. Cierre la sesión para consolidar los porcentajes.' : 'Disponible hasta ' . date('d/m/Y H:i', strtotime($sesion['fecha_cierre_programada'])) . '.' ?></p>
              <?php elseif ($estadoSesion === 'CONCLUIDA'): ?>
                <h3 class="h6 fw-bold text-secondary mb-1"><i class="bi bi-check2-circle me-1"></i> Asistencia consolidada</h3>
                <p class="small text-muted mb-0">Puede hacer correcciones puntuales; Presente y Justificado cuentan para certificación.</p>
              <?php else: ?>
                <h3 class="h6 fw-bold text-danger mb-1"><i class="bi bi-x-circle me-1"></i> Sesión cancelada</h3>
              <?php endif; ?>
            </div>
            <?php if ($estadoSesion === 'PROGRAMADA'): ?>
              <form method="post" action="index.php?action=expositor_abrir_asistencia" class="d-flex align-items-center gap-2">
                <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
                <input type="hidden" name="id_evento" value="<?= (int)$idEvento ?>">
                <input type="hidden" name="id_sesion" value="<?= (int)$sesion['id_sesion'] ?>">
                <label for="duracion_minutos" class="visually-hidden">Duración</label>
                <select id="duracion_minutos" name="duracion_minutos" class="form-select">
                  <?php foreach ([15, 30, 45, 60, 90, 120] as $minutos): ?><option value="<?= $minutos ?>" <?= $minutos === 30 ? 'selected' : '' ?>><?= $minutos ?> min</option><?php endforeach; ?>
                </select>
                <button class="btn btn-success text-nowrap" type="submit"><i class="bi bi-unlock me-1"></i> Abrir asistencia</button>
              </form>
            <?php elseif ($estadoSesion === 'EN_CURSO'): ?>
              <form method="post" action="index.php?action=expositor_cerrar_asistencia">
                <input type="hidden" name="csrf_token" value="<?= $escapar(AuthHelper::tokenCsrf()) ?>">
                <input type="hidden" name="id_evento" value="<?= (int)$idEvento ?>">
                <input type="hidden" name="id_sesion" value="<?= (int)$sesion['id_sesion'] ?>">
                <button class="btn btn-outline-danger" type="submit"><i class="bi bi-lock me-1"></i> Cerrar asistencia</button>
              </form>
            <?php endif; ?>
          </div>
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
                      <?php if ($puedeCorregir): ?>
                      <div class="btn-group btn-group-sm asistencia-opciones" role="group" aria-label="Asistencia de <?= $escapar($participante['nombres']) ?>">
                        <?php foreach (['PRESENTE' => 'Presente', 'JUSTIFICADO' => 'Justificado', 'FALTA' => 'Falta'] as $valor => $etiqueta): ?>
                          <button type="button" class="btn <?= $estadoActual($participante['estado_asistencia']) === $valor ? 'btn-uab-azul active' : 'btn-outline-secondary' ?>" data-estado="<?= $valor ?>"><?= $etiqueta ?></button>
                        <?php endforeach; ?>
                      </div>
                      <?php else: ?>
                        <span class="badge bg-light text-dark border"><?= $escapar($participante['estado_asistencia'] === 'SIN_REGISTRO' ? 'Pendiente' : $participante['estado_asistencia']) ?></span>
                      <?php endif; ?>
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
