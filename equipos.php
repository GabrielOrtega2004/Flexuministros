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

$marcasEquipos = array_filter(obtenerMarcasServicio(true), fn($m) => $m['nombre'] !== 'Aalberts Surface Technologies');

ob_start();
?>
<section class="svc-top" data-pausar-fuera>
  <div class="svc-top__media" aria-hidden="true"><img src="/uploads/configuracion/<?= esc($c('equipos_hero_imagen', 'hero-flexo-3-restaurada-v2.webp')) ?>" alt="" width="<?= (int)$c('equipos_hero_ancho', '1448') ?>" height="<?= (int)$c('equipos_hero_alto', '1086') ?>"></div>
  <div class="svc-top__aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="svc-top__mesh" aria-hidden="true"></div>
  <div class="svc-top__beam" aria-hidden="true"></div>
  <div class="svc-top__scrim" aria-hidden="true"></div>
  <div class="container">
    <?= crumbs_html([['Inicio', '/'], ['Equipos', '/equipos.html']]) ?>
    <p class="svc-top__eyebrow hero-in" style="--d:80ms"><span></span>Nuevos &amp; usados</p>
    <h1 class="hero-in" style="--d:150ms">Equipos nuevos y usados para su línea de <em>producción.</em></h1>
    <p class="svc-top__lead hero-in" style="--d:220ms">Fabricamos en México equipos para eficientar su operación y comercializamos maquinaria de marcas reconocidas internacionalmente para distintas etapas del proceso de impresión. Además, ofrecemos compra, venta y consignación de equipos usados, brindando soluciones para diferentes necesidades de producción e inversión.</p>
  </div>
</section>

<?php foreach ($items as $i => $item) echo seccion_con_foto($item, $i, 'equipos'); ?>

<?php if ($marcasEquipos): ?>
<section class="section section--tight svc-brands">
  <div class="container">
    <div class="section-head center">
      <p class="eyebrow">Representantes y distribuidores autorizados en México</p>
      <h2>Marcas que respaldan nuestros equipos y soluciones</h2>
      <p class="lead">Equipos, tecnología y soluciones especializadas de marcas líderes a nivel mundial.</p>
    </div>
    <div class="grid grid--2" style="max-width:44rem;margin-inline:auto">
      <?php foreach ($marcasEquipos as $m): ?>
      <div class="card">
        <img class="svc-brands__logo" src="/uploads/marcas_servicio/<?= esc($m['logo']) ?>" width="611" height="160" alt="<?= esc($m['nombre']) ?>" loading="lazy" decoding="async">
        <h3><?= esc($m['nombre']) ?></h3>
        <p><?= esc($m['descripcion']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

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
