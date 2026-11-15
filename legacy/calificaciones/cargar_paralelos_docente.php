<?php
// Indicamos que la salida estricta será formato JSON
header('Content-Type: application/json; charset=utf-8');
require_once("../scripts/clases/class.mysql.php");
$db = new MySQL();

ini_set('session.cookie_samesite', 'Lax');
session_start();

$id_usuario = isset($_SESSION["id_usuario"]) ? intval($_SESSION["id_usuario"]) : 0;
$id_periodo_lectivo = isset($_SESSION["id_periodo_lectivo"]) ? intval($_SESSION["id_periodo_lectivo"]) : 0;

// Consulta unificada con INNER JOIN para traer paralelos y materias en una sola petición
$sql = "SELECT d.id_paralelo,
               c.cu_nombre, 
               pa.pa_nombre, 
               e.es_figura,
               a.id_asignatura,
               a.as_nombre AS nombre_asignatura,
               a.id_tipo_asignatura AS tipo_asignatura
          FROM sw_distributivo d
         INNER JOIN sw_paralelo pa ON d.id_paralelo = pa.id_paralelo
         INNER JOIN sw_curso c ON pa.id_curso = c.id_curso
         INNER JOIN sw_especialidad e ON c.id_especialidad = e.id_especialidad
         INNER JOIN sw_asignatura a ON d.id_asignatura = a.id_asignatura
         WHERE d.id_usuario = $id_usuario
           AND d.id_periodo_lectivo = $id_periodo_lectivo
         ORDER BY c.id_curso ASC, pa.pa_orden ASC, a.as_nombre ASC";

$consulta = $db->consulta($sql);
$num_total_registros = $db->num_rows($consulta);

$resultado = [];

if ($num_total_registros > 0) {
  $paralelos_agrupados = [];

  while ($row = $db->fetch_assoc($consulta)) {
    $id_paralelo = $row["id_paralelo"];

    // Si el paralelo aún no existe en nuestro arreglo maestro, lo creamos
    if (!isset($paralelos_agrupados[$id_paralelo])) {
      $nombre_completo = $row["cu_nombre"] . " " . $row["pa_nombre"] . " - " . $row["es_figura"];

      $paralelos_agrupados[$id_paralelo] = [
        "id_paralelo"     => $id_paralelo,
        "nombre_paralelo" => $nombre_completo,
        "asignaturas"     => []
      ];
    }

    // Añadimos la asignatura al arreglo interno de ese paralelo
    $paralelos_agrupados[$id_paralelo]["asignaturas"][] = [
      "id_asignatura"     => $row["id_asignatura"],
      "nombre_asignatura" => $row["nombre_asignatura"],
      "tipo_asignatura"   => $row["tipo_asignatura"]
    ];
  }

  // Reindexamos el arreglo para que json_encode genere una lista limpia [...]
  $resultado = array_values($paralelos_agrupados);
}

// Imprimimos el JSON final (si no hay registros devolverá [])
echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
