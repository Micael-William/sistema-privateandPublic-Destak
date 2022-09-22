<?php
require_once("lib/View.php");
require_once("lib/DataValidator.php");
require_once("classes/Paginacao.class.php");
require_once("models/RelatorioFinanceiroModel.php");

/**
 * @package destak publicidade
 * @author Monica Cosme
 * @version 1.0
 */
class RelatorioFinanceiroController
{

	public function indexAction()
	{

		//data padrão
		$data_inicio = '01/' . date("m") . '/' . date("Y");
		$data_fim = DataFilter::ultimoDiaMes() . '/' . date("m") . '/' . date("Y");

		$view = new View('views/relatorios-financeiros.php');
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
			if ($busca_tipo == 'Selecione')
				header("Location: ?controle=RelatorioFinanceiro&acao=index");
		} //busca tipo

		switch ($busca_tipo) {
			case "rel002":
				$titulo = "Boletos Pendentes de Pgto";
				$retorno = RelatorioFinanceiroModel::boletosPendentes($data_de, $data_ate);
				break;
			case "rel003":
				$titulo = "Boletos Vencidos";
				$retorno = RelatorioFinanceiroModel::boletosVencidos($data_de, $data_ate);
				break;
			case "rel004":
				$titulo = "Boletos Pagos";
				$retorno = RelatorioFinanceiroModel::boletosPagos($data_de, $data_ate);
				break;
			default:
				$titulo = "Boletos Emitidos";
				$retorno = RelatorioFinanceiroModel::boletosEmitidos($data_de, $data_ate);
				break;
		}

		$retorno = array_merge($retorno, array('titulo' => $titulo));

		$view = new View('views/relatorios-financeiros.php');
		$view->setParams($retorno);
		$view->showContents();
	}
}
