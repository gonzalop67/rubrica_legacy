<?php

use App\Controllers\Admin\AdminDashboardController;
use App\Controllers\Admin\MenuController;
use App\Controllers\Admin\PermisoController;
use App\Controllers\Admin\RolController;
use App\Controllers\Admin\UserController;
use App\Controllers\LoginController;
use Core\Route;

require_once RAIZ_PROYECTO . '/Core/middlewares.php';

// =========================================================================
// 1. RUTAS PÚBLICAS / AUTENTICACIÓN
// =========================================================================
// IMPORTANTE: Mientras sigas usando el login antiguo de legacy/index.php,
// mantén comentada la ruta '/' para que el Router caiga en legacy/index.php.
// Cuando quieras activar el login moderno, simplemente descoméntala:
// Route::get('/', [LoginController::class, 'showLoginForm']);

Route::post('auth/login', [LoginController::class, 'login']);
Route::get('auth/logout', [LoginController::class, 'logout']);


// =========================================================================
// 2. MÓDULOS PROTEGIDOS (Todos comparten $authMiddleware automáticamente)
// =========================================================================
Route::group(['middleware' => [$authMiddleware]], function () {

    // Dashboard moderno
    Route::get('admin/dashboard', [AdminDashboardController::class, 'index']);

    // --- Módulo de Usuarios (Migrado) ---
    Route::group(['prefix' => 'usuarios'], function () {
        Route::get('/', [UserController::class, 'index']);               // GET  /usuarios
        Route::get('create', [UserController::class, 'create']);         // GET  /usuarios/create
        Route::post('/', [UserController::class, 'store']);              // POST /usuarios
        Route::get(':id/edit', [UserController::class, 'edit']);         // GET  /usuarios/5/edit
        Route::post(':id/update', [UserController::class, 'update']);    // POST /usuarios/5/update
        Route::post(':id/delete', [UserController::class, 'delete']);    // POST /usuarios/5/delete
        // Si agregas papelera en el futuro:
        // Route::get('wastebasket', [UserController::class, 'wastebasket']);
        // Route::post(':id/restore', [UserController::class, 'restore']);
    });

    // --- Módulo de Roles ---
    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RolController::class, 'index']);
        Route::get('create', [RolController::class, 'create']);
        Route::post('/', [RolController::class, 'store']);
        Route::get('wastebasket', [RolController::class, 'wastebasket']);
        Route::get(':id/edit', [RolController::class, 'edit']);
        Route::post(':id/update', [RolController::class, 'update']);
        Route::post(':id/delete', [RolController::class, 'delete']);
        Route::post(':id/restore', [RolController::class, 'restore']);
        Route::post(':id/destroy', [RolController::class, 'destroy']);
    });

    // --- Módulo de Permisos ---
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/', [PermisoController::class, 'index']);
        Route::get('create', [PermisoController::class, 'create']);
        Route::post('/', [PermisoController::class, 'store']);
        Route::get('wastebasket', [PermisoController::class, 'wastebasket']);
        Route::get(':id/edit', [PermisoController::class, 'edit']);
        Route::post(':id/update', [PermisoController::class, 'update']);
        Route::post(':id/delete', [PermisoController::class, 'delete']);
        Route::post(':id/restore', [PermisoController::class, 'restore']);
        Route::post(':id/destroy', [PermisoController::class, 'destroy']);
    });

    // --- Módulo de Menús ---
    Route::group(['prefix' => 'menus'], function () {
        Route::get('/', [MenuController::class, 'index']);
        Route::post('get_menu_ajax', [MenuController::class, 'get_menu_ajax']);
    });

});

// Ejecutar el despachador (si ninguna ruta coincide, pasará automáticamente a /legacy)
Route::dispatch();
