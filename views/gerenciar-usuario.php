<?php
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$usuario = isset($params['usuario']) ? $params['usuario'] : new Usuario();	
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
			
				<h1 class="std-title title title-w-btn">
				<span class="txt-14 txt-normal">Usuários</span>
				<span class="seta"> &gt; </span>
				<?php echo !DataValidator::isEmpty($usuario->getNome()) ? $usuario->getNome() : 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="?controle=Usuario&acao=index" title="Voltar" class="std-btn dark-gray-btn">Voltar</a>
				</div><!-- buttons -->

			</div>

				<div class="principal">

				<?php if( !DataValidator::isEmpty($usuario->getId()) ){ ?>
					<div class="alert-panel panel-2">					

					<div class="alert-box">
						<span class="text-lg"><?php echo $usuario->getStatusDesc(); ?></span>
						<span class="text-sm">Status</span>
					</div>

					<div class="alert-box">
						<span class="text-lg"><?php echo !DataValidator::isEmpty($usuario->getPerfil()) ? $usuario->getPerfil()->getNome() : ''; ?></span>
						<span class="text-sm">Nível de Acesso</span>
					</div>

				</div>
				<!-- alert panel -->
        <?php } ?>

				<!-- start: warnings -->
				<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
					<span class="warning erro"><?php echo $msg; ?></span>
				<?php } ?>

				<?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
					<span class="warning sucesso"><?php echo $sucesso; ?></span>
				<?php } ?>
				<!-- end: warnings -->

				<form action="" id="form-exclusao" method="post">
                    <input type="hidden" name="controle" value="Usuario">
                    <input type="hidden" name="acao" value="exclui">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario->getId(); ?>">
                </form>
                <!--for exclusao-->

				<form action="" method="post" class="std-form clear">

						<input type="hidden" name="controle" value="Usuario">
						<input type="hidden" name="acao" value="salva">
						<input type="hidden" name="usuario_id" value="<?php echo $usuario->getId(); ?>">

						<div class="campo-box">	
							<label>Data de Cadastro</label>
							<input type="text" name="data_cadastro" class="std-input date-input" readonly="readonly" value="<?php echo !DataValidator::isEmpty($usuario->getDataEntrada()) ? date('d/m/Y', strtotime($usuario->getDataEntrada()) ) : date('d/m/Y', strtotime(date("Y-m-d H:i:s")) ); ?>">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Status*</label>
							<select name="status" id="" class="">
								<option value="A" <?php echo $usuario->getStatus() == 'A' ? 'selected' : ''; ?>>Ativo</option>
								<option value="I" <?php echo $usuario->getStatus() == 'I' ? 'selected' : ''; ?>>Inativo</option>
							</select>
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Nível de Acesso*</label>
							<select name="perfil_id" id="">
								<option value="0">Selecione</option>
                                <?php 
								$perfis = PerfilModel::lista(); 
								if( !DataValidator::isEmpty($perfis) ){
									foreach( $perfis as $perfil ){
								?>
								<option value="<?php echo $perfil->getId(); ?>" <?php echo !DataValidator::isEmpty($usuario->getPerfil()) && $usuario->getPerfil()->getId() == $perfil->getId() ? 'selected' : ''; ?>><?php echo $perfil->getNome(); ?></option>
								<?php }} ?>
							</select>
						</div>
						<!-- campo -->

						<div class="campo-box clear">
							<label>Nome*</label>
							<input type="text" name="nome" value="<?php echo $usuario->getNome(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box clear">
							<label>CPF*</label>
							<input type="text" name="cpf" <?php echo !DataValidator::isEmpty($usuario->getId()) ? 'readonly' : ''; ?> value="<?php echo $usuario->getCpf(); ?>" class="std-input md-input cpf-input">
						</div>
						<!-- campo -->

						<div class="campo-box clear">
							<label>E-mail*</label>
							<input type="text" name="email" value="<?php echo $usuario->getEmail(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box clear">
							<label>Senha <?php echo DataValidator::isEmpty($usuario->getId()) ? '*' : ''; ?></label>
							<input type="password" name="senha" class="std-input">
						</div>
						<!-- campo -->		

						<br>	
            			<div class=""><em>* Campos de preenchimento obrigatório.</em></div>

						<div class="controles clearfix">
						<?php 
							if( !DataValidator::isEmpty($usuario->getId())) {							
								if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){										
							?>
									<a href="#" target="_blank" title="Excluir" 
									class="std-btn fl del-item" data-del-message="Tem certeza que deseja excluir este Usuário?">Excluir</a>
									
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