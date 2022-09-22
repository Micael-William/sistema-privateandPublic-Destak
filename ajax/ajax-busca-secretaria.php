<?php
	require_once("lib/DataValidator.php");
	require_once("models/SecretariaModel.php");

	$term = $_REQUEST['term'];	
	$arrProc = array();
	
	if( isset($term) && !DataValidator::isEmpty($term) ){
		
		$retorno = SecretariaModel::lista(  null, $term );
		
		if( !DataValidator::isEmpty($retorno['secretarias']) ){
			foreach($retorno['secretarias'] as $sec){
				$arr = "COD. " . $sec->getId() . ' - ' . $sec->getNome();
				array_push($arrProc, $arr);
			}
		}
	}
	
	echo json_encode($arrProc);
?>