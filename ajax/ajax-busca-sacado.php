<?php
require_once("lib/DataValidator.php");
require_once("models/SacadoModel.php");

$term = $_REQUEST['term'];
$type = @$_REQUEST['type'];

switch ($type) {
	case "1":
		$retorno = SacadoModel::getBy(isset($term) && !DataValidator::isEmpty($term) ? $term : null);

		$endereco = $retorno->getEndereco();

		$emails = array();

		foreach ($retorno->getEmails() as $email) {
			if ($email->getEnviar() == "S") {
				$emails[] = array(
					"email" => $email->getEmailEndereco()
				);
			}
		}

		$resultado = array(
			'nome' => $retorno->getNome(),
			'cpf_cnpj' => $retorno->getCpfCnpj(),
			'emails' => $emails,
			'logradouro' => $endereco->getLogradouro(),
			'numero' => $endereco->getNumero(),
			'complemento' => $endereco->getComplemento(),
			'bairro' => $endereco->getBairro(),
			'cidade' => $endereco->getCidade(),
			'estado' => $endereco->getEstado(),
			'cep' => $endereco->getCep()
		);

		echo json_encode($resultado);
		break;
	case "2":
		$arrProc = array();
		$retorno = SacadoModel::getByCpfCnpj($term);

		$arrProc = array(
			"existe" => $retorno == null ? "N" : "S"
		);

		echo json_encode($arrProc);
		break;
	default:
		$arrProc = array();

		$retorno = SacadoModel::lista(isset($term) && !DataValidator::isEmpty($term) ? $term : null);

		if (!DataValidator::isEmpty($retorno['sacados'])) {
			foreach ($retorno['sacados'] as $sac) {
				$arr = 'COD. ' . $sac->getId() . ' - ' . $sac->getNome() . (!DataValidator::isEmpty($sac->getCpfCnpj()) ? ' - CPF/CNPJ ' . $sac->getCpfCnpj() : '');
				array_push($arrProc, $arr);
			}
		}

		echo json_encode($arrProc);
}
