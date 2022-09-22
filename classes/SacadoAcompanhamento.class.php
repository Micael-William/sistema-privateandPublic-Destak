<?php
class SacadoAcompanhamento
{
	private $acompanhamento_id;
	private $sacado_id;
	private $nome_sacado;
	private $cpf_cnpj;
	private $data_entrada;
	private $data_alteracao;
	private $endereco;
	private $usuario;
	private $emails;

	public function getAcompanhamentoId()
	{
		return $this->acompanhamento_id;
	}

	public function setAcompanhamentoId($acompanhamento_id)
	{
		$this->acompanhamento_id = $acompanhamento_id;
	}


	/**
	 * Get the value of sacado_id
	 */
	public function getSacadoId()
	{
		return $this->sacado_id;
	}

	/**
	 * Set the value of sacado_id
	 *
	 * @return  self
	 */
	public function setSacadoId($sacado_id)
	{
		$this->sacado_id = $sacado_id;
	}

	/**
	 * Get the value of data_entrada
	 */
	public function getDataEntrada()
	{
		return $this->data_entrada;
	}

	/**
	 * Set the value of data_entrada
	 *
	 * @return  self
	 */
	public function setDataEntrada($data_entrada)
	{
		$this->data_entrada = $data_entrada;
	}

	/**
	 * Get the value of data_alteracao
	 */
	public function getDataAlteracao()
	{
		return $this->data_alteracao;
	}

	/**
	 * Set the value of data_alteracao
	 *
	 * @return  self
	 */
	public function setDataAlteracao($data_alteracao)
	{
		$this->data_alteracao = $data_alteracao;
	}

	/**
	 * Get the value of endereco
	 */
	public function getEndereco()
	{
		return $this->endereco;
	}

	/**
	 * Set the value of endereco
	 *
	 * @return  self
	 */
	public function setEndereco($endereco)
	{
		$this->endereco = $endereco instanceof Endereco ? $endereco : null;;
	}

	public function getUsuario()
	{
		return $this->usuario;
	}

	public function setUsuario($usuario)
	{
		$this->usuario = $usuario instanceof Usuario ? $usuario : null;
	}


	/**
	 * Get the value of nome_sacado
	 */
	public function getNomeSacado()
	{
		return $this->nome_sacado;
	}

	/**
	 * Set the value of nome_sacado
	 *
	 * @return  self
	 */
	public function setNomeSacado($nome_sacado)
	{
		$this->nome_sacado = $nome_sacado;
	}

	/**
	 * Get the value of cpf_cnpj
	 */
	public function getCpfCnpj()
	{
		return $this->cpf_cnpj;
	}

	/**
	 * Set the value of cpf_cnpj
	 *
	 * @return  self
	 */
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

	public function getPodeEmitir()
	{
		$sacado = $this;

		if (empty($sacado))
			return false;


		$emails = $this->getEmails();

		if (empty($emails) || sizeof($emails) == 0)
			return false;

		$endereco = $sacado->getEndereco();

		if (empty($endereco))
			return false;

		return !empty($sacado->getCpfCnpj())
			&& !empty($sacado->getNomeSacado())
			&& !empty($endereco->getLogradouro())
			&& !empty($endereco->getNumero())
			&& !empty($endereco->getBairro())
			&& !empty($endereco->getCidade())
			&& !empty($endereco->getEstado())
			&& !empty($endereco->getCep());
	}
}
