<?php
/* Validación y guardado de imágenes subidas desde el panel. */
require_once __DIR__ . '/../../inc/db.php';

const TIPOS_IMAGEN_PERMITIDOS = [
  'image/jpeg' => 'jpg',
  'image/png' => 'png',
  'image/webp' => 'webp',
  'image/gif' => 'gif',
];
const TAMANO_MAXIMO_BYTES = 4 * 1024 * 1024; // 4 MB

/**
 * Procesa un campo <input type="file"> y regresa ['ok'=>bool, 'archivo'=>string|null,
 * 'ancho'=>int|null, 'alto'=>int|null, 'error'=>string|null].
 * $carpeta: 'productos' | 'equipos' | 'servicios' | 'marcas_servicio'.
 */
function procesarImagenSubida(string $campo, string $carpeta): array {
  if (empty($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
    return ['ok' => true, 'archivo' => null, 'ancho' => null, 'alto' => null, 'error' => null];
  }
  $f = $_FILES[$campo];

  if ($f['error'] !== UPLOAD_ERR_OK) {
    return ['ok' => false, 'archivo' => null, 'ancho' => null, 'alto' => null, 'error' => 'No se pudo subir el archivo.'];
  }
  if ($f['size'] > TAMANO_MAXIMO_BYTES) {
    return ['ok' => false, 'archivo' => null, 'ancho' => null, 'alto' => null, 'error' => 'La imagen pesa más de 4 MB.'];
  }

  // No confiar en el nombre/extensión: se valida el contenido real del archivo.
  $info = @getimagesize($f['tmp_name']);
  if ($info === false || !isset(TIPOS_IMAGEN_PERMITIDOS[$info['mime']])) {
    return ['ok' => false, 'archivo' => null, 'ancho' => null, 'alto' => null, 'error' => 'El archivo no es una imagen válida (use JPG, PNG, WEBP o GIF).'];
  }

  $ext = TIPOS_IMAGEN_PERMITIDOS[$info['mime']];
  $nombre = bin2hex(random_bytes(8)) . '.' . $ext;
  $cfg = config();
  $destinoDir = $cfg['uploads_dir'] . '/' . $carpeta;
  if (!is_dir($destinoDir)) mkdir($destinoDir, 0775, true);
  $destino = $destinoDir . '/' . $nombre;

  if (!move_uploaded_file($f['tmp_name'], $destino)) {
    return ['ok' => false, 'archivo' => null, 'ancho' => null, 'alto' => null, 'error' => 'No se pudo guardar la imagen en el servidor.'];
  }

  return ['ok' => true, 'archivo' => $nombre, 'ancho' => $info[0], 'alto' => $info[1], 'error' => null];
}

function eliminarArchivoSubido(?string $archivo, string $carpeta): void {
  if (!$archivo) return;
  $cfg = config();
  $ruta = $cfg['uploads_dir'] . '/' . $carpeta . '/' . $archivo;
  if (is_file($ruta)) @unlink($ruta);
}
