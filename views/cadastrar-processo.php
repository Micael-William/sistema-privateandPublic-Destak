<?php	
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$processos_repetidos = isset($params['processos_repetidos']) ? $params['processos_repetidos'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Processo - Destak Publicidade</title>
</head>
<body class="body-internas">

<div class="loading-mask">
	<div class="loading-content">
		<img src="img/loading-screen.gif" alt="" class="loading-ico">
		<p>Carregando...</p>
	</div>
</div>
<!-- loading mask -->

<div id="geral">

	<?php require_once("inc/header.inc.php"); ?>
	<!-- faixa admin -->
	
	<div id="conteudo" class="clearfix">
	
	<?php require_once("inc/sidebar.inc.php"); ?>
	<!-- sidebar -->

	<div id="direita">
			
			<div class="controls clearfix">		

				<h1 class="std-title title title-full">Cadastrar Processo</h1>

			</div>

			<div class="principal">

				<!-- start: warnings -->
				 <div class="warning-box">

					<?php 
						if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ 
							echo '<span class="warning sucesso">' . $sucesso . '</span>';
						} elseif( isset($processos_repetidos) && !DataValidator::isEmpty($processos_repetidos) ){
							$incr = 0;
                    		echo '<span class="warning erro"> Processos repetidos e não recadastrados:<br> ';
							foreach( $processos_repetidos as $repetido ){
								$incr++;
								echo $repetido;
								if($incr < sizeof($processos_repetidos)) echo '<br> '; 
							}
							echo '</span>';
                    	}
						elseif( isset($msg) && !DataValidator::isEmpty($msg) ){ 	
                        	echo '<span class="warning erro">' . $msg . '</span>';
					} ?>           

				</div>  
				<!-- end: warnings -->

				<form action="" class="std-form form-cadastro-processo" method="post" enctype="multipart/form-data">
                	<input type="hidden" name="controle" value="Processo">
                    <input type="hidden" name="acao" value="salva">

						<div class="campo-box">
							<label for="">Estado</label>
							<select name="estado" id="sel-estado-processo">
							<?php
							$estados = array("0"=>"Selecione","DF"=>"DF", "MS"=>"MS", "MT"=>"MT" ,"RJ"=>"RJ", "SE"=>"SE", "SP"=>"SP" );
							foreach( $estados as $key=>$value ){
							?>			
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php
							}
							?>
							</select>
						</div>
						<!-- campo -->

						<div class="campo-box campo-arquivo-processo">
							<label for="">Arquivo</label>
							<input type="file" name="arquivo_processo" class="std-input">
						</div>
						<!-- campo -->

						<div class="panel panel-info bordered-1 panel-accordion">
						<div class="panel-title">Notificações <i class="seta seta-baixo"></i> </div>

							<div class="panel-content" style="display: block;">
							<ul class="list-group list-zebra">						
								<li class="list-group-item list-group-item-bold">Importação de arquivo com padrão de nomenclatura e ordem de linhas e colunas previamente definidas, conforme a seguir: </li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> JORNAL</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> DATA_PUBLICACAO</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> NOME_PESQUISADO</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> TRIBUNAL</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> SECRETARIA</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> NUMERO_PROCESSO</li>
								<li class="list-group-item list-group-item-bold"><i class="bullet"></i> PUBLICACAO</li>
								<li>&nbsp;</li>
								<li>Arquivos que não respeitem exatamente o padrão informado acima não serão inseridos.</li>
							</ul>
							</div> 

						</div>

						<div class="controles clearfix">
							<!-- <a href="#" title="Excluir" class="std-btn fl">Cancelar</a> -->
							<input type="submit" value="Importar" class="std-btn send-btn fr">	
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

<script>
	App.processos();
</script>


</body>
</html>