<?php

namespace App\Models\Admin;

use App\Models\Model;

class Menu extends Model
{
    protected string $table = 'sw_menu';
    protected string $primaryKey = 'id_menu';

    protected array $fillable = [
        'mnu_texto',
        'mnu_enlace',
        'mnu_link',
        'mnu_nivel',
        'mnu_orden',
        'mnu_padre',
        'mnu_publicado',
        'mnu_icono'
    ];

    protected bool $useSoftDeletes = false;

    public function validate(array $data): bool
    {
        $this->errors = [];

        $texto    = trim($data['mnu_texto'] ?? '');
        $enlace   = trim($data['mnu_enlace'] ?? '');
        $perfilId = (int)($data['perfil_id'] ?? 0);

        if ($perfilId <= 0 && empty($data['id_menu'])) {
            $this->errors['perfil_id'] = 'Debe seleccionar un perfil.';
        }

        if ($texto === '') {
            $this->errors['mnu_texto'] = 'El texto del menú es obligatorio.';
        }

        if ($enlace === '') {
            $this->errors['mnu_enlace'] = 'El enlace del menú es obligatorio (use # si es menú padre).';
        }

        return empty($this->errors);
    }
}
