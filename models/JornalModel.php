<?php
require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Jornal.class.php");
require_once("classes/Endereco.class.php");
require_once("classes/Telefone.class.php");
require_once("classes/Secretaria.class.php");
require_once("classes/Cidade.class.php");
require_once("classes/Email.class.php");
require_once("classes/Custo.class.php");
require_once("classes/Usuario.class.php");
require_once("classes/Perfil.class.php");
require_once("models/SecretariaModel.php");
require_once("models/CustoModel.php");
require_once("models/AlertaModel.php");

class JornalModel extends PersistModelAbstract
{

	/*
		lista todos os jornais
		*/
	public static function lista(
		$status = null,
		$secretaria_id = 0,
		$termo = null,
		$representante = null,
		$cidade = null,
		$estado = null,
		$email = null,
		$ativo = null,
		$endereco = null,
		$telefone = null,
		$pagina = 0,
		$qtd_pagina = 0
	) {

		$sql = " SELECT SQL_CALC_FOUND_ROWS DISTINCT js.jornal_ID, jr.* 
					 FROM jornal jr
					 LEFT JOIN jornal_secretaria js ON js.jornal_ID=jr.id
					 LEFT JOIN jornal_email je ON je.jornal_ID=jr.id
					 LEFT JOIN jornal_cidades jc ON jc.jornal_ID=jr.id
					 LEFT JOIN jornal_tel jt ON jr.id = jt.jornal_ID 
			";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.status_jornal =:status ";
		}

		if (!DataValidator::isEmpty($secretaria_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " js.secretaria_id =:secretaria_id ";
		}

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.nome_jornal LIKE _utf8 :termo COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($representante)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.nome_representante LIKE _utf8 :representante COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($endereco)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.logradouro LIKE _utf8 :endereco COLLATE utf8_unicode_ci OR ";
			$sql .= " jr.bairro LIKE _utf8 :endereco COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($cidade)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jc.cidade LIKE _utf8 :cidade COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($estado)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.estado_periodo =:estado ";
		}

		if (!DataValidator::isEmpty($email)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " je.email LIKE :email ";
		}

		if (!DataValidator::isEmpty($telefone)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jt.numero LIKE _utf8 :telefone COLLATE utf8_unicode_ci ";
		}

		if (!DataValidator::isEmpty($ativo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " jr.ativo =:ativo ";
		}

		$sql .= " ORDER BY jr.nome_jornal ";

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$jornais = array();
		$jornalModel = new JornalModel();

		$query = $jornalModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($secretaria_id))
			$query->bindValue(':secretaria_id', $secretaria_id, PDO::PARAM_INT);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($representante))
			$query->bindValue(':representante', "%$representante%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($endereco))
			$query->bindValue(':endereco', "%$endereco%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($cidade))
			$query->bindValue(':cidade', "%$cidade%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($estado))
			$query->bindValue(':estado', $estado, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($email))
			$query->bindValue(':email', "%$email%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($telefone))
			$query->bindValue(':telefone', "%$telefone%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($ativo))
			$query->bindValue(':ativo', $ativo, PDO::PARAM_STR);

		$query->execute();

		//*******
		$query_num_linhas = $jornalModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query_num_linhas->execute();

		$num_linhas = $query_num_linhas->fetchObject();
		$totalLinhas = $num_linhas ? $num_linhas->frows : 0;
		//*******

		while ($linha = $query->fetchObject()) {
			$jornal = new Jornal();
			$jornal->setId($linha->id);
			$jornal->setNome($linha->nome_jornal);
			$jornal->setStatus($linha->status_jornal);
			$jornal->setAtivo($linha->ativo);
			$jornal->setDataConfirmacao($linha->data_confirmacao);
			$jornal->setEstadoPeriodo($linha->estado_periodo);

			$endereco = new Endereco();
			$endereco->setCidade($linha->cidade);
			$endereco->setEstado($linha->estado);
			$jornal->setEndereco($endereco);

			$jornal->setCidades(self::get_cidades($jornal, $jornalModel->getDB()));

			$jornais[] = $jornal;
		}

		return array('jornais' => $jornais, 'totalLinhas' => $totalLinhas);
	}

	public static function getById($jornal_id = 0, $db = null)
	{

		$jornal = null;

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal_id))
			throw new UserException('Jornal: O Jornal deve ser identificado.');

		$sql = " SELECT jr.*, jc.*, u.nome_usuario FROM jornal jr
					 LEFT JOIN jornal_secretaria js ON js.jornal_ID=jr.id
					 LEFT JOIN usuario u ON jr.usuario_id=u.id
					 LEFT JOIN jornal_email je ON je.jornal_ID=jr.id
					 LEFT JOIN jornal_custo jc ON jc.jornal_ID=jr.id
			";

		$sql .= " WHERE jr.id=:jornal_id ";


		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal_id, PDO::PARAM_INT);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$jornal = new Jornal();
		$jornal->setId($linha->id);
		$jornal->setNome($linha->nome_jornal);
		$jornal->setStatus($linha->status_jornal);
		$jornal->setAtivo($linha->ativo);
		$jornal->setDataEntrada($linha->data_entrada);
		$jornal->setDataConfirmacao($linha->data_confirmacao);
		$jornal->setDataAlteracao($linha->data_alteracao);
		$jornal->setNomeRepresentante($linha->nome_representante);
		$jornal->setContatoRepresentante($linha->contato_representante);
		$jornal->setFechamento($linha->fechamento);
		$jornal->setComposicao($linha->composicao);
		$jornal->setObservacoes($linha->observacoes);
		$jornal->setEstadoPeriodo($linha->estado_periodo);

		$custo = new Custo();
		$custo->setMedida($linha->medida);
		$custo->setValorForense($linha->valor_forense);
		$custo->setNegociacao($linha->negociacao);
		$custo->setDesconto($linha->desconto);
		$custo->setValorPadrao($linha->valor_padrao);
		$custo->setValorDje($linha->valor_dje);
		$custo->setValorEmpregos($linha->valor_empregos);
		$custo->setValorPublicidade($linha->valor_publicidade);
		$jornal->setCusto($custo);

		$endereco = new Endereco();
		$endereco->setLogradouro($linha->logradouro);
		$endereco->setNumero($linha->numero);
		$endereco->setComplemento($linha->complemento);
		$endereco->setBairro($linha->bairro);
		$endereco->setCidade($linha->cidade);
		$endereco->setEstado($linha->estado);
		$endereco->setCep($linha->cep);
		$jornal->setEndereco($endereco);

		$usuario = new Usuario();
		$usuario->setId($linha->usuario_ID);
		$usuario->setNome($linha->nome_usuario);
		$jornal->setUsuario($usuario);

		$jornal->setTelefones(self::getTelefones($jornal, $db));
		$jornal->setEmails(self::getEmails($jornal, $db));
		$jornal->setAlertas(AlertaModel::getAlertasJornal($jornal, $db));
		$jornal->setSecretarias(self::getSecretarias($jornal, $db));
		$jornal->setCidades(self::get_cidades($jornal, $db));

		return $jornal;
	}

	//validação se exatamente o nome, sem like, já existe
	public static function getByNome($param = null, $db = null)
	{

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($param)) {
			throw new UserException('Jornal: O nome do Jornal deve ser fornecido.');
		}

		$sql = " SELECT nome_jornal
					 FROM jornal
					 WHERE nome_jornal = _utf8 :param COLLATE utf8_unicode_ci
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':param', $param, PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if ($linha)
			return 'existe';
		else
			return null;
	}

	//**********************//

	public static function getTelefones($jornal, $db = null)
	{
		$telefones = array();

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Jornal Telefones: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Jornal Telefones: O Jornal deve ser identificado.');

		$sql = " SELECT jt.*
					 FROM  jornal_tel jt
					 INNER JOIN jornal jr ON jt.jornal_ID=jr.id
					 WHERE jt.jornal_ID=:jornal_id
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);

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

	public static function getEmails($jornal, $db = null)
	{
		$emails = array();

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Jornal Emails: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Jornal Emails: O Jornal deve ser identificado.');

		$sql = " SELECT je.id, je.email 
					 FROM  jornal jr
					 LEFT JOIN jornal_email je ON je.jornal_ID=jr.id
					 WHERE je.jornal_ID=:jornal_id
					 ";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$email = new Email();
			$email->setId($linha->id);
			$email->setEmailEndereco($linha->email);
			$emails[] = $email;
		}

		return $emails;
	}

	public static function getSecretarias($jornal, $db = null)
	{
		$secretarias = array();

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Jornal Secretarias: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Jornal Secretarias: O Jornal deve ser identificado.');

		$sql = " SELECT s.*
					 FROM jornal_secretaria js
					 INNER JOIN secretaria s ON js.secretaria_ID=s.id
					 WHERE js.jornal_ID=:jornal_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$secretaria = new Secretaria();
			$secretaria->setId($linha->id);
			$secretaria->setNome($linha->nome_secretaria);
			$secretarias[] = $secretaria;
		}

		return $secretarias;
	}

	public static function get_periodos($jornal_id)
	{
		$periodos = array();

		if (DataValidator::isEmpty($jornal_id))
			return $periodos;

		$sql = " SELECT jp.periodo
					 FROM jornal_periodo jp
					 INNER JOIN jornal jr ON jp.jornal_ID=jr.id
					 WHERE jp.jornal_ID=:jornal_id; ";

		$jornalModel = new JornalModel();
		$query = $jornalModel->getDB()->prepare($sql);
		$query->bindValue(':jornal_id', $jornal_id, PDO::PARAM_INT);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$periodos[] = $linha->periodo;
		}

		return $periodos;
	}

	public static function get_cidades($jornal, $db = null)
	{
		$cidades = array();

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Jornal Cidades: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Jornal Cidades: O Jornal deve ser identificado.');

		$sql = " SELECT jc.*
					 FROM jornal_cidades jc
					 INNER JOIN jornal jr ON jc.jornal_ID=jr.id
					 WHERE jc.jornal_ID=:jornal_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();

		while ($linha = $query->fetchObject()) {
			$cidade = new Cidade();
			$cidade->setId($linha->id);
			$cidade->setNome($linha->cidade);
			$cidade->setJornalId($linha->jornal_ID);

			$cidades[] = $cidade;
		}

		return $cidades;
	}


	//*****************************************//

	public static function excluiTelefone($item_id)
	{

		$msg = null;
		$jornalModel = new JornalModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Telefone: O Telefone deve ser identificado.');

			$sql = " DELETE FROM jornal_tel WHERE id=:item_id ";

			$query = $jornalModel->getDB()->prepare($sql);
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
		$jornalModel = new JornalModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Email: O Telefone deve ser identificado.');

			$sql = " DELETE FROM jornal_email WHERE id=:item_id ";

			$query = $jornalModel->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function excluiPeriodo($jornal, $db = null)
	{

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Exclui Períodos - Jornal: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Exclui Períodos - Jornal: O Jornal deve ser identificado.');

		$sql = ' DELETE FROM jornal_periodo WHERE jornal_ID=:jornal_id ';
		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();
	}

	public static function excluiSecretaria($secretaria_id, $jornal_id)
	{
		$msg = null;
		$jornalModel = new JornalModel();

		try {
			if (DataValidator::isEmpty($secretaria_id))
				throw new UserException('Exclui Secretaria: A Secretaria deve ser identificada.');

			if (DataValidator::isEmpty($jornal_id))
				throw new UserException('Exclui Secretaria: O Jornal deve ser identificado.');

			$sql = " DELETE FROM jornal_secretaria WHERE secretaria_ID=:secretaria_id AND jornal_ID=:jornal_id ";

			$query = $jornalModel->getDB()->prepare($sql);
			$query->bindValue(':secretaria_id', $secretaria_id, PDO::PARAM_INT);
			$query->bindValue(':jornal_id', $jornal_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	public static function excluiCidade($item_id)
	{

		$msg = null;
		$jornalModel = new JornalModel();

		try {
			if (DataValidator::isEmpty($item_id))
				throw new UserException('Exclui Cidade: A Cidade deve ser identificada.');

			$sql = " DELETE FROM jornal_cidades WHERE id=:item_id ";

			$query = $jornalModel->getDB()->prepare($sql);
			$query->bindValue(':item_id', $item_id, PDO::PARAM_INT);

			$query->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	//****************************//

	//salva secretarias
	public static function saveSecretaria($jornal, $db = null)
	{

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Salva Secretarias - O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Salva Secretarias - O Jornal deve ser identificado.');

		$secretarias = $jornal->getSecretarias();

		if (!DataValidator::isEmpty($secretarias)) {
			SecretariaModel::exclui($jornal, $db);

			foreach ($secretarias as $secr) {
				$sql = ' INSERT INTO jornal_secretaria (jornal_ID, secretaria_id) VALUES (:jornal_id, :secretaria_id) ';
				$query = $db->prepare($sql);
				$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
				$query->bindValue(':secretaria_id', $secr->getId(), PDO::PARAM_INT);
				$query->execute();
			}
		}
	}

	//salva telefones
	public static function saveTelefone($jornal, $db)
	{

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Salva Telefones: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Salva Telefones: O Jornal deve ser identificado.');

		$telefones = $jornal->getTelefones();

		if (!DataValidator::isEmpty($telefones)) {
			foreach ($telefones as $tel) {

				if (!DataValidator::isEmpty($tel->getNumero()) && !DataValidator::isEmpty($tel->getDdd())) {

					if (DataValidator::isEmpty($tel->getId())) {
						$sql = " INSERT INTO jornal_tel (jornal_ID, ddd, numero) VALUES (:jornal_id, :ddd, :numero) ";
						$query = $db->prepare($sql);
						$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
						$query->bindValue(':ddd', $tel->getDdd(), PDO::PARAM_STR);
						$query->bindValue(':numero', $tel->getNumero(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE jornal_tel SET ddd=:ddd, numero=:numero WHERE id=:tel_id";
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
	public static function saveEmail($jornal, $db)
	{

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Salva Emails: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Salva Emails: O Jornal deve ser identificado.');

		$emails = $jornal->getEmails();

		if (!DataValidator::isEmpty($emails)) {
			foreach ($emails as $email) {

				if (!DataValidator::isEmpty($email->getEmailEndereco()) && filter_var($email->getEmailEndereco(), FILTER_VALIDATE_EMAIL)) {

					if (DataValidator::isEmpty($email->getId())) {
						$sql = " INSERT INTO jornal_email (email, jornal_ID) VALUES (:email, :jornal_id) ";

						$query = $db->prepare($sql);
						$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
						$query->bindValue(':email', $email->getEmailEndereco(), PDO::PARAM_STR);

						$query->execute();
					} else {

						$sql = " UPDATE jornal_email SET email=:email WHERE id=:email_id";

						$query = $db->prepare($sql);
						$query->bindValue(':email_id', $email->getId(), PDO::PARAM_INT);
						$query->bindValue(':email', $email->getEmailEndereco(), PDO::PARAM_STR);

						$query->execute();
					}
				}
			}
		}
	}

	//salva periodos
	public static function savePeriodo($jornal, $db = null)
	{

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Salva Períodos - O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Salva Períodos - O Jornal deve ser identificado.');

		$periodos = $jornal->getPeriodos();

		if (!DataValidator::isEmpty($periodos)) {
			self::excluiPeriodo($jornal, $db);

			foreach ($periodos as $periodo) {
				$sql = ' INSERT INTO jornal_periodo (jornal_ID, periodo) VALUES (:jornal_id, :periodo) ';
				$query = $db->prepare($sql);
				$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
				$query->bindValue(':periodo', $periodo, PDO::PARAM_INT);
				$query->execute();
			}
		}
	}

	//salva cidades
	public static function saveCidade($jornal, $db)
	{

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Salva Cidades: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Salva Cidades: O Jornal deve ser identificado.');

		$cidades = $jornal->getCidades();

		if (!DataValidator::isEmpty($cidades)) {
			foreach ($cidades as $cidade) {

				if (!DataValidator::isEmpty($cidade->getNome())) {

					if (DataValidator::isEmpty($cidade->getId())) {
						$sql = " INSERT INTO jornal_cidades (cidade, jornal_ID) VALUES (:cidade, :jornal_id) ";

						$query = $db->prepare($sql);
						$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
						$query->bindValue(':cidade', $cidade->getNome(), PDO::PARAM_STR);

						$query->execute();
					} else {
						$sql = " UPDATE jornal_cidades SET cidade=:cidade WHERE id=:jornal_id ";

						$query = $db->prepare($sql);
						$query->bindValue(':jornal_id', $cidade->getId(), PDO::PARAM_INT);
						$query->bindValue(':cidade', $cidade->getNome(), PDO::PARAM_STR);

						$query->execute();
					}
				}
			}
		}
	}

	//**********************//

	public static function insert($jornal)
	{
		$msg = null;
		$jornalModel = new JornalModel();
		$jornalModel->getDB()->beginTransaction();

		try {

			$data_conf = null;

			if (DataValidator::isEmpty($jornal))
				throw new UserException('O Jornal deve ser fornecido.');

			/*if( DataValidator::isEmpty( $jornal->getAtivo() ) )
					throw new UserException('O campo Ativo é obrigatório.');*/

			if (DataValidator::isEmpty($jornal->getStatus()))
				throw new UserException('O campo Status é obrigatório.');

			if (DataValidator::isEmpty($jornal->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			if (!DataValidator::isEmpty($jornal->getDataConfirmacao())) {
				$data = explode("/", $jornal->getDataConfirmacao());
				$data_conf = $data[2] . '-' . $data[1] . '-' . $data[0];
			};

			if (!DataValidator::isEmpty($data_conf)) {
				if (strtotime($data_conf) > strtotime(date("Y-m-d")))
					throw new UserException('A data de Confirmação não pode ser superior a data de hoje.');
			}

			$jornal_cadastrado = self::getByNome($jornal->getNome(), $jornalModel->getDB());
			if (DataValidator::isEmpty($jornal_cadastrado)) {

				$sql = " INSERT INTO jornal (
												 status_jornal, 
												 ativo,
												 nome_jornal, 
												 data_entrada, 
												 data_alteracao,
												 data_confirmacao,
												 nome_representante,
												 contato_representante,
												 logradouro,
												 numero,
												 complemento,
												 bairro,
												 cidade,
												 estado,
												 cep,
												 fechamento,
												 composicao,
												 observacoes,
												 estado_periodo,
												 usuario_ID) 
												 VALUES (
												  :status_jornal, 
												  :ativo,
												  :nome_jornal, 
												  :data_entrada, 
												  :data_alteracao,
												  :data_confirmacao,
												  :nome_representante,
												  :contato_representante,
												  :logradouro,
												  :numero,
												  :complemento,
												  :bairro,
												  :cidade,
												  :estado,
												  :cep,
												  :fechamento,
												  :composicao,
												  :observacoes,
												  :estado_periodo,
												  :usuario_id) ";

				$query = $jornalModel->getDB()->prepare($sql);
				$query->bindValue(':status_jornal', $jornal->getStatus(), PDO::PARAM_STR);
				$query->bindValue(':ativo', $jornal->getAtivo(), PDO::PARAM_STR);
				$query->bindValue(':nome_jornal', $jornal->getNome(), PDO::PARAM_STR);
				$query->bindValue(':data_entrada', date("Y-m-d H:i:s"), PDO::PARAM_STR);
				$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

				if (!DataValidator::isEmpty($data_conf))
					$query->bindValue(':data_confirmacao', $data_conf, PDO::PARAM_STR);
				else
					$query->bindValue(':data_confirmacao', NULL, PDO::PARAM_NULL);

				$query->bindValue(':nome_representante', $jornal->getNomeRepresentante(), PDO::PARAM_STR);
				$query->bindValue(':contato_representante', $jornal->getContatoRepresentante(), PDO::PARAM_STR);

				$query->bindValue(':logradouro', $jornal->getEndereco()->getLogradouro(), PDO::PARAM_STR);
				$query->bindValue(':numero', $jornal->getEndereco()->getNumero(), PDO::PARAM_STR);
				$query->bindValue(':complemento', $jornal->getEndereco()->getComplemento(), PDO::PARAM_STR);
				$query->bindValue(':bairro', $jornal->getEndereco()->getBairro(), PDO::PARAM_STR);
				$query->bindValue(':cidade', $jornal->getEndereco()->getCidade(), PDO::PARAM_STR);
				$query->bindValue(':estado', $jornal->getEndereco()->getEstado(), PDO::PARAM_STR);
				$query->bindValue(':cep', $jornal->getEndereco()->getCep(), PDO::PARAM_STR);

				$query->bindValue(':fechamento', $jornal->getFechamento(), PDO::PARAM_STR);
				$query->bindValue(':composicao', $jornal->getComposicao(), PDO::PARAM_STR);
				$query->bindValue(':observacoes', $jornal->getObservacoes(), PDO::PARAM_STR);
				$query->bindValue(':estado_periodo', $jornal->getEstadoPeriodo(), PDO::PARAM_STR);

				$query->bindValue(':usuario_id', $jornal->getUsuario()->getId(), PDO::PARAM_INT);

				$query->execute();
				$jornal->setId($jornalModel->getDB()->lastInsertId());

				CustoModel::insert($jornal, $jornalModel->getDB());
				self::saveSecretaria($jornal, $jornalModel->getDB());
				self::saveTelefone($jornal, $jornalModel->getDB());
				self::saveEmail($jornal, $jornalModel->getDB());
				self::savePeriodo($jornal, $jornalModel->getDB());
				self::saveCidade($jornal, $jornalModel->getDB());

				$jornalModel->getDB()->commit();

				self::atualizaQtdAlertas($jornal, $jornalModel->getDB());
			} else
				throw new UserException('Nome do Jornal já existente.');
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$jornalModel->getDB()->rollback();
		}

		return array('msg' => $msg, 'jornal_id' => $jornal->getId());
	}

	public static function update($jornal)
	{
		$msg = null;
		$jornalModel = new JornalModel();
		$jornalModel->getDB()->beginTransaction();

		try {

			if (DataValidator::isEmpty($jornal))
				throw new UserException('O Jornal deve ser fornecido.');

			if (DataValidator::isEmpty($jornal->getId()))
				throw new UserException('O Jornal deve ser identificado.');

			/*if( DataValidator::isEmpty( $jornal->getAtivo() ) )
					throw new UserException('O campo Ativo é obrigatório.');*/

			if (DataValidator::isEmpty($jornal->getStatus()))
				throw new UserException('O campo Status é obrigatório.');

			if (DataValidator::isEmpty($jornal->getNome()))
				throw new UserException('O campo Nome é obrigatório.');

			if (!DataValidator::isEmpty($jornal->getDataConfirmacao())) {
				$data = explode("/", $jornal->getDataConfirmacao());
				$data_conf = $data[2] . '-' . $data[1] . '-' . $data[0];
			};

			if (isset($data_conf) && !DataValidator::isEmpty($data_conf)) {
				if (strtotime($data_conf) > strtotime(date("Y-m-d")))
					throw new UserException('A data de Confirmação não pode ser superior a data de hoje.');
			}

			$sql = " UPDATE jornal SET status_jornal=:status_jornal, 
										   ativo=:ativo,
										   nome_jornal=:nome_jornal, 
										   data_alteracao=:data_alteracao,
										   data_confirmacao=:data_confirmacao,
										   nome_representante=:nome_representante,
										   contato_representante=:contato_representante,
										   logradouro=:logradouro,
										   numero=:numero,
										   complemento=:complemento,
										   bairro=:bairro,
										   cidade=:cidade,
										   estado=:estado,
										   cep=:cep,
										   fechamento=:fechamento,
										   composicao=:composicao,
										   observacoes=:observacoes,
										   estado_periodo=:estado_periodo,
										   usuario_id=:usuario_id
										   WHERE id=:jornal_id; ";

			$query = $jornalModel->getDB()->prepare($sql);
			$query->bindValue(':status_jornal', $jornal->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':ativo', $jornal->getAtivo(), PDO::PARAM_STR);
			$query->bindValue(':nome_jornal', $jornal->getNome(), PDO::PARAM_STR);
			$query->bindValue(':data_alteracao', date("Y-m-d H:i:s"), PDO::PARAM_STR);

			if (!DataValidator::isEmpty($jornal->getDataConfirmacao()))
				$query->bindValue(':data_confirmacao', $data_conf, PDO::PARAM_STR);
			else
				$query->bindValue(':data_confirmacao', NULL, PDO::PARAM_NULL);

			$query->bindValue(':nome_representante', $jornal->getNomeRepresentante(), PDO::PARAM_STR);
			$query->bindValue(':contato_representante', $jornal->getContatoRepresentante(), PDO::PARAM_STR);

			$query->bindValue(':logradouro', $jornal->getEndereco()->getLogradouro(), PDO::PARAM_STR);
			$query->bindValue(':numero', $jornal->getEndereco()->getNumero(), PDO::PARAM_STR);
			$query->bindValue(':complemento', $jornal->getEndereco()->getComplemento(), PDO::PARAM_STR);
			$query->bindValue(':bairro', $jornal->getEndereco()->getBairro(), PDO::PARAM_STR);
			$query->bindValue(':cidade', $jornal->getEndereco()->getCidade(), PDO::PARAM_STR);
			$query->bindValue(':estado', $jornal->getEndereco()->getEstado(), PDO::PARAM_STR);
			$query->bindValue(':cep', $jornal->getEndereco()->getCep(), PDO::PARAM_STR);

			$query->bindValue(':fechamento', $jornal->getFechamento(), PDO::PARAM_STR);
			$query->bindValue(':composicao', $jornal->getComposicao(), PDO::PARAM_STR);
			$query->bindValue(':observacoes', $jornal->getObservacoes(), PDO::PARAM_STR);
			$query->bindValue(':estado_periodo', $jornal->getEstadoPeriodo(), PDO::PARAM_STR);
			$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);

			//$jr = self::getById( $jornal->getId(), $jornalModel->getDB() );
			//$data_conf = date('d/m/Y', strtotime( $jr->getDataConfirmacao() ));
			//$user_responsavel = $jr->getUsuario()->getId();

			//if( $data_conf != $jornal->getDataConfirmacao() )
			$query->bindValue(':usuario_id', $jornal->getUsuario()->getId(), PDO::PARAM_INT);
			//else
			//	$query->bindValue(':usuario_id', $user_responsavel, PDO::PARAM_INT);	

			$query->execute();

			CustoModel::update($jornal, $jornalModel->getDB());
			self::saveSecretaria($jornal, $jornalModel->getDB());
			self::saveTelefone($jornal, $jornalModel->getDB());
			self::saveEmail($jornal, $jornalModel->getDB());
			self::savePeriodo($jornal, $jornalModel->getDB());
			self::saveCidade($jornal, $jornalModel->getDB());

			$jornalModel->getDB()->commit();

			self::atualizaQtdAlertas($jornal, $jornalModel->getDB());
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$jornalModel->getDB()->rollback();
		}

		return array('msg' => $msg);
	}

	//no processo, vincula jornal a secretaria
	public static function vinculaSecretaria($processo, $db = null)
	{

		$msg = null;

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		try {

			if (DataValidator::isEmpty($processo))
				throw new UserException('O Processo deve ser fornecido.');

			if (DataValidator::isEmpty($processo->getId()))
				throw new UserException('O Processo deve ser identificado.');

			if (DataValidator::isEmpty($processo->getSecretaria()))
				throw new UserException('A Secretaria deve ser fornecida.');

			if (DataValidator::isEmpty($processo->getSecretaria()->getId()))
				throw new UserException('A Secretaria deve ser identificada.');

			if (DataValidator::isEmpty($processo->getJornal()))
				throw new UserException('O Jornal deve ser fornecido.');

			if (DataValidator::isEmpty($processo->getJornal()->getId()))
				throw new UserException('O Jornal deve ser identificado.');

			$sql = " SELECT * FROM jornal_secretaria WHERE jornal_ID=:jornal_id AND secretaria_ID=:secretaria_id ";

			$query = $db->prepare($sql);
			$query->bindValue(':jornal_id', $processo->getJornal()->getId(), PDO::PARAM_INT);
			$query->bindValue(':secretaria_id', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
			$query->execute();

			$linha = $query->fetchObject();
			if (!$linha) {

				$insert = " INSERT INTO jornal_secretaria SET jornal_ID=:jornal_id, secretaria_ID=:secretaria_id ";

				$query = $db->prepare($insert);
				$query->bindValue(':jornal_id', $processo->getJornal()->getId(), PDO::PARAM_INT);
				$query->bindValue(':secretaria_id', $processo->getSecretaria()->getId(), PDO::PARAM_INT);
				$query->execute();
			}
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}

	/*
		lista os jornais de uma dada secretaria, usada para popular select de jornais de acordo com secretaria
		*/
	public static function listaBySecretaria($secretaria_id = 0)
	{

		if (DataValidator::isEmpty($secretaria_id))
			return null;

		if (!DataValidator::isNumeric($secretaria_id))
			return null;

		$sql = " SELECT jr.* FROM jornal jr
					 INNER JOIN jornal_secretaria js ON js.jornal_ID=jr.id					 
			";

		if (!DataValidator::isEmpty($secretaria_id))
			$sql .= " WHERE js.secretaria_id=:secretaria_id ";

		$sql .= " ORDER BY jr.status_jornal DESC, jr.data_confirmacao DESC ";

		$jornais = array();
		$jornalModel = new JornalModel();

		$query = $jornalModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($secretaria_id))
			$query->bindValue(':secretaria_id', $secretaria_id, PDO::PARAM_INT);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$jornal = new Jornal();
			$jornal->setId($linha->id);
			$jornal->setNome($linha->nome_jornal);
			$jornal->setStatus($linha->status_jornal);
			$jornal->setDataConfirmacao($linha->data_confirmacao);

			$jornais[] = $jornal;
		}

		return $jornais;
	}

	/*
		resgata um jornal de acordo com a secretaria fornecida, usada na Entrada do processo
		*/
	public static function getBySecretaria($secretaria_id, $db = null)
	{
		$jornal = null;

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($secretaria_id))
			throw new UserException('Jornal: A secretaria deve ser identificada.');

		$sql = " SELECT jr.id, jr.data_confirmacao , jc.valor_padrao
					 FROM jornal jr
					 INNER JOIN jornal_secretaria js ON js.jornal_ID=jr.id
					 INNER JOIN usuario u ON jr.usuario_id=u.id
					 LEFT JOIN jornal_custo jc ON jc.jornal_ID=jr.id
					 WHERE js.secretaria_id=:secretaria_id AND jr.ativo='A'
					 ORDER BY jr.status_jornal DESC, jr.data_confirmacao DESC LIMIT 0,1
			";

		$query = $db->prepare($sql);

		if (!DataValidator::isEmpty($secretaria_id))
			$query->bindValue(':secretaria_id', $secretaria_id, PDO::PARAM_INT);

		$query->execute();
		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$jornal = new Jornal();
		$jornal->setId($linha->id);
		$jornal->setDataConfirmacao($linha->data_confirmacao);

		if (!DataValidator::isEmpty($linha->valor_padrao)) {
			$custo = new Custo();
			$custo->setValorPadrao($linha->valor_padrao);
			$jornal->setCusto($custo);
		}

		return $jornal;
	}

	//atualiza a contagem de alertas no registro do processo 
	public static function atualizaQtdAlertas($jornal, $db = null)
	{

		if (is_null($db)) {
			$jornalModel = new JornalModel();
			$db = $jornalModel->getDB();
		}

		if (DataValidator::isEmpty($jornal))
			throw new UserException('Atualiza Alertas: O Jornal deve ser fornecido.');

		if (DataValidator::isEmpty($jornal->getId()))
			throw new UserException('Atualiza Alertas: O Jornal deve ser identificado.');

		$sql = " UPDATE processo SET alertas = Alertas(id) WHERE Jornal_ID = :jornal_id; ";

		$query = $db->prepare($sql);
		$query->bindValue(':jornal_id', $jornal->getId(), PDO::PARAM_INT);
		$query->execute();
	}
}
