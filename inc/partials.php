<?php
/* Fragmentos compartidos del sitio público — mismo HTML que generaba
   _src/partials.js, ahora leyendo los datos editables desde la tabla
   `configuracion` en vez de un objeto fijo en el código. */
require_once __DIR__ . '/iconos.php';
require_once __DIR__ . '/datos.php';

const SITIO_URL = 'https://www.flexuministros.com';

/* $items: [[etiqueta, href], ...] — el último se marca como página actual. */
function crumbs_html(array $items): string {
  $html = '<ol class="crumbs">' . "\n";
  $ultimo = count($items) - 1;
  foreach ($items as $i => [$etiqueta, $href]) {
    $html .= $i === $ultimo
      ? '        <li aria-current="page">' . esc($etiqueta) . "</li>\n"
      : '        <li><a href="' . $href . '">' . esc($etiqueta) . "</a></li>\n";
  }
  $html .= '      </ol>';
  return $html;
}

function crumbs_jsonld(array $items): array {
  $lista = [];
  foreach ($items as $i => [$etiqueta, $href]) {
    $lista[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $etiqueta, 'item' => SITIO_URL . $href];
  }
  return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $lista];
}

function nav_html(string $pagina): string {
  $enlaces = [
    ['/', 'Inicio', 'inicio'],
    ['/nosotros.html', 'Nosotros', 'nosotros'],
    ['/productos.html', 'Productos', 'productos'],
    ['/equipos.html', 'Equipos', 'equipos'],
    ['/servicios.html', 'Servicios', 'servicios'],
    ['/contacto.html', 'Contacto', 'contacto'],
  ];
  $html = '<nav class="nav" id="nav-principal" aria-label="Navegación principal">' . "\n";
  foreach ($enlaces as [$href, $etiqueta, $id]) {
    $actual = $pagina === $id ? ' aria-current="page"' : '';
    $html .= "        <a href=\"{$href}\"{$actual}>{$etiqueta}</a>\n";
  }
  $html .= '        <a class="btn btn--primary btn--sm" href="/contacto.html">Solicitar cotización ' . icono('arrow') . "</a>\n";
  $html .= '      </nav>';
  return $html;
}

function topbar_html(): string {
  $tel = obtenerConfig('telefono_principal', '3338387947');
  $telFmt = obtenerConfig('telefono_principal_formato', '33 3838 7947');
  $correo = obtenerConfig('correo_info', 'info@flexuministros.com');
  $mapsUrl = obtenerConfig('maps_url', '#');
  $ciudad = obtenerConfig('direccion_ciudad', 'Guadalajara') . ', ' . obtenerConfig('direccion_estado', 'Jalisco');
  $fb = obtenerConfig('facebook_url', '#');
  $tiktok = obtenerConfig('tiktok_url', '#');
  $iTel = icono('phone');
  $iCorreo = icono('mail');
  $iPin = icono('pin');
  $iFb = iconoRelleno('fb');
  $iTiktok = iconoRelleno('tiktok');
  return '<div class="topbar">
    <div class="container">
      <ul class="topbar__list">
        <li><a href="tel:+52' . esc($tel) . '">' . $iTel . '<span>' . esc($telFmt) . '</span></a></li>
        <li><a href="mailto:' . esc($correo) . '">' . $iCorreo . '<span>' . esc($correo) . '</span></a></li>
        <li><a href="' . $mapsUrl . '" target="_blank" rel="noopener">' . $iPin . '<span>' . esc($ciudad) . '</span></a></li>
      </ul>
      <div class="topbar__social">
        <a href="' . $fb . '" target="_blank" rel="noopener" aria-label="Flexuministros en Facebook">' . $iFb . '</a>
        <a href="' . $tiktok . '" target="_blank" rel="noopener" aria-label="Flexuministros en TikTok">' . $iTiktok . '</a>
      </div>
    </div>
  </div>';
}

function encabezado_html(string $pagina): string {
  return '<div class="header-shell">
    ' . topbar_html() . '
    <header class="header">
      <div class="container">
        <a class="brand" href="/" aria-label="Flexuministros — inicio">
          <img class="brand__logo brand__logo--main" src="/assets/img/logo.svg" width="190" height="65" alt="Flexuministros, suministros para impresión y equipos" fetchpriority="high" decoding="async">
          <img class="brand__logo brand__logo--inverse" src="/assets/img/logo-inverso.svg" width="190" height="65" alt="" aria-hidden="true" decoding="async">
        </a>
        ' . nav_html($pagina) . '
        <div class="header__cta">
          <button class="burger" type="button" aria-expanded="false" aria-controls="nav-principal" aria-label="Abrir menú"><span></span></button>
        </div>
      </div>
    </header>
  </div>';
}

function banda_cta_html(): string {
  $wa = enlaceWhatsApp('Hola Flexuministros, necesito cotizar un suministro para mi proceso de impresión flexográfica.');
  return '<section class="ctaband" id="asesoria" data-reveal="trigger">
    <div class="ctaband__media" aria-hidden="true">
      <div class="ctaband__shot">
        <img src="/assets/img/cta/flexo-industrial-v2.webp" width="1600" height="900" alt="" loading="lazy" decoding="async">
      </div>
      <div class="ctaband__veil"></div>
    </div>
    <div class="container">
      <div class="ctaband__inner">
        <h2>¿Necesita un <em>suministro específico?</em></h2>
        <p>Cuéntenos su proceso y le recomendamos el producto correcto. Cotizaciones sin compromiso y asesoría técnica incluida.</p>
        <div class="ctaband__actions">
          <a class="btn btn--primary" href="/contacto.html">Solicitar cotización</a>
          <a class="btn btn--onDark" href="' . $wa . '" target="_blank" rel="noopener">' . iconoRelleno('wa', 'ctaband__wa-logo') . 'Escribir por WhatsApp</a>
        </div>
      </div>
    </div>
  </section>';
}

function pie_html(): string {
  $anioFundacion = obtenerConfig('fundacion_anio', '1996');
  $tel = obtenerConfig('telefono_principal', '3338387947');
  $telFmt = obtenerConfig('telefono_principal_formato', '33 3838 7947');
  $waIntl = obtenerConfig('whatsapp_intl', '523321063740');
  $waFmt = obtenerConfig('whatsapp_formato', '33 2106 3740');
  $correoInfo = obtenerConfig('correo_info', 'info@flexuministros.com');
  $correoVentas = obtenerConfig('correo_ventas', 'ventas@flexuministros.com');
  $calle = obtenerConfig('direccion_calle', 'Aldama 770, Col. La Loma');
  $ciudad = obtenerConfig('direccion_ciudad', 'Guadalajara');
  $estado = obtenerConfig('direccion_estado', 'Jalisco');
  $cp = obtenerConfig('direccion_cp', '44410');
  $mapsUrl = obtenerConfig('maps_url', '#');
  $fb = obtenerConfig('facebook_url', '#');
  $tiktok = obtenerConfig('tiktok_url', '#');
  $horario = obtenerConfig('horario', 'Lunes a viernes, 9:00 – 18:00 h');
  $waSaludo = enlaceWhatsApp('Hola Flexuministros, me gustaría recibir información.');
  $anioActual = date('Y');

  return '<footer class="footer">
    <div class="container">
      <div class="footer__grid">
        <div data-reveal style="--d:0ms">
          <img class="footer__logo" src="/assets/img/logo-inverso.svg" width="200" height="68" alt="Flexuministros" loading="lazy">
          <p class="footer__about">Compañía mexicana fundada en ' . esc($anioFundacion) . ', especializada en soluciones integrales para la industria de impresión flexográfica: consultoría y productos innovadores de bajo impacto ambiental.</p>
          <div class="footer__social">
            <a href="' . $fb . '" target="_blank" rel="noopener" aria-label="Flexuministros en Facebook">' . iconoRelleno('fb') . '</a>
            <a href="' . $tiktok . '" target="_blank" rel="noopener" aria-label="Flexuministros en TikTok">' . iconoRelleno('tiktok') . '</a>
            <a href="' . $waSaludo . '" target="_blank" rel="noopener" aria-label="Escribir por WhatsApp">' . iconoRelleno('wa') . '</a>
          </div>
        </div>

        <nav aria-labelledby="f-nav" data-reveal style="--d:70ms">
          <h3 id="f-nav">Sitio</h3>
          <ul>
            <li><a href="/">Inicio</a></li>
            <li><a href="/nosotros.html">Nosotros</a></li>
            <li><a href="/productos.html">Productos</a></li>
            <li><a href="/equipos.html">Equipos</a></li>
            <li><a href="/servicios.html">Servicios</a></li>
            <li><a href="/contacto.html">Contacto</a></li>
          </ul>
        </nav>

        <nav aria-labelledby="f-cat" data-reveal style="--d:140ms">
          <h3 id="f-cat">Categorías</h3>
          <ul>
            <li><a href="/productos.html?cat=auxiliares">Productos auxiliares</a></li>
            <li><a href="/productos.html?cat=instrumentos">Instrumentos</a></li>
            <li><a href="/productos.html?cat=herramentales">Herramentales</a></li>
            <li><a href="/productos.html?cat=cintas">Cintas y adhesivos</a></li>
            <li><a href="/productos.html?cat=limpiadores">Limpiadores</a></li>
            <li><a href="/productos.html?cat=lubricantes">Lubricantes</a></li>
            <li><a href="/productos.html?cat=varios">Consumibles</a></li>
          </ul>
        </nav>

        <div data-reveal style="--d:210ms">
          <h3>Contacto</h3>
          <ul>
            <li><a href="' . $mapsUrl . '" target="_blank" rel="noopener">' . esc($calle) . '<br>' . esc($ciudad) . ', ' . esc($estado) . ' ' . esc($cp) . '</a></li>
            <li><a href="tel:+52' . esc($tel) . '">' . esc($telFmt) . '</a></li>
            <li><a href="https://wa.me/' . esc($waIntl) . '" target="_blank" rel="noopener">WhatsApp ' . esc($waFmt) . '</a></li>
            <li><a href="mailto:' . esc($correoInfo) . '">' . esc($correoInfo) . '</a></li>
            <li><a href="mailto:' . esc($correoVentas) . '">' . esc($correoVentas) . '</a></li>
          </ul>
        </div>
      </div>

      <div class="footer__bottom">
        <span>© <span id="anio">' . esc($anioActual) . '</span> Flexuministros. Todos los derechos reservados.</span>
        <span>' . esc($horario) . '</span>
      </div>
    </div>
  </footer>

  <a class="wa-float" href="' . enlaceWhatsApp('Hola Flexuministros, me gustaría recibir información sobre sus productos.') . '" target="_blank" rel="noopener" aria-label="Escribir a Flexuministros por WhatsApp">
    <span class="wa-float__label" aria-hidden="true">Hablar con un asesor</span>
    <img src="/assets/img/whatsapp.svg" width="32" height="32" alt="" aria-hidden="true">
  </a>

  <script src="/assets/js/main.js" defer></script>';
}

$__css_cache = null;
function css_minificado(): string {
  global $__css_cache;
  if ($__css_cache !== null) return $__css_cache;
  $css = file_get_contents(__DIR__ . '/../_src/style.css');
  // El archivo trae un BOM UTF-8 al inicio; quitarlo (el minificador JS lo
  // hacía sin querer al final, vía trim(), que sí reconoce el BOM como
  // espacio en blanco — el trim() de PHP no).
  $css = preg_replace('/^\xEF\xBB\xBF/', '', $css);
  $css = preg_replace('#/\*.*?\*/#s', '', $css);
  $css = preg_replace('/\s*([{}:;,>])\s*/', '$1', $css);
  $css = str_replace(';}', '}', $css);
  $css = preg_replace('/\s+/', ' ', $css);
  $__css_cache = trim($css);
  return $__css_cache;
}

/**
 * Arma el documento HTML completo de una página pública.
 * $opciones: id, title, description, canonical, jsonld (arreglo de arreglos ya
 * listos para json_encode), body (HTML ya armado), ogImage.
 */
function pagina(array $opciones): string {
  $id = $opciones['id'];
  $title = esc($opciones['title']);
  $description = esc($opciones['description']);
  $canonical = $opciones['canonical'];
  $jsonld = $opciones['jsonld'] ?? [];
  $body = $opciones['body'];
  $ogImage = $opciones['ogImage'] ?? '/assets/img/og.jpg';

  $jsonldHtml = '';
  foreach ($jsonld as $bloque) {
    $jsonldHtml .= '<script type="application/ld+json">' . json_encode($bloque, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";
  }

  $css = css_minificado();
  $sitio = SITIO_URL;
  $encabezadoHtml = encabezado_html($id);
  $pieHtml = pie_html();

  return <<<HTML
<!doctype html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title}</title>
<meta name="description" content="{$description}">
<link rel="canonical" href="{$sitio}{$canonical}">
<meta name="theme-color" content="#F57F01">
<meta name="author" content="Flexuministros">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">

<meta property="og:type" content="website">
<meta property="og:site_name" content="Flexuministros">
<meta property="og:locale" content="es_MX">
<meta property="og:title" content="{$title}">
<meta property="og:description" content="{$description}">
<meta property="og:url" content="{$sitio}{$canonical}">
<meta property="og:image" content="{$sitio}{$ogImage}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Flexuministros — suministros para impresión flexográfica">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{$title}">
<meta name="twitter:description" content="{$description}">
<meta name="twitter:image" content="{$sitio}{$ogImage}">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

<style>{$css}</style>
{$jsonldHtml}</head>
<body>
<script>document.documentElement.className+=" js-anim"</script>
<div id="cargador" class="cargador" aria-hidden="true">
  <div class="cargador__ambient"><i></i><i></i></div>
  <div class="cargador__grid"></div>
  <div class="cargador__content">
    <div class="cargador__logo-wrap">
      <img class="cargador__logo" src="/assets/img/logo-inverso.svg" width="240" height="82" alt="">
    </div>
    <p class="cargador__claim">Suministros para impresión <span>&amp;</span> equipos</p>
    <div class="cargador__progress">
      <span class="cargador__bar"><i id="cargador-bar"></i></span>
      <span class="cargador__percent" id="cargador-percent">0%</span>
    </div>
    <p class="cargador__status">Preparando su experiencia</p>
  </div>
  <span class="cargador__edge"></span>
</div>
<script>
(function () {
  var el = document.getElementById('cargador');
  var bar = document.getElementById('cargador-bar');
  var percent = document.getElementById('cargador-percent');
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var inicio = performance.now(), listo = false, cerrando = false;
  var duracion = reduce ? 80 : 480;
  function pintar(valor) {
    valor = Math.max(0, Math.min(100, Math.round(valor)));
    bar.style.width = valor + '%';
    percent.textContent = valor + '%';
  }
  function cuadro(ahora) {
    var transcurrido = ahora - inicio;
    var t = Math.min(1, transcurrido / duracion);
    var suave = t * t * (3 - 2 * t);
    var valor = 4 + 96 * suave;
    if (!listo) valor = Math.min(valor, 92);
    pintar(valor);
    if (listo && t >= 1 && !cerrando) {
      cerrando = true;
      el.classList.add('is-ready');
      setTimeout(function () {
        el.classList.add('is-hidden');
        setTimeout(function () { el.remove(); }, reduce ? 80 : 340);
      }, reduce ? 0 : 60);
      return;
    }
    requestAnimationFrame(cuadro);
  }
  window.addEventListener('load', function () { listo = true; });
  setTimeout(function () { listo = true; }, 1400);
  requestAnimationFrame(cuadro);
})();
</script>
<a class="skip" href="#main">Saltar al contenido</a>
{$encabezadoHtml}
<main id="main">
{$body}
</main>
{$pieHtml}
</body>
</html>
HTML;
}
