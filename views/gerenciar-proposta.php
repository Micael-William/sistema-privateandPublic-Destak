<?php
	require_once("valida-sessao.php");
	require_once("controllers/PropostaController.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$tem_custo_dje = isset($params['tem_custo_dje']) ? $params['tem_custo_dje'] : 'nao'; 
	$proposta = isset($params['proposta']) ? $params['proposta'] : new Proposta();
	
	$processo = $proposta->getProcesso();
	if( isset($processo) )
		$entrada = $processo->getEntrada();		
		
	$_SESSION[PropostaController::PROP_KEY] = mt_rand(1, 1000);
	
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
            
            	<?php 
				$sinal = null; $classe = null;
				if( !DataValidator::isEmpty($processo) && $processo->getSinalizador() == 'V' ){
					$sinal = 'Verde'; $classe = 'verde';
				}
				elseif( !DataValidator::isEmpty($processo) && $processo->getSinalizador() == 'A' ){
					$sinal = 'Amarelo'; $classe = 'amarelo';
				}
				?>     
			
				<h1 class="std-title title title-<?php echo $classe; ?> title-w-btn">
				<span class="txt-14 txt-normal">Proposta</span> <span class="seta"> &gt; </span> <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getNumero()) ? $entrada->getNumero() : 'Cadastro'; ?>
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

				<?php 
				if( !DataValidator::isEmpty($proposta->getId()) ){
				?>
                                <div class="alert-panel panel-2">
				
					<div class="alert-box">
						<span class="text-lg"><?php echo $proposta->getStatusDesc(); ?></span>
						<span class="text-sm">Status</span>
					</div>					

					<div class="alert-box">
						<span class="text-lg"><?php echo $sinal; ?></span>
						<span class="text-sm">Sinalizador</span>
					</div>

				</div>
				<!-- alert panel -->	
                <?php } ?>
                
                <form method="post" id="form-pesquisa">
                    <input type="hidden" name="controle" value="Proposta">
                    <input type="hidden" name="acao" value="busca">
                    <input type="hidden" name="origem" value="proposta">
                </form>
                <!--form pesquisa-->

				<form action="" id="form-exclusao" class="clear" method="post">
					<input type="hidden" name="controle" value="Proposta">
					<input type="hidden" name="acao" value="exclui">
					<input type="hidden" name="proposta_id" value="<?php echo !DataValidator::isEmpty($proposta) ? $proposta->getId() : 0; ?>">
				</form>
                <!--form exclusão-->

				<form class="std-form form-proposta clear" method="post">
					
					<input type="hidden" name="controle" value="Proposta">
					<input type="hidden" name="acao" value="salva">
					<input type="hidden" name="proposta_id" value="<?php echo $proposta->getId(); ?>">
					<input type="hidden" name="processo_id" value="<?php echo !DataValidator::isEmpty($processo) ? $processo->getId() : 0; ?>">
					<input type="hidden" name="estado_proposta" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : ''; ?>">
					<input type="hidden" name="adv_id_aux" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">   
					<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">
					<input type="hidden" class="status-aux" name="status-aux" value="<?php echo !DataValidator::isEmpty($proposta->getStatus()) ? $proposta->getStatus() : ''; ?>">
					<input type="hidden" name="key" value="<?php echo $_SESSION[PropostaController::PROP_KEY]; ?>" />
					<input type="hidden" name="jornal-id-aux" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? $processo->getJornal()->getId() : 0; ?>">
					<input type="hidden" name="sec-id-aux" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) ? $processo->getSecretaria()->getId() : 0; ?>">

					<?php 
					if( !DataValidator::isEmpty($processo) && !DataValidator::isEmpty( $processo->getAlertas() ) ) { 
					?>
					
					<div class="panel panel-warning bordered-1 panel-accordion">
					<div class="panel-title">Quadro de Alertas <i class="seta seta-baixo"></i> </div>

					<div class="panel-content">
                                            <ul class="list-group list-zebra">
                                            <?php 								
						foreach( $processo->getAlertas() as $alerta ){
                                            ?>
                                                    <li class="list-group-item list-group-item-bold"><?php echo $alerta; ?></li>
                                            <?php 
                                                }
                                            ?>                                
                                            </ul>
					</div> 

					</div>
                                        <br>
					<!-- quadro de alertas -->
                                        <?php 
                                        }//alertas 
                                        ?>
                    			
			
                                <?php
                                
                                if( (!DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getRepetidos()))) {																				
                                    $processos_repetidos = $processo->getRepetidos();

                                ?>
						<div class="panel panel-info bordered-1 panel-accordion">
						<div class="panel-title">Notificações <i class="seta seta-baixo"></i> </div>

						<div class="panel-content">
						<ul class="list-group list-zebra">
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

					</div><br>
						<!-- quadro de alertas -->
                                <?php 
                                }
                                ?>
                                
					<div class="panel panel-accordion">
						
						<div class="panel-title">Dados Gerais <i class="seta seta-baixo"></i> </div>

						<div class="panel-content" style="display: block;">
						<br>

						<div class="campo-box">
							
							<label>Status</label>
							
							<select name="status" disabled class="sel-status">
								<option value="N" <?php echo $proposta->getStatus() == 'N' ? 'selected' : ''; ?>>Nova</option>
								<option value="E" <?php echo $proposta->getStatus() == 'E' ? 'selected' : ''; ?>>Enviada</option>
								<option value="R" <?php echo $proposta->getStatus() == 'R' ? 'selected' : ''; ?>>Rejeitada</option>
								<option value="A" <?php echo $proposta->getStatus() == 'A' ? 'selected' : ''; ?>>Aceite</option>
							</select>

						</div>

						<div class="campo-box">

							<label>Estado*</label>
							
							<select name="estado_processo" id="estado_processo" <?php echo !DataValidator::isEmpty($proposta->getId()) ? 'disabled' : ''; ?> class="sel-estado">
								<?php
                                                                $estados = EstadosEnum::getChavesUFs('Selecione');
								//$estados = array("0" => "Selecione", "DF"=>"DF", "RJ"=>"RJ", "SP"=>"SP" );			
								foreach( $estados as $key=>$value ){
								?>			
									<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && $entrada->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
								<?php
								}
								?>
							</select>

						</div>
						<!-- campo -->                        
                       						                                           
                                                <div class="campo-box">              
						<label for="">Data do Processo*</label>
						<input type="text" name="data_processo" value="<?php 
						if( !DataValidator::isEmpty($processo) && 
							!DataValidator::isEmpty($entrada) && 
							!DataValidator::isEmpty($entrada->getDataProcesso()) && 
							!DataValidator::isEmpty($proposta->getId())
							) 
								echo date('d/m/Y', strtotime($entrada->getDataProcesso())); 
						elseif(isset($_POST['data_processo']) && !DataValidator::isEmpty($_POST['data_processo']))
								echo $_POST['data_processo'];							
							?>"
						class="std-input date-input" <?php echo !DataValidator::isEmpty($proposta->getId()) ? 'readonly' : ''; ?>>
						</div>

						<div class="campo-box">						
                                        <?php if( !DataValidator::isEmpty($proposta->getId() ) ){ ?> 
						<label for="">Data Entrada Sistema</label>
						<input type="text" value="<?php echo !DataValidator::isEmpty($processo) ? date('d/m/Y', strtotime($processo->getDataEntrada() )) : date('d/m/Y', strtotime( date("d/m/Y") )); ?>"
						class="std-input date-input" readonly="readonly">   
						
                                        <?php }//edição da proposta ?>                        
                                                </div>
                        
						<div class="campo-box"> 

						<label for="">Data Entrada Proposta</label>
						<input type="text" value="<?php echo !DataValidator::isEmpty($proposta->getDataEntrada()) ? date('d/m/Y', strtotime($proposta->getDataEntrada() )) : date('d/m/Y', strtotime( date("Y-m-d") )); ?>"
						class="std-input date-input" readonly>

						</div>
						<!-- campo -->
                                                
                        <div class="campo-box">
						<?php if( !DataValidator::isEmpty($proposta->getNomeRespEnvio())) { ?>
                        
						<label for="" class="tooltip-link" 
						data-title="<?php echo $proposta->getNomeRespEnvio(); ?>" 
						data-x="30" data-y="30" 
						data-direction="left">Data de Envio</label>
						<input type="text" value="<?php echo date('d/m/Y', strtotime($proposta->getDataEnvio() )); ?>"
						class="std-input date-input" readonly="readonly">
                        
						<?php }//se enviada ?>
                        
                        </div>
                        
                        <div class="campo-box">
						<?php if( !DataValidator::isEmpty($proposta->getNomeRespAceite()) ){ ?>
                        
						<label for="" class="tooltip-link" 
						data-title="<?php echo $proposta->getNomeRespAceite(); ?>" 
						data-x="30" data-y="30" data-direction="left">Data de Aceite</label>
						<input type="text" value="<?php echo date('d/m/Y', strtotime($proposta->getDataAceite() )); ?>"
						class="std-input date-input" readonly="readonly">
                        
						<?php }//se aceita ?>					

						</div>
						<!-- campo -->
                        
                        <div class="campo-box">
						<?php if( !DataValidator::isEmpty($proposta->getNomeRespRejeicao()) ){ ?>
                        
						<label for="" class="tooltip-link" 
						data-title="<?php echo $proposta->getNomeRespRejeicao(); ?>" 
						data-x="30" data-y="30" data-direction="left">Data de Rejeição</label>
						<input type="text" value="<?php echo date('d/m/Y', strtotime($proposta->getDataRejeicao() )); ?>"
						class="std-input date-input" readonly="readonly">
                        
						<?php }//se rejeitada ?>					

						</div>
						<!-- campo -->

						<div class="campo-box">

							<label for="">Nº do Processo*</label>
							<input type="text" name="num_processo" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getNumero()) ? $entrada->getNumero() : ''; ?>" <?php echo !DataValidator::isEmpty($proposta->getId()) ? 'readonly' : ''; ?>
							class="std-input">

						</div>

						<div class="campo-box">
							
							<label for="">Código Interno</label>
							<input type="text" value="<?php echo !DataValidator::isEmpty($proposta->getId()) ? $proposta->getId() : ''; ?>" 
							class="std-input" readonly="readonly">

						</div>
						<!-- campo -->
                        
                         <?php //if( !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && $entrada->getEstado() == 'SP' ){ ?>
                                                <div class="campo-box">
							<label for="">Ação</label>
							<input type="text" name="processo_acao" value="<?php echo !DataValidator::isEmpty($processo) ? $processo->getAcao() : ''; ?>" class="std-input">
						</div>
						<!-- campo -->
                        <?php //} ?>

						<div class="campo-box">
							<label>Requerente</label>
							<input type="text" name="requerente" 
							value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getRequerente()) ? $processo->getRequerente() : ''; ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Requerido</label>
							<input type="text" name="requerido" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getRequerido()) ? $processo->getRequerido(): ''; ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">

							<div class="area-label">							
								<label for="" class="fl">Secretaria/Fórum</label>
							</div>

							<div class="area-campo">
                                                            
                                                                <!-- label -->
<?php 
								if( DataValidator::isEmpty($proposta->getId()) ){
?>
								<a href="#" target="_blank" class="std-btn add-secretaria add-btn clear link-lightbox" <?php echo isset($_POST['estado_processo']) && !DataValidator::isEmpty($_POST['estado_processo']) ? 'style="display:inline-block; margin-bottom:15px;"' : 'style="display:none; margin-bottom:15px;"'; ?> data-rel="box-add-secretaria">Cadastrar</a>
<?php	
								}//inserção								
								
								else{
								
								if( !DataValidator::isEmpty($processo) && DataValidator::isEmpty($processo->getSecretaria()) ) { 								
?>
                                                                    <span class="dado-arquivo dado-secretaria"> <?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria()) ? 'Secretaria/Fórum do arquivo: <strong>' . $entrada->getSecretaria() . '</strong>' : 'Secretaria/Fórum não encontrado no arquivo'; ?> </span>

                                                                        <!-- btn cadastrar -->
<?php 
                                                                        if( !DataValidator::isEmpty($responsabilidades) && 
										  isset($responsabilidades[3]) && 
										  $responsabilidades[3]['acao'] == 'E'										  
                                                                            ) { 
?>	
                                                                            <a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox" data-rel="box-add-secretaria">Cadastrar</a>
                                                                            <br class="clear">
                                                                            <br>
<?php 
                                                                        }//nivel acesso 
?>
                                        				<!-- //btn cadastrar -->
<?php                                                               }//processo sem secretaria 
                                                                }//edição
?>                            		                       
                                                            <!-- //label -->	                           							
								
								<!-- select -->
								<select name="secr_id" <?php echo $proposta->getStatus() == 'A' ? 'disabled' : ''; ?> class="sel-full sel-carrega-jornal" id="sel-secretaria">
									<option value="0">Selecione</option>
									<?php 
									$secretarias = SecretariaModel::listaByEstado( !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : '' );
									if( !DataValidator::isEmpty($secretarias) ){
										foreach($secretarias as $sec){
									?>
									<option value="<?php echo $sec->getId(); ?>" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) && $processo->getSecretaria()->getId() == $sec->getId() ? 'selected' : null; ?>><?php echo $sec->getNome(); ?></option>
									<?php }} ?>
								</select>	
								<!-- //select -->							
								
                                
							</div>

						</div>
						<!-- campo -->
<?php 
                                                if( DataValidator::isEmpty($proposta->getId()) ){
?>
                                                <div class="campo-box">
                                                    <label></label> 
                                                    <input type="text" name="secretaria" value="<?php if( !DataValidator::isEmpty($proposta->getId()) && 
														!DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria())) 
                                                                                                                    echo $processo->getSecretaria()->getNome(); 
														elseif( isset($_POST['secretaria']))
														echo $_POST['secretaria'];	 
								?>" class="std-input secretaria-field-2" placeholder="Selecione o estado ou Digite o nome da Secretaria para busca">
						</div>
						<!-- campo -->
<?php 
                                                }
?>    
						<div class="clear">&nbsp;</div>

						<?php if( !DataValidator::isEmpty($proposta->getId()) && 
								  !DataValidator::isEmpty($processo) && 
								  !DataValidator::isEmpty($entrada) && 
								  !DataValidator::isEmpty($entrada->getConteudo()) ){ 
						?>
						<div class="campo-box">
							<label class="label-textarea">Conteúdo</label>
							<textarea name="conteudo" readonly rows="8" class="std-input"><?php echo $proposta->getProcesso()->getEntrada()->getConteudo(); ?></textarea>
						</div>
						<!-- campo -->
<?php                                           }//se entrada com conteudo ?>

						</div>
						<!-- panel content -->

					</div>
					<!-- panel dados gerais -->

					<br>

					<div class="panel panel-accordion">
					
					<div class="panel-title">Advogado <i class="seta seta-baixo"></i> </div>

					<div class="panel-content" style="display: block;">

						<!-- se não houver advogado -->
						<div class="campo-box">

							<div class="area-label">							
								<label for="" class="fl">Advogado</label>
							</div>
							<div class="area-campo">
<?php 
                                                            if( DataValidator::isEmpty($proposta->getId()) ){
?>
    								<span class="dado-arquivo dado-advogado">
									<a href="#" target="_blank" class="std-btn btn sm-btn clear link-lightbox" data-rel="box-add-advogado" style="margin: 10px 0;">Cadastrar</a>
								</span>
<?php	
                                                            }//inserção								
								
                                                            else{
								
								if( !DataValidator::isEmpty($processo) && DataValidator::isEmpty($processo->getAdvogado()) ) { 
?>
                                                                    <span class="dado-arquivo dado-advogado"> <?php echo !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getAdvogado()) ? 'Advogado do arquivo: <strong>' . $entrada->getAdvogado() . '</strong>' : 'Advogado não encontrado no arquivo'; ?> 
<?php 
                                                                    if( !DataValidator::isEmpty($responsabilidades) && 
                                                                        isset($responsabilidades[3]) && 
									$responsabilidades[3]['acao'] == 'E'
									) { 
?>   
                                                                        <br>    
                                                                        <a href="#" target="_blank" class="std-btn btn sm-btn clear link-lightbox" data-rel="box-add-advogado" style="margin: 10px 0;">Cadastrar</a>
<?php
                                                                    }//nivel acesso 
?>
                                                                    </span>
<?php
                                                                }//processo sem advogado 
                                                            }//edição
?>
                                                            <input type="text" name="advogado" <?php echo $proposta->getStatus() == 'A' ? 'readonly' : ''; ?> value="<?php if( !DataValidator::isEmpty($proposta->getId()) && 
																					!DataValidator::isEmpty($processo) && 
																					!DataValidator::isEmpty($processo->getAdvogado())) 
																						echo $processo->getAdvogado()->getNome(); 
																				elseif( isset($_POST['advogado']))
																					echo $_POST['advogado'];	 
																		 ?>" class="std-input advogado-field" placeholder="Digite o nome do Advogado para a seleção no Banco de Dados">
							  
                                                            <input type="hidden" id="advogado-id" name="advogado_id" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">

							</div>

						</div>
						<!-- campo -->
                        
                        <br class="clear"><br>
                        
                        <label>Status</label>
							<input type="text" readonly value="<?php echo !DataValidator::isEmpty($proposta->getId()) && 
																		  !DataValidator::isEmpty($processo) && 
																		  !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getStatusDesc() : ''; ?>" class="std-input">

						<br class="clear"><br>
					
						<?php if( !DataValidator::isEmpty($processo) && 
								  !DataValidator::isEmpty($processo->getAdvogado()) &&
								  !DataValidator::isEmpty($processo->getAdvogado()->getEmails()) )
								{
									foreach( $processo->getAdvogado()->getEmails() as $email ){
						?> 

						<div class="campo-box">
							<label>E-mail para envio</label>
							<input type="text" readonly value="<?php echo $email->getEmailEndereco(); ?>" class="std-input">
						</div>
						<!-- campo -->
						<?php }} ?>	
						
						<?php if( !DataValidator::isEmpty($processo) && 
								  !DataValidator::isEmpty($processo->getAdvogado()) &&
								  !DataValidator::isEmpty($processo->getAdvogado()->getTelefones()) ){
									foreach( $processo->getAdvogado()->getTelefones() as $tel ){
						?> 
						<div class="campo-box">
							<label>DDD/Telefone</label>
							<input type="text" readonly value="<?php echo $tel->getDdd(); ?>" class="std-input ddd-input">
							<input type="text" readonly value="<?php echo $tel->getNumero(); ?>" class="std-input tel-input">
						</div>
						<!-- campo -->
						<?php }} ?>	

						<?php if( !DataValidator::isEmpty($proposta->getId()) &&
								  !DataValidator::isEmpty($processo) && 
								  !DataValidator::isEmpty($processo->getAdvogado()) &&
								  !DataValidator::isEmpty($processo->getAdvogado()->getId()) ){ 
						?>
						<div class="campo-box">
							<label>Nome do Contato</label>
							<input type="text" readonly value="<?php echo $processo->getAdvogado()->getNomeContato(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>E-mail do Contato</label>
							<input type="text" readonly value="<?php echo $processo->getAdvogado()->getEmailContato(); ?>" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box clearfix">

							<div class="panel panel-accordion panel-default-bg" style="display:block;">
								
								<?php if(!DataValidator::isEmpty($responsabilidades) && 
										 isset($responsabilidades[3]) && 
										 $responsabilidades[3]['acao'] == 'E'){ 
								?>
                                <div class="panel-title obs-title">
								Observações &nbsp;&nbsp; <a href="#" title="Adicionar Observação" class="std-btn sm-btn add-obs">Adicionar</a>
								<i class="seta seta-frente"></i>
								</div>
                                <?php }//nivel de acesso ?>
								
								<div class="panel-content panel-obs" style="display:block">
                                
                                <?php
								if( !DataValidator::isEmpty( $proposta->getObservacoes() ) ){
									foreach( $proposta->getObservacoes() as $obs ){	
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
											isset($responsabilidades[3]) && 
											$responsabilidades[3]['acao'] == 'E' &&
											isset($usuario_logado) && 
											!DataValidator::isEmpty($usuario_logado) && 
											$usuario_logado->getId() == $obs->getUsuarioCadastroId()
											){ 
										?>
                                        <a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="proposta-obs">Excluir</a>
                                        <?php }//nivel de acesso ?>
                                        
										</label>

										<textarea name="observacao[]" rows="5" readonly class="std-input adv-obs"><?php echo $obs->getMensagem(); ?></textarea>
                                        <input type="hidden" name="obs_id[]" value="<?php echo $obs->getId(); ?>">

									</div>
									<!-- obs gravada -->
									<?php }}//observações ?>								
									
								</div>
								<!-- panel content -->

							</div>
							<!-- panel obs -->		

						</div>
						<!-- campo -->
                        <?php }//processo com advogado ?>
                        
                        

					</div>
					<!-- panel content -->

					</div>
					<!-- panel advogado -->

					<br>

					<div class="panel panel-accordion panel-jornal" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) ? 'style="display:block;"' : 'style="display:none;"'; ?>>
						<div class="panel-title jornal-title"> Jornal 
						&nbsp;&nbsp;
                        
                        <?php 
							$custos_proposta = !DataValidator::isEmpty($proposta->getCustos()) ? $proposta->getCustos() : null;
						?>	                        
						<a href="#" title="Adicionar" class="std-btn sm-btn add-dje" <?php echo isset($tem_custo_dje) && $tem_custo_dje == 'nao' && !isset($custos_proposta['valor_D']) && $proposta->getStatus() != 'A' ? 'style="display:inline-block;"' : 'style="display:none;"'; ?>>Adicionar DJE</a>
                        
						<i class="seta seta-baixo"></i> </div>
						
						<div class="panel-content clearfix" style="display: block;">	

								<div class="campo-box">

									<div class="area-label">							
										<label for="" class="fl">Jornal</label>
									</div>

									<div class="area-campo">	
                            
										<?php //if( !DataValidator::isEmpty($processo) && 
												  //DataValidator::isEmpty($processo->getJornal()) ) { 
										?>						
										<span class="dado-arquivo dado-jornal"> <?php //echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getJornal()) ? 'Jornal do arquivo: <strong>' . $entrada->getJornal() . '</strong>' : 'Jornal não encontrado no arquivo'; ?> 
										<a href="#" target="_blank" class="std-btn btn sm-btn clear link-lightbox btn-lightbox-jornal" data-rel="box-add-jornal">Cadastrar</a>
                                        
                                        <a href="#" target="_blank" style="margin-top: 5px;" 
                                        class="std-btn btn sm-btn clear link-lightbox" 
                                        data-rel="box-add-jornal-cidade">Buscar</a>

									<br class="clear"> 
										 </span>
										<?php //}//processo sem jornal ?>
		                                
										<select name="jornal_id" <?php echo $proposta->getStatus() == 'A' && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'disabled' : ''; ?> class="sel-full" id="sel-jornal">
											<option value="0">Selecione</option>
											<?php 
											$jornais = JornalModel::listaBySecretaria( !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getSecretaria()) ? $processo->getSecretaria()->getId() : 0 );
											
											if( !DataValidator::isEmpty($jornais) ){
												foreach($jornais as $journal){
											?>
											<option value="<?php echo $journal->getId(); ?>" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) && $processo->getJornal()->getId() == $journal->getId() ? 'selected' : ''; ?>>
											<?php echo $journal->getNome(); ?> - <?php echo $journal->getStatusDesc(); ?> <?php echo ' - ' . (!DataValidator::isEmpty($journal->getDataConfirmacao()) ? date('d/m/Y', strtotime($journal->getDataConfirmacao() )) : 'sem Data de Confirmação'); ?></option>
											<?php }} ?>									
										</select>   									

									</div>

								</div>
								<!-- campo -->
							
							<br class="clear">
							<br>                           		
                                                   
							<div class="box-jornal box-wrapper fl" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'style="display:block"' : 'style="display:none"'; ?> >	                   

								<div class="campo-box">
									<label class="label-block">Quantidade</label>
									<input type="text" name="quantidade_padrao" value="<?php if( !DataValidator::isEmpty($custos_proposta) && 
											  isset($custos_proposta['valor_P']) )
												echo $custos_proposta['valor_P']->getQuantidade();
										  elseif(isset($_POST['quantidade_padrao']))
												echo $_POST['quantidade_padrao'];
										else
												echo '2';
									
									?>" class="std-input sm-input number-input qtd-jornal" <?php echo $proposta->getStatus() == 'A' && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'readonly' : ''; ?>>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label class="label-block">Valor Padrão (R$) </label>
									<input type="text" name="valor_padrao" value="<?php if( !DataValidator::isEmpty($custos_proposta) && 
											  isset($custos_proposta['valor_P']) )
											  	echo $custos_proposta['valor_P']->getValorPadrao(); 
										  	  elseif( isset($_POST['valor_padrao']) )
										  		echo $_POST['valor_padrao'];
											  else
												echo '0,00';
									?>" class="std-input sm-input money-input valor-jornal val-padrao" <?php echo $proposta->getStatus() == 'A' && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'readonly' : ''; ?>>
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label class="label-block">Valor final (R$) </label>
									<input type="text" name="valor_final_padrao" readonly value="<?php if( !DataValidator::isEmpty($custos_proposta) && 
											  isset($custos_proposta['valor_P']) )
											  	echo $custos_proposta['valor_P']->getValorFinal();
										elseif( isset($_POST['valor_final_padrao']) )
											echo $_POST['valor_final_padrao'];
										 else
											echo '0,00';
												?>" class="std-input sm-input money-input valor-final-jornal">
								</div>
								<!-- campo -->
                                
                                	<input type="hidden" name="custo_padrao_id" value="<?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_P']) ? $custos_proposta['valor_P']->getId() : 0; ?>">
                                    
                                    <input type="hidden" name="aceite_padrao" class="hidden-aceite" value="<?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_P']) && $custos_proposta['valor_P']->getAceite() == 'A' ? 'A' : ''; ?>">

								<?php if( !DataValidator::isEmpty($proposta->getId()) && 
										  !DataValidator::isEmpty($responsabilidades) && 
										  isset($responsabilidades[3]) && 
										  $responsabilidades[3]['acao'] == 'E' &&										  
										  //$proposta->getStatus() != 'R' &&
										  DataValidator::isEmpty( $processo->getAlertas() )
										  ) { 
								?>
								<a href="#" class="std-btn sm-btn full-width-btn <?php echo $proposta->getStatus() != 'A' ? 'aceite-jornal' : 'jornal-nao-aceito'; ?> <?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_P']) && $custos_proposta['valor_P']->getAceite() == 'A' ? 'green-btn jornal-aceito' : 'black-btn'; ?>">                                
                                 <?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_P']) && $custos_proposta['valor_P']->getAceite() == 'A' ? 'Aceito' : 'Aceite'; ?>
                                </a>
                                <?php }//nivel de acesso ?>

							</div>
							<!-- box jornal	-->                            
             			
                        <div class="box-adicional box-wrapper box-dje fr" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) && 
						( (!DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D'])) || (isset($tem_custo_dje) && $tem_custo_dje == 'sim')) ? 'style="display:block"' : 'style="display:none"'; ?>>	
                        						
                   <div class="form-title"> <span class="fl">DJE</span> 
                    <?php 
					if(!DataValidator::isEmpty($responsabilidades) && 
						isset($responsabilidades[3]) && 
						$responsabilidades[3]['acao'] == 'E' &&
						$proposta->getStatus() != 'A'){ 
					?>
                   <a href="#" class="std-btn gray-btn del-dje fr" data-id="<?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']) ? $custos_proposta['valor_D']->getId() : 0; ?>">Excluir</a> 
                   <?php }//nivel de acesso ?>
                   </div>
                   
                   <div class="campo-box">
                   <label class="label-block">Quantidade</label>
                   <input type="text" name="quantidade_dje" class="std-input sm-input number-input qtd-dje" <?php echo $proposta->getStatus() == 'A' && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'readonly' : ''; ?> value="<?php if( !DataValidator::isEmpty($custos_proposta) 
											  && isset($custos_proposta['valor_D']) )
												echo $custos_proposta['valor_D']->getQuantidade();
										  elseif(isset($_POST['quantidade_dje']))
												echo $_POST['quantidade_dje'];
										else
												echo '1';									
									?>">
                   </div>
                   <!-- campo -->
                   
                   <!--<div class="campo-box">
                   <label class="label-block">Valor Padrão (R$) </label>
                   <input type="text" name="valor_padrao_dje" class="std-input sm-input money-input valor-dje-padrao val-padrao" <?php //echo $proposta->getStatus() == 'A' ? 'readonly' : ''; ?> value="<?php /*if(!DataValidator::isEmpty($custos_proposta) && 
					  isset($custos_proposta['valor_D']))
				   		echo $custos_proposta['valor_D']->getValorPadrao();
					 elseif( isset($_POST['valor_padrao_dje']) )
						echo $_POST['valor_padrao_dje'];	
					 else
						echo '0,00';*/
				   ?>">
                   </div>-->
                   <!-- campo -->
                   
                   <div class="campo-box">
                   <label class="label-block">Valor DJE (R$) </label>
                   <input type="text" name="valor_dje" class="std-input sm-input money-input valor-dje" <?php echo $proposta->getStatus() == 'A' && !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($processo->getJornal()) ? 'readonly' : ''; ?> value="<?php if(!DataValidator::isEmpty($custos_proposta) && 
					  isset($custos_proposta['valor_D']))
				   		echo $custos_proposta['valor_D']->getValorDje();
					 elseif( isset($_POST['valor_dje']) )
						echo $_POST['valor_dje'];	
					else
						echo '0,00';
				   ?>">
                   </div>
                   <!-- campo -->
                   <div class="campo-box">
                   <label class="label-block">Valor Final (R$) </label>
                   <input type="text" name="valor_final_dje" readonly class="std-input sm-input money-input valor-dje-final" value="<?php if(!DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']))
				   		echo $custos_proposta['valor_D']->getValorFinal();
					 elseif( isset($_POST['valor_final_dje']) )
						echo $_POST['valor_final_dje'];		
					else
						echo '0,00';					
				   ?>">
                   </div>
                   <!-- campo -->
                   
                   <input type="hidden" class="id-dje" name="custo_dje_id" value="<?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']) ? $custos_proposta['valor_D']->getId() : 0; ?>">                   
                   <input type="hidden" name="adiciona_dje" value="">
                   <input type="hidden" name="aceite_dje" class="hidden-aceite" value="<?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']) && $custos_proposta['valor_D']->getAceite() == 'A' ? 'A' : ''; ?>">
                   
                   
                   <?php
				   		if( !DataValidator::isEmpty($proposta->getId()) && 
							isset($responsabilidades[3]) && 
							$responsabilidades[3]['acao'] == 'E' &&	
							//$proposta->getStatus() != 'R' &&
							DataValidator::isEmpty( $processo->getAlertas() )
							) { 
					?>
                   <a href="#" class="std-btn sm-btn full-width-btn <?php echo $proposta->getStatus() != 'A' ? 'aceite-jornal' : 'jornal-nao-aceito'; ?> <?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']) && $custos_proposta['valor_D']->getAceite() == 'A' ? 'green-btn jornal-aceito' : 'black-btn'; ?>">                   
                   
				   <?php echo !DataValidator::isEmpty($custos_proposta) && isset($custos_proposta['valor_D']) && $custos_proposta['valor_D']->getAceite() == 'A' ? 'Aceito' : 'Aceite'; ?></a>
				   
				   <?php }//nivel de acesso ?>
               </div>			   
               			<!-- box dje -->
                            
                            						
						</div>
						<!-- panel content -->

					</div>	
					<!-- panel jornal -->
					
					<br class="clear">
					<br>

            <div class=""><em>* Campos de preenchimento obrigatório.</em></div>
            <br>
				
					<div class="controles clearfix">
						
						<div class="fl">	
							<a href="#" title="Voltar" class="std-btn btn-pesquisa">Voltar</a>
						
						<?php 
							if( $usuario_logado->getPerfil()->getId() == 1 && 
								!DataValidator::isEmpty($responsabilidades) && 
								isset($responsabilidades[3]) && 
								$responsabilidades[3]['acao'] == 'E' ){			
						?>
							
								<a href="#" id="exclui-item-btn" title="Excluir" class="std-btn black-btn del-item" data-del-message="Tem certeza que deseja excluir esta Proposta?">Excluir</a>
						<?php }//nivel acesso ?>
						
						</div>
						<div class="fr">	
                        
					<?php 					
						if( !DataValidator::isEmpty($responsabilidades) && 
							isset($responsabilidades[3]) && 
							$responsabilidades[3]['acao'] == 'E' ){										
					?>  

						<?php //if( $proposta->getStatus() == 'E' ){ ?>	
						<input type="button" value="Rejeitar Proposta" data-acao="rejeitar" data-msg="Confirma a rejeição da proposta?" class="std-btn dark-red-btn btn-proposta">	
						<?php //}//rejeitar ?>

						<?php 
						if( !DataValidator::isEmpty($proposta->getId()) && !DataValidator::isEmpty($processo) && DataValidator::isEmpty( $processo->getAlertas() ) && $proposta->getStatus() != 'R' ){ 
						?>	
							<input type="button" value="Enviar ao Advogado" data-acao="enviar" data-msg="Confirma o envio da proposta?" class="std-btn dark-gray-btn btn-proposta">	
						<?php }//envio ?>
										
							<input type="submit" value="Salvar" class="std-btn send-btn">	
                            
                    <?php }//nivel acesso ?>
                            
						</div>

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
				$estados = array("RJ"=>"RJ", "SP"=>"SP" );			
				foreach( $estados as $key=>$value ){
				?>			
					<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && $entrada->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
				<?php
				}
				?>
			</select>
            <input type="hidden" name="estado_lightbox" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : ''; ?>">
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Secretaria/Fórum</label>
			<input type="text" name="nome_secretaria" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getSecretaria()) ? $entrada->getSecretaria() : ''; ?>" class="std-input md-input">
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
                        <input type="text" name="nome_advogado" value="<?php echo !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) && !DataValidator::isEmpty($entrada->getAdvogado()) ? $entrada->getAdvogado() : ''; ?>" class="std-input md-input">
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
			<select name="status_jornal" id="sel-status-jornal" class="">
				<option value="0">Selecione</option>
				<option value="P">Padrão</option>
				<option value="C">Comum</option>
			</select>
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Nome do Jornal</label>
			<input type="text" name="nome_jornal" value="" class="std-input">
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
			$secretarias = SecretariaModel::listaByEstado( !DataValidator::isEmpty($processo) && !DataValidator::isEmpty($entrada) ? $entrada->getEstado() : null );
			
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

	$('.btn-pesquisa').bind('click', function() { 
		$('#form-pesquisa').submit();
	});
	
	App.propostas();
</script>
	
</body>
</html>