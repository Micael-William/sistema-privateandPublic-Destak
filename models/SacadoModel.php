<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("lib/DataValidator.php");
require_once("classes/Sacado.class.php");
require_once("classes/Endereco.class.php");
require_once("classes/Usuario.class.php");
require_once("models/AcompanhamentoModel.php");

class SacadoModel extends PersistModelAbstract
{

	public static function lista(
		$termo = null,
		$status = null,
		$email = null,
		$cpf_cnpj =  null,
		$cidade = null,
		$estado = null,
		$endereco = null,
		$pagina = 0,
		$qtd_pagina = 0
	) {

		$sql = " SELECT SQL_CALC_FOUND_ROWS DISTINCT ae.sacado_ID, sac.*
					FROM sacado sac
				LEFT JOIN sacado_email ae ON ae.sacado_ID=sac.id 
					 ";

		$where = false;

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.nome_sacado LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.status_sacado =:status ";
		}

		if (!DataValidator::isEmpty($email)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " ae.email LIKE :email ";
		}

		if (!DataValidator::isEmpty($cpf_cnpj)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.cpf_cnpj LIKE :cpf_cnpj ";
		}

		if (!DataValidator::isEmpty($endereco)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.logradouro LIKE _utf8 :endereco COLLATE utf8_unicode_ci OR ";
			$sql .= " sac.bairro LIKE _utf8 :endereco COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($cidade)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.cidade LIKE _utf8 :cidade COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($estado)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " sac.estado =:estado ";
		}

		$sql .= " ORDER BY sac.nome_sacado ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$sacados = array();
		$sacadoModel = new SacadoModel();

		$query = $sacadoModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($email))
			$query->bindValue(':email', "%$email%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($cpf_cnpj))
			$query->bindValue(':cpf_cnpj', "%$cpf_cnpj%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($endereco))
			$query->bindValue(':endereco', "%$endereco%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($cidade))
			$query->bindValue(':cidade', "%$cidade%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$sacado = new Sacado();
			$sacado->setId($linha->id);
			$sacado->setNome($linha->nome_sacado);
			$sacado->setCpfCnpj($linha->cpf_cnpj);

			$sacados[] = $sacado;
		}

		$query = $sacadoModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$totalLinhas = $linha ? $linha->frows : 0;

		return array('sacados' => $sacados, 'totalLinhas' => $totalLinhas);
	}


	public static function getAllhtml()
	{

		$origem = null;

		$sql = " SELECT sac.* 
					FROM  sacado sac ";

		$sacados = array();
		$sacadoModel = new SacadoModel();
		$db = $sacadoModel->getDB();

		$query = $sacadoModel->getDB()->prepare($sql);

		$query->execute();

		$html = "";
		$html .= "<table border=1>";
		$html .= "<tr align='center'>";
		$html .= "<th height='30'><b>C&Ocirc;DIGO</b></th>";
		$html .= "<th height='30'><b>NOME</b></th>";
		$html .= "<th height='30'><b>CPF/CPNJ</b></th>";
		$html .= "<th height='30'><b>DATA CADASTRO</b></th>";
		$html .= "<th height='30'><b>CEP</b></th>";
		$html .= "<th height='30'><b>ENDERE&Ccedil;O</b></th>";
		$html .= "<th height='30'><b>N&Uacute;MERO</b></th>";
		$html .= "<th height='30'><b>COMPLEMENTO</b></th>";
		$html .= "<th height='30'><b>BAIRRO</b></th>";
		$html .= "<th height='30'><b>CIDADE</b></th>";
		$html .= "<th height='30'><b>ESTADO</b></th>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "<table>";

		while ($linha = $query->fetchObject()) {

			$html .= "<tr>";
			$html .= "<td>" . $linha->id . "</td>";
			$html .= "<td>" . $linha->nome_sacado . "</td>";
			$html .= "<td>" . (strlen($linha->cpf_cnpj) > 11 ? DataFilter::mask($linha->cpf_cnpj, '##.###.###/####-##') : DataFilter::mask($linha->cpf_cnpj, '###.###.###-##')) . "</td>";
			$html .= "<td>" . date('d/m/Y', strtotime($linha->data_entrada)) . "</td>";
			$html .= "<td>" . $linha->cep . "</td>";
			$html .= "<td>" . $linha->logradouro . "</td>";
			$html .= "<td>" . $linha->numero . "</td>";
			$html .= "<td>" . $linha->complemento . "</td>";
			$html .= "<td>" . $linha->bairro . "</td>";
			$html .= "<td>" . $linha->cidade . "</td>";
			$html .= "<td>" . $linha->estado . "</td>";
			$html .= "</tr>";
		}

		$html .= "</table>";

		return $html;
	}

	/*
		param: id ou nome
		*/
	public static function getBy($param = null, $db = null, $origem = null)
	{
		$sacado = null;

		if (is_null($db)) {
			$sacadoModel = new SacadoModel();
			$db = $sacadoModel->getDB();
		}

		if (DataValidator::isEmpty($param)) {
			if (is_numeric($param)) {
				throw new UserException('Sacado: O Parâmetro deve ser fornecido.');
			} else {
				//busca pelo sacado na entrada do processo
				return null;
			}
		}

		$sql = " SELECT sac.*, ae.email, u.nome_usuario 
					 FROM  sacado sac
					 LEFT JOIN sacado_email ae ON ae.sacado_ID=sac.id
					 LEFT JOIN usuario u ON sac.usuario_id=u.id
					 ";

		if (is_numeric($param))
			$sql .= " WHERE sac.id=:param ";
		else
			$sql .= " WHERE sac.nome_sacado LIKE _utf8 :param COLLATE utf8_unicode_ci ";

		$query = $db->prepare($sql);

		if (is_numeric($param))
			$query->bindValue(':param', $param, PDO::PARAM_INT);
		else
			$query->bindValue(':param', "%$param%", PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$sacado = new Sacado();
		$sacado->setId($linha->id);
		$sacado->setNome($linha->nome_sacado);
		$sacado->setStatus($linha->status_sacado);
		$sacado->setDataEntrada($linha->data_entrada);
		$sacado->setDataAlteracao($linha->data_alteracao);
		$sacado->setCpfCnpj($linha->cpf_cnpj);

		$endereco = new Endereco();
		$endereco->setLogradouro($linha->logradouro);
		$endereco->setNumero($linha->numero);
		$endereco->setComplemento($linha->complemento);
		$endereco->setBairro($linha->bairro);
		$endereco->setCidade($linha->cidade);
		$endereco->setEstado($linha->estado);
		$endereco->setCep($linha->cep);

		$usuario = new Usuario();
		$usuario->setId($linha->usuario_ID);
		$usuario->setNome($linha->nome_usuario);
		$sacado->setUsuario($usuario);

		$sacado->setEndereco($endereco);
		$sacado->setAcompanhamentos(AcompanhamentoModel::listaBySacado($sacado, $db));
		$sacado->setEmails(self::getEmails($sacado, $db, $origem));

		return $sacado;
	}

	//**************************//		

	public static function getByCpfCnpj($cpf_cnpj = null, $db = null)
	{

		if (is_null($db)) {
			$sacadoModel = new SacadoModel();
			$db = $sacadoModel->getDB();
		}

		if (DataValidator::isEmpty($cpf_cnpj)) {
			return null;
		}

		$sql = " SELECT cpf_cnpj
					 FROM  sacado
					 WHERE cpf_cnpj = :cpf_cnpj
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':cpf_cnpj', $cpf_cnpj, PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if ($linha)
			return 'existe';
		else
			return null;
	}

	public static function insert($sacado)
	{
		$msg = null;
		$sacadoModel = new SacadoModel();
		$sacadoModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($sacado))
				throw new UserException('Sacado: O Sacado deve ser fornecido.');

			if (DataValidator::isEmpty($sacado->getNome()))
				throw new UserException('Sacado: O campo nome é obrigatório');


			$sac_cadastrado = self::getByCpfCnpj($sacado->getCpfCnpj(), $sacadoModel->getDB());
			if (DataValidator::isEmpty($sac_cadastrado)) {

				$sql = " INSERT INTO sacado 
									(nome_sacado, 
									 data_entrada,
									 data_alteracao,
									 cpf_cnpj, 
									 status_sacado, 
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
									( :nome_sacado, 
									 :data_entrada,
									 :data_alteracao,
									 :cpf_cnpj, 
									 :status_sacado, 
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
				$query->bindValue(':nome_sacado', $sacado->getNome(), PDO::PARAM_STR);
				$query->bindValue(':cpf_cnpj', $sacado->getCpfCnpj(), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($sacado->getStatus()))
					$query->bindValue(':status_sacado', $sacado->getStatus(), PDO::PARAM_STR);
				else
					$query->bindValue(':status_sacado', 'N', PDO::PARAM_STR);

				$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

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
				$sacado->setId($sacadoModel->getDB()->lastInsertId());

				self::saveEmail($sacado, $sacadoModel->getDB());

				$sacadoModel->getDB()->commit();
			} else
				throw new UserException('CPF/CPNJ já existente.');
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$sacadoModel->getDB()->rollback();
		}

		return array('msg' => $msg, 'sacado_id' => $sacado->getId(), 'sacado_nome' => $sacado->getNome());
	}

	public static function update($sacado)
	{

		$msg = null;
		$sacadoModel = new SacadoModel();
		$sacadoModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($sacado))
				throw new UserException('Sacado: O Sacado deve ser fornecido.');

			if (DataValidator::isEmpty($sacado->getNome()))
				throw new UserException('Sacado: O campo nome é obrigatório.');

			if (DataValidator::isEmpty($sacado->getCpfCnpj()))
				throw new UserException('Sacado: O campo CPF/CPNJ é obrigatório.');

			$sql = " UPDATE sacado SET
								nome_sacado=:nome_sacado, 
								data_alteracao=:data_alteracao,
								cpf_cnpj=:cpf_cnpj, 
								status_sacado=:status_sacado, 
								logradouro=:logradouro, 
								numero=:numero, 
								complemento=:complemento,
								bairro=:bairro,
								cidade=:cidade,
								estado=:estado,
								cep=:cep,
								usuario_id=:usuario_id
								WHERE id=:sacado_id
								";

			$query = $sacadoModel->getDB()->prepare($sql);
			$query->bindValue(':nome_sacado', $sacado->getNome(), PDO::PARAM_STR);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->bindValue(':cpf_cnpj', $sacado->getCpfCnpj(), PDO::PARAM_STR);
			$query->bindValue(':status_sacado', $sacado->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':logradouro', $sacado->getEndereco()->getLogradouro(), PDO::PARAM_STR);
			$query->bindValue(':numero', $sacado->getEndereco()->getNumero(), PDO::PARAM_STR);
			$query->bindValue(':complemento', $sacado->getEndereco()->getComplemento(), PDO::PARAM_STR);
			$query->bindValue(':bairro', $sacado->getEndereco()->getBairro(), PDO::PARAM_STR);
			$query->bindValue(':cidade', $sacado->getEndereco()->getCidade(), PDO::PARAM_STR);
			$query->bindValue(':estado', $sacado->getEndereco()->getEstado(), PDO::PARAM_STR);
			$query->bindValue(':cep', $sacado->getEndereco()->getCep(), PDO::PARAM_STR);
			$query->bindValue(':sacado_id', $sacado->getId(), PDO::PARAM_STR);
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

	public static function excluiSacado($sacado_id)
	{

		$msg = null;
		$sacado_model = new SacadoModel();

		try {

			if (DataValidator::isEmpty($sacado_id))
				throw new UserException('Exclui Sacado: O Sacado deve ser identificado.');

			$sql = ' SELECT sac.id FROM acompanhamento_fin_sacado ac 
						 INNER JOIN sacado sac ON ac.sacado_ID=sac.id 
						 WHERE ac.sacado_ID=:sacado_id LIMIT 1
				';

			$query = $sacado_model->getDB()->prepare($sql);
			$query->bindValue(':sacado_id', $sacado_id, PDO::PARAM_INT);
			$query->execute();

			$linha = $query->fetchObject();

			if ($linha) {
				throw new UserException('Sacado vinculado a Acompanhamentos Processuais. Não á possível excluir.');
			} else {
				$del = ' DELETE FROM sacado
							 WHERE sacado.id=:sacado_id ';

				$query = $sacado_model->getDB()->prepare($del);
				$query->bindValue(':sacado_id', $sacado_id, PDO::PARAM_INT);
				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function getEmails($sacado, $db = null, $origem = null)
	{
		$emails = array();

		if (DataValidator::isEmpty($sacado))
			throw new UserException('Sacado Emails: O Sacado deve ser fornecido.');

		if (DataValidator::isEmpty($sacado->getId()))
			throw new UserException('Sacado Emails: O Sacado deve ser identificado.');

		$sql = " SELECT ae.id, ae.email, ae.enviar
					 FROM  sacado sac
					 LEFT JOIN sacado_email ae ON ae.sacado_ID=sac.id
					 WHERE ae.sacado_ID=:sacado_id
					 ";
		if (!DataValidator::isEmpty($origem))
			$sql .= " AND ae.enviar='S' ";

		$query = $db->prepare($sql);
		$query->bindValue(':sacado_id', $sacado->getId(), PDO::PARAM_INT);

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

		if (DataValidator::isEmpty($sacado->getId()))
			throw new UserException('Salva Email: O Sacado deve ser identificado.');

		$emails = $sacado->getEmails();

		if (!DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {

				$endereco_email = trim($email->getEmailEndereco());

				if (!DataValidator::isEmpty($email->getEmailEndereco()) && filter_var($endereco_email, FILTER_VALIDATE_EMAIL)) {

					if (DataValidator::isEmpty($email->getId())) {
						$sql = " INSERT INTO sacado_email (email, sacado_ID, enviar) VALUES (:email, :sacado_id, :enviar) ";

						$query = $db->prepare($sql);
						$query->bindValue(':sacado_id', $sacado->getId(), PDO::PARAM_INT);
						$query->bindValue(':email', $endereco_email, PDO::PARAM_STR);
						$query->bindValue(':enviar', $email->getEnviar(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE sacado_email SET email=:email, enviar=:enviar WHERE id=:email_id";

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
		$sacado_ID = null;
		$sacado_model = new SacadoModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Email: O E-mail deve ser identificado.');

			$sql = " SELECT sacado_ID FROM sacado_email WHERE id=:item_id ";

			$query = $sacado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();

			$linha = $query->fetchObject();
			$sacado_ID = $linha->sacado_ID;

			$sql = " DELETE FROM sacado_email WHERE id=:item_id ";

			$query = $sacado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}
}
