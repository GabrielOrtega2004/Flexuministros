/* Servidor local para ver el sitio antes de publicarlo.
   Uso:  node servidor.js     y abrir http://localhost:8080          */
const http = require('http');
const fs = require('fs');
const path = require('path');

const ROOT = __dirname;
const PORT = process.env.PORT || 8080;

const TIPOS = {
  '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8', '.svg': 'image/svg+xml',
  '.webp': 'image/webp', '.png': 'image/png', '.jpg': 'image/jpeg',
  '.ico': 'image/x-icon', '.json': 'application/json',
  '.webmanifest': 'application/manifest+json', '.xml': 'application/xml',
  '.txt': 'text/plain; charset=utf-8',
};

http.createServer((req, res) => {
  let ruta = decodeURIComponent(req.url.split('?')[0]);
  if (ruta.endsWith('/')) ruta += 'index.html';

  const archivo = path.join(ROOT, ruta);
  // _src/ trae el código fuente sin minificar y material original del
  // cliente (fotos, video) que nunca debe quedar accesible por URL directa.
  if (!archivo.startsWith(ROOT) || ruta === '/_src' || ruta.startsWith('/_src/')) {
    res.writeHead(403).end('Prohibido'); return;
  }

  fs.readFile(archivo, (err, buf) => {
    if (err) {
      fs.readFile(path.join(ROOT, '404.html'), (e2, b2) => {
        res.writeHead(404, { 'Content-Type': 'text/html; charset=utf-8' });
        res.end(e2 ? 'No encontrado' : b2);
      });
      return;
    }
    res.writeHead(200, {
      'Content-Type': TIPOS[path.extname(archivo).toLowerCase()] || 'application/octet-stream',
      // Vista previa local: nunca se cachea. Sin esto el navegador guarda
      // páginas e imágenes por su cuenta y usted seguiría viendo la versión
      // anterior después de regenerar el sitio.
      'Cache-Control': 'no-store, must-revalidate',
    });
    res.end(buf);
  });
}).listen(PORT, () => {
  console.log(`\n  Flexuministros — vista previa en  http://localhost:${PORT}\n  (Ctrl+C para detener)\n`);
});
