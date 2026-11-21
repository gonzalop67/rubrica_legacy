<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;

use App\Models\Admin\Perfil;

class RolController extends Controller
{
    protected Perfil $roleModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->roleModel = new Perfil;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = "Lista de Perfiles";
        $search = isset($_GET['search']) ? trim($_GET['search']) : "";

        $this->roleModel->where = "";
        $this->roleModel->values = [];

        if ($search !== "") {
            $likeSearch = '%' . $search . '%';
            $this->roleModel->where = "(pe_nombre LIKE ? OR pe_slug LIKE ?)";
            $this->roleModel->values = [$likeSearch, $likeSearch];
        }

        $roles = $this->roleModel->orderBy('pe_nombre', 'ASC')->paginate(10);

        return $this->view('admin.roles.index', compact('title', 'roles'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = "Nuevo Perfil";
        return $this->view('admin.roles.create', compact('title'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        // Indicar al navegador/JS que la respuesta siempre será un JSON
        header('Content-Type: application/json');

        // 1. Capturar datos directamente de $_POST (compatible al 100% con FormData de JS)
        $input = $_POST ?? [];

        // 2. Validar datos de entrada
        if (!$this->roleModel->validate($input)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->roleModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'pe_nombre' => preg_replace('/\s+/', ' ', trim($input['nombre'])),
            'pe_slug'   => trim($input['slug'])
        ];

        // 4. Persistencia con manejo de transacciones
        try {
            // Iniciar transacción SQL
            $this->roleModel->beginTransaction();

            // Ejecutamos la creación en la base de datos
            $this->roleModel->create($datos);

            // Confirmar cambios si todo salió bien
            $this->roleModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Rol procesado con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Revertir transacción SQL ante cualquier fallo
            $this->roleModel->rollBack();

            return json_encode([
                'ok' => false,
                'mensaje' => "Ocurrió un error inesperado: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit(int $id)
    {
        $role = $this->roleModel->find($id);
        $title = "Editar Perfil";

        return $this->view('admin.roles.edit', compact('title', 'role'));
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     */
    public function update(int $id)
    {
        // Indicar al navegador/JS que la respuesta siempre será un JSON
        header('Content-Type: application/json');

        // 1. Capturar datos directamente de $_POST (compatible al 100% con FormData de JS)
        $input = $_POST ?? [];

        // 2. Validar datos de entrada
        if (!$this->roleModel->validate($input, $id)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->roleModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'pe_nombre' => preg_replace('/\s+/', ' ', trim($input['nombre'])),
            'pe_slug'   => trim($input['slug'])
        ];

        // 4. Persistencia con manejo de transacciones atómicas
        try {
            $this->roleModel->beginTransaction();
            // echo "<pre>"; print_r($datos); echo "</pre>"; die();

            // Ejecutar actualización
            $this->roleModel->update($id, $datos);

            // Confirmar cambios en la base de datos
            $this->roleModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Perfil procesado con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Deshace cualquier cambio si algo falla en el proceso
            $this->roleModel->rollBack();

            return json_encode([
                'ok' => false,
                'mensaje' => "Ocurrió un error inesperado: " . $e->getMessage()
            ]);
        }
    }

    public function delete(int $id)
    {
        header('Content-Type: application/json');

        try {
            $eliminado = $this->roleModel->delete($id);

            if ($eliminado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'El registro ha sido eliminado correctamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró el registro o ya fue eliminado.'
                ]);
            }
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
        exit; // Detiene la ejecución para que solo devuelva el JSON
    }

    public function wastebasket()
    {
        $title = "Papelera de Perfiles";
        $search = isset($_GET['search']) ? $_GET['search'] : "";

        // 1. Iniciamos el filtro de borrados lógicos
        $query = $this->roleModel->onlyTrashed();

        if ($search !== "") {
            // Obtenemos dinámicamente el nombre de la tabla del modelo (ej: usuarios)
            $table = $this->roleModel->getTable() ?? 'sw_perfil'; // Asegura tener un método getTable o pon el nombre de tu tabla directamente

            // 2. Asignamos el WHERE explícito calificando las columnas con su tabla
            $query->where = "{$table}.deleted_at IS NOT NULL AND ({$table}.pe_nombre LIKE ? OR {$table}.pe_slug LIKE ?)";

            $query->values = [
                '%' . $search . '%',
                '%' . $search . '%'
            ];
        }

        // 3. Ordenamos y paginamos de forma nativa
        $roles = $query->orderBy('deleted_at', 'DESC')->paginate(5);

        // 4. Renderizamos la vista de la papelera
        return $this->view('admin.roles.wastebasket', compact('roles', 'title'));
    }

    // Método para restaurar un usuario (Botón Verde)
    public function restore(int $id)
    {
        header('Content-Type: application/json');
        try {
            // Llama al método restore() que añadimos en la clase Model
            $restaurado = $this->roleModel->restore($id);

            if ($restaurado) {
                echo json_encode(['success' => true, 'message' => 'El perfil ha sido restaurado con éxito.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo restaurar el perfil.']);
            }
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    // Método para eliminación física definitiva (Botón Rojo)
    public function destroy(int $id)
    {
        header('Content-Type: application/json');

        try {
            // 1. Buscamos al usuario en la base de datos antes de borrarlo
            $perfil = $this->roleModel->withTrashed()->find($id);

            if (!$perfil) {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'Atención',
                    'mensaje' => 'El perfil no existe en el sistema.',
                    'estado'  => 'warning'
                ]);
                exit;
            }

            // 2. Ejecutamos la eliminación física definitiva en la base de datos
            $resultado = $this->roleModel->forceDelete($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'titulo'  => '¡Completado!',
                    'mensaje' => 'El rol ha sido eliminado permanentemente del sistema.',
                    'estado'  => 'success'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'Error',
                    'mensaje' => 'No se pudo eliminar el registro de la base de datos.',
                    'estado'  => 'error'
                ]);
            }
        } catch (\mysqli_sql_exception $e) {
            // CAPTURA EXITOSA: el catch atrapa el error 1451 (Claves foráneas) perfectamente
            if ($e->getCode() === 1451) {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'No se puede eliminar',
                    'mensaje' => 'El rol tiene registros vinculados. Debe reasignar o borrar esas dependencias antes de eliminarlo de forma definitiva.',
                    'estado'  => 'error'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'Error de Base de Datos',
                    'mensaje' => 'Fallo en la consulta: ' . $e->getMessage(),
                    'estado'  => 'error'
                ]);
            }
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'titulo'  => 'Error inesperado',
                'mensaje' => $e->getMessage(),
                'estado'  => 'error'
            ]);
        }
        exit;
    }

    /**
     * Muestra el árbol de menús y permisos granulares para asignar a un perfil.
     */
    public function permissions(int $id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            $assetBase = rtrim(str_replace(['\\', '/public'], ['/', ''], dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            header("Location: {$assetBase}/roles");
            exit;
        }

        $title = "Permisos del Perfil: " . $role['pe_nombre'];

        // 1. Obtener IDs de menús actualmente asignados a este perfil
        $asignadosData = $this->roleModel->query(
            "SELECT id_menu FROM sw_menu_perfil WHERE id_perfil = ?",
            [$id],
            'i'
        )->get();
        $permisosAsignados = array_map('intval', array_column($asignadosData, 'id_menu'));

        // 2. Obtener todos los menús padre publicados
        $menusPadre = $this->roleModel->query(
            "SELECT * FROM sw_menu WHERE mnu_padre = 0 AND mnu_publicado = 1 ORDER BY mnu_orden ASC"
        )->get();

        // 3. Adjuntar los submenús publicados de cada padre
        foreach ($menusPadre as &$menu) {
            $menu['submenus'] = $this->roleModel->query(
                "SELECT * FROM sw_menu WHERE mnu_padre = ? AND mnu_publicado = 1 ORDER BY mnu_orden ASC",
                [(int)$menu['id_menu']],
                'i'
            )->get();
        }
        unset($menu);

        // 4. Obtener IDs de permisos granulares asignados en sw_perfil_permiso
        $granularesData = $this->roleModel->query(
            "SELECT id_permiso FROM sw_perfil_permiso WHERE id_perfil = ?",
            [$id],
            'i'
        )->get();
        $granularesAsignados = array_map('intval', array_column($granularesData, 'id_permiso'));

        // 5. Obtener catálogo activo de sw_permiso
        $todosLosPermisos = $this->roleModel->query(
            "SELECT id_permiso, nombre, slug, descripcion FROM sw_permiso WHERE deleted_at IS NULL ORDER BY slug ASC"
        )->get();

        // 6. Agrupar permisos tomando la última palabra del slug (ej: 'crear-usuario' -> 'Usuario')
        $permisosAgrupados = [];
        foreach ($todosLosPermisos as $perm) {
            $partes = explode('-', $perm['slug']);

            // Si tiene guion, toma el último elemento (end) como módulo; si no, lo envía a 'Generales'
            $grupo = count($partes) > 1 ? ucfirst(end($partes)) : 'Generales';
            $permisosAgrupados[$grupo][] = $perm;
        }

        // Ordenar las tarjetas alfabéticamente por nombre de módulo
        ksort($permisosAgrupados);

        return $this->view('admin.roles.permissions', compact(
            'title',
            'role',
            'menusPadre',
            'permisosAsignados',
            'permisosAgrupados',
            'granularesAsignados'
        ));
    }

    /**
     * Sincroniza menús (sw_menu_perfil) y permisos granulares (sw_perfil_permiso) en una transacción.
     */
    public function savePermissions(int $id)
    {
        try {
            $role = $this->roleModel->find($id);
            if (!$role) {
                return $this->json([
                    'ok' => false,
                    'mensaje' => 'El perfil seleccionado no existe.'
                ]);
            }

            // Recibimos menús y permisos marcados
            $menusSeleccionados = $_POST['menus'] ?? [];
            $menusIds = array_unique(array_map('intval', (array)$menusSeleccionados));

            $permisosSeleccionados = $_POST['permisos'] ?? [];
            $permisosIds = array_unique(array_map('intval', (array)$permisosSeleccionados));

            $this->roleModel->beginTransaction();

            // 1. Sincronizar Menús (sw_menu_perfil)
            $this->roleModel->query("DELETE FROM sw_menu_perfil WHERE id_perfil = ?", [$id], 'i');
            foreach ($menusIds as $idMenu) {
                if ($idMenu > 0) {
                    $this->roleModel->query(
                        "INSERT INTO sw_menu_perfil (id_perfil, id_menu) VALUES (?, ?)",
                        [$id, $idMenu],
                        'ii'
                    );
                }
            }

            // 2. Sincronizar Permisos Granulares (sw_perfil_permiso)
            $this->roleModel->query("DELETE FROM sw_perfil_permiso WHERE id_perfil = ?", [$id], 'i');
            foreach ($permisosIds as $idPermiso) {
                if ($idPermiso > 0) {
                    $this->roleModel->query(
                        "INSERT INTO sw_perfil_permiso (id_perfil, id_permiso) VALUES (?, ?)",
                        [$id, $idPermiso],
                        'ii'
                    );
                }
            }

            $this->roleModel->commit();

            return $this->json([
                'ok' => true,
                'mensaje' => 'Menús y permisos actualizados correctamente para el perfil ' . $role['pe_nombre'] . '.'
            ]);
        } catch (\Throwable $e) {
            $this->roleModel->rollBack();
            return $this->json([
                'ok' => false,
                'mensaje' => 'Error al guardar los permisos: ' . $e->getMessage()
            ]);
        }
    }
}
