<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("classes/Paginacao.class.php");
require_once("models/RelatorioModel.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class RelatorioController
{

	public function indexAction()
	{

		//data padrão
		$data_inicio = '01/' . date("m") . '/' . date("Y");
		$data_fim = DataFilter::ultimoDiaMes() . '/' . date("m") . '/' . date("Y");

		$view = new View('views/relatorios.php');
		$view->setParams(array('data_inicio' => $data_inicio, 'data_fim' => $data_fim));
		$view->showContents();
	}

	public static function buscaAction()
	{
		$msg = null;
		$retorno = null;

		//data padrão
		$data_inicio = '01/' . date("m") . '/' . date("Y");
		$data_fim = DataFilter::ultimoDiaMes() . '/' . date("m") . '/' . date("Y");

		$data_de = isset($_REQUEST['data_de']) && !DataValidator::isEmpty($_REQUEST['data_de']) ? $_REQUEST['data_de'] : null;
		$data_ate = isset($_REQUEST['data_ate']) && !DataValidator::isEmpty($_REQUEST['data_ate']) ? $_REQUEST['data_ate'] : null;
		$busca_tipo = isset($_REQUEST['tipo_relatorio']) && !DataValidator::isEmpty($_REQUEST['tipo_relatorio']) ? $_REQUEST['tipo_relatorio'] : null;
		$status = null;

		if (!DataValidator::isEmpty($busca_tipo)) {

			//Propostas
			if ($busca_tipo == 'Propostas Enviadas' || $busca_tipo == 'Propostas Rejeitadas') {

				if ($busca_tipo == 'Propostas Enviadas') $status = 'E';
				elseif ($busca_tipo == 'Propostas Rejeitadas') $status = 'R';

				$retorno = RelatorioModel::propostas(
					$status,
					$data_de,
					$data_ate
				);

				$view = new View('views/relatorios.php');
				$view->setParams(array('total_sp' => $retorno['total_sp'], 'total_rj' => $retorno['total_rj'], 'data_inicio' => $data_inicio, 'data_fim' => $data_fim, 'tipo' => $busca_tipo));
				$view->showContents();
			}

			//Acompanhamento por data de conclusão
			if ($busca_tipo == 'Acompanhamento Data de Conclusão') {

				$retorno = RelatorioModel::acompanhamentosConcluidos(
					$data_de,
					$data_ate
				);

				$view = new View('views/relatorios.php');
				$view->setParams(array('total_sp' => $retorno['total_sp'], 'total_rj' => $retorno['total_rj'], 'data_inicio' => $data_inicio, 'data_fim' => $data_fim, 'tipo' => $busca_tipo));
				$view->showContents();
			}

			//Acompanhamento por Usuários
			if ($busca_tipo == 'Acompanhamento Usuario') {

				$retorno = RelatorioModel::acompanhamentosUsuarios(
					$data_de,
					$data_ate
				);

				$view = new View('views/relatorios.php');
				$view->setParams(array('totais' => $retorno['totais'], 'registros' => $retorno['registros'], 'data_inicio' => $data_inicio, 'data_fim' => $data_fim, 'tipo' => $busca_tipo));
				$view->showContents();
			}

			//Acompanhamento por data de aceite da proposta
			if ($busca_tipo == 'Acompanhamento Data de Aceite') {

				$retorno = RelatorioModel::acompanhamentosAceitos(
					$data_de,
					$data_ate
				);

				$view = new View('views/relatorios.php');
				$view->setParams(array('total_sp' => $retorno['total_sp'], 'total_rj' => $retorno['total_rj'], 'data_inicio' => $data_inicio, 'data_fim' => $data_fim, 'tipo' => $busca_tipo));
				$view->showContents();
			}

			//Numero de importações
			if ($busca_tipo == 'Processos Importados') {

				$retorno = RelatorioModel::numero_importacoes(
					$data_de,
					$data_ate
				);

				$view = new View('views/relatorios.php');
				$view->setParams(array('total_sp' => $retorno['total_sp'], 'total_rj' => $retorno['total_rj'], 'data_inicio' => $data_inicio, 'data_fim' => $data_fim, 'tipo' => $busca_tipo));
				$view->showContents();
			}

			//Sinaliador dos Processos
			if ($busca_tipo == 'Sinalizadores') {
				$retorno = RelatorioModel::sinalizadores();

				$view = new View('views/relatorios.php');
				$view->setParams(array('total_amarelo_sp' => $retorno['total_amarelo_sp'], 'total_amarelo_rj' => $retorno['total_amarelo_rj'], 'total_vermelho_sp' => $retorno['total_vermelho_sp'], 'total_vermelho_rj' => $retorno['total_vermelho_rj'], 'tipo' => $busca_tipo));
				$view->showContents();
			}

			if ($busca_tipo == 'Selecione')
				header("Location: ?controle=Relatorio&acao=index");
		} //busca tipo		
	}
}
