# Sitio web de Flexuministros

Sitio estático (HTML + CSS + JavaScript, sin frameworks ni base de datos).
Puntuación **100 / 100 / 100 / 100** en Lighthouse — rendimiento, accesibilidad,
prácticas recomendadas y SEO — en móvil y escritorio.

---

## 1. Ver el sitio en su computadora

```bash
node servidor.js
```

Luego abra <http://localhost:8080>.

> El sitio usa rutas absolutas (`/assets/...`), como corresponde a un sitio
> publicado. Por eso **no** funciona abriendo `index.html` con doble clic:
> use el servidor local o súbalo a su hosting.

> **Si regenera el sitio y no ve los cambios**, recargue con `Ctrl + Shift + R`.
> El servidor de vista previa ya pide al navegador que no guarde nada en
> caché, pero una copia guardada antes de ese ajuste puede seguir ahí.

---

## 2. Publicar

Suba **todo el contenido de esta carpeta** (excepto `_src/`, que es opcional)
a la raíz pública del hosting — normalmente `public_html/`.

| Archivo | Para qué sirve |
|---|---|
| `.htaccess` | Apache / cPanel: compresión, caché, HTTPS y redirección a `www` |
| `_headers` | Netlify o Cloudflare Pages (equivalente al anterior) |
| `robots.txt`, `sitemap.xml` | Indexación en buscadores |
| `site.webmanifest` | Instalación como aplicación en móvil |

**Después de publicar:** dé de alta el sitio en
[Google Search Console](https://search.google.com/search-console) y envíe
`https://www.flexuministros.com/sitemap.xml`.

Si el dominio final **no** fuera `www.flexuministros.com`, cambie la constante
`SITE` en `_src/partials.js` y vuelva a generar el sitio (paso 4).

---

## 3. Estructura

```
index.html          Inicio
nosotros.html       Historia, misión, visión, valores y marcas
productos.html      Catálogo de 100 productos con buscador y filtros
servicios.html      Equipo, fabricación propia y servicio técnico (ver abajo)
contacto.html       Datos de contacto, formulario y mapa
404.html            Página de error

assets/
  js/main.js        Menú móvil, buscador, filtros, mapa, formulario y animaciones
  img/
    logo.svg              Logotipo completo (vectorial)
    logo-inverso.svg      Logotipo para fondos oscuros
    icono.svg             Sólo el símbolo
    favicon.svg           Ícono de pestaña
    og.jpg                Imagen para redes sociales
    hero/                 3 recortes en alta resolución para la portada
    cta/                  Fotografía del bloque de cierre
    productos/            99 fotos de producto en WebP
    marcas/               17 logotipos de marcas representadas
    servicios/            Fotos de servicios.html (equipo, cilindros, anilox…)

_src/               Fuentes para regenerar el sitio (ver paso 4)
servidor.js         Servidor local de vista previa
```

---

## 4. Modificar contenido

Puede editar los `.html` directamente. Pero si va a cambiar algo que se repite
(menú, pie de página, teléfonos) o el catálogo, conviene usar el generador:

```bash
node _src/build.js
```

Esto reescribe las cinco páginas, `sitemap.xml`, `robots.txt` y
`site.webmanifest`. No requiere instalar nada: sólo Node.js.

| Quiero cambiar… | Edite… |
|---|---|
| Teléfonos, correos, dirección, redes sociales | `_src/partials.js` → objeto `BIZ` |
| Menú, barra superior, pie de página | `_src/partials.js` |
| Textos de cada página | `_src/build.js` |
| Colores, tipografías, espaciados | `_src/style.css` |
| Productos del catálogo | `_src/catalogo.json` |
| Imagen para redes sociales | `_src/og.html` (luego capture a 1200×630 px) |

### Agregar un producto

En `_src/catalogo.json`, dentro de `products`:

```json
{
  "name": "Nombre del producto",
  "sub": "Presentación o característica breve",
  "cat": "auxiliares",
  "slug": "nombre-del-producto",
  "img": "nombre-del-producto.webp",
  "w": 420,
  "h": 315
}
```

Categorías válidas: `auxiliares`, `instrumentos`, `herramentales`, `cintas`,
`limpiadores`, `lubricantes`, `varios`.

Coloque la foto en `assets/img/productos/` en formato **WebP**, de unos 420 px
de ancho, y ponga en `w` y `h` sus dimensiones reales (esto evita saltos de
maquetación). Si omite `img`, la tarjeta muestra el ícono de su categoría.
Luego ejecute `node _src/build.js`.

### Agregar o cambiar un servicio (servicios.html)

Cada bloque de la página está en el arreglo `SERVICIOS` de `_src/build.js`.
Para agregar uno nuevo, copie uno existente y cambie `h2`, `lead`, `p`, el
mensaje de `wa` y la imagen (`img`, con `w`/`h` reales) o una galería de dos
fotos (`gallery`). Las fotos de origen que mandó el cliente están en
`_src/material-cliente-servicios/`; el script `node _src/build-servicios-img.js`
las recorta y convierte a WebP dentro de `assets/img/servicios/`. Después
ejecute `node _src/build.js` para regenerar las páginas.

---

## 5. Cómo funciona el formulario

No hay servidor ni base de datos, así que el formulario de `contacto.html`
**arma el mensaje y lo abre** en la aplicación del visitante:

- **Enviar por correo** → abre su gestor de correo con el mensaje redactado
  hacia `ventas@flexuministros.com`.
- **Enviar por WhatsApp** → abre WhatsApp con el mensaje hacia el 33 2106 3740.

Ventaja: cero mantenimiento y cero costo. Si más adelante prefiere recibir los
mensajes en una bandeja propia, se puede conectar un servicio como Formspree o
Web3Forms cambiando unas pocas líneas en `assets/js/main.js`.

Cada producto del catálogo tiene además su propio botón **Cotizar**, que abre
WhatsApp con el nombre del producto ya escrito.

---

## 6. Animaciones

Todas son de CSS (`transform` y `opacity`), las únicas propiedades que el
navegador puede animar sin volver a calcular la maquetación. Por eso el sitio
conserva **CLS 0** y **100 de rendimiento**.

| Dónde | Qué ocurre |
|---|---|
| Portada | Entrada escalonada: distintivo → titular → texto → botones → indicadores |
| Portada | Subrayado que se dibuja bajo la línea naranja del titular |
| Portada | Las tres tarjetas de producto entran y luego flotan despacio |
| Portada | El halo naranja late; el patrón de marca se desplaza muy lentamente |
| Portada | Cinta inferior con las 7 categorías en desplazamiento continuo (se detiene al pasar el cursor) |
| Indicadores | Los números cuentan desde cero al entrar en pantalla |
| Todas | Cada bloque aparece al hacer scroll (`data-reveal`), con escalonado |
| Encabezado | Aparece una sombra suave al desplazarse |
| Tarjetas | La foto del producto hace un ligero acercamiento al pasar el cursor |
| Bloque de cierre | Al entrar en pantalla: el filo naranja se dibuja de izquierda a derecha, la fotografía entra desde la derecha y el texto aparece escalonado |
| Bloque de cierre | La fotografía hace un acercamiento continuo de 28 s y, donde el navegador lo admite, se mueve con paralaje al desplazarse |

### Cómo controlarlas

- **Añadir la animación a un bloque nuevo:** póngale el atributo `data-reveal`.
  Variantes: `data-reveal="zoom"`, `="left"`, `="right"`.
  Para escalonar, agregue `style="--d:120ms"`.
- **Ajustar velocidad o distancia:** bloque *ANIMACIONES* en `_src/style.css`.
- **Bloque de cierre:** sección *llamada a la acción* en `_src/style.css`.
  La fotografía está en `assets/img/cta/` y se declara en `_src/partials.js`.
  El panel está limitado a **520 px de ancho** y usa una fotografía restaurada
  de **1536 × 1024 px**, suficiente para verse nítida también en pantallas
  de alta densidad.
  `ctaZoom` controla el acercamiento; `ctaParalaje`, el movimiento al
  desplazarse; `.ctaband__veil`, cuánto se funde la foto con el grafito.
  En móvil la fotografía se oculta y **ni siquiera se descarga**.
- **Quitarlas todas:** borre la línea
  `<script>document.documentElement.className+=" js-anim"</script>`
  de `_src/partials.js` y regenere. El sitio queda idéntico, sin movimiento.

Dos salvaguardas importantes:

1. **Sin JavaScript el contenido se ve completo.** Los estilos que ocultan
   dependen de la clase `js-anim`, que sólo añade un script. Buscadores y
   lectores de pantalla siempre reciben la página entera.
2. **Se respeta «reducir movimiento»** del sistema operativo: quien lo tenga
   activado ve el sitio sin ninguna animación.

El titular de la portada se desplaza pero **nunca parte de opacidad cero**: si
lo hiciera, Google contaría la animación dentro del tiempo de carga (LCP) y
bajaría la calificación de rendimiento.

---

## 7. Decisiones técnicas

- **CSS incrustado en cada página.** Elimina una petición bloqueante y es lo que
  permite el 100 de rendimiento. La fuente única es `_src/style.css`.
- **Tipografía del sistema.** Sin fuentes web: cero descargas y cero parpadeo.
- **Imágenes en WebP** con `width`/`height` declarados y carga diferida
  (`loading="lazy"`) salvo las primeras, que cargan de inmediato.
  Peso total del catálogo: 558 KB para 99 fotos.
- **El mapa de Google se carga sólo al pulsarlo**, para no arrastrar su peso ni
  sus cookies en cada visita.
- **SEO:** título y descripción únicos por página, URL canónica, Open Graph,
  Twitter Card y datos estructurados JSON-LD (`Organization`, `Store`,
  `BreadcrumbList` y un `ItemList` con los 100 productos).
- **Accesibilidad:** contraste AA verificado, navegación por teclado, enlace de
  salto al contenido, `alt` en todas las imágenes y estados de foco visibles.

---

## 8. Origen del material

- Logotipo vectorizado a SVG desde `logo flexuministros-cambio cvs.pdf`
  (colores exactos del original: naranja `#F57F01`, gris `#686763`).
- Los 100 productos, sus descripciones y las 17 marcas provienen del
  catálogo `FLEXUMINISTROS Suministros 2024.pdf`.
- La misión es la del material institucional de la empresa.
