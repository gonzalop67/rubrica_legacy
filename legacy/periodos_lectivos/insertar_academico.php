<?php
header('Content-Type: application/json; charset=utf-8');
require_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

$id_periodo_lectivo = isset($_POST['id_periodo_lectivo']) ? intval($_POST['id_periodo_lectivo']) : 0;
$id_tipo_periodo    = isset($_POST['id_tipo_periodo']) ? intval($_POST['id_tipo_periodo']) : 0;
$pe_nombre          = isset($_POST['pe_nombre']) ? addslashes(trim($_POST['pe_nombre'])) : '';
$pe_abreviatura     = isset($_POST['pe_abreviatura']) ? strtoupper(addslashes(trim($_POST['pe_abreviatura']))) : '';
$pe_ponderacion     = isset($_POST['pe_ponderacion']) ? floatval($_POST['pe_ponderacion']) : 0;
$pe_fecha_inicio    = isset($_POST['pe_fecha_inicio']) ? addslashes(trim($_POST['pe_fecha_inicio'])) : '';
$pe_fecha_fin       = isset($_POST['pe_fecha_fin']) ? addslashes(trim($_POST['pe_fecha_fin'])) : '';
$pe_orden           = isset($_POST['pe_orden']) ? intval($_POST['pe_orden']) : 1;

if ($id_periodo_lectivo <= 0 || $id_tipo_periodo <= 0 || empty($pe_nombre) || empty($pe_abreviatura)) {
    echo json_encode(['success' => false, 'message' => 'Complete todos los campos obligatorios.']);
    exit;
}

if ($pe_fecha_fin <= $pe_fecha_inicio) {
    echo json_encode(['success' => false, 'message' => 'La fecha de fin debe ser posterior a la de inicio.']);
    exit;
}

$sql = "INSERT INTO sw_periodo_evaluacion 
        (id_periodo_lectivo, id_tipo_periodo, pe_nombre, pe_abreviatura, pe_ponderacion, pe_fecha_inicio, pe_fecha_fin, pe_orden) 
        VALUES 
        ($id_periodo_lectivo, $id_tipo_periodo, '$pe_nombre', '$pe_abreviatura', $pe_ponderacion, '$pe_fecha_inicio', '$pe_fecha_fin', $pe_orden)";

$resultado = $db->consulta($sql);

if ($resultado) {
    echo json_encode([
        'success' => true,
        'message' => 'Bloque académico registrado correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el bloque académico en la base de datos.'
    ]);
}
