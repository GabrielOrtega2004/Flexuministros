<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;

$servicios = obtenerServicios(true);
$items = array_map(fn($s) => [
  'slug' => $s['slug'], 'eyebrow' => $s['eyebrow'], 'titulo' => $s['titulo'],
  'texto_principal' => nl2br($s['texto_principal']), 'texto_secundario' => nl2br($s['texto_secundario']),
  'texto_extra' => $s['texto_extra'], 'icono' => $s['icono'], 'imagen' => $s['imagen'],
  'imagen_ancho' => $s['imagen_ancho'], 'imagen_alto' => $s['imagen_alto'], 'whatsapp_texto' => $s['whatsapp_texto'],
], $servicios);

$marcasServicio = array_filter(obtenerMarcasServicio(true), fn($m) => $m['nombre'] !== 'BDTECH');

ob_start();
?>
<section class="svc-top" data-pausar-fuera>
  <div class="svc-top__media" aria-hidden="true"><img src="/uploads/configuracion/<?= esc($c('servicios_hero_imagen', 'servicios-hero-v1.webp')) ?>" alt="" width="<?= (int)$c('servicios_hero_ancho', '1840') ?>" height="<?= (int)$c('servicios_hero_alto', '855') ?>"></div>
  <div class="svc-top__aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="svc-top__mesh" aria-hidden="true"></div>
  <div class="svc-top__beam" aria-hidden="true"></div>
  <div class="svc-top__scrim" aria-hidden="true"></div>
  <div class="container">
    <?= crumbs_html([['Inicio', '/'], ['Servicios', '/servicios.html']]) ?>
    <p class="svc-top__eyebrow hero-in" style="--d:80ms"><span></span>Más que suministros</p>
    <h1 class="hero-in" style="--d:150ms">Servicios</h1>
    <p class="svc-top__lead hero-in" style="--d:220ms">Recubrimiento de rodillos anilox y de hule, limpieza profunda de rodillos anilox, corte de centros, desarrollo de empaque e inventario en consignación.</p>
  </div>
</section>

<?php foreach ($items as $i => $item) echo seccion_con_foto($item, $i, 'servicios'); ?>

<?php if ($marcasServicio): ?>
<section class="section section--tight svc-brands">
  <div class="container">
    <div class="section-head center">
      <p class="eyebrow">Tecnología y respaldo especializado</p>
      <h2>Marcas que respaldan nuestros servicios</h2>
      <p class="lead">Contamos con el respaldo de tecnologías y fabricantes reconocidos mundialmente, así como soluciones locales.</p>
    </div>
    <div class="grid grid--2" style="max-width:44rem;margin-inline:auto">
      <?php foreach ($marcasServicio as $m): ?>
      <div class="card">
        <img class="svc-brands__logo" src="/uploads/marcas_servicio/<?= esc($m['logo']) ?>" width="611" height="160" alt="<?= esc($m['nombre']) ?>" loading="lazy" decoding="async" <?= $m['nombre'] === 'Aalberts Surface Technologies' ? 'style="height:4.4rem"' : '' ?>>
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
  'id' => 'servicios',
  'title' => 'Servicios | Flexuministros — Guadalajara, Jalisco',
  'description' => 'Recubrimiento de rodillos anilox y de hule, limpieza profunda de rodillos anilox con ultrasonido, corte de centros a la medida, desarrollo de empaque e inventario en consignación.',
  'canonical' => '/servicios.html',
  'jsonld' => [crumbs_jsonld([['Inicio', '/'], ['Servicios', '/servicios.html']])],
  'body' => $body,
]);
