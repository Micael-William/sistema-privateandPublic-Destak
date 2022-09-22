<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Jornal.class.php");

class CustoModel extends PersistModelAbstract
{

	public static function insert($jornal, $db = null)
	{
		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Custo Jornal: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Custo Jornal: O Jornal deve ser identificado.');

		if (DataValidator::isEmpty($jornal->getCusto()))
			throw new UserException('Custo Jornal: O Custo Jornal deve ser fornecido.');

		$sql = ' INSERT INTO jornal_custo (jornal_id, 
											   valor_padrao, 
											   medida, 
											   valor_forense, 
											   negociacao, 
											   desconto, 
											   valor_dje, 
											   valor_empregos, 
											   valor_publicidade) 
										VALUES (:jornal_id, 
												:valor_padrao,
												:medida,
												:valor_forense,
												:negociacao,
												:desconto,
												:valor_dje,
												:valor_empregos,
												:valor_publicidade													
												) ';

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);

		//$medida = str_replace(",", ".", $jornal->getCusto()->getMedida());

		$val_padrao = str_replace(".", "", $jornal->getCusto()->getValorPadrao());
		$valor_padrao = str_replace(",", ".", $val_padrao);

		//$val_forense = str_replace(".", "", $jornal->getCusto()->getValorForense());
		//$valor_forense = str_replace(",", ".", $val_forense);

		//$val_negociacao = str_replace(".", "", $jornal->getCusto()->getNegociacao());
		//$valor_negociacao = str_replace(",", ".", $val_negociacao);

		//$val_desconto = str_replace(".", "", $jornal->getCusto()->getDesconto());
		//$valor_desconto = str_replace(",", ".", $val_desconto);

		$val_dje = str_replace(".", "", $jornal->getCusto()->getValorDje());
		$valor_dje = str_replace(",", ".", $val_dje);

		//$val_empregos = str_replace(".", "", $jornal->getCusto()->getValorEmpregos());
		//$valor_empregos = str_replace(",", ".", $val_empregos);

		$val_public = str_replace(".", "", $jornal->getCusto()->getValorPublicidade());
		$valor_publicidade = str_replace(",", ".", $val_public);

		$query->bindValue(':medida', $jornal->getCusto()->getMedida(), PDO::PARAM_STR);
		$query->bindValue(':valor_padrao', $valor_padrao, PDO::PARAM_STR);
		$query->bindValue(':valor_forense', $jornal->getCusto()->getValorForense(), PDO::PARAM_STR);
		$query->bindValue(':negociacao', $jornal->getCusto()->getNegociacao(), PDO::PARAM_STR);
		$query->bindValue(':desconto', $jornal->getCusto()->getDesconto(), PDO::PARAM_STR);
		$query->bindValue(':valor_dje', $valor_dje, PDO::PARAM_STR);
		$query->bindValue(':valor_empregos', $jornal->getCusto()->getValorEmpregos(), PDO::PARAM_STR);
		$query->bindValue(':valor_publicidade', $valor_publicidade, PDO::PARAM_STR);
		$query->execute();
	}

	public static function update($jornal, $db = null)
	{
		$msg = null;

		if (is_null($db)) {
			$custoModel = new CustoModel();
			$db = $custoModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Custo Jornal: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Custo Jornal: O Jornal deve ser identificado.');

		if (DataValidator::isEmpty($jornal->getCusto()))
			throw new UserException('Custo Jornal: O Custo Jornal deve ser fornecido.');

		$sql = ' UPDATE jornal_custo SET valor_padrao=:valor_padrao, 
											 medida=:medida, 
											 valor_forense=:valor_forense, 
											 negociacao=:negociacao, 
											 desconto=:desconto, 
											 valor_dje=:valor_dje, 
											 valor_empregos=:valor_empregos, 
											 valor_publicidade=:valor_publicidade
											 WHERE jornal_id=:jornal_id;
											 ';

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);

		//$medida = str_replace(",", ".", $jornal->getCusto()->getMedida());

		$val_padrao = str_replace(".", "", $jornal->getCusto()->getValorPadrao());
		$valor_padrao = str_replace(",", ".", $val_padrao);

		//$val_forense = str_replace(".", "", $jornal->getCusto()->getValorForense());
		//$valor_forense = str_replace(",", ".", $val_forense);

		//$val_negociacao = str_replace(".", "", $jornal->getCusto()->getNegociacao());
		//$valor_negociacao = str_replace(",", ".", $val_negociacao);

		//$val_desconto = str_replace(".", "", $jornal->getCusto()->getDesconto());
		//$valor_desconto = str_replace(",", ".", $val_desconto);

		$val_dje = str_replace(".", "", $jornal->getCusto()->getValorDje());
		$valor_dje = str_replace(",", ".", $val_dje);

		//$val_empregos = str_replace(".", "", $jornal->getCusto()->getValorEmpregos());
		//$valor_empregos = str_replace(",", ".", $val_empregos);

		$val_public = str_replace(".", "", $jornal->getCusto()->getValorPublicidade());
		$valor_publicidade = str_replace(",", ".", $val_public);

		$query->bindValue(':medida', $jornal->getCusto()->getMedida(), PDO::PARAM_STR);
		$query->bindValue(':valor_padrao', $valor_padrao, PDO::PARAM_STR);
		$query->bindValue(':valor_forense', $jornal->getCusto()->getValorForense(), PDO::PARAM_STR);
		$query->bindValue(':negociacao', $jornal->getCusto()->getNegociacao(), PDO::PARAM_STR);
		$query->bindValue(':desconto', $jornal->getCusto()->getDesconto(), PDO::PARAM_STR);
		$query->bindValue(':valor_dje', $valor_dje, PDO::PARAM_STR);
		$query->bindValue(':valor_empregos', $jornal->getCusto()->getValorEmpregos(), PDO::PARAM_STR);
		$query->bindValue(':valor_publicidade', $valor_publicidade, PDO::PARAM_STR);
		$query->execute();
	}
}
