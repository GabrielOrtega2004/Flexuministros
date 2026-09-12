<?php
require_once __DIR__ . '/inc/auth.php';
require_once __DIR__ . '/../inc/datos.php';
require_once __DIR__ . '/../inc/iconos.php';

if (usuarioActual()) {
  header('Location: /admin/index.php');
  exit;
}

$error = null;
$usuario = '';
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
<link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
<style>
*{box-sizing:border-box}
body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:#171511;color:#22211E;min-height:100vh;display:grid;place-items:center;padding:24px;
  background-image:radial-gradient(60rem 40rem at 18% -10%,rgba(245,127,1,.16),transparent 60%),radial-gradient(50rem 35rem at 100% 100%,rgba(255,255,255,.05),transparent 55%)}
button,input{font:inherit}
button{cursor:pointer}
.envoltura{position:relative}
.resplandor{position:absolute;inset:-1.5rem;background:radial-gradient(circle,rgba(245,127,1,.22),transparent 68%);filter:blur(6px);z-index:0}
.caja{position:relative;z-index:1;background:#fff;border-radius:20px;padding:2.75rem 2.5rem;width:23.5rem;max-width:92vw;box-shadow:0 30px 70px rgba(0,0,0,.45),0 0 0 1px rgba(255,255,255,.06) inset}
.caja>*{opacity:0;animation:surgir .5s cubic-bezier(.16,1,.3,1) both}
.caja>*:nth-child(1){animation-delay:.02s}
.caja>*:nth-child(2){animation-delay:.08s}
.caja>*:nth-child(3){animation-delay:.13s}
.caja>*:nth-child(4){animation-delay:.18s}
.caja>*:nth-child(5){animation-delay:.23s}
.caja>*:nth-child(6){animation-delay:.28s}
.caja>*:nth-child(7){animation-delay:.33s}
.caja>*:nth-child(8){animation-delay:.38s}
.marca{display:flex;align-items:center;gap:.7rem;margin-bottom:1.85rem}
.marca img{width:2.35rem;height:2.35rem}
.marca span{font-weight:800;font-size:1.05rem;letter-spacing:-.01em}
.etiqueta{text-transform:uppercase;letter-spacing:.14em;font-size:.68rem;font-weight:800;color:#C56B12;margin:0 0 .4rem}
h1{font-size:1.4rem;margin:0 0 .35rem;letter-spacing:-.015em}
.caja p.subtitulo{color:#77736B;font-size:.87rem;margin:0 0 1.75rem}
label{display:block;font-weight:700;font-size:.83rem;margin:0 0 .4rem}
.campo{margin-bottom:1.05rem}
.campo__control{position:relative}
.campo__icono{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#9C978C;display:flex;pointer-events:none}
.campo__icono svg{width:1.05rem;height:1.05rem}
input{width:100%;padding:.7rem .8rem .7rem 2.55rem;border:1px solid #DDD7CD;border-radius:10px;background:#fff;color:#22211E;outline:none;transition:border-color .15s,box-shadow .15s}
input:hover{border-color:#C7C1B4}
input:focus{border-color:#F57F01;box-shadow:0 0 0 3px rgba(245,127,1,.16)}
.campo-contrasena input{padding-right:2.6rem}
input[type=password]::-ms-reveal,input[type=password]::-ms-clear{display:none}
.campo-contrasena__ojo{position:absolute;top:0;right:0;bottom:0;width:2.6rem;display:grid;place-items:center;background:none;border:0;cursor:pointer;color:#77736B;padding:0;transition:color .15s}
.campo-contrasena__ojo:hover{color:#22211E}
.campo-contrasena__ojo svg{width:1.1rem;height:1.1rem}
.entrar{width:100%;padding:.8rem;border:0;border-radius:10px;background:#F57F01;color:#171511;font-weight:800;font-size:.95rem;margin-top:.4rem;display:flex;align-items:center;justify-content:center;gap:.5rem;box-shadow:0 8px 20px rgba(245,127,1,.28);transition:background .15s,transform .1s,box-shadow .15s}
.entrar svg{width:1rem;height:1rem;transition:transform .15s}
.entrar:hover{background:#FF8B12;transform:translateY(-1px);box-shadow:0 10px 24px rgba(245,127,1,.4)}
.entrar:hover svg{transform:translateX(3px)}
.entrar:active{transform:translateY(0)}
.error{background:#FBEAE5;color:#B23A24;border:1px solid #E4B4A4;padding:.7rem .9rem;border-radius:10px;font-size:.85rem;margin-bottom:1.1rem;font-weight:600;opacity:1;animation:sacudir .4s}
.volver{display:block;text-align:center;margin-top:1.6rem;font-size:.8rem;color:#948F84;text-decoration:none;transition:color .15s}
.volver:hover{color:#171511}
@keyframes surgir{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
@keyframes sacudir{15%,55%{transform:translateX(-5px)}35%,75%{transform:translateX(5px)}90%{transform:translateX(-2px)}100%{transform:translateX(0)}}
@media(prefers-reduced-motion:reduce){.caja>*,.error{animation:none;opacity:1}.entrar,.entrar svg{transition:none}}
</style>
</head>
<body>
  <div class="envoltura">
    <div class="resplandor" aria-hidden="true"></div>
    <form class="caja" method="post" autocomplete="off">
      <div class="marca"><img src="/assets/img/favicon.svg" alt=""><span>Panel Flexuministros</span></div>
      <p class="etiqueta">Panel administrativo</p>
      <h1>Iniciar sesión</h1>
      <p class="subtitulo">Acceso solo para personal autorizado.</p>
      <?php if ($error): ?><div class="error"><?= esc($error) ?></div><?php endif; ?>
      <div class="campo">
        <label for="usuario">Usuario</label>
        <div class="campo__control">
          <span class="campo__icono" aria-hidden="true"><?= icono('user') ?></span>
          <input id="usuario" name="usuario" type="text" value="<?= esc($usuario) ?>" required autofocus>
        </div>
      </div>
      <div class="campo">
        <label for="contrasena">Contraseña</label>
        <div class="campo__control campo-contrasena">
          <span class="campo__icono" aria-hidden="true"><?= icono('lock') ?></span>
          <input id="contrasena" name="contrasena" type="password" required>
          <button type="button" class="campo-contrasena__ojo" onclick="alternarContrasena(this)" tabindex="-1" aria-label="Mostrar contraseña"><?= icono('eye') ?></button>
        </div>
      </div>
      <button type="submit" class="entrar"><span>Entrar</span><?= icono('arrow') ?></button>
      <a class="volver" href="/">← Volver al sitio</a>
    </form>
  </div>
  <script>function alternarContrasena(boton){var campo=boton.previousElementSibling;var visible=campo.type==="text";campo.type=visible?"password":"text";boton.innerHTML=<?= json_encode(icono('eye')) ?>;if(!visible)boton.innerHTML=<?= json_encode(icono('eye-off')) ?>;}</script>
</body>
</html>
