<?php
$sacado = $acompanhamento->getSacado();
?>
<div class="panel panel-accordion panel-default-bg clear" id="form-financeiro<?php echo $modalImpressao ? "-modal" : ""; ?>">
    <div class="panel-title">Financeiro <i class="seta seta-frente"></i></div>
    <!-- panel title -->
    <div class="panel-content" style="display: <?php echo $modalImpressao ? "block" : "none" ?>;">

        <div class="campo-box">
            <label for="">Sacado</label>
            <input type="text" name="sacado_nome" class="std-input sacado-field" placeholder="Digite o nome do Sacado para a seleção no Banco de Dados" value="<?php echo $sacado ? $sacado->getNomeSacado() : ""; ?>">
            <input type="hidden" name="sacado_id" id="sacado-id" value="<?php echo $sacado ? $sacado->getSacadoId() : ""; ?>">
        </div>

        <div class="campo-box">
            <label>&nbsp;</label>
            <label style="width: auto; height:30px;"><input type="radio" name="sacado_tipo" class="btn-documento" value="pf" <?php echo $sacado && strlen($sacado->getCpfCnpj()) <= 14 ? "checked" : ""; ?> /> Pessoa Fisica&nbsp;</label>
            <label style="width: auto;"><input type="radio" name="sacado_tipo" class="btn-documento" value="pj" <?php echo $sacado && strlen($sacado->getCpfCnpj()) > 14 ? "checked" : ""; ?> /> Pessoa Juridica</label>
        </div>
        <!-- campo -->

        <div class="campo-box">
            <label for="">CPF/CNPJ</label>
            <input type="text" name="sacado_cpf_cnpj" value="<?php echo $sacado ? $sacado->getCpfCnpj() : ""; ?>" class="std-input md-input <?php echo $sacado && strlen($sacado->getCpfCnpj()) <= 14 ? "cpf-input" : "cnpj-input"; ?>">
        </div>

        <div class="campo-box">
            <a href="#" class="std-btn sm-btn btn-endereco-escritorio">Preencher com endereço do escritório</a>
        </div>
        <div class="campo-box">
            <label for="">CEP</label>
            <input type="text" name="sacado_cep" value="<?php echo $sacado ? $sacado->getEndereco()->getCep() : ""; ?>" class="std-input sm-input cep-input">
            <a href="#" title="Buscar CEP" class="std-btn sm-btn busca-cep-btn">Buscar CEP</a>
            <i class="loading-ico"><img src="img/loading.gif" alt=""></i>
        </div>
        <!-- campo -->

        <div class="campo-box">
            <label for="">Logradouro</label>
            <input type="text" name="sacado_logradouro" value="<?php echo $sacado ? $sacado->getEndereco()->getLogradouro() : ""; ?>" class="std-input" id="logradouro">
        </div>
        <!-- campo -->

        <div class="campo-box">
            <label for="">Número</label>
            <input type="text" name="sacado_numero" value="<?php echo $sacado ? $sacado->getEndereco()->getNumero() : ""; ?>" class="std-input sm-input" id="numero">
            <label for="" style="width: 100px;">Complemento</label>
            <input type="text" name="sacado_complemento" value="<?php echo $sacado ? $sacado->getEndereco()->getComplemento() : ""; ?>" class="std-input" id="complemento">
            &nbsp;
        </div>
        <div class="clear"></div>
        <!-- campo -->

        <div class="campo-box">

            <label for="">Bairro</label>
            <input type="text" name="sacado_bairro" value="<?php echo $sacado ? $sacado->getEndereco()->getBairro() : ""; ?>" class="std-input" id="bairro">

        </div>
        <!-- campo -->

        <div class="campo-box">
            <label for="">Cidade</label>
            <input type="text" name="sacado_cidade" value="<?php echo $sacado ? $sacado->getEndereco()->getCidade() : ""; ?>" class="std-input" id="cidade">
            <label for="" style="width: 55px;">Estado</label>
            <select name="sacado_estado" class="" id="estado">
                <?php
                $estados = EstadosEnum::getChavesUFs('Selecione');

                foreach ($estados as $key => $value) {
                ?>
                    <option value="<?php echo $key; ?>" <?php echo $sacado && !DataValidator::isEmpty($sacado->getEndereco()) &&  $key == $sacado->getEndereco()->getEstado() ? 'selected' : ''; ?>><?php echo $value; ?></option>
                <?php
                }
                ?>
            </select>
        </div>
        <div class="clear"></div>

        <div class="campo-box clearfix">

            <div class="panel panel-accordion panel-default-bg">

                <div class="panel-title email-finan-title">
                    E-mails &nbsp;&nbsp;

                    <a href="#" title="Adicionar" class="std-btn sm-btn add-email-sac">
                        Adicionar
                    </a>
                </div>

                <div class="panel-content email-sacado" style="display: block; <?php echo $modalImpressao ? "max-height:100px; overflow:auto;" : ""; ?>">

                    <?php
                    if (!DataValidator::isEmpty($sacado) && !DataValidator::isEmpty($sacado->getEmails())) {
                        foreach ($sacado->getEmails() as $key => $email) {
                            $key++;
                    ?>

                            <div class="campo-box email-box">
                                <label for="">Email</label>
                                <input type="text" name="email_<?php echo $key; ?>" value="<?php echo $email->getEmailEndereco(); ?>" class="campo-email std-input md-input">
                                <input type="hidden" name="email_id_<?php echo $key; ?>" class="id-email" value="<?php echo $email->getId(); ?>">
                                &nbsp;

                                <?php
                                if (!DataValidator::isEmpty($responsabilidades) && isset($responsabilidades[5]) && $responsabilidades[5]['acao'] == 'E') {
                                ?>
                                    <a href="#" title="Excluir" class="std-btn gray-btn sm-btn del-campo" data-id="<?php echo $email->getId(); ?>" data-acao="acompanhamento-fin-email">Excluir</a>
                                <?php } ?>

                            </div>
                            <!-- campo -->

                        <?php }
                    } else { ?>

                        <div class="campo-box email-box">
                            <label for="">Email</label>
                            <input type="text" name="email_1" class="std-input campo-email md-input">
                            <input type="hidden" name="email_id_1" class="id-email" value="0">
                        </div>
                        <!-- campo -->

                    <?php } ?>

                    <input type="hidden" name="qtd_emails" value="<?php echo !DataValidator::isEmpty($sacado) && !DataValidator::isEmpty($sacado->getEmails()) ? count($sacado->getEmails()) : 1; ?>" class="qtd-email">
                </div>
            </div>
        </div>
        <?php
        if (!$modalImpressao) {
        ?>
            <div class="campo-box clearfix">

                <div class="panel panel-accordion panel-default-bg">

                    <div class="panel-title obs-finan-title">
                        Observações Financeiro&nbsp;&nbsp;
                        <?php
                        if (
                            !DataValidator::isEmpty($responsabilidades) &&
                            isset($responsabilidades[4]) &&
                            $responsabilidades[4]['acao'] == 'E'
                        ) {
                        ?>
                            <a href="#" title="Adicionar Observação" class="std-btn sm-btn add-finan-obs">Adicionar</a>
                        <?php } //nivel de acesso 
                        ?>
                    </div>

                    <div class="panel-content panel-finan" style="display: block;">

                        <?php
                        $observacoes_financeiro = $acompanhamento->getObservacoesFinanceiro();
                        if (!DataValidator::isEmpty($observacoes_financeiro)) {
                            foreach ($observacoes_financeiro as $obs) {
                        ?>
                                <div class="box box-obs-finan">

                                    <label>
                                        <span class="fl w-335">
                                            Data: <strong><?php echo date('d/m/Y', strtotime($obs->getDataEntrada())); ?></strong>
                                            <br>
                                            Usuário: <strong><?php echo $obs->getRespCadastro(); ?></strong>
                                        </span>

                                        <br class="clear">
                                        <br>

                                        <?php
                                        if (
                                            !DataValidator::isEmpty($responsabilidades) &&
                                            isset($responsabilidades[4]) &&
                                            $responsabilidades[4]['acao'] == 'E' &&
                                            isset($usuario_logado) &&
                                            !DataValidator::isEmpty($usuario_logado) &&
                                            $usuario_logado->getId() == $obs->getUsuarioCadastroId()
                                        ) {
                                        ?>

                                            <a href="#" title="Excluir" class="std-btn gray-btn del-btn del-campo" data-id="<?php echo $obs->getId(); ?>" data-acao="acompanhamento-fin-obs">Excluir</a>
                                        <?php } //nivel de acesso 
                                        ?>

                                    </label>

                                    <textarea name="observacao_financeiro[]" readonly rows="5" class="std-input finan-obs"><?php echo $obs->getMensagem(); ?></textarea>
                                    <input type="hidden" name="obs_finan_id[]" value="<?php echo $obs->getId(); ?>">

                                </div>
                                <!-- obs gravada -->

                        <?php }
                        } else {
                            echo "<p>Nenhuma observação inserida.</p>";
                        } ?>

                    </div>
                    <!-- panel content -->

                </div>
                <!-- panel obs -->
            </div>
            <div class="campo-box clearfix">
                <?php
                if (DataValidator::isEmpty($sacado) || (!DataValidator::isEmpty($sacado) && !$sacado->getPodeEmitir())) {
                ?>
                    <a href="javascript:void(0);" class="std-btn sm-btn" disabled title="Para emitir um boleto é necessário ter salvo um sacado para este Acompanhamento">Emitir Boleto</a>
                <?php
                } else {
                ?>
                    <a href="#" class="std-btn sm-btn emissao-boleto-btn link-lightbox" data-rel="box-emissao-boleto" title="Emitir Boleto">Emitir Boleto</a>
                <?php
                }
                ?>


                <!-- <a href="#" class="std-btn sm-btn emisao-nfe-btn link-lightbox" data-rel="box-emissao-nfe" title="Emitir NF-e">Emitir NF-e</a> -->
            </div>
        <?php
        }
        ?>
        <!-- campo -->
    </div>

</div>