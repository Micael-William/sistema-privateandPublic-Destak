<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Proposta.class.php");
require_once("classes/Processo.class.php");
require_once("classes/ProcessoEntrada.class.php");
require_once("models/ProcessoEntradaModel.php");
require_once("classes/Advogado.class.php");
require_once("models/AlertaModel.php");
require_once("models/ProcessoModel.php");
require_once("models/CustoPropostaModel.php");
require_once("models/UsuarioModel.php");
require_once("models/AcompanhamentoModel.php");

class PropostaModel extends PersistModelAbstract
{

	public static function lista(
		$status = null,
		$advogado_nome = null,
		$secretaria_id = 0,
		$numero_processo = null,
		$requerente_nome = null,
		$requerido_nome = null,
		$proposta_id = 0,
		$estado = null,
		$pagina = 0,
		$qtd_pagina = 0,
		$ordenacao = 0,
		$sentido_ordenacao = "a",
		$pendente = ""
	) {

		$sql = " SELECT SQL_CALC_FOUND_ROWS 
						prop.id, 
						prop.data_entrada, 
						prop.status_proposta, 
						prop.data_envio,
						prop.pendente,
					 	pr.id as processo_id, 
						pr.requerente, 
						pr.requerido, 
						pr.sinalizador, 
						pr.advogado_ID, 
						pr.data_entrada as entrada_processo,
					 	pe.numero, 
						pe.estado, 
						pe.data_processo, 
						adv.nome_advogado,
						(   SELECT
								group_concat(concat(pobs.data_entrada,'|:|',pobs.mensagem) SEPARATOR '|*|') AS obs 
								FROM proposta_obs pobs 
								WHERE pobs.proposta_id = prop.id AND pobs.deleted='N'
						) as observacoes_1, 
                        (    SELECT 
                            	group_concat(concat(aobs.data_entrada,'|:|',aobs.mensagem) SEPARATOR '|*|') AS obs
                               	FROM advogado_obs aobs 
                               	WHERE aobs.advogado_ID = pr.advogado_ID AND aobs.deleted='N' 
                        ) as observacoes_2 
					 FROM processo_entrada pe
					 INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='S'
					 INNER JOIN proposta prop ON prop.processo_ID=pr.id				 
					 LEFT JOIN advogado adv ON pr.advogado_ID=adv.id
					 LEFT JOIN secretaria s ON pr.secretaria_id=s.id 					 					
					";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "prop.status_proposta=:status ";
		}

		if (!DataValidator::isEmpty($pendente)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "prop.pendente=:pendente ";
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
			$sql .= " pr.secretaria_id=:secretaria_id ";
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

		if (!DataValidator::isEmpty($proposta_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " prop.id=:proposta_id ";
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

		if (DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  " prop.status_proposta<>'R' ";
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
				array_push($order, 'prop.status_proposta' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 4:
				array_push($order, 'pe.estado' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			default:
				array_push($order, 'prop.data_envio DESC');
				array_push($order, 'prop.id DESC');
				break;
		}

		$sql .= implode(",", $order);

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		//echo $sql;

		$propostas = array();
		$propostaModel = new PropostaModel();

		$query = $propostaModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pendente))
			$query->bindValue(':pendente', $pendente, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($advogado_nome))
			$query->bindValue(':advogado_nome', "%$advogado_nome%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($secretaria_id))
			$query->bindValue(':secretaria_id', $secretaria_id, PDO::PARAM_INT);

		if (!DataValidator::isEmpty($numero_processo))
			$query->bindValue(':numero_processo', "%$numero_processo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($requerente_nome))
			$query->bindValue(':requerente_nome', "%$requerente_nome%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($requerido_nome))
			$query->bindValue(':requerido_nome', "%$requerido_nome%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($proposta_id))
			$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		$query->execute();

		//*******
		$query_num_linhas = $propostaModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query_num_linhas->execute();

		$num_linhas = $query_num_linhas->fetchObject();
		$totalLinhas = $num_linhas ? $num_linhas->frows : 0;
		//*******

		while ($linha = $query->fetchObject()) {
			$arrObs = array();
			$tmpObs1 = array();
			$tmpObs2 = array();
			$proposta = new Proposta();
			$proposta->setId($linha->id);
			$proposta->setDataEntrada($linha->data_entrada);
			$proposta->setDataEnvio($linha->data_envio);
			$proposta->setPendente($linha->pendente);
			$proposta->setStatus($linha->status_proposta);
			$tmpObs1 = explode('|*|', $linha->observacoes_1);
			$tmpObs2 = explode('|*|', $linha->observacoes_2);
			if (is_array($tmpObs1) && $tmpObs1[0] != '') {
				foreach ($tmpObs1 as $obs) {
					$arrReg = explode("|:|", $obs);
					if (is_array($arrReg) && isset($arrReg[1])) {
						$arrObs[$arrReg[0]] = $arrReg[1];
					}
				}
			}
			if (is_array($tmpObs2) && $tmpObs2[0] != '') {
				foreach ($tmpObs2 as $obs) {
					$arrReg = explode("|:|", $obs);
					if (is_array($arrReg) && isset($arrReg[1])) {
						$arrObs[$arrReg[0]] = $arrReg[1];
					}
				}
			}
			krsort($arrObs);
			$proposta->setDescObs($arrObs);

			$processo = new Processo();
			$processo->setId($linha->processo_id);
			$processo->setRequerente($linha->requerente);
			$processo->setRequerido($linha->requerido);
			$processo->setSinalizador($linha->sinalizador);
			$processo->setDataEntrada($linha->entrada_processo);

			$entrada = new ProcessoEntrada();
			$entrada->setNumero($linha->numero);
			$entrada->setEstado($linha->estado);
			$entrada->setDataProcesso($linha->data_processo);

			if (!DataValidator::isEmpty($linha->advogado_ID)) {
				$advogado = new Advogado();
				$advogado->setNome($linha->nome_advogado);
				$processo->setAdvogado($advogado);
			}

			$processo->setAlertas(AlertaModel::lista($processo, $propostaModel->getDB()));

			$proposta->setProcesso($processo);
			$proposta->getProcesso()->setEntrada($entrada);

			$propostas[] = $proposta;
		}

		return array('propostas' => $propostas, 'totalLinhas' => $totalLinhas);
	}

	public static function listaByAdvogado($advogado = null, $db = null)
	{

		$propostas = array();

		if (is_null($db)) {
			$propostaModel = new PropostaModel();
			$db = $propostaModel->getDB();
		}

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Lista Propostas: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Lista Propostas: O Advogado deve ser identificado.');

		$sql = " SELECT prop.*, pe.numero
					 FROM processo_entrada pe
					 INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='S'
					 INNER JOIN proposta prop ON prop.processo_ID=pr.id				 
					 INNER JOIN advogado adv ON pr.advogado_ID=adv.id	
					 WHERE pr.advogado_ID=:advogado_id
					 ORDER BY prop.id DESC
					";

		$query = $db->prepare($sql);
		$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$proposta = new Proposta();
			$proposta->setId($linha->id);
			$proposta->setStatus($linha->status_proposta);

			$processo = new Processo();
			$processo->setId($linha->processo_ID);

			$entrada = new ProcessoEntrada();
			$entrada->setNumero($linha->numero);

			$proposta->setProcesso($processo);
			$proposta->getProcesso()->setEntrada($entrada);

			$propostas[] = $proposta;
		}

		return $propostas;
	}

	public static function setPropostaPendencia($proposta_id, $novo_estado)
	{

		$msg = null;

		try {
			$propostaModel = new PropostaModel();
			$db = $propostaModel->getDB();
			$sql = " UPDATE proposta SET pendente = :pendente WHERE id = :proposta_id; ";
			$propostaModel->getDB()->beginTransaction();
			$query = $propostaModel->getDB()->prepare($sql);
			$query->bindValue(':pendente', $novo_estado, PDO::PARAM_STR);
			$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);
			$query->execute();
			$propostaModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$propostaModel->getDB()->rollback();
			return false;
		}
		return true;
	}

	public static function getById($proposta_id, $db = null, $origem = null)
	{

		$msg = null;
		$retorno = null;

		if (is_null($db)) {
			$propostaModel = new PropostaModel();
			$db = $propostaModel->getDB();
		}

		if (DataValidator::isEmpty($proposta_id))
			throw new UserException('Proposta: A Proposta deve ser identificada.');

		$sql = " SELECT prop.*, u.nome_usuario as resp_envio, us.nome_usuario as resp_aceite, user.nome_usuario as resp_rejeicao
					 FROM proposta prop
					 LEFT JOIN proposta_custo pc ON pc.proposta_ID=prop.id
					 LEFT JOIN usuario u ON prop.usuario_ID_envio=u.id
					 LEFT JOIN usuario us ON prop.usuario_ID_aceite=us.id
					 LEFT JOIN usuario user ON prop.usuario_ID_rejeicao=user.id
					 WHERE prop.id=:proposta_id
					";

		$query = $db->prepare($sql);
		$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		$proposta = new Proposta();
		$proposta->setId($proposta_id);
		$proposta->setDataEntrada($linha->data_entrada);
		$proposta->setStatus($linha->status_proposta);
		$proposta->setDataEnvio($linha->data_envio);
		$proposta->setDataAceite($linha->data_aceite);
		$proposta->setDataRejeicao($linha->data_rejeicao);
		//usuário envio
		$proposta->setNomeRespEnvio($linha->resp_envio);
		//usuário aceite
		$proposta->setNomeRespAceite($linha->resp_aceite);
		//usuário rejeição
		$proposta->setNomeRespRejeicao($linha->resp_rejeicao);

		//custo
		$proposta->setCustos(CustoPropostaModel::getById($proposta->getId(), $db));

		//observações
		$proposta->setObservacoes(ObservacaoModel::getObservacoes($proposta, $db));

		$proposta->setProcesso(ProcessoModel::getById($linha->processo_ID, $db, $origem));

		return $proposta;
	}

	public static function update($proposta)
	{

		$msg = null;
		//apenas para redirecionamento da página: proposta para aconpanhamento
		$acompanhamento_id = 0;

		try {

			$propostaModel = new PropostaModel();
			$propostaModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($proposta))
				throw new UserException('Update Proposta: a Proposta deve ser fornecida.');

			if (DataValidator::isEmpty($proposta->getId()))
				throw new UserException('Update Proposta: A Proposta deve ser identificada.');

			if (DataValidator::isEmpty($proposta->getProcesso()))
				throw new UserException('Update Proposta: o Processo deve ser fornecido.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getId()))
				throw new UserException('Update Proposta: o Processo deve ser identificado.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getId()))
				throw new UserException('Proposta: O campo Advogado é obrigatório.');

			if (DataValidator::isEmpty($proposta->getStatus()))
				throw new UserException('O campo Status é obrigatório.');

			$sql = " UPDATE proposta SET status_proposta = :status_proposta,
										  usuario_ID_aceite = :usuario_id_aceite, 
										  data_aceite = :data_aceite
										  WHERE id = :proposta_id; ";

			//echo 'data: ' . $proposta->getDataAceite();

			$query = $propostaModel->getDB()->prepare($sql);

			$query->bindValue(':status_proposta', $proposta->getStatus(), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($proposta->getUsuarioAceiteId()))
				$query->bindValue(':usuario_id_aceite', $proposta->getUsuarioAceiteId(), PDO::PARAM_INT);
			else
				$query->bindValue(':usuario_id_aceite', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($proposta->getDataAceite()))
				$query->bindValue(':data_aceite', $proposta->getDataAceite(), PDO::PARAM_STR);
			else
				$query->bindValue(':data_aceite', NULL, PDO::PARAM_NULL);

			$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);

			$query->execute();

			//observações do advogado				
			$prop_cadastrada = self::getById($proposta->getId(), $propostaModel->getDB());

			//se insertou um advogado
			if (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getId())) {
				//se já existia um advogado cadastrado
				if (!DataValidator::isEmpty($prop_cadastrada) && !DataValidator::isEmpty($prop_cadastrada->getProcesso()->getAdvogado())) {

					//se o id do insertado for diferente do cadastrado...
					if ($prop_cadastrada->getProcesso()->getAdvogado()->getId() != $proposta->getProcesso()->getAdvogado()->getId()) {
						ObservacaoModel::excluiObservacoes($proposta->getId(), $propostaModel->getDB());
						ObservacaoModel::saveObservacaoFromAdvogado($proposta->getProcesso()->getAdvogado(), $proposta, $propostaModel->getDB());
					}
				} else
					ObservacaoModel::saveObservacaoFromAdvogado($proposta->getProcesso()->getAdvogado(), $proposta, $propostaModel->getDB());
			}

			//cadastra/altera demais observações
			if (!DataValidator::isEmpty($proposta->getObservacoes()))
				ObservacaoModel::saveObservacao($proposta, $propostaModel->getDB());

			ProcessoModel::updateFromProposta($proposta->getProcesso(), $propostaModel->getDB());
			CustoPropostaModel::save($proposta, $propostaModel->getDB());

			//se proposta Aceita passa pra Acompanhamento
			$prop_alterada = self::getById($proposta->getId(), $propostaModel->getDB());

			if ($proposta->getStatus() == 'A' && $prop_alterada->getProcesso()->getSinalizador() == 'V') {
				$acompanhamento_id = AcompanhamentoModel::insert($proposta, $propostaModel->getDB());
				ProcessoModel::alteraStatus($proposta->getProcesso()->getId(), 'A', $propostaModel->getDB());
			}

			$propostaModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$propostaModel->getDB()->rollback();
		}

		return array('msg' => $msg, 'acompanhamento_id' => $acompanhamento_id);
	}

	public static function insert($proposta)
	{

		$msg = null;

		try {

			$propostaModel = new PropostaModel();
			$propostaModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($proposta))
				throw new UserException('Proposta: A Proposta deve ser fornecida.');

			if (DataValidator::isEmpty($proposta->getProcesso()))
				throw new UserException('Proposta: O Processo deve ser fornecido.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()))
				throw new UserException('Proposta: A Entrada deve ser fornecida.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getEstado()))
				throw new UserException('Proposta: O campo Estado é obrigatório.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getDataProcesso()))
				throw new UserException('Proposta: O campo Data do Processo é obrigatório.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getId()))
				throw new UserException('Proposta: O campo Advogado é obrigatório.');

			if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getNumero()))
				throw new UserException('Proposta: O campo Número do Processo é obrigatório.');

			$entrada_id = ProcessoEntradaModel::save($proposta->getProcesso()->getEntrada(), $propostaModel->getDB());
			$processo_id = ProcessoModel::insertFromProposta($proposta->getProcesso(), $propostaModel->getDB());

			$sql = " INSERT INTO proposta 
								(processo_ID, 
								 data_entrada,
								 status_proposta, 
								 usuario_ID_aceite, 
								 usuario_ID_envio, 
								 data_envio, 
								 data_aceite							
								 ) 
							VALUES 
								(:processo_id, 
								 :data_entrada,
								 :status_proposta, 
								 :usuario_id_aceite, 
								 :usuario_id_envio,  
								 :data_envio, 
								 :data_aceite
								 ) ";

			$query = $propostaModel->getDB()->prepare($sql);
			$query->bindValue(':processo_id', $processo_id, PDO::PARAM_INT);
			$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);

			$query->bindValue(':status_proposta', 'N', PDO::PARAM_STR);

			if (!DataValidator::isEmpty($proposta->getUsuarioAceiteId()))
				$query->bindValue(':usuario_id_aceite', $proposta->getUsuarioAceiteId(), PDO::PARAM_INT);
			else
				$query->bindValue(':usuario_id_aceite', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($proposta->getUsuarioEnvioId()))
				$query->bindValue(':usuario_id_envio', $proposta->getUsuarioEnvioId(), PDO::PARAM_INT);
			else
				$query->bindValue(':usuario_id_envio', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($proposta->getDataEnvio()))
				$query->bindValue(':data_envio', $proposta->getDataEnvio(), PDO::PARAM_INT);
			else
				$query->bindValue(':data_envio', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($proposta->getDataAceite()))
				$query->bindValue(':data_aceite', $proposta->getDataAceite(), PDO::PARAM_INT);
			else
				$query->bindValue(':data_aceite', NULL, PDO::PARAM_NULL);

			$query->execute();

			$proposta->setId($propostaModel->getDB()->lastInsertId());

			//custo do jornal
			CustoPropostaModel::save($proposta, $propostaModel->getDB());

			//observações do advogado
			if (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getId())) {
				ObservacaoModel::saveObservacaoFromAdvogado($proposta->getProcesso()->getAdvogado(), $proposta, $propostaModel->getDB());
			}

			/*$processo_cadastrado = ProcessoModel::getById($processo_id, $propostaModel->getDB());
				//se proposta Aceita passa pra Acompanhamento
				if( $proposta->getStatus() == 'A' && $processo_cadastrado->getSinalizador() == 'V' )	{		
					AcompanhamentoModel::insert($proposta, $propostaModel->getDB());
					ProcessoModel::alteraStatus($proposta->getProcesso()->getId(), 'A', $propostaModel->getDB());
				}*/

			$propostaModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$propostaModel->getDB()->rollback();
		}

		return array('msg' => $msg);
	}


	//**********//

	//insere a proposta: no cadastro/update do processo e ao enviar o processo para proposta manualmente
	//@param origem = manual
	public static function insertFromProcesso($processo, $origem = null, $db = null)
	{

		if (is_null($db)) {
			$propostaModel = new PropostaModel();
			$db = $propostaModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Processo para Proposta: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Processo para Proposta: O Processo deve ser identificado.');

		$sql = " INSERT INTO proposta (processo_ID, status_proposta, data_entrada) VALUES (:processo_ID, :status_proposta, :data_entrada) ";

		$query = $db->prepare($sql);
		$query->bindValue(':processo_ID', $processo->getId(), PDO::PARAM_INT);
		$query->bindValue(':status_proposta', 'N', PDO::PARAM_STR);
		$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);

		$query->execute();

		$proposta = new Proposta();
		$proposta->setStatus('N');
		$proposta->setId($db->lastInsertId());

		//observações do advogado
		if (!DataValidator::isEmpty($processo->getAdvogado()))
			ObservacaoModel::saveObservacaoFromAdvogado($processo->getAdvogado(), $proposta, $db);

		//custo do jornal
		if (!DataValidator::isEmpty($processo->getJornal())) {

			$jornal = JornalModel::getById($processo->getJornal()->getId(), $db);
			$custo_padrao = new CustoProposta();
			$custo_padrao->setQuantidade(2);
			//o modelo do custo trata as pontuações
			$custo_padrao->setValorPadrao(!DataValidator::isEmpty($jornal->getCusto()->getValorPadrao()) ? number_format($jornal->getCusto()->getValorPadrao(), 2, ',', '.') : 0);
			$custo_padrao->setValorFinal(!DataValidator::isEmpty($jornal->getCusto()->getValorPadrao()) ? number_format($jornal->getCusto()->getValorPadrao(), 2, ',', '.') : 0);
			$custo_padrao->setStatus('P');
			$proposta->setCusto($custo_padrao);

			CustoPropostaModel::save($proposta, $db);
		}

		//se manual troca sinalizador de 'P' para 'S' de proposta
		if (!DataValidator::isEmpty($origem)) {
			ProcessoModel::verificaAlertaParaSinalizador($processo, $db);
			ProcessoModel::alteraStatus($processo->getId(), 'S', $db);
		}

		//retorna o id pra redirecionar a página para a  proposta
		return	$proposta->getId();
	}

	//rejeita a proposta
	public static function rejeitar($proposta, $usuario_id = 0, $db = null)
	{

		if (is_null($db)) {
			$propostaModel = new PropostaModel();
			$db = $propostaModel->getDB();
		}

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Rejeitar Proposta: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Rejeitar Proposta: A Proposta deve ser identificada.');

		if (DataValidator::isEmpty($usuario_id))
			throw new UserException('Envia Proposta: O Usuário deve ser identificado.');

		$sql = " UPDATE proposta SET status_proposta = :status, usuario_ID_rejeicao=:usuario_id, data_rejeicao=:data_rejeicao
									  WHERE id = :proposta_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':status', 'R', PDO::PARAM_STR);
		$query->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
		$query->bindValue(':data_rejeicao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

		$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);
		$query->execute();
	}


	public static function exclui($proposta_id)
	{

		$msg = null;
		$propostaModel = new ProcessoModel();

		try {
			if (DataValidator::isEmpty($proposta_id))
				throw new UserException('Excluir Proposta: A Proposta deve ser identificada.');

			$sql = ' DELETE FROM proposta WHERE id=:proposta_id ';
			$query = $propostaModel->getDB()->prepare($sql);
			$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//recebe array de propostas
	public static function excluiPropostas($propostas)
	{

		$msg = null;
		$propostaModel = new ProcessoModel();

		try {
			if (DataValidator::isEmpty($propostas))
				throw new UserException('Excluir Propostas: As Propostas devem ser identificadas.');

			foreach ($propostas as $proposta_id) {
				$sql = ' DELETE FROM proposta WHERE id=:proposta_id ';
				$query = $propostaModel->getDB()->prepare($sql);
				$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);
				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//**********//

	public static function enviaEmail($proposta, $usuario_id = 0)
	{

		$msg = null;

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Envia Proposta: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Envia Proposta: A Proposta deve ser identificada.');

		if (DataValidator::isEmpty($proposta->getProcesso()))
			throw new UserException('Envia Proposta: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($proposta->getProcesso()->getId()))
			throw new UserException('Envia Proposta: O Processo deve ser identificado.');

		if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()))
			throw new UserException('Envia Proposta: A Entrada deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getId()))
			throw new UserException('Envia Proposta: A Entrada deve ser identificada.');

		if (DataValidator::isEmpty($usuario_id))
			throw new UserException('Envia Proposta: O Usuário deve ser identificado.');

		if (DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()))
			throw new UserException('Envia Proposta: O Advogado deve ser fornecido.');

		//Advogado precisa ter, no minimo, 1 email para envio da proposta
		if (DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getEmails()))
			throw new UserException('Envia Proposta: O Advogado deve ter, no mínimo, 1 email.');

		$responsavel = UsuarioModel::getById($usuario_id);
		$nome_responsavel = $responsavel->getNome();

		$blackfriday = false;

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
		
		<td align="left" valign="top" width="30%" valign="middle"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" alt="Destak Publicidade">
		<p style="font: 16px arial, sans-serif; color: #000; line-height: 20px;">
		<br />
		Proposta nº <strong>' . (!DataValidator::isEmpty($proposta->getId()) && !DataValidator::isEmpty($proposta->getDataEntrada()) ? $proposta->getId() . '/' . DataFilter::retornaAno(date("Y-m-d H:i:s")) : '') . '</strong>
		</font>
		</p>
		</td>

		<td align="right" width="50%" valign="middle">

			<p style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
				<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME.</strong>
				<br />
				CNPJ: 08.324.897/0001-10
				<br />
				' . parent::ENDERECO_PADRAO . '
				<br />
				Telefone/WhatsApp.: ' . parent::TELEFONE_PADRAO . '&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
				<br />
				<strong>Contato:</strong> ' . $responsavel->getNome() . ' - ' . $responsavel->getEmail() . '
			</p>
		
		</td>

	</tr>';

	if($blackfriday){
		$mensagem .= '
			<tr>
			  <td colspan="2">
			  	<br />
			  	<img width="700" src="http://www.sistemadestakpublicidade.com.br/img/blackfriday/banner.jpg">
			  </td>
			</tr>
		';
	}

$mensagem .= '<tr>
<td colspan="2">
	<table width="100%">
		<tr>
			<td>
		<p>
		<br />
		<br />
		<font face="Arial, sans-serif" size="3">
		São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '
		<br />
		<br />
		Ilmo.(a) Sr.(a) Dr.(a): <strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()) ? $proposta->getProcesso()->getAdvogado()->getNome() : '') . '</strong> 
		<br />';

		$tels = $proposta->getProcesso()->getAdvogado()->getTelefones();
		if (!DataValidator::isEmpty($tels)) {

			$mensagem .= 'Tel.: <strong>';

			$qtd_tels = sizeof($tels);
			foreach ($tels as $chave_ => $tel) {
				$chave_ += 1;
				$mensagem .= $tel->getDdd() . ' ' . $tel->getNumero();
				$mensagem .= $qtd_tels > $chave_ ? ' | ' : '';
			}

			$mensagem .= '</strong>';
		}

		$mensagem .= '<br />
		E-mail: <strong>';

		$emails = $proposta->getProcesso()->getAdvogado()->getEmails();
		if (!DataValidator::isEmpty($emails)) {
			$qtd_emails = sizeof($emails);
			foreach ($emails as $chave => $email) {
				$chave += 1;
				$mensagem .= $email->getEmailEndereco();
				$mensagem .= $qtd_emails > $chave ? ' | ' : '';
			}
		}

		$mensagem .= '</strong> 
		</font>	
		</p>
	</td>
		';

	if($blackfriday)
	{
		$mensagem .= '
			<td width="30%" style="text-align:right;" valign="middle">
				<br />
				<br />
				<img width="200" style="position:relative;" src="http://www.sistemadestakpublicidade.com.br/img/blackfriday/tempo-limitado.jpg">
			</td>
		';
	}

$mensagem .= '
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<p>
		<font face="Arial, sans-serif" size="3">
		<br />
		Pela presente, vimos apresentar orçamento para publicação de edital.  
		<br />
		<br />
		</font>
		</p>
		</td>
	</tr>

</table>
<!-- intro -->

<table width="700" align="center" cellpadding="0" cellspacing="0">

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerente: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getRequerente()) ? $proposta->getProcesso()->getRequerente() : '') . '</strong></font></td>
	</tr>

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerido: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getRequerido()) ? $proposta->getProcesso()->getRequerido() : '') . '</strong></font></td>
	</tr>
	
	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Vara: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getSecretaria()) ? $proposta->getProcesso()->getSecretaria()->getNome() : '') . '</strong></font></td>
	</tr>';

		if (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()) && !DataValidator::isEmpty($proposta->getProcesso()->getAcao())) {
			$mensagem .= '
	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Ação: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . $proposta->getProcesso()->getAcao() . '</strong> </font></td>
	</tr>';
		}

		$mensagem .= '
	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Processo: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getNumero()) ? $proposta->getProcesso()->getEntrada()->getNumero() : '') . '</strong> </font></td>
	</tr>

</table>
<!-- dados -->

<br />';

if($blackfriday){
	$mensagem .= '<table width="700" align="center" cellspacing="0" cellpadding="1">
		<tr bgcolor="#000000">
		  <td height="40">
		  	<img width="700" src="http://www.sistemadestakpublicidade.com.br/img/blackfriday/opcoes-publicacao.jpg">
		  </td>
		</tr>
	</table>';
}else{
	$mensagem .= '<table width="700" align="center" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
	<tr bgcolor="01911c">
		<td height="40">
			<font face="arial" color="#FFFFFF" size="3"> <strong>&nbsp; Opções de Publicação</strong> </font>
		</td>
	</tr>
	</table>';
}

		$custos_proposta = !DataValidator::isEmpty($proposta->getCustos()) ? $proposta->getCustos() : null;

		if (!DataValidator::isEmpty($custos_proposta)) {
			if (isset($custos_proposta['valor_P'])) {

				$mensagem .= '
<table width="700" align="center" border="1" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr bgcolor="#eeeeee" height="40">
		<th width="20%" align="left">&nbsp; Quantidade</th>
		<th>&nbsp; Jornal</th>
		<th width="25%">&nbsp; Valor</th>
	</tr>

	<tr height="40">
		<td>&nbsp; ' . $custos_proposta['valor_P']->getQuantidade() . '</td>
		<td>&nbsp; ' . (!DataValidator::isEmpty($proposta->getProcesso()->getJornal()) ? $proposta->getProcesso()->getJornal()->getNome() : '') . '</td>
		<td>&nbsp; R$ ' .  number_format($custos_proposta['valor_P']->getValorFinal(), 2, ',', '.') . '*</td>
	</tr>

</table>
<!-- publicação -->';
			} //valor padrao

			if (isset($custos_proposta['valor_D']) && isset($custos_proposta['valor_P'])) {

				$mensagem .= '
<table width="700" align="center" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
<tr>
	<td height="40" align="center">
		<font face="arial" color="#000000" size="3"> <strong>OU</strong> </font>
	</td>
</tr>
</table>

<table width="700" align="center" border="1" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr bgcolor="#eeeeee" height="40">
		<th width="20%" align="left">&nbsp; Quantidade</th>
		<th>&nbsp; Jornal</th>
		<th width="25%">&nbsp; Valor</th>
	</tr>

	<tr height="40">
		<td>&nbsp; ' . $custos_proposta['valor_D']->getQuantidade() . '</td>
		<td>&nbsp; Diário da Justiça Eletrônico</td>
		<td>&nbsp; R$ ' . number_format($custos_proposta['valor_D']->getValorDje(), 2, ',', '.') . '</td>
	</tr>

	<tr height="40">
		<td>&nbsp; ' . $custos_proposta['valor_P']->getQuantidade() . '</td>
		<td>&nbsp; ' . (!DataValidator::isEmpty($proposta->getProcesso()->getJornal()) ? $proposta->getProcesso()->getJornal()->getNome() : '') . '</td>
		<td>&nbsp; R$ ' . number_format($custos_proposta['valor_P']->getValorFinal(), 2, ',', '.') . '</td>
	</tr>

	<tr height="40">
		<td></td>
		<td></td>
		<td>&nbsp; Total: <strong>R$ ' . number_format($custos_proposta['valor_D']->getValorFinal(), 2, ',', '.') . ' **</strong> </td>
	</tr>

</table>
<!-- publicação -->';
			} //valor dje

		} //jornais

		$mensagem .= '
<br />

<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;">';

	if($blackfriday){
		$mensagem .= '
			<tr>
				<td><p style="color: #000;">
					Orçamento válido até <span style="text-decoration: underline;">30/11/2020</span>. Após esta data consulte valores.
					<br />
					<br />
				</p></td>
			</tr>
		';
	}
	
	$mensagem .= '<tr>
		<td>
			<p>
			<em>*	Recolhimento da guia do DJE (Prov. 1668/2009) a cargo do advogado. ';

		if (isset($custos_proposta['valor_D'])) {
			$mensagem .= '
			<br />
			**	Recolhimento da guia do DJE (Prov. 1668/2009) até o valor máximo de R$ ' . number_format($custos_proposta['valor_D']->getValorDje(), 2, ',', '.') . ' a cargo da Destak, já incluso no valor do orçamento.</em>';
		}

		$mensagem .= '
			<br />
			<br />
			Nosso serviço consiste na elaboração da minuta pelo advogado da agência, acompanhamento processual, composição do material, veiculação nos jornais competentes e juntada dos comprovantes originais nos autos (quando do Estado de São Paulo) além de envio gratuito de exemplares para o escritório.	
			<br />
			<br />
			Em caso de cancelamento do serviço durante a execução, será cobrado um valor relativo à elaboração da minuta.
			<br />
			<br />
			<strong>Forma e Prazo de Pagamento:</strong> Boleto bancário com vencimento para 15 dias após a publicação.';

		if($blackfriday){
			$mensagem .= '
				<br />
				<br />
				<span style="color: #000">Publicação de edital de leilão totalmente gratuito. Consulte-nos.</span>
			';
		}

		$mensagem .= '
			<br />
			<br />
			Saiba mais sobre a Destak Publicidade acessando nosso site: <a href="http://www.destakpublicidade.com.br" title="Destak Publicidade"  target="_blank" style="color: #000; text-decoration: underline;">www.destakpublicidade.com.br</a>
			<br />
			<br />
			Conheça também nossa gestora de leilões judiciais online: <a href="http://www.destakleiloes.com.br" title="Destak Leilões"  target="_blank" style="color: #000; text-decoration: underline;">www.destakleiloes.com.br</a>
			</p>
		</td>
	</tr>

</table>

</body>
</html>';

		$email_from = null;

		if (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada())) {
			$conta = strtolower($proposta->getProcesso()->getEntrada()->getEstado());

			switch ($conta) {
				case "rj":
					$email_from = $conta . '@sistemadestakpublicidade.com.br';
					break;
				case "sp":
					$email_from = $conta . '@sistemadestakpublicidade.com.br';
					break;
				case "ms":
					$email_from = $conta . '@sistemadestakpublicidade.com.br';
					break;
				case "mt":
					$email_from = $conta . '@sistemadestakpublicidade.com.br';
					break;
				default:
					$email_from = 'sp@sistemadestakpublicidade.com.br';
					break;
			}
		}

		// Define os dados do servidor e tipo de conexao
		require_once("lib/Mail.php");
		$mail = Mail::Init();

		//$mail->Username   = $email_from;

		// Define o remetente
		$mail->SetFrom($email_from, 'Destak Publicidade');

		$emails_advogado = $proposta->getProcesso()->getAdvogado()->getEmails();
		if (!DataValidator::isEmpty($emails_advogado)) {
			foreach ($emails_advogado as $email) {
				$mail->AddAddress($email->getEmailEndereco(), $email->getEmailEndereco());
			}
		}

		// Define a mensagem (Texto e Assunto)
		$mail->Subject  = "Proposta de Publicação de Edital - Processo " . (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getNumero()) ? $proposta->getProcesso()->getEntrada()->getNumero() : '') . ' - Cobrimos qualquer proposta comprovada';
		$mail->Body = $mensagem;

		$enviado = $mail->Send();

		// Limpa os destinatarios e os anexos
		$mail->ClearAllRecipients();
		$mail->ClearAttachments();

		// Exibe uma mensagem de resultado
		if ($enviado) {

			$sql = " UPDATE proposta SET status_proposta=:status_proposta, usuario_ID_envio=:usuario_id_envio, data_envio=:data_envio WHERE id=:proposta_id; ";

			$propostaModel = new PropostaModel();
			$query = $propostaModel->getDB()->prepare($sql);
			$query->bindValue(':data_envio', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->bindValue(':status_proposta', 'E', PDO::PARAM_STR);
			$query->bindValue(':usuario_id_envio', $usuario_id, PDO::PARAM_INT);
			$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);

			$query->execute();
		} else {
			$msg =  $mail->ErrorInfo;
		}
		//--Envio do email por SMTP		

		return $msg;
	}
}
