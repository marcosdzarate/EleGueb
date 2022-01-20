<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_validar.php'; 

$temporada=2001;
$escala=0.005;
$temporadaActual = date("Y");
$preguntaCal = "pelos2001";
if (!empty($_POST)) {
    $temporada = limpia($_POST['temporada']);
	$escala = limpia($_POST['escala']);
	$preguntaCal = limpia($_POST['preguntaCal']);
	
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
        {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
        }
	switch ($preguntaCal)  {
		case "pelos2001":
			$sql ="CALL `pelos de Valdes`($temporada,$escala,'KML');";
			$salida="Pelos_de_Valdes_$temporada.kml";
			if ($result=mysqli_query($con, $sql)) {

				// salida...
				$fp = fopen('php://output', 'w');
				$blibli = array("","");
				if ($fp) {

					ob_start();
				// inicializo kml
				$str = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">
<Document>
	<name>Pelos de Valdes $temporada</name>
	<open>1</open>
	<Snippet maxLines="0"></Snippet>
	<Style id="dotdot0">
		<IconStyle>
			<color>ff05c1ff</color>
			<scale>0.5</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/shapes/shaded_dot.png</href>
			</Icon>
		</IconStyle>
		<LabelStyle>
			<color>00ffffff</color>
		</LabelStyle>
		<BalloonStyle>
			<text>$[description]</text>
			<bgColor>ff689a9a</bgColor>
		</BalloonStyle>
		<LineStyle>
			<color>fff4f4f4</color>
		</LineStyle>
	</Style>




	<StyleMap id="dotdotCR0">
		<Pair>
			<key>normal</key>
			<styleUrl>#dotdotHaren</styleUrl>
		</Pair>
		<Pair>
			<key>highlight</key>
			<styleUrl>#dotdot0</styleUrl>
		</Pair>
	</StyleMap>
	<Style id="dotdotHaren">
		<IconStyle>
			<color>ffffbc05</color>
			<scale>0.5</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/shapes/shaded_dot.png</href>
			</Icon>
		</IconStyle>
		<LabelStyle>
			<color>00ffffff</color>
		</LabelStyle>
		<BalloonStyle>
			<text>$[description]</text>
			<bgColor>ff689a9a</bgColor>
		</BalloonStyle>
		<LineStyle>
			<color>fff4f4f4</color>
		</LineStyle>
	</Style>

	<StyleMap id="dotdotCR1">
		<Pair>
			<key>normal</key>
			<styleUrl>#dotdotGrupoHarenes</styleUrl>
		</Pair>
		<Pair>
			<key>highlight</key>
			<styleUrl>#dotdot0</styleUrl>
		</Pair>
	</StyleMap>
	<Style id="dotdotGrupoHarenes">
		<IconStyle>
			<color>ffd100ff</color>
			<scale>0.5</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/shapes/shaded_dot.png</href>
			</Icon>
		</IconStyle>
		<LabelStyle>
			<color>00ffffff</color>
		</LabelStyle>
		<BalloonStyle>
			<text>$[description]</text>
			<bgColor>ff689a9a</bgColor>
		</BalloonStyle>
		<LineStyle>
			<color>fff4f4f4</color>
		</LineStyle>
	</Style>	
	
	<StyleMap id="dotdotCR2">
		<Pair>
			<key>normal</key>
			<styleUrl>#dotdotHarenSinAlfa</styleUrl>
		</Pair>
		<Pair>
			<key>highlight</key>
			<styleUrl>#dotdot0</styleUrl>
		</Pair>
	</StyleMap>
	<Style id="dotdotHarenSinAlfa">
		<IconStyle>
			<color>ff00ff00</color>
			<scale>0.5</scale>
			<Icon>
				<href>http://maps.google.com/mapfiles/kml/shapes/shaded_dot.png</href>
			</Icon>
		</IconStyle>
		<LabelStyle>
			<color>00ffffff</color>
		</LabelStyle>
		<BalloonStyle>
			<text>$[description]</text>
			<bgColor>ff689a9a</bgColor>
		</BalloonStyle>
		<LineStyle>
			<color>fff4f4f4</color>
		</LineStyle>
	</Style>	
EOD;
		
				fwrite($fp,$str);
				
				
				
				
				
				/* el kml va a tener una carpeta por "referencia" */
				$colores=array('HAREN','GRUPO DE HARENES','HAREN SIN ALFA');
				/* recorro archivo para obtener las "referencias" y en que registro comienzan */
				$refer_ant='';
				$reg=-1;
				$refdh=array();
				while ($row = mysqli_fetch_assoc($result)) 
				{
					$reg +=1;
					if ($row['referencia'] <> $refer_ant){
						$colo=array_search($row['referencia'],$colores);
						if($colo===false) {
							$colo=0;
						}
						$refdh[]=["refer"=>$row['referencia'], "desde"=>$reg, "hasta"=>0, "colordot"=>"CR".$colo];
						$refer_ant=$row['referencia'];
					}
				}
				$xreg=count($refdh)-1;
				for ($i = 0; $i <= $xreg-1; $i++)
				{
					$refdh[$i]["hasta"] = $refdh[$i+1]["desde"]-1;					
				}
				$refdh[$xreg]["hasta"] = 999999;
				
				 
				/* segun "referencia" ... carpeta ad-hoc on puntos y lineas*/
				for ($i = 0; $i <= $xreg; $i++)
				{
					
					$refnom = $refdh[$i]["refer"];
					$desde = $refdh[$i]["desde"];
					$hasta = $refdh[$i]["hasta"];
					$colo  = $refdh[$i]["colordot"];
					
					
					$str = <<<EOD

<Folder>
	<name>$refnom</name>	

	<Folder>
		<name>Puntos</name>	
EOD;

					fwrite($fp,$str);
					
				    mysqli_data_seek($result,$desde);					
				    $reg=$desde;
					while ($row = mysqli_fetch_assoc($result) and $reg<=$hasta)
					{
						$reg += 1;
						$fecha = $row['fecha'];
						$libreta = $row['libreta'];
						$orden = $row['orden'];
						$nombre = $fecha.".".$libreta.".".$orden;
						$them = $row['them'];
						$tmac = $row['tmac'];
						$refer = $row['referencia'];
						$cant = $row['cantidad'];
						$descripcion = "<![CDATA[ 

<div width='250px'>
  
      <h2><font color='#006600'>$nombre</font></h2>
	
        <p  >
		fecha: $fecha <br>
		libreta: $libreta  <br>
		orden: $orden <br>
		referencia: $refer <br>
		hembras/machos=cantidad: $them/$tmac=$cant
		</p>

</div>]]>";
						$ll = $row["lon0"].",".$row["lat0"].",0";
				
						$str = <<<EOD

		<Placemark>
			<name>$nombre</name>
			<description>$descripcion</description>
			<Snippet maxLines="0"></Snippet>
			<styleUrl>#dotdot$colo</styleUrl>
			<Point>
				<coordinates>$ll</coordinates>
			</Point>
		</Placemark>
EOD;
			
						fwrite($fp,$str);
					}
	
					$str = <<<EOD

	</Folder>

	<Folder>
		<name>Pelos</name>	
EOD;

					fwrite($fp,$str);
					
				    mysqli_data_seek($result,$desde);					
				    $reg=$desde;
					while ($row = mysqli_fetch_assoc($result) and $reg<=$hasta)
					{
						$reg+=1;
						$fecha = $row['fecha'];
						$libreta = $row['libreta'];
						$orden = $row['orden'];
						$nombre = $fecha.".".$libreta.".".$orden;
						$them = $row['them'];
						$tmac = $row['tmac'];
						$refer = $row['referencia'];
						$cant = $row['cantidad'];
						$descripcion = "<![CDATA[ 

<div width='250px'>
  
      <h2><font color='#006600'>$nombre</font></h2>
	
        <p  >
		fecha: $fecha <br>
		libreta: $libreta  <br>
		orden: $orden <br>
		referencia: $refer <br>
		hembras/machos=cantidad: $them/$tmac=$cant
		</p>

</div>]]>";			
						$ll1 = $row["lon0"].",".$row["lat0"].",0";
						$ll2 = $row["lon1"].",".$row["lat1"].",0";
			
$str = <<<EOD

		<Placemark>
			<name>$nombre</name>
			<description>$descripcion</description>
			<Snippet maxLines="0"></Snippet>
			<styleUrl>#dotdot$colo</styleUrl>
			<LineString>
				<coordinates>
				$ll1 $ll2
				</coordinates>
			</LineString>
		</Placemark>
EOD;
			
					fwrite($fp,$str);			
					}
				
					$str = <<<EOD

	</Folder>

</Folder>	
	
EOD;

				fwrite($fp,$str);


				}	/* fin ciclo sobre refdh */
				
				mysqli_close($con);

				$str = <<<EOD


</Document>
</kml>	
EOD;

				fwrite($fp,$str);
			
				$strob = ob_get_clean();
				header('Content-type: application/vnd.google-earth.kml+xml');
				header('Content-Disposition: attachment; filename="'.$salida.'"');
				header('Pragma: no-cache');
				header('Expires: 0');
				exit($strob);	
					
				}
				
				

			}
			break;
		case "pelos0":
			$sql ="CALL `pelos de Valdes`($temporada,$escala,'csv');";
			$salida="Pelos_de_Valdes_$temporada";
			csvOnDeFlai($sql,$salida);
			
			break;
	}	
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">

<!--<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">-->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe?></title>

    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>	
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>


  <link rel="stylesheet" href="UI2/jquery-ui.css">
  <script src="UI2/jquery-ui.js"></script>


	
  <script src="js/imiei.js"></script>

  <link rel="stylesheet" href="login/style/main.css">
    <link   href="css/miSideBar.css" rel="stylesheet">

<style>
.condato{
	-webkit-box-shadow: 0 3px 4px rgba(0, 0, 0, .5);
	box-shadow: 0 3px 4px rgba(0, 0, 0, .5);
	margin-top:-48px;
	border-top: 0px;
	cursor: default;
}

.panel-heading {
	font-size:18px;
}
</style>

<script>
/* para resultados */
function datosMres(pregunta,val){	
    preguntaCal.value = pregunta;
	t=document.getElementById("temporada").value;
    if(t>=2001 || (t<2001 && pregunta=="pelos0"))
	{
		document.getElementById("fRespuestas").submit();
	}
	else {
		$( "#dialog" ).dialog( "open" );
	}
}
</script>
  <script>
  $( function() {
    $( "#dialog" ).dialog({
      autoOpen: false,
      show: {
        effect: "blind",
        duration: 300
      }
    });
 
  } );
  </script>
  

<script src="js/aiuta.js"></script>	
	
	
  
  
</HEAD>


<BODY  class=bodycolor >
<div id="dialog" title="KML antes del 2001">
  <p>En temporadas anteriores a 2001, no se hac&iacute;a uso de GPS para localizar grupos en la playa. No se puede generar KML.</p>
</div>
<?php
require_once 'tb_barramenu.php';
?>

<div w3-include-html="sideBar_censo.html"></div>
	
<button type="button" class="botonayuda" style="background-color: #344560" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


<div class="container-fluid" style="width:70%; padding-top:10px;">   
	<div class=row>             
		<div class=col-sm-6>	
			<h1 style='color:#f4f4f4'>Pelos de Vald&eacute;s: hembras por grupo</h1>
			  
		<br>
			<h3 style='color:#f4f4f4'>
			S&oacute;lo para censos reproductivos
			</h3>
		</div>	
		<div class=col-sm-6>	
			<img src="imas/pelosMuestra.png" class="img-thumbnail" alt="ficha tag-nombre"> 
			<br><br>
		</div>	
	</div>	

	
	<div class=row>             
		<div class=col-sm-12>	
			<h4 style='color:#f4f4f4'>
			<blockquote>
			- Se incluyen harenes, grupos de harenes y grupos de hembras sin alfa.
			<br>
			- El número que se representa es el de hembras/alfas (hembras sin alfa se asume 1 macho).
			</blockquote>
			</h4>
		</div>	
	</div>	
	
	
	
    <form data-toggle="validator" id="fRespuestas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
		<input id="preguntaCal" name="preguntaCal" type="hidden"  value="<?php echo !empty($preguntaCal)?$preguntaCal:'';?>">

		<div class="panel panel-consulta">
			<div class="panel-body" >							
                <div class=row>             
					<div class=col-sm-2>
					</div>
					<div class=col-sm-4>
						<div class="form-group ">
							<label for="temporada">temporada</label>
								<input type="text" class="form-control input-sm" style="width:90px" id="temporada" name="temporada"  
								data-dmin="1990" data-dmax=<?php echo $temporadaActual; ?> data-pentero required  
								data-error="1990 y &lt= <?php echo $temporadaActual?>" value="<?php echo !empty($temporada)?$temporada:'';?>" >
								<div class="help-block with-errors"></div>
								<p id="JSErrortemporada"></p>
								<?php if (!empty($temporadaError)): ?>
									<span class="help-inline"><?php echo $temporadaError;?></span>
								<?php endif; ?>
						</div>
					</div>
					<div class=col-sm-2>
					</div>
					
					<div class=col-sm-4>
						<div class="form-group ">
							<label for="escala">escala (largo del pelo)</label>
								<input type="text" class="form-control input-sm" style="width:90px" id="escala" name="escala"  
								data-dmin="0.0005" data-dmax=0.02 data-pdecimal required  
								data-error="entre 0.0005 y 0.02" value="<?php echo !empty($escala)?$escala:'';?>" >
								<div class="help-block with-errors"></div>
								<p id="JSErrorescala"></p>
								<?php if (!empty($escalaError)): ?>
									<span class="help-inline"><?php echo $escalaError;?></span>
								<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>			
		<div class="panel panel-consulta">
			<div class="panel-body" >							
				<div class="row">
					<div class=col-sm-2>
					</div>
			  
					<div class="col-sm-4" >
						<div class="panel panel-consulta" onclick='datosMres("pelos2001","")' >
							<div class="panel-heading ">KML</div>
							<div class="panel-body" id=Pelos2001>
									Para temporadas 2001 en adelante. La salida puede abrirse en Google Earth o en cualquier otro software o aplicaci&oacute;n que implemente KML (por ej. QGIS).
							</div>
						</div>
					</div>	
					<div class="col-sm-4" >
						<div class="panel panel-consulta" onclick='datosMres("pelos0","")' >
							<div class="panel-heading ">csv</div>
							<div class="panel-body" id=Pelos0>
									datos relativos a las hembras por grupo
							</div>
						</div>
					</div>	
					<div class="col-sm-4" >
					</div>
				</div>	
			</div>	
		</div>	
	</form>
</div>	



<script src="js/w3.js"></script> 
 <!-- <div w3-include-html="tb_ventanaMres.html"></div> -->
<script>
w3.includeHTML();

</script> 

<script>
$('form[data-toggle="validator"]').validator({
    custom: {
		pentero: function($el) {
			var r = validatorEntero ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		},
		pdecimal: function($el) {
			var r = validatorDecimal ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		}
   }
});
</script>	

</BODY>
</HTML>