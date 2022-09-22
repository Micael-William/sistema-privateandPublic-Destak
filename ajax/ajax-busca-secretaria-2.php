<?php
	require_once("lib/DataValidator.php");
	require_once("models/SecretariaModel.php");

	$term = (isset($_REQUEST['term'])) ? $_REQUEST['term'] : null ;
        $uf = (isset($_REQUEST['uf'])) ? $_REQUEST['uf'] : null ;
        $arrProc = array();
	
	if( isset($term) && !DataValidator::isEmpty($term) ){
		
		$retorno = SecretariaModel::lista(  null, $term, $uf);
		
		if( !DataValidator::isEmpty($retorno['secretarias']) ){
			foreach($retorno['secretarias'] as $sec){
				$arr = "COD. " . $sec->getId() . ' - ' . $sec->getEstado() . ' - ' . $sec->getNome();
				array_push($arrProc, $arr);
			}
		}
	}
	
	echo json_encode($arrProc);
?>