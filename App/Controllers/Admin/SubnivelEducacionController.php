<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Models\Admin\NivelEducacion;
use App\Models\Admin\SubnivelEducacion;

class SubnivelEducacionController extends Controller
{
    protected SubnivelEducacion $subnivelModel;
    protected NivelEducacion $nivelModel;

    public function __construct()
    {
        parent::__construct(); // <--- ESTO ES OBLIGATORIO
        $this->subnivelModel = new SubnivelEducacion;
        $this->nivelModel    = new NivelEducacion;
    }

    /**
     * Muestra el listado del recurso.
     */
    public function index()
    {
        $title = 'Listado de Subniveles de Educación';

        $search = trim($_GET['search'] ?? '');

        $this->subnivelModel
            ->select(
                'sw_sub_nivel_educacion.*',
                'sw_nivel_educacion.nombre AS nivel_nombre'
            )
            ->join(
                'sw_nivel_educacion',
                'sw_nivel_educacion.id',
                '=',
                'sw_sub_nivel_educacion.nivel_id AND sw_nivel_educacion.deleted_at IS NULL'
            );

        if ($search !== '') {
            // 1. Creamos la estructura SQL agrupada con paréntesis para proteger la lógica
            $this->subnivelModel->where = "(sw_sub_nivel_educacion.nombre LIKE ? OR sw_sub_nivel_educacion.slug LIKE ? OR sw_nivel_educacion.nombre LIKE ?)";

            // 2. Preparamos los comodines de forma segura
            $term = "%{$search}%";

            // 3. Pasamos los valores al arreglo que procesará el prepare del ORM
            $this->subnivelModel->values = [$term, $term, $term];
        }

        // El ORM inyectará de forma automática el ORDER BY y resolverá la paginación
        $subniveleseducacion = $this->subnivelModel
            ->orderBy('sw_nivel_educacion.orden')
            ->orderBy('sw_sub_nivel_educacion.orden')
            ->paginate(10);

        return $this->view('admin.subniveleseducacion.index', compact('title', 'subniveleseducacion'));
    }

    public function wastebasket()
    {
        $title = "Papelera de Subniveles de Educación";
        $search = trim($_GET['search'] ?? "");

        // 1. Iniciamos el filtro de borrados lógicos y el JOIN para traer el nombre del nivel
        $query = $this->subnivelModel
            ->onlyTrashed()
            ->select(
                'sw_sub_nivel_educacion.*',
                'sw_nivel_educacion.nombre AS nivel_nombre'
            )
            ->join(
                'sw_nivel_educacion',
                'sw_nivel_educacion.id',
                '=',
                'sw_sub_nivel_educacion.nivel_id'
            );

        if ($search !== "") {
            $table = $this->subnivelModel->getTable(); // 'sw_sub_nivel_educacion'

            // 2. Solo la condición de búsqueda entre paréntesis (onlyTrashed ya inyecta AND sw_sub_nivel_educacion.deleted_at IS NOT NULL)
            $query->where = "({$table}.nombre LIKE ? OR {$table}.slug LIKE ? OR sw_nivel_educacion.nombre LIKE ?)";

            $term = "%{$search}%";
            $query->values = [$term, $term, $term];
        }

        // 3. Ordenamos calificando la columna para evitar ambigüedad con sw_nivel_educacion.deleted_at
        $subniveleseducacion = $query
            ->orderBy('sw_sub_nivel_educacion.deleted_at', 'DESC')
            ->paginate(5);

        // 4. Renderizamos la vista de la papelera
        return $this->view('admin.subniveleseducacion.wastebasket', compact('subniveleseducacion', 'title'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     */
    public function create()
    {
        $title = 'Crear Subnivel de Educación';
        $niveles = $this->nivelModel->orderBy('orden', 'ASC')->get();

        return $this->view('admin.subniveleseducacion.create', compact('title', 'niveles'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        $nivelId        = (int)($_POST['nivel_id'] ?? 0);
        $nombre         = trim($_POST['nombre'] ?? '');
        $slug           = trim($_POST['slug'] ?? '');
        $esBachillerato = isset($_POST['es_bachillerato']) ? 1 : 0;
        $orden          = (int)($_POST['orden'] ?? 0);

        $errors = [];
        if ($nivelId <= 0) $errors['nivel_id'] = 'Debe seleccionar un nivel de educación.';
        if ($nombre === '') $errors['nombre'] = 'El nombre es obligatorio.';
        if ($slug === '')   $errors['slug']   = 'El slug es obligatorio.';

        if (!empty($errors)) {
            return json_encode(['success' => false, 'errors' => $errors]);
        }

        if ($this->subnivelModel->exists('slug', $slug)) {
            return json_encode([
                'success' => false,
                'errors'  => ['slug' => 'Este slug ya está registrado en otro subnivel.']
            ]);
        }

        if ($orden <= 0) {
            $orden = $this->subnivelModel->siguienteOrdenPorNivel($nivelId);
        }

        $this->subnivelModel->create([
            'nivel_id'        => $nivelId,
            'nombre'          => $nombre,
            'slug'            => $slug,
            'es_bachillerato' => $esBachillerato,
            'orden'           => $orden
        ]);

        return json_encode([
            'success' => true,
            'message' => 'Subnivel de educación creado correctamente.'
        ]);
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit(int $id)
    {
        $subnivel = $this->subnivelModel->find($id);

        if (!$subnivel) {
            header('Location: /rubrica/subniveles_educacion');
            exit;
        }

        $niveles = $this->nivelModel->orderBy('orden', 'ASC')->get();

        return $this->view('admin.subniveleseducacion.edit', [
            'title'               => 'Editar Subnivel de Educación',
            'subnivel_educacion'  => $subnivel,
            'niveles'             => $niveles
        ]);
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     */
    public function update(int $id)
    {
        $nivelId        = (int)($_POST['nivel_id'] ?? 0);
        $nombre         = trim($_POST['nombre'] ?? '');
        $slug           = trim($_POST['slug'] ?? '');
        $esBachillerato = isset($_POST['es_bachillerato']) ? 1 : 0;
        $orden          = (int)($_POST['orden'] ?? 0);

        $errors = [];
        if ($nivelId <= 0) $errors['nivel_id'] = 'Debe seleccionar un nivel de educación.';
        if ($nombre === '') $errors['nombre'] = 'El nombre es obligatorio.';
        if ($slug === '')   $errors['slug']   = 'El slug es obligatorio.';

        if (!empty($errors)) {
            return json_encode(['success' => false, 'errors' => $errors]);
        }

        if ($this->subnivelModel->exists('slug', $slug, $id)) {
            return json_encode([
                'success' => false,
                'errors'  => ['slug' => 'Este slug ya está registrado en otro subnivel.']
            ]);
        }

        $this->subnivelModel->update($id, [
            'nivel_id'        => $nivelId,
            'nombre'          => $nombre,
            'slug'            => $slug,
            'es_bachillerato' => $esBachillerato,
            'orden'           => $orden
        ]);

        return json_encode([
            'success' => true,
            'message' => 'Subnivel de educación actualizado correctamente.'
        ]);
    }

    /**
     * Elimina un recurso específico de la base de datos.
     */
    public function delete(int $id)
    {
        $this->subnivelModel->delete($id);
        return json_encode([
            'success' => true,
            'message' => 'El subnivel de educación ha sido enviado a la papelera.'
        ]);
    }

    public function restore(int $id)
    {
        $this->subnivelModel->restore($id);
        return json_encode([
            'success' => true,
            'message' => 'El subnivel de educación ha sido restaurado.'
        ]);
    }

    public function destroy(int $id)
    {
        try {
            $this->subnivelModel->forceDelete($id);
            return json_encode([
                'success' => true,
                'message' => 'El subnivel de educación ha sido eliminado permanentemente.'
            ]);
        } catch (\mysqli_sql_exception $e) {
            if ($e->getCode() === 1451) {
                return json_encode([
                    'success' => false,
                    'message' => 'No se puede eliminar porque tiene cursos o registros asociados.'
                ]);
            }
            return json_encode([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ]);
        }
    }
}
