<?php
/* Migra el contenido actual del sitio (catalogo.json + los datos que vivían
   en _src/build.js y _src/partials.js) a la base de datos.
   Ejecutar UNA SOLA VEZ, por línea de comandos, después de importar schema.sql:
     C:\xampp\php\php.exe sql\semilla.php
   Volver a correrlo no duplica nada: borra y vuelve a insertar todo. */

require_once __DIR__ . '/../inc/db.php';
$pdo = conexionBD();

function copiarImagen(string $origen, string $destinoDir, string $archivo): ?string {
  if (!$archivo) return null;
  $rutaOrigen = $origen . '/' . $archivo;
  if (!is_file($rutaOrigen)) return null;
  if (!is_dir($destinoDir)) mkdir($destinoDir, 0775, true);
  copy($rutaOrigen, $destinoDir . '/' . $archivo);
  return $archivo;
}

$RAIZ = dirname(__DIR__);

echo "Limpiando tablas...\n";
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['producto_imagenes', 'productos', 'equipo_imagenes', 'equipos', 'servicios', 'marcas_servicio', 'marcas', 'categorias', 'configuracion'] as $t) {
  $pdo->exec("TRUNCATE TABLE {$t}");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

/* ---------------- Configuración general ---------------- */
echo "Configuración general...\n";
$config = [
  'fundacion_anio' => '1996',
  'telefono_principal' => '3338387947',
  'telefono_principal_formato' => '33 3838 7947',
  'whatsapp' => '3321063740',
  'whatsapp_intl' => '523321063740',
  'whatsapp_formato' => '33 2106 3740',
  'correo_info' => 'info@flexuministros.com',
  'correo_ventas' => 'ventas@flexuministros.com',
  'correo_atencion' => 'atnclientes@flexuministros.com',
  'direccion_calle' => 'Aldama 770, Col. La Loma',
  'direccion_ciudad' => 'Guadalajara',
  'direccion_estado' => 'Jalisco',
  'direccion_cp' => '44410',
  'facebook_url' => 'https://www.facebook.com/share/1DdKDYp6Ny/?mibextid=wwXIfr',
  'tiktok_url' => 'https://www.tiktok.com/@flexuministros?_r=1&_t=ZS-99FGgMykmgC',
  'maps_url' => 'https://maps.app.goo.gl/55397uErGRgQx1js5',
  'maps_embed_url' => 'https://www.google.com/maps?q=Aldama%20770%2C%20La%20Loma%2C%2044410%20Guadalajara%2C%20Jal.&output=embed',
  'horario' => 'Lunes a viernes, 9:00 – 18:00 h',
  'hero_badge' => 'Soluciones para la industria flexográfica',
  'hero_titulo_1' => 'Lo tuyo es FLEXO.',
  'hero_titulo_2' => 'Lo nuestro, Suministros & Equipos.',
  'hero_texto' => 'Consumibles, herramientas, accesorios, equipos, servicios especiales y soporte técnico para mantener su operación precisa, eficiente y siempre en movimiento.',
  'hero_imagen_1' => 'hero-flexo-4.webp', 'hero_imagen_1_ancho' => '1254', 'hero_imagen_1_alto' => '1254',
  'hero_imagen_2' => 'hero-flexo-3-restaurada-v2.webp', 'hero_imagen_2_ancho' => '1448', 'hero_imagen_2_alto' => '1086',
  'quienes_somos_titulo' => 'Especialistas en optimizar su proceso de impresión',
  'quienes_somos_texto' => 'Ofrecemos a la industria de impresión flexográfica servicios especiales, comercialización de productos innovadores y equipos. Representamos firmas reconocidas mundialmente, símbolo de innovación y confianza, para atender todas sus necesidades antes, durante y después de su proceso de impresión flexo.',
  'mision_texto' => 'Brindar a nuestros clientes soluciones integrales mediante productos innovadores, equipos confiables y asesoría técnica especializada que contribuyan al éxito y crecimiento de sus negocios de impresión.',
  'nosotros_hero_imagen' => 'nosotros-equipo-flexo-2-v2.webp', 'nosotros_hero_ancho' => '1606', 'nosotros_hero_alto' => '979',
  'equipos_hero_imagen' => 'hero-flexo-3-restaurada-v2.webp', 'equipos_hero_ancho' => '1448', 'equipos_hero_alto' => '1086',
  'servicios_hero_imagen' => 'servicios-hero-v1.webp', 'servicios_hero_ancho' => '1840', 'servicios_hero_alto' => '855',
  'telefonos_directorio' => json_encode([
    ['33 3838 7947', '3338387947'], ['33 3657 1253', '3336571253'],
    ['33 3659 9911', '3336599911'], ['33 3944 3711', '3339443711'],
  ]),
  'correos_directorio' => json_encode([
    ['info@flexuministros.com', 'Información general'],
    ['ventas@flexuministros.com', 'Ventas y cotizaciones'],
    ['atnclientes@flexuministros.com', 'Atención a clientes'],
  ]),
];
$stConfig = $pdo->prepare('INSERT INTO configuracion (clave, valor) VALUES (?, ?)');
foreach ($config as $clave => $valor) $stConfig->execute([$clave, $valor]);

// Las 2 fotos de fondo del inicio son editables desde el panel (Configuración),
// así que viven en uploads/ igual que el resto de las imágenes administrables.
copiarImagen($RAIZ . '/assets/img/hero', $RAIZ . '/uploads/configuracion', $config['hero_imagen_1']);
copiarImagen($RAIZ . '/assets/img/hero', $RAIZ . '/uploads/configuracion', $config['hero_imagen_2']);

/* ---------------- Catálogo (categorías, marcas, productos) ---------------- */
echo "Leyendo catalogo.json...\n";
$catalogo = json_decode(file_get_contents(__DIR__ . '/../_src/catalogo.json'), true);

$CAT_ICON = ['auxiliares' => 'tray', 'instrumentos' => 'gauge', 'herramentales' => 'blade', 'cintas' => 'roll', 'limpiadores' => 'spray', 'lubricantes' => 'drop', 'varios' => 'box'];
$CAT_DESC = [
  'auxiliares' => 'Charolas, rasquetas, mangueras, cepillos, sprays, mangas y todo lo que mantiene la prensa corriendo.',
  'instrumentos' => 'Medición y control de proceso: viscosidad, pH, espesores, color, tratamiento corona, cuentahílos, reglas, básculas, etc.',
  'herramentales' => 'Herramientas, cuchillas rotativas, navajas, etc.',
  'cintas' => 'Montaje de clissés (Sticky Back), empalme, teflón, corcho, lija, foam, etc.',
  'limpiadores' => 'Soluciones base agua y solventes para limpieza de anilox, rodillos, prensas y manos. Sprays y químicos para limpieza.',
  'lubricantes' => 'Grasas naturales y sintéticas, aceites penetrantes para prensas y suajes.',
  'varios' => 'Centros de cartón y plástico, guantes, cubetas y recipientes, bolsas plásticas, material de empaque, para su proceso de producción.',
];

echo "Categorías...\n";
$stCat = $pdo->prepare('INSERT INTO categorias (id, nombre, descripcion, icono, orden) VALUES (?, ?, ?, ?, ?)');
foreach ($catalogo['categories'] as $i => $cat) {
  $stCat->execute([$cat['id'], $cat['name'], $CAT_DESC[$cat['id']] ?? '', $CAT_ICON[$cat['id']] ?? 'box', $i]);
}

echo "Marcas del catálogo...\n";
$stMarca = $pdo->prepare('INSERT INTO marcas (nombre, logo, ancho, alto, enlace, orden) VALUES (?, ?, ?, ?, ?, ?)');
foreach ($catalogo['brands'] as $i => $b) {
  $stMarca->execute([$b['name'], $b['img'], $b['w'], $b['h'], $b['link'] ?? null, $i]);
}

echo "Productos (con sus imágenes)...\n";
$dirProdOrigen = $RAIZ . '/assets/img/productos';
$dirProdDestino = $RAIZ . '/uploads/productos';
$stProd = $pdo->prepare('INSERT INTO productos (nombre, descripcion, slug, categoria_id, imagen, imagen_ancho, imagen_alto, visible, orden) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)');
foreach ($catalogo['products'] as $i => $p) {
  $archivo = null;
  if (!empty($p['img'])) {
    $nombreArchivo = preg_replace('/-hd\.webp$/', '.webp', $p['img']);
    $archivo = copiarImagen($dirProdOrigen, $dirProdDestino, $nombreArchivo);
  }
  $stProd->execute([$p['name'], $p['sub'], $p['slug'], $p['cat'], $archivo, $p['w'] ?? null, $p['h'] ?? null, $i]);
}

/* ---------------- Equipos ---------------- */
echo "Equipos...\n";
$EQUIPOS = [
  ['slug' => 'mesa-rebobinadora', 'icono' => 'roll', 'eyebrow' => 'Fabricación propia · Hecho en México',
    'nombre' => 'Mesa rebobinadora para rollos y etiquetas', 'tipo' => 'nuevo',
    'lead' => 'Equipo robusto y de fácil operación para re-bobinar e inspeccionar rollos de material impreso o etiquetas en blanco, en modo horizontal.',
    'p' => 'Brazos de desembobinado/embobinado con ajuste manual, sistema de discos giratorios para un embobinado uniforme, y contador de etiquetas y longitud.',
    'img' => 'mesa-rebobinadora.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la mesa rebobinadora.'],
  ['slug' => 'cortadora-centros-mx', 'icono' => 'blade', 'eyebrow' => 'Fabricación propia · Hecho en México',
    'nombre' => 'Cortadora de centros de cartón y plástico', 'tipo' => 'nuevo',
    'lead' => 'Corte limpio y preciso en cualquier tipo de centro, en cualquier espesor, mediante un sistema de corte rotativo-cerrado.',
    'p' => 'Guía/tope ajustable para fijar la medida a cortar, pedal accionador y guardas de protección superior e inferior para la seguridad del operador.',
    'img' => 'cortadora-centros-mx.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la cortadora de centros.'],
  ['slug' => 'levantador-bobinas', 'icono' => 'truck', 'eyebrow' => 'Fabricación propia · Hecho en México',
    'nombre' => 'Levantador de bobinas', 'tipo' => 'nuevo',
    'lead' => 'Diseño robusto para levantar, trasladar y posicionar bobinas de forma segura y eficiente, con capacidad máxima de carga de 150 kg.',
    'p' => 'Accionamiento manual mediante malacate para un control preciso de la carga, con sistema de freno en las ruedas traseras.',
    'img' => 'levantador-bobinas.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre el levantador de bobinas.'],
  ['slug' => 'suajadora-bdtech', 'icono' => 'wrench', 'eyebrow' => 'Distribuidor autorizado BDTECH',
    'nombre' => 'Suajadora / cortadora rotativa', 'tipo' => 'usado',
    'lead' => 'Troquelado rotativo y corte longitudinal de etiquetas en blanco, con alta velocidad y corte preciso.',
    'p' => 'Fácil operación y funcionamiento estable — ideal para líneas de producción de etiquetas que requieren alta eficiencia.',
    'img' => 'suajadora-bdtech.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la suajadora/cortadora BDTECH.'],
  ['slug' => 'revisadora-bdtech', 'icono' => 'eye', 'eyebrow' => 'Distribuidor autorizado BDTECH',
    'nombre' => 'Revisadora / cortadora', 'tipo' => 'usado',
    'lead' => 'Construcción compacta con alta velocidad, corte preciso y operación estable y confiable.',
    'p' => 'Equipo complementario ideal para líneas de impresión de etiquetas en rollo y suajadoras rotativas.',
    'img' => 'revisadora-bdtech.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la revisadora/cortadora BDTECH.'],
  ['slug' => 'montadora-grabados-bdtech', 'icono' => 'target', 'eyebrow' => 'Distribuidor autorizado BDTECH',
    'nombre' => 'Montadora de grabados', 'tipo' => 'usado',
    'lead' => 'Posicionamiento y montaje preciso de placas fotopoliméricas sobre cilindros o sleeves, antes de impresión.',
    'p' => 'Sistema de doble cámara para visualizar y alinear las marcas de registro, logrando una preparación uniforme y repetible.',
    'img' => 'montadora-grabados-bdtech.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la montadora de grabados BDTECH.'],
  ['slug' => 'tratadora-residuos-tinta', 'icono' => 'drop', 'eyebrow' => 'Distribuidor autorizado Alphasonics',
    'nombre' => 'Tratadora de residuos de tinta base agua', 'tipo' => 'nuevo',
    'lead' => 'Sistema de lavado de piezas para tintas y recubrimientos base agua, con función de floculación.',
    'p' => 'Separa los pigmentos de la tinta para que, tras una simple filtración, el agua resultante pueda reutilizarse o desecharse de forma segura, cumpliendo normativas ambientales cada vez más estrictas.',
    'img' => 'tratadora-residuos-tinta.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la tratadora de residuos de tinta.'],
  ['slug' => 'lavadora-ultrasonido-anilox', 'icono' => 'spray', 'eyebrow' => 'Distribuidor autorizado Alphasonics',
    'nombre' => 'Lavadora de ultrasonido para rodillos anilox', 'tipo' => 'nuevo',
    'lead' => 'Sistema de limpieza ultrasónica de mesa para impresión flexográfica de banda ancha: limpia rodillos anilox de forma segura y precisa.',
    'p' => 'Elimina tinta seca de las celdas del anilox sin dañar la cerámica, incluso en rodillos de alto lineaje, restaurando el rendimiento óptimo de transferencia de tinta.',
    'img' => 'lavadora-ultrasonido-anilox.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera información sobre la lavadora de ultrasonido para anilox.'],
  ['slug' => 'servicio-corte-centros', 'icono' => 'roll', 'eyebrow' => 'Fabricación propia · Hecho en México',
    'nombre' => 'Servicio de corte de centros a la medida', 'tipo' => 'nuevo',
    'lead' => 'Centros de cartón o plástico, cualquier diámetro: cortamos la medida y cantidad que requiera.',
    'p' => 'Evita el sobre-inventario.',
    'img' => 'cortadora-centros-mx.webp', 'w' => 900, 'h' => 675, 'wa' => 'Hola Flexuministros, quisiera cotizar el corte de centros de cartón/plástico.'],
];
$dirEquiposOrigen = $RAIZ . '/assets/img/equipos';
$dirEquiposDestino = $RAIZ . '/uploads/equipos';
$stEquipo = $pdo->prepare('INSERT INTO equipos (slug, nombre, eyebrow, descripcion_corta, descripcion, tipo, icono, imagen_principal, imagen_ancho, imagen_alto, whatsapp_texto, visible, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)');
foreach ($EQUIPOS as $i => $e) {
  $archivo = copiarImagen($dirEquiposOrigen, $dirEquiposDestino, $e['img']);
  $stEquipo->execute([$e['slug'], $e['nombre'], $e['eyebrow'], $e['lead'], $e['p'], $e['tipo'], $e['icono'], $archivo, $e['w'], $e['h'], $e['wa'], $i]);
}

/* ---------------- Servicios ---------------- */
echo "Servicios...\n";
$SERVICIOS = [
  ['slug' => 'recubrimiento-anilox', 'icono' => 'spray', 'eyebrow' => 'Recubrimiento y renovación',
    'titulo' => 'Recubrimiento de Rodillos Anilox',
    'lead' => 'Recubrimiento en cerámico o cromo para rodillos anilox, restaurando sus condiciones de trabajo y prolongando su vida útil.',
    'p' => 'Ahorre hasta un 30% frente al costo de adquirir un rodillo nuevo.', 'extra' => null,
    'img' => 'recubrimiento-anilox-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera cotizar el recubrimiento de un rodillo anilox.'],
  ['slug' => 'recubrimiento-hule', 'icono' => 'spray', 'eyebrow' => 'Recubrimiento y renovación',
    'titulo' => 'Recubrimiento de Rodillos',
    'lead' => 'Recubrimiento de hule para rodillos entintadores, nips y otras aplicaciones dentro de su proceso de impresión. Recuperamos el desempeño, precisión y funcionalidad de sus rodillos, evitando reemplazos innecesarios.',
    'p' => 'Devolvemos a sus rodillos el desempeño y precisión originales, evitando la compra de nuevos.',
    'extra' => 'También ofrecemos mangas desmontables para aplicaciones de impresión y acabados especiales. <a class="link-inline" href="/productos.html?q=manga">Ver catálogo de productos</a>.',
    'img' => 'recubrimiento-hule-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera cotizar el recubrimiento de un rodillo.'],
  ['slug' => 'limpieza-anilox', 'icono' => 'drop', 'eyebrow' => 'Limpieza certificada Alphasonics',
    'titulo' => 'Limpieza Profunda de Rodillos Anilox',
    'lead' => 'Proteja una de las inversiones más importantes de su prensa flexográfica. Nuestro sistema de limpieza ultrasónica especializada remueve profundamente los residuos acumulados sin dañar el recubrimiento del rodillo.',
    'p' => 'Recupere el volumen y la correcta transferencia de tinta sin desgaste del anilox y con garantía por escrito.', 'extra' => null,
    'img' => 'limpieza-anilox-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera información sobre la limpieza de anilox con ultrasonido.'],
  ['slug' => 'corte-centros', 'icono' => 'roll', 'eyebrow' => 'Fabricación propia',
    'titulo' => 'Corte de Centros',
    'lead' => 'Suministro y corte de centros de cartón y plástico en diferentes diámetros, espesores y longitudes.',
    'p' => 'Cortamos únicamente la medida y cantidad que su operación requiere, ayudándole a reducir inventarios, desperdicios y espacio de almacenamiento.', 'extra' => null,
    'img' => 'corte-centros-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera cotizar el corte de centros de cartón/plástico.'],
  ['slug' => 'desarrollo-empaque', 'icono' => 'box', 'eyebrow' => 'Diseño a la medida',
    'titulo' => 'Desarrollo de Empaque',
    'lead' => 'Desarrollamos soluciones de empaque de acuerdo con las características y necesidades de su producto terminado.',
    'p' => 'Desde la selección de materiales hasta dimensiones y presentación, buscamos una solución que facilite su protección, manejo, almacenamiento y transporte.', 'extra' => null,
    'img' => 'desarrollo-empaque-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera información sobre desarrollo de empaque.'],
  ['slug' => 'inventario-consignacion', 'icono' => 'tray', 'eyebrow' => 'Disponibilidad garantizada',
    'titulo' => 'Inventario en Consignación',
    'lead' => 'Mantenemos productos seleccionados disponibles en sus instalaciones o en nuestro almacén exclusivamente para su operación, permitiéndole contar con los materiales que necesita sin realizar compras anticipadas de grandes volúmenes.',
    'p' => 'El consumo se factura conforme se utiliza o de acuerdo con las condiciones previamente establecidas, ayudándole a reducir inventarios, optimizar su flujo de efectivo y asegurar la disponibilidad de productos críticos para su proceso.', 'extra' => null,
    'img' => 'inventario-consignacion-v2.webp', 'w' => 1448, 'h' => 1086, 'wa' => 'Hola Flexuministros, quisiera información sobre inventario en consignación.'],
];
$dirServiciosOrigen = $RAIZ . '/assets/img/servicios';
$dirServiciosDestino = $RAIZ . '/uploads/servicios';
$stServ = $pdo->prepare('INSERT INTO servicios (slug, titulo, eyebrow, texto_principal, texto_secundario, texto_extra, icono, imagen, imagen_ancho, imagen_alto, whatsapp_texto, visible, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)');
foreach ($SERVICIOS as $i => $s) {
  $archivo = copiarImagen($dirServiciosOrigen, $dirServiciosDestino, $s['img']);
  $stServ->execute([$s['slug'], $s['titulo'], $s['eyebrow'], $s['lead'], $s['p'], $s['extra'], $s['icono'], $archivo, $s['w'], $s['h'], $s['wa'], $i]);
}

/* ---------------- Marcas técnicas de Servicios ---------------- */
echo "Marcas técnicas (Servicios)...\n";
$MARCAS_SERVICIO = [
  ['nombre' => 'Alphasonics', 'logo' => 'alphasonics.webp', 'descripcion' => 'Equipos certificados para limpieza ultrasonido de rodillos anilox y sistemas de tratamiento de residuos.'],
  ['nombre' => 'Aalberts Surface Technologies', 'logo' => 'aalberts.webp', 'descripcion' => 'Tecnología de recubrimiento de cerámica para rodillos anilox.'],
  ['nombre' => 'BDTECH', 'logo' => 'bdtech.webp', 'descripcion' => 'Fabricante de maquinaria auxiliar para el proceso de impresión flexográfica.'],
];
$dirMarcasSrvOrigen = $RAIZ . '/assets/img/marcas-servicios';
$dirMarcasSrvDestino = $RAIZ . '/uploads/marcas_servicio';
$stMarcaSrv = $pdo->prepare('INSERT INTO marcas_servicio (nombre, logo, descripcion, orden, visible) VALUES (?, ?, ?, ?, 1)');
foreach ($MARCAS_SERVICIO as $i => $m) {
  $archivo = copiarImagen($dirMarcasSrvOrigen, $dirMarcasSrvDestino, $m['logo']) ?? $m['logo'];
  $stMarcaSrv->execute([$m['nombre'], $archivo, $m['descripcion'], $i]);
}

echo "\nListo. Se migraron:\n";
echo ' - ' . count($catalogo['categories']) . " categorías\n";
echo ' - ' . count($catalogo['brands']) . " marcas de catálogo\n";
echo ' - ' . count($catalogo['products']) . " productos\n";
echo ' - ' . count($EQUIPOS) . " equipos\n";
echo ' - ' . count($SERVICIOS) . " servicios\n";
echo ' - ' . count($MARCAS_SERVICIO) . " marcas técnicas\n";
