<?php
class Advogado
{
	private $id;
	private $nome;
	private $oab;
	private $status;
	private $empresa;
	private $cnpj;
	private $site;
	private $nome_contato;
	private $email_contato;
	private $data_entrada;
	private $data_alteracao;

	private $endereco;
	private $usuario;

	private $emails;
	private $observacoes;
	private $propostas;
	private $acompanhamentos;
	private $telefones;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getNome()
	{
		return $this->nome;
	}

	public function setNome($nome)
	{
		$this->nome = $nome;
	}

	public function getOab()
	{
		return $this->oab;
	}

	public function setOab($oab)
	{
		$this->oab = $oab;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getStatusDesc()
	{
		if ($this->status == 'S') return 'Cliente';
		if ($this->status == 'N') return 'Não Cliente';
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getEmpresa()
	{
		return $this->empresa;
	}

	public function setEmpresa($empresa)
	{
		$this->empresa = $empresa;
	}

	public function getCnpj()
	{
		return $this->cnpj;
	}

	public function setCnpj($cnpj)
	{
		$this->cnpj = $cnpj;
	}

	public function getSite()
	{
		return $this->site;
	}

	public function setSite($site)
	{
		$this->site = $site;
	}

	public function getNomeContato()
	{
		return $this->nome_contato;
	}

	public function setNomeContato($nome_contato)
	{
		$this->nome_contato = $nome_contato;
	}

	public function getEmailContato()
	{
		return $this->email_contato;
	}

	public function setEmailContato($email_contato)
	{
		$this->email_contato = $email_contato;
	}

	public function getDataEntrada()
	{
		return $this->data_entrada;
	}

	public function setDataEntrada($data_entrada)
	{
		$this->data_entrada = $data_entrada;
	}

	public function getDataAlteracao()
	{
		return $this->data_alteracao;
	}

	public function setDataAlteracao($data_alteracao)
	{
		$this->data_alteracao = $data_alteracao;
	}

	public function getUsuario()
	{
		return $this->usuario;
	}

	public function setUsuario($usuario)
	{
		$this->usuario = $usuario instanceof Usuario ? $usuario : null;
	}

	public function getEndereco()
	{
		return $this->endereco;
	}

	public function setEndereco($endereco)
	{
		$this->endereco = $endereco instanceof Endereco ? $endereco : null;
	}

	public function getStrEmailsEnviar()
	{

		$strEmailsEnviar = '';
		$arrTmp = array();

		if (is_array($this->emails)) {
			foreach ($this->emails as $e) {
				if (($e instanceof Email) && ($e->getEnviar() == 'S')) {
					$arrTmp[] = $e->getEmailEndereco();
				}
			}
		}

		$arrTmp = array_unique($arrTmp);
		$strEmailsEnviar = implode("; ", $arrTmp);

		return $strEmailsEnviar;
	}

	public function getEmails()
	{
		return $this->emails;
	}

	public function setEmails($emails)
	{
		$this->emails = null;

		if (is_array($emails)) {
			$isEmail = true;

			foreach ($emails as $e) {
				if (!$e instanceof Email) {
					$isEmail = false;
					break;
				}
			}

			if ($isEmail)
				$this->emails = $emails;
		}
	}

	public function setEmail($email)
	{
		if (!isset($this->emails) or is_null($this->emails))
			$this->emails = array();

		$this->emails[] = $email;
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

	public function setObservacao($observacao)
	{
		if (!isset($this->observacoes) or is_null($this->observacoes))
			$this->observacoes = array();

		$this->observacoes[] = $observacao;
	}

	public function getPropostas()
	{
		return $this->propostas;
	}

	public function setPropostas($propostas)
	{
		$this->propostas = null;

		if (is_array($propostas)) {
			$isProposta = true;

			foreach ($propostas as $p) {
				if (!$p instanceof Proposta) {
					$isProposta = false;
					break;
				}
			}

			if ($isProposta)
				$this->propostas = $propostas;
		}
	}

	public function setProposta($proposta)
	{
		if (!isset($this->propostas) or is_null($this->propostas))
			$this->propostas = array();

		$this->propostas[] = $proposta;
	}

	public function getAcompanhamentos()
	{
		return $this->acompanhamentos;
	}

	public function setAcompanhamentos($acompanhamentos)
	{
		$this->acompanhamentos = null;

		if (is_array($acompanhamentos)) {
			$isAcomp = true;

			foreach ($acompanhamentos as $ac) {
				if (!$ac instanceof Acompanhamento) {
					$isAcomp = false;
					break;
				}
			}

			if ($isAcomp)
				$this->acompanhamentos = $acompanhamentos;
		}
	}

	public function setAcompanhamento($acompanhamento)
	{
		if (!isset($this->acompanhamentos) or is_null($this->acompanhamentos))
			$this->acompanhamentos = array();

		$this->acompanhamentos[] = $acompanhamento;
	}







	public function getTelefones()
	{
		return $this->telefones;
	}

	public function setTelefones($telefones)
	{
		$this->telefones = null;

		if (is_array($telefones)) {
			$isTel = true;

			foreach ($telefones as $t) {
				if (!$t instanceof Telefone) {
					$isTel = false;
					break;
				}
			}

			if ($isTel)
				$this->telefones = $telefones;
		}
	}

	public function setTelefone($telefone)
	{
		if (!isset($this->telefones) or is_null($this->telefones))
			$this->telefones = array();

		$this->telefones[] = $telefone;
	}
}
