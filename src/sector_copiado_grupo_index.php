<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_database.php';
require_once 'tb_validar.php';

require_once 'tb_param.php';   /* por $condi */

$_SESSION['referencia']="";


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
		$m = validar_libreta_virtual ($libreta,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);


    $eError = null;


     $eError = null;
    if (  ( null==$fecha ) AND  ( null==$libreta ) ) {
        /* sin parametros, da logout*/
        header("Location: tb_logout.php");
        exit;
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM sector_copiado where  fecha =? AND  libreta =?";
        $q = $pdo->prepare($sql);
        $q->execute(array($fecha,$libreta));
        $data = $q->fetch(PDO::FETCH_ASSOC);

        $arr = $q->errorInfo();

        Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            if (!$data) {
                $eError = "No hay datos para esta condici&oacute;n";
            }
			else {
				$condi='fecha="'.$data['fecha_copia'].'" AND libreta="'.$data['libreta_copia'].'" AND '.
				       'orden>='.$data['orden_desde']. ' AND orden<='.$data['orden_hasta'];			
			}
			
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

  <link rel="stylesheet" href="login/style/main.css">

    <link   href="css/miSideBar.css" rel="stylesheet">
	
	
<script src="js/imiei.js"></script>


<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       ini_datatable ('#tablax' , 'sector_copiado_grupo_tb.php?condi=<?php echo $condi;?>',15,1.02) ;
	   $('#tablax').DataTable().column( 8 ).visible( false );
} );

</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_censo.html"></div>

	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



    <div class="container well"  style="width:1100px">
            <div class="row">
                <h4>Censos de elefantes marinos</h4>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">grupos del sector copiado</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha censo</th>
                          <th>Libreta</th>
                          <th>Orden</th>
                          <th>Playa</th>
                          <th>Refer.</th>
                          <th>Conformado OK</th>
                          <th>Playa/sustrato</th>
                          <th>Punto WKT</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>

                    <a class="btn btn-default  btn-sm" href="vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>">a Sector</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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