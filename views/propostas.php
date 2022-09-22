<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
//$msg = isset($params['mensagem']) ? $params['mensagem'] : null;
$mensagens = isset($params['mensagens']) ? $params['mensagens'] : null;
$sucesso = isset($mensagens['sucesso']) ? $mensagens['sucesso'] : null;
$msg = isset($mensagens['mensagem']) ? $mensagens['mensagem'] : null;
$propostas = $params['propostas'];
$pesquisa = isset($params['pesquisa']) ? $params['pesquisa'] : new Pesquisa();
$paginacao = isset($params['paginacao']) ? $params['paginacao'] : null;	

?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.2.custom.min.css">
	<title>Propostas - Destak Publicidade</title>
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
					<h1 style="width:<?php echo ($usuario_logado->getPerfil()->getId() == 1) ? "527" : "680" ?>px;" class="std-title title <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[3]) && $responsabilidades[3]['acao'] == 'E' ? 'title-w-btn' : 'title-full'; ?>">Propostas</h1>
				</div>
    			<div class="fr">
				<?php 				
				if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[3]) && $responsabilidades[3]['acao'] == 'E' ){
				?>
					<a href="?controle=Proposta&acao=detalhe" title="Incluir" class="std-btn confirm-btn fl" style="width:70px;">Incluir</a>
				<?php
				}
				if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[3]) && $responsabilidades[3]['acao'] == 'E' ){
				?>
					&nbsp;<a href="#" class="std-btn dark-red-btn confirm-btn fr del-proposals-btn" style="width:150px;">Excluir Selecionados</a>
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
                
                                <form action="" class="form-detalhe" method="post">
                                    <input type="hidden" name="controle" value="Proposta">
                                    <input type="hidden" name="acao" value="detalhe">
                                    <input type="hidden" name="proposta_id" class="proposta-id" value="">
                                </form>
                                <!--form detalhe-->
                                    
				<form method="post" id="form-limpa">				
                                    <input type="hidden" name="controle" value="Proposta">
                                    <input type="hidden" name="acao" value="limpaBusca">
                                </form>
                                <!--form limpa busca-->
                                
                                <form action="" id="form-list" class="form-filtro clearfix form-busca-lista" method="post">

                                        <input type="hidden" name="controle" value="Proposta">
                                        <input type="hidden" name="acao" value="busca">
                                        <input type="hidden" name="ordenacao" id="campo_ordenacao" value="<?php echo $paginacao->getOrdenacao(); ?>">
                                        <input type="hidden" name="sentido_ordenacao" id="sentido_ordenacao" value="<?php echo $paginacao->getSentidoOrdenacao(); ?>">
                                        <input type="hidden" name="numero_pagina" id="numero-pagina" value="">

										<div class="campo-box">
											<label for="">Status da Proposta</label>
											<select name="busca_status" id="">
												<option value="0">Selecione</option>
												<option value="N" <?php echo $pesquisa->getStatus() == 'N' ? 'selected' : ''; ?>>Nova</option>
												<option value="E" <?php echo $pesquisa->getStatus() == 'E' ? 'selected' : ''; ?>>Enviada</option>							
												<option value="R" <?php echo $pesquisa->getStatus() == 'R' ? 'selected' : ''; ?>>Rejeitada</option>
											</select>
                                        </div>
                                        <!-- campo -->						

										<div class="campo-box">
											<label for="">Trabalho Pendente</label>
											<select name="busca_pendente" id="">
												<option value="0">Selecione</option>
												<option value="S" <?php echo $pesquisa->getPendente() == 'S' ? 'selected' : ''; ?>>Marcadas</option>
												<option value="N" <?php echo $pesquisa->getPendente() == 'N' ? 'selected' : ''; ?>>Não Marcadas</option>							
											</select>
                                        </div>

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
						<!-- campo -->						

						<div class="controles fr clearfix">
							<!--<a href="#" title="Limpar" class="std-btn clean-btn">Limpar</a>-->
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
						
				<form action="" class="form-propostas" method="post">
                    <input type="hidden" name="controle" value="Proposta"> 
					<input type="hidden" name="acao" value="excluiPropostas"> 

				<table width="100%" class="std-table table-proposals">

					<tr>
						<th width="32%" colspan="2" class="ordenacao" campo="1"><div>Número do Processo</div><div class="<?php if ($paginacao->getOrdenacao() == 1) { if($paginacao->getSentidoOrdenacao() == 'd') { echo 'seta-para-cima'; } else { echo 'seta-para-baixo'; } }?>">&nbsp;</div></th>
						<th width="" class="ordenacao" campo="2"><div>Advogado</div><div class="<?php if ($paginacao->getOrdenacao() == 2) { if($paginacao->getSentidoOrdenacao() == 'd') { echo 'seta-para-cima'; } else { echo 'seta-para-baixo'; } }?>">&nbsp;</div></th>
                        <th width="12%" class="ordenacao" campo="3"><div>Status</div><div class="<?php if ($paginacao->getOrdenacao() == 3) { if($paginacao->getSentidoOrdenacao() == 'd') { echo 'seta-para-cima'; } else { echo 'seta-para-baixo'; } }?>">&nbsp;</div></th>					
                        <th width="10%" class="ordenacao" campo="4"><div style="padding-left:18px;">UF</div><?php if ($paginacao->getOrdenacao() == 4) { if($paginacao->getSentidoOrdenacao() == 'd') { echo '<div class=seta-para-cima>&nbsp;</div>'; } else { echo '<div class=seta-para-baixo>&nbsp;</div>'; } }?></th>
						<?php 				
						if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[3]) && $responsabilidades[3]['acao'] == 'E' ){
						?>
						<th width="7%" style="text-align: center; padding: 0;"><input type="checkbox" value="" title="Selecionar/Desselecionar todos para Exclusão" class="check check-all-proposals"></th>
						<?php
						}
						?>
				        <!--<th width="7%">&nbsp;</th>			-->
					</tr>  
                    
                    <?php 
					 if( !DataValidator::isEmpty( $propostas ) ){
						foreach($propostas as $proposta){
							$observacoes = "";
							$strObservacoes = "";
							$processo = $proposta->getProcesso();
							$entrada = $proposta->getProcesso()->getEntrada();
							$arrObs = $proposta->getDescObs();
							foreach($arrObs as $key => $obs) { 
								$strObservacoes .= "<div class=\"box-observacao\"><b>".htmlspecialchars(date_format(date_create($key), 'd/m/Y H:i:s'))."</b> : ".htmlspecialchars($obs)."</div>"; 
							}
							$observacoes = (count($arrObs) > 0) ? "<hr><b>Observações:</b> <br> ".$strObservacoes : "";
							
							if( !DataValidator::isEmpty($processo) && $processo->getSinalizador() == 'M' ) $classe_sinalizador = 'status-negativo';	
							elseif( !DataValidator::isEmpty($processo) && $processo->getSinalizador() == 'V' ) $classe_sinalizador = 'status-positivo';	
							else $classe_sinalizador = 'status-atencao';
							
							//$alertas = !DataValidator::isEmpty($processo) ? $processo->getAlertas() : null;
							
							$data_processo = !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty( $entrada->getDataProcesso() ) ? strtotime($entrada->getDataProcesso()) : '';
							$data_entrada = !DataValidator::isEmpty($processo) && !DataValidator::isEmpty( $processo->getDataEntrada() ) ? strtotime($processo->getDataEntrada()) : '';
							$data_envio = !DataValidator::isEmpty($proposta) && !DataValidator::isEmpty( $proposta->getDataEnvio() ) ? strtotime($proposta->getDataEnvio()) : '';
                            $arr_replace = array("'","\"");
					 ?>        

					<tr <?php if( $proposta->getPendente() == "S" ) echo "style='background-color:lemonchiffon'";?>>
						<td width="4%" style="text-align: center;"><span title="Marcar como Pendência"><input type="checkbox" value="<?php echo !DataValidator::isEmpty($proposta->getId()) ? $proposta->getId() : 0; ?>" name="marcar_proposta_id[]" class="mark-proposal" <?php echo ( $proposta->getPendente() == "S" ) ? "checked" : "" ;?>></span></td>
						<td class="detalhe-proposta" data-id="<?php echo !DataValidator::isEmpty( $proposta->getId() ) ? $proposta->getId() : 0; ?>">
							<a href="#" 
							data-title="
								<?php
								if (!DataValidator::isEmpty( $data_envio )) {
								?>
								Data de envio: <strong><?php echo date('d/m/Y', $data_envio); ?></strong><br>
								<?php
								}
								?>
								Data do processo: <strong><?php echo !DataValidator::isEmpty( $data_processo ) ? date('d/m/Y', $data_processo) : ''; ?></strong> 
								<br>
								Data entrada no sistema: <strong><?php echo !DataValidator::isEmpty( $data_entrada ) ? date('d/m/Y', $data_entrada) : ''; ?></strong> 
								<br>
								Requerente: <strong><?php echo !DataValidator::isEmpty( $processo ) && !DataValidator::isEmpty( $processo->getRequerente() ) ? str_replace( $arr_replace, "", DataFilter::limitaString( $processo->getRequerente() , 65) ) : ''; ?></strong> 
								<br>
								Requerido: <strong><?php echo !DataValidator::isEmpty( $processo ) && !DataValidator::isEmpty( $processo->getRequerido() ) ? str_replace( $arr_replace, "", DataFilter::limitaString( $processo->getRequerido() , 65) ) : ''; ?></strong> 
								<br>
								<?php echo str_replace( $arr_replace, "", $observacoes ); ?>
							"
						data-direction="left"
						data-width="520"
						data-y="10" data-x="15" class="tooltip-link"><?php echo !DataValidator::isEmpty( $entrada ) && !DataValidator::isEmpty( $entrada->getNumero() ) ? $entrada->getNumero() : ''; ?></a>
						</td>

						<td class="detalhe-proposta" data-id="<?php echo !DataValidator::isEmpty( $proposta->getId() ) ? $proposta->getId() : 0; ?>">
						<a href="#" title="Item Description"><?php echo !DataValidator::isEmpty( $processo ) && !DataValidator::isEmpty( $processo->getAdvogado() ) ?  $processo->getAdvogado()->getNome() : ''; ?></a></td>
						<td class="txt-center detalhe-proposta" data-id="<?php echo !DataValidator::isEmpty( $proposta->getId() ) ? $proposta->getId() : 0; ?>"><a href="#" title="Item Description"><?php echo !DataValidator::isEmpty( $proposta->getStatus() ) ? $proposta->getStatusDesc() : ''; ?></a></td>				
						<td class="txt-center detalhe-proposta" data-id="<?php echo !DataValidator::isEmpty( $proposta->getId() ) ? $proposta->getId() : 0; ?>"><a href="#" class="tooltip-link" ><?php echo !DataValidator::isEmpty( $entrada ) ? $entrada->getEstado() : ''; ?></a></td>

						<?php 				
						if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[3]) && $responsabilidades[3]['acao'] == 'E' ){
						?>
						<td style="text-align: center;"><span title="Marcar para Exclusão"><input type="checkbox" value="<?php echo !DataValidator::isEmpty($proposta->getId()) ? $proposta->getId() : 0; ?>" name="exclui_proposta_id[]" class="check-proposal"></span></td>
						<?php
						}
						?>
					</tr>
                    
                    <?php }} ?>
	
					</table><!-- list -->

					</form>
                
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

    $('.detalhe-proposta').bind('click', function(){
	var proposta_id = $(this).attr('data-id');
	$('.proposta-id').val(proposta_id);
	$('.form-detalhe').submit();
    });

    $('#btn-limpar').bind('click', function() {
	$("#form-limpa").submit();
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
	
	// CHECK/UNCHECK
	$('.check-all-proposals').on('click', function() {
		var table = $(this).closest('.table-proposals');
		var checks = table.find('.check-proposal');

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

	$('.mark-proposal').on('click', function(e) {

		e.preventDefault();
		var checkbox = $(this);
		var tr_reg = checkbox.closest('tr');
		var new_state = checkbox.is(':checked');
		var proposta_id = checkbox.val();

		$.post('ajax-proposta-pendente.php', { proposta_id: proposta_id, novo_estado: new_state }, function(response) {
			if( response == "sucesso" ) {
				if(new_state === true) {
					tr_reg.css("background-color","lemonchiffon");
					checkbox.prop('checked', true);
				}
				else {
					tr_reg.css("background-color","#fff");
					checkbox.prop('checked', false);
				}
			 }
		});
	});
	
	// EXCLUI SELECIONADOS
	$('.del-proposals-btn').on('click', function() {

		var formDeleta = $('.form-propostas'),
		confirmacao = confirm( 'Tem certeza que deseja excluir as propostas selecionadas?' );

		if(confirmacao) {
			formDeleta.find('.hidden-del-proposals').val('');
			formDeleta.submit();
		}

	});

</script>
	
</body>
</html>