<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");

class AlertaModel extends PersistModelAbstract
{

	public static function lista($processo, $db = null)
	{
		$alertas = array();

		if (is_null($db)) {
			$alertaModel = new AlertaModel();
			$db = $alertaModel->getDB();
		}

		if (DataValidator::isEmpty($processo))
			throw new UserException('Alerta: O Processo deve ser fornecido.');

		if (DataValidator::isEmpty($processo->getId()))
			throw new UserException('Alerta: O Processo deve ser identificado.');

		$sql = "SELECT pr.requerente, pr.requerido, pr.advogado_ID, pr.secretaria_ID, pr.jornal_ID,
					ae.email, jc.valor_padrao, jr.data_confirmacao, pe.advogado as advogado_entrada, pe.secretaria as secretaria_entrada, sec.status_secretaria, sec.nome_secretaria
					FROM processo pr
					INNER JOIN processo_entrada pe ON pr.entrada_ID=pe.id
					LEFT JOIN advogado adv ON pr.advogado_ID=adv.id
					LEFT JOIN advogado_email ae ON ae.advogado_id=adv.id					
					LEFT JOIN secretaria sec ON pr.secretaria_ID=sec.id					
					LEFT JOIN jornal jr ON pr.jornal_ID=jr.id
					LEFT JOIN jornal_custo jc ON jc.jornal_id=jr.id					
					WHERE pr.id=:processo_id ";

		$query = $db->prepare($sql);
		$query->bindValue(':processo_id', $processo->getId(), PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return array();

		if (DataValidator::isEmpty($linha->requerente))
			$alertas[] = 'Requerente não identificado.';

		if (DataValidator::isEmpty($linha->requerido))
			$alertas[] = 'Requerido não identificado.';

		if (!DataValidator::isEmpty($linha->advogado_entrada) && DataValidator::isEmpty($linha->advogado_ID))
			$alertas[] = 'Advogado não cadastrado.';
		elseif (DataValidator::isEmpty($linha->advogado_entrada) && DataValidator::isEmpty($linha->advogado_ID))
			$alertas[] = 'Advogado não identificado.';

		if (!DataValidator::isEmpty($linha->secretaria_entrada) && DataValidator::isEmpty($linha->secretaria_ID))
			$alertas[] = 'Secretaria não cadastrada.';
		elseif (DataValidator::isEmpty($linha->secretaria_entrada) && DataValidator::isEmpty($linha->secretaria_ID))
			$alertas[] = 'Secretaria não identificada.';
		elseif (!DataValidator::isEmpty($linha->secretaria_ID) && $linha->status_secretaria == 'I')
			$alertas[] = 'Secretaria ' . $linha->nome_secretaria . ' inativa.';


		if (DataValidator::isEmpty($linha->jornal_ID))
			$alertas[] = 'Jornal não identificado.';

		if (!DataValidator::isEmpty($linha->advogado_ID) && DataValidator::isEmpty($linha->email))
			$alertas[] = 'Advogado identificado não possui e-mail cadastrado.';

		if (!DataValidator::isEmpty($linha->jornal_ID) && DataValidator::isEmpty($linha->valor_padrao))
			$alertas[] = 'Jornal identificado sem Valor Padrão em seu cadastro.';

		if (!DataValidator::isEmpty($linha->jornal_ID) && DataValidator::isEmpty($linha->data_confirmacao))
			$alertas[] = 'Jornal identificado deve possuir uma data de confirmação';

		if (!DataValidator::isEmpty($linha->jornal_ID) && !DataValidator::isEmpty($linha->data_confirmacao)) {
			if (DataFilter::calculaDias($linha->data_confirmacao, date('Y-m-d')) >= 365)
				$alertas[] = 'Data de Confirmação do Jornal superior a 365 dias.';
		}

		return $alertas;
	}

	public static function getAlertasJornal($jornal, $db = null)
	{

		$alertas = null;

		if (is_null($db)) {
			$alertaModel = new AlertaModel();
			$db = $alertaModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Alerta Jornal: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Alerta: O Jornal deve ser identificado.');

		$sql = " SELECT jr.data_confirmacao, jc.valor_padrao
					 FROM jornal jr
					 LEFT JOIN jornal_custo jc ON jc.jornal_id=jr.id
					 WHERE jr.id=:jornal_id
			";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		if (DataValidator::isEmpty($linha->valor_padrao))
			$alertas['sem_valor'] = 'Jornal sem Valor Padrão em seu cadastro.';

		if (DataValidator::isEmpty($linha->data_confirmacao) || $linha->data_confirmacao == '0000-00-00')
			$alertas['sem_data'] = 'Jornal sem data de confirmação';

		if ($linha->data_confirmacao != '0000-00-00' && !DataValidator::isEmpty($linha->data_confirmacao)) {
			if (DataFilter::calculaDias($linha->data_confirmacao, date('Y-m-d')) >= 365)
				$alertas['data_expirada'] = 'Data de Confirmação do Jornal superior a 365 dias.';
		}

		return $alertas;
	}
}
