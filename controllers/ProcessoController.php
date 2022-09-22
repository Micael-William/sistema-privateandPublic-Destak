<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("classes/ProcessoEntrada.class.php");
require_once("classes/Processo.class.php");
require_once("classes/Pesquisa.class.php");
require_once("classes/Paginacao.class.php");
require_once("models/ProcessoEntradaModel.php");
require_once("models/SecretariaModel.php");
require_once("models/JornalModel.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class ProcessoController
{

	const QTD_PAGINACAO = 100;
	const PESQUISA = "Resultado-processo";

	//Entrada
	public function cadastroAction()
	{
		$view = new View('views/cadastrar-processo.php');
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;

		$entrada = new ProcessoEntrada();
		$entrada->setArquivo(isset($_FILES['arquivo_processo']) && !DataValidator::isEmpty($_FILES['arquivo_processo']) ? $_FILES['arquivo_processo'] : null);
		$entrada->setEstado(isset($_POST['estado']) && !DataValidator::isEmpty($_POST['estado']) ? $_POST['estado'] : null);

		$retorno = ProcessoEntradaModel::insert($entrada);

		if (DataValidator::isEmpty($retorno['msg']) && DataValidator::isEmpty($retorno['repetidos'])) {
			$view = new View('views/cadastrar-processo.php');
			$view->setParams(array('sucesso' => 'Todos os Processo(s) cadastrado(s) com sucesso'));
			$view->showContents();
		} else {
			$view = new View('views/cadastrar-processo.php');
			$view->setParams(array('mensagem' => $retorno['msg'], 'processos_repetidos' => $retorno['repetidos']));
			$view->showContents();
		}
	}

	//*****************//

	//Processo
	public function indexAction()
	{
		$retorno = null;
		$msg = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;
		$o = isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : 0;
		$s = isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : 'd';

		try {
			$retorno = ProcessoModel::lista('A', 0, null, null, null, null, null, 0, null, $p, self::QTD_PAGINACAO, $o, $s);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setOrdenacao($o);
		$paginacao->setSentidoOrdenacao($s);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Processo');

		$view = new View('views/processos.php');
		$view->setParams(array('mensagem' => $msg, 'processos' => $retorno['processos'], 'paginacao' => $paginacao, 'total_processos' => $retorno['totalLinhas']));
		$view->showContents();
	}

	public static function buscaAction($msg = array())
	{

		session_start();

		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		try {
			$retorno = ProcessoModel::lista(
				$pesquisa->getSinalizador(),
				$pesquisa->getSecretariaId(),
				$pesquisa->getNumeroProcesso(),
				$pesquisa->getNomeAdvogado(),
				$pesquisa->getNomeRequerente(),
				$pesquisa->getNomeRequerido(),
				$pesquisa->getCodigoInterno(),
				$pesquisa->getDataProcesso(),
				$pesquisa->getEstado(),
				$pesquisa->getPagina(),
				self::QTD_PAGINACAO,
				$pesquisa->getOrdenacao(),
				$pesquisa->getSentidoOrdenacao()
			);
		} catch (UserException $e) {
			$msg['mensagem'] = $e->getMessage();
		}

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setOrdenacao($pesquisa->getOrdenacao());
		$paginacao->setSentidoOrdenacao($pesquisa->getSentidoOrdenacao());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Processo');

		$view = new View('views/processos.php');
		$view->setParams(array('mensagens' => $msg, 'processos' => $retorno['processos'], 'paginacao' => $paginacao, 'total_processos' => $retorno['totalLinhas'], 'pesquisa' => $pesquisa));
		$view->showContents();
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA]);

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA]) ? new Pesquisa() : $_SESSION[self::PESQUISA];

		if (!isset($_SESSION[self::PESQUISA]))
			$_SESSION[self::PESQUISA] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA]->getPagina());
		$pesquisa->setOrdenacao(isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : $_SESSION[self::PESQUISA]->getOrdenacao());
		$pesquisa->setSentidoOrdenacao(isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : $_SESSION[self::PESQUISA]->getSentidoOrdenacao());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setSinalizador(isset($_REQUEST["busca_sinalizador"]) && !DataValidator::isEmpty($_REQUEST['busca_sinalizador']) ? $_REQUEST["busca_sinalizador"] : $_SESSION[self::PESQUISA]->getSinalizador());
		$pesquisa->setNomeAdvogado(isset($_REQUEST["busca_advogado"]) && !DataValidator::isEmpty($_REQUEST['busca_advogado']) ? $_REQUEST["busca_advogado"] : $_SESSION[self::PESQUISA]->getNomeAdvogado());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA]->getEstado());
		$pesquisa->setSecretariaId(isset($_REQUEST["busca_secretaria"]) && !DataValidator::isEmpty($_REQUEST['busca_secretaria']) ? $_REQUEST["busca_secretaria"] : $_SESSION[self::PESQUISA]->getSecretariaId());
		$pesquisa->setNumeroProcesso(isset($_REQUEST["busca_num_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_num_processo']) ? $_REQUEST["busca_num_processo"] : $_SESSION[self::PESQUISA]->getNumeroProcesso());
		$pesquisa->setNomeRequerente(isset($_REQUEST["busca_requerente"]) && !DataValidator::isEmpty($_REQUEST['busca_requerente']) ? $_REQUEST["busca_requerente"] : $_SESSION[self::PESQUISA]->getNomeRequerente());
		$pesquisa->setNomeRequerido(isset($_REQUEST["busca_requerido"]) && !DataValidator::isEmpty($_REQUEST['busca_requerido']) ? $_REQUEST["busca_requerido"] : $_SESSION[self::PESQUISA]->getNomeRequerido());
		$pesquisa->setCodigoInterno(isset($_REQUEST["busca_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_processo']) ? $_REQUEST["busca_processo"] : $_SESSION[self::PESQUISA]->getCodigoInterno());

		$pesquisa->setDataProcesso(isset($_REQUEST["busca_data_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_data_processo']) ? $_REQUEST["busca_data_processo"] : $_SESSION[self::PESQUISA]->getDataProcesso());

		return $pesquisa;
	}

	public static function detalheAction()
	{
		$processo = null;
		$msg = null;

		try {
			if (isset($_REQUEST['processo_id']) && !DataValidator::isEmpty($_REQUEST['processo_id']))
				$processo = ProcessoModel::getById($_REQUEST['processo_id']);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		$view = new View('views/gerenciar-processo.php');
		$view->setParams(array('mensagem' => $msg, 'processo' => $processo));
		$view->showContents();
	}

	public function atualizaAction()
	{

		$retorno = null;
		$sucesso = null;

		$processo = new Processo();
		$processo->setId(isset($_POST['processo_id']) && !DataValidator::isEmpty($_POST['processo_id']) ? $_POST['processo_id'] : 0);
		$processo->setRequerente(isset($_POST['requerente']) && !DataValidator::isEmpty($_POST['requerente']) ? $_POST['requerente'] : 0);
		$processo->setRequerido(isset($_POST['requerido']) && !DataValidator::isEmpty($_POST['requerido']) ? $_POST['requerido'] : null);
		$processo->setAcao(isset($_POST['processo_acao']) && !DataValidator::isEmpty($_POST['processo_acao']) ? $_POST['processo_acao'] : null);
		$processo->setSinalizador(isset($_POST['sinalizador_processo']) && !DataValidator::isEmpty($_POST['sinalizador_processo']) ? $_POST['sinalizador_processo'] : null);

		$entrada = new ProcessoEntrada();
		$entrada->setEstado(isset($_POST['estado_processo']) && !DataValidator::isEmpty($_POST['estado_processo']) ? $_POST['estado_processo'] : null);
		$entrada->setNumero(isset($_POST['numero_processo']) && !DataValidator::isEmpty($_POST['numero_processo']) ? $_POST['numero_processo'] : null);
		$processo->setEntrada($entrada);

		$advogado = new Advogado();
		$advogado->setId(isset($_POST['advogado_id']) && !DataValidator::isEmpty($_POST['advogado_id']) ? $_POST['advogado_id'] : $_POST['adv_id_aux']);
		$processo->setAdvogado($advogado);

		if (isset($_POST['secr_id']) && !DataValidator::isEmpty($_POST['secr_id'])) {
			$secretaria = new Secretaria();
			$secretaria->setId($_POST['secr_id']);
			$processo->setSecretaria($secretaria);
		}

		if (isset($_POST['jornal_id']) && !DataValidator::isEmpty($_POST['jornal_id'])) {
			$jornal = new Jornal();
			$jornal->setId($_POST['jornal_id']);
			$processo->setJornal($jornal);
		}

		if (isset($_POST['observacao'])) {
			for ($b = 0; $b < sizeof($_POST['observacao']); $b++) {
				$obs = new Observacao();
				$obs->setId($_POST['obs_id'][$b]);
				$obs->setMensagem($_POST['observacao'][$b]);
				$obs->setUsuarioCadastroId($_POST['usuario_id']);
				$processo->setObservacao($obs);
			}
		}

		$retorno = ProcessoModel::updateFromProcesso($processo);

		try {
			if (isset($_REQUEST['processo_id']) && !DataValidator::isEmpty($_REQUEST['processo_id']))
				$processo = ProcessoModel::getById($_REQUEST['processo_id']);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		if (DataValidator::isEmpty($retorno['mensagem'])) {
			//se foi pra proposta automaticamente, redireciona para proposta			
			if (!DataValidator::isEmpty($retorno['proposta_id'])) {
				$proposta = PropostaModel::getById($retorno['proposta_id']);
				$view = new View('views/gerenciar-proposta.php');
				$view->setParams(array('sucesso' => 'Este Processo foi encaminhado a área de Propostas.', 'proposta' => $proposta));
				$view->showContents();
			} else {
				$view = new View('views/gerenciar-processo.php');
				$view->setParams(array('sucesso' => 'Processo alterado com sucesso.', 'processo' => $processo));
				$view->showContents();
			}
		} else {
			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('mensagem' => $retorno['mensagem'], 'processo' => $processo));
			$view->showContents();
		}
	}

	//envio manual do processo para a proposta
	public static function enviaPropostaAction()
	{
		$processo = null;
		$proposta = null;
		$msg = null;

		try {
			if (isset($_REQUEST['processo_id']) && !DataValidator::isEmpty($_REQUEST['processo_id'])) {
				$processo = ProcessoModel::getById($_REQUEST['processo_id']);
				$proposta_id = PropostaModel::insertFromProcesso($processo, 'manual');
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			//redireciona para a página de proposta
			try {
				$proposta = PropostaModel::getById($proposta_id);
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}

			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('sucesso' => 'Este Processo foi encaminhado a área de Propostas.', 'proposta' => $proposta));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('mensagem' => $msg, 'processo' => $processo));
			$view->showContents();
		}
	}

	//altera sinalizador de Vermelho para Amarelo
	public static function alteraSinalizadorAction()
	{
		$processo = null;
		$msg = null;

		$processo_id = isset($_REQUEST['processo_id']) && !DataValidator::isEmpty($_REQUEST['processo_id']) ? $_REQUEST['processo_id'] : 0;
		$msg = ProcessoModel::alteraSinalizador($processo_id, 'A');

		if (DataValidator::isEmpty($msg)) {

			try {
				$processo = ProcessoModel::getById($processo_id);
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}

			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('sucesso' => 'Sinalizador alterado com sucesso.', 'processo' => $processo));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('mensagem' => $msg, 'processo' => $processo));
			$view->showContents();
		}
	}

	//exclui um Processo
	public static function excluiAction()
	{
		$processo = null;
		$msg = null;

		$entrada_id = isset($_REQUEST['entrada_id']) && !DataValidator::isEmpty($_REQUEST['entrada_id']) ? $_REQUEST['entrada_id'] : 0;
		$msg = ProcessoModel::exclui($entrada_id);

		if (DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Processo&acao=index");
		} else {
			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('mensagem' => $msg, 'processo' => $processo));
			$view->showContents();
		}
	}

	//exclui varios processos
	public static function excluiProcessosAction()
	{
		$processo = null;
		$msg = null;

		$entradas = isset($_REQUEST['exclui_entrada_id']) && !DataValidator::isEmpty($_REQUEST['exclui_entrada_id']) ? $_REQUEST['exclui_entrada_id'] : array();
		$msg = ProcessoModel::excluiProcessos($entradas);

		if (DataValidator::isEmpty($msg)) {
			//header("Location: ?controle=Processo&acao=index");
			$_REQUEST['origem'] = "exclusao";
			self::buscaAction(array('sucesso' => 'Processo(s) excluído(s) com sucesso.'));
			//self::buscaAction();
		} else {
			$view = new View('views/gerenciar-processo.php');
			$view->setParams(array('mensagem' => $msg, 'processo' => $processo));
			$view->showContents();
		}
	}

	public function limpaBuscaAction()
	{
		session_start();
		unset($_SESSION[self::PESQUISA]);
		header("Location:?controle=Processo&acao=index");
	}
}
