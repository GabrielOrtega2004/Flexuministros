<?php
require_once __DIR__ . '/inc/partials.php';
require_once __DIR__ . '/inc/tarjetas.php';

$cfg = todaConfig();
$c = fn(string $clave, string $porDefecto = '') => $cfg[$clave] ?? $porDefecto;
$anioFundacion = $c('fundacion_anio', '1996');
$mision = $c('mision_texto', 'Brindar a nuestros clientes soluciones integrales mediante productos innovadores, equipos confiables y asesoría técnica especializada que contribuyan al éxito y crecimiento de sus negocios de impresión.');
$marcas = obtenerMarcas();
$representantes = obtenerMarcasServicio(true);

ob_start();
?>
<section class="pagehero pagehero--about">
  <div class="pagehero__media" aria-hidden="true">
    <img src="/uploads/configuracion/<?= esc($c('nosotros_hero_imagen', 'nosotros-equipo-flexo-2-v2.webp')) ?>" width="<?= (int)$c('nosotros_hero_ancho', '1606') ?>" height="<?= (int)$c('nosotros_hero_alto', '979') ?>" alt="" fetchpriority="high" decoding="async">
  </div>
  <div class="pagehero__veil" aria-hidden="true"></div>
  <div class="pagehero__grid" aria-hidden="true"></div>
  <div class="container">
    <?= crumbs_html([['Inicio', '/'], ['Nosotros', '/nosotros.html']]) ?>
    <h1>Más de dos décadas resolviendo el piso de producción</h1>
    <p>Somos una compañía mexicana especializada en soluciones integrales para contribuir a la optimización de procesos productivos en la industria de impresión flexográfica.</p>
    <div class="pagehero__actions">
      <a class="btn btn--primary" href="/productos.html">Ver nuestros productos <?= icono('arrow') ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid--2" style="gap:clamp(2rem,5vw,4rem);align-items:start">
      <div class="prose" data-reveal="left">
        <p class="eyebrow">Nuestra historia</p>
        <h2>Fundada en <?= esc($anioFundacion) ?>, respaldada por más de 30 años de experiencia en la industria flexográfica</h2>
        <p>Flexuministros fue fundada en <?= esc($anioFundacion) ?> en México (Guadalajara) con el objetivo de acercar a los impresores mexicanos: productos, tecnología y conocimiento especializado, respaldados por décadas de experiencia en la industria flexográfica.</p>
        <p>A lo largo de los años hemos desarrollado una oferta integral que acompaña distintas etapas del proceso productivo: preparación de tintas, montaje de placas, impresión, suajado, limpieza de rodillos anilox, control de calidad, empaque y herramientas para el control de producción.</p>
        <p>Además de suministros y consumibles especializados, comercializamos equipos nuevos y usados, herramentales y soluciones técnicas, ofreciendo asesoría para seleccionar la alternativa más adecuada de acuerdo con productividad, desempeño, calidad y presupuesto.</p>
        <p>Trabajamos con marcas y fabricantes reconocidos mundialmente, incorporando tecnología, productos y soluciones confiables para atender tanto a empresas consolidadas como a nuevos proyectos que se preparan para incursionar en la industria flexográfica.</p>
        <p>Nuestro objetivo es contribuir a fortalecer su productividad, calidad y competitividad, acompañándolos desde el inicio y a lo largo de su crecimiento.</p>
      </div>

      <figure class="quote" data-reveal="right" style="margin:0">
        <span class="quote__mark" aria-hidden="true">&ldquo;</span>
        <blockquote><?= $mision ?></blockquote>
        <figcaption>Misión Flexuministros</figcaption>
      </figure>
    </div>
  </div>
</section>

<section class="section principles" aria-labelledby="mv-h">
  <div class="container">
    <div class="section-head center principles__head" data-reveal>
      <p class="eyebrow">Lo que nos guía</p>
      <h2 id="mv-h">Misión, visión y valores</h2>
    </div>
    <div class="principles__grid">
      <article class="principle" data-reveal="zoom" style="--d:0ms">
        <div class="principle__top"><span class="principle__icon"><?= icono('target') ?></span><span class="principle__number" aria-hidden="true">01</span></div>
        <span class="principle__rule" aria-hidden="true"></span>
        <h3>Misión</h3>
        <p><?= $mision ?></p>
      </article>
      <article class="principle" data-reveal="zoom" style="--d:100ms">
        <div class="principle__top"><span class="principle__icon"><?= icono('eye') ?></span><span class="principle__number" aria-hidden="true">02</span></div>
        <span class="principle__rule" aria-hidden="true"></span>
        <h3>Visión</h3>
        <p>Convertirnos en líderes de la comercialización de suministros, equipos y soluciones innovadoras para impresión, satisfaciendo las necesidades de calidad, servicio y precio para todos nuestros clientes, comprometidos con el desarrollo constante de la empresa, de nuestros colaboradores y el beneficio social.</p>
      </article>
      <article class="principle" data-reveal="zoom" style="--d:200ms">
        <div class="principle__top"><span class="principle__icon"><?= icono('spark') ?></span><span class="principle__number" aria-hidden="true">03</span></div>
        <span class="principle__rule" aria-hidden="true"></span>
        <h3>Valores</h3>
        <div class="principle__value">
          <h4>Enfoque al cliente</h4>
          <p>El cliente es lo más importante: a él nos debemos y por lo tanto sus necesidades determinarán la calidad de nuestros productos y servicios.</p>
        </div>
        <div class="principle__value">
          <h4>Trabajo en equipo</h4>
          <p>Ambiente de confianza, intercambio de ideas, respeto y comunicación entre todos, para unificar esfuerzos, con la finalidad de mejorar todo lo que hacemos.</p>
        </div>
        <div class="principle__value">
          <h4>Innovación</h4>
          <p>Mantenernos siempre a la vanguardia para ser mejores y satisfacer las necesidades de nuestros clientes.</p>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="section workflow" aria-labelledby="serv-h">
  <div class="container">
    <div class="workflow__layout">
      <div class="section-head workflow__head" data-reveal="left">
        <p class="eyebrow">Cómo trabajamos</p>
        <h2 id="serv-h">Consultoría antes que catálogo</h2>
        <p class="lead">Cada prensa, sustrato y tinta plantea un problema distinto. Nuestro trabajo empieza entendiendo el suyo.</p>
        <a class="workflow__cta" href="/contacto.html">Cuéntenos su reto <?= icono('arrow') ?></a>
      </div>
      <div class="workflow__steps" aria-label="Proceso de trabajo en cuatro etapas">
        <article class="workflow__step" data-reveal="right" style="--d:0ms">
          <span class="workflow__number" aria-hidden="true">01</span>
          <span class="workflow__marker"><span class="workflow__icon"><?= icono('users') ?></span></span>
          <div><h3>Diagnóstico del proceso</h3><p>Revisamos con usted dónde se pierde tiempo o material: cambios de trabajo, limpieza de herramentales, montaje de grabados, mermas, etc.</p></div>
        </article>
        <article class="workflow__step" data-reveal="right" style="--d:90ms">
          <span class="workflow__number" aria-hidden="true">02</span>
          <span class="workflow__marker"><span class="workflow__icon"><?= icono('wrench') ?></span></span>
          <div><h3>Recomendación técnica</h3><p>Proponemos el producto y la especificación correcta en base a sus procesos y equipos con los que cuenta.</p></div>
        </article>
        <article class="workflow__step" data-reveal="right" style="--d:180ms">
          <span class="workflow__number" aria-hidden="true">03</span>
          <span class="workflow__marker"><span class="workflow__icon"><?= icono('leaf') ?></span></span>
          <div><h3>Alternativas sustentables</h3><p>Cuando existe una opción biodegradable, reutilizable o que reduce el impacto ambiental, la ponemos sobre su mesa.</p></div>
        </article>
        <article class="workflow__step" data-reveal="right" style="--d:270ms">
          <span class="workflow__number" aria-hidden="true">04</span>
          <span class="workflow__marker"><span class="workflow__icon"><?= icono('truck') ?></span></span>
          <div><h3>Suministro y seguimiento</h3><p>Abastecemos de forma recurrente y damos seguimiento al desempeño del producto en piso, para ajustar/mejorar lo que haga falta…</p></div>
        </article>
      </div>
    </div>
  </div>
</section>

<section class="section section--alt" aria-labelledby="marcas-h">
  <div class="container">
    <div class="section-head center" data-reveal>
      <p class="eyebrow">Alianzas</p>
      <h2 id="marcas-h">Marcas que representamos</h2>
      <p class="lead">Soluciones respaldadas por marcas líderes a nivel mundial, reconocidas por su calidad, innovación y desempeño.</p>
    </div>
    <?= marcas_html($marcas) ?>
  </div>
</section>

<?php if ($representantes): ?>
<section class="section section--tight svc-brands">
  <div class="container">
    <div class="section-head center" data-reveal>
      <h2>Representantes autorizados para México</h2>
      <p class="lead">Representación directa de fabricantes internacionales, brindando respaldo comercial, técnico y especializado en todo México.</p>
    </div>
    <div class="grid grid--3">
      <?php foreach ($representantes as $m): ?>
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
  'id' => 'nosotros',
  'title' => 'Nosotros | Flexuministros — Más de 20 años en impresión flexográfica',
  'description' => 'Conozca a Flexuministros: compañía mexicana fundada en ' . $anioFundacion . ', especializada en consultoría y suministros de bajo impacto ambiental para la industria de impresión flexográfica.',
  'canonical' => '/nosotros.html',
  'jsonld' => [crumbs_jsonld([['Inicio', '/'], ['Nosotros', '/nosotros.html']])],
  'body' => $body,
]);
