<?php
	require_once("valida-sessao.php");
        
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$processo = isset($params['processo']) ? $params['processo'] : new Processo();
	$entrada = $processo->getEntrada();
	//echo "<pre>";
	//var_dump(EstadosEnum::getUFs());
	//var_dump(EstadosEnum::getEstados());
	//var_dump(EstadosEnum::getChavesUFs('Selecione'));
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.2.custom.min.css">
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
            
            	<?php 
				$sinal = null; $classe = null;
				if( $processo->getSinalizador() == 'M' ){
					$sinal = 'Vermelho'; $classe = 'vermelho';
				}
				elseif( $processo->getSinalizador() == 'A' ){
					$sinal = 'Amarelo'; $classe = 'amarelo';
				}
				?>         
			
				<h1 class="std-title title title-w-btn title-<?php echo $classe; ?>"> 
				<span class="txt-14 txt-normal">Processo</span> <span class="seta"> &gt; </span> <?php echo !DataValidator::isEmpty($entrada) ? $entrada->getNumero() : ''; ?>
				</h1>

				<div class="buttons fr">
					<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
				</div><!-- buttons -->

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

				<div class="alert-panel panel-3">

					<div class="alert-box">
						<span class="text-lg">
						<?php 
							echo $sinal;
						?>                        
                        </span>
						<span class="text-sm">Sinalizador</span>
					</div>

					<div class="alert-box">
						<span class="text-lg"><?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getDataProcesso()) ? date('d/m/Y', strtotime($entrada->getDataProcesso() )) : ''; ?></span>
						<span class="text-sm">Data do Processo</span>
					</div>

					<div class="alert-box">
						<span class="text-lg"><?php echo !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : ''; ?></span>
						<span class="text-sm">Estado</span>
					</div>					

				</div>
				<!-- alert panel -->
                
                <form method="post" id="form-pesquisa">
                    <input type="hidden" name="controle" value="Processo">
                    <input type="hidden" name="acao" value="busca">
                    <input type="hidden" name="origem" value="processo">
                </form>
                <!--form pesquisa-->
                
				<form action="" class="" id="form-detalhe-jornal" target="_blank" method="post">
					<input type="hidden" name="controle" value="Jornal">
					<input type="hidden" name="acao" value="detalhe">
					<input type="hidden" name="jornal_id" id="jornal-id" value="">
				</form>
                <!--form jornal-->

				<form action="" class="" id="form-detalhe-sec" target="_blank" method="post">
					<input type="hidden" name="controle" value="Secretaria">
					<input type="hidden" name="acao" value="detalhe">
					<input type="hidden" name="secretaria_id" id="sec-id" value="">
				</form>
                <!--form secretaria-->

				<form action="" id="form-detalhe-adv" target="_blank" method="post">
					<input type="hidden" name="controle" value="Advogado">
					<input type="hidden" name="acao" value="detalhe">
					<input type="hidden" name="advogado_id" id="adv-id" value="">
				</form>
                <!--form advogado-->

				<form action="" id="form-altera-status" class="clear" method="post">
					<input type="hidden" name="controle" value="Processo">
					<input type="hidden" name="acao" value="">
					<input type="hidden" name="processo_id" value="<?php echo $processo->getId(); ?>">
				</form>
                <!--form status-->

				<form action="" id="form-exclusao" class="clear" method="post">
					<input type="hidden" name="controle" value="Processo">
					<input type="hidden" name="acao" value="exclui">
					<input type="hidden" name="entrada_id" value="<?php echo !DataValidator::isEmpty($entrada) ? $entrada->getId() : 0; ?>">
				</form>
                <!--form exclusão-->

				<form action="" id="form-processo" class="std-form clear" method="post">	      	
                <input type="hidden" name="controle" value="Processo">
                <input type="hidden" name="acao" value="atualiza">
                <input type="hidden" name="processo_id" value="<?php echo $processo->getId(); ?>">
                <input type="hidden" name="adv_id_aux" value="<?php echo !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">   
				<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
	    		<input type="hidden" name="sinalizador_processo" value="<?php echo $processo->getSinalizador(); ?>">  
							
				<?php if( !DataValidator::isEmpty( $processo->getAlertas() ) ){ ?>
				<div class="panel panel-warning bordered-1 panel-accordion">
				<div class="panel-title">Quadro de Alertas <i class="seta seta-baixo"></i> </div>

				<div class="panel-content">
					<ul class="list-group list-zebra">
								<?php 								
					foreach( $processo->getAlertas() as $alerta ){
					?>
					<li class="list-group-item list-group-item-bold"><?php echo $alerta; ?></li>
					<?php } ?>
					</ul>
				</div> 

			</div>
			<!-- quadro de alertas -->
                        
            <?php } ?>

            <br>
			
            	<?php
				$processos_repetidos = $processo->getRepetidos();
				
				if( (!DataValidator::isEmpty($entrada) && DataValidator::isEmpty($entrada->getTermoInicio())) ||																				
					 !DataValidator::isEmpty($processos_repetidos)																					  
				){ ?>
						<div class="panel panel-info bordered-1 panel-accordion">
						<div class="panel-title">Notificações <i class="seta seta-baixo"></i> </div>

						<div class="panel-content">
						<ul class="list-group list-zebra">						
							<li class="list-group-item list-group-item-bold">Não foram encontradas palavras-chave no conteúdo do processo.</li>          
							<?php
								if( !DataValidator::isEmpty($processos_repetidos) ){
									echo '<br><strong>' . count($processos_repetidos) . '</strong> Processo(s) já existe(m) com este número. Fase(s): <strong>';
									foreach($processos_repetidos as $k => $repetido){
										$k++;
										echo $repetido->getStatusDesc();
										echo $k<count($processos_repetidos) ? ', ' : '';
									}
									echo '</strong>';
								}
							?>
                            
						</ul>
						</div> 

						</div>
						<!-- quadro de alertas -->
                        
            <br>
	    <?php } ?>

            <div class="panel panel-accordion panel-jornal" style="display:block;">

						<div class="panel-title jornal-title"> Dados Gerais	&nbsp;&nbsp; <i class="seta seta-baixo"></i> </div>
						
							<div class="panel-content clearfix" style="display: block;">	    
							
								<div class="campo-box">
									
									<label for="">Sinalizador</label>
									
									<select id="" disabled="disabled">
										<option value="0">Selecione</option>
										<option value="V" <?php echo $processo->getSinalizador() == 'V' ? 'selected' : null; ?>>Verde</option>
										<option value="A" <?php echo $processo->getSinalizador() == 'A' ? 'selected' : null; ?>>Amarelo</option>
										<option value="M" <?php echo $processo->getSinalizador() == 'M' ? 'selected' : null; ?>>Vermelho</option>
									</select>

								</div>
								<!-- campo -->

								<div class="campo-box">

									<label for="">Estado</label>
									<select class="sel-estado" id="" disabled="disabled">
									<?php
                                                                        $estados = EstadosEnum::getChavesUFs('Selecione');
									//$estados = array( "DF"=>"DF", "RJ"=>"RJ", "SP"=>"SP" );			
									foreach( $estados as $key=>$value ){ ?>			
									<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($entrada) && $entrada->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
									<?php } ?>
									</select>

								</div>
								<!-- campo -->
					
								<div class="campo-box">							
									<label for="">Data do Processo</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getDataProcesso()) ? date('d/m/Y', strtotime($entrada->getDataProcesso() )) : ''; ?>"
									class="std-input date-input" readonly="readonly">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Data Entrada Sistema</label>
									<input type="text" value="<?php echo date('d/m/Y', strtotime($processo->getDataEntrada() )); ?>"
									class="std-input date-input" readonly="readonly">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label for="">Nº do Processo</label>
									<input type="text" value="<?php echo !DataValidator::isEmpty($entrada) ? $entrada->getNumero() : ''; ?>" readonly="readonly"
									class="std-input">
								</div>
								<!-- campo -->

								<div class="campo-box">							
									<label for="">Código Interno</label>
									<input type="text" value="<?php echo $processo->getId(); ?>" 
									class="std-input" readonly="readonly">
								</div>
								<!-- campo -->

							</div>
							<!-- panel content -->

						</div>
						<!-- panel accordion -->

						<br>
                        
                        <?php //if( !DataValidator::isEmpty($entrada) && $entrada->getEstado() == 'SP' ){ ?>
                        <div class="campo-box">
							<label for="">Ação</label>
							<input type="text" name="processo_acao" value="<?php echo $processo->getAcao(); ?>" class="std-input">
						</div>
						<!-- campo -->
                        <?php //} ?>

						<div class="campo-box">
							<label for="">Requerente</label>
							<input type="text" name="requerente" value="<?php echo $processo->getRequerente(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label for="">Requerido</label>
							<input type="text" name="requerido" value="<?php echo $processo->getRequerido(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">

							<div class="area-label">							
							<label 
                            <?php 
							if( !DataValidator::isEmpty($processo->getAdvogado()) ){
							?>
							for="" class="fl tooltip-link detalhe-advogado detalhe" 
							data-title="Clique aqui para ir à área de cadastro"
							data-x="30" data-y="30" data-direction="left"
							data-id="<?php echo $processo->getAdvogado()->getId(); ?>"
                            <?php } ?>
                            >Advogado</label>
							</div>                            

							<div class="area-campo">							
                            	
								<?php if( DataValidator::isEmpty($processo->getAdvogado()) ) { ?>
								<span class="dado-arquivo dado-advogado"> 
								<?php if( !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getAdvogado()) ){
										echo 'Advogado do arquivo: <strong>' . $entrada->getAdvogado() . '</strong>';
										
										if( !DataValidator::isEmpty($entrada->getQtdHomonimo()) )
									  		echo '<br>Existem <strong>' . $entrada->getQtdHomonimo() . '</strong> Advogados com o mesmo nome';				
									  }									  				  	
									  else
										echo 'Advogado não encontrado no arquivo'; ?> 

								<?php if( !DataValidator::isEmpty($responsabilidades) && 
										  isset($responsabilidades[2]) && $responsabilidades[2]['acao'] == 'E' && 
										  DataValidator::isEmpty($processo->getAdvogado()) ) { 
								?>
									<br>
									<br>
								 	<a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox" data-rel="box-add-advogado">Cadastrar</a>
								<?php }//nivel aesso ?>

								</span>
								<?php }//se processo sem advogado ?>

								<input type="text" value="<?php echo !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getNome() : ''; ?>" placeholder="Digite o nome do Advogado para a seleção no Banco de Dados" class="std-input advogado-field">	

								</div>

								<div class="clear">&nbsp;</div>

								<div class="campo-box">
				                                
									<?php 
									if( !DataValidator::isEmpty($processo->getAdvogado()) && !DataValidator::isEmpty($processo->getAdvogado()->getEmails())) { 
										$emails_advogado = $processo->getAdvogado()->getEmails();
										$mail_adv = $emails_advogado[0];
									}

									if( !DataValidator::isEmpty($processo->getAdvogado()) ){
									?>
                                    
                                    <label for="">E-mail do Advogado</label>

									<input type="text" readonly value="<?php echo isset($mail_adv) && !DataValidator::isEmpty($mail_adv) ? $mail_adv->getEmailEndereco() : ''; ?>" placeholder="Email do advogado" class="std-input advogado-field">							
									<?php }//se processo tem advogado ?>

								</div>	
								<!-- email advogado -->

								<input type="hidden" id="advogado-id" name="advogado_id" value="<?php echo !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">

						</div>
						<!-- campo -->

						<div class="campo-box">

							<div class="area-label">							
								<label 
                                <?php if( !DataValidator::isEmpty($processo->getSecretaria()) ){ ?>
                                for="" class="fl tooltip-link detalhe-secretaria detalhe" 
								data-title="Clique aqui para ir à área de cadastro"
								data-x="30" data-y="30" data-direction="left"
								data-id="<?php echo $processo->getSecretaria()->getId(); ?>"
                                <?php } ?>
                                >Secretaria/Fórum</label>
							</div>

							<div class="area-campo">						
								<span class="dado-arquivo dado-secretaria">

<?php 								if( !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria()) ) { 
 									echo 'Secretaria/Fórum do arquivo: <strong>' . $entrada->getSecretaria();
									if(!DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria())) { 
										echo ' -- ' . $processo->getSecretaria()->getEstado();
									}
									echo '</strong>';
								}
								else {
									echo '<strong>Secretaria/Fórum não encontrado no arquivo</strong>'; 
								}
?>
									<br>
									<br>
									<!-- btn cadastrar -->

									<?php 
									if( !DataValidator::isEmpty($responsabilidades) && 
											  isset($responsabilidades[2]) && 
											  $responsabilidades[2]['acao'] == 'E' && 
											  (DataValidator::isEmpty($processo->getSecretaria()) OR ($processo->getSecretaria()->getEstado() != $entrada->getEstado())) ) { 
									?>							
									<a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox" data-rel="box-add-secretaria">Cadastrar</a>
									<?php }//nivel acesso ?>
									<!-- //btn cadastrar -->

								</span>
								
								<!-- select -->
								<select name="secr_id" class="sel-carrega-jornal" id="sel-secretaria">
									<option value="0">Selecione</option>
<?php 
									$secretarias = SecretariaModel::listaByEstado( !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : null );
									if( !DataValidator::isEmpty($secretarias) ){
										$combosel = '';
										foreach($secretarias as $sec){
 											if( !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria()) && DataValidator::isEmpty($processo->getSecretaria()) )
												$combosel = (trim($entrada->getSecretaria()) == trim($sec->getNome())) ? "selected" : "";

 											if(empty($combosel) && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) )  
												$combosel = ($processo->getSecretaria()->getId() == $sec->getId()) ? "selected" : "";	
?>
											<option value="<?php echo $sec->getId(); ?>" <?php echo $combosel; ?>><?php echo $sec->getNome(); ?></option>
<?php 
											$combosel = '';
										}
									} ?>
								</select>
								<!-- //select -->
                                
							</div>

						</div>
						<!-- campo -->

						<div class="clear">&nbsp;</div>

						<div class="campo-box wrapper-campo-jornal" <?php echo !DataValidator::isEmpty($processo->getSecretaria()) && !DataValidator::isEmpty($processo->getSecretaria()->getId()) ? 'style="display:block; "' : 'style="display:none; "'; ?>>

							<div class="area-label">                            		
							<label 
							<?php if( !DataValidator::isEmpty($processo->getJornal()) ){?>
							for="" class="fl tooltip-link detalhe-jornal detalhe"
							data-title="Clique aqui para ir à área de cadastro"
							data-x="30" data-y="30" data-direction="left"
							data-id="<?php echo $processo->getJornal()->getId(); ?>"
							<?php } ?>
							>Jornal
							</label>
							</div>

							<div class="area-campo">	                            
				
								<span class="dado-arquivo dado-jornal"> 
                                
								 <?php if( !DataValidator::isEmpty($responsabilidades) && 
										   isset($responsabilidades[2]) && $responsabilidades[2]['acao'] == 'E' ) { 
								 ?>
								<a href="#" target="_blank" style="margin-top: 5px;" 
									class="std-btn btn sm-btn clear link-lightbox btn-lightbox-jornal" 
									data-rel="box-add-jornal">Cadastrar</a>

								<a href="#" target="_blank" style="margin-top: 5px;" 
								class="std-btn btn sm-btn clear link-lightbox" 
								data-rel="box-add-jornal-cidade">Buscar</a>
								</span>
								
                                <br class="clear"> 
								<?php }//nivel acesso ?>
								                               
								<select name="jornal_id" class="" id="sel-jornal" style="margin-top: 10px;">
									<option value="0" selected>Selecione</option>
									<?php 
									$jornais = JornalModel::listaBySecretaria( !DataValidator::isEmpty($processo->getSecretaria()) ? $processo->getSecretaria()->getId() : 0 );
									if( !DataValidator::isEmpty($jornais) ){
									foreach($jornais as $journal){
									?>
									<option value="<?php echo $journal->getId(); ?>" <?php echo !DataValidator::isEmpty($processo->getJornal()) && $processo->getJornal()->getId() == $journal->getId() ? 'selected' : ''; ?>><?php echo $journal->getNome(); ?> - <?php echo $journal->getStatusDesc(); ?> <?php echo (!DataValidator::isEmpty($journal->getDataConfirmacao()) ? ' - ' . date('d/m/Y', strtotime($journal->getDataConfirmacao() )) : ' - sem Data de Confirmação'); ?></option>
									<?php }} ?>									
								</select>  

							</div>

						</div>
						<!-- campo -->

						<div class="clear">
						&nbsp;
						</div>

						<div class="campo-box">
							<label for="" class="label-textarea">Conteúdo</label>
							<textarea name="conteudo" rows="8" readonly class="std-input"><?php echo !DataValidator::isEmpty($entrada) ? $entrada->getConteudo() : null; ?></textarea>
						</div>
						<!-- campo -->	
                                                
            
						<?php if( !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getTermoInicio()) ){ ?>
            			<div class="campo-box">
							<label for="">Termo Inicio</label>
							<input type="text" value="<?php echo $entrada->getTermoInicio(); ?>" class="std-input">
						</div>
						<!-- campo -->
                        
            			<div class="campo-box">
							<label for="">Termo Fim</label>
							<input type="text" value="<?php echo $entrada->getTermoFim(); ?>" class="std-input">
						</div>
						<!-- campo -->
                        
           				<div class="campo-box">
							<label for="">Intervalo entre os termos</label>
							<input type="text" value="<?php echo $entrada->getIntervalo(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<?php }	//termos ?>

						<div class="campo-box">
							<div class="panel panel-accordion panel-default-bg" style="display:block;">
								
								<?php if(!DataValidator::isEmpty($responsabilidades) && 
										 isset($responsabilidades[2]) && 
										 $responsabilidades[2]['acao'] == 'E'){ 
								?>
                                <div class="panel-title obs-title">
								Observações&nbsp;&nbsp; <a href="#" title="Adicionar Observação" class="std-btn sm-btn add-obs">Adicionar</a>
								<i class="seta seta-baixo"></i>
								</div>
                                <?php }//nivel de acesso ?>
								
								<div class="panel-content panel-obs" style="display:block">
                                
                                <?php
								if( !DataValidator::isEmpty( $processo->getObservacoes() ) ){
									foreach( $processo->getObservacoes() as $obs ){	
								?>
								
									<div class="box box-obs" id="box1"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong><?php echo date('d/m/Y', strtotime($obs->getDataEntrada()) ); ?></strong>
										<br>
										Usuário: <strong><?php echo $obs->getRespCadastro(); ?></strong>
										</span>

										<br class="clear">
										<br>

										<?php 
										if(!DataValidator::isEmpty($responsabilidades) && 
											isset($responsabilidades[2]) && 
											$responsabilidades[2]['acao'] == 'E' &&
											isset($usuario_logado) && 
											!DataValidator::isEmpty($usuario_logado) && 
											$usuario_logado->getId() == $obs->getUsuarioCadastroId()
											){ 
										?>
                                        <a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="processo-obs">Excluir</a>
                                        <?php }//nivel de acesso ?>
                                        
										</label>

										<textarea name="observacao[]" rows="5" readonly class="std-input adv-obs"><?php echo $obs->getMensagem(); ?></textarea>
                                        <input type="hidden" name="obs_id[]" value="<?php echo $obs->getId(); ?>">

									</div>
									<!-- obs gravada -->
									<?php }}//observações ?>								
									
								
                                <?php
								if( !DataValidator::isEmpty( $processo->getObservacoesAdvogado() ) ){
									foreach( $processo->getObservacoesAdvogado() as $obs ){	
								?>
								
									<div class="box box-obs" id="box1"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong><?php echo date('d/m/Y', strtotime($obs->getDataEntrada()) ); ?></strong>
										<br>
										Usuário: <strong><?php echo $obs->getRespCadastro(); ?></strong>
										</span>

										<br class="clear">
										<br>

										</label>

										<textarea name="observacao_advogado[]" rows="5" readonly class="std-input adv-obs"><?php echo $obs->getMensagem(); ?></textarea>
                                    
									</div>
									<!-- obs gravada -->
									<?php }}//observações ?>								
									
								</div>
								<!-- panel content -->

							</div>
							<!-- panel obs -->
						
						</div>
						<!-- campo -->
                        					
			<div class="controles clearfix">
            
            	<div class="fl">
                	<a href="#" title="Voltar" class="std-btn btn-pesquisa">Voltar</a>
                    
             	<?php 
					if( $usuario_logado->getPerfil()->getId() == 1 && !DataValidator::isEmpty($responsabilidades) && 
						isset($responsabilidades[2]) && 
						$responsabilidades[2]['acao'] == 'E' ){			
				?>
                    
                        <a href="#" id="exclui-item-btn" title="Excluir" class="std-btn black-btn del-item" data-del-message="Tem certeza que deseja excluir este Processo?">Excluir</a>
                <?php }//nivel acesso ?>
                
                </div>                    
                
                <div class="fr">
                
             	<?php
					if( !DataValidator::isEmpty($responsabilidades) && 
						isset($responsabilidades[2]) && 
						$responsabilidades[2]['acao'] == 'E' 
						){	
					
						if( $processo->getSinalizador() == 'A' ){ 
			  	?>
							<a href="#" data-acao="enviaProposta" title="Enviar para Proposta" class="muda-sinalizacao-btn std-btn" data-msg="Tem certeza que deseja enviar para Proposta?">Enviar para Proposta</a>
                            
             		<?php } //enviar para proposta
								
					elseif( $processo->getSinalizador() == 'M' ){ 
				
				?>
				<a href="#" data-acao="alteraSinalizador" title="Enviar para Processo" class="muda-sinalizacao-btn std-btn" data-msg="Tem certeza que deseja alterar o Sinalizador para Amarelo?">Alterar Sinalizador para Amarelo</a>
				<?php } //enviar para Amarelo ?>
							
				<input type="submit" value="Salvar" class="std-btn send-btn">
                
                <?php }//nivel de acesso ?>	
                         
			</div>     

			</div>
			<!-- controles -->

            </form>
            <!-- form -->

			</div>

	</div><!-- direita -->

	</div>

</div>



<!-- start: lightbox add secretaria -->
<div class="lightbox" id="box-add-secretaria">

	<div class="header">Cadastrar Secretaria/Fórum</div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">
	
	<form action="" class="std-form form-lightbox" id="form-secretaria-lightbox">

		<div class="warning-box clear" style="display: none; margin-bottom: 20px;">
			<span class="warning erro"></span>
		</div>

		<div class="campo-box">
			<label>Estado</label>
			<select name="estado_secretaria" disabled id="">
				<?php
                                $estados = array("DF"=>"DF", "MS"=>"MS", "MT"=>"MT" ,"RJ"=>"RJ", "SE"=>"SE", "SP"=>"SP" );
				foreach( $estados as $key=>$value ){
				?>			
					<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($entrada) && $entrada->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
				<?php
				}
				?>
			</select>
            <input type="hidden" name="estado_lightbox" value="<?php echo !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : ''; ?>">
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Secretaria/Fórum</label>
			<input type="text" name="nome_secretaria" style="width:67%" value="<?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria()) ? $entrada->getSecretaria() : ''; ?>" class="std-input md-input">
			<!-- <span class="std-btn sm-btn check-btn check-secretaria">CHECAR</span> -->
		</div>
		<!-- campo -->

		<div class="controles clearfix">
			<a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
			<input type="button" value="Cadastrar" class="std-btn send-btn fr" id="btn-cadastra-secretaria">	
		</div>
		<!-- controles -->
									
	</form>
	<!-- form -->

	</div><!-- content -->

</div>
<!-- end: lightbox add secretaria -->

<!-- start: lightbox add advogado -->
<div class="lightbox" id="box-add-advogado">

	<div class="header">Cadastrar Advogado</div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">
	
	<form action="" class="std-form form-lightbox" id="form-advogado-lightbox">

		<div class="warning-box clear" style="display: none; margin-bottom: 20px;">
			<span class="warning erro"></span>
		</div>

		<div class="campo-box clear">
			<label>Nome</label>
			<input type="text" name="nome_advogado" value="<?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getAdvogado()) ? $entrada->getAdvogado() : ''; ?>" class="std-input md-input">
			<!-- <span class="std-btn sm-btn check-btn check-advogado">CHECAR</span> -->
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>OAB</label>
			<input type="text" name="oab_advogado" class="std-input md-input">
		</div>
		<!-- campo -->
        
                <div class="<?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ? 'stack-box' : ''; ?>">
			<div class="campo-box email-box">
                            <label>Email</label>
                            <input type="text" name="email_advogado[]" class="std-input md-input email-advogado" style="width:247px;">
                            <input type="checkbox" name="tick_advogado[]" class="check-email">
                            <a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-email-lightbox" style="margin-right:6px;">Adicionar</a>
                        </div>
		</div>
		<!-- campo -->

                <div class="<?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E' ? 'stack-box' : ''; ?>">
			<div class="campo-box email-box">
                            <label>DDD/Telefone</label>
                            <input type="text" name="ddd_tel_advogado[]" class="std-input md-input" style="width:30px;">
                            <input type="text" name="telefone_advogado[]" class="std-input md-input telefone-advogado" style="width:200px">
                            <a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-tel-lightbox" style="margin-right:6px;">Adicionar</a>
                        </div>
		</div>
		<!-- campo -->

		<div class="controles clearfix">
			<a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
			<input type="button" value="Cadastrar" class="std-btn send-btn fr" id="btn-cadastra-advogado">	
		</div>
		<!-- controles -->
									
	</form>
	<!-- form -->

	</div><!-- content -->

</div>
<!-- end: lightbox add advogado -->

<!-- start: lightbox add jornal -->
<div class="lightbox" id="box-add-jornal">

	<div class="header">Cadastrar Jornal</div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">
	
	<form action="" class="std-form form-lightbox" id="form-jornal-lightbox">
    	<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
		<div class="warning-box clear" style="display: none; margin-bottom: 20px;">
			<span class="warning erro"></span>
		</div>

		<div class="campo-box">
			<label>Status</label>
			<select name="status_jornal" id="sel-status-jornal" class="sm-input">
				<option value="0">Selecione</option>
				<option value="P">Padrão</option>
				<option value="C">Comum</option>
			</select>
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Nome do Jornal</label>
			<input type="text" name="nome_jornal" value="" class="std-input md-input">
			<!-- <span class="std-btn sm-btn check-btn check-jornal">CHECAR</span> -->
		</div>
		<!-- campo -->
        
    	<!--<div class="campo-box clear">
			<label>Cidade</label>
			<input type="text" name="cidade_jornal" class="std-input sm-input">
		</div>-->
		<!-- campo -->

		<div class="campo-box clear">
			<label>Secretaria/Fórum</label>
			<input name="secretaria_id" type="hidden" value="" id="hidden-secretaria-jornal">
			<select id="sel-secretaria-jornal" disabled="disabled">
			<?php 
			$secretarias = SecretariaModel::listaByEstado( !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : null );
			if( !DataValidator::isEmpty($secretarias) ){
				foreach($secretarias as $sec){
			?>
			<option value="<?php echo $sec->getId(); ?>"><?php echo $sec->getNome(); ?></option>
			<?php }} ?>
			</select>
		</div>
		<!-- campo -->

		<div class="controles clearfix">
			<a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
			<input type="button" value="Cadastrar" class="std-btn send-btn fr" id="btn-cadastra-jornal">				
		</div>
		<!-- controles -->
									
	</form>
	<!-- form -->

	</div><!-- content -->

</div>
<!-- end: lightbox add jornal -->

<!-- start: lightbox add jornal por cidade -->
<div class="lightbox" id="box-add-jornal-cidade">

	<div class="header">Buscar Jornal por Cidade</div><!-- header -->

	<div class="close-box close-lightbox">
	<span class="text">Fechar &nbsp; </span> <span class="close" title="Fechar"></span>
	</div>
	<!-- //close btn -->

	<div class="content">
	
		<form action="" class="std-form form-lightbox" id="">
		
		<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
		<input type="hidden" class="jornal-id" value="">
		<input type="hidden" class="jornal-nome" value="">

		<div class="warning-box clear" style="display: none; margin-bottom: 20px;">
		<span class="warning erro"></span>
		</div>
		<!-- warning -->

		<div class="campo-box">
		<label>Cidade</label>
		<input type="text" class="std-input md-input cidade-field" 
		placeholder="Digite o nome da Cidade para a seleção no Banco de Dados">
		</div>
		<!-- campo -->	

		<div class="controles clearfix">
			<a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
			<input type="button" value="Adicionar" class="std-btn send-btn fr" id="btn-adiciona-jornal">				
		</div>
		<!-- controles -->

		</form>
		<!-- form -->	

	</div>
	<!-- content -->

</div>
<!-- end: lightbox add jornal por cidade -->

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>
	
<script>
	App.processos();
	
	$('.btn-pesquisa').bind('click', function() { 
		$('#form-pesquisa').submit();
	});	
	
	$('.detalhe-advogado').bind('click', function(e){
		e.preventDefault();
		var advogado_id = $(this).attr('data-id');						
		$('#adv-id').val(advogado_id);	
		$('#form-detalhe-adv').submit();
	});
	
	$('.detalhe-secretaria').bind('click', function(){
		var secretaria_id = $(this).attr('data-id');						
		$('#sec-id').val(secretaria_id);	
		$('#form-detalhe-sec').submit();
	});
	
	$('.detalhe-jornal').bind('click', function(){
		var jornal_id = $(this).attr('data-id');						
		$('#jornal-id').val(jornal_id);	
		$('#form-detalhe-jornal').submit();
	});

</script>

</body>
</html>
