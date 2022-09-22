<?php
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$perfil = isset($params['perfil']) ? $params['perfil'] : new Perfil();
	$responsabilidades = $perfil->getResponsabilidades();
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Níveis de Acesso - Destak Publicidade</title>
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
				<span class="txt-14 txt-normal">Níveis de Acesso</span> 
				<span class="seta"> &gt; </span> 
				<?php echo !DataValidator::isEmpty($perfil->getNome()) ? $perfil->getNome(): 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="?controle=Perfil&acao=index" title="Voltar" class="std-btn dark-gray-btn">Voltar</a>
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

				<form action="" class="std-form clear" method="post">
					
					<input type="hidden" name="controle" value="Perfil">
					<input type="hidden" name="acao" value="salva">
					<input type="hidden" name="perfil_id" value="<?php echo !DataValidator::isEmpty($perfil->getId()) ? $perfil->getId() : 0 ?>">

					<div class="campo-box clear">
						<label class="w-60">Nome</label>
						<input type="text" name="nome" value="<?php echo $perfil->getNome(); ?>" class="std-input">
					</div>
					<!-- campo -->

					<?php
						$responsabilidades = ResponsabilidadeModel::lista();
						if( !DataValidator::isEmpty($responsabilidades) ){

						$perfil_responsas = ResponsabilidadeModel::get_perfil_responsabilidades( !DataValidator::isEmpty($perfil->getId()) ? $perfil->getId() : 0 );

						foreach( $responsabilidades as $chave => $responsabilidade ){
							$chave++;
					?>
                      
					<div class="campo-box clearfix">
						
          			<input type="hidden" name="<?php echo 'resp_id'.$chave; ?>" value="<?php echo $responsabilidade->getId(); ?>">
                          
						<label class="label-block label-bold"><?php echo $responsabilidade->getNome(); ?></label>

						<div class="nivel-check-box">	                            							
							<input type="checkbox" name="<?php echo 'leitura'.$chave; ?>" class="std-check nivel-check"
							<?php echo !DataValidator::isEmpty($perfil_responsas) && isset($perfil_responsas['posicao'.$chave]) && $perfil_responsas['posicao'.$chave]['resp_id'] == $responsabilidade->getId() && $perfil_responsas['posicao'.$chave]['acao'] == 'L' ? 'checked' : ''; ?> 
                              >
							<?php 
							echo $chave == 1 ? 'Cadastro' : 'Apenas Leitura';
							?>
                              
              				<?php if( $chave != 1 && $chave != 8 && $chave != 11){ ?>
							&nbsp; &nbsp; &nbsp;
							<input type="checkbox" name="<?php echo 'edicao'.$chave; ?>" class="std-check nivel-check"
							<?php echo !DataValidator::isEmpty($perfil_responsas) && isset($perfil_responsas['posicao'.$chave]) && $perfil_responsas['posicao'.$chave]['resp_id'] == $responsabilidade->getId() && $perfil_responsas['posicao'.$chave]['acao'] == 'E' ? 'checked' : ''; ?> 
							>
							Leitura e Edição                                
							<?php } ?>

							<?php if( $chave == 9){ ?>
							&nbsp; &nbsp; &nbsp;
							<input type="checkbox" name="<?php echo 'exclusao'.$chave; ?>" class="std-check nivel-check"
							<?php echo !DataValidator::isEmpty($perfil_responsas) && isset($perfil_responsas['posicao'.$chave]) && $perfil_responsas['posicao'.$chave]['resp_id'] == $responsabilidade->getId() && $perfil_responsas['posicao'.$chave]['acao'] == 'D' ? 'checked' : ''; ?> 
							>
							Exclusão
							<?php } ?>
						</div>
			
					</div>
					<!-- campo -->
                      
          <?php }} ?>
								
					<div class="controles clearfix">
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