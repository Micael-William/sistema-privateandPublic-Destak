<?php
	class AcompanhamentoStatus{
		private $id;
		private $parent_id;
		private $codigo;
		private $nome_status;
		private $nome_status_pai;
		private $descricao;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getStatus(){
			return $this->nome_status;
		}
		
		public function setStatus($nome_status){
			$this->nome_status = $nome_status;
		}
		
		public function getStatusPai(){
			return $this->nome_status_pai;
		}
		
		public function setStatusPai($nome_status_pai){
			$this->nome_status_pai = $nome_status_pai;
		}
		
		public function getParentId(){
			return $this->parent_id;
		}
		
		public function setParentId($parent_id){
			$this->parent_id = $parent_id;
		}
		
		public function getCodigo(){
			return $this->codigo;
		}
		
		public function setCodigo($codigo){
			$this->codigo = $codigo;
		}
		
		public function getDescricao(){
			return $this->descricao;
		}
		
		public function setDescricao($descricao){
			$this->descricao = $descricao;
		}
		
	}
?>