<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/Externo/Iugu.php");
require_once("models/AcompanhamentoModel.php");
require_once("classes/Boleto.class.php");

date_default_timezone_set('America/Sao_Paulo');

const IUGU_KEY = "de7c17360d4903d414ced852c2777af2";

class BoletoModel extends PersistModelAbstract
{

	public static function lista(
		$status = null,
		$advogado_nome = null,
		$secretaria_id = null,
		$numero_processo = 0,
		$requerente_nome = null,
		$requerido_nome = null,
		$acompanhamento_id = 0,
		$estado = null,
		$pagina = 0,
		$qtd_pagina = 0,
		$ordenacao = 0,
		$sentido_ordenacao = "a"
	) {

		$campos = " 
		CASE WHEN DATE(bo.iugu_vencimento) = DATE(NOW()) THEN 2
				WHEN DATE(bo.iugu_vencimento) < DATE(NOW()) THEN 0
		ELSE 1 END AS ordenacao, 
		bo.id, bo.acompanhamento_ID, ac.proposta_ID, pt.processo_ID, pc.advogado_ID, 
		bo.iugu_invoice, bo.iugu_boleto, bo.iugu_valor, bo.iugu_url, bo.iugu_vencimento, bo.iugu_status, bo.data_alteracao, 
		bo.iugu_request, pe.numero, pe.data_processo, pe.estado, adv.nome_advogado  
		";

		$sql = " SELECT {{campos}}
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID
					INNER JOIN advogado adv ON adv.id = pc.advogado_ID
					LEFT JOIN secretaria s ON pc.secretaria_id=s.id ";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "bo.iugu_status=:status ";
		}

		if (!DataValidator::isEmpty($advogado_nome)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.nome_advogado LIKE _utf8 :advogado_nome COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($secretaria_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " (";
			foreach ($secretaria_id as $i => $sec_id) {
				$sql .= " pc.secretaria_ID=:sec_id{$i} OR ";
			}
			$sql = substr($sql, 0, strlen($sql) - 3);
			$sql .= ") ";
		}

		if (!DataValidator::isEmpty($numero_processo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pe.numero LIKE :numero_processo ";
		}

		if (!DataValidator::isEmpty($requerente_nome)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pc.requerente LIKE _utf8 :requerente_nome COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($requerido_nome)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pc.requerido LIKE _utf8 :requerido_nome COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($acompanhamento_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " ac.id=:acompanhamento_id ";
		}

		if (!DataValidator::isEmpty($estado)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pe.estado=:estado ";
		}

		$sql .= " ORDER BY ";

		$order = array();
		$arr_sentido = array("a" => "ASC", "d" => "DESC");

		switch ($ordenacao) {
			case 1:
				array_push($order, 'pe.numero' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 2:
				array_push($order, 'adv.nome_advogado' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 3:
				array_push($order, 'bo.iugu_vencimento' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 4:
				array_push($order, 'bo.iugu_valor' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 5:
				array_push($order, 'bo.iugu_status' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 6:
				array_push($order, 'pe.estado' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			default:
				array_push($order, '1 DESC');
				array_push($order, 'bo.iugu_vencimento DESC');
				array_push($order, 'pe.id DESC');
				break;
		}

		$sql .= implode(",", $order);

		$sqlbase = $sql;

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$boletoModel = new BoletoModel();

		$query = $boletoModel->getDB()->prepare("SET @@session.group_concat_max_len = 10000;");
		$query->execute();

		$query = $boletoModel->getDB()->prepare(str_replace('{{campos}}', $campos, $sql));
		$query_num_linhas = $boletoModel->getDB()->prepare(str_replace('{{campos}}', ' COUNT(*) as items ', $sqlbase));

		if (!DataValidator::isEmpty($status)) {
			$query->bindValue(':status', $status, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':status', $status, PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($advogado_nome)) {
			$query->bindValue(':advogado_nome', "%$advogado_nome%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':advogado_nome', "%$advogado_nome%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($secretaria_id)) {
			foreach ($secretaria_id as $key => $value) {
				$query->bindValue(':sec_id' . $key, $value, PDO::PARAM_INT);
				$query_num_linhas->bindValue(':sec_id' . $key, $value, PDO::PARAM_INT);
			}
		}

		if (!DataValidator::isEmpty($numero_processo)) {
			$query->bindValue(':numero_processo', "%$numero_processo%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':numero_processo', "%$numero_processo%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($requerente_nome)) {
			$query->bindValue(':requerente_nome', "%$requerente_nome%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':requerente_nome', "%$requerente_nome%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($requerido_nome)) {
			$query->bindValue(':requerido_nome', "%$requerido_nome%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':requerido_nome', "%$requerido_nome%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($acompanhamento_id)) {
			$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);
			$query_num_linhas->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);
		}

		if (!DataValidator::isEmpty($estado)) {
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':estado', $estado, PDO::PARAM_STR);
		}

		$query->execute();

		//*******
		$query_num_linhas->execute();

		$num_linhas = $query_num_linhas->fetchObject();
		$totalLinhas = $num_linhas->items;
		//*******

		$boletos = [];

		while ($linha = $query->fetchObject()) {
			$boletos[] = self::setData($linha);
		}

		return array('boletos' => $boletos, 'totalLinhas' => $totalLinhas);
	}

	private static function setData($linha)
	{
		$boleto = new Boleto();
		$boleto->setId($linha->id);
		$boleto->setAcompanhamentoId($linha->acompanhamento_ID);
		$boleto->setIuguInvoice($linha->iugu_invoice);
		$boleto->setIuguUrl($linha->iugu_url);
		$boleto->setIuguBoleto($linha->iugu_boleto);
		$boleto->setIuguVencimento($linha->iugu_vencimento);
		$boleto->setIuguStatus($linha->iugu_status);
		$boleto->setIuguRequest($linha->iugu_request);
		$boleto->setIuguValor($linha->iugu_valor);
		$boleto->setDataAlteracao($linha->data_alteracao);

		$acompanhamento = new Acompanhamento();
		$acompanhamento->setId($linha->acompanhamento_ID);

		$proposta = new Proposta();
		$proposta->setId($linha->proposta_ID);

		$processo = new Processo();
		$processo->setId($linha->processo_ID);

		$entrada = new ProcessoEntrada();
		$entrada->setNumero($linha->numero);
		$entrada->setEstado($linha->estado);
		$entrada->setDataProcesso($linha->data_processo);

		if (!DataValidator::isEmpty($linha->advogado_ID)) {
			$advogado = new Advogado();
			$advogado->setNome($linha->nome_advogado);
			$processo->setAdvogado($advogado);
		}

		$processo->setEntrada($entrada);
		$proposta->setProcesso($processo);
		$acompanhamento->setProposta($proposta);

		$boleto->setObservacoes(ObservacaoModel::getObservacoes($boleto));

		$boleto->setAcompanhamento($acompanhamento);

		return $boleto;
	}

	public static function getListaBoletoPendente()
	{
		$sql = " SELECT bo.id, bo.acompanhamento_ID, ac.proposta_ID, pt.processo_ID, 
					pc.advogado_ID, bo.iugu_invoice, bo.iugu_boleto, bo.iugu_valor, bo.iugu_url, bo.iugu_vencimento, bo.iugu_status, bo.data_alteracao, bo.iugu_request, pe.numero, pe.data_processo, pe.estado, adv.nome_advogado
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID
					INNER JOIN advogado adv ON adv.id = pc.advogado_ID
					LEFT JOIN secretaria s ON pc.secretaria_id=s.id 
				WHERE bo.iugu_status NOT IN('canceled', 'paid', 'overdue') 
				  AND DATE(bo.iugu_vencimento) <= DATE(NOW()) ";

		$boletoModel = new BoletoModel();

		$query = $boletoModel->getDB()->prepare($sql);

		$query->execute();

		$boletos = [];

		while ($linha = $query->fetchObject()) {
			$boletos[] = self::setData($linha);
		}

		return $boletos;
	}

	public static function getByIuguInvoice($invoice_id)
	{
		$boleto = null;

		if (DataValidator::isEmpty($invoice_id))
			throw new UserException('A invoice deve ser informada.');

		$sql = " SELECT * FROM acompanhamento_boleto WHERE iugu_invoice=:iugu_invoice ";

		$boletoModel = new BoletoModel();
		$query = $boletoModel->getDB()->prepare($sql);
		$query->bindValue(':iugu_invoice', $invoice_id, PDO::PARAM_STR);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$boleto = new Boleto();
		$boleto->setId($linha->id);
		$boleto->setIuguStatus($linha->iugu_status);

		return $boleto;
	}

	public static function getById($boleto_id)
	{
		$boleto = null;

		if (DataValidator::isEmpty($boleto_id))
			throw new UserException('A boleto deve ser informada.');


		$sql = "SELECT bo.id, bo.acompanhamento_ID, ac.proposta_ID, pt.processo_ID, pc.advogado_ID, 
		bo.iugu_invoice, bo.iugu_valor, bo.iugu_url, bo.iugu_boleto, bo.iugu_vencimento, bo.iugu_status, bo.data_alteracao, 
		bo.iugu_request, pe.numero, pe.data_processo, pe.estado, adv.nome_advogado 
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID
					INNER JOIN advogado adv ON adv.id = pc.advogado_ID 
				 WHERE bo.id=:boleto_id ";

		$boletoModel = new BoletoModel();
		$query = $boletoModel->getDB()->prepare($sql);
		$query->bindValue(':boleto_id', $boleto_id, PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$boleto = self::setData($linha);

		return $boleto;
	}

	public static function insert($boleto)
	{
		$msg = null;

		$boletoModel = new BoletoModel();
		$boletoModel->getDB()->beginTransaction();

		try {

			if (!$boleto instanceof Boleto)
				throw new UserException('Insert Boleto: O objeto deve ser do tipo Boleto.');

			if (DataValidator::isEmpty($boleto))
				throw new UserException('O Boleto deve ser fornecido.');

			$sql = " INSERT INTO acompanhamento_boleto 
									(acompanhamento_ID, 
									 sacado_ID,
									 iugu_invoice, 
									 iugu_valor,
									 iugu_url, 
									 iugu_boleto, 
									 iugu_vencimento, 
									 iugu_status,
									 iugu_request,
									 data_entrada,
									 data_alteracao,
									 usuario_ID
									 ) 
								VALUES 
									(:acompanhamento_id, 
									 :sacado_id,
									 :iugu_invoice, 
									 :iugu_valor,
									 :iugu_url, 
									 :iugu_boleto, 
									 :iugu_vencimento, 
									 :iugu_status,
									 :iugu_request,
									 :data_entrada,
									 :data_alteracao,
									 :usuario_id
									 ) ";

			$query = $boletoModel->getDB()->prepare($sql);
			$query->bindValue(':acompanhamento_id', $boleto->getAcompanhamentoId(), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($boleto->getSacadoId()))
				$query->bindValue(':sacado_id', $boleto->getSacadoId(), PDO::PARAM_STR);
			else
				$query->bindValue(':sacado_id', NULL, PDO::PARAM_STR);

			$query->bindValue(':iugu_invoice', $boleto->getIuguInvoice(), PDO::PARAM_STR);
			$query->bindValue(':iugu_valor', $boleto->getIuguValor(), PDO::PARAM_STR);
			$query->bindValue(':iugu_url', $boleto->getIuguUrl(), PDO::PARAM_STR);
			$query->bindValue(':iugu_boleto', $boleto->getIuguBoleto(), PDO::PARAM_STR);
			$query->bindValue(':iugu_vencimento', $boleto->getIuguVencimento(), PDO::PARAM_STR);
			$query->bindValue(':iugu_status', $boleto->getIuguStatus(), PDO::PARAM_STR);
			$query->bindValue(':iugu_request', $boleto->getIuguRequest(), PDO::PARAM_STR);

			$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

			$query->bindValue(':usuario_id', $boleto->getUsuario()->getId(), PDO::PARAM_INT);

			$query->execute();

			$msg = $boletoModel->getDB()->lastInsertId();

			$boletoModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$boletoModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function update($boleto)
	{
		$msg = null;

		$boletoModel = new BoletoModel();
		$boletoModel->getDB()->beginTransaction();

		try {

			if (!$boleto instanceof Boleto)
				throw new UserException('Update Boleto: O objeto deve ser do tipo Boleto.');

			if (DataValidator::isEmpty($boleto))
				throw new UserException('O Boleto deve ser fornecido.');

			if (DataValidator::isEmpty($boleto->getId()))
				throw new UserException('O Boleto deve ser identificado.');

			$sql = " UPDATE acompanhamento_boleto SET iugu_status=:iugu_status, data_alteracao=:data_alteracao WHERE id=:boleto_id ";

			$query = $boletoModel->getDB()->prepare($sql);
			$query->bindValue(':iugu_status', $boleto->getIuguStatus(), PDO::PARAM_STR);
			$query->bindValue(':boleto_id', $boleto->getId(), PDO::PARAM_INT);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->execute();

			$boletoModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$boletoModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function saveObs($boleto)
	{
		$msg = null;

		$boletoModel = new BoletoModel();
		$boletoModel->getDB()->beginTransaction();

		try {
			//observações do boleto
			ObservacaoModel::saveObservacao($boleto, $boletoModel->getDB());

			$boletoModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$boletoModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function cancelarFatura($boletoId)
	{
		Iugu::setApiKey(IUGU_KEY);

		$msg = '';

		try {

			$boleto = self::getById($boletoId);

			if (!$boleto instanceof Boleto)
				throw new UserException('Update Boleto: O objeto deve ser do tipo Boleto.');

			if (DataValidator::isEmpty($boleto))
				throw new UserException('O Boleto deve ser fornecido.');

			if (DataValidator::isEmpty($boleto->getId()))
				throw new UserException('O Boleto deve ser identificado.');

			$invoice = Iugu_Invoice::fetch($boleto->getIuguInvoice());
			$invoice->cancel();

			$boleto->setIuguStatus('canceled');
			self::update($boleto);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function buscaFaturasPagas()
	{
		Iugu::setApiKey(IUGU_KEY);

		$invoices = [];
		$inicio = 0;
		$limite = 50;
		$hoje = new DateTime();
		$erro = false;

		do {

			try {
				$opcoes = [
					'limit' => $limite,
					'start' => $inicio,
					'paid_at_to' => $hoje->format('Y-m-d'),
					'paid_at_from' => ($hoje->sub(new DateInterval('P5D')))->format('Y-m-d'),
					'status_filter' => 'paid'
				];

				$retorno = Iugu_Invoice::search($opcoes);
				$items = $retorno->results();
				$invoices = array_merge($invoices, $items);

				$inicio += $limite;
			} catch (Exception $ex) {
				error_log($ex->getMessage());
				$erro = true;
			}
		} while ($inicio < $retorno->total() && !$erro);

		return $invoices;
	}

	public static function criarFatura($dados)
	{
		$msg = "";

		try {

			Iugu::setApiKey(IUGU_KEY);

			$acomp = AcompanhamentoModel::getById($dados['acompanhamento_id']);

			$proposta = $acomp->getProposta();
			$processo = $proposta->getProcesso();
			$entrada = $processo->getEntrada();

			$sacado = $acomp->getSacado();
			$endereco = $sacado->getEndereco();

			$custo = $acomp->getCusto();

			$sacadoEmails = $sacado->getEmails();

			$emails = array();

			foreach ($sacadoEmails as $key => $email) {
				$emails[] = $email->getEmailEndereco();
			}

			$descricao = array(
				'description'     => 'Número do Processo: ' . $entrada->getNumero() . ' | Ação: ' . $proposta->getProcesso()->getAcao() . ' | Requerente: ' . $processo->getRequerente() . ' | Requerido: ' . $processo->getRequerido() . ' | Secretaria/Fórum: ' . $processo->getSecretaria()->getNome() . '| Data Aceite: ' . date("d/m/Y", strtotime($proposta->getDataAceite())) . ' ',
				'quantity'         => '1',
				'price_cents'     => $custo->getValorFinal() * 100
			);

			$cliente = array(
				'cpf_cnpj' => $sacado->getCpfCnpj(),
				'name' => $sacado->getNomeSacado(),
				'address' => array(
					'zip_code'      => str_pad($endereco->getCep(), 8, "0", STR_PAD_LEFT),
					'street'        => $endereco->getLogradouro(),
					'number'        => $endereco->getNumero(),
					'district'      => $endereco->getBairro(),
					'city'          => $endereco->getCidade(),
					'state'         => $endereco->getEstado(),
					'country'       => 'BR',
					'complement'    => $endereco->getComplemento()
				)
			);

			$vencimento = isset($dados['boleto_vencimento']) ? DateTime::createFromFormat('d/m/Y', $dados['boleto_vencimento']) : false;

			if ($vencimento === FALSE) {
				$vencimento = (new DateTime())->add(new DateInterval('P7D'));
			}

			$diferenca = (new DateTime())->diff($vencimento, true);
			$dias = $diferenca->days;

			$boleto = array(
				'method'                    => 'bank_slip',
				'email'                     => array_shift($emails),
				//'ignore_due_email'		=> true,
				'ignore_canceled_email'		=> true,
				'bank_slip_extra_days'		=> $dias,
				'due_date'                  => $vencimento->format('Y-m-d'),
				'notification_url'          => 'https://www.sistemadestakpublicidade.com.br/webhook-boleto.php',
				'items'                     => array($descricao),
				'custom_variables'          => array('sacado_id' => $sacado->getSacadoId(), 'acompanhamento_id' => $acomp->getId()),
				'payer'                     => $cliente,
				'external_reference'		=> $acomp->getId()
			);

			if (sizeof($emails) > 0) {
				$boleto['cc_emails'] = implode(";", $emails);
			}

			$retorno = Iugu_Charge::create($boleto);

			if ($retorno->success) {
				$Boleto = new Boleto();
				$Boleto->setAcompanhamentoId($acomp->getId());
				$Boleto->setSacadoId($sacado->getSacadoId());
				$Boleto->setIuguValor($custo->getValorFinal());
				$Boleto->setIuguInvoice($retorno->invoice_id);
				$Boleto->setIuguUrl($retorno->url);
				$Boleto->setIuguBoleto($retorno->pdf);
				$Boleto->setIuguVencimento($retorno->due_date);
				$Boleto->setIuguStatus('pending');
				$Boleto->setIuguRequest(json_encode($boleto));

				$usuario = new Usuario();
				$usuario->setId($dados['user_id']);

				$Boleto->setUsuario($usuario);

				$msg = self::insert($Boleto);
			} else {
				$campos = array(
					"payer.cpf_cnpj" => "CPF/CNPJ",
					"payer.name" => "Sacado",
					"payer.email" => "E-mail",
					"payer.address.street" => "Logradouro",
					"payer.address.number" => "Número",
					"payer.address.district" => "Bairro",
					"payer.address.city" => "Cidade",
					"payer.address.state" => "Estado",
					"payer.address.zip_code" => "CEP",
				);

				foreach ($retorno->errors as $key => $val) {
					if (key_exists($key, $campos) !== false) {
						$msg .= "- <i>O campo <u>" . $campos[$key] . "</u> " . implode(",", $val) . "</i><br>";
					} else {
						$msg .= "- <i>O campo <u> " . $key . "</u> " . implode(",", $val) . "</i><br>";
					}
				}
			}
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}
		return $msg;
	}
}
