<?php
 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	if (isset($_POST['descarga']))  // lo de pantalla
	{
		$xFil="";
		if (isset($_SESSION['publiQWhere'])) {
			$xFil=$_SESSION['publiQWhere'];
		}
		$pasaArch="Datos_de_censoBasico";	
		$sql  =  "CALL `vw_tabtem_censo_ampliado`();";
		$sql .= "SELECT fecha,tipo,fechaTotal,zona FROM tt_censo_ampliado $xFil ORDER BY 1;";
		XlsXOnDeFlai($sql,$pasaArch);
	} 

	if (isset($_POST['descargaDatos']))  // lo de pantalla
	{
		$xFil="";
		if (isset($_SESSION['publiQWhere'])) {
			$xFil=$_SESSION['publiQWhere'];
		}
		$pasaArch="Datos_de_censos";	
		$sql  =  "CALL `vw_tabtem_censo_ampliado`();";
		$sql .= "SELECT vw_censo_con_copiados.* FROM tt_censo_ampliado,vw_censo_con_copiados $xFil and vw_censo_con_copiados.fecha=tt_censo_ampliado.fecha ORDER BY 1,2,3,4,5;";
		XlsXOnDeFlai($sql,$pasaArch);
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
       ini_datatable ('#tablax' , 'censo_tb.php',15,0) ;
	   
} );

</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	


</head>

<body  class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_censo.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
    <div class="container well"  style="text-align:left;width:950px">
            <div class="row">
                <h4>Censos en REPRODUCCI&Oacute;N y MUDA de elefantes marinos</h4>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">d&iacute;as de censo</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha censo</th>
                          <th>Tipo</th>
                          <th>Fecha total</th>
						  <th>Zonas</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <div class="row">
					<div class="col-sm-3">
                   <?php
                     if (edita()) {
                         ECHO '<a onclick=ventanaM("censo_crear.php",this.title) title="agregar nuevo d&iacute;a de censo" class="btn btn-primary  btn-sm">nuevo</a>&nbsp;&nbsp;';
                         }
                    ?>
                    
					</div>
					
					<div class="col-sm-3">				
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descarga  class="btn btn-warning btn-sm" >descargar esta lista</button>
						</form>
					</div>
					<div class="col-sm-3">				
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descargaDatos  class="btn btn-warning btn-sm" >descargar datos</button>
						</form>
					</div>
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