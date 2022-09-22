<?php
require_once("valida-sessao.php");

$params = $this->getParams();
$boletos = $params['boletos'];
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new Pesquisa();
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;

$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null;

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Boletos - Destak Publicidade</title>
	<style>
		.canceled a {
			color: red;
			font-size: 90%;
		}

		.paid a {
			color: green;
			font-size: 110%;
		}
	</style>
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

					<h1 class="std-title title title-preto title-full">Boletos</h1>

				</div>

				<div class="box-filtros clearfix">

					<div class="title titulo-filtro clearfix">
						<span class="fl">Filtros</span>
						<i class="seta fr"></i>
					</div>
					<!-- titulo -->

					<div class="campos clear">

						<form action="" class="std-form" id="form-detalhe" method="post">
							<input type="hidden" name="controle" value="Boleto">
							<input type="hidden" name="acao" value="detalhe">
							<input type="hidden" name="boleto_id" id="bol-id" value="">
						</form>
						<!--form detalhe-->

						<form method="post" id="form-limpa">
							<input type="hidden" name="controle" value="Boleto">
							<input type="hidden" name="acao" value="limpaBusca">
						</form>
						<!--form limpa busca-->

						<form action="" id="form-list" class="form-filtro clearfix form-busca-lista" method="post">

							<input type="hidden" name="controle" value="Boleto">
							<input type="hidden" name="acao" value="busca">
							<input type="hidden" name="ordenacao" id="campo_ordenacao" value="<?php echo $paginacao->getOrdenacao(); ?>">
							<input type="hidden" name="sentido_ordenacao" id="sentido_ordenacao" value="<?php echo $paginacao->getSentidoOrdenacao(); ?>">
							<input type="hidden" name="numero_pagina" id="numero-pagina" value="">

							<div class="campo-box">
								<label for="">Status</label>
								<select name="busca_status" id="bol-status" class="bol-status">
									<option value="0">Selecione</option>
									<?php
									$status = Boleto::getIuguStatuses();
									if (!DataValidator::isEmpty($status)) {
										foreach ($status as $key => $val) {
									?>
											<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($pesquisa->getStatus()) && $pesquisa->getStatus() == $key ? 'selected' : ''; ?>><?php echo $val; ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Advogado</label>
								<input type="text" name="busca_advogado" value="<?php echo $pesquisa->getNomeAdvogado(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Estado</label>
								<select name="busca_estado" id="" class="sel-estado">
									<?php
									$estados = EstadosEnum::getChavesUFs('Selecione');
									//$estados = array("0" => "Selecione", "DF"=>"DF", "RJ"=>"RJ", "SP"=>"SP" );			
									foreach ($estados as $key => $value) {
									?>
										<option value="<?php echo $key; ?>" <?php echo $pesquisa->getEstado() == $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
									<?php
									}
									?>
								</select>
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label class="fl" style="margin-right: 3px;">Secretaria/Fórum</label>
								<select multiple name="busca_secretaria[]" id="sel-secretaria">
									<?php
									if (!DataValidator::isEmpty($pesquisa->getEstado())) {
										$secretarias = SecretariaModel::listaByEstado($pesquisa->getEstado());
										if (!DataValidator::isEmpty($secretarias)) {
											foreach ($secretarias as $sec) {
												$pesqSecretarias = ($pesquisa->getSecretariaId() ? (array) $pesquisa->getSecretariaId() : array());
												?>
												<option <?php echo in_array($sec->getId(), $pesqSecretarias)  ? 'selected' : ''; ?> value="<?php echo $sec->getId(); ?>"><?php echo $sec->getNome(); ?></option>
									<?php }
										}
									} ?>
								</select>
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Número do Processo</label>
								<input type="text" name="busca_num_processo" value="<?php echo $pesquisa->getNumeroProcesso() != 0 ? $pesquisa->getNumeroProcesso() : ''; ?>" class="std-input md-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Requerente</label>
								<input type="text" name="busca_requerente" value="<?php echo $pesquisa->getNomeRequerente() != '' ? $pesquisa->getNomeRequerente() : ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Requerido</label>
								<input type="text" name="busca_requerido" value="<?php echo $pesquisa->getNomeRequerido() != '' ? $pesquisa->getNomeRequerido() : ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Código Interno</label>
								<input type="text" name="busca_acompanhamento" value="<?php echo $pesquisa->getCodigoInterno() != 0 ? $pesquisa->getCodigoInterno() : ''; ?>" class="std-input sm-input">
							</div>
							<!-- campo -->

							<div class="controles clearfix fr">
								<!--<a href="#" title="Limpar" class="std-btn clean-btn">Limpar</a>-->
								<input type="button" value="Limpar" id="btn-limpar" title="Limpar" class="std-btn clean-btn">
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

					<table width="100%" class="std-table">

						<tr>
							<th width="20%" class="ordenacao" campo="1">
								<div>Número do Processo</div>
								<div class="<?php if ($paginacao->getOrdenacao() == 1) {
												if ($paginacao->getSentidoOrdenacao() == 'd') {
													echo 'seta-para-cima';
												} else {
													echo 'seta-para-baixo';
												}
											} ?>">&nbsp;</div>
							</th>
							<th width="" class="ordenacao" campo="2">
								<div>Advogado</div>
								<div class="<?php if ($paginacao->getOrdenacao() == 2) {
												if ($paginacao->getSentidoOrdenacao() == 'd') {
													echo 'seta-para-cima';
												} else {
													echo 'seta-para-baixo';
												}
											} ?>">&nbsp;</div>
							</th>
							<th width="15%" class="ordenacao" campo="3">
								<div>Data Vencto</div>
								<div class="<?php if ($paginacao->getOrdenacao() == 3) {
												if ($paginacao->getSentidoOrdenacao() == 'd') {
													echo 'seta-para-cima';
												} else {
													echo 'seta-para-baixo';
												}
											} ?>">&nbsp;</div>
							</th>
							<th width="15%" class="ordenacao" campo="4">
								<div>Valor</div>
								<div class="<?php if ($paginacao->getOrdenacao() == 4) {
												if ($paginacao->getSentidoOrdenacao() == 'd') {
													echo 'seta-para-cima';
												} else {
													echo 'seta-para-baixo';
												}
											} ?>">&nbsp;</div>
							</th>
							<th width="10%" class="ordenacao" campo="5">
								<div>Status</div>
								<div class="<?php if ($paginacao->getOrdenacao() == 5) {
												if ($paginacao->getSentidoOrdenacao() == 'd') {
													echo 'seta-para-cima';
												} else {
													echo 'seta-para-baixo';
												}
											} ?>">&nbsp;</div>
							</th>
							<th width="10%" class="ordenacao" campo="6">
								<div style="padding-left:18px;">UF</div><?php if ($paginacao->getOrdenacao() == 6) {
																			if ($paginacao->getSentidoOrdenacao() == 'd') {
																				echo '<div class=seta-para-cima>&nbsp;</div>';
																			} else {
																				echo '<div class=seta-para-baixo>&nbsp;</div>';
																			}
																		} ?>
							</th>

						</tr>

						<?php
						if (!DataValidator::isEmpty($boletos)) {
							foreach ($boletos as $bol) {
								$observacoes = "";
								$strObservacoes = "";

								$acomp = $bol->getAcompanhamento();

								$proposta = $acomp->getProposta();
								$processo = $acomp->getProposta()->getProcesso();
								$entrada = $acomp->getProposta()->getProcesso()->getEntrada();

								$id = !DataValidator::isEmpty($bol->getId()) ? $bol->getId() : 0;
								$numero = !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getNumero() : '';
								$nome_advogado = !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getNome() : '';
								$status = !DataValidator::isEmpty($bol->getIuguStatus()) ? $bol->getIuguStatusDesc() : '';

								$estado = !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : '';

								$data_vencimento = !DataValidator::isEmpty($bol) ?  date("d/m/Y", strtotime($bol->getIuguVencimento())) : '';

								$valor = number_format($bol->getIuguValor(), 2, ",", ".");
						?>

								<tr class="detalhe-bol <?php echo $bol->getIuguStatus(); ?>" data-id="<?php echo $id; ?>">
									<td><a href="#" title="<?php echo $numero; ?>"><?php echo $numero; ?></a></td>
									<td><a href="#" title="<?php echo $nome_advogado; ?>"><?php echo $nome_advogado; ?></a></td>
									<td><a href="#" title="<?php echo $data_vencimento; ?>"><?php echo $data_vencimento; ?></a></td>
									<td><a href="#" title="R$ <?php echo $valor; ?>">R$ <?php echo $valor; ?></a></td>
									<td><a href="#" title="<?php echo $status; ?>"><?php echo $status; ?></a></td>
									<td><a href="#" title="<?php echo $estado; ?>"><?php echo $estado; ?></a></td>
								</tr>

						<?php }
						} ?>

					</table><!-- list -->

					<ul class="paginacao-lista">
						<?php echo !DataValidator::isEmpty($paginacao) ? $paginacao->getAll() : ''; ?>
					</ul>
					<!--//paginacao-->

				</div>

			</div><!-- direita -->

		</div>

	</div>

	<!-- Scripts -->
	<?php require_once("inc/scripts.inc.php"); ?>

	<script>
		$('.detalhe-bol').bind('click', function(e) {
			e.preventDefault();
			var acomp_id = $(this).attr('data-id');
			$('#bol-id').val(acomp_id);
			$('#form-detalhe').submit();
		});

		$('#btn-limpar').bind('click', function() {
			$("#form-limpa").submit();
		});

		// TRATAMENTOS DA ORDENAÇÃO
		$('.ordenacao').bind('click', function() {
			var campo_id = $(this).attr('campo');
			//alert(campo_id);
			//alert($('#sentido_ordenacao').attr('value'));        
			if (campo_id !== $('#campo_ordenacao').attr('value')) {
				$('#sentido_ordenacao').val('a');
			} else {
				var sentido = $('#sentido_ordenacao').attr('value');
				(sentido === 'a') ? $('#sentido_ordenacao').val('d'): $('#sentido_ordenacao').val('a');
			}
			$('#campo_ordenacao').val(campo_id);
			$('#form-list').submit();
		});
	</script>
</body>

</html>