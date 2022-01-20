<?php
/* descarga de los datos de buceos 
   de la tabla a0001_buceos (resumen de cada buceo)*/
   
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

/* datos de los viajes */
	$valid = true;
	$eError=null;
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
		{
		  $valid=false;
		  $eError = "No se puede conectar a MySQL: " . mysqli_connect_error();
		}
	else
	{				

		/* la siguiente llamada da una tablita con datos por viaje-buceo */
		$sql="CALL `Buc-para descarga buceos`()";
		if(mysqli_real_query($con, $sql)===false){
		  $valid=false;
		  $eError = "Problemas con CALL 0".mysqli_error($con);
		}
		else
		{
			$result=mysqli_store_result($con);
			/* $datosBuc=mysqli_fetch_all ($result,MYSQLI_ASSOC); NO ME SIRVE EN SERVER */
			while ($fila = mysqli_fetch_assoc($result)) {
			  $datosBuc[] = $fila;
			}					
			mysqli_free_result($result);
			$ndv=count($datosBuc);
					/* vacio result sets ocultos */
					while (mysqli_more_results($con) && mysqli_next_result($con)) 
					{
						if($result=mysqli_store_result($con)) {
							mysqli_free_result($result);
						}
					}	
		}
		
		mysqli_close($con);



$claveU=null;
$nroViaje=null;
$tabDeta=null;

		
 if (isset($_POST["claveU"]) and isset($_POST["nroViaje"]) and isset($_POST["tabDeta"])  and $valid)
		{
			/*parametros inválidos, fuera*/        
			$valid=true;	
			$nroViaje = null;
			if (isset($_POST["nroViaje"])) {
				$nroViaje=$_POST["nroViaje"];
				$r=validar_entero ($nroViaje,1,100,$valid,true);
			}	
			siErrorFuera($valid);	

			$claveU = null;
			if (isset($_POST["claveU"])) {
				$claveU=$_POST["claveU"];
				$r=validar_claveU ($claveU,$valid,true);
			}	
			siErrorFuera($valid);

			$tabDeta = null;
			if (isset($_POST["tabDeta"])) {
				$tabDeta=$_POST["tabDeta"];
				$r=validar_archivo ($tabDeta,$valid,true);
			}	
			siErrorFuera($valid);

			/* descarga */		
			$pasaArch="buceos-$tabDeta";
			$pasaSQL ="SELECT claveU,nroViaje,nroBuceo,julianoIni,duracion, profundidadMax,julianoMax,profundidadMed,profundidadMin, temperaturaFondo,temperaturaSuperficie,IntervaloEnSuperficie,donde,longi,lati 
			FROM a0001_buceos WHERE claveU='$claveU' and nroViaje=$nroViaje ORDER BY 3";
			//XlsXOnDeFlai($pasaSQL,$pasaArch);	
			csvOnDeFlai($pasaSQL,$pasaArch);
				
		}
	
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>

  <link rel="stylesheet" href="assetsMobi/web/assets/mobirise-icons/mobirise-icons.css">
  <link rel="stylesheet" href="assetsMobi/bootstrap/css/bootstrap.min.css">

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

    <link rel="stylesheet" href="login/style/main.css">

<script src="js/imiei.js"></script>

	
    <link   href="css/miSideBar.css" rel="stylesheet">

 
<style>
.table > tfoot > tr > th > input {
	width:100%
}
.bchico {
	padding:0px 4px;
	line-height:1;
}
</style>
   
    
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 
   
    var tablin = $("#tablax").DataTable( {
     stateSave: true,
	  "order": [[ 7, "desc" ],[ 3, "asc" ]],
     "stateDuration": -1, 
     "pageLength": 15,
     "lengthMenu": [ 5,10, 15, 30, 70, 100 ],
     "pagingType": "full_numbers",
     "language": {
         "emptyTable": "No hay datos disponibles en la tabla",
         "lengthMenu": "Mostrar _MENU_ registros",
         "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
         "infoFiltered":   "(filtrado de _MAX_ registros totales)",
         "infoEmpty":      "L&iacute;nea 0 de 0",
         "loadingRecords": "Cargando...",
         "processing":    "Procesando...",
         "search":         "Buscar por cualquier t&eacute;rmino:",
         "zeroRecords":    "No se encuentran registros",
         "paginate": {
            "first":      "|<<",
            "last":       ">>|",
            "next":       ">",
            "previous":   "<"
            },   
         "aria": {
            "sortAscending":  ": activar para ordenar la columna ascendente",
            "sortDescending": ": activar para ordenar la columna descendente"
            }   
         }

    } );
    $('#tablax').DataTable().column( 1 ).visible( false );
    $('#tablax').DataTable().column( 3 ).visible( false );
    $('#tablax').DataTable().column( 5 ).visible( false );
  
 
        
       
} );

</script>
<script>
function descargaTrack(viaje,clU,tabD) {
	document.getElementById("claveU").value=clU;
	document.getElementById("nroViaje").value=viaje;
	document.getElementById("tabDeta").value=tabD;
	document.getElementById("formDescarga").submit();
}
</script>


	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
	


</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_oremota.html"></div>

	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
	
    <div class="container well"  style="font-size:14px;width:80%" >
			<h3>viajes con buceos para graficar/descargar</h3>
	</div>	
    <div class="container well"  style="width:80%">

		<form id="formDescarga" class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<input type="hidden" id="claveU" name="claveU" value="<?php echo $claveU;?>"/>
					<input type="hidden" id="nroViaje" name="nroViaje" value="<?php echo $nroViaje;?>"/>
					<input type="hidden" id="tabDeta" name="tabDeta" value="<?php echo $tabDeta;?>"/>
		</form>
		<br>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">lista</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>viaje ID</th>
                          <th>id</th>
                          <th>claveU</th>
                          <th>viaje#</th>
                          <th>tabla detalle</th>
                          <th>a&ntilde;o col</th>
                          <th>marca</th>
                          <th>fecha de colocaci&oacute;n</th>
                          <th>temporada (POST...)</th>
                          <th>categor&iacute;a</th>
                          <th>sexo</th>
                          <th>repro?</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
					  <tbody>
					  
					  
					  
<?php


					
					/* muestro datos del arreglo  */
					for ($v=0; $v<$ndv; $v++)
					{
							echo "<tr>";
							echo "<td>".$datosBuc[$v]['viajeID']."</td>";
							echo "<td>".$datosBuc[$v]['ID']."</td>";
							echo "<td><a title='ir a la ficha' target=_blank href='tr_resumen_individuo.php?claveU=".$datosBuc[$v]['claveU']."'>".
								$datosBuc[$v]['claveU']."</a></td>";
							echo "<td>".$datosBuc[$v]['nroViaje']."</td>";
							echo "<td>".$datosBuc[$v]['tablaDetalle']."</td>";
							echo "<td>".substr($datosBuc[$v]['fecha_colocacion'],0,4)."</td>"; //$datosBuc[$v]['anio_colocacion']
							echo "<td>".$datosBuc[$v]['marca']."</td>";
							echo "<td>".$datosBuc[$v]['fecha_colocacion']."</td>";
							echo "<td>".$datosBuc[$v]['tipoTempo']."</td>";
							echo "<td>".$datosBuc[$v]['categoria']."</td>";
							echo "<td>".$datosBuc[$v]['sexo']."</td>";
							echo "<td>".$datosBuc[$v]['reproductiva']."</td>";
							$xcu=$datosBuc[$v]['claveU'];
							$nvia=$datosBuc[$v]['nroViaje'];
							$xde=$datosBuc[$v]['tablaDetalle'];
							echo "<td><a title='graficar resumen de buceos' 
							 href='aBuceos_grafico.php?claveU=$xcu&nroViaje=$nvia&xde=$xde' target=_blank class='btn btn-info btn-sm bchico'>buc</a>";							
							echo '	 <a class="btn btn-warning" title="descargar resumen de buceos" 
							onclick=descargaTrack('.urlencode($datosBuc[$v]['nroViaje']).',"'.urlencode($datosBuc[$v]['claveU']).'","'.urldecode($datosBuc[$v]['tablaDetalle']).'")>'.
								 '<span class="glyphicon glyphicon-download"></span></a> </td>';
							echo "</tr>";

					}
					
				}



?>					  
					  
					  
					  
					  
					  
						</tbody>					  
                    </table>

                </div>


				
				
				
				<div class="row">
					<div class="col-sm-1">
					</div>
					<div class="col-sm-8">
						<?php if (!empty($eError)): ?>
							<span class="help-inline"><?php echo $eError;?></span>
						<?php endif; ?>

					</div>
				</div>                
				
				
			</div>

    </div> <!-- /container -->


    


<!-- para menu lateral/ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>



  </body>


</html>