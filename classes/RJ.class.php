<?php
	class RJ{		
		
		public static function getRequerente( $conteudo, $str='Proc.' ){
			
			//Inicio: �ltima ocorr�ncia de '-' antes de $str / Fim: at� $str
			$op = strpos($conteudo,$str);

			if($op == null)
				return "";

			$var = substr($conteudo, $op);
			
			$requerente = DataFilter::get_string_between($var, " - ", '(Adv(s).');
			
			return trim($requerente);
			
		}	
		
		public static function getRequerido( $conteudo, $str=' X ' ){
			
			//Inicio: primeira ocorr�ncia de $str / Fim: at� o $termo
			//Se houver '(Adv' neste meio, considera-se o fim at� '(Adv'
			$termo = null;	
			$requerido = "";

			// echo '<hr>####<hr>';

			$op = strpos($conteudo, $str);

			if($op == false)
				return "";

			$var = substr($conteudo, $op-3);
																
			if(strpos($var, '(Adv(s).' ) !== false){
				$requerido = DataFilter::get_string_between($var, $str, '(Adv(s).');
				// echo "<hr>1";
				// var_dump($requerido);
				// echo "<hr>";
			}

			if(empty($requerido) && strpos($var, ', ' ) !== false)
			{
				$requerido = DataFilter::get_string_between($var, $str, ', ');
				// echo "<hr>2";
				// var_dump($requerido);
				// echo "<hr>";
			}
						
			if((empty($requerido) || $requerido == false)){
				$requerido = substr($conteudo, $op + 3, 50);
				// echo "<hr>3";
				// var_dump($requerido);
				// echo "<hr>";
			}
					
			return trim($requerido);			
		}
		
		public static function getAdvogado($conteudo){
			//Inicio: primeira ocorr�ncia de 'Adv(s). Dr(a). ' / Fim: primeira ocorr�ncia de ' ('
			$adv = DataFilter::get_string_between($conteudo, '(Adv(s). Dr(a). ', ' (OAB/');
			return trim($adv);
		}
		
		public static function getAcao( $conteudo, $str = ' - ' ){			
			$acao = null;
			
			// if( strpos($conteudo, $str) ){
			$inicio = strpos($conteudo, $str);

			$prox = substr($conteudo, $inicio);

			$opt1 = strpos($prox, '0');
			$opt2 = strpos($prox, ' (');

			if($opt2 < $opt1)
				$acao = substr($prox, 3, $opt2-3);
			else
				$acao = substr($prox, 3, $opt1-3);

			return trim($acao);					
		}
				
	}
?>