<?php
require_once('Enumeration.class.php');

abstract class EstadosEnum extends Enumeration {
    
    const __default = self::SP;
    
    const AC = 'Acre';
    const AL = 'Alagoas';
    const AM = 'Amazonas';
    const AP = 'Amapá';
    const BA = 'Bahia';
    const CE = 'Ceará';
    const DF = 'Distrito Federal';
    const ES = 'Espírito Santo';
    const GO = 'Goiás';
    const MA = 'Maranhão';
    const MG = 'Minas Gerais';
    const MS = 'Mato Grosso do Sul';
    const MT = 'Mato Grosso';
    const PA = 'Pará';
    const PB = 'Paraíba';
    const PE = 'Pernambuco';
    const PI = 'Piauí';
    const PR = 'Paraná';
    const RJ = 'Rio de Janeiro';
    const RN = 'Rio Grande do Norte';
    const RO = 'Rondônia';
    const RR = 'Rorâima';
    const RS = 'Rio Grande do Sul';
    const SC = 'Santa Catarina';
    const SE = 'Sergipe';
    const SP = 'São Paulo';
    const TO = 'Tocantins';
    
    public static function getUFs() {
        $constants = parent::getConstants();
        unset($constants['__default']);
        return array_keys($constants);
    }
    
    public static function getEstados( $sel = false ) {
        $constants = parent::getConstants();
        if($sel) {
            array_unshift($constants,'Selecione');
        }
        unset($constants['__default']);
        return $constants;
    }
    
    public static function getChavesUFs( $sel = '' ) {
        $arrUFs = array();
        $constants = self::getUFs();
        foreach($constants AS $key => $value) $arrUFs[$value] = $value;
        if($sel != '') array_unshift($arrUFs,$sel);
        return $arrUFs;
    }

}

