<?php
require_once("valida-sessao.php");

$params = $this->getParams();
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null;
$sacado = isset($params['sacado']) ? $params['sacado'] : new Sacado();
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Sacados - Destak Publicidade</title>
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
						<span class="txt-14 txt-normal">Sacado</span> <span class="seta"> &gt; </span>
						<?php echo !DataValidator::isEmpty($sacado->getNome()) ? $sacado->getNome() : 'Cadastro'; ?>
					</h1>

					<div class="buttons fr">
						<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
					</div><!-- buttons -->

				</div>

				<div class="principal">

					<div class="alert-panel panel-2">

						<div class="alert-box">
							<span class="text-lg"><?php echo !DataValidator::isEmpty($sacado->getDataEntrada()) ? date('d/m/Y', strtotime($sacado->getDataEntrada())) : date('d/m/Y', strtotime(date("Y-m-d H:i:s"))); ?></span>
							<span class="text-sm">Data do Cadastro</span>
						</div>

						<div class="alert-box">
						</div>

					</div>
					<!-- alert panel -->

					<!-- start: warnings -->
					<div class="warning-box">
						<?php if (isset($msg) && !DataValidator::isEmpty($msg)) { ?>
							<span class="warning erro"><?php echo $msg; ?></span>
						<?php } ?>

						<?php if (isset($sucesso) && !DataValidator::isEmpty($sucesso)) { ?>
							<span class="warning sucesso"><?php echo $sucesso; ?></span>
						<?php } ?>
					</div>
					<!-- end: warnings -->

					<form method="post" id="form-pesquisa">
						<input type="hidden" name="controle" value="Sacado">
						<input type="hidden" name="acao" value="busca">
						<input type="hidden" name="origem" value="sacado">
					</form>
					<!--form pesquisa-->

					<form action="" id="form-exclusao" method="post">
						<input type="hidden" name="controle" value="Sacado">
						<input type="hidden" name="acao" value="exclui">
						<input type="hidden" name="sacado_id" value="<?php echo $sacado->getId(); ?>">
					</form>
					<!--for exclusao-->

					<form action="" id="form-excel" method="post">
						<input type="hidden" name="controle" value="Sacado">
						<input type="hidden" name="acao" value="gerarExcel">
						<input type="hidden" name="sacado_id" value="<?php echo $sacado->getId(); ?>">
					</form>
					<!--form excel-->

					<form action="" class="std-form clear form-has-tel form-sacado" method="post">
						<input type="hidden" name="controle" value="Sacado">
						<input type="hidden" name="acao" value="salva">
						<input type="hidden" name="sacado_id" value="<?php echo $sacado->getId(); ?>">
						<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

						<!-- start: dados gerais -->
						<div class="panel panel-accordion">

							<div class="panel-title">Dados Gerais <i class="seta seta-baixo"></i></div>
							<!-- panel title -->

							<div class="panel-content" style="display: block;">
								<input type="hidden" name="status" value="S">

								<div class="campo-box">

									<label for="">Data Cadastro</label>
									<input type="text" name="data_cadastro" readonly value="<?php echo !DataValidator::isEmpty($sacado->getDataEntrada()) ? date('d/m/Y', strtotime($sacado->getDataEntrada())) : date('d/m/Y', strtotime(date("Y-m-d H:i:s"))); ?>" class="std-input date-input">

								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Código Identificador</label>
									<input type="text" value="<?php echo $sacado->getId(); ?>" class="std-input sm-input" readonly>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Nome*</label>
									<input type="text" name="nome" value="<?php echo $sacado->getNome(); ?>" class="std-input" id="nome">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label>&nbsp;</label>
									<label style="width: auto; height:30px;"><input type="radio" name="sacado_tipo" class="btn-documento" value="pf" <?php echo !DataValidator::isEmpty($sacado->getCpfCnpj()) && strlen($sacado->getCpfCnpj()) <= 14 ? "checked" : ""; ?> /> Pessoa Fisica&nbsp;</label>
									<label style="width: auto;"><input type="radio" name="sacado_tipo" class="btn-documento" value="pj" <?php echo !DataValidator::isEmpty($sacado->getCpfCnpj()) && strlen($sacado->getCpfCnpj()) > 14 ? "checked" : ""; ?> /> Pessoa Juridica</label>
								</div>


								<div class="campo-box">
									<label for="">CPF/CNPJ*</label>
									<input type="text" name="cpf_cnpj" value="<?php echo $sacado->getCpfCnpj(); ?>" class="std-input <?php echo !DataValidator::isEmpty($sacado->getCpfCnpj()) && strlen($sacado->getCpfCnpj()) > 14 ? "cnpj-input" : "cpf-input"; ?>" id="cpf_cnpj">
								</div>

								<div class="campo-box">
									<label for="">CEP</label>
									<input type="text" name="cep" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getCep() : ''; ?>" class="std-input sm-input cep-input">

									<?php
									if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
									?>
										<a href="#" title="Buscar CEP" class="std-btn sm-btn busca-cep-btn">Buscar CEP</a>
									<?php } ?>

									<i class="loading-ico"><img src="img/loading.gif" alt=""></i>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Logradouro</label>
									<input type="text" name="logradouro" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getLogradouro() : ''; ?>" class="std-input" id="logradouro">
								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Número</label>
									<input type="text" name="numero" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getNumero() : ''; ?>" class="std-input">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Complemento</label>
									<input type="text" name="complemento" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getComplemento() : ''; ?>" class="std-input">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Bairro</label>
									<input type="text" name="bairro" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getBairro() : ''; ?>" class="std-input" id="bairro">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Cidade</label>
									<input type="text" name="cidade" value="<?php echo !DataValidator::isEmpty($sacado->getEndereco()) ? $sacado->getEndereco()->getCidade() : ''; ?>" class="std-input" id="cidade">

								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Estado</label>
									<select name="estado" class="" id="estado">
										<?php
										$estados = EstadosEnum::getChavesUFs('Selecione');

										foreach ($estados as $key => $value) {
										?>
											<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($sacado->getEndereco()) &&  $key == $sacado->getEndereco()->getEstado() ? 'selected' : ''; ?>><?php echo $value; ?></option>
										<?php
										}
										?>
									</select>
								</div>
								<!-- campo -->

								<span align="right">
									<?php if (!DataValidator::isEmpty($sacado->getDataAlteracao())) { ?>
										<span class="clear obs-form obs-form-preto obs-form-block">
											Última alteração realizada por <strong><?php echo !DataValidator::isEmpty($sacado->getUsuario()) ? $sacado->getUsuario()->getNome() : ''; ?></strong> no dia <?php echo date('d/m/Y', strtotime($sacado->getDataAlteracao())); ?>.
										</span>
									<?php } ?>
								</span>

								<div class="campo-box <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L' ? 'multiple-box' : ''; ?> clearfix">

									<?php
									if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
									?>
										<a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-email-sac">
											Adicionar E-mail
										</a>
									<?php } ?>

									<?php
									if (!DataValidator::isEmpty($sacado->getEmails())) {
										foreach ($sacado->getEmails() as $key => $email) {
											$key++;
									?>

											<div class="campo-box email-box">
												<label for="">Email</label>
												<input type="text" name="email_<?php echo $key; ?>" value="<?php echo $email->getEmailEndereco(); ?>" class="campo-email std-input md-input">
												<input type="checkbox" name="enviar_email_<?php echo $key; ?>" class="check-email" <?php echo $email->getEnviar() == 'S' ? 'checked' : ''; ?>>
												<input type="hidden" name="email_id_<?php echo $key; ?>" class="id-email" value="<?php echo $email->getId(); ?>">
												&nbsp;

												<?php
												if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
												?>
													<a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-id="<?php echo $email->getId(); ?>" data-acao="sacado-email">Excluir</a>
												<?php } ?>

											</div>
											<!-- campo -->

										<?php }
									} else { ?>

										<div class="campo-box email-box">
											<label for="">Email</label>
											<input type="text" name="email_1" class="std-input campo-email md-input">
											<input type="checkbox" name="enviar_email_1" class="check-email">
											<input type="hidden" name="email_id_1" class="id-email" value="0">
										</div>
										<!-- campo -->

									<?php } ?>

									<input type="hidden" name="qtd_emails" value="<?php echo !DataValidator::isEmpty($sacado->getEmails()) ? count($sacado->getEmails()) : 1; ?>" class="qtd-email">

								</div>
								<!-- multiple box -->

							</div>
							<!-- panel -->
							<!-- end: dados gerais -->

							<br>

							<?php
							$acompanhamentos = $sacado->getAcompanhamentos();
							if (!DataValidator::isEmpty($acompanhamentos)) {
							?>
								<!-- start: acompanhamentos vinculados -->
								<div class="panel panel-accordion">

									<div class="panel-title">Acompanhamentos Vinculados <i class="seta seta-baixo"></i> </div>

									<div class="panel-content" style="display: block;">

										<table width="100%" class="std-table stripe-table">

											<tr>
												<th width="50%">Nº do Processo</th>
												<th width="50%">Status</th>
											</tr>

											<?php foreach ($acompanhamentos as $acompanhamento) { ?>

												<tr class="detalhe-acompanhamento" data-id="<?php echo $acompanhamento->getId(); ?>">
													<td><a href="#" title="<?php echo !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()) && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : ''; ?>"><?php echo !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()) && !DataValidator::isEmpty($acompanhamento->getProposta()->getProcesso()->getEntrada()) ? $acompanhamento->getProposta()->getProcesso()->getEntrada()->getNumero() : ''; ?></a></td>
													<td><a href="#" title="<?php echo $acompanhamento->getProposta()->getStatusDesc(); ?>"><?php echo $acompanhamento->getStatusDesc(); ?></a></td>
												</tr>

											<?php } ?>

										</table>

									</div>

								</div>
								<!-- end: acompanhamentos vinculados -->
							<?php } ?>

							<br>
							<div class=""><em>* Campos de preenchimento obrigatório.</em></div>
							<br>

							<div class="controles clearfix">

								<div class="buttons fl">
									<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
									<a href="#" title="" class="std-btn excel-item">Gerar Excel</a>
								</div>

								<div class="buttons fr">
									<?php
									if (!DataValidator::isEmpty($sacado->getId())) {
										if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] == 'D') {
									?>
											<a href="#" target="_blank" title="Excluir" class="std-btn dark-red-btn del-item" data-del-message="Tem certeza que deseja excluir este Sacado?">Excluir</a>
										<?php } //nivel acesso 
										?>
									<?php } //id 
									?>

									<?php
									if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
									?>

										<input type="submit" value="Salvar" class="std-btn send-btn">

									<?php } ?>

								</div>


							</div>
							<!-- controles -->

						</div>
						<!--panel-->

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

		$('.detalhe-proposta').bind('click', function() {
			var proposta_id = $(this).attr('data-id');
			$('.proposta-id').val(proposta_id);
			$('#form-detalhe-proposta').submit();
		});

		$('.detalhe-acompanhamento').bind('click', function() {
			var item_id = $(this).attr('data-id');
			$('.acompanhamento-id').val(item_id);
			$('#form-detalhe-acompanhamento').submit();
		});
	</script>

	<script>
		App.sacados();
	</script>

</body>

</html>