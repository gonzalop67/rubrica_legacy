<?php

namespace Core;

class Route
{
    private static array $routes = [];
    private static array $groupStack = []; // Pila para manejar grupos anidados

    public static function get(string $uri, callable|array $callback, array $middlewares = []): void
    {
        self::addRoute('GET', $uri, $callback, $middlewares);
    }

    public static function post(string $uri, callable|array $callback, array $middlewares = []): void
    {
        self::addRoute('POST', $uri, $callback, $middlewares);
    }

    public static function put(string $uri, callable|array $callback, array $middlewares = []): void
    {
        self::addRoute('PUT', $uri, $callback, $middlewares);
    }

    public static function delete(string $uri, callable|array $callback, array $middlewares = []): void
    {
        self::addRoute('DELETE', $uri, $callback, $middlewares);
    }

    // Nuevo método para agrupar rutas por prefijo (ej. 'api') y/o middlewares
    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    private static function addRoute(string $method, string $uri, callable|array $callback, array $middlewares): void
    {
        $prefix = '';
        $groupMiddlewares = [];

        // Recolectamos prefijos y middlewares de los grupos activos
        foreach (self::$groupStack as $group) {
            if (!empty($group['prefix'])) {
                $prefix .= '/' . trim($group['prefix'], '/');
            }
            if (!empty($group['middleware'])) {
                $groupMiddlewares = array_merge($groupMiddlewares, (array) $group['middleware']);
            }
        }

        $fullUri = trim($prefix . '/' . trim($uri, '/'), '/');

        self::$routes[$method][$fullUri] = [
            'callback'    => $callback,
            // Los middlewares del grupo se ejecutan primero, luego los de la ruta
            'middlewares' => array_merge($groupMiddlewares, $middlewares)
        ];
    }

    public static function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $projectFolder = '/' . basename(dirname(__DIR__));

        if ($projectFolder !== '/public_html' && $projectFolder !== '/htdocs' && str_starts_with($uri, $projectFolder)) {
            $uri = substr($uri, strlen($projectFolder));
        }
        $uri = trim($uri, '/');

        $method = $_SERVER['REQUEST_METHOD'];

        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $routePath => $routeData) {
                $quotedRoute = preg_quote($routePath, '#');
                $pattern = preg_replace('#\\\\:[a-zA-Z0-9_]+#', '([a-zA-Z0-9_-]+)', $quotedRoute);

                if (preg_match("#^$pattern$#", $uri, $matches)) {
                    $params = array_slice($matches, 1);

                    // 1. Ejecutar Middlewares
                    if (!empty($routeData['middlewares'])) {
                        foreach ($routeData['middlewares'] as $middleware) {
                            if (is_string($middleware) && class_exists($middleware)) {
                                (new $middleware())->handle();
                            } elseif (is_callable($middleware)) {
                                $middleware();
                            }
                        }
                    }

                    // 2. Ejecutar Controlador o Closure
                    $callback = $routeData['callback'];
                    $response = null;

                    if (is_callable($callback)) {
                        $response = $callback(...$params);
                    } elseif (is_array($callback)) {
                        $controller = new $callback[0];
                        $response = $controller->{$callback[1]}(...$params);
                    }

                    // 3. Manejar Respuesta (HTML o JSON automático)
                    if (is_array($response) || is_object($response)) {
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    } else {
                        echo $response;
                    }

                    return;
                }
            }
        }

        // --- PUENTE LEGACY (Si no coincide con ninguna ruta moderna) ---
        $legacyBaseDir = dirname(__DIR__) . '/legacy';
        $targetPath = ($uri === '') ? 'index.php' : $uri;
        $legacyFile = $legacyBaseDir . '/' . $targetPath;

        if (is_dir($legacyFile)) {
            $legacyFile = rtrim($legacyFile, '/') . '/index.php';
        }

        $realLegacyDir = realpath($legacyBaseDir);
        $realFile = realpath($legacyFile);

        if ($realFile && $realLegacyDir && str_starts_with($realFile, $realLegacyDir) && pathinfo($realFile, PATHINFO_EXTENSION) === 'php') {
            set_error_handler(function ($errno, $errstr) {
                return ($errno === E_NOTICE && str_contains($errstr, 'session_start()'));
            });
            chdir(dirname($realFile));
            require $realFile;
            restore_error_handler();
            return;
        }
        // --- FIN PUENTE LEGACY ---

        // Respuesta 404 limpia según el tipo de petición
        http_response_code(404);
        if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'mensaje' => "Ruta '$uri' no encontrada."]);
        } else {
            echo "404 - Página no encontrada";
        }
    }
}
