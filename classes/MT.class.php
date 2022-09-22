<?php

class MT {

    public static function getRequerente($conteudo, $str = '') {
        $requerente = null;
        preg_match("/(PARTE\sAUTORA:|PARTE\sREQUERENTE:|Polo Ativo:)\s(?P<str>.+?)(PARTE|\s-\s|\()/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerente = trim($match['str']);
        }
        return $requerente;
    }

    public static function getRequerido($conteudo, $str = '') {
        $requerido = null;
        preg_match("/(PARTE\sREQUERIDA:|REQUERIDA:|PARTE\(S\)\sREQUERIDA\(S\):|REQUERIDA\(S\):|Polo Passivo:)\s(?P<str>.+?)(ADVOGADO\(S\)|ADVOGADO|\()/", $conteudo, $match);
        if (isset($match['str'])) {
            $requerido = trim($match['str']);
        }
        $requerido = (stripos($requerido,'ADVOGADO') === false) ? $requerido : '';
        return $requerido;
    }

    public static function getAdvogado($conteudo) {
        $advogado = null;
        preg_match("/(ADVOGADO\sDA\sPARTE\sAUTORA:|ADVOGADO\(S\)\sDA\sPARTE\sAUTORA:|ADVOGADO\sDA\sPARTE\sREQUERENTE|ADVOGADO\(S\)\sDA\sPARTE\sREQUERENTE)\s(?P<str>.+?)(\s-\s|ADVOGADO)/", $conteudo, $match);
        if (isset($match['str'])) {
            $advogado = trim($match['str']);
        }
        //$advogado = DataFilter::get_string_between($conteudo, ' ADVOGADO: ', ' REQUERIDO(A): ');
        return $advogado;
    }

    public static function getAcao($conteudo) {
        $acao = null;
        $acao = trim(DataFilter::get_string_between($conteudo, ' ACAO: ', '->'));
        $acao = (strlen($acao) <= 100) ? $acao : '';
        return $acao;
    }

}

?>