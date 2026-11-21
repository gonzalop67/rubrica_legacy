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
        $title = 'Crear TareaController';
        // return $this->view('admin.tarea.create', compact('title'));
    }

    /**
     * Almacena un recurso recién creado en la base de datos.
     */
    public function store()
    {
        // $this->model->create($_POST);
        // return redirect('/tarea');
    }

    /**
     * Muestra un recurso específico.
     */
    public function show($id)
    {
        // $data = $this->model->find($id);
        // return $this->view('admin.tarea.show', compact('data'));
    }

    /**
     * Muestra el formulario para editar un recurso específico.
     */
    public function edit($id)
    {
        $title = 'Editar TareaController';
        // $data = $this->model->find($id);
        // return $this->view('admin.tarea.edit', compact('data', 'title'));
    }

    /**
     * Actualiza un recurso específico en la base de datos.
     */
    public function update($id)
    {
        // $this->model->update($id, $_POST);
        // return redirect('/tarea');
    }

    /**
     * Elimina un recurso específico de la base de datos.
     */
    public function destroy($id)
    {
        // $this->model->delete($id);
        // return redirect('/tarea');
    }
}
