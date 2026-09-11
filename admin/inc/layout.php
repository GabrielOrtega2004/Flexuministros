<?php
/* Estructura visual compartida del panel: mismos colores y tipografía del
   sitio público, pero con su propio layout simple (barra lateral + tabla o
   formulario), separado del diseño del sitio público. */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../inc/iconos.php';
require_once __DIR__ . '/../../inc/datos.php';

const ADMIN_CSS = <<<CSS
*{box-sizing:border-box}
body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:#F6F3ED;color:#22211E}
a{color:inherit}
.panel{display:flex;min-height:100vh}
.panel__lateral{width:15rem;flex:none;background:#171511;color:#E8E5DE;padding:1.5rem 1rem;display:flex;flex-direction:column}
.panel__logo{display:flex;align-items:center;gap:.6rem;font-weight:800;font-size:1.05rem;margin-bottom:2rem;color:#fff;text-decoration:none}
.panel__logo img{width:2rem;height:2rem}
.panel__nav{display:flex;flex-direction:column;gap:.25rem;flex:1}
.panel__nav a{display:flex;align-items:center;gap:.65rem;padding:.65rem .8rem;border-radius:9px;text-decoration:none;color:#C7C3BA;font-weight:600;font-size:.92rem}
.panel__nav a svg{width:1.1rem;height:1.1rem;flex:none}
.panel__nav a:hover{background:rgba(255,255,255,.07);color:#fff}
.panel__nav a.activo{background:#F57F01;color:#171511}
.panel__pie{border-top:1px solid rgba(255,255,255,.12);padding-top:1rem;margin-top:1rem;font-size:.82rem;color:#948F84}
.panel__pie a{color:#E8E5DE;text-decoration:underline}
.panel__cuerpo{flex:1;padding:2rem clamp(1.25rem,4vw,3rem);max-width:76rem}
.panel__encabezado{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap}
.panel__encabezado h1{margin:0;font-size:1.55rem;letter-spacing:-.02em}
.btn{display:inline-flex;align-items:center;gap:.4rem;border:1px solid transparent;border-radius:9px;padding:.6rem 1.1rem;font-weight:700;font-size:.88rem;cursor:pointer;text-decoration:none}
.btn--primario{background:#F57F01;color:#171511}
.btn--primario:hover{background:#FF8B12}
.btn--fantasma{background:#fff;border-color:#E0DAD0;color:#22211E}
.btn--fantasma:hover{border-color:#F57F01}
.btn--peligro{background:#fff;border-color:#E4B4A4;color:#B23A24}
.btn--peligro:hover{background:#FBEAE5}
.btn svg{width:1rem;height:1rem}
.aviso{padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;font-weight:600;font-size:.9rem}
.aviso--ok{background:#E4F5E9;color:#166534;border:1px solid #B7E4C7}
.aviso--error{background:#FBEAE5;color:#B23A24;border:1px solid #E4B4A4}
.tarjeta{background:#fff;border:1px solid #E7E1D7;border-radius:14px;padding:1.5rem}
.tabla-scroll{overflow-x:auto}
table{width:100%;min-width:38rem;border-collapse:collapse;font-size:.9rem}
th{text-align:left;padding:.6rem .7rem;border-bottom:2px solid #E7E1D7;color:#66625B;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em}
td{padding:.65rem .7rem;border-bottom:1px solid #EFEDE8;vertical-align:middle}
tr:last-child td{border-bottom:0}
.tabla-img{width:3rem;height:3rem;border-radius:8px;object-fit:cover;background:#F0EDE7;display:block}
.tabla-acciones{display:flex;gap:.4rem;white-space:nowrap}
.tabla-acciones .btn{padding:.4rem .65rem;font-size:.8rem}
.etiqueta{display:inline-block;padding:.2rem .55rem;border-radius:999px;font-size:.74rem;font-weight:700}
.etiqueta--si{background:#E4F5E9;color:#166534}
.etiqueta--no{background:#F0EDE7;color:#77736B}
.form__campo{margin-bottom:1.1rem}
.form__campo label{display:block;font-weight:700;font-size:.85rem;margin-bottom:.35rem}
.form__ayuda{font-size:.78rem;color:#77736B;margin-top:.3rem}
input[type=text],input[type=number],input[type=password],textarea,select{
  width:100%;padding:.6rem .75rem;border:1px solid #DDD7CD;border-radius:9px;font:inherit;background:#fff
}
textarea{min-height:6rem;resize:vertical}
.form__fila{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form__vista-imagen{width:8rem;height:6rem;object-fit:cover;border-radius:9px;background:#F0EDE7;margin-bottom:.5rem;display:block}
.checkbox{display:flex;align-items:center;gap:.5rem;font-weight:600}
.checkbox input{width:auto}
.campo-contrasena{position:relative}
.campo-contrasena input{padding-right:2.6rem}
.campo-contrasena__ojo{position:absolute;top:0;right:0;bottom:0;width:2.6rem;display:grid;place-items:center;background:none;border:0;cursor:pointer;color:#77736B;padding:0}
.campo-contrasena__ojo:hover{color:#22211E}
.campo-contrasena__ojo svg{width:1.15rem;height:1.15rem}
.panel__login{min-height:100vh;display:grid;place-items:center;background:#171511}
.login-caja{background:#fff;border-radius:16px;padding:2.25rem;width:22rem;max-width:92vw}
.login-caja h1{font-size:1.3rem;margin:0 0 .3rem}
.login-caja p{color:#77736B;font-size:.88rem;margin:0 0 1.5rem}
@media (max-width:760px){
  .panel{flex-direction:column}
  .panel__lateral{width:100%;flex-direction:row;align-items:center;padding:1rem}
  .panel__logo{margin-bottom:0}
  .panel__nav{flex-direction:row;overflow-x:auto}
  .panel__pie{display:none}
  .form__fila{grid-template-columns:1fr}
}
CSS;

function panelAbrir(string $titulo, string $activo): void {
  $mensaje = leerMensaje();
  $usuario = esc(usuarioActual() ?? '');
  $enlaces = [
    ['inicio', '/admin/index.php', 'Inicio', 'target'],
    ['equipos', '/admin/equipos.php', 'Equipos', 'box'],
    ['productos', '/admin/productos.php', 'Productos', 'tray'],
    ['servicios', '/admin/servicios.php', 'Servicios', 'wrench'],
    ['configuracion', '/admin/configuracion.php', 'Configuración', 'gauge'],
    ['usuarios', '/admin/usuarios.php', 'Usuarios', 'user'],
  ];
  echo '<!doctype html><html lang="es-MX"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>' . esc($titulo) . ' · Panel Flexuministros</title><style>' . ADMIN_CSS . '</style></head><body>';
  echo '<div class="panel"><aside class="panel__lateral"><a class="panel__logo" href="/admin/index.php"><img src="/assets/img/favicon.svg" alt="">Panel Flexuministros</a><nav class="panel__nav">';
  foreach ($enlaces as [$id, $href, $etiqueta, $icono]) {
    $clase = $activo === $id ? ' activo' : '';
    echo '<a class="' . $clase . '" href="' . $href . '">' . icono($icono) . $etiqueta . '</a>';
  }
  echo '</nav><div class="panel__pie">Sesión: ' . $usuario . '<br><a href="/admin/logout.php">Cerrar sesión</a><br><br><a href="/" target="_blank" rel="noopener">Ver sitio público ↗</a></div></aside>';
  echo '<div class="panel__cuerpo">';
  if ($mensaje) {
    $clase = $mensaje['tipo'] === 'error' ? 'aviso--error' : 'aviso--ok';
    echo '<div class="aviso ' . $clase . '">' . esc($mensaje['texto']) . '</div>';
  }
}

function panelCerrar(): void {
  echo '<script>function alternarContrasena(boton){var campo=boton.previousElementSibling;var visible=campo.type==="text";campo.type=visible?"password":"text";boton.innerHTML=' . json_encode(icono('eye')) . ';if(!visible)boton.innerHTML=' . json_encode(icono('eye-off')) . ';}</script>';
  echo '</div></div></body></html>';
}
