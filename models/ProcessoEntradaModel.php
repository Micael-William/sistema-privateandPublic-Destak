<?php

set_time_limit(0);

require_once("lib/PersistModelAbstract.php");
require_once("lib/UserException.php");
require_once("lib/DataFilter.php");
require_once("classes/Processo.class.php");
require_once("models/ProcessoModel.php");
require_once("models/RegraModel.php");
require_once("models/AdvogadoModel.php");
require_once("models/SecretariaModel.php");
require_once("models/JornalModel.php");
require_once("classes/RJ.class.php");
require_once("classes/SP.class.php");
require_once("classes/DF.class.php");
require_once("classes/MT.class.php");
require_once("classes/MS.class.php");
require_once("classes/SE.class.php");

class ProcessoEntradaModel extends PersistModelAbstract
{

    const DIR_ARQUIVO = 'cadastro-xml/';

    /*
      param: Entrada Object
     */

    public static function insert($entrada)
    {
        $msg = null;
        $inputFileName = null;
        $processos_repetidos = array();

        try {

            $entradaModel = new ProcessoEntradaModel();
            $entradaModel->getDB()->beginTransaction();

            if (DataValidator::isEmpty($entrada))
                throw new UserException('Insert Entrada: A Entrada deve ser fornecida.');

            if (!$entrada instanceof ProcessoEntrada)
                throw new UserException('Insert Entrada: A Entrada deve ser do tipo ProcessoEntrada.');

            if (DataValidator::isEmpty($entrada->getEstado()))
                throw new UserException(htmlentities('O campo Estado é obrigatório.', ENT_COMPAT, 'UTF-8'));

            $inputFileName = $entrada->getArquivo();

            if (DataValidator::isEmpty($inputFileName['name']))
                throw new UserException(htmlentities('O campo Arquivo é obrigatório.', ENT_COMPAT, 'UTF-8'));

            $file_name = self::upload($entrada);

            $processos = new XMLReader();

            if (DataValidator::isEmpty($file_name))
                throw new UserException(htmlentities('Nome Arquivo: Não foi possível fazer o upload do arquivo.', ENT_COMPAT, 'UTF-8'));

            if (!$processos->open(self::DIR_ARQUIVO . $file_name))
                throw new UserException(htmlentities('Não foi possível abrir o arquivo', ENT_COMPAT, 'UTF-8'));

            while ($processos->read()) {

                switch ($processos->nodeType) {

                    case (XMLReader::ELEMENT):

                        if ($processos->localName == "PROCESSO") {

                            $node = $processos->expand();
                            $dom = new DomDocument();
                            $n = $dom->importNode($node, true);
                            $dom->appendChild($n);
                            $simple_xml = simplexml_import_dom($n);

                            $entrada->setDataProcesso($simple_xml->DATA_PUBLICACAO);

                            $arrSec = array();
                            $nome_secretaria = "";

                            if ($entrada->getEstado() == 'SP') {
                                $veia = stripos($simple_xml->SECRETARIA, '#') + 1;
                                $nome_secretaria = substr($simple_xml->SECRETARIA, $veia, strlen($simple_xml->SECRETARIA));
                            } elseif ($entrada->getEstado() == 'DF') {
                                $arrSec = explode(' / ', $simple_xml->SECRETARIA);
                                if (isset($arrSec) && count($arrSec) > 2) {
                                    for ($i = 2; $i < count($arrSec); $i++) {
                                        $nome_secretaria .= " / " . $arrSec[$i];
                                    }
                                    $nome_secretaria = substr($nome_secretaria, 2);
                                } else
                                    $nome_secretaria = "";
                            } elseif ($entrada->getEstado() == 'MS' || $entrada->getEstado() == 'MT') {
                                $arrSec = explode('/', $simple_xml->SECRETARIA);
                                if (count($arrSec) > 1) {
                                    array_shift($arrSec);
                                    $nome_secretaria = trim(implode('/', $arrSec));
                                } else
                                    $nome_secretaria = "";
                            } elseif ($entrada->getEstado() == 'SE') {
                                $arrSec = explode('/', $simple_xml->SECRETARIA);
                                if (count($arrSec) > 1) {
                                    array_shift($arrSec);
                                    $nome_secretaria = trim(implode('/', $arrSec));
                                    if ($pos = strpos($nome_secretaria, '-')) {
                                        $nome_secretaria = trim(substr($nome_secretaria, 0, $pos));
                                    }
                                } else
                                    $nome_secretaria = "";
                            } elseif ($entrada->getEstado() == 'RJ') {
                                $arrSec = explode(' / ', $simple_xml->SECRETARIA);

                                if (sizeof($arrSec) > 2) {
                                    for ($i = 2; $i < sizeof($arrSec); $i++)
                                        $nome_secretaria .= $arrSec[$i] . ($i == sizeof($arrSec) - 1 ? "" : " \ ");
                                }
                            } else {
                                $nome_secretaria = $simple_xml->SECRETARIA;
                            }

                            $entrada->setSecretaria($nome_secretaria);
                            $entrada->setNumero(DataFilter::removeEspacos($simple_xml->NUMERO_PROCESSO));
                            $entrada->setConteudo($simple_xml->PUBLICACAO);
                            $entrada->setJornal($simple_xml->JORNAL);
                            $entrada->setTribunal($simple_xml->TRIBUNAL);
                            $entrada->setNomePesquisado($simple_xml->NOME_PESQUISADO);

                            //$processo_duplicado = ProcessoModel::getByEstado( $entrada, $entradaModel->getDB() );																				
                            //if( DataValidator::isEmpty($processo_duplicado) ){	
                            //aplica sinalizador de acordo com termos do conteudo
                            $processo = self::aplicaBusca($entrada, $entradaModel->getDB());
                            //substitui o sinalizador de acordo com as regras de palavras: inicio - fim
                            $processo->setSinalizador(self::aplicaRegra($processo, $entradaModel->getDB()));

                            //cadastra entrada
                            self::save($entrada, $entradaModel->getDB());

                            //cadastra processo
                            ProcessoModel::insertFromEntrada($processo, $entradaModel->getDB());

                            //} 
                            //processos repetidos
                            //else{
                            //$processos_repetidos[] = $entrada->getNumero();	
                            //}
                        } //if						   							   
                } //switch
            } //while

            $entradaModel->getDB()->commit();
        } //try
        catch (Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
            $msg = $e->getMessage();
            $entradaModel->getDB()->rollback();
        }

        return array('msg' => $msg, 'repetidos' => $processos_repetidos);
    }

    public static function insert_view($entrada)
    {
        $msg = null;
        $inputFileName = null;
        $processos_repetidos = array();
        $retorno = null;

        try {

            $entradaModel = new ProcessoEntradaModel();
            $entradaModel->getDB()->beginTransaction();

            if (DataValidator::isEmpty($entrada))
                throw new UserException('Insert Entrada: A Entrada deve ser fornecida.');

            if (!$entrada instanceof ProcessoEntrada)
                throw new UserException('Insert Entrada: A Entrada deve ser do tipo ProcessoEntrada.');

            if (DataValidator::isEmpty($entrada->getEstado()))
                throw new UserException(htmlentities('O campo Estado é obrigatório.', ENT_COMPAT, 'UTF-8'));

            $inputFileName = $entrada->getArquivo();

            if (DataValidator::isEmpty($inputFileName['name']))
                throw new UserException(htmlentities('O campo Arquivo é obrigatório.', ENT_COMPAT, 'UTF-8'));

            $file_name = self::upload($entrada);

            $processos = new XMLReader();

            if (DataValidator::isEmpty($file_name))
                throw new UserException(htmlentities('Nome Arquivo: Não foi possível fazer o upload do arquivo.', ENT_COMPAT, 'UTF-8'));

            if (!$processos->open(self::DIR_ARQUIVO . $file_name))
                throw new UserException(htmlentities('Não foi possível abrir o arquivo', ENT_COMPAT, 'UTF-8'));

            while ($processos->read()) {

                switch ($processos->nodeType) {

                    case (XMLReader::ELEMENT):

                        if ($processos->localName == "PROCESSO") {

                            $node = $processos->expand();
                            $dom = new DomDocument();
                            $n = $dom->importNode($node, true);
                            $dom->appendChild($n);
                            $simple_xml = simplexml_import_dom($n);

                            echo "DTP: " . $simple_xml->DATA_PUBLICACAO, "<br>";

                            if ($entrada->getEstado() == 'SP') {
                                echo $veia = stripos($simple_xml->SECRETARIA, '#') + 1;
                                echo "<br>SP :: ";
                                echo $nome_secretaria = substr($simple_xml->SECRETARIA, $veia, strlen($simple_xml->SECRETARIA));
                                echo "<br>";
                            } elseif ($entrada->getEstado() == 'DF') {
                                $arrSec = explode(' / ', $simple_xml->SECRETARIA);
                                $nome_secretaria = "";
                                if (isset($arrSec) && count($arrSec) > 2) {
                                    for ($i = 2; $i < count($arrSec); $i++) {
                                        $nome_secretaria .= " / " . $arrSec[$i];
                                    }
                                    echo "DF :: ";
                                    echo substr($nome_secretaria, 2);
                                    echo "<br>";
                                } else
                                    $nome_secretaria = "";
                            } else {
                                echo "RJ :: ";
                                echo $nome_secretaria = $simple_xml->SECRETARIA;
                                echo "<br>";
                            }

                            echo "SEC: " . $nome_secretaria;
                            echo "<br>";
                            echo "PRC: " . DataFilter::removeEspacos($simple_xml->NUMERO_PROCESSO);
                            echo "<br>";
                            echo "PBL: " . $simple_xml->PUBLICACAO;
                            echo "<br>";
                            echo "JRN:" . $simple_xml->JORNAL;
                            echo "<br>";
                            echo "TRB: " . $simple_xml->TRIBUNAL;
                            echo "<br>";
                            echo "PSQ: " . $simple_xml->NOME_PESQUISADO;
                            echo "<br>";
                            $entrada->setSecretaria($nome_secretaria);
                            $entrada->setNumero(DataFilter::removeEspacos($simple_xml->NUMERO_PROCESSO));
                            $entrada->setConteudo($simple_xml->PUBLICACAO);
                            $entrada->setJornal($simple_xml->JORNAL);
                            $entrada->setTribunal($simple_xml->TRIBUNAL);
                            $entrada->setNomePesquisado($simple_xml->NOME_PESQUISADO);

                            //$processo_duplicado = ProcessoModel::getByEstado( $entrada, $entradaModel->getDB() );																				
                            //if( DataValidator::isEmpty($processo_duplicado) ){	
                            //aplica sinalizador de acordo com termos do conteudo
                            $processo = self::aplicaBusca($entrada, $entradaModel->getDB());
                            //substitui o sinalizador de acordo com as regras de palavras: inicio - fim
                            $processo->setSinalizador(self::aplicaRegra($processo, $entradaModel->getDB()));
                            echo "SIN: " . self::aplicaRegra($processo, $entradaModel->getDB());
                            echo "<br>";
                            echo "<br>";
                            //cadastra entrada
                            //self::save( $entrada, $entradaModel->getDB() );
                            //cadastra processo
                            ProcessoModel::insertFromEntrada($processo, $entradaModel->getDB());

                            //} 
                            //processos repetidos
                            //else{
                            //$processos_repetidos[] = $entrada->getNumero();	
                            //}
                        } //if						   							   
                } //switch
            } //while

            $entradaModel->getDB()->commit();
        } //try
        catch (UserException $e) {
            $msg = $e->getMessage();
        } catch (UserException $e) {
            $msg = $e->getMessage();
            $entradaModel->getDB()->rollback();
        }

        return $retorno = array('msg' => $msg, 'repetidos' => $processos_repetidos);
    }

    public static function LimparCaracter($text)
    {
        $chr_map = array(
            // Windows codepage 1252
            "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
            "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
            "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
            "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
            "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
            "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
            "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
        );
        $chr = array_keys($chr_map); // but: for efficiency you should
        $rpl = array_values($chr_map); // pre-calculate these two arrays

        $string =  str_ireplace($chr, $rpl, html_entity_decode($text, ENT_QUOTES, "UTF-8"));

        $string = htmlentities($string, ENT_SUBSTITUTE);

        return $string;
    }

    //****************************************//

    /*
      aplica busca por termos no conteudo do processo, de acordo com o estado passado
     */
    public static function aplicaBusca($entrada, $db)
    {

        $processo = new Processo();
        $processo->setSinalizador('V');
        $processo->setEntrada($entrada);
        $total_adv = 0;

        $conteudo = $processo->getEntrada()->getConteudo();
        $estado = $processo->getEntrada()->getEstado();

        //instancia o estado
        $classe_estado = new $estado();

        if (method_exists($classe_estado, 'getNumeroProcesso')) {
            $processo->getEntrada()->setNumero($classe_estado::getNumeroProcesso($conteudo));
        }
        $nome_requerente = self::LimparCaracter($classe_estado::getRequerente($conteudo));
        $nome_requerido = self::LimparCaracter($classe_estado::getRequerido($conteudo));
        $nome_advogado = self::LimparCaracter($classe_estado::getAdvogado($conteudo));
        //if($estado == 'SP')
        $processo->setAcao($classe_estado::getAcao($conteudo));

        if (DataValidator::isEmpty($nome_requerente))
            $processo->setSinalizador('A');

        $processo->setRequerente($nome_requerente);

        if (DataValidator::isEmpty($nome_requerido))
            $processo->setSinalizador('A');

        $processo->setRequerido($nome_requerido);

        if (DataValidator::isEmpty($nome_advogado))
            $processo->setSinalizador('A');

        $entrada->setAdvogado($nome_advogado);

        if ($processo->getEntrada()->getSecretaria() == '')
            $processo->setSinalizador('A');


        //advogado
        $retorno_adv = AdvogadoModel::getFromEntrada($nome_advogado, $db);
        if ($retorno_adv != null && !DataValidator::isEmpty($retorno_adv['advogados'])) {

            $total_adv = $retorno_adv['totalLinhas'];

            //se existem advogados homonimos, nï¿½o vincula o advogado. Apenas grava a quantidade de homonimos.
            if ($total_adv > 1) {
                $processo->setSinalizador('A');
                $processo->getEntrada()->setQtdHomonimo($total_adv);
            } else {
                $adv_encontrado = $retorno_adv['advogados'][0];
                $adv = new Advogado();
                $adv->setId($adv_encontrado->getId());
                $processo->setAdvogado($adv);
                $processo->getEntrada()->setQtdHomonimo($total_adv);

                if (DataValidator::isEmpty($adv_encontrado->getEmails()))
                    $processo->setSinalizador('A');
            }
        } else {
            $processo->setSinalizador('A');
            $processo->getEntrada()->setQtdHomonimo($total_adv);
        }
        //--advogado			
        //Secretaria				
        $secretaria = SecretariaModel::getBy($processo->getEntrada()->getSecretaria(), $db);
        if (!DataValidator::isEmpty($secretaria)) {

            $sec = new Secretaria();
            $sec->setId($secretaria->getId());
            $processo->setSecretaria($sec);

            //jornal
            $jornal = JornalModel::getBySecretaria($secretaria->getId(), $db);
            if (!DataValidator::isEmpty($jornal)) {

                $journal = new Jornal();
                $journal->setId($jornal->getId());
                $processo->setJornal($journal);

                if (DataValidator::isEmpty($jornal->getCusto())) //valor padrï¿½o
                    $processo->setSinalizador('A');

                if (!DataValidator::isEmpty($jornal->getDataConfirmacao())) {
                    if (DataFilter::calculaDias($jornal->getDataConfirmacao(), date('Y-m-d')) >= 365)
                        $processo->setSinalizador('A');
                } else
                    $processo->setSinalizador('A');
            } else
                $processo->setSinalizador('A');
            //--jornal
        } else
            $processo->setSinalizador('A');
        //--Secretaria


        return $processo;
    }

    ////////////////////////////////////////////////////////////////////////////
    //aplica regras de termos inicio - termino					
    public static function aplicaRegra($processo, $db)
    {

        $sinalizador = null;
        $termo_inicio = null;
        $termo_fim = null;
        $tam = 0;

        if (DataValidator::isEmpty($processo))
            throw new UserException('Aplica Regra: Processo deve ser fornecido.');

        $regras = RegraModel::lista($processo->getEntrada()->getEstado(), $db);
        $conteudo = (string) $processo->getEntrada()->getConteudo();

        if (!DataValidator::isEmpty($regras) && !DataValidator::isEmpty($conteudo)) {

            for ($i = 0; $i < sizeof($regras); $i++) {

                //processos de SP que possuem 'EDITAL DE INTIMACAO DE ADVOGADOS' devem ignorar a regra 'edital' contida neste termo					
                if (stripos($conteudo, 'EDITAL DE INTIMACAO DE ADVOGADOS'))
                    $conteudo = str_replace(' EDITAL DE INTIMACAO DE ADVOGADOS ', '', $conteudo);
                else
                    $conteudo = $conteudo;

                if (stripos($conteudo, $regras[$i]['inicio']) && stripos($conteudo, $regras[$i]['termino'])) {

                    $t_start = stripos($conteudo, $regras[$i]['inicio']);
                    $pos_start = $t_start + strlen($regras[$i]['inicio']);

                    $t_end = stripos($conteudo, $regras[$i]['termino']);
                    $pos_end = $t_end + strlen($regras[$i]['termino']);

                    //ex: defiro x indefiro
                    $x = ($pos_start - strlen($regras[$i]['inicio'])) - 1;
                    //ex: publique x publiquem
                    $y = $conteudo[$pos_start];
                    //ex: edital x editalicia
                    $z = $conteudo[$pos_end];

                    if (($conteudo[$x] == ' ' || $conteudo[$x] == '.' ||
                            $conteudo[$x] == ',') && ($y == ' ' || $y == '.' || $y == ',') && ($z == ' ' || $z == '.' || $z == ',') &&
                        $pos_start < $pos_end
                    ) {
                        $termo_inicio = $regras[$i]['inicio'];
                        $termo_fim = $regras[$i]['termino'];

                        $tam = strlen(DataFilter::get_string_between($conteudo, $regras[$i]['inicio'], $regras[$i]['termino']));

                        if ($tam > 0 && $tam <= $regras[$i]['tamanho']) {
                            $sinalizador = $regras[$i]['sinal'];
                            break;
                        }
                    } //condiï¿½ï¿½es
                } //stripos					
            } //for					
        } //regras			

        if (DataValidator::isEmpty($sinalizador))
            $sinalizador = 'A';

        //se o sinalizador ï¿½ Amarelo (termos), nï¿½o pode ser Verde (regras de palavras)
        if ($sinalizador == 'V' && $processo->getSinalizador() == 'A')
            $sinalizador = 'A';
        else
            $sinalizador = $sinalizador;

        $processo->getEntrada()->setTermoInicio($termo_inicio);
        $processo->getEntrada()->setTermoFim($termo_fim);
        $processo->getEntrada()->setIntervalo($tam);

        return $sinalizador;
    }

    ////////////////////////////////////////////////////////////////////////////

    public static function save($entrada, $db = null)
    {

        if (is_null($db)) {
            $entradaModel = new ProcessoEntradaModel();
            $db = $entradaModel->getDB();
        }

        if (DataValidator::isEmpty($entrada))
            throw new UserException('Insert Entrada: Entrada deve ser fornecida.');

        $sql = " INSERT INTO processo_entrada (	
										 data_processo,
										 secretaria, 
										 jornal,
										 tribunal,
										 advogado,
										 nome_pesquisado,
										 numero, 
										 conteudo,
										 estado,
										 termo_inicio,
										 termo_fim,
										 intervalo,
										 qtd_homonimo
										 ) 
								VALUES (
										:data_processo,
										:secretaria,
										:jornal,
										:tribunal,
										:advogado,
										:nome_pesquisado,
										:numero_processo,
										:conteudo,
										:estado,
										:termo_inicio,
										:termo_fim,
										:intervalo,
										:qtd_homonimo
										) ";


        $query = $db->prepare($sql);

        if ($entrada->getDataProcesso() != '') {
            $data = explode("/", $entrada->getDataProcesso());
            $data_processo = $data[2] . '-' . $data[1] . '-' . $data[0];

            $query->bindValue(':data_processo', $data_processo, PDO::PARAM_STR);
        } else
            $query->bindValue(':data_processo', NULL, PDO::PARAM_NULL);

        if ($entrada->getSecretaria() != '')
            $query->bindValue(':secretaria', $entrada->getSecretaria(), PDO::PARAM_STR);
        else
            $query->bindValue(':secretaria', NULL, PDO::PARAM_NULL);

        if ($entrada->getJornal() != '')
            $query->bindValue(':jornal', $entrada->getJornal(), PDO::PARAM_STR);
        else
            $query->bindValue(':jornal', NULL, PDO::PARAM_NULL);

        if ($entrada->getTribunal() != '')
            $query->bindValue(':tribunal', $entrada->getTribunal(), PDO::PARAM_STR);
        else
            $query->bindValue(':tribunal', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getAdvogado()))
            $query->bindValue(':advogado', $entrada->getAdvogado(), PDO::PARAM_STR);
        else
            $query->bindValue(':advogado', NULL, PDO::PARAM_NULL);

        if ($entrada->getNomePesquisado() != '')
            $query->bindValue(':nome_pesquisado', $entrada->getNomePesquisado(), PDO::PARAM_STR);
        else
            $query->bindValue(':nome_pesquisado', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getNumero()))
            $query->bindValue(':numero_processo', $entrada->getNumero(), PDO::PARAM_STR);
        else
            $query->bindValue(':numero_processo', NULL, PDO::PARAM_NULL);

        if ($entrada->getConteudo() != '')
            $query->bindValue(':conteudo', $entrada->getConteudo(), PDO::PARAM_STR);
        else
            $query->bindValue(':conteudo', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getEstado()))
            $query->bindValue(':estado', $entrada->getEstado(), PDO::PARAM_STR);
        else
            $query->bindValue(':estado', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getTermoInicio()))
            $query->bindValue(':termo_inicio', $entrada->getTermoInicio(), PDO::PARAM_STR);
        else
            $query->bindValue(':termo_inicio', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getTermoFim()))
            $query->bindValue(':termo_fim', $entrada->getTermoFim(), PDO::PARAM_STR);
        else
            $query->bindValue(':termo_fim', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getIntervalo()))
            $query->bindValue(':intervalo', $entrada->getIntervalo(), PDO::PARAM_INT);
        else
            $query->bindValue(':intervalo', NULL, PDO::PARAM_NULL);

        if (!DataValidator::isEmpty($entrada->getQtdHomonimo()))
            $query->bindValue(':qtd_homonimo', $entrada->getQtdHomonimo(), PDO::PARAM_INT);
        else
            $query->bindValue(':qtd_homonimo', NULL, PDO::PARAM_NULL);


        $query->execute();
        $entrada->setId($db->lastInsertId());

        return $entrada->getId();
    }

    public static function getById($entrada_id, $db = null)
    {
        $entrada = null;

        if (is_null($db)) {
            $entradaModel = new ProcessoEntradaModel();
            $db = $entradaModel->getDB();
        }

        if (DataValidator::isEmpty($entrada_id))
            throw new UserException('Entrada: A Entrada deve ser identificada.');

        $sql = " SELECT *
					FROM processo_entrada
					WHERE id=:entrada_id ";

        $query = $db->prepare($sql);
        $query->bindValue(':entrada_id', $entrada_id, PDO::PARAM_INT);

        $query->execute();

        $linha = $query->fetchObject();

        if (!$linha)
            return null;

        $entrada = new ProcessoEntrada();
        $entrada->setId($linha->id);
        $entrada->setNumero($linha->numero);
        $entrada->setEstado($linha->estado);
        $entrada->setDataProcesso($linha->data_processo);
        $entrada->setConteudo($linha->conteudo);
        $entrada->setTermoInicio($linha->termo_inicio);
        $entrada->setTermoFim($linha->termo_fim);
        $entrada->setIntervalo($linha->intervalo);
        $entrada->setQtdHomonimo($linha->qtd_homonimo);

        //nomes vindos do arquivo
        $entrada->setAdvogado($linha->advogado);
        $entrada->setSecretaria($linha->secretaria);
        $entrada->setJornal($linha->jornal);

        return $entrada;
    }

    public static function upload($entrada)
    {

        $inputFileName = $entrada->getArquivo();
        $nome_arquivo = null;

        if (
            $inputFileName['type'] == "application/xml" ||
            $inputFileName['type'] == "text/xml"
        ) {

            $nome_arquivo = date("Y-m-d") . '-' . $entrada->getEstado() . '-' . rand(0, 1000000000) . '.' . DataFilter::ver_extensao($inputFileName['name']);
            move_uploaded_file($inputFileName['tmp_name'], self::DIR_ARQUIVO . $nome_arquivo);
        } else
            throw new UserException(htmlentities('Não foi possível fazer o upload do arquivo. Extensão permitida: .xml', ENT_COMPAT, 'UTF-8'));

        return $nome_arquivo;
    }

    //Altera do Acompanhamento
    public static function update($entrada, $db = null)
    {

        if (is_null($db)) {
            $entradaModel = new ProcessoEntradaModel();
            $db = $entradaModel->getDB();
        }


        if (DataValidator::isEmpty($entrada))
            throw new UserException('Entrada: A Entrada deve ser fornecida.');

        if (DataValidator::isEmpty($entrada->getId()))
            throw new UserException('Entrada: A Entrada deve ser identificada.');

        $sql = " UPDATE processo_entrada SET
							numero=:numero
							WHERE id=:entrada_id
							";

        $query = $db->prepare($sql);
        $query->bindValue(':numero', $entrada->getNumero(), PDO::PARAM_STR);
        $query->bindValue(':entrada_id', $entrada->getId(), PDO::PARAM_INT);
        $query->execute();
    }
}
