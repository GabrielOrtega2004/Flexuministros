<?php
/* Funciones de acceso a datos: todo lo que las páginas públicas y el panel
   necesitan leer/escribir vive aquí, para no repetir SQL por todos lados. */
require_once __DIR__ . '/db.php';

function esc($s): string {
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/* ---------------- Configuración general (clave/valor) ---------------- */

function obtenerConfig(string $clave, string $porDefecto = ''): string {
  $fila = conexionBD()->prepare('SELECT valor FROM configuracion WHERE clave = ?');
  $fila->execute([$clave]);
  $v = $fila->fetchColumn();
  return $v === false ? $porDefecto : $v;
}

function todaConfig(): array {
  $filas = conexionBD()->query('SELECT clave, valor FROM configuracion')->fetchAll();
  $out = [];
  foreach ($filas as $f) $out[$f['clave']] = $f['valor'];
  return $out;
}

function guardarConfig(string $clave, string $valor): void {
  $st = conexionBD()->prepare(
    'INSERT INTO configuracion (clave, valor) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE valor = VALUES(valor)'
  );
  $st->execute([$clave, $valor]);
}

function enlaceWhatsApp(string $texto): string {
  $numero = obtenerConfig('whatsapp_intl', '523321063740');
  return 'https://wa.me/' . $numero . '?text=' . rawurlencode($texto);
}

/* ---------------- Categorías ---------------- */

function obtenerCategorias(): array {
  return conexionBD()->query('SELECT * FROM categorias ORDER BY orden ASC')->fetchAll();
}

function obtenerCategoriaPorId(string $id): ?array {
  $st = conexionBD()->prepare('SELECT * FROM categorias WHERE id = ?');
  $st->execute([$id]);
  $r = $st->fetch();
  return $r ?: null;
}

/* ---------------- Marcas del catálogo ---------------- */

function obtenerMarcas(): array {
  return conexionBD()->query('SELECT * FROM marcas ORDER BY orden ASC')->fetchAll();
}

/* ---------------- Productos ---------------- */

function obtenerProductos(array $opts = []): array {
  $soloVisibles = $opts['solo_visibles'] ?? true;
  $categoriaId = $opts['categoria_id'] ?? null;

  $sql = 'SELECT p.*, c.nombre AS categoria_nombre, m.nombre AS marca_nombre
          FROM productos p
          LEFT JOIN categorias c ON c.id = p.categoria_id
          LEFT JOIN marcas m ON m.id = p.marca_id
          WHERE 1=1';
  $params = [];
  if ($soloVisibles) $sql .= ' AND p.visible = 1';
  if ($categoriaId) { $sql .= ' AND p.categoria_id = ?'; $params[] = $categoriaId; }
  $sql .= ' ORDER BY p.orden ASC, p.id ASC';

  $st = conexionBD()->prepare($sql);
  $st->execute($params);
  return $st->fetchAll();
}

function obtenerProductoPorId(int $id): ?array {
  $st = conexionBD()->prepare('SELECT * FROM productos WHERE id = ?');
  $st->execute([$id]);
  $r = $st->fetch();
  return $r ?: null;
}

function obtenerImagenesProducto(int $productoId): array {
  $st = conexionBD()->prepare('SELECT * FROM producto_imagenes WHERE producto_id = ? ORDER BY orden ASC');
  $st->execute([$productoId]);
  return $st->fetchAll();
}

/* ---------------- Equipos ---------------- */

function obtenerEquipos(bool $soloVisibles = true): array {
  $sql = 'SELECT * FROM equipos' . ($soloVisibles ? ' WHERE visible = 1' : '') . ' ORDER BY orden ASC, id ASC';
  return conexionBD()->query($sql)->fetchAll();
}

function obtenerEquipoPorId(int $id): ?array {
  $st = conexionBD()->prepare('SELECT * FROM equipos WHERE id = ?');
  $st->execute([$id]);
  $r = $st->fetch();
  return $r ?: null;
}

function obtenerImagenesEquipo(int $equipoId): array {
  $st = conexionBD()->prepare('SELECT * FROM equipo_imagenes WHERE equipo_id = ? ORDER BY orden ASC');
  $st->execute([$equipoId]);
  return $st->fetchAll();
}

/* ---------------- Servicios ---------------- */

function obtenerServicios(bool $soloVisibles = true): array {
  $sql = 'SELECT * FROM servicios' . ($soloVisibles ? ' WHERE visible = 1' : '') . ' ORDER BY orden ASC, id ASC';
  return conexionBD()->query($sql)->fetchAll();
}

function obtenerServicioPorId(int $id): ?array {
  $st = conexionBD()->prepare('SELECT * FROM servicios WHERE id = ?');
  $st->execute([$id]);
  $r = $st->fetch();
  return $r ?: null;
}

function obtenerMarcasServicio(bool $soloVisibles = true): array {
  $sql = 'SELECT * FROM marcas_servicio' . ($soloVisibles ? ' WHERE visible = 1' : '') . ' ORDER BY orden ASC, id ASC';
  return conexionBD()->query($sql)->fetchAll();
}
