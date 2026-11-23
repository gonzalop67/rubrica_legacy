<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin\Menu;
use App\Models\Admin\Perfil;

class MenuController extends Controller
{
    protected Menu $menuModel;
    protected Perfil $perfilModel;

    public function __construct()
    {
        parent::__construct();
        $this->menuModel = new Menu;
        $this->perfilModel = new Perfil;
    }

    /**
     * Muestra el listado y organizador jerárquico de menús.
     */
    public function index()
    {
        $title = 'Gestión de Menús';

        // 1. Consulta de perfiles ordenados alfabéticamente
        $roles = $this->perfilModel->orderBy('pe_nombre', 'ASC')->get();

        // 2. Consulta exacta de menús principales (mnu_padre = 0) asociados a su perfil
        $sqlMenusPadre = "SELECT m.id_menu, m.mnu_texto, p.pe_nombre 
                          FROM sw_menu m 
                          INNER JOIN sw_menu_perfil mp ON m.id_menu = mp.id_menu 
                          INNER JOIN sw_perfil p ON p.id_perfil = mp.id_perfil 
                          WHERE m.mnu_padre = 0 
                          ORDER BY p.pe_nombre ASC, m.mnu_orden ASC";

        $this->menuModel->query($sqlMenusPadre);
        $queryResult = $this->menuModel->getQueryResult();
        $menus_principales = ($queryResult instanceof \mysqli_result)
            ? $queryResult->fetch_all(MYSQLI_ASSOC)
            : [];

        return $this->view('admin.menus.index', compact('title', 'roles', 'menus_principales'));
    }

    /**
     * Carga vía AJAX el árbol jerárquico de menús de un perfil específico.
     */
    public function get_menu_ajax()
    {
        $id_rol = isset($_POST['perfil_id']) ? (int)$_POST['perfil_id'] : 0;

        if ($id_rol === 0) {
            echo '<div id="nestable-placeholder"><div class="text-muted text-center" style="padding: 30px 0;">Seleccione un perfil para ver y organizar sus menús.</div></div>';
            exit;
        }

        // Refactorizado: m.mnu_enlace en lugar de m.mnu_link
        $sql = "SELECT m.id_menu, m.mnu_texto, m.mnu_enlace, m.mnu_padre, m.mnu_orden, m.mnu_icono, m.mnu_publicado
                FROM `sw_menu` m
                INNER JOIN `sw_menu_perfil` mp ON m.id_menu = mp.id_menu
                WHERE mp.id_perfil = ?
                ORDER BY m.mnu_padre ASC, m.mnu_orden ASC";

        $this->menuModel->query($sql, [$id_rol], 'i');

        $rows = [];
        $queryResult = $this->menuModel->getQueryResult();

        if ($queryResult instanceof \mysqli_result) {
            $rows = $queryResult->fetch_all(MYSQLI_ASSOC);
        }

        if (empty($rows)) {
            echo '<div class="dd-empty text-muted text-center" style="padding: 30px 0;">Este perfil aún no tiene menús asignados.</div>';
            exit;
        }

        // Construcción del Árbol Jerárquico
        $menuTree = [];
        $submenus = [];

        foreach ($rows as $row) {
            if ($row['mnu_padre'] === null || (int)$row['mnu_padre'] === 0) {
                $row['submenu'] = [];
                $menuTree[$row['id_menu']] = $row;
            } else {
                $submenus[] = $row;
            }
        }

        foreach ($submenus as $sub) {
            $padreId = $sub['mnu_padre'];
            if (isset($menuTree[$padreId])) {
                $sub['submenu'] = [];
                $menuTree[$padreId]['submenu'][] = $sub;
            } else {
                $asignado = false;
                foreach ($menuTree as &$padreRaiz) {
                    if ($this->insertarEnHijo($padreRaiz, $sub)) {
                        $asignado = true;
                        break;
                    }
                }
                if (!$asignado) {
                    $sub['submenu'] = [];
                    $menuTree[$sub['id_menu']] = $sub;
                }
            }
        }

        echo $this->renderNestableTree(array_values($menuTree));
        exit;
    }

    private function insertarEnHijo(&$nodoPadre, $subnode)
    {
        if ($nodoPadre['id_menu'] == $subnode['mnu_padre']) {
            if (!isset($nodoPadre['submenu'])) {
                $nodoPadre['submenu'] = [];
            }
            $subnode['submenu'] = [];
            $nodoPadre['submenu'][] = $subnode;
            return true;
        }

        if (isset($nodoPadre['submenu']) && is_array($nodoPadre['submenu'])) {
            foreach ($nodoPadre['submenu'] as &$hijo) {
                if ($this->insertarEnHijo($hijo, $subnode)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function renderNestableTree(array $menus)
    {
        if (empty($menus)) return '';

        $html = '<ol class="dd-list">';
        foreach ($menus as $menu) {
            $hasChildren = !empty($menu['submenu']);
            $badgeOculto = ((int)$menu['mnu_publicado'] === 0)
                ? ' <span class="label label-default" style="margin-left:5px;">Oculto</span>'
                : '';

            $html .= '<li class="dd-item dd3-item" data-id="' . $menu["id_menu"] . '">';
            $html .= '  <div class="dd-handle dd3-handle"></div>';
            $html .= '  <div class="dd3-content menu_link">';

            $iconoHtml = !empty($menu['mnu_icono'])
                ? '<i class="' . htmlspecialchars($menu['mnu_icono']) . '" style="margin-right: 8px; width: 16px; text-align: center;"></i> '
                : '';

            $html .= '      <a href="#" onclick="obtenerDatos(' . $menu["id_menu"] . '); return false;">'
                . $iconoHtml . htmlspecialchars($menu["mnu_texto"]) . $badgeOculto . '</a>';

            $html .= '      <a href="#" onclick="eliminarMenu(' . $menu["id_menu"] . '); return false;" class="pull-right" title="Eliminar este menú" style="margin-top: 2px;">';
            $html .= '          <i class="fa fa-trash text-danger"></i>';
            $html .= '      </a>';
            $html .= '  </div>';

            if ($hasChildren) {
                $html .= $this->renderNestableTree($menu['submenu']);
            }
            $html .= '</li>';
        }
        $html .= '</ol>';

        return $html;
    }

    /**
     * Crea un nuevo menú y lo vincula al perfil seleccionado en sw_menu_perfil.
     */
    public function store()
    {
        header('Content-Type: application/json');
        $input = $_POST ?? [];

        $errors = [];
        if (empty((int)($input['id_perfil'] ?? 0))) {
            $errors['id_perfil'] = 'Debe seleccionar un perfil.';
        }
        if (trim($input['nombre'] ?? '') === '') {
            $errors['nombre'] = 'El texto del menú es obligatorio.';
        }
        if (trim($input['url'] ?? '') === '') {
            $errors['url'] = 'El enlace/URL es obligatorio.';
        }

        if (!empty($errors)) {
            return json_encode(['ok' => false, 'errors' => $errors]);
        }

        $perfilId = (int)$input['id_perfil'];
        $padreId  = (int)($input['padre_id'] ?? 0);

        // Refactorizado: la URL ahora se guarda en mnu_enlace
        $datos = [
            'mnu_texto'     => trim($input['nombre']),
            'mnu_enlace'    => trim($input['url']),
            'mnu_link'      => '',
            'mnu_nivel'     => $padreId > 0 ? 2 : 1,
            'mnu_orden'     => 99,
            'mnu_padre'     => $padreId,
            'mnu_publicado' => 1,
            'mnu_icono'     => trim($input['icono'] ?? 'fa fa-circle-o'),
        ];

        try {
            $this->menuModel->beginTransaction();
            $nuevoId = $this->menuModel->create($datos);

            if ($nuevoId && $perfilId > 0) {
                $this->menuModel->query(
                    "INSERT INTO sw_menu_perfil (id_perfil, id_menu) VALUES (?, ?)",
                    [$perfilId, (int)$nuevoId],
                    'ii'
                );
            }

            $this->menuModel->commit();
            return json_encode(['ok' => true, 'mensaje' => 'Menú registrado correctamente.']);
        } catch (\Throwable $e) {
            $this->menuModel->rollBack();
            return json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Devuelve en JSON los datos de un menú para rellenar el formulario de edición.
     */
    public function edit(int $id)
    {
        header('Content-Type: application/json');

        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return json_encode(['ok' => false, 'mensaje' => 'Menú no encontrado.']);
        }

        return json_encode([
            'ok' => true,
            'menu' => $menu
        ]);
    }

    /**
     * Actualiza los datos de un menú existente.
     */
    public function update(int $id)
    {
        header('Content-Type: application/json');

        $input = $_POST ?? [];

        $errors = [];
        $nombre  = trim($input['nombre'] ?? '');
        $url     = trim($input['url'] ?? '');
        $icono   = trim($input['icono'] ?? 'fa fa-circle-o');
        $padreId = (int)($input['padre_id'] ?? 0);

        if ($nombre === '') {
            $errors['nombre'] = 'El texto del menú es obligatorio.';
        }
        if ($url === '') {
            $errors['url'] = 'El enlace/URL es obligatorio (use # si es menú padre).';
        }
        if ($padreId === $id) {
            $errors['padre_id'] = 'Un menú no puede ser su propio padre.';
        }

        if (!empty($errors)) {
            return json_encode([
                'ok'     => false,
                'errors' => $errors
            ]);
        }

        $nivel = $padreId > 0 ? 2 : 1;

        try {
            $this->menuModel->beginTransaction();

            // Refactorizado: actualiza mnu_enlace en lugar de mnu_link
            $this->menuModel->query(
                "UPDATE sw_menu 
                 SET mnu_texto = ?, mnu_enlace = ?, mnu_icono = ?, mnu_padre = ?, mnu_nivel = ? 
                 WHERE id_menu = ?",
                [$nombre, $url, $icono, $padreId, $nivel, $id],
                'sssiii'
            );

            $this->menuModel->commit();

            return json_encode([
                'ok'      => true,
                'mensaje' => 'Menú actualizado correctamente.'
            ]);
        } catch (\Throwable $e) {
            $this->menuModel->rollBack();

            return json_encode([
                'ok'      => false,
                'mensaje' => 'Error al actualizar el menú: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guarda la jerarquía (mnu_padre, mnu_nivel y mnu_orden) enviada por Nestable.
     */
    public function guardarOrden()
    {
        header('Content-Type: application/json');

        $menuJson = $_POST['menu'] ?? '';
        $arbol = json_decode($menuJson, true);

        if (!is_array($arbol)) {
            return json_encode(['ok' => false, 'mensaje' => 'Estructura de orden no válida.']);
        }

        try {
            $this->menuModel->beginTransaction();
            $this->actualizarArbolRecursivo($arbol, 0, 1);
            $this->menuModel->commit();

            return json_encode(['ok' => true, 'mensaje' => 'Orden actualizado.']);
        } catch (\Throwable $e) {
            $this->menuModel->rollBack();
            return json_encode(['ok' => false, 'mensaje' => 'Error al guardar orden: ' . $e->getMessage()]);
        }
    }

    private function actualizarArbolRecursivo(array $items, int $padreId, int $nivel)
    {
        foreach ($items as $orden => $item) {
            $idMenu = (int)$item['id'];
            $nuevoOrden = $orden + 1;

            $this->menuModel->query(
                "UPDATE sw_menu SET mnu_padre = ?, mnu_nivel = ?, mnu_orden = ? WHERE id_menu = ?",
                [$padreId, $nivel, $nuevoOrden, $idMenu],
                'iiii'
            );

            if (!empty($item['children']) && is_array($item['children'])) {
                $this->actualizarArbolRecursivo($item['children'], $idMenu, $nivel + 1);
            }
        }
    }

    /**
     * Elimina el menú y sus referencias en sw_menu_perfil.
     */
    public function destroy(int $id)
    {
        header('Content-Type: application/json');
        try {
            $this->menuModel->beginTransaction();
            // Reasignar hijos huérfanos a raíz (mnu_padre = 0) para no perderlos
            $this->menuModel->query("UPDATE sw_menu SET mnu_padre = 0 WHERE mnu_padre = ?", [$id], 'i');
            // Eliminar relación con perfiles
            $this->menuModel->query("DELETE FROM sw_menu_perfil WHERE id_menu = ?", [$id], 'i');
            // Eliminar menú
            $this->menuModel->delete($id);
            $this->menuModel->commit();
            return json_encode([
                'ok' => true,
                'mensaje' => 'El menú ha sido eliminado correctamente.'
            ]);
        } catch (\Throwable $e) {
            $this->menuModel->rollBack();
            return json_encode([
                'ok' => false,
                'mensaje' => 'Error al eliminar el menú: ' . $e->getMessage()
            ]);
        }
    }
}
