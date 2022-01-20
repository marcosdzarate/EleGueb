<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	if (isset($_POST['descarga']))
	{
		$xFil="";
		if (isset($_SESSION['publiQWhere'])) {
			$xFil=$_SESSION['publiQWhere'];
		}
		$laTabla='grupo';
		$pasaArch="Datos_de_grupos_".$_SESSION['tipocen']."_".$_SESSION['fecTot']."_".$_POST['libreta'];	
		$sql = "SELECT fecha,libreta,orden,playa.nombre as playa,referencia,desde,tipoPlaya,grupo.geomTex,grupo.lati,grupo.longi,latiH,longiH,distancia,grupo.comentario
 FROM $laTabla,playa $xFil and (grupo.playa=playa.IDplaya) ORDER BY 1,2;";
		XlsXOnDeFlai($sql,$pasaArch);
	} 
	else {
		if (isset($_POST['descargaDatos']))
		{
			$xFil=" WHERE true ";
			if (isset($_SESSION['publiQWhere'])) {
				$xFil=$_SESSION['publiQWhere'];
			}
			$pasaArch="Datos_de_censos";	
			$sql = "SELECT vw_censo_con_copiados.* FROM vw_censo_con_copiados,(SELECT grupo.fecha,grupo.libreta,grupo.orden  FROM grupo,playa $xFil) as t1 
			WHERE vw_censo_con_copiados.fecha=t1.fecha AND vw_censo_con_copiados.libreta=t1.libreta AND vw_censo_con_copiados.orden=t1.orden;";
			XlsXOnDeFlai($sql,$pasaArch);
		} 
	
		else {
			
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
		

		require_once 'tb_param.php';		/*por $condi y $param */
		$_SESSION['referencia']="";

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
       ini_datatable ('#tablax' , 'grupo_tb.php?condi=<?php echo $condi;?>',15,2) ;
} );

</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>

<?php if ($_SESSION['tipocen']=='MENSUAL' )	:?>

	<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=window.open("aayudote/CensosMensuales9500Datos.html");>
		<span class="glyphicon glyphicon-question-sign"></span>
	</button>
	
<?php else : ?>	
	
	<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
		<span class="glyphicon glyphicon-question-sign"></span>
	</button>

<?php endif ?>


<div w3-include-html="sideBar_censo.html"></div>
	
	
	
	

    <div class="container well"  style="width:1100px">
            <div class="row">
                <h4>Censos de elefantes marinos</h4>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">grupos del sector para censo <?php echo $_SESSION['tipocen'];?> </h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha censo</th>
                          <th>Libreta</th>
                          <th>Orden</th>
                          <th>Playa</th>
                          <th>Referencia</th>
                          <th>Conformado OK</th>
                          <th>Playa/sustrato</th>
                          <th>Punto WKT</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <div class="row">
					<div class="col-sm-3">
                   <?php
                     if (edita()  and $_SESSION['tipocen']<>'MENSUAL'   and editaCenso($fecha)   ) {
                         ECHO '<a onclick=ventanaM("grupo_crear.php'. $param .'",this.title) title="agregar grupo al sector" class="btn btn-primary btn-sm">nuevo</a>&nbsp;&nbsp;';
                         }
                    ?>
                    <a class="btn btn-default  btn-sm" href="vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>">a Sector</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</div>
					
				<?php if ($_SESSION['tipocen']<>'MENSUAL'): ?>
					
					<div class="col-sm-3">					
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
							<input type="hidden" name="libreta" name="libreta" value=<?php echo $libreta?>>
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar esta lista</button>
						</form>
					</div>
					<div class="col-sm-3">				
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descargaDatos  class="btn btn-warning btn-sm" >descargar datos</button>
						</form>
					</div>
				<?php endif ?>
					
				</div>
				
			
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