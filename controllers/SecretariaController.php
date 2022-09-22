<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/SecretariaModel.php");
require_once("classes/Paginacao.class.php");
require_once("classes/PesquisaSecretaria.class.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class SecretariaController
{

	const QTD_PAGINACAO = 50;
	const PESQUISA_SECRETARIA = "Resultado-secretaria";

	public function indexAction()
	{
		$retorno = null;

		//Paginação
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;

		$retorno = SecretariaModel::lista('A', null, null, $p, self::QTD_PAGINACAO);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Secretaria');

		$view = new View('views/secretarias.php');
		$view->setParams(array('secretarias' => $retorno['secretarias'], 'paginacao' => $paginacao));
		$view->showContents();
	}

	public static function buscaAction()
	{

		session_start();

		$msg = null;
		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		$retorno = SecretariaModel::lista(
			$pesquisa->getStatus(),
			$pesquisa->getTermo(),
			$pesquisa->getEstado(),
			$pesquisa->getPagina(),
			self::QTD_PAGINACAO
		);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Secretaria');


		$view = new View('views/secretarias.php');
		$view->setParams(array('secretarias' => $retorno['secretarias'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa));
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;

		$secretaria = new Secretaria();
		$secretaria->setId(isset($_POST['secretaria_id']) && !DataValidator::isEmpty($_POST['secretaria_id']) ? $_POST['secretaria_id'] : 0);
		$secretaria->setNome(isset($_POST['secretaria_nome']) && !DataValidator::isEmpty($_POST['secretaria_nome']) ? $_POST['secretaria_nome'] : null);
		$secretaria->setEstado(isset($_POST['estado']) && !DataValidator::isEmpty($_POST['estado']) ? $_POST['estado'] : null);
		$secretaria->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);

		if (!DataValidator::isEmpty($secretaria->getId()))
			$retorno = SecretariaModel::update($secretaria);
		else
			$retorno = SecretariaModel::insert($secretaria);

		if (DataValidator::isEmpty($_POST['secretaria_id']) && DataValidator::isEmpty($retorno['msg'])) {
			header("Location: ?controle=Secretaria&acao=index");
		} elseif (!DataValidator::isEmpty($_POST['secretaria_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$view = new View('views/gerenciar-secretaria.php');
			$view->setParams(array('sucesso' => 'Secretaria alterada com sucesso.', 'secretaria' => $secretaria));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-secretaria.php');
			$view->setParams(array('mensagem' => $retorno['msg'], 'secretaria' => $secretaria));
			$view->showContents();
		}
	}

	public static function detalheAction()
	{
		$secretaria = null;
		$msg = null;

		try {
			if (isset($_REQUEST['secretaria_id']) && !DataValidator::isEmpty($_REQUEST['secretaria_id']))
				$secretaria = SecretariaModel::getBy($_REQUEST['secretaria_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-secretaria.php');
		$view->setParams(array('mensagem' => $msg, 'secretaria' => $secretaria));
		$view->showContents();
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA_SECRETARIA]);

		/**/
		$pesquisa = !isset($_SESSION[self::PESQUISA_SECRETARIA]) ? new PesquisaSecretaria() : $_SESSION[self::PESQUISA_SECRETARIA];

		if (!isset($_SESSION[self::PESQUISA_SECRETARIA]))
			$_SESSION[self::PESQUISA_SECRETARIA] = $pesquisa;

		/**/
		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_SECRETARIA]->getPagina());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_SECRETARIA]->getStatus());
		$pesquisa->setTermo(isset($_REQUEST["busca_termo"]) && !DataValidator::isEmpty($_REQUEST['busca_termo']) ? $_REQUEST["busca_termo"] : $_SESSION[self::PESQUISA_SECRETARIA]->getTermo());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_SECRETARIA]->getEstado());

		return $pesquisa;
	}
}
