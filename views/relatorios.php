<?php 
require_once("valida-sessao.php");

$params = $this->getParams(); 
$total_sp = isset($params['total_sp']) ? $params['total_sp'] : 0; 
$total_rj = isset($params['total_rj']) ? $params['total_rj'] : 0; 
$data_inicio = isset($params['data_inicio']) ? $params['data_inicio'] : null; 
$data_fim = isset($params['data_fim']) ? $params['data_fim'] : null; 
$tipo = isset($params['tipo']) ? $params['tipo'] : null; 
$total_amarelo_sp = isset($params['total_amarelo_sp']) ? $params['total_amarelo_sp'] : 0; 
$total_amarelo_rj = isset($params['total_amarelo_rj']) ? $params['total_amarelo_rj'] : 0; 
$total_vermelho_sp = isset($params['total_vermelho_sp']) ? $params['total_vermelho_sp'] : 0; 
$total_vermelho_rj = isset($params['total_vermelho_rj']) ? $params['total_vermelho_rj'] : 0;
$totais = isset($params['totais']) ? $params['totais'] : 0;
$registros = isset($params['registros']) ? $params['registros'] : 0;

?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
        <link rel="stylesheet" href="css/Raleway.css">
	<link rel="stylesheet" href="css/estilos.css">
	<title>Relatórios - Destak Publicidade</title>
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
			
				<h1 class="std-title title title-full">Relatórios</h1>

			</div>

			<div class="box-filtros">
					 
				<div class="title titulo-filtro clearfix">
					<span class="fl">Filtros</span>
					<i class="seta-baixo fr"></i>
				</div>
				<!-- titulo -->

				<div class="campos clear" style="display:block;">
					
					<form action="" class="form-filtro clearfix form-busca-lista" method="post">
                    	<input type="hidden" name="controle" value="Relatorio">
                        <input type="hidden" name="acao" value="busca">
					
					<div class="campo-box">
						<label>Tipo de Relatório</label>
						<select name="tipo_relatorio" id="" class="sel-relatorio">
							<option value="Selecione">Selecione</option>
							<option value="Processos Importados" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Processos Importados' ? 'selected' : ''; ?>>Processos Importados</option>
							<option value="Sinalizadores" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Sinalizadores' ? 'selected' : ''; ?> class="opt-cores">Por Cores</option>
							<option value="Propostas Enviadas" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Propostas Enviadas' ? 'selected' : ''; ?>>Propostas Enviadas</option>							
							<option value="Propostas Rejeitadas" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Propostas Rejeitadas' ? 'selected' : ''; ?>>Propostas Rejeitadas</option>
                                                        <option value="Acompanhamento Usuario" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Acompanhamento Usuario' ? 'selected' : ''; ?>>Acompanhamento Processual por Usuário</option>
                                                        <option value="Acompanhamento Data de Aceite" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Acompanhamento Data de Aceite' ? 'selected' : ''; ?>>Acompanhamento Processual por Data de Aceite</option>
							<option value="Acompanhamento Data de Conclusão" <?php echo isset($_POST['tipo_relatorio']) && $_POST['tipo_relatorio'] == 'Acompanhamento Data de Conclusão' ? 'selected' : ''; ?>>Acompanhamento Processual por Data de Conclusão</option>
						</select>
					</div>
					<!-- campo -->	

					<div class="campo-box <?php echo DataValidator::isEmpty($tipo) || $tipo == 'Sinalizadores' ? 'hidden-field' : ''; ?> hidden-periodo">
						<label for="">Período</label>
						De &nbsp;&nbsp; <input type="text" name="data_de" value="<?php echo isset($_POST['data_de']) && !DataValidator::isEmpty($_POST['data_de']) ? $_POST['data_de'] : $data_inicio; ?>" class="date-input std-input sm-input"> &nbsp;&nbsp;&nbsp;
						Até &nbsp;&nbsp; <input type="text" name="data_ate" value="<?php echo isset($_POST['data_ate']) && !DataValidator::isEmpty($_POST['data_ate']) ? $_POST['data_ate'] : $data_fim; ?>" class="date-input std-input sm-input">
					</div>
					<!-- campo -->

					<div class="controles fr clearfix">
						<a href="http://sistemadestakpublicidade.com.br/?controle=Relatorio&acao=index" title="Limpar" class="std-btn">Limpar</a>
						<input type="submit" value="Buscar" class="std-btn send-btn">	
					</div>
					<!-- controles -->

				</form>
					<!-- form -->

				</div>
				<!-- campos-->

			</div>
			<!-- filtros -->
            
            <?php if( !DataValidator::isEmpty($tipo) ){ ?>

			<div class="principal">

				<?php 

				//tipo 'por cores'
                switch($tipo) {
                    case 'Sinalizadores' :  
			   ?>
				
                <div class="box-relatorio">
					<span class="rel-text-big"><?php echo $total_amarelo_sp . ' / ' . $total_vermelho_sp; ?></span>
					<span class="rel-text-small">Amarelos / Vermelhos - SP</span>
				</div>		
				<!-- box relatório -->	
                
                <div class="box-relatorio">
					<span class="rel-text-big rel-text-big--cor"><?php echo $total_amarelo_rj . ' / ' . $total_vermelho_rj; ?></span>
					<span class="rel-text-small">Amarelos / Vermelhos - RJ</span>
				</div>		
				<!-- box relatório -->

		<?php 
                        break;
                    case 'Acompanhamento Usuario' :
                        if(is_array($registros)) {
                            $estado_anterior = '';
                ?>
                            
                <?php
                            foreach($registros AS $estado => $usuarios) {
                                if($estado_anterior != $estado) {
                ?>
                                <div class="box-relatorio" align="center"><table width="70%" align="center"><tr><td colspan="2">
                                    <span class="rel-text-big"><?php echo strtoupper($estado)." - ".$totais[$estado];  ?></span>
                                </td></tr>
                <?php
                                }
                                foreach($usuarios AS $usuario) {
                                    list($nome,$qtd) = explode("::",$usuario);
                ?>
                                    <tr>
                                    <td width="90%" style="padding:3px;"><span class="rel-text-table" align="left">
                                        <?php echo $nome; ?>
                                    </span></td>
                                    <td width="10%"><span class="rel-text-small">
                                        <?php echo $qtd; ?>
                                    </span></td>
                                    </tr>
                <?php
                                }
                ?>
                                </table></div>
                <?php
                                $estado_anterior = $estado;
                            }
                
                        }
                        break;
                    default :
                ?>
                
				<div class="box-relatorio">
					<span class="rel-text-big"><?php echo $total_sp; ?></span>
					<span class="rel-text-small"><?php echo $tipo; ?> - SP</span>
				</div>		
				<!-- box relatório -->	
                
                <div class="box-relatorio">
					<span class="rel-text-big"><?php echo $total_rj; ?></span>
					<span class="rel-text-small"><?php echo $tipo; ?> - RJ</span>
				</div>		
				<!-- box relatório -->
                	
                <?php 
                        break;
                }
                ?>
				
			</div>
            
            <?php } ?>

	</div><!-- direita -->

	</div>

</div>

<!-- Scripts -->
<?php require_once("inc/scripts.inc.php"); ?>

</body>
</html>