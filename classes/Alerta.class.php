<?php
	class Alerta{
		private $id;
		private $processo_id;
		private $mensagem;
		private $status;
		private $data_entrada;
		
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getProcessoId(){
			return $this->processo_id;
		}
		
		public function setProcessoId($processo_id){
			$this->processo_id = $processo_id;
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
		
		public function getDataEntrada(){
			return $this->data_entrada;
		}
		
		public function setDataEntrada($data_entrada){
			$this->data_entrada = $data_entrada;
		}
		
	}
?>