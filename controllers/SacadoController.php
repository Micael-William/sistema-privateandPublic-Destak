<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/SacadoModel.php");
require_once("classes/Paginacao.class.php");
require_once("classes/PesquisaSacado.class.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class SacadoController
{

	const QTD_PAGINACAO = 100;
	const PESQUISA_SACADO = "Resultado-sacado";

	public function indexAction($msg = array())
	{
		$params = array();
		$retorno = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;

		$retorno = SacadoModel::lista(null, null, null, null, null, null, null, null, null, null, null, $p, self::QTD_PAGINACAO);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Sacado');

		$params['sacados'] = $retorno['sacados'];
		$params['paginacao'] = $paginacao;
		if (isset($msg['sucesso'])) $params['mensagens'] = $msg;

		$view = new View('views/sacados.php');
		$view->setParams($params);
		$view->showContents();
	}

	public static function buscaAction($mensagens = array())
	{

		@session_start();

		$msg = null;
		$retorno = null;
		$pesquisa = self::getPesquisaAction();
		//echo "<pre>";
		//print_r($pesquisa);
		//echo "</pre>";

		$retorno = SacadoModel::lista(
			$pesquisa->getNome(),
			$pesquisa->getStatus(),
			$pesquisa->getEmail(),
			$pesquisa->getCpfCnpj(),
			$pesquisa->getCidade(),
			$pesquisa->getEstado(),
			$pesquisa->getEndereco(),
			$pesquisa->getPagina(),
			self::QTD_PAGINACAO
		);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Sacado');

		$view = new View('views/sacados.php');
		$view->setParams(array('sacados' => $retorno['sacados'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa, 'mensagens' => $mensagens));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$sacado = null;
		$msg = null;

		try {
			if (isset($_REQUEST['sacado_id']) && !DataValidator::isEmpty($_REQUEST['sacado_id']))
				$sacado = SacadoModel::getBy($_REQUEST['sacado_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-sacado.php');
		$view->setParams(array('mensagem' => $msg, 'sacado' => $sacado));
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;
		
		$sacado = new Sacado();
		$sacado->setId(isset($_POST['sacado_id']) && !DataValidator::isEmpty($_POST['sacado_id']) ? $_POST['sacado_id'] : 0);
		$sacado->setNome(isset($_POST['nome']) && !DataValidator::isEmpty($_POST['nome']) ? $_POST['nome'] : null);
		$sacado->setCpfCnpj(isset($_POST['cpf_cnpj']) && !DataValidator::isEmpty($_POST['cpf_cnpj']) ? $_POST['cpf_cnpj'] : null);
		$sacado->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);

		$endereco = new Endereco();
		$endereco->setCep(isset($_POST['cep']) && !DataValidator::isEmpty($_POST['cep']) ? DataFilter::numeric($_POST['cep']) : null);
		$endereco->setLogradouro(isset($_POST['logradouro']) && !DataValidator::isEmpty($_POST['logradouro']) ? $_POST['logradouro'] : null);
		$endereco->setNumero(isset($_POST['numero']) && !DataValidator::isEmpty($_POST['numero']) ? $_POST['numero'] : null);
		$endereco->setComplemento(isset($_POST['complemento']) && !DataValidator::isEmpty($_POST['complemento']) ? $_POST['complemento'] : null);
		$endereco->setBairro(isset($_POST['bairro']) && !DataValidator::isEmpty($_POST['bairro']) ? $_POST['bairro'] : null);
		$endereco->setCidade(isset($_POST['cidade']) && !DataValidator::isEmpty($_POST['cidade']) ? $_POST['cidade'] : null);
		$endereco->setEstado(isset($_POST['estado']) && !DataValidator::isEmpty($_POST['estado']) ? $_POST['estado'] : null);
		$sacado->setEndereco($endereco);

		$usuario = new Usuario();
		$usuario->setId($_POST['usuario_id']);
		$sacado->setUsuario($usuario);

		$qtdeEmails = isset($_POST["qtd_emails"]) ? (int) $_POST["qtd_emails"] : 0;

		for ($e = 1; $e <= $qtdeEmails; $e++) {

			if (isset($_POST['email_' . $e]) && !DataValidator::isEmpty($_POST['email_' . $e])) {

				$email = new Email();
				$email->setId(isset($_POST["email_id_" . $e]) && !DataValidator::isEmpty($_POST["email_id_" . $e]) ? $_POST["email_id_" . $e] : 0);
				$email->setEmailEndereco(isset($_POST["email_" . $e]) && !DataValidator::isEmpty($_POST["email_" . $e]) ? $_POST["email_" . $e] : null);
				$email->setEnviar(isset($_POST["enviar_email_" . $e]) ? 'S' : 'N');

				$sacado->setEmail($email);
			}
		}

		if (!DataValidator::isEmpty($sacado->getId()))
			$retorno = SacadoModel::update($sacado);
		else
			$retorno = SacadoModel::insert($sacado);


		if (!DataValidator::isEmpty($_POST['sacado_id'])) {
			try {
				$sacado = SacadoModel::getBy($sacado->getId());
			} catch (UserException $e) {
				$retorno['msg'] = $e->getMessage();
			}
		}

		if (DataValidator::isEmpty($_POST['sacado_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$this->indexAction(array('sucesso' => 'Sacado cadastrado com sucesso.'));
		} elseif (!DataValidator::isEmpty($_POST['sacado_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$view = new View('views/gerenciar-sacado.php');
			$view->setParams(array('sucesso' => 'Sacado alterado com sucesso.', 'sacado' => $sacado));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-sacado.php');
			$view->setParams(array('mensagem' => $retorno['msg'], 'sacado' => $sacado));
			$view->showContents();
		}
	}

	public static function limparAction()
	{
		session_start();
		unset($_SESSION[self::PESQUISA_SACADO]);
		self::buscaAction();
	}

	public static function excluiAction()
	{
		$sacado = null;
		$msg = null;

		$sacado_id = isset($_REQUEST['sacado_id']) && !DataValidator::isEmpty($_REQUEST['sacado_id']) ? $_REQUEST['sacado_id'] : 0;
		$msg = SacadoModel::excluiSacado($sacado_id);

		if (DataValidator::isEmpty($msg)) {
			self::buscaAction(array('sucesso' => 'Sacado excluído com sucesso.'));
			header("Location: ?controle=Sacado&acao=index");
		} else {
			$sacado = SacadoModel::getBy($sacado_id);
			$view = new View('views/gerenciar-sacado.php');
			$view->setParams(array('mensagem' => $msg, 'sacado' => $sacado));
			$view->showContents();
		}
	}

	public static function getPesquisaAction()
	{

		//if( isset($_REQUEST['origem']) && !DataValidator::isEmpty( $_REQUEST['origem'] && $_REQUEST['origem'] != 'sacado'))
		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else {
			unset($_SESSION[self::PESQUISA_SACADO]);
		}

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_SACADO]) ? new PesquisaSacado() : $_SESSION[self::PESQUISA_SACADO];

		if (!isset($_SESSION[self::PESQUISA_SACADO]))
			$_SESSION[self::PESQUISA_SACADO] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_SACADO]->getPagina());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_SACADO]->getStatus());
		$pesquisa->setNome(isset($_REQUEST["busca_nome"]) && !DataValidator::isEmpty($_REQUEST['busca_nome']) ? trim($_REQUEST["busca_nome"]) : $_SESSION[self::PESQUISA_SACADO]->getNome());
		$pesquisa->setEmail(isset($_REQUEST["busca_email"]) && !DataValidator::isEmpty($_REQUEST['busca_email']) ? $_REQUEST["busca_email"] : $_SESSION[self::PESQUISA_SACADO]->getEmail());
		$pesquisa->setCpfCnpj(isset($_REQUEST["busca_cpf_cnpj"]) && !DataValidator::isEmpty($_REQUEST['busca_cpf_cnpj']) ? $_REQUEST["busca_cpf_cnpj"] : $_SESSION[self::PESQUISA_SACADO]->getCpfCnpj());

		$pesquisa->setEndereco(isset($_REQUEST["busca_endereco"]) && !DataValidator::isEmpty($_REQUEST['busca_endereco']) ? $_REQUEST["busca_endereco"] : $_SESSION[self::PESQUISA_SACADO]->getEndereco());
		$pesquisa->setCidade(isset($_REQUEST["busca_cidade"]) && !DataValidator::isEmpty($_REQUEST['busca_cidade']) ? $_REQUEST["busca_cidade"] : $_SESSION[self::PESQUISA_SACADO]->getCidade());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_SACADO]->getEstado());

		return $pesquisa;
	}

	
	public static function gerarTodosExcelAction()
	{

		$html = SacadoModel::getAllhtml();

		$arquivo = 'sacados-' .  date("Y-m-d") . '.xls';
		header("Expires: Mon, 23 Dec 2011 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-type: application/x-msexcel");
		header("Content-Disposition: attachment; filename={$arquivo}");
		header("Content-Description: PHP Generated Data");
		echo $html;
	}

	public static function gerarExcelAction()
	{

		$sacado = null;
		$sacado_id = isset($_POST['sacado_id']) && !DataValidator::isEmpty($_POST['sacado_id']) ? $_POST['sacado_id'] : 0;

		$sacado = SacadoModel::getBy($sacado_id);

		$html = "";
		$html .= "<table border=1>";
		$html .= "<tr align='center'>";
		$html .= "<th height='30'><b>C&Ocirc;DIGO</b></th>";
		$html .= "<th height='30'><b>NOME</b></th>";
		$html .= "<th height='30'><b>CPF/CNPJ</b></th>";

		$html .= "<th height='30'><b>CEP</b></th>";
		$html .= "<th height='30'><b>ENDERE&Ccedil;O</b></th>";
		$html .= "<th height='30'><b>N&Uacute;MERO</b></th>";
		$html .= "<th height='30'><b>COMPLEMENTO</b></th>";
		$html .= "<th height='30'><b>BAIRRO</b></th>";
		$html .= "<th height='30'><b>CIDADE</b></th>";
		$html .= "<th height='30'><b>ESTADO</b></th>";

		$emails = $sacado->getEmails();
		if (!DataValidator::isEmpty($sacado) && !DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {
				$html .= "<th height='30'><b>EMAIL</b></th>";
			}
		}

		$html .= "</tr>";
		$html .= "</table>";


		if (!DataValidator::isEmpty($sacado)) {
			$html .= "<table border=1>";
			$html .= "<tr>";
			$html .= "<td>" . $sacado->getId() . "</td>";
			$html .= "<td>" . $sacado->getNome() . "</td>";
			$html .= "<td>" . $sacado->getCpfCnpj() . "</td>";

			$html .= "<td>" . $sacado->getEndereco()->getCep() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getLogradouro() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getNumero() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getComplemento() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getBairro() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getCidade() . "</td>";
			$html .= "<td>" . $sacado->getEndereco()->getEstado() . "</td>";

			if (!DataValidator::isEmpty($emails)) {
				foreach ($emails as $email) {
					$html .= "<td>" . $email->getEmailEndereco() .  "</td>";
				}
			}

			$html .= "</tr>";
			$html .= "</table>";
		}

		$arquivo = 'sacado' . (!DataValidator::isEmpty($sacado) ? '-' . $sacado->getNome() : '') . '-' .  date("Y-m-d") . '.xls';
		//Determinar até quando este arquivo ficará em cache;
		header("Expires: Mon, 23 Dec 2011 05:00:00 GMT");
		//Indicar a data de última modificação;
		header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
		//Indicar que o arquivo não deverá ficar no cache, forçando o seu reprocessamento.
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		//Mudar o tipo do arquivo;
		header("Content-type: application/x-msexcel");
		//Forçar o download, informando o nome do arquivo.
		header("Content-Disposition: attachment; filename={$arquivo}");
		header("Content-Description: PHP Generated Data");

		echo $html;
	}
}
