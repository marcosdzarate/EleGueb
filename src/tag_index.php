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


require_once 'tb_param.php';        /*por $condi y $param */

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
       ini_datatable ('#tablax' , 'tag_tb.php?condi=<?php echo $condi;?>',10,2) ;
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
                    <h3 class="panel-title">tags en la fecha</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>ClaveU</th>
                          <th>Fecha</th>
                          <th>Tag</th>
                          <th>A&ntilde;o borrado</th>
                          <th>A&ntilde;o perdido<br>/encontrado</th>
                          <th>Playa encontrado</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
                   <?php

                     if (edita()) {
                         ECHO '<a href="tag_crear.php'. $param .'" title="agregar tag" class="btn btn-primary btn-sm">nuevo</a>&nbsp;&nbsp;';
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