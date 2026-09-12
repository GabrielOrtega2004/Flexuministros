// Exporta solo las páginas públicas. PHP/MySQL se necesitan al actualizar
// la copia; GitHub Actions publica esa copia sin acceder a la base de datos.
import { execFileSync } from 'node:child_process';
import { mkdirSync, readFileSync, writeFileSync, readdirSync, copyFileSync, existsSync } from 'node:fs';
import { dirname, resolve, extname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const snapshot = join(root, 'github-pages');
const output = join(root, '_site');
const pages = ['index', 'nosotros', 'productos', 'equipos', 'servicios', 'contacto', '404'];
const base = process.env.PAGES_BASE_PATH || '/Flexuministros/';
if (!/^\/(?:[A-Za-z0-9._-]+\/)*$/.test(base)) throw new Error('PAGES_BASE_PATH debe empezar y terminar con /.');

if (process.argv.includes('--refresh')) {
  // Completar todas las páginas antes de reemplazar la copia anterior.
  const rendered = pages.map(name => {
    const html = execFileSync(process.env.PHP_BIN || 'php', [
      '-d', 'display_errors=stderr', '-d', 'log_errors=0', `${name}.php`,
    ], { cwd: root, encoding: 'utf8', maxBuffer: 10 * 1024 * 1024 });
    if (!/<!doctype html>/i.test(html) || !html.includes('</html>') || /(?:Fatal error|Warning):/.test(html)) {
      throw new Error(`No se pudo exportar ${name}.php`);
    }
    return [name, html.replace(/[\t ]+$/gm, '')];
  });
  mkdirSync(snapshot, { recursive: true });
  for (const [name, html] of rendered) writeFileSync(join(snapshot, `${name}.html`), html);
  console.log(`Actualizadas ${rendered.length} páginas desde PHP/MySQL.`);
}

// Un directorio nuevo evita conservar archivos de una publicación anterior.
if (existsSync(output)) throw new Error('Ya existe _site. Renómbralo o elimínalo antes de generar otra publicación.');
mkdirSync(output);
const rebase = text => text
  .replace(/(\b(?:href|src|data-src|poster|action)\s*=\s*["'])\/(?!\/)/gi, `$1${base}`)
  .replace(/(url\(\s*["']?)\/(?!\/)/gi, `$1${base}`)
  .replace(/(\bsrcset\s*=\s*)(["'])(.*?)\2/gi, (_, attr, quote, urls) =>
    attr + quote + urls.replace(/(^|,\s*)\/(?!\/)/g, `$1${base}`) + quote);

for (const name of pages) {
  writeFileSync(join(output, `${name}.html`), rebase(readFileSync(join(snapshot, `${name}.html`), 'utf8')));
}

// Lista explícita: nunca publicar PHP, SQL, credenciales ni el panel.
const assetExtensions = new Set(['.svg', '.png', '.jpg', '.jpeg', '.webp', '.gif', '.ico', '.js', '.css', '.woff', '.woff2', '.ttf', '.pdf']);
function copyAssets(source, destination) {
  mkdirSync(destination, { recursive: true });
  for (const entry of readdirSync(source, { withFileTypes: true })) {
    if (entry.name.startsWith('.')) continue;
    const from = join(source, entry.name), to = join(destination, entry.name);
    if (entry.isDirectory()) copyAssets(from, to);
    else if (entry.isFile() && assetExtensions.has(extname(entry.name).toLowerCase())) {
      if (extname(entry.name) === '.css') writeFileSync(to, rebase(readFileSync(from, 'utf8')));
      else copyFileSync(from, to);
    }
  }
}
for (const directory of ['assets', 'uploads']) copyAssets(join(root, directory), join(output, directory));
for (const name of ['favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png']) {
  copyFileSync(join(root, name), join(output, name));
}
const manifest = JSON.parse(readFileSync(join(root, 'site.webmanifest'), 'utf8'));
manifest.start_url = base;
manifest.scope = base;
for (const icon of manifest.icons) if (icon.src.startsWith('/')) icon.src = base + icon.src.slice(1);
writeFileSync(join(output, 'site.webmanifest'), JSON.stringify(manifest, null, 2) + '\n');
writeFileSync(join(output, '.nojekyll'), '');

// Detectar enlaces y recursos rotos antes de subir el sitio.
let checked = 0;
for (const name of pages) {
  const html = readFileSync(join(output, `${name}.html`), 'utf8');
  for (const match of html.matchAll(/\b(?:href|src|data-src|poster)\s*=\s*["']([^"']+)["']/gi)) {
    const url = match[1];
    if (!url.startsWith('/') || url.startsWith('//')) continue;
    if (!url.startsWith(base)) throw new Error(`${name}: ruta fuera del sitio: ${url}`);
    const local = decodeURIComponent(url.slice(base.length).split(/[?#]/)[0]) || 'index.html';
    if (!existsSync(join(output, local))) throw new Error(`${name}: recurso ausente: ${url}`);
    checked++;
  }
}
console.log(`Sitio preparado en _site; ${pages.length} páginas y ${checked} referencias locales verificadas.`);
