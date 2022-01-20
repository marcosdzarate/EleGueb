<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_validar.php';


    if ( !empty($_POST)) {
				if (isset($_POST['descarga'])) //Generate text file on the fly
				{
					$pasaArch="Total_censos_mensuales_1995_2000";

					/* descargas */
					$pasaSQL ="CALL `censos-totales MENSUALES`();";
					$pasaSQL .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					//echo $sql;
					XlsXOnDeFlai($pasaSQL,$pasaArch);
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


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>

        <link rel="stylesheet" href="login/style/main.css">
	
    <link   href="css/miSideBar.css" rel="stylesheet">
	

<script src="js/aiuta.js"></script>	
	

	
</head>

<body class=bodycolor >
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_censo.html"></div>

	
<button type="button" class="botonayuda" style="background-color: #344560" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

  <div class="container well" style="width:1000px">

    <div class="panel panel-azulino">
        <div class="panel-heading">
            <h3 class="panel-title">n&uacute;meros totales de censos mensuales 1995-2000</h3>
        </div>
        <br>

		<div class="container">		

 
		
			<form id="CRtotalCensoM" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            
				<div class=row>
				  <div class="col-sm-7">
				  </div>
				  <div class="col-sm-5">
                    <div class="form-actions">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>			   
                    </div>
				  </div>
				</div>
					<br><br>
							
			</form>
		</div> 
    </div> 
  </div> 
	

	

<!-- RESULTADOS -->  

            <div class="panel-body">

                    <div class="panel panel-info" >
                        <div class="panel-heading">Los totales mensuales</div>
                        <div class="panel-body" style="overflow:auto">            

			<?php
			/* Totales*/ 
                $sql ="CALL `censos-totales MENSUALES`();";
				//echo $sql;
                muestraMultipleRS($sql,"botonno");

			?>                          

                        </div>
                    </div>                      
            </div>


<script src="js/w3.js"></script> 
 
<script>
w3.includeHTML();

</script> 


</body>
</html>