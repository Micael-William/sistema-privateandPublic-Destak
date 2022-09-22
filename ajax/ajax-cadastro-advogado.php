<?php
require_once("lib/DataValidator.php");
require_once("models/AdvogadoModel.php");

$nome = $_REQUEST['nome_advogado'];
$oab = $_REQUEST['oab_advogado'];
$email_advogado = $_REQUEST['email_advogado'];
$retorno = null;

if (
	isset($nome) &&
	!DataValidator::isEmpty($nome) &&
	isset($oab) &&
	!DataValidator::isEmpty($oab)
) {

	try {

		$adv_cadastrado = AdvogadoModel::getByOAB($oab);

		if (DataValidator::isEmpty($adv_cadastrado)) {

			$advogado = new Advogado();
			$advogado->setNome(isset($nome) && !DataValidator::isEmpty($nome) ? $nome : null);
			$advogado->setOab(isset($oab) && !DataValidator::isEmpty($oab) ? $oab : null);

			//$email = new Email();
			//$email->setEmailEndereco( $email_advogado );
			//$advogado->setEmail( $email );

			if (isset($_POST['email_advogado'])) {
				for ($e = 0; $e < sizeof($_POST['email_advogado']); $e++) {
					$email = new Email();
					$email->setEmailEndereco(isset($_POST['email_advogado'][$e]) && !DataValidator::isEmpty($_POST['email_advogado'][$e]) ? $_POST['email_advogado'][$e] : null);
					if (isset($_POST['tick_advogado'])) {
						$email->setEnviar(isset($_POST['tick_advogado'][$e]) ? 'S' : 'N');
					}
					$advogado->setEmail($email);
				}
			}

			if (isset($_POST['ddd_tel_advogado']) && isset($_POST['telefone_advogado'])) {
				for ($i = 0; $i < sizeof($_POST['telefone_advogado']); $i++) {
					$tel = new Telefone();
					$tel->setDdd($_POST['ddd_tel_advogado'][$i]);
					$tel->setNumero($_POST['telefone_advogado'][$i]);
					$advogado->setTelefone($tel);
				}
			}

			$retorno = AdvogadoModel::insert($advogado);

			if (DataValidator::isEmpty($retorno['msg']))
				echo json_encode(array('msg' => 'sucesso', 'adv_id' => $retorno['advogado_id'], 'adv_nome' => $retorno['advogado_nome']));
		} else
			echo json_encode(array('msg' => 'ja existe'));
	} catch (UserException $e) {
		$retorno['msg'] = $e->getMessage();
	}
}
