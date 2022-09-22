<?php			
require_once("lib/DataValidator.php");
require_once("controllers/IndexController.php");
require_once("controllers/UsuarioController.php");
require_once("classes/EstadosEnum.class.php");

if ( !isset($_SESSION) )
	@session_start();

if ( !isset($_SESSION[UsuarioController::LOGIN]) || is_null($_SESSION[UsuarioController::LOGIN]) || DataValidator::isEmpty($_SESSION[UsuarioController::LOGIN]) ) {
	session_destroy();
	IndexController::indexAction();
} else{
	$usuario_logado = $_SESSION[UsuarioController::LOGIN];	 
}
?>