<?php
	class Cidade{
		private $id;
		private $nome;
		private $jornal_id;
				
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getNome(){
			return $this->nome;
		}
		
		public function setNome($nome){
			$this->nome = $nome;
		}
		
		public function getJornalId(){
			return $this->jornal_id;
		}
		
		public function setJornalId($jornal_id){
			$this->jornal_id = $jornal_id;
		}		
				
	}
?>