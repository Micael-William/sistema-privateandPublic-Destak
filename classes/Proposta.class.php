<?php
	class Proposta{
		private $id;
		private $data_entrada;		
		private $status;		
		private $data_envio;
		private $data_aceite;
		private $data_rejeicao;
		private $pendente;
		
		private $processo;
		private $resp_envio;
		private $resp_aceite;
		private $resp_rejeicao;
		
		private $usuario_envio_id;
		private $usuario_aceite_id;
		private $usuario_rejeicao_id;
		private $custos;
		private $observacoes;
        private $desc_obs = array();
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getProcesso(){
			return $this->processo;
		}
		
		public function setProcesso($processo){
			$this->processo = $processo instanceof Processo ? $processo : null;
		}
		
		public function getDataEntrada(){
			return $this->data_entrada;
		}
		
		public function setDataEntrada($data_entrada){
			$this->data_entrada = $data_entrada;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function getStatusDesc(){
			if($this->status == 'N') return 'Nova';
			else if($this->status == 'E') return 'Enviada';
			else if($this->status == 'A') return 'Aceita';
			else if($this->status == 'R') return 'Rejeitada';
		}
		
		public function setStatus($status){
			$this->status = $status;
		}	
		
		//*****************//
		
		public function getDataEnvio(){
			return $this->data_envio;
		}
		
		public function setDataEnvio($data_envio){
			$this->data_envio = $data_envio;
		}
		
		public function getDataAceite(){
			return $this->data_aceite;
		}
		
		public function setDataAceite($data_aceite){
			$this->data_aceite = $data_aceite;
		}	
		
		public function getDataRejeicao(){
			return $this->data_rejeicao;
		}
		
		public function setDataRejeicao($data_rejeicao){
			$this->data_rejeicao = $data_rejeicao;
		}	
				
		public function getPendente(){
			return $this->pendente;
		}
		
		public function setpendente($pendente){
			$this->pendente = $pendente;
		}	
				
		public function getUsuarioEnvioId(){
			return $this->usuario_envio_id;
		}
		
		public function setUsuarioEnvioId($usuario_envio_id){
			$this->usuario_envio_id = $usuario_envio_id;
		}			
		
		public function getUsuarioAceiteId(){
			return $this->usuario_aceite_id;
		}
		
		public function setUsuarioAceiteId($usuario_aceite_id){
			$this->usuario_aceite_id = $usuario_aceite_id;
		}	
		
		public function getUsuarioRejeicaoId(){
			return $this->usuario_rejeicao_id;
		}
		
		public function setUsuarioRejeicaoId($usuario_rejeicao_id){
			$this->usuario_rejeicao_id = $usuario_rejeicao_id;
		}	
		
		public function getNomeRespAceite(){
			return $this->resp_aceite;
		}
		
		public function setNomeRespAceite($resp_aceite){
			$this->resp_aceite = $resp_aceite;
		}	
		
		public function getNomeRespEnvio(){
			return $this->resp_envio;
		}
		
		public function setNomeRespEnvio($resp_envio){
			$this->resp_envio = $resp_envio;
		}	
		
		public function getNomeRespRejeicao(){
			return $this->resp_rejeicao;
		}
		
		public function setNomeRespRejeicao($resp_rejeicao){
			$this->resp_rejeicao = $resp_rejeicao;
		}
			
		//****************//
		
		public function getCustos() {
			return $this->custos;
		}		
	
		public function setCustos( $custos ) {
			$this->custos = null;
			
			if (is_array( $custos )) {
				$isCusto = true;
				
				foreach ($custos as $c){
					if (!$c instanceof CustoProposta) {
						$isCusto = false;
						break;
					}
				}
				
				if ($isCusto)
					$this->custos = $custos;
			}
		}
		
		public function setCusto( $custo ){
			if (!isset($this->custos) or is_null($this->custos))
				$this->custos = array();
	
			$this->custos[] = $custo;
		}
		
		public function getObservacoes() {
			return $this->observacoes;
		}		
	
		public function setObservacoes( $observacoes ) {
			$this->observacoes = null;
			
			if (is_array( $observacoes )) {
				$isObs = true;
				
				foreach ($observacoes as $ob){
					if (!$ob instanceof Observacao) {
						$isObs = false;
						break;
					}
				}
				
				if ($isObs)
					$this->observacoes = $observacoes;
			}
		}
		
		public function setObservacao( $observacao ){
			if (!isset($this->observacoes) or is_null($this->observacoes))
				$this->observacoes = array();
	
			$this->observacoes[] = $observacao;
		}
                
		public function setDescObs( $observacoes ){
			$this->desc_obs = $observacoes;
		}
                
        public function getDescObs(){
			return $this->desc_obs;
		}
		
	}
?>