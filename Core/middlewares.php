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

$permissionMiddleware = function () {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $idPerfil = (int) ($_SESSION['id_perfil'] ?? 0);

    // Función auxiliar interna para denegar el acceso (JSON o HTML 403)
    $denegarAcceso = function (string $mensaje) {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
            || ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

        if (ob_get_length()) {
            ob_clean();
        }

        http_response_code(403);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok'      => false,
                'success' => false,
                'titulo'  => 'Acceso Denegado',
                'mensaje' => $mensaje,
                'estado'  => 'error'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $assetBase = rtrim(str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head><meta charset='UTF-8'><title>403 - Acceso Denegado</title>
        <link rel='stylesheet' href='{$assetBase}/bower_components/bootstrap/dist/css/bootstrap.min.css'></head>
        <body style='background:#ecf0f5; padding-top: 80px;'>
            <div class='container'>
                <div class='alert alert-danger text-center' style='max-width: 500px; margin: 0 auto;'>
                    <h3><i class='glyphicon glyphicon-ban-circle'></i> 403 - Acceso Denegado</h3>
                    <p>{$mensaje}</p>
                    <br>
                    <a href='javascript:history.back()' class='btn btn-default'>Volver atrás</a>
                </div>
            </div>
        </body></html>";
        exit;
    };

    if ($idPerfil <= 0) {
        $denegarAcceso('No se ha identificado un perfil activo en la sesión.');
    }

    // 1. Extraer el módulo base de la URL actual (ej: '/usuarios/5/edit' -> '/usuarios')
    $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $basePath   = rtrim(str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

    if ($basePath !== '' && str_starts_with($requestUri, $basePath)) {
        $requestUri = substr($requestUri, strlen($basePath));
    }

    $rutaLimpia = trim($requestUri, '/');
    $segmentos  = explode('/', $rutaLimpia);
    $moduloBase = '/' . ($segmentos[0] ?? '');

    // 2. Verificar en sw_menu_perfil si el perfil activo tiene asignado ese módulo
    $db = new \App\Models\Model();
    $sql = "SELECT COUNT(*) AS total
            FROM sw_menu m
            INNER JOIN sw_menu_perfil mp ON m.id_menu = mp.id_menu
            WHERE mp.id_perfil = ?
              AND m.mnu_publicado = 1
              AND (m.mnu_enlace = ? OR m.mnu_enlace LIKE ?)";

    $enlaceConPrefijo = '%/' . ltrim($moduloBase, '/');
    $resultado = $db->query($sql, [$idPerfil, $moduloBase, $enlaceConPrefijo], 'iss')->first();

    if (empty($resultado) || (int)$resultado['total'] === 0) {
        $denegarAcceso('No tienes permisos asignados para acceder a este módulo.');
    }
};
