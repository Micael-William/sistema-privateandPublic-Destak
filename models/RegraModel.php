<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");

class RegraModel extends PersistModelAbstract
{

	public static function lista($estado = null, $db = null)
	{

		$regras = null;

		if (is_null($db)) {
			$regraModel = new RegraModel();
			$db = $regraModel->getDB();
		}

		if (DataValidator::isEmpty($estado))
			throw new UserException('Regra: O campo Estado é obrigatório.');

		$sql = "SELECT * FROM regra WHERE status_regra='A' AND estado=:estado ORDER BY sinal_regra, inicio ";

		$query = $db->prepare($sql);
		$query->bindValue(':estado', $estado, PDO::PARAM_STR);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$regras[] = array('sinal' => $linha->sinal_regra, 'inicio' => $linha->inicio, 'termino' => $linha->termino, 'tamanho' => $linha->tamanho);
		}

		return $regras;
	}
}
