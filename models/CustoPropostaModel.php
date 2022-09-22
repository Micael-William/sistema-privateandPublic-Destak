<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/CustoProposta.class.php");

class CustoPropostaModel extends PersistModelAbstract
{

	public static function getById($proposta_id = null, $db = null)
	{

		$custos = array();

		if (is_null($db)) {
			$custoModel = new CustoPropostaModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($proposta_id))
			throw new UserException('Custo Proposta: A Proposta deve ser identificada.');

		$sql = " SELECT pc.* 
					 FROM proposta p
					 INNER JOIN proposta_custo pc ON pc.proposta_ID=p.id
					 WHERE pc.proposta_ID=:proposta_id
					 ORDER BY status DESC
			";

		$query = $db->prepare($sql);
		$query->bindValue(':proposta_id', $proposta_id, PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {

			$custo = new CustoProposta();
			$custo->setId($linha->id);
			$custo->setQuantidade($linha->quantidade);
			$custo->setValorPadrao($linha->valor_padrao);
			$custo->setValorDje($linha->valor_dje);
			$custo->setValorFinal($linha->valor_final);
			$custo->setAceite($linha->aceite);
			$custo->setStatus($linha->status);

			$custos['valor_' . $linha->status] = $custo;
		}

		return $custos;
	}

	public static function excluiCusto($custo_id)
	{

		$msg = null;
		$custoModel = new CustoPropostaModel();

		try {
			if (DataValidator::isEmpty($custo_id))
				throw new UserException('Exclui Custo: O Custo deve ser identificado.');

			$sql = ' DELETE FROM proposta_custo WHERE id=:custo_id ';
			$query = $custoModel->getDB()->prepare($sql);
			$query->bindValue(':custo_id', $custo_id, PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function excluiCustos($proposta, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoPropostaModel();
			$db = $custoModel->getDB();
		}

		try {
			if (DataValidator::isEmpty($proposta))
				throw new UserException('Exclui Custos: A Proposta deve ser fornecida.');

			if (DataValidator::isEmpty($proposta->getId()))
				throw new UserException('Exclui Custos: A Proposta deve ser identificada.');

			$sql = ' DELETE FROM proposta_custo WHERE proposta_ID=:proposta_id ';
			$query = $db->prepare($sql);
			$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);
			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function save($proposta, $db = null, $origem = null)
	{

		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoPropostaModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($proposta))
			throw new UserException('Custo Proposta: A Proposta deve ser fornecida.');

		if (DataValidator::isEmpty($proposta->getId()))
			throw new UserException('Custo Proposta: A Proposta deve ser identificada.');

		$custos = $proposta->getCustos();

		if (!DataValidator::isEmpty($custos)) {

			foreach ($custos as $custo) {

				$v_padrao = 0;
				$v_dje = 0;
				$v_final = 0;

				$val_padrao = str_replace(".", "", $custo->getValorPadrao());
				$v_padrao = str_replace(",", ".", $val_padrao);

				$val_dje = str_replace(".", "", $custo->getValorDje());
				$v_dje = (!DataValidator::isEmpty($custo->getValorDje())) ? str_replace(",", ".", $val_dje) : "0.00";

				$val_final = str_replace(".", "", $custo->getValorFinal());
				$v_final = str_replace(",", ".", $val_final);

				if (DataValidator::isEmpty($custo->getId())) {

					if (!DataValidator::isEmpty($v_final)) {

						$sql = ' INSERT INTO proposta_custo 
											(quantidade, 
											 valor_padrao, 
											 valor_dje, 
											 valor_final, 
											 aceite, 
											 status,
											 proposta_id) 
											VALUES(
												:quantidade, 
												:valor_padrao, 
												:valor_dje, 
												:valor_final, 
												:aceite, 
												:status,
												:proposta_id) 
								   ';

						$query = $db->prepare($sql);

						$query->bindValue(':quantidade', $custo->getQuantidade(), PDO::PARAM_INT);
						$query->bindValue(':valor_padrao', $v_padrao, PDO::PARAM_STR);
						$query->bindValue(':valor_dje', $v_dje, PDO::PARAM_STR);
						$query->bindValue(':valor_final', $v_final, PDO::PARAM_STR);
						$query->bindValue(':aceite', $custo->getAceite(), PDO::PARAM_STR);
						$query->bindValue(':status', $custo->getStatus(), PDO::PARAM_STR);
						$query->bindValue(':proposta_id', $proposta->getId(), PDO::PARAM_INT);
						$query->execute();
					}
				} else {

					if (DataValidator::isEmpty($v_final) && DataValidator::isEmpty($origem))
						self::excluiCustos($proposta, $db);

					else {

						$sql = ' UPDATE proposta_custo SET quantidade=:quantidade, 
														 valor_padrao=:valor_padrao, 
														 valor_dje=:valor_dje, 
														 valor_final=:valor_final, 
														 aceite=:aceite,
														 status=:status
														 WHERE id=:custo_id;
														 ';

						$query = $db->prepare($sql);
						$query->bindValue(':quantidade', $custo->getQuantidade(), PDO::PARAM_INT);
						$query->bindValue(':valor_padrao', $v_padrao, PDO::PARAM_STR);
						$query->bindValue(':valor_dje', $v_dje, PDO::PARAM_STR);
						$query->bindValue(':valor_final', $v_final, PDO::PARAM_STR);
						$query->bindValue(':aceite', $custo->getAceite(), PDO::PARAM_STR);
						$query->bindValue(':status', $custo->getStatus(), PDO::PARAM_STR);
						$query->bindValue(':custo_id', $custo->getId(), PDO::PARAM_INT);
						$query->execute();
					}
				}
			}
		}
	}
}
