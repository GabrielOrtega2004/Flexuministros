<?php
/* Crea (o actualiza la contraseña de) un usuario del panel administrativo.
   Uso, por línea de comandos:
     C:\xampp\php\php.exe sql\crear_admin.php usuario "contraseña segura"
   Si el usuario ya existe, se le actualiza la contraseña. */

require_once __DIR__ . '/../inc/db.php';

$usuario = $argv[1] ?? null;
$contrasena = $argv[2] ?? null;

if (!$usuario || !$contrasena) {
  echo "Uso: php sql/crear_admin.php usuario \"contraseña\"\n";
  exit(1);
}
if (strlen($contrasena) < 8) {
  echo "La contraseña debe tener al menos 8 caracteres.\n";
  exit(1);
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);
$pdo = conexionBD();

$st = $pdo->prepare('SELECT id FROM usuarios_admin WHERE usuario = ?');
$st->execute([$usuario]);

if ($st->fetch()) {
  $pdo->prepare('UPDATE usuarios_admin SET contrasena_hash = ? WHERE usuario = ?')->execute([$hash, $usuario]);
  echo "Contraseña actualizada para el usuario \"{$usuario}\".\n";
} else {
  $pdo->prepare('INSERT INTO usuarios_admin (usuario, contrasena_hash) VALUES (?, ?)')->execute([$usuario, $hash]);
  echo "Usuario \"{$usuario}\" creado correctamente.\n";
}
echo "Ya puede iniciar sesión en /admin con ese usuario y contraseña.\n";
