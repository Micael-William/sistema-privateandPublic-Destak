<?php

require_once("models/PropostaModel.php");

if (@$_REQUEST['env'] != 'se') {
	exit;
}


$mensagem = '
			
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="UTF-8">
	<title>Proposta para o Advogado</title>
</head>
<body>

<table width="700" align="center" cellpadding="0" cellspacing="0">	
	<tr>
		<td align="left" width="30%" valign="middle"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" alt=""></td>
		<td align="right" width="50%" valign="middle">
		<p style="font: 14px arial, sans-serif; color: #000; line-height: 20px;">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME.</strong>
			<br />
			CNPJ: 08.324.897/0001-10
			<br />
			' . parent::ENDERECO_PADRAO . '
			<br />
			Telefone/WhatsApp.:' . parent::TELEFONE_PADRAO . '&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/Whatapp" height="20" width="20">
			<br />
			e-mail: destak@destakpublicidade.com.br
		</p>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<p>
		<font face="Arial, sans-serif" size="3">
		São Paulo,
		<br />
		<br />
		Ilmo.(a) Sr.(a) Dr.(a): <strong>FULANO</strong> 	
		</font>
		</p>
		</td>
	</tr>


</table>
<!-- intro -->

<table width="700" align="center" cellpadding="0" cellspacing="0">

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Requerente: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>FULANO</strong></font></td>
	</tr>

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerido: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>CICLANO</strong></font></td>
	</tr>

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Vara: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>VARA DE SAO PAULO</strong></font></td>
	</tr>

	<tr height="25">
		<td><font face="arial, sans-serif" size="3">Processo: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>100.300.200.100-00</strong> </font></td>
	</tr>

	<tr height="25"><td colspan="2">&nbsp;  </td></tr>

	<tr height="25" style="font: 15px arial, sans-serif; text-align: left;">

	<td colspan="2">
		<p>
		Encaminhamos informações referentes ao processo citado:
		<br />
		<br />
		</p>		
	</td>		
	</tr>


</table>
<!-- dados -->

<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr>
		<td>
			<p><strong>MENSAGEM</strong>
			</p>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

</table>

</body>
</html>';


require_once("lib/Mail.php");

$mail = Mail::Init();

try {
	//Server settings
	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
	// $mail->isSMTP();
	// $mail->IsHTML(true);
	// $mail->CharSet = 'UTF-8';                                    //Send using SMTP
	// $mail->Host       = 'email-ssl.com.br';                     //Set the SMTP server to send through
	// $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
	// $mail->Username   = 'acompanhamento@sistemadestakpublicidade.com.br';                     //SMTP username
	// $mail->Password   = 'Publi1234$';                               //SMTP password
	// $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
	// $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

	// Define os destinatário(s)
	$email = 'bruno.camargo@thricein.com.br';

	if (isset($_GET['eeemail'])) {
		$email = $_GET['eeemail'];
	}

	$mail->AddAddress($email, $email);

	$mail->SetFrom('acompanhamento@sistemadestakpublicidade.com.br', 'Destak Publicidade2');

	// Define os dados técnicos da Mensagem
	$proposta = PropostaModel::getById(454569, null, 'envio_proposta');

	//Content
	$mail->isHTML(true);                                  //Set email format to HTML
	$mail->Subject = ("Proposta de Publicação de Edital - Processo " . (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getNumero()) ? $proposta->getProcesso()->getEntrada()->getNumero() : '') . ' - Cobrimos qualquer proposta comprovada');
	$mail->Body    = $mensagem;
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

	$mail->send();
	echo 'Message has been sent';
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo} {$e}";
}
