<?php
	class PesquisaSecretaria{
		private $status;
		private $estado;
		private $termo;
		private $pagina;
		
		function __construct() {
			$this->status = null;
			$this->estado = null;
			$this->termo = null;
			$this->pagina = null;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getEstado(){
			return $this->estado;
		}
		
		public function setEstado($estado){
			$this->estado = $estado;
		}		
		
		public function getTermo(){
			return $this->termo;
		}
		
		public function setTermo($termo){
			$this->termo = $termo;
		}
		
		public function getPagina(){
			return $this->pagina;
		}
		
		public function setPagina($pagina){
			$this->pagina = $pagina;
		}
	}
?>