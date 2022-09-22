<?php

class SE {

    public static function getNumeroProcesso($conteudo) {
        $numero = null;
        preg_match("/(PROC.:|PROCESSO:)\s(?P<str>.+?)\s/", $conteudo, $match);
        if (isset($match['str'])) {
            $numero = trim($match['str']);
        }
        return $numero;
    }

    public static function getRequerente($conteudo, $str = '') {
        $requerente = null;
        //preg_match("/(REQUERENTE\s:|REQUERENTE:|EXEQUENTE:|EXEQUENTE\s:|AUTOR:|AUTOR\s:)\s(?P<str>.+?)(ADV\.\s:|EXECUTADA:|EXECUTADA\s:|EXECUTADO:|EXECUTADO\s:|REQUERIDO:|REQUERIDO\s:|DEF\.(\s){0,}:|\s\w+?(\.){0,}(\s){0,}:\s?)/", $conteudo, $match);
        preg_match("/(REQUERENTE\s:|REQUERENTE:|EXEQUENTE:|EXEQUENTE\s:|AUTOR:|AUTOR\s:)\s(?P<str>.+?)(\.|ADV|ADV\.\s:|EXECUTADA:|EXECUTADA\s:|EXECUTADO:|EXECUTADO\s:|REQUERIDO:|REQUERIDO\s:|DEF\.(\s){0,}:)/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerente = trim($match['str']);
            if(strlen($requerente) >= 10)
                $requerente = trim(str_replace('.',' ',$requerente));
        }
        return $requerente;
    }

    public static function getRequerido($conteudo, $str = '') {
        $requerido = null;
        //preg_match("/(REQUERIDO\s:|REQUERIDO:|EXECUTADO:|EXECUTADO\s:|REU:|REU\s:|INTIMANDO:)\s(?P<str>.+?)(DECISAO|SENTENCA|EXECUTADO|\sATO|DATAS:(\s){0,}|,|\sADV\.\s:|ADV\.\s:|\s\w+?(\.){0,}(\s){0,}:\s?)/", $conteudo, $match);
        preg_match("/(REQUERIDO\s:|REQUERIDO:|EXECUTADO:|EXECUTADO\s:|EXECUTADOS:|EXECUTADOS\s:|REU:|REU\s:|INTIMANDO:)\s(?P<str>.+?)(ADV|\sADV\.\s:|ADV\.\s:|DECISAO|SENTENCA|EXECUTADO|\sATO|DATAS:(\s){0,}|,)/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerido = trim($match['str']);
            if(strlen($requerido) >= 10) {
                $requerido = trim(str_replace('.',' ',$requerido));
                if(strpos($requerido,':')) {
                    preg_match("/(?P<str>.+?)(\s\w+?(\.){0,}(\s){0,}:\s?)/", $requerido, $match2);
                    $requerido = (strlen($match2['str']) > 0) ? trim($match2['str']) : $requerido;  
                }
            }
        }
        return $requerido;
    }

    public static function getAdvogado($conteudo) {
        $advogado = null;
        preg_match("/ADV.(\s){0,}:(\s){0,}(?P<str>.+?)(-|\s\w+?:\s?|:\s?)/", $conteudo, $match);
        if (isset($match['str'])) {
            $advogado = trim($match['str']);
        }
        return $advogado;
    }

    public static function getAcao($conteudo) {
        $acao = null;
        preg_match("/(ACAO:|ACAO\s:|-)\s(?P<str>.+?)(-|0|\s\w+?:\s?|:\s?)/", $conteudo, $match);
        if (isset($match['str'])) {
            $acao = trim($match['str']);
        }
        return $acao;
    }

}

?>