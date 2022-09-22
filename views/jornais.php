<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$jornais = $params['jornais'];
$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($mensagens['sucesso']) ? $mensagens['sucesso'] : null;
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new PesquisaJornal();
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;	

?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Jornais - Destak Publicidade</title>
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
			
				<h1 class="std-title title <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ? 'title-w-btn' : 'title-full'; ?>">Jornais</h1>
				
                        <?php 				
				if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
				?>
				<div class="buttons fr">
					<a href="?controle=Jornal&acao=detalhe" title="Incluir" class="std-btn confirm-btn">Incluir</a>
				</div><!-- buttons -->
			<?php } ?>
                
			</div>

			<div class="box-filtros">
					 
				<div class="title titulo-filtro clearfix">
					<span class="fl">Filtros</span>
					<i class="seta fr"></i>
				</div>
				<!-- titulo -->

				<div class="campos clear">
                
				<form action="" class="" id="form-detalhe" method="post">
					<input type="hidden" name="controle" value="Jornal">
					<input type="hidden" name="acao" value="detalhe">
					<input type="hidden" name="jornal_id" id="jornal-id" value="">
				</form>
                                    
                                <form action="" id="form-limpar-busca" method="post">
                                        <input type="hidden" name="controle" value="Jornal">
					<input type="hidden" name="acao" value="limpar">
				</form>
					
				<form action="" class="form-filtro clearfix form-busca-lista" method="post">
					
					<input type="hidden" name="controle" value="Jornal">
					<input type="hidden" name="acao" value="busca">
          			<input type="hidden" name="numero_pagina" id="numero-pagina" value="">
					
					<div class="campo-box">
						<label for="">Status do Jornal</label>
						<select name="busca_status" id="">
							<option value="0">Selecione</option>
							<option value="P" <?php echo $pesquisa->getStatus() == 'P' ? 'selected' : ''; ?>>Padrão</option>
							<option value="C" <?php echo $pesquisa->getStatus() == 'C' ? 'selected' : ''; ?>>Comum</option>
						</select>
					</div>
					<!-- campo -->	
                    
                                        <div class="campo-box">
						<label for="">Ativo</label>
						<select name="busca_ativo" id="">
							<option value="0">Selecione</option>
							<option value="A" <?php echo $pesquisa->getAtivo() == 'A' ? 'selected' : ''; ?>>Ativo</option>
							<option value="I" <?php echo $pesquisa->getAtivo() == 'I' ? 'selected' : ''; ?>>Inativo</option>
						</select>
					</div>
					<!-- campo -->

					<div class="campo-box">
						<label for="">Estado de Circulação</label>
						<select name="busca_estado" id="" class="sel-estado sel-estado-jornal">
							<?php
							//$estados = array("0"=>"Selecione", "AC"=>"AC", "AL"=>"AL", "AM"=>"AM", "AP"=>"AP", "BA"=>"BA", "CE"=>"CE", "DF"=>"DF", "ES"=>"ES", "GO"=>"GO", "MA"=>"MA", "MG"=>"MG", "MS"=>"MS", "MT"=>"MT", "PA"=>"PA", "PB"=>"PB", "PE"=>"PE", "PI"=>"PI", "PR"=>"PR", "RJ"=>"RJ", "RN"=>"RN", "RO"=>"RO", "RR"=>"RR", "RS"=>"RS", "SC"=>"SC", "SE"=>"SE", "SP"=>"SP", "TO"=>"TO" );
                                                        $estados = EstadosEnum::getChavesUFs('Selecione');
							foreach( $estados as $key=>$value ){
							?>			
							<option value="<?php echo $key; ?>" <?php echo $pesquisa->getEstado() == $key ? 'selected' : ''; ?> ><?php echo $value; ?></option>
							<?php
							}
							?>	        
						</select>
					</div>
					<!-- campo -->
					
					<div class="campo-box">
						<label for="">Secretaria/Fórum</label>
						<select name="busca_secretaria" id="sel-secretaria">
							<option value="0">Selecione</option>
							<?php 
							$retorno_secretarias = SecretariaModel::lista();
							if( !DataValidator::isEmpty($retorno_secretarias['secretarias']) ){
								foreach($retorno_secretarias['secretarias'] as $sec){
							?>
							<option <?php echo $pesquisa->getSecretariaId() == $sec->getId() ? 'selected' : ''; ?> value="<?php echo $sec->getId(); ?>"><?php echo $sec->getNome(); ?></option>								
                                                        <?php }} ?>
						</select>
					</div>
					<!-- campo -->					

					<div class="campo-box">
						<label for="">Nome Jornal</label>
						<input type="text" name="busca_jornal" value="<?php echo $pesquisa->getNome(); ?>" class="std-input">
					</div>
					<!-- campo -->

					<div class="campo-box">
						<label for="">Nome Representante</label>
						<input type="text" name="busca_representante" value="<?php echo $pesquisa->getRepresentante(); ?>" class="std-input">
					</div>
					<!-- campo -->

					<div class="campo-box">
						<label for="">Endereço</label>
						<input type="text" name="busca_endereco" value="<?php echo $pesquisa->getEndereco(); ?>" class="std-input">
					</div>
					<!-- campo -->
                    
					<div class="campo-box">
						<label for="">Cidade</label>
						<input type="text" name="busca_cidade" value="<?php echo $pesquisa->getCidade(); ?>" class="std-input md-input">
					</div>
					<!-- campo -->
		
					<div class="campo-box">
						<label for="">E-mail</label>
						<input type="text" name="busca_email" value="<?php echo $pesquisa->getEmail(); ?>" class="std-input">
					</div>
					<!-- campo -->

					<div class="campo-box">
						<label for="">Telefone</label>
						<input type="text" name="busca_telefone" value="<?php echo $pesquisa->getTelefone(); ?>" class="std-input">
					</div>
					<!-- campo -->

					<div class="controles fr clearfix">
                                                <a href="#" title="Limpar" class="std-btn limpar-btn">Limpar</a>
						<input type="submit" value="Buscar" class="std-btn send-btn">	
					</div>
					<!-- controles -->

				</form>
				<!-- form -->

				</div>
				<!-- campos-->

			</div>

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
						<th width="">Jornal</th>	
                                                <th width="12%">Circulação</th>	
						<th width="10%">Status</th>	
                                                <th width="10%">Ativo</th>
					</tr>          

					<?php 
					if( !DataValidator::isEmpty( $jornais ) ){												
						foreach($jornais as $jornal){
							$id = !DataValidator::isEmpty($jornal->getId()) ? $jornal->getId() : 0;
							$nome = !DataValidator::isEmpty($jornal->getNome()) ? $jornal->getNome() : '';
							$status = !DataValidator::isEmpty($jornal->getStatus()) ? $jornal->getStatusDesc() : '';
							$ativo = !DataValidator::isEmpty($jornal->getAtivo()) ? $jornal->getAtivoDesc() : '';
							$estado_periodo = !DataValidator::isEmpty($jornal->getEstadoPeriodo()) ? $jornal->getEstadoPeriodo() : '';
					?>      
                    
					<tr class="detalhe-jornal" data-id="<?php echo $id; ?>">
                    
						<td><a href="#" title="<?php echo $nome; ?>" class="tooltip-link" 
						data-title="<?php 
									$cidades = $jornal->getCidades();									
									if( !DataValidator::isEmpty($cidades) ){
										$qtd_cidades = sizeof($cidades);
										foreach( $cidades as $chave => $cidade ){
											$chave+=1;
											echo $cidade->getNome();
											echo $qtd_cidades > $chave ? ', ' : '';											
										}
									}
									?>" data-direction="left" data-y="15" data-x="15" data-width="520"><?php echo $nome; ?></a></td>			
                        
						<!--<td><a href="#" title="<?php //echo $cidade; ?>"><?php //echo $cidade; ?></a></td>-->	
						<td><a href="#" title="<?php echo $estado_periodo; ?>"><?php echo $estado_periodo; ?></a></td>	
						<td><a href="#" title="<?php echo $status; ?>"><?php echo $status; ?></a></td>
						<td><a href="#" title="<?php echo $ativo; ?>"><?php echo $ativo; ?></a></td>	
					</tr>

					<?php }} ?>
                    
				</table>
				<!-- list -->
                
                        <ul class="paginacao-lista">
                                <?php echo !DataValidator::isEmpty($paginacao) ? $paginacao->getAll() : ''; ?>
                        </ul> <!--//paginacao-->

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
        $('.detalhe-jornal').bind('click', function(){
		var jornal_id = $(this).attr('data-id');						
		$('#jornal-id').val(jornal_id);	
		$('#form-detalhe').submit();
	});
</script>
	
</body>
</html>