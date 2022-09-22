<?php
class Processo
{
	private $id;
	private $data_entrada;
	private $sinalizador;
	private $secretaria;
	private $advogado;
	private $jornal;
	private $requerente;
	private $requerido;

	//apenas pra SP
	private $acao;

	private $entrada;
	private $alertas;
	private $qtd_alertas;
	//Processos de mesmo numero
	private $repetidos;
	private $observacoes;
	private $observacoes_advogado;
	private $desc_obs = array();
	private $desc_obs_advogado = array();

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getDataEntrada()
	{
		return $this->data_entrada;
	}

	public function setDataEntrada($data_entrada)
	{
		$this->data_entrada = $data_entrada;
	}

	public function getSinalizador()
	{
		return $this->sinalizador;
	}

	public function setSinalizador($sinalizador)
	{
		$this->sinalizador = $sinalizador;
	}

	public function getSecretaria()
	{
		return $this->secretaria;
	}

	public function setSecretaria($secretaria)
	{
		$this->secretaria = $secretaria instanceof Secretaria ? $secretaria : null;
	}

	public function getAdvogado()
	{
		return $this->advogado;
	}

	public function setAdvogado($advogado)
	{
		$this->advogado = $advogado instanceof Advogado ? $advogado : null;
	}

	public function getJornal()
	{
		return $this->jornal;
	}

	public function setJornal($jornal)
	{
		$this->jornal = $jornal instanceof Jornal ? $jornal : null;
	}

	public function getRequerente()
	{
		return $this->requerente;
	}

	public function setRequerente($requerente)
	{

		if (strlen($requerente) > 300)
			$requerente = substr($requerente, 0, 300);

		$this->requerente = $requerente;
	}

	public function getRequerido()
	{
		return $this->requerido;
	}

	public function setRequerido($requerido)
	{

		if (strlen($requerido) > 300)
			$requerido = substr($requerido, 0, 300);

		$this->requerido = $requerido;
	}

	public function getAcao()
	{
		return $this->acao;
	}

	public function setAcao($acao)
	{
		if (strlen($acao) > 300)
			$acao = substr($acao, 0, 300);

		$this->acao = $acao;
	}

	public function getEntrada()
	{
		return $this->entrada;
	}

	public function setEntrada($entrada)
	{
		$this->entrada = $entrada instanceof ProcessoEntrada ? $entrada : null;
	}

	public function getAlertas()
	{
		return $this->alertas;
	}

	public function setAlertas($alertas)
	{
		$this->alertas = null;

		if (is_array($alertas))
			$this->alertas = $alertas;
	}

	public function getQtdAlertas()
	{
		return $this->qtd_alertas;
	}

	public function setQtdAlertas($qtd_alertas)
	{
		$this->qtd_alertas = $qtd_alertas;
	}

	public function setAlerta($alerta)
	{
		if (!isset($this->alertas) or is_null($this->alertas))
			$this->alertas = array();

		$this->alertas[] = $alerta;
	}

	public function getRepetidos()
	{
		return $this->repetidos;
	}

	public function setRepetidos($repetidos)
	{
		$this->repetidos = null;

		if (is_array($repetidos)) {
			$isRep = true;

			foreach ($repetidos as $r) {
				if (!$r instanceof ProcessoRepetido) {
					$isRep = false;
					break;
				}
			}

			if ($isRep)
				$this->repetidos = $repetidos;
		}
	}

	public function setRepetido($repetido)
	{
		if (!isset($this->repetidos) or is_null($this->repetidos))
			$this->repetidos = array();

		$this->repetidos[] = $repetido;
	}

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

	public function getObservacoesAdvogado()
	{
		return $this->observacoes_advogado;
	}

	public function setObservacoesAdvogado($observacoes_advogado)
	{
		$this->observacoes_advogado = null;

		if (is_array($observacoes_advogado)) {
			$isObs = true;

			foreach ($observacoes_advogado as $ob) {
				if (!$ob instanceof Observacao) {
					$isObs = false;
					break;
				}
			}

			if ($isObs)
				$this->observacoes_advogado = $observacoes_advogado;
		}
	}

	public function setObservacao($observacao)
	{
		if (!isset($this->observacoes) or is_null($this->observacoes))
			$this->observacoes = array();

		$this->observacoes[] = $observacao;
	}

	public function setDescObs($observacoes)
	{
		$this->desc_obs = $observacoes;
	}

	public function getDescObs()
	{
		return $this->desc_obs;
	}
}
