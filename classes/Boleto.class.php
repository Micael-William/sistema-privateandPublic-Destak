<?php

class Boleto
{
    private $id;
    private $acompanhamentoId;
    private $sacadoId;
    private $iuguInvoice;
    private $iuguValor;
    private $iuguUrl;
    private $iuguBoleto;
    private $iuguVencimento;
    private $iuguStatus;
    private $iuguRequest;
    private $dataEntrada;
    private $dataAlteracao;

    private $usuario;
    private $acompanhamento;
    private $observacoes;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of acompanhamentoId
     */
    public function getAcompanhamentoId()
    {
        return $this->acompanhamentoId;
    }

    /**
     * Set the value of acompanhamentoId
     *
     * @return  self
     */
    public function setAcompanhamentoId($acompanhamentoId)
    {
        $this->acompanhamentoId = $acompanhamentoId;

        return $this;
    }

    /**
     * Get the value of sacadoId
     */
    public function getSacadoId()
    {
        return $this->sacadoId;
    }

    /**
     * Set the value of sacadoId
     *
     * @return  self
     */
    public function setSacadoId($sacadoId)
    {
        $this->sacadoId = $sacadoId;

        return $this;
    }

    /**
     * Get the value of iuguInvoice
     */
    public function getIuguInvoice()
    {
        return $this->iuguInvoice;
    }

    /**
     * Set the value of iuguInvoice
     *
     * @return  self
     */
    public function setIuguInvoice($iuguInvoice)
    {
        $this->iuguInvoice = $iuguInvoice;

        return $this;
    }

    /**
     * Get the value of iuguUrl
     */
    public function getIuguUrl()
    {
        return $this->iuguUrl;
    }

    /**
     * Set the value of iuguUrl
     *
     * @return  self
     */
    public function setIuguUrl($iuguUrl)
    {
        $this->iuguUrl = $iuguUrl;

        return $this;
    }

    /**
     * Get the value of iuguBoleto
     */
    public function getIuguBoleto()
    {
        return $this->iuguBoleto;
    }

    /**
     * Set the value of iuguBoleto
     *
     * @return  self
     */
    public function setIuguBoleto($iuguBoleto)
    {
        $this->iuguBoleto = $iuguBoleto;

        return $this;
    }

    /**
     * Get the value of iuguVencimento
     */
    public function getIuguVencimento()
    {
        return $this->iuguVencimento;
    }

    /**
     * Set the value of iuguVencimento
     *
     * @return  self
     */
    public function setIuguVencimento($iuguVencimento)
    {
        $this->iuguVencimento = $iuguVencimento;

        return $this;
    }

    /**
     * Get the value of iuguStatus
     */
    public function getIuguStatus()
    {
        return $this->iuguStatus;
    }

    /**
     * Set the value of iuguStatus
     *
     * @return  self
     */
    public function setIuguStatus($iuguStatus)
    {
        $this->iuguStatus = $iuguStatus;

        return $this;
    }

    /**
     * Get the value of iuguRequest
     */
    public function getIuguRequest()
    {
        return $this->iuguRequest;
    }

    /**
     * Set the value of iuguRequest
     *
     * @return  self
     */
    public function setIuguRequest($iuguRequest)
    {
        $this->iuguRequest = $iuguRequest;

        return $this;
    }

    /**
     * Get the value of dataEntrada
     */
    public function getDataEntrada()
    {
        return $this->dataEntrada;
    }

    /**
     * Set the value of dataEntrada
     *
     * @return  self
     */
    public function setDataEntrada($dataEntrada)
    {
        $this->dataEntrada = $dataEntrada;

        return $this;
    }

    /**
     * Get the value of dataAlteracao
     */
    public function getDataAlteracao()
    {
        return $this->dataAlteracao;
    }

    /**
     * Set the value of dataAlteracao
     *
     * @return  self
     */
    public function setDataAlteracao($dataAlteracao)
    {
        $this->dataAlteracao = $dataAlteracao;

        return $this;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario instanceof Usuario ? $usuario : null;
    }

    public function getAcompanhamento()
    {
        return $this->acompanhamento;
    }

    public function setAcompanhamento($acompanhamento)
    {
        $this->acompanhamento = $acompanhamento instanceof Acompanhamento ? $acompanhamento : null;
    }

    /**
     * Get the value of iuguStatusDesc
     */
    public function getIuguStatusDesc()
    {
        $statuses = self::getIuguStatuses();

        return key_exists($this->iuguStatus, $statuses) ? $statuses[$this->iuguStatus] : $statuses['pending'];
    }

    public function getPodeCancelar()
    {
        $statusPodeCancelar = array('processing', 'overdue', 'pending');

        return (in_array($this->iuguStatus, $statusPodeCancelar));
    }

    /**
     * Get the value of iuguValor
     */
    public function getIuguValor()
    {
        return $this->iuguValor;
    }

    /**
     * Set the value of iuguValor
     *
     * @return  self
     */
    public function setIuguValor($iuguValor)
    {
        $this->iuguValor = $iuguValor;

        return $this;
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

    public static function getIuguStatuses()
    {
        return array(
            'processing'         => 'Processando',
            // 'accepted' 			=> 'Aceita',
            // 'rejected' 			=> 'Rejeitada',
            // 'in_analysis' 		=> 'Em Análise',
            // 'chargeback' 		=> 'Contestada',
            // 'in_protest' 		=> 'Em Protesto',
            'overdue'            => 'Vencido',
            'expired'            => 'Expirado',
            // 'refunded' 			=> 'Reembolsada',
            // 'partially_paid'	=> 'Parcialmente Paga',
            // 'draft'				=> 'Rascunho',
            'canceled'           => 'Cancelada',
            'paid'               => 'Paga',
            'pending'            => 'A vencer',
        );
    }
}
