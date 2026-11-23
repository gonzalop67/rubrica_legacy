<?php

namespace App\Models\Admin;

use App\Models\Model;

class Tarea extends Model
{
    protected string $table = 'sw_tarea';
    protected string $primaryKey = 'id';
    
    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'tarea',
        'hecho',
        'fecha',
        'deleted_at'
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true; 

    public function validate(array $data, ?int $id = null): bool
    {
        $this->errors = [];

        // Limpiar espacios múltiples en la tarea
        $tarea = preg_replace('/\s+/', ' ', trim($data['tarea'] ?? ''));

        // -------------------------------------------------------------
        // VALIDACIÓN: TAREA
        // -------------------------------------------------------------
        if (empty($tarea)) {
            $this->errors['tarea'] = "El campo Tarea es obligatorio.";
        } elseif ($this->exists('tarea', $tarea, $id)) {
            $this->errors['tarea'] = "Ya existe la Tarea en la base de datos.";
        }

        return empty($this->errors);
    }
}
