<?php
	require_once("lib/DataValidator.php");
	require_once("models/AcompanhamentoStatusModel.php");

	$status = $_POST['status_codigo'];	
	$html = null;
			
	$substatuses = AcompanhamentoStatusModel::listaByStatus( isset($status) && !DataValidator::isEmpty($status) ? $status : null );
	
	if( !DataValidator::isEmpty($substatuses) ){
		$html .= '<option value="0">Selecione</option>';
		foreach($substatuses as $status){
			$html .= '<option value="' . $status->getId() . '">' . $status->getStatus() . '</option>';
		}
	}
	
	echo $html;
?>

