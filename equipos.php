<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;

$equipos = obtenerEquipos(true);
// Normaliza los nombres de columna al formato que espera seccion_con_foto().
$items = array_map(fn($e) => [
  'slug' => $e['slug'], 'eyebrow' => $e['eyebrow'], 'titulo' => $e['nombre'],
  'texto_principal' => nl2br($e['descripcion_corta']), 'texto_secundario' => nl2br($e['descripcion']),
  'texto_extra' => null, 'icono' => $e['icono'], 'imagen' => $e['imagen_principal'],
  'imagen_ancho' => $e['imagen_ancho'], 'imagen_alto' => $e['imagen_alto'], 'whatsapp_texto' => $e['whatsapp_texto'],
], $equipos);

ob_start();
?>
<section class="svc-top" data-pausar-fuera>
  <div class="svc-top__media" aria-hidden="true"><img src="/assets/img/hero/<?= esc($c('equipos_hero_imagen', 'hero-flexo-3-restaurada-v2.webp')) ?>" alt="" width="<?= (int)$c('equipos_hero_ancho', '1448') ?>" height="<?= (int)$c('equipos_hero_alto', '1086') ?>"></div>
  <div class="svc-top__aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="svc-top__mesh" aria-hidden="true"></div>
  <div class="svc-top__beam" aria-hidden="true"></div>
  <div class="svc-top__scrim" aria-hidden="true"></div>
  <div class="container">
    <?= crumbs_html([['Inicio', '/'], ['Equipos', '/equipos.html']]) ?>
    <p class="svc-top__eyebrow hero-in" style="--d:80ms"><span></span>Compra, venta y/o consignación</p>
    <h1 class="hero-in" style="--d:150ms">Equipos para su línea de <em>producción.</em></h1>
    <p class="svc-top__lead hero-in" style="--d:220ms">Fabricamos y comercializamos equipo nuevo: lavadoras de ultrasonido para limpieza de rodillos anilox, tratadoras de residuos de tinta, mesas rebobinadoras, cortadoras de centros, levantadores de rollos, herramentales y más. Además ofrecemos compra-venta de equipo usado: impresoras flexográficas, revisadoras, accesorios y herramentales.</p>
  </div>
</section>

<?php foreach ($items as $i => $item) echo seccion_con_foto($item, $i, 'equipos'); ?>

<?= banda_cta_html() ?>
<?php
$body = ob_get_clean();

echo pagina([
  'id' => 'equipos',
  'title' => 'Equipos: compra, venta y consignación | Flexuministros — Guadalajara, Jalisco',
  'description' => 'Fabricamos y comercializamos equipo nuevo: mesas rebobinadoras, cortadoras de centros, levantadores de bobinas. Además compra-venta de equipo usado: suajadoras, revisadoras, montadoras de grabados y lavadoras de anilox.',
  'canonical' => '/equipos.html',
  'jsonld' => [crumbs_jsonld([['Inicio', '/'], ['Equipos', '/equipos.html']])],
  'body' => $body,
]);
