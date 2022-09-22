<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("models/PropostaModel.php");

class RelatorioModel extends PersistModelAbstract
{

	//Propostas envidas ou rejeitadas
	public static function propostas($status = null, $data_de = null, $data_ate = null)
	{

		$sql = " SELECT pe.estado
				 	 FROM processo_entrada pe
					 INNER JOIN processo pr ON pr.entrada_ID=pe.id AND pr.status_processo='S'
					 INNER JOIN proposta p ON p.processo_ID=pr.id				 
			";

		$total_sp = 0;
		$total_rj = 0;
		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.status_proposta =:status ";
		}

		if (!DataValidator::isEmpty($data_de)) {

			$de = explode('/', $data_de);
			$data_de = $de[2] . '-' . $de[1] . '-' . $de[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			if ($status == 'E') $sql .= " p.data_envio>=:data_de ";
			elseif ($status == 'R') $sql .= " p.data_rejeicao>=:data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}

			if ($status == 'E') $sql .= " p.data_envio<=:data_ate ";
			elseif ($status == 'R') $sql .= " p.data_rejeicao<=:data_ate ";
		}

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			if ($linha->estado == 'SP')
				$total_sp++;
			elseif ($linha->estado == 'RJ')
				$total_rj++;
		}

		return array('total_sp' => $total_sp, 'total_rj' => $total_rj);
	}

	//Acompanhamentos Processuais concluidos
	public static function acompanhamentosConcluidos($data_de = null, $data_ate = null)
	{

		$sql = " SELECT pe.estado
				 	 FROM acompanhamento ap
					 INNER JOIN proposta p ON ap.proposta_ID=p.id
					 INNER JOIN processo pr ON p.processo_ID=pr.id AND pr.status_processo='A'	
					 INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id
			";

		$total_sp = 0;
		$total_rj = 0;
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
			$sql .= " ap.data_conclusao>=:data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " ap.data_conclusao<=:data_ate ";
		}

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= " ap.status_acompanhamento='C' ";

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			if ($linha->estado == 'SP')
				$total_sp++;
			elseif ($linha->estado == 'RJ')
				$total_rj++;
		}

		return array('total_sp' => $total_sp, 'total_rj' => $total_rj);
	}

	//Acompanhamentos por Usuários
	public static function acompanhamentosUsuarios($data_de = null, $data_ate = null)
	{
		/*	
                    SELECT 
                            pe.estado, 
                            us.nome_usuario,
                            count(pe.estado)
                    FROM proposta p 
                            INNER JOIN processo pr ON p.processo_ID=pr.id 
                            INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id 
                            INNER JOIN usuario us ON p.usuario_ID_aceite = us.id
                    WHERE 
                            p.data_aceite>='2016-06-01' AND p.data_aceite<='2017-02-20 23:59:50' AND 
                            p.status_proposta='A' 
                    GROUP BY 
                            pe.estado, 
                            p.usuario_ID_aceite 
                    ORDER BY 
                            pe.estado,
                            p.usuario_ID_aceite  
                            
                */
		$sql = " SELECT 
                                        pe.estado, 
                                        us.nome_usuario,
                                        count(pe.estado) AS subtotal
                                    FROM proposta p 
                                        INNER JOIN processo pr ON p.processo_ID=pr.id 
                                        INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id 
                                        INNER JOIN usuario us ON p.usuario_ID_aceite = us.id
			";

		$total_sp = 0;
		$total_rj = 0;
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
			$sql .= " p.data_aceite>=:data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.data_aceite<=:data_ate ";
		}

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= " p.status_proposta='A' ";

		$sql .= "GROUP BY 
                                        pe.estado, 
                                        p.usuario_ID_aceite 
                                    ORDER BY 
                                        pe.estado,
                                        p.usuario_ID_aceite";

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		$estado_anterior = '';
		$registros = array();
		$totais = array();

		while ($linha = $query->fetchObject()) {

			if ($linha->estado != $estado_anterior) {
				$totais[$linha->estado] = 0;
				$registros[$linha->estado] = array();
			}

			$registros[$linha->estado][] = $linha->nome_usuario . "::" . $linha->subtotal;
			$totais[$linha->estado] += $linha->subtotal;
			$estado_anterior = $linha->estado;
		}

		return array('totais' => $totais, 'registros' => $registros);
	}

	//Acompanhamentos por data de aceite da proposta
	public static function acompanhamentosAceitos($data_de = null, $data_ate = null)
	{

		$sql = " SELECT pe.estado
				 	 FROM proposta p
					 INNER JOIN processo pr ON p.processo_ID=pr.id	
					 INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id
			";

		$total_sp = 0;
		$total_rj = 0;
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
			$sql .= " p.data_aceite>=:data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.data_aceite<=:data_ate ";
		}

		if (!$where) {
			$where = true;
			$sql .= "WHERE ";
		} else {
			$sql .= "AND ";
		}
		$sql .= " p.status_proposta='A' ";

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			if ($linha->estado == 'SP')
				$total_sp++;
			elseif ($linha->estado == 'RJ')
				$total_rj++;
		}

		return array('total_sp' => $total_sp, 'total_rj' => $total_rj);
	}

	//Número de entrada dos processos importados
	public static function numero_importacoes($data_de = null, $data_ate = null)
	{

		$sql = " SELECT pe.estado
				 	 FROM relatorio r
					 INNER JOIN processo pr ON r.processo_ID=pr.id
					 INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id
			";

		$total_sp = 0;
		$total_rj = 0;
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
			$sql .= " r.data_entrada>=:data_de ";
		}

		if (!DataValidator::isEmpty($data_ate)) {

			$ate = explode('/', $data_ate);
			$data_ate = $ate[2] . '-' . $ate[1] . '-' . $ate[0];

			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " r.data_entrada<=:data_ate ";
		}

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($data_de))
			$query->bindValue(':data_de', $data_de, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($data_ate))
			$query->bindValue(':data_ate', $data_ate . ' 23:59:59', PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			if ($linha->estado == 'SP')
				$total_sp++;
			elseif ($linha->estado == 'RJ')
				$total_rj++;
		}

		return array('total_sp' => $total_sp, 'total_rj' => $total_rj);
	}

	//Sinalizador dos processos: vermelhos, amarelos e verdes
	public static function sinalizadores()
	{

		$total_amarelo_sp = 0;
		$total_vermelho_sp = 0;
		$total_amarelo_rj = 0;
		$total_vermelho_rj = 0;

		$sql = " SELECT pr.sinalizador, pe.estado
				 	 FROM processo pr
					 INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id
					 WHERE pr.status_processo='P'
			";

		$where = false;

		$relatorioModel = new RelatorioModel();
		$query = $relatorioModel->getDB()->prepare($sql);
		$query->execute();

		while ($linha = $query->fetchObject()) {

			if ($linha->estado == 'SP' && $linha->sinalizador == 'A')
				$total_amarelo_sp++;
			elseif ($linha->estado == 'RJ' && $linha->sinalizador == 'A')
				$total_amarelo_rj++;

			if ($linha->estado == 'SP' && $linha->sinalizador == 'M')
				$total_vermelho_sp++;
			elseif ($linha->estado == 'RJ' && $linha->sinalizador == 'M')
				$total_vermelho_rj++;
		}

		return array('total_amarelo_sp' => $total_amarelo_sp, 'total_amarelo_rj' => $total_amarelo_rj, 'total_vermelho_sp' => $total_vermelho_sp, 'total_vermelho_rj' => $total_vermelho_rj);
	}

	//**************************//

	public static function grava_numero_importacoes($processo, $db = null)
	{

		if (is_null($db)) {
			$relatorioModel = new RelatorioModel();
			$db = $relatorioModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Grava Importações: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Grava Importações: O Processo deve ser identificado.');

		$sql = " INSERT INTO relatorio (processo_ID, data_entrada) VALUES (:processo_id, :data_entrada) ";
		$query = $db->prepare($sql);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_STR);
		$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);
		$query->execute();
	}
}
