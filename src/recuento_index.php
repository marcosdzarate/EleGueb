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
	
    $orden = null;
	if (isset($_GET["orden"])) {
		$orden=$_GET["orden"];
		$m = validar_orden ($orden,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
	
    $x= null;
	if (isset($_GET["xtrx"])) {
		$x=$_GET["xtrx"];
		$m = validar_referencia ($x,$v,true);
	}
	/* puede estar o no 
	else{
			$v=false;
		}  */
	siErrorFuera($v);	

require_once 'tb_param.php';  /* por $condi y $param */



if (empty($_SESSION['referencia']) ) {
	$_SESSION['referencia']=$xtrx;
}



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

<script src="js/imiei.js"></script>


<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       ini_datatable ('#tablax' , 'recuento_tb.php?condi=<?php echo $condi;?>',-15,0) ;
	   $('#tablax').on( 'draw.dt', function () {
			botonLibreta();
		} );
	   
} );

</script>

<script>
function botonLibreta(){
   var info = $("#tablax").DataTable().page.info();
   var b0 = document.getElementById("btnLibreta0");
   var b1 = document.getElementById("btnLibreta1");
   if(info.recordsDisplay==0){
	   b0.style.display='block';
	   b1.style.display='block';
   }
	else{
	   b0.style.display='none';		
	   b1.style.display='none';		
	}
}
</script>


<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body >
    <div class="container well"  style="width:90%">
		
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">referencia: <?php echo $_SESSION['referencia'];?></h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha censo</th>
                          <th>Libreta</th>
                          <th>Orden</th>
                          <th>Categor&iacute;a</th>
                          <th>Sexo</th>
                          <th>Status macho</th>
                          <th>Cantidad</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
                   <?php
                     if (edita() and $_SESSION['tipocen']<>'MENSUAL'   and editaCenso($fecha) ) {
                         ECHO '<div class="col-sm-2"><a href="recuento_crear.php'. $param .'" class="btn btn-primary btn-sm">nuevo</a></div>';
                         ECHO "\r\n".'<div class="col-sm-3"><a id="btnLibreta0" href="recuento_ingresaLibreta.php'. $param .'&mod=0" class="btn btn-primary btn-sm">nuevo como libreta 0</a></div>';
						 ECHO "\r\n".'<div class="col-sm-3"><a id="btnLibreta1" href="recuento_ingresaLibreta.php'. $param .'&mod=1" class="btn btn-primary btn-sm">nuevo como libreta 1</a></div>';
                         }
                    ?>
					<div class="col-sm-3">
                    <a class="btn btn-default btn-sm" onclick=parent.cierroM("grupo_index.php<?php echo $param0;?>")>a Grupo</a>
					</div>
		
                    </p>

    </div> <!-- /container -->

  </body>


</html>