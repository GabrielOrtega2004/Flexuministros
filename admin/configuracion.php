<?php
require_once __DIR__ . '/inc/layout.php';
require_once __DIR__ . '/inc/subir_imagen.php';
requiereSesion();

$pdo = conexionBD();

$camposTexto = [
  'telefono_principal', 'telefono_principal_formato', 'whatsapp_intl', 'whatsapp_formato',
  'correo_info', 'correo_ventas', 'correo_atencion',
  'direccion_calle', 'direccion_ciudad', 'direccion_estado', 'direccion_cp', 'horario',
  'facebook_url', 'tiktok_url', 'maps_url', 'maps_embed_url',
  'hero_badge', 'hero_titulo_1', 'hero_titulo_2', 'hero_texto',
  'quienes_somos_titulo', 'quienes_somos_texto', 'mision_texto',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($camposTexto as $clave) {
    if (isset($_POST[$clave])) guardarConfig($clave, trim($_POST[$clave]));
  }
  foreach ([1, 2] as $n) {
    $subida = procesarImagenSubida("hero_imagen_{$n}_archivo", 'configuracion');
    if ($subida['ok'] && $subida['archivo']) {
      guardarConfig("hero_imagen_{$n}", $subida['archivo']);
      guardarConfig("hero_imagen_{$n}_ancho", (string)$subida['ancho']);
      guardarConfig("hero_imagen_{$n}_alto", (string)$subida['alto']);
    }
  }
  foreach (['productos', 'nosotros', 'equipos', 'servicios'] as $pagina) {
    $subida = procesarImagenSubida("{$pagina}_hero_imagen_archivo", 'configuracion');
    if ($subida['ok'] && $subida['archivo']) {
      guardarConfig("{$pagina}_hero_imagen", $subida['archivo']);
      guardarConfig("{$pagina}_hero_ancho", (string)$subida['ancho']);
      guardarConfig("{$pagina}_hero_alto", (string)$subida['alto']);
    }
  }
  $subida = procesarImagenSubida('ctaband_imagen_archivo', 'configuracion');
  if ($subida['ok'] && $subida['archivo']) {
    guardarConfig('ctaband_imagen', $subida['archivo']);
    guardarConfig('ctaband_ancho', (string)$subida['ancho']);
    guardarConfig('ctaband_alto', (string)$subida['alto']);
  }
  ponerMensaje('Configuración guardada correctamente.');
  header('Location: /admin/configuracion.php');
  exit;
}

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;

panelAbrir('Configuración', 'configuracion');
?>
<div class="panel__encabezado"><h1>Configuración general</h1></div>
<p style="color:#66625B;margin-top:-1rem;margin-bottom:1.5rem">Estos datos aparecen en varias partes del sitio (encabezado, pie de página, formulario de contacto, botones de WhatsApp).</p>

<form method="post" enctype="multipart/form-data">
  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Contacto</h2>
    <div class="form__fila">
      <div class="form__campo"><label for="telefono_principal_formato">Teléfono principal (para mostrar)</label><input id="telefono_principal_formato" name="telefono_principal_formato" type="text" value="<?= esc($c('telefono_principal_formato')) ?>" placeholder="33 3838 7947"></div>
      <div class="form__campo"><label for="telefono_principal">Teléfono principal (solo números)</label><input id="telefono_principal" name="telefono_principal" type="text" value="<?= esc($c('telefono_principal')) ?>" placeholder="3338387947"></div>
    </div>
    <div class="form__fila">
      <div class="form__campo"><label for="whatsapp_formato">WhatsApp (para mostrar)</label><input id="whatsapp_formato" name="whatsapp_formato" type="text" value="<?= esc($c('whatsapp_formato')) ?>" placeholder="33 2106 3740"></div>
      <div class="form__campo"><label for="whatsapp_intl">WhatsApp (con código de país, solo números)</label><input id="whatsapp_intl" name="whatsapp_intl" type="text" value="<?= esc($c('whatsapp_intl')) ?>" placeholder="523321063740"></div>
    </div>
    <div class="form__fila">
      <div class="form__campo"><label for="correo_info">Correo — información general</label><input id="correo_info" name="correo_info" type="text" value="<?= esc($c('correo_info')) ?>"></div>
      <div class="form__campo"><label for="correo_ventas">Correo — ventas y cotizaciones</label><input id="correo_ventas" name="correo_ventas" type="text" value="<?= esc($c('correo_ventas')) ?>"></div>
    </div>
    <div class="form__campo"><label for="horario">Horario de atención</label><input id="horario" name="horario" type="text" value="<?= esc($c('horario')) ?>"></div>
  </div>

  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Dirección</h2>
    <div class="form__campo"><label for="direccion_calle">Calle y número</label><input id="direccion_calle" name="direccion_calle" type="text" value="<?= esc($c('direccion_calle')) ?>"></div>
    <div class="form__fila">
      <div class="form__campo"><label for="direccion_ciudad">Ciudad</label><input id="direccion_ciudad" name="direccion_ciudad" type="text" value="<?= esc($c('direccion_ciudad')) ?>"></div>
      <div class="form__campo"><label for="direccion_estado">Estado</label><input id="direccion_estado" name="direccion_estado" type="text" value="<?= esc($c('direccion_estado')) ?>"></div>
    </div>
    <div class="form__fila">
      <div class="form__campo"><label for="direccion_cp">Código postal</label><input id="direccion_cp" name="direccion_cp" type="text" value="<?= esc($c('direccion_cp')) ?>"></div>
      <div class="form__campo"><label for="maps_url">Enlace de Google Maps</label><input id="maps_url" name="maps_url" type="text" value="<?= esc($c('maps_url')) ?>"></div>
    </div>
  </div>

  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Redes sociales</h2>
    <div class="form__fila">
      <div class="form__campo"><label for="facebook_url">Facebook</label><input id="facebook_url" name="facebook_url" type="text" value="<?= esc($c('facebook_url')) ?>"></div>
      <div class="form__campo"><label for="tiktok_url">TikTok</label><input id="tiktok_url" name="tiktok_url" type="text" value="<?= esc($c('tiktok_url')) ?>"></div>
    </div>
  </div>

  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Portada del inicio</h2>
    <div class="form__campo"><label for="hero_badge">Etiqueta pequeña (arriba del título)</label><input id="hero_badge" name="hero_badge" type="text" value="<?= esc($c('hero_badge')) ?>"></div>
    <div class="form__fila">
      <div class="form__campo"><label for="hero_titulo_1">Título — línea 1</label><input id="hero_titulo_1" name="hero_titulo_1" type="text" value="<?= esc($c('hero_titulo_1')) ?>"></div>
      <div class="form__campo"><label for="hero_titulo_2">Título — línea 2 (en naranja)</label><input id="hero_titulo_2" name="hero_titulo_2" type="text" value="<?= esc($c('hero_titulo_2')) ?>"></div>
    </div>
    <div class="form__campo"><label for="hero_texto">Texto principal</label><textarea id="hero_texto" name="hero_texto"><?= esc($c('hero_texto')) ?></textarea></div>
    <div class="form__fila">
      <div class="form__campo">
        <label>Foto de fondo 1</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('hero_imagen_1')) ?>" alt="">
        <input name="hero_imagen_1_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
      <div class="form__campo">
        <label>Foto de fondo 2</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('hero_imagen_2')) ?>" alt="">
        <input name="hero_imagen_2_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
    </div>
    <p class="form__ayuda">El carrusel del inicio alterna entre estas dos fotos.</p>
    <div class="form__campo" style="margin-top:1rem">
      <label>Foto del bloque "¿Necesitas algo en específico?"</label>
      <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('ctaband_imagen', 'flexo-industrial-v2.webp')) ?>" alt="">
      <input name="ctaband_imagen_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      <p class="form__ayuda">Aparece al final de todas las páginas, junto al botón de cotización.</p>
    </div>
  </div>

  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Fotos de portada de cada sección</h2>
    <p class="form__ayuda" style="margin-top:-.4rem">Foto grande que aparece arriba de cada página.</p>
    <div class="form__fila">
      <div class="form__campo">
        <label>Productos</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('productos_hero_imagen', 'catalogo-v3-hd.webp')) ?>" alt="">
        <input name="productos_hero_imagen_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
      <div class="form__campo">
        <label>Nosotros</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('nosotros_hero_imagen', 'nosotros-equipo-flexo-2-v2.webp')) ?>" alt="">
        <input name="nosotros_hero_imagen_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
    </div>
    <div class="form__fila">
      <div class="form__campo">
        <label>Equipos</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('equipos_hero_imagen', 'hero-flexo-3-restaurada-v2.webp')) ?>" alt="">
        <input name="equipos_hero_imagen_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
      <div class="form__campo">
        <label>Servicios</label>
        <img class="form__vista-imagen" src="/uploads/configuracion/<?= esc($c('servicios_hero_imagen', 'servicios-hero-v1.webp')) ?>" alt="">
        <input name="servicios_hero_imagen_archivo" type="file" accept="image/png,image/jpeg,image/webp,image/gif">
      </div>
    </div>
  </div>

  <div class="tarjeta" style="margin-bottom:1.25rem">
    <h2 style="margin-top:0;font-size:1.1rem">Quiénes somos / Misión</h2>
    <div class="form__campo"><label for="quienes_somos_titulo">Título de "Quiénes somos"</label><input id="quienes_somos_titulo" name="quienes_somos_titulo" type="text" value="<?= esc($c('quienes_somos_titulo')) ?>"></div>
    <div class="form__campo"><label for="quienes_somos_texto">Texto de "Quiénes somos"</label><textarea id="quienes_somos_texto" name="quienes_somos_texto"><?= esc($c('quienes_somos_texto')) ?></textarea></div>
    <div class="form__campo"><label for="mision_texto">Misión</label><textarea id="mision_texto" name="mision_texto"><?= esc($c('mision_texto')) ?></textarea><p class="form__ayuda">Se usa en Inicio y en Nosotros.</p></div>
  </div>

  <button class="btn btn--primario" type="submit"><?= icono('check') ?>Guardar configuración</button>
</form>
<?php
panelCerrar();
