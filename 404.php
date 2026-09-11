<?php
require_once __DIR__ . '/inc/partials.php';
http_response_code(404);

$body = '<section class="section center">
    <div class="container" style="max-width:38rem">
      <p class="eyebrow" style="justify-content:center">Error 404</p>
      <h1>No encontramos esta página</h1>
      <p class="lead">Puede que el enlace haya cambiado. Le dejamos dos atajos útiles.</p>
      <p style="display:flex;gap:.75rem;justify-content:center;flex-wrap:wrap;margin-top:2rem">
        <a class="btn btn--primary" href="/">Ir al inicio</a>
        <a class="btn btn--ghost" href="/productos.html">Ver catálogo</a>
      </p>
    </div>
  </section>';

echo pagina([
  'id' => '',
  'title' => 'Página no encontrada | Flexuministros',
  'description' => 'La página que busca no existe o cambió de dirección. Vuelva al inicio o consulte el catálogo de suministros para impresión flexográfica.',
  'canonical' => '/404.html',
  'jsonld' => [],
  'body' => $body,
]);
