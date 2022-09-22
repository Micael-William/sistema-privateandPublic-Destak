<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("models/ProcessoModel.php");
require_once("classes/Acompanhamento.class.php");
require_once("models/ProcessoEntradaModel.php");
require_once("models/CustoAcompanhamentoModel.php");
require_once("models/SacadoAcompanhamentoModel.php");
require_once("models/AcompanhamentoStatusModel.php");

class AcompanhamentoModel extends PersistModelAbstract
{

	public static function lista(
		$status = null,
		$substatus = null,
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

		$sql = " SELECT {{campos}}
					 FROM processo_entrada pe					 
					 INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='A'	
					 INNER JOIN proposta p ON p.processo_ID=pr.id
					 INNER JOIN acompanhamento ac ON ac.proposta_ID=p.id
					 INNER JOIN acompanhamento_status ast ON ac.status_acompanhamento=ast.codigo 
					 INNER JOIN advogado adv ON pr.advogado_ID=adv.id	
					 LEFT JOIN secretaria s ON pr.secretaria_id=s.id ";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "ac.status_acompanhamento=:status ";
		}

		if (!DataValidator::isEmpty($substatus)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "ac.substatus_acompanhamento=:substatus ";
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
				$sql .= " pr.secretaria_ID=:sec_id{$i} OR ";
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
			$sql .= " pr.requerente LIKE _utf8 :requerente_nome COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($requerido_nome)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pr.requerido LIKE _utf8 :requerido_nome COLLATE utf8_unicode_ci ";
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
				array_push($order, 'ac.status_acompanhamento' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 4:
				array_push($order, 'pe.estado' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			default:
				array_push($order, 'p.data_aceite DESC');
				array_push($order, 'p.id DESC');
				break;
		}

		$sql .= implode(",", $order);

		$sqlbase = $sql;

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$acompanhamentos = array();
		$acompModel = new AcompanhamentoModel();

		$query = $acompModel->getDB()->prepare("SET @@session.group_concat_max_len = 10000;");
		$query->execute();

		$campos = " ac.id, ac.status_acompanhamento, ast.nome_status, pr.data_entrada, pr.id as processo_id, pr.requerente, pr.requerido, pr.advogado_ID,
					 pr.secretaria_ID, pe.numero, pe.data_processo, pe.estado, adv.nome_advogado, p.data_aceite,
					(SELECT 
					group_concat(concat(obs.data_entrada,'|:|',obs.mensagem) SEPARATOR '|*|') 
					FROM acompanhamento_obs obs 
					WHERE obs.acompanhamento_id = ac.id AND deleted='N'
					ORDER BY obs.data_entrada DESC) as observacoes ";

		$query = $acompModel->getDB()->prepare(str_replace('{{campos}}', $campos, $sql));
		$query_num_linhas = $acompModel->getDB()->prepare(str_replace('{{campos}}', ' COUNT(*) as items ', $sqlbase));

		if (!DataValidator::isEmpty($status)) {
			$query->bindValue(':status', $status, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':status', $status, PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($substatus)) {
			$query->bindValue(':substatus', $substatus, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':substatus', $substatus, PDO::PARAM_STR);
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

		while ($linha = $query->fetchObject()) {
			$arrObs = array();
			$acompanhamento = new Acompanhamento();
			$acompanhamento->setId($linha->id);
			$acompanhamento->setStatus($linha->status_acompanhamento);
			$acompanhamento->setStatusDesc($linha->nome_status);
			$acompanhamento->setObservacoesAcompanhamento(ObservacaoModel::ultimaObs($acompanhamento, $acompModel->getDB()));

			$tmpObs = explode('|*|', $linha->observacoes);
			if (is_array($tmpObs) && $tmpObs[0] != '') {
				foreach ($tmpObs as $obs) {
					$arrReg = explode("|:|", $obs);
					if (is_array($arrReg) && isset($arrReg[1])) {
						$arrObs[$arrReg[0]] = $arrReg[1];
					}
				}
				krsort($arrObs);
			}
			$acompanhamento->setDescObs($arrObs);

			$proposta = new Proposta();
			$proposta->setDataAceite($linha->data_aceite);

			$processo = new Processo();
			$processo->setId($linha->processo_id);
			$processo->setDataEntrada($linha->data_entrada);
			$processo->setRequerente($linha->requerente);
			$processo->setRequerido($linha->requerido);

			if (!DataValidator::isEmpty($linha->secretaria_ID))
				$processo->setSecretaria(SecretariaModel::getBy($linha->secretaria_ID, $acompModel->getDB()));

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

			$acompanhamentos[] = $acompanhamento;
		}

		return array('acompanhamentos' => $acompanhamentos, 'totalLinhas' => $totalLinhas);
	}

	public static function getById($acompanhamento_id, $db = null, $origem = null)
	{

		$msg = null;
		$acompanhamento = null;

		if (is_null($db)) {
			$acompModel = new AcompanhamentoModel();
			$db = $acompModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento_id))
			throw new UserException('Acompanhamento: O Acompanhamento deve ser identificado.');

		$sql = " SELECT ac.*, u.nome_usuario as resp_envio, us.nome_usuario as resp_aceite,
					 p.requerente, p.requerido, p.data_entrada as data_entrada_processo, p.entrada_ID, p.advogado_ID, p.secretaria_ID, p.jornal_ID,
					 prop.data_envio, prop.data_aceite, 
					 (SELECT MAX(ab.id) FROM acompanhamento_boleto ab WHERE ab.acompanhamento_ID=ac.id AND ab.iugu_status IN('paid', 'overdue', 'pending')) as boleto
					 FROM acompanhamento ac
					 INNER JOIN proposta prop ON ac.proposta_ID=prop.id
					 INNER JOIN processo p ON prop.processo_ID=p.id
					 INNER JOIN processo_entrada pe ON p.entrada_ID=pe.id
					 LEFT JOIN proposta_custo pc ON pc.proposta_ID=prop.id
					 LEFT JOIN usuario u ON prop.usuario_ID_envio=u.id
					 LEFT JOIN usuario us ON prop.usuario_ID_aceite=us.id
					 WHERE ac.id=:acompanhamento_id
					";

		$query = $db->prepare($sql);
		$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$acompanhamento = new Acompanhamento();
		$acompanhamento->setId($linha->id);

		$acompanhamento->setBoletoEmAberto($linha->boleto);

		$status_acompanhamento = AcompanhamentoStatusModel::getByCodigo($linha->status_acompanhamento, $db);
		$acompanhamento->setStatus($status_acompanhamento->getCodigo());
		$acompanhamento->setStatusDesc($status_acompanhamento->getStatus());

		if (isset($linha->substatus_acompanhamento) && $linha->substatus_acompanhamento != null) {
			$substatus_acompanhamento = AcompanhamentoStatusModel::getById($linha->substatus_acompanhamento, $db);
			$acompanhamento->setSubStatus($substatus_acompanhamento->getId());
			$acompanhamento->setSubStatusDesc($substatus_acompanhamento->getStatus());
		}

		//envio de petições
		$acompanhamento->setEnvioAutorizacao($linha->envio_autorizacao);
		$acompanhamento->setEnvioComprovante($linha->envio_comprovante);
		$acompanhamento->setEnvioGuia($linha->envio_guia);
		$acompanhamento->setEnvioMinuta($linha->envio_minuta);

		//geração das petições
		$acompanhamento->setGeraAutorizacao($linha->gera_autorizacao);
		$acompanhamento->setGeraComprovante($linha->gera_comprovante);
		$acompanhamento->setGeraGuia($linha->gera_guia);
		$acompanhamento->setGeraMinuta($linha->gera_minuta);

		//observações do advogado
		$acompanhamento->setObservacoes(ObservacaoModel::getObservacoes($acompanhamento, $db, 'acomp-adv-obs'));
		//observações do acompanhamento
		$acompanhamento->setObservacoesAcompanhamento(ObservacaoModel::getObservacoes($acompanhamento, $db, 'acomp-obs'));
		//observações do financeiro
		$acompanhamento->setObservacoesFinanceiro(ObservacaoModel::getObservacoes($acompanhamento, $db, 'acomp-fin-obs'));

		$acompanhamento->setSacado(SacadoAcompanhamentoModel::getById($acompanhamento->getId(), $db));

		//custo do acompanhamento
		$acompanhamento->setCusto(CustoAcompanhamentoModel::getById($acompanhamento->getId(), $db));

		if (!DataValidator::isEmpty($linha->proposta_ID)) {
			$acompanhamento->setProposta(PropostaModel::getById($linha->proposta_ID, $db, $origem));
		}

		return $acompanhamento;
	}


	//*******************************//

	//insere vindo de proposta
	public static function insert($proposta, $db = null)
	{

		if (is_null($db)) {
			$acompModel = new AcompanhamentoModel();
			$db = $acompModel->getDB();
		}

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Insert Acompanhamento: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Insert Acompanhamento: A Proposta deve ser identificada.');

		$sql = " INSERT INTO acompanhamento (proposta_ID, data_entrada, status_acompanhamento, substatus_acompanhamento) 
					VALUES (:proposta_id, :data_entrada, :status, :substatus)";

		$query = $db->prepare($sql);
		$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$query->bindValue(':status', 'E', PDO::PARAM_STR);
		$query->bindValue(':substatus', $tmpVal = NULL, PDO::PARAM_INT);
		$query->execute();

		//insere custos da proposta em acompanhamento
		$acomp = new Acompanhamento();
		$acomp->setId($db->lastInsertId());
		$acomp->setProposta($proposta);

		$custos = $proposta->getCustos();
		if (!DataValidator::isEmpty($custos)) {
			CustoAcompanhamentoModel::insert($acomp, $db);
		}

		//observações da proposta vão pra acompanhamento
		ObservacaoModel::saveObservacaoFromProposta($proposta, $acomp->getId(), $db);

		return $acomp->getId();
	}

	public static function update($acompanhamento)
	{
		$msg = null;

		try {
			$acompModel = new AcompanhamentoModel();
			//$acompModel->getDB()->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
			//$acompModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($acompanhamento))
				throw new UserException('Update Acompanhamento: O Acompanhamento deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getId()))
				throw new UserException('Update Acompanhamento: O Acompanhamento deve ser identificado.');

			$tmpDate = ($acompanhamento->getStatus() == 'C') ? date("Y-m-d H:i:s") : NULL;

			$sql = "UPDATE acompanhamento SET
								status_acompanhamento=:status,
								substatus_acompanhamento=:substatus,
								data_conclusao=:data_conclusao
					WHERE id=:acompanhamento_id
								";

			$query = $acompModel->getDB()->prepare($sql);
			$query->bindValue(':status', $acompanhamento->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':substatus', $acompanhamento->getSubStatus(), PDO::PARAM_INT);
			$query->bindValue(':data_conclusao', $tmpDate, PDO::PARAM_STR);
			$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);
			$query->execute();

			//observações do acompanhamento				
			ObservacaoModel::saveObservacao($acompanhamento, $acompModel->getDB(), 'acomp-obs');

			$acomp_cadastrado = self::getById($acompanhamento->getId(), $acompModel->getDB());

			ProcessoEntradaModel::update($acompanhamento->getProposta()->getProcesso()->getEntrada(), $acompModel->getDB());
			ProcessoModel::updateFromAcompanhamento($acompanhamento->getProposta()->getProcesso(), $acompModel->getDB());
			//PropostaModel::updateFromAcompanhamento( $acompanhamento, $acompModel->getDB());
			CustoAcompanhamentoModel::update($acompanhamento, $acompModel->getDB());

			//se insertou um advogado
			if (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()->getId())) {

				//se já existia um advogado cadastrado
				if (!DataValidator::isEmpty($acomp_cadastrado) && !DataValidator::isEmpty($acomp_cadastrado->getProposta()->getProcesso()->getAdvogado())) {

					//se o id do insertado for diferente do cadastrado...
					if ($acomp_cadastrado->getProposta()->getProcesso()->getAdvogado()->getId() != $acompanhamento->getProposta()->getProcesso()->getAdvogado()->getId()) {
						ObservacaoModel::excluiObservacoesFromAcompanhamento($acompanhamento->getId(), $acompModel->getDB());
						ObservacaoModel::saveObservacaoFromAdvogadoToAcomp($acompanhamento->getProposta()->getProcesso()->getAdvogado(), $acompanhamento, $acompModel->getDB());
					}
				}
			}

			//Sacado
			SacadoAcompanhamentoModel::insertOrUpdate($acompanhamento->getSacado(), $acompModel->getDB());

			//cadastra/altera demais observações
			if (!DataValidator::isEmpty($acompanhamento->getObservacoes()))
				ObservacaoModel::saveObservacao($acompanhamento, $acompModel->getDB(), 'acomp-adv-obs');

			if (!DataValidator::isEmpty($acompanhamento->getObservacoesFinanceiro()))
				ObservacaoModel::saveObservacao($acompanhamento, $acompModel->getDB(), 'acomp-fin-obs');

			//$acompModel->getDB()->commit();

		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			//$acompModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function listaBySacado($sacado = null, $db = null)
	{
		$acompanhamentos = array();

		return $acompanhamentos;
	}

	//acompanhamentos vinculados ao advogado
	public static function listaByAdvogado($advogado = null, $db = null)
	{

		$acompanhamentos = array();

		if (is_null($db)) {
			$acompanhamentoModel = new AcompanhamentoModel();
			$db = $acompanhamentoModel->getDB();
		}

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Lista Acompanhamentos: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Lista Acompanhamentos: O Advogado deve ser identificado.');

		$sql = " SELECT acomp.ID, 
						acomp.status_acompanhamento, 
						acost.nome_status AS status_desc, 
						acomp.substatus_acompanhamento, 
						asbst.nome_status AS substatus_desc, 
						acomp.proposta_ID, 
						pe.numero, 
						prop.processo_ID
					FROM processo_entrada pe
	INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='A'
	INNER JOIN proposta prop ON prop.processo_ID=pr.id		
	INNER JOIN acompanhamento acomp ON acomp.proposta_ID=prop.ID
						INNER JOIN acompanhamento_status acost ON acomp.status_acompanhamento = acost.codigo
						LEFT JOIN acompanhamento_status asbst ON acomp.substatus_acompanhamento = asbst.id
	INNER JOIN advogado adv ON pr.advogado_ID=adv.id	
					WHERE pr.advogado_ID=:advogado_id
					ORDER BY acomp.ID DESC";

		$query = $db->prepare($sql);
		$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$acompanhamento = new Acompanhamento();
			$acompanhamento->setId($linha->ID);
			$acompanhamento->setStatus($linha->status_acompanhamento);
			$acompanhamento->setStatusDesc($linha->status_desc);

			if (isset($linha->substatus_acompanhamento) && $linha->substatus_acompanhamento != NULL) {
				$acompanhamento->setSubStatus($linha->substatus_acompanhamento);
				$acompanhamento->setSubStatusDesc($linha->substatus_desc);
			}

			$proposta = new Proposta();
			$proposta->setId($linha->proposta_ID);

			$processo = new Processo();
			$processo->setId($linha->processo_ID);

			$entrada = new ProcessoEntrada();
			$entrada->setNumero($linha->numero);

			$acompanhamento->setProposta($proposta);
			$acompanhamento->getProposta()->setProcesso($processo);
			$acompanhamento->getProposta()->getProcesso()->setEntrada($entrada);

			$acompanhamentos[] = $acompanhamento;
		}

		return $acompanhamentos;
	}

	//**********//

	//Envio da obs do Acompanhamento
	public static function enviaEmail($acompanhamento, $usuario_id = 0, $usuario_nome = null, $observacao)
	{

		$msg = null;
		$acompModel = new AcompanhamentoModel();

		if (DataValidator::isEmpty($acompanhamento))
			throw new UserException('Envia Observação: O Acompanhamento deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getId()))
			throw new UserException('Envia Observação: O Acompanhamento deve ser identificado.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()))
			throw new UserException('Envia Observação: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()->getId()))
			throw new UserException('Envia Observação: A Proposta deve ser identificada.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()))
			throw new UserException('Envia Observação: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getId()))
			throw new UserException('Envia Observação: O Processo deve ser identificado.');

		if (DataValidator::isEmpty($usuario_id))
			throw new UserException('Envia Observação: O Usuário deve ser identificado.');

		if (DataValidator::isEmpty($usuario_nome))
			throw new UserException('Envia Observação: O nome do Usuário deve ser fornecido.');

		if (DataValidator::isEmpty($observacao))
			throw new UserException('Envia Observação: A Observação deve ser fornecida.');

		if (DataValidator::isEmpty($observacao->getId()))
			throw new UserException('Envia Observação: A Observação deve ser identificada.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()))
			throw new UserException('Envia Observação: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()->getEmails()))
			throw new UserException('Envia Observação: O Advogado deve ter, no mínimo, 1 email.');

		$obs = ObservacaoModel::getObservacaoById($observacao, 'acompanhamento_obs', $acompModel->getDB());

		$mensagem = '
			
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8">
	<title>Proposta para o Advogado</title>
</head>
<body>

<table width="700" align="center" cellpadding="0" cellspacing="0">	
	<tr>
		<td align="left" width="30%" valign="middle"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" alt=""></td>
		<td align="right" width="50%" valign="middle">
		<p style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME.</strong>
			<br />
			CNPJ: 08.324.897/0001-10
			<br />
			' . parent::ENDERECO_PADRAO . '/SP
			<br />
			Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			e-mail: destak@destakpublicidade.com.br
		</p>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<p>
		<font face="Arial, sans-serif" size="3">
		São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '
		<br />
		<br />
		Ilmo.(a) Sr.(a) Dr.(a): <strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()) ? $acompanhamento->getProposta()->getProcesso()->getAdvogado()->getNome() : '') . '</strong> 	
		</font>
		</p>
		</td>
	</tr>


</table>
<!-- intro -->

<table width="700" align="center" cellpadding="0" cellspacing="0">

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Requerente: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></font></td>
	</tr>

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerido: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></font></td>
	</tr>

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Vara: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong></font></td>
	</tr>

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Processo: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : '') . '</strong> </font></td>
	</tr>

	<tr height="25"><td colspan="2">&nbsp;  </td></tr>

	<tr height="25" style="font: 15px arial, sans-serif; text-align: left;">

	<td colspan="2">
		<p>
		Encaminhamos informações referentes ao processo citado:
		<br />
		<br />
		</p>		
	</td>		
	</tr>


</table>
<!-- dados -->

<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr>
		<td>
			<p><strong>' . (!DataValidator::isEmpty($obs) && !DataValidator::isEmpty($obs->getMensagem()) ? $obs->getMensagem() : '') . '</strong>
			</p>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

</table>

</body>
</html>';

		require_once("lib/Mail.php");
		$mail = Mail::Init();

		// Define os destinatário(s)
		$emails_advogado = explode(';', $observacao->getEmailDestino());
		if (!DataValidator::isEmpty($emails_advogado)) {
			foreach ($emails_advogado as $email) {
				$mail->AddAddress($email, $email);
			}
		}

		// Define a mensagem (Texto e Assunto)
		$mail->Subject  = "Observação do Processo nº " . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : '');
		$mail->Body = $mensagem;

		$mail->SetFrom('acompanhamento@sistemadestakpublicidade.com.br', 'Destak Publicidade');

		$enviado = $mail->Send();

		// Limpa os destinatários e os anexos
		$mail->ClearAllRecipients();
		$mail->ClearAttachments();

		if ($enviado) {

			$sql = " UPDATE acompanhamento_obs SET status=:status, data_envio=:data_envio, usuario_ID_envio=:usuario_id_envio WHERE id=:observacao_id; ";
			$propostaModel = new PropostaModel();
			$query = $propostaModel->getDB()->prepare($sql);
			$query->bindValue(':data_envio', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->bindValue(':status', 'E', PDO::PARAM_STR);
			$query->bindValue(':usuario_id_envio', $usuario_id, PDO::PARAM_INT);
			$query->bindValue(':observacao_id', $observacao->getId(), PDO::PARAM_INT);

			$query->execute();
		} else {
			$msg =  $mail->ErrorInfo;
		}
		//--Envio do email por SMTP		

		return $msg;
	}

	public static function enviaPeticao($acompanhamento, $tipo = null)
	{

		$msg = null;

		try {

			if (DataValidator::isEmpty($acompanhamento))
				throw new UserException('Petições: O Acompanhamento deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getId()))
				throw new UserException('Petições: O Acompanhamento deve ser identificado.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()))
				throw new UserException('Petições: A Proposta deve ser fornecida.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getId()))
				throw new UserException('Petições: A Proposta deve ser identificada.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()))
				throw new UserException('PEtições: O Processo deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getId()))
				throw new UserException('Petições: O Processo deve ser identificado.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()))
				throw new UserException('Petições: A Entrada deve ser fornecida.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getId()))
				throw new UserException('Petições: A Entrada deve ser identificada.');

			if (DataValidator::isEmpty($tipo))
				throw new UserException('Petições: O tipo deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()))
				throw new UserException('Petições: O Advogado deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAdvogado()->getEmails()))
				throw new UserException('Petições: O Advogado deve ter, no mínimo, 1 email.');

			$html = null;
			$assunto = null;

			if ($tipo == 'autorizacao') {
				$assunto = 'Petição para Autorização - Processo nº ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "");
				$html = '
				
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<strong>O Requerente</strong>, por seu advogado infra assinado, nos autos do processo em epígrafe, que move em contra o <strong>Requerido</strong>, vem respeitosamente à presença de V. Excelência, <strong><u>AUTORIZAR</u></strong> Danilo Raymundo RG. 32.521.930-8, Josefa Maria Gonçalves RG. 22.740.237-6, Vinicius Silva Oliveira RG. 52.218.764-X, Julio César Pelicelli dos Santos RG. 36.270.269-X e o Advogado Maurício Malanga OAB/SP 229.996, representantes da <strong><u>AGÊNCIA DESTAK DE PUBLICIDADE LTDA</u></strong>, com escritório à ' . parent::ENDERECO_PETICAO . ', Telefone/WhatsApp: ' . parent::TELEFONE_PADRAO . ', para firmar recibo por ocasião da retirada do edital a ser expedido, bem como peticionar nos autos em relação as providências inerentes ao edital e providenciar o que for necessário para a publicação e posterior juntada dos comprovantes, tendo esta o valor de SUBSTABELECIMENTO, especificamente para os fins ora declinados. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
<strong>OAB/SP</strong>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //autorizacao

			elseif ($tipo == 'comprovante') {
				$assunto = 'Publicação Edital - Processo nº ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "");
				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
</head>
<body>

<div class="texto-peticao" style="text-align:justify">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Excelência, requerer a juntada dos <strong>COMPROVANTES DE PUBLICAÇÕES DO EDITAL</strong>, nos autos do processo supra, para os devidos fins de direito.
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
<strong>OAB/SP</strong>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>
</div>

</body>
</html>';
			} //comprovante

			elseif ($tipo == 'guia') {
				$assunto = 'Recolhimento de Guia - Processo nº ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "");
				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Excelência, requerer a juntada aos autos da <strong>GUIA - FEDTJ (doc. anexo)</strong> relativa à quantidade de caracteres do edital, requerendo, portanto, <em><strong>QUE A SERVENTIA PROVIDENCIE A PUBLICAÇÃO DO EDITAL NO DIÁRIO DA JUSTIÇA ELETRÔNICO - DJE</strong></em>, para que, posteriormente, providenciemos as demais publicações em jornal particular. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
<strong>OAB/SP</strong>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //guia

			elseif ($tipo == 'minuta') {
				$assunto = 'Minuta do Edital - Processo nº ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "");
				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Exa., nos autos do processo em epígrafe, requerer a juntada da <strong>MINUTA DE EDITAL</strong>.
<br>
<br>
Após a conferência de referida minuta pela serventia, aguardaremos a publicação/intimação do valor das custas para recolhimento da guia destinada a publicação do edital no DJE. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
<strong>OAB/SP</strong>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //minuta			

			require_once("lib/Mail.php");
			$mail = Mail::Init();

			// Define os destinatario(s)
			$emails_advogado = $acompanhamento->getProposta()->getProcesso()->getAdvogado()->getEmails();
			if (!DataValidator::isEmpty($emails_advogado)) {
				foreach ($emails_advogado as $email) {
					$mail->AddAddress($email->getEmailEndereco(), $email->getEmailEndereco());
				}
			}
			
			// Define a mensagem (Texto e Assunto)
			$mail->Subject  = $assunto;
			$mail->Body = $html;

			$mail->SetFrom('acompanhamento@sistemadestakpublicidade.com.br', 'Destak Publicidade');

			$enviado = $mail->Send();

			// Limpa os destinatarios e os anexos
			$mail->ClearAllRecipients();
			$mail->ClearAttachments();

			if ($enviado) {

				if ($tipo == 'autorizacao') {
					$sql = " UPDATE acompanhamento SET envio_autorizacao=:status WHERE id=:acompanhamento_id; ";
					$acompanhamento->setEnvioAutorizacao('S');
				} else if ($tipo == 'comprovante') {
					$sql = " UPDATE acompanhamento SET envio_comprovante=:status WHERE id=:acompanhamento_id; ";
					$acompanhamento->setEnvioComprovante('S');
				} else if ($tipo == 'guia') {
					$sql = " UPDATE acompanhamento SET envio_guia=:status WHERE id=:acompanhamento_id; ";
					$acompanhamento->setEnvioGuia('S');
				} else if ($tipo == 'minuta') {
					$sql = " UPDATE acompanhamento SET envio_minuta=:status WHERE id=:acompanhamento_id; ";
					$acompanhamento->setEnvioMinuta('S');
				}

				$acompModel = new AcompanhamentoModel();
				$query = $acompModel->getDB()->prepare($sql);

				$query->bindValue(':status', 'S', PDO::PARAM_STR);
				$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);

				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function geraPeticao($acompanhamento, $tipo = null)
	{


		try {

			if (DataValidator::isEmpty($acompanhamento))
				throw new UserException('Petições: O Acompanhamento deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getId()))
				throw new UserException('Petições: O Acompanhamento deve ser identificado.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()))
				throw new UserException('Petições: A Proposta deve ser fornecida.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getId()))
				throw new UserException('Petições: A Proposta deve ser identificada.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()))
				throw new UserException('Petições: O Processo deve ser fornecido.');

			if (DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getId()))
				throw new UserException('Petições: O Processo deve ser identificado.');

			if (DataValidator::isEmpty($tipo))
				throw new UserException('Petições: O tipo deve ser fornecido.');

			$html = null;

			$basePath = $_SERVER["DOCUMENT_ROOT"];

			if ($tipo == 'autorizacao') {

				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify;margin-left:25px;">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="' . $basePath . '/img/logo-email.jpg" width="200" /></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<strong>O Requerente</strong>, por seu advogado infra assinado, nos autos do processo em epígrafe, que move em contra o <strong>Requerido</strong>, vem respeitosamente à presença de V. Excelência, <strong><u>AUTORIZAR</u></strong> Danilo Raymundo RG. 32.521.930-8, Josefa Maria Gonçalves RG. 22.740.237-6, Vinicius Silva Oliveira RG. 52.218.764-X, Julio César Pelicelli dos Santos RG. 36.270.269-X e o Advogado Maurício Malanga OAB/SP 229.996, representantes da <strong><u>AGÊNCIA DESTAK DE PUBLICIDADE LTDA</u></strong>, com escritório à ' . parent::ENDERECO_PETICAO . ', Telefone/WhatsApp: ' . parent::TELEFONE_PADRAO . ', para firmar recibo por ocasião da retirada do edital a ser expedido, bem como peticionar nos autos em relação as providências inerentes ao edital e providenciar o que for necessário para a publicação e posterior juntada dos comprovantes, tendo esta o valor de SUBSTABELECIMENTO, especificamente para os fins ora declinados. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
___________________________________________
<br>
<br>
<br>
<strong>OAB/SP:</strong>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="' . $basePath . '/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //autorizacao

			elseif ($tipo == 'comprovante') {

				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
</head>
<body>

<div class="texto-peticao" style="text-align:justify;margin-left:25px;">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="' . $basePath . '/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Excelência, requerer a juntada dos <strong>COMPROVANTES DE PUBLICAÇÕES DO EDITAL</strong>, nos autos do processo supra, para os devidos fins de direito.
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
___________________________________________
<br>
<br>
<br>
<strong>OAB/SP:</strong>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="' . $basePath . '/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>
</div>

</body>
</html>';
			} //comprovante

			elseif ($tipo == 'guia') {

				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify;margin-left:25px;">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="' . $basePath . '/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Excelência, requerer a juntada aos autos da <strong>GUIA - FEDTJ (doc. anexo)</strong> relativa à quantidade de caracteres do edital, requerendo, portanto, <em><strong>QUE A SERVENTIA PROVIDENCIE A PUBLICAÇÃO DO EDITAL NO DIÁRIO DA JUSTIÇA ELETRÔNICO - DJE</strong></em>, para que, posteriormente, providenciemos as demais publicações em jornal particular. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
___________________________________________
<br>
<br>
<br>
<strong>OAB/SP:</strong>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="' . $basePath . '/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //guia

			elseif ($tipo == 'minuta') {

				$html = '
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">	
</head>
<body>
<div class="texto-peticao" style="text-align:justify;margin-left:25px;">
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0" style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
	<tr>
		<td align="center" width="100%" valign="top"><img src="' . $basePath . '/img/logo-email.jpg" width="200" alt="Destak Publicidade"></td>
	</tr>
	<tr>
	<td height="40">&nbsp;</td>
	</tr>	
	<tr>
		<td colspan="2">
		<strong>EXMO (A). SR (A). DR (A). JUIZ (A) DE DIREITO DA ' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getSecretaria()) ? $acompanhamento->getProposta()->getProcesso()->getSecretaria()->getNome() : "") . '</strong>
		</td>
	</tr>
	<tr>
	<td>&nbsp;<br><br><br><br></td>
	</tr>

</table>
<table width="700" border="0" align="center" style="font: 15px arial, sans-serif; color: #000; line-height: 20px;">
<tr>
<td width="14%">Requerente:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerente()) ? $acompanhamento->getProposta()->getProcesso()->getRequerente() : "") . '</strong></td>
</tr>
<tr>
<td>Requerido:</td>' .
					'<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getRequerido()) ? $acompanhamento->getProposta()->getProcesso()->getRequerido() : "") . '</strong></td>
</tr>
';

				if ($acompanhamento->getProposta()->getProcesso()->getEntrada()->getEstado() == 'SP' && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getAcao())) {
					$html .= '
<tr><td>Ação:</td>
<td><strong>' . $acompanhamento->getProposta()->getProcesso()->getAcao() . '</strong></td><tr>';
				}

				$html .= '
<tr><td>Processo:</td>
<td><strong>' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '</strong></td>
</tr>
<tr>
<td colspan="2" style="text-align:justify">
<br>
<br>
<br>
<br>
Pela Presente, o subscritor desta, vem respeitosamente à presença de V. Exa., nos autos do processo em epígrafe, requerer a juntada da <strong>MINUTA DE EDITAL</strong>.
<br>
<br>
Após a conferência de referida minuta pela serventia, aguardaremos a publicação/intimação do valor das custas para recolhimento da guia destinada a publicação do edital no DJE. 
</td>
</tr>
<tr>
<td width="14%">&nbsp;</td>
<td>
<br>
<br>
<br>
<div class="assinatura">
Nestes Termos,
<br>
P. Deferimento.
<br>
São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '.
<br>
<br>
<br>
<br>
___________________________________________
<br>
<br>
<br>
<strong>OAB/SP:</strong>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</div>
</td>
</tr>
</table>
<table width="700" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" width="50%" valign="middle">
		<p style="font: 11px arial, sans-serif; color: #000; line-height: 15px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME</strong>
			<br />
			<strong>CNPJ: 08.324.897/0001-10</strong>
			<br />
			<strong>' . parent::ENDERECO_PADRAO . '</strong>
			<br />
			<strong>E-mail: destak@destakpublicidade.com.br / Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '</strong>&nbsp;<img src="' . $basePath . '/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			<strong>site: www.destakpublicidade.com.br</strong>
		</p>
		</td>
	</tr>	
</table>		
</div>
</body>
</html>';
			} //minuta	

			require_once __DIR__ . '/../vendor/autoload.php';
			$config = array(
				'mode' => 'utf-8',
				'format' => 'A4-P',
				'margin_left' => 15,
				'margin_right' => 15,
				'margin_top' => 16,
				'margin_bottom' => 16,
				'margin_header' => 9,
				'margin_footer' => 9
			);

			$mpdf = new \Mpdf\Mpdf($config);

			//$mpdf->imageVars['logodestak'] = file_get_contents($basePath . "/img/logo-email.jpg");

			$mpdf->SetDisplayMode('fullpage');
			$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

			$mpdf->WriteHTML($html, 2);
			$mpdf->Output('Petição-' . $tipo . '-Processo-' . (!DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : "") . '.pdf', 'D');



			if ($tipo == 'autorizacao') {
				$sql = " UPDATE acompanhamento SET gera_autorizacao=:status WHERE id=:acompanhamento_id; ";
				$acompanhamento->setGeraAutorizacao('S');
			} else if ($tipo == 'comprovante') {
				$sql = " UPDATE acompanhamento SET gera_comprovante=:status WHERE id=:acompanhamento_id; ";
				$acompanhamento->setGeraComprovante('S');
			} else if ($tipo == 'guia') {
				$sql = " UPDATE acompanhamento SET gera_guia=:status WHERE id=:acompanhamento_id; ";
				$acompanhamento->setGeraGuia('S');
			} else if ($tipo == 'minuta') {
				$sql = " UPDATE acompanhamento SET gera_minuta=:status WHERE id=:acompanhamento_id; ";
				$acompanhamento->setGeraMinuta('S');
			}

			$acompModel = new AcompanhamentoModel();
			$query = $acompModel->getDB()->prepare($sql);

			$query->bindValue(':status', 'S', PDO::PARAM_STR);
			$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}
	}

	public static function exclui($acompanhamento_id, $processo_id)
	{

		$msg = null;
		$acompanhamentoModel = new ProcessoModel();

		try {
			if (DataValidator::isEmpty($processo_id))
				throw new UserException('Exclui Acompanhamento: O Processo deve ser identificado.');
			if (DataValidator::isEmpty($acompanhamento_id))
				throw new UserException('Exclui Acompanhamento: O Acompanhamento deve ser identificado.');

			$sql = ' DELETE FROM acompanhamento_adv_obs WHERE acompanhamento_ID=:acompanhamento_id; ';
			$sql .= ' DELETE FROM acompanhamento_obs WHERE acompanhamento_ID=:acompanhamento_id; ';
			$sql .= ' DELETE FROM acompanhamento_custo WHERE acompanhamento_ID=:acompanhamento_id; ';
			$sql .= ' DELETE FROM acompanhamento WHERE id=:acompanhamento_id; ';
			$sql .= " UPDATE processo SET status_processo='S' WHERE id=:processo_id; ";
			$query = $acompanhamentoModel->getDB()->prepare($sql);
			$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);
			$query->bindValue(':processo_id', $processo_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}
}
