<?php
	require_once("lib/DataValidator.php");
	require_once("models/SecretariaModel.php");

	$estado = @$_POST['item_id'];	
	$html = null;
			
	$secretarias = SecretariaModel::listaByEstado( isset($estado) && !DataValidator::isEmpty($estado) ? $estado : null );
	
	if( !DataValidator::isEmpty($secretarias) ){
		$html .= '<option value="0">Selecione</option>';
		foreach($secretarias as $sec){
			$html .= '<option value="' . $sec->getId() . '">' . $sec->getNome() . '</option>';
		}
	}
	
	echo $html;
?>