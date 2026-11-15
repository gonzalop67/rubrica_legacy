<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = [];
session_destroy();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirige dinámicamente a la raíz del proyecto (/rubrica_legacy/ o / en producción)
$baseUrl = str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME']));
header('Location: ' . rtrim($baseUrl, '/') . '/');
exit;
