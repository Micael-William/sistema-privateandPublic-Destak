<?php
	require_once("lib/DataValidator.php");
	require_once("models/JornalModel.php");

	$secretaria_id = $_POST['item_id'];	
	$html = null;
			
	$jornais = JornalModel::listaBySecretaria( isset($secretaria_id) && !DataValidator::isEmpty($secretaria_id) ? $secretaria_id : 0 );
	
	if( !DataValidator::isEmpty($jornais) ){
		foreach($jornais as $jr){
			$html .= '<option value="' . $jr->getId() . '">' . $jr->getNome() . ' - ' . $jr->getStatusDesc() . ' - ' . (!DataValidator::isEmpty($jr->getDataConfirmacao()) ? date('d/m/Y', strtotime($jr->getDataConfirmacao() )) : 'sem Data de Confirmação') . '</option>';
		}
	}
	
	echo $html;
?>