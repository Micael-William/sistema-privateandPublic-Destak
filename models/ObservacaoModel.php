<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Observacao.class.php");

class ObservacaoModel extends PersistModelAbstract
{

	//entidades: advogado, proposta, acompanhamento

	public static function getObservacoes($entidade, $db = null, $origem = null)
	{
		$observacoes = array();
		$sql = null;

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($entidade))
			throw new UserException('Observações: O Objeto deve ser fornecido.');

		if (DataValidator::isEmpty($entidade->getId()))
			throw new UserException('Observações: O Objeto deve ser identificado.');

		if ($entidade instanceof Advogado) {
			$sql = " SELECT ao.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
						 FROM  advogado_obs ao
						 INNER JOIN advogado adv ON ao.advogado_ID=adv.id
						 INNER JOIN usuario u ON ao.usuario_ID=u.id
						 LEFT JOIN usuario us ON ao.usuario_ID_envio=us.id
						 WHERE ao.advogado_ID=:advogado_id AND ao.deleted='N'
						 ORDER BY data_entrada DESC
						 ";
		}

		if ($entidade instanceof Processo) {
			$sql = " SELECT pc.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
					 FROM processo_obs pc
					 INNER JOIN usuario u ON pc.usuario_ID=u.id
					 LEFT JOIN usuario us ON pc.usuario_ID_envio=us.id
					 WHERE pc.processo_ID=:processo_id AND pc.deleted='N'
					 ORDER BY data_entrada DESC
					 ";
		}

		if ($entidade instanceof Proposta) {
			$sql = " SELECT po.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
					 FROM proposta_obs po
					 INNER JOIN proposta p ON po.proposta_ID=p.id
					 INNER JOIN usuario u ON po.usuario_ID=u.id
					 LEFT JOIN usuario us ON po.usuario_ID_envio=us.id
					 WHERE po.proposta_ID=:proposta_id AND po.deleted='N'
					 ORDER BY data_entrada DESC
					 ";
		}

		//obs do advogado no acompanhamento
		if ($entidade instanceof Acompanhamento && $origem == 'acomp-adv-obs') {
			$sql = " SELECT a_adv.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
					 FROM acompanhamento_adv_obs a_adv
					 INNER JOIN acompanhamento a ON a_adv.acompanhamento_ID=a.id
					 INNER JOIN usuario u ON a_adv.usuario_ID=u.id
					 LEFT JOIN usuario us ON a_adv.usuario_ID_envio=us.id
					 WHERE a_adv.acompanhamento_ID=:acompanhamento_id AND a_adv.deleted='N'
					 ORDER BY data_entrada DESC
					 ";
		}

		//obs do financeiro no acompanhamento
		if ($entidade instanceof Acompanhamento && $origem == 'acomp-fin-obs') {
			$sql = " SELECT a_fin.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
						FROM acompanhamento_fin_obs a_fin
						INNER JOIN acompanhamento a ON a_fin.acompanhamento_ID=a.id
						INNER JOIN usuario u ON a_fin.usuario_ID=u.id
						LEFT JOIN usuario us ON a_fin.usuario_ID_envio=us.id
						WHERE a_fin.acompanhamento_ID=:acompanhamento_id AND a_fin.deleted='N'
						ORDER BY data_entrada DESC
						";
		}

		//obs do acompanhamento
		if ($entidade instanceof Acompanhamento && $origem == 'acomp-obs') {
			$sql = " SELECT ao.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
					 FROM acompanhamento_obs ao
					 INNER JOIN acompanhamento a ON ao.acompanhamento_ID=a.id
					 INNER JOIN usuario u ON ao.usuario_ID=u.id
					 LEFT JOIN usuario us ON ao.usuario_ID_envio=us.id
					 WHERE ao.acompanhamento_ID=:acompanhamento_id AND ao.deleted='N'
					 ORDER BY data_entrada DESC
					 ";
		}

		//obs do boleto
		if ($entidade instanceof Boleto) {
			$sql = " SELECT po.*, u.nome_usuario as resp_cadastro, us.nome_usuario as resp_envio
					 FROM acompanhamento_boleto_obs po
					 INNER JOIN acompanhamento_boleto bo ON po.boleto_ID=bo.id
					 INNER JOIN usuario u ON po.usuario_ID=u.id
					 LEFT JOIN usuario us ON po.usuario_ID_envio=us.id
					 WHERE po.boleto_ID=:boleto_id AND po.deleted='N'
					 ORDER BY data_entrada DESC
					 ";
		}

		$query = $db->prepare($sql);

		if ($entidade instanceof Advogado)
			$query->bindValue(':advogado_id', $entidade->getId(), PDO::PARAM_INT);

		if ($entidade instanceof Processo)
			$query->bindValue(':processo_id', $entidade->getId(), PDO::PARAM_INT);

		if ($entidade instanceof Proposta)
			$query->bindValue(':proposta_id', $entidade->getId(), PDO::PARAM_INT);

		if ($entidade instanceof Acompanhamento)
			$query->bindValue(':acompanhamento_id', $entidade->getId(), PDO::PARAM_INT);

		if ($entidade instanceof Boleto)
			$query->bindValue(':boleto_id', $entidade->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$obs = new Observacao();
			$obs->setId($linha->id);
			$obs->setMensagem($linha->mensagem);
			$obs->setDataEntrada($linha->data_entrada);
			$obs->setStatus($linha->status);
			$obs->setDataEnvio($linha->data_envio);
			if (isset($linha->email_destino)) $obs->setEmailDestino($linha->email_destino);
			$obs->setUsuarioCadastroId($linha->usuario_ID);

			//usuario cadastro
			$obs->setRespEnvio($linha->resp_envio);
			$obs->setRespCadastro($linha->resp_cadastro);

			//usuario envio
			/*$usuario_envio = new Usuario();
				$usuario_envio->setId( $linha-> );
				$usuario_envio->setNome( $linha->nome_usuario );*/
			//$obs->setUsuarioEnvio( UsuarioModel::getById($linha->usuario_ID_envio, $db) );

			$observacoes[] = $obs;
		}
		
		return $observacoes;
	}

	public static function saveObservacao($entidade, $db = null, $origem = null)
	{
		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($entidade))
			throw new UserException('Salva Observações: O Objeto deve ser fornecido.');

		if (DataValidator::isEmpty($entidade->getId()))
			throw new UserException('Salva Observações: O Objeto deve ser identificado.');

		if ($entidade instanceof Acompanhamento && $origem == 'acomp-obs')
			$observacoes = $entidade->getObservacoesAcompanhamento();
		else if ($entidade instanceof Acompanhamento && $origem == 'acomp-fin-obs')
			$observacoes = $entidade->getObservacoesFinanceiro();
		else
			$observacoes = $entidade->getObservacoes();

		if (!DataValidator::isEmpty($observacoes)) {
			foreach ($observacoes as $obs) {

				if (!DataValidator::isEmpty($obs->getMensagem())) {

					if (DataValidator::isEmpty($obs->getId())) {

						//advogado
						if ($entidade instanceof Advogado) {
							$sql = " INSERT INTO advogado_obs (advogado_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:advogado_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':advogado_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						} //--advogado

						//processo
						if ($entidade instanceof Processo) {
							$sql = " INSERT INTO processo_obs (processo_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:processo_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':processo_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--processo

						//proposta
						if ($entidade instanceof Proposta) {
							$sql = " INSERT INTO proposta_obs (proposta_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:proposta_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':proposta_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--proposta

						//obs do advogado no acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-adv-obs') {
							$sql = " INSERT INTO acompanhamento_adv_obs (acompanhamento_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:acompanhamento_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':acompanhamento_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--acompanhamento

						//obs do financeiro no acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-fin-obs') {
							$sql = " INSERT INTO acompanhamento_fin_obs (acompanhamento_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:acompanhamento_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':acompanhamento_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--acompanhamento

						//obs do acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-obs') {
							$sql = " INSERT INTO acompanhamento_obs (acompanhamento_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:acompanhamento_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':acompanhamento_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--acompanhamento


						//obs do boleto
						if ($entidade instanceof Boleto) {
							$sql = " INSERT INTO acompanhamento_boleto_obs (boleto_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:boleto_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

							$query = $db->prepare($sql);
							$query->bindValue(':boleto_id', $entidade->getId(), PDO::PARAM_INT);
							$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

							$query->execute();
						}
						//--boleto

					} else {

						//advogado
						if ($entidade instanceof Advogado) {
							$sql = " UPDATE advogado_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'advogado', $db);

							if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
								$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
							else
								$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--advogado

						//proposta
						if ($entidade instanceof Processo) {
							$sql = " UPDATE processo_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'processo', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--proposta

						//proposta
						if ($entidade instanceof Proposta) {
							$sql = " UPDATE proposta_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'proposta', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--proposta

						//obs do advogado no acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-adv-obs') {
							$sql = " UPDATE acompanhamento_adv_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'acompanhamento_adv', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--acompanhamento


						//obs do financeiro no acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-fin-obs') {
							$sql = " UPDATE acompanhamento_fin_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'acompanhamento_fin', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--acompanhamento

						//obs do acompanhamento
						if ($entidade instanceof Acompanhamento && $origem == 'acomp-obs') {
							$sql = " UPDATE acompanhamento_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'acompanhamento_obs', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--acompanhamento


						//obs do boleto
						if ($entidade instanceof Boleto) {
							$sql = " UPDATE acompanhamento_boleto_obs SET usuario_ID=:usuario_id, data_alteracao=:data_alteracao, mensagem=:mensagem WHERE id=:obs_id";
							$query = $db->prepare($sql);

							$obs_cadastrada = self::getObservacaoById($obs, 'acompanhamento_boleto_obs', $db);

							if (!DataValidator::isEmpty($obs_cadastrada)) {
								if ($obs->getMensagem() != $obs_cadastrada->getMensagem())
									$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
								else
									$query->bindValue(':usuario_id', $obs_cadastrada->getUsuarioCadastroId(), PDO::PARAM_INT);
							}

							$query->bindValue(':obs_id', $obs->getId(), PDO::PARAM_INT);
							$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);
							$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

							$query->execute();
						} //--acompanhamento


					}
				}
			}
		}
	}

	public static function excluiObservacao($item_id, $origem = null)
	{

		$msg = null;
		$observacaoModel = new ObservacaoModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Observação: A Observação deve ser identificada.');

			if (DataValidator::isEmpty($origem))
				throw new UserException('Exclui Observação: A Origem deve ser fornecida.');

			if ($origem == 'advogado')
				$sql = " UPDATE advogado_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'processo')
				$sql = " UPDATE processo_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'proposta')
				$sql = " UPDATE proposta_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'acompanhamento-adv-obs')
				$sql = " UPDATE acompanhamento_adv_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'acompanhamento-fin-obs')
				$sql = " UPDATE acompanhamento_fin_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'acompanhamento-obs')
				$sql = " UPDATE acompanhamento_obs SET deleted=:deleted WHERE id=:item_id ";

			elseif ($origem == 'acompanhamento-boleto-obs')
				$sql = " UPDATE acompanhamento_boleto_obs SET deleted=:deleted WHERE id=:item_id ";

			$query = $observacaoModel->getDB()->prepare($sql);
			$query->bindValue(':deleted', 'S', PDO::PARAM_STR);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//verifica se o conteudo da mensagem foi alterado para cadastrar usuário responsável pela alteração
	public static function getObservacaoById($observacao, $origem = null, $db = null)
	{
		$obs = null;

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($observacao))
			throw new UserException('Observação deve ser fornecida.');

		if (DataValidator::isEmpty($observacao->getId()))
			throw new UserException('Observação identificada.');

		if (DataValidator::isEmpty($origem))
			throw new UserException('A Origem deve ser fornecida.');

		if ($origem == 'advogado') {
			$sql = " SELECT id, mensagem, usuario_ID
					 FROM advogado_obs
					 WHERE id=:obs_id; ";
		} elseif ($origem == 'processo') {
			$sql = " SELECT id, mensagem, usuario_ID
					 FROM processo_obs
					 WHERE id=:obs_id; ";
		} elseif ($origem == 'proposta') {
			$sql = " SELECT id, mensagem, usuario_ID
					 FROM proposta_obs
					 WHERE id=:obs_id; ";
		} elseif ($origem == 'acompanhamento_adv') {
			$sql = " SELECT id, mensagem, usuario_ID
					 FROM acompanhamento_adv_obs
					 WHERE id=:obs_id; ";
		} elseif ($origem == 'acompanhamento_fin') {
			$sql = " SELECT id, mensagem, usuario_ID
						FROM acompanhamento_fin_obs
						WHERE id=:obs_id; ";
		} elseif ($origem == 'acompanhamento_obs') {
			$sql = " SELECT id, mensagem, usuario_ID
					 FROM acompanhamento_obs
					 WHERE id=:obs_id; ";
		}elseif ($origem == 'acompanhamento_boleto_obs') {
			$sql = " SELECT id, mensagem, usuario_ID
			FROM acompanhamento_boleto_obs
			WHERE id=:obs_id; ";
		}

		$query = $db->prepare($sql);

		$query->bindValue(':obs_id', $observacao->getId(), PDO::PARAM_INT);

		$query->execute();
		$linha = $query->fetchObject();

		$obs = new Observacao();
		$obs->setId($linha->id);
		$obs->setMensagem($linha->mensagem);
		$obs->setUsuarioCadastroId($linha->usuario_ID);

		return $obs;
	}

	//*****************************************//		

	//advogado

	//insre observações do advogado, no cadastro da proposta
	public static function saveObservacaoFromAdvogado($advogado, $proposta, $db = null)
	{

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Salva Observação: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Salva Observação: O Advogado deve ser identificado.');

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Salva Observação: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Salva Observação: A Proposta deve ser identificada.');

		$observacoes = self::getObservacoes($advogado, $db);

		if (!DataValidator::isEmpty($observacoes)) {
			foreach ($observacoes as $obs) {

				$sql = " INSERT INTO proposta_obs (proposta_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:proposta_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

				$query = $db->prepare($sql);
				$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);
				$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
				$query->bindValue(':data_entrada', $obs->getDataEntrada(), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

				$query->execute();
			}
		}
	}

	//insre observações do advogado, no acompanhamento
	public static function saveObservacaoFromAdvogadoToAcomp($advogado, $acompanhamento, $db = null)
	{

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Salva Observação: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Salva Observação: O Advogado deve ser identificado.');

		if (DataValidator::isEmpty($acompanhamento))
			throw new UserException('Salva Observação: O Acompanhamento deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getId()))
			throw new UserException('Salva Observação: O Acompanhamento deve ser identificado.');

		$observacoes = self::getObservacoes($advogado, $db);

		if (!DataValidator::isEmpty($observacoes)) {
			foreach ($observacoes as $obs) {

				$sql = " INSERT INTO acompanhamento_adv_obs (acompanhamento_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:acompanhamento_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

				$query = $db->prepare($sql);
				$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);
				$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
				$query->bindValue(':data_entrada', $obs->getDataEntrada(), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

				$query->execute();
			}
		}
	}

	//**************************//

	//proposta

	public static function excluiObservacoes($proposta_id, $db = null)
	{

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($proposta_id))
			throw new UserException('Exclui Observações: A Proposta deve ser identificada.');

		$sql = " UPDATE proposta_obs SET deleted=:deleted WHERE proposta_ID=:proposta_id ";

		$query = $db->prepare($sql);
		$query->bindValue(':deleted', 'S', PDO::PARAM_STR);
		$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);

		$query->execute();
	}

	//insre observações da proposta, no cadastro de acompanhametno
	public static function saveObservacaoFromProposta($proposta, $acompanhamento_id, $db = null)
	{

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Salva Observação: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Salva Observação: A Proposta deve ser identificada.');

		if (DataValidator::isEmpty($acompanhamento_id))
			throw new UserException('Salva Observação: O Acompanhamento deve ser identificado.');

		$observacoes = self::getObservacoes($proposta, $db);

		if (!DataValidator::isEmpty($observacoes)) {
			foreach ($observacoes as $obs) {

				$sql = " INSERT INTO acompanhamento_adv_obs (acompanhamento_ID, usuario_ID, data_entrada, data_alteracao, mensagem) VALUES (:acompanhamento_id, :usuario_id, :data_entrada, :data_alteracao, :mensagem) ";

				$query = $db->prepare($sql);
				$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);
				$query->bindValue(':usuario_id', $obs->getUsuarioCadastroId(), PDO::PARAM_INT);
				$query->bindValue(':data_entrada', $obs->getDataEntrada(), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':mensagem', $obs->getMensagem(), PDO::PARAM_STR);

				$query->execute();
			}
		}
	}

	//*******************//

	//Acompanhamento
	public static function excluiObservacoesFromAcompanhamento($acompanhamento_id, $db = null)
	{

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento_id))
			throw new UserException('Exclui Observações: O Acompanhamento deve ser identificado.');

		$sql = " UPDATE acompanhamento_adv_obs SET deleted=:deleted WHERE acompanhamento_ID=:acompanhamento_id ";

		$query = $db->prepare($sql);
		$query->bindValue(':deleted', 'S', PDO::PARAM_STR);
		$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);

		$query->execute();
	}

	//retorna última observação do acompanhamento
	public static function ultimaObs($acompanhamento, $db = null)
	{
		$observacoes = null;

		if (is_null($db)) {
			$observacaoModel = new ObservacaoModel();
			$db = $observacaoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento))
			throw new UserException('Observações: O Acompanhamento deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getId()))
			throw new UserException('Observações: O Acompanhamento deve ser identificado.');

		$sql = " SELECT ao.*
					 FROM acompanhamento_obs ao
					 INNER JOIN acompanhamento a ON ao.acompanhamento_ID=a.id
					 INNER JOIN usuario u ON ao.usuario_ID=u.id
					 WHERE ao.acompanhamento_ID=:acompanhamento_id AND ao.deleted='N'
					 ORDER BY data_entrada DESC LIMIT 0,1
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);

		$query->execute();
		while ($linha = $query->fetchObject()) {
			$obs = new Observacao();
			$obs->setMensagem($linha->mensagem);
			$obs->setDataEntrada($linha->data_entrada);

			$observacoes[] = $obs;
		}

		return $observacoes;
	}
}
