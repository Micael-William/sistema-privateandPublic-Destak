<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/AcompanhamentoStatusModel.php");

/**
 * @package destak publicidade
 * @author Gerson Luis Vertematti
 * @version 1.0
 */
class StatusController
{

	const LOGIN = "login";

	public function indexAction()
	{

		$acompanhamento_status = AcompanhamentoStatusModel::lista();

		$view = new View('views/status.php');
		$view->setParams(array('acompanhamento_status' => $acompanhamento_status));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$status = null;
		$msg = null;

		try {
			if (isset($_REQUEST['status_id']) && !DataValidator::isEmpty($_REQUEST['status_id']))
				$status = AcompanhamentoStatusModel::getById($_REQUEST['status_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-status.php');
		$view->setParams(array('mensagem' => $msg, 'status' => $status));
		$view->showContents();
	}

	public static function buscaAction()
	{
		$msg = null;
		$usuarios = null;

		$busca_status_pai = isset($_REQUEST['busca_status_pai']) && !DataValidator::isEmpty($_REQUEST['busca_status_pai']) ? $_REQUEST['busca_status_pai'] : null;
		$busca_status = isset($_REQUEST['busca_status']) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST['busca_status'] : null;

		$status = AcompanhamentoStatusModel::lista(
			$busca_status_pai,
			$busca_status
		);

		$view = new View('views/status.php');
		$view->setParams(array('acompanhamento_status' => $status));
		$view->showContents();
	}

	public function salvaAction()
	{
		$msg = null;
		$sucesso = null;

		$status = new AcompanhamentoStatus();
		$status->setId(isset($_POST['status_id']) && !DataValidator::isEmpty($_POST['status_id']) ? $_POST['status_id'] : 0);
		$status->setParentId(isset($_POST['parent_id']) && !DataValidator::isEmpty($_POST['parent_id']) ? DataFilter::numeric($_POST['parent_id']) : null);
		$status->setStatus(isset($_POST['nome_status']) && !DataValidator::isEmpty($_POST['nome_status']) ? $_POST['nome_status'] : null);
		$status->setDescricao(isset($_POST['descricao']) && !DataValidator::isEmpty($_POST['descricao']) ? $_POST['descricao'] : null);

		if (!DataValidator::isEmpty($status->getId()))
			$msg = AcompanhamentoStatusModel::update($status);
		else
			$msg = AcompanhamentoStatusModel::insert($status);

		try {
			if (isset($_REQUEST['status_id']) && !DataValidator::isEmpty($_REQUEST['status_id']))
				$status = AcompanhamentoStatusModel::getById($_REQUEST['status_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($status->getId()) && DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Status&acao=index");
		} elseif (!DataValidator::isEmpty($status->getId()) && DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-status.php');
			$view->setParams(array('sucesso' => 'Status alterado com sucesso.', 'status' => $status));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-status.php');
			$view->setParams(array('mensagem' => $msg, 'status' => $status));
			$view->showContents();
		}
	}

	public static function excluiAction()
	{
		$status = null;
		$msg = null;

		$status_id = isset($_REQUEST['status_id']) && !DataValidator::isEmpty($_REQUEST['status_id']) ? $_REQUEST['status_id'] : 0;
		$msg = AcompanhamentoStatusModel::delete($status_id);

		if (DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Status&acao=index");
		} else {
			$status = AcompanhamentoStatusModel::getById($status_id);
			$view = new View('views/gerenciar-status.php');
			$view->setParams(array('mensagem' => $msg, 'status' => $status));
			$view->showContents();
		}
	}
}
