<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("models/AlertaModel.php");
require_once("models/RelatorioModel.php");
require_once("models/PropostaModel.php");
require_once("classes/Processo.class.php");
require_once("classes/ProcessoRepetido.class.php");
require_once("models/AdvogadoModel.php");

class ProcessoModel extends PersistModelAbstract
{

	public static function lista(
		$sinalizador = null,
		$secretaria_id = 0,
		$numero_processo = null,
		$advogado_nome = null,
		$requerente_nome = null,
		$requerido_nome = null,
		$processo_id = 0,
		$data_processo = null,
		$estado = null,
		$pagina = 0,
		$qtd_pagina = 0,
		$ordenacao = 0,
		$sentido_ordenacao = "a"
	) {

		$sql = " 
					SELECT SQL_CALC_FOUND_ROWS DISTINCT
						pr.id, 
						pr.data_entrada, 
						pr.requerente, 
						pr.requerido, 
						pr.sinalizador, 
						pr.advogado_ID,
						pr.alertas,
						pe.numero, 
						pe.ID as entrada_id, 
						pe.estado, 
						pe.data_processo, 
						adv.nome_advogado, 
						pr.alertas,
						(   SELECT 
                            	group_concat(concat(pobs.data_entrada,'|:|',pobs.mensagem) SEPARATOR '|*|') AS obs 
                               	FROM processo_obs pobs 
                               	WHERE pobs.processo_ID = pr.id AND pobs.deleted='N' 
                        ) as observacoes_1, 
                        (    SELECT 
                            	group_concat(concat(aobs.data_entrada,'|:|',aobs.mensagem) SEPARATOR '|*|') AS obs
                               	FROM advogado_obs aobs 
                               	WHERE aobs.advogado_ID = pr.advogado_ID AND aobs.deleted='N' 
                        ) as observacoes_2,
						(SELECT COUNT(1) 
						from processo pr1
						INNER JOIN processo_entrada pe1 ON pr1.entrada_ID=pe1.id 
						WHERE pe1.numero = pe.numero AND pr1.id <> pr.id AND pe1.data_processo = pe.data_processo) as num_repetidos
						
					FROM processo_entrada pe 
						INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='P'  
						LEFT JOIN advogado adv ON pr.advogado_ID=adv.id	
					";

		$where = true;

		$sql .= " WHERE pr.id IN(SELECT id FROM Ultimos10mil) ";

		if (!DataValidator::isEmpty($sinalizador)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "pr.sinalizador=:sinalizador ";
		} else {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "pr.sinalizador<>'M' ";
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

		if (!DataValidator::isEmpty($processo_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " pr.id=:processo_id ";
		}

		if (!DataValidator::isEmpty($data_processo)) {
			$data = DateTime::createFromFormat('d/m/Y', $data_processo);

			if($data != false){
				if (!$where) {
					$where = true;
					$sql .= "WHERE ";
				} else {
					$sql .= "AND ";
				}
				$sql .= " pe.data_processo=:data_processo ";

				$data_processo = $data->format('Y-m-d');
			}else{
				$data_processo = null;
			}			
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

		// try{
		// 	session_start();
		// 	$usuario = $_SESSION['login'];
		// 	$usuario->getId();

		// 	if (!$where) {
		// 		$where = true;
		// 		$sql .= "WHERE ";
		// 	} else {
		// 		$sql .= "AND ";
		// 	}
		// 	$sql .= " pe.data_processo > '2021-04-06' ";

		// }catch(Exception $ex){
			
		// }

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
				array_push($order, 'pe.estado' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			case 4:
				array_push($order, 'pr.alertas' . " " . $arr_sentido[$sentido_ordenacao]);
				break;
			default:
				array_push($order, 'pe.data_processo DESC');
				array_push($order, 'pe.numero DESC');
				break;
		}

		$sql .= implode(",", $order);

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		//echo $sql;

		$processos = array();
		$processoModel = new ProcessoModel();
		

		$query = $processoModel->getDB()->prepare($sql);
	

		if (!DataValidator::isEmpty($sinalizador))
			$query->bindValue(':sinalizador', $sinalizador, PDO::PARAM_STR);

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

		if (!DataValidator::isEmpty($processo_id))
			$query->bindValue(':processo_id', $processo_id, PDO::PARAM_INT);

		if (!DataValidator::isEmpty($data_processo))
			$query->bindValue(':data_processo', $data_processo, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		$query->execute();

		//*******
		$query_num_linhas = $processoModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query_num_linhas->execute();

		$num_linhas = $query_num_linhas->fetchObject();
		$totalLinhas = $num_linhas ? $num_linhas->frows : 0;
		//*******

		while ($linha = $query->fetchObject()) {
			$arrObs = array();
			$processo = new Processo();
			$processo->setId($linha->id);
			$processo->setDataEntrada($linha->data_entrada);
			$processo->setRequerente($linha->requerente);
			$processo->setRequerido($linha->requerido);
			$processo->setSinalizador($linha->sinalizador);
			$processo->setRepetido($linha->num_repetidos);
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
			$processo->setDescObs($arrObs);

			$entrada = new ProcessoEntrada();
			$entrada->setId($linha->entrada_id);
			$entrada->setNumero($linha->numero);
			$entrada->setEstado($linha->estado);
			$entrada->setDataProcesso($linha->data_processo);

			if (!DataValidator::isEmpty($linha->advogado_ID)) {
				$advogado = new Advogado();
				$advogado->setNome($linha->nome_advogado);
				$processo->setAdvogado($advogado);
			}

			$processo->setEntrada($entrada);
			$processo->setQtdAlertas($linha->alertas);
			$processo->setAlertas(AlertaModel::lista($processo, $processoModel->getDB()));

			$processos[] = $processo;
		}

		return array('processos' => $processos, 'totalLinhas' => $totalLinhas);
	}

	/*public static function retornaTotal( $sinalizador = null , $secretaria_id = 0, $numero_processo = null, $processo_id = 0, $estado = null ){
			
		}*/

	public static function getById($proceso_id, $db = null, $origem = null)
	{

		$msg = null;
		$processo = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($proceso_id))
			throw new UserException('Processo: O Processo deve ser identificado.');

		$sql = " SELECT 	
						pr.*, 
						pe.numero, 
						pe.estado, 
						pe.data_processo, 
						pe.conteudo, 
						pe.advogado, 
						pe.jornal, 
						pe.secretaria, 
						pe.id as entrada_id
						FROM processo pr
						INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id 
						WHERE pr.id=:id ";

		$query = $db->prepare($sql);
		$query->bindValue(':id', $proceso_id, PDO::PARAM_INT);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$processo = new Processo();
		$processo->setId($linha->id);
		$processo->setDataEntrada($linha->data_entrada);
		$processo->setRequerente($linha->requerente);
		$processo->setRequerido($linha->requerido);
		$processo->setAcao($linha->acao);
		$processo->setSinalizador($linha->sinalizador);

		if (!DataValidator::isEmpty($linha->entrada_ID)) {
			$processo->setEntrada(ProcessoEntradaModel::getById($linha->entrada_ID, $db));
		}

		if (!DataValidator::isEmpty($linha->advogado_ID)) {
			$advogado = AdvogadoModel::getBy($linha->advogado_ID, $db, $origem);
			$processo->setAdvogado($advogado);
		}

		if (!DataValidator::isEmpty($linha->secretaria_ID)) {
			$processo->setSecretaria(SecretariaModel::getBy($linha->secretaria_ID, $db));
		}

		if (!DataValidator::isEmpty($linha->jornal_ID)) {
			$processo->setJornal(JornalModel::getById($linha->jornal_ID, $db));
		}

		//observações do Processo
		$processo->setObservacoes(ObservacaoModel::getObservacoes($processo, $db));

		//observações do Advogado
		if (isset($advogado) && $advogado instanceof Advogado) {
			$processo->setObservacoesAdvogado(ObservacaoModel::getObservacoes($advogado, $db));
		}

		$processo->setAlertas(AlertaModel::lista($processo, $db));
		$processo->setRepetidos(self::getNumeroRepetido($processo, $db));

		return $processo;
	}


	//*******************************//

	public static function insertFromEntrada($processo, $db = null)
	{

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Insert Processo: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getEntrada()))
			throw new UserException('Insert Processo: A Entrada deve ser fornecida.');

		if (DataValidator::isEmpty($processo->getEntrada()->getId()))
			throw new UserException('Insert Processo: A Entrada deve ser identificada.');

		$sql = " INSERT INTO processo (
											  entrada_ID, 
											  data_entrada, 
											  sinalizador, 
											  secretaria_ID, 
											  advogado_ID, 
											  jornal_ID, 
											  requerente, 
											  requerido,
											  acao,
											  status_processo
											  ) 
										VALUES (:entrada_id, 
												:data_entrada, 
												:sinalizador, 
												:secretaria_ID, 
												:advogado_ID, 
												:jornal_ID, 
												:requerente, 
												:requerido,
												:acao,
												:status_processo) ";

		$query = $db->prepare($sql);
		$query->bindValue(':entrada_id', $processo->getEntrada()->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$query->bindValue(':sinalizador', $processo->getSinalizador(), PDO::PARAM_STR);

		if (!DataValidator::isEmpty($processo->getSecretaria()))
			$query->bindValue(':secretaria_ID', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':secretaria_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getAdvogado()))
			$query->bindValue(':advogado_ID', $processo->getAdvogado()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':advogado_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getJornal()))
			$query->bindValue(':jornal_ID', $processo->getJornal()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':jornal_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getRequerente()))
			$query->bindValue(':requerente', $processo->getRequerente(), PDO::PARAM_STR);
		else
			$query->bindValue(':requerente', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getRequerido()))
			$query->bindValue(':requerido', $processo->getRequerido(), PDO::PARAM_STR);
		else
			$query->bindValue(':requerido', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getAcao()))
			$query->bindValue(':acao', $processo->getAcao(), PDO::PARAM_STR);
		else
			$query->bindValue(':acao', NULL, PDO::PARAM_NULL);

		if ($processo->getSinalizador() == 'V')
			$query->bindValue(':status_processo', 'S', PDO::PARAM_STR);
		else
			$query->bindValue(':status_processo', 'P', PDO::PARAM_STR);

		$query->execute();
		$processo->setId($db->lastInsertId());

		RelatorioModel::grava_numero_importacoes($processo, $db);

		if ($processo->getSinalizador() == 'V')
			PropostaModel::insertFromProcesso($processo, null, $db);

		self::atualizaQtdAlertas($processo, $db);
	}

	public static function insertFromProposta($processo, $db = null)
	{

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Insert Processo: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getEntrada()))
			throw new UserException('Insert Processo: A Entrada deve ser fornecida.');

		if (DataValidator::isEmpty($processo->getEntrada()->getId()))
			throw new UserException('Insert Processo: A Entrada deve ser identificada.');

		$sql = " INSERT INTO processo (
											  entrada_ID, 
											  data_entrada, 
											  sinalizador, 
											  secretaria_ID, 
											  advogado_ID, 
											  jornal_ID, 
											  requerente, 
											  requerido,
											  status_processo,
											  acao
											  ) 
										VALUES (:entrada_id, 
												:data_entrada, 
												:sinalizador, 
												:secretaria_ID, 
												:advogado_ID, 
												:jornal_ID, 
												:requerente, 
												:requerido,
												:status_processo,
												:acao
												) ";

		$query = $db->prepare($sql);
		$query->bindValue(':entrada_id', $processo->getEntrada()->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
		$query->bindValue(':sinalizador', $processo->getSinalizador(), PDO::PARAM_STR);

		if (!DataValidator::isEmpty($processo->getSecretaria()))
			$query->bindValue(':secretaria_ID', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':secretaria_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getAdvogado()))
			$query->bindValue(':advogado_ID', $processo->getAdvogado()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':advogado_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getJornal()))
			$query->bindValue(':jornal_ID', $processo->getJornal()->getId(), PDO::PARAM_INT);
		else
			$query->bindValue(':jornal_ID', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getRequerente()))
			$query->bindValue(':requerente', $processo->getRequerente(), PDO::PARAM_STR);
		else
			$query->bindValue(':requerente', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getRequerido()))
			$query->bindValue(':requerido', $processo->getRequerido(), PDO::PARAM_STR);
		else
			$query->bindValue(':requerido', NULL, PDO::PARAM_NULL);

		if (!DataValidator::isEmpty($processo->getAcao()))
			$query->bindValue(':acao', $processo->getAcao(), PDO::PARAM_STR);
		else
			$query->bindValue(':acao', NULL, PDO::PARAM_NULL);

		$query->bindValue(':status_processo', 'S', PDO::PARAM_STR);

		$query->execute();
		$processo->setId($db->lastInsertId());

		self::verificaAlertaParaSinalizador($processo, $db);
		self::atualizaQtdAlertas($processo, $db);

		return $processo->getId();
	}

	public static function updateFromProcesso($processo)
	{

		$msg = null;
		$proposta_id = 0;

		try {

			$processoModel = new ProcessoModel();
			$processoModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($processo))
				throw new UserException('Update Processo: o Processo deve ser fornecido.');

			if (DataValidator::isEmpty($processo->getId()))
				throw new UserException('O Processo deve ser identificado.');

			$sql = " UPDATE processo SET requerente = :requerente,
										  requerido = :requerido, 
										  acao = :acao,
										  jornal_ID = :jornal_ID,
										  secretaria_ID = :secretaria_ID,
										  advogado_ID = :advogado_ID
										  WHERE id = :processo_id; ";

			$query = $processoModel->getDB()->prepare($sql);
			$query->bindValue(':requerente', $processo->getRequerente(), PDO::PARAM_STR);
			$query->bindValue(':requerido', $processo->getRequerido(), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($processo->getAcao()))
				$query->bindValue(':acao', $processo->getAcao(), PDO::PARAM_STR);
			else
				$query->bindValue(':acao', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($processo->getJornal()))
				$query->bindValue(':jornal_ID', $processo->getJornal()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':jornal_ID', NULL, PDO::PARAM_NULL);

			//vincula jornal a secretaria selecionada
			if (!DataValidator::isEmpty($processo->getJornal()))
				JornalModel::vinculaSecretaria($processo, $processoModel->getDB());

			if (!DataValidator::isEmpty($processo->getSecretaria()))
				$query->bindValue(':secretaria_ID', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':secretaria_ID', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($processo->getAdvogado()))
				$query->bindValue(':advogado_ID', $processo->getAdvogado()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':advogado_ID', NULL, PDO::PARAM_NULL);

			$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
			$query->execute();

			//cadastra/altera demais observações
			if (!DataValidator::isEmpty($processo->getObservacoes()))
				ObservacaoModel::saveObservacao($processo, $processoModel->getDB());

			//salva alterações e resgata o objeto para verificar se há alertas
			$processo_cadastrado = self::getById($processo->getId(), $processoModel->getDB());

			//apenas se sinaliador Amarelo e sem alertas, passa automaticamente para Proposta
			if ($processo->getSinalizador() == 'A' && count($processo_cadastrado->getAlertas()) <= 0) {
				self::verificaAlertaParaStatus($processo, $processoModel->getDB());
				self::verificaAlertaParaSinalizador($processo, $processoModel->getDB());
				$proposta_id = PropostaModel::insertFromProcesso($processo, null, $processoModel->getDB());
			}

			$processoModel->getDB()->commit();

			self::atualizaQtdAlertas($processo, $processoModel->getDB());
		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$processoModel->getDB()->rollback();
		}

		return array('mensagem' => $msg, 'proposta_id' => $proposta_id);
	}

	public static function updateFromProposta($processo, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		try {

			if (DataValidator::isEmpty($processo))
				throw new UserException('Update Processo: o Processo deve ser fornecido.');

			if (DataValidator::isEmpty($processo->getId()))
				throw new UserException('O Processo deve ser identificado.');

			$sql = " UPDATE processo SET requerente = :requerente,
										  requerido = :requerido, 
										  acao = :acao,
										  jornal_ID = :jornal_ID,
										  secretaria_ID = :secretaria_ID,
										  advogado_ID = :advogado_ID
										  WHERE id = :processo_id; ";

			$query = $db->prepare($sql);
			$query->bindValue(':requerente', $processo->getRequerente(), PDO::PARAM_STR);
			$query->bindValue(':requerido', $processo->getRequerido(), PDO::PARAM_STR);
			$query->bindValue(':acao', $processo->getAcao(), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($processo->getJornal()))
				$query->bindValue(':jornal_ID', $processo->getJornal()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':jornal_ID', NULL, PDO::PARAM_NULL);

			//vincula jornal a secretaria selecionada
			if (!DataValidator::isEmpty($processo->getJornal()))
				JornalModel::vinculaSecretaria($processo, $db);

			if (!DataValidator::isEmpty($processo->getSecretaria()))
				$query->bindValue(':secretaria_ID', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':secretaria_ID', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($processo->getAdvogado()))
				$query->bindValue(':advogado_ID', $processo->getAdvogado()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':advogado_ID', NULL, PDO::PARAM_NULL);

			$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
			$query->execute();

			self::verificaAlertaParaSinalizador($processo, $db);
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function updateFromAcompanhamento($processo, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		try {

			if (DataValidator::isEmpty($processo))
				throw new UserException('Update Processo: o Processo deve ser fornecido.');

			if (DataValidator::isEmpty($processo->getId()))
				throw new UserException('O Processo deve ser identificado.');

			$sql = " UPDATE processo SET requerente = :requerente,
										  requerido = :requerido, 
										  acao = :acao,
										  jornal_ID = :jornal_ID,
										  secretaria_ID = :secretaria_ID,
										  advogado_ID = :advogado_ID
										  WHERE id = :processo_id; ";

			$query = $db->prepare($sql);
			$query->bindValue(':requerente', $processo->getRequerente(), PDO::PARAM_STR);
			$query->bindValue(':requerido', $processo->getRequerido(), PDO::PARAM_STR);
			$query->bindValue(':acao', $processo->getAcao(), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($processo->getJornal()))
				$query->bindValue(':jornal_ID', $processo->getJornal()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':jornal_ID', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($processo->getSecretaria()))
				$query->bindValue(':secretaria_ID', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':secretaria_ID', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($processo->getAdvogado()))
				$query->bindValue(':advogado_ID', $processo->getAdvogado()->getId(), PDO::PARAM_INT);
			else
				$query->bindValue(':advogado_ID', NULL, PDO::PARAM_NULL);

			$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//*******************//

	public static function alteraStatus($processo_id, $status = null, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		try {

			if (DataValidator::isEmpty($processo_id))
				throw new UserException('Altera Status: O Processo deve ser identificado.');

			if (DataValidator::isEmpty($status))
				throw new UserException('Altera Status: O Sinalizador deve ser fornecido.');

			$sql = " UPDATE processo SET status_processo = :status
										  WHERE id = :processo_id; ";

			$query = $db->prepare($sql);
			$query->bindValue(':status', $status, PDO::PARAM_STR);
			$query->bindValue(':processo_id', $processo_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function alteraSinalizador($processo_id, $sinalizador = null, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		try {

			if (DataValidator::isEmpty($processo_id))
				throw new UserException('Altera Sinalizador: O Processo deve ser identificado.');

			if (DataValidator::isEmpty($sinalizador))
				throw new UserException('Altera Sinalizador: O Sinalizador deve ser fornecido.');

			$sql = " UPDATE processo SET sinalizador = :sinalizador
										  WHERE id = :processo_id; ";

			$query = $db->prepare($sql);
			$query->bindValue(':sinalizador', $sinalizador, PDO::PARAM_STR);
			$query->bindValue(':processo_id', $processo_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function exclui($entrada_id)
	{

		$msg = null;
		$processoModel = new ProcessoModel();

		try {
			if (DataValidator::isEmpty($entrada_id))
				throw new UserException('Exclui Processo: A Entrada deve ser identificada.');

			$sql = ' DELETE FROM processo_entrada WHERE id=:entrada_id ';
			$query = $processoModel->getDB()->prepare($sql);
			$query->bindValue(':entrada_id', $entrada_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//recebe array de entradas
	public static function excluiProcessos($entradas)
	{

		$msg = null;
		$processoModel = new ProcessoModel();

		try {
			if (DataValidator::isEmpty($entradas))
				throw new UserException('Exclui Processos: As Entradas devem ser identificadas.');

			foreach ($entradas as $entrada_id) {
				$sql = ' DELETE FROM processo_entrada WHERE id=:entrada_id ';
				$query = $processoModel->getDB()->prepare($sql);
				$query->bindValue(':entrada_id', $entrada_id, PDO::PARAM_INT);
				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//verificação se o numero do processo existe no mesmo Estado
	public static function getByEstado($entrada, $db = null)
	{
		$processo = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($entrada))
			throw new UserException('Processos duplicados: A Entrada deve ser fornecida.');

		if (DataValidator::isEmpty($entrada->getEstado()))
			throw new UserException('Processos duplicados: O Estado deve ser fornecido.');

		$sql = " SELECT pr.id from processo pr
					INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id 
					WHERE pe.numero LIKE :numero AND pe.estado = :estado ";

		$query = $db->prepare($sql);
		$query->bindValue(':numero', "%" . $entrada->getNumero() . "%", PDO::PARAM_STR);
		$query->bindValue(':estado', $entrada->getEstado(), PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$processo = new Processo();
		$processo->setId($linha->id);

		return $processo;
	}

	//verificação se o numero do processo existe no mesmo Estado
	public static function getNumeroRepetido($processo, $db = null)
	{
		$processos = null;

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Processos duplicados: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Processos duplicados: O Processo deve ser identificado.');

		if (DataValidator::isEmpty($processo->getEntrada()))
			throw new UserException('Processos duplicados: A Entrada deve ser fornecida.');

		$sql = " SELECT pr.id, pr.status_processo 
					 from processo pr
					 INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id 
					 WHERE pe.numero =:numero AND pr.id <> :processo_id";

		$query = $db->prepare($sql);
		$query->bindValue(':numero', $processo->getEntrada()->getNumero(), PDO::PARAM_STR);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {

			$processo = new ProcessoRepetido();
			$processo->setId($linha->id);
			$processo->setStatus($linha->status_processo);
			$processos[] = $processo;
		}

		return $processos;
	}

	//na inserção de Proposta: verifica se existem alertas no processo, para alterar sinalizador para Amarelo
	public static function verificaAlertaParaSinalizador($processo, $db = null)
	{

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Verifica Alertas: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Verifica Alertas: O Processo deve ser identificado.');

		$alertas = AlertaModel::lista($processo, $db);
		
		if (count($alertas) <= 0)
			$sinalizador = 'V';
		else
			$sinalizador = 'A';

		$sql = " UPDATE processo SET sinalizador = :sinalizador
									  WHERE id = :processo_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':sinalizador', $sinalizador, PDO::PARAM_STR);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
		$query->execute();
	}

	//atualiza a contagem de alertas no registro do processo 
	public static function atualizaQtdAlertas($processo, $db = null)
	{

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Atualiza Alertas: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Atualiza Alertas: O Processo deve ser identificado.');

		$sql = " UPDATE processo SET alertas = Alertas(:processo_id) WHERE id = :processo_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
		$query->execute();
	}

	//**********//

	//no update do Processo: altera o processo e verifica se tem alertas para já redireciona para proposta
	public static function verificaAlertaParaStatus($processo, $db = null)
	{

		if (is_null($db)) {
			$processoModel = new ProcessoModel();
			$db = $processoModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Verifica Alertas: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Verifica Alertas: O Processo deve ser identificado.');

		$alertas = AlertaModel::lista($processo, $db);
		if (count($alertas) <= 0)
			$status = 'S';
		else
			$status = 'P';

		//echo 'status: ' . $status;

		$sql = " UPDATE processo SET status_processo = :status
									  WHERE id = :processo_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':status', $status, PDO::PARAM_STR);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
		$query->execute();
	}
}
