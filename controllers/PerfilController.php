<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/PerfilModel.php");
require_once("controllers/UsuarioController.php");

/**
 * @package acril destac
 * @author Monica Cosme
 * @version 1.0
 */
class PerfilController
{

	public function indexAction()
	{

		$perfis = PerfilModel::lista();

		$view = new View('views/niveis.php');
		$view->setParams(array('perfis' => $perfis));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$perfil = null;
		$msg = null;

		try {
			if (isset($_REQUEST['perfil_id']) and !DataValidator::isEmpty($_REQUEST['perfil_id']))
				$perfil = PerfilModel::getById($_REQUEST['perfil_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-nivel.php');
		$view->setParams(array('mensagem' => $msg, 'perfil' => $perfil));
		$view->showContents();
	}

	public function salvaAction()
	{
		$msg = null;
		$sucesso = null;

		$perfil = new Perfil();
		$perfil->setId(isset($_POST['perfil_id']) && !DataValidator::isEmpty($_POST['perfil_id']) ? $_POST['perfil_id'] : 0);
		$perfil->setNome(isset($_POST['nome']) && !DataValidator::isEmpty($_POST['nome']) ? $_POST['nome'] : null);

		for ($r = 1; $r <= 11; $r++) {
			if (isset($_POST['leitura' . $r]) || isset($_POST['edicao' . $r]) || isset($_POST['exclusao' . $r])) {

				$acao = null;
				$responsabilidade = new Responsabilidade();
				$responsabilidade->setId(isset($_POST['resp_id' . $r]) && !DataValidator::isEmpty($_POST['resp_id' . $r]) ? $_POST['resp_id' . $r] : 0);

				if (isset($_POST['leitura' . $r]))
					$acao = 'L';
				if (isset($_POST['edicao' . $r]))
					$acao = 'E';
				if (isset($_POST['exclusao' . $r]))
					$acao = 'D';

				$responsabilidade->setAcao($acao);
				$perfil->setResponsabilidade($responsabilidade);
			}
		}

		if (DataValidator::isEmpty($perfil->getId()))
			$msg = PerfilModel::insert($perfil);
		else
			$msg = PerfilModel::update($perfil);


		if (DataValidator::isEmpty($_POST['perfil_id']) && DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Perfil&acao=index");
		} elseif (!DataValidator::isEmpty($_POST['perfil_id']) && DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-nivel.php');
			$view->setParams(array('sucesso' => 'Nível alterado com sucesso.', 'perfil' => $perfil));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-nivel.php');
			$view->setParams(array('mensagem' => $msg, 'perfil' => $perfil));
			$view->showContents();
		}
	}
}
