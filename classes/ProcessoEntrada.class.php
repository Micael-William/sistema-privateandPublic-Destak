<?php
class ProcessoEntrada
{
	private $id;
	private $data_processo;
	private $numero;
	private $conteudo;
	private $estado;
	private $arquivo;

	//regras encontradas no arquivo
	private $termo_inicio;
	private $termo_fim;
	private $intervalo;

	//nomes e não objetos
	private $secretaria;
	private $advogado;

	private $qtd_homonimo;

	//informações apenas cadastradas sem utlidade para o sistema
	private $jornal;
	private $trubunal;
	private $nome_pesquisado;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getDataProcesso()
	{
		return $this->data_processo;
	}

	public function setDataProcesso($data_processo)
	{
		$this->data_processo = $data_processo;
	}

	public function getSecretaria()
	{
		return $this->secretaria;
	}

	public function setSecretaria($secretaria)
	{
		$this->secretaria = $secretaria;
	}

	public function getAdvogado()
	{
		return $this->advogado;
	}

	public function setAdvogado($advogado)
	{
		$this->advogado = $advogado;
	}

	public function getNumero()
	{
		return $this->numero;
	}

	public function setNumero($numero)
	{
		$this->numero = $numero;
	}

	public function getConteudo()
	{
		return $this->conteudo;
	}

	public function setConteudo($conteudo)
	{
		$this->conteudo = $conteudo;
	}

	public function getEstado()
	{
		return $this->estado;
	}

	public function setEstado($estado)
	{
		$this->estado = $estado;
	}

	public function getArquivo()
	{
		return $this->arquivo;
	}

	public function setArquivo($arquivo)
	{
		$this->arquivo = $arquivo;
	}

	public function getJornal()
	{
		return $this->jornal;
	}

	public function setJornal($jornal)
	{
		$this->jornal = $jornal;
	}

	public function getTribunal()
	{
		return $this->tribunal;
	}

	public function setTribunal($tribunal)
	{
		$this->tribunal = $tribunal;
	}

	public function getNomePesquisado()
	{
		return $this->nome_pesquisado;
	}

	public function setNomePesquisado($nome_pesquisado)
	{
		$this->nome_pesquisado = $nome_pesquisado;
	}

	public function getTermoInicio()
	{
		return $this->termo_inicio;
	}

	public function setTermoInicio($termo_inicio)
	{
		$this->termo_inicio = $termo_inicio;
	}

	public function getTermoFim()
	{
		return $this->termo_fim;
	}

	public function setTermoFim($termo_fim)
	{
		$this->termo_fim = $termo_fim;
	}

	public function getIntervalo()
	{
		return $this->intervalo;
	}

	public function setIntervalo($intervalo)
	{
		$this->intervalo = $intervalo;
	}

	//advogados homonimos
	public function getQtdHomonimo()
	{
		return $this->qtd_homonimo;
	}

	public function setQtdHomonimo($qtd_homonimo)
	{
		$this->qtd_homonimo = $qtd_homonimo;
	}
}
