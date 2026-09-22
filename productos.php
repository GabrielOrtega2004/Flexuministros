<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;
$categorias = obtenerCategorias();
$productos = conexionBD()->query(
  'SELECT p.*, c.nombre AS categoria_nombre FROM productos p
   LEFT JOIN categorias c ON c.id = p.categoria_id
   WHERE p.visible = 1 ORDER BY p.orden ASC, p.id ASC'
)->fetchAll();
$totalProductos = count($productos);

$chips = [['todos', 'Todos']];
foreach ($categorias as $cat) $chips[] = [$cat['id'], $cat['nombre']];

ob_start();
?>
<section class="pagehero pagehero--catalog">
  <div class="pagehero__media" aria-hidden="true">
    <img src="/uploads/configuracion/<?= esc($c('productos_hero_imagen', 'catalogo-v3-hd.webp')) ?>" width="<?= (int)$c('productos_hero_ancho', '1559') ?>" height="<?= (int)$c('productos_hero_alto', '1009') ?>" alt="" fetchpriority="high" decoding="async">
  </div>
  <div class="pagehero__veil" aria-hidden="true"></div>
  <div class="container">
    <?= crumbs_html([['Inicio', '/'], ['Productos', '/productos.html']]) ?>
    <h1>Catálogo de suministros</h1>
    <p>Variedad de productos disponibles en diversas líneas para la industria de impresión flexográfica. Busque por nombre o filtre por categoría; cotice cualquier producto en un click.</p>
    <p class="pagehero__note">¿No encuentra lo que busca? <a href="<?= enlaceWhatsApp('Hola Flexuministros, no encontré en el catálogo lo que busco, ¿me pueden ayudar?') ?>" target="_blank" rel="noopener">Hable con un especialista</a>.</p>
  </div>
</section>

<div class="toolbar">
  <div class="container">
    <div class="search" id="busqueda-catalogo">
      <?= icono('search') ?>
      <label class="sr-only" for="buscador">Buscar productos en el catálogo</label>
      <input id="buscador" type="search" placeholder="Buscar: rasqueta, anilox, cinta, viscosidad…" autocomplete="off">
      <button class="search__clear" type="button" aria-label="Limpiar búsqueda"><?= icono('x') ?></button>
    </div>
    <div class="toolbar__filters" id="filtros-catalogo" role="group" aria-label="Filtrar por categoría">
      <ul class="chips">
        <?php foreach ($chips as $i => [$id, $etiqueta]): ?>
        <li><button class="chip" type="button" data-cat="<?= esc($id) ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><?= esc($etiqueta) ?></button></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <button class="toolbar__toggle" type="button" aria-expanded="true" aria-controls="filtros-catalogo" aria-label="Minimizar categorías" hidden><?= icono('chevron') ?></button>
  </div>
</div>

<section class="section section--tight">
  <div class="container">
    <h2 class="sr-only">Catálogo completo de productos</h2>
    <p class="result-count" id="conteo" role="status"><b><?= $totalProductos ?></b> productos en catálogo</p>
    <div class="products" id="catalogo">
      <?php foreach ($productos as $i => $p) echo tarjeta_producto($p, $i < 8, $i); ?>
    </div>
    <div class="empty-state" id="sin-resultados" hidden>
      <h3>Sin coincidencias</h3>
      <p>No encontramos productos con esos términos. Pruebe con otra palabra o escríbanos: manejamos más referencias de las publicadas.</p>
      <p style="margin-top:1.25rem"><a class="btn btn--primary" href="<?= enlaceWhatsApp('Hola Flexuministros, busco un producto que no encontré en el catálogo del sitio.') ?>" target="_blank" rel="noopener"><?= iconoRelleno('wa') ?>Consultar por WhatsApp</a></p>
    </div>
    <p class="form__note" style="margin-top:2.5rem">Las presentaciones y medidas mostradas son referenciales. Disponibilidad, especificaciones y precios se confirman por cotización.</p>
  </div>
</section>

<?= banda_cta_html() ?>
<?php
$body = ob_get_clean();

$itemListElement = [];
foreach ($productos as $i => $p) {
  $item = [
    '@type' => 'Product', 'name' => $p['nombre'], 'description' => $p['descripcion'],
    'category' => $p['categoria_nombre'], 'brand' => ['@type' => 'Brand', 'name' => 'Flexuministros'],
  ];
  if (!empty($p['imagen'])) $item['image'] = SITIO_URL . '/uploads/productos/' . $p['imagen'];
  $itemListElement[] = ['@type' => 'ListItem', 'position' => $i + 1, 'item' => $item];
}

echo pagina([
  'id' => 'productos',
  'title' => "Catálogo de productos | {$totalProductos}+ suministros para flexografía | Flexuministros",
  'description' => 'Catálogo completo de suministros para impresión flexográfica: charolas, rasquetas, cintas de montaje, anilox, instrumentos de medición, cuchillas, limpiadores y lubricantes. Cotice en línea.',
  'canonical' => '/productos.html',
  'jsonld' => [
    crumbs_jsonld([['Inicio', '/'], ['Productos', '/productos.html']]),
    ['@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'Catálogo de suministros para impresión flexográfica', 'numberOfItems' => $totalProductos, 'itemListElement' => $itemListElement],
  ],
  'body' => $body,
]);
