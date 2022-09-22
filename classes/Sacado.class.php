<?php
class Sacado
{
	private $id;
	private $nome;
	private $status;
	private $cpf_cnpj;
	private $data_entrada;
	private $data_alteracao;

	private $endereco;
	private $usuario;

	private $emails;
	private $acompanhamentos;

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

	public function getCpfCnpj()
	{
		return $this->cpf_cnpj;
	}

	public function setCpfCnpj($cpf_cnpj)
	{
		$this->cpf_cnpj = $cpf_cnpj;
	}

	public function setEmail($email)
	{
		if (!isset($this->emails) or is_null($this->emails))
			$this->emails = array();

		$this->emails[] = $email;
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
}
