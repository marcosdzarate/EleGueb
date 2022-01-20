<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';


    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
            $v=false;
        }
    siErrorFuera($v);

    $fecha = null;
    if (isset($_GET["fecha"])) {
        $fecha=$_GET["fecha"];
        $m = validar_fecha ($fecha,$v,true);
    }
    else{
            $v=false;
        }
    siErrorFuera($v);
	
    $xtrx = null;  /* sexo */
    if (isset($_GET["xtrx"])) {
        $xtrx=$_GET["xtrx"];
        $v = ($xtrx=="HEMBRA" or $xtrx=="MACHO");
    }
    else{
        if (!isset($_GET["xtrx"]) and !isset($_POST["xtrx"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);
	
require_once 'tb_param.php';        /*por $condi y $param y $xtrx*/

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
 <title><?php echo siglaGrupe ?></title>

 <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">

    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

    <link rel="stylesheet" href="login/style/main.css">

    <link   href="css/miSideBar.css" rel="stylesheet">

<script src="js/imiei.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
       ini_datatable ('#tablax' , 'copula_tb.php?condi=<?php echo $condi;?>&xtrx=<?php echo $xtrx;?>',15,2) ;
} );

</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body>

    <div class="container well"  style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">c&oacute;pulas en la fecha</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>ClaveU</th>
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Con cu&aacute;l</th>
                          <th>Duraci&oacute;n</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
                   <?php
                     if (edita()) {
                         ECHO "<a href='copula_crear.php$param&xtrx=$xtrx' title='agregar anestesia' class='btn btn-primary btn-sm'>nuevo</a>&nbsp;&nbsp;";
                         }
                    ?>
                    <a class="btn btn-default  btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php ECHO $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </p>

    </div> <!-- /container -->

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

  </body>


</html>