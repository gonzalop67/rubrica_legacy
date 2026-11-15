<?php
header('Content-Type: text/html; charset=utf-8');

require_once "../scripts/clases/class.mysql.php";
$db = new MySQL();

// Ajusta 'tp_descripcion' si la columna de texto en sw_tipo_periodo tiene otro nombre (ej. tp_nombre)
$sql = "SELECT id_tipo_periodo, tp_descripcion 
        FROM sw_tipo_periodo 
        ORDER BY id_tipo_periodo ASC";

$resultado = $db->consulta($sql);

$html = "";
if ($resultado && $db->num_rows($resultado) > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $id = intval($row['id_tipo_periodo']);
        $nombre = htmlspecialchars($row['tp_descripcion'], ENT_QUOTES, 'UTF-8');
        $html .= "<option value=\"{$id}\">{$nombre}</option>";
    }
}

echo $html;
