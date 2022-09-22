<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
//$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($mensagens['sucesso']) ? $mensagens['sucesso'] : null;
$msg = isset($mensagens['mensagem']) ? $mensagens['mensagem'] : null;
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new Pesquisa();
$processos = $params['processos'];
//Para quadro de numeros de laranjas/amarelos
//$estatistica_processos = $params['estatistica_processos'];
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;	
$total_processos = isset($params['total_processos']) ? $params['total_processos'] : 0;	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Processos - Destak Publicidade</title>
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
					<h1 class="std-title title" style="width:<?php echo ($usuario_logado->getPerfil()->getId() == 1) ? "600" : "755" ?>px;">Processos</h1>
				</div>

				<?php 				
				if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[2]) && $responsabilidades[2]['acao'] == 'E' ){
				?>
				<div class="buttons fr">
					<a href="#" class="std-btn dark-red-btn confirm-btn fr del-processos-btn" style="width:150px;">Excluir Selecionados</a>
				</div><!-- buttons -->
				<?php } ?>

			</div>

			<!-- alert panel -->
			<div class="alert-panel panel-2">

				<?php 
				$qtd_amarelos = 0;
				$qtd_laranjas = 0;
				
				if( !DataValidator::isEmpty( $processos ) ){						
					foreach($processos as $proc){
						if( DataValidator::isEmpty( $proc->getAlertas() ) )
							$qtd_laranjas++;
						else	
							$qtd_amarelos++;
					}
				}
				?>

				<div class="alert-box">
					<span class="text-lg"><?php echo $total_processos; ?></span>
					<span class="text-sm"><?php echo ($pesquisa->getSinalizador() == 'M' ) ? "Vermelhos" : "Amarelos"; ?></span>
				</div>
				<!-- alert -->

				<div class="alert-box">
					<span class="text-lg"><?php echo $qtd_laranjas; ?></span>
					<span class="text-sm">Laranjas na Página</span>
				</div>	
				<!-- alert -->

			</div>
			<!-- end alert panel -->

			<div class="box-filtros clear">
					 
				<div class="title titulo-filtro clearfix">
					<span class="fl">Filtros</span>
					<i class="seta fr"></i>
				</div>
				<!-- titulo -->

				<div class="campos clear">
                
                                <form action="" class="form-detalhe" method="post">
                                        <input type="hidden" name="controle" value="Processo">
                                        <input type="hidden" name="acao" value="detalhe">
                                        <input type="hidden" name="processo_id" class="processo-id" value="">
                                </form>
                                <!--form detalhe-->

                                <form method="post" id="form-limpa">				
                                    <input type="hidden" name="controle" value="Processo">
                                    <input type="hidden" name="acao" value="limpaBusca">
                                </form>
                                <!--form limpa busca-->
					
				<form action="" id="form-list" class="form-filtro clearfix form-busca-lista" method="post">        	
							
					<input type="hidden" name="controle" value="Processo">
					<input type="hidden" name="acao" value="busca">
                                        <input type="hidden" name="ordenacao" id="campo_ordenacao" value="<?php echo $paginacao->getOrdenacao(); ?>">
                                        <input type="hidden" name="sentido_ordenacao" id="sentido_ordenacao" value="<?php echo $paginacao->getSentidoOrdenacao(); ?>">
					<input type="hidden" name="numero_pagina" id="numero-pagina" value="">
					
					<div class="campo-box">
						<label for="">Sinalizador</label>
						<select name="busca_sinalizador" id="" class="sel-sinalizador">
							<option value="0">Selecione</option>
							<option value="A" <?php echo $pesquisa->getSinalizador() == 'A' ? 'selected' : ''; ?>>Amarelo</option>
							<option value="M" <?php echo $pesquisa->getSinalizador() == 'M' ? 'selected' : ''; ?>>Vermelho</option>
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
						foreach( $estados as $key=>$value ){
						?>			
							<option value="<?php echo $key; ?>" <?php echo $pesquisa->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
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
							if( !DataValidator::isEmpty($pesquisa->getEstado()) ){
								$secretarias = SecretariaModel::listaByEstado( $pesquisa->getEstado() );
								if( !DataValidator::isEmpty($secretarias) ){
									foreach($secretarias as $sec){
							?>
							<option <?php echo $pesquisa->getSecretariaId() == $sec->getId() ? 'selected' : ''; ?> value="<?php echo $sec->getId(); ?>"><?php echo $sec->getNome(); ?></option>								
	                            <?php }}} ?>
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
						<input type="text" name="busca_processo" value="<?php echo $pesquisa->getCodigoInterno() != 0 ? $pesquisa->getCodigoInterno() : ''; ?>" class="std-input sm-input">
					</div>


					<div class="campo-box">
						<label for="">Data do Processo</label>
						<input type="text" name="busca_data_processo" value="<?php echo $pesquisa->getDataProcesso() != 0 ? $pesquisa->getDataProcesso() : ''; ?>" class="std-input sm-input">
					</div>
					<!-- campo -->

					<div class="controles fr clearfix">
						<!--<a href="#" title="Limpar" class="std-btn btn-limpar">Limpar</a>-->
						<input type="button" value="Limpar" id="btn-limpar" title="Limpar" class="std-btn clean-btn">
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
            
				<div class="warning-box">
					<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
						<span class="warning erro"><?php echo $msg; ?></span>
					<?php } ?>             
					
					<?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
                        <span class="warning sucesso"><?php echo $sucesso; ?></span>
					<?php } ?>
				</div>

				<!--
				<div class="content-box clearfix">
					<a href="#" class="std-btn confirm-btn fr del-processos-btn">Excluir Selecionados</a>
				</div>
				content box -->

				<form action="" class="form-processos" method="post">
                    <input type="hidden" name="controle" value="Processo"> 
					<input type="hidden" name="acao" value="excluiProcessos"> 
								
				<table width="100%" class="std-table table-processos">

				<tr>
                    <th width="27%" class="ordenacao" campo="1"><div>Número do Processo</div><div class="<?php if ($paginacao->getOrdenacao() == 1) { if($paginacao->getSentidoOrdenacao() == 'd') { echo 'seta-para-cima'; } else { echo 'seta-para-baixo'; } }?>">&nbsp;</div></th>
                    <th width="" class="ordenacao" campo="2"><div>Advogado</div><div class="<?php if ($paginacao->getOrdenacao() == 2) { if($paginacao->getSentidoOrdenacao() == 'd') { echo 'seta-para-cima'; } else { echo 'seta-para-baixo'; } }?>">&nbsp;</div></th>					
					<th width="10%" class="ordenacao" campo="3"><div style="padding-left:18px;">UF</div><?php if ($paginacao->getOrdenacao() == 3) { if($paginacao->getSentidoOrdenacao() == 'd') { echo '<div class=seta-para-cima>&nbsp;</div>'; } else { echo '<div class=seta-para-baixo>&nbsp;</div>'; } }?></th>
					<th width="10%" class="ordenacao" campo="4"><div style="padding-left:13px;">Flag</div><?php if ($paginacao->getOrdenacao() == 4) { if($paginacao->getSentidoOrdenacao() == 'd') { echo '<div class=seta-para-cima>&nbsp;</div>'; } else { echo '<div class=seta-para-baixo>&nbsp;</div>'; } }?></th>
					<th width="7%">&nbsp;</th>
					<?php 				
					if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[2]) && $responsabilidades[2]['acao'] == 'E' ){
					?>
					<th width="7%" style="text-align: center; padding: 0;"> <input type="checkbox" value="" title="Selecionar todos" class="check check-all-processes"> </th>
					<?php
					}
					?>
				</tr>  
                
				<?php 
				if( !DataValidator::isEmpty( $processos ) ){
						
				foreach($processos as $processo){
					$observacoes = "";
					$strObservacoes = "";
					if( $processo->getSinalizador() == 'M' ) $classe_sinalizador = 'status-negativo';	
					elseif( $processo->getSinalizador() == 'V' ) $classe_sinalizador = 'status-positivo';	
					else $classe_sinalizador = 'status-atencao';

					$arrObs = $processo->getDescObs();
					foreach($arrObs as $key => $obs) { 
						$strObservacoes .= "<div class=\"box-observacao\"><b>".htmlspecialchars(date_format(date_create($key), 'd/m/Y H:i:s'))."</b> : ".htmlspecialchars($obs)."</div>"; 
					}
					$observacoes = (count($arrObs) > 0) ? "<hr><b>Observações:</b> <br> ".$strObservacoes : "";
					
					$qtd_alertas = !DataValidator::isEmpty( $processo->getAlertas() ) ? count( $processo->getAlertas() ) : 0;							
					$data_processo = !DataValidator::isEmpty( $processo->getEntrada()->getDataProcesso() ) ? strtotime($processo->getEntrada()->getDataProcesso()) : null;
                    $arr_replace = array("'","\"");
				?>        

				<tr <?php if(@reset($processo->getRepetidos()) > 0) echo "style='opacity:0.5'"; ?>>
					<td class="detalhe-processo" data-id="<?php echo !DataValidator::isEmpty( $processo->getId() ) ? $processo->getId() : 0; ?>"><a href="#" 
					data-title="
						Data do processo: <strong><?php echo !DataValidator::isEmpty( $data_processo ) ? date('d/m/Y', $data_processo) : ''; ?></strong> 
						<br>
						Data entrada no sistema: <strong><?php echo date('d/m/Y', strtotime($processo->getDataEntrada())); ?></strong> 
						<br>
						Requerente: <strong><?php echo !DataValidator::isEmpty( $processo ) && !DataValidator::isEmpty( $processo->getRequerente() ) ? str_replace( $arr_replace, "", DataFilter::limitaString( $processo->getRequerente() , 65) ) : ''; ?></strong> 
                                                <br>
						Requerido: <strong><?php echo !DataValidator::isEmpty( $processo ) && !DataValidator::isEmpty( $processo->getRequerido() ) ? str_replace( $arr_replace, "", DataFilter::limitaString( $processo->getRequerido() , 65) ) : ''; ?></strong> 
                        <br>
						<?php echo str_replace( $arr_replace, "", $observacoes ); ?>   
					"
					data-direction="left"
					data-width="520"
					data-y="10" data-x="15" class="tooltip-link"><?php echo !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getNumero() : ''; ?></a></td>
					
					<td class="detalhe-processo" data-id="<?php echo !DataValidator::isEmpty( $processo->getId() ) ? $processo->getId() : 0; ?>"><a href="#"><?php echo !DataValidator::isEmpty($processo->getAdvogado()) ? DataFilter::limitaString($processo->getAdvogado()->getNome(), 30) : ''; ?></a></td>					
					
					<td class="txt-center detalhe-processo" data-id="<?php echo !DataValidator::isEmpty( $processo->getId() ) ? $processo->getId() : 0; ?>"><a href="#" ><?php echo !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getEstado() : ''; ?></a></td>
					
					<td nowrap class="txt-center detalhe-processo" data-id="<?php echo !DataValidator::isEmpty( $processo->getId() ) ? $processo->getId() : 0; ?>"><a href="#" class="tooltip-link" <?php echo DataValidator::isEmpty( $processo->getAlertas() ) ? 'style="background-color:#F90;"' : ''; ?>
                    accesskey=""<?php if($qtd_alertas > 0){ ?>
					data-title="
					<?php 
					if( !DataValidator::isEmpty( $processo->getAlertas() ) ){
						foreach( $processo->getAlertas() as $alerta ){
							echo $alerta . '<br>';
						}
					} 
					?>
					"
					data-direction="right"
					data-width="570"
					data-y="10" data-x="600"
                                        <?php } ?>
                                        contenteditable="">
					<?php echo $qtd_alertas; ?></a>
					</td>
					<td class="detalhe-processo status-cel <?php echo $classe_sinalizador; ?>" data-id="<?php echo !DataValidator::isEmpty( $processo->getId() ) ? $processo->getId() : 0; ?>"><a href="#" title="">&nbsp;</a></td>
					<?php 				
					if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[2]) && $responsabilidades[2]['acao'] == 'E' ){
					?>
					<td style="text-align: center;"><input type="checkbox" value="<?php echo !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getId() : 0; ?>" name="exclui_entrada_id[]" class="check-process"></td>
					<?php
					}
					?>	
				</tr>	
                
        <?php }} ?>

				</table>
				<!-- list -->

				</form>
				<!-- form -->
                
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

    $('.detalhe-processo').bind('click', function(){
	var processo_id = $(this).attr('data-id');										  
        $('.processo-id').val(processo_id);
	$('.form-detalhe').submit();
    });
	
    $('#btn-limpar').bind('click', function() {
	$("#form-limpa").submit();
    });

    // CHECK/UNCHECK
    $('.check-all-processes').on('click', function() {
	var table = $(this).closest('.table-processos'),
	checks = table.find('.check-process');

	if( this.checked ) {
            checks.each(function() {
                this.checked = true;                        
            });
        }
        else {
            checks.each(function() {
                this.checked = false;                        
            });
        }
    });

    // EXCLUI SELECIONADOS
    $('.del-processos-btn').on('click', function() {

		var formDeleta = $('.form-processos'),
		confirmacao = confirm( 'Tem certeza que deseja excluir os processos selecionados?' );

		if(confirmacao) {
			formDeleta.find('.hidden-del-processos').val('');
			formDeleta.submit();
		}

    });

    // TRATAMENTOS DA ORDENAÇÃO
    $('.ordenacao').bind('click', function(){
            var campo_id = $(this).attr('campo');
            //alert(campo_id);
            //alert($('#sentido_ordenacao').attr('value'));        
            if(campo_id !== $('#campo_ordenacao').attr('value')) {
                $('#sentido_ordenacao').val('a');
            }
            else {
                var sentido = $('#sentido_ordenacao').attr('value');
                (sentido === 'a') ? $('#sentido_ordenacao').val('d') : $('#sentido_ordenacao').val('a');
            }
            $('#campo_ordenacao').val(campo_id);
            $('#form-list').submit();
    });
        
</script>
</body>
</html>