<?php
	require_once("valida-sessao.php");
	error_reporting(E_ALL ^ E_DEPRECATED);
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$status = isset($params['status']) ? $params['status'] : new AcompanhamentoStatus();	
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Status de Acompanhamento - Destak Publicidade</title>
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
				<span class="txt-14 txt-normal">Status</span>
				<span class="seta"> &gt; </span>
				<?php echo !DataValidator::isEmpty($status->getStatus()) ? $status->getStatus() : 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="?controle=Status&acao=index" title="Voltar" class="std-btn dark-gray-btn">Voltar</a>
				</div><!-- buttons -->

			</div>

				<div class="principal">

				<!-- start: warnings -->
				<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
					<span class="warning erro"><?php echo $msg; ?></span>
				<?php } ?>

				<?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
					<span class="warning sucesso"><?php echo $sucesso; ?></span>
				<?php } ?>
				<!-- end: warnings -->
				
				<form action="" id="form-exclusao" method="post">
                    <input type="hidden" name="controle" value="Status">
                    <input type="hidden" name="acao" value="exclui">
                    <input type="hidden" name="status_id" value="<?php echo $status->getId(); ?>">
                </form>
                <!--for exclusao-->

				<form action="" method="post" class="std-form clear">

						<input type="hidden" name="controle" value="Status">
						<input type="hidden" name="acao" value="salva">
						<input type="hidden" name="status_id" value="<?php echo $status->getId(); ?>">

                                                <div class="campo-box">
							<label>Status*</label>
							<select name="parent_id" id="">
								<option value="0"></option>
                                                                <?php 
								$status_pai = AcompanhamentoStatusModel::listaPai(); 
								if( !DataValidator::isEmpty($status_pai) ){
									foreach( $status_pai as $status_reg ){
										if($status_reg->getId() != $status->getId()) {
								?>
									<option value="<?php echo $status_reg->getId(); ?>" <?php echo $status_reg->getId() == $status->getParentId() ? 'selected' : ''; ?>><?php echo $status_reg->getStatus(); ?></option>
								<?php 
										}
									}
								} 
								?>
							</select>
						</div>
						<!-- campo -->
                                                
						<div class="campo-box">
							<label>Substatus*</label>
							<input type="text" name="nome_status" <?php echo $status->getStatus(); ?> value="<?php echo $status->getStatus(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box clear">
							<label>Descrição</label>
							<textarea name="descricao" rows="8" class="std-input"><?php echo $status->getDescricao(); ?></textarea>
						</div>
						<!-- campo -->

						<br>	
                                                <div class=""><em>* Campos de preenchimento obrigatório.</em></div>

						<div class="controles clearfix">
							<?php 
							if( !DataValidator::isEmpty($status->getId())) {							
								if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){										
							?>
									<a href="http://www.google.com" target="_blank" title="Excluir" 
									class="std-btn fl del-item" data-del-message="Tem certeza que deseja excluir este Status?">Excluir</a>
									
                            <?php }
							}
							//nivel acesso ?>
                        	<input type="submit" value="Salvar" class="std-btn send-btn fr">
                        </div>
						<!-- controles -->
												
				</form>
				<!-- form -->

			</div>

	</div><!-- direita -->

	</div>

</div>

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>
	
</body>
</html>