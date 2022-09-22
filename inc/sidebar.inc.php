<div id="sidebar" class="clearfix">

    <nav id="menu">

        <?php if (isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) && !DataValidator::isEmpty($responsabilidades)) { ?>
            <ul>

                <?php if (isset($responsabilidades[1])) { ?>
                    <li class="main-item"><a href="?controle=Processo&acao=cadastro" title="Cadastro de Processo">Cadastro de Processo</a></li>
                <?php } ?>

                <?php if (isset($responsabilidades[2])) { ?>
                    <li class="main-item"><a href="?controle=Processo&acao=LimpaBusca" title="Processo">Processo</a></li>
                <?php } ?>

                <?php if (isset($responsabilidades[3])) { ?>
                    <li class="main-item"><a href="?controle=Proposta&acao=LimpaBusca" title="Proposta">Proposta</a></li>
                <?php } ?>

                <?php if (isset($responsabilidades[4])) { ?>
                    <li class="main-item"><a href="?controle=Acompanhamento&acao=LimpaBusca" title="Acompanhamento Processual">Acompanhamento Processual</a></li>
                <?php } ?>


                <?php
                if (
                    isset($responsabilidades[5]) ||
                    isset($responsabilidades[6]) ||
                    isset($responsabilidades[7]) ||
                    $usuario_logado->getPerfil()->getNome() == 'Administrador'
                ) {
                ?>
                    <li class="main-item sub arrow-right">
                        <a href="#" title="Cadastro"> Cadastro</a>
                        <ol>

                            <?php if (isset($responsabilidades[5])) { ?>
                                <li><a href="?controle=Advogado&acao=index" title="Advogado">Advogado</a></li>
                            <?php } ?>

                            <?php if (isset($responsabilidades[6])) { ?>
                                <li><a href="?controle=Jornal&acao=index" title="Jornal">Jornal</a></li>
                            <?php } ?>

                            <?php if (isset($responsabilidades[7])) { ?>
                                <li><a href="?controle=Secretaria&acao=index" title="Secretaria/Fórum">Secretaria/Fórum</a></li>
                            <?php } ?>

                            <?php if ($usuario_logado->getPerfil()->getNome() == 'Administrador') { ?>
                                <li><a href="?controle=Usuario&acao=index" title="Usuário">Usuário</a></li>

                                <li><a href="?controle=Perfil&acao=index" title="Nível de Acesso">Nível de Acesso</a></li>

                                <li><a href="?controle=Status&acao=index" title="Status de Acompanhamento">Status de Acompanhamento</a></li>
                            <?php } ?>

                        </ol>
                    </li>
                <?php } ?>

                <?php if (isset($responsabilidades[8])) { ?>
                    <li class="main-item"><a href="?controle=Relatorio&acao=index" title="Relatório Comercial">Relatório Comercial</a></li>
                <?php } ?>

                <?php
                if (
                    isset($responsabilidades[9]) ||
                    isset($responsabilidades[10]) ||
                    isset($responsabilidades[11])) {
                ?>
                    <li class="main-item sub arrow-right">
                        <a href="#" title="Financeiro"> Financeiro</a>
                        <ol>

                            <?php if (isset($responsabilidades[9])) { ?>
                                <li><a href="?controle=Sacado&acao=index" title="Sacados">Sacados</a></li>
                            <?php } ?>

                            <?php if (isset($responsabilidades[10])) { ?>
                                <li><a href="?controle=Boleto&acao=index" title="Boletos">Boletos</a></li>
                            <?php } ?>

                            <?php if (isset($responsabilidades[11])) { ?>
                                <li><a href="?controle=RelatorioFinanceiro&acao=index" title="Relatórios Financeiros">Relatórios Financeiros</a></li>
                            <?php } ?>

                        </ol>
                    </li>
                <?php } ?>

            </ul>
        <?php } ?>

    </nav><!-- menu -->

</div>