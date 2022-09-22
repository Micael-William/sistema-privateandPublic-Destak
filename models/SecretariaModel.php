<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Usuario.class.php");
require_once("classes/Perfil.class.php");
require_once("classes/Secretaria.class.php");

class SecretariaModel extends PersistModelAbstract
{

	public static function lista(
		$status = null,
		$termo = null,
		$estado = null,
		$pagina = 0,
		$qtd_pagina = 0
	) {

		$sql = " SELECT SQL_CALC_FOUND_ROWS id, nome_secretaria, estado_secretaria, status_secretaria FROM secretaria ";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .=  "status_secretaria=:status ";
		}

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " nome_secretaria LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($estado)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " estado_secretaria	=:estado ";
		}

		$sql .= " ORDER BY nome_secretaria ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$secretarias = array();
		$secretariaModel = new SecretariaModel();

		$query = $secretariaModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$secretaria = new Secretaria();
			$secretaria->setId($linha->id);
			$secretaria->setNome($linha->nome_secretaria);
			$secretaria->setEstado($linha->estado_secretaria);
			$secretaria->setStatus($linha->status_secretaria);

			$secretarias[] = $secretaria;
		}

		$query = $secretariaModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$totalLinhas = $linha ? $linha->frows : 0;

		return array('secretarias' => $secretarias, 'totalLinhas' => $totalLinhas);
	}

	/*
		param: id ou nome
		*/
	public static function getBy($param = null, $db = null)
	{
		$secretaria = null;
		$param = trim($param);

		if (is_null($db)) {
			$secretariaModel = new SecretariaModel();
			$db = $secretariaModel->getDB();
		}

		if (DataValidator::isEmpty($param)) {
			if (is_numeric($param))
				throw new UserException('Secretaria: O Parâmetro deve ser fornecido.');
		} elseif ($param == '') //object vindo da entrada
			return null;

		$sql = " SELECT * from secretaria ";

		if (is_numeric($param))
			$sql .= " WHERE id=:param ";
		else {
			$sql .= " WHERE nome_secretaria LIKE _utf8 :param COLLATE utf8_unicode_ci ";
		}

		$query = $db->prepare($sql);

		if (is_numeric($param))
			$query->bindValue(':param', $param, PDO::PARAM_STR);
		else
			$query->bindValue(':param', "%$param%", PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$secretaria = new Secretaria();
		$secretaria->setId($linha->id);
		$secretaria->setNome($linha->nome_secretaria);
		$secretaria->setEstado($linha->estado_secretaria);
		$secretaria->setStatus($linha->status_secretaria);

		return $secretaria;
	}

	//verifica a existencia pelo nome, sem like.
	public static function getByNome($param = null, $estado = null, $db = null)
	{

		if (is_null($db)) {
			$secretariaModel = new SecretariaModel();
			$db = $secretariaModel->getDB();
		}

		if (DataValidator::isEmpty($param)) {
			throw new UserException('Secretaria: O nome da Secretaria deve ser fornecido.');
		}

		if (DataValidator::isEmpty($estado)) {
			throw new UserException('Secretaria: O Estado da Secretaria deve ser fornecido.');
		}

		$sql = " SELECT id, nome_secretaria
					 FROM secretaria
					 WHERE nome_secretaria = _utf8 :param COLLATE utf8_unicode_ci AND estado_secretaria=:estado
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':estado', $estado, PDO::PARAM_STR);
		$query->bindValue(':param', trim($param), PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if ($linha)
			return $linha->id;
		else
			return null;
	}

	public static function insert($secretaria)
	{

		$msg = null;

		try {

			$secretariaModel = new SecretariaModel();
			$secretariaModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($secretaria))
				throw new UserException('Secretaria: A Secretaria deve ser fornecida.');

			if (DataValidator::isEmpty($secretaria->getEstado()))
				throw new UserException('Secretaria: O campo Estado é obrigatório.');

			if (DataValidator::isEmpty($secretaria->getNome()))
				throw new UserException('Secretaria: O campo nome é obrigatório.');

			$sec_cadastrada = self::getByNome($secretaria->getNome(), $secretaria->getEstado(), $secretariaModel->getDB());
			if (DataValidator::isEmpty($sec_cadastrada)) {

				$sql = " INSERT INTO secretaria (nome_secretaria, estado_secretaria, status_secretaria) VALUES (:nome_secretaria, :estado_secretaria, :status_secretaria) ";

				$query = $secretariaModel->getDB()->prepare($sql);
				$query->bindValue(':nome_secretaria', trim($secretaria->getNome()), PDO::PARAM_STR);
				$query->bindValue(':estado_secretaria', $secretaria->getEstado(), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($secretaria->getStatus()))
					$query->bindValue(':status_secretaria', $secretaria->getStatus(), PDO::PARAM_STR);
				else
					$query->bindValue(':status_secretaria', 'A', PDO::PARAM_STR);

				$query->execute();

				$secretariaModel->getDB()->commit();

				$sec_cadastrada = self::getByNome($secretaria->getNome(), $secretaria->getEstado(), $secretariaModel->getDB());

				$secretaria->setId($sec_cadastrada);

				self::atualizaQtdAlertas($secretaria, $secretariaModel->getDB());
			} else {
				throw new UserException('Nome da Secretaria j&aacute; existente.');
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$secretariaModel->getDB()->rollback();
		}

		return array('msg' => $msg, 'secretaria_id' => $secretaria->getId());
	}

	public static function update($secretaria)
	{

		$msg = null;

		try {

			$secretariaModel = new SecretariaModel();
			$secretariaModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($secretaria))
				throw new UserException('Update Secretaria: a Secretaria deve ser fornecida.');

			if (DataValidator::isEmpty($secretaria->getId()))
				throw new UserException('A Secretaria deve ser identificada.');

			if (DataValidator::isEmpty($secretaria->getEstado()))
				throw new UserException('Secretaria: O campo Estado é obrigatório.');

			if (DataValidator::isEmpty($secretaria->getNome()))
				throw new UserException('Secretaria: O campo nome é obrigatório.');

			$sec_cadastrada = self::getByNome($secretaria->getNome(), $secretaria->getEstado(), $secretariaModel->getDB());
			if (DataValidator::isEmpty($sec_cadastrada)) {

				$sql = " UPDATE secretaria SET nome_secretaria = :nome,
                                          estado_secretaria = :estado, 
                                          status_secretaria = :status
                                          WHERE id = :secretaria_id; ";

				$query = $secretariaModel->getDB()->prepare($sql);
				$query->bindValue(':nome', trim($secretaria->getNome()), PDO::PARAM_STR);
				$query->bindValue(':estado', $secretaria->getEstado(), PDO::PARAM_STR);
				$query->bindValue(':status', $secretaria->getStatus(), PDO::PARAM_STR);
				$query->bindValue(':secretaria_id', $secretaria->getId(), PDO::PARAM_INT);
				$query->execute();

				$secretariaModel->getDB()->commit();

				self::atualizaQtdAlertas($secretaria, $secretariaModel->getDB());
			} else {
				throw new UserException('Não houve alteração no cadastro.');
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$secretariaModel->getDB()->rollback();
		}

		return array('msg' => $msg);
	}

	//Exclui a secretaria do jornal
	public static function exclui($jornal, $db = null)
	{

		if (is_null($db)) {
			$secretariaModel = new SecretariaModel();
			$db = $secretariaModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Exclui Secretarias - Jornal: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Exclui Secretarias - Jornal: O Jornal deve ser identificado.');

		$sql = ' DELETE FROM jornal_secretaria WHERE jornal_id=:jornal_id ';
		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();
	}

	public static function listaByEstado($estado)
	{

		$msg = null;

		if (DataValidator::isEmpty($estado))
			return null;

		$secretarias = array();
		$secretariaModel = new SecretariaModel();

		$sql = " SELECT * FROM secretaria WHERE estado_secretaria=:estado AND status_secretaria='A' ORDER BY nome_secretaria ";

		$query = $secretariaModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$secretaria = new Secretaria();
			$secretaria->setId($linha->id);
			$secretaria->setNome($linha->nome_secretaria);

			$secretarias[] = $secretaria;
		}

		return $secretarias;
	}

	//atualiza a contagem de alertas no registro do processo 
	public static function atualizaQtdAlertas($secretaria, $db = null)
	{

		if (is_null($db)) {
			$secretariaModel = new SecretariaModel();
			$db = $secretariaModel->getDB();
		}

		if (DataValidator::isEmpty($secretaria))
			throw new UserException('Atualiza Alertas: A Secretaria deve ser fornecida.');

		if (DataValidator::isEmpty($secretaria->getId()))
			throw new UserException('Atualiza Alertas: A Secretaria deve ser identificada.');

		$sql = " UPDATE processo SET alertas = Alertas(id) WHERE secretaria_ID = :secretaria_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':secretaria_id', $secretaria->getId(), PDO::PARAM_INT);
		$query->execute();
	}
}
