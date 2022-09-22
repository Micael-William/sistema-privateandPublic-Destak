<?php
	require_once("lib/DataValidator.php");
	require_once("lib/DataFilter.php");
	require_once("models/UsuarioModel.php");
	require_once("phpmailer/class.phpmailer.php");

	$cpf = $_REQUEST['cpf'];
	$msg = null;
	
	if( isset($cpf) && 
		!DataValidator::isEmpty($cpf)
	){
		
		try{
			$msg = UsuarioModel::envioSenha( DataFilter::numeric($cpf) );				
			if( DataValidator::isEmpty($msg) )
				echo 'sucesso';				
		}
		catch(UserException $e){
			$msg = $e->getMessage();
		}
				
	}
?>