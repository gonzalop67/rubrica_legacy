<?php

namespace App\Models\Admin;

use App\Models\Model;

class SubnivelEducacion extends Model
{
    protected string $table = 'sw_sub_nivel_educacion';
    protected string $primaryKey = 'id';

    // Define los campos que se pueden llenar masivamente
    protected array $fillable = [
        'nivel_id',
        'nombre',
        'slug',
        'es_bachillerato',
        'orden',
    ];

    // Activa o desactiva el Soft Delete según tus necesidades en la tabla
    protected bool $useSoftDeletes = true;

    public function listarPaginado(string $search = '', int $porPagina = 15): array
    {
        $this->select(
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
            $termino = "%{$search}%";
            $this->where('sw_sub_nivel_educacion.nombre', 'LIKE', $termino)
                ->orWhere('sw_nivel_educacion.nombre', 'LIKE', $termino);
        }

        return $this->orderBy('sw_nivel_educacion.orden', 'ASC')
            ->orderBy('sw_sub_nivel_educacion.orden', 'ASC')
            ->paginate($porPagina);
    }

    public function listarEliminadosPaginado(string $search = '', int $porPagina = 15): array
    {
        $this->onlyTrashed()
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

        if ($search !== '') {
            $termino = "%{$search}%";
            $this->where('sw_sub_nivel_educacion.nombre', 'LIKE', $termino)
                ->orWhere('sw_nivel_educacion.nombre', 'LIKE', $termino);
        }

        return $this->orderBy('sw_sub_nivel_educacion.deleted_at', 'DESC')
            ->paginate($porPagina);
    }

    public function siguienteOrdenPorNivel(int $nivelId): int
    {
        $ultimo = $this->where('nivel_id', '=', $nivelId)
            ->orderBy('orden', 'DESC')
            ->first();

        return $ultimo ? ((int)$ultimo['orden'] + 1) : 1;
    }
}
