<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/CustoAcompanhamento.class.php");

class CustoAcompanhamentoModel extends PersistModelAbstract
{

	public static function getById($acompanhamento_id = null, $db = null)
	{

		$custo = null;

		if (is_null($db)) {
			$custoModel = new CustoAcompanhamentoModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento_id))
			throw new UserException('Custo Acompanhamento: O Acompanhamento deve ser identificado.');

		$sql = " SELECT ac.* 
					 FROM acompanhamento a
					 INNER JOIN acompanhamento_custo ac ON ac.acompanhamento_ID=a.id
					 WHERE ac.acompanhamento_ID=:acompanhamento_id
			";

		$query = $db->prepare($sql);
		$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);

		$query->execute();
		$linha = $query->fetchObject();

		$custo = new CustoAcompanhamento();
		$custo->setQuantidadePadrao($linha->quantidade_padrao);
		$custo->setQuantidadeDje($linha->quantidade_dje);
		$custo->setValorPadrao($linha->valor_padrao);
		$custo->setValorDje($linha->valor_dje);
		$custo->setValorFinal($linha->valor_final);

		return $custo;
	}

	//salva custos do acompanhamento. Salva os dois valores, pois no acompanhamento existem os 2 custos juntos. 
	public static function update($acompanhamento, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoAcompanhamentoModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento))
			throw new UserException('Custo Acompanhamento: O Acompanhamento deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getId()))
			throw new UserException('Custo Acompanhamento: O Acompanhamento deve ser identificado.');

		$v_padrao = 0;
		$v_dje = 0;
		$v_final = 0;

		$val_padrao = str_replace(".", "", !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto()->getValorPadrao() : 0);
		$v_padrao = str_replace(",", ".", $val_padrao);

		$val_dje = str_replace(".", "", !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto()->getValorDje() : 0);
		$v_dje = str_replace(",", ".", $val_dje);

		$val_final = str_replace(".", "", !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto()->getValorFinal() : 0);
		$v_final = str_replace(",", ".", $val_final);

		$sql = ' UPDATE acompanhamento_custo SET quantidade_padrao=:quantidade_padrao, 
												 quantidade_dje=:quantidade_dje,
												 valor_padrao=:valor_padrao, 
												 valor_dje=:valor_dje, 
												 valor_final=:valor_final													
												 WHERE acompanhamento_ID=:acompanhamento_id;
												 ';

		$query = $db->prepare($sql);
		$query->bindValue(':quantidade_padrao', !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto()->getQuantidadePadrao() : 0, PDO::PARAM_INT);
		$query->bindValue(':quantidade_dje', !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto()->getQuantidadeDje() : 0, PDO::PARAM_INT);
		$query->bindValue(':valor_padrao', $v_padrao, PDO::PARAM_STR);
		$query->bindValue(':valor_dje', $v_dje, PDO::PARAM_STR);
		$query->bindValue(':valor_final', $v_final, PDO::PARAM_STR);
		$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);
		$query->execute();
	}

	//Insere custos da proposta em uma unica linha no acompanhamento, já que no acompanhamento os custos estão misturados
	public static function insert($acompanhamento, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoAcompanhamentoModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento))
			throw new UserException('Custo Acompanhamento: O Acompanhamento deve ser fornecido.');

		if (DataValidator::isEmpty($acompanhamento->getId()))
			throw new UserException('Custo Acompanhamento: O Acompanhamento deve ser identificado.');

		$custos = $acompanhamento->getProposta()->getCustos();
		if (!DataValidator::isEmpty($custos)) {

			$custo_padrao = isset($custos[0]) ? $custos[0] : null;
			$custo_dje = isset($custos[1]) ? $custos[1] : null;

			$qtd_padrao = 0;
			$v_padrao = 0;
			$v_dje = 0;
			$qtd_dje = 0;
			$v_final = 0;

			if (!DataValidator::isEmpty($custo_padrao)) {
				$val_padrao = str_replace(".", "", $custo_padrao->getValorPadrao());
				$v_padrao = str_replace(",", ".", $val_padrao);

				$qtd_padrao = $custo_padrao->getQuantidade();
			}

			if (!DataValidator::isEmpty($custo_dje) && $custo_dje->getAceite() == 'A') {
				$val_dje = str_replace(".", "", $custo_dje->getValorDje());
				$v_dje = str_replace(",", ".", $val_dje);

				$qtd_dje = $custo_dje->getQuantidade();
			}

			$total_final = !DataValidator::isEmpty($custo_dje) && $custo_dje->getAceite() == 'A' ? $custo_dje->getValorFinal() : $custo_padrao->getValorFinal();

			$val_final = str_replace(".", "", $total_final);
			$v_final = str_replace(",", ".", $val_final);

			$sql = ' INSERT INTO acompanhamento_custo (quantidade_padrao, quantidade_dje, valor_padrao, valor_dje, valor_final, acompanhamento_ID) VALUES(:quantidade_padrao, :quantidade_dje, :valor_padrao, :valor_dje, :valor_final, :acompanhamento_id) ';

			$query = $db->prepare($sql);
			$query->bindValue(':quantidade_padrao', $qtd_padrao, PDO::PARAM_INT);
			$query->bindValue(':quantidade_dje', $qtd_dje, PDO::PARAM_INT);
			$query->bindValue(':valor_padrao', $v_padrao, PDO::PARAM_STR);
			$query->bindValue(':valor_dje', $v_dje, PDO::PARAM_STR);
			$query->bindValue(':valor_final', $v_final, PDO::PARAM_STR);
			$query->bindValue(':acompanhamento_id', $acompanhamento->getId(), PDO::PARAM_INT);
			$query->execute();
		}
	}
}
