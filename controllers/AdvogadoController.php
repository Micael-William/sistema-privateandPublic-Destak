<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/AdvogadoModel.php");
require_once("classes/Paginacao.class.php");
require_once("classes/PesquisaAdvogado.class.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class AdvogadoController
{

	const QTD_PAGINACAO = 100;
	const PESQUISA_ADVOGADO = "Resultado-advogado";

	public function indexAction($msg = array())
	{
		$params = array();
		$retorno = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;

		$retorno = AdvogadoModel::lista(null, null, null, null, null, null, null, null, null, null, null, $p, self::QTD_PAGINACAO);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Advogado');

		$params['advogados'] = $retorno['advogados'];
		$params['paginacao'] = $paginacao;
		if (isset($msg['sucesso'])) $params['mensagens'] = $msg;

		$view = new View('views/advogados.php');
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

		$retorno = AdvogadoModel::lista(
			$pesquisa->getNome(),
			$pesquisa->getStatus(),
			$pesquisa->getEmail(),
			$pesquisa->getOab(),
			$pesquisa->getEmpresa(),
			$pesquisa->getCidade(),
			$pesquisa->getEstado(),
			$pesquisa->getNomeContato(),
			$pesquisa->getEmailContato(),
			$pesquisa->getEndereco(),
			$pesquisa->getTelefone(),
			$pesquisa->getPagina(),
			self::QTD_PAGINACAO
		);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Advogado');

		$view = new View('views/advogados.php');
		$view->setParams(array('advogados' => $retorno['advogados'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa, 'mensagens' => $mensagens));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$advogado = null;
		$msg = null;

		try {
			if (isset($_REQUEST['advogado_id']) && !DataValidator::isEmpty($_REQUEST['advogado_id']))
				$advogado = AdvogadoModel::getBy($_REQUEST['advogado_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-advogado.php');
		$view->setParams(array('mensagem' => $msg, 'advogado' => $advogado));
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;

		$advogado = new Advogado();
		$advogado->setId(isset($_POST['advogado_id']) && !DataValidator::isEmpty($_POST['advogado_id']) ? $_POST['advogado_id'] : 0);
		$advogado->setNome(isset($_POST['nome']) && !DataValidator::isEmpty($_POST['nome']) ? $_POST['nome'] : null);
		$advogado->setOab(isset($_POST['oab']) && !DataValidator::isEmpty($_POST['oab']) ? $_POST['oab'] : null);
		$advogado->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);
		$advogado->setEmpresa(isset($_POST['empresa']) && !DataValidator::isEmpty($_POST['empresa']) ? $_POST['empresa'] : null);
		$advogado->setCnpj(isset($_POST['cnpj']) && !DataValidator::isEmpty($_POST['cnpj']) ? DataFilter::numeric($_POST['cnpj']) : null);
		$advogado->setSite(isset($_POST['site']) && !DataValidator::isEmpty($_POST['site']) ? $_POST['site'] : null);
		$advogado->setNomeContato(isset($_POST['nome_contato']) && !DataValidator::isEmpty($_POST['nome_contato']) ? $_POST['nome_contato'] : null);
		$advogado->setEmailContato(isset($_POST['email_contato']) && !DataValidator::isEmpty($_POST['email_contato']) ? $_POST['email_contato'] : null);

		$endereco = new Endereco();
		$endereco->setCep(isset($_POST['cep']) && !DataValidator::isEmpty($_POST['cep']) ? DataFilter::numeric($_POST['cep']) : null);
		$endereco->setLogradouro(isset($_POST['logradouro']) && !DataValidator::isEmpty($_POST['logradouro']) ? $_POST['logradouro'] : null);
		$endereco->setNumero(isset($_POST['numero']) && !DataValidator::isEmpty($_POST['numero']) ? $_POST['numero'] : null);
		$endereco->setComplemento(isset($_POST['complemento']) && !DataValidator::isEmpty($_POST['complemento']) ? $_POST['complemento'] : null);
		$endereco->setBairro(isset($_POST['bairro']) && !DataValidator::isEmpty($_POST['bairro']) ? $_POST['bairro'] : null);
		$endereco->setCidade(isset($_POST['cidade']) && !DataValidator::isEmpty($_POST['cidade']) ? $_POST['cidade'] : null);
		$endereco->setEstado(isset($_POST['estado']) && !DataValidator::isEmpty($_POST['estado']) ? $_POST['estado'] : null);
		$advogado->setEndereco($endereco);

		$usuario = new Usuario();
		$usuario->setId($_POST['usuario_id']);
		$advogado->setUsuario($usuario);

		if (isset($_POST['ddd']) && isset($_POST['numero_telefone'])) {
			for ($i = 0; $i < sizeof($_POST['numero_telefone']); $i++) {
				$tel = new Telefone();
				$tel->setId($_POST['tel_id'][$i]);
				$tel->setDdd($_POST['ddd'][$i]);
				$tel->setNumero($_POST['numero_telefone'][$i]);
				$advogado->setTelefone($tel);
			}
		}

		$qtdeEmails = isset($_POST["qtd_emails"]) ? (int) $_POST["qtd_emails"] : 0;

		for ($e = 1; $e <= $qtdeEmails; $e++) {

			if (isset($_POST['email_' . $e]) && !DataValidator::isEmpty($_POST['email_' . $e])) {
				$nome_email = $_POST["email_" . $e];

				$email = new Email();
				$email->setId(isset($_POST["email_id_" . $e]) && !DataValidator::isEmpty($_POST["email_id_" . $e]) ? $_POST["email_id_" . $e] : 0);
				$email->setEmailEndereco(isset($_POST["email_" . $e]) && !DataValidator::isEmpty($_POST["email_" . $e]) ? $_POST["email_" . $e] : null);
				$email->setEnviar(isset($_POST["enviar_email_" . $e]) ? 'S' : 'N');
				$advogado->setEmail($email);
			}
		}

		if (isset($_POST['observacao'])) {
			for ($b = 0; $b < sizeof($_POST['observacao']); $b++) {
				$obs = new Observacao();
				$obs->setId($_POST['obs_id'][$b]);
				$obs->setMensagem($_POST['observacao'][$b]);
				$obs->setUsuarioCadastroId($_POST['usuario_id']);

				$advogado->setObservacao($obs);
			}
		}

		if (!DataValidator::isEmpty($advogado->getId()))
			$retorno = AdvogadoModel::update($advogado);
		else
			$retorno = AdvogadoModel::insert($advogado);


		if (!DataValidator::isEmpty($_POST['advogado_id'])) {
			try {
				$advogado = AdvogadoModel::getBy($advogado->getId());
			} catch (UserException $e) {
				$retorno['msg'] = $e->getMessage();
			}
		}


		if (DataValidator::isEmpty($_POST['advogado_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$this->indexAction(array('sucesso' => 'Advogado cadastrado com sucesso.'));
		} elseif (!DataValidator::isEmpty($_POST['advogado_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$view = new View('views/gerenciar-advogado.php');
			$view->setParams(array('sucesso' => 'Advogado alterado com sucesso.', 'advogado' => $advogado));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-advogado.php');
			$view->setParams(array('mensagem' => $retorno['msg'], 'advogado' => $advogado));
			$view->showContents();
		}
	}

	public static function limparAction()
	{

		session_start();
		unset($_SESSION[self::PESQUISA_ADVOGADO]);
		self::buscaAction();
	}

	public static function excluiAction()
	{
		$advogado = null;
		$msg = null;

		$advogado_id = isset($_REQUEST['advogado_id']) && !DataValidator::isEmpty($_REQUEST['advogado_id']) ? $_REQUEST['advogado_id'] : 0;
		$msg = AdvogadoModel::excluiAdvogado($advogado_id);

		if (DataValidator::isEmpty($msg)) {
			self::buscaAction(array('sucesso' => 'Advogado excluído com sucesso.'));
			//header("Location: ?controle=Advogado&acao=index");		
		} else {
			$advogado = AdvogadoModel::getBy($advogado_id);
			$view = new View('views/gerenciar-advogado.php');
			$view->setParams(array('mensagem' => $msg, 'advogado' => $advogado));
			$view->showContents();
		}
	}

	public static function gerarTodosExcelAction()
	{

		$html = AdvogadoModel::getAllhtml();

		$arquivo = 'advogados-' .  date("Y-m-d") . '.xls';
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

		$advogado = null;
		$advogado_id = isset($_POST['advogado_id']) && !DataValidator::isEmpty($_POST['advogado_id']) ? $_POST['advogado_id'] : 0;

		$advogado = AdvogadoModel::getBy($advogado_id);

		$html = "";
		$html .= "<table border=1>";
		$html .= "<tr align='center'>";
		$html .= "<th height='30'><b>C&Ocirc;DIGO</b></th>";
		$html .= "<th height='30'><b>NOME</b></th>";
		$html .= "<th height='30'><b>OAB</b></th>";
		$html .= "<th height='30'><b>DATA CADASTRO</b></th>";
		$html .= "<th height='30'><b>STATUS</b></th>";
		$html .= "<th height='30'><b>EMPRESA</b></th>";
		$html .= "<th height='30'><b>CNPJ</b></th>";
		$html .= "<th height='30'><b>SITE</b></th>";
		$html .= "<th height='30'><b>NOME CONTATO</b></th>";
		$html .= "<th height='30'><b>EMAIL CONTATO</b></th>";

		$html .= "<th height='30'><b>CEP</b></th>";
		$html .= "<th height='30'><b>ENDERE&Ccedil;O</b></th>";
		$html .= "<th height='30'><b>N&Uacute;MERO</b></th>";
		$html .= "<th height='30'><b>COMPLEMENTO</b></th>";
		$html .= "<th height='30'><b>BAIRRO</b></th>";
		$html .= "<th height='30'><b>CIDADE</b></th>";
		$html .= "<th height='30'><b>ESTADO</b></th>";

		$telefones = $advogado->getTelefones();
		if (!DataValidator::isEmpty($advogado) && !DataValidator::isEmpty($telefones)) {
			foreach ($telefones as $tel) {
				$html .= "<th height='30'><b>TELEFONE</b></th>";
			}
		}

		$emails = $advogado->getEmails();
		if (!DataValidator::isEmpty($advogado) && !DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {
				$html .= "<th height='30'><b>EMAIL</b></th>";
			}
		}

		$html .= "</tr>";
		$html .= "</table>";


		if (!DataValidator::isEmpty($advogado)) {
			$html .= "<table border=1>";
			$html .= "<tr>";
			$html .= "<td>" . $advogado->getId() . "</td>";
			$html .= "<td>" . $advogado->getNome() . "</td>";
			$html .= "<td>" . $advogado->getOab() . "</td>";
			$html .= "<td>" . date('d/m/Y', strtotime($advogado->getDataEntrada())) . "</td>";
			$html .= "<td>" . $advogado->getStatusDesc() . "</td>";
			$html .= "<td>" . $advogado->getEmpresa() . "</td>";
			$html .= "<td>" . (!DataValidator::isEmpty($advogado->getCnpj()) ? DataFilter::mask($advogado->getCnpj(), '##.###.###/####-##') : '') . "</td>";
			$html .= "<td>" . $advogado->getSite() . "</td>";
			$html .= "<td>" . $advogado->getNomeContato() . "</td>";
			$html .= "<td>" . $advogado->getEmailContato() . "</td>";

			$html .= "<td>" . $advogado->getEndereco()->getCep() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getLogradouro() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getNumero() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getComplemento() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getBairro() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getCidade() . "</td>";
			$html .= "<td>" . $advogado->getEndereco()->getEstado() . "</td>";

			if (!DataValidator::isEmpty($telefones)) {
				foreach ($telefones as $tel) {
					$html .= "<td>" . $tel->getDdd() . " " . $tel->getNumero() .  "</td>";
				}
			}

			if (!DataValidator::isEmpty($emails)) {
				foreach ($emails as $email) {
					$html .= "<td>" . $email->getEmailEndereco() .  "</td>";
				}
			}

			$html .= "</tr>";
			$html .= "</table>";
		}

		$arquivo = 'advogado' . (!DataValidator::isEmpty($advogado) ? '-' . $advogado->getNome() : '') . '-' .  date("Y-m-d") . '.xls';
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

	public static function getPesquisaAction()
	{

		//if( isset($_REQUEST['origem']) && !DataValidator::isEmpty( $_REQUEST['origem'] && $_REQUEST['origem'] != 'advogado'))
		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else {
			unset($_SESSION[self::PESQUISA_ADVOGADO]);
		}

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_ADVOGADO]) ? new PesquisaAdvogado() : $_SESSION[self::PESQUISA_ADVOGADO];

		if (!isset($_SESSION[self::PESQUISA_ADVOGADO]))
			$_SESSION[self::PESQUISA_ADVOGADO] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_ADVOGADO]->getPagina());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_ADVOGADO]->getStatus());
		$pesquisa->setNome(isset($_REQUEST["busca_nome"]) && !DataValidator::isEmpty($_REQUEST['busca_nome']) ? trim($_REQUEST["busca_nome"]) : $_SESSION[self::PESQUISA_ADVOGADO]->getNome());
		$pesquisa->setEmail(isset($_REQUEST["busca_email"]) && !DataValidator::isEmpty($_REQUEST['busca_email']) ? $_REQUEST["busca_email"] : $_SESSION[self::PESQUISA_ADVOGADO]->getEmail());
		$pesquisa->setTelefone(isset($_REQUEST["busca_telefone"]) && !DataValidator::isEmpty($_REQUEST['busca_telefone']) ? $_REQUEST["busca_telefone"] : $_SESSION[self::PESQUISA_ADVOGADO]->getTelefone());
		$pesquisa->setOab(isset($_REQUEST["busca_oab"]) && !DataValidator::isEmpty($_REQUEST['busca_oab']) ? $_REQUEST["busca_oab"] : $_SESSION[self::PESQUISA_ADVOGADO]->getOab());
		$pesquisa->setEmpresa(isset($_REQUEST["busca_empresa"]) && !DataValidator::isEmpty($_REQUEST['busca_empresa']) ? $_REQUEST["busca_empresa"] : $_SESSION[self::PESQUISA_ADVOGADO]->getEmpresa());
		$pesquisa->setNomeContato(isset($_REQUEST["busca_nome_contato"]) && !DataValidator::isEmpty($_REQUEST['busca_nome_contato']) ? $_REQUEST["busca_nome_contato"] : $_SESSION[self::PESQUISA_ADVOGADO]->getNomeContato());
		$pesquisa->setEmailContato(isset($_REQUEST["busca_email_contato"]) && !DataValidator::isEmpty($_REQUEST['busca_email_contato']) ? $_REQUEST["busca_email_contato"] : $_SESSION[self::PESQUISA_ADVOGADO]->getEmailContato());

		$pesquisa->setEndereco(isset($_REQUEST["busca_endereco"]) && !DataValidator::isEmpty($_REQUEST['busca_endereco']) ? $_REQUEST["busca_endereco"] : $_SESSION[self::PESQUISA_ADVOGADO]->getEndereco());
		$pesquisa->setCidade(isset($_REQUEST["busca_cidade"]) && !DataValidator::isEmpty($_REQUEST['busca_cidade']) ? $_REQUEST["busca_cidade"] : $_SESSION[self::PESQUISA_ADVOGADO]->getCidade());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_ADVOGADO]->getEstado());

		return $pesquisa;
	}
}
