<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="js/jquery.easing.js"></script>
<script src="js/selectivizr-min.js"></script>
<script src="js/jquery.mask.min.js"></script>
<script src="js/scripts.js?t=<?php echo time(); ?>"></script>
<!-- <script src="js/autocompletar.jornal.js"></script> -->

<?php

    $noFooter = array('index', 'login');
    $controller = strtolower(isset($_REQUEST['controle']) && !empty($_REQUEST['controle']) ? $_REQUEST['controle'] : 'index');

    if(in_array($controller, $noFooter) == false){

        include("footer.inc.php");
    }
?>

<?php
$path = $_SERVER['REQUEST_URI'];
if(stripos($path, 'homologacao')){
    echo '<div style="z-index:999999; width:160px; position: fixed; bottom: 10px; right:30%; padding:5px 0; display:block; background:red; color:white; font-size:11px; text-align:center">HOMOLOGAÇÃO</div>';
}
?>