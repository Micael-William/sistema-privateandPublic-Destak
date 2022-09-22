<?php
	class CustoAcompanhamento{
		private $id;
		private $acompanhamento_id;
		private $quantidade_padrao;
		private $quantidade_dje;
		private $valor_padrao;
		private $valor_dje;
		private $valor_final;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getAcompanhamentoId(){
			return $this->acompanhamento_id;
		}
		
		public function setAcompanhamentoId($acompanhamento_id){
			$this->acompanhamento_id = $acompanhamento_id;
		}
				
		public function getQuantidadePadrao(){
			return $this->quantidade_padrao;
		}
		
		public function setQuantidadePadrao($quantidade_padrao){
			$this->quantidade_padrao = $quantidade_padrao;
		}
		
		public function getQuantidadeDje(){
			return $this->quantidade_dje;
		}
		
		public function setQuantidadeDje($quantidade_dje){
			$this->quantidade_dje = $quantidade_dje;
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
	}
?>