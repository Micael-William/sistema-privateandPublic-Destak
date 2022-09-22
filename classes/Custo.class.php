<?php
	class Custo{
		private $jornal_id;
		private $medida;
		private $valor_forense;
		private $negociacao;
		private $desconto;
		private $valor_padrao;
		private $valor_dje;
		private $valor_empregos;
		private $valor_publicidade;
		
		public function getJornalId(){
			return $this->jornal_id;
		}
		
		public function setJornalId($jornal_id){
			$this->jornal_id = $jornal_id;
		}
		
		public function getMedida(){
			return $this->medida;
		}
		
		public function setMedida($medida){
			$this->medida = $medida;
		}
		
		public function getValorForense(){
			return $this->valor_forense;
		}
		
		public function setValorForense($valor_forense){
			$this->valor_forense = $valor_forense;
		}
		
		public function getNegociacao(){
			return $this->negociacao;
		}
		
		public function setNegociacao($negociacao){
			$this->negociacao = $negociacao;
		}
		
		public function getDesconto(){
			return $this->desconto;
		}
		
		public function setDesconto($desconto){
			$this->desconto = $desconto;
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
		
		public function getValorEmpregos(){
			return $this->valor_empregos;
		}
		
		public function setValorEmpregos($valor_empregos){
			$this->valor_empregos = $valor_empregos;
		}
		
		public function getValorPublicidade(){
			return $this->valor_publicidade;
		}
		
		public function setValorPublicidade($valor_publicidade){
			$this->valor_publicidade = $valor_publicidade;
		}
		
	}
?>