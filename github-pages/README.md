# Publicación en GitHub Pages

Sitio: https://gabrielortega2004.github.io/Flexuministros/

Los HTML de esta carpeta son una copia de las siete páginas públicas,
generada desde el PHP actual y la base de datos local. GitHub Pages solo
sirve archivos estáticos; el panel administrativo necesita PHP y MySQL.

Para actualizar la copia después de editar las páginas o su contenido en el
panel, con MySQL activo y `inc/config.php` configurado, ejecutar desde `web`:

```powershell
node scripts/build-pages.mjs --refresh
```

Se requiere Node.js y PHP en PATH (o la variable `PHP_BIN` apuntando a PHP).
Si existe `_site`, renombrar o eliminar esa carpeta generada antes de ejecutar.
El comando exporta el HTML, adapta enlaces e imágenes a `/Flexuministros/`
y verifica que existan los recursos locales. Revisar y subir los HTML de
`github-pages/` junto con los cambios en `assets/` y `uploads/`.

Cada push a `main` publica automáticamente la copia mediante GitHub Actions.
Los cambios del panel no aparecen en Pages hasta regenerar y subir la copia.
El artefacto publicado contiene únicamente HTML, imágenes, JavaScript,
estilos, fuentes e iconos; no incluye el panel, PHP, SQL ni configuración.

Para generar el artefacto desde una copia ya exportada (sin PHP/MySQL):

```powershell
node scripts/build-pages.mjs
```
