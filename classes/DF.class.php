<?php
	class DF{		
		
		public static function getRequerente( $conteudo, $str='' ){
                    $requerente = trim(DataFilter::get_string_between($conteudo, ' - A: ', '. Adv'));
                    return $requerente;
		}	
		
		public static function getRequerido( $conteudo, $str='' ){
                    $requerido = trim(DataFilter::get_string_between($conteudo, '. R: ', '. Adv'));
                    return $requerido;			
		}
		
		public static function getAdvogado($conteudo){
                    $advogado = null;
                    
                    if($conteudo != '') {
                        preg_match('/. Adv\(s\)\.: DF[0-9]{1,6} - (?P<advogado>\w.[^,|^.]{1,})/',$conteudo,$match);
                        if(isset($match['advogado'])) {
                            $advogado = trim($match['advogado']);
                        }
                    }
                    return $advogado;
		}
		
		public static function getAcao( $conteudo ){			
                    $acao = null;
                    $tmp_acao = '';
                    
                    $tmp_acao = DataFilter::get_string_between($conteudo, '-', ' - A: ');
                    if($tmp_acao != '') {
                        preg_match('/[0-9]{1}-[0-9]{1} - (?P<acao>\w.+)$/',$tmp_acao,$match);
                        if(isset($match['acao'])) {
                            $acao = $match['acao'];
                        }
                    }
                    
                    if ($acao ==! null && strlen($acao) > 150) {
                        preg_match('/[0-9]{4}-[0-9]{1} - (?P<acao_2>\w[^-]{1,}) - /',$tmp_acao,$match_2);
                        if(isset($match_2['acao_2'])) {
                            $acao = $match_2['acao_2'];
                        }
                    }
                    return $acao;				
		}
				
	}
?>