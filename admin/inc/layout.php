<?php
/* Estructura visual compartida del panel: mismos colores y tipografía del
   sitio público, pero con su propio layout simple (barra lateral + tabla o
   formulario), separado del diseño del sitio público. */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../inc/iconos.php';
require_once __DIR__ . '/../../inc/datos.php';

const ADMIN_CSS = <<<CSS
*{box-sizing:border-box}
body{margin:0;font:15px/1.5 system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;background:#F6F3ED;color:#22211E;background-image:radial-gradient(60rem 24rem at 15% -10%,rgba(245,127,1,.05),transparent 60%)}
a{color:inherit}
.panel{display:flex;min-height:100vh}
.panel__lateral{width:15rem;flex:none;background:linear-gradient(185deg,#1c1915,#131110);color:#E8E5DE;padding:1.5rem 1rem;display:flex;flex-direction:column;position:relative}
.panel__franja{display:flex;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:2rem}
.panel__logo{display:flex;align-items:center;gap:.6rem;font-weight:800;font-size:1.05rem;color:#fff;text-decoration:none;flex:1;min-width:0}
.panel__logo img{width:2rem;height:2rem;flex:none}
.panel__menu-boton{display:none;background:none;border:0;color:#fff;padding:.3rem;cursor:pointer;flex:none}
.panel__menu-boton svg{width:1.5rem;height:1.5rem}
.panel__menu{display:flex;flex-direction:column;flex:1;min-height:0}
.panel__nav{display:flex;flex-direction:column;gap:.25rem;flex:1}
.panel__nav a{display:flex;align-items:center;gap:.65rem;padding:.65rem .8rem;border-radius:9px;text-decoration:none;color:#C7C3BA;font-weight:600;font-size:.92rem;transition:background .15s,color .15s}
.panel__nav a svg{width:1.1rem;height:1.1rem;flex:none;transition:transform .15s}
.panel__nav a:hover{background:rgba(255,255,255,.08);color:#fff}
.panel__nav a:hover svg{transform:translateX(2px)}
.panel__nav a.activo{background:#F57F01;color:#171511;box-shadow:0 4px 14px rgba(245,127,1,.32)}
.panel__nav a.activo:hover svg{transform:none}
.panel__pie{border-top:1px solid rgba(255,255,255,.12);padding-top:1rem;margin-top:1rem;font-size:.82rem;color:#948F84}
.panel__pie a{color:#E8E5DE;text-decoration:underline;transition:color .15s}
.panel__pie a:hover{color:#FF9A2E}
.panel__cuerpo{flex:1;padding:2rem clamp(1.25rem,4vw,3rem);max-width:76rem}
.panel__encabezado{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap}
.panel__encabezado h1{margin:0;font-size:1.55rem;letter-spacing:-.02em}
.btn{display:inline-flex;align-items:center;gap:.4rem;border:1px solid transparent;border-radius:9px;padding:.6rem 1.1rem;font-weight:700;font-size:.88rem;cursor:pointer;text-decoration:none;transition:background .15s,border-color .15s,box-shadow .15s,transform .1s}
.btn--primario{background:#F57F01;color:#171511;box-shadow:0 3px 10px rgba(245,127,1,.25)}
.btn--primario:hover{background:#FF8B12;transform:translateY(-1px);box-shadow:0 6px 16px rgba(245,127,1,.35)}
.btn--fantasma{background:#fff;border-color:#E0DAD0;color:#22211E}
.btn--fantasma:hover{border-color:#F57F01;transform:translateY(-1px)}
.btn--peligro{background:#fff;border-color:#E4B4A4;color:#B23A24}
.btn--peligro:hover{background:#FBEAE5}
.btn:active{transform:translateY(0)}
.btn svg{width:1rem;height:1rem}
.aviso{padding:.85rem 1.1rem;border-radius:10px;margin-bottom:1.25rem;font-weight:600;font-size:.9rem;animation:deslizar .3s ease both}
.aviso--ok{background:#E4F5E9;color:#166534;border:1px solid #B7E4C7}
.aviso--error{background:#FBEAE5;color:#B23A24;border:1px solid #E4B4A4}
.tarjeta{background:#fff;border:1px solid #E7E1D7;border-radius:14px;padding:1.5rem;box-shadow:0 1px 2px rgba(34,33,30,.02),0 10px 26px rgba(34,33,30,.045)}
a.tarjeta{text-decoration:none;color:inherit;display:block;transition:transform .18s,box-shadow .18s,border-color .18s}
a.tarjeta:hover{transform:translateY(-3px);box-shadow:0 16px 32px rgba(34,33,30,.09);border-color:#F1D9BC}
.estadistica__icono{width:2.75rem;height:2.75rem;border-radius:10px;display:grid;place-items:center;margin-bottom:.9rem;background:#FDECD8;color:#C56B12}
.estadistica__icono svg{width:1.3rem;height:1.3rem}
.estadistica__etiqueta{margin:0;color:#77736B;font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.03em}
.estadistica__numero{margin:.3rem 0 0;font-size:1.95rem;font-weight:800;letter-spacing:-.02em}
.estadistica__nota{margin:.2rem 0 0;font-size:.82rem;color:#77736B}
.aparecer{opacity:0;animation:surgir .45s cubic-bezier(.16,1,.3,1) both}
@keyframes surgir{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
@keyframes deslizar{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
@media(prefers-reduced-motion:reduce){.aparecer,.aviso{animation:none;opacity:1}.btn,a.tarjeta{transition:none}}
.tabla-scroll{overflow-x:auto}
table{width:100%;min-width:38rem;border-collapse:collapse;font-size:.9rem}
th{text-align:left;padding:.6rem .7rem;border-bottom:2px solid #E7E1D7;color:#66625B;font-size:.78rem;text-transform:uppercase;letter-spacing:.04em}
td{padding:.7rem;border-bottom:1px solid #EFEDE8;vertical-align:middle}
tr:last-child td{border-bottom:0}
tbody tr{transition:background .12s}
tbody tr:hover{background:#FBF8F2}
.tabla-img{width:3rem;height:3rem;border-radius:8px;object-fit:cover;background:#F0EDE7;display:block}
.tabla-acciones{display:flex;gap:.4rem;white-space:nowrap}
.tabla-acciones .btn{padding:.4rem .65rem;font-size:.8rem}
.etiqueta{display:inline-block;padding:.2rem .55rem;border-radius:999px;font-size:.74rem;font-weight:700}
.etiqueta--si{background:#E4F5E9;color:#166534}
.etiqueta--no{background:#F0EDE7;color:#77736B}
.form__seccion{padding-bottom:1.6rem;margin-bottom:1.6rem;border-bottom:1px solid #EFEDE8}
.form__seccion:last-of-type{border-bottom:0;margin-bottom:0;padding-bottom:0}
.form__seccion-titulo{display:flex;align-items:center;gap:.6rem;font-size:1rem;font-weight:800;margin:0 0 1.2rem;letter-spacing:-.01em}
.form__seccion-icono{width:1.9rem;height:1.9rem;border-radius:8px;background:#FDECD8;color:#C56B12;display:grid;place-items:center;flex:none}
.form__seccion-icono svg{width:1rem;height:1rem}
.form__campo{margin-bottom:1.1rem}
.form__campo:last-child{margin-bottom:0}
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
input[type=password]::-ms-reveal,input[type=password]::-ms-clear{display:none}
.campo-contrasena__ojo{position:absolute;top:0;right:0;bottom:0;width:2.6rem;display:grid;place-items:center;background:none;border:0;cursor:pointer;color:#77736B;padding:0}
.campo-contrasena__ojo:hover{color:#22211E}
.campo-contrasena__ojo svg{width:1.15rem;height:1.15rem}
.panel__login{min-height:100vh;display:grid;place-items:center;background:#171511}
.login-caja{background:#fff;border-radius:16px;padding:2.25rem;width:22rem;max-width:92vw}
.login-caja h1{font-size:1.3rem;margin:0 0 .3rem}
.login-caja p{color:#77736B;font-size:.88rem;margin:0 0 1.5rem}
@media (max-width:760px){
  .panel{flex-direction:column}
  .panel__lateral{width:100%;padding:.85rem 1rem}
  .panel__franja{margin-bottom:0}
  .panel__logo{font-size:.95rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .panel__logo img{width:1.75rem;height:1.75rem}
  .panel__menu-boton{display:block}
  .panel__menu{display:none;position:absolute;top:100%;left:0;right:0;background:#171511;padding:.5rem 1rem 1rem;z-index:30;border-radius:0 0 14px 14px;box-shadow:0 16px 30px rgba(0,0,0,.3);max-height:calc(100vh - 4rem);overflow-y:auto}
  .panel__menu.abierto{display:flex}
  .panel__pie{display:block}
  .form__fila{grid-template-columns:1fr}

  .tabla-scroll{overflow-x:visible}
  table{min-width:0}
  thead{display:none}
  tbody{display:block}
  tr{display:block;border:1px solid #E7E1D7;border-radius:12px;padding:.9rem 1rem;margin-bottom:.75rem}
  tr:last-child{margin-bottom:0}
  td{display:block;padding:.5rem 0;border-bottom:1px dashed #EFEDE8;text-align:left}
  td:last-child{border-bottom:0}
  td[data-label]::before{content:attr(data-label);display:block;font-weight:700;font-size:.7rem;text-transform:uppercase;letter-spacing:.04em;color:#77736B;margin-bottom:.25rem}
  td.tabla-miniatura{text-align:center;padding-bottom:.75rem}
  td.tabla-miniatura .tabla-img{width:5rem;height:5rem;margin:0 auto}
  td.tabla-acciones{display:flex;gap:.5rem;flex-wrap:wrap;padding-top:.65rem}
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
  echo '<div class="panel"><aside class="panel__lateral">';
  echo '<div class="panel__franja"><a class="panel__logo" href="/admin/index.php"><img src="/assets/img/favicon.svg" alt="">Panel Flexuministros</a>';
  echo '<button type="button" class="panel__menu-boton" onclick="alternarMenuPanel()" aria-label="Abrir menú">' . icono('menu') . '</button></div>';
  echo '<div class="panel__menu" id="panelMenu"><nav class="panel__nav">';
  foreach ($enlaces as [$id, $href, $etiqueta, $icono]) {
    $clase = $activo === $id ? ' activo' : '';
    echo '<a class="' . $clase . '" href="' . $href . '">' . icono($icono) . $etiqueta . '</a>';
  }
  echo '</nav><div class="panel__pie">Sesión: ' . $usuario . '<br><a href="/admin/logout.php">Cerrar sesión</a><br><br><a href="/" target="_blank" rel="noopener">Ver sitio público ↗</a></div></div></aside>';
  echo '<div class="panel__cuerpo">';
  if ($mensaje) {
    $clase = $mensaje['tipo'] === 'error' ? 'aviso--error' : 'aviso--ok';
    echo '<div class="aviso ' . $clase . '">' . esc($mensaje['texto']) . '</div>';
  }
}

function panelCerrar(): void {
  echo '<script>function alternarContrasena(boton){var campo=boton.previousElementSibling;var visible=campo.type==="text";campo.type=visible?"password":"text";boton.innerHTML=' . json_encode(icono('eye')) . ';if(!visible)boton.innerHTML=' . json_encode(icono('eye-off')) . ';}function alternarMenuPanel(){document.getElementById("panelMenu").classList.toggle("abierto");}</script>';
  echo '</div></div></body></html>';
}
