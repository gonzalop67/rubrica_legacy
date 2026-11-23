<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin\Tarea;

class TareaController extends Controller
{
    protected Tarea $tareaModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->tareaModel = new Tarea;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = 'Listado de Tareas';
        $search = isset($_GET['search']) ? trim($_GET['search']) : "";

        $this->tareaModel->where = "";
        $this->tareaModel->values = [];

        if ($search !== "") {
            $likeSearch = '%' . $search . '%';
            $this->tareaModel->where = "tarea LIKE ?";
            $this->tareaModel->values = [$likeSearch];
        }

        $tareas = $this->tareaModel->orderBy('fecha', 'DESC')->paginate(10);

        return $this->view('admin.tareas.index', compact('title', 'tareas'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = "Nueva Tarea";
        return $this->view('admin.tareas.create', compact('title'));
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
        if (!$this->tareaModel->validate($input)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->tareaModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'tarea' => preg_replace('/\s+/', ' ', trim($input['tarea'])),
            'hecho' => 0,
            'fecha' => date('Y-m-d H:i:s'),
        ];

        // 4. Persistencia con manejo de transacciones
        try {
            // Iniciar transacción SQL
            $this->tareaModel->beginTransaction();

            // Ejecutamos la creación en la base de datos
            $this->tareaModel->create($datos);

            // Confirmar cambios si todo salió bien
            $this->tareaModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Tarea procesada con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Revertir transacción SQL ante cualquier fallo
            $this->tareaModel->rollBack();

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
        $tarea = $this->tareaModel->find($id);
        $title = "Editar Tarea";

        return $this->view('admin.tareas.edit', compact('title', 'tarea'));
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

        // 2. Validar datos de entrada (pasando $id para ignorar el registro actual)
        if (!$this->tareaModel->validate($input, $id)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->tareaModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'tarea' => preg_replace('/\s+/', ' ', trim($input['tarea']))
        ];

        // 4. Persistencia con manejo de transacciones atómicas
        try {
            $this->tareaModel->beginTransaction();

            // Ejecutar actualización
            $this->tareaModel->update($id, $datos);

            // Confirmar cambios en la base de datos
            $this->tareaModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Tarea procesada con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Deshace cualquier cambio si algo falla en el proceso
            $this->tareaModel->rollBack();

            return json_encode([
                'ok' => false,
                'mensaje' => "Ocurrió un error inesperado: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Envía el registro a la papelera (Soft Delete).
     */
    public function delete(int $id)
    {
        header('Content-Type: application/json');

        try {
            $eliminado = $this->tareaModel->delete($id);

            if ($eliminado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'La tarea ha sido enviada a la papelera correctamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró la tarea o ya fue eliminada.'
                ]);
            }
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Muestra el listado de tareas en la papelera.
     */
    public function wastebasket()
    {
        $title = "Papelera de Tareas";
        $search = isset($_GET['search']) ? trim($_GET['search']) : "";

        $query = $this->tareaModel->onlyTrashed();

        if ($search !== "") {
            $table = $this->tareaModel->getTable() ?? 'sw_tarea';
            $query->where = "{$table}.deleted_at IS NOT NULL AND {$table}.tarea LIKE ?";
            $query->values = ['%' . $search . '%'];
        }

        $tareas = $query->orderBy('deleted_at', 'DESC')->paginate(10);

        return $this->view('admin.tareas.wastebasket', compact('tareas', 'title'));
    }

    /**
     * Restaura una tarea desde la papelera.
     */
    public function restore(int $id)
    {
        header('Content-Type: application/json');

        try {
            $restaurado = $this->tareaModel->restore($id);

            if ($restaurado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'La tarea ha sido restaurada con éxito.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo restaurar la tarea.'
                ]);
            }
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Eliminación física definitiva de la base de datos.
     */
    public function destroy(int $id)
    {
        header('Content-Type: application/json');

        try {
            $tarea = $this->tareaModel->withTrashed()->find($id);

            if (!$tarea) {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'Atención',
                    'mensaje' => 'La tarea no existe en el sistema.',
                    'estado'  => 'warning'
                ]);
                exit;
            }

            $resultado = $this->tareaModel->forceDelete($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'titulo'  => '¡Completado!',
                    'mensaje' => 'La tarea ha sido eliminada permanentemente del sistema.',
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
            if ($e->getCode() === 1451) {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'No se puede eliminar',
                    'mensaje' => 'La tarea tiene registros vinculados. Debe borrar esas dependencias antes de eliminarla de forma definitiva.',
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
     * Cambia el estado hecho/pendiente de una tarea desde el checkbox.
     */
    public function check(int $id)
    {
        header('Content-Type: application/json');

        try {
            $tarea = $this->tareaModel->find($id);

            if (!$tarea) {
                echo json_encode([
                    'success' => false,
                    'message' => 'La tarea no existe.'
                ]);
                exit;
            }

            $hecho = isset($_POST['hecho']) ? (int) $_POST['hecho'] : 0;

            $this->tareaModel->update($id, [
                'hecho' => $hecho
            ]);

            echo json_encode([
                'success' => true,
                'message' => $hecho === 1 ? 'Tarea marcada como REALIZADA.' : 'Tarea marcada como PENDIENTE.'
            ]);
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
