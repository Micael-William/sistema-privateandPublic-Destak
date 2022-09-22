<?php
	class Endereco{
		private $logradouro;
		private $numero;	
		private $complemento;
		private $bairro;
		private $cidade;
		private $cep;
		private $estado;
		
		public function getLogradouro(){
			return $this->logradouro;
		}
		
		public function setLogradouro($logradouro){
			$this->logradouro = $logradouro;
		}
		
		public function getNumero(){
			return $this->numero;
		}
		
		public function setNumero($numero){
			$this->numero = $numero;
		}
		
		public function getComplemento(){
			return $this->complemento;
		}
		
		public function setComplemento($complemento){
			$this->complemento = $complemento;
		}	
		
		public function getBairro(){
			return $this->bairro;
		}
		
		public function setBairro($bairro){
			$this->bairro = $bairro;
		}
		
		public function getCidade(){
			return $this->cidade;
		}
		
		public function setCidade($cidade){
			$this->cidade = $cidade;
		}
		
		public function getCep() {
			return $this->cep;
		}	
		
		public function setCep($cep){
			$this->cep = $cep;
		}
		
		public function getEstado() {
			return $this->estado;
		}	
		
		public function setEstado($estado){
			$this->estado = $estado;
		}
	}
?>