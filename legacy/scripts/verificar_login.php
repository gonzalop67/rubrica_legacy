<?php
sleep(1);
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
require_once("clases/class.mysql.php");
require_once("clases/class.usuarios.php");
require_once("clases/class.periodos_lectivos.php");
$usuarios = new usuarios();
//recibo las variables de tipo post de la pagina login.php
$login = $_POST['uname'];
$clave = $_POST['passwd'];
$id_periodo_lectivo = $_POST['cboPeriodo'];
$periodo_lectivo = new periodos_lectivos();
$periodo_lectivo = $periodo_lectivo->obtenerPeriodoLectivo($id_periodo_lectivo);
$id_modalidad = $periodo_lectivo->id_modalidad;
//$id_perfil = $_POST['cboPerfil'];
//consultar a la tabla usuario si existe un usuario en la tabla
$_SESSION['usuario_logueado'] = false;
if ($usuarios->existeUsuario($login, $clave)) {
	$id_usuario = $usuarios->obtenerIdUsuario($login, $clave);
	$datosUsuario = $usuarios->obtenerUsuario($id_usuario); // Ajusta al método de tu class.usuarios.php
	
	$_SESSION['usuario_logueado'] = true;
	$_SESSION['id_periodo_lectivo'] = $id_periodo_lectivo;
	$_SESSION['id_modalidad'] = $id_modalidad;
	$_SESSION['id_usuario'] = $id_usuario;
	$_SESSION['us_avatar'] = !empty($datosUsuario->us_foto) ? $datosUsuario->us_foto : 'default.png';

	$_SESSION['cambio_paralelo'] = 0;

	// GUARDAMOS LA LISTA COMPLETA EN LA SESIÓN
	$perfilesUsuario = $usuarios->obtenerPerfilesPorUsuario($id_usuario);
	$_SESSION['perfiles_asociados'] = $perfilesUsuario;

	// 🔥 ASIGNACIÓN ASÍNCRONA DINÁMICA DEL PRIMER PERFIL
	$primerPerfil = isset($perfilesUsuario[0]) ? $perfilesUsuario[0] : null;
	$id_perfil    = $primerPerfil ? $primerPerfil['id_perfil'] : null;

	/*echo "<pre>";
    	print_r($primerPerfil);
    	echo "</pre>";
		exit;*/


	$_SESSION['id_perfil'] = $id_perfil;

	$respuesta = [
		'id_usuario' => encrypter::encrypt($id_usuario),
		'id_perfil'  => $id_perfil
	];

	echo json_encode($respuesta);
} else {
	echo json_encode(array('error' => true));
}
