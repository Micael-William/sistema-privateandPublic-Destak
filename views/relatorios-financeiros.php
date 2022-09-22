<?php
require_once("valida-sessao.php");

$params = $this->getParams();
$data_inicio = isset($params['data_inicio']) ? $params['data_inicio'] : null;
$data_fim = isset($params['data_fim']) ? $params['data_fim'] : null;
$resultado = isset($params['resultado']) ? $params['resultado'] : null;
$titulo = isset($params['titulo']) ? $params['titulo'] : null;
?>

<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Relatórios Financeiros - Destak Publicidade</title>
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

					<h1 class="std-title title title-full">Relatórios Financeiros</h1>

				</div>

				<div class="box-filtros">

					<div class="title titulo-filtro clearfix">
						<span class="fl">Filtros</span>
						<i class="seta-baixo fr"></i>
					</div>
					<!-- titulo -->

					<div class="campos clear" style="display:block;">

						<form action="" class="form-filtro clearfix form-busca-lista" method="post">
							<input type="hidden" name="controle" value="RelatorioFinanceiro">
							<input type="hidden" name="acao" value="busca">

							<div class="campo-box">
								<label>Tipo de Relatório</label>
								<select name="tipo_relatorio" id="" class="sel-relatorio">
									<option value="Selecione">Selecione</option>
									<option value="rel001" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'rel001' ? 'selected' : ''; ?>>Boletos Emitidos</option>
									<option value="rel002" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'rel002' ? 'selected' : ''; ?>>Boletos Pendentes de Pgto</option>
									<option value="rel003" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'rel003' ? 'selected' : ''; ?>>Boletos Vencidos</option>
									<option value="rel004" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'rel004' ? 'selected' : ''; ?>>Boletos Pagos</option>
								</select>
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Período</label>
								De &nbsp;&nbsp; <input type="text" name="data_de" value="<?php echo isset($_POST['data_de']) && !DataValidator::isEmpty($_POST['data_de']) ? $_POST['data_de'] : $data_inicio; ?>" class="date-input std-input sm-input"> &nbsp;&nbsp;&nbsp;
								Até &nbsp;&nbsp; <input type="text" name="data_ate" value="<?php echo isset($_POST['data_ate']) && !DataValidator::isEmpty($_POST['data_ate']) ? $_POST['data_ate'] : $data_fim; ?>" class="date-input std-input sm-input">
							</div>
							<!-- campo -->

							<div class="controles fr clearfix">
								<a href="?controle=RelatorioFinanceiro&acao=index" title="Limpar" class="std-btn">Limpar</a>
								<input type="submit" value="Buscar" class="std-btn send-btn">
							</div>
							<!-- controles -->

						</form>
						<!-- form -->

					</div>
					<!-- campos-->

				</div>
				<!-- filtros -->

				<?php
				if (isset($resultado)) { ?>
					<div class="box-relatorio">
						<h1 class="rel-text-cor"><?php echo $titulo; ?></h2>
					</div>
					<?php
					foreach ($resultado as $item) {
					?>
						<div class="box-relatorio" align="center">
							<table width="70%" align="center">
								<tr>
									<td colspan="2">
										<span class="rel-text-big"><?php echo strtoupper($item['estado']);  ?></span>
									</td>
								</tr>
								<tr>
									<td width="90%" style="padding:3px;"><span class="rel-text-table" align="left">
											R$ <?php echo number_format($item['total'], 2, ",", "."); ?>
										</span></td>
									<td width="10%"><span class="rel-text-small">
											<?php echo $item['qtde']; ?>
										</span></td>
								</tr>
							</table>
						</div>
						<!-- box relatório -->
				<?php
					}
				}
				?>

			</div><!-- direita -->

		</div>

	</div>

	<!-- Scripts -->
	<?php require_once("inc/scripts.inc.php"); ?>

</body>

</html>