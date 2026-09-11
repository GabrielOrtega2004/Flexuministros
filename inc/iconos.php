<?php
/* Iconos de línea (24×24, currentColor) — mismos trazos que _src/partials.js
   para que el HTML resultante sea idéntico al sitio estático. */

const ICONOS = [
  'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.5 2.8.6a2 2 0 0 1 1.7 2Z"/>',
  'mail' => '<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path d="m22 7-10 6L2 7"/>',
  'pin' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
  'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
  'wa' => '<path d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2Z"/><path d="M8.6 7.9c.2-.4.4-.4.6-.4h.5c.2 0 .4 0 .6.5l.8 1.9c.1.2 0 .4-.1.6l-.4.5c-.1.2-.3.3-.1.6a7 7 0 0 0 3.4 3c.3.1.5 0 .6-.1l.6-.7c.2-.2.3-.2.6-.1l1.8.9c.3.1.4.2.5.4v.5c-.1.6-.7 1.3-1.3 1.5-.5.2-1.2.3-3.4-.6a11 11 0 0 1-4.7-4.2c-.4-.5-.9-1.5-.9-2.5s.5-1.6.7-1.8Z"/>',
  'fb' => '<path d="M15 3h-2.5A4.5 4.5 0 0 0 8 7.5V10H5.5v4H8v8h4v-8h3l1-4h-4V7.5a.5.5 0 0 1 .5-.5H15V3Z"/>',
  'tiktok' => '<path d="M16 3a5.5 5.5 0 0 0 5 4v3.5a8.8 8.8 0 0 1-5-1.6v6.4a6.3 6.3 0 1 1-6.3-6.3c.3 0 .6 0 .9.1v3.6a2.8 2.8 0 1 0 2 2.6V3H16Z"/>',
  'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
  'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
  'chevron' => '<path d="m18 15-6-6-6 6"/>',
  'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
  'check' => '<path d="M20 6 9 17l-5-5"/>',
  'leaf' => '<path d="M11 20A7 7 0 0 1 4 13c0-5 5-9 16-9 0 11-4 16-9 16Z"/><path d="M4 20c2-4 5-7 9-9"/>',
  'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
  'users' => '<path d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 20v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
  'truck' => '<path d="M3 16V6a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v10"/><path d="M15 9h3.5a1 1 0 0 1 .8.4l2.5 3.2a1 1 0 0 1 .2.6V16"/><circle cx="7.5" cy="17.5" r="2"/><circle cx="17.5" cy="17.5" r="2"/><path d="M9.5 17.5h6"/>',
  'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
  'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>',
  'spark' => '<path d="m12 3 2.2 5.6L20 10.8l-5.8 2.2L12 19l-2.2-6L4 10.8l5.8-2.2L12 3Z"/>',
  'box' => '<path d="m21 8-9-5-9 5v8l9 5 9-5V8Z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
  'tray' => '<path d="M3 8h18l-2 9a2 2 0 0 1-2 1.6H7A2 2 0 0 1 5 17L3 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>',
  'gauge' => '<path d="M4 18a9 9 0 1 1 16 0"/><path d="m12 14 4-4"/>',
  'blade' => '<path d="M4 16 16 4l4 4L8 20H4v-4Z"/><path d="m13 7 4 4"/>',
  'roll' => '<ellipse cx="8" cy="12" rx="4" ry="8"/><path d="M8 4h8a4 8 0 0 1 0 16H8"/>',
  'spray' => '<path d="M9 8h6v13a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V8Z"/><path d="M10 8V4h4v4M17 4h2M17 7h2M17 10h2"/>',
  'drop' => '<path d="M12 3s6 6.4 6 10.5A6 6 0 0 1 6 13.5C6 9.4 12 3 12 3Z"/>',
  'wrench' => '<path d="M15 3a5 5 0 0 0-4.5 7.2L3 17.7 6.3 21l7.5-7.5A5 5 0 1 0 15 3Z"/>',
  'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
  'eye-off' => '<path d="M9.9 5.1A10.4 10.4 0 0 1 12 5c7 0 10 7 10 7a13 13 0 0 1-2.2 3.2M6.6 6.6A13.5 13.5 0 0 0 2 12s3 7 10 7a9.7 9.7 0 0 0 4.4-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/><path d="M2 2l20 20"/>',
];

function icono(string $nombre, string $clase = ''): string {
  $cls = $clase !== '' ? " class=\"{$clase}\"" : '';
  $trazo = ICONOS[$nombre] ?? '';
  return "<svg{$cls} viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.7\" stroke-linecap=\"round\" stroke-linejoin=\"round\" aria-hidden=\"true\">{$trazo}</svg>";
}

function iconoRelleno(string $nombre, string $clase = ''): string {
  $cls = $clase !== '' ? " class=\"{$clase}\"" : '';
  $trazo = ICONOS[$nombre] ?? '';
  return "<svg{$cls} viewBox=\"0 0 24 24\" fill=\"currentColor\" aria-hidden=\"true\">{$trazo}</svg>";
}
