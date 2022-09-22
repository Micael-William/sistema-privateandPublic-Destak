<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");

/**
 * @package acril destac
 * @author Monica Cosme
 * @version 1.0
 */
class IndexController
{

	public static function indexAction()
	{
		self::loginAction();
	}

	public static function loginAction()
	{

		if (DataValidator::isEmpty(session_id()))
			session_start();

		$view = new View('views/login.php');
		$view->showContents();
	}

	public static function logoutAction()
	{
		session_start();

		$_SESSION = array();
		session_destroy();

		self::loginAction();
	}
}
