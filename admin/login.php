<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/../inc/datos.php';
require_once __DIR__ . '/../inc/iconos.php';

if (usuarioActual()) {
  header('Location: /admin/index.php');
  exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $usuario = trim($_POST['usuario'] ?? '');
  $contrasena = $_POST['contrasena'] ?? '';
  if (iniciarSesion($usuario, $contrasena)) {
    header('Location: /admin/index.php');
    exit;
  }
  $error = 'Usuario o contraseña incorrectos.';
}
?>
<!doctype html>
<html lang="es-MX">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Iniciar sesión · Panel Flexuministros</title>
<style>
*{box-sizing:border-box}
body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:#171511;color:#22211E;min-height:100vh;display:grid;place-items:center}
.caja{background:#fff;border-radius:16px;padding:2.25rem;width:22rem;max-width:92vw}
.caja h1{font-size:1.3rem;margin:0 0 .3rem}
.caja p{color:#77736B;font-size:.88rem;margin:0 0 1.5rem}
label{display:block;font-weight:700;font-size:.85rem;margin:0 0 .35rem}
input{width:100%;padding:.65rem .8rem;border:1px solid #DDD7CD;border-radius:9px;font:inherit;margin-bottom:1rem}
button{width:100%;padding:.75rem;border:0;border-radius:9px;background:#F57F01;color:#171511;font-weight:800;font-size:.95rem;cursor:pointer}
button:hover{background:#FF8B12}
.error{background:#FBEAE5;color:#B23A24;border:1px solid #E4B4A4;padding:.7rem .9rem;border-radius:9px;font-size:.85rem;margin-bottom:1rem;font-weight:600}
.campo-contrasena{position:relative}
.campo-contrasena input{padding-right:2.6rem}
.campo-contrasena__ojo{position:absolute;top:0;right:0;width:2.6rem;height:2.6rem;display:grid;place-items:center;background:none;border:0;cursor:pointer;color:#77736B;padding:0}
.campo-contrasena__ojo:hover{color:#22211E;background:none}
.campo-contrasena__ojo svg{width:1.15rem;height:1.15rem}
</style>
</head>
<body>
  <form class="caja" method="post" autocomplete="off">
    <h1>Panel Flexuministros</h1>
    <p>Acceso solo para personal autorizado.</p>
    <?php if ($error): ?><div class="error"><?= esc($error) ?></div><?php endif; ?>
    <label for="usuario">Usuario</label>
    <input id="usuario" name="usuario" type="text" required autofocus>
    <label for="contrasena">Contraseña</label>
    <div class="campo-contrasena">
      <input id="contrasena" name="contrasena" type="password" required>
      <button type="button" class="campo-contrasena__ojo" onclick="alternarContrasena(this)" tabindex="-1" aria-label="Mostrar contraseña"><?= icono('eye') ?></button>
    </div>
    <button type="submit">Entrar</button>
  </form>
  <script>function alternarContrasena(boton){var campo=boton.previousElementSibling;var visible=campo.type==="text";campo.type=visible?"password":"text";boton.innerHTML=<?= json_encode(icono('eye')) ?>;if(!visible)boton.innerHTML=<?= json_encode(icono('eye-off')) ?>;}</script>
</body>
</html>
