<?php
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$usuario = isset($params['usuario']) ? $params['usuario'] : null;		
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Ambiente Administrativo</title>
</head>
<body>

	
<div id="geral">


	<div id="conteudo-home">
		
		<form action="" class="std-form login-form" method="post">
		<input type="hidden" name="controle" value="Usuario">
		<input type="hidden" name="acao" value="login">

		<img src="img/logo.png" alt="Sistema Destak">

		<fieldset>

			<!-- <span class="title">Área Restrita</span> -->

			<!-- start: warnings -->
			<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
			<span class="warning erro"><?php echo $msg; ?></span>
			<?php } ?>

			<?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
			<span class="warning sucesso"><?php echo $sucesso; ?></span>
			<?php } ?>
			<!-- end: warnings -->

			<div class="campo-box">
			<label>CPF</label>
			<input type="text" name="cpf" placeholder="CPF" value="<?php echo isset($_POST['cpf']) ? $_POST['cpf'] : ''; ?>" class="std-input cpf-input">
			</div>

			<div class="campo-box">
			<label>Senha</label>
			<input type="password" name="senha" placeholder="Senha" class="std-input">
			</div>

		</fieldset>	

		<a href="#" title="Esqueceu sua senha?" class="txt-senha link-lightbox" data-rel="box-senha">Esqueceu sua senha?</a>
		<input type="submit" value="Login" name="salvar" class="std-btn">

		<br>		

		</form>
		<!-- formulário de login -->

		
		<footer class="footer">
			<center>
			<div style="display: table-cell;">
				<img src="img/www.jpg" alt="Pagina Web" height="20" width="20">&nbsp;&nbsp;
			</div>
			<div style="display: table-cell; vertical-align: top;">
				<a href="http://destakpublicidade.com.br/" class="login-link" style="text-decoration: none">www.destakpublicidade.com.br</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			
			<div style="display: table-cell;">
				<img src="img/mail.jpg" alt="E-Mail" height="20" width="20">&nbsp;&nbsp;
			</div>
			<div style="display: table-cell; vertical-align: top;">
				<a href="mailto:destak@destakpublicidade.com.br" class="login-link" style="text-decoration: none">destak@destakpublicidade.com.br</a>
			</div>
			<center>
		<br>
		<center>
			<div style="display: table-cell;">
				<img src="img/address.jpg" alt="Endereço" height="20" width="20">&nbsp;&nbsp;
			</div>
			<div style="display: table-cell; vertical-align: top;">
			<a href="https://maps.google.com/maps?ll=-23.538589,-46.587887&z=15&t=m&hl=pt-BR&gl=BR&mapclient=embed&q=R.%20Pimenta%20Bueno%2C%20232%20-%20Ch%C3%A1cara%20Tatuap%C3%A9%20S%C3%A3o%20Paulo%20-%20SP%2003058-000" class="login-link" style="text-decoration: none">
				Rua Padre Estevão Pernet, 718 - Sala 2601 - Tatuapé - São Paulo/SP - CEP 03315-000</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
			
			<div style="display: table-cell;">
				<img src="img/whatsapp.jpg" alt="Telefone/WhatsApp" height="20" width="20">&nbsp;&nbsp;
			</div>
			<div style="display: table-cell; vertical-align: top;">
				(11) 3107-0933
			</div>
			<center>
		</footer>

	</div>	

</div>

<div id="box-senha" class="lightbox">
	
	<div class="header">Reenvio de Senha</div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">
	
	<form action="" method="post" class="std-form">

	<p class="std-text">
	Preencha o campo abaixo com seu CPF para receber uma nova senha.
	<br class="clear"><br>
	</p>

	<div class="campo-box">
		<input type="text" name="cpf" placeholder="CPF" class="std-input md-input cpf-input" id="cpf">
		<input type="button" value="Enviar" title="Enviar E-mail" name="enviar_email" class="std-btn sm-btn send-btn" id="send-pass-btn">
	</div> 
	<!-- campo -->

	<span class="feedback-senha">
		
	</span><!-- feedback -->
	
	</form>

	</div>

</div>
	
<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

<script>
	
	$(function(){
		
		// envia senha
		$('#send-pass-btn').on('click', function() {
			
			var cpf = $('#cpf').val();
			var dataObj = { cpf: cpf }			

			$.ajax({
				
				type: 'POST',
				url : 'ajax-envio-senha.php',
				data : dataObj,
				success: function(response) {
					
					console.log(response);

					if( response == 'sucesso' ) {
						$('.feedback-senha').removeClass('feedback-senha--red').html('E-mail enviado com sucesso.').fadeIn('fast');
					}

					else if( cpf == '' ) {
						$('.feedback-senha').addClass('feedback-senha--red').html('Digite seu CPF.').fadeIn('fast');	
					}

					else {
						$('.feedback-senha').addClass('feedback-senha--red').html('CPF inexistente no sistema.').fadeIn('fast');	
					}

				}

			});		

		});

	});

</script>


</body>
</html>