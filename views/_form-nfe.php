<!-- start: lightbox msg -->
<div class="lightbox lightbox-emissao-nfe" id="box-emissao-nfe">

    <div class="header">Impressão de Nfe</div><!-- header -->

    <div class="content">

        <form class="form-emissao-nfe std-form" action="tela-emissao-nfe-proposta.php" target="_blank" method="post">
            <input type="hidden" class="reemissao-nfe" name="reemissao-nfe" value="">
            <input type="hidden" name="proposta_id" value="<?php echo !DataValidator::isEmpty($proposta) ? $proposta->getId() : 0; ?>">
            <input type="hidden" name="user_id" value="<?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getId() : 0; ?>">

            <table>
                <tr>
                    <td>Conteudo pra Emissão</td>
                </tr>
            </table>

            <div class="controles clearfix">
                <a href="#" title="Cancelar" class="std-btn close-lightbox fl">Cancelar</a>
                <input type="button" value="Emitir nfe" class="std-btn send-btn fr" id="btn-emite-nfe">
            </div>
        </form>

    </div><!-- content -->

</div>
<!-- end: lightbox msg -->