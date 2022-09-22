<?php		
	class Paginacao{
		
		private $numeroPagina; //numero da pagina passada pela URL
                private $ordenacao;    //indice do campo de ordenacao
                private $sentidoOrdenacao; //sentido da ordenacao "a" para crescente ou "d" para decrescente
		private $totalRegistros;
		private $qtdPagina; //quantidade de registros por pagina
		private $paginaDestino;
                		
		public function getNumeroPagina(){
			return $this->numeroPagina;
		}
		
		public function setNumeroPagina($numeroPagina){
			$this->numeroPagina = $numeroPagina;
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
		
		public function getTotalRegistros(){
			return $this->totalRegistros;
		}
		
		public function setTotalRegistros($totalRegistros){
			$this->totalRegistros = $totalRegistros;
		}
		
		public function getQtdPagina(){
			return $this->qtdPagina;
		}
		
		public function setQtdPagina($qtdPagina){
			$this->qtdPagina = $qtdPagina;
		}
		
		public function getPaginaDestino(){
			return $this->paginaDestino;
		}
		
		public function setPaginaDestino($paginaDestino){
			$this->paginaDestino = $paginaDestino;
		}		
				
		//--------------------------------------------------------------------------------------	
			
				
		public function getAll(){
			
			$html = "";			
			
			$pags = ceil($this->getTotalRegistros() / $this->getQtdPagina() );
			
			$pagina_antes = $this->getNumeroPagina() - 1;
			$pagina_depois = $this->getNumeroPagina() + 1;			
			
			$html .= '<li><a class="page-link" data-page="1" href="?controle=' . $this->getPaginaDestino() . '&acao=busca&p=1" target="_self"> Primeira </a></li>';
			//3 => 2-3 = -1
			
			for ($i = $pagina_antes-8; $i <= $this->getNumeroPagina()-1; $i++) {
				
				if($i >0) {
					$html .= '<li><a class="page-link" data-page="' . $i . '" href="?controle=' . $this->getPaginaDestino() . '&acao=busca" >' . $i . '</a></li>';					
				}
			}
			
			$html .= "<li><a href='#' class='active'>" . $this->getNumeroPagina() . " </a></li>";		
				
			for ($i = $this->getNumeroPagina() + 1; $i<= $pagina_depois + 8; $i++) {
				if($i <= $pags) {
					$html .= '<li><a class="page-link" data-page="' . $i . '" href="?controle=' . $this->getPaginaDestino() . '&acao=busca" >' . $i . '</a></li>';
				}
			}
				
			$html .= '<li><a class="page-link" data-page="' . $pags . '" href="?controle=' . $this->getPaginaDestino() . '&acao=busca' . $pags .' "> &Uacute;ltima </a></li>';	
			
			return $html;
		}			
		
	}	
?>