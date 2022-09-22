	<?php 
	require_once("valida-sessao.php");
?>

<!doctype html>
<html>
<head>	
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Ambiente Administrativo</title>
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
				<h1 class="std-title title title-full">Bem Vindo</h1>
			</div>

			<div class="principal">
				
				<p class="std-content">
					Utilize o menu ao lado para navegar pelo sistema.
				</p>

			</div>

	</div><!-- direita -->

	</div>

</div>



<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>
	
</body>
</html>