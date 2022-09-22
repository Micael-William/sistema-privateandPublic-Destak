<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/JornalModel.php");
require_once("models/PropostaModel.php");
require_once("models/SecretariaModel.php");
require_once("classes/Paginacao.class.php");
require_once("classes/Pesquisa.class.php");
require_once("phpmailer/class.phpmailer.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class PropostaController
{

	const PROP_KEY = "prop_key";
	const QTD_PAGINACAO = 100;
	const PESQUISA_PROPOSTA = "Resultado-proposta";

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
			$retorno = PropostaModel::lista(null, null, 0, null, null, null, 0, null, $p, self::QTD_PAGINACAO, $o, $s);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setOrdenacao($o);
		$paginacao->setSentidoOrdenacao($s);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Proposta');

		$view = new View('views/propostas.php');
		$view->setParams(array('propostas' => $retorno['propostas'], 'paginacao' => $paginacao));
		$view->showContents();
	}

	public static function buscaAction($msg = array())
	{

		session_start();

		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		try {
			$retorno = PropostaModel::lista(
				$pesquisa->getStatus(),
				$pesquisa->getNomeAdvogado(),
				$pesquisa->getSecretariaId(),
				$pesquisa->getNumeroProcesso(),
				$pesquisa->getNomeRequerente(),
				$pesquisa->getNomeRequerido(),
				$pesquisa->getCodigoInterno(),
				$pesquisa->getEstado(),
				$pesquisa->getPagina(),
				self::QTD_PAGINACAO,
				$pesquisa->getOrdenacao(),
				$pesquisa->getSentidoOrdenacao(),
				$pesquisa->getPendente()
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
		$paginacao->setPaginaDestino('Proposta');

		$view = new View('views/propostas.php');
		$view->setParams(array('propostas' => $retorno['propostas'], 'mensagens' => $msg, 'paginacao' => $paginacao, 'pesquisa' => $pesquisa));
		$view->showContents();
	}

	public static function detalheAction()
	{
		$proposta = null;
		$msg = null;

		try {
			if (isset($_REQUEST['proposta_id']) && !DataValidator::isEmpty($_REQUEST['proposta_id']))
				$proposta = PropostaModel::getById($_REQUEST['proposta_id'], null, 'emails_envio');
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-proposta.php');
		$view->setParams(array('mensagem' => $msg, 'proposta' => $proposta));
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;

		$proposta = new Proposta();
		$proposta->setId(isset($_POST['proposta_id']) && !DataValidator::isEmpty($_POST['proposta_id']) ? $_POST['proposta_id'] : 0);
		$proposta->setStatus(isset($_POST['status-aux']) && !DataValidator::isEmpty($_POST['status-aux']) ? $_POST['status-aux'] : null);

		$processo = new Processo();
		$processo->setId(isset($_POST['processo_id']) && !DataValidator::isEmpty($_POST['processo_id']) ? $_POST['processo_id'] : 0);
		$processo->setRequerente(isset($_POST['requerente']) && !DataValidator::isEmpty($_POST['requerente']) ? $_POST['requerente'] : null);
		$processo->setRequerido(isset($_POST['requerido']) && !DataValidator::isEmpty($_POST['requerido']) ? $_POST['requerido'] : null);
		$processo->setAcao(isset($_POST['processo_acao']) && !DataValidator::isEmpty($_POST['processo_acao']) ? $_POST['processo_acao'] : null);
		$processo->setSinalizador('V');

		$advogado = new Advogado();
		$advogado->setId(isset($_POST['advogado_id']) && !DataValidator::isEmpty($_POST['advogado_id']) ? $_POST['advogado_id'] : $_POST['adv_id_aux']);
		$processo->setAdvogado($advogado);

		$secr = isset($_POST['secr_id']) && !DataValidator::isEmpty($_POST['secr_id']) ? $_POST['secr_id'] : $_POST['sec-id-aux'];

		if (isset($secr) && !DataValidator::isEmpty($secr)) {
			$secretaria = new Secretaria();
			$secretaria->setId($secr);
			$processo->setSecretaria($secretaria);
		}

		if (isset($_POST['jornal_id']) && !DataValidator::isEmpty($_POST['jornal_id'])) {
			$jornal = new Jornal();
			$jornal->setId($_POST['jornal_id']);
			$processo->setJornal($jornal);
		} elseif (isset($_POST['jornal-id-aux']) && !DataValidator::isEmpty($_POST['jornal-id-aux'])) {
			$jornal = new Jornal();
			$jornal->setId($_POST['jornal-id-aux']);
			$processo->setJornal($jornal);
		}

		if (isset($_POST['observacao'])) {
			for ($b = 0; $b < sizeof($_POST['observacao']); $b++) {
				$obs = new Observacao();
				$obs->setId($_POST['obs_id'][$b]);
				$obs->setMensagem($_POST['observacao'][$b]);
				$obs->setUsuarioCadastroId($_POST['usuario_id']);

				$proposta->setObservacao($obs);
			}
		}

		//custo padrao
		//if( isset($_POST['valor_final_padrao']) && !DataValidator::isEmpty($_POST['valor_final_padrao']) ){
		$custo_padrao = new CustoProposta();
		$custo_padrao->setId(isset($_POST['custo_padrao_id']) && !DataValidator::isEmpty($_POST['custo_padrao_id']) ? $_POST['custo_padrao_id'] : 0);
		$custo_padrao->setPropostaId(isset($_POST['proposta_id']) && !DataValidator::isEmpty($_POST['proposta_id']) ? $_POST['proposta_id'] : 0);
		$custo_padrao->setQuantidade(isset($_POST['quantidade_padrao']) && !DataValidator::isEmpty($_POST['quantidade_padrao']) ? $_POST['quantidade_padrao'] : 0);
		$custo_padrao->setValorPadrao(isset($_POST['valor_padrao']) && !DataValidator::isEmpty($_POST['valor_padrao']) ? $_POST['valor_padrao'] : 0);
		$custo_padrao->setValorFinal(isset($_POST['valor_final_padrao']) && !DataValidator::isEmpty($_POST['valor_final_padrao']) ? $_POST['valor_final_padrao'] : 0);
		$custo_padrao->setAceite(isset($_POST['aceite_padrao']) && !DataValidator::isEmpty($_POST['aceite_padrao']) ? $_POST['aceite_padrao'] : null);
		$custo_padrao->setStatus('P');
		$proposta->setCusto($custo_padrao);
		//}

		$tem_custo_dje = 'nao';
		//custo dje
		//if( isset($_POST['valor_final_dje']) && !DataValidator::isEmpty($_POST['valor_final_dje']) ){
		$custo_dje = new CustoProposta();
		$custo_dje->setId(isset($_POST['custo_dje_id']) && !DataValidator::isEmpty($_POST['custo_dje_id']) ? $_POST['custo_dje_id'] : 0);
		$custo_dje->setPropostaId(isset($_POST['proposta_id']) && !DataValidator::isEmpty($_POST['proposta_id']) ? $_POST['proposta_id'] : 0);
		$custo_dje->setQuantidade(isset($_POST['quantidade_dje']) && !DataValidator::isEmpty($_POST['quantidade_dje']) ? $_POST['quantidade_dje'] : 0);
		$custo_dje->setValorPadrao(isset($_POST['valor_padrao_dje']) && !DataValidator::isEmpty($_POST['valor_padrao_dje']) ? $_POST['valor_padrao_dje'] : 0);
		$custo_dje->setValorDje(isset($_POST['valor_dje']) && !DataValidator::isEmpty($_POST['valor_dje']) ? $_POST['valor_dje'] : 0);
		$custo_dje->setValorFinal(isset($_POST['valor_final_dje']) && !DataValidator::isEmpty($_POST['valor_final_dje']) ? $_POST['valor_final_dje'] : 0);
		$custo_dje->setAceite(isset($_POST['aceite_dje']) && !DataValidator::isEmpty($_POST['aceite_dje']) ? $_POST['aceite_dje'] : null);
		$custo_dje->setStatus('D');
		$proposta->setCusto($custo_dje);

		if ((isset($_POST['aceite_padrao']) && !DataValidator::isEmpty($_POST['aceite_padrao']))
			|| (isset($_POST['aceite_dje']) && !DataValidator::isEmpty($_POST['aceite_dje']))
		) {
			$proposta->setUsuarioAceiteId($_POST['usuario_id']);
			$proposta->setDataAceite(date("Y-m-d H:i:s"));
		}

		if (isset($_POST['valor_final_dje']) && !DataValidator::isEmpty($_POST['valor_final_dje']))
			$tem_custo_dje = 'sim';

		//}

		//nao permite que aceite a proposta sem valor padrão preenchido
		/*if( ( (isset($_POST['aceite_padrao']) && !DataValidator::isEmpty($_POST['aceite_padrao'])) && isset($_POST['valor_padrao']) && !DataValidator::isEmpty($_POST['valor_padrao']) ) || 																																																
			(isset($_POST['aceite_dje']) && !DataValidator::isEmpty($_POST['aceite_dje']) && isset($_POST['valor_padrao_dje']) && !DataValidator::isEmpty($_POST['valor_padrao_dje'])) 
			)
				echo 'vazio';*/


		$entrada = new ProcessoEntrada();
		$entrada->setEstado(isset($_POST['estado_processo']) && !DataValidator::isEmpty($_POST['estado_processo']) ? $_POST['estado_processo'] : null);
		$entrada->setDataProcesso(isset($_POST['data_processo']) && !DataValidator::isEmpty($_POST['data_processo']) ? $_POST['data_processo'] : null);
		$entrada->setConteudo(isset($_POST['conteudo']) && !DataValidator::isEmpty($_POST['conteudo']) ? $_POST['conteudo'] : null);
		$entrada->setTribunal(null);

		$entrada->setNumero(isset($_POST['num_processo']) && !DataValidator::isEmpty($_POST['num_processo']) ? $_POST['num_processo'] : 0);
		$processo->setEntrada($entrada);

		$proposta->setProcesso($processo);

		session_start();
		if (isset($_POST['key']) && isset($_SESSION[self::PROP_KEY]) && $_POST['key'] == $_SESSION[self::PROP_KEY]) {
			if (!DataValidator::isEmpty($proposta->getId()))
				$retorno = PropostaModel::update($proposta);
			else
				$retorno = PropostaModel::insert($proposta);
		}

		if (!DataValidator::isEmpty($_POST['proposta_id'])) {
			try {
				$proposta = PropostaModel::getById($proposta->getId(), null, 'emails_envio');
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}
		}

		if (DataValidator::isEmpty($_POST['proposta_id']) && ($retorno == null || DataValidator::isEmpty($retorno['msg']))) {
			header("Location: ?controle=Proposta&acao=index");
		} elseif (!DataValidator::isEmpty($proposta->getId()) && ($retorno == null || DataValidator::isEmpty($retorno['msg']))) {

			if ($retorno != null && !DataValidator::isEmpty($retorno['acompanhamento_id'])) {
				//se foi para acompanhamento, redireciona para a pagina de acompanhamento
				$acompanhamento = AcompanhamentoModel::getById($retorno['acompanhamento_id']);
				$view = new View('views/gerenciar-acompanhamento.php');
				$view->setParams(array('sucesso' => 'Esta Proposta foi encaminhada a área de Acompanhamento Processual.', 'acompanhamento' => $acompanhamento, 'impressao' => 'sim', 'flag' => 'sim'));
				$view->showContents();
			} else {
				$view = new View('views/gerenciar-proposta.php');
				$view->setParams(array('sucesso' => 'Proposta alterada com sucesso.', 'proposta' => $proposta));
				$view->showContents();
			}
		} else {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('mensagem' => @$retorno['msg'], 'proposta' => $proposta, 'tem_custo_dje' => $tem_custo_dje));
			$view->showContents();
		}
	}

	public static function enviarAction()
	{
		$proposta = null;
		$msg = null;

		try {
			if (
				isset($_REQUEST['proposta_id']) && !DataValidator::isEmpty($_REQUEST['proposta_id']) &&
				isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id'])
			) {

				//envia proposta apenas para os emails autorizados do advogado
				$proposta = PropostaModel::getById($_REQUEST['proposta_id'], null, 'envio_proposta');
				if (!DataValidator::isEmpty($proposta))
					PropostaModel::enviaEmail($proposta, $_REQUEST['usuario_id']);

				$proposta = PropostaModel::getById($_REQUEST['proposta_id']);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('sucesso' => 'Proposta enviada com sucesso.', 'proposta' => $proposta));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('mensagem' => $msg, 'proposta' => $proposta));
			$view->showContents();
		}
	}

	public static function rejeitarAction()
	{
		$proposta = null;
		$msg = null;

		try {
			if (
				isset($_REQUEST['proposta_id']) && !DataValidator::isEmpty($_REQUEST['proposta_id']) &&
				isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id'])
			) {

				$proposta = PropostaModel::getById($_REQUEST['proposta_id']);
				if (!DataValidator::isEmpty($proposta))
					PropostaModel::rejeitar($proposta, $_REQUEST['usuario_id']);

				$proposta = PropostaModel::getById($_REQUEST['proposta_id']);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('sucesso' => 'Proposta rejeitada com sucesso.', 'proposta' => $proposta));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('mensagem' => $msg, 'proposta' => $proposta));
			$view->showContents();
		}
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA_PROPOSTA]);

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_PROPOSTA]) ? new Pesquisa() : $_SESSION[self::PESQUISA_PROPOSTA];

		if (!isset($_SESSION[self::PESQUISA_PROPOSTA]))
			$_SESSION[self::PESQUISA_PROPOSTA] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_PROPOSTA]->getPagina());
		$pesquisa->setOrdenacao(isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : $_SESSION[self::PESQUISA_PROPOSTA]->getOrdenacao());
		$pesquisa->setSentidoOrdenacao(isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : $_SESSION[self::PESQUISA_PROPOSTA]->getSentidoOrdenacao());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_PROPOSTA]->getStatus());
		$pesquisa->setPendente(isset($_REQUEST["busca_pendente"]) && !DataValidator::isEmpty($_REQUEST['busca_pendente']) ? $_REQUEST["busca_pendente"] : $_SESSION[self::PESQUISA_PROPOSTA]->getPendente());
		$pesquisa->setNomeAdvogado(isset($_REQUEST["busca_advogado"]) && !DataValidator::isEmpty($_REQUEST['busca_advogado']) ? $_REQUEST["busca_advogado"] : $_SESSION[self::PESQUISA_PROPOSTA]->getNomeAdvogado());
		$pesquisa->setSecretariaId(isset($_REQUEST["busca_secretaria"]) && !DataValidator::isEmpty($_REQUEST['busca_secretaria']) ? $_REQUEST["busca_secretaria"] : $_SESSION[self::PESQUISA_PROPOSTA]->getSecretariaId());
		$pesquisa->setNumeroProcesso(isset($_REQUEST["busca_num_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_num_processo']) ? $_REQUEST["busca_num_processo"] : $_SESSION[self::PESQUISA_PROPOSTA]->getNumeroProcesso());
		$pesquisa->setNomeRequerente(isset($_REQUEST["busca_requerente"]) && !DataValidator::isEmpty($_REQUEST['busca_requerente']) ? $_REQUEST["busca_requerente"] : $_SESSION[self::PESQUISA_PROPOSTA]->getNomeRequerente());
		$pesquisa->setNomeRequerido(isset($_REQUEST["busca_requerido"]) && !DataValidator::isEmpty($_REQUEST['busca_requerido']) ? $_REQUEST["busca_requerido"] : $_SESSION[self::PESQUISA_PROPOSTA]->getNomeRequerido());
		$pesquisa->setCodigoInterno(isset($_REQUEST["busca_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_processo']) ? $_REQUEST["busca_processo"] : $_SESSION[self::PESQUISA_PROPOSTA]->getCodigoInterno());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_PROPOSTA]->getEstado());

		return $pesquisa;
	}

	//exclui uma Proposta
	public static function excluiAction()
	{
		$proposta = null;
		$msg = null;

		$proposta_id = isset($_REQUEST['proposta_id']) && !DataValidator::isEmpty($_REQUEST['proposta_id']) ? $_REQUEST['proposta_id'] : 0;
		$msg = PropostaModel::exclui($proposta_id);

		if (DataValidator::isEmpty($msg)) {
			self::buscaAction(array('sucesso' => 'Proposta(s) excluída(s) com sucesso.'));
		} else {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('mensagem' => $msg, 'proposta' => $proposta));
			$view->showContents();
		}
	}

	//exclui varias propostas
	public static function excluiPropostasAction()
	{
		$proposta = null;
		$msg = null;

		$propostas = isset($_REQUEST['exclui_proposta_id']) && !DataValidator::isEmpty($_REQUEST['exclui_proposta_id']) ? $_REQUEST['exclui_proposta_id'] : array();
		$msg = PropostaModel::excluiPropostas($propostas);

		if (DataValidator::isEmpty($msg)) {
			self::buscaAction(array('sucesso' => 'Proposta(s) excluída(s) com sucesso.'));
		} else {
			$view = new View('views/gerenciar-proposta.php');
			$view->setParams(array('mensagem' => $msg, 'proposta' => $proposta));
			$view->showContents();
		}
	}

	public function limpaBuscaAction()
	{
		session_start();
		unset($_SESSION[self::PESQUISA_PROPOSTA]);
		header("Location:?controle=Proposta&acao=index");
	}
}
