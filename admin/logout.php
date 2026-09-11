<?php
require_once __DIR__ . '/inc/auth.php';
cerrarSesion();
header('Location: /admin/login.php');
exit;
