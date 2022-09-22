<?php
	class Perfil{
		private $id;
		private $nome;	
		private $responsabilidades;
		
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
		
		public function getResponsabilidades() {
			return $this->responsabilidades;
		}		
	
		public function setResponsabilidades( $responsabilidades ) {	
		
			if (is_array( $responsabilidades )) {				
				$this->responsabilidades = $responsabilidades;
			}
			
		}
		
		public function setResponsabilidade( $responsabilidade ){
			if (!isset($this->responsabilidades) or is_null($this->responsabilidades))
				$this->responsabilidades = array();
	
			$this->responsabilidades[] = $responsabilidade;
		}
				
	}
?>