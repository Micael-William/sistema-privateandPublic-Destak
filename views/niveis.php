<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$perfis = $params['perfis'];
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
			
				<h1 class="std-title title title-w-btn">Níveis de Acesso</h1>

				<div class="buttons fr">
					<a href="?controle=Perfil&acao=detalhe" title="Incluir" class="std-btn confirm-btn">Incluir</a>
				</div><!-- buttons -->

			</div>

			<div class="principal">
            	
        <form action="" id="form-detalhe" method="post">
        	<input type="hidden" name="controle" value="Perfil">
        	<input type="hidden" name="acao" value="detalhe">
        	<input type="hidden" name="perfil_id" id="perfil-id" value="">
        </form>
				
				<table width="100%" class="std-table">

					<tr>	
						<th width="">Nível</th>		
					</tr>   

					<?php 
					if( !DataValidator::isEmpty( $perfis ) ){												
						foreach($perfis as $perfil){
						$id = !DataValidator::isEmpty($perfil->getId()) ? $perfil->getId() : 0;
						$nome = !DataValidator::isEmpty($perfil->getNome()) ? $perfil->getNome() : '';
					?>              

					<tr class="detalhe-perfil" data-id="<?php echo $id; ?>">				
						<td><a href="#" title="<?php echo $nome; ?>"><?php echo $nome; ?></a></td>						
					</tr>

					<?php }} ?>
	
				</table>
				<!-- list -->

			</div>
			<!-- principal -->

	</div><!-- direita -->

	</div>

</div>



<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

<script>
	$('.detalhe-perfil').bind('click', function(){
		var perfil_id = $(this).attr('data-id');						
		$('#perfil-id').val(perfil_id);	
		$('#form-detalhe').submit();
	});
</script>
	
</body>
</html>