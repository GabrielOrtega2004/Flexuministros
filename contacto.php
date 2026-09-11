<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;

$telPrincipal = $c('telefono_principal', '3338387947');
$telPrincipalFmt = $c('telefono_principal_formato', '33 3838 7947');
$correoVentas = $c('correo_ventas', 'ventas@flexuministros.com');
$calle = $c('direccion_calle', 'Aldama 770, Col. La Loma');
$ciudad = $c('direccion_ciudad', 'Guadalajara');
$estado = $c('direccion_estado', 'Jalisco');
$cp = $c('direccion_cp', '44410');
$mapsUrl = $c('maps_url', '#');
$mapsEmbed = $c('maps_embed_url', '');
$horario = $c('horario', 'Lunes a viernes, 9:00 – 18:00 h');
$waIntl = $c('whatsapp_intl', '523321063740');

$telefonos = json_decode($c('telefonos_directorio', '[]'), true) ?: [[$telPrincipalFmt, $telPrincipal]];
$correos = json_decode($c('correos_directorio', '[]'), true) ?: [[$c('correo_info', 'info@flexuministros.com'), 'Información general'], [$correoVentas, 'Ventas y cotizaciones']];

$categorias = obtenerCategorias();

ob_start();
?>
<section class="contact-top" data-pausar-fuera>
  <div class="contact-top__aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="contact-top__mesh" aria-hidden="true"></div>
  <div class="contact-top__beam" aria-hidden="true"></div>
  <div class="contact-top__scrim" aria-hidden="true"></div>
  <div class="container">
    <div class="contact-top__copy">
      <?= crumbs_html([['Inicio', '/'], ['Contacto', '/contacto.html']]) ?>
      <p class="contact-top__eyebrow hero-in" style="--d:80ms"><span></span>Contacto técnico y comercial</p>
      <h1 class="hero-in" style="--d:150ms">Hablemos de <em>su proceso.</em></h1>
      <p class="contact-top__lead hero-in" style="--d:220ms">Cuéntenos qué imprime y qué necesita resolver. El formulario toma menos de un minuto y respondemos con una recomendación concreta, no un catálogo genérico.</p>
      <div class="contact-top__points hero-in" style="--d:290ms" aria-label="Ventajas de contactar a Flexuministros">
        <span><?= icono('check') ?>Asesoría especializada</span>
        <span><?= icono('clock') ?>Respuesta de lunes a viernes</span>
        <span><?= icono('pin') ?>Atención en México y el extranjero</span>
      </div>
    </div>
    <div class="contact-top__visual hero-in" style="--d:260ms" aria-hidden="true">
      <div class="contact-orbit contact-orbit--outer"><i></i><i></i></div>
      <div class="contact-orbit contact-orbit--inner"><i></i></div>
      <div class="contact-signal"><?= icono('mail') ?><span></span></div>
      <div class="contact-float contact-float--one"><?= icono('spark') ?><span><b>Solución precisa</b><small>Asesoría técnica</small></span></div>
      <div class="contact-float contact-float--two"><?= icono('check') ?><span><b>Contacto directo</b><small>Sin intermediarios</small></span></div>
    </div>
  </div>
</section>

<section class="contact-main">
  <div class="container contact-main__grid">
    <form class="form contact-form" id="form-contacto" data-reveal="left" novalidate>
      <p class="contact-form__eyebrow">Solicitud de asesoría <span>01</span></p>
      <h2>Cuéntenos qué necesita</h2>
      <p class="contact-form__hint"><?= icono('spark') ?>No necesita la referencia exacta: con el equipo, el material y lo que busca resolver, nosotros identificamos el suministro.</p>

      <div class="form__row form__row--2">
        <div class="field">
          <label for="nombre">Nombre <span class="req">*</span></label>
          <div class="field__control"><?= icono('user', 'field__icon') ?><input id="nombre" name="nombre" type="text" required autocomplete="name" placeholder="Su nombre"></div>
        </div>
        <div class="field">
          <label for="empresa">Empresa</label>
          <div class="field__control"><?= icono('box', 'field__icon') ?><input id="empresa" name="empresa" type="text" autocomplete="organization" placeholder="Nombre de su empresa"></div>
        </div>
      </div>

      <div class="form__row form__row--2">
        <div class="field">
          <label for="email">Correo electrónico <span class="req">*</span></label>
          <div class="field__control"><?= icono('mail', 'field__icon') ?><input id="email" name="email" type="email" required autocomplete="email" placeholder="correo@empresa.com"></div>
        </div>
        <div class="field">
          <label for="telefono">Teléfono</label>
          <div class="field__control"><?= icono('phone', 'field__icon') ?><input id="telefono" name="telefono" type="tel" autocomplete="tel" placeholder="33 0000 0000"></div>
        </div>
      </div>

      <div class="field">
        <label for="interes">Línea de interés</label>
        <div class="field__control field__control--select"><?= icono('tray', 'field__icon') ?><select id="interes" name="interes">
          <option value="">Seleccione una opción…</option>
          <?php foreach ($categorias as $cat): ?>
          <option value="<?= esc($cat['nombre']) ?>"><?= esc($cat['nombre']) ?></option>
          <?php endforeach; ?>
          <option value="Equipos">Equipos</option>
          <option value="Recubrimiento de rodillos">Recubrimiento de rodillos</option>
          <option value="Limpieza de anilox">Limpieza de anilox</option>
          <option value="Asesoría técnica">Asesoría técnica / consultoría</option>
          <option value="Otro">Otro</option>
        </select><?= icono('chevron', 'field__chevron') ?></div>
      </div>

      <div class="field">
        <label for="mensaje">¿Cómo podemos ayudarle? <span class="req">*</span></label>
        <textarea id="mensaje" name="mensaje" required placeholder="Ejemplo: busco una rasqueta para una prensa Mark Andy que trabaja con tinta base agua…"></textarea>
      </div>

      <div class="form__channel" role="radiogroup" aria-label="¿Cómo prefiere recibir la respuesta?">
        <p>¿Cómo prefiere recibir la respuesta?</p>
        <div class="form__channel-options">
          <button class="form__channel-opt is-active" type="button" data-channel="email" aria-pressed="true"><?= icono('mail') ?>Correo</button>
          <button class="form__channel-opt" type="button" data-channel="wa" aria-pressed="false"><?= iconoRelleno('wa') ?>WhatsApp</button>
        </div>
      </div>

      <button class="btn btn--primary contact-form__submit" type="submit">Enviar solicitud <?= icono('arrow') ?></button>
      <p class="contact-form__privacy"><?= icono('check') ?>El sitio no almacena estos datos: se abrirá su correo o WhatsApp con el mensaje preparado.</p>
    </form>

    <aside class="contact-side" data-reveal="right" aria-label="Otras formas de contactar a Flexuministros">
      <div class="contact-side__body">
        <p class="contact-side__eyebrow">Contacto directo <span class="contact-side__status"><i></i>Disponibles</span></p>
        <p class="contact-side__title">Estamos para ayudarle</p>
        <p class="contact-side__intro">Elija el canal que le resulte más cómodo. Para una respuesta inmediata, escríbanos por WhatsApp.</p>
        <a class="contact-side__wa" href="<?= enlaceWhatsApp('Hola Flexuministros, necesito asesoría para mi proceso de impresión.') ?>" target="_blank" rel="noopener">
          <span class="contact-side__wa-icon"><?= iconoRelleno('wa') ?></span>
          <span><strong>Escribir por WhatsApp</strong><small>Respuesta más rápida · <?= esc($horario) ?></small></span>
          <?= icono('arrow') ?>
        </a>

        <ul class="contact-side__list">
          <li><a href="tel:+52<?= esc($telPrincipal) ?>"><?= icono('phone') ?><span><b><?= esc($telPrincipalFmt) ?></b><small>Atención telefónica</small></span></a></li>
          <li><a href="mailto:<?= esc($correoVentas) ?>"><?= icono('mail') ?><span><b><?= esc($correoVentas) ?></b><small>Cotizaciones</small></span></a></li>
          <li><a href="<?= $mapsUrl ?>" target="_blank" rel="noopener"><?= icono('pin') ?><span><b><?= esc($calle) ?></b><small><?= esc($ciudad) ?>, <?= esc($estado) ?></small></span></a></li>
          <li class="contact-side__hours"><?= icono('clock') ?><span><b><?= esc($horario) ?></b></span></li>
        </ul>

        <details class="contact-directory">
          <summary>Ver todos los teléfonos y correos <?= icono('chevron') ?></summary>
          <div class="contact-directory__body">
            <div><b>Teléfonos</b><?php foreach ($telefonos as [$etiqueta, $numero]): ?><a href="tel:+52<?= esc($numero) ?>"><?= esc($etiqueta) ?></a><?php endforeach; ?></div>
            <div><b>Correos</b><?php foreach ($correos as [$direccion, $rol]): ?><a href="mailto:<?= esc($direccion) ?>"><?= esc($direccion) ?><small><?= esc($rol) ?></small></a><?php endforeach; ?></div>
          </div>
        </details>
      </div>

      <div class="map-card contact-side__map">
        <button class="map-card__facade" type="button" data-src="<?= esc($mapsEmbed) ?>">
          <span class="map-card__marker"><?= icono('pin') ?></span>
          <strong><?= esc($calle) ?></strong>
          <span><?= esc($ciudad) ?>, <?= esc($estado) ?> <?= esc($cp) ?></span>
          <small>Toque para cargar el mapa</small>
        </button>
      </div>
    </aside>
  </div>
</section>
<?php
$body = ob_get_clean();

echo pagina([
  'id' => 'contacto',
  'title' => 'Contacto | Flexuministros — Guadalajara, Jalisco',
  'description' => "Contacte a Flexuministros en Guadalajara: teléfono {$telPrincipalFmt}, WhatsApp y correo {$correoVentas}. Cotizaciones y asesoría técnica sin compromiso.",
  'canonical' => '/contacto.html',
  'jsonld' => [
    crumbs_jsonld([['Inicio', '/'], ['Contacto', '/contacto.html']]),
    ['@context' => 'https://schema.org', '@type' => 'ContactPage', 'name' => 'Contacto Flexuministros', 'url' => SITIO_URL . '/contacto.html'],
  ],
  'body' => $body,
]);
