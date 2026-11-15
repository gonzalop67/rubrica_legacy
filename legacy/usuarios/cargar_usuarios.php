<?php
header('Content-Type: application/json; charset=utf-8');

// 1. Conexión a la base de datos
require_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

// 2. Leer datos enviados por Fetch (JSON)
$input = json_decode(file_get_contents("php://input"), true);

$start = isset($input['start']) ? intval($input['start']) : 0;
$length = isset($input['length']) ? intval($input['length']) : 10;
$draw = isset($input['draw']) ? intval($input['draw']) : 1;
$searchValue = $input['search']['value'] ?? '';

$searchSafe = addslashes(trim($searchValue));

// Mapeo de columnas para ordenamiento dinámico desde DataTables
$columnasOrden = [
    0 => 'u.id_usuario',
    1 => 'u.us_foto',
    2 => 'nombre',
    3 => 'u.us_login',
    4 => 'u.us_activo',
    5 => 'perfiles'
];

$orderColumnIndex = $input['order']['column'] ?? 2;
$orderDir = (isset($input['order']['dir']) && strtoupper($input['order']['dir']) === 'DESC') ? 'DESC' : 'ASC';
$orderBy = isset($columnasOrden[$orderColumnIndex]) ? $columnasOrden[$orderColumnIndex] . " " . $orderDir : "pe.primer_apellido ASC, pe.primer_nombre ASC";

// SOFT DELETE: Excluir los eliminados del conteo total
$resTotal = $db->consulta("SELECT COUNT(*) AS totalRecords 
                           FROM sw_usuario u 
                           INNER JOIN sw_persona pe ON u.persona_id = pe.id_persona 
                           WHERE u.deleted_at IS NULL AND pe.deleted_at IS NULL");
$totalRecords = $db->fetch_object($resTotal)->totalRecords;

// 3. Consulta con filtrado
$searchQuery = " WHERE u.deleted_at IS NULL AND pe.deleted_at IS NULL ";
if (!empty($searchSafe)) {
    $searchQuery .= " AND (pe.nombre_completo LIKE '%$searchSafe%' 
                        OR pe.primer_apellido LIKE '%$searchSafe%' 
                        OR pe.segundo_apellido LIKE '%$searchSafe%' 
                        OR pe.primer_nombre LIKE '%$searchSafe%' 
                        OR pe.segundo_nombre LIKE '%$searchSafe%' 
                        OR pe.dni LIKE '%$searchSafe%' 
                        OR pe.titulo LIKE '%$searchSafe%' 
                        OR u.us_login LIKE '%$searchSafe%' 
                        OR u.us_email LIKE '%$searchSafe%') ";
}

// Obtener total filtrado
$resFiltered = $db->consulta("SELECT COUNT(*) AS totalFiltered 
                              FROM sw_usuario u 
                              INNER JOIN sw_persona pe ON u.persona_id = pe.id_persona 
                              $searchQuery");
$totalFiltered = $db->fetch_object($resFiltered)->totalFiltered;

// Obtener datos finales
$sql = "SELECT u.id_usuario, 
               u.us_foto, 
               pe.nombre_completo AS nombre, 
               u.us_login, 
               u.us_activo, 
               COALESCE(GROUP_CONCAT(p.pe_nombre SEPARATOR ', '), 'Sin perfil') AS perfiles 
        FROM sw_usuario u 
        INNER JOIN sw_persona pe ON u.persona_id = pe.id_persona 
        LEFT JOIN sw_usuario_perfil up ON u.id_usuario = up.id_usuario 
        LEFT JOIN sw_perfil p ON up.id_perfil = p.id_perfil 
        $searchQuery 
        GROUP BY u.id_usuario, u.us_foto, pe.nombre_completo, pe.primer_apellido, pe.primer_nombre, u.us_login, u.us_activo 
        ORDER BY $orderBy 
        LIMIT $start, $length";

$resultado = $db->consulta($sql);

$data = [];
while ($row = $resultado->fetch_assoc()) {
    if (empty($row['us_foto'])) {
        $row['us_foto'] = 'no-disponible.png';
    }
    $data[] = $row;
}

// 4. Respuesta en formato esperado por DataTables
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
