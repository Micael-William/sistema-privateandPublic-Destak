<?php
	class Email{
		private $id;
		private $email_endereco;
		private $enviar;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getEmailEndereco(){
			return $this->email_endereco;
		}
		
		public function setEmailEndereco($email_endereco){
			$this->email_endereco = $email_endereco;
		}	
		
		public function getEnviar(){
			return $this->enviar;
		}
		
		public function setEnviar($enviar){
			$this->enviar = $enviar;
		}
		
	}
?>