<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("models/JornalModel.php");
require_once("classes/Paginacao.class.php");
require_once("classes/PesquisaJornal.class.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class JornalController
{

	const QTD_PAGINACAO = 100;
	const PESQUISA_JORNAL = "Resultado-jornal";

	public function indexAction($msg = array())
	{
		$params = array();
		$retorno = null;

		//Paginacao
		$paginacao = null;
		$p = isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : 1;

		$retorno = JornalModel::lista(null, 0, null, null, null, null, null, 'A', null, null, $p, self::QTD_PAGINACAO);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($p);
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Jornal');

		$params['jornais'] = $retorno['jornais'];
		$params['paginacao'] = $paginacao;
		if (isset($msg['sucesso'])) $params['mensagens'] = $msg;

		$view = new View('views/jornais.php');
		$view->setParams($params);
		$view->showContents();
	}

	public static function buscaAction()
	{

		@session_start();

		$msg = null;
		$retorno = null;
		$pesquisa = self::getPesquisaAction();

		$retorno = JornalModel::lista(
			$pesquisa->getStatus(),
			$pesquisa->getSecretariaId(),
			$pesquisa->getNome(),
			$pesquisa->getRepresentante(),
			$pesquisa->getCidade(),
			$pesquisa->getEstado(),
			$pesquisa->getEmail(),
			$pesquisa->getAtivo(),
			$pesquisa->getEndereco(),
			$pesquisa->getTelefone(),
			$pesquisa->getPagina(),
			self::QTD_PAGINACAO
		);

		$paginacao = new Paginacao();
		$paginacao->setQtdPagina(self::QTD_PAGINACAO);
		$paginacao->setNumeroPagina($pesquisa->getPagina());
		$paginacao->setTotalRegistros($retorno['totalLinhas']);
		$paginacao->setPaginaDestino('Jornal');

		$view = new View('views/jornais.php');
		$view->setParams(array('jornais' => $retorno['jornais'], 'paginacao' => $paginacao, 'pesquisa' => $pesquisa));
		$view->showContents();
	}

	public function salvaAction()
	{
		$retorno = null;
		$sucesso = null;

		$jornal = new Jornal();
		$jornal->setId(isset($_POST['jornal_id']) && !DataValidator::isEmpty($_POST['jornal_id']) ? $_POST['jornal_id'] : 0);
		$jornal->setNome(isset($_POST['nome']) && !DataValidator::isEmpty($_POST['nome']) ? $_POST['nome'] : null);
		$jornal->setDataConfirmacao(isset($_POST['data_confirmacao']) && !DataValidator::isEmpty($_POST['data_confirmacao']) ? $_POST['data_confirmacao'] : null);
		$jornal->setStatus(isset($_POST['status']) && !DataValidator::isEmpty($_POST['status']) ? $_POST['status'] : null);
		$jornal->setAtivo(isset($_POST['ativo']) && !DataValidator::isEmpty($_POST['ativo']) ? $_POST['ativo'] : null);
		$jornal->setNomeRepresentante(isset($_POST['nome_representante']) && !DataValidator::isEmpty($_POST['nome_representante']) ? $_POST['nome_representante'] : null);
		$jornal->setContatoRepresentante(isset($_POST['contato_representante']) && !DataValidator::isEmpty($_POST['contato_representante']) ? $_POST['contato_representante'] : null);

		$jornal->setEstadoPeriodo(isset($_POST['estado_periodo']) && !DataValidator::isEmpty($_POST['estado_periodo']) ? $_POST['estado_periodo'] : null);
		$jornal->setFechamento(isset($_POST['fechamento']) && !DataValidator::isEmpty($_POST['fechamento']) ? $_POST['fechamento'] : null);
		$jornal->setComposicao(isset($_POST['composicao']) && !DataValidator::isEmpty($_POST['composicao']) ? $_POST['composicao'] : null);
		$jornal->setObservacoes(isset($_POST['observacoes']) && !DataValidator::isEmpty($_POST['observacoes']) ? $_POST['observacoes'] : null);

		$endereco = new Endereco();
		$endereco->setCep(isset($_POST['cep']) && !DataValidator::isEmpty($_POST['cep']) ? DataFilter::numeric($_POST['cep']) : null);
		$endereco->setLogradouro(isset($_POST['logradouro']) && !DataValidator::isEmpty($_POST['logradouro']) ? $_POST['logradouro'] : null);
		$endereco->setNumero(isset($_POST['numero']) && !DataValidator::isEmpty($_POST['numero']) ? $_POST['numero'] : null);
		$endereco->setComplemento(isset($_POST['complemento']) && !DataValidator::isEmpty($_POST['complemento']) ? $_POST['complemento'] : null);
		$endereco->setBairro(isset($_POST['bairro']) && !DataValidator::isEmpty($_POST['bairro']) ? $_POST['bairro'] : null);
		$endereco->setCidade(isset($_POST['cidade']) && !DataValidator::isEmpty($_POST['cidade']) ? $_POST['cidade'] : null);
		$endereco->setEstado(isset($_POST['estado']) && !DataValidator::isEmpty($_POST['estado']) ? $_POST['estado'] : null);
		$jornal->setEndereco($endereco);

		$custo = new Custo();
		$custo->setJornalId(isset($_POST['jornal_id']) && !DataValidator::isEmpty($_POST['jornal_id']) ? $_POST['jornal_id'] : 0);
		$custo->setMedida(isset($_POST['medida']) && !DataValidator::isEmpty($_POST['medida']) ? $_POST['medida'] : 0);
		$custo->setValorForense(isset($_POST['valor_forense']) && !DataValidator::isEmpty($_POST['valor_forense']) ? $_POST['valor_forense'] : 0);
		$custo->setNegociacao(isset($_POST['negociacao']) && !DataValidator::isEmpty($_POST['negociacao']) ? $_POST['negociacao'] : 0);
		$custo->setDesconto(isset($_POST['desconto']) && !DataValidator::isEmpty($_POST['desconto']) ? $_POST['desconto'] : 0);
		$custo->setValorPadrao(isset($_POST['valor_padrao']) && !DataValidator::isEmpty($_POST['valor_padrao']) ? $_POST['valor_padrao'] : 0);
		$custo->setValorDje(isset($_POST['valor_dje']) && !DataValidator::isEmpty($_POST['valor_dje']) ? $_POST['valor_dje'] : 0);
		$custo->setValorEmpregos(isset($_POST['valor_empregos']) && !DataValidator::isEmpty($_POST['valor_empregos']) ? $_POST['valor_empregos'] : 0);
		$custo->setValorPublicidade(isset($_POST['valor_publicidade']) && !DataValidator::isEmpty($_POST['valor_publicidade']) ? $_POST['valor_publicidade'] : 0);
		$jornal->setCusto($custo);

		$usuario = new Usuario();
		$usuario->setId($_POST['usuario_id']);
		$jornal->setUsuario($usuario);

		if (isset($_POST['ddd']) && isset($_POST['numero_telefone'])) {
			for ($i = 0; $i < sizeof($_POST['numero_telefone']); $i++) {
				$tel = new Telefone();
				$tel->setId($_POST['tel_id'][$i]);
				$tel->setDdd($_POST['ddd'][$i]);
				$tel->setNumero($_POST['numero_telefone'][$i]);
				$jornal->setTelefone($tel);
			}
		}

		if (isset($_POST['email'])) {
			for ($e = 0; $e < sizeof($_POST['email']); $e++) {
				$email = new Email();
				$email->setId($_POST['email_id'][$e]);
				$email->setEmailEndereco($_POST['email'][$e]);
				$jornal->setEmail($email);
			}
		}

		if (isset($_POST['cidade_circulacao'])) {
			for ($cid = 0; $cid < sizeof($_POST['cidade_circulacao']); $cid++) {
				$cidade = new Cidade();
				$cidade->setId($_POST['cidade_id'][$cid]);
				$cidade->setNome($_POST['cidade_circulacao'][$cid]);
				$jornal->setCidade($cidade);
			}
		}

		if (isset($_POST['secretaria'])) {
			for ($s = 0; $s < sizeof($_POST['secretaria']); $s++) {
				$secretaria = new Secretaria();
				$secretaria->setId($_POST['secretaria'][$s]);
				$jornal->setSecretaria($secretaria);
			}
		}

		if (isset($_POST['periodo'])) {
			for ($b = 0; $b < sizeof($_POST['periodo']); $b++) {
				$jornal->setPeriodo($_POST['periodo'][$b]);
			}
		}

		if (!DataValidator::isEmpty($jornal->getId()))
			$retorno = JornalModel::update($jornal);
		else
			$retorno = JornalModel::insert($jornal);

		if (!DataValidator::isEmpty($_POST['jornal_id'])) {
			try {
				$advogado = JornalModel::getById($jornal->getId());
			} catch (UserException $e) {
				$retorno['msg'] = $e->getMessage();
			}
		}

		if (DataValidator::isEmpty($_POST['jornal_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$this->indexAction(array('sucesso' => 'Jornal cadastrado com sucesso.'));
		} elseif (!DataValidator::isEmpty($_POST['jornal_id']) && DataValidator::isEmpty($retorno['msg'])) {
			$view = new View('views/gerenciar-jornal.php');
			$view->setParams(array('sucesso' => 'Jornal alterado com sucesso.', 'jornal' => $jornal));
			$view->showContents();
		} else {
			$view = new View('views/gerenciar-jornal.php');
			$view->setParams(array('mensagem' => $retorno['msg'], 'jornal' => $jornal));
			$view->showContents();
		}
	}

	public static function limparAction()
	{

		session_start();
		unset($_SESSION[self::PESQUISA_JORNAL]);
		self::buscaAction();
	}

	public static function detalheAction()
	{
		$jornal = null;
		$msg = null;

		try {
			if (isset($_REQUEST['jornal_id']) && !DataValidator::isEmpty($_REQUEST['jornal_id']))
				$jornal = JornalModel::getById($_REQUEST['jornal_id']);
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		$view = new View('views/gerenciar-jornal.php');
		$view->setParams(array('mensagem' => $msg, 'jornal' => $jornal));
		$view->showContents();
	}

	public static function getPesquisaAction()
	{

		if (isset($_REQUEST['origem']) && !DataValidator::isEmpty($_REQUEST['origem']))
			echo '';
		else
			unset($_SESSION[self::PESQUISA_JORNAL]);

		/**/

		$pesquisa = !isset($_SESSION[self::PESQUISA_JORNAL]) ? new PesquisaJornal() : $_SESSION[self::PESQUISA_JORNAL];

		if (!isset($_SESSION[self::PESQUISA_JORNAL]))
			$_SESSION[self::PESQUISA_JORNAL] = $pesquisa;

		/**/

		$pesquisa->setPagina(isset($_REQUEST["numero_pagina"]) && !DataValidator::isEmpty($_REQUEST['numero_pagina']) ? $_REQUEST["numero_pagina"] : $_SESSION[self::PESQUISA_JORNAL]->getPagina());

		if (DataValidator::isEmpty($pesquisa->getPagina()))
			$pesquisa->setPagina(1);

		$pesquisa->setStatus(isset($_REQUEST["busca_status"]) && !DataValidator::isEmpty($_REQUEST['busca_status']) ? $_REQUEST["busca_status"] : $_SESSION[self::PESQUISA_JORNAL]->getStatus());
		$pesquisa->setAtivo(isset($_REQUEST["busca_ativo"]) && !DataValidator::isEmpty($_REQUEST['busca_ativo']) ? $_REQUEST["busca_ativo"] : $_SESSION[self::PESQUISA_JORNAL]->getAtivo());
		$pesquisa->setSecretariaId(isset($_REQUEST["busca_secretaria"]) && !DataValidator::isEmpty($_REQUEST['busca_secretaria']) ? $_REQUEST["busca_secretaria"] : $_SESSION[self::PESQUISA_JORNAL]->getSecretariaId());
		$pesquisa->setNome(isset($_REQUEST["busca_jornal"]) && !DataValidator::isEmpty($_REQUEST['busca_jornal']) ? $_REQUEST["busca_jornal"] : $_SESSION[self::PESQUISA_JORNAL]->getNome());
		$pesquisa->setRepresentante(isset($_REQUEST["busca_representante"]) && !DataValidator::isEmpty($_REQUEST['busca_representante']) ? $_REQUEST["busca_representante"] : $_SESSION[self::PESQUISA_JORNAL]->getRepresentante());
		$pesquisa->setEndereco(isset($_REQUEST["busca_endereco"]) && !DataValidator::isEmpty($_REQUEST['busca_endereco']) ? $_REQUEST["busca_endereco"] : $_SESSION[self::PESQUISA_JORNAL]->getEndereco());
		$pesquisa->setCidade(isset($_REQUEST["busca_cidade"]) && !DataValidator::isEmpty($_REQUEST['busca_cidade']) ? $_REQUEST["busca_cidade"] : $_SESSION[self::PESQUISA_JORNAL]->getCidade());
		$pesquisa->setEstado(isset($_REQUEST["busca_estado"]) && !DataValidator::isEmpty($_REQUEST['busca_estado']) ? $_REQUEST["busca_estado"] : $_SESSION[self::PESQUISA_JORNAL]->getEstado());
		$pesquisa->setEmail(isset($_REQUEST["busca_email"]) && !DataValidator::isEmpty($_REQUEST['busca_email']) ? $_REQUEST["busca_email"] : $_SESSION[self::PESQUISA_JORNAL]->getEmail());
		$pesquisa->setTelefone(isset($_REQUEST["busca_telefone"]) && !DataValidator::isEmpty($_REQUEST['busca_telefone']) ? $_REQUEST["busca_telefone"] : $_SESSION[self::PESQUISA_JORNAL]->getTelefone());

		return $pesquisa;
	}
}
