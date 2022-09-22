<?php
require_once("lib/DataValidator.php");
require_once("models/AdvogadoModel.php");
require_once("models/ObservacaoModel.php");
require_once("models/JornalModel.php");
require_once("models/CustoPropostaModel.php");
require_once("models/PropostaModel.php");
require_once("models/SacadoModel.php");

$item_id = (isset($_REQUEST['item_id'])) ? $_REQUEST['item_id'] : '';
$acao = (isset($_REQUEST['acao'])) ? $_REQUEST['acao'] : '';

//exclusao da secretaria do jornal
$jornal_id = (isset($_REQUEST['jornal_id'])) ? $_REQUEST['jornal_id'] : '';

if (
	isset($item_id) && !DataValidator::isEmpty($item_id) &&
	isset($acao) && !DataValidator::isEmpty($acao)
) {

	//ADVOGADO
	if ($acao == 'advogado-tel') {

		$msg = AdvogadoModel::excluiTelefone($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'advogado-email') {

		$msg = AdvogadoModel::excluiEmail($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'advogado-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'advogado');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	//JORNAL
	if ($acao == 'jornal-tel') {

		$msg = JornalModel::excluiTelefone($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'jornal-email') {

		$msg = JornalModel::excluiEmail($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'jornal-secretaria') {

		$msg = JornalModel::excluiSecretaria($item_id, $jornal_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'jornal-cidade') {

		$msg = JornalModel::excluiCidade($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	//PROPOSTA
	if ($acao == 'valor-dje') {

		$msg = CustoPropostaModel::excluiCusto($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'proposta-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'proposta');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	//ACOMPANHAMENTO
	if ($acao == 'acompanhamento-adv-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'acompanhamento-adv-obs');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'acompanhamento-fin-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'acompanhamento-fin-obs');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'acompanhamento-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'acompanhamento-obs');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'acompanhamento-boleto-obs') {

		$msg = ObservacaoModel::excluiObservacao($item_id, 'acompanhamento-boleto-obs');
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'sacado-email') {

		$msg = SacadoModel::excluiEmail($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}

	if ($acao == 'acompanhamento-fin-email') {

		$msg = SacadoAcompanhamentoModel::excluiEmail($item_id);
		if (DataValidator::isEmpty($msg))
			echo 'sucesso';
	}
} else {
	echo 'sucesso';
}
