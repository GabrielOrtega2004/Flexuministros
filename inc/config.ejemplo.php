<?php
/* Copie este archivo a config.php y ajuste los datos de su hosting.
   config.php NO se sube a git (está en .gitignore) porque contiene
   credenciales reales. */
return [
  'db_host' => 'localhost',
  'db_nombre' => 'flexuministros',
  'db_usuario' => 'root',
  'db_password' => '',
  // Carpeta donde el panel guarda las imágenes subidas (ruta de disco).
  'uploads_dir' => __DIR__ . '/../uploads',
  // Misma carpeta, pero como URL pública.
  'uploads_url' => '/uploads',
];
