<?php
$absolute_path = $_SERVER["DOCUMENT_ROOT"];
require_once($absolute_path . "/sts/lib/PersistModelAbstract.php");
require_once($absolute_path . "/sts/lib/UserException.php");
require_once($absolute_path . "/sts/lib/DataFilter.php");
require_once($absolute_path . "/sts/classes/PessoaFisica.class.php");
require_once($absolute_path . "/sts/models/CidadeModel.php");

class PessoaFisicaModel extends PersistModelAbstract
{

	public static function insert($pessoa, $db = null)
	{

		if (is_null($db)) {
			$pessoaFisicaModel = new PessoaFisicaModel();
			$db = $pessoaFisicaModel->getDB();
		}

		if (DataValidator::isEmpty($pessoa))
			throw new UserException('Pessoa Fisica: A Pessoa deve ser fornecida.');

		if (DataValidator::isEmpty($pessoa->getId()))
			throw new UserException('Pessoa Fisica: A Pessoa deve ser identificada.');

		//se brasileira
		if ($pessoa->getNacionalidade()->getId() == 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getEstado()->getPais()->getId()))
			throw new UserException('Insert Entidade: O campo País de Nascimento é obrigatório.');

		//se estrangeiro
		if ($pessoa->getNacionalidade()->getId() != 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getPais()->getId()))
			throw new UserException('Insert Entidade: O campo Paísde Nascimento é obrigatório.');

		//se brasileira
		if ($pessoa->getNacionalidade()->getId() == 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getEstado()->getSigla()))
			throw new UserException('Insert Entidade: O campo Estado de Nascimento é obrigatório.');

		if (DataValidator::isEmpty($pessoa->getCidadeNascimento()->getNome()))
			throw new UserException('Pessoa Fisica: O campo Cidade de Nascimento é obrigatório.');

		//se não houver a Cidade, grava e resgata seu id		
		if (DataValidator::isEmpty($pessoa->getCidadeNascimento()->getId()))
			$cidade_id = CidadeModel::insert($pessoa->getCidadeNascimento(), $db);
		else
			$cidade_id = $pessoa->getCidadeNascimento()->getId();

		$sql = " INSERT INTO pessoa_fisica 
					(pessoa_ID, 
					 data_nascimento,
					 idade,
					 RG,
					 orgao_expedidor,
					 estado_expeditor_ID,
					 passaporte,
					 vcto_passaporte,
					 pais_emissor_passaporte_ID,
					 pis_pasep,
					 cidade_nascimento_ID
					) 
					VALUES 
					( :pessoa_id,
					 :data_nascimento,
					 :idade,
					 :rg,
					 :orgao_expedidor,
					 :estado_expeditor_id,
					 :passaporte,
					 :vcto_passaporte,
					 :pais_emissor_passaporte_id,
					 :pis_pasep,
					 :cidade_nascimento_id
					 ) ";

		$query = $db->prepare($sql);
		$query->bindValue(':pessoa_id', $pessoa->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_nascimento', $pessoa->getDataNascimento(), PDO::PARAM_STR);
		$query->bindValue(':idade', $pessoa->getIdade(), PDO::PARAM_STR);
		$query->bindValue(':rg', $pessoa->getRg(), PDO::PARAM_STR);
		$query->bindValue(':orgao_expedidor', $pessoa->getOrgaoExpedidor(), PDO::PARAM_STR);
		$query->bindValue(':estado_expeditor_id', $pessoa->getEstadoExpedidor(), PDO::PARAM_INT);
		$query->bindValue(':passaporte', $pessoa->getPassaporte(), PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pessoa->getVctoPassaporte()))
			$query->bindValue(':vcto_passaporte', date('Y-m-d', strtotime($pessoa->getVctoPassaporte())), PDO::PARAM_STR);
		else
			$query->bindValue(':vcto_passaporte', null, PDO::PARAM_NULL);

		$query->bindValue(':pais_emissor_passaporte_id', $pessoa->getPaisEmissorPassaporte(), PDO::PARAM_INT);
		$query->bindValue(':pis_pasep', $pessoa->getPisPasep(), PDO::PARAM_STR);
		$query->bindValue(':cidade_nascimento_id', $cidade_id, PDO::PARAM_INT);
		$query->execute();
	}

	public static function update($pessoa, $db = null)
	{

		if (is_null($db)) {
			$pessoaFisicaModel = new PessoaFisicaModel();
			$db = $pessoaFisicaModel->getDB();
		}

		//echo $pessoa->getVctoPassaporte();

		if (DataValidator::isEmpty($pessoa))
			throw new UserException('Pessoa Fisica: A Pessoa deve ser fornecida.');

		if (DataValidator::isEmpty($pessoa->getId()))
			throw new UserException('Pessoa Fisica: A Pessoa deve ser identificada.');

		//se brasileira
		if ($pessoa->getNacionalidade()->getId() == 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getEstado()->getPais()->getId()))
			throw new UserException('Insert Entidade: O campo Paísde Nascimento é obrigatório.');

		//se estrangeiro
		if ($pessoa->getNacionalidade()->getId() != 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getPais()->getId()))
			throw new UserException('Insert Entidade: O campo Paísde Nascimento é obrigatório.');

		//se brasileira
		if ($pessoa->getNacionalidade()->getId() == 1  && DataValidator::isEmpty($pessoa->getCidadeNascimento()->getEstado()->getSigla()))
			throw new UserException('Insert Entidade: O campo Estado de Nascimento é obrigatório.');

		if (DataValidator::isEmpty($pessoa->getCidadeNascimento()->getNome()))
			throw new UserException('Pessoa Fisica: O campo Cidade de Nascimento é obrigatório.');

		//se não houver a Cidade, grava e resgata seu id		
		if (DataValidator::isEmpty($pessoa->getCidadeNascimento()->getId()))
			$cidade_id = CidadeModel::insert($pessoa->getCidadeNascimento(), $db);
		else
			$cidade_id = $pessoa->getCidadeNascimento()->getId();

		$sql = " UPDATE pessoa_fisica SET 
					 data_nascimento=:data_nascimento,
					 idade=:idade,
					 RG=:rg,
					 orgao_expedidor=:orgao_expedidor,
					 estado_expeditor_ID=:estado_expeditor_id,
					 passaporte=:passaporte,
					 vcto_passaporte=:vcto_passaporte,
					 pais_emissor_passaporte_ID=:pais_emissor_passaporte_id,
					 pis_pasep=:pis_pasep,
					 cidade_nascimento_ID=:cidade_nascimento_id
					 WHERE pessoa_ID=:pessoa_id;
					 ";

		/*echo " UPDATE pessoa_fisica SET 
					 data_nascimento='" . $pessoa->getDataNascimento() . "',
					 idade=" . $pessoa->getIdade() . ",
					 RG='" . $pessoa->getRg() . "',
					 orgao_expedidor='" . $pessoa->getOrgaoExpedidor() . "',
					 estado_expeditor_ID=" . $pessoa->getEstadoExpedidor() . ",
					 passaporte='" . $pessoa->getPassaporte() . "',
					 vcto_passaporte='" . $pessoa->getVctoPassaporte() . "',
					 pais_emissor_passaporte_ID=" . $pessoa->getPaisEmissorPassaporte() . ",
					 pis_pasep='" . $pessoa->getPisPasep() . "',
					 cidade_nascimento_ID=" . $cidade_id . "
					 WHERE pessoa_ID=
					 " . $pessoa->getId();*/

		$query = $db->prepare($sql);
		$query->bindValue(':pessoa_id', $pessoa->getId(), PDO::PARAM_INT);
		$query->bindValue(':data_nascimento', $pessoa->getDataNascimento(), PDO::PARAM_STR);
		$query->bindValue(':idade', $pessoa->getIdade(), PDO::PARAM_STR);
		$query->bindValue(':rg', $pessoa->getRg(), PDO::PARAM_STR);
		$query->bindValue(':orgao_expedidor', $pessoa->getOrgaoExpedidor(), PDO::PARAM_STR);
		$query->bindValue(':estado_expeditor_id', $pessoa->getEstadoExpedidor(), PDO::PARAM_INT);
		$query->bindValue(':passaporte', $pessoa->getPassaporte(), PDO::PARAM_STR);

		if (!DataValidator::isEmpty($pessoa->getVctoPassaporte()))
			$query->bindValue(':vcto_passaporte', date('Y-m-d', $pessoa->getVctoPassaporte()), PDO::PARAM_STR);
		else
			$query->bindValue(':vcto_passaporte', null, PDO::PARAM_NULL);

		$query->bindValue(':pais_emissor_passaporte_id', $pessoa->getPaisEmissorPassaporte(), PDO::PARAM_INT);
		$query->bindValue(':pis_pasep', $pessoa->getPisPasep(), PDO::PARAM_STR);
		$query->bindValue(':cidade_nascimento_id', $cidade_id, PDO::PARAM_INT);
		$query->bindValue(':pessoa_id', $pessoa->getId(), PDO::PARAM_INT);
		$query->execute();
	}
}
