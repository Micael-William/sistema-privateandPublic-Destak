<?php
	class PesquisaSacado{
		private $status;
		private $nome;
		private $email;
		private $cpf_cnpj;
		private $endereco;
		private $cidade;
		private $estado;
		private $pagina;
		
		function __construct() {
			$this->status = null;
			$this->nome = null;
			$this->email = null;
			$this->cpf_cnpj = null;
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
				
		public function getCpfCnpj(){
			return $this->cpf_cnpj;
		}
		
		public function setCpfCnpj($cpf_cnpj){
			$this->cpf_cnpj = $cpf_cnpj;
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