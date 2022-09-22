<?php
	//Pesquisa de Processo/Proposta
	class Pesquisa{
		
		//Processo
		private $sinalizador;
		
		//Proposta
		private $status;
		private $pendente;
        private $substatus;
		private $nome_advogado;
		private $estado;
		private $secretaria_id;
		private $numero_processo;
		private $nome_requerente;
		private $nome_requerido;
		private $codigo_interno;
		private $data_processo;
		private $pagina;
        private $ordenacao;
        private $sentidoOrdenacao;
		
		function __construct() {
			$this->sinalizador = null;
			$this->estado = null;
			$this->secretaria_id = 0;
			$this->numero_processo = 0;
			$this->codigo_interno = 0;
			$this->status = null;
			$this->nome_advogado = null;
		}
		
		public function getSinalizador(){
			return $this->sinalizador;
		}
		
		public function setSinalizador($sinalizador){
			$this->sinalizador = $sinalizador;
		}
		
		public function getStatus(){
			return $this->status;
		}
		
		public function setStatus($status){
			$this->status = $status;
		}
		
        public function getPendente(){
			return $this->pendente;
		}
		
		public function setPendente($pendente){
			$this->pendente = $pendente;
		}
		
        public function getSubStatus(){
			return $this->substatus;
		}
		
		public function setSubStatus($substatus){
			$this->substatus = $substatus;
		}
                
		public function getNomeAdvogado(){
			return $this->nome_advogado;
		}
		
		public function setNomeAdvogado($nome_advogado){
			$this->nome_advogado = $nome_advogado;
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
				
		public function getNumeroProcesso(){
			return $this->numero_processo;
		}
		
		public function setNumeroProcesso($numero_processo){
			$this->numero_processo = $numero_processo;
		}
		
		public function getNomeRequerente(){
			return $this->nome_requerente;
		}
		
		public function setNomeRequerente($nome_requerente){
			$this->nome_requerente = $nome_requerente;
		}
		
		public function getNomeRequerido(){
			return $this->nome_requerido;
		}
		
		public function setNomeRequerido($nome_requerido){
			$this->nome_requerido = $nome_requerido;
		}
				
		public function getCodigoInterno(){
			return $this->codigo_interno;
		}
		
		public function setCodigoInterno($codigo_interno){
			$this->codigo_interno = $codigo_interno;
		}

		public function getDataProcesso(){
			return $this->data_processo;
		}
		
		public function setDataProcesso($data_processo){
			$this->data_processo = $data_processo;
		}
		
		public function getPagina(){
			return $this->pagina;
		}
		
		public function setPagina($pagina){
			$this->pagina = $pagina;
		}
                
        public function getOrdenacao(){
			return $this->ordenacao;
		}
		
		public function setOrdenacao($ordenacao){
			$this->ordenacao = $ordenacao;
		}
                
        public function getSentidoOrdenacao(){
			return $this->sentidoOrdenacao;
		}
		
		public function setSentidoOrdenacao($sentidoOrdenacao){
			$this->sentidoOrdenacao = $sentidoOrdenacao;
		}		
	}
?>