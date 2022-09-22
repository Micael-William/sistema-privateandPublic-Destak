<?php

require_once("lib/DataValidator.php");
require_once("models/PropostaModel.php");
require_once("models/UsuarioModel.php");

//$proposta_id = 50;
$proposta_id = $_POST['proposta_id'];
$usuario_id = $_POST['user_id'];
$reimpressao = (isset($_POST['reimpressao']) && $_POST['reimpressao'] == 'fromAndamento') ? '</br>(SEGUNDA VIA)' : '';
//$usuario_id = 1;
$mensagem = null;

if (isset($proposta_id) && !DataValidator::isEmpty($proposta_id) && 	isset($usuario_id) && !DataValidator::isEmpty($usuario_id)) {

	try {
		$proposta = PropostaModel::getById($proposta_id, null, 'emails ativos');
		$responsavel = UsuarioModel::getById($usuario_id);

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
		<td align="left" valign="top" width="30%" valign="middle"><img src="http://www.sistemadestakpublicidade.com.br/img/logo-email.jpg" alt="Destak Publicidade">
			<br />
			<p style="font: 16px arial, sans-serif; color: #000; line-height: 20px;">
			Proposta nº <strong>' . (!DataValidator::isEmpty($proposta->getId()) && !DataValidator::isEmpty($proposta->getDataEntrada()) ? $proposta->getId() . '/' . DataFilter::retornaAno(date("Y-m-d H:i:s")) : '') . ' ' . $reimpressao . '</strong>
			</p>

		</td>
		<td align="right" width="50%" valign="middle">
			<strong>AGÊNCIA DESTAK DE PUBLICIDADE LTDA ME.</strong>
			<br />
			CNPJ: 08.324.897/0001-10
			<br />
			' . PropostaModel::ENDERECO_PADRAO . '
			<br />
			Telefone/WhatsApp.:' . PropostaModel::TELEFONE_PADRAO . '&nbsp;<img src="http://www.sistemadestakpublicidade.com.br/img/whatsapp.jpg" alt="Telefone/WhatsApp" height="20" width="20">
			<br />
			<strong>Contato:</strong> ' . $responsavel->getNome() . ' - ' . $responsavel->getEmail() . '
		</td>
		
	</tr>

	<tr>
		<td colspan="2">
		<p style="margin: 0;">
		<font face="Arial, sans-serif" size="3">
		São Paulo, ' . DataFilter::dataExtenso(date("Y-m-d H:i:s")) . '
		<br />
		<br />
		Ilmo.(a) Sr.(a) Dr.(a): <strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()) ? $proposta->getProcesso()->getAdvogado()->getNome() : '') . '</strong> 
		<br />';

		$tels = $proposta->getProcesso()->getAdvogado()->getTelefones();
		if (!DataValidator::isEmpty($tels)) {

			$mensagem .= 'Tel.: <strong>';

			$qtd_tels = sizeof($tels);
			foreach ($tels as $chave_ => $tel) {
				$chave_ += 1;
				$mensagem .= $tel->getDdd() . ' ' . $tel->getNumero();
				$mensagem .= $qtd_tels > $chave_ ? ' | ' : '';
			}

			$mensagem .= '</strong>';
		}

		$mensagem .= '<br />
		E-mail: <strong>';

		$emails = $proposta->getProcesso()->getAdvogado()->getEmails();
		if (!DataValidator::isEmpty($emails)) {
			$qtd_emails = sizeof($emails);
			foreach ($emails as $chave => $email) {
				$chave += 1;
				$mensagem .= $email->getEmailEndereco();
				$mensagem .= $qtd_emails > $chave ? ' | ' : '';
			}
		}

		$mensagem .= '</strong> 
		</font>
		</p>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<p>
		<font face="Arial, sans-serif" size="3">
		Pela presente, vimos apresentar orçamento para publicação de edital.  
		<br />
		</font>
		</p>
		</td>
	</tr>

</table>
<!-- intro -->

<table width="700" align="center" cellpadding="0" cellspacing="0">

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerente: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getRequerente()) ? $proposta->getProcesso()->getRequerente() : '') . '</strong></font></td>
	</tr>

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Requerido: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getRequerido()) ? $proposta->getProcesso()->getRequerido() : '') . '</strong></font></td>
	</tr>

	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Vara: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getSecretaria()) ? $proposta->getProcesso()->getSecretaria()->getNome() : '') . '</strong></font></td>
	</tr>';

		if (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()) && !DataValidator::isEmpty($proposta->getProcesso()->getAcao())) {
			$mensagem .= '
	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Ação: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . $proposta->getProcesso()->getAcao() . '</strong> </font></td>
	</tr>';
		}

		$mensagem .= '
	<tr height="25">
		<td valign="top"><font face="arial, sans-serif" size="3">Processo: </font></td>
		<td><font face="arial, sans-serif" size="3"><strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getEntrada()->getNumero()) ? $proposta->getProcesso()->getEntrada()->getNumero() : '') . '</strong> </font></td>
	</tr>

</table>
<!-- dados -->

<br />

<table width="700" align="center" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
<tr bgcolor="01911c">
	<td height="40">
		<font face="arial" color="#FFFFFF" size="3"> <strong>&nbsp; Opções de Publicação</strong> </font>
	</td>
</tr>
</table>';

		$custos_proposta = !DataValidator::isEmpty($proposta->getCustos()) ? $proposta->getCustos() : null;

		if (!DataValidator::isEmpty($custos_proposta)) {
			if (isset($custos_proposta['valor_P']) && $custos_proposta['valor_P']->getAceite() == 'A') {

				$mensagem .= '
<table width="700" align="center" border="1" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr bgcolor="#eeeeee" height="40">
		<th width="20%" align="left">&nbsp; Quantidade</th>
		<th>&nbsp; Jornal</th>
		<th width="25%">&nbsp; Valor</th>
	</tr>

	<tr height="40">
		<td>&nbsp; ' . $custos_proposta['valor_P']->getQuantidade() . '</td>
		<td>&nbsp; ' . (!DataValidator::isEmpty($proposta->getProcesso()->getJornal()) ? $proposta->getProcesso()->getJornal()->getNome() : '') . '</td>
		<td>&nbsp; R$ ' .  number_format($custos_proposta['valor_P']->getValorFinal(), 2, ',', '.') . '*</td>
	</tr>

</table>
<!-- publicação -->';
			} //valor padrao

			if (isset($custos_proposta['valor_D']) && isset($custos_proposta['valor_P']) && $custos_proposta['valor_D']->getAceite() == 'A') {

				$mensagem .= '
<table width="700" align="center" border="1" cellspacing="0" cellpadding="1" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr bgcolor="#eeeeee" height="40">
		<th width="20%" align="left">&nbsp; Quantidade</th>
		<th>&nbsp; Jornal</th>
		<th width="25%">&nbsp; Valor</th>
	</tr>

	<tr height="40">
		<td>&nbsp;' . $custos_proposta['valor_D']->getQuantidade() . '</td>
		<td>&nbsp; Diário da Justiça Eletrônico</td>
		<td>&nbsp; R$ ' . number_format($custos_proposta['valor_D']->getValorDje(), 2, ',', '.') . '</td>
	</tr>

	<tr height="40">
		<td>&nbsp; ' . $custos_proposta['valor_P']->getQuantidade() . '</td>
		<td>&nbsp; ' . (!DataValidator::isEmpty($proposta->getProcesso()->getJornal()) ? $proposta->getProcesso()->getJornal()->getNome() : '') . '</td>
		<td>&nbsp; R$ ' . number_format($custos_proposta['valor_P']->getValorFinal(), 2, ',', '.') . '</td>
	</tr>

	<tr height="40">
		<td></td>
		<td></td>
		<td>&nbsp; Total: <strong> R$ ' . number_format($custos_proposta['valor_D']->getValorFinal(), 2, ',', '.') . ' **</strong> </td>
	</tr>

</table>
<!-- publicação -->';
			} //valor dje

		} //jornais

		$mensagem .= '

<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;">
	
	<tr>
		<td>
			<p>
			<em>*	Recolhimento da guia do DJE (Prov. 1668/2009) a cargo do advogado.';

		if (isset($custos_proposta['valor_D']) && $custos_proposta['valor_D']->getAceite() == 'A') {
			$mensagem .= '
			<br />**	Recolhimento da guia do DJE (Prov. 1668/2009) até o valor máximo de R$ ' . number_format($custos_proposta['valor_D']->getValorDje(), 2, ',', '.') . ' a cargo da Destak, já incluso no valor do orçamento.</em>';
		}

		$mensagem .= '
			<br />
			</p>
		</td>
	</tr>	
</table>

<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;">
	<tr>
	    <td width="40%" style="padding:5px;">
			<div width="100%" style="border:1px solid black;padding:10px;">
				<span style="font-size:14px;">
				    <table width="100%">
				        <tr>
				            <td align="center" width="50%">
					            ENVIO<br />ONLINE<br />
					            <div style="padding:3px;">
					                <div style="height:20px;width:20px;border:1px solid black"></div>
					            </div>     
					        </td>
					        <td align="center" width="50%">
					            ENVIO<br />IMPRESSO<br />
					            <div style="padding:3px;">
					                <div style="height:20px;width:20px;border:1px solid black"></div>
					            </div>
					        </td>
					    </tr>
					    <tr>
				            <td align="center" colspan="2" style="padding:15px 10px 0px 10px;">
				                <strong>PROTOCOLO DE PETIÇÕES</strong>
					        </td>
					    </tr>
					    <tr>
				            <td align="center">
					            DESTAK<br />
					            <div style="padding:3px;">
					                <div style="height:20px;width:20px;border:1px solid black"></div>
					            </div>  
					        </td>
					        <td align="center">
					            CLIENTE<br />
					            <div style="padding:3px;">
					                <div style="height:20px;width:20px;border:1px solid black"></div>
					            </div>  
					        </td>
					    </tr>
					</table>    
				</span>
			</div>
		</td>
		<td width="20%">
			&nbsp;
		</td>
		<td width="40%" style="padding:5px;">
			<div width="100%" style="border:1px solid black;padding:12px;">
				<span style="font-size:14px;">
					<strong>Publicação DO:</strong>&nbsp;&nbsp;&nbsp;_____&nbsp;/&nbsp;_____&nbsp;/&nbsp;______
					<br /><br />
					<strong>Envio Jornal Local:</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______&nbsp;/&nbsp;______
					<br /><br />
					__________________________________
					<br /><br />
					__________________________________
					<br /><br />
					____&nbsp;/&nbsp;____&nbsp;&nbsp;-&nbsp;&nbsp;____&nbsp;/&nbsp;____&nbsp;&nbsp;-&nbsp;&nbsp;____&nbsp;/&nbsp;____ 
				</span>
			</div>
		</td>
	</tr>	
</table>
<br />';


		if (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getEndereco()->getLogradouro())) {

			$mensagem .= '
	<table width="700" align="center" cellpadding="0" cellspacing="0" style="font: 15px arial, sans-serif; text-align: left;border-top:2px dotted black">
		<tr>
			<td style="padding:25px 0px 0px 0px;">
				<span style="font-size:16px;">
					<strong>' . (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()) ? $proposta->getProcesso()->getAdvogado()->getNome() : '') . '</strong>
					<br />
					' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getLogradouro() . ', ' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getNumero() . '
					<br />
					' . (!DataValidator::isEmpty($proposta->getProcesso()->getAdvogado()->getEndereco()->getComplemento()) ? $proposta->getProcesso()->getAdvogado()->getEndereco()->getComplemento() . "<br \>" : "") . '
					' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getBairro() . ' - ' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getCidade() . '/' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getEstado() . '
					<br />
					CEP: <strong>' . $proposta->getProcesso()->getAdvogado()->getEndereco()->getCep() . '</strong>
				</span>
			</td>
		</tr>	
	</table>';
		}

		$mensagem .= '
</body>
</html>';
	} catch (UserException $e) {
		$msg = $e->getMessage();
	}
}

echo $mensagem;
