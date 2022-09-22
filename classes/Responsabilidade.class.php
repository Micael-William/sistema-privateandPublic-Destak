<?php
	class Responsabilidade{
		private $id;
		private $nome;	
		private $acao;	
		
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
		
		public function getAcao(){
			return $this->acao;
		}
		
		public function setAcao($acao){
			$this->acao = $acao;
		}
		
	}
?>