<?php
	//Busca jornal pela cidade
	require_once("lib/DataValidator.php");
	require_once("models/JornalModel.php");

	$term = $_REQUEST['term'];	
	$arrProc = array();
		
	if( isset($term) && !DataValidator::isEmpty($term) ){	
	
		$retorno = JornalModel::lista( null, 0, null, null, $term );
	
		if( !DataValidator::isEmpty($retorno['jornais']) ){
			foreach($retorno['jornais'] as $jornal){			
				$arr = $jornal->getNome() . ' - ' . $jornal->getStatusDesc() . ' - COD. ' . $jornal->getId();
				array_push($arrProc, $arr);
			}
		}
		
	}
		
	echo json_encode($arrProc);
?>