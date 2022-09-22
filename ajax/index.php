<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
//	ini_set('display_errors', 0);
//	error_reporting(E_ERROR);
	setlocale(LC_ALL, 'pt_BR');
	require_once "lib/Application.php";
	$o_Application = new Application();
	$o_Application->dispatch();
?>
