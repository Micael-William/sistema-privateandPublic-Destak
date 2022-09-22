<!-- start: lightbox msg -->
<div class="lightbox lightbox-emissao-boleto" id="box-emissao-boleto">

    <div class="header">Emissão de Boleto</div><!-- header -->

    <div class="content">



        <?php
        if ($acompanhamento->getBoletoEmAberto() == null) {
        ?>

            <form class="form-emissao-boleto std-form form-lightbox" style="padding:0;" action="?controle=Boleto&acao=emite" method="post">
                <input type="hidden" name="acompanhamento_id" value="<?php echo !DataValidator::isEmpty($acompanhamento) ? $acompanhamento->getId() : 0; ?>">
                <input type="hidden" name="user_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

                <div class="panel panel-accordion">
                    <div class="panel-title">Dados Emissão</div>
                    <!-- panel title -->
                    <div class="panel-content" style="display: block;">
                        <?php
                        $vencimento = (new DateTime())->add(new DateInterval('P18D'));

                        switch ($vencimento->format("w")) {
                            case 0: //DOM
                                $vencimento = $vencimento->sub(new DateInterval('P2D'));
                                break;
                            case 1: //SEG
                                $vencimento = $vencimento->sub(new DateInterval('P3D'));
                                break;
                            case 2: //TER
                                $vencimento = $vencimento->sub(new DateInterval('P4D'));
                                break;
                            case 3: //QUA
                                $vencimento = $vencimento->sub(new DateInterval('P5D'));
                                break;
                            case 4: //QUI
                                $vencimento = $vencimento->sub(new DateInterval('P6D'));
                                break;
                            case 6: //SAB
                                $vencimento = $vencimento->sub(new DateInterval('P1D'));
                                break;
                            default: //SEX
                                break;
                        }

                        $valor = !DataValidator::isEmpty($acompanhamento) ? $acompanhamento->getCusto()->getValorFinal() : 0;
                        ?>
                        <div class="campo-box">
                            <label for="">Data Vencimento</label>
                            <input type="text" name="boleto_vencimento" class="std-input md-input date-input" value="<?php echo $vencimento->format('d/m/Y'); ?>" />
                        </div>

                        <div class="campo-box">
                            <label for="">Valor</label><?php echo $valor; ?>
                        </div>
                    </div>
                </div>

                <br />
                <div class="controles clearfix">
                    <a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
                    <input type="submit" style="cursor: pointer;" value="Emitir Boleto" class="std-btn send-btn fr" id="btn-emite-boleto">
                </div>
            </form>

        <?php
        } else { ?>
            <div class="warning-box">
                <?php
                if ($boleto) {
                ?>
                    <span class="warning sucesso">Boleto emitido com sucesso!</span>
                <?php
                } else {
                ?>

                    <span class="warning erro">Existe um Boleto em Aberto para esse Acompanhamento.</span>

                <?php
                }

                ?>
            </div>

            <form action="" id="form-boleto-detalhe" method="post">
                <input type="hidden" name="controle" value="Boleto">
                <input type="hidden" name="acao" value="detalhe">
                <input type="hidden" name="boleto_id" id="bol-id" value="<?php echo $acompanhamento->getBoletoEmAberto(); ?>">
            </form>

            <form action="" id="form-boleto-pdf" method="post">
                <input type="hidden" name="controle" value="Boleto">
                <input type="hidden" name="acao" value="pdf">
                <input type="hidden" name="boleto_id" id="bol-id" value="<?php echo $acompanhamento->getBoletoEmAberto(); ?>">

            </form>

            <div class="controles clearfix">
                <input type="button" style="cursor: pointer;" value="Visualizar Detalhes" class="std-btn btn-boleto-detalhe fl">
                <input type="button" style="cursor: pointer;" value="Baixar Boleto" class="std-btn btn-boleto-pdf fr">
            </div>

        <?php
        }
        ?>

    </div><!-- content -->

</div>
<!-- end: lightbox msg -->