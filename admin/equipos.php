<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/subir_imagen.php';
requiereSesion();

$pdo = conexionBD();

/* ---------------- Acciones (POST) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'guardar') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $eyebrow = trim($_POST['eyebrow'] ?? '');
    $descripcionCorta = trim($_POST['descripcion_corta'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = ($_POST['tipo'] ?? 'nuevo') === 'usado' ? 'usado' : 'nuevo';
    $icono = trim($_POST['icono'] ?? 'box');
    $whatsapp = trim($_POST['whatsapp_texto'] ?? '');
    $visible = isset($_POST['visible']) ? 1 : 0;

    if ($nombre === '' || $descripcionCorta === '') {
      ponerMensaje('El nombre y la descripción corta son obligatorios.', 'error');
      header('Location: /admin/equipos.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    $subida = procesarImagenSubida('imagen_principal', 'equipos');
    if (!$subida['ok']) {
      ponerMensaje($subida['error'], 'error');
      header('Location: /admin/equipos.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    if ($id) {
      $sql = 'UPDATE equipos SET nombre=?, eyebrow=?, descripcion_corta=?, descripcion=?, tipo=?, icono=?, whatsapp_texto=?, visible=?';
      $params = [$nombre, $eyebrow, $descripcionCorta, $descripcion, $tipo, $icono, $whatsapp, $visible];
      if ($subida['archivo']) {
        $actual = obtenerEquipoPorId($id);
        eliminarArchivoSubido($actual['imagen_principal'] ?? null, 'equipos');
        $sql .= ', imagen_principal=?, imagen_ancho=?, imagen_alto=?';
        array_push($params, $subida['archivo'], $subida['ancho'], $subida['alto']);
      }
      $sql .= ' WHERE id=?';
      $params[] = $id;
      $pdo->prepare($sql)->execute($params);
    } else {
      $orden = (int)$pdo->query('SELECT COALESCE(MAX(orden),-1)+1 FROM equipos')->fetchColumn();
      $slug = slugificar($nombre);
      $pdo->prepare('INSERT INTO equipos (slug, nombre, eyebrow, descripcion_corta, descripcion, tipo, icono, imagen_principal, imagen_ancho, imagen_alto, whatsapp_texto, visible, orden) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$slug, $nombre, $eyebrow, $descripcionCorta, $descripcion, $tipo, $icono, $subida['archivo'], $subida['ancho'], $subida['alto'], $whatsapp, $visible, $orden]);
      $id = (int)$pdo->lastInsertId();
    }

    // Imágenes adicionales de la galería.
    if (!empty($_FILES['galeria']['name'][0])) {
      $stOrden = $pdo->prepare('SELECT COALESCE(MAX(orden),-1)+1 FROM equipo_imagenes WHERE equipo_id=?');
      $stOrden->execute([$id]);
      $ordenSiguiente = (int)$stOrden->fetchColumn();
      $insertarImg = $pdo->prepare('INSERT INTO equipo_imagenes (equipo_id, ruta, orden) VALUES (?,?,?)');
      $n = count($_FILES['galeria']['name']);
      for ($i = 0; $i < $n; $i++) {
        if ($_FILES['galeria']['error'][$i] !== UPLOAD_ERR_OK) continue;
        $_FILES['galeria_tmp'] = [
          'name' => $_FILES['galeria']['name'][$i], 'type' => $_FILES['galeria']['type'][$i],
          'tmp_name' => $_FILES['galeria']['tmp_name'][$i], 'error' => $_FILES['galeria']['error'][$i], 'size' => $_FILES['galeria']['size'][$i],
        ];
        $r = procesarImagenSubida('galeria_tmp', 'equipos');
        if ($r['ok'] && $r['archivo']) {
          $insertarImg->execute([$id, $r['archivo'], $ordenSiguiente]);
          $ordenSiguiente++;
        }
      }
    }

    ponerMensaje('Equipo guardado correctamente.');
    header('Location: /admin/equipos.php');
    exit;
  }

  if ($accion === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);
    $item = obtenerEquipoPorId($id);
    if ($item) {
      eliminarArchivoSubido($item['imagen_principal'], 'equipos');
      foreach (obtenerImagenesEquipo($id) as $img) eliminarArchivoSubido($img['ruta'], 'equipos');
      $pdo->prepare('DELETE FROM equipos WHERE id=?')->execute([$id]);
      ponerMensaje('Equipo eliminado.');
    }
    header('Location: /admin/equipos.php');
    exit;
  }

  if ($accion === 'eliminar_imagen_galeria') {
    $imgId = (int)($_POST['imagen_id'] ?? 0);
    $equipoId = (int)($_POST['id'] ?? 0);
    $st = $pdo->prepare('SELECT * FROM equipo_imagenes WHERE id=?');
    $st->execute([$imgId]);
    $img = $st->fetch();
    if ($img) {
      eliminarArchivoSubido($img['ruta'], 'equipos');
      $pdo->prepare('DELETE FROM equipo_imagenes WHERE id=?')->execute([$imgId]);
    }
    header('Location: /admin/equipos.php?editar=' . $equipoId);
    exit;
  }

  if ($accion === 'mover') {
    $id = (int)($_POST['id'] ?? 0);
    $direccion = $_POST['direccion'] ?? '';
    $actual = obtenerEquipoPorId($id);
    if ($actual) {
      $cmp = $direccion === 'arriba' ? '<' : '>';
      $orden = $direccion === 'arriba' ? 'DESC' : 'ASC';
      $st = $pdo->prepare("SELECT * FROM equipos WHERE orden {$cmp} ? ORDER BY orden {$orden} LIMIT 1");
      $st->execute([$actual['orden']]);
      $vecino = $st->fetch();
      if ($vecino) {
        $pdo->prepare('UPDATE equipos SET orden=? WHERE id=?')->execute([$vecino['orden'], $actual['id']]);
        $pdo->prepare('UPDATE equipos SET orden=? WHERE id=?')->execute([$actual['orden'], $vecino['id']]);
      }
    }
    header('Location: /admin/equipos.php');
    exit;
  }

  if ($accion === 'visibilidad') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE equipos SET visible = 1 - visible WHERE id=?')->execute([$id]);
    header('Location: /admin/equipos.php');
    exit;
  }
}

function slugificar(string $texto): string {
  $t = strtolower($texto);
  $t = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $t);
  $t = preg_replace('/[^a-z0-9]+/', '-', $t);
  return trim($t, '-') . '-' . substr(md5(uniqid()), 0, 5);
}

/* ---------------- Vista ---------------- */
$editando = null;
if (isset($_GET['editar'])) $editando = obtenerEquipoPorId((int)$_GET['editar']);
$mostrarFormulario = $editando || isset($_GET['nuevo']);

$iconosDisponibles = ['roll','blade','truck','wrench','eye','target','drop','spray','box','tray','gauge','spark'];

panelAbrir($mostrarFormulario ? ($editando ? 'Editar equipo' : 'Nuevo equipo') : 'Equipos', 'equipos');

if ($mostrarFormulario):
  $imagenesGaleria = $editando ? obtenerImagenesEquipo($editando['id']) : [];
  ?>
  <div class="panel__encabezado">
    <h1><?= $editando ? 'Editar equipo' : 'Nuevo equipo' ?></h1>
    <a class="btn btn--fantasma" href="/admin/equipos.php"><?= icono('chevron') ?>Volver a la lista</a>
  </div>
  <div class="tarjeta">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="guardar">
      <input type="hidden" name="id" value="<?= (int)($editando['id'] ?? 0) ?>">

      <div class="form__seccion">
        <h2 class="form__seccion-titulo"><span class="form__seccion-icono"><?= icono('target') ?></span>Información general</h2>
        <div class="form__campo">
          <label for="nombre">Nombre del equipo *</label>
          <input id="nombre" name="nombre" type="text" required value="<?= esc($editando['nombre'] ?? '') ?>">
        </div>
        <div class="form__fila">
          <div class="form__campo">
            <label for="tipo">Condición</label>
            <select id="tipo" name="tipo">
              <option value="nuevo" <?= ($editando['tipo'] ?? '') === 'nuevo' ? 'selected' : '' ?>>Nuevo</option>
              <option value="usado" <?= ($editando['tipo'] ?? '') === 'usado' ? 'selected' : '' ?>>Usado</option>
            </select>
          </div>
          <div class="form__campo">
            <label for="eyebrow">Etiqueta (arriba del título)</label>
            <input id="eyebrow" name="eyebrow" type="text" placeholder="Ej. Fabricación propia" value="<?= esc($editando['eyebrow'] ?? '') ?>">
          </div>
        </div>
        <div class="form__campo">
          <label for="icono">Ícono de respaldo</label>
          <select id="icono" name="icono">
            <?php foreach ($iconosDisponibles as $ic): ?>
            <option value="<?= $ic ?>" <?= ($editando['icono'] ?? '') === $ic ? 'selected' : '' ?>><?= ucfirst($ic) ?></option>
            <?php endforeach; ?>
          </select>
          <p class="form__ayuda">Se usa solo si no hay foto principal.</p>
        </div>
      </div>

      <div class="form__seccion">
        <h2 class="form__seccion-titulo"><span class="form__seccion-icono"><?= icono('leaf') ?></span>Descripción</h2>
        <div class="form__campo">
          <label for="descripcion_corta">Descripción corta *</label>
          <textarea id="descripcion_corta" name="descripcion_corta" required><?= esc($editando['descripcion_corta'] ?? '') ?></textarea>
          <p class="form__ayuda">Aparece como primer párrafo, más destacado.</p>
        </div>
        <div class="form__campo">
          <label for="descripcion">Descripción adicional</label>
          <textarea id="descripcion" name="descripcion"><?= esc($editando['descripcion'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="form__seccion">
        <h2 class="form__seccion-titulo"><span class="form__seccion-icono"><?= icono('imagen') ?></span>Imágenes</h2>
        <div class="form__campo">
          <label>Imagen principal</label>
          <?php if (!empty($editando['imagen_principal'])): ?>
          <img class="form__vista-imagen" src="/uploads/equipos/<?= esc($editando['imagen_principal']) ?>" alt="">
          <?php endif; ?>
          <input name="imagen_principal" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
          <p class="form__ayuda">JPG, PNG, WEBP o GIF, máximo 4 MB. Deje vacío para conservar la actual.</p>
        </div>

        <?php if ($editando && $imagenesGaleria): ?>
        <div class="form__campo">
          <label>Imágenes adicionales</label>
          <div style="display:flex;gap:.6rem;flex-wrap:wrap">
            <?php foreach ($imagenesGaleria as $img): ?>
            <div style="text-align:center">
              <img class="tabla-img" style="width:5rem;height:5rem" src="/uploads/equipos/<?= esc($img['ruta']) ?>" alt="">
              <form method="post" onsubmit="return confirm('¿Quitar esta imagen de la galería?')" style="margin-top:.3rem">
                <input type="hidden" name="accion" value="eliminar_imagen_galeria">
                <input type="hidden" name="id" value="<?= $editando['id'] ?>">
                <input type="hidden" name="imagen_id" value="<?= $img['id'] ?>">
                <button class="btn btn--peligro" type="submit" style="padding:.2rem .5rem;font-size:.72rem">Quitar</button>
              </form>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        <div class="form__campo">
          <label>Agregar imágenes a la galería</label>
          <input name="galeria[]" type="file" accept="image/png,image/jpeg,image/webp,image/gif" multiple>
          <p class="form__ayuda">Puede seleccionar varias a la vez. Se guardan al presionar "Guardar equipo".</p>
        </div>
      </div>

      <div class="form__seccion">
        <h2 class="form__seccion-titulo"><span class="form__seccion-icono"><?= icono('wa') ?></span>Contacto y visibilidad</h2>
        <div class="form__campo">
          <label for="whatsapp_texto">Mensaje de WhatsApp al cotizar</label>
          <input id="whatsapp_texto" name="whatsapp_texto" type="text" value="<?= esc($editando['whatsapp_texto'] ?? ('Hola Flexuministros, quisiera información sobre ' . ($editando['nombre'] ?? 'este equipo') . '.')) ?>">
        </div>
        <div class="form__campo">
          <label class="checkbox"><input type="checkbox" name="visible" <?= ($editando['visible'] ?? 1) ? 'checked' : '' ?>> Mostrar este equipo en el sitio</label>
        </div>
      </div>

      <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar equipo</button>
    </form>
  </div>
<?php else: ?>
  <div class="panel__encabezado">
    <h1>Equipos</h1>
    <a class="btn btn--primario" href="/admin/equipos.php?nuevo=1"><?= icono('check') ?>Agregar equipo</a>
  </div>
  <div class="tarjeta">
    <div class="tabla-scroll">
    <table>
      <thead><tr><th></th><th>Nombre</th><th>Condición</th><th>Visible</th><th>Orden</th><th></th></tr></thead>
      <tbody>
        <?php foreach (obtenerEquipos(false) as $e): ?>
        <tr>
          <td class="tabla-miniatura"><?php if ($e['imagen_principal']): ?><img class="tabla-img" src="/uploads/equipos/<?= esc($e['imagen_principal']) ?>" alt=""><?php else: ?><span class="tabla-img" style="display:grid;place-items:center;color:#B7B2A6"><?= icono($e['icono'] ?: 'box') ?></span><?php endif; ?></td>
          <td data-label="Nombre"><strong><?= esc($e['nombre']) ?></strong><br><span style="color:#77736B;font-size:.8rem"><?= esc($e['eyebrow']) ?></span></td>
          <td data-label="Condición"><?= $e['tipo'] === 'nuevo' ? 'Nuevo' : 'Usado' ?></td>
          <td data-label="Visible">
            <form method="post" style="display:inline">
              <input type="hidden" name="accion" value="visibilidad"><input type="hidden" name="id" value="<?= $e['id'] ?>">
              <button class="btn" style="padding:.2rem;background:none;border:0" type="submit" title="Mostrar/ocultar">
                <span class="etiqueta <?= $e['visible'] ? 'etiqueta--si' : 'etiqueta--no' ?>"><?= $e['visible'] ? 'Visible' : 'Oculto' ?></span>
              </button>
            </form>
          </td>
          <td data-label="Orden">
            <form method="post" style="display:inline"><input type="hidden" name="accion" value="mover"><input type="hidden" name="id" value="<?= $e['id'] ?>"><input type="hidden" name="direccion" value="arriba"><button class="btn btn--fantasma" style="padding:.3rem .5rem" type="submit" title="Subir">↑</button></form>
            <form method="post" style="display:inline"><input type="hidden" name="accion" value="mover"><input type="hidden" name="id" value="<?= $e['id'] ?>"><input type="hidden" name="direccion" value="abajo"><button class="btn btn--fantasma" style="padding:.3rem .5rem" type="submit" title="Bajar">↓</button></form>
          </td>
          <td class="tabla-acciones">
            <a class="btn btn--fantasma" href="/admin/equipos.php?editar=<?= $e['id'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar «<?= esc(addslashes($e['nombre'])) ?>»? Esta acción no se puede deshacer.')" style="display:inline">
              <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $e['id'] ?>">
              <button class="btn btn--peligro" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
<?php endif;
panelCerrar();
