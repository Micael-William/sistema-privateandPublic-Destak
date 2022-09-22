<?php

if (@$_REQUEST["http_x_cron_auth"] != "70c81333fbc00f2c4ad386f258e26410") {
    die("Acesso nao Autorizado");
}

require_once("models/BoletoModel.php");

$boletosPendentes = BoletoModel::getListaBoletoPendente();

foreach ($boletosPendentes as $boleto) {
    $boleto->setIuguStatus('overdue');
    BoletoModel::update($boleto);
}


$boletosPagos = BoletoModel::buscaFaturasPagas();

foreach ($boletosPagos as $boleto) {
    $boletoB = BoletoModel::getByIuguInvoice($boleto->id);

    if ($boletoB != null && $boletoB->getIuguStatus() != 'paid') {
        $boletoB->setIuguStatus('paid');
        BoletoModel::update($boletoB);
    }
}
