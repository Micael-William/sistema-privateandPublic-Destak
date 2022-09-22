<?php
	class Observacao{
		private $id;
		private $data_entrada;
		private $data_envio;
		private $mensagem;
		private $status;
        private $email_destino;
		
		//usuario cadastro
		//private $usuario;
		//usuario envio
		//private $usuario_envio;
		
		private $resp_envio;
		private $resp_cadastro;
		private $usuario_envio_id;
		private $usuario_cadastro_id;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getDataEntrada(){
			return $this->data_entrada;
		}
		
		public function setDataEntrada($data_entrada){
			$this->data_entrada = $data_entrada;
		}
		
		public function getDataEnvio(){
			return $this->data_envio;
		}
		
		public function setDataEnvio($data_envio){
			$this->data_envio = $data_envio;
		}
				
		public function getMensagem(){
			return $this->mensagem;
		}
		
		public function setMensagem($mensagem){
			$this->mensagem = $mensagem;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getRespEnvio(){
			return $this->resp_envio;
		}
		
		public function setRespEnvio($resp_envio){
			$this->resp_envio = $resp_envio;
		}	
		
		public function getRespCadastro(){
			return $this->resp_cadastro;
		}
		
		public function setRespCadastro($resp_cadastro){
			$this->resp_cadastro = $resp_cadastro;
		}
		
		public function getUsuarioEnvioId(){
			return $this->usuario_envio_id;
		}
		
		public function setUsuarioEnvioId($usuario_envio_id){
			$this->usuario_envio_id = $usuario_envio_id;
		}	
		
		public function getUsuarioCadastroId(){
			return $this->usuario_cadastro_id;
		}
		
		public function setUsuarioCadastroId($usuario_cadastro_id){
			$this->usuario_cadastro_id = $usuario_cadastro_id;
		}	
		
        public function getEmailDestino(){
			return $this->email_destino;
		}
		
		public function setEmailDestino($email_destino){
			$this->email_destino = $email_destino;
		}	
	}
?>