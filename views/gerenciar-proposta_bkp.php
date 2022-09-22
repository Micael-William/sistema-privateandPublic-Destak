<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<link rel="stylesheet" href="css/jquery-ui-1.9.2.custom.min.css">
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
			
				<h1 class="std-title title title-amarelo title-w-btn">
				<span class="txt-14 txt-normal">Proposta</span> <span class="seta"> &gt; </span> 0013237-71.2007.8.19.0028
				</h1>

				<div class="buttons fr">
					<a href="propostas.html" title="Voltar" class="std-btn dark-gray-btn">Voltar</a>
				</div><!-- buttons -->

			</div>

			<div class="principal">

				<!-- start: warnings -->
				<!-- <div class="warning-box">
					<span class="warning erro">Erro</span>
					<span class="warning sucesso">Sucesso</span>
				</div> -->
				<!-- end: warnings -->

				<div class="alert-panel panel-2">
				
					<div class="alert-box">
						<span class="text-lg">Enviada</span>
						<span class="text-sm">Status</span>
					</div>					

					<div class="alert-box">
						<span class="text-lg">Amarelo</span>
						<span class="text-sm">Sinalizador</span>
					</div>

				</div>
				<!-- alert panel -->	

				<form action="" class="std-form clear">

					<div class="panel panel-warning bordered-1 panel-accordion">
						<div class="panel-title">Quadro de Alertas <i class="seta seta-frente"></i> </div>

					  <div class="panel-content">
						<ul class="list-group list-zebra">
							<li class="list-group-item list-group-item-bold">Requerente não identificado.</li>
							<li class="list-group-item list-group-item-bold">Requerido não identificado.</li>
							<li class="list-group-item list-group-item-bold">Advogado não identificado.</li>
							<li class="list-group-item list-group-item-bold">Advogado identificado não possui e-mail cadastrado.</li>
							<li class="list-group-item list-group-item-bold">Jornal não identificado.</li>
							<li class="list-group-item list-group-item-bold">Jornal identificado sem Valor Padrão.</li>
							<li class="list-group-item list-group-item-bold">Data de Confirmação do Jornal superior a 365 dias.</li>
						</ul>
						</div> 

					</div>
					<!-- quadro de alertas -->

					<br>

					<div class="panel panel-accordion">
						
						<div class="panel-title">Dados Gerais <i class="seta seta-frente"></i> </div>

						<div class="panel-content" style="display: block;">
						<br>

						<div class="campo-box inline-campo-box">
							
							<label>Status</label>
							
							<select name="" id="" class="campo-1">
								<option value="0">Selecione</option>
								<option value="N">Nova</option>
								<option value="E">Enviada</option>
								<option value="A">Aceite</option>
								<option value="R">Rejeitada</option>
							</select>

							<label>Estado</label>
							<select name="" id="" disabled="disabled">
								<option value="0">Selecione</option>
								<option value="SP">SP</option>
								<option value="RJ">RJ</option>
							</select>

						</div>
						<!-- campo -->

						<div class="campo-box inline-campo-box">

							<label for="">Data de Envio</label>
							<input type="text" name="data_envio" value="11/11/2014"
							class="std-input date-input" readonly="readonly">
							
							<label for="">Usuário</label>
							<input type="text" name="data_processo" value="Fulano de Tal"
							class="std-input md-input campo-1" readonly="readonly">

						</div>
						<!-- campo -->

						<div class="campo-box inline-campo-box">
							
							<label for="">Data do Processo</label>
							<input type="text" name="data_processo" value="11/11/2014"
							class="std-input date-input campo-1" readonly="readonly">

							<label for="">Data Entrada Sistema</label>
							<input type="text" name="data_triagem" value="11/11/2014"
							class="std-input date-input" readonly="readonly">

						</div>
						<!-- campo -->

						<div class="campo-box inline-campo-box">

							<label for="">Nº do Processo</label>
							<input type="text" name="num_processo" value="0013237-71.2007.8.19.0028" readonly="readonly"
							class="std-input md-input campo-1">
							
							<label for="">Código Interno</label>
							<input type="text" name="num_processo" value="XPTO123" 
							class="std-input sm-input" readonly="readonly">

						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Requerente</label>
							<input type="text" name="requerente" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Requerido</label>
							<input type="text" name="requerido" class="std-input">
						</div>
						<!-- campo -->

						<div class="clear">&nbsp;</div>

						<div class="campo-box">

							<div class="area-label">							
								<label for="" class="fl"
								>Secretaria/Fórum</label>
							</div>

							<div class="area-campo">						
                
                <!-- label -->
								<?php if (isset($processo) && DataValidator::isEmpty($processo->getSecretaria()) ) { ?>
								<span class="dado-arquivo"> <?php echo !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getSecretaria()) ? 'Secretaria/Fórum do arquivo: <strong>' . utf8_encode($processo->getEntrada()->getSecretaria()) . '</strong>' : 'Secretaria/Fórum não ecnontrado no arquivo'; ?> </span>
								<?php } ?>
								<!-- //label -->
								
								<!-- select -->
								<select name="secr_id" class="sel-full sel-carrega-jornal" id="sel-secretaria">
									<option value="0" selected>Selecione</option>
									<?php 
									$secretarias = SecretariaModel::listaByEstado( isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getEstado() : null );
									if( !DataValidator::isEmpty($secretarias) ){
									foreach($secretarias as $sec){
									?>
									<option value="<?php echo $sec->getId(); ?>" <?php echo isset($processo) && !DataValidator::isEmpty($processo->getSecretaria()) && $processo->getSecretaria()->getId() == $sec->getId() ? 'selected' : null; ?>><?php echo utf8_encode($sec->getNome()); ?></option>
									<?php }} ?>
								</select>	
								<!-- //select -->
								
								<!-- btn cadastrar -->
								<?php if (isset($processo) && DataValidator::isEmpty($processo->getSecretaria()) ) { ?>							
								<a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox" data-rel="box-add-secretaria">Cadastrar</a>
								<?php } ?>
								<!-- //btn cadastrar -->
                                
							</div>

						</div>
						<!-- campo -->

						<div class="clear">&nbsp;</div>

						<div class="campo-box">
							<label class="label-textarea">Conteúdo</label>
							<textarea name="conteudo" rows="8" class="std-input"></textarea>
						</div>
						<!-- campo -->

						</div>
						<!-- panel content -->

					</div>
					<!-- panel dados gerais -->

					<br>

					<div class="panel panel-accordion">
					
					<div class="panel-title">Advogado <i class="seta seta-frente"></i> </div>

					<div class="panel-content" style="display: block;">

						<div class="campo-box">

							<div class="area-label">							
								<label 
                                <?php 
								if( isset($processo) && !DataValidator::isEmpty($processo->getAdvogado()) && !DataValidator::isEmpty($processo->getAdvogado()->getEmails())) { 
								$emails_advogado = $processo->getAdvogado()->getEmails();
								$mail_adv = $emails_advogado[0];
								
								?>
                                for="" class="fl tooltip-link" 
								data-title="<?php echo utf8_encode($mail_adv); ?>"
								data-direction="right" data-y="45" data-x="-20"
                                <?php } ?>
                                >Advogado</label>
							</div>

							<div class="area-campo">							
                            	
								<?php if (isset($processo) && DataValidator::isEmpty($processo->getAdvogado()) ) { ?>
								<span class="dado-arquivo"> <?php echo !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getAdvogado()) ? 'Advogado do arquivo: <strong>' . utf8_encode($processo->getEntrada()->getAdvogado()) . '</strong>' : 'Advogado não ecnontrado no arquivo'; ?> </span>
								<?php } ?>

								<input type="text" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? utf8_encode($processo->getAdvogado()->getNome()) : null; ?>" class="std-input advogado-field">

								<?php if (isset($processo) && DataValidator::isEmpty($processo->getAdvogado()) ) { ?>
								<a href="#" target="_blank" class="std-btn btn add-btn clear link-lightbox" data-rel="box-add-advogado">Cadastrar</a>
								<?php } ?>

								</div>

								<input type="hidden" id="advogado-id" name="advogado_id" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getAdvogado()) ? $processo->getAdvogado()->getId() : 0; ?>">

						</div>
						<!-- campo -->

						<br class="clear"><br>
					
						<div class="campo-box">
							<label>Nome</label>
							<input type="text" name="nome_advogado" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>E-mail</label>
							<input type="text" name="email_advogado" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>DDD/Telefone 1</label>
							<input type="text" name="ddd_1" class="std-input ddd-input">
							<input type="text" name="telefone_1" class="std-input tel-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>DDD/Telefone 2</label>
							<input type="text" name="ddd_2" class="std-input ddd-input">
							<input type="text" name="telefone_2" class="std-input tel-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>DDD/Telefone 3</label>
							<input type="text" name="ddd_3" class="std-input ddd-input">
							<input type="text" name="telefone_3" class="std-input tel-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>Nome do Contato</label>
							<input type="text" name="nome_contato" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box">
							<label>E-mail do Contato</label>
							<input type="text" name="email_contato" class="std-input">
						</div>
						<!-- campo -->

						<div class="campo-box clearfix">

							<div class="panel panel-accordion panel-default-bg">
								
								<div class="panel-title obs-title">
								Observações &nbsp;&nbsp; <a href="#" title="Adicionar Observação" class="std-btn sm-btn add-obs">Adicionar</a>
								<i class="seta seta-frente"></i>
								</div>
								
								<div class="panel-content panel-obs">
								
									<div class="box box-obs" id="box1"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong>18/08/2014</strong>
										<br>
										Usuário: <strong>Katelyn Kusac</strong>
										</span>

										<br class="clear">
										<br>

										<a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-obs">Excluir</a>
										</label>

										<textarea name="observacao[]" rows="5" class="std-input adv-obs" disabled="disabled">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius error ie consequuntur?</textarea>

									</div>
									<!-- obs gravada -->

									<div class="box box-obs" id="box2"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong>18/08/2014</strong>
										<br>
										Usuário: <strong>Katelyn Kusac</strong>
										</span>

										<br class="clear">
										<br>

										<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-obs">Excluir</a>
										</label>

										<textarea name="observacao[]" rows="5" class="std-input adv-obs" disabled="disabled">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius error ie consequuntur?</textarea>

									</div>
									<!-- obs gravada -->

									<div class="box box-obs" id="box3"> 
									
										<label>								
										<span class="fl w-335">
										Data: <strong>18/08/2014</strong>
										<br>
										Usuário: <strong>Katelyn Kusac</strong>
										</span>

										<br class="clear">
										<br>

										<a href="#" title="Excluir" class="std-btn gray-btn del-btn del-obs">Excluir</a>
										</label>

										<textarea name="observacao[]" rows="5" class="std-input adv-obs" disabled="disabled">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius error ie consequuntur?</textarea>

									</div>
									<!-- obs gravada -->									
									
								</div>
								<!-- panel content -->

							</div>
							<!-- panel obs -->		

						</div>
						<!-- campo -->

					</div>
					<!-- panel content -->

					</div>
					<!-- panel advogado -->

					<br>

					<div class="panel panel-accordion">
						<div class="panel-title jornal-title"> Jornal 
						&nbsp;&nbsp;
						<a href="#" title="Adicionar" class="std-btn sm-btn add-dje">Adicionar DJE</a>
						<i class="seta seta-frente"></i> </div>
						
						<div class="panel-content" style="display: block;">	

							<!-- apenas aparece quando não houver secretaria/fórum -->
							<p class="std-text">
								<br>
								É necessário escolher uma Secretaria/Fórum.
								<br>
								<br>
							</p>
							<!-- // apenas aparece quando não houver secretaria/fórum -->

							<!-- apenas aparece quando não houver jornal -->

							<div class="campo-box wrapper-campo-jornal" <?php echo isset($processo) && !DataValidator::isEmpty($processo->getSecretaria()) && !DataValidator::isEmpty($processo->getSecretaria()->getId()) ? 'style="display:block; "' : 'style="display:none; "'; ?>>

							<div class="area-label">							
								<label for="" class="fl"
								>Jornal</label>
							</div>

							<div class="area-campo">	
                            
								<?php if (isset($processo) && DataValidator::isEmpty($processo->getJornal()) ) { ?>						
								<span class="dado-arquivo"> <?php echo !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getJornal()) ? 'Jornal do arquivo: <strong>' . utf8_encode($processo->getEntrada()->getJornal()) . '</strong>' : 'Jornal não ecnontrado no arquivo'; ?> </span>
								<?php } ?>
                                
								<select name="jornal_id" class="sel-full" id="sel-jornal">
									<option value="0" selected>Selecione</option>
									<?php 
									$jornais = JornalModel::listaBySecretaria( isset($processo) && !DataValidator::isEmpty($processo->getSecretaria()) ? $processo->getSecretaria()->getId() : 0 );
									if( !DataValidator::isEmpty($jornais) ){
									foreach($jornais as $journal){
									?>
									<option value="<?php echo $journal->getId(); ?>" <?php echo isset($processo) && !DataValidator::isEmpty($processo->getJornal()) && $processo->getJornal()->getId() == $journal->getId() ? 'selected' : null; ?>><?php echo $journal->getNome(); ?> - <?php echo utf8_encode($journal->getStatusDesc()); ?> <?php echo ' - ' . date('d/m/Y', strtotime($journal->getDataConfirmacao() )); ?></option>
									<?php }} ?>									
								</select>   

								<?php //if (isset($processo) && DataValidator::isEmpty($processo->getJornal()) ) { ?>	
								<a href="#" target="_blank" class="std-btn btn sm-btn clear link-lightbox btn-lightbox-jornal" data-rel="box-add-jornal"
								<?php if (isset($processo) && DataValidator::isEmpty($processo->getSecretaria()) ) { echo ' style="display: inline-block;" '; }
								else { echo ' style="display: none;" '; } ?>>Cadastrar</a>
								<?php //} ?>

							</div>

						</div>
						<!-- campo -->

							<!-- // apenas aparece quando não houver jornal -->

							<br class="clear">
							<br>

							<div class="campo-box">
								<label class="label-block"><strong>Jornal <!-- Padrão --></strong></label>
								<select name="" id="sel-jornal-padrao">
									<option value="0">Selecione</option>
									<option value="1">Jornal 1</option>
									<option value="2">Jornal 2</option>
									<option value="3">Jornal 3</option>
							</select>
							</div>
							<!-- campo -->							

							<div class="box-jornal fl">									

								<div class="campo-box">
									<label class="label-block">Quantidade</label>
									<input type="text" name="quantidade" class="std-input sm-input number-input qtd-jornal">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label class="label-block">Valor Padrão (R$) </label>
									<input type="text" name="valor_padrao" class="std-input sm-input money-input valor-jornal">
								</div>
								<!-- campo -->

								<div class="campo-box">
									<label class="label-block">Valor final (R$) </label>
									<input type="text" name="valor_final" class="std-input sm-input money-input valor-final-jornal">
								</div>
								<!-- campo -->

								<a href="#" class="std-btn sm-btn black-btn full-width-btn aceite-jornal">Aceite</a>

							</div>
							<!-- box jornal	-->
						
						</div>
						<!-- panel content -->

					</div>	
					<!-- panel jornal -->
					
					<br class="clear">
					<br>
				
					<div class="controles clearfix">							
						
						<input type="submit" value="Enviar Proposta" class="std-btn dark-gray-btn send-btn fl">	

						<div class="fr">
						<a href="#" target="_blank" title="Excluir" 
						class="std-btn">Mudar Sinalização</a>						
						<input type="submit" value="Alterar" class="std-btn send-btn">	
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
					<option value="<?php echo $key; ?>" <?php echo isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) && $processo->getEntrada()->getEstado() == $key ? 'selected' : null; ?>><?php echo $value; ?></option>
				<?php
				}
				?>
			</select>
            <input type="hidden" name="estado_lightbox" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getEstado() : ''; ?>">
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Secretaria/Fórum</label>
			<input type="text" name="nome_secretaria" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getSecretaria()) ? utf8_encode($processo->getEntrada()->getSecretaria()) : ''; ?>" class="std-input md-input">
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
			<input type="text" name="nome_advogado" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getAdvogado()) ? utf8_encode($processo->getEntrada()->getAdvogado()) : ''; ?>" class="std-input md-input">
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>OAB</label>
			<input type="text" name="oab_advogado" class="std-input md-input">
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
			<input type="text" name="nome_jornal" value="<?php echo isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) && !DataValidator::isEmpty($processo->getEntrada()->getJornal()) ? utf8_encode($processo->getEntrada()->getJornal()) : ''; ?>" class="std-input md-input">
		</div>
		<!-- campo -->
        
    <div class="campo-box clear">
			<label>Cidade</label>
			<input type="text" name="cidade_jornal" class="std-input sm-input">
		</div>
		<!-- campo -->

		<div class="campo-box clear">
			<label>Secretaria/Fórum</label>
			<input name="secretaria_id" type="hidden" value="" id="hidden-secretaria-jornal">
			<select id="sel-secretaria-jornal" disabled="disabled">
			<?php 
			$secretarias = SecretariaModel::listaByEstado( isset($processo) && !DataValidator::isEmpty($processo->getEntrada()) ? $processo->getEntrada()->getEstado() : null );
			if( !DataValidator::isEmpty($secretarias) ){
			foreach($secretarias as $sec){
			?>
			<option value="<?php echo $sec->getId(); ?>"><?php echo utf8_encode($sec->getNome()); ?></option>
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

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

<script>
	App.propostas();
</script>
	
</body>
</html>