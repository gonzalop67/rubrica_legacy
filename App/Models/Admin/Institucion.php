<?php

namespace App\Models\Admin;

use App\Models\Model;

class Institucion extends Model
{
    protected string $table = 'sw_institucion';
    protected string $primaryKey = 'id_institucion';
    protected array $fillable = [
        'admin_id', 
        'in_nombre',
        'in_direccion',
        'in_telefono',
        'in_regimen',
        'in_nom_rector',
        'in_genero_rector',
        'in_nom_vicerrector',
        'in_genero_vicerrector',
        'in_nom_secretario',
        'in_genero_secretario',
        'in_email',
        'in_url',
        'in_logo',
        'in_amie',
        'in_ciudad',
        'in_copiar_y_pegar'
    ];

    public function obtenerPosiblesAdministradores()
    {
        $sql = "SELECT u.id_usuario, 
                       nombre_corto, 
                       us_foto
                  FROM sw_usuario u,
                       sw_persona pe,
                       sw_usuario_perfil up,
                       sw_perfil p  
                 WHERE u.id_usuario = up.id_usuario 
                   AND p.id_perfil = up.id_perfil 
                   AND pe.id_persona = u.persona_id 
                   AND pe_slug = 'administrador-de-ue' 
                   AND us_activo = 1 
                 ORDER BY primer_apellido, segundo_apellido, primer_nombre, segundo_nombre";

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            die("Error en la consulta SQL: " . $this->connection->error);
        }

        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }
}