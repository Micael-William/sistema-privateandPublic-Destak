<?php
	class SP{		
				
		public static function getRequerente( $conteudo, $str='Processo' ){
			
			if( strpos($conteudo, $str) ){
				//Inicio: após a 1ª ocorrência de 'Processo', procura-se pela 4ª ocorrência do '-' / Fim: até a próxima ocorrência de '-'			
				$inicio = strpos($conteudo, $str);
				$termos = substr($conteudo, $inicio);			
				$requerente = explode(' - ', $termos);
			
				if( isset($requerente) && isset($requerente[3]) )
					return $requerente[3];//existem caso em que requerente esta após o quinto traço
				else
					return null;
			} 
			else
				return null;
			
		}	
		
		public static function getRequerido( $conteudo, $str='Processo' ){
			
			$requerido = null;
			
			if( strpos($conteudo, $str) ){
				$inicio = strpos($conteudo, $str);
				$termos = substr($conteudo, $inicio);			
				$requerente = explode(' - ', $termos);
				
				if( isset($requerente) && isset($requerente[4]) )
					$requerido = $requerente[4];	
			}
		
			return $requerido;			
			
		}
		
		public static function getAdvogado($conteudo){
			$adv = null;
			
			$adv = DataFilter::get_string_between($conteudo, 'ADV: ', ' (');
			return $adv;
		}
		
		public static function getAcao( $conteudo, $str='Processo' ){
			
			$acao = null;
			
			if( strpos($conteudo, $str) ){
				$inicio = strpos($conteudo, $str);
				$termos = substr($conteudo, $inicio);			
				$acoes = explode(' - ', $termos);
				
				if( isset($acoes) && isset($acoes[1]) )
					$acao = $acoes[1];				
			}	
		
			return $acao;			
			
		}
				
	}
?>