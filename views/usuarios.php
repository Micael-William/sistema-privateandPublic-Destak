<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($mensagens['sucesso']) ? $mensagens['sucesso'] : null;
$usuarios = $params['usuarios'];
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Usuários - Destak Publicidade</title>
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
			
				<h1 class="std-title title title-w-btn">Usuários</h1>

				<div class="buttons fr">
					<a href="?controle=Usuario&acao=detalhe" title="Incluir" class="std-btn confirm-btn">Incluir</a>
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
						<input type="hidden" name="controle" value="Usuario">
						<input type="hidden" name="acao" value="detalhe">
						<input type="hidden" name="usuario_id" id="usuario-id" value="">
					</form>
					
					<form action="" class="form-filtro clearfix" method="post">
						
						<input type="hidden" name="controle" value="Usuario">
						<input type="hidden" name="acao" value="busca">
						
						<div class="campo-box">
							<label>Status</label>
							<select name="busca_status" id="">
								<option value="0">Selecione</option>
								<option value="A" <?php echo isset($_POST['busca_status']) && $_POST['busca_status'] == 'A' ? 'selected' : ''; ?>>Ativo</option>
								<option value="I" <?php echo isset($_POST['busca_status']) && $_POST['busca_status'] == 'I' ? 'selected' : ''; ?>>Inativo</option>
							</select>
						</div>
						<!-- campo -->						

						<div class="campo-box">
							<label>Nome</label>
							<input type="text" name="busca_nome" value="<?php echo isset($_POST['busca_nome']) ? $_POST['busca_nome'] : ''; ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>E-mail</label>
							<input type="text" name="busca_email" value="<?php echo isset($_POST['busca_email']) ? $_POST['busca_email'] : ''; ?>" class="std-input">
						</div>
						<!-- campo -->
						
						<div class="campo-box">
							<label>Nível de Acesso</label>
							<select name="busca_perfil" id="">
								<option value="0">Selecione</option>
								<?php 
								$perfis = PerfilModel::lista(); 
								if( !DataValidator::isEmpty($perfis) ){
									foreach( $perfis as $perfil ){
								?>
								<option value="<?php echo $perfil->getId(); ?>" <?php echo isset($_POST['busca_perfil']) && $_POST['busca_perfil'] == $perfil->getId() ? 'selected' : ''; ?>><?php echo $perfil->getNome(); ?></option>
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
				
				<table width="100%" class="std-table">

				<tr>	
					<th width="30%">Nível</th>					
					<th width="">Nome</th>
					<th width="15%">Status</th>		
				</tr>    
                
                <?php 
				if( !DataValidator::isEmpty( $usuarios ) ){												
					foreach($usuarios as $usuario){
						$id = !DataValidator::isEmpty($usuario->getId()) ? $usuario->getId() : 0;
						$nome = !DataValidator::isEmpty($usuario->getNome()) ? $usuario->getNome() : '';
						$status = !DataValidator::isEmpty($usuario->getStatus()) ? $usuario->getStatusDesc() : '';
						$perfil = !DataValidator::isEmpty($usuario->getPerfil()) ? $usuario->getPerfil()->getNome() : '';
				?>        

				<tr class="detalhe-usuario" data-id="<?php echo $id; ?>">							
					<td><a href="#" title="<?php echo $perfil; ?>"><?php echo $perfil; ?></a></td>	
					<td><a href="#" title="<?php echo $nome; ?>"><?php echo $nome; ?></a></td>	
					<td><a href="#" title="<?php echo $status; ?>"><?php echo $status; ?></a></td>									
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
	$('.detalhe-usuario').bind('click', function(){
		var usuario_id = $(this).attr('data-id');						
		$('#usuario-id').val(usuario_id);	
		$('#form-detalhe').submit();
	});
</script>
	
</body>
</html>