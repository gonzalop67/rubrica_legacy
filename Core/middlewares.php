<?php

$authMiddleware = function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $logueadoLegacy = !empty($_SESSION['usuario_logueado']) && !empty($_SESSION['id_usuario']);
    $logueadoNuevo  = !empty($_SESSION['authenticated']);

    if (!$logueadoLegacy && !$logueadoNuevo) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'mensaje' => 'Sesión expirada.']);
            exit;
        }

        // Calcula la raíz dinámica (/rubrica_legacy) para nunca desviarse a /rubrica
        $baseUrl = str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME']));
        header('Location: ' . rtrim($baseUrl, '/') . '/');
        exit;
    }
};
