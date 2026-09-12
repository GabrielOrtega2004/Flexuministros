<?php
require_once __DIR__ . '/inc/layout.php';
requiereSesion();

$pdo = conexionBD();
$totalEquipos = (int)$pdo->query('SELECT COUNT(*) FROM equipos')->fetchColumn();
$totalProductos = (int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
$totalServicios = (int)$pdo->query('SELECT COUNT(*) FROM servicios')->fetchColumn();
$equiposOcultos = (int)$pdo->query('SELECT COUNT(*) FROM equipos WHERE visible = 0')->fetchColumn();
$productosOcultos = (int)$pdo->query('SELECT COUNT(*) FROM productos WHERE visible = 0')->fetchColumn();
$serviciosOcultos = (int)$pdo->query('SELECT COUNT(*) FROM servicios WHERE visible = 0')->fetchColumn();

panelAbrir('Inicio', 'inicio');
?>
<div class="panel__encabezado"><h1>Bienvenido</h1></div>
<p style="color:#66625B;margin-top:-.75rem;margin-bottom:1.5rem">Desde aquí puede actualizar el contenido del sitio. Los cambios se publican de inmediato.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));gap:1rem">
  <a class="tarjeta aparecer" href="/admin/equipos.php" style="animation-delay:.03s">
    <div class="estadistica__icono"><?= icono('box') ?></div>
    <p class="estadistica__etiqueta">Equipos</p>
    <p class="estadistica__numero"><?= $totalEquipos ?></p>
    <p class="estadistica__nota"><?= $equiposOcultos ?> oculto(s)</p>
  </a>
  <a class="tarjeta aparecer" href="/admin/productos.php" style="animation-delay:.08s">
    <div class="estadistica__icono"><?= icono('tray') ?></div>
    <p class="estadistica__etiqueta">Productos</p>
    <p class="estadistica__numero"><?= $totalProductos ?></p>
    <p class="estadistica__nota"><?= $productosOcultos ?> oculto(s)</p>
  </a>
  <a class="tarjeta aparecer" href="/admin/servicios.php" style="animation-delay:.13s">
    <div class="estadistica__icono"><?= icono('wrench') ?></div>
    <p class="estadistica__etiqueta">Servicios</p>
    <p class="estadistica__numero"><?= $totalServicios ?></p>
    <p class="estadistica__nota"><?= $serviciosOcultos ?> oculto(s)</p>
  </a>
</div>

<div class="tarjeta aparecer" style="margin-top:1.5rem;animation-delay:.18s">
  <p style="margin:0 0 .75rem;font-weight:800">Accesos rápidos</p>
  <div style="display:flex;gap:.6rem;flex-wrap:wrap">
    <a class="btn btn--primario" href="/admin/equipos.php?nuevo=1"><?= icono('check') ?>Agregar equipo</a>
    <a class="btn btn--primario" href="/admin/productos.php?nuevo=1"><?= icono('check') ?>Agregar producto</a>
    <a class="btn btn--fantasma" href="/admin/configuracion.php"><?= icono('gauge') ?>Editar datos de contacto</a>
  </div>
</div>
<?php
panelCerrar();
