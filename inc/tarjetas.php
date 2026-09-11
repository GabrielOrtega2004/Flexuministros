<?php
/* Piezas de HTML repetidas entre páginas: tarjeta de producto, tarjeta de
   categoría y la sección grande con foto que usan Equipos y Servicios. */
require_once __DIR__ . '/iconos.php';
require_once __DIR__ . '/datos.php';

const CAT_ICONOS = [
  'auxiliares' => 'tray', 'instrumentos' => 'gauge', 'herramentales' => 'blade',
  'cintas' => 'roll', 'limpiadores' => 'spray', 'lubricantes' => 'drop', 'varios' => 'box',
];

function tarjeta_producto(array $p, bool $eager, int $i): string {
  $animacion = $i < 8 ? ' data-reveal="zoom" style="--d:' . (($i % 4) * 70) . 'ms"' : '';
  $busqueda = esc($p['nombre'] . ' ' . $p['descripcion'] . ' ' . ($p['categoria_nombre'] ?? ''));
  $iconoCat = CAT_ICONOS[$p['categoria_id']] ?? 'box';

  if (!empty($p['imagen'])) {
    $carga = $eager ? 'loading="eager"' : 'loading="lazy"';
    $media = '<img src="/uploads/productos/' . esc($p['imagen']) . '" width="' . (int)($p['imagen_ancho'] ?: 400) . '" height="' . (int)($p['imagen_alto'] ?: 300) . '" alt="' . esc($p['nombre']) . '" ' . $carga . ' decoding="async">';
    $mediaClase = 'product__media';
  } else {
    $media = icono($iconoCat);
    $mediaClase = 'product__media product__media--empty';
  }

  $waTexto = 'Hola Flexuministros, quisiera cotizar: ' . $p['nombre'] . ' (' . $p['descripcion'] . ').';

  return '<article class="product"' . $animacion . ' data-cat="' . esc($p['categoria_id']) . '" data-search="' . $busqueda . '">
            <div class="' . $mediaClase . '">
              ' . $media . '
            </div>
            <div class="product__body">
              <span class="product__cat">' . esc($p['categoria_nombre'] ?? '') . '</span>
              <h3 class="product__name">' . esc($p['nombre']) . '</h3>
              <p class="product__sub">' . esc($p['descripcion']) . '</p>
              <a class="product__cta" href="' . enlaceWhatsApp($waTexto) . '" target="_blank" rel="noopener">Cotizar ' . icono('arrow') . '<span class="sr-only"> ' . esc($p['nombre']) . ' por WhatsApp</span></a>
            </div>
          </article>';
}

function tarjeta_categoria(array $c, int $i): string {
  return '<a class="card card--link cat-card" data-reveal="zoom" style="--d:' . ($i * 70) . 'ms" href="/productos.html?cat=' . esc($c['id']) . '">
          <span class="cat-card__num" aria-hidden="true">' . str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) . '</span>
          <span class="icon-badge">' . icono($c['icono'] ?: (CAT_ICONOS[$c['id']] ?? 'box')) . '</span>
          <h3>' . esc($c['nombre']) . '</h3>
          <p>' . esc($c['descripcion']) . '</p>
          <span class="cat-card__count">Ver los productos ' . icono('arrow') . '</span>
        </a>';
}

/**
 * Sección grande alternada con foto (usada por Equipos y Servicios).
 * $item debe traer: slug, eyebrow, titulo, texto_principal, texto_secundario,
 * texto_extra (opcional), icono, imagen (nombre de archivo o null), imagen_ancho,
 * imagen_alto, whatsapp_texto.
 */
function seccion_con_foto(array $item, int $i, string $carpetaUploads): string {
  if (!empty($item['imagen'])) {
    $media = '<img src="/uploads/' . $carpetaUploads . '/' . esc($item['imagen']) . '" width="' . (int)($item['imagen_ancho'] ?: 900) . '" height="' . (int)($item['imagen_alto'] ?: 675) . '" alt="' . esc($item['titulo']) . '" loading="lazy" decoding="async">';
  } else {
    $media = '<div class="svc-photo--icon">' . icono($item['icono'] ?: 'box') . '</div>';
  }
  $foto = '<figure class="svc-photo" data-reveal="' . ($i % 2 ? 'right' : 'left') . '">' . $media . '</figure>';

  $extra = !empty($item['texto_extra']) ? '<p>' . $item['texto_extra'] . '</p>' : '';
  $wa = enlaceWhatsApp($item['whatsapp_texto'] ?: ('Hola Flexuministros, quisiera información sobre ' . $item['titulo'] . '.'));

  $copy = '<div data-reveal="' . ($i % 2 ? 'left' : 'right') . '">
          <p class="eyebrow">' . esc($item['eyebrow'] ?? '') . '</p>
          <h2>' . esc($item['titulo']) . '</h2>
          <p class="lead">' . $item['texto_principal'] . '</p>
          <p>' . $item['texto_secundario'] . '</p>
          ' . $extra . '
          <p style="margin-top:1.5rem"><a class="btn svc-wa-btn" href="' . $wa . '" target="_blank" rel="noopener"><span class="svc-wa-btn__icon"><img src="/assets/img/whatsapp.svg" width="22" height="22" alt="" aria-hidden="true"></span><span class="svc-wa-btn__copy"><strong>Cotizar por WhatsApp</strong><small>Respuesta rápida</small></span>' . icono('arrow') . '</a></p>
        </div>';

  $idAttr = !empty($item['slug']) ? ' id="' . esc($item['slug']) . '"' : '';
  $orden = $i % 2 ? $copy . "\n        " . $foto : $foto . "\n        " . $copy;

  return '<section' . $idAttr . ' class="section' . ($i % 2 ? ' section--alt' : '') . ' svc-section">
    <div class="container">
      <div class="grid grid--2 svc-section__grid">
        ' . $orden . '
      </div>
    </div>
  </section>';
}

function marcas_html(array $marcas): string {
  $html = '<ul class="brands">' . "\n";
  foreach ($marcas as $i => $b) {
    $img = '<img src="/assets/img/marcas/' . esc($b['logo']) . '" width="' . (int)$b['ancho'] . '" height="' . (int)$b['alto'] . '" alt="' . esc($b['nombre']) . '" loading="lazy" decoding="async">';
    $contenido = !empty($b['enlace'])
      ? '<a href="' . esc($b['enlace']) . '" aria-label="Ver productos ' . esc($b['nombre']) . ' en el catálogo">' . $img . '</a>'
      : $img;
    $html .= '        <li data-reveal="zoom" style="--d:' . ($i * 45) . 'ms">' . $contenido . "</li>\n";
  }
  $html .= '      </ul>';
  return $html;
}
