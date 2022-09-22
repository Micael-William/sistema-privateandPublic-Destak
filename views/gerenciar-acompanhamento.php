<?php
require_once("valida-sessao.php");
require_once("controllers/AcompanhamentoController.php");
error_reporting(E_ALL ^ E_DEPRECATED);

$params = $this->getParams();
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null;
$flag = isset($params['flag']) ? $params['flag'] : null;
$acompanhamento = isset($params['acompanhamento']) ? $params['acompanhamento'] : new Acompanhamento();
//tela de impressão
$impressao = isset($params['impressao']) ? $params['impressao'] : null;
$boleto = isset($params['boleto']) ? true : false;

$proposta = $acompanhamento->getProposta();
if (isset($proposta)) {
	$processo = $acompanhamento->getProposta()->getProcesso();
	$entrada = $acompanhamento->getProposta()->getProcesso()->getEntrada();
}

if (!DataValidator::isEmpty($flag))
	$_SESSION[AcompanhamentoController::ACOMP_KEY] = mt_rand(1, 1000);

?>

<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.2.custom.min.css">
	<title>Acompanhamento Processual - Destak Publicidade</title>
</head>

<body class="body-internas">

	<div id="geral">

		<form class="form-impressao std-form" action="tela-impressao-proposta.php" target="_blank" method="post">
			<input type="hidden" class="reimpressao" name="reimpressao" value="">
			<input type="hidden" name="proposta_id" value="<?php echo !DataValidator::isEmpty($proposta) ? $proposta->getId() : 0; ?>">
			<input type="hidden" name="user_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
			<input type="hidden" name="acompanhamento_id" value="<?php echo !DataValidator::isEmpty($acompanhamento) ? $acompanhamento->getId() : 0; ?>">
		</form>

		<?php
		$modalImpressao = false;
		if (isset($impressao) && !DataValidator::isEmpty($impressao)) {
			$modalImpressao = true;
		?>
			<div class="flag-impressao link-lightbox" data-rel="box-impressao"></div>

			<!-- start: lightbox msg -->
			<div class="lightbox lightbox-impressao" id="box-impressao">

				<div class="header">Impressão de Proposta</div><!-- header -->

				<div class="content">


					<form class="form-impressao form-sacado std-form" action="?controle=Acompanhamento&acao=index" method="post">
						<input type="hidden" name="controle" value="Acompanhamento">
						<input type="hidden" name="acao" value="salvaSacado">
						<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
						<input type="hidden" name="acompanhamento_id" value="<?php echo !DataValidator::isEmpty($acompanhamento) ? $acompanhamento->getId() : 0; ?>">
						<?php include("_form-financeiro.php"); ?>
					</form>
					<div class="clear"></div>
					<a href="#" title="" class="btn-impressao-proposta salvar-financeiro">
						Salvar e imprimir a proposta
					</a>
					<a href="#" title="" class="btn-impressao-proposta">
						Pular o preenchimento de faturamento e ir imprimir a proposta.
					</a>

				</div><!-- content -->

			</div>
			<!-- end: lightbox msg -->
		<?php } ?>

		<?php require_once("inc/header.inc.php"); ?>
		<!-- faixa admin -->

		<div id="conteudo" class="clearfix">

			<?php require_once("inc/sidebar.inc.php"); ?>
			<!-- sidebar -->

			<div id="direita">

				<div class="controls clearfix">

					<h1 class="std-title title title-preto title-w-btn">
						<span class="txt-14 txt-normal">Acompanhamento Processual</span> <span class="seta"> &gt; </span> <?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getNumero() : ''; ?>
					</h1>

					<div class="buttons fr">
						<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
					</div><!-- buttons -->

				</div>
				<!-- controles navegação -->

				<div class="principal">

					<!-- start: warnings -->
					<?php if (isset($msg) && !DataValidator::isEmpty($msg)) { ?>
						<span class="warning erro"><?php echo $msg; ?></span>
					<?php } ?>

					<?php if (isset($sucesso) && !DataValidator::isEmpty($sucesso)) { ?>
						<span class="warning sucesso"><?php echo $sucesso; ?></span>
					<?php } ?>
					<!-- end: warnings -->

					<div class="alert-panel panel-3">

						<div class="alert-box">
							<span class="text-lg"><?php echo !DataValidator::isEmpty($acompanhamento->getStatus()) ? $acompanhamento->getStatusDesc() : ''; ?></span>
							<span class="text-sm">Status</span>
						</div>

						<div class="alert-box">
							<span class="text-lg"><?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getDataEnvio()) ? date('d/m/Y', strtotime($proposta->getDataEnvio())) : ''; ?></span>
							<span class="text-sm">Data do Envio</span>
						</div>

						<div class="alert-box">
							<span class="text-lg"><?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getDataAceite()) ? date('d/m/Y', strtotime($proposta->getDataAceite())) : ''; ?></span>
							<span class="text-sm">Data do Aceite</span>
						</div>

					</div>
					<!-- alert panel -->

					<form method="post" id="form-pesquisa">
						<input type="hidden" name="controle" value="Acompanhamento">
						<input type="hidden" name="acao" value="busca">
						<input type="hidden" name="origem" value="acompanhamento">
					</form>

					<form action="" class="std-form" id="form-detalhe" method="post">
						<input type="hidden" name="controle" value="Acompanhamento">
						<input type="hidden" name="acao" value="detalhe">
						<input type="hidden" name="acompanhamento_id" id="acomp-id" value="<?php echo !DataValidator::isEmpty($acompanhamento) ? $acompanhamento->getId() : 0; ?>">
					</form>

					<form action="" id="form-exclusao" class="clear" method="post">
						<input type="hidden" name="controle" value="Acompanhamento">
						<input type="hidden" name="acao" value="exclui">
						<input type="hidden" name="processo_id" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) ? $processo->getId() : 0; ?>">
						<input type="hidden" name="acompanhamento_id" value="<?php echo $acompanhamento->getId(); ?>">
					</form>

					<form action="" class="std-form form-acompanhamento clear" method="post">
						<input type="hidden" name="controle" value="Acompanhamento">
						<input type="hidden" name="acao" value="salva">
						<input type="hidden" name="acompanhamento_id" value="<?php echo $acompanhamento->getId(); ?>">
						<input type="hidden" name="proposta_id" value="<?php echo !DataValidator::isEmpty($proposta) ? $proposta->getId() : 0; ?>">
						<input type="hidden" name="processo_id" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) ? $processo->getId() : 0; ?>">
						<input type="hidden" name="entrada_id" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getId() : 0; ?>">
						<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
						<input type="hidden" name="usuario_nome" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getNome() : ''; ?>">
						<input type="hidden" class="andamento_email_destino" name="andamento_email_destino" value="">
						<input type="hidden" class="obs-id" name="observacao_id" value="">
						<input type="hidden" name="tipo_peticao" value="">
						<input type="hidden" name="key" value="<?php echo $_SESSION[AcompanhamentoController::ACOMP_KEY]; ?>" />


						<div class="panel panel-accordion">

							<div class="panel-title">Dados Gerais <i class="seta seta-baixo"></i> </div>

							<div class="panel-content" style="display: block;">

								<br>

								<div class="campo-box">

									<label>Status</label>

									<select name="status" id="acomp-status" class="acomp-status">
										<?php
										$status = AcompanhamentoStatusModel::listaPai();
										if (!DataValidator::isEmpty($status)) {
											foreach ($status as $status_reg) {
										?>
												<option value="<?php echo $status_reg->getCodigo(); ?>" <?php echo !DataValidator::isEmpty($acompanhamento->getStatus()) && $acompanhamento->getStatus() == $status_reg->getCodigo() ? 'selected' : ''; ?>><?php echo $status_reg->getStatus(); ?></option>
										<?php
											}
										}
										?>
									</select>

								</div>

								<div class="campo-box">

									<label>Substatus</label>

									<select name="substatus" id="acomp-substatus" class="acomp-substatus">
										<option value="0">Selecione</option>
										<?php
										$substatus = AcompanhamentoStatusModel::listaByStatus($acompanhamento->getStatus());
										if (!DataValidator::isEmpty($substatus)) {
											foreach ($substatus as $substatus_reg) {
										?>
												<option value="<?php echo $substatus_reg->getId(); ?>" <?php echo !DataValidator::isEmpty($acompanhamento->getSubStatus()) && $acompanhamento->getSubStatus() == $substatus_reg->getId() ? 'selected' : ''; ?>><?php echo $substatus_reg->getStatus(); ?></option>
										<?php
											}
										}
										?>
									</select>

								</div>

								<div class="campo-box">

									<label for="">Estado</label>
									<select name="" disabled>
										<?php
										$estados = EstadosEnum::getChavesUFs();
										//$estados = array( "DF"=>"DF", "RJ"=>"RJ", "SP"=>"SP" );			
										foreach ($estados as $key => $value) { ?>
											<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && $entrada->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
										<?php } ?>
									</select>

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Data do Processo</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getDataEntrada()) ? date('d/m/Y', strtotime($processo->getDataEntrada())) : ''; ?>" class="std-input date-input campo-1" readonly="readonly">

								</div>

								<!--<div class="campo-box">

								<label for="">Data Entrada Sistema</label>
								<input type="text" value="<?php //echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getDataEntrada()) ? date('d/m/Y', strtotime($entrada->getDataEntrada() )) : ''; 
															?>"
								class="std-input date-input" readonly="readonly">

							</div>-->
								<!-- campo -->

								<div class="campo-box">

									<label for="">Nº do Processo</label>
									<input type="text" name="num_processo" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getNumero() : ''; ?>" class="std-input">

								</div>

								<div class="campo-box">

									<label for="">Código Interno</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($acompanhamento->getId()) ? $acompanhamento->getId() : ''; ?>" class="std-input" readonly="readonly">

								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="" class="tooltip-link" data-title="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getNomeRespEnvio()) ? $proposta->getNomeRespEnvio() : ''; ?>" data-x="30" data-y="30">Data do Envio</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getDataEnvio()) ? date('d/m/Y', strtotime($proposta->getDataEnvio())) : ''; ?>" class="std-input date-input" readonly="readonly">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="" class="tooltip-link" data-title="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getNomeRespAceite()) ? $proposta->getNomeRespAceite() : ''; ?>" data-x="30" data-y="30" data-direction="left">Data do Aceite</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($proposta->getDataAceite()) ? date('d/m/Y', strtotime($proposta->getDataAceite())) : ''; ?>" class="std-input date-input" readonly="readonly">
								</div>

								<?php if (!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada)) { ?>
									<div class="campo-box">
										<label for="">Ação</label>
										<input type="text" name="processo_acao" value="<?php echo $processo->getAcao(); ?>" class="std-input">
									</div>
									<!-- campo -->
								<?php } ?>

								<div class="campo-box">
									<label for="">Requerente</label>
									<input type="text" class="std-input" name="requerente" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) ? $processo->getRequerente() : ''; ?>">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Requerido</label>
									<input type="text" class="std-input" name="requerido" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) ? $processo->getRequerido() : ''; ?>">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Secretaria/Fórum</label>
									<select name="secr_id" id="">
										<option value="0">Selecione</option>
										<?php
										$secretarias = SecretariaModel::listaByEstado(!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : null);
										if (!DataValidator::isEmpty($secretarias)) {
											foreach ($secretarias as $sec) {
										?>
												<option value="<?php echo $sec->getId(); ?>" <?php echo (!DataValidator::isEmpty($processo->getSecretaria()) && $processo->getSecretaria()->getId() == $sec->getId()) ? 'selected' : null; ?>><?php echo $sec->getNome(); ?></option>
										<?php }
										} ?>
									</select>
								</div>
								<!-- campo -->

								<?php
								if (!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getConteudo())) {
								?>
									<div class="campo-box">
										<label for="" class="label-textarea">Conteúdo</label>
										<textarea readonly name="conteudo" rows="8" class="std-input"><?php echo $entrada->getConteudo(); ?>
								</textarea>
									</div>
									<!-- campo -->
								<?php } ?>

							</div>
							<!-- panel content -->

						</div>
						<!-- panel dados gerais -->

						<br>

						<!-- start:advogado -->
						<div class="panel panel-accordion">

							<div class="panel-title">Advogado <i class="seta seta-baixo"></i> </div>

							<div class="panel-content" style="display: block;">

								<div class="campo-box">
									<label>Nome</label>
									<input type="text" name="advogado" value="<?php if (
																					!DataValidator::isEmpty($proposta) &&
																					!DataValidator::isEmpty($processo) &&
																					!DataValidator::isEmpty($processo->getAdvogado())
																				)
																					echo $processo->getAdvogado()->getNome();
																				elseif (isset($_POST['advogado']))
																					echo $_POST['advogado'];
																				?>" class="std-input advogado-field" placeholder="Digite o nome do Advogado para a seleção no Banco de Dados">

									<input type="hidden" id="advogado-id" name="advogado_id" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">




								</div>
								<!-- campo -->

								<?php
								if (!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado())) {
									$emails = $processo->getAdvogado()->getEmails();
									if (!DataValidator::isEmpty($emails)) {
										foreach ($emails as $email) {
								?>

											<div class="campo-box">
												<label>E-mail</label>
												<input type="text" value="<?php echo $email->getEmailEndereco(); ?>" readonly class="std-input">
											</div>
											<!-- campo -->

								<?php }
									}
								} ?>

								<?php
								if (!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado())) {
									$telefones = $processo->getAdvogado()->getTelefones();
									if (!DataValidator::isEmpty($telefones)) {
										foreach ($telefones as $tel) {
								?>
											<div class="campo-box">
												<label>DDD/Telefone</label>
												<input type="text" value="<?php echo $tel->getDdd(); ?>" readonly class="std-input ddd-input">
												<input type="text" value="<?php echo $tel->getNumero(); ?>" readonly class="std-input tel-input">
											</div>
											<!-- campo -->

								<?php }
									}
								} ?>

								<div class="campo-box">
									<label>Nome do Contato</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ?	$processo->getAdvogado()->getNomeContato() : ''; ?>" readonly class="std-input">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label>E-mail do Contato</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ?	$processo->getAdvogado()->getEmailContato() : ''; ?>" readonly class="std-input">
								</div>
								<!-- campo -->


								<div class="campo-box clearfix">

									<div class="panel panel-accordion panel-default-bg">

										<div class="panel-title obs-title">
											Observações &nbsp;&nbsp;
											<?php
											if (
												!DataValidator::isEmpty($responsabilidades) &&
												isset($responsabilidades[4]) &&
												$responsabilidades[4]['acao'] == 'E'
											) {
											?>
												<a href="#" title="Adicionar Observação" class="std-btn sm-btn add-obs">Adicionar</a>
											<?php } //nivel de acesso 
											?>
											<i class="seta seta-frente"></i>
										</div>

										<div class="panel-content panel-obs">

											<?php
											$observacoes = $acompanhamento->getObservacoes();
											if (!DataValidator::isEmpty($observacoes)) {
												foreach ($observacoes as $obs) {
											?>
													<div class="box box-obs">

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

																<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="acompanhamento-adv-obs">Excluir</a>
															<?php } //nivel de acesso 
															?>

														</label>

														<textarea name="observacao[]" readonly rows="5" class="std-input adv-obs"><?php echo $obs->getMensagem(); ?></textarea>
														<input type="hidden" name="obs_id[]" value="<?php echo $obs->getId(); ?>">

													</div>
													<!-- obs gravada -->

											<?php }
											} ?>

										</div>
										<!-- panel content -->

									</div>
									<!-- panel obs -->

								</div>
								<!-- campo -->

							</div>
							<!-- panel content -->

						</div>
						<!-- panel advogado -->

						<!-- end:advogado -->
						<br>

						<?php include("_form-financeiro.php"); ?>

						<br>

						<!-- start:jornal -->
						<div class="panel panel-accordion clearfix">

							<div class="panel-title jornal-title">
								Jornal Aceito <i class="seta seta-frente"></i>
							</div>

							<div class="panel-content" style="display: none;">

								<div class="campo-box">
									<label>Jornal</label>
									<select name="jornal_id" id="sel-jornal-padrao">
										<option value="0">Selecione</option>
										<?php
										$jornais = JornalModel::listaBySecretaria(!DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) ? $processo->getSecretaria()->getId() : 0);

										if (!DataValidator::isEmpty($jornais)) {
											foreach ($jornais as $journal) {
										?>
												<option value="<?php echo $journal->getId(); ?>" <?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) && $processo->getJornal()->getId() == $journal->getId() ? 'selected' : ''; ?>>
													<?php echo $journal->getNome(); ?> - <?php echo $journal->getStatusDesc(); ?> <?php echo ' - ' . (!DataValidator::isEmpty($journal->getDataConfirmacao()) ? date('d/m/Y', strtotime($journal->getDataConfirmacao())) : 'sem Data de Confirmação'); ?></option>
										<?php }
										} ?>
									</select>
								</div>
								<!-- campo -->

								<?php
								$custo = !DataValidator::isEmpty($acompanhamento->getCusto()) ? $acompanhamento->getCusto() : null;
								if (!DataValidator::isEmpty($custo)) {
								?>
									<div class="campo-box">
										<label class="label">Quantidade Padrão</label>
										<input type="text" name="quantidade_padrao" value="<?php echo $custo->getQuantidadePadrao(); ?>" class="std-input">
									</div>
									<!-- campo -->

									<div class="campo-box">
										<label class="label">Valor Padrão (R$) </label>
										<input type="text" name="valor_padrao" value="<?php echo $custo->getValorPadrao(); ?>" class="std-input money-input">
									</div>
									<!-- campo -->

									<?php if (!DataValidator::isEmpty($custo->getQuantidadeDje())) { ?>
										<div class="campo-box">
											<label class="label">Quantidade DJE</label>
											<input type="text" name="quantidade_dje" value="<?php echo $custo->getQuantidadeDje(); ?>" class="std-input">
										</div>
										<!-- campo -->

										<div class="campo-box">
											<label class="label">Valor DJE (R$) </label>
											<input type="text" name="valor_dje" value="<?php echo $custo->getValorDje(); ?>" class="std-input money-input">
										</div>
										<!-- campo -->
									<?php } //dje 
									?>

									<div class="campo-box">
										<label class="label">Valor Final (R$) </label>
										<input type="text" name="valor_final" value="<?php echo $custo->getValorFinal(); ?>" class="std-input money-input">
									</div>

								<?php } //custo proposta 
								?>
								<!-- campo -->

							</div>
							<!-- panel content -->

						</div>
						<!-- panel jornal -->

						<!-- end:jornal -->

						<br>

						<!-- start:andamento -->
						<div class="panel panel-accordion panel-default-bg clearfix">

							<div class="panel-title andamento-title">Andamento Processual &nbsp;&nbsp;
								<?php
								if (
									!DataValidator::isEmpty($responsabilidades) &&
									isset($responsabilidades[4]) &&
									$responsabilidades[4]['acao'] == 'E'
								) {
								?>
									<a href="#" title="Adicionar Andamento" class="std-btn add-btn add-andamento">Adicionar</a>
								<?php                                           } //nivel de acesso 
								?>
								<i class="seta seta-baixo"></i>
							</div>

							<div class="panel-content panel-andamento" style="display: block;">
								<input type="hidden" name="andamento_email" id="andamento_email" value="<?php echo !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) ? $processo->getAdvogado()->getStrEmailsEnviar() : ''; ?>">
								<?php
								$observacoes_acomp = $acompanhamento->getObservacoesAcompanhamento();
								if (!DataValidator::isEmpty($observacoes_acomp)) {
									$iterator = 0;
									foreach ($observacoes_acomp as $obs_acomp) {
										$iterator++;
								?>
										<div class="box box-andamento clearfix <?php echo $obs_acomp->getStatus() == 'E' ? 'andamento-enviado' : ''; ?>" data-id="<?php echo $obs_acomp->getId(); ?>" style="border: 1px dotted #bbb">

											<span class="fl" style="padding:5px;">
												Criação do Andamento em <strong><?php echo date('d/m/Y', strtotime($obs_acomp->getDataEntrada())); ?></strong>
												&nbsp;por&nbsp;<strong><?php echo $obs_acomp->getRespCadastro(); ?></strong><br />
											</span>
											<span class="fr">

												<?php if ($obs_acomp->getStatus() == 'E') { ?>
													<a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox snd_andamento" data-id="<?php echo $iterator; ?>" data-rel="box-envia-andamento">Reenviar e-mail</a>
												<?php                                                           } else {
												?> <a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox snd_andamento" data-id="<?php echo $iterator; ?>" data-rel="box-envia-andamento">Enviar e-mail</a>
													<?php                                                           }
												if (
													!DataValidator::isEmpty($responsabilidades) &&
													isset($responsabilidades[4]) &&
													$responsabilidades[4]['acao'] == 'E' &&
													$obs_acomp->getStatus() == 'N'
												) {
													if (
														isset($usuario_logado) &&
														!DataValidator::isEmpty($usuario_logado) &&
														$usuario_logado->getId() == $obs_acomp->getUsuarioCadastroId()
													) {
													?>
														<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs_acomp->getId(); ?>" data-acao="acompanhamento-obs">Excluir</a>
												<?php
													} //usuario 
												} //nivel acesso
												?>
											</span>

											<textarea name="observacao_acompanhamento[]" class="std_andamento" id="tex<?php echo $iterator; ?>" readonly <?php echo $obs_acomp->getStatus() == 'E' ? 'readonly' : ''; ?> rows="5" class="std-input"><?php echo $obs_acomp->getMensagem(); ?></textarea>
											<input type="hidden" name="obs_acomp_id[]" id="and<?php echo $iterator; ?>" value="<?php echo $obs_acomp->getId(); ?>">

											<?php if ($obs_acomp->getStatus() == 'E') { ?>
												<div class="box" style="padding:5px;">
													Enviado em <strong><?php echo date('d/m/Y', strtotime($obs_acomp->getDataEnvio())); ?></strong>
													&nbsp;por&nbsp;<strong><?php echo $obs_acomp->getRespEnvio(); ?></strong>
												</div>
											<?php                                                           }
											?> </div>
										<!-- obs gravada -->
								<?php                                               }
								}
								?>
							</div>
							<!-- panel content -->

						</div>
						<!-- panel andamento -->

						<br>

						<div class="panel panel-accordion panel-default-bg clear">

							<div class="panel-title">Petições <i class="seta seta-frente"></i> </div>
							<!-- panel title -->

							<div class="panel-content" style="display: none;">

								<div class="box-peticoes clearfix">

									<div class="peticao-item clearfix clear">
										<span class="nome">Autorização</span>

										<div class="btn-peticao-wrapper">

											<?php
											if (
												!DataValidator::isEmpty($responsabilidades) &&
												isset($responsabilidades[4]) &&
												$responsabilidades[4]['acao'] == 'E'
											) {
											?>

												<a href="#" title="Enviar por e-mail" data-acao="envio" data-tipo="autorizacao" data-mensagem="Confirma o envio da Petição?" class="std-btn sm-btn <?php echo $acompanhamento->getEnvioAutorizacao() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> btn-peticao">Enviar por e-mail</a>
											<?php } //nivel de acesso 
											?>
											<a href="#" data-acao="geracao" data-tipo="autorizacao" data-mensagem="Confirma a geração da Petição?" title="Gerar arquivo" class="std-btn <?php echo $acompanhamento->getGeraAutorizacao() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> sm-btn btn-peticao" target="_blank">Gerar arquivo</a>

										</div>

									</div>
									<!-- peticao -->

									<div class="peticao-item clearfix clear">
										<span class="nome">Comprovante</span>

										<div class="btn-peticao-wrapper">

											<?php
											if (
												!DataValidator::isEmpty($responsabilidades) &&
												isset($responsabilidades[4]) &&
												$responsabilidades[4]['acao'] == 'E'
											) {
											?>

												<a href="#" title="Enviar por e-mail" data-acao="envio" data-tipo="comprovante" data-mensagem="Confirma o envio da Petição?" class="std-btn sm-btn <?php echo $acompanhamento->getEnvioComprovante() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> btn-peticao">Enviar por e-mail</a>
											<?php } //nivel de acesso 
											?>
											<a href="#" data-acao="geracao" data-tipo="comprovante" data-mensagem="Confirma a geração da Petição?" title="Gerar arquivo" target="_blank" class="std-btn <?php echo $acompanhamento->getGeraComprovante() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> sm-btn btn-peticao">Gerar arquivo</a>

										</div>

									</div>
									<!-- peticao -->

									<div class="peticao-item clearfix clear">
										<span class="nome">Guia DJE</span>

										<div class="btn-peticao-wrapper">
											<?php
											if (
												!DataValidator::isEmpty($responsabilidades) &&
												isset($responsabilidades[4]) &&
												$responsabilidades[4]['acao'] == 'E'
											) {
											?>
												<a href="#" title="Enviar por e-mail" data-acao="envio" data-tipo="guia" data-mensagem="Confirma o envio da Petição?" class="std-btn sm-btn <?php echo $acompanhamento->getEnvioGuia() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> btn-peticao">Enviar por e-mail</a>
											<?php } //nivel de acesso 
											?>
											<a href="#" data-acao="geracao" data-tipo="guia" data-mensagem="Confirma a geração da Petição?" title="Gerar arquivo" target="_blank" class="std-btn <?php echo $acompanhamento->getGeraGuia() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> sm-btn btn-peticao">Gerar arquivo</a>

										</div>

									</div>
									<!-- peticao -->

									<div class="peticao-item clearfix clear">
										<span class="nome">Minuta</span>

										<div class="btn-peticao-wrapper">
											<?php
											if (
												!DataValidator::isEmpty($responsabilidades) &&
												isset($responsabilidades[4]) &&
												$responsabilidades[4]['acao'] == 'E'
											) {
											?>
												<a href="#" title="Enviar por e-mail" data-acao="envio" data-tipo="minuta" data-mensagem="Confirma o envio da Petição?" class="std-btn sm-btn <?php echo $acompanhamento->getEnvioMinuta() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> btn-peticao">Enviar por e-mail</a>
											<?php } //nivel de acesso 
											?>
											<a href="#" title="Gerar arquivo" data-acao="geracao" data-tipo="minuta" data-mensagem="Confirma a geração da Petição?" target="_blank" class="std-btn <?php echo $acompanhamento->getGeraMinuta() == 'S' ? 'dark-gray-btn' : 'dark-red-btn'; ?> sm-btn btn-peticao">Gerar arquivo</a>

										</div>

									</div>
									<!-- peticao -->

								</div>

								<ul class="list-group petitions-list list-zebra" style="display: none;">

									<li class="list-group-item">
										<a href="#" title="Autorização" class="tooltip-link" data-title="Clique aqui para gerar o arquivo" data-width="200" data-direction="left" data-y="20" data-x="20">Autorização

										</a>
									</li>

									<li class="list-group-item"><a href="#" title="Comprovante" class="tooltip-link" data-title="Clique aqui para gerar o arquivo" data-width="200" data-direction="left" data-y="20" data-x="20">Comprovante</a></li>

									<li class="list-group-item"><a href="#" title="Guia DJE" class="tooltip-link" data-title="Clique aqui para gerar o arquivo" data-width="200" data-direction="left" data-y="20" data-x="20">Guia DJE</a></li>

									<li class="list-group-item"><a href="#" title="Minuta" class="tooltip-link" data-title="Clique aqui para gerar o arquivo" data-width="200" data-direction="left" data-y="20" data-x="20">Minuta</a></li>

								</ul>

							</div>
							<!-- panel content -->

						</div>
						<!-- panel petições -->

						<div class="controles clearfix clear">

							<div class="fl">
								<a href="#" title="Voltar" class="std-btn btn-pesquisa">Voltar</a>
								<?php if (
									!DataValidator::isEmpty($responsabilidades) &&
									isset($responsabilidades[4]) &&
									$responsabilidades[4]['acao'] == 'E'
								) {
								?>

									<a href="#" id="exclui-item-btn" title="Excluir" class="std-btn dark-red-btn del-item" data-del-message="Tem certeza que deseja excluir este Acompanhamento Processual?">Excluir</a>
								<?php       } //nivel acesso 
								?>
							</div>
							<!-- back -->

							<?php if (
								!DataValidator::isEmpty($responsabilidades) &&
								isset($responsabilidades[4]) &&
								$responsabilidades[4]['acao'] == 'E'
							) {
							?>

								<div class="fr">
									<a href="#" id="imprimirPropostaSegundaVia" title="Imprimir Proposta" class="std-btn">Imprimir Proposta</a>&nbsp;
									<input type="submit" value="Salvar" class="std-btn send-btn btn-salva-acompanhamento fr">
								</div>
								<!-- save -->

							<?php } //nivel de acesso 
							?>

						</div>
						<!-- controles -->

					</form>
					<!-- form -->

				</div>
				<!-- principal -->

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

	<?php
	if ($boleto) { ?>
		<div class="flag-boleto link-lightbox" data-rel="box-emissao-boleto"></div>
	<?php
	}
	?>

	<?php include("_form-boleto.php"); ?>

	<!-- start: lightbox de envio de andamento por e-mail -->
	<div class="lightbox" id="box-envia-andamento">

		<div class="header">Enviar Andamento Processual por e-mail</div><!-- header -->

		<div class="close-box close-lightbox">
			<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
		</div>
		<!-- //close btn -->

		<div class="content">

			<form action="" class="std-form form-lightbox" id="form-andamento-lightbox">

				<div class="warning-box clear" style="display: none; margin-bottom: 20px;">
					<span class="warning erro"></span>
				</div>

				<div class="campo-box">
					<label>Andamento</label>
					<textarea readonly name="andamento_lightbox" id="andamento_lightbox" rows="5" class="std_andamento std-input" style="width:100%;"></textarea>
				</div>
				<!-- campo -->

				<div class="campo-box clear">
					<label style="width:30%;">Endereços de E-mail</label>
					<input type="text" name="andamento_emails_lightbox" id="andamento_emails_lightbox" value="" class="std-input md-input" style="width:100%;">
					<!-- <span class="std-btn sm-btn check-btn check-secretaria">CHECAR</span> -->
				</div>
				<!-- campo -->

				<div class="controles clearfix">
					<a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
					<input type="button" value="Enviar" class="std-btn send-btn fr" id="btn-envia-andamento">
				</div>
				<!-- controles -->

			</form>
			<!-- form -->

		</div><!-- content -->

	</div>
	<!-- end: lightbox de envio de andamento por e-mail -->

	<!-- Scripts -->
	<?php require_once("inc/scripts.inc.php"); ?>

	<script>
		App.acompanhamento();
		$('.flag-impressao').trigger('click');
		$('.flag-boleto').trigger('click');

		$('.btn-pesquisa').bind('click', function() {
			$('#form-pesquisa').submit();
		});

		$('#imprimirProposta').bind('click', function() {
			$('.form-impressao').submit();
		});

		$('#imprimirPropostaSegundaVia').bind('click', function() {
			var rimp = $('input[name="reimpressao"]');
			rimp.val('fromAndamento');
			rimp.closest('.form-impressao').submit();
		});
	</script>

</body>

</html>