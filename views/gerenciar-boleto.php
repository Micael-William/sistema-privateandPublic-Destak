<?php
require_once("valida-sessao.php");

$params = $this->getParams();
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null;
$boleto = isset($params['boleto']) ? $params['boleto'] : new Boleto();

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Boletos - Destak Publicidade</title>
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
						<span class="txt-14 txt-normal">Boletos</span> <span class="seta"> &gt; </span> Detalhes
					</h1>

					<div class="buttons fr">
						<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
					</div><!-- buttons -->

				</div>

				<div class="principal">

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
						<input type="hidden" name="controle" value="Boleto">
						<input type="hidden" name="acao" value="busca">
						<input type="hidden" name="origem" value="boleto">
					</form>

					<form action="" id="form-exclusao" method="post">
						<input type="hidden" name="controle" value="Boleto">
						<input type="hidden" name="acao" value="cancela">
						<input type="hidden" name="boleto_id" value="<?php echo $boleto->getId(); ?>">
					</form>
					<!--for exclusao-->

					<form action="" id="form-download" method="post">
						<input type="hidden" name="controle" value="Boleto">
						<input type="hidden" name="acao" value="pdf">
						<input type="hidden" name="boleto_id" value="<?php echo $boleto->getId(); ?>">
					</form>
					<!--for exclusao-->

					<!--form pesquisa-->
					<form action="" class="std-form" method="post">
						<input type="hidden" name="controle" value="Boleto">
						<input type="hidden" name="acao" value="salva">
						<input type="hidden" name="boleto_id" value="<?php echo !DataValidator::isEmpty($boleto->getId()) ? $boleto->getId() : 0; ?>">
						<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

						<div class="panel panel-accordion panel-default-bg clear">
							<div class="panel-title">Acompanhamento Processual</div>
							<!-- panel title -->
							<div class="panel-content" style="display: block">
								<?php
								$acomp = $boleto->getAcompanhamento();
								$proposta = $acomp->getProposta();
								$processo = $proposta->getProcesso();
								$secretaria = $processo->getSecretaria();
								$advogado = $processo->getAdvogado();
								$entrada = $processo->getEntrada();
								?>
								<div class="campo-box">
									<label>Número do Processo</label>
									<?php echo $entrada->getNumero(); ?>
								</div>
								<div class="campo-box">
									<label>Ação</label>
									<?php echo $processo->getAcao(); ?>
								</div>
								<div class="campo-box">
									<label>Advogado</label>
									<?php echo $advogado->getNome(); ?>
								</div>
								<div class="campo-box">
									<label>Requerente</label>
									<?php echo $processo->getRequerente(); ?>
								</div>
								<div class="campo-box">
									<label>Requerido</label>
									<?php echo $processo->getRequerido(); ?>
								</div>
								<div class="campo-box">
									<label>Secretaria/Fórum</label>
									<?php echo $secretaria->getNome(); ?>
								</div>
								<div class="campo-box">
									<label>Data Aceite</label>
									<?php echo date("d/m/Y", strtotime($proposta->getDataAceite())); ?>
								</div>
							</div>
						</div>

						<div class="controles clearfix">
							<div class="panel panel-accordion panel-default-bg">

								<div class="panel-title obs-boleto-title">
									Observações&nbsp;&nbsp;
									<?php
									if (
										!DataValidator::isEmpty($responsabilidades) &&
										isset($responsabilidades[4]) &&
										$responsabilidades[4]['acao'] == 'E'
									) {
									?>
										<a href="#" title="Adicionar Observação" class="std-btn sm-btn add-boleto-obs">Adicionar</a>
									<?php } //nivel de acesso 
									?>
								</div>

								<div class="panel-content panel-boleto" style="display: block;">

									<?php
									$observacoes = $boleto->getObservacoes();
									if (!DataValidator::isEmpty($observacoes)) {
										foreach ($observacoes as $obs) {
									?>
											<div class="box box-obs-boleto clearfix">

												<label>
													<span class="fl w-335">
														Data: <strong><?php echo date('d/m/Y', strtotime($obs->getDataEntrada())); ?></strong>
														<br>
														Usuário: <strong><?php echo $obs->getRespCadastro(); ?></strong>
													</span>

													<br class="clear">
													<br>

													<?php
													if (
														!DataValidator::isEmpty($responsabilidades) &&
														isset($responsabilidades[4]) &&
														$responsabilidades[4]['acao'] == 'E' &&
														isset($usuario_logado) &&
														!DataValidator::isEmpty($usuario_logado) &&
														$usuario_logado->getId() == $obs->getUsuarioCadastroId()
													) {
													?>

														<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="acompanhamento-boleto-obs">Excluir</a>
													<?php } //nivel de acesso 
													?>

												</label>

												<textarea name="observacao[]" readonly rows="5" class="std-input boleto-obs"><?php echo $obs->getMensagem(); ?></textarea>
												<input type="hidden" name="obs_boleto_id[]" value="<?php echo $obs->getId(); ?>">

											</div>
											<!-- obs gravada -->

									<?php }
									} else {
										echo "<p>Nenhuma observação inserida.</p>";
									} ?>

								</div>
								<!-- panel content -->

							</div>
							<!-- panel obs -->
						</div>

						<br />
						<div class="panel panel-accordion panel-default-bg clear">
							<div class="panel-title">Informações do Boleto</div>
							<!-- panel title -->
							<div class="panel-content" style="display: block">

								<div class="campo-box">
									<label>Status</label>
									<?php echo $boleto->getIuguStatusDesc(); ?>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label>Invoice</label>
									<?php echo $boleto->getIuguInvoice(); ?>
								</div>
								<!-- campo -->
								<?php
								$statuses = array('pending', 'overdue', 'accepted', 'processing');
								if (in_array($boleto->getIuguStatus(), $statuses)) {
								?>
									<div class="campo-box clear">
										<label>Boleto Link</label>
										<a href="<?php echo $boleto->getIuguUrl(); ?>" target="_blank"><?php echo $boleto->getIuguUrl(); ?></a>
									</div>
									<!-- campo -->

									<div class="campo-box clear">
										<label>Boleto</label>
										<input type="button" name="boleto" class="std-btn impressao-boleto" value="Baixar Boleto" />
									</div>
									<!-- campo -->

								<?php
								}
								?>
								<?php
								$vencimento = DateTime::createFromFormat('Y-m-d H:i:s',  $boleto->getIuguVencimento());
								?>

								<div class="campo-box clear">
									<label>Vencimento</label>
									<?php echo $vencimento->format('d/m/Y'); ?>
								</div>
								<!-- campo -->

								<div class="campo-box clear">
									<label>Valor</label>
									R$ <?php echo number_format($boleto->getIuguValor(), 2, ",", "."); ?>
								</div>
								<!-- campo -->
								<div class="campo-box">
									<label>Atualizado em</label>
									<?php echo date("d/m/Y", strtotime($boleto->getDataAlteracao())); ?>
								</div>
							</div>
						</div>
						<br />
						<div class="panel panel-accordion panel-default-bg clear">
							<div class="panel-title">Informações do Sacado</div>
							<!-- panel title -->
							<div class="panel-content" style="display: block">
								<?php
								$request = json_decode($boleto->getIuguRequest());

								$emails = isset($request->email) ? $request->email : '';

								if (isset($request->cc_emails)) {
									$emails .= ';' . $request->cc_emails;
								}

								// var_dump($request);
								?>
								<div class="campo-box clear">
									<label>Email (s)</label>
									<p><?php echo str_replace(';', '; ', $emails); ?></p>
								</div>

								<?php
								if (isset($request->payer)) :
									$payer = $request->payer;
									$address = $payer->address;
								?>
									<div class="campo-box clear">
										<label>CPF/CNPJ</label>
										<p><?php echo $payer->cpf_cnpj; ?></p>
									</div>
									<div class="campo-box clear">
										<label>Sacado</label>
										<p><?php echo $payer->name; ?></p>
									</div>
									<div class="panel">
										<h3>Endereço</h3>
										<div class="campo-box clear">
											<label>CEP</label>
											<p><?php echo $address->zip_code; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Logradouro</label>
											<p><?php echo $address->street; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Número</label>
											<p><?php echo $address->number; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Complemento</label>
											<p><?php echo $address->complement; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Bairro</label>
											<p><?php echo $address->district; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Cidade</label>
											<p><?php echo $address->city; ?></p>
										</div>
										<div class="campo-box clear">
											<label>Estado</label>
											<p><?php echo $address->state; ?></p>
										</div>
									</div>
								<?php
								endif;
								?>
							</div>
						</div>


						<div class="controles clearfix">

							<div class="buttons fl">
								<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
							</div>

							<div class="buttons fr">
								<?php
								if (!DataValidator::isEmpty($boleto->getId()) && $boleto->getPodeCancelar()) {
									if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
								?>
										<a href="#" target="_blank" title="Cancelar" class="std-btn dark-red-btn del-item" data-del-message="Tem certeza que deseja cancelar este Boleto?">Cancelar</a>
									<?php } //nivel acesso 
									?>
									<?php } //id 
									?>&nbsp;
									<input type="submit" value="Salvar" class="std-btn send-btn fr">
							</div>

						</div>
						<!-- controles -->

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

		App.boletos();
	</script>

</body>

</html>