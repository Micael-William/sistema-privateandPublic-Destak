<?php
require_once("lib/View.php");
require_once("controllers/UsuarioController.php");

/**
 * @package acril destac
 * @author Monica Cosme
 * @version 1.0
 */
class InicioController
{

	public function indexAction()
	{
		$view = new View('views/bem-vindo.php');
		$view->showContents();
	}
}
