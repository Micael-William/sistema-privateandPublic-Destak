<?php
	class Usuario{
		private $id;
		private $nome;
		private $status;
		private $data_entrada;
		private $cpf;
		private $nivel;
		private $email;
		private $senha;
		
		private $perfil;	
		
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
		
		public function getCpf(){
			return $this->cpf;
		}
		
		public function setCpf($cpf){
			$this->cpf = $cpf;
		}
		
		public function getEmail(){
			return $this->email;
		}
		
		public function setEmail($email){
			$this->email = $email;
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
		
		public function getDataEntrada(){
			return $this->data_entrada;
		}
		
		public function setDataEntrada($data_entrada){
			$this->data_entrada = $data_entrada;
		}
		
		public function getPerfil(){
			return $this->perfil;
		}
		
		public function setPerfil($perfil){
			$this->perfil = $perfil instanceof Perfil ? $perfil : null;
		}
		
		public function getSenha(){
			return $this->senha;
		}
		
		public function setSenha($senha){
			$this->senha = $senha;
		}
		
	}
?>