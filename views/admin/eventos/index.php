<?php
$__qrHelper = __DIR__ . '/../../../helpers/QrHelper.php';
require_once $__qrHelper;
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

      <form method="get" class="card border-0 shadow-sm mb-3">
        <input type="hidden" name="action" value="admin_eventos">
        <div class="card-body row g-3 align-items-end">
          <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="buscar-evento">Buscar por nombre</label><input class="form-control" id="buscar-evento" type="search" name="buscar" value="<?= htmlspecialchars($filtrosEventos['buscar']) ?>" placeholder="Nombre del evento"></div>
          <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="tipo-evento-filtro">Tipo de evento</label><select class="form-select" id="tipo-evento-filtro" name="id_tipo_evento"><option value="">Todos los tipos</option><?php foreach ($tipos as $tipo): ?><option value="<?= (int)$tipo['id_tipo_evento'] ?>" <?= $filtrosEventos['id_tipo_evento'] === (int)$tipo['id_tipo_evento'] ? 'selected' : '' ?>><?= htmlspecialchars($tipo['nombre']) ?></option><?php endforeach; ?></select></div>
          <div class="col-12 col-md-6 col-xl-3"><label class="form-label fw-semibold" for="area-evento-filtro">Área</label><select class="form-select" id="area-evento-filtro" name="id_categoria"><option value="">Todas las áreas</option><?php foreach ($categorias as $categoria): ?><option value="<?= (int)$categoria['id_categoria'] ?>" <?= $filtrosEventos['id_categoria'] === (int)$categoria['id_categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option><?php endforeach; ?></select></div>
          <div class="col-12 col-md-6 col-xl-2"><label class="form-label fw-semibold" for="habilitado-evento-filtro">Habilitado</label><select class="form-select" id="habilitado-evento-filtro" name="habilitado"><option value="">Todos</option><option value="SI" <?= $filtrosEventos['habilitado'] === 'SI' ? 'selected' : '' ?>>Habilitado</option><option value="NO" <?= $filtrosEventos['habilitado'] === 'NO' ? 'selected' : '' ?>>No habilitado</option></select></div>
          <div class="col-12 col-xl-2 d-flex gap-2"><button class="btn btn-uab-azul flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button><a class="btn btn-outline-secondary" href="index.php?action=admin_eventos" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a></div>
        </div>
      </form>

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
              <tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron eventos con los filtros seleccionados.</td></tr>
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
                    <?php $urlInscripcionQr = QrHelper::generarUrlInscripcionEvento((int)$ev['id_evento']); ?>
                    <button type="button" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalQrInscripcion" data-evento-titulo="<?= htmlspecialchars($ev['titulo'], ENT_QUOTES, 'UTF-8') ?>" data-url-inscripcion="<?= htmlspecialchars($urlInscripcionQr, ENT_QUOTES, 'UTF-8') ?>" onclick="prepararQrInscripcion(this)" title="Código QR de inscripción">
                      <i class="bi bi-qr-code"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalInscripcionManual" data-id-evento="<?= (int)$ev['id_evento'] ?>" data-evento-titulo="<?= htmlspecialchars($ev['titulo'], ENT_QUOTES, 'UTF-8') ?>" onclick="prepararInscripcionManual(this)" title="Inscribir participantes o expositores">
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
                        'series' => $seriesPorEvento[(int)$ev['id_evento']] ?? [],
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

<div class="modal fade" id="modalQrInscripcion" tabindex="-1" aria-labelledby="modalQrInscripcionTitulo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
    <div class="modal-header bg-uab-azul text-white"><h2 class="modal-title h5" id="modalQrInscripcionTitulo"><i class="bi bi-qr-code me-2"></i>QR de inscripción</h2><button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <div class="modal-body text-center"><p class="mb-3">Escanee este código para abrir la ficha de <strong id="qr-inscripcion-evento"></strong>.</p><img id="qr-inscripcion-imagen" class="img-fluid border rounded p-2 bg-white" width="260" height="260" alt="Código QR de inscripción"><label class="form-label visually-hidden" for="qr-inscripcion-url">Enlace de inscripción</label><div class="input-group mt-3"><input id="qr-inscripcion-url" class="form-control" readonly><button type="button" class="btn btn-outline-secondary" onclick="copiarUrlInscripcionQr()" title="Copiar enlace"><i class="bi bi-copy"></i></button></div><p class="form-text text-start mb-0">El enlace abre la ficha pública, donde la persona podrá iniciar sesión y confirmar su inscripción.</p></div>
    <div class="modal-footer"><a id="qr-inscripcion-abrir" class="btn btn-outline-primary" target="_blank"><i class="bi bi-box-arrow-up-right me-1"></i>Abrir enlace</a><a id="qr-inscripcion-descargar" class="btn btn-uab-azul" target="_blank"><i class="bi bi-download me-1"></i>Abrir QR</a></div>
  </div></div>
</div>

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
              <label class="form-label small fw-semibold" for="nuevo_codigo_previsualizacion">Código Único</label>
              <input type="text" id="nuevo_codigo_previsualizacion" class="form-control bg-light" readonly aria-describedby="ayuda_codigo_previsualizacion">
              <div id="ayuda_codigo_previsualizacion" class="form-text">Se asignará automáticamente al guardar el evento.</div>
            </div>
            <div class="col-md-8">
              <label class="form-label small fw-semibold">Título del Evento *</label>
              <input type="text" name="titulo" class="form-control" required minlength="8">
            </div>

            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="nuevo_tipo_evento">Tipo *</label>
              <select id="nuevo_tipo_evento" name="id_tipo_evento" class="form-select" required>
                <?php foreach ($tipos as $t): ?>
                  <option value="<?= $t['id_tipo_evento'] ?>" data-abreviatura="<?= htmlspecialchars($t['abreviatura'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-semibold" for="nuevo_categoria_evento">Área *</label>
              <select id="nuevo_categoria_evento" name="id_categoria" class="form-select" required>
                <?php foreach ($categorias as $c): ?>
                  <option value="<?= $c['id_categoria'] ?>" data-abreviatura="<?= htmlspecialchars($c['abreviatura'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($c['nombre']) ?></option>
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
              <label class="form-label small fw-semibold">Área *</label>
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
            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
              <div>
                <h6 class="small fw-bold mb-0">Series recurrentes</h6>
                <div class="form-text">Para cursos con varias fechas, genere las sesiones automáticamente.</div>
              </div>
              <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalSerieSesiones" onclick="abrirFormularioSerie()">
                <i class="bi bi-calendar-plus me-1"></i> Nueva serie
              </button>
            </div>
            <div id="lista_series_evento" class="small text-muted">Seleccione un evento.</div>
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

<!-- Modal: Serie recurrente de sesiones -->
<div class="modal fade" id="modalSerieSesiones" tabindex="-1" aria-labelledby="modalSerieSesionesTitulo" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form action="index.php?action=admin_evento_guardar_serie_sesiones" method="POST" id="formSerieSesiones">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <input type="hidden" name="id_serie" id="serie_id_serie">
        <input type="hidden" name="id_evento" id="serie_id_evento">
        <div class="modal-header bg-uab-azul text-white">
          <h5 class="modal-title" id="modalSerieSesionesTitulo"><i class="bi bi-calendar-range me-2"></i>Nueva serie de sesiones</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-primary small py-2"><i class="bi bi-info-circle me-1"></i>Se creará una sesión por cada fecha generada. Cada una tendrá su propia asistencia.</div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Evento</label>
            <input type="text" id="serie_evento_titulo" class="form-control bg-light" readonly>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold" for="serie_titulo">Título de las sesiones *</label>
              <input type="text" name="titulo" id="serie_titulo" class="form-control" maxlength="150" required placeholder="Ej.: Desarrollo Web — Clase">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold" for="serie_fecha_inicio">Desde *</label>
              <input type="date" name="fecha_inicio" id="serie_fecha_inicio" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold" for="serie_fecha_fin">Hasta *</label>
              <input type="date" name="fecha_fin" id="serie_fecha_fin" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold" for="serie_hora_inicio">Hora de inicio *</label>
              <input type="time" name="hora_inicio" id="serie_hora_inicio" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold" for="serie_hora_fin">Hora de fin *</label>
              <input type="time" name="hora_fin" id="serie_hora_fin" class="form-control" required>
            </div>
            <div class="col-md-7">
              <label class="form-label small fw-semibold" for="serie_frecuencia">Frecuencia *</label>
              <select name="frecuencia" id="serie_frecuencia" class="form-select">
                <option value="SEMANAL">Semanal, en días seleccionados</option>
                <option value="DIARIA">Cada cierto número de días</option>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-semibold" for="serie_intervalo">Repetir cada *</label>
              <div class="input-group"><input type="number" name="intervalo_recurrencia" id="serie_intervalo" class="form-control" min="1" max="30" value="1" required><span class="input-group-text" id="serie_intervalo_texto">semana(s)</span></div>
            </div>
            <div class="col-12" id="serie_dias_contenedor">
              <label class="form-label small fw-semibold d-block">Días de clase *</label>
              <div class="d-flex flex-wrap gap-2">
                <?php foreach ([1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'] as $dia => $nombre): ?>
                  <input class="btn-check serie-dia" type="checkbox" name="dias_semana[]" value="<?= $dia ?>" id="serie_dia_<?= $dia ?>">
                  <label class="btn btn-outline-secondary btn-sm" for="serie_dia_<?= $dia ?>"><?= $nombre ?></label>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold" for="serie_lugar">Lugar específico</label>
              <input type="text" name="lugar_especifico" id="serie_lugar" class="form-control" maxlength="200" placeholder="Aula, auditorio o enlace virtual">
            </div>
          </div>
          <div class="alert alert-light border mt-4 mb-0">
            <strong><i class="bi bi-calendar-check me-1"></i>Vista previa:</strong>
            <span id="serie_previsualizacion">Complete el periodo y la frecuencia para calcular las sesiones.</span>
          </div>
          <p class="form-text mb-0 mt-2">Al editar, sólo se regeneran las sesiones futuras programadas que no sean excepciones.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-azul btn-sm"><i class="bi bi-calendar-plus me-1"></i><span id="serie_boton_texto">Crear sesiones</span></button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal: Gestión de Expositores -->
<div class="modal fade" id="modalExpositores" tabindex="-1" aria-labelledby="modalExpositoresTitulo" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formAsignarExpositor" action="index.php?action=admin_evento_asignar_expositor" method="POST">
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
              <input type="hidden" name="id_usuario" id="expositor_usuario">
              <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
                <label class="form-label small fw-semibold mb-0" for="expositor_buscar">Buscar expositor *</label>
                <button type="button" id="expositor_limpiar_seleccion" class="btn btn-outline-secondary btn-sm">Limpiar</button>
              </div>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" id="expositor_buscar" class="form-control" autocomplete="off" placeholder="Escriba nombre, apellido o CI">
              </div>
              <div class="form-text">Solo se mostrarán cuentas activas con rol de expositor. Escriba al menos dos caracteres.</div>
              <div id="expositor_resultados" class="border rounded overflow-auto mt-2" style="max-height: 220px;">
                <div class="text-muted small p-3">Use el buscador para encontrar un expositor por nombre o CI.</div>
              </div>
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
<div class="modal fade" id="modalInscripcionManual" tabindex="-1" aria-hidden="true" aria-labelledby="tituloInscripcionManual">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formInscripcionManual" action="index.php?action=admin_inscribir_manual" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(AuthHelper::tokenCsrf()) ?>">
        <div class="modal-header bg-uab-verde text-white">
          <h5 class="modal-title" id="tituloInscripcionManual"><i class="bi bi-person-plus me-1"></i> Inscripción Manual Administrativa</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="id_evento" id="manual_id_evento">
          <div id="manual_ids_seleccionados"></div>
          <div class="mb-3">
            <label class="form-label small fw-semibold">Evento Seleccionado</label>
            <input type="text" id="manual_evento_titulo" class="form-control bg-light" readonly>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-1">
              <label class="form-label small fw-semibold mb-0" for="manual_buscar_participante">Buscar personas</label>
              <span id="manual_contador_seleccion" class="badge text-bg-primary">0 seleccionados</span>
            </div>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="search" id="manual_buscar_participante" class="form-control" autocomplete="off" placeholder="Escriba nombre, apellido o CI">
              <button type="button" id="manual_limpiar_seleccion" class="btn btn-outline-secondary">Limpiar</button>
            </div>
            <div class="form-text">Puede seleccionar participantes o expositores. Escriba al menos dos caracteres para buscarlos.</div>
          </div>
          <div id="manual_resultados_participantes" class="border rounded overflow-auto" style="max-height: 320px;">
            <div class="text-muted small p-3">Use el buscador para encontrar participantes o expositores por nombre o CI.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-uab-verde btn-sm"><i class="bi bi-person-plus me-1"></i>Confirmar inscripciones</button>
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
let eventoSesionesActivo = null;
const participantesInscripcionManual = <?= json_encode(array_map(static fn(array $usuario): array => [
  'id' => (int)$usuario['id_usuario'],
  'nombres' => (string)$usuario['nombres'],
  'apellidos' => (string)$usuario['apellidos'],
  'ci' => (string)$usuario['ci'],
  'rol' => (string)$usuario['rol_nombre'],
], $usuarios_participantes), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
const participantesSeleccionadosManual = new Set();
const expositoresAsignacion = <?= json_encode(array_map(static fn(array $usuario): array => [
  'id' => (int)$usuario['id_usuario'],
  'nombres' => (string)$usuario['nombres'],
  'apellidos' => (string)$usuario['apellidos'],
  'ci' => (string)$usuario['ci'],
  'correo' => (string)$usuario['correo'],
], $usuarios_expositores), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>;
let expositorSeleccionadoAsignacion = null;

// La base conserva id_categoria por compatibilidad; la interfaz lo presenta como Área.
document.querySelectorAll('select[name="id_categoria"]').forEach((select) => {
  const etiqueta = select.closest('.col-md-4')?.querySelector('label');
  if (etiqueta) etiqueta.textContent = 'Área *';
});

function normalizarBusquedaParticipante(valor) {
  return String(valor || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase();
}

function escaparTextoInscripcionManual(valor) {
  return String(valor ?? '').replace(/[&<>'"]/g, (caracter) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'
  }[caracter]));
}

function renderizarResultadosExpositor() {
  const campo = document.getElementById('expositor_buscar');
  const contenedor = document.getElementById('expositor_resultados');
  if (!campo || !contenedor) return;

  const termino = normalizarBusquedaParticipante(campo.value.trim());
  const seleccionado = expositoresAsignacion.find((expositor) => expositor.id === expositorSeleccionadoAsignacion);
  const crearFila = (expositor, seleccionadoActual = false) => {
    const nombre = escaparTextoInscripcionManual(`${expositor.apellidos} ${expositor.nombres}`);
    const ci = escaparTextoInscripcionManual(expositor.ci);
    const correo = escaparTextoInscripcionManual(expositor.correo);
    return `<label class="d-flex align-items-center gap-3 px-3 py-2 border-top cursor-pointer ${seleccionadoActual ? 'bg-light' : ''}">
      <input class="form-check-input flex-shrink-0 m-0" type="checkbox" data-seleccionar-expositor="${expositor.id}" ${seleccionadoActual ? 'checked' : ''}>
      <span><strong>${nombre}</strong><span class="d-block small text-muted">CI: ${ci}${correo ? ` · ${correo}` : ''}</span></span>
    </label>`;
  };
  const bloqueSeleccionado = seleccionado
    ? `<div class="px-3 py-2 small fw-semibold text-primary bg-light border-bottom"><i class="bi bi-check2-circle me-1"></i>Expositor seleccionado</div>${crearFila(seleccionado, true)}`
    : '';

  if (termino.length < 2) {
    contenedor.innerHTML = bloqueSeleccionado + '<div class="text-muted small p-3">Use al menos dos caracteres para buscar por nombre, apellido o CI.</div>';
  } else {
    const coincidencias = expositoresAsignacion.filter((expositor) => {
      const texto = normalizarBusquedaParticipante(`${expositor.nombres} ${expositor.apellidos} ${expositor.ci}`);
      return texto.includes(termino) && expositor.id !== expositorSeleccionadoAsignacion;
    });
    const limite = 50;
    const aviso = coincidencias.length > limite
      ? `<div class="small text-muted px-3 pt-2">Mostrando los primeros ${limite} resultados. Especifique más su búsqueda.</div>`
      : '';
    contenedor.innerHTML = bloqueSeleccionado + (coincidencias.length
      ? aviso + coincidencias.slice(0, limite).map((expositor) => crearFila(expositor)).join('')
      : '<div class="text-muted small p-3">No se encontraron otros expositores activos con esos datos.</div>');
  }

  contenedor.querySelectorAll('[data-seleccionar-expositor]').forEach((campoSeleccion) => {
    campoSeleccion.addEventListener('change', () => {
      expositorSeleccionadoAsignacion = campoSeleccion.checked
        ? Number(campoSeleccion.dataset.seleccionarExpositor)
        : null;
      document.getElementById('expositor_usuario').value = expositorSeleccionadoAsignacion === null
        ? ''
        : String(expositorSeleccionadoAsignacion);
      campo.value = '';
      renderizarResultadosExpositor();
    });
  });
}

function actualizarSeleccionInscripcionManual() {
  const contenedor = document.getElementById('manual_ids_seleccionados');
  contenedor.innerHTML = [...participantesSeleccionadosManual]
    .map((id) => `<input type="hidden" name="id_usuarios[]" value="${Number(id)}">`)
    .join('');
  const cantidad = participantesSeleccionadosManual.size;
  document.getElementById('manual_contador_seleccion').textContent = `${cantidad} seleccionado${cantidad === 1 ? '' : 's'}`;
}

function renderizarResultadosInscripcionManual() {
  const campo = document.getElementById('manual_buscar_participante');
  const contenedor = document.getElementById('manual_resultados_participantes');
  const termino = normalizarBusquedaParticipante(campo.value.trim());
  const seleccionados = participantesInscripcionManual.filter((participante) => participantesSeleccionadosManual.has(participante.id));
  const crearFila = (participante) => {
    const seleccionado = participantesSeleccionadosManual.has(participante.id);
    const nombre = escaparTextoInscripcionManual(`${participante.apellidos} ${participante.nombres}`);
    const ci = escaparTextoInscripcionManual(participante.ci);
    const rol = escaparTextoInscripcionManual(participante.rol === 'EXPOSITOR' ? 'Expositor' : 'Participante');
    return `<label class="d-flex align-items-center gap-3 px-3 py-2 border-top cursor-pointer ${seleccionado ? 'bg-light' : ''}">
      <input class="form-check-input flex-shrink-0 m-0" type="checkbox" data-participante-manual="${participante.id}" ${seleccionado ? 'checked' : ''}>
      <span><strong>${nombre}</strong><span class="d-block small text-muted">CI: ${ci} · ${rol}</span></span>
    </label>`;
  };
  const bloqueSeleccionados = seleccionados.length
    ? `<div class="px-3 py-2 small fw-semibold text-primary bg-light border-bottom"><i class="bi bi-check2-square me-1"></i>Personas seleccionadas</div>${seleccionados.map(crearFila).join('')}`
    : '';

  if (termino.length < 2) {
    contenedor.innerHTML = bloqueSeleccionados + '<div class="text-muted small p-3">Use al menos dos caracteres para buscar por nombre, apellido o CI.</div>';
    activarSeleccionInscripcionManual(contenedor);
    return;
  }

  const coincidencias = participantesInscripcionManual.filter((participante) => {
    const texto = normalizarBusquedaParticipante(`${participante.nombres} ${participante.apellidos} ${participante.ci}`);
    return texto.includes(termino) && !participantesSeleccionadosManual.has(participante.id);
  });
  const limite = 50;
  const visibles = coincidencias.slice(0, limite);
  if (!visibles.length) {
    contenedor.innerHTML = bloqueSeleccionados + '<div class="text-muted small p-3">No se encontraron más personas activas con esos datos.</div>';
    activarSeleccionInscripcionManual(contenedor);
    return;
  }

  const aviso = coincidencias.length > limite
    ? `<div class="small text-muted px-3 pt-2">Mostrando los primeros ${limite} resultados. Especifique más su búsqueda.</div>`
    : '';
  contenedor.innerHTML = bloqueSeleccionados + aviso + visibles.map(crearFila).join('');
  activarSeleccionInscripcionManual(contenedor);
}

function activarSeleccionInscripcionManual(contenedor) {
  contenedor.querySelectorAll('[data-participante-manual]').forEach((campoSeleccion) => {
    campoSeleccion.addEventListener('change', () => {
      const id = Number(campoSeleccion.dataset.participanteManual);
      if (campoSeleccion.checked) participantesSeleccionadosManual.add(id);
      else participantesSeleccionadosManual.delete(id);
      actualizarSeleccionInscripcionManual();
      renderizarResultadosInscripcionManual();
    });
  });
}

function prepararInscripcionManual(boton) {
  document.getElementById('manual_id_evento').value = boton.dataset.idEvento;
  document.getElementById('manual_evento_titulo').value = boton.dataset.eventoTitulo;
  participantesSeleccionadosManual.clear();
  document.getElementById('manual_buscar_participante').value = '';
  actualizarSeleccionInscripcionManual();
  renderizarResultadosInscripcionManual();
}

document.getElementById('manual_buscar_participante')?.addEventListener('input', renderizarResultadosInscripcionManual);
document.getElementById('manual_limpiar_seleccion')?.addEventListener('click', () => {
  participantesSeleccionadosManual.clear();
  actualizarSeleccionInscripcionManual();
  renderizarResultadosInscripcionManual();
});
document.getElementById('formInscripcionManual')?.addEventListener('submit', (evento) => {
  if (!participantesSeleccionadosManual.size) {
    evento.preventDefault();
    document.getElementById('manual_buscar_participante').focus();
    window.alert('Seleccione al menos una persona para continuar.');
  }
});

document.getElementById('expositor_buscar')?.addEventListener('input', renderizarResultadosExpositor);
document.getElementById('expositor_limpiar_seleccion')?.addEventListener('click', () => {
  expositorSeleccionadoAsignacion = null;
  document.getElementById('expositor_usuario').value = '';
  document.getElementById('expositor_buscar').value = '';
  renderizarResultadosExpositor();
});
document.getElementById('formAsignarExpositor')?.addEventListener('submit', (evento) => {
  if (!document.getElementById('expositor_usuario').value) {
    evento.preventDefault();
    document.getElementById('expositor_buscar').focus();
    window.alert('Seleccione un expositor para continuar.');
  }
});

function prepararSesionEvento(boton) {
  const evento = JSON.parse(boton.dataset.evento);
  eventoSesionesActivo = evento;
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

  const series = Array.isArray(evento.series) ? evento.series : [];
  const listaSeries = document.getElementById('lista_series_evento');
  listaSeries.innerHTML = series.length
    ? series.map((serie) => {
        const dias = String(serie.dias_semana || '').split(',').filter(Boolean).map(Number);
        const nombresDias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        const frecuencia = serie.frecuencia === 'SEMANAL'
          ? dias.map((dia) => nombresDias[dia - 1]).join(', ')
          : `cada ${serie.intervalo_recurrencia} día(s)`;
        return `<div class="d-flex align-items-center justify-content-between gap-2 border rounded px-2 py-2 mb-2">
          <div><strong>${escaparHtml(serie.titulo)}</strong><br><span class="text-muted">${escaparHtml(serie.fecha_inicio)} al ${escaparHtml(serie.fecha_fin)} · ${escaparHtml(frecuencia)}</span></div>
          <button type="button" class="btn btn-outline-primary btn-sm" data-editar-serie="${Number(serie.id_serie)}" title="Editar sesiones futuras"><i class="bi bi-pencil"></i></button>
        </div>`;
      }).join('')
    : '<div class="text-muted">No hay series recurrentes para este evento.</div>';
  listaSeries.querySelectorAll('[data-editar-serie]').forEach((botonEditar) => {
    botonEditar.addEventListener('click', () => {
      const serie = series.find((item) => Number(item.id_serie) === Number(botonEditar.dataset.editarSerie));
      abrirFormularioSerie(serie);
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalSerieSesiones')).show();
    });
  });
}

function abrirFormularioSerie(serie = null) {
  const evento = eventoSesionesActivo;
  if (!evento) return;
  document.getElementById('serie_id_serie').value = serie ? serie.id_serie : '';
  document.getElementById('serie_id_evento').value = evento.id_evento;
  document.getElementById('serie_evento_titulo').value = `${evento.codigo} — ${evento.titulo}`;
  document.getElementById('modalSerieSesionesTitulo').innerHTML = `<i class="bi bi-calendar-range me-2"></i>${serie ? 'Editar serie de sesiones' : 'Nueva serie de sesiones'}`;
  document.getElementById('serie_boton_texto').textContent = serie ? 'Guardar y regenerar futuras' : 'Crear sesiones';
  ['serie_fecha_inicio', 'serie_fecha_fin'].forEach((id) => {
    document.getElementById(id).min = evento.fecha_inicio;
    document.getElementById(id).max = evento.fecha_fin;
  });
  document.getElementById('serie_titulo').value = serie ? serie.titulo : '';
  document.getElementById('serie_fecha_inicio').value = serie ? serie.fecha_inicio : evento.fecha_inicio;
  document.getElementById('serie_fecha_fin').value = serie ? serie.fecha_fin : evento.fecha_fin;
  document.getElementById('serie_hora_inicio').value = serie ? String(serie.hora_inicio).slice(0, 5) : '';
  document.getElementById('serie_hora_fin').value = serie ? String(serie.hora_fin).slice(0, 5) : '';
  document.getElementById('serie_lugar').value = serie ? (serie.lugar_especifico || '') : (evento.lugar || '');
  document.getElementById('serie_frecuencia').value = serie ? serie.frecuencia : 'SEMANAL';
  document.getElementById('serie_intervalo').value = serie ? serie.intervalo_recurrencia : 1;
  const dias = serie ? String(serie.dias_semana || '').split(',').filter(Boolean).map(Number) : [1, 2, 3, 4, 5];
  document.querySelectorAll('.serie-dia').forEach((campo) => { campo.checked = dias.includes(Number(campo.value)); });
  actualizarFormularioSerie();
}

function actualizarFormularioSerie() {
  const semanal = document.getElementById('serie_frecuencia').value === 'SEMANAL';
  document.getElementById('serie_dias_contenedor').classList.toggle('d-none', !semanal);
  document.getElementById('serie_intervalo_texto').textContent = semanal ? 'semana(s)' : 'día(s)';
  actualizarPrevisualizacionSerie();
}

function actualizarPrevisualizacionSerie() {
  const inicioTexto = document.getElementById('serie_fecha_inicio').value;
  const finTexto = document.getElementById('serie_fecha_fin').value;
  const destino = document.getElementById('serie_previsualizacion');
  if (!inicioTexto || !finTexto || inicioTexto > finTexto) {
    destino.textContent = 'Complete un periodo válido para calcular las sesiones.';
    return;
  }
  const frecuencia = document.getElementById('serie_frecuencia').value;
  const intervalo = Math.max(1, Number(document.getElementById('serie_intervalo').value) || 1);
  const dias = [...document.querySelectorAll('.serie-dia:checked')].map((campo) => Number(campo.value));
  if (frecuencia === 'SEMANAL' && !dias.length) {
    destino.textContent = 'Seleccione al menos un día de clase.';
    return;
  }
  const inicio = new Date(`${inicioTexto}T00:00:00`);
  const fin = new Date(`${finTexto}T00:00:00`);
  let cantidad = 0;
  for (let fecha = new Date(inicio); fecha <= fin; fecha.setDate(fecha.getDate() + 1)) {
    const diasTranscurridos = Math.floor((fecha - inicio) / 86400000);
    const diaSemana = fecha.getDay() === 0 ? 7 : fecha.getDay();
    const corresponde = frecuencia === 'DIARIA'
      ? diasTranscurridos % intervalo === 0
      : dias.includes(diaSemana) && Math.floor(diasTranscurridos / 7) % intervalo === 0;
    if (corresponde) cantidad++;
  }
  destino.textContent = cantidad > 366
    ? `Se generarían ${cantidad} sesiones. El límite permitido es 366.`
    : `Se crearán ${cantidad} sesiones individuales para registrar asistencia.`;
}

['serie_fecha_inicio', 'serie_fecha_fin', 'serie_frecuencia', 'serie_intervalo'].forEach((id) => {
  document.getElementById(id).addEventListener('change', actualizarFormularioSerie);
  document.getElementById(id).addEventListener('input', actualizarFormularioSerie);
});
document.querySelectorAll('.serie-dia').forEach((campo) => campo.addEventListener('change', actualizarPrevisualizacionSerie));

function prepararAsignacionExpositor(boton) {
  const evento = JSON.parse(boton.dataset.evento);
  document.getElementById('expositor_id_evento').value = evento.id_evento;
  document.getElementById('expositor_evento_titulo').value = `${evento.codigo} — ${evento.titulo}`;
  document.getElementById('expositor_usuario').value = '';
  expositorSeleccionadoAsignacion = null;
  document.getElementById('expositor_buscar').value = '';
  renderizarResultadosExpositor();
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

function prepararQrInscripcion(boton) {
  const url = boton.dataset.urlInscripcion;
  const titulo = boton.dataset.eventoTitulo;
  const urlQr = 'index.php?action=generar_qr&contenido=' + encodeURIComponent(url);
  document.getElementById('qr-inscripcion-evento').textContent = titulo;
  document.getElementById('qr-inscripcion-url').value = url;
  document.getElementById('qr-inscripcion-imagen').src = urlQr;
  document.getElementById('qr-inscripcion-abrir').href = url;
  document.getElementById('qr-inscripcion-descargar').href = urlQr;
}

function copiarUrlInscripcionQr() {
  const campo = document.getElementById('qr-inscripcion-url');
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(campo.value);
  } else {
    campo.select();
    document.execCommand('copy');
  }
}

function actualizarPrevisualizacionCodigoEvento() {
  const tipo = document.getElementById('nuevo_tipo_evento');
  const area = document.getElementById('nuevo_categoria_evento');
  const destino = document.getElementById('nuevo_codigo_previsualizacion');
  if (!tipo || !area || !destino) return;

  const abreviaturaTipo = tipo.selectedOptions[0]?.dataset.abreviatura || '';
  const abreviaturaArea = area.selectedOptions[0]?.dataset.abreviatura || '';
  destino.value = abreviaturaTipo && abreviaturaArea
    ? `EVT-${abreviaturaTipo}-${abreviaturaArea}-######`
    : 'EVT-…-…-######';
}

document.getElementById('nuevo_tipo_evento')?.addEventListener('change', actualizarPrevisualizacionCodigoEvento);
document.getElementById('nuevo_categoria_evento')?.addEventListener('change', actualizarPrevisualizacionCodigoEvento);
document.getElementById('modalNuevoEvento')?.addEventListener('shown.bs.modal', actualizarPrevisualizacionCodigoEvento);
actualizarPrevisualizacionCodigoEvento();
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
