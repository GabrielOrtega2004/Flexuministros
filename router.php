<?php
/* Enrutador solo para pruebas locales con el servidor embebido de PHP
   (php -S), que NO lee .htaccess. Reproduce las mismas reglas que Apache
   usa en producción, para que las pruebas locales coincidan con el sitio
   real. No se usa ni se sube al hosting real (ahí manda .htaccess). */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

foreach (['/_src', '/inc', '/admin/inc', '/sql'] as $prefijo) {
  if ($uri === $prefijo || strpos($uri, $prefijo . '/') === 0) {
    http_response_code(403);
    echo 'Prohibido';
    return true;
  }
}

$mapa = [
  '/' => '/index.php',
  '/nosotros.html' => '/nosotros.php',
  '/productos.html' => '/productos.php',
  '/equipos.html' => '/equipos.php',
  '/servicios.html' => '/servicios.php',
  '/contacto.html' => '/contacto.php',
];
if (isset($mapa[$uri])) {
  require __DIR__ . $mapa[$uri];
  return true;
}

$archivo = __DIR__ . $uri;
if ($uri !== '/' && file_exists($archivo)) {
  return false;
}

http_response_code(404);
require __DIR__ . '/404.php';
