<?php
	class PesquisaAdvogado{
		private $status;
		private $nome;
		private $email;
		private $telefone;
		private $oab;
		private $empresa;
		private $nome_contato;
		private $email_contato;
		private $endereco;
		private $cidade;
		private $estado;
		private $pagina;
		
		function __construct() {
			$this->status = null;
			$this->nome = null;
			$this->email = null;
			$this->telefone = null;
			$this->oab = null;
			$this->empresa = null;
			$this->nome_contato = null;
			$this->email_contato = null;
			$this->endereco = null;
			$this->cidade = null;
			$this->estado = null;
			$this->pagina = null;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getNome(){
			return $this->nome;
		}
		
		public function setNome($nome){
			$this->nome = $nome;
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
				
		public function getOab(){
			return $this->oab;
		}
		
		public function setOab($oab){
			$this->oab = $oab;
		}
		
		public function getEmpresa(){
			return $this->empresa;
		}
		
		public function setEmpresa($empresa){
			$this->empresa = $empresa;
		}
		
		public function getNomeContato(){
			return $this->nome_contato;
		}
		
		public function setNomeContato($nome_contato){
			$this->nome_contato = $nome_contato;
		}
		
		public function getEmailContato(){
			return $this->email_contato;
		}
		
		public function setEmailContato($email_contato){
			$this->email_contato = $email_contato;
		}
		
		public function getCidade(){
			return $this->cidade;
		}
		
		public function setCidade($cidade){
			$this->cidade = $cidade;
		}
		
		public function getEndereco(){
			return $this->endereco;
		}
		
		public function setEndereco($endereco){
			$this->endereco = $endereco;
		}
		
		public function getEstado(){
			return $this->estado;
		}
		
		public function setEstado($estado){
			$this->estado = $estado;
		}		
		
		public function getPagina(){
			return $this->pagina;
		}
		
		public function setPagina($pagina){
			$this->pagina = $pagina;
		}
		
	}
?>