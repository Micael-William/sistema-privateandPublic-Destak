<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("classes/Responsabilidade.class.php");

class ResponsabilidadeModel extends PersistModelAbstract
{

	//retorna todas as responsabilidades
	public static function lista()
	{

		$responsabilidadeModel = new ResponsabilidadeModel();

		$sql = " SELECT * FROM responsabilidade ORDER BY id; ";

		$responsabilidades = array();
		$query = $responsabilidadeModel->getDB()->prepare($sql);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$responsabilidade = new Responsabilidade();
			$responsabilidade->setId($linha->id);
			$responsabilidade->setNome($linha->nome_responsabilidade);

			$responsabilidades[] = $responsabilidade;
		}

		return $responsabilidades;
	}

	//retorna as responsabilidades, com prosicao, de acordo com o perfil - gerenciamento de niveis
	public static function get_perfil_responsabilidades($perfil_id, $db = null)
	{
		$responsabilidades = null;

		if (is_null($db)) {
			$responsabilidadeModel = new ResponsabilidadeModel();
			$db = $responsabilidadeModel->getDB();
		}

		if (DataValidator::isEmpty($perfil_id))
			return $responsabilidades;

		$sql = " SELECT r.id, pr.acao
					 FROM perfil_responsabilidade pr
					 INNER JOIN responsabilidade r ON pr.responsabilidade_ID=r.id
					 WHERE pr.perfil_id=:perfil_id; ";


		$query = $db->prepare($sql);
		$query->bindValue(':perfil_id', $perfil_id, PDO::PARAM_INT);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$id = $linha->id;
			$responsabilidades['posicao' . $id] = array('resp_id' => $id, 'acao' => $linha->acao);
		}

		return $responsabilidades;
	}

	//retorna responsabilidades - verificação de niveis acesso para login
	public static function get_responsabilidades($perfil_id, $db = null)
	{
		$responsabilidades = null;

		if (is_null($db)) {
			$responsabilidadeModel = new ResponsabilidadeModel();
			$db = $responsabilidadeModel->getDB();
		}

		if (DataValidator::isEmpty($perfil_id))
			throw new UserException('O perfil deve ser identificado.');

		$sql = " SELECT r.id, r.nome_responsabilidade, pr.acao, p.nome_perfil
					 FROM perfil_responsabilidade pr
					 INNER JOIN responsabilidade r ON pr.responsabilidade_ID=r.id
					 INNER JOIN perfil p ON pr.perfil_ID=p.id
					 WHERE pr.perfil_ID=:perfil_id; ";


		$query = $db->prepare($sql);
		$query->bindValue(':perfil_id', $perfil_id, PDO::PARAM_INT);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$responsabilidades[$linha->id] = array('acao' => $linha->acao, 'nome_perfil' => $linha->nome_perfil);
		}

		return $responsabilidades;
	}

	public static function insert($perfil, $db = null)
	{

		if (is_null($db)) {
			$responsabilidadeModel = new ResponsabilidadeModel();
			$db = $responsabilidadeModel->getDB();
		}

		if (DataValidator::isEmpty($perfil))
			throw new UserException('Insere Resp. - O Perfil deve ser fornecido.');

		if (DataValidator::isEmpty($perfil->getId()))
			throw new UserException('Insere Resp. - O Perfil deve ser identificado.');

		$responsabilidades = $perfil->getResponsabilidades();

		self::exclui($perfil, $db);

		if (!DataValidator::isEmpty($responsabilidades)) {
			foreach ($responsabilidades as $r) {
				$sql = ' INSERT INTO perfil_responsabilidade (perfil_ID, responsabilidade_ID, acao) VALUES (:perfil_id, :responsabilidade_id, :acao) ';
				$query = $db->prepare($sql);
				$query->bindValue(':acao', $r->getAcao(), PDO::PARAM_STR);
				$query->bindValue(':perfil_id', $perfil->getId(), PDO::PARAM_INT);
				$query->bindValue(':responsabilidade_id', $r->getId(), PDO::PARAM_INT);
				$query->execute();
			}
		}
	}

	public static function exclui($perfil, $db = null)
	{

		if (DataValidator::isEmpty($perfil))
			throw new UserException('Exclui Resp. - O Perfil deve ser fornecido.');

		if (DataValidator::isEmpty($perfil->getId()))
			throw new UserException('Exclui Resp. - O Perfil deve ser identificado.');

		if (is_null($db)) {
			$responsabilidadeModel = new ResponsabilidadeModel();
			$db = $responsabilidadeModel->getDB();
		}

		if (!DataValidator::isEmpty($perfil->getId())) {
			$sql = ' DELETE FROM perfil_responsabilidade WHERE perfil_id=:perfil_id ';
			$query = $db->prepare($sql);
			$query->bindValue(':perfil_id', $perfil->getId(), PDO::PARAM_INT);
			$query->execute();
		}
	}
}
