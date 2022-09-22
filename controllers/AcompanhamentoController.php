<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("classes/Paginacao.class.php");
require_once("models/AcompanhamentoModel.php");
require_once("models/AcompanhamentoStatusModel.php");
require_once("classes/Pesquisa.class.php");
require_once("phpmailer/class.phpmailer.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class AcompanhamentoController
{

	const ACOMP_KEY = "acomp_key";
	const QTD_PAGINACAO = 100;
	const PESQUISA_ACOMPANHA = "Resultado-acompanhamento";

	public function indexAction()
	{

		$retorno = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;
		$o = isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : 0;
		$s = isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : 'd';

		$retorno = AcompanhamentoModel::lista(null, null, null, 0, 0, null, null, 0, null, $p, self::QTD_PAGINACAO, $o, $s);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setOrdenacao($o);
		$paginacao->setSentidoOrdenacao($s);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Acompanhamento');

		$view = new View('views/acompanhar-propostas.php');
		$view->setParams(array('acompanhamentos' => $retorno['acompanhamentos'], 'paginacao' => $paginacao));
		$view->showContents();
	}

	public static function buscaAction()
	{

		session_start();

		$msg = null;
		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		$retorno = AcompanhamentoModel::lista(
			$pesquisa->getStatus(),
			$pesquisa->getSubStatus(),
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
			$pesquisa->getSentidoOrdenacao()
		);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setOrdenacao($pesquisa->getOrdenacao());
		$paginacao->setSentidoOrdenacao($pesquisa->getSentidoOrdenacao());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Acompanhamento');

		$view = new View('views/acompanhar-propostas.php');
		$view->setParams(array('acompanhamentos' => $retorno['acompanhamentos'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa));
		$view->showContents();
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA_ACOMPANHA]);

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_ACOMPANHA]) ? new Pesquisa() : $_SESSION[self::PESQUISA_ACOMPANHA];

		if (!isset($_SESSION[self::PESQUISA_ACOMPANHA]))
			$_SESSION[self::PESQUISA_ACOMPANHA] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getPagina());
		$pesquisa->setOrdenacao(isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getOrdenacao());
		$pesquisa->setSentidoOrdenacao(isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getSentidoOrdenacao());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getStatus());
		$pesquisa->setSubStatus(isset($_REQUEST["busca_substatus"]) && !DataValidator::isEmpty($_REQUEST['busca_substatus']) ? $_REQUEST["busca_substatus"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getSubStatus());
		$pesquisa->setNomeAdvogado(isset($_REQUEST["busca_advogado"]) && !DataValidator::isEmpty($_REQUEST['busca_advogado']) ? $_REQUEST["busca_advogado"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getNomeAdvogado());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getEstado());
		$pesquisa->setSecretariaId(isset($_REQUEST["busca_secretaria"]) && !DataValidator::isEmpty($_REQUEST['busca_secretaria']) ? $_REQUEST["busca_secretaria"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getSecretariaId());
		$pesquisa->setNumeroProcesso(isset($_REQUEST["busca_num_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_num_processo']) ? $_REQUEST["busca_num_processo"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getNumeroProcesso());
		$pesquisa->setNomeRequerente(isset($_REQUEST["busca_requerente"]) && !DataValidator::isEmpty($_REQUEST['busca_requerente']) ? $_REQUEST["busca_requerente"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getNomeRequerente());
		$pesquisa->setNomeRequerido(isset($_REQUEST["busca_requerido"]) && !DataValidator::isEmpty($_REQUEST['busca_requerido']) ? $_REQUEST["busca_requerido"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getNomeRequerido());
		$pesquisa->setCodigoInterno(isset($_REQUEST["busca_acompanhamento"]) && !DataValidator::isEmpty($_REQUEST['busca_acompanhamento']) ? $_REQUEST["busca_acompanhamento"] : $_SESSION[self::PESQUISA_ACOMPANHA]->getCodigoInterno());

		return $pesquisa;
	}

	public static function detalheAction()
	{
		$acomp = null;
		$msg = null;

		try {
			if (isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id']))
				$acomp = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id'], null, 'emails_envio');
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-acompanhamento.php');
		$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acomp, 'flag' => 'sim'));
		$view->showContents();
	}

	public function salvaSacadoAction()
	{
		$msg = "";

		$acompModel = new AcompanhamentoModel();
		$acompanhamento = $acompModel->getById(isset($_POST['acompanhamento_id']) && !DataValidator::isEmpty($_POST['acompanhamento_id']) ? $_POST['acompanhamento_id'] : 0);

		$acompanhamento->setSacado(self::setSacadoData());

		SacadoAcompanhamentoModel::insertOrUpdate($acompanhamento->getSacado(), $acompModel->getDB());

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('sucesso' => 'Acompanhamento alterado com sucesso.', 'acompanhamento' => $acompanhamento, 'flag' => 'sim'));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento, 'flag' => 'sim'));
			$view->showContents();
		}
	}

	public function salvaAction()
	{
		$msg = null;
		$sucesso = null;

		//Processo
		$processo = new Processo();
		$processo->setId(isset($_POST['processo_id']) && !DataValidator::isEmpty($_POST['processo_id']) ? $_POST['processo_id'] : 0);
		$processo->setRequerente(isset($_POST['requerente']) && !DataValidator::isEmpty($_POST['requerente']) ? $_POST['requerente'] : null);
		$processo->setRequerido(isset($_POST['requerido']) && !DataValidator::isEmpty($_POST['requerido']) ? $_POST['requerido'] : null);
		$processo->setAcao(isset($_POST['processo_acao']) && !DataValidator::isEmpty($_POST['processo_acao']) ? $_POST['processo_acao'] : null);

		//Advogado
		$advogado = new Advogado();
		$advogado->setId(isset($_POST['advogado_id']) && !DataValidator::isEmpty($_POST['advogado_id']) ? $_POST['advogado_id'] : $_POST['adv_id_aux']);
		$processo->setAdvogado($advogado);

		//Secretaria
		if (isset($_POST['secr_id']) && !DataValidator::isEmpty($_POST['secr_id'])) {
			$secretaria = new Secretaria();
			$secretaria->setId($_POST['secr_id']);
			$processo->setSecretaria($secretaria);
		}

		//Entrada
		$entrada = new ProcessoEntrada();
		$entrada->setId(isset($_POST['entrada_id']) && !DataValidator::isEmpty($_POST['entrada_id']) ? $_POST['entrada_id'] : 0);
		$entrada->setNumero(isset($_POST['num_processo']) && !DataValidator::isEmpty($_POST['num_processo']) ? $_POST['num_processo'] : 0);
		$processo->setEntrada($entrada);

		//Proposta
		$proposta = new Proposta();
		$proposta->setId(isset($_POST['proposta_id']) && !DataValidator::isEmpty($_POST['proposta_id']) ? $_POST['proposta_id'] : 0);
		$proposta->setProcesso($processo);

		//Acompanhamento
		$acompanhamento = new Acompanhamento();
		$acompanhamento->setId(isset($_POST['acompanhamento_id']) && !DataValidator::isEmpty($_POST['acompanhamento_id']) ? $_POST['acompanhamento_id'] : 0);
		$acompanhamento->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);
		$acompanhamento->setSubStatus(isset($_POST['substatus']) && !DataValidator::isEmpty($_POST['substatus']) ? $_POST['substatus'] : null);

		//Sacado
		$acompanhamento->setSacado(self::setSacadoData());

		//Jornal
		if (isset($_POST['jornal_id']) && !DataValidator::isEmpty($_POST['jornal_id'])) {
			$jornal = new Jornal();
			$jornal->setId($_POST['jornal_id']);
			$processo->setJornal($jornal);

			$custo = new CustoAcompanhamento();
			$custo->setQuantidadePadrao(isset($_POST['quantidade_padrao']) && !DataValidator::isEmpty($_POST['quantidade_padrao']) ? $_POST['quantidade_padrao'] : 0);
			$custo->setQuantidadeDje(isset($_POST['quantidade_dje']) && !DataValidator::isEmpty($_POST['quantidade_dje']) ? $_POST['quantidade_dje'] : 0);
			$custo->setValorPadrao(isset($_POST['valor_padrao']) && !DataValidator::isEmpty($_POST['valor_padrao']) ? $_POST['valor_padrao'] : 0);
			$custo->setValorDje(isset($_POST['valor_dje']) && !DataValidator::isEmpty($_POST['valor_dje']) ? $_POST['valor_dje'] : 0);
			$custo->setValorFinal(isset($_POST['valor_final']) && !DataValidator::isEmpty($_POST['valor_final']) ? $_POST['valor_final'] : 0);
			$acompanhamento->setCusto($custo);
		}

		//observacoes do advogado
		if (isset($_POST['observacao'])) {
			for ($b = 0; $b < sizeof($_POST['observacao']); $b++) {
				$obs = new Observacao();
				$obs->setId($_POST['obs_id'][$b]);
				$obs->setMensagem($_POST['observacao'][$b]);
				$obs->setUsuarioCadastroId($_POST['usuario_id']);

				$acompanhamento->setObservacao($obs);
			}
		}

		//observacoes financeiras
		if (isset($_POST['observacao_financeiro'])) {

			for ($a = 0; $a < sizeof($_POST['observacao_financeiro']); $a++) {
				$obs_fin = new Observacao();
				$obs_fin->setId($_POST['obs_finan_id'][$a]);
				$obs_fin->setMensagem($_POST['observacao_financeiro'][$a]);
				$obs_fin->setUsuarioCadastroId($_POST['usuario_id']);

				$acompanhamento->setObservacaoFinanceiro($obs_fin);
			}
		}

		//observacoes do acompanhamento
		if (isset($_POST['observacao_acompanhamento'])) {
			for ($c = 0; $c < sizeof($_POST['observacao_acompanhamento']); $c++) {
				$obs_ac = new Observacao();
				$obs_ac->setId($_POST['obs_acomp_id'][$c]);
				$obs_ac->setMensagem($_POST['observacao_acompanhamento'][$c]);
				$obs_ac->setUsuarioCadastroId($_POST['usuario_id']);

				$acompanhamento->setObservacaoAcompanhamento($obs_ac);
			}
		}

		$proposta->setProcesso($processo);
		$acompanhamento->setProposta($proposta);

		session_start();
		if (isset($_POST['key']) && isset($_SESSION[self::ACOMP_KEY]) && $_POST['key'] == $_SESSION[self::ACOMP_KEY])
			$msg = AcompanhamentoModel::update($acompanhamento);

		if (!DataValidator::isEmpty($_POST['acompanhamento_id'])) {
			try {
				$acompanhamento = AcompanhamentoModel::getById($acompanhamento->getId(), null, 'emails_envio');
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('sucesso' => 'Acompanhamento alterado com sucesso.', 'acompanhamento' => $acompanhamento, 'flag' => 'sim'));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento, 'flag' => 'sim'));
			$view->showContents();
		}
	}

	//envia a obs
	public static function enviarObservacaoAction()
	{
		$acompanhamento = null;
		$msg = null;

		try {
			if (
				isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id']) &&
				isset($_REQUEST['usuario_id']) && !DataValidator::isEmpty($_REQUEST['usuario_id']) &&
				isset($_REQUEST['usuario_nome']) && !DataValidator::isEmpty($_REQUEST['usuario_nome']) &&
				isset($_REQUEST['observacao_id']) && !DataValidator::isEmpty($_REQUEST['observacao_id']) &&
				isset($_REQUEST['andamento_email_destino']) && !DataValidator::isEmpty($_REQUEST['andamento_email_destino'])
			) {

				$acompanhamento = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id'], null, 'envio_obs');
				$obs = new Observacao();
				$obs->setId($_REQUEST['observacao_id']);
				$obs->setEmailDestino($_REQUEST['andamento_email_destino']);

				if (!DataValidator::isEmpty($acompanhamento))
					AcompanhamentoModel::enviaEmail($acompanhamento, $_REQUEST['usuario_id'], $_REQUEST['usuario_nome'], $obs);

				$acompanhamento = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id']);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('sucesso' => 'Andamento Processual enviado com sucesso.', 'acompanhamento' => $acompanhamento));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento));
			$view->showContents();
		}
	}

	//envia petições
	public static function envioAction()
	{
		$acompanhamento = null;
		$msg = null;

		try {
			if (
				isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id']) &&
				isset($_REQUEST['tipo_peticao']) && !DataValidator::isEmpty($_REQUEST['tipo_peticao'])
			) {

				$acompanhamento = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id'], null, 'emails_envio');
				if (!DataValidator::isEmpty($acompanhamento))
					$msg = AcompanhamentoModel::enviaPeticao($acompanhamento, $_REQUEST['tipo_peticao']);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('sucesso' => 'Petição enviada com sucesso.', 'acompanhamento' => $acompanhamento));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento));
			$view->showContents();
		}
	}

	//gera petições
	public static function geracaoAction()
	{
		$acompanhamento = null;
		$msg = null;

		try {
			if (
				isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id']) &&
				isset($_REQUEST['tipo_peticao']) && !DataValidator::isEmpty($_REQUEST['tipo_peticao'])
			) {

				$acompanhamento = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id']);
				if (!DataValidator::isEmpty($acompanhamento))
					AcompanhamentoModel::geraPeticao($acompanhamento, $_REQUEST['tipo_peticao']);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		if (DataValidator::isEmpty($msg)) {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('sucesso' => 'Petição gerada com sucesso.', 'acompanhamento' => $acompanhamento));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento));
			$view->showContents();
		}
	}

	//exclui um Acompanhamento Processual
	public static function excluiAction()
	{
		$acompanhamento_id = null;
		$processo_id = null;
		$msg = null;

		$acompanhamento_id = isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id']) ? $_REQUEST['acompanhamento_id'] : 0;
		$processo_id = isset($_REQUEST['processo_id']) && !DataValidator::isEmpty($_REQUEST['processo_id']) ? $_REQUEST['processo_id'] : 0;
		$msg = AcompanhamentoModel::exclui($acompanhamento_id, $processo_id);

		if (DataValidator::isEmpty($msg)) {
			header("Location: ?controle=Acompanhamento&acao=index");
		} else {
			$view = new View('views/gerenciar-acompanhamento.php');
			$view->setParams(array('mensagem' => $msg, 'acompanhamento' => $acompanhamento_id));
			$view->showContents();
		}
	}

	public function limpaBuscaAction()
	{
		session_start();
		unset($_SESSION[self::PESQUISA_ACOMPANHA]);
		header("Location:?controle=Acompanhamento&acao=index");
	}



	private static function setSacadoData()
	{
		$sacado = new SacadoAcompanhamento();
		$sacado->setAcompanhamentoId(isset($_POST['acompanhamento_id']) && !DataValidator::isEmpty($_POST['acompanhamento_id']) ? $_POST['acompanhamento_id'] : 0);
		$sacado->setSacadoId(isset($_POST['sacado_id']) && !DataValidator::isEmpty($_POST['sacado_id']) ? $_POST['sacado_id'] : 0);
		$sacado->setNomeSacado(isset($_POST['sacado_nome']) && !DataValidator::isEmpty($_POST['sacado_nome']) ? $_POST['sacado_nome'] : null);
		$sacado->setCpfCnpj(isset($_POST['sacado_cpf_cnpj']) && !DataValidator::isEmpty($_POST['sacado_cpf_cnpj']) ? $_POST['sacado_cpf_cnpj'] : null);

		$endereco = new Endereco();
		$endereco->setCep(isset($_POST['sacado_cep']) && !DataValidator::isEmpty($_POST['sacado_cep']) ? DataFilter::numeric($_POST['sacado_cep']) : null);
		$endereco->setLogradouro(isset($_POST['sacado_logradouro']) && !DataValidator::isEmpty($_POST['sacado_logradouro']) ? $_POST['sacado_logradouro'] : null);
		$endereco->setNumero(isset($_POST['sacado_numero']) && !DataValidator::isEmpty($_POST['sacado_numero']) ? $_POST['sacado_numero'] : null);
		$endereco->setComplemento(isset($_POST['sacado_complemento']) && !DataValidator::isEmpty($_POST['sacado_complemento']) ? $_POST['sacado_complemento'] : null);
		$endereco->setBairro(isset($_POST['sacado_bairro']) && !DataValidator::isEmpty($_POST['sacado_bairro']) ? $_POST['sacado_bairro'] : null);
		$endereco->setCidade(isset($_POST['sacado_cidade']) && !DataValidator::isEmpty($_POST['sacado_cidade']) ? $_POST['sacado_cidade'] : null);
		$endereco->setEstado(isset($_POST['sacado_estado']) && !DataValidator::isEmpty($_POST['sacado_estado']) ? $_POST['sacado_estado'] : null);
		$sacado->setEndereco($endereco);

		$usuario = new Usuario();
		$usuario->setId($_POST['usuario_id']);
		$sacado->setUsuario($usuario);

		// EMAILS SACADO
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

		return $sacado;
	}
}
