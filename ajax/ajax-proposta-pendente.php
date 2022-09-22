<?php
	require_once("lib/DataValidator.php");
	require_once("models/PropostaModel.php");

	$proposta_id = (isset($_REQUEST['proposta_id'])) ? $_REQUEST['proposta_id'] : '';
	$novo_estado = (isset($_REQUEST['novo_estado'])) ? $_REQUEST['novo_estado'] : '';
	$novo_estado = ($novo_estado == "true") ? "S" : "N";
	
	$msg = null;
		
	if( isset($proposta_id) && !DataValidator::isEmpty($proposta_id) ){	
				
		try{
			if( in_array($novo_estado,array("S","N")) ) {
				echo (PropostaModel::setPropostaPendencia( $proposta_id, $novo_estado ) ) ? "sucesso" : "Proposta: Erro na alteração de Pendencia";
			}
		} 
		catch(UserException $e){
			$msg = $e->getMessage();
		}
		
	}	
?>