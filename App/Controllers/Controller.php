<?php

namespace App\Controllers;

use App\Models\Model;
use Core\MiniBlade;

class Controller
{
    protected MiniBlade $blade;

    public function __construct()
    {
        $this->blade = new MiniBlade();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si el usuario viene logueado desde SIAE (legacy o nuevo), compartimos sus datos globales
        if (!empty($_SESSION['id_usuario'])) {
            $this->shareSiaeLayoutData();
        }
    }

    protected function view(string $route, array $data = [])
    {
        $data['menuItems'] = $_SESSION['menuItems'] ?? [];
        return $this->blade->render($route, $data);
    }

    protected function json(mixed $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    private function shareSiaeLayoutData(): void
    {
        $db = new Model();
        $idUsuario = (int) ($_SESSION['id_usuario'] ?? 0);
        $idPerfil  = (int) ($_SESSION['id_perfil'] ?? 0);
        $idPeriodo = (int) ($_SESSION['id_periodo_lectivo'] ?? 0);

        // 1. Usuario
        $usuario = $db->query(
            "SELECT p.primer_nombre, p.primer_apellido, u.us_foto 
             FROM sw_usuario u 
             INNER JOIN sw_persona p ON p.id_persona = u.persona_id 
             WHERE u.id_usuario = ?",
            [$idUsuario],
            'i'
        )->first();

        $nombreUsuario = $usuario ? ($usuario['primer_nombre'] . ' ' . $usuario['primer_apellido']) : 'Usuario';
        $userImage     = \RUTA_URL . '/uploads/' . ($usuario['us_foto'] ?? 'default.png');

        // 2. Perfil activo
        $perfil = $db->query("SELECT pe_nombre FROM sw_perfil WHERE id_perfil = ?", [$idPerfil], 'i')->first();
        $nombrePerfil = strtoupper($perfil['pe_nombre'] ?? '');

        // 3. Periodo lectivo y Oferta educativa
        $periodo = $db->query(
            "SELECT pl.*, oe.nombre AS oferta_nombre 
             FROM sw_periodo_lectivo pl 
             LEFT JOIN sw_ofertas_educativas oe ON oe.id = pl.oferta_educativa_id 
             WHERE pl.id_periodo_lectivo = ?",
            [$idPeriodo],
            'i'
        )->first();

        $aniosPeriodo = $periodo ? ($periodo['pe_anio_inicio'] . ' - ' . $periodo['pe_anio_fin']) : '';
        $nombreOferta = $periodo['oferta_nombre'] ?? '';

        // 4. Menú lateral dinámico desde sw_menu
        $menusPadre = $db->query(
            "SELECT m.* FROM sw_menu m 
             INNER JOIN sw_menu_perfil mp ON m.id_menu = mp.id_menu 
             WHERE mp.id_perfil = ? AND m.mnu_padre = 0 AND m.mnu_publicado = 1 
             ORDER BY m.mnu_orden",
            [$idPerfil],
            'i'
        )->get();

        foreach ($menusPadre as &$menu) {
            $menu['submenus'] = $db->query(
                "SELECT * FROM sw_menu WHERE mnu_publicado = 1 AND mnu_padre = ? ORDER BY mnu_orden",
                [(int)$menu['id_menu']],
                'i'
            )->get();
        }

        $assetBase = rtrim(str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

        $this->blade->share([
            'nombreUsuario'         => $nombreUsuario,
            'userImage'             => $userImage,
            'nombrePerfil'          => $nombrePerfil,
            'aniosPeriodoLectivo'   => $aniosPeriodo,
            'nombreOfertaEducativa' => $nombreOferta,
            'menusSidebar'          => $menusPadre,
            'idUsuarioEnc'          => $_SESSION['id_usuario_enc'] ?? base64_encode((string)$idUsuario),
            'idPerfil'              => $idPerfil,
            'assetBase'             => $assetBase,
        ]);
    }
}
