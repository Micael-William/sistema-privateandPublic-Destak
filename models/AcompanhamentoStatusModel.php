<?php
require_once("lib/PersistModelAbstract.php");
require_once("classes/AcompanhamentoStatus.class.php");

class AcompanhamentoStatusModel extends PersistModelAbstract
{

	public static function lista($parent_status = null, $termo = null)
	{
		$sql = "SELECT ast.*, 
					pst.nome_status AS parent_nome_status  
				FROM acompanhamento_status ast 
			LEFT JOIN acompanhamento_status pst ON ast.parent_id = pst.id ";

		$sql .= "WHERE ast.parent_id <> 0 ";

		if (!DataValidator::isEmpty($parent_status)) {
			$sql .= "AND ast.parent_id =:parent_status ";
		}

		if (!DataValidator::isEmpty($termo)) {
			$sql .= "AND ast.nome_status LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		$sql .= " ORDER BY ast.id, ast.parent_id ";

		$acompanhamento_status = array();
		$statusModel = new AcompanhamentoStatusModel();

		$query = $statusModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($parent_status))
			$query->bindValue(':parent_status', $parent_status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$status = new AcompanhamentoStatus();
			$status->setId($linha->id);
			$status->setParentId($linha->parent_id);
			$status->setStatus($linha->nome_status);
			$status->setStatusPai($linha->parent_nome_status);
			$status->setDescricao($linha->descricao);

			$acompanhamento_status[] = $status;
		}

		return $acompanhamento_status;
	}

	public static function listaPai()
	{
		$sql = "SELECT ast.* FROM acompanhamento_status ast ";
		$sql .= "WHERE ast.parent_id = 0 ";
		$sql .= "ORDER BY ast.id";

		$acompanhamento_status_pai = array();
		$statusPaiModel = new AcompanhamentoStatusModel();

		$query = $statusPaiModel->getDB()->prepare($sql);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$statusPai = new AcompanhamentoStatus();
			$statusPai->setId($linha->id);
			$statusPai->setParentId($linha->parent_id);
			$statusPai->setCodigo($linha->codigo);
			$statusPai->setStatus($linha->nome_status);
			$statusPai->setStatusPai('');
			$statusPai->setDescricao($linha->descricao);

			$acompanhamento_status_pai[] = $statusPai;
		}

		return $acompanhamento_status_pai;
	}

	public static function listaByStatus($status_codigo)
	{

		$statusModel = new AcompanhamentoStatusModel();
		$db = $statusModel->getDB();

		if (DataValidator::isEmpty($status_codigo))
			return null;

		$substatuses = array();

		$sql = " SELECT 
					ast.*, 
					pst.nome_status AS parent_nome_status 
					FROM acompanhamento_status ast 
					INNER JOIN acompanhamento_status pst ON ast.parent_id = pst.id 
					WHERE pst.codigo LIKE :status_codigo;";

		$query = $db->prepare($sql);
		$query->bindValue(':status_codigo', $status_codigo, PDO::PARAM_STR);
		$query->execute();

		while ($linha = $query->fetchObject()) {

			$status = new AcompanhamentoStatus();
			$status->setId($linha->id);
			$status->setParentId($linha->parent_id);
			$status->setCodigo($linha->codigo);
			$status->setStatus($linha->nome_status);
			$status->setStatusPai($linha->parent_nome_status);
			$status->setDescricao($linha->descricao);

			$substatuses[] = $status;
		}

		return $substatuses;
	}

	public static function getById($status_id, $db = null)
	{

		if (is_null($db)) {
			$statusModel = new AcompanhamentoStatusModel();
			$db = $statusModel->getDB();
		}

		if (DataValidator::isEmpty($status_id))
			throw new UserException('O Status deve ser identificado.');

		$sql = "SELECT ast.*, 
				   	pst.nome_status AS parent_nome_status 
					FROM acompanhamento_status ast
				LEFT JOIN acompanhamento_status pst ON ast.parent_id = pst.id 
					WHERE ast.id=:status_id";

		$statusModel = new AcompanhamentoStatusModel();
		$query = $db->prepare($sql);
		$query->bindValue(':status_id', $status_id, PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$status = new AcompanhamentoStatus();
		$status->setId($linha->id);
		$status->setParentId($linha->parent_id);
		$status->setCodigo($linha->codigo);
		$status->setStatus($linha->nome_status);
		$status->setStatusPai($linha->parent_nome_status);
		$status->setDescricao($linha->descricao);

		return $status;
	}

	public static function getByCodigo($status_codigo, $db = null)
	{

		if (is_null($db)) {
			$statusModel = new AcompanhamentoStatusModel();
			$db = $statusModel->getDB();
		}

		if (DataValidator::isEmpty($status_codigo))
			throw new UserException('O Status deve ser identificado.');

		$sql = " SELECT ast.*
					FROM acompanhamento_status ast
					WHERE ast.codigo LIKE :status_codigo;";

		$statusModel = new AcompanhamentoStatusModel();
		$query = $db->prepare($sql);
		$query->bindValue(':status_codigo', $status_codigo, PDO::PARAM_STR);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$status = new AcompanhamentoStatus();
		$status->setId($linha->id);
		$status->setParentId($linha->parent_id);
		$status->setCodigo($linha->codigo);
		$status->setStatus($linha->nome_status);
		$status->setDescricao($linha->descricao);

		return $status;
	}



	public static function insert($status)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($status->getStatus()))
				throw new UserException('O campo Status é obrigatório.');

			$sql = " INSERT INTO acompanhamento_status (parent_id, nome_status, descricao) 
						 VALUES (:parent_id, :nome_status, :descricao) ";

			$statusModel = new AcompanhamentoStatusModel();
			$query = $statusModel->getDB()->prepare($sql);
			$query->bindValue(':parent_id', $status->getParentId(), PDO::PARAM_INT);
			$query->bindValue(':nome_status', $status->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':descricao', $status->getDescricao(), PDO::PARAM_STR);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function update($status)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($status->getParentId()))
				throw new UserException('O campo Status é obrigatório.');

			if (DataValidator::isEmpty($status->getStatus()))
				throw new UserException('O campo Substatus é obrigatório.');

			$sql = " UPDATE acompanhamento_status SET 
						 parent_id=:parent_id,
						 nome_status=:nome_status,
						 descricao=:descricao
					WHERE id=:status_id ";

			$statusModel = new AcompanhamentoStatusModel();
			$query = $statusModel->getDB()->prepare($sql);
			$query->bindValue(':status_id', $status->getId(), PDO::PARAM_INT);
			$query->bindValue(':parent_id', $status->getParentId(), PDO::PARAM_INT);
			$query->bindValue(':nome_status', $status->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':descricao', $status->getDescricao(), PDO::PARAM_STR);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function delete($id)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($id))
				throw new UserException('O Status deve ser identificado.');

			$statusModel = new AcompanhamentoStatusModel();
			$query = $statusModel->getDB()->prepare("DELETE FROM acompanhamento_status WHERE ID = :ID;");
			$query->bindValue(':ID', $id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}
}
