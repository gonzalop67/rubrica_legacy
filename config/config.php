<?php

// 1. Configuración de acceso a la base de datos
if (!defined('DB_HOST'))  define('DB_HOST', 'localhost');
if (!defined('DB_USER'))  define('DB_USER', 'colegion_1');
if (!defined('DB_PASS'))  define('DB_PASS', 'AQSWDE123');
if (!defined('DB_NAME'))  define('DB_NAME', 'colegion_1');
if (!defined('APP_NAME')) define('APP_NAME', 'SIAE_2025');

// 2. Rutas absolutas del proyecto
if (!defined('RUTA_APP')) {
    define('RUTA_APP', dirname(__DIR__));
}

if (!defined('RAIZ_PROYECTO')) {
    define('RAIZ_PROYECTO', dirname(RUTA_APP));
}

// 3. URL base dinámica (Funciona en XAMPP local y en producción)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$folder = basename(RAIZ_PROYECTO);

// Si está en una subcarpeta de htdocs/www (como rubrica_legacy), la agrega; si está en la raíz del dominio, la omite
$subFolder = (!in_array($folder, ['htdocs', 'public_html', 'www'], true)) ? '/' . $folder : '';

if (!defined('RUTA_URL')) {
    define('RUTA_URL', $protocol . $host . $subFolder);
}

date_default_timezone_set("America/Guayaquil");
