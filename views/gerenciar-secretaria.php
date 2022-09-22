<?php
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$secretaria = isset($params['secretaria']) ? $params['secretaria'] : new Secretaria();	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Secretarias/Fóruns - Destak Publicidade</title>
</head>
<body class="body-internas">

<div id="geral">

	<?php require_once("inc/header.inc.php"); ?>
	<!-- faixa admin -->
	
	<div id="conteudo" class="clearfix">
	
	<?php require_once("inc/sidebar.inc.php"); ?>
	<!-- sidebar -->

	<div id="direita">
			
			<div class="controls clearfix">
			
				<h1 class="std-title title title-w-btn">
				<span class="txt-14 txt-normal">Secretaria/Fórum</span> <span class="seta"> &gt; </span>
				<?php echo !DataValidator::isEmpty($secretaria->getNome()) ? $secretaria->getNome(): 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
				</div><!-- buttons -->

			</div>

			<div class="principal">

				<!-- start: warnings -->
				<div class="warning-box">
					<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
                        <span class="warning erro"><?php echo $msg; ?></span>
                    <?php } ?>
                    
                    <?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
                        <span class="warning sucesso"><?php echo $sucesso; ?></span>
                    <?php } ?>
				</div>
				<!-- end: warnings -->
                    <form method="post" id="form-pesquisa">
                        <input type="hidden" name="controle" value="Secretaria">
                        <input type="hidden" name="acao" value="busca">
                        <input type="hidden" name="origem" value="secretaria">
                    </form>
                    <!--form pesquisa-->
				<form action="" class="std-form" method="post">
                	<input type="hidden" name="controle" value="Secretaria">
                    <input type="hidden" name="acao" value="salva">
                    <input type="hidden" name="secretaria_id" value="<?php echo !DataValidator::isEmpty($secretaria->getId()) ? $secretaria->getId() : 0; ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

						<div class="campo-box">
							<label>Status</label>
							<select name="status" id="" class="">
								<option value="A" <?php echo $secretaria->getStatus() == 'A' ? 'selected' : ''; ?>>Ativo</option>
								<option value="I" <?php echo $secretaria->getStatus() == 'I' ? 'selected' : ''; ?>>Inativo</option>
							</select>
						</div>
						<!-- campo -->

                                                <div class="campo-box">
                                                        <label>Estado</label>
                                                        <select name="estado" id="" class="">
                                                        <?php
                                                        $estados = EstadosEnum::getChavesUFs('Selecione');	
                                                        foreach( $estados as $key=>$value ) {
?>			
                                                                <option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($secretaria->getEstado()) &&  $key == $secretaria->getEstado() ? 'selected' : ''; ?>><?php echo $value; ?></option>
<?php
                                                        }
?>	
                                                        </select>										
                                                </div>
                                                <!-- campo -->
                                                
						<div class="campo-box clear">
							<label>Secretaria/Fórum</label>
							<input type="text" name="secretaria_nome" value="<?php echo !DataValidator::isEmpty($secretaria->getNome()) ? $secretaria->getNome(): ''; ?>" class="std-input">
						</div>
						<!-- campo -->

						<?php 				
						if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[7]) && $responsabilidades[7]['acao'] == 'E' ){
						?>
						<div class="controles clearfix">
							<input type="submit" value="Salvar" class="std-btn send-btn fr">	
						</div>
						<!-- controles -->
                        <?php } ?>
												
				</form>
				<!-- form -->

			</div>

	</div><!-- direita -->

	</div>

</div>



<!-- start: lightbox msg -->
<div class="lightbox" id="box-msg">

	<div class="header"></div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">	
	
	</div><!-- content -->

</div>
<!-- end: lightbox msg -->

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>
	
<script>

	$('.btn-pesquisa').bind('click', function() { 
		$('#form-pesquisa').submit();
	});
	
	App.secretarias();
	
</script>
	
</body>
</html>