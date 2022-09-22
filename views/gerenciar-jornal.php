<?php
	require_once("valida-sessao.php");
	
	$params = $this->getParams();
	$msg = isset($params['mensagem']) ? $params['mensagem'] : null; 
	$sucesso = isset($params['sucesso']) ? $params['sucesso'] : null; 
	$jornal = isset($params['jornal']) ? $params['jornal'] : new Jornal();	
	$alertas = $jornal->getAlertas();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.2.custom.min.css">
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
			
				<h1 class="std-title title title-w-btn">
				<span class="txt-14 txt-normal">Jornal</span> <span class="seta"> &gt; </span>
				<?php echo !DataValidator::isEmpty($jornal->getNome()) ? $jornal->getNome(): 'Cadastro'; ?>
				</h1>

				<div class="buttons fr">
					<a href="#" title="Voltar" class="std-btn dark-gray-btn btn-pesquisa">Voltar</a>
				</div><!-- buttons -->

			</div>

			<div class="principal">

				<?php if( !DataValidator::isEmpty($jornal->getId()) ){ ?>
                <div class="alert-panel panel-2">

					<div class="alert-box                     
                    <?php 					
                    echo !DataValidator::isEmpty($alertas) && isset($alertas['data_expirada']) ? 'danger' : ''; 
					?>">
						<span class="text-lg"><?php 
						if( isset($_POST['data_confirmacao']) && !DataValidator::isEmpty($_POST['data_confirmacao']) ) echo $_POST['data_confirmacao'];
						elseif( !DataValidator::isEmpty($jornal->getDataConfirmacao()) && $jornal->getDataConfirmacao() != '0000-00-00 00:00:00') echo date('d/m/Y', strtotime($jornal->getDataConfirmacao()) ); ?></span>
						<span class="text-sm">Data da Última Confirmação</span>
					</div>

					<div class="alert-box">
						<span class="text-lg"><?php echo $jornal->getStatusDesc(); ?></span>
						<span class="text-sm">Status</span>
					</div>

				</div>
				<!-- alert panel -->
                <?php } ?>

				<!-- start: warnings -->
				<?php if( isset($msg) && !DataValidator::isEmpty($msg) ){ ?>
                        <span class="warning erro"><?php echo $msg; ?></span>
				<?php } ?>
                
                <?php if( isset($sucesso) && !DataValidator::isEmpty($sucesso) ){ ?>
                    	<span class="warning sucesso"><?php echo $sucesso; ?></span>
                <?php } ?>
				<!-- end: warnings -->
                
                <form method="post" id="form-pesquisa">
                    <input type="hidden" name="controle" value="Jornal">
                    <input type="hidden" name="acao" value="busca">
                    <input type="hidden" name="origem" value="jornal">
                </form>
                <!--form pesquisa-->

				<form action="" class="std-form clear form-has-tel" method="post">
					
					<input type="hidden" name="controle" value="Jornal">
					<input type="hidden" name="acao" value="salva">
					<input type="hidden" name="jornal_id" value="<?php echo $jornal->getId(); ?>">
					<input type="hidden" name="usuario_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

					<?php 
					if( !DataValidator::isEmpty($alertas) ){ 
					?>	
					
					<div class="panel panel-danger bordered-1">
					
						<div class="panel-title">Quadro de Alertas </div>
						
						<div class="panel-content">
							<ul class="list-group list-zebra">
							<?php 
							foreach($alertas as $alerta){
							?>
							<li class="list-group-item list-group-item-bold">
							<?php echo $alerta; ?>
							</li>
							<?php } ?>
							</ul>
						</div> 
						<!-- panel content -->

					</div>
					<!-- quadro de alertas -->
                    
					<br>
					<br>

          <?php } ?>

					<div class="panel panel-accordion">
						
						<div class="panel-title">Dados Gerais<i class="seta seta-baixo"></i></div>
						<!-- panel title -->
						
						<div class="panel-content" style="display: block;">

							<div class="campo-box clearfix">
								<label class="fl">Data do Cadastro</label>
								<input type="text" readonly value="<?php echo !DataValidator::isEmpty($jornal->getDataEntrada()) ? date('d/m/Y', strtotime($jornal->getDataEntrada()) ) : date('d/m/Y', strtotime(date("Y-m-d H:i:s")) ); ?>" class="std-input date-input fl">
							</div>
							<!-- campo -->

							<div class="campo-box clearfix">
								<label class="fl">Data de Confirmação</label>

								<input type="text" name="data_confirmacao" value="<?php 
								if( isset($_POST['data_confirmacao']) && !DataValidator::isEmpty($_POST['data_confirmacao']) ) echo $_POST['data_confirmacao'];
								elseif( !DataValidator::isEmpty($jornal->getDataConfirmacao()) && $jornal->getDataConfirmacao() != '0000-00-00 00:00:00') echo date('d/m/Y', strtotime($jornal->getDataConfirmacao()) );								
								?>" class="std-input date-input fl">		

								

							</div>
							<!-- campo -->

							<div class="campo-box clear">
								<label for="">Nome Jornal*</label>
								<input type="text" name="nome" value="<?php echo $jornal->getNome(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Representante</label>
								<input type="text" name="nome_representante" value="<?php echo $jornal->getNomeRepresentante(); ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Contato</label>
								<input type="text" name="contato_representante" value="<?php echo $jornal->getContatoRepresentante(); ?>" class="std-input">
							</div>
							<!-- campo -->                     

							<div class="campo-box">
								
								<label for="">Ativo*</label>								
								<select name="ativo" id="" class="">
									<option value="A" <?php echo $jornal->getAtivo() == 'A' ? 'selected' : ''; ?>>Ativo</option>
									<option value="I" <?php echo $jornal->getAtivo() == 'I' ? 'selected' : ''; ?>>Inativo</option>
								</select>

							</div>
							<!-- campo -->
                            
                            <div class="campo-box">
								
								<label for="">Status*</label>								
								<select name="status" id="" class="">
									<option value="0">Selecione</option>
									<option value="P" <?php echo $jornal->getStatus() == 'P' ? 'selected' : ''; ?>>Padrão</option>
									<option value="C" <?php echo $jornal->getStatus() == 'C' ? 'selected' : ''; ?>>Comum</option>
								</select>

							</div>
							<!-- campo -->
							
							<span align="right">
								<?php if( !DataValidator::isEmpty($jornal->getDataAlteracao()) && !DataValidator::isEmpty($jornal->getDataConfirmacao()) ){ ?>
								<span class="clear obs-form <?php echo !isset($alertas['data_expirada']) ? 'obs-form-preto' : ''; ?> obs-form-block">
								Última alteração realizada por <strong><?php echo !DataValidator::isEmpty($jornal->getUsuario()) ? $jornal->getUsuario()->getNome() : ''; ?></strong> no dia <?php echo date('d/m/Y', strtotime($jornal->getDataAlteracao()) ); ?>.
								</span>	
								<?php } ?>
							</span>
							
						</div>
						
						</div>
						
						</br>
						
						<div class="panel panel-accordion">

						<div class="panel-title">Endereço<i class="seta seta-frente"></i></div>
						<!-- panel title -->
						
						<div class="panel-content" style="display: none;">

							<div class="campo-box">
								<label for="">CEP</label>
								<input type="text" name="cep" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getCep(): ''; ?>" class="std-input sm-input cep-input">

								<?php 				
								if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
								?>
								<a href="#" title="Buscar CEP" class="std-btn sm-btn busca-cep-btn">Buscar CEP</a>
								<?php } ?>                                
								<i class="loading-ico"><img src="img/loading.gif" alt=""></i>
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Logradouro</label>
								<input type="text" name="logradouro" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getLogradouro(): ''; ?>" class="std-input" id="logradouro">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Número</label>
								<input type="text" name="numero" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getNumero(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Compl.</label>
								<input type="text" name="complemento" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getComplemento(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">

								<label for="">Bairro</label>
								<input type="text" name="bairro" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getBairro(): ''; ?>" class="std-input" id="bairro">

							</div>
							<!-- campo -->

							<div class="campo-box">

								<label for="">Cidade</label>
								<input type="text" name="cidade" value="<?php echo !DataValidator::isEmpty($jornal->getEndereco()) ? $jornal->getEndereco()->getCidade(): ''; ?>" class="std-input" id="cidade">

							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Estado</label>
								<select name="estado" class="" id="estado">
									<?php
									//$estados = array("0"=>"Selecione", "AC"=>"AC", "AL"=>"AL", "AM"=>"AM", "AP"=>"AP", "BA"=>"BA", "CE"=>"CE", "DF"=>"DF", "ES"=>"ES", "GO"=>"GO", "MA"=>"MA", "MG"=>"MG", "MS"=>"MS", "MT"=>"MT", "PA"=>"PA", "PB"=>"PB", "PE"=>"PE", "PI"=>"PI", "PR"=>"PR", "RJ"=>"RJ", "RN"=>"RN", "RO"=>"RO", "RR"=>"RR", "RS"=>"RS", "SC"=>"SC", "SE"=>"SE", "SP"=>"SP", "TO"=>"TO" );
									$estados = EstadosEnum::getChavesUFs('Selecione');
									foreach( $estados as $key=>$value ){
									?>			
										<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($jornal->getEndereco()) &&  $key == $jornal->getEndereco()->getEstado() ? 'selected' : ''; ?> ><?php echo $value; ?></option>
									<?php
                                        }
                                    ?>			
								</select>
							</div>
							<!-- campo -->
							
							</div>
							
							</div>
							
							</br>
						
							<div class="panel panel-accordion">

							<div class="panel-title">Telefones<i class="seta seta-baixo"></i></div>
							<!-- panel title -->
						
							<div class="panel-content" style="display: block;">
							

							<div class="campo-box <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ? 'multiple-box' : ''; ?> clearfix">

									<?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
									?>
                                    <a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-tel">
										Adicionar Telefone
									</a>
                                    <?php } ?>

									<div class="clear"></div>
	 
									<?php 
									if( !DataValidator::isEmpty($jornal->getTelefones()) ){
										foreach( $jornal->getTelefones() as $tel ){
									?>  
										               
									<div class="campo-box tel-box">

										<label>DDD/Telefone</label>
										
										<input type="text" name="ddd[]" value="<?php echo $tel->getDdd(); ?>" class="std-input ddd-input">
										
										<input type="text" name="numero_telefone[]" value="<?php echo $tel->getNumero(); ?>" class="std-input tel-input">
										<input type="hidden" name="tel_id[]" value="<?php echo $tel->getId(); ?>">
										&nbsp;

										<?php 				
										if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
										?>
										<a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-acao="jornal-tel"
										data-id="<?php echo $tel->getId(); ?>">Excluir</a>
										<?php } ?>
                                        
									</div>
									<!-- campo -->
									
									<?php }} ?>

									</div>
							    <!-- campo -->
	
                       			</div>
                       
                       			</div>
                       			
                       			</br>
						
								<div class="panel panel-accordion">

								<div class="panel-title">E-mails<i class="seta seta-baixo"></i></div>
								<!-- panel title -->
						
								<div class="panel-content" style="display: block;">
                       			
							    <div class="campo-box <?php echo !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ? 'multiple-box' : ''; ?> clearfix">
									
                                    <?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
									?>
									<a href="#" title="Adicionar" class="std-btn sm-btn add-btn fr add-email">
										Adicionar E-mail
									</a>
                                    <?php } ?>

									<div class="clear"></div>                               							
                                
								<?php 
								if( !DataValidator::isEmpty($jornal->getEmails()) ){
									foreach( $jornal->getEmails() as $email ){	
								?>   

								<div class="campo-box email-box">
									<label for="">Email</label>
									<input type="text" name="email[]" value="<?php echo $email->getEmailEndereco(); ?>" class="std-input md-input">
                                    <input type="hidden" name="email_id[]" value="<?php echo $email->getId(); ?>">
                                    &nbsp;
                                    
                                    <?php 				
									if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
									?>
                                    <a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-id="<?php echo $email->getId(); ?>" data-acao="jornal-email">Excluir</a>
                                    <?php } ?>
                                    
								</div>
								<!-- campo -->

								<?php }} else{ ?> 
                                
								<div class="campo-box email-box">
								<label for="">Email</label>
								<input type="text" name="email[]" class="std-input md-input">
								<input type="hidden" name="email_id[]" value="0">
								</div>
								<!-- campo -->
                                
								<?php } ?>

							</div>
								<!-- multiple box -->

						</div>
						<!-- panel -->

					<br>

					<!-- start: circulação -->
					<div class="panel panel-accordion">
						
						<div class="panel-title jornal-circ-title"> Circulação <i class="seta seta-baixo"></i> </div>
						<!-- panel title -->
						
						<div class="panel-content panel-circ" style="display:block;">

							<div class="campo-box clearfix">
								<label class="fl">Período <i class="icon-question tooltip-link" 
								data-title="Para selecionar mais de uma opção, mantenha a tecla CTRL (control) pressionada e clique nas opções desejadas" data-y="105" data-x="0" data-direction="right" data-width="250">?</i></label>
								<select name="periodo[]" id="" multiple="multiple" class="fl">
								<?php
								$periodos = array("1"=>"Segunda-feira", "2"=>"Terça-feira", "3"=>"Quarta-feira", "4"=>"Quinta-feira", "5"=>"Sexta-feira", "6"=>"Sábado", "7"=>"Domingo", "8"=>"Quinzenal", "9"=>"Mensal", "10"=>"Outros" );

								$jornal_periodos = JornalModel::get_periodos( !DataValidator::isEmpty($jornal->getId()) ? $jornal->getId() : 0 );

								foreach( $periodos as $key => $value ){
								?>			
								<option value="<?php echo $key; ?>"                                        
								<?php 
								if( in_array($key, $jornal_periodos) )
								echo 'selected';					   
								?>> 			
								<?php echo $value; ?></option>
								<?php } ?>
								</select>

								<!-- <div class="obs-form obs-form-inline">
									Para selecionar mais de uma opção, mantenha a tecla CTRL (control) pressionada e clique nas opções desejadas. 
								</div> -->

							</div>
							<!-- campo -->
								
							<div class="campo-box">
								<label for="">Fechamento</label>
								<input type="text" name="fechamento" class="std-input" value="<?php echo !DataValidator::isEmpty($jornal->getFechamento()) ? $jornal->getFechamento() : ''; ?>">
							</div>
							<!-- campo -->
					
					
							<div class="campo-box">
								<label>Estado de circulação</label>
								<select name="estado_periodo" class="sm-input">
								<?php
								//$estados = array("0"=>"Selecione", "AC"=>"AC", "AL"=>"AL", "AM"=>"AM", "AP"=>"AP", "BA"=>"BA", "CE"=>"CE", "DF"=>"DF", "ES"=>"ES", "GO"=>"GO", "MA"=>"MA", "MG"=>"MG", "MS"=>"MS", "MT"=>"MT", "PA"=>"PA", "PB"=>"PB", "PE"=>"PE", "PI"=>"PI", "PR"=>"PR", "RJ"=>"RJ", "RN"=>"RN", "RO"=>"RO", "RR"=>"RR", "RS"=>"RS", "SC"=>"SC", "SE"=>"SE", "SP"=>"SP", "TO"=>"TO" );
								$estados = EstadosEnum::getChavesUFs('Selecione');	
								foreach( $estados as $key=>$value ) {
?>			
									<option value="<?php echo $key; ?>" <?php echo !DataValidator::isEmpty($jornal->getEstadoPeriodo()) &&  $key == $jornal->getEstadoPeriodo() ? 'selected' : ''; ?>><?php echo $value; ?></option>
<?php
                                                                }
?>	
								</select>										
							</div>
							<!-- campo -->	

							<div class="cidade-jornal-wrapper campo-box multiple-box">

								<a href="#" class="std-btn sm-btn black-btn add-city fr">
									Adicionar Cidade
								</a>

								<div class="clear"></div>

								<?php 									  
								$cidades = $jornal->getCidades();
								if( !DataValidator::isEmpty($cidades) ){
									foreach($cidades as $cidade){	  
								?>
								 <div class="campo-box clear">
									<label>Cidade</label>
									<input type="hidden" name="cidade_id[]" class="id-cidade" value="<?php echo $cidade->getId(); ?>">	
									<input type="text" name="cidade_circulacao[]" value="<?php echo $cidade->getNome(); ?>" class="campo-cidade std-input md-input">
                                    <a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-acao="jornal-cidade"
										data-id="<?php echo $cidade->getId(); ?>">Excluir</a>
								</div> 
								<!-- box cidade -->
                                <?php }} ?>

							</div>
							<!-- cidade jornal wrapper -->


							</div>
					
						</div>
					
					</br>


					<!-- start: custo -->
					<div class="panel panel-accordion">
						
						<div class="panel-title"> Custo <i class="seta seta-baixo"></i> </div>
						<!-- panel title -->
						
						<div class="panel-content clearfix" style="display: block;">
			
							<div class="campo-box">
								<label for="">Medida de 2 colunas (cm)</label>
								<input type="text" name="medida" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getMedida(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Valor CM Forense (R$)</label>
								<input type="text" name="valor_forense" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getValorForense(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Negociação (%)</label>
								<input type="text" name="negociacao" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getNegociacao(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">				
								<label for="">Desconto (%)</label>
								<input type="text" name="desconto" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getDesconto(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Valor Padrão (R$)</label>
								<input type="text" name="valor_padrao" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getValorPadrao(): ''; ?>" class="std-input money-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Valor DJE (R$)</label>
								<input type="text" name="valor_dje" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getValorDje(): ''; ?>" class="std-input money-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Valor cm Empregos</label>
								<input type="text" name="valor_empregos" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getValorEmpregos(): ''; ?>" class="std-input">
							</div>
							<!-- campo -->

							<div class="campo-box">
								<label for="">Valor cm Publicidade Legal</label>
								<input type="text" name="valor_publicidade" value="<?php echo !DataValidator::isEmpty($jornal->getCusto()) ? $jornal->getCusto()->getValorPublicidade(): ''; ?>" class="std-input money-input">
							</div>
							<!-- campo -->

						</div>
						<!-- panel content -->

					</div>
					<!-- panel -->
					<!-- end: custo -->

					</br>

					<!-- start: secretarias -->
					<div class="panel panel-accordion panel-secretaria">

					<div class="panel-title jornal-circ-title"> Composição/Observações <i class="seta seta-baixo"></i> </div>
					<!-- panel title -->

					<div class="panel-content" style="display:block;">


							<div class="campo-box">
								<label for="">Composição</label>
								<input type="text" name="composicao" class="std-input" value="<?php echo !DataValidator::isEmpty($jornal->getComposicao()) ? $jornal->getComposicao() : ''; ?>">
							</div>
							<!-- campo -->
		
							<div class="campo-box">
								<label for="">Observações</label>
								<textarea name="observacoes" cols="30" rows="4" class="std-input"><?php echo $jornal->getObservacoes(); ?></textarea>
							</div>
							<!-- campo -->

					</div>
					
					</div>
					
					</br>

					<!-- start: secretarias -->
					<div class="panel panel-accordion panel-secretaria">

					<div class="panel-title jornal-circ-title"> Secretarias
					<i class="seta seta-frente"></i> </div>
					<!-- panel title -->

					<div class="panel-content" style="display:none;">

					<div class="campo-box">
						<label><strong>Buscar Secretaria</strong></label>
						<input type="text" class="std-input lg-input secretaria-field" 
						placeholder="Digite o nome da Secretaria para a seleção no Banco de Dados" >
					</div>
					<!-- campo -->
                    
					<?php 									  
					$secretarias = $jornal->getSecretarias();
					if( !DataValidator::isEmpty($secretarias) ){
					foreach($secretarias as $secretaria){	  
					?>
					<div class="campo-box"><label>Secretaria/Fórum</label>
					<input type="hidden" name="secretaria[]" value="<?php echo $secretaria->getId(); ?>">
					<input type="text" class="std-input secretaria-nome" readonly value="<?php echo $secretaria->getNome(); ?>">
					&nbsp; <a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $secretaria->getId(); ?>" data-acao="jornal-secretaria">Excluir</a></div>
					<?php }} ?>

					</div>
					<!-- panel content -->

					</div>
					<!-- panel -->	

					<br>

					
					
					
                    <?php 				
					if( !DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[6]) && $responsabilidades[6]['acao'] == 'E' ){
					?>

					<br>	
					<div class=""><em>* Campos de preenchimento obrigatório.</em></div>
            
					<div class="controles clearfix">
					
						<div class="fl">
		                	<a href="#" title="Voltar" class="std-btn btn-pesquisa">Voltar</a>
		            	</div>   
						<div class="fr">
							<input type="submit" value="Salvar" class="std-btn send-btn">
						</div>
					</div>
					<!-- controles -->
                    <?php } ?>
                    
                    </div>

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
	
	App.jornais();
</script>	

</body>
</html>