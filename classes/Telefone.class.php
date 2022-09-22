<?php
	class Telefone{
		private $id;
		private $ddd;
		private $numero;	
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getDdd(){
			return $this->ddd;
		}
		
		public function setDdd($ddd){
			$this->ddd = $ddd;
		}
		
		public function getNumero(){
			return $this->numero;
		}
		
		public function setNumero($numero){
			$this->numero = $numero;
		}	
		
	}
?>