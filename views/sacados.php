<?php
require_once("valida-sessao.php");

$params = $this->getParams();
$sacados = $params['sacados'];
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($mensagens['sucesso']) ? $mensagens['sucesso'] : null;
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new PesquisaSacado();
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;

$super_admin = in_array($usuario_logado->getId(), array(8, 11, 17, 61));

?>

<!doctype html>
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

					<div class="fl">
						<h1 style="width:<?php echo ($super_admin) ? "577" : "680" ?>px;" class="std-title title <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L' ? 'title-w-btn' : 'title-full'; ?>">Sacados</h1>
					</div>
					<div class="fr">
						<?php
						if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[9]) && $responsabilidades[9]['acao'] != 'L') {
						?>
							<a href="?controle=Sacado&acao=detalhe" title="Incluir" class="std-btn confirm-btn fl" style="width:70px;">Incluir</a>
						<?php
						}
						if ($super_admin) {
						?>
							&nbsp;<a href="#" class="std-btn excel-item dark-gray-btn fr" style="width:100px;">Gerar Excel</a>
						<?php
						}
						?>
					</div>

				</div>

				<div class="box-filtros">

					<div class="title titulo-filtro clearfix">
						<span class="fl">Filtros</span>
						<i class="seta fr"></i>
					</div>
					<!-- titulo -->

					<div class="campos clear">

						<form action="" id="form-detalhe" method="post">
							<input type="hidden" name="controle" value="Sacado">
							<input type="hidden" name="acao" value="detalhe">
							<input type="hidden" name="sacado_id" id="sac-id" value="">
						</form>

						<form action="" id="form-limpar-busca" method="post">
							<input type="hidden" name="controle" value="Sacado">
							<input type="hidden" name="acao" value="limpar">
						</form>

						<form action="" id="form-excel" method="post">
							<input type="hidden" name="controle" value="Sacado">
							<input type="hidden" name="acao" value="gerarTodosExcel">
							<input type="hidden" name="sacado_id" value="">
						</form>
						<!--form excel-->

						<form action="" class="form-filtro clearfix form-busca-lista" method="post">
							<input type="hidden" name="controle" value="Sacado">
							<input type="hidden" name="acao" value="busca">
							<input type="hidden" name="numero_pagina" id="numero-pagina" value="">

							<div class="campo-box">
								<label>Nome</label>
								<input type="text" name="busca_nome" value="<?php echo $pesquisa->getNome(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label>E-mail</label>
								<input type="text" name="busca_email" value="<?php echo $pesquisa->getEmail(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label>CPF/CNPJ</label>
								<input type="text" name="busca_cpf_cnpj" value="<?php echo $pesquisa->getCpfCnpj(); ?>" class="std-input sm-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Endere??o</label>
								<input type="text" name="busca_endereco" value="<?php echo $pesquisa->getEndereco(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Cidade</label>
								<input type="text" name="busca_cidade" value="<?php echo $pesquisa->getCidade(); ?>" class="std-input md-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Estado</label>
								<select name="busca_estado" id="">
									<?php
									$estados = EstadosEnum::getChavesUFs('Selecione');
									foreach ($estados as $key => $value) {
									?>
										<option value="<?php echo $key; ?>" <?php echo $pesquisa->getEstado() == $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
									<?php
									}
									?>
								</select>
							</div>
							<!-- campo -->

							<div class="controles clearfix fr">
								<a href="#" title="Limpar" class="std-btn limpar-btn">Limpar</a>
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
							<th width="">Nome</th>
							<th width="35%">CPF/CNPJ</th>
						</tr>

						<?php
						if (!DataValidator::isEmpty($sacados)) {
							foreach ($sacados as $sacado) {
								$id = !DataValidator::isEmpty($sacado->getId()) ? $sacado->getId() : 0;
								$nome = !DataValidator::isEmpty($sacado->getNome()) ? $sacado->getNome() : '';
								$cpf_cnpj = !DataValidator::isEmpty($sacado->getCpfCnpj()) ? $sacado->getCpfCnpj() : '';
								$status = !DataValidator::isEmpty($sacado->getStatus()) ? $sacado->getStatusDesc() : '';
						?>

								<tr class="detalhe-sacado" data-id="<?php echo $id; ?>">
									<td><a href="#" title="<?php echo $nome; ?>"><?php echo $nome; ?></a></td>
									<td><a href="#" title="<?php echo $cpf_cnpj; ?>"><?php echo $cpf_cnpj; ?></a></td>
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
		$('.limpar-btn').on('click', function(e) {
			$('#form-limpar-busca').submit();
		});
		$('.detalhe-sacado').bind('click', function() {
			var sacado_id = $(this).attr('data-id');
			$('#sac-id').val(sacado_id);
			$('#form-detalhe').submit();
		});
	</script>

</body>

</html>