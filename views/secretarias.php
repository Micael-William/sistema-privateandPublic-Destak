<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$secretarias = $params['secretarias'];
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new PesquisaSecretaria();
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;	

?>

<!doctype html>
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
            			
				<h1 class="std-title title <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[7]) && $responsabilidades[7]['acao'] == 'E' ? 'title-w-btn' : 'title-full'; ?>">Secretaria/Fórum</h1>
				
                <?php 				
				if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[7]) && $responsabilidades[7]['acao'] == 'E' ){
				?>
				<div class="buttons fr">
					<a href="?controle=Secretaria&acao=detalhe" title="Incluir" class="std-btn green-btn">Incluir</a>
				</div><!-- buttons -->
                <?php } ?>

			</div>
            
            <div class="box-filtros">
					 
				<div class="title titulo-filtro clearfix">
					<span class="fl">Filtros</span>
					<i class="seta fr"></i>
				</div>
				<!-- titulo -->

				<div class="campos clear">
                
                <form action="" class="" id="form-detalhe" method="post">
					<input type="hidden" name="controle" value="Secretaria">
					<input type="hidden" name="acao" value="detalhe">
					<input type="hidden" name="secretaria_id" id="sec-id" value="">
				</form>
                					
					<form action="" class="form-filtro clearfix form-busca-lista" method="post">
						<input type="hidden" name="controle" value="Secretaria">
						<input type="hidden" name="acao" value="busca">
                        <input type="hidden" name="numero_pagina" id="numero-pagina" value="">
						
						<div class="campo-box">
							<label>Status</label>
							<select name="busca_status" id="">
								<option value="0">Selecione</option>
								<option value="A" <?php echo $pesquisa->getStatus() == 'A' ? 'selected' : ''; ?>>Ativo</option>
								<option value="I" <?php echo $pesquisa->getStatus() == 'I' ? 'selected' : ''; ?>>Inativo</option>
							</select>
						</div>
						<!-- campo -->	
                        
                        <div class="campo-box">
							<label for="">Estado</label>
							<select name="busca_estado" id="">
							<?php
							$estados = EstadosEnum::getChavesUFs('Selecione');
							foreach( $estados as $key=>$value ){
							?>			
							<option value="<?php echo $key; ?>" <?php echo $pesquisa->getEstado() == $key ? 'selected' : ''; ?> ><?php echo $value; ?></option>
							<?php
							}
							?>	                            
							</select>
                      </div>
                      <!-- campo -->					

						<div class="campo-box">
							<label>Termo</label>
							<input type="text" name="busca_termo" value="<?php echo $pesquisa->getTermo() !== null ? $pesquisa->getTermo() : ''; ?>" class="std-input">
						</div>
						<!-- campo -->
					
						<div class="controles clearfix fr">
							<a href="#" title="Limpar" class="std-btn clean-btn">Limpar</a>
							<input type="submit" value="Buscar" class="std-btn send-btn ">	
						</div>
						<!-- controles -->

					</form>
					<!-- form -->

				</div>
				<!-- campos-->

			</div>
			<!-- filtros -->

			<div class="principal">
            
				<table width="100%" class="std-table">

					<tr>					
						<th width="">Secretaria/Fórum</th>	
						<th width="15%">Estado</th>
						<th width="15%">Status</th>
					</tr>      
                    
                    <?php 
					if( !DataValidator::isEmpty( $secretarias ) ){												
						foreach($secretarias as $secretaria){
							$id = !DataValidator::isEmpty($secretaria->getId()) ? $secretaria->getId() : '';
							$nome = !DataValidator::isEmpty($secretaria->getNome()) ? $secretaria->getNome() : '';
							$estado = !DataValidator::isEmpty($secretaria->getEstado()) ? $secretaria->getEstado() : '';
							$status = !DataValidator::isEmpty($secretaria->getStatus()) ? $secretaria->getStatusDesc() : '';
					?>           

					<tr class="detalhe-secretaria" data-id="<?php echo $id; ?>">
						<td><a href="#" title="<?php echo $nome; ?>"><?php echo $nome; ?></a></td>			
						<td><a href="#" title="<?php echo $estado; ?>"><?php echo $estado; ?></a></td>		
						<td><a href="#" title=""><?php echo $status; ?></a></td>				
					</tr>
                    
                    <?php }} ?>

				</table><!-- list -->
                
<ul class="paginacao-lista">
	<?php echo !DataValidator::isEmpty($paginacao) ? $paginacao->getAll() : ''; ?>
</ul> <!--//paginacao-->

			</div>

	</div><!-- direita -->

	</div>

</div>

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

<script>
	$('.detalhe-secretaria').bind('click', function(){
		var secretaria_id = $(this).attr('data-id');						
		$('#sec-id').val(secretaria_id);	
		$('#form-detalhe').submit();
	});
</script>
	
</body>
</html>