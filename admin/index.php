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
  <a class="tarjeta" href="/admin/equipos.php" style="text-decoration:none;color:inherit">
    <p style="margin:0;color:#77736B;font-size:.82rem;font-weight:700;text-transform:uppercase">Equipos</p>
    <p style="margin:.35rem 0 0;font-size:1.9rem;font-weight:800"><?= $totalEquipos ?></p>
    <p style="margin:.25rem 0 0;font-size:.82rem;color:#77736B"><?= $equiposOcultos ?> oculto(s)</p>
  </a>
  <a class="tarjeta" href="/admin/productos.php" style="text-decoration:none;color:inherit">
    <p style="margin:0;color:#77736B;font-size:.82rem;font-weight:700;text-transform:uppercase">Productos</p>
    <p style="margin:.35rem 0 0;font-size:1.9rem;font-weight:800"><?= $totalProductos ?></p>
    <p style="margin:.25rem 0 0;font-size:.82rem;color:#77736B"><?= $productosOcultos ?> oculto(s)</p>
  </a>
  <a class="tarjeta" href="/admin/servicios.php" style="text-decoration:none;color:inherit">
    <p style="margin:0;color:#77736B;font-size:.82rem;font-weight:700;text-transform:uppercase">Servicios</p>
    <p style="margin:.35rem 0 0;font-size:1.9rem;font-weight:800"><?= $totalServicios ?></p>
    <p style="margin:.25rem 0 0;font-size:.82rem;color:#77736B"><?= $serviciosOcultos ?> oculto(s)</p>
  </a>
</div>

<div class="tarjeta" style="margin-top:1.5rem">
  <p style="margin:0 0 .75rem;font-weight:800">Accesos rápidos</p>
  <div style="display:flex;gap:.6rem;flex-wrap:wrap">
    <a class="btn btn--primario" href="/admin/equipos.php?nuevo=1"><?= icono('check') ?>Agregar equipo</a>
    <a class="btn btn--primario" href="/admin/productos.php?nuevo=1"><?= icono('check') ?>Agregar producto</a>
    <a class="btn btn--fantasma" href="/admin/configuracion.php"><?= icono('gauge') ?>Editar datos de contacto</a>
  </div>
</div>
<?php
panelCerrar();
