<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("classes/Perfil.class.php");
require_once("models/ResponsabilidadeModel.php");

class PerfilModel extends PersistModelAbstract
{

	public static function lista()
	{
		$sql = " SELECT * FROM perfil ORDER BY nome_perfil ";

		$perfis = array();
		$perfilModel = new PerfilModel();

		$query = $perfilModel->getDB()->prepare($sql);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$perfil = new Perfil();
			$perfil->setId($linha->id);
			$perfil->setNome($linha->nome_perfil);

			$perfis[] = $perfil;
		}

		return $perfis;
	}

	public static function getById($perfil_id)
	{
		$perfil = null;

		if (DataValidator::isEmpty($perfil_id))
			throw new UserException('O Perfil deve ser identificado.');

		$sql = " SELECT * FROM perfil WHERE id=:perfil_id ";

		$perfilModel = new PerfilModel();
		$query = $perfilModel->getDB()->prepare($sql);
		$query->bindValue(':perfil_id', $perfil_id, PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$perfil = new Perfil();
		$perfil->setId($linha->id);
		$perfil->setNome($linha->nome_perfil);

		return $perfil;
	}


	public static function insert($perfil)
	{
		$msg = null;

		$perfilModel = new PerfilModel();
		$perfilModel->getDB()->beginTransaction();

		try {

			if (!$perfil instanceof Perfil)
				throw new UserException('Insert Perfil: O objeto deve ser do tipo Perfil.');

			if (DataValidator::isEmpty($perfil))
				throw new UserException('O Perfil deve ser fornecido.');

			if (DataValidator::isEmpty($perfil->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			$sql = " INSERT INTO perfil (nome_perfil) VALUES (:nome_perfil) ";

			$query = $perfilModel->getDB()->prepare($sql);
			$query->bindValue(':nome_perfil', $perfil->getNome(), PDO::PARAM_STR);
			$query->execute();

			$perfil->setId($perfilModel->getDB()->lastInsertId());

			try {
				ResponsabilidadeModel::insert($perfil, $perfilModel->getDB());
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}

			$perfilModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$perfilModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function update($perfil)
	{
		$msg = null;

		$perfilModel = new PerfilModel();
		$perfilModel->getDB()->beginTransaction();

		try {

			if (!$perfil instanceof Perfil)
				throw new UserException('Update Perfil: O objeto deve ser do tipo Perfil.');

			if (DataValidator::isEmpty($perfil))
				throw new UserException('O Perfil deve ser fornecido.');

			if (DataValidator::isEmpty($perfil->getId()))
				throw new UserException('O Perfil deve ser identificado.');

			if (DataValidator::isEmpty($perfil->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			$sql = " UPDATE perfil SET nome_perfil=:nome_perfil WHERE id=:perfil_id ";

			$query = $perfilModel->getDB()->prepare($sql);
			$query->bindValue(':nome_perfil', $perfil->getNome(), PDO::PARAM_STR);
			$query->bindValue(':perfil_id', $perfil->getId(), PDO::PARAM_INT);
			$query->execute();

			try {
				ResponsabilidadeModel::insert($perfil, $perfilModel->getDB());
			} catch (UserException $e) {
				$msg = $e->getMessage();
			}

			$perfilModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$perfilModel->getDB()->rollback();
		}

		return $msg;
	}
}
