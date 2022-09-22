<?php
require_once("lib/DataValidator.php");
require_once("models/AdvogadoModel.php");

$term = $_REQUEST['term'];

if (!isset($_REQUEST['type'])) {
	$arrProc = array();

	$retorno = AdvogadoModel::lista(isset($term) && !DataValidator::isEmpty($term) ? $term : null);

	if (!DataValidator::isEmpty($retorno['advogados'])) {
		foreach ($retorno['advogados'] as $adv) {
			$arr = 'COD. ' . $adv->getId() . ' - ' . $adv->getNome() . (!DataValidator::isEmpty($adv->getOab()) ? ' - OAB ' . $adv->getOab() : '');
			array_push($arrProc, $arr);
		}
	}

	echo json_encode($arrProc);
} else {
	$retorno = AdvogadoModel::getBy(isset($term) && !DataValidator::isEmpty($term) ? $term : null);

	$endereco = $retorno->getEndereco();

	$resultado = array(
		'logradouro' => $endereco->getLogradouro(),
		'numero' => $endereco->getNumero(),
		'complemento' => $endereco->getComplemento(),
		'bairro' => $endereco->getBairro(),
		'cidade' => $endereco->getCidade(),
		'estado' => $endereco->getEstado(),
		'cep' => $endereco->getCep()
	);

	echo json_encode($resultado);
}
