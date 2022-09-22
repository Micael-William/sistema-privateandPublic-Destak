<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("classes/Paginacao.class.php");
require_once("models/AcompanhamentoModel.php");
require_once("models/AcompanhamentoStatusModel.php");
require_once("classes/Pesquisa.class.php");
require_once("phpmailer/class.phpmailer.php");

require_once("models/BoletoModel.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class BoletoController
{

	const BOLETO_KEY = "boleto_key";
	const QTD_PAGINACAO = 100;
	const PESQUISA_BOLETO = "Resultado-Boleto";

	public function indexAction()
	{
		$retorno = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;
		$o = isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : 0;
		$s = isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : 'd';

		$retorno = BoletoModel::lista(null, null, null, 0, null, null, 0, null, $p, self::QTD_PAGINACAO, $o, $s);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setOrdenacao($o);
		$paginacao->setSentidoOrdenacao($s);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Boleto');

		$view = new View('views/boletos.php');
		$view->setParams(array('boletos' => $retorno['boletos'], 'paginacao' => $paginacao));
		$view->showContents();
	}

	public static function buscaAction($msg = null)
	{

		session_start();

		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		$retorno = BoletoModel::lista(
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
			$pesquisa->getSentidoOrdenacao()
		);


		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setOrdenacao($pesquisa->getOrdenacao());
		$paginacao->setSentidoOrdenacao($pesquisa->getSentidoOrdenacao());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Acompanhamento');

		$params = array('boletos' => $retorno['boletos'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa);

		if (!empty($msg) && is_array($msg)) {
			$params = array_merge_recursive($params, $msg);
		}

		$view = new View('views/boletos.php');
		$view->setParams($params);
		$view->showContents();
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA_BOLETO]);

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_BOLETO]) ? new Pesquisa() : $_SESSION[self::PESQUISA_BOLETO];

		if (!isset($_SESSION[self::PESQUISA_BOLETO]))
			$_SESSION[self::PESQUISA_BOLETO] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_BOLETO]->getPagina());
		$pesquisa->setOrdenacao(isset($_REQUEST["ordenacao"]) && !DataValidator::isEmpty($_REQUEST['ordenacao']) ? $_REQUEST["ordenacao"] : $_SESSION[self::PESQUISA_BOLETO]->getOrdenacao());
		$pesquisa->setSentidoOrdenacao(isset($_REQUEST["sentido_ordenacao"]) && !DataValidator::isEmpty($_REQUEST['sentido_ordenacao']) ? $_REQUEST["sentido_ordenacao"] : $_SESSION[self::PESQUISA_BOLETO]->getSentidoOrdenacao());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_BOLETO]->getStatus());
		$pesquisa->setNomeAdvogado(isset($_REQUEST["busca_advogado"]) && !DataValidator::isEmpty($_REQUEST['busca_advogado']) ? $_REQUEST["busca_advogado"] : $_SESSION[self::PESQUISA_BOLETO]->getNomeAdvogado());
		$pesquisa->setSecretariaId(isset($_REQUEST["busca_secretaria"]) && !DataValidator::isEmpty($_REQUEST['busca_secretaria']) ? $_REQUEST["busca_secretaria"] : $_SESSION[self::PESQUISA_BOLETO]->getSecretariaId());
		$pesquisa->setNumeroProcesso(isset($_REQUEST["busca_num_processo"]) && !DataValidator::isEmpty($_REQUEST['busca_num_processo']) ? $_REQUEST["busca_num_processo"] : $_SESSION[self::PESQUISA_BOLETO]->getNumeroProcesso());
		$pesquisa->setNomeRequerente(isset($_REQUEST["busca_requerente"]) && !DataValidator::isEmpty($_REQUEST['busca_requerente']) ? $_REQUEST["busca_requerente"] : $_SESSION[self::PESQUISA_BOLETO]->getNomeRequerente());
		$pesquisa->setNomeRequerido(isset($_REQUEST["busca_requerido"]) && !DataValidator::isEmpty($_REQUEST['busca_requerido']) ? $_REQUEST["busca_requerido"] : $_SESSION[self::PESQUISA_BOLETO]->getNomeRequerido());
		$pesquisa->setCodigoInterno(isset($_REQUEST["busca_acompanhamento"]) && !DataValidator::isEmpty($_REQUEST['busca_acompanhamento']) ? $_REQUEST["busca_acompanhamento"] : $_SESSION[self::PESQUISA_BOLETO]->getCodigoInterno());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_BOLETO]->getEstado());

		return $pesquisa;
	}

	public static function detalheAction()
	{
		$bol = null;
		$msg = null;

		try {
			if (isset($_REQUEST['boleto_id']) && !DataValidator::isEmpty($_REQUEST['boleto_id']))
				$bol = BoletoModel::getById($_REQUEST['boleto_id']);
			$acomp = AcompanhamentoModel::getById($bol->getAcompanhamentoId());
			$bol->setAcompanhamento($acomp);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-boleto.php');
		$view->setParams(array('mensagem' => $msg, 'boleto' => $bol, 'flag' => 'sim'));
		$view->showContents();
	}

	public static function pdfAction()
	{

		$bol = null;
		$msg = null;

		try {
			if (isset($_REQUEST['boleto_id']) && !DataValidator::isEmpty($_REQUEST['boleto_id'])) {
				$bol = BoletoModel::getById($_REQUEST['boleto_id']);

				$acomp = $bol->getAcompanhamento();
				$proposta = $acomp->getProposta();
				$processo = $proposta->getProcesso();
				$entrada = $processo->getEntrada();

				$file = $bol->getIuguBoleto();

				header('Content-type: application/pdf');
				header('Content-Disposition: attachment; filename="' . $entrada->getNumero() . '.pdf"');
				readfile($file);
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		echo $msg;
	}

	public function cancelaAction()
	{
		$advogado = null;
		$msg = null;

		$boleto_id = isset($_REQUEST['boleto_id']) && !DataValidator::isEmpty($_REQUEST['boleto_id']) ? $_REQUEST['boleto_id'] : 0;


		$msg = BoletoModel::cancelarFatura($boleto_id);

		if (DataValidator::isEmpty($msg)) {
			self::buscaAction(array('sucesso' => 'Boleto cancelado com sucesso.'));
		} else {
			$bol = BoletoModel::getById($_REQUEST['boleto_id']);

			$view = new View('views/gerenciar-boleto.php');
			$view->setParams(array('mensagem' => $msg, 'boleto' => $bol, 'flag' => 'sim'));
			$view->showContents();
		}
	}

	public function emiteAction()
	{
		$acomp = null;
		$msg = null;
		$boleto = null;

		$params = array();

		try {
			if (isset($_REQUEST['acompanhamento_id']) && !DataValidator::isEmpty($_REQUEST['acompanhamento_id'])) {
				$boleto = BoletoModel::criarFatura($_REQUEST);

				if ((int)$boleto == 0) {
					$msg = 'Problema ao gerar Boleto: <br> ' .  $boleto;
				} else {
					$params['boleto'] = $boleto;
				}
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$acomp = AcompanhamentoModel::getById($_REQUEST['acompanhamento_id'], null, 'emails_envio');

		$params['mensagem'] = $msg;
		$params['acompanhamento'] = $acomp;
		$params['flag'] = 'sim';

		$view = new View('views/gerenciar-acompanhamento.php');
		$view->setParams($params);
		$view->showContents();
	}

	public function salvaAction()
	{
		$msg = "";
		$bol = BoletoModel::getById($_REQUEST['boleto_id']);

		//observacoes do advogado
		if (isset($_POST['observacao'])) {
			for ($b = 0; $b < sizeof($_POST['observacao']); $b++) {
				$obs = new Observacao();
				$obs->setId($_POST['obs_boleto_id'][$b]);
				$obs->setMensagem($_POST['observacao'][$b]);
				$obs->setUsuarioCadastroId($_POST['usuario_id']);

				$bol->setObservacao($obs);
			}
		}

		try {
			$msg = BoletoModel::saveObs($bol);

			$bol = BoletoModel::getById($_REQUEST['boleto_id']);
			$acomp = AcompanhamentoModel::getById($bol->getAcompanhamentoId());
			$bol->setAcompanhamento($acomp);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-boleto.php');
		$view->setParams(array('sucesso' => 'Boleto salvo com sucesso.', 'mensagem' => $msg, 'boleto' => $bol, 'flag' => 'sim'));
		$view->showContents();
	}

	public function limpaBuscaAction()
	{
		session_start();
		unset($_SESSION[self::PESQUISA_BOLETO]);
		header("Location:?controle=Acompanhamento&acao=index");
	}
}
