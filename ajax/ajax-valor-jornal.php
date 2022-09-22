<?php
	require_once("lib/DataValidator.php");
	require_once("models/JornalModel.php");

	$jornal_id = $_REQUEST['jornal_id'];
	
	$msg = null;
	$valor = 0;
	$valor_dje = 0;
		
	if( isset($jornal_id) && 
		!DataValidator::isEmpty($jornal_id)
	){	
				
		try{
			$jornal = JornalModel::getById( $jornal_id );
			
			if( !DataValidator::isEmpty($jornal) && 
				!DataValidator::isEmpty($jornal->getCusto())
			){

				$valor = !DataValidator::isEmpty($jornal->getCusto()->getValorPadrao()) ? $jornal->getCusto()->getValorPadrao() : '0,00';	
				$valor_dje = !DataValidator::isEmpty($jornal->getCusto()->getValorDje()) ? $jornal->getCusto()->getValorDje() : '0,00';
				
			}
				
		} 
		catch(UserException $e){
			$msg = $e->getMessage();
		}
		
	}	
			
	echo json_encode( array('valor_padrao' => $valor, 'valor_dje' => $valor_dje) );
?>