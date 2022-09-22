<?php
	class Secretaria{
		private $id;
		private $nome;
		private $estado;
		private $status;
		
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
		
		public function getEstado(){
			return $this->estado;
		}
		
		public function setEstado($estado){
			$this->estado = $estado;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function getStatusDesc(){
			if($this->status == 'A') return 'Ativo';
			if($this->status == 'I') return 'Inativo';
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
				
	}
?>