<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/subir_imagen.php';
requiereSesion();

$pdo = conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'guardar') {
    $id = (int)($_POST['id'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $eyebrow = trim($_POST['eyebrow'] ?? '');
    $textoPrincipal = trim($_POST['texto_principal'] ?? '');
    $textoSecundario = trim($_POST['texto_secundario'] ?? '');
    $textoExtra = trim($_POST['texto_extra'] ?? '');
    $whatsapp = trim($_POST['whatsapp_texto'] ?? '');
    $visible = isset($_POST['visible']) ? 1 : 0;

    if ($titulo === '' || $textoPrincipal === '') {
      ponerMensaje('El título y el texto principal son obligatorios.', 'error');
      header('Location: /admin/servicios.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    $subida = procesarImagenSubida('imagen', 'servicios');
    if (!$subida['ok']) {
      ponerMensaje($subida['error'], 'error');
      header('Location: /admin/servicios.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    if ($id) {
      $sql = 'UPDATE servicios SET titulo=?, eyebrow=?, texto_principal=?, texto_secundario=?, texto_extra=?, whatsapp_texto=?, visible=?';
      $params = [$titulo, $eyebrow, $textoPrincipal, $textoSecundario, $textoExtra ?: null, $whatsapp, $visible];
      if ($subida['archivo']) {
        $actual = obtenerServicioPorId($id);
        eliminarArchivoSubido($actual['imagen'] ?? null, 'servicios');
        $sql .= ', imagen=?, imagen_ancho=?, imagen_alto=?';
        array_push($params, $subida['archivo'], $subida['ancho'], $subida['alto']);
      }
      $sql .= ' WHERE id=?';
      $params[] = $id;
      $pdo->prepare($sql)->execute($params);
    } else {
      $orden = (int)$pdo->query('SELECT COALESCE(MAX(orden),-1)+1 FROM servicios')->fetchColumn();
      $slug = slugificarServicio($titulo);
      $pdo->prepare('INSERT INTO servicios (slug, titulo, eyebrow, texto_principal, texto_secundario, texto_extra, icono, imagen, imagen_ancho, imagen_alto, whatsapp_texto, visible, orden) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([$slug, $titulo, $eyebrow, $textoPrincipal, $textoSecundario, $textoExtra ?: null, 'box', $subida['archivo'], $subida['ancho'], $subida['alto'], $whatsapp, $visible, $orden]);
    }

    ponerMensaje('Servicio guardado correctamente.');
    header('Location: /admin/servicios.php');
    exit;
  }

  if ($accion === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);
    $item = obtenerServicioPorId($id);
    if ($item) {
      eliminarArchivoSubido($item['imagen'], 'servicios');
      $pdo->prepare('DELETE FROM servicios WHERE id=?')->execute([$id]);
      ponerMensaje('Servicio eliminado.');
    }
    header('Location: /admin/servicios.php');
    exit;
  }

  if ($accion === 'visibilidad') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE servicios SET visible = 1 - visible WHERE id=?')->execute([$id]);
    header('Location: /admin/servicios.php');
    exit;
  }

  if ($accion === 'mover') {
    $id = (int)($_POST['id'] ?? 0);
    $direccion = $_POST['direccion'] ?? '';
    $actual = obtenerServicioPorId($id);
    if ($actual) {
      $cmp = $direccion === 'arriba' ? '<' : '>';
      $orden = $direccion === 'arriba' ? 'DESC' : 'ASC';
      $st = $pdo->prepare("SELECT * FROM servicios WHERE orden {$cmp} ? ORDER BY orden {$orden} LIMIT 1");
      $st->execute([$actual['orden']]);
      $vecino = $st->fetch();
      if ($vecino) {
        $pdo->prepare('UPDATE servicios SET orden=? WHERE id=?')->execute([$vecino['orden'], $actual['id']]);
        $pdo->prepare('UPDATE servicios SET orden=? WHERE id=?')->execute([$actual['orden'], $vecino['id']]);
      }
    }
    header('Location: /admin/servicios.php');
    exit;
  }

  /* ---- Marcas técnicas ---- */
  if ($accion === 'guardar_marca') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $visible = isset($_POST['visible']) ? 1 : 0;
    $subida = procesarImagenSubida('logo', 'marcas_servicio');
    if ($nombre === '') {
      ponerMensaje('El nombre de la marca es obligatorio.', 'error');
    } elseif (!$subida['ok']) {
      ponerMensaje($subida['error'], 'error');
    } else {
      if ($id) {
        $sql = 'UPDATE marcas_servicio SET nombre=?, descripcion=?, visible=?';
        $params = [$nombre, $descripcion, $visible];
        if ($subida['archivo']) {
          $st = $pdo->prepare('SELECT logo FROM marcas_servicio WHERE id=?'); $st->execute([$id]); $actualLogo = $st->fetchColumn();
          eliminarArchivoSubido($actualLogo, 'marcas_servicio');
          $sql .= ', logo=?'; $params[] = $subida['archivo'];
        }
        $sql .= ' WHERE id=?'; $params[] = $id;
        $pdo->prepare($sql)->execute($params);
      } else {
        $orden = (int)$pdo->query('SELECT COALESCE(MAX(orden),-1)+1 FROM marcas_servicio')->fetchColumn();
        $pdo->prepare('INSERT INTO marcas_servicio (nombre, logo, descripcion, orden, visible) VALUES (?,?,?,?,?)')
          ->execute([$nombre, $subida['archivo'] ?? '', $descripcion, $orden, $visible]);
      }
      ponerMensaje('Marca guardada correctamente.');
    }
    header('Location: /admin/servicios.php?vista=marcas');
    exit;
  }

  if ($accion === 'visibilidad_marca') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE marcas_servicio SET visible = 1 - visible WHERE id=?')->execute([$id]);
    header('Location: /admin/servicios.php?vista=marcas');
    exit;
  }

  if ($accion === 'eliminar_marca') {
    $id = (int)($_POST['id'] ?? 0);
    $st = $pdo->prepare('SELECT logo FROM marcas_servicio WHERE id=?'); $st->execute([$id]); $logo = $st->fetchColumn();
    if ($logo) eliminarArchivoSubido($logo, 'marcas_servicio');
    $pdo->prepare('DELETE FROM marcas_servicio WHERE id=?')->execute([$id]);
    ponerMensaje('Marca eliminada.');
    header('Location: /admin/servicios.php?vista=marcas');
    exit;
  }
}

function slugificarServicio(string $texto): string {
  $t = strtolower($texto);
  $t = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $t);
  $t = preg_replace('/[^a-z0-9]+/', '-', $t);
  return trim($t, '-') . '-' . substr(md5(uniqid()), 0, 5);
}

/* ---------------- Vista ---------------- */
$vista = $_GET['vista'] ?? 'servicios';
$editando = null;
if (isset($_GET['editar'])) $editando = obtenerServicioPorId((int)$_GET['editar']);
$mostrarFormulario = $editando || isset($_GET['nuevo']);
$editandoMarca = null;
if (isset($_GET['editar_marca'])) {
  $st = $pdo->prepare('SELECT * FROM marcas_servicio WHERE id=?'); $st->execute([(int)$_GET['editar_marca']]); $editandoMarca = $st->fetch() ?: null;
}
$mostrarFormularioMarca = $editandoMarca || isset($_GET['nueva_marca']);

$titulo = 'Servicios';
if ($vista === 'marcas') $titulo = 'Marcas técnicas';
if ($mostrarFormulario) $titulo = $editando ? 'Editar servicio' : 'Nuevo servicio';
if ($mostrarFormularioMarca) $titulo = $editandoMarca ? 'Editar marca' : 'Nueva marca';

panelAbrir($titulo, 'servicios');
?>
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem">
  <a class="btn <?= $vista !== 'marcas' ? 'btn--primario' : 'btn--fantasma' ?>" href="/admin/servicios.php" style="padding:.4rem .9rem;font-size:.82rem">Servicios</a>
  <a class="btn <?= $vista === 'marcas' ? 'btn--primario' : 'btn--fantasma' ?>" href="/admin/servicios.php?vista=marcas" style="padding:.4rem .9rem;font-size:.82rem">Marcas técnicas</a>
</div>

<?php if ($vista === 'marcas'): ?>

  <?php if ($mostrarFormularioMarca): ?>
  <div class="panel__encabezado">
    <h1><?= $editandoMarca ? 'Editar marca' : 'Nueva marca' ?></h1>
    <a class="btn btn--fantasma" href="/admin/servicios.php?vista=marcas"><?= icono('chevron') ?>Volver</a>
  </div>
  <div class="tarjeta">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="guardar_marca">
      <input type="hidden" name="id" value="<?= (int)($editandoMarca['id'] ?? 0) ?>">
      <div class="form__campo"><label for="nombre">Nombre de la marca *</label><input id="nombre" name="nombre" type="text" required value="<?= esc($editandoMarca['nombre'] ?? '') ?>"></div>
      <div class="form__campo"><label for="descripcion">Descripción corta</label><input id="descripcion" name="descripcion" type="text" value="<?= esc($editandoMarca['descripcion'] ?? '') ?>"></div>
      <div class="form__campo">
        <label>Logo</label>
        <?php if (!empty($editandoMarca['logo'])): ?><img class="form__vista-imagen" src="/uploads/marcas_servicio/<?= esc($editandoMarca['logo']) ?>" alt=""><?php endif; ?>
        <input name="logo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
        <p class="form__ayuda">Idealmente fondo transparente o blanco.</p>
      </div>
      <div class="form__campo"><label class="checkbox"><input type="checkbox" name="visible" <?= ($editandoMarca['visible'] ?? 1) ? 'checked' : '' ?>> Mostrar en el sitio</label></div>
      <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar marca</button>
    </form>
  </div>
  <?php else: ?>
  <div class="panel__encabezado">
    <h1>Marcas técnicas</h1>
    <a class="btn btn--primario" href="/admin/servicios.php?vista=marcas&nueva_marca=1"><?= icono('check') ?>Agregar marca</a>
  </div>
  <p style="color:#66625B;margin-top:-1rem">Son las tarjetas de distribuidores (Alphasonics, Aalberts, BDTECH…) que aparecen al final de la página de Servicios.</p>
  <div class="tarjeta">
    <div class="tabla-scroll">
    <table>
      <thead><tr><th></th><th>Nombre</th><th>Descripción</th><th>Visible</th><th></th></tr></thead>
      <tbody>
        <?php $stM = $pdo->query('SELECT * FROM marcas_servicio ORDER BY orden ASC'); foreach ($stM->fetchAll() as $m): ?>
        <tr>
          <td><img class="tabla-img" src="/uploads/marcas_servicio/<?= esc($m['logo']) ?>" alt="" style="object-fit:contain;background:#fff;border:1px solid #EFEDE8"></td>
          <td><strong><?= esc($m['nombre']) ?></strong></td>
          <td style="color:#77736B;font-size:.85rem"><?= esc($m['descripcion']) ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="accion" value="visibilidad_marca"><input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button style="padding:.2rem;background:none;border:0" type="submit"><span class="etiqueta <?= $m['visible'] ? 'etiqueta--si' : 'etiqueta--no' ?>"><?= $m['visible'] ? 'Visible' : 'Oculto' ?></span></button>
            </form>
          </td>
          <td class="tabla-acciones">
            <a class="btn btn--fantasma" href="/admin/servicios.php?vista=marcas&editar_marca=<?= $m['id'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar la marca «<?= esc(addslashes($m['nombre'])) ?>»?')" style="display:inline">
              <input type="hidden" name="accion" value="eliminar_marca"><input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button class="btn btn--peligro" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
  <?php endif; ?>

<?php elseif ($mostrarFormulario): ?>
  <div class="panel__encabezado">
    <h1><?= $editando ? 'Editar servicio' : 'Nuevo servicio' ?></h1>
    <a class="btn btn--fantasma" href="/admin/servicios.php"><?= icono('chevron') ?>Volver a la lista</a>
  </div>
  <div class="tarjeta">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="guardar">
      <input type="hidden" name="id" value="<?= (int)($editando['id'] ?? 0) ?>">

      <div class="form__campo"><label for="titulo">Título del servicio *</label><input id="titulo" name="titulo" type="text" required value="<?= esc($editando['titulo'] ?? '') ?>"></div>
      <div class="form__campo"><label for="eyebrow">Etiqueta (arriba del título)</label><input id="eyebrow" name="eyebrow" type="text" value="<?= esc($editando['eyebrow'] ?? '') ?>"></div>
      <div class="form__campo"><label for="texto_principal">Texto principal *</label><textarea id="texto_principal" name="texto_principal" required><?= esc($editando['texto_principal'] ?? '') ?></textarea></div>
      <div class="form__campo"><label for="texto_secundario">Texto adicional</label><textarea id="texto_secundario" name="texto_secundario"><?= esc($editando['texto_secundario'] ?? '') ?></textarea></div>
      <div class="form__campo"><label for="texto_extra">Nota extra (opcional)</label><textarea id="texto_extra" name="texto_extra"><?= esc($editando['texto_extra'] ?? '') ?></textarea><p class="form__ayuda">Se muestra como un párrafo aparte, después del texto adicional.</p></div>
      <div class="form__campo"><label for="whatsapp_texto">Mensaje de WhatsApp al cotizar</label><input id="whatsapp_texto" name="whatsapp_texto" type="text" value="<?= esc($editando['whatsapp_texto'] ?? '') ?>"></div>

      <div class="form__campo">
        <label>Imagen</label>
        <?php if (!empty($editando['imagen'])): ?><img class="form__vista-imagen" src="/uploads/servicios/<?= esc($editando['imagen']) ?>" alt=""><?php endif; ?>
        <input name="imagen" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
        <p class="form__ayuda">JPG, PNG, WEBP o GIF, máximo 4 MB. Deje vacío para conservar la actual.</p>
      </div>
      <div class="form__campo"><label class="checkbox"><input type="checkbox" name="visible" <?= ($editando['visible'] ?? 1) ? 'checked' : '' ?>> Mostrar este servicio en el sitio</label></div>

      <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar servicio</button>
    </form>
  </div>
<?php else: ?>
  <div class="panel__encabezado">
    <h1>Servicios</h1>
    <a class="btn btn--primario" href="/admin/servicios.php?nuevo=1"><?= icono('check') ?>Agregar servicio</a>
  </div>
  <div class="tarjeta">
    <div class="tabla-scroll">
    <table>
      <thead><tr><th></th><th>Título</th><th>Visible</th><th>Orden</th><th></th></tr></thead>
      <tbody>
        <?php foreach (obtenerServicios(false) as $s): ?>
        <tr>
          <td><?php if ($s['imagen']): ?><img class="tabla-img" src="/uploads/servicios/<?= esc($s['imagen']) ?>" alt=""><?php else: ?><span class="tabla-img" style="display:grid;place-items:center;color:#B7B2A6"><?= icono($s['icono'] ?: 'box') ?></span><?php endif; ?></td>
          <td><strong><?= esc($s['titulo']) ?></strong><br><span style="color:#77736B;font-size:.8rem"><?= esc($s['eyebrow']) ?></span></td>
          <td>
            <form method="post" style="display:inline"><input type="hidden" name="accion" value="visibilidad"><input type="hidden" name="id" value="<?= $s['id'] ?>">
              <button style="padding:.2rem;background:none;border:0" type="submit"><span class="etiqueta <?= $s['visible'] ? 'etiqueta--si' : 'etiqueta--no' ?>"><?= $s['visible'] ? 'Visible' : 'Oculto' ?></span></button>
            </form>
          </td>
          <td>
            <form method="post" style="display:inline"><input type="hidden" name="accion" value="mover"><input type="hidden" name="id" value="<?= $s['id'] ?>"><input type="hidden" name="direccion" value="arriba"><button class="btn btn--fantasma" style="padding:.3rem .5rem" type="submit">↑</button></form>
            <form method="post" style="display:inline"><input type="hidden" name="accion" value="mover"><input type="hidden" name="id" value="<?= $s['id'] ?>"><input type="hidden" name="direccion" value="abajo"><button class="btn btn--fantasma" style="padding:.3rem .5rem" type="submit">↓</button></form>
          </td>
          <td class="tabla-acciones">
            <a class="btn btn--fantasma" href="/admin/servicios.php?editar=<?= $s['id'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar «<?= esc(addslashes($s['titulo'])) ?>»? Esta acción no se puede deshacer.')" style="display:inline">
              <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $s['id'] ?>">
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
