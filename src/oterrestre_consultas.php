<?php 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

$salidax = "pantalla";
$preguntaCal = "gene01";
if (!empty($_POST)) {
	
	/* validacion basica */	
	$preguntaCal=limpia($_POST['Pregunta_cal']);
	/* arreglo de preguntas, completar cuando se agregue una nueva pregunta */
	/* si la pregunta no está en el arreglo, se cierra la sesion*/
	$arrPreg=array("gene01", "gene02", "gene03", "gene04", "gene05",
					"recu01", "recu01T", "recu02", "recu03", "recu04", "recu05", "recu06",
					"recu07", "recu08", "recu09", "recu10", "recu11", "recu12", "recu13",
					"tag01", "tag02", "tag03", "tag04", "tag05", "tag06", "tag07");
	siErrorFuera (in_array($preguntaCal,$arrPreg));
	
	$x=preg_match("/^[A-Z0-9ÑÁÉÍÓÚÜa-zñáéíóúü\/\-'\s\.,:;)\(]{10,700}$/",$_POST['Pregunta_tit'], $a);
	$v=true;
	if ($x<>1) {
		$v=false;
	}
	siErrorFuera ($v);
	
	$salidax = limpia($_POST['salidax']);
	siErrorFuera ($salidax=="pantalla" or $salidax=="xlsx" or $salidax=="csv");

	$v=true;
	$queTabla="preguntas";
	$m= validar_tipo($_POST['tipoTempo'],$v,false);
	siErrorFuera (empty($_POST['tipoTempo']) OR $v);

	
	$errorXL=false;
	
	if ($salidax =="xlsx" or $salidax =="csv") {
		/* respondiendo en XLSX o csv ... */
		$archi = "res_".$preguntaCal;
		$funcion = "XlsXOnDeFlai";  /* funcion variable */
		if($salidax == "csv"){
			$funcion = "csvOnDeFlai";
		}
		switch ($preguntaCal)  {
			case "gene01":
				$sql ="CALL `gene01-animales registrados`();";
				$funcion($sql,$archi);
				break;
				
			case "gene02":
				$sql ="CALL `gene02-marcados por sexo-categoria`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						

			case "gene03":
				$sql ="CALL `gene03-marcados por temporada-sexo-categoria`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;	

			case "gene04":
				$sql ="CALL `gene04-cuantos hay por temporada-sexo-categoria`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;	
				
			case "gene05":
				if (!isset($_POST['tipoTempo']) or empty($_POST['tipoTempo']) ) {
					$errorXL = true;
				}
				else {
					$sql ="CALL `gene05-marcados en un TipoTemporada por temporada-sexo-categoria`('".$_POST['tipoTempo']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;		



			case "recu01":
				$sql ="CALL `recu01-recuperados al menos un temporada`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;

			case "recu01T":
				$v=true;
				$m=validar_tAnio($_POST['numero'],$v,true);
				if (!$v){
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu01T-recuperados en una temporada dada`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;						

			case "recu02":
				if (!isset($_POST['numero']) or empty($_POST['numero']) ) {
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu02-recuperados n temporadas a partir de la marcacion`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;							

			case "recu03":
				$sql ="CALL `recu03-recuperados solo en muda-solo en repro-en ambas`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						
															
			case "recu04":
				$sql ="CALL `recu04-recuperados solo en repro n temporadas`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						

			case "recu05":
				$sql ="CALL `recu05-recuperados solo en muda n temporadas`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						

			case "recu06":
				if (!isset($_POST['numero']) or empty($_POST['numero']) ) {
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu06-recuperados en n temporadas muda repro consecutivas`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;						
				
			case "recu07":
				if (!isset($_POST['numero']) or empty($_POST['numero']) ) {
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu07-recuperados en n temporadas de muda consecutivas`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;						

			case "recu08":
				if (!isset($_POST['numero']) or empty($_POST['numero']) ) {
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu08-recuperados en n temporadas de repro consecutiva`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;							

			case "recu09":
				$sql ="CALL `recu09-crias y destetados recuperados al menos una vez`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;


			case "recu10":
				$sql ="CALL `recu10-crias y destetados recuperados por primera vez como`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;

			case "recu11":
				if (!isset($_POST['numero']) or empty($_POST['numero']) or ($_POST['numero']) < 3) {
					$errorXL = true;
				}
				else{
					$sql ="CALL `recu11-vistos mas de n anios y no recuperados desde maxT-2`('".$_POST['numero']."');";
					$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$funcion($sql,$archi);
				}
				break;							
				

			case "recu12":
				$sql ="CALL `recu12-vistos una-dos-tres... veces en reproduccion`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;

			case "recu13":
				$sql ="CALL `recu13-vistos una-dos-tres... veces en muda`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						
				
			case "tag01":
				$sql ="CALL `tag01-cuantos tags se colocaron`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;			

			case "tag02":
				$sql ="CALL `tag02-animles a los que se les puso 1-2-3...tags por anio`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;			

			case "tag03":
				$sql ="CALL `tag03-tags perdidos segun sexo`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;								

			case "tag04":
				$sql ="CALL `tag04-tags perdidos a x anios de la colocacion`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;						

			case "tag05":
				$sql ="CALL `tag05-tags con numeracion borrada`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;	

			case "tag06":
				$sql ="CALL `tag06-tags borrados por color`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;	

			case "tag07":
				$sql ="CALL `tag07-animales con 1-2-3... tags`();";
				$sql .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
				$funcion($sql,$archi);
				break;							

				
			}   /*fin switch*/
			
			
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
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
	
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


<BODY  class=bodycolor 
     <?php  if (!empty($_POST)) {
		 $pregu=limpia($_POST['Pregunta_tit']);
		 /* NO CAMBIAR LA POSICION DE LAS LINEAS SIGUIENTE!!!!!*/  
$fun_onload = <<<EOD
onload="ventanaMres('$pregu');"
EOD;
		/* HASTA ACA*/		
	 echo $fun_onload;} ?> >

<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_oterrestre.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

<div class="container-fluid" style="width:90%; padding-top:10px;">   
	
<nav class="navbar navbar-inverse" id=fRes data-spy="affix" data-offset-top="20" z-index="9999 !important">
    <div class="row">
		<div class="col-sm-8">
			<form id="fRespuestas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">	
				<div class="well" style="font-size:14px" > 
					    <h3>preguntas preestablecidas</h3>
						<br>
				
				
				Elegir la salida: <br>
					<label class="radio-inline">
					  <input type="radio" name="salidax" id="salidax" value="pantalla" 
						<?php if (isset($salidax) && $salidax=="pantalla") echo "checked";?>>por pantalla
					</label>
					<label class="radio-inline">
					  <input type="radio" name="salidax" id="salidax" value="csv" 
						<?php if (isset($salidax) && $salidax=="csv") echo "checked";?>>a csv (archivo &uacute;nico)
					</label>
					<label class="radio-inline">
					  <input type="radio" name="salidax" id="salidax" value="xlsx" 
						<?php if (isset($salidax) && $salidax=="xlsx") echo "checked";?>>a XLSX 
					</label>
				</div>

				<input id="tipoTempo"  name="tipoTempo" type="hidden"  value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">
				<input id="numero"  name="numero" type="hidden"  value="<?php echo !empty($numero)?$numero:'';?>">

				<input id="Pregunta_tit"  name="Pregunta_tit" type="hidden"  value="<?php echo !empty($pregu)?$pregu:'';?>">
				<input id="Pregunta_cal" name="Pregunta_cal" type="hidden"  value="<?php echo !empty($preguntaCal)?$preguntaCal:'';?>">
			</form>
		</div>
	</div>
</nav>



	<ul class="nav nav-tabs nav-justified" role="tablist" style="font-size:18px">
		<li <?php echo substr($preguntaCal,0,4)=="gene"?"class=active":"";?>><a data-toggle="tab" href="#generales">generales </a></li>
		
		<li <?php echo substr($preguntaCal,0,4)=="recu"?"class=active":"";?>><a data-toggle="tab" href="#recuperacion">de recuperaci&oacute;n</a></li>
		
		<li <?php echo substr($preguntaCal,0,3)=="tag"?"class=active":"";?>><a data-toggle="tab" href="#tags">tags</a></li>
	</ul>

    <div class="tab-content">
	<br><br>
		<div id="generales" class="tab-pane fade <?php echo substr($preguntaCal,0,4)=='gene'?'in active':'';?>" >
			<!-- <div class="panel panel-consulta" >
				<div class="panel-heading2">generales</div>
			</div> -->
	  
			<div class="row">
		  
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("gene01","")' >
						<div class="panel-heading "><span class="badge badge">1</span></div>
						<div class="panel-body" id=Pgene01>		  
							Cu&aacute;ntos animales registrados hay - (con tag y sin tag)
						</div>
					</div>
				</div>
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("gene02","")' >
						<div class="panel-heading "><span class="badge badge">2</span></div>
						<div class="panel-body" id=Pgene02>		  
							Cu&aacute;ntos individuos se marcaron (con tag y/o stamp) en las distintas categor&iacute;as de edad y sexo
						</div>
					</div>
				</div>			
			
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("gene03","")' >
						<div class="panel-heading "><span class="badge badge">3</span></div>
						<div class="panel-body" id=Pgene03>		  
							Cu&aacute;ntos individuos se marcaron en cada a&ntilde;o para las distintas categor&iacute;as de edad y sexo
						</div>
					</div>
				</div>			

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("gene04","")' >
						<div class="panel-heading "><span class="badge badge">4</span></div>
						<div class="panel-body" id=Pgene04>		  
							Cu&aacute;ntos animales hay marcados para cada a&ntilde;o seg&uacute;n las distintas categor&iacute;as
						</div>
					</div>
				</div>	

				
			</div>	<!-- fin row 1 generales--->

			<div class="row">

			
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("gene05","")' >
						<div class="panel-heading "><span class="badge badge">5</span></div>
						<div class="panel-body" id=Pgene05>		  
							Cu&aacute;ntos animales se marcaron en un tipo de temporada
						</div>
					</div>
					<div class="panel panel-consulta condato"  >
						<div class="panel-body" >	
							<h6 >seleccionar</h6>					
								<select class="form-control input-sm" style="width: auto" id="tipo"  name="tipo"
										onchange='getElementById("tipoTempo").value=this.value'>
									<option value=""  ></option>
									<option value="REPRO" >REPRO</option>
									<option value="MUDA" >MUDA</option>
									<option value="FUERA">FUERA</option>
								</select>
						</div>
					</div>

				</div>			
			
			
			</div>	<!-- fin row 2 generales--->

		</div>	<!-- fin tab generales--->

		<div id="recuperacion" class="tab-pane fade <?php echo substr($preguntaCal,0,4)=='recu'?'in active':'';?>">
			
			<!-- <div class="panel panel-consulta" >
				<div class="panel-heading2">de recuperaci&oacute;n</div>
			</div> -->

			<div class="row">
		  
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu01","")' >
						<div class="panel-heading "><span class="badge badge">1</span></div>
						<div class="panel-body" id=Precu01>
								Cu&aacute;ntos animales se recuperaron al menos una vez luego de la marcaci&oacute;n
						</div>
					</div>
					
				</div>	


				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu01T","")' >
						<div class="panel-heading "><span class="badge badge">1 T</span></div>
						<div class="panel-body" id=Precu01T>
								Cu&aacute;ntos animales se recuperaron en una temporada dada
						</div>
					</div>
					<div class="panel panel-consulta condato">
						<div class="panel-body" >	
							<h6 >ingresar temporada</h6>
							<input type="number" class="form-control input-sm" min=1960 style="width: 100px" id="tempo" name="tempo" onchange='getElementById("numero").value=this.value'>						
						</div>
					</div>
				</div>	

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu02",nveces_recu02.value)' >
						<div class="panel-heading "><span class="badge badge">2</span></div>
						<div class="panel-body" id=Precu02>
								Cu&aacute;ntos animales fueron vistos N temporadas a partir de la marcaci&oacute;n
						</div>
					</div>
					<div class="panel panel-consulta condato" >
						<div class="panel-body" >	
							<h6 >ingresar "N"</h6>
							<input type="number" class="form-control input-sm" style="width: 100px" id="nveces_recu02" name="nveces_recu02" value="" >						
						</div>
					</div>
				</div>				

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu03","")' >
						<div class="panel-heading "><span class="badge badge">3</span></div>
						<div class="panel-body" id=Precu03>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos s&oacute;lo en REPRO - cu&aacute;ntos s&oacute;lo en MUDA - cu&aacute;ntos en REPRO y MUDA
						</div>
					</div>
					
				</div>	

			</div>	<!-- fin row 1 recuperacion --->
		
			<div class="row">
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu04","")' >
						<div class="panel-heading "><span class="badge badge">4</span></div>
						<div class="panel-body" id=Precu04>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos s&oacute;lo en REPRO y en cu&aacute;ntas temporadas
						</div>
					</div>
					
				</div>	

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu05","")' >
						<div class="panel-heading "><span class="badge badge">5</span></div>
						<div class="panel-body" id=Precu05>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos s&oacute;lo en MUDA y en cu&aacute;ntas temporadas
						</div>
					</div>
					
				</div>				
				

		  
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu06",nveces_recu06.value)' >
						<div class="panel-heading "><span class="badge badge">6</span></div>
						<div class="panel-body" id=Precu06>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos en N temporadas consecutivas (patr&oacute;n)
						</div>
					</div>
					<div class="panel panel-consulta condato" >
						<div class="panel-body" >	
							<h6 >ingresar "N"</h6>
							<input type="number" class="form-control input-sm" style="width: 100px" id="nveces_recu06" name="nveces_recu06" value="" >						
						</div>
					</div>				
					
				</div>	

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu07",nveces_recu07.value)' >
						<div class="panel-heading "><span class="badge badge">7</span></div>
						<div class="panel-body" id=Precu07>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos en N temporadas de MUDA consecutivas (patr&oacute;n)
						</div>
					</div>
					<div class="panel panel-consulta condato" >
						<div class="panel-body" >	
							<h6 >ingresar "N"</h6>
							<input type="number" class="form-control input-sm" style="width: 100px" id="nveces_recu07" name="nveces_recu07" value="" >						
						</div>
					</div>				
					
				</div>				

				
			</div>	<!-- fin row 2 recuperacion --->
		
			<div class="row">
			
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu08",nveces_recu08.value)' >
						<div class="panel-heading "><span class="badge badge">8</span></div>
						<div class="panel-body" id=Precu08>
								Cu&aacute;ntos animales en edad reproductiva fueron vistos en N temporadas de REPRO consecutivas (patr&oacute;n)
						</div>
					</div>
					<div class="panel panel-consulta condato" >
						<div class="panel-body" >	
							<h6 >ingresar "N"</h6>
							<input type="number" class="form-control input-sm" style="width: 100px" id="nveces_recu08" name="nveces_recu08" value="" >						
						</div>
					</div>				
					
				</div>				

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu09","")' >
						<div class="panel-heading "><span class="badge badge">9</span></div>
						<div class="panel-body" id=Precu09>
								Cu&aacute;ntos animales marcados como cr&iacute;as y destetados fueron recuperados al menos una vez
						</div>
					</div>
					
				</div>		

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu10","")' >
						<div class="panel-heading "><span class="badge badge">10</span></div>
						<div class="panel-body" id=Precu10>
								Cu&aacute;ntos de las cr&iacute;as y destetados recuperados fueron vistos por primera vez como yearlings, juveniles, adultos ...
						</div>
					</div>
					
				</div>		

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu11",nveces_recu11.value)' >
						<div class="panel-heading "><span class="badge badge">11</span></div>
						<div class="panel-body" id=Precu11>
								Cu&aacute;ntos animales que fueron vistos en tres o m&aacutes a&ntilde;os (incluyendo el a&ntilde;o de marcaci&oacute;n) no fueron vistos nunca m&aacute;s
						</div>
					</div>
					<div class="panel panel-consulta condato" >
						<div class="panel-body" >	
							<h6 >ingresar c&uacute;antos a&ntilde;os </h6>
							<input type="number" class="form-control input-sm" min=3 style="width: 100px" id="nveces_recu11" name="nveces_recu11" value="">						
						</div>
					</div>				
					
				</div>				

				
			</div>	<!-- fin row 3 de recuperacion --->
		
			<div class="row">	

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu12","")' >
						<div class="panel-heading "><span class="badge badge">12</span></div>
						<div class="panel-body" id=Precu12>
								Cu&aacute;ntos animales fueron vistos 1, 2, 3 ... veces en REPRO
						</div>
					</div>
					
				</div>		
				
				

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("recu13","")' >
						<div class="panel-heading "><span class="badge badge">13</span></div>
						<div class="panel-body" id=Precu13>
								Cu&aacute;ntos animales fueron vistos 1, 2, 3 ... veces en MUDA
						</div>
					</div>
					
				</div>		


				
			</div>	<!-- fin row 4 recuperacion --->
		
		</div>	<!-- fin tab recupearcion--->

		<div id="tags" class="tab-pane fade <?php echo substr($preguntaCal,0,3)=='tag'?'in active':'';?>">

			<!-- <div class="panel panel-consulta" >
				<div class="panel-heading2">tags</div>
			</div> -->
	  
			<div class="row">
		  
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag01","")' >
						<div class="panel-heading "><span class="badge badge">1</span></div>
						<div class="panel-body" id=Ptag01>
								Cu&aacute;ntos tags se colocaron
						</div>
					</div>
					
				</div>		

				
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag02","")' >
						<div class="panel-heading "><span class="badge badge">2</span></div>
						<div class="panel-body" id=Ptag02>
								A cu&aacute;ntos animales se les puso 1, 2, 3...tags
						</div>
					</div>
					
				</div>					

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag03","")' >
						<div class="panel-heading "><span class="badge badge">3</span></div>
						<div class="panel-body" id=Ptag03>
								Cu&aacute;ntos tags se perdieron seg&uacute;n sexo
						</div>
					</div>
					
				</div>					
							
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag04","")' >
						<div class="panel-heading "><span class="badge badge">4</span></div>
						<div class="panel-body" id=Ptag04>
								Cu&aacute;ntos tags se encontraron perdidos a a&ntilde;os de colocaci&oacute;n
						</div>
					</div>

				</div>
							
			</div>	<!-- fin row 1 tags --->

			<div class="row">
		  
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag05","")' >
						<div class="panel-heading "><span class="badge badge">5</span></div>
						<div class="panel-body" id=Ptag05>
								Cu&aacute;ntos tags tienen se han visto con la numeraci&oacute;n borrada total o parcialmente
						</div>
					</div>
					
				</div>		

				
				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag06","")' >
						<div class="panel-heading "><span class="badge badge">6</span></div>
						<div class="panel-body" id=Ptag06>
							   Cu&aacute;ntos tags borrados seg&uacute;n color
						</div>
					</div>
					
				</div>					

				<div class="col-sm-3" >
					<div class="panel panel-consulta" onclick='datosMres("tag07","")' >
						<div class="panel-heading "><span class="badge badge">7</span></div>
						<div class="panel-body" id=Ptag07>
								Cu&aacute;ntos animales tienen 1, 2, 3... tags
						</div>
					</div>
					
				</div>					
							

							
			</div>	<!-- fin row 2 tags --->		
		</div>	<!-- fin tab tags--->

    </div>			


	







<!-- ventana modal para contener otras páginas dentro del iframe -->
<div class="container">
   <!-- Modal -->
  <div class="modal fade" id="vModal" role="dialog" >
    <div class="modal-dialog  modal-lgres" >
    
      <!-- Modal content-->
      <div class="modal-content" style="overflow:auto">
        <div class="modal-header">
          <h4 class="modal-title" id="tituloM"></h4>
        </div>
        <div class="modal-body" style="width:90%;margin-left:auto;margin-right:auto">
          <p style="text-align:center">
		  
		  <?php 
		    if (isset($_POST['Pregunta_cal'])) {
			    /* respondiendo... */				
				$preguntaCal=limpia($_POST['Pregunta_cal']);
				switch ($preguntaCal)  {
					case "gene01":
						$sql ="CALL `gene01-animales registrados`();";
						muestraMultipleRS($sql,"botonno");
						break;
						
					case "gene02":
						$sql ="CALL `gene02-marcados por sexo-categoria`();";
						muestraMultipleRS($sql,"botonno");
						break;						

					case "gene03":
						$sql ="CALL `gene03-marcados por temporada-sexo-categoria`();";
						muestraMultipleRS($sql,"botonno");
						break;	

					case "gene04":
						$sql ="CALL `gene04-cuantos hay por temporada-sexo-categoria`();";
						muestraMultipleRS($sql,"botonno");
						break;	
						
					case "gene05":
					    if ($errorXL or !isset($_POST['tipoTempo']) or empty($_POST['tipoTempo']) ) {
							echo "Falta establecer el tipo de temporada!!! ";
						}
						else {
							echo "tipo de temporada: ".$_POST['tipoTempo'];
							$sql ="CALL `gene05-marcados en un TipoTemporada por temporada-sexo-categoria`('".$_POST['tipoTempo']."');";
							muestraMultipleRS($sql,"botonno");
						}
						break;		



					case "recu01":
						$sql ="CALL `recu01-recuperados al menos un temporada`();";
						muestraMultipleRS($sql,"botonno");
						break;

					case "recu01T":
						$v=true;
						$m=validar_tAnio($_POST['numero'],$v,true);
						if ($errorXL or !$v){
							echo $m;
						}					
						else{
							echo "en temporada: ".$_POST['numero'];
							$sql ="CALL `recu01T-recuperados en una temporada dada`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"botonno");
						}
						break;						

					case "recu02":
					    if ($errorXL or !isset($_POST['numero']) or empty($_POST['numero']) ) {
							echo "Falta establecer el n&uacute;mero de veces!!! ";
						}
						else{
							echo "n&uacute;mero de temporadas: ".$_POST['numero'];
							$sql ="CALL `recu02-recuperados n temporadas a partir de la marcacion`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"botonno");
						}
						break;							

					case "recu03":
						$sql ="CALL `recu03-recuperados solo en muda-solo en repro-en ambas`();";
						muestraMultipleRS($sql,"botonno");
						break;						
																	
					case "recu04":
						$sql ="CALL `recu04-recuperados solo en repro n temporadas`();";
						muestraMultipleRS($sql,"botonno");
						break;						

					case "recu05":
						$sql ="CALL `recu05-recuperados solo en muda n temporadas`();";
						muestraMultipleRS($sql,"botonno");
						break;						

					case "recu06":
					    if ($errorXL or !isset($_POST['numero']) or empty($_POST['numero']) ) {
							echo "Falta establecer el n&uacute;mero de temporadas!!! ";
						}
						else{
							echo "n&uacute;mero de temporadas: ".$_POST['numero'];					
							$sql ="CALL `recu06-recuperados en n temporadas muda repro consecutivas`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"monoscape");
						}
						break;						
						
					case "recu07":
					    if ($errorXL or !isset($_POST['numero']) or empty($_POST['numero']) ) {
							echo "Falta establecer el n&uacute;mero de temporadas!!! ";
						}
						else{
							echo "n&uacute;mero de temporadas: ".$_POST['numero'];					
							$sql ="CALL `recu07-recuperados en n temporadas de muda consecutivas`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"monoscape");
						}
						break;						

					case "recu08":
					    if ($errorXL or !isset($_POST['numero']) or empty($_POST['numero']) ) {
							echo "Falta establecer el n&uacute;mero de temporadas!!! ";
						}
						else{
							echo "n&uacute;mero de temporadas: ".$_POST['numero'];					
							$sql ="CALL `recu08-recuperados en n temporadas de repro consecutiva`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"monoscape");
						}
						break;							

					case "recu09":
						$sql ="CALL `recu09-crias y destetados recuperados al menos una vez`();";
						muestraMultipleRS($sql,"botonno");
						break;


					case "recu10":
						$sql ="CALL `recu10-crias y destetados recuperados por primera vez como`();";
						muestraMultipleRS($sql,"botonno");
						break;

					case "recu11":
					    if ($errorXL or !isset($_POST['numero']) or empty($_POST['numero']) or ($_POST['numero']) < 3) {
							echo "Falta establecer la cantidad de a&ntilde;os o es menor a 3!!! ";
						}
						else{
							echo "n&uacute;mero de temporadas: ".$_POST['numero'];					
							$sql ="CALL `recu11-vistos mas de n anios y no recuperados desde maxT-2`('".$_POST['numero']."');";
							muestraMultipleRS($sql,"botonno");
						}
						break;							
						

					case "recu12":
						$sql ="CALL `recu12-vistos una-dos-tres... veces en reproduccion`();";
						muestraMultipleRS($sql,"botonno");
						break;

					case "recu13":
						$sql ="CALL `recu13-vistos una-dos-tres... veces en muda`();";
						muestraMultipleRS($sql,"botonno");
						break;						

			

						
						
					case "tag01":
						$sql ="CALL `tag01-cuantos tags se colocaron`();";
						muestraMultipleRS($sql,"botonno");
						break;			

					case "tag02":
						$sql ="CALL `tag02-animles a los que se les puso 1-2-3...tags por anio`();";
						muestraMultipleRS($sql,"botonno");
						break;			

					case "tag03":
						$sql ="CALL `tag03-tags perdidos segun sexo`();";
						muestraMultipleRS($sql,"botonno");
						break;

					case "tag04":
						$sql ="CALL `tag04-tags perdidos a x anios de la colocacion`();";
						muestraMultipleRS($sql,"botonno");
						break;

					case "tag05":
						$sql ="CALL `tag05-tags con numeracion borrada`();";
						muestraMultipleRS($sql,"botonno");
						break;	

					case "tag06":
						$sql ="CALL `tag06-tags borrados por color`();";
						muestraMultipleRS($sql,"botonno");
						break;	

					case "tag07":
						$sql ="CALL `tag07-animales con 1-2-3... tags`();";
						muestraMultipleRS($sql,"botonno");
						break;							

						
					}   /*fin switch*/
				$numero="";	
			}
		  ?>

		  

		  
          <button type="button" class="btn btn-lg btn-primary" data-dismiss="modal">cerrar</button>

		</p>
        </div>
		 <div w3-include-html="footer.html"></div>
					<!-- <div class="modal-footer">
						   <h5 class="modal-title">GEMM@ - CESIMAR - CONICET (2018)</h5> 
					</div> -->

      </div>
      
    </div>
  </div>
  
</div>






<script src="js/w3.js"></script> 
 <!-- <div w3-include-html="tb_ventanaMres.html"></div> -->
<script>
w3.includeHTML();

</script> 


</BODY>
</HTML>