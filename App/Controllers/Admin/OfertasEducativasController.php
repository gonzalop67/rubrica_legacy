<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin\OfertaEducativa;

class OfertasEducativasController extends Controller
{
    protected OfertaEducativa $ofertaEducativaModel;

    public function __construct()
    {
        parent::__construct();
        $this->ofertaEducativaModel = new OfertaEducativa;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = 'Listado de Ofertas Educativas';

        $search = trim($_GET['search'] ?? '');

        if ($search !== '') {
            // 1. Creamos la estructura SQL agrupada con paréntesis para proteger la lógica
            $this->ofertaEducativaModel->where = "(nombre LIKE ? OR slug LIKE ?)";

            // 2. Preparamos los comodines de forma segura
            $term = "%{$search}%";

            // 3. Pasamos los valores al arreglo que procesará el prepare del ORM
            $this->ofertaEducativaModel->values = [$term, $term, $term];
        }

        // El ORM inyectará de forma automática el ORDER BY y resolverá la paginación
        $ofertaseducativas = $this->ofertaEducativaModel
            ->orderBy('nombre')
            ->paginate(10);

        return $this->view('admin.ofertaseducativas.index', compact('title', 'ofertaseducativas'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = "Crear Oferta Educativa";

        return $this->view('admin.ofertaseducativas.create', compact('title'));
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
        if (!$this->ofertaEducativaModel->validate($input)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->ofertaEducativaModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'nombre' => preg_replace('/\s+/', ' ', trim($input['nombre'])),
            'slug'   => trim($input['slug'])
        ];

        // 4. Persistencia con manejo de transacciones
        try {
            // Iniciar transacción SQL
            $this->ofertaEducativaModel->beginTransaction();

            // Ejecutamos la creación en la base de datos
            $this->ofertaEducativaModel->create($datos);

            // Confirmar cambios si todo salió bien
            $this->ofertaEducativaModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Rol procesado con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Revertir transacción SQL ante cualquier fallo
            $this->ofertaEducativaModel->rollBack();

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
        $oferta_educativa = $this->ofertaEducativaModel->find($id);
        $title = "Editar Oferta Educativa";

        return $this->view('admin.ofertaseducativas.edit', compact('title', 'oferta_educativa'));
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
        if (!$this->ofertaEducativaModel->validate($input, $id)) {
            return json_encode([
                'ok' => false,
                'errors' => $this->ofertaEducativaModel->errors
            ]);
        }

        // 3. Preparación del set de datos (limpiando espacios)
        $datos = [
            'nombre' => trim($input['nombre'] ?? ''),
            'slug'   => trim($input['slug'] ?? ''),
        ];

        // 4. Persistencia con manejo de transacciones atómicas
        try {
            $this->ofertaEducativaModel->beginTransaction();

            // Ejecutar actualización
            $this->ofertaEducativaModel->update($id, $datos);

            // Confirmar cambios en la base de datos
            $this->ofertaEducativaModel->commit();

            return json_encode([
                'ok' => true,
                'mensaje' => 'Permiso procesado con éxito.'
            ]);
        } catch (\Throwable $e) {
            // Deshace cualquier cambio si algo falla en el proceso
            $this->ofertaEducativaModel->rollBack();

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
            $eliminado = $this->ofertaEducativaModel->delete($id);

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
        $title = "Papelera de Ofertas Educativas";
        $search = isset($_GET['search']) ? $_GET['search'] : "";

        // 1. Iniciamos el filtro de borrados lógicos
        $query = $this->ofertaEducativaModel->onlyTrashed();

        if ($search !== "") {
            // Obtenemos dinámicamente el nombre de la tabla del modelo (ej: usuarios)
            $table = $this->ofertaEducativaModel->getTable() ?? 'sw_ofertas_educativas'; // Asegura tener un método getTable o pon el nombre de tu tabla directamente

            // 2. Asignamos el WHERE explícito calificando las columnas con su tabla
            $query->where = "({$table}.nombre LIKE ? OR {$table}.slug LIKE ?)";

            $query->values = [
                '%' . $search . '%',
                '%' . $search . '%'
            ];
        }

        // 3. Ordenamos y paginamos de forma nativa
        $ofertaseducativas = $query->orderBy('deleted_at', 'DESC')->paginate(5);

        // 4. Renderizamos la vista de la papelera
        return $this->view('admin.ofertaseducativas.wastebasket', compact('ofertaseducativas', 'title'));
    }

    // Método para restaurar una oferta educativa (Botón Verde)
    public function restore(int $id)
    {
        header('Content-Type: application/json');
        try {
            // Llama al método restore() que añadimos en la clase Model
            $restaurado = $this->ofertaEducativaModel->restore($id);

            if ($restaurado) {
                echo json_encode(['success' => true, 'message' => 'La oferta educativa ha sido restaurada con éxito.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo restaurar la oferta educativa.']);
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
            // 1. Buscamos a la oferta educativa en la base de datos antes de borrarlo
            $perfil = $this->ofertaEducativaModel->withTrashed()->find($id);

            if (!$perfil) {
                echo json_encode([
                    'success' => false,
                    'titulo'  => 'Atención',
                    'mensaje' => 'La Oferta Educativa no existe en el sistema.',
                    'estado'  => 'warning'
                ]);
                exit;
            }

            // 2. Ejecutamos la eliminación física definitiva en la base de datos
            $resultado = $this->ofertaEducativaModel->forceDelete($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'titulo'  => '¡Completado!',
                    'mensaje' => 'La Oferta Educativa ha sido eliminada permanentemente del sistema.',
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
                    'mensaje' => 'La Oferta Educativa tiene registros vinculados. Debe reasignar o borrar esas dependencias antes de eliminarlo de forma definitiva.',
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
}
