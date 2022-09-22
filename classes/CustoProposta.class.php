<?php
	class CustoProposta{
		private $id;
		private $proposta_id;
		private $status;
		private $quantidade;
		private $valor_padrao;
		private $valor_dje;
		private $valor_final;
		private $aceite;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getPropostaId(){
			return $this->proposta_id;
		}
		
		public function setPropostaId($proposta_id){
			$this->proposta_id = $proposta_id;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getQuantidade(){
			return $this->quantidade;
		}
		
		public function setQuantidade($quantidade){
			$this->quantidade = $quantidade;
		}
		
		public function getValorPadrao(){
			return $this->valor_padrao;
		}
		
		public function setValorPadrao($valor_padrao){
			$this->valor_padrao = $valor_padrao;
		}
		
		public function getValorDje(){
			return $this->valor_dje;
		}
		
		public function setValorDje($valor_dje){
			$this->valor_dje = $valor_dje;
		}
		
		public function getValorFinal(){
			return $this->valor_final;
		}
		
		public function setValorFinal($valor_final){
			$this->valor_final = $valor_final;
		}
		
		public function getAceite(){
			return $this->aceite;
		}
		
		public function setAceite($aceite){
			$this->aceite = $aceite;
		}
		
	}
?>