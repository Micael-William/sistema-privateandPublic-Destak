<?php
	class ProcessoRepetido{
		private $id;
		private $numero;
		private $status;		
				
		public function getId(){
			return $this->id;
		}
		
		public function setId($id){
			$this->id = $id;
		}
		
		public function getNumero(){
			return $this->numero;
		}
		
		public function setNumero($numero){
			$this->numero = $numero;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
		public function getStatusDesc(){
			if($this->status == 'P') return 'Processo';
			else if($this->status == 'S') return 'Proposta';
			else if($this->status == 'A') return 'Acompanhamento';
		}
		
	}
?>