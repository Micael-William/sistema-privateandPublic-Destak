<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("lib/DataValidator.php");
require_once("classes/Advogado.class.php");
require_once("classes/Endereco.class.php");
require_once("classes/Observacao.class.php");
require_once("classes/Usuario.class.php");
require_once("classes/Telefone.class.php");
require_once("classes/Email.class.php");
require_once("models/ObservacaoModel.php");
require_once("models/PropostaModel.php");

class AdvogadoModel extends PersistModelAbstract
{

	public static function lista(
		$termo = null,
		$status = null,
		$email = null,
		$oab = null,
		$empresa = null,
		$cidade = null,
		$estado = null,
		$nome_contato = null,
		$email_contato = null,
		$endereco = null,
		$telefone = null,
		$pagina = 0,
		$qtd_pagina = 0
	) {

		$sql = " SELECT {{campos}}
					FROM advogado adv
				LEFT JOIN advogado_email ae ON ae.advogado_ID=adv.id 
				LEFT JOIN advogado_tefefone at ON adv.id = at.advogado_ID 
					 ";

		$where = false;

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.nome_advogado LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.status_advogado =:status ";
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

		if (!DataValidator::isEmpty($oab)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.oab LIKE :oab ";
		}

		if (!DataValidator::isEmpty($empresa)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.empresa LIKE _utf8 :empresa COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($endereco)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.logradouro LIKE _utf8 :endereco COLLATE utf8_unicode_ci OR ";
			$sql .= " adv.bairro LIKE _utf8 :endereco COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($cidade)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.cidade LIKE _utf8 :cidade COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($estado)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.estado =:estado ";
		}

		if (!DataValidator::isEmpty($nome_contato)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.nome_contato LIKE _utf8 :nome_contato COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($email_contato)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " adv.email_contato LIKE _utf8 :email_contato COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($telefone)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " at.numero LIKE _utf8 :telefone COLLATE utf8_unicode_ci ";
		}

		$sql .= " ORDER BY adv.nome_advogado ";

		$advogados = array();
		$advogadoModel = new AdvogadoModel();

		$campos = "  DISTINCT ae.advogado_ID, adv.* ";

		$query_num_linhas = $advogadoModel->getDB()->prepare(str_replace('{{campos}}', ' COUNT(DISTINCT ae.advogado_ID) as items ', $sql));

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$query = $advogadoModel->getDB()->prepare(str_replace('{{campos}}', $campos, $sql));

		if (!DataValidator::isEmpty($termo)) {
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($status)) {
			$query->bindValue(':status', $status, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':status', $status, PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($email)) {
			$query->bindValue(':email', "%$email%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':email', "%$email%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($telefone)) {
			$query->bindValue(':telefone', "%$telefone%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':telefone', "%$telefone%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($oab)) {
			$query->bindValue(':oab', "%$oab%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':oab', "%$oab%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($empresa)) {
			$query->bindValue(':empresa', "%$empresa%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':empresa', "%$empresa%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($endereco)) {
			$query->bindValue(':endereco', "%$endereco%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':endereco', "%$endereco%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($cidade)) {
			$query->bindValue(':cidade', "%$cidade%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':cidade', "%$cidade%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($estado)) {
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);
			$query_num_linhas->bindValue(':estado', $estado, PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($nome_contato)) {
			$query->bindValue(':nome_contato', "%$nome_contato%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':nome_contato', "%$nome_contato%", PDO::PARAM_STR);
		}

		if (!DataValidator::isEmpty($email_contato)) {
			$query->bindValue(':email_contato', "%$email_contato%", PDO::PARAM_STR);
			$query_num_linhas->bindValue(':email_contato', "%$email_contato%", PDO::PARAM_STR);
		}

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$advogado = new Advogado();
			$advogado->setId($linha->id);
			$advogado->setNome($linha->nome_advogado);
			$advogado->setOab($linha->oab);
			$advogado->setStatus($linha->status_advogado);

			$advogados[] = $advogado;
		}

		$query_num_linhas->execute();

		$linha = $query_num_linhas->fetchObject();
		$totalLinhas = $linha->items;

		return array('advogados' => $advogados, 'totalLinhas' => $totalLinhas);
	}


	public static function getAllhtml()
	{

		$origem = null;

		$sql = " SELECT adv.*,
						(SELECT GROUP_CONCAT(CONCAT('(',ddd,')',numero)) FROM advogado_tefefone at WHERE at.advogado_ID=adv.id ) AS telefones, 
						(SELECT GROUP_CONCAT(email) FROM advogado_email ae WHERE ae.advogado_ID=adv.id AND ae.enviar = 'S' ) AS email_tick, 
						(SELECT GROUP_CONCAT(email) FROM advogado_email ae WHERE ae.advogado_ID=adv.id AND ae.enviar = 'N' ) AS email_no_tick  
					FROM  advogado adv ";

		$advogados = array();
		$advogadoModel = new AdvogadoModel();
		$db = $advogadoModel->getDB();

		$query = $advogadoModel->getDB()->prepare($sql);

		$query->execute();

		$html = "";
		$html .= "<table border=1>";
		$html .= "<tr align='center'>";
		$html .= "<th height='30'><b>C&Ocirc;DIGO</b></th>";
		$html .= "<th height='30'><b>NOME</b></th>";
		$html .= "<th height='30'><b>OAB</b></th>";
		$html .= "<th height='30'><b>DATA CADASTRO</b></th>";
		$html .= "<th height='30'><b>STATUS</b></th>";
		$html .= "<th height='30'><b>EMPRESA</b></th>";
		$html .= "<th height='30'><b>CNPJ</b></th>";
		$html .= "<th height='30'><b>SITE</b></th>";
		$html .= "<th height='30'><b>NOME CONTATO</b></th>";
		$html .= "<th height='30'><b>EMAIL CONTATO</b></th>";
		$html .= "<th height='30'><b>CEP</b></th>";
		$html .= "<th height='30'><b>ENDERE&Ccedil;O</b></th>";
		$html .= "<th height='30'><b>N&Uacute;MERO</b></th>";
		$html .= "<th height='30'><b>COMPLEMENTO</b></th>";
		$html .= "<th height='30'><b>BAIRRO</b></th>";
		$html .= "<th height='30'><b>CIDADE</b></th>";
		$html .= "<th height='30'><b>ESTADO</b></th>";
		$html .= "<th height='30'><b>TELEFONE</b></th>";
		$html .= "<th height='30'><b>EMAIL TICK</b></th>";
		$html .= "<th height='30'><b>EMAIL NAO TICK</b></th>";
		$html .= "</tr>";
		$html .= "</table>";
		$html .= "<table>";

		while ($linha = $query->fetchObject()) {

			$html .= "<tr>";
			$html .= "<td>" . $linha->id . "</td>";
			$html .= "<td>" . $linha->nome_advogado . "</td>";
			$html .= "<td>" . $linha->oab . "</td>";
			$html .= "<td>" . date('d/m/Y', strtotime($linha->data_entrada)) . "</td>";
			$html .= "<td>" . $linha->status_advogado . "</td>";
			$html .= "<td>" . $linha->empresa . "</td>";
			$html .= "<td>" . (!DataValidator::isEmpty($linha->cnpj) ? DataFilter::mask($linha->cnpj, '##.###.###/####-##') : '') . "</td>";
			$html .= "<td>" . $linha->site . "</td>";
			$html .= "<td>" . $linha->nome_contato . "</td>";
			$html .= "<td>" . $linha->email_contato . "</td>";
			$html .= "<td>" . $linha->cep . "</td>";
			$html .= "<td>" . $linha->logradouro . "</td>";
			$html .= "<td>" . $linha->numero . "</td>";
			$html .= "<td>" . $linha->complemento . "</td>";
			$html .= "<td>" . $linha->bairro . "</td>";
			$html .= "<td>" . $linha->cidade . "</td>";
			$html .= "<td>" . $linha->estado . "</td>";
			$html .= "<td>" . $linha->telefones . "</td>";
			$html .= "<td>" . $linha->email_tick . "</td>";
			$html .= "<td>" . $linha->email_no_tick . "</td>";
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
		$advogado = null;

		if (is_null($db)) {
			$advogadoModel = new AdvogadoModel();
			$db = $advogadoModel->getDB();
		}

		if (DataValidator::isEmpty($param)) {
			if (is_numeric($param)) {
				throw new UserException('Advogado: O Parâmetro deve ser fornecido.');
			} else {
				//busca pelo advogado na entrada do processo
				return null;
			}
		}

		$sql = " SELECT adv.*, ae.email, u.nome_usuario 
					 FROM  advogado adv
					 LEFT JOIN advogado_email ae ON ae.advogado_ID=adv.id
					 LEFT JOIN usuario u ON adv.usuario_id=u.id
					 ";

		if (is_numeric($param))
			$sql .= " WHERE adv.id=:param ";
		else
			$sql .= " WHERE adv.nome_advogado LIKE _utf8 :param COLLATE utf8_unicode_ci ";

		$query = $db->prepare($sql);

		if (is_numeric($param))
			$query->bindValue(':param', $param, PDO::PARAM_INT);
		else
			$query->bindValue(':param', "%$param%", PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$advogado = new Advogado();
		$advogado->setId($linha->id);
		$advogado->setNome($linha->nome_advogado);
		$advogado->setOab($linha->oab);
		$advogado->setStatus($linha->status_advogado);
		$advogado->setDataEntrada($linha->data_entrada);
		$advogado->setDataAlteracao($linha->data_alteracao);
		$advogado->setEmpresa($linha->empresa);
		$advogado->setCnpj($linha->cnpj);
		$advogado->setSite($linha->site);
		$advogado->setNomeContato($linha->nome_contato);
		$advogado->setEmailContato($linha->email_contato);

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
		$advogado->setUsuario($usuario);

		$advogado->setEndereco($endereco);
		$advogado->setEmails(self::getEmails($advogado, $db, $origem));
		$advogado->setObservacoes(ObservacaoModel::getObservacoes($advogado, $db));
		$advogado->setTelefones(self::getTelefones($advogado, $db));
		$advogado->setPropostas(PropostaModel::listaByAdvogado($advogado, $db));
		$advogado->setAcompanhamentos(AcompanhamentoModel::listaByAdvogado($advogado, $db));

		return $advogado;
	}

	//usada na entrada do processo. Verifica se existem advogados homonimos
	public static function getFromEntrada($param = null, $db = null)
	{
		$advogados = array();

		if (is_null($db)) {
			$advogadoModel = new AdvogadoModel();
			$db = $advogadoModel->getDB();
		}

		if (DataValidator::isEmpty($param))
			return null;

		$sql = " SELECT adv.* 
					 FROM  advogado adv
					 WHERE adv.nome_advogado LIKE _utf8 :param COLLATE utf8_unicode_ci
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':param', "%$param%", PDO::PARAM_STR);
		$query->execute();

		// $query_ = $db->prepare("SELECT FOUND_ROWS() as frows;");
		// $query_->execute();

		// $linha = $query_->fetchObject();
		// $totalLinhas = $linha ? $linha->frows : 0;

		while ($linha = $query->fetchObject()) {
			$advogado = new Advogado();
			$advogado->setId($linha->id);
			$advogado->setNome($linha->nome_advogado);
			$advogado->setOab($linha->oab);
			$advogado->setStatus($linha->status_advogado);
			$advogado->setDataEntrada($linha->data_entrada);
			$advogado->setEmpresa($linha->empresa);
			$advogado->setCnpj($linha->cnpj);
			$advogado->setSite($linha->site);
			$advogado->setNomeContato($linha->nome_contato);
			$advogado->setEmailContato($linha->email_contato);

			$advogado->setEmails(self::getEmails($advogado, $db));

			$advogados[] = $advogado;
		}

		return array('advogados' => $advogados, 'totalLinhas' => sizeof($advogados));
	}

	//validação se a OAB já existe
	public static function getByOAB($oab = null, $db = null)
	{

		if (is_null($db)) {
			$advogadoModel = new AdvogadoModel();
			$db = $advogadoModel->getDB();
		}

		if (DataValidator::isEmpty($oab)) {
			throw new UserException('Advogado: OAB do Advogado deve ser fornecido.');
		}

		$sql = " SELECT oab
					 FROM  advogado
					 WHERE oab = :oab
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':oab', $oab, PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if ($linha)
			return 'existe';
		else
			return null;
	}

	//*******************************//

	public static function getEmails($advogado, $db = null, $origem = null)
	{
		$emails = array();

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Advogado Emails: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Advogado Emails: O Advogado deve ser identificado.');

		$sql = " SELECT ae.id, ae.email, ae.enviar
					 FROM  advogado adv
					 LEFT JOIN advogado_email ae ON ae.advogado_ID=adv.id
					 WHERE ae.advogado_ID=:advogado_id
					 ";
		if (!DataValidator::isEmpty($origem))
			$sql .= " AND ae.enviar='S' ";

		$query = $db->prepare($sql);
		$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);

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

	public static function getTelefones($advogado, $db = null)
	{
		$telefones = array();

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Advogado Telefones: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Advogado Telefones: O Advogado deve ser identificado.');

		$sql = " SELECT at.*
					 FROM  advogado_tefefone at
					 INNER JOIN advogado adv ON at.advogado_ID=adv.id
					 WHERE at.advogado_ID=:advogado_id
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$telefone = new Telefone();
			$telefone->setId($linha->id);
			$telefone->setNumero($linha->numero);
			$telefone->setDdd($linha->ddd);

			$telefones[] = $telefone;
		}

		return $telefones;
	}

	//**************************//		

	public static function insert($advogado)
	{

		$msg = null;
		$advogadoModel = new AdvogadoModel();
		$advogadoModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($advogado))
				throw new UserException('Advogado: O Advogado deve ser fornecido.');

			if (DataValidator::isEmpty($advogado->getNome()))
				throw new UserException('Advogado: O campo nome é obrigatório');

			if (DataValidator::isEmpty($advogado->getOab()))
				throw new UserException('Advogado: O campo OAB é obrigatório.');

			if (!DataValidator::isEmpty($advogado->getEmailContato()) && !filter_var($advogado->getEmailContato(), FILTER_VALIDATE_EMAIL))
				throw new UserException('Email do contato inválido.');

			/*if( DataValidator::isEmpty( $advogado->getEmails() ) )
					throw new UserException('É necessário fornecer, ao menos, 1 email.');*/

			$adv_cadastrado = self::getByOAB($advogado->getOab(), $advogadoModel->getDB());
			if (DataValidator::isEmpty($adv_cadastrado)) {

				$sql = " INSERT INTO advogado 
									(nome_advogado, 
									 data_entrada,
									 data_alteracao,
									 oab, 
									 status_advogado, 
									 empresa, 
									 cnpj, 
									 site, 
									 nome_contato, 
									 email_contato, 
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
									( :nome_advogado, 
									 :data_entrada,
									 :data_alteracao,
									 :oab, 
									 :status_advogado, 
									 :empresa,  
									 :cnpj, 
									 :site, 
									 :nome_contato, 
									 :email_contato, 
									 :logradouro, 
									 :numero, 
									 :complemento,
									 :bairro,
									 :cidade,
									 :estado,
									 :cep,
									 :usuario_id
									 ) ";

				$query = $advogadoModel->getDB()->prepare($sql);
				$query->bindValue(':nome_advogado', $advogado->getNome(), PDO::PARAM_STR);
				$query->bindValue(':oab', $advogado->getOab(), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($advogado->getStatus()))
					$query->bindValue(':status_advogado', $advogado->getStatus(), PDO::PARAM_STR);
				else
					$query->bindValue(':status_advogado', 'N', PDO::PARAM_STR);

				$query->bindValue(':empresa', $advogado->getEmpresa(), PDO::PARAM_STR);
				$query->bindValue(':data_entrada', date('Y-m-d H:i:s'), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':cnpj', $advogado->getCnpj(), PDO::PARAM_STR);
				$query->bindValue(':site', $advogado->getSite(), PDO::PARAM_STR);
				$query->bindValue(':nome_contato', $advogado->getNomeContato(), PDO::PARAM_STR);
				$query->bindValue(':email_contato', $advogado->getEmailContato(), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':logradouro', $advogado->getEndereco()->getLogradouro(), PDO::PARAM_STR);
				else
					$query->bindValue(':logradouro', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':numero', $advogado->getEndereco()->getNumero(), PDO::PARAM_STR);
				else
					$query->bindValue(':numero', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':complemento', $advogado->getEndereco()->getComplemento(), PDO::PARAM_STR);
				else
					$query->bindValue(':complemento', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':bairro', $advogado->getEndereco()->getBairro(), PDO::PARAM_STR);
				else
					$query->bindValue(':bairro', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':cidade', $advogado->getEndereco()->getCidade(), PDO::PARAM_STR);
				else
					$query->bindValue(':cidade', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':estado', $advogado->getEndereco()->getEstado(), PDO::PARAM_STR);
				else
					$query->bindValue(':estado', NULL, PDO::PARAM_NULL);

				if (!DataValidator::isEmpty($advogado->getEndereco()))
					$query->bindValue(':cep', $advogado->getEndereco()->getCep(), PDO::PARAM_STR);
				else
					$query->bindValue(':cep', NULL, PDO::PARAM_NULL);

				$query->bindValue(':usuario_id', $advogado->getUsuario()->getId(), PDO::PARAM_INT);

				$query->execute();
				$advogado->setId($advogadoModel->getDB()->lastInsertId());

				self::saveTelefone($advogado, $advogadoModel->getDB());
				ObservacaoModel::saveObservacao($advogado, $advogadoModel->getDB());
				self::saveEmail($advogado, $advogadoModel->getDB());

				$advogadoModel->getDB()->commit();
			} else
				throw new UserException('OAB já existente.');
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$advogadoModel->getDB()->rollback();
		}

		return array('msg' => $msg, 'advogado_id' => $advogado->getId(), 'advogado_nome' => $advogado->getNome());
	}

	public static function update($advogado)
	{

		$msg = null;
		$advogadoModel = new AdvogadoModel();
		$advogadoModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($advogado))
				throw new UserException('Advogado: O Advogado deve ser fornecido.');

			if (DataValidator::isEmpty($advogado->getNome()))
				throw new UserException('Advogado: O campo nome é obrigatório.');

			if (DataValidator::isEmpty($advogado->getOab()))
				throw new UserException('Advogado: O campo OAB é obrigatório.');

			if (!DataValidator::isEmpty($advogado->getEmailContato()) && !filter_var($advogado->getEmailContato(), FILTER_VALIDATE_EMAIL))
				throw new UserException('Email do contato inválido.');

			/*if( DataValidator::isEmpty( $advogado->getEmails() ) )
					throw new UserException('É necessário fornecer, ao menos, 1 email.');*/

			$sql = " UPDATE advogado SET
								nome_advogado=:nome_advogado, 
								data_alteracao=:data_alteracao,
								oab=:oab, 
								status_advogado=:status_advogado, 
								empresa=:empresa, 
								cnpj=:cnpj, 
								site=:site, 
								nome_contato=:nome_contato, 
								email_contato=:email_contato, 
								logradouro=:logradouro, 
								numero=:numero, 
								complemento=:complemento,
								bairro=:bairro,
								cidade=:cidade,
								estado=:estado,
								cep=:cep,
								usuario_id=:usuario_id
								WHERE id=:advogado_id
								";

			$query = $advogadoModel->getDB()->prepare($sql);
			$query->bindValue(':nome_advogado', $advogado->getNome(), PDO::PARAM_STR);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);
			$query->bindValue(':oab', $advogado->getOab(), PDO::PARAM_STR);
			$query->bindValue(':status_advogado', $advogado->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':empresa', $advogado->getEmpresa(), PDO::PARAM_STR);
			$query->bindValue(':cnpj', $advogado->getCnpj(), PDO::PARAM_STR);
			$query->bindValue(':site', $advogado->getSite(), PDO::PARAM_STR);
			$query->bindValue(':nome_contato', $advogado->getNomeContato(), PDO::PARAM_STR);
			$query->bindValue(':email_contato', $advogado->getEmailContato(), PDO::PARAM_STR);
			$query->bindValue(':logradouro', $advogado->getEndereco()->getLogradouro(), PDO::PARAM_STR);
			$query->bindValue(':numero', $advogado->getEndereco()->getNumero(), PDO::PARAM_STR);
			$query->bindValue(':complemento', $advogado->getEndereco()->getComplemento(), PDO::PARAM_STR);
			$query->bindValue(':bairro', $advogado->getEndereco()->getBairro(), PDO::PARAM_STR);
			$query->bindValue(':cidade', $advogado->getEndereco()->getCidade(), PDO::PARAM_STR);
			$query->bindValue(':estado', $advogado->getEndereco()->getEstado(), PDO::PARAM_STR);
			$query->bindValue(':cep', $advogado->getEndereco()->getCep(), PDO::PARAM_STR);
			$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_STR);
			$query->bindValue(':usuario_id', $advogado->getUsuario()->getId(), PDO::PARAM_INT);
			$query->execute();

			self::saveTelefone($advogado, $advogadoModel->getDB());
			ObservacaoModel::saveObservacao($advogado, $advogadoModel->getDB());
			self::saveEmail($advogado, $advogadoModel->getDB());

			$advogadoModel->getDB()->commit();

			self::atualizaQtdAlertas($advogado->getId(), $advogadoModel->getDB());
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$advogadoModel->getDB()->rollback();
		}

		return array('msg' => $msg);
	}

	//salva telefones
	public static function saveTelefone($advogado, $db)
	{

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Salva Telefone: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Salva Telefone: O Advogado deve ser identificado.');

		$telefones = $advogado->getTelefones();

		if (!DataValidator::isEmpty($telefones)) {
			foreach ($telefones as $tel) {

				if (!DataValidator::isEmpty($tel->getNumero()) && !DataValidator::isEmpty($tel->getDdd())) {

					if (DataValidator::isEmpty($tel->getId())) {
						$sql = " INSERT INTO advogado_tefefone (advogado_ID, ddd, numero) VALUES (:advogado_id, :ddd, :numero) ";

						$query = $db->prepare($sql);
						$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);
						$query->bindValue(':ddd', $tel->getDdd(), PDO::PARAM_STR);
						$query->bindValue(':numero', $tel->getNumero(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE advogado_tefefone SET ddd=:ddd, numero=:numero WHERE id=:tel_id";

						$query = $db->prepare($sql);
						$query->bindValue(':tel_id', $tel->getId(), PDO::PARAM_INT);
						$query->bindValue(':ddd', $tel->getDdd(), PDO::PARAM_STR);
						$query->bindValue(':numero', $tel->getNumero(), PDO::PARAM_STR);

						$query->execute();
					}
				}
			}
		}
	}


	//salva emails
	public static function saveEmail($advogado, $db)
	{

		if (DataValidator::isEmpty($advogado))
			throw new UserException('Salva Email: O Advogado deve ser fornecido.');

		if (DataValidator::isEmpty($advogado->getId()))
			throw new UserException('Salva Email: O Advogado deve ser identificado.');

		$emails = $advogado->getEmails();

		if (!DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {

				$endereco_email = trim($email->getEmailEndereco());

				if (!DataValidator::isEmpty($email->getEmailEndereco()) && filter_var($endereco_email, FILTER_VALIDATE_EMAIL)) {

					if (DataValidator::isEmpty($email->getId())) {
						$sql = " INSERT INTO advogado_email (email, advogado_ID, enviar) VALUES (:email, :advogado_id, :enviar) ";

						$query = $db->prepare($sql);
						$query->bindValue(':advogado_id', $advogado->getId(), PDO::PARAM_INT);
						$query->bindValue(':email', $endereco_email, PDO::PARAM_STR);
						$query->bindValue(':enviar', $email->getEnviar(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE advogado_email SET email=:email, enviar=:enviar WHERE id=:email_id";

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

	//*****************************************//

	public static function excluiTelefone($item_id)
	{

		$msg = null;
		$advogado_model = new AdvogadoModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Telefone: O Telefone deve ser identificado.');

			$sql = " DELETE FROM advogado_tefefone WHERE id=:item_id ";

			$query = $advogado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function excluiEmail($item_id)
	{

		$msg = null;
		$advogado_ID = null;
		$advogado_model = new AdvogadoModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Email: O Telefone deve ser identificado.');

			$sql = " SELECT advogado_ID FROM advogado_email WHERE id=:item_id ";

			$query = $advogado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();

			$linha = $query->fetchObject();
			$advogado_ID = $linha->advogado_ID;

			$sql = " DELETE FROM advogado_email WHERE id=:item_id ";

			$query = $advogado_model->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();

			if ($advogado_ID != null) {
				self::atualizaQtdAlertas($advogado_ID, $advogado_model->getDB());
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function excluiAdvogado($advogado_id)
	{

		$msg = null;
		$advogado_model = new AdvogadoModel();

		try {

			if (DataValidator::isEmpty($advogado_id))
				throw new UserException('Exclui Advogado: O Advogado deve ser identificado.');

			$sql = ' SELECT pr.id FROM processo pr
						 INNER JOIN advogado adv ON pr.advogado_ID=adv.id
						 WHERE pr.advogado_ID=:advogado_id
				';

			$query = $advogado_model->getDB()->prepare($sql);
			$query->bindValue(':advogado_id', $advogado_id, PDO::PARAM_INT);
			$query->execute();

			$linha = $query->fetchObject();

			if ($linha) {
				throw new UserException('Advogado vinculado a Processo(s). Não á possível excluir.');
			} else {
				$del = ' DELETE FROM advogado
							 WHERE advogado.id=:advogado_id ';

				$query = $advogado_model->getDB()->prepare($del);
				$query->bindValue(':advogado_id', $advogado_id, PDO::PARAM_INT);
				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//atualiza a contagem de alertas no registro do processo 
	public static function atualizaQtdAlertas($advogado_ID, $db = null)
	{

		if (is_null($db)) {
			$advogado_model = new AdvogadoModel();
			$db = $advogado_model->getDB();
		}

		if (DataValidator::isEmpty($advogado_ID))
			throw new UserException('Atualiza Alertas: O Advogado deve ser identificado.');

		$sql = " UPDATE processo SET alertas = Alertas(id) WHERE advogado_ID = :advogado_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':advogado_id', $advogado_ID, PDO::PARAM_INT);
		$query->execute();
	}
}
