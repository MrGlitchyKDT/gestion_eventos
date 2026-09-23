<?php 
$titulo_pagina = "Gestión de Eventos — Admin UAB";
$seccion_activa = "eventos";
require_once __DIR__ . '/../../layouts/header.php';
require_once __DIR__ . '/../../layouts/navbar.php'; 
?>

<main class="container-fluid px-3 px-md-4 py-4">
  <div class="row g-4">
    <section class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
          <h1 class="h3 fw-bold text-uab-azul mb-0">Gestión de Eventos</h1>
          <p class="text-muted small mb-0">Administración de capacitaciones, cupos y ciclo de publicación</p>
        </div>
        <button class="btn btn-uab-azul" data-bs-toggle="modal" data-bs-target="#modalNuevoEvento">
          <i class="bi bi-plus-circle me-1"></i> Nuevo Evento
        </button>
      </div>

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success py-2 small mb-3"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($_SESSION['error']) ?></div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <div class="table-responsive card border-0 shadow-sm">
        <table class="table table-uab tabla-eventos-admin table-hover align-middle mb-0">
          <thead>
            <tr>
              <th scope="col">Evento</th>
              <th scope="col">Fechas</th>
              <th scope="col">Cupo</th>
              <th scope="col">Estado</th>
              <th scope="col" class="text-end">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($eventos)): ?>
              <tr><td colspan="5" class="text-center py-4 text-muted">No existen eventos registrados en el sistema.</td></tr>
            <?php else: ?>
              <?php foreach ($eventos as $ev): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($ev['titulo']) ?></strong>
                    <div class="small text-muted">
                      <code><?= htmlspecialchars($ev['codigo']) ?></code> · <?= htmlspecialchars($ev['tipo_evento_nombre']) ?> (<?= htmlspecialchars($ev['modalidad']) ?>)
                    </div>
                  </td>
                  <td class="small">
                    <div><?= date('d/m/Y', strtotime($ev['fecha_inicio'])) ?></div>
                    <span class="text-muted"><?= date('d/m/Y', strtotime($ev['fecha_fin'])) ?></span>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      <?= $ev['total_inscritos'] ?? 0 ?> / <?= $ev['cupo_maximo'] > 0 ? $ev['cupo_maximo'] : '∞' ?>
                    </span>
                  </td>
                  <td>
                    <!-- Switch para cambiar entre BORRADOR y PUBLICADO -->
                    <form action="index.php?action=admin_evento_cambiar_estado" method="POST" class="d-inline">
                      <input type="hidden" name="id_evento" value="<?= $ev['id_evento'] ?>">
                      <input type="hidden" name="estado" value="<?= ($ev['estado'] === 'PUBLICADO') ? 'BORRADOR' : 'PUBLICADO' ?>">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" <?= ($ev['estado'] === 'PUBLICADO') ? 'checked' : '' ?>>
                        <label class="form-check-label small text-muted"><?= $ev['estado'] ?></label>
                      </div>
                    </form>
                  </td>
                  <td class="text-end text-nowrap">
                    <a class="btn btn-sm btn-outline-secondary" href="index.php?action=detalle_evento&id=<?= $ev['id_evento'] ?>" title="Ver público" target="_blank">
                      <i class="bi bi-eye"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalInscripcionManual" onclick="prepararInscripcionManual(<?= $ev['id_evento'] ?>, '<?= htmlspecialchars(addslashes($ev['titulo'])) ?>')">
                      <i class="bi bi-person-plus"></i>
                    </button>
                    <a class="btn btn-sm btn-outline-danger" href="index.php?action=admin_certificados&id_evento=<?= $ev['id_evento'] ?>" title="Emitir certificados">
                      <i class="bi bi-award"></i>
                    </a>
                    <?php
                    $datosEdicion = htmlspecialchars(json_encode([
                        'id_evento' => (int)$ev['id_evento'],
                        'codigo' => $ev['codigo'],
                        'titulo' => $ev['titulo'],
                        'descripcion' => $ev['descripcion'],
                        'id_tipo_evento' => (int)$ev['id_tipo_evento'],
                        'id_categoria' => (int)$ev['id_categoria'],
                        'modalidad' => $ev['modalidad'],
                        'cupo_maximo' => (int)$ev['cupo_maximo'],
                        'horas_academicas' => (int)$ev['horas_academicas'],
                        'porcentaje_asistencia_minimo' => (float)$ev['porcentaje_asistencia_minimo'],
                        'fecha_inicio_inscripcion' => $ev['fecha_inicio_inscripcion'],
                        'fecha_fin_inscripcion' => $ev['fecha_fin_inscripcion'],
                        'fecha_inicio' => $ev['fecha_inicio'],
                        'fecha_fin' => $ev['fecha_fin'],
                        'lugar' => $ev['lugar'],
                        'enlace_virtual' => $ev['enlace_virtual'],
                        'emite_certificado' => (int)$ev['emite_certificado'],
                        'materiales' => $materialesPorEvento[(int)$ev['id_evento']] ?? [],
                        'expositores' => $expositoresPorEvento[(int)$ev['id_evento']] ?? [],
                        'sesiones' => $sesionesPorEvento[(int)$ev['id_evento']] ?? [],
                    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                    ?>
                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalExpositores" data-evento="<?= $datosEdicion ?>" onclick="prepararAsignacionExpositor(this)" title="Gestionar expositores">
                      <i class="bi bi-person-video3"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalSesiones" data-evento="<?= $datosEdicion ?>" onclick="prepararSesionEvento(this)" title="Gestionar sesiones">
                      <i class="bi bi-calendar-week"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-uab-azul" data-bs-toggle="modal" data-bs-target="#modalEditarEvento" data-evento="<?= $datosEdicion ?>" onclick="prepararEdicionEvento(this)" title="Editar evento">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</main>

<!-- Modal: Nuevo Evento -->
<div class="modal fade" id="modalNuevoEvento" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form action="index.php?action=admin_eventos_guardar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Registrar Nuevo Evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Código Único *</label>
              <input type="text" name="codigo" class="form-control" required placeholder="EVT-2026-001">
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Título del Evento *</label>
              <input type="text" name="titulo" class="form-control" required minlength="8">
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-semibold">Tipo *</label>
              <select name="id_tipo_evento" class="form-select" required>
                <?php foreach ($tipos as $t): ?>
                  <option value="<?= $t['id_tipo_evento'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Categoría *</label>
              <select name="id_categoria" class="form-select" required>
                <?php foreach ($categorias as $c): ?>
                  <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Modalidad *</label>
              <select name="modalidad" class="form-select" required>
                <option value="PRESENCIAL">Presencial</option>
                <option value="VIRTUAL">Virtual</option>
                <option value="HIBRIDA">Híbrida</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-semibold">Cupo Máximo (0 = libre)</label>
              <input type="number" name="cupo_maximo" class="form-control" min="0" value="50" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Horas Académicas</label>
              <input type="number" name="horas_academicas" class="form-control" min="1" value="20" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">% Asistencia Mínimo</label>
              <input type="number" step="0.1" name="porcentaje_asistencia_minimo" class="form-control" value="80.0" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Inicio Inscripciones *</label>
              <input type="datetime-local" name="fecha_inicio_inscripcion" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Cierre Inscripciones *</label>
              <input type="datetime-local" name="fecha_fin_inscripcion" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Fecha Inicio Evento *</label>
              <input type="date" name="fecha_inicio" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Fecha Fin Evento *</label>
              <input type="date" name="fecha_fin" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Lugar Físico</label>
              <input type="text" name="lugar" class="form-control" placeholder="Auditorio Central">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Enlace Virtual</label>
              <input type="url" name="enlace_virtual" class="form-control" placeholder="https://meet.google.com/...">
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold">Descripción del Evento *</label>
              <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold" for="nuevo_materiales">Materiales para participantes</label>
              <input class="form-control" type="file" id="nuevo_materiales" name="materiales[]" multiple accept=".pdf,.zip,.rar,.pptx,.docx,.xlsx,.txt">
              <div class="form-text">Puede seleccionar varios archivos. PDF, ZIP, RAR, PPTX, DOCX, XLSX o TXT; máximo 20 MB por archivo.</div>
            </div>

            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="emite_certificado" value="1" id="checkCert" checked>
                <label class="form-check-label small fw-semibold" for="checkCert">Este evento otorga certificado oficial</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm">Guardar en Borrador</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Editar Evento -->
<div class="modal fade" id="modalEditarEvento" tabindex="-1" aria-labelledby="modalEditarEventoTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formEditarEvento" action="index.php?action=admin_evento_actualizar" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <input type="hidden" name="id_evento">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title" id="modalEditarEventoTitulo"><i class="bi bi-pencil-square me-2"></i>Editar Evento</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Código Único</label>
              <input type="text" id="editar_codigo" class="form-control bg-light" readonly>
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Título del Evento *</label>
              <input type="text" name="titulo" class="form-control" required minlength="8">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Tipo *</label>
              <select name="id_tipo_evento" class="form-select" required>
                <?php foreach ($tipos as $t): ?>
                  <option value="<?= $t['id_tipo_evento'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Categoría *</label>
              <select name="id_categoria" class="form-select" required>
                <?php foreach ($categorias as $c): ?>
                  <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Modalidad *</label>
              <select name="modalidad" class="form-select" required>
                <option value="PRESENCIAL">Presencial</option>
                <option value="VIRTUAL">Virtual</option>
                <option value="HIBRIDA">Híbrida</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Cupo Máximo (0 = libre)</label>
              <input type="number" name="cupo_maximo" class="form-control" min="0" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">Horas Académicas</label>
              <input type="number" name="horas_academicas" class="form-control" min="1" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold">% Asistencia Mínimo</label>
              <input type="number" step="0.1" name="porcentaje_asistencia_minimo" class="form-control" min="0" max="100" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Inicio Inscripciones *</label>
              <input type="datetime-local" name="fecha_inicio_inscripcion" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Cierre Inscripciones *</label>
              <input type="datetime-local" name="fecha_fin_inscripcion" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Fecha Inicio Evento *</label>
              <input type="date" name="fecha_inicio" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Fecha Fin Evento *</label>
              <input type="date" name="fecha_fin" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Lugar Físico</label>
              <input type="text" name="lugar" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Enlace Virtual</label>
              <input type="url" name="enlace_virtual" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Descripción del Evento *</label>
              <textarea name="descripcion" class="form-control" rows="3" required></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold" for="editar_materiales">Añadir materiales para participantes</label>
              <input class="form-control" type="file" id="editar_materiales" name="materiales[]" multiple accept=".pdf,.zip,.rar,.pptx,.docx,.xlsx,.txt">
              <div class="form-text">Puede agregar varios archivos. Máximo 20 MB por archivo.</div>
              <div id="editar_materiales_existentes" class="mt-2"></div>
            </div>
            <div class="col-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="emite_certificado" value="1" id="editar_check_certificado">
                <label class="form-check-label small fw-semibold" for="editar_check_certificado">Este evento otorga certificado oficial</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-check-lg me-1"></i> Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Gestión de Sesiones -->
<div class="modal fade" id="modalSesiones" tabindex="-1" aria-labelledby="modalSesionesTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <form action="index.php?action=admin_evento_crear_sesion" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <input type="hidden" name="id_evento" id="sesion_id_evento">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title" id="modalSesionesTitulo"><i class="bi bi-calendar-week me-2"></i>Cronograma de Sesiones</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Evento</label>
            <input type="text" id="sesion_evento_titulo" class="form-control bg-light" readonly>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold" for="sesion_titulo">Título de la sesión *</label>
              <input type="text" name="titulo" id="sesion_titulo" class="form-control" maxlength="150" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="sesion_fecha">Fecha *</label>
              <input type="date" name="fecha" id="sesion_fecha" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="sesion_hora_inicio">Hora de inicio *</label>
              <input type="time" name="hora_inicio" id="sesion_hora_inicio" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="sesion_hora_fin">Hora de fin *</label>
              <input type="time" name="hora_fin" id="sesion_hora_fin" class="form-control" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold" for="sesion_lugar">Lugar específico</label>
              <input type="text" name="lugar_especifico" id="sesion_lugar" class="form-control" maxlength="200" placeholder="Aula, auditorio o enlace virtual">
            </div>
          </div>
          <div class="border-top mt-4 pt-3">
            <h6 class="small fw-bold mb-2">Sesiones registradas</h6>
            <div id="lista_sesiones_evento" class="small text-muted">Seleccione un evento.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-plus-circle me-1"></i>Agregar sesión</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Gestión de Expositores -->
<div class="modal fade" id="modalExpositores" tabindex="-1" aria-labelledby="modalExpositoresTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <form action="index.php?action=admin_evento_asignar_expositor" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <input type="hidden" name="id_evento" id="expositor_id_evento">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title" id="modalExpositoresTitulo"><i class="bi bi-person-video3 me-2"></i>Asignar Expositores</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Evento</label>
            <input type="text" id="expositor_evento_titulo" class="form-control bg-light" readonly>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold" for="expositor_usuario">Expositor *</label>
              <select name="id_usuario" id="expositor_usuario" class="form-select" required>
                <option value="">Seleccione un expositor activo...</option>
                <?php foreach ($usuarios_expositores as $expositor): ?>
                  <option value="<?= (int)$expositor['id_usuario'] ?>"><?= htmlspecialchars($expositor['apellidos'] . ', ' . $expositor['nombres'] . ' — ' . $expositor['correo']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold" for="rol_expositor">Función en el evento *</label>
              <input type="text" name="rol_expositor" id="rol_expositor" class="form-control" value="Expositor Principal" maxlength="100" required>
            </div>
          </div>
          <div class="border-top mt-4 pt-3">
            <h6 class="small fw-bold mb-2">Expositores asignados</h6>
            <div id="lista_expositores_asignados" class="small text-muted">Seleccione un evento.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-person-plus me-1"></i>Asignar expositor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Inscripción Manual -->
<div class="modal fade" id="modalInscripcionManual" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="index.php?action=admin_inscribir_manual" method="POST">
        <div class="modal-header bg-uab-verde text-white">
          <h5 class="modal-title"><i class="bi bi-person-plus me-1"></i> Inscripción Manual Administrativa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="id_evento" id="manual_id_evento">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Evento Seleccionado</label>
            <input type="text" id="manual_evento_titulo" class="form-control bg-light" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Seleccione el Participante *</label>
            <select name="id_usuario" class="form-select" required>
              <option value="" disabled selected>Seleccione usuario registrado...</option>
              <?php foreach ($usuarios_participantes as $u): ?>
                <option value="<?= $u['id_usuario'] ?>">
                  <?= htmlspecialchars($u['apellidos'] . ' ' . $u['nombres']) ?> (CI: <?= htmlspecialchars($u['ci']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-verde btn-sm">Confirmar Inscripción</button>
        </div>
      </form>
    </div>
  </div>
</div>

<form id="formEliminarMaterial" action="index.php?action=admin_material_eliminar" method="POST" class="d-none">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
  <input type="hidden" name="id_material" id="eliminar_id_material">
</form>

<form id="formDesasignarExpositor" action="index.php?action=admin_evento_desasignar_expositor" method="POST" class="d-none">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
  <input type="hidden" name="id_evento" id="desasignar_expositor_evento">
  <input type="hidden" name="id_usuario" id="desasignar_expositor_usuario">
</form>

<form id="formEliminarSesion" action="index.php?action=admin_evento_eliminar_sesion" method="POST" class="d-none">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
  <input type="hidden" name="id_sesion" id="eliminar_id_sesion">
</form>

<script>
function prepararInscripcionManual(idEvento, titulo) {
  document.getElementById('manual_id_evento').value = idEvento;
  document.getElementById('manual_evento_titulo').value = titulo;
}

function prepararSesionEvento(boton) {
  const evento = JSON.parse(boton.dataset.evento);
  document.getElementById('sesion_id_evento').value = evento.id_evento;
  document.getElementById('sesion_evento_titulo').value = `${evento.codigo} — ${evento.titulo}`;
  document.getElementById('sesion_titulo').value = '';
  document.getElementById('sesion_fecha').value = evento.fecha_inicio;
  document.getElementById('sesion_fecha').min = evento.fecha_inicio;
  document.getElementById('sesion_fecha').max = evento.fecha_fin;
  document.getElementById('sesion_hora_inicio').value = '';
  document.getElementById('sesion_hora_fin').value = '';
  document.getElementById('sesion_lugar').value = evento.lugar || '';

  const escaparHtml = (texto) => String(texto ?? '').replace(/[&<>'"]/g, (caracter) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  }[caracter]));
  const sesiones = Array.isArray(evento.sesiones) ? evento.sesiones : [];
  const lista = document.getElementById('lista_sesiones_evento');
  lista.innerHTML = sesiones.length
    ? sesiones.map((sesion) => `
        <div class="d-flex align-items-center justify-content-between gap-2 border rounded px-2 py-2 mb-2">
          <div><strong>${escaparHtml(sesion.titulo)}</strong><br><span class="text-muted"><i class="bi bi-calendar3 me-1"></i>${escaparHtml(sesion.fecha)} · ${escaparHtml(String(sesion.hora_inicio).slice(0, 5))} - ${escaparHtml(String(sesion.hora_fin).slice(0, 5))}${sesion.lugar_especifico ? ` · ${escaparHtml(sesion.lugar_especifico)}` : ''}</span></div>
          <button type="button" class="btn btn-outline-danger btn-sm" data-eliminar-sesion="${Number(sesion.id_sesion)}" title="Eliminar sesión"><i class="bi bi-trash"></i></button>
        </div>`).join('')
    : '<div class="text-muted">Aún no se registraron sesiones para este evento.</div>';

  lista.querySelectorAll('[data-eliminar-sesion]').forEach((botonEliminar) => {
    botonEliminar.addEventListener('click', () => {
      if (window.confirm('¿Eliminar esta sesión del cronograma?')) {
        document.getElementById('eliminar_id_sesion').value = botonEliminar.dataset.eliminarSesion;
        document.getElementById('formEliminarSesion').submit();
      }
    });
  });
}

function prepararAsignacionExpositor(boton) {
  const evento = JSON.parse(boton.dataset.evento);
  document.getElementById('expositor_id_evento').value = evento.id_evento;
  document.getElementById('expositor_evento_titulo').value = `${evento.codigo} — ${evento.titulo}`;
  document.getElementById('expositor_usuario').value = '';
  document.getElementById('rol_expositor').value = 'Expositor Principal';

  const escaparHtml = (texto) => String(texto ?? '').replace(/[&<>'"]/g, (caracter) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  }[caracter]));
  const expositores = Array.isArray(evento.expositores) ? evento.expositores : [];
  const lista = document.getElementById('lista_expositores_asignados');
  lista.innerHTML = expositores.length
    ? expositores.map((expositor) => `
        <div class="d-flex align-items-center justify-content-between gap-2 border rounded px-2 py-2 mb-2">
          <div><strong>${escaparHtml(expositor.nombres)} ${escaparHtml(expositor.apellidos)}</strong><br><span class="text-muted">${escaparHtml(expositor.rol_expositor || 'Expositor')}</span></div>
          <button type="button" class="btn btn-outline-danger btn-sm" data-desasignar-expositor="${Number(expositor.id_usuario)}" title="Retirar del evento"><i class="bi bi-person-dash"></i></button>
        </div>`).join('')
    : '<div class="text-muted">Aún no se asignaron expositores a este evento.</div>';

  lista.querySelectorAll('[data-desasignar-expositor]').forEach((botonRetirar) => {
    botonRetirar.addEventListener('click', () => {
      if (window.confirm('¿Retirar a este expositor del evento?')) {
        document.getElementById('desasignar_expositor_evento').value = evento.id_evento;
        document.getElementById('desasignar_expositor_usuario').value = botonRetirar.dataset.desasignarExpositor;
        document.getElementById('formDesasignarExpositor').submit();
      }
    });
  });
}

function prepararEdicionEvento(boton) {
  const evento = JSON.parse(boton.dataset.evento);
  const formulario = document.getElementById('formEditarEvento');
  const fechaHoraLocal = (valor) => valor ? valor.replace(' ', 'T').slice(0, 16) : '';

  formulario.elements.id_evento.value = evento.id_evento;
  document.getElementById('editar_codigo').value = evento.codigo;
  formulario.elements.titulo.value = evento.titulo;
  formulario.elements.descripcion.value = evento.descripcion;
  formulario.elements.id_tipo_evento.value = evento.id_tipo_evento;
  formulario.elements.id_categoria.value = evento.id_categoria;
  formulario.elements.modalidad.value = evento.modalidad;
  formulario.elements.cupo_maximo.value = evento.cupo_maximo;
  formulario.elements.horas_academicas.value = evento.horas_academicas;
  formulario.elements.porcentaje_asistencia_minimo.value = evento.porcentaje_asistencia_minimo;
  formulario.elements.fecha_inicio_inscripcion.value = fechaHoraLocal(evento.fecha_inicio_inscripcion);
  formulario.elements.fecha_fin_inscripcion.value = fechaHoraLocal(evento.fecha_fin_inscripcion);
  formulario.elements.fecha_inicio.value = evento.fecha_inicio;
  formulario.elements.fecha_fin.value = evento.fecha_fin;
  formulario.elements.lugar.value = evento.lugar || '';
  formulario.elements.enlace_virtual.value = evento.enlace_virtual || '';
  formulario.elements.emite_certificado.checked = Number(evento.emite_certificado) === 1;
  document.getElementById('editar_materiales').value = '';

  const contenedor = document.getElementById('editar_materiales_existentes');
  const escaparHtml = (texto) => String(texto ?? '').replace(/[&<>'"]/g, (caracter) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  }[caracter]));
  const materiales = Array.isArray(evento.materiales) ? evento.materiales : [];
  contenedor.innerHTML = materiales.length
    ? `<div class="small fw-semibold mt-2 mb-1">Archivos publicados</div>${materiales.map((material) => `
        <div class="d-flex align-items-center justify-content-between gap-2 border rounded px-2 py-1 mb-1 small">
          <a href="index.php?action=descargar_material&id=${Number(material.id_material)}" class="text-decoration-none text-truncate">
            <i class="bi bi-file-earmark-arrow-down me-1"></i>${escaparHtml(material.titulo)} <span class="text-muted">(${escaparHtml(material.tipo_archivo)})</span>
          </a>
          <button type="button" class="btn btn-outline-danger btn-sm py-0" data-eliminar-material="${Number(material.id_material)}" aria-label="Eliminar archivo"><i class="bi bi-trash"></i></button>
        </div>`).join('')}`
    : '<div class="form-text">Este evento aún no tiene archivos publicados.</div>';

  contenedor.querySelectorAll('[data-eliminar-material]').forEach((botonEliminar) => {
    botonEliminar.addEventListener('click', () => {
      if (window.confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) {
        document.getElementById('eliminar_id_material').value = botonEliminar.dataset.eliminarMaterial;
        document.getElementById('formEliminarMaterial').submit();
      }
    });
  });
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
