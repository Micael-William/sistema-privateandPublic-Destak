<?php
class Acompanhamento
{
	private $id;
	private $data_entrada;
	private $status = null;
	private $status_desc = null;
	private $substatus = null;
	private $substatus_desc = null;
	private $data_conclusao;

	//envio peticoes
	private $envio_autorizacao;
	private $envio_comprovante;
	private $envio_guia;
	private $envio_minuta;

	//geracao peticoes
	private $gera_autorizacao;
	private $gera_comprovante;
	private $gera_guia;
	private $gera_minuta;

	private $proposta;
	private $custo;
	private $sacado;

	//observacaes do advogado
	private $observacoes;

	//observacoes financeiro
	private $observacoes_financeiro;

	//obs do acompanhamento
	private $observacoes_acompanhamento;
	private $desc_obs = array();
	private $email_dest = array();

	private $boleto_em_aberto;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getEnvioAutorizacao()
	{
		return $this->envio_autorizacao;
	}

	public function setEnvioAutorizacao($envio_autorizacao)
	{
		$this->envio_autorizacao = $envio_autorizacao;
	}

	public function getEnvioComprovante()
	{
		return $this->envio_comprovante;
	}

	public function setEnvioComprovante($envio_comprovante)
	{
		$this->envio_comprovante = $envio_comprovante;
	}

	public function getEnvioGuia()
	{
		return $this->envio_guia;
	}

	public function setEnvioGuia($envio_guia)
	{
		$this->envio_guia = $envio_guia;
	}

	public function getEnvioMinuta()
	{
		return $this->envio_minuta;
	}

	public function setEnvioMinuta($envio_minuta)
	{
		$this->envio_minuta = $envio_minuta;
	}

	public function getGeraAutorizacao()
	{
		return $this->gera_autorizacao;
	}

	public function setGeraAutorizacao($gera_autorizacao)
	{
		$this->gera_autorizacao = $gera_autorizacao;
	}

	public function getGeraComprovante()
	{
		return $this->gera_comprovante;
	}

	public function setGeraComprovante($gera_comprovante)
	{
		$this->gera_comprovante = $gera_comprovante;
	}

	public function getGeraGuia()
	{
		return $this->gera_guia;
	}

	public function setGeraGuia($gera_guia)
	{
		$this->gera_guia = $gera_guia;
	}

	public function getGeraMinuta()
	{
		return $this->gera_minuta;
	}

	public function setGeraMinuta($gera_minuta)
	{
		$this->gera_minuta = $gera_minuta;
	}

	public function getProposta()
	{
		return $this->proposta;
	}

	public function setProposta($proposta)
	{
		$this->proposta = $proposta instanceof Proposta ? $proposta : null;
	}

	public function getDataEntrada()
	{
		return $this->data_entrada;
	}

	public function setDataEntrada($data_entrada)
	{
		$this->data_entrada = $data_entrada;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	//public function getStatusDesc(){
	//	if($this->status == 'E') return 'Em Andamento';
	//	else if($this->status == 'C') return 'Concluída';
	//}

	public function getStatusDesc()
	{
		return $this->status_desc;
	}

	public function setStatusDesc($status_desc)
	{
		$this->status_desc = $status_desc;
	}

	public function getSubStatus()
	{
		return $this->substatus;
	}

	public function setSubStatus($substatus)
	{
		$this->substatus = $substatus;
	}

	public function getSubStatusDesc()
	{
		return $this->substatus_desc;
	}

	public function setSubStatusDesc($substatus_desc)
	{
		$this->substatus_desc = $substatus_desc;
	}

	public function getDataConclusao()
	{
		return $this->data_conclusao;
	}

	public function setDataConclusao($data_conclusao)
	{
		$this->data_conclusao = $data_conclusao;
	}

	//****************//

	public function getObservacoes()
	{
		return $this->observacoes;
	}

	public function setObservacoes($observacoes)
	{
		$this->observacoes = null;

		if (is_array($observacoes)) {
			$isObs = true;

			foreach ($observacoes as $ob) {
				if (!$ob instanceof Observacao) {
					$isObs = false;
					break;
				}
			}

			if ($isObs)
				$this->observacoes = $observacoes;
		}
	}

	public function setObservacao($observacao)
	{
		if (!isset($this->observacoes) or is_null($this->observacoes))
			$this->observacoes = array();

		$this->observacoes[] = $observacao;
	}

	public function getObservacoesFinanceiro()
	{
		return $this->observacoes_financeiro;
	}

	public function setObservacoesFinanceiro($observacoes)
	{
		$this->observacoes_financeiro = null;

		if (is_array($observacoes)) {
			$isObs = true;

			foreach ($observacoes as $ob) {
				if (!$ob instanceof Observacao) {
					$isObs = false;
					break;
				}
			}

			if ($isObs)
				$this->observacoes_financeiro = $observacoes;
		}
	}

	public function setObservacaoFinanceiro($observacao)
	{
		if (!isset($this->observacoes_financeiro) or is_null($this->observacoes_financeiro))
			$this->observacoes_financeiro = array();

		$this->observacoes_financeiro[] = $observacao;
	}

	public function getObservacoesAcompanhamento()
	{
		return $this->observacoes_acompanhamento;
	}

	public function setObservacoesAcompanhamento($observacoes_acompanhamento)
	{
		$this->observacoes_acompanhamento = null;

		if (is_array($observacoes_acompanhamento)) {
			$isObs = true;

			foreach ($observacoes_acompanhamento as $ob) {
				if (!$ob instanceof Observacao) {
					$isObs = false;
					break;
				}
			}

			if ($isObs)
				$this->observacoes_acompanhamento = $observacoes_acompanhamento;
		}
	}

	public function setObservacaoAcompanhamento($observacao_acompanhamento)
	{
		if (!isset($this->observacoes_acompanhamento) or is_null($this->observacoes_acompanhamento))
			$this->observacoes_acompanhamento = array();

		$this->observacoes_acompanhamento[] = $observacao_acompanhamento;
	}

	public function getCusto()
	{
		return $this->custo;
	}

	public function setCusto($custo)
	{
		$this->custo = $custo instanceof CustoAcompanhamento ? $custo : null;
	}

	public function getSacado()
	{
		return $this->sacado;
	}

	public function setSacado($sacado)
	{
		$this->sacado = $sacado instanceof SacadoAcompanhamento ? $sacado : null;
	}

	public function setDescObs($observacoes)
	{
		$this->desc_obs = $observacoes;
	}

	public function getDescObs()
	{
		return $this->desc_obs;
	}

	public function setAndamentoEmailDest($email_dest)
	{
		$this->email_dest = $email_dest;
	}

	public function getAndamentoEmailDest()
	{
		return $this->email_dest;
	}

	public function getBoletoEmAberto()
	{
		return $this->boleto_em_aberto;
	}

	public function setBoletoEmAberto($boleto_em_aberto)
	{
		$this->boleto_em_aberto = $boleto_em_aberto;

		return $this;
	}
}
