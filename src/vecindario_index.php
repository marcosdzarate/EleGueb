<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	if (isset($_POST['descarga']))
	{
		$laTabla='vecindario';
		$pasaArch="Datos_de_".$laTabla;	
		$sql= "SELECT * FROM $laTabla WHERE IDesquema <> 999 ORDER BY IDesquema,NSdesde;";
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

    <script src="js/imiei.js"></script>

	
    <link   href="css/miSideBar.css" rel="stylesheet">


<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       ini_datatable ('#tablax' , 'vecindario_tb.php',15,0.03) ;
} );

</script>


	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		

</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_mdatos.html"></div>

	
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



    <div class="container well"  style="width:70%">
            <div class="row">
                <h4>vecindarios <small>playas/tramos vecinos geogr&aacute;ficamente; pueden agruparse en esquemas</small></h4>
            </div>

            <div class="panel panel-grisDark">
                <div class="panel-heading">
                    <h3 class="panel-title">vecindarios</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>IDesquema</th>
                          <th>IDvecindario</th>
                          <th>Tipo</th>
                          <th>N-S desde</th>
                          <th>N-S hasta</th>
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
							 ECHO '<a title="agregar nuevo vecindario" onclick=ventanaM("vecindario_crear.php",this.title) class="btn btn-primary btn-sm" >nuevo</a>&nbsp;&nbsp;';
							 }
						?>

					</div>
					
					<div class="col-sm-3">					
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
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