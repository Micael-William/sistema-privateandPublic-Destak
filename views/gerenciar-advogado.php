<?php
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 		
	$advogado = isset($params['advogado']) ? $params['advogado'] : new Advogado();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Cadastro de Advogados - Destak Publicidade</title>
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
				<span class="txt-14 txt-normal">Advogado</span> <span class="seta"> &gt; </span>
				<?php echo !DataValidator::isEmpty($advogado->getNome()) ? $advogado->getNome(): 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
				</div><!-- buttons -->

			</div>

			<div class="principal">

				<div class="alert-panel panel-2">

					<div class="alert-box">
						<span class="text-lg"><?php echo !DataValidator::isEmpty($advogado->getDataEntrada()) ? date('d/m/Y', strtotime($advogado->getDataEntrada()) ) : date('d/m/Y', strtotime(date("Y-m-d H:i:s")) ); ?></span>
						<span class="text-sm">Data do Cadastro</span>
					</div>

					<div class="alert-box">
						<span class="text-lg"><?php echo !DataValidator::isEmpty($advogado->getStatus()) ? $advogado->getStatusDesc(): 'Não Cliente'; ?></span>
						<span class="text-sm">Status</span>
					</div>	

				</div>
				<!-- alert panel -->

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
                
                <form method="post" id="form-pesquisa">
                    <input type="hidden" name="controle" value="Advogado">
                    <input type="hidden" name="acao" value="busca">
                    <input type="hidden" name="origem" value="advogado">
                </form>
                <!--form pesquisa-->
                
                <form action="" id="form-detalhe-proposta" method="post" target="_blank">
                    <input type="hidden" name="controle" value="Proposta">
                    <input type="hidden" name="acao" value="detalhe">
                    <input type="hidden" class="proposta-id" name="proposta_id" value="">
                </form>
                <!--form detalhe proposta-->
                
                <form action="" id="form-detalhe-acompanhamento" method="post" target="_blank">
                    <input type="hidden" name="controle" value="Acompanhamento">
                    <input type="hidden" name="acao" value="detalhe">
                    <input type="hidden" class="acompanhamento-id" name="acompanhamento_id" value="">
                </form>
                <!--form detalhe acompanhamento-->
                
                <form action="" id="form-exclusao" method="post">
                    <input type="hidden" name="controle" value="Advogado">
                    <input type="hidden" name="acao" value="exclui">
                    <input type="hidden" name="advogado_id" value="<?php echo $advogado->getId(); ?>">
                </form>
                <!--for exclusao-->
                
                <form action="" id="form-excel" method="post">
                    <input type="hidden" name="controle" value="Advogado">
                    <input type="hidden" name="acao" value="gerarExcel">
                    <input type="hidden" name="advogado_id" value="<?php echo $advogado->getId(); ?>">
                </form>
                <!--form excel-->
        
                <form action="" class="std-form clear form-has-tel form-advogado" method="post">
                    <input type="hidden" name="controle" value="Advogado">
                    <input type="hidden" name="acao" value="salva">
                    <input type="hidden" name="advogado_id" value="<?php echo $advogado->getId(); ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

						<!-- start: dados gerais -->
						<div class="panel panel-accordion">
							
							<div class="panel-title">Dados Gerais <i class="seta seta-baixo"></i></div>	
							<!-- panel title -->
							
							<div class="panel-content" style="display: block;">

								<div class="campo-box">
									
									<label for="">Status</label>									
									<select name="status" id="" class="">
										<option value="0">Selecione</option>
										<option value="S" <?php echo $advogado->getStatus() == 'S' ? 'selected' : ''; ?>>Cliente</option>
										<option value="N" <?php echo $advogado->getStatus() == 'N' ? 'selected' : ''; ?>>Não Cliente</option>
									</select>

								</div>

								<div class="campo-box">

									<label for="">Data Cadastro</label>
									<input type="text" name="data_cadastro" readonly value="<?php echo !DataValidator::isEmpty($advogado->getDataEntrada()) ? date('d/m/Y', strtotime($advogado->getDataEntrada()) ) : date('d/m/Y', strtotime(date("Y-m-d H:i:s")) ); ?>" class="std-input date-input">

								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Código Identificador</label>
									<input type="text" value="<?php echo $advogado->getId(); ?>" class="std-input sm-input" readonly>
								</div>
								<!-- campo -->
	
								<div class="campo-box">
									<label for="">Nome*</label>
									<input type="text" name="nome" value="<?php echo $advogado->getNome(); ?>" class="std-input" id="nome">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">OAB*</label>
									<input type="text" name="oab" value="<?php echo $advogado->getOab(); ?>" class="std-input" id="oab">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Empresa</label>
									<input type="text" name="empresa" value="<?php echo $advogado->getEmpresa(); ?>" class="std-input">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">CNPJ</label>
									<input type="text" name="cnpj" value="<?php echo $advogado->getCnpj(); ?>" class="std-input md-input cnpj-input">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">CEP</label>
									<input type="text" name="cep" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getCep(): ''; ?>" class="std-input sm-input cep-input">
                                    
                                    <?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
									?>
									<a href="#" title="Buscar CEP" class="std-btn sm-btn busca-cep-btn">Buscar CEP</a>
                                    <?php } ?>
                                    
									<i class="loading-ico"><img src="img/loading.gif" alt=""></i>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Logradouro</label>
									<input type="text" name="logradouro" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getLogradouro(): ''; ?>" class="std-input" id="logradouro">
								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Número</label>
									<input type="text" name="numero" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getNumero(): ''; ?>" class="std-input">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Complemento</label>
									<input type="text" name="complemento" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getComplemento(): ''; ?>" class="std-input">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Bairro</label>
									<input type="text" name="bairro" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getBairro(): ''; ?>" class="std-input" id="bairro">

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Cidade</label>
									<input type="text" name="cidade" value="<?php echo !DataValidator::isEmpty($advogado->getEndereco()) ? $advogado->getEndereco()->getCidade(): ''; ?>" class="std-input" id="cidade">

								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Estado</label>
									<select name="estado" class="" id="estado">
									<?php
									$estados = EstadosEnum::getChavesUFs('Selecione');
									//$estados = array("0"=>"Selecione", "AC"=>"AC", "AL"=>"AL", "AM"=>"AM", "AP"=>"AP", "BA"=>"BA", "CE"=>"CE", "DF"=>"DF", "ES"=>"ES", "GO"=>"GO", "MA"=>"MA", "MG"=>"MG", "MS"=>"MS", "MT"=>"MT", "PA"=>"PA", "PB"=>"PB", "PE"=>"PE", "PI"=>"PI", "PR"=>"PR", "RJ"=>"RJ", "RN"=>"RN", "RO"=>"RO", "RR"=>"RR", "RS"=>"RS", "SC"=>"SC", "SE"=>"SE", "SP"=>"SP", "TO"=>"TO" );
									
									foreach( $estados as $key=>$value ){
									?>			
										<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($advogado->getEndereco()) &&  $key == $advogado->getEndereco()->getEstado() ? 'selected' : ''; ?> ><?php echo $value; ?></option>
									<?php
                                        }
                                    ?>										
									</select>
								</div>
								<!-- campo -->

								<span align="right">
									<?php if( !DataValidator::isEmpty($advogado->getDataAlteracao()) ){ ?>
									<span class="clear obs-form obs-form-preto obs-form-block">
									Última alteração realizada por <strong><?php echo !DataValidator::isEmpty($advogado->getUsuario()) ? $advogado->getUsuario()->getNome() : ''; ?></strong> no dia <?php echo date('d/m/Y', strtotime($advogado->getDataAlteracao()) ); ?>.
									</span>	
									<?php } ?>
								</span>

								<div class="campo-box <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ? 'multiple-box' : ''; ?> clearfix">

									<?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
									?> 
                                    <a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-tel">
										Adicionar Telefone
									</a>
                                    <?php } ?>
	 
									<?php if( !DataValidator::isEmpty($advogado->getTelefones()) ){
											foreach( $advogado->getTelefones() as $tel ){
									?>  
										               
	                				<div class="campo-box tel-box">
										<label>DDD/Telefone</label>
										<input type="text" name="ddd[]" value="<?php echo $tel->getDdd(); ?>" class="std-input ddd-input">
										<input type="text" name="numero_telefone[]" value="<?php echo $tel->getNumero(); ?>" class="std-input tel-input">
                                        <input type="hidden" name="tel_id[]" value="<?php echo $tel->getId(); ?>">
                                        &nbsp;
                                        
                                        <?php 				
										if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
										?>
                                        <a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-acao="advogado-tel" data-id="<?php echo $tel->getId(); ?>">Excluir</a>
                                        <?php } ?>
                                        
									</div>
									<!-- campo -->
									
	               					<?php }} ?>                                    

                                </div>
                                <!-- multiple box -->

								<div class="campo-box <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ? 'multiple-box' : ''; ?> clearfix">

									<?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
									?>  
                                    <a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-email-adv">
										Adicionar E-mail
									</a>
                                    <?php } ?>
                               
								<?php 
								if( !DataValidator::isEmpty($advogado->getEmails()) ){
									foreach( $advogado->getEmails() as $key=>$email ){
										$key++;
								?>   

								<div class="campo-box email-box">
									<label for="">Email</label>
									<input type="text" name="email_<?php echo $key; ?>" value="<?php echo $email->getEmailEndereco(); ?>" class="campo-email std-input md-input">
									<input type="checkbox" name="enviar_email_<?php echo $key; ?>" class="check-email" <?php echo $email->getEnviar() == 'S' ? 'checked' : ''; ?>>
									<input type="hidden" name="email_id_<?php echo $key; ?>" class="id-email" value="<?php echo $email->getId(); ?>">
									&nbsp;

									<?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
									?>
									<a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-id="<?php echo $email->getId(); ?>" data-acao="advogado-email">Excluir</a>
									<?php } ?>

								</div>
								<!-- campo -->

								<?php }} else{ ?> 

								<div class="campo-box email-box">
								<label for="">Email</label>
								<input type="text" name="email_1" class="std-input campo-email md-input">
                                <input type="checkbox" name="enviar_email_1" class="check-email">
								<input type="hidden" name="email_id_1" class="id-email" value="0">
								</div>
								<!-- campo -->

								<?php } ?>
                                
                                <input type="hidden" name="qtd_emails" value="<?php echo !DataValidator::isEmpty($advogado->getEmails()) ? count( $advogado->getEmails() ) : 1; ?>" class="qtd-email">

								</div>
								<!-- multiple box -->
                                
                <div class="campo-box">
									<label>Site</label>
									<input type="text" name="site" value="<?php echo $advogado->getSite(); ?>" class="std-input">
								</div>
								<!-- campo --> 

								<div class="campo-box">
									<label for="">Nome Contato</label>
									<input type="text" name="nome_contato" value="<?php echo $advogado->getNomeContato(); ?>" 
									class="std-input">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">E-mail Contato</label>
									<input type="text" name="email_contato" value="<?php echo $advogado->getEmailContato(); ?>" class="std-input">
								</div>
								<!-- campo -->
	
						</div>
						<!-- panel -->	
						<!-- end: dados gerais -->

						<br>

						<!-- start: observações vinculadas -->
						<div class="panel panel-accordion">

							<!-- start: observacoes -->
							<div class="campo-box clearfix">

								<div class="panel panel-accordion">
								
								<div class="panel-title obs-title">
								Observações &nbsp;&nbsp; 
                                
								<?php 				
								if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
								?>  
								<a href="#" title="Adicionar Observação" class="std-btn add-btn add-obs">Adicionar</a>
								<?php } ?>
                                
								<i class="seta seta-baixo"></i>
								</div>
								
								<div class="panel-content panel-obs" style="display: block;">
									
                 					<?php if( !DataValidator::isEmpty($advogado->getObservacoes()) ){
											foreach( $advogado->getObservacoes() as $obs ){	
									?> 
									<div class="box box-obs" id="obs-1"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong><?php echo date('d/m/Y', strtotime($obs->getDataEntrada()) ); ?></strong>
										<br>
										Usuário: <strong><?php echo $obs->getRespCadastro(); ?></strong>
										</span>

										<br class="clear">
										<br>
										
                                        <?php 				
										if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){
										?> 
										<a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="advogado-obs">Excluir</a>
                    <?php } ?>
                                        
										</label>
										<textarea name="observacao[]" rows="5" class="std-input adv-obs"><?php echo $obs->getMensagem(); ?></textarea>
                    <input type="hidden" name="obs_id[]" value="<?php echo $obs->getId(); ?>">

									</div>
									<!-- obs gravada -->
                                    <?php }} ?>
									
								</div>
								<!-- panel content -->

								</div>
								<!-- panel obs -->		

							</div>
							<!-- end: observacoes -->

						</div> 
						<!-- panel content -->
						
						<?php 
						$propostas = $advogado->getPropostas();
						if( !DataValidator::isEmpty($propostas) ){
						?>
						<!-- start: propostas vinculadas -->
						<div class="panel panel-accordion">
							
							<div class="panel-title">Propostas Vinculadas <i class="seta seta-baixo"></i> </div>

							<div class="panel-content" style="display: block;">
								
								<table width="100%" class="std-table stripe-table">

									<tr>
										<th width="50%">Nº do Processo</th>
										<th width="50%">Status</th>
									</tr>
                                    
									<?php foreach( $propostas as $proposta ){ ?>

									<tr class="detalhe-proposta" data-id="<?php echo $proposta->getId(); ?>">
										<td><a href="#" title="<?php echo !DataValidator::isEmpty($proposta->getProcesso()) && !DataValidator::isEmpty($proposta->getProcesso()->getEntrada()) ? $proposta->getProcesso()->getEntrada()->getNumero() : ''; ?>"><?php echo !DataValidator::isEmpty($proposta->getProcesso()) && !DataValidator::isEmpty($proposta->getProcesso()->getEntrada()) ? $proposta->getProcesso()->getEntrada()->getNumero() : ''; ?></a></td>
										<td><a href="#" title="<?php echo $proposta->getStatusDesc(); ?>"><?php echo $proposta->getStatusDesc(); ?></a></td>
									</tr>
                                    
								<?php } ?>

								</table>

							</div>

						</div>
						<!-- end: propostas vinculadas -->
            <?php } ?>
            
            <br>
            
            <?php 
						$acompanhamentos = $advogado->getAcompanhamentos();
						if( !DataValidator::isEmpty($acompanhamentos) ){
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
                                    
									<?php foreach( $acompanhamentos as $acompanhamento ){ ?>

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
							if( !DataValidator::isEmpty($advogado->getId())) {							
								if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){										
							?>
									<a href="#" target="_blank" title="Excluir" 
									class="std-btn dark-red-btn del-item" data-del-message="Tem certeza que deseja excluir este Advogado?">Excluir</a>
							<?php }//nivel acesso ?>
							<?php }//id ?>

              				<?php 					
							if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ){										
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

	$('.detalhe-proposta').bind('click', function(){
		var proposta_id = $(this).attr('data-id');										  
		$('.proposta-id').val(proposta_id);		
		$('#form-detalhe-proposta').submit();
	});

	$('.detalhe-acompanhamento').bind('click', function(){
		var item_id = $(this).attr('data-id');										  
		$('.acompanhamento-id').val(item_id);		
		$('#form-detalhe-acompanhamento').submit();
	});

</script>

<script>
	App.advogados();
</script>	

</body>
</html>