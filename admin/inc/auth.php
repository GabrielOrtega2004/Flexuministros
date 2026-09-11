<?php
/* Sesión y protección de rutas del panel. Incluir al inicio de cada
   página administrativa (excepto login.php). */
require_once __DIR__ . '/../../inc/db.php';

if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
  session_start();
}

function usuarioActual(): ?string {
  return $_SESSION['admin_usuario'] ?? null;
}

function requiereSesion(): void {
  if (!usuarioActual()) {
    header('Location: /admin/login.php');
    exit;
  }
}

function iniciarSesion(string $usuario, string $contrasena): bool {
  $st = conexionBD()->prepare('SELECT * FROM usuarios_admin WHERE usuario = ?');
  $st->execute([$usuario]);
  $fila = $st->fetch();
  if (!$fila || !password_verify($contrasena, $fila['contrasena_hash'])) return false;
  session_regenerate_id(true);
  $_SESSION['admin_usuario'] = $fila['usuario'];
  return true;
}

function cerrarSesion(): void {
  $_SESSION = [];
  session_destroy();
}

/** Mensaje flash de una sola lectura (para confirmaciones tras guardar/eliminar). */
function ponerMensaje(string $texto, string $tipo = 'ok'): void {
  $_SESSION['flash'] = ['texto' => $texto, 'tipo' => $tipo];
}

function leerMensaje(): ?array {
  if (empty($_SESSION['flash'])) return null;
  $m = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $m;
}
