<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;

$marcas = obtenerMarcas();
$categorias = obtenerCategorias();
$totalProductos = (int)conexionBD()->query('SELECT COUNT(*) FROM productos WHERE visible = 1')->fetchColumn();
$totalMarcas = count($marcas);

$destacados = conexionBD()->query(
  'SELECT p.*, c.nombre AS categoria_nombre FROM productos p
   LEFT JOIN categorias c ON c.id = p.categoria_id
   WHERE p.visible = 1 ORDER BY p.orden ASC, p.id ASC LIMIT 8'
)->fetchAll();

$tickerItems = '';
foreach ($categorias as $cat) $tickerItems .= '<span>' . esc($cat['nombre']) . '</span>';

$heroImg1 = $c('hero_imagen_1', 'hero-flexo-4.webp');
$heroImg1W = (int)$c('hero_imagen_1_ancho', '1254');
$heroImg1H = (int)$c('hero_imagen_1_alto', '1254');
$heroImg2 = $c('hero_imagen_2', 'hero-flexo-3-restaurada-v2.webp');
$heroImg2W = (int)$c('hero_imagen_2_ancho', '1448');
$heroImg2H = (int)$c('hero_imagen_2_alto', '1086');

$mision = $c('mision_texto', 'Brindar a nuestros clientes soluciones integrales mediante productos innovadores, equipos confiables y asesoría técnica especializada que contribuyan al éxito y crecimiento de sus negocios de impresión.');

ob_start();
?>
<section class="hero" id="hero-carrusel" role="group" aria-roledescription="carrusel" aria-label="Flexuministros: suministros, productos y equipo para impresión flexográfica">
  <div class="hero__media" aria-hidden="true">
    <div class="hero__slide is-active">
      <img src="/uploads/configuracion/<?= esc($heroImg1) ?>" width="<?= $heroImg1W ?>" height="<?= $heroImg1H ?>" alt="" loading="eager" decoding="async" fetchpriority="high">
    </div>
    <div class="hero__slide" aria-hidden="true">
      <img data-src="/uploads/configuracion/<?= esc($heroImg2) ?>" width="<?= $heroImg2W ?>" height="<?= $heroImg2H ?>" alt="" decoding="async">
    </div>
  </div>
  <div class="hero__overlay" aria-hidden="true"></div>
  <div class="hero__pips" role="group" aria-label="Elegir imagen del carrusel">
    <button class="hero__pip" type="button" data-i="0" aria-current="true" aria-label="Diapositiva 1"></button>
    <button class="hero__pip" type="button" data-i="1" aria-label="Diapositiva 2"></button>
  </div>
  <div class="container">
    <div class="hero__stage">
      <div class="hero__copy">
        <p class="hero__badge hero-in" style="--d:60ms"><span class="hero__dot"></span><?= esc($c('hero_badge', 'Soluciones para la industria flexográfica')) ?></p>
        <?php
          $tituloSecundario = esc($c('hero_titulo_2', 'Lo nuestro, Suministros & Equipos.'));
          $tituloSecundario = str_replace('&amp;', '<span class="hero__headline-accent">&amp;</span>', $tituloSecundario);
          if (substr($tituloSecundario, -1) === '.') {
            $tituloSecundario = substr($tituloSecundario, 0, -1) . '<span class="hero__headline-accent">.</span>';
          }
        ?>
        <h1><span class="hero__headline-main"><?= esc($c('hero_titulo_1', 'Lo tuyo es FLEXO.')) ?></span><em><?= $tituloSecundario ?></em></h1>
        <p class="hero__text hero-in" style="--d:420ms"><?= esc($c('hero_texto', 'Consumibles, herramientas, accesorios, equipos, servicios especiales y soporte técnico para mantener su operación precisa, eficiente y siempre en movimiento.')) ?></p>
        <div class="hero__actions hero-in" style="--d:540ms">
          <a class="btn btn--primary" href="/productos.html">Ver catálogo <?= icono('arrow') ?></a>
          <a class="btn btn--onDark" href="<?= enlaceWhatsApp('Hola Flexuministros, me gustaría recibir asesoría sobre suministros para impresión flexográfica.') ?>" target="_blank" rel="noopener"><?= iconoRelleno('wa') ?>Hablar con un especialista</a>
        </div>
        <ul class="hero__assurance hero-in" style="--d:600ms" aria-label="Ventajas de Flexuministros">
          <li><?= icono('check') ?>Asesoría especializada</li>
          <li><?= icono('check') ?>Rodillos anilox</li>
          <li><?= icono('check') ?>Limpieza ultrasonido</li>
          <li><?= icono('check') ?>Tratamiento de residuos de tinta</li>
          <li><?= icono('check') ?>Marcas líderes</li>
          <li><?= icono('check') ?>Equipos nuevos y usados</li>
          <li><?= icono('check') ?>Envíos dentro y fuera de México</li>
        </ul>
      </div>
    </div>

    <div class="stats hero-in" style="--d:660ms">
      <div><b data-count="30" data-suffix="+">30+</b><span>Años de experiencia</span></div>
      <div><b data-count="150" data-suffix="+">150+</b><span>Productos en catálogo</span></div>
      <div><b data-count="20" data-suffix="+">20+</b><span>Marcas representadas</span></div>
    </div>
  </div>

  <div class="ticker" aria-hidden="true">
    <div class="ticker__track"><?= $tickerItems . $tickerItems ?></div>
  </div>
</section>

<section class="section section--tight section--alt">
  <div class="container">
    <div class="section-head center" data-reveal style="margin-bottom:1.75rem">
      <p class="eyebrow">Representamos firmas reconocidas mundialmente</p>
      <p class="lead" style="margin:0">Símbolo de innovación y confianza para atender todas sus necesidades de suministro.</p>
    </div>
    <?= marcas_html($marcas) ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid--2" style="align-items:center;gap:clamp(2rem,5vw,4rem)">
      <div data-reveal="left">
        <p class="eyebrow">Quiénes somos</p>
        <h2><?= esc($c('quienes_somos_titulo', 'Especialistas en optimizar su proceso de impresión')) ?></h2>
        <p class="lead">Flexuministros es una compañía mexicana fundada en <?= esc($c('fundacion_anio', '1996')) ?>, especializada en soluciones integrales para contribuir a la optimización de procesos productivos.</p>
        <p><?= $c('quienes_somos_texto', 'Ofrecemos a la industria de impresión flexográfica servicios especiales, comercialización de productos innovadores y equipos. Representamos firmas reconocidas mundialmente, símbolo de innovación y confianza, para atender todas sus necesidades antes, durante y después de su proceso de impresión flexo.') ?></p>
        <p><a class="btn btn--ghost" href="/nosotros.html">Conocer más sobre nosotros <?= icono('arrow') ?></a></p>
      </div>
      <figure class="quote" data-reveal="right" style="margin:0">
        <span class="quote__mark" aria-hidden="true">&ldquo;</span>
        <blockquote><?= $mision ?></blockquote>
        <figcaption>Nuestra misión</figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="section section--alt cat-section" aria-labelledby="cat-h">
  <div class="container">
    <div class="section-head center" data-reveal>
      <p class="eyebrow">Catálogo</p>
      <h2 id="cat-h">Diversas líneas que cubren toda su operación</h2>
      <p class="lead">Desde el centro hasta el instrumento de medición: todo lo que su prensa flexográfica necesita, en un solo proveedor.</p>
    </div>
    <div class="grid grid--3">
      <?php foreach ($categorias as $i => $cat) echo tarjeta_categoria($cat, $i); ?>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="why-h">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">Por qué Flexuministros</p>
      <h2 id="why-h">Más que un proveedor, un socio técnico</h2>
    </div>
    <div class="grid grid--duo">
      <div class="feature" data-reveal style="--d:0ms"><span class="icon-badge"><?= icono('users') ?></span><div><h3>Consultoría especializada</h3><p>No solo vendemos producto: analizamos su proceso y recomendamos la solución que realmente resuelve el problema en piso.</p></div></div>
      <div class="feature" data-reveal style="--d:90ms"><span class="icon-badge"><?= icono('leaf') ?></span><div><h3>Bajo impacto ambiental</h3><p>Priorizamos productos biodegradables, reutilizables y equipos que tratan los residuos de tinta, contribuyendo a la ecología.</p></div></div>
      <div class="feature" data-reveal style="--d:180ms"><span class="icon-badge"><?= icono('globe') ?></span><div><h3>Marcas líderes mundiales</h3><p>Representamos firmas reconocidas internacionalmente, símbolo de innovación y confianza en la industria flexográfica.</p></div></div>
      <div class="feature" data-reveal style="--d:270ms"><span class="icon-badge"><?= icono('truck') ?></span><div><h3>Surtido integral</h3><p>Más de 150 productos en siete líneas: auxiliares, instrumentos, herramentales, cintas, limpiadores, lubricantes y consumibles.</p></div></div>
      <div class="feature" data-reveal style="--d:360ms"><span class="icon-badge"><?= icono('wrench') ?></span><div><h3>Compra y venta de equipos</h3><p>Conectamos a nuestros clientes con maquinaria y accesorios confiables y adecuados para sus procesos, brindando asesoría durante el proceso de selección, compra, venta y/o consignación.</p></div></div>
    </div>
  </div>
</section>

<section class="section section--alt" aria-labelledby="dest-h">
  <div class="container">
    <div class="section-head center" data-reveal>
      <p class="eyebrow">Selección</p>
      <h2 id="dest-h">Productos destacados</h2>
    </div>
    <div class="products">
      <?php foreach ($destacados as $i => $p) echo tarjeta_producto($p, false, $i); ?>
    </div>
    <p class="center" style="margin-top:2.25rem">
      <a class="btn btn--primary" href="/productos.html">Ver todos los productos <?= icono('arrow') ?></a>
    </p>
  </div>
</section>

<?= banda_cta_html() ?>
<?php
$body = ob_get_clean();

echo pagina([
  'id' => 'inicio',
  'title' => 'Flexuministros | Suministros para impresión flexográfica en México',
  'description' => 'Desde ' . $c('fundacion_anio', '1996') . ' abastecemos a la industria de impresión flexográfica en México: consultoría especializada y más de ' . $totalProductos . ' productos de bajo impacto ambiental. Guadalajara, Jalisco.',
  'canonical' => '/',
  'jsonld' => [
    [
      '@context' => 'https://schema.org', '@type' => 'Organization', '@id' => SITIO_URL . '/#organizacion',
      'name' => 'Flexuministros', 'url' => SITIO_URL . '/', 'logo' => SITIO_URL . '/assets/img/icon-512.png',
      'foundingDate' => $c('fundacion_anio', '1996'),
      'description' => 'Compañía mexicana especializada en soluciones integrales para la industria de impresión flexográfica: consultoría y comercialización de productos innovadores de bajo impacto ambiental.',
      'email' => $c('correo_info', 'info@flexuministros.com'),
      'telephone' => '+52-' . $c('telefono_principal', '3338387947'),
      'address' => [
        '@type' => 'PostalAddress', 'streetAddress' => $c('direccion_calle'), 'addressLocality' => $c('direccion_ciudad'),
        'addressRegion' => $c('direccion_estado'), 'postalCode' => $c('direccion_cp'), 'addressCountry' => 'MX',
      ],
      'sameAs' => [$c('facebook_url'), $c('tiktok_url')],
      'areaServed' => ['@type' => 'Country', 'name' => 'México'],
      'slogan' => 'Lo tuyo es flexo. Lo nuestro, suministros & equipos.',
    ],
    [
      '@context' => 'https://schema.org', '@type' => 'WebSite', '@id' => SITIO_URL . '/#sitio',
      'url' => SITIO_URL . '/', 'name' => 'Flexuministros', 'inLanguage' => 'es-MX',
      'publisher' => ['@id' => SITIO_URL . '/#organizacion'],
    ],
  ],
  'body' => $body,
]);
