<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("models/PropostaModel.php");

class RelatorioFinanceiroModel extends PersistModelAbstract
{
	public static function boletosEmitidos($data_de = null, $data_ate = null)
	{

		$sql = " SELECT COUNT(*) AS qtde, SUM(bo.iugu_valor) AS total, pe.estado
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID ";

		$where = false;

		if (!DataValidator::isEmpty($data_de)) {

			$de = explode('/', $data_de);
			$data_de = $de[2] . '-' . $de[1] . '-' . $de[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento >= :data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento <= :data_ate ";
		}

		$sql .= " GROUP BY pe.estado ";

		$relatorioModel = new RelatorioFinanceiroModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		$items = array();

		while ($linha = $query->fetchObject()) {
			$items[] = array(
				'estado' => $linha->estado,
				'qtde' => $linha->qtde,
				'total' => $linha->total
			);
		}

		return array('resultado' => $items);
	}

	public static function boletosPagos($data_de = null, $data_ate = null)
	{

		$sql = " SELECT COUNT(*) AS qtde, SUM(bo.iugu_valor) AS total, pe.estado
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID 
				 WHERE bo.iugu_status IN('paid') ";

		$where = true;

		if (!DataValidator::isEmpty($data_de)) {

			$de = explode('/', $data_de);
			$data_de = $de[2] . '-' . $de[1] . '-' . $de[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento >= :data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento <= :data_ate ";
		}

		$sql .= " GROUP BY pe.estado ";

		$relatorioModel = new RelatorioFinanceiroModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		$items = array();

		while ($linha = $query->fetchObject()) {
			$items[] = array(
				'estado' => $linha->estado,
				'qtde' => $linha->qtde,
				'total' => $linha->total
			);
		}

		return array('resultado' => $items);
	}

	public static function boletosVencidos($data_de = null, $data_ate = null)
	{

		$sql = " SELECT COUNT(*) AS qtde, SUM(bo.iugu_valor) AS total, pe.estado
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID 
				 WHERE bo.iugu_status IN('expired') ";

		$where = true;

		if (!DataValidator::isEmpty($data_de)) {

			$de = explode('/', $data_de);
			$data_de = $de[2] . '-' . $de[1] . '-' . $de[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento >= :data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento <= :data_ate ";
		}

		$sql .= " GROUP BY pe.estado ";

		$relatorioModel = new RelatorioFinanceiroModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		$items = array();

		while ($linha = $query->fetchObject()) {
			$items[] = array(
				'estado' => $linha->estado,
				'qtde' => $linha->qtde,
				'total' => $linha->total
			);
		}

		return array('resultado' => $items);
	}

	public static function boletosPendentes($data_de = null, $data_ate = null)
	{

		$sql = " SELECT COUNT(*) AS qtde, SUM(bo.iugu_valor) AS total, pe.estado
					FROM acompanhamento_boleto bo 
					INNER JOIN acompanhamento ac ON bo.acompanhamento_ID=ac.id
					INNER JOIN proposta pt ON pt.id = ac.proposta_ID 
					INNER JOIN processo pc ON pc.id = pt.processo_ID
					INNER JOIN processo_entrada pe ON pe.id=pc.entrada_ID 
				 WHERE bo.iugu_status NOT IN('expired','canceled','paid') ";

		$where = true;

		if (!DataValidator::isEmpty($data_de)) {

			$de = explode('/', $data_de);
			$data_de = $de[2] . '-' . $de[1] . '-' . $de[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento >= :data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			$sql .= " bo.iugu_vencimento <= :data_ate ";
		}

		$sql .= " GROUP BY pe.estado ";

		$relatorioModel = new RelatorioFinanceiroModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		$items = array();

		while ($linha = $query->fetchObject()) {
			$items[] = array(
				'estado' => $linha->estado,
				'qtde' => $linha->qtde,
				'total' => $linha->total
			);
		}

		return array('resultado' => $items);
	}
}
