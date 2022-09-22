<?php		
    $responsabilidades = isset($usuario_logado) ? $usuario_logado->getPerfil()->getResponsabilidades() : null;		
?>

<div class="faixa-admin">

<div class="inner">

    <div class="logo">
        <img src="img/logo.png" alt="">
    </div>
    
    <!-- logo -->

    <div class="box-admin">

    <i class="avatar"></i>
    <span class="username"><?php echo isset($usuario_logado) && !DataValidator::isEmpty($usuario_logado) ? $usuario_logado->getNome() : ''; ?></span>
    <i class="seta seta-frente"></i>

    <ol>          
    <li><a href="?controle=Index&acao=logout" title="">Sair do sistema</a></li>
    </ol>

    </div>
    <!-- box admin -->	

</div>
<!-- inner -->

</div>
<!-- faixa admin -->