<?php

require_once("models/BoletoModel.php");

if ($_POST['event'] == "invoice.status_changed") {

    $invoice_id = $_POST['data']['id'];
    $status = $_POST['data']['status'];

    $boleto = BoletoModel::getByIuguInvoice($invoice_id);

    if ($boleto != null) {
        $boleto->setIuguStatus($status);
        BoletoModel::update($boleto);
    }
}
