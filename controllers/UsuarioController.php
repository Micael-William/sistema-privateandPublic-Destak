<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/UsuarioModel.php");
require_once("models/PerfilModel.php");

/**
 * @package destak publicidade
 * @author Moginnica Cosme
 * @version 1.0
 */
class UsuarioController
{

	const LOGIN = "login";

	public function indexAction()
	{

		$usuarios = UsuarioModel::lista();

		$view = new View('views/usuarios.php');
		$view->setParams(array('usuarios' => $usuarios));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$usuario = null;
		$msg = null;

		try {
			if (isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id']))
				$usuario = UsuarioModel::getById($_REQUEST['usuario_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-usuario.php');
		$view->setParams(array('mensagem' => $msg, 'usuario' => $usuario));
		$view->showContents();
	}

	public static function buscaAction()
	{
		$msg = null;
		$usuarios = null;

		$busca_status = isset($_REQUEST['busca_status']) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST['busca_status'] : null;
		$busca_nome = isset($_REQUEST['busca_nome']) && !DataValidator::isEmpty($_REQUEST['busca_nome']) ? $_REQUEST['busca_nome'] : null;
		$busca_email = isset($_REQUEST['busca_email']) && !DataValidator::isEmpty($_REQUEST['busca_email']) ? $_REQUEST['busca_email'] : null;
		$busca_perfil = isset($_REQUEST['busca_perfil']) && !DataValidator::isEmpty($_REQUEST['busca_perfil']) ? $_REQUEST['busca_perfil'] : 0;

		$usuarios = UsuarioModel::lista(
			$busca_status,
			$busca_nome,
			$busca_email,
			$busca_perfil
		);

		$view = new View('views/usuarios.php');
		$view->setParams(array('usuarios' => $usuarios));
		$view->showContents();
	}

	public function salvaAction()
	{
		$msg = null;
		$sucesso = null;

		$usuario = new Usuario();
		$usuario->setId(isset($_POST['usuario_id']) && !DataValidator::isEmpty($_POST['usuario_id']) ? $_POST['usuario_id'] : 0);
		$usuario->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);
		$usuario->setNome(isset($_POST['nome']) && !DataValidator::isEmpty($_POST['nome']) ? $_POST['nome'] : null);
		$usuario->setCpf(isset($_POST['cpf']) && !DataValidator::isEmpty($_POST['cpf']) ? DataFilter::numeric($_POST['cpf']) : null);
		$usuario->setEmail(isset($_POST['email']) && !DataValidator::isEmpty($_POST['email']) ? $_POST['email'] : null);
		$usuario->setSenha(isset($_POST['senha']) && !DataValidator::isEmpty($_POST['senha']) ? $_POST['senha'] : null);

		$perfil = new Perfil();
		$perfil->setId(isset($_POST['perfil_id']) && !DataValidator::isEmpty($_POST['perfil_id']) ? $_POST['perfil_id'] : 0);
		$usuario->setPerfil($perfil);

		if (!DataValidator::isEmpty($usuario->getId()))
			$msg = UsuarioModel::update($usuario);
		else
			$msg = UsuarioModel::insert($usuario);

		try {
			if (isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id']))
				$usuario = UsuarioModel::getById($_REQUEST['usuario_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($usuario->getId()) && DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Usuario&acao=index");
		} elseif (!DataValidator::isEmpty($usuario->getId()) && DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-usuario.php');
			$view->setParams(array('sucesso' => 'Usuario alterado com sucesso.', 'usuario' => $usuario));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-usuario.php');
			$view->setParams(array('mensagem' => $msg, 'usuario' => $usuario));
			$view->showContents();
		}
	}

	public function loginAction()
	{

		$msg = null;
		$usuario = new Usuario();

		$usuario->setCpf(isset($_POST['cpf']) && !DataValidator::isEmpty($_POST['cpf']) ? DataFilter::numeric($_POST['cpf']) : null);
		$usuario->setSenha(isset($_POST['senha']) && !DataValidator::isEmpty($_POST['senha']) ? $_POST['senha'] : null);

		try {
			$usuario = UsuarioModel::login($usuario);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			session_start();
			$_SESSION[self::LOGIN] = $usuario;

			header("Location: ?controle=Inicio&acao=index");
		} else {
			$view = new View('views/login.php');
			$view->setParams(array('mensagem' => $msg, 'usuario' => $usuario));
			$view->showContents();
		}
	}

	public function logoutAction()
	{
		session_start();
		session_destroy();
		unset($_SESSION[self::LOGIN]);
		header("Location: ?controle=Index&acao=index");
	}

	public static function excluiAction()
	{
		$status = null;
		$msg = null;

		$usuario_id = isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id']) ? $_REQUEST['usuario_id'] : 0;
		$msg = UsuarioModel::delete($usuario_id);

		if (DataValidator::isEmpty($msg)) {
			$usuarios = UsuarioModel::lista();
			$view = new View('views/usuarios.php');
			$view->setParams(array('usuarios' => $usuarios, 'mensagens' => array('sucesso' => 'Usuário excluído com sucesso.')));
			$view->showContents();
		} else {
			$usuario = UsuarioModel::getById($usuario_id);
			$view = new View('views/gerenciar-usuario.php');
			$view->setParams(array('mensagem' => $msg, 'usuario' => $usuario));
			$view->showContents();
		}
	}
}
