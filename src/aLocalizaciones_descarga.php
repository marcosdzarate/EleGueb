<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

function dameDatosViaje ($ptt,$viajeID,$datosViajes,$ndv,&$sexo,&$cate,&$marca,&$ttempo){
	/* buscamos datos en datosViajes para completar descarga */
	for ($v=0; $v<$ndv; $v++) {
		if ($viajeID==$datosViajes[$v]["viajeID"] and $ptt==$datosViajes[$v]["ptt"] ) {
			$sexo=$datosViajes[$v]["sexo"];
			$cate=$datosViajes[$v]["categoria"];
			$marca=$datosViajes[$v]["marcas"];
			$ttempo=$datosViajes[$v]["tipoTempo"];
			break;
		}
	}
	
}


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

		/* genera vtResultado, tabla temporaria usada por los siguientes procedures */
		$condi="''";
		$sql = "CALL `locx-viajes Seleccion`($condi)";
		if (mysqli_query($con,$sql)===false){
			$eError="Problemas con CALL 0. ";
			$valid=false;
		}


		/* la siguiente llamada da una tablita con datos por viaje */
		$sql="CALL `locx-viajes y tracks`()";
		if(mysqli_real_query($con, $sql)===false){
		  $valid=false;
		  $eError = "Problemas con CALL 1".mysqli_error($con);
		}
		else
		{
			$result=mysqli_store_result($con);
			/* $datosViajes=mysqli_fetch_all ($result,MYSQLI_ASSOC); NO ME SIRVE EN SERVER */
			while ($fila = mysqli_fetch_assoc($result)) {
			  $datosViajes[] = $fila;
			}					
			mysqli_free_result($result);
			$ndv=count($datosViajes);
					/* vacio result sets ocultos */
					while (mysqli_more_results($con) && mysqli_next_result($con)) 
					{
						if($result=mysqli_store_result($con)) {
							mysqli_free_result($result);
						}
					}	
		}
		
		mysqli_close($con);



$ptt=null;
$viajeID=null;
$sexo=null;
$cate=null;
$marca=null;
$ttempo=null;

	if (isset($_POST["queGrupo"]) and $valid) {
		$queIndi=$_POST["queIndi"];
	    switch ($queIndi) {
			case "HembrasAdu" :
				$c1 = " sexo='HEMBRA' and reproductiva='SI' ";
				break;
			case "MachosAdu" :
				$c1 = " sexo='MACHO' and reproductiva='SI' ";
				break;
			case "Juveniles" :
				$c1 = " reproductiva='NO' ";
				break;
		}
		$queGrupo = $_POST["queGrupo"];
	    switch ($queGrupo) {
			case "todos" :
				break;
			case "muda" :
				$c1 .= " and locate('muda',lower(tipoTempo))>0 ";
				break;
			case "fuera" :
				$c1 .= " and locate('fuera',lower(tipoTempo))>0 ";
				break;
			case "repro" :
				$c1 .= " and locate('repro',lower(tipoTempo))>0 ";
				break;
		}
		
		$pasaArch="localizaciones-$queIndi-$queGrupo";
		$condi="''";
		$pasaSQL = "CALL `locx-viajes Seleccion`($condi);";	/* sale tabla temporaria vtResultado */
		$pasaSQL .="SELECT localizaciones.viajeID,localizaciones.ptt,
		             fecha,hora,locQuality,lon,lat,estado,
					 distanciaPDelgada,distanciaSalida,distanciaKM_p,velocidadKM_p,
					 tiempoH_p,velocidadFiltro,sexo,categoria,marcas,tipoTempo,ultimo
		FROM localizaciones,vtResultado WHERE $c1 
		      and localizaciones.viajeID=vtResultado.viajeID AND
			localizaciones.ptt=vtResultado.ptt ORDER BY 1,2,3,4";
		
		//XlsXOnDeFlai($pasaSQL,$pasaArch);
		csvOnDeFlai($pasaSQL,$pasaArch);		
		
	}
	else if (isset($_POST["ptt"]) and isset($_POST["viajeID"]) and $valid)
		{
			/*parametros inválidos, fuera*/        
			$valid=true;	
			$viajeID = null;
			if (isset($_POST["viajeID"])) {
				$viajeID=$_POST["viajeID"];
				$r=validar_entero ($viajeID,1,50000,$valid,true);
			}	
			siErrorFuera($valid);	

			$ptt = null;
			if (isset($_POST["ptt"])) {
				$ptt=$_POST["ptt"];
				$r=validar_entero ($ptt,0,5000000,$valid,true);
			}	
			siErrorFuera($valid);

			/* buscamos datos en datosViajes para completar descarga */
			dameDatosViaje ($ptt,$viajeID,$datosViajes,$ndv,$sexo,$cate,$marca,$ttempo);
			/* descarga */		
			$pasaArch="localizaciones-$viajeID-$ptt";
			$pasaSQL ="SELECT viajeID,ptt,fecha,hora,locQuality,lon,lat,estado,distanciaPDelgada,distanciaSalida,distanciaKM_p,velocidadKM_p,tiempoH_p,velocidadFiltro,'$sexo' as sexo,'$cate' as categoria,'$marca' as marcas,'$ttempo' as tipoTempo,ultimo
			FROM localizaciones WHERE viajeID=$viajeID and ptt=$ptt ORDER BY 1,2,3,4";
			
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
	padding:2px 6px;
	line-height:1;
}
</style>
   
    
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 
   
    var tablin = $("#tablax").DataTable( {
     stateSave: true,
	  "order": [[ 7, "desc" ],[ 0, "asc" ]],
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
    
 
        
       
} );

</script>
<script>
function descargaTrack(viaje,ptt) {
	document.getElementById("viajeID").value=viaje;
	document.getElementById("ptt").value=ptt;
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
			<h3>viajes con localizaciones para mapear/descargar</h3>
	</div>
    <div class="container well"  style="font-size:14px;width:80%" >
	    descargar selecci&oacute;n
		<form id="formDescarga" class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<input type="hidden" id="ptt" name="ptt" value="<?php echo $ptt;?>"/>
					<input type="hidden" id="viajeID" name="viajeID" value="<?php echo $viajeID;?>"/>
			<div class="row">
				<div class="col-sm-1">
				</div>
				<div class="col-sm-6">			
					<label class="radio-inline"><input type="radio" value="HembrasAdu" name="queIndi" checked>hembras adultas</label>
					<label class="radio-inline"><input type="radio" value="MachosAdu" name="queIndi">machos adultos</label>
					<label class="radio-inline"><input type="radio" value="Juveniles" name="queIndi">juveniles</label>
				</div>
				<div class="col-sm-1">
					<button type="submit" value="todos" name="queGrupo" class="btn btn-warning btn-sm">todos</button>
				</div>
				<div class="col-sm-1">
					<button type="submit" value="muda" name="queGrupo" class="btn btn-warning btn-sm">muda</button>
				</div>
				<div class="col-sm-1">
					<button type="submit" value="fuera" name="queGrupo" class="btn btn-warning btn-sm">fuera</button>
				</div>
				<div class="col-sm-1">
					<button type="submit" value="repro" name="queGrupo" class="btn btn-warning btn-sm">repro</button>
				</div>
			</div>
		</form>
			
	</div>	

    <div class="container well"  style="width:80%">



		<br>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">mapa/descarga individual</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>viajeID</th>
                          <th>ptt</th>
                          <th>claveU</th>
                          <th>sexo</th>
                          <th>marcas</th>
                          <th>categor&iacute;a</th>
                          <th>temporada (POST...)</th>
                          <th>fecha de colocaci&oacute;n</th>
                          <th>playa</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
					  <tbody>
					  
					  
					  
<?php


					
					/* muestro datos del arreglo desde el ultimo al primero */
					for ($v=$ndv-1; $v>=0; $v--)
					{
						if($datosViajes[$v]['conLoc']=='tiene') {
							echo "<tr>";
							echo "<td>".$datosViajes[$v]['viajeID']."</td>";
							echo "<td>".$datosViajes[$v]['ptt']."</td>";
							echo "<td><a title='ir a la ficha' target=_blank href='tr_resumen_individuo.php?claveU=".$datosViajes[$v]['claveU']."'>".
								$datosViajes[$v]['claveU']."</a></td>";
							echo "<td>".$datosViajes[$v]['sexo']."</td>";
							echo "<td>".$datosViajes[$v]['marcas']."</td>";
							echo "<td>".$datosViajes[$v]['categoria']."</td>";
							echo "<td>".$datosViajes[$v]['tipoTempo']."</td>";
							echo "<td>".$datosViajes[$v]['fecha_colocacion']."</td>";
							echo "<td>".$datosViajes[$v]['nombre']."</td>";
							echo '<td>';
							echo ' <a title="localizaciones para " onclick=ventanaM("mapin_local.php?ID='.$datosViajes[$v]['viajeID'].'&mar='.str_replace(", ","_",$datosViajes[$v]['marcas']).'&ptt='.str_replace(", ","_",$datosViajes[$v]['ptt']).'",this.title) class="btn btn-info btn-sm bchico"><span class="glyphicon glyphicon-map-marker"></span> </a>';							
							
							echo '<a class="btn btn-warning" title="descargar localizaciones" onclick=descargaTrack('.urlencode($datosViajes[$v]['viajeID']).','.$datosViajes[$v]['ptt'].')>'.
								 '<span class="glyphicon glyphicon-download"></span></a></td>';
							echo "</tr>";

							
						}
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


    



<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

  </body>


</html>