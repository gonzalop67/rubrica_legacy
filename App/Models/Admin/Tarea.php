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
}
