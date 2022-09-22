<?php
	class PesquisaJornal{
		private $status;
		private $ativo;
		private $estado;
		private $secretaria_id;
		private $nome;
		private $representante;
		private $endereco;
		private $cidade;
		private $email;
		private $telefone;
		private $pagina;
		
		function __construct() {
			$this->status = null;
			$this->ativo = null;
			$this->estado = null;
			$this->secretaria_id = 0;
			$this->nome = null;
			$this->representante = null;
			$this->endereco = null;
			$this->cidade = null;
			$this->email = null;
			$this->telefone = null;
			$this->pagina = null;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getAtivo(){
			return $this->ativo;
		}
		
		public function setAtivo($ativo){
			$this->ativo = $ativo;
		}
		
		public function getEstado(){
			return $this->estado;
		}
		
		public function setEstado($estado){
			$this->estado = $estado;
		}	
		
		public function getSecretariaId(){
			return $this->secretaria_id;
		}
		
		public function setSecretariaId($secretaria_id){
			$this->secretaria_id = $secretaria_id;
		}	
		
		public function getNome(){
			return $this->nome;
		}
		
		public function setNome($nome){
			$this->nome = $nome;
		}
		
		public function getRepresentante(){
			return $this->representante;
		}
		
		public function setRepresentante($representante){
			$this->representante = $representante;
		}
		
		public function getEndereco(){
			return $this->endereco;
		}
		
		public function setEndereco($endereco){
			$this->endereco = $endereco;
		}
		
		public function getCidade(){
			return $this->cidade;
		}
		
		public function setCidade($cidade){
			$this->cidade = $cidade;
		}		
		
		public function getEmail(){
			return $this->email;
		}
		
		public function setEmail($email){
			$this->email = $email;
		}			
		
		public function getTelefone(){
			return $this->telefone;
		}
		
		public function setTelefone($telefone){
			$this->telefone = $telefone;
		}
		
		public function getPagina(){
			return $this->pagina;
		}
		
		public function setPagina($pagina){
			$this->pagina = $pagina;
		}
		
	}
?>