<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/subir_imagen.php';
requiereSesion();

$pdo = conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  if ($accion === 'guardar') {
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoriaId = trim($_POST['categoria_id'] ?? '');
    $marcaId = $_POST['marca_id'] !== '' ? (int)$_POST['marca_id'] : null;
    $visible = isset($_POST['visible']) ? 1 : 0;

    if ($nombre === '' || $categoriaId === '') {
      ponerMensaje('El nombre y la categoría son obligatorios.', 'error');
      header('Location: /admin/productos.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    $subida = procesarImagenSubida('imagen', 'productos');
    if (!$subida['ok']) {
      ponerMensaje($subida['error'], 'error');
      header('Location: /admin/productos.php?' . ($id ? "editar={$id}" : 'nuevo=1'));
      exit;
    }

    if ($id) {
      $sql = 'UPDATE productos SET nombre=?, descripcion=?, categoria_id=?, marca_id=?, visible=?';
      $params = [$nombre, $descripcion, $categoriaId, $marcaId, $visible];
      if ($subida['archivo']) {
        $actual = obtenerProductoPorId($id);
        eliminarArchivoSubido($actual['imagen'] ?? null, 'productos');
        $sql .= ', imagen=?, imagen_ancho=?, imagen_alto=?';
        array_push($params, $subida['archivo'], $subida['ancho'], $subida['alto']);
      }
      $sql .= ' WHERE id=?';
      $params[] = $id;
      $pdo->prepare($sql)->execute($params);
    } else {
      $orden = (int)$pdo->query('SELECT COALESCE(MAX(orden),-1)+1 FROM productos')->fetchColumn();
      $slug = slugificarProducto($nombre);
      $pdo->prepare('INSERT INTO productos (nombre, descripcion, slug, categoria_id, marca_id, imagen, imagen_ancho, imagen_alto, visible, orden) VALUES (?,?,?,?,?,?,?,?,?,?)')
        ->execute([$nombre, $descripcion, $slug, $categoriaId, $marcaId, $subida['archivo'], $subida['ancho'], $subida['alto'], $visible, $orden]);
    }

    ponerMensaje('Producto guardado correctamente.');
    header('Location: /admin/productos.php');
    exit;
  }

  if ($accion === 'eliminar') {
    $id = (int)($_POST['id'] ?? 0);
    $item = obtenerProductoPorId($id);
    if ($item) {
      eliminarArchivoSubido($item['imagen'], 'productos');
      $pdo->prepare('DELETE FROM productos WHERE id=?')->execute([$id]);
      ponerMensaje('Producto eliminado.');
    }
    header('Location: /admin/productos.php');
    exit;
  }

  if ($accion === 'visibilidad') {
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare('UPDATE productos SET visible = 1 - visible WHERE id=?')->execute([$id]);
    header('Location: /admin/productos.php' . (isset($_POST['pagina']) ? '?pagina=' . (int)$_POST['pagina'] : ''));
    exit;
  }
}

function slugificarProducto(string $texto): string {
  $t = strtolower($texto);
  $t = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $t);
  $t = preg_replace('/[^a-z0-9]+/', '-', $t);
  return trim($t, '-') . '-' . substr(md5(uniqid()), 0, 5);
}

/* ---------------- Vista ---------------- */
$editando = null;
if (isset($_GET['editar'])) $editando = obtenerProductoPorId((int)$_GET['editar']);
$mostrarFormulario = $editando || isset($_GET['nuevo']);
$categorias = obtenerCategorias();
$marcas = obtenerMarcas();

panelAbrir($mostrarFormulario ? ($editando ? 'Editar producto' : 'Nuevo producto') : 'Productos', 'productos');

if ($mostrarFormulario): ?>
  <div class="panel__encabezado">
    <h1><?= $editando ? 'Editar producto' : 'Nuevo producto' ?></h1>
    <a class="btn btn--fantasma" href="/admin/productos.php"><?= icono('chevron') ?>Volver a la lista</a>
  </div>
  <div class="tarjeta">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="accion" value="guardar">
      <input type="hidden" name="id" value="<?= (int)($editando['id'] ?? 0) ?>">

      <div class="form__campo">
        <label for="nombre">Nombre del producto *</label>
        <input id="nombre" name="nombre" type="text" required value="<?= esc($editando['nombre'] ?? '') ?>">
      </div>
      <div class="form__campo">
        <label for="descripcion">Descripción</label>
        <input id="descripcion" name="descripcion" type="text" placeholder="Ej. Aquaflex, Comco, Mark Andy y más" value="<?= esc($editando['descripcion'] ?? '') ?>">
      </div>

      <div class="form__fila">
        <div class="form__campo">
          <label for="categoria_id">Categoría *</label>
          <select id="categoria_id" name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
            <option value="<?= esc($cat['id']) ?>" <?= ($editando['categoria_id'] ?? '') === $cat['id'] ? 'selected' : '' ?>><?= esc($cat['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form__campo">
          <label for="marca_id">Marca</label>
          <select id="marca_id" name="marca_id">
            <option value="">— Sin marca —</option>
            <?php foreach ($marcas as $m): ?>
            <option value="<?= $m['id'] ?>" <?= (int)($editando['marca_id'] ?? 0) === (int)$m['id'] ? 'selected' : '' ?>><?= esc($m['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form__campo">
        <label>Imagen del producto</label>
        <?php if (!empty($editando['imagen'])): ?>
        <img class="form__vista-imagen" src="/uploads/productos/<?= esc($editando['imagen']) ?>" alt="">
        <?php endif; ?>
        <input name="imagen" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
        <p class="form__ayuda">JPG, PNG, WEBP o GIF, máximo 4 MB. Deje vacío para conservar la actual.</p>
      </div>

      <div class="form__campo">
        <label class="checkbox"><input type="checkbox" name="visible" <?= ($editando['visible'] ?? 1) ? 'checked' : '' ?>> Mostrar este producto en el catálogo</label>
      </div>

      <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar producto</button>
    </form>
  </div>
<?php else:
  $pagina = max(1, (int)($_GET['pagina'] ?? 1));
  $porPagina = 25;
  $total = (int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
  $totalPaginas = max(1, (int)ceil($total / $porPagina));
  $pagina = min($pagina, $totalPaginas);
  $offset = ($pagina - 1) * $porPagina;
  $filtroCategoria = $_GET['cat'] ?? '';
  $sql = 'SELECT p.*, c.nombre AS categoria_nombre, m.nombre AS marca_nombre FROM productos p
          LEFT JOIN categorias c ON c.id = p.categoria_id LEFT JOIN marcas m ON m.id = p.marca_id';
  $params = [];
  if ($filtroCategoria) { $sql .= ' WHERE p.categoria_id = ?'; $params[] = $filtroCategoria; }
  $sql .= ' ORDER BY p.orden ASC, p.id ASC LIMIT ' . $porPagina . ' OFFSET ' . $offset;
  $st = $pdo->prepare($sql);
  $st->execute($params);
  $productos = $st->fetchAll();
  ?>
  <div class="panel__encabezado">
    <h1>Productos <span style="color:#77736B;font-weight:600;font-size:1rem">(<?= $total ?>)</span></h1>
    <a class="btn btn--primario" href="/admin/productos.php?nuevo=1"><?= icono('check') ?>Agregar producto</a>
  </div>

  <div style="margin-bottom:1rem;display:flex;gap:.5rem;flex-wrap:wrap">
    <a class="btn <?= $filtroCategoria === '' ? 'btn--primario' : 'btn--fantasma' ?>" href="/admin/productos.php" style="padding:.4rem .8rem;font-size:.8rem">Todas</a>
    <?php foreach ($categorias as $cat): ?>
    <a class="btn <?= $filtroCategoria === $cat['id'] ? 'btn--primario' : 'btn--fantasma' ?>" href="/admin/productos.php?cat=<?= esc($cat['id']) ?>" style="padding:.4rem .8rem;font-size:.8rem"><?= esc($cat['nombre']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="tarjeta">
    <div class="tabla-scroll">
    <table>
      <thead><tr><th></th><th>Nombre</th><th>Categoría</th><th>Marca</th><th>Visible</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($productos as $p): ?>
        <tr>
          <td><?php if ($p['imagen']): ?><img class="tabla-img" src="/uploads/productos/<?= esc($p['imagen']) ?>" alt=""><?php else: ?><span class="tabla-img" style="display:grid;place-items:center;color:#B7B2A6"><?= icono('box') ?></span><?php endif; ?></td>
          <td><strong><?= esc($p['nombre']) ?></strong><br><span style="color:#77736B;font-size:.8rem"><?= esc($p['descripcion']) ?></span></td>
          <td><?= esc($p['categoria_nombre'] ?? '') ?></td>
          <td><?= esc($p['marca_nombre'] ?? '—') ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="accion" value="visibilidad"><input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="pagina" value="<?= $pagina ?>">
              <button style="padding:.2rem;background:none;border:0" type="submit"><span class="etiqueta <?= $p['visible'] ? 'etiqueta--si' : 'etiqueta--no' ?>"><?= $p['visible'] ? 'Visible' : 'Oculto' ?></span></button>
            </form>
          </td>
          <td class="tabla-acciones">
            <a class="btn btn--fantasma" href="/admin/productos.php?editar=<?= $p['id'] ?>">Editar</a>
            <form method="post" onsubmit="return confirm('¿Eliminar «<?= esc(addslashes($p['nombre'])) ?>»? Esta acción no se puede deshacer.')" style="display:inline">
              <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button class="btn btn--peligro" type="submit">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <?php if ($totalPaginas > 1): ?>
    <div style="display:flex;gap:.4rem;justify-content:center;margin-top:1.25rem;flex-wrap:wrap">
      <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
      <a class="btn <?= $i === $pagina ? 'btn--primario' : 'btn--fantasma' ?>" style="padding:.35rem .7rem;font-size:.8rem" href="/admin/productos.php?pagina=<?= $i ?><?= $filtroCategoria ? '&cat=' . esc($filtroCategoria) : '' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
<?php endif;
panelCerrar();
