<?php

class MS {

    public static function getRequerente($conteudo, $str = '') {
        $requerente = null;
        preg_match("/(Reqte:|Exeqte:|Autora:|Autor:)\s(?P<str>.+?)(\s-\s|\sADV:|\sReqdo:|\sExectdo:|\s\w+?(\.){0,}(\s){0,}:\s?)/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerente = trim($match['str']);
        }
        return $requerente;
    }

    public static function getRequerido($conteudo, $str = '') {
        $requerido = null;
        preg_match("/(Reqdo:|Exectdo:|Reu:)\s(?P<str>.+?)(\s-\s|\sADV:|\s\w+?(\.){0,}(\s){0,}:\s?)/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerido = trim($match['str']);
        }
        return $requerido;
    }

    public static function getAdvogado($conteudo) {
        $advogado = null;
        $advogado = trim(DataFilter::get_string_between($conteudo, 'ADV:', '('));
        return $advogado;
    }

    public static function getAcao($conteudo) {
        $acao = null;
        $segmts = explode("-", $conteudo);

        $acao = (isset($segmts[3]) && $segmts[3] != '') ? trim($segmts[3]) : '';
        if (strpos($acao, 'Reqte') || strpos($acao, 'Exeqte')) {
            preg_match("/(?P<str>.+)(Reqte:|Exeqte:)/", $acao, $match);
            $acao = trim($match['str']);
        }

        if ((strlen($acao) < 4 || strpos($acao, '.')) && isset($segmts[4]) && $segmts[4] != '' && strlen($segmts[4]) <= 100) {
            if (strpos($segmts[4], 'Reqte') || strpos($segmts[4], 'Exeqte')) {
                preg_match("/(?P<str>.+)(Reqte:|Exeqte:)/", $segmts[4], $match);
                if (isset($match['str'])) {
                    $acao = trim($match['str']);
                } else {
                    $acao = '';
                }
            } else {
                $acao = $segmts[4];
            }
        }

        $acao = (strlen($acao) >= 5) ? $acao : '';
        $acao = (strlen($acao) <= 100) ? $acao : '';
        
        return $acao;
    }

}

?>