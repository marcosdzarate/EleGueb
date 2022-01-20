<?php
/* lista no editable de los sectores (libretas) para una fecha dada*/
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_database.php';

require_once 'tb_validar.php';

	if (isset($_POST['descarga']))
	{
		$xFil="";
		if (isset($_SESSION['publiQWhere'])) {
			$xFil=$_SESSION['publiQWhere'];
		}
		$pasaArch="Datos_de_sectores";	
		$sql = "SELECT * FROM vw_censo_sector $xFil ORDER BY 1,2;";
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
			$sql = "SELECT vw_censo_con_copiados.* FROM vw_censo_con_copiados,(SELECT * FROM vw_censo_sector $xFil) as t1 
			WHERE vw_censo_con_copiados.fecha=t1.fecha AND vw_censo_con_copiados.libreta=t1.libreta;";
			XlsXOnDeFlai($sql,$pasaArch);
		} 
	
		else {

		$v=true;

		$fecha = null;
		if (isset($_GET["fechaTotal"])) {
			$fecha=$_GET["fechaTotal"];
			$m = validar_fecha ($fecha,$v,true);
		}
		else{
				$v=false;
			}
		siErrorFuera($v);
		
		$anio=intval(substr($fecha,0,4));
		
		$x = null;
		if (isset($_GET["xtrx"])) {
			$x=$_GET["xtrx"];
			$queTabla='censo';
			$m = validar_tipo ($x,$v,true);
		}
		/* xtrx puede faltar
		else{
				$v=false;
			}*/
		siErrorFuera($v);	

		require_once 'tb_param.php';			/* por $condi */

		$_SESSION['fecTot'] = $_GET["fechaTotal"];
		if (!empty($_GET["xtrx"])){
		   $_SESSION['tipocen'] = $_GET["xtrx"];
		  }
	  

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
       ini_datatable ('#tablax' , 'vw_censo_sector_tb.php?condi=<?php echo $condi;?>',15,1.02) ;
	        
		/* oculta columna de fechaTotal */
		$('#tablax').DataTable().column( 0 ).visible( false );

} );

</script>

<script type="text/javascript" language="javascript">
function FechaSectorNuevo(fec){
   var bn = document.getElementById("sectorNuevo");
   var bc = document.getElementById("sectorCopiadoNuevo");
   if (fec.length==0) {
       bn.setAttribute("class", "btn btn-primary btn-sm disabled");
       bc.setAttribute("class", "btn btn-primary btn-sm disabled");
   }
   else{
       xhref="sector_crear.php?fecha="+fec;
       bn.setAttribute("onclick", 'ventanaM("'+xhref+'","nuevo sector para el censo")');
       bn.setAttribute("class", "btn btn-primary btn-sm");
       xhref="sector_copiado_crear.php?fecha="+fec;
       bc.setAttribute("onclick", 'ventanaM("'+xhref+'","nuevo sector copiado para el censo")');
       bc.setAttribute("class", "btn btn-primary btn-sm");
   }
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_censo.html"></div>

<?php if ($_SESSION['tipocen']=='MENSUAL' )	:?>

	<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=window.open("aayudote/CensosMensuales9500Datos.html");>
		<span class="glyphicon glyphicon-question-sign"></span>
	</button>
	
<?php else : ?>	
	
	<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
		<span class="glyphicon glyphicon-question-sign"></span>
	</button>

<?php endif ?>

    <div class="container well"  style="width:800px">
            <div class="row">
                <h4>Censos de elefantes marinos</h4>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">

<?php  
                        $pdo = Database::connect();
						$sqlM= "SELECT MIN(fecha) as fmin,MAX(fecha) as fmax FROM censo WHERE ".$condi." GROUP BY fechaTotal";
                        $q = $pdo->prepare($sqlM);
                        $q->execute();
                        if ($q->rowCount()==0) {
                                Database::disconnect();
                                echo '<span class="alert alert-danger-derecha"> Falta al menos un sector para la fecha      </span>      ';
                                /*exit;*/
                                }
                          else{	
							 $aFec = $q->fetch(PDO::FETCH_ASSOC);
							 $_SESSION['entreFechas'] = $aFec['fmin']." al ".$aFec['fmax'];

						  ?>
                    <h3 class="panel-title">sectores para censo <?php echo $_SESSION['tipocen'];?> entre fechas <?php echo $_SESSION['entreFechas'];?></h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha de total</th>
                          <th>Fecha censo</th>
                          <th>Libreta</th>
<!--                          <th>Hora inicio</th>
                          <th>Hora fin</th> -->
                          <th>Sector recorrido</th>
<!--                          <th>Direc.</th>
                          <th>Marea</th>  -->
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
				
<?php
							 $sql = "SELECT DISTINCT fecha FROM censo WHERE ".$condi." ORDER BY 1";
                             $q = $pdo->prepare($sql);
                             $q->execute();
							if (edita() and $_SESSION['tipocen']<>'MENSUAL' and editaCenso($fecha) ) {
                                echo '          <label for="fechaNuevo"><small>Fecha de censo para la que se agrega sector<small> </label>'."\n";
                                echo '          <select  style=width:auto id="fechaNuevo" data-error="Seleccionar un elemento de la lista" name="fechaNuevo" onchange="FechaSectorNuevo(this.value)">'."\n";
                                echo '              <option value="" selected ></option>'."\n";
                            
                                while ( $aFec = $q->fetch(PDO::FETCH_ASSOC) ) {
                                     echo str_repeat(" ",16). '<option value="' .$aFec['fecha']. '">' .$aFec['fecha'].'</option>'."\n";
                                }
                                echo '</select>';
                                ECHO '&nbsp;&nbsp;<a id="sectorNuevo" onclick="" class="btn btn-primary btn-sm disabled">nuevo caminado</a>&nbsp;&nbsp;&nbsp;&nbsp;';
                                if ($_SESSION['tipocen']=="PVALDES") {
									ECHO '&nbsp;&nbsp;<a id="sectorCopiadoNuevo" onclick="" class="btn btn-primary btn-sm disabled">nuevo copiado</a>&nbsp;&nbsp;&nbsp;&nbsp;';
								}
                            }
							
                            Database::disconnect();
                          } 
                         
                         
?>
                </p>
				<div class="row">
					<div class="col-sm-3">
                 <?php if ($_SESSION['tipocen']<>'MENSUAL'): ?>  
                    <a class="btn btn-default btn-sm" href="censo_index.php">a Censo</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 <?php else: ?>
                    <a class="btn btn-default btn-sm" href="censo_index_mensual.php">a Censo</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				 <?php endif; ?>
					</div>
				<?php if ($_SESSION['tipocen']<>'MENSUAL'): ?>
					<div class="col-sm-3">
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar esta lista</button>
						</form>
					</div>
					<div class="col-sm-3">				
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descargaDatos  class="btn btn-warning btn-sm" >descargar datos</button>
						</form>
					</div>
					
					<div class="col-sm-3">				
						<a title="ubicaciones georeferenciadas" onclick=ventanaM("mapin_censo.php?fec=<?php echo $fecha;?>",this.title) class="btn btn-info btn-sm <?php if($anio<2001){echo "disabled";}?>"><span class="glyphicon glyphicon-map-marker"></span> mapa</a>
					</div>
					
				<?php endif?>	
				</div>



	</div>

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script> 


  </body>


</html>