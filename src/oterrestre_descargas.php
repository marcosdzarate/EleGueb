<?php 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

$salidax = "csv";
$preguntaCal = "fichaCampo";
$numero="";
$tipoTempo="";
if (!empty($_POST)) {
	$salidax = limpia($_POST['salidax']);
	siErrorFuera ($salidax=="xlsx" or $salidax=="csv");

	
	/* validacion basica */	
	$preguntaCal=limpia($_POST['Pregunta_cal']);
	
	
	$errorXL=false;
	
	/* respondiendo en... */
	
	$archi = "pla_".$preguntaCal;
	switch ($preguntaCal)  {	

		case "fichaCampo":			
				plaFichaCampo();
			break;

			case "fichaTodos":
				$sql ="CALL `pla-todosFicha`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;

			case "fichaTodosX":			
				$sql ="CALL `pla-todosFichaX`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
			
			case "animalesSinTag":			
				$sql ="CALL `pla-animalesSinTag`();";
				XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				break;

			case "datosIden":			
				$sql ="CALL `pla-datos identificacion`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
				
			case "datosMuda":			
				$sql ="CALL `pla-datos muda`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
					
			case "datosRHembra":			
				$sql ="CALL `pla-datos reproductivos hembra`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
	
			case "datosRMacho":			
				$sql ="CALL `pla-datos reproductivos macho`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;


			case "datosDeste":			
				$sql ="CALL `pla-datos destete`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;


			case "datosMueMed":			
				$sql ="CALL `pla-datos muestras y medidas`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
				
			case "datosAnes":			
				$sql ="CALL `pla-datos anestesia`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;

			case "datosVia":			
				$sql ="CALL `pla-datos viajes`();";
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
				}
				break;
				
				

	}   /*fin switch*/
			
			
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
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
	
	<script src="js/imiei.js"></script>

  <link rel="stylesheet" href="login/style/main.css">
    <link   href="css/miSideBar.css" rel="stylesheet">

<style>

.panel-abajo {
	margin-top: 0px;
	margin-bottom: 0px;
	background-color: var(--marron3) transparent;
	box-shadow:none;
	color:#e5e5e5;
}
.naranja {
    color: #efb107;
}
.rojo {
    color: #f25829;
}
  .affix {
      top: 20;
      width: 85%;
      z-index: 9999 !important;
  }

  .affix + .container-fluid {
      padding-top: 0px;
  }
.navbar-inverse {
    background-color: transparent;
}

#fRes {
   letter-spacing: 0px;
}
</style>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</HEAD>


<BODY  class=bodycolor >

<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_oterrestre.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

<div class="container-fluid" style="width:90%; padding-top:10px;">   

    <div class="row">
		<div class="col-sm-8">
			<form id="fRespuestas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<div class="well" style="font-size:14px"  > 
					    <h3>otras descargas</h3>
						<br>
					
					
					Elegir el tipo de salida: <br>
						<label class="radio-inline">
						  <input type="radio" name="salidax" id="salidax" value="csv" 
							<?php if (isset($salidax) && $salidax=="csv") echo "checked";?>>a csv (recomendado; descarg&aacute; el template para dar formato)
						</label>
						<br>
						<label class="radio-inline">
						  <input type="radio" name="salidax" id="salidax" value="xlsx" 
							<?php if (isset($salidax) && $salidax=="xlsx") echo "checked";?>>a XLSX ( <span class="glyphicon glyphicon-warning-sign naranja"></span> : puede demorar!!!   <span class="glyphicon glyphicon-warning-sign rojo"></span> : puede demorar varios minutos!!!  
							 )
						</label>
					</div>


				<input id="tipoTempo"  name="tipoTempo" type="hidden"  value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">
				<input id="numero"  name="numero" type="hidden"  value="<?php echo !empty($numero)?$numero:'';?>">

				<input id="Pregunta_tit"  name="Pregunta_tit" type="hidden"  value="<?php echo !empty($pregu)?$pregu:'';?>">
				<input id="Pregunta_cal" name="Pregunta_cal" type="hidden"  value="<?php echo !empty($preguntaCal)?$preguntaCal:'';?>">
			</form>
		</div>
	</div>



    <div class="tab-content">
	<br><br>
	  
			<div class="row">
		  
				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("fichaCampo","")' >
						<div class="panel-heading "><span class="badge badge">1</span></div>
						<div class="panel-body" id=PfichaCampo>		  
							Ficha tag-marca para trabajo en el campo
							<br><br>
							<small>para marcar los individuos recuperados por tag con el &uacute;ltimo nombre asignado.</small>
							<br><br>
							<img src="xlstempla/fichacampo.png" class="img-thumbnail" alt="ficha tag-nombre"> 
							<br><br>
							<small>solo en XLSX hoja A4</small>
						</div>
					</div>
				</div>

				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("fichaTodos","")' >
						<div class="panel-heading ">
							<span class="badge badge">2</span> 
							
						</div>
						<div class="panel-body" id=PfichaTodos>
							Marca y recuperaciones de todos los individuos, indicando eventos/  observaciones en cada fecha  <span class="glyphicon glyphicon-warning-sign rojo"></span>
							<br><br>
							
							<img src="xlstempla/fichaTodos.png" class="img-thumbnail" alt="ficha todos"> 
							
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_fichaTodos-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
					
				</div>				

				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("fichaTodosX","")' >
						<div class="panel-heading ">
							<span class="badge badge">2-x</span> 
						</div>
						<div class="panel-body" id=PfichaTodosX>
								Marca y recuperaciones de todos los individuos, indicando eventos / observaciones en cada fecha con X  <span class="glyphicon glyphicon-warning-sign rojo"></span>
							<br><br>
							
							<img src="xlstempla/fichaTodosX.png" class="img-thumbnail" alt="ficha todos X"> 
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_fichaTodosX-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>				
				
				
				
			</div>	


			
			<div class="row">
		  
				<div class="col-sm-5" >
					<div class="panel panel-consulta" onclick='datosMres("animalesSinTag","")' >
						<div class="panel-heading "><span class="badge badge">3</span></div>
						<div class="panel-body" id=PanimalesSinTag>		  
							Lista de animales sin tag (no incluye los de categor&iacute;a "cr&iacute;a" ni "sin clasificar") 
							<br><br>
							<img src="xlstempla/animalesSinTag.png" class="img-thumbnail" alt="animales sin tag"> 
							<br><br>
							<small>solo en XLSX hoja A4</small>
						</div>
					</div>
				</div>

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosIden","")' >
						<div class="panel-heading ">
							<span class="badge badge">4</span> 
						</div>
						<div class="panel-body" id=PdatosIden>
							Datos de identificaci&oacute;n de todos los individuos: tags y marcas <span class="glyphicon glyphicon-warning-sign rojo"></span>
							<br>							
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosIden-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>				

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosMuda","")' >
						<div class="panel-heading "><span class="badge badge">5</span></div>
						<div class="panel-body" id=PdatosMuda>
							Datos de todos los individuos con muda
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosMuda-template.xlsx" role="button">template XLSX para csv</a>
							</div>
						</div>
						</div>
					</div>
					
				</div>				
				
				
				
			</div>
			
			<div class="row">
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosRHembra","")' >
						<div class="panel-heading "><span class="badge badge">6</span></div>
						<div class="panel-body" id=PdatosRHembra>
							Datos reproductivos de hembra, con c&oacute;pulas y v&iacute;nculo madre-cr&iacute;a
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosRhembra-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>				

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosRMacho","")' >
						<div class="panel-heading ">
							<span class="badge badge">7</span>
						</div>
						<div class="panel-body" id=PdatosRMacho>
							Datos reproductivos de macho con c&oacute;pulas  <span class="glyphicon glyphicon-warning-sign naranja"></span>
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosRMacho-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>	

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosDeste","")' >
						<div class="panel-heading "><span class="badge badge">8</span></div>
						<div class="panel-body" id=PdatosDeste>
							Datos de destete y v&iacute;nculo madre-cr&iacute;a
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosDeste-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>
				
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("datosMueMed","")' >
						<div class="panel-heading "><span class="badge badge">9</span></div>
						<div class="panel-body" id=PdatosMueMed>
							Datos de muestras y medidas de los individuos
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosDeste-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</div>				
			
			<div class="row">


				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("datosAnes","")' >
						<div class="panel-heading "><span class="badge badge">10</span></div>
						<div class="panel-body" id=PdatosAnes>
							Datos de las anestesias aplicadas
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosAnes-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
						
					</div>
					
				</div>					

				
				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("datosVia","")' >
						<div class="panel-heading "><span class="badge badge">11</span></div>
						<div class="panel-body" id=PdatosVia>
							Datos de los viajes y su configuraci&oacute;n
							<br>
							<div class="panel panel-abajo" >
								<div class="panel-heading" >
								<a class="btn btn-primary" href="xlstempla/pla_datosVia-template.xlsx" role="button">template XLSX para csv</a>
								</div>
							</div>
						</div>
						
					</div>
					
				</div>					
				
			</div>				
			
			<div class="row">
			</div>				
	


    </div>			


</div>			
	










<script src="js/w3.js"></script> 
<script>
w3.includeHTML();
</script> 


</BODY>
</HTML>