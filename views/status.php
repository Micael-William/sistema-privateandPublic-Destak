<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$acompanhamento_status = $params['acompanhamento_status'];
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
			
				<h1 class="std-title title title-w-btn">Status de Acompanhamento</h1>

				<div class="buttons fr">
					<a href="?controle=Status&acao=detalhe" title="Incluir" class="std-btn confirm-btn">Incluir</a>
				</div><!-- buttons -->

			</div>

			<div class="box-filtros">
					 
				<div class="title titulo-filtro clearfix">
					<span class="fl">Filtros</span>
					<i class="seta fr"></i>
				</div>
				<!-- titulo -->

				<div class="campos clear">
                
					<form action="" class="std-form" id="form-detalhe" method="post">
						<input type="hidden" name="controle" value="Status">
						<input type="hidden" name="acao" value="detalhe">
						<input type="hidden" name="status_id" id="status-id" value="">
					</form>
					
					<form action="" class="form-filtro clearfix" method="post">
						
						<input type="hidden" name="controle" value="Status">
						<input type="hidden" name="acao" value="busca">
						
						<div class="campo-box">
							<label>Substatus</label>
							<input type="text" name="busca_status" value="<?php echo isset($_POST['busca_status']) ? $_POST['busca_status'] : ''; ?>" class="std-input">
						</div>
						<!-- campo -->
						
						<div class="campo-box">
							<label>Status Pai Associado</label>
							<select name="busca_status_pai" id="">
								<option value="0">Selecione</option>
								<?php 
								$status_pai = AcompanhamentoStatusModel::listaPai(); 
								if( !DataValidator::isEmpty($status_pai) ){
									foreach( $status_pai as $status_reg ){
								?>
								<option value="<?php echo $status_reg->getId(); ?>" <?php echo isset($_POST['busca_status_pai']) && $_POST['busca_status_pai'] == $status_reg->getId() ? 'selected' : ''; ?>><?php echo $status_reg->getStatus(); ?></option>
								<?php }} ?>
							</select>
						</div>
						<!-- campo -->						

						<div class="controles fr clearfix">
							<a href="#" title="Limpar" class="std-btn clean-btn">Limpar</a>
							<input type="submit" value="Buscar" class="std-btn send-btn">	
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
					<th width="">Status</th>
					<th width="50%">Substatus</th>					
				</tr>    
                
                <?php 
				if( !DataValidator::isEmpty( $acompanhamento_status ) ){												
					foreach($acompanhamento_status as $status){
						$id = !DataValidator::isEmpty($status->getId()) ? $status->getId() : 0;
						$nome_status = !DataValidator::isEmpty($status->getStatus()) ? $status->getStatus() : '';
						$nome_status_pai = !DataValidator::isEmpty($status->getStatusPai()) ? $status->getStatusPai() : '';
				?>        

				<tr class="detalhe-status" data-id="<?php echo $id; ?>">							
					<td><a href="#" title="<?php echo $nome_status_pai; ?>"><?php echo $nome_status_pai; ?></a></td>
                                        <td><a href="#" title="<?php echo $nome_status; ?>"><?php echo $nome_status; ?></a></td>	
				</tr>
                
                <?php }} ?>
	
				</table><!-- list -->

			</div>

	</div><!-- direita -->

	</div>

</div>



<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

<script>
	$('.detalhe-status').bind('click', function(){
		var status_id = $(this).attr('data-id');						
		$('#status-id').val(status_id);	
		$('#form-detalhe').submit();
	});
</script>
	
</body>
</html>