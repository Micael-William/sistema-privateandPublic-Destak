<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/SacadoAcompanhamento.class.php");
require_once("models/SacadoModel.php");

class SacadoAcompanhamentoModel extends PersistModelAbstract
{

	public static function getById($acompanhamento_id = null, $db = null, $origem = null)
	{
		$sacado = null;

		if (is_null($db)) {
			$sacadoModel = new SacadoAcompanhamentoModel();
			$db = $sacadoModel->getDB();
		}

		if (DataValidator::isEmpty($acompanhamento_id))
			throw new UserException('Sacado Acompanhamento: O Acompanhamento deve ser identificado.');

		$sql = " SELECT ac.* 
					 FROM acompanhamento a
					 INNER JOIN acompanhamento_fin_sacado ac ON ac.acompanhamento_ID=a.id
					 WHERE ac.acompanhamento_ID=:acompanhamento_id
			";

		$query = $db->prepare($sql);
		$query->bindValue(':acompanhamento_id', $acompanhamento_id, PDO::PARAM_INT);

		$query->execute();
		$linha = $query->fetchObject();

		if ($linha == null)
			return null;

		$sacado = new SacadoAcompanhamento();
		$sacado->setAcompanhamentoId($linha->acompanhamento_ID);
		$sacado->setSacadoId($linha->sacado_ID);
		$sacado->setNomeSacado($linha->nome_sacado);
		$sacado->setCpfCnpj($linha->cpf_cnpj);

		$sacado->setDataEntrada($linha->data_entrada);
		$sacado->setDataAlteracao($linha->data_alteracao);

		$endereco = new Endereco();
		$endereco->setLogradouro($linha->logradouro);
		$endereco->setNumero($linha->numero);
		$endereco->setComplemento($linha->complemento);
		$endereco->setBairro($linha->bairro);
		$endereco->setCidade($linha->cidade);
		$endereco->setEstado($linha->estado);
		$endereco->setCep($linha->cep);

		$sacado->setEndereco($endereco);

		$sacado->setEmails(self::getEmails($sacado, $db, $origem));

		return $sacado;
	}

	//salva sacado do acompanhamento. 
	public static function insertOrUpdate($sacado, $db = null)
	{
		$msg = null;
		$sacadoModel = new SacadoAcompanhamentoModel();
		$sacadoModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($sacado))
				throw new UserException('Sacado: O Sacado deve ser fornecido.');

			if (DataValidator::isEmpty($sacado->getNomeSacado()))
				throw new UserException('Sacado: O campo nome é obrigatório.');

			if (DataValidator::isEmpty($sacado->getCpfCnpj()))
				throw new UserException('Sacado: O campo CPF/CNPJ é obrigatório.');


			$ver = new SacadoModel();
			if ($ver->getByCpfCnpj($sacado->getCpfCnpj()) == null) {

				$sac = new Sacado();
				$sac->setNome($sacado->getNomeSacado());
				$sac->setCpfCnpj($sacado->getCpfCnpj());
				$sac->setEndereco($sacado->getEndereco());
				$sac->setUsuario($sacado->getUsuario());

				$acomp = new Acompanhamento();
				$acomp->setId($sacado->getAcompanhamentoId());

				$sac->setAcompanhamentos(array($acomp));
				$sac->setEmails($sacado->getEmails());
				$ver->insert($sac);
			}

			$existe = self::getById($sacado->getAcompanhamentoId(), $db);
			if ($existe == null) {
				return self::insert($sacado, $sacadoModel);
			} else {
				$sql = " UPDATE acompanhamento_fin_sacado SET
									data_alteracao=:data_alteracao,
									sacado_id=:sacado_id,
									nome_sacado=:nome_sacado, 
									cpf_cnpj=:cpf_cnpj,
									logradouro=:logradouro, 
									numero=:numero, 
									complemento=:complemento,
									bairro=:bairro,
									cidade=:cidade,
									estado=:estado,
									cep=:cep,
									usuario_id=:usuario_id
									WHERE acompanhamento_id=:acompanhamento_id
									";

				$query = $sacadoModel->getDB()->prepare($sql);

				$query->bindValue(':acompanhamento_id', $sacado->getAcompanhamentoId(), PDO::PARAM_STR);
				$query->bindValue(':nome_sacado', $sacado->getNomeSacado(), PDO::PARAM_STR);
				$query->bindValue(':cpf_cnpj', $sacado->getCpfCnpj(), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($sacado->getSacadoId()))
					$query->bindValue(':sacado_id', $sacado->getSacadoId(), PDO::PARAM_INT);
				else
					$query->bindValue(':sacado_id', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':logradouro', $sacado->getEndereco()->getLogradouro(), PDO::PARAM_STR);
				else
					$query->bindValue(':logradouro', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':numero', $sacado->getEndereco()->getNumero(), PDO::PARAM_STR);
				else
					$query->bindValue(':numero', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':complemento', $sacado->getEndereco()->getComplemento(), PDO::PARAM_STR);
				else
					$query->bindValue(':complemento', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':bairro', $sacado->getEndereco()->getBairro(), PDO::PARAM_STR);
				else
					$query->bindValue(':bairro', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':cidade', $sacado->getEndereco()->getCidade(), PDO::PARAM_STR);
				else
					$query->bindValue(':cidade', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':estado', $sacado->getEndereco()->getEstado(), PDO::PARAM_STR);
				else
					$query->bindValue(':estado', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($sacado->getEndereco()))
					$query->bindValue(':cep', $sacado->getEndereco()->getCep(), PDO::PARAM_STR);
				else
					$query->bindValue(':cep', NULL, PDO::PARAM_NULL);

				$query->bindValue(':usuario_id', $sacado->getUsuario()->getId(), PDO::PARAM_INT);
				$query->execute();

				self::saveEmail($sacado, $db);
			}

			$sacadoModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$sacadoModel->getDB()->rollback();
		} catch (Exception $ex) {
			$msg = $ex->getMessage();
		}

		return array('msg' => $msg);
	}

	private static function insert($sacado, $sacadoModel = null)
	{

		$msg = null;

		if (is_null($sacadoModel)) {
			$sacadoModel = new SacadoAcompanhamentoModel();
			$sacadoModel->getDB()->beginTransaction();
		}

		try {

			if (DataValidator::isEmpty($sacado))
				throw new UserException('Sacado: O Sacado deve ser fornecido.');

			if (DataValidator::isEmpty($sacado->getNomeSacado()))
				throw new UserException('Sacado: O campo nome é obrigatório');

			if (DataValidator::isEmpty($sacado->getCpfCnpj()))
				throw new UserException('Sacado: O campo CPF/CNPJ é obrigatório.');

			$sql = " INSERT INTO acompanhamento_fin_sacado 
								(	acompanhamento_ID,
									sacado_ID,
									nome_sacado, 
									data_entrada,
									data_alteracao,
									cpf_cnpj, 
									logradouro, 
									numero, 
									complemento,
									bairro,
									cidade,
									estado,
									cep,
									usuario_ID
									) 
							VALUES 
								( 	:acompanhamento_id,
									:sacado_id,
									:nome_sacado, 
									:data_entrada,
									:data_alteracao,
									:cpf_cnpj, 
									:logradouro, 
									:numero, 
									:complemento,
									:bairro,
									:cidade,
									:estado,
									:cep,
									:usuario_id
									) ";

			$query = $sacadoModel->getDB()->prepare($sql);

			$query->bindValue(':acompanhamento_id', $sacado->getAcompanhamentoId(), PDO::PARAM_INT);
			$query->bindValue(':nome_sacado', $sacado->getNomeSacado(), PDO::PARAM_STR);
			$query->bindValue(':cpf_cnpj', $sacado->getCpfCnpj(), PDO::PARAM_STR);
			$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($sacado->getSacadoId()))
				$query->bindValue(':sacado_id', $sacado->getSacadoId(), PDO::PARAM_INT);
			else
				$query->bindValue(':sacado_id', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':logradouro', $sacado->getEndereco()->getLogradouro(), PDO::PARAM_STR);
			else
				$query->bindValue(':logradouro', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':numero', $sacado->getEndereco()->getNumero(), PDO::PARAM_STR);
			else
				$query->bindValue(':numero', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':complemento', $sacado->getEndereco()->getComplemento(), PDO::PARAM_STR);
			else
				$query->bindValue(':complemento', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':bairro', $sacado->getEndereco()->getBairro(), PDO::PARAM_STR);
			else
				$query->bindValue(':bairro', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':cidade', $sacado->getEndereco()->getCidade(), PDO::PARAM_STR);
			else
				$query->bindValue(':cidade', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':estado', $sacado->getEndereco()->getEstado(), PDO::PARAM_STR);
			else
				$query->bindValue(':estado', NULL, PDO::PARAM_NULL);

			if (!DataValidator::isEmpty($sacado->getEndereco()))
				$query->bindValue(':cep', $sacado->getEndereco()->getCep(), PDO::PARAM_STR);
			else
				$query->bindValue(':cep', NULL, PDO::PARAM_NULL);

			$query->bindValue(':usuario_id', $sacado->getUsuario()->getId(), PDO::PARAM_INT);
			$query->execute();

			self::saveEmail($sacado, $sacadoModel->getDB());
			
			$sacadoModel->getDB()->commit();

		} catch (UserException $e) {
			$msg = $e->getMessage();
			$sacadoModel->getDB()->rollback();
		}

		return array('msg' => $msg);
	}

	public static function getEmails($sacado, $db = null, $origem = null)
	{
		$emails = array();

		if (DataValidator::isEmpty($sacado))
			throw new UserException('Sacado Emails: O Sacado deve ser fornecido.');

		if (DataValidator::isEmpty($sacado->getAcompanhamentoId()))
			throw new UserException('Sacado Emails: O Sacado deve ser identificado.');

		$sql = " SELECT ae.id, ae.email, ae.enviar
					 FROM  acompanhamento ac
					 LEFT JOIN acompanhamento_fin_email ae ON ae.acompanhamento_ID=ac.id
					 WHERE ae.acompanhamento_ID=:acompanhamento_id
					 ";
		if (!DataValidator::isEmpty($origem))
			$sql .= " AND ae.enviar='S' ";

		$query = $db->prepare($sql);
		$query->bindValue(':acompanhamento_id', $sacado->getAcompanhamentoId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$email = new Email();
			$email->setId($linha->id);
			$email->setEmailEndereco($linha->email);
			$email->setEnviar($linha->enviar);
			$emails[] = $email;
		}

		return $emails;
	}

	//salva emails
	public static function saveEmail($sacado, $db)
	{



		if (DataValidator::isEmpty($sacado))
			throw new UserException('Salva Email: O Sacado deve ser fornecido.');

		if (DataValidator::isEmpty($sacado->getAcompanhamentoId()))
			throw new UserException('Salva Email: O Sacado deve ser identificado.');

		$emails = $sacado->getEmails();

		if (!DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {

				$endereco_email = trim($email->getEmailEndereco());

				if (!DataValidator::isEmpty($email->getEmailEndereco()) && filter_var($endereco_email, FILTER_VALIDATE_EMAIL)) {

					if (DataValidator::isEmpty($email->getId())) {
						$sql = " INSERT INTO acompanhamento_fin_email (email, acompanhamento_ID, enviar) VALUES (:email, :acompanhamento_id, :enviar) ";

						$query = $db->prepare($sql);
						$query->bindValue(':acompanhamento_id', $sacado->getAcompanhamentoId(), PDO::PARAM_INT);
						$query->bindValue(':email', $endereco_email, PDO::PARAM_STR);
						$query->bindValue(':enviar', $email->getEnviar(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE acompanhamento_fin_email SET email=:email, enviar=:enviar WHERE id=:email_id";

						$query = $db->prepare($sql);
						$query->bindValue(':email_id', $email->getId(), PDO::PARAM_INT);
						$query->bindValue(':email', $email->getEmailEndereco(), PDO::PARAM_STR);
						$query->bindValue(':enviar', $email->getEnviar(), PDO::PARAM_STR);

						$query->execute();
					}
				}
			}
		}
	}

	public static function excluiEmail($item_id)
	{

		$msg = null;
		$sacado_model = new SacadoAcompanhamentoModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Email: O E-mail deve ser identificado.');

			$sql = " DELETE FROM acompanhamento_fin_email WHERE id=:item_id ";

			$query = $sacado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}
}
