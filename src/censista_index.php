<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	$v=true;

    $fecha = null;
	if (isset($_GET["fecha"])) {
		$fecha=$_GET["fecha"];
		$m = validar_fecha ($fecha,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);


    $libreta = null;
	if (isset($_GET["libreta"])) {
		$libreta=$_GET["libreta"];
		$m = validar_libreta ($libreta,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);


require_once 'tb_param.php';     /* por $condi y $param */
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
 <link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title>Elefantes marinos del sur de Pen&iacute;sula Vald&eacute;s</title>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

<script src="js/imiei.js"></script>


<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       ini_datatable ('#tablax' , 'censista_tb.php?condi=<?php echo $condi;?>',15,0) ;
} );

</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	



</head>

<body class=bodycolor>
    <div class="container"  style="width:80%">
	
<button type="button" class="botonayuda" style="background-color: #344560" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">lista</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Libreta</th>
                          <th>Censista</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
                   <?php
                     if (edita() and $_SESSION['tipocen']<>'MENSUAL') {
                         ECHO '<a href="censista_crear.php'. $param .'" class="btn btn-primary btn-sm">nuevo</a>&nbsp;&nbsp;';
                         }
                    ?>
                    <a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>a Sector</a>				</p>

    </div> <!-- /container -->

  </body>


</html>