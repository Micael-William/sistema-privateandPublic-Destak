<?php
$absolute_path = $_SERVER["DOCUMENT_ROOT"];
require_once($absolute_path . "/sts/lib/PersistModelAbstract.php");
require_once($absolute_path . "/sts/lib/UserException.php");
require_once($absolute_path . "/sts/lib/DataFilter.php");
require_once($absolute_path . "/sts/classes/Entidade.class.php");
require_once($absolute_path . "/sts/classes/PessoaFisica.class.php");
require_once($absolute_path . "/sts/classes/PessoaJuridica.class.php");
require_once($absolute_path . "/sts/classes/Cidade.class.php");
require_once($absolute_path . "/sts/models/EnderecoModel.php");
require_once($absolute_path . "/sts/classes/Nacionalidade.class.php");
require_once($absolute_path . "/sts/models/RepresentanteModel.php");

class EntidadeModel extends PersistModelAbstract
{

	public static function lista($status = null, $contrato_id = 0, $termo = null, $documento = null, $email = null, $pagina = 0, $qtd_pagina = 0)
	{
		$sql = " SELECT SQL_CALC_FOUND_ROWS e.*, p.*
					 FROM entidade e
					 INNER JOIN pessoa p ON e.pessoa_ID=p.ID
					 LEFT JOIN pessoa_juridica pj ON pj.pessoa_ID=p.ID
					 LEFT JOIN pessoa_fisica pf ON pf.pessoa_ID=p.ID
					 ";

		$where = false;

		if (!DataValidator::isEmpty($status)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " e.status_entidade = :status ";
		}

		if (!DataValidator::isEmpty($contrato_id)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " e.contrato_ID = :contrato_id ";
		}

		if (!DataValidator::isEmpty($termo)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " (p.nome LIKE _utf8 :termo COLLATE utf8_unicode_ci OR pj.nome_fantasia LIKE _utf8 :termo COLLATE utf8_unicode_ci) ";
		}

		if (!DataValidator::isEmpty($documento)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.num_doc_ident LIKE :documento ";
		}

		if (!DataValidator::isEmpty($email)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.email LIKE :email ";
		}

		//$sql.= ' ORDER BY ID DESC ';

		if (!DataValidator::isEmpty($pagina) && !DataValidator::isEmpty($qtd_pagina))
			$sql .= " LIMIT " . ($pagina - 1) * $qtd_pagina . "," . $qtd_pagina;

		$entidades = array();
		$entidadeModel = new EntidadeModel();

		$query = $entidadeModel->getDB()->prepare($sql);

		if (!DataValidator::isEmpty($status))
			$query->bindValue(':status', $status, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($contrato_id))
			$query->bindValue(':contrato_id', $contrato_id, PDO::PARAM_INT);

		if (!DataValidator::isEmpty($termo))
			$query->bindValue(':termo', "%$termo%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($documento))
			$query->bindValue(':documento', "%$documento%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($email))
			$query->bindValue(':email', "%$email%", PDO::PARAM_STR);

		$query->execute();

		while ($linha = $query->fetchObject()) {
			$entidade = new Entidade();
			$entidade->setStatus($linha->status_entidade);

			if ($linha->tipo_pessoa == 'F')
				$pessoa = new PessoaFisica();
			else
				$pessoa = new PessoaJuridica();

			$pessoa->setId($linha->pessoa_ID);
			$pessoa->setNome($linha->nome);
			$pessoa->setEmail($linha->email);
			$pessoa->setNumDoc($linha->num_doc_ident);

			$entidade->setPessoa($pessoa);
			$entidades[] = $entidade;
		}

		$query = $entidadeModel->getDB()->prepare("SELECT FOUND_ROWS() as frows;");
		$query->execute();

		$linha = $query->fetchObject();
		$total_linhas = $linha ? $linha->frows : 0;

		return array('entidades' => $entidades, 'total_linhas' => $total_linhas);
	}

	/*
		param origem: quando busca por documento/emails recebe origem.
		*/
	public static function getBy($param = null, $documento = null, $email = null, $origem = null, $db = null)
	{

		$entidade = null;

		if (is_null($db)) {
			$entidadeModel = new EntidadeModel();
			$db = $entidadeModel->getDB();
		}

		if (DataValidator::isEmpty($origem) && DataValidator::isEmpty($param))
			throw new UserException('Entidade: O Parâmetro deve ser fornecido.');

		$sql = " SELECT e.status_entidade, e.entidade_superior_ID, p.tipo_pessoa, p.ID, p.nome, p.email, p.num_doc_ident, p.data_hora, p.tipo_pessoa, p.nacionalidade_ID,
					 pf.passaporte, pf.vcto_passaporte, pf.pais_emissor_passaporte_ID, pf.data_nascimento, pf.idade, pf.pis_pasep, pf.rg, pf.cidade_nascimento_ID,
					 pf.orgao_expedidor, pf.estado_expeditor_ID, p_.nome as entidade_superior,
					 pj.nome_fantasia, pj.is_nacional, pj.is_nacional, pj.inscricao_municipal, pj.inscricao_estadual, pj.logomarca,
					 c.nome_cidade, c.estado_ID, c.pais_ID, est.sigla as sigla_estado
					 FROM entidade e
					 INNER JOIN pessoa p ON e.pessoa_ID=p.ID
					 LEFT JOIN pessoa_juridica pj ON pj.pessoa_ID=p.ID
					 LEFT JOIN pessoa_fisica pf ON pf.pessoa_ID=p.ID					 
					 LEFT JOIN cidade c ON pf.cidade_nascimento_ID=c.ID			
					 LEFT JOIN estado est ON c.estado_ID=est.ID
					 LEFT JOIN pessoa p_ ON e.entidade_superior_ID=p_.ID
			";

		$where = false;

		if (is_numeric($param)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.ID=:param ";
		} else {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " (p.nome LIKE _utf8 :param COLLATE utf8_unicode_ci OR pj.nome_fantasia LIKE _utf8 :param COLLATE utf8_unicode_ci) ";
		}

		if (!DataValidator::isEmpty($documento)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.num_doc_ident=:documento ";
		}

		if (!DataValidator::isEmpty($email)) {
			if (!$where) {
				$where = true;
				$sql .= "WHERE ";
			} else {
				$sql .= "AND ";
			}
			$sql .= " p.email=:email ";
		}

		$query = $db->prepare($sql);

		if (is_numeric($param))
			$query->bindValue(':param', $param, PDO::PARAM_INT);
		else
			$query->bindValue(':param', "%$param%", PDO::PARAM_STR);

		if (!DataValidator::isEmpty($documento))
			$query->bindValue(':documento', $documento, PDO::PARAM_STR);

		if (!DataValidator::isEmpty($email))
			$query->bindValue(':email', $email, PDO::PARAM_STR);

		$query->execute();

		$linha = $query->fetchObject();

		if (!$linha)
			return null;

		$entidade = new Entidade();
		$entidade->setStatus($linha->status_entidade);

		//Entidade superior
		if (!DataValidator::isEmpty($linha->entidade_superior_ID)) {

			$pessoa_superior = new Pessoa();
			$pessoa_superior->setId($linha->entidade_superior_ID);
			$pessoa_superior->setNome($linha->entidade_superior);

			$entidade_superior = new Entidade();
			$entidade_superior->setPessoa($pessoa_superior);
			$entidade->setEntidadeSuperior($entidade_superior);
		}

		if ($linha->tipo_pessoa == 'F')
			$pessoa = new PessoaFisica();
		else
			$pessoa = new PessoaJuridica();

		$pessoa->setId($linha->ID);
		$pessoa->setNome($linha->nome); //PJ = razão social, PF = nome
		$pessoa->setEmail($linha->email);
		$pessoa->setNumDoc($linha->num_doc_ident);
		$pessoa->setDataCadastro($linha->data_hora);
		$pessoa->setTipoPessoa($linha->tipo_pessoa);
		$pessoa->setEnderecos(EnderecoModel::getByEntidade($pessoa->getId()));

		$nacionalidade = new Nacionalidade();
		$nacionalidade->setId($linha->nacionalidade_ID);
		$pessoa->setNacionalidade($nacionalidade);

		if ($pessoa instanceof PessoaFisica) {
			$pessoa->setPassaporte($linha->passaporte);
			$pessoa->setVctoPassaporte($linha->vcto_passaporte);
			echo 'cto: ' . $pessoa->getVctoPassaporte();
			$pessoa->setPaisEmissorPassaporte($linha->pais_emissor_passaporte_ID);
			$pessoa->setDataNascimento($linha->data_nascimento);
			$pessoa->setIdade($linha->idade);
			$pessoa->setPisPasep($linha->pis_pasep);
			$pessoa->setRg($linha->rg);
			$pessoa->setOrgaoExpedidor($linha->orgao_expedidor);
			$pessoa->setEstadoExpedidor($linha->estado_expeditor_ID);

			$cidade_nascimento = new Cidade();
			$cidade_nascimento->setId($linha->cidade_nascimento_ID);
			$cidade_nascimento->setNome($linha->nome_cidade);

			//Se brasileira
			if (!DataValidator::isEmpty($linha->estado_ID)) {
				$estado_nascimento = new Estado();
				$estado_nascimento->setId($linha->estado_ID);
				$estado_nascimento->setSigla($linha->sigla_estado);
				$cidade_nascimento->setEstado($estado_nascimento);

				$pais_nascimento = new Pais();
				$pais_nascimento->setId($linha->pais_ID);
				$estado_nascimento->setPais($pais_nascimento);
			} else {
				$pais_nascimento = new Pais();
				$pais_nascimento->setId($linha->pais_ID);
				$cidade_nascimento->setPais($pais_nascimento);
			}

			$pessoa->setCidadeNascimento($cidade_nascimento);
		} elseif ($pessoa instanceof PessoaJuridica) {
			$pessoa->setNomeFantasia($linha->nome_fantasia);
			$pessoa->setIsNacional($linha->is_nacional);
			$pessoa->setInscricaoMunicipal($linha->inscricao_municipal);
			$pessoa->setInscricaoEstadual($linha->inscricao_estadual);
			$pessoa->setLogomarca($linha->logomarca);
		}

		$entidade->setPessoa($pessoa);
		$entidade->setRepresentantes(RepresentanteModel::getByEntidade($pessoa, $db));
		$entidade->setModalidades(ModalidadeModel::getByEntidade($pessoa->getId(), $db));

		return $entidade;
	}

	public static function excluiLogomarca($pessoa_id)
	{
		$msg = null;

		try {

			if (DataValidator::isEmpty($pessoa_id))
				throw new Exception("Exclui Logomarca: A Pessoa deve ser identificada.");

			$sql = " SELECT logomarca FROM pessoa_juridica WHERE pessoa_ID=:pessoa_id ";

			$entidadeModel = new EntidadeModel();
			$query = $entidadeModel->getDB()->prepare($sql);
			$query->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
			$query->execute();

			$linha = $query->fetchObject();

			if (file_exists('../imagens/logos/' . $linha->logomarca))
				unlink('../imagens/logos/' . $linha->logomarca);

			$sql_ = " UPDATE pessoa_juridica SET logomarca='' WHERE pessoa_ID=:pessoa_id ";
			$query_ = $entidadeModel->getDB()->prepare($sql_);
			$query_->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
			$query_->execute();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		}

		return $msg;
	}


	public static function insert($entidade)
	{

		$msg = null;

		try {

			$entidadeModel = new EntidadeModel();
			$entidadeModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($entidade))
				throw new UserException('Entidade: A Entidade deve ser fornecida.');

			if (DataValidator::isEmpty($entidade->getPessoa()))
				throw new UserException('Entidade: A Pessoa deve ser fornecida.');

			if (DataValidator::isEmpty($entidade->getStatus()))
				throw new UserException('Entidade: O campo Status é obrigatório.');

			$pessoa_id = PessoaModel::insert($entidade->getPessoa(), $entidadeModel->getDB());

			$sql = " INSERT INTO entidade 
								(pessoa_ID, 
								 entidade_superior_ID,
								 status_entidade
								 ) 
							VALUES 
								( :pessoa_id,
								 :entidade_superior_id,
								 :status_entidade
								 ) ";

			$query = $entidadeModel->getDB()->prepare($sql);
			$query->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
			$query->bindValue(':entidade_superior_id', 1, PDO::PARAM_INT);
			$query->bindValue(':status_entidade', $entidade->getStatus(), PDO::PARAM_STR);

			$query->execute();

			$entidadeModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$entidadeModel->getDB()->rollback();
		}

		return $msg;
	}

	public static function update($entidade)
	{

		$msg = null;

		try {

			$entidadeModel = new EntidadeModel();
			$entidadeModel->getDB()->beginTransaction();

			if (DataValidator::isEmpty($entidade))
				throw new UserException('Entidade: A Entidade deve ser fornecida.');

			if (DataValidator::isEmpty($entidade->getPessoa()))
				throw new UserException('Entidade: A Pessoa deve ser fornecida.');

			if (DataValidator::isEmpty($entidade->getPessoa()->getId()))
				throw new UserException('Entidade: A Pessoa deve ser identificada.');

			if (DataValidator::isEmpty($entidade->getStatus()))
				throw new UserException('Entidade: O campo Status é obrigatório.');

			$sql = " UPDATE entidade SET 								
								entidade_superior_ID=:entidade_superior_id,
								status_entidade=:status_entidade
								WHERE pessoa_ID=:pessoa_id
								";

			$query = $entidadeModel->getDB()->prepare($sql);
			$query->bindValue(':entidade_superior_id', 1, PDO::PARAM_INT);
			$query->bindValue(':status_entidade', $entidade->getStatus(), PDO::PARAM_STR);
			$query->bindValue(':pessoa_id', $entidade->getPessoa()->getId(), PDO::PARAM_INT);
			$query->execute();

			PessoaModel::update($entidade->getPessoa(), $entidadeModel->getDB());

			$entidadeModel->getDB()->commit();
		} catch (UserException $e) {
			$msg = $e->getMessage();
		} catch (UserException $e) {
			$msg = $e->getMessage();
			$entidadeModel->getDB()->rollback();
		}

		return $msg;
	}
}
