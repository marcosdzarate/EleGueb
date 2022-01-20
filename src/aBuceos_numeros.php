<?php 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

$salidax = "pantalla";
$preguntaCal = "NumerosOnOffShelf";
$numero="";
$tipoTempo="";

        $queTabla = 'buceos';  //para validar tipo!!
		
if (!empty($_POST)) {
	$salidax = limpia($_POST['salidax']);
	siErrorFuera ($salidax=="xlsx" or $salidax=="csv" or $salidax=="pantalla");

	$m= validar_tipo($_POST['tipoTempo'],$v,false);
	
	/* validacion basica */	
	$preguntaCal=limpia($_POST['Pregunta_cal']);
	
	
	$errorXL=false;
	
	/* respondiendo en... */
	$tipoTempo=$_POST['tipoTempo'];
	$archi = "pla_".$preguntaCal;
	if ($salidax<>"pantalla") {
		switch ($preguntaCal)  {	

			case "NumerosOnOffShelf":
				$sql ="CALL `Buc-Numeros00-resultado completo`(1,'".$tipoTempo."');"; 
				if ($salidax<>'pantalla') {
					if ($salidax == 'csv') {
						csvOnDeFlai($sql,$archi);
					}
					else {
						XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx",6);
					}
				}
				break;

			case "NumerosSegunDonde":			
				$sql ="CALL `Buc-Numeros00-resultado completo`(2,'".$tipoTempo."');"; 
				if ($salidax == 'csv') {
					csvOnDeFlai($sql,$archi);
				}
				else {
					XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx",6);
				}
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

    <div w3-include-html="sideBar_oremota.html"></div>

	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

<div class="container-fluid" style="width:90%; padding-top:10px;">   

    <div class="row">
		<div class="col-sm-8">
			<form id="fRespuestas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<div class="well" style="font-size:14px"  > 
					    <h3> estad&iacute;sticas b&aacute;sicas de los buceos</h3>
						<br>
						<div class="row">
							<div class="col-sm-5" >
								Elegir el tipo de salida: <br>
								<label class="radio-inline">
								  <input type="radio" name="salidax" id="salidax" value="pantalla" 
									<?php if (isset($salidax) && $salidax=="pantalla") echo "checked";?>>a pantalla 
								</label>
								
								<label class="radio-inline">
								  <input type="radio" name="salidax" id="salidax" value="csv" 
									<?php if (isset($salidax) && $salidax=="csv") echo "checked";?>>a csv 
								</label>
								
								<label class="radio-inline">
								  <input type="radio" name="salidax" id="salidax" value="xlsx" 
									<?php if (isset($salidax) && $salidax=="xlsx") echo "checked";?>>a XLSX 
								</label>
							</div>
							<div class="col-sm-2" >
							  &nbsp;&nbsp;&nbsp;&nbsp;
							</div>
							<div class="col-sm-5" >
								Seleccionar temporada
									<select class="form-control input-sm" style="width: auto" id="tipo"  name="tipo"
											onchange='getElementById("tipoTempo").value=this.value'>
										<option value=" " <?php echo empty($tipoTempo)?'selected':'';?> > </option>
										<option value="REPRO" <?php echo $tipoTempo=='REPRO'?'selected':'';?> >REPRO</option>
										<option value="MUDA"  <?php echo $tipoTempo=='MUDA'?'selected':'';?>>MUDA</option>
										<option value="FUERA"  <?php echo $tipoTempo=='FUERA'?'selected':'';?>>FUERA</option>
									</select>
							</div>
						</div>
						
					</div>


				<input id="tipoTempo"  name="tipoTempo" type="hidden"  value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">

				<input id="Pregunta_tit"  name="Pregunta_tit" type="hidden"  value="<?php echo !empty($pregu)?$pregu:'';?>">
				<input id="Pregunta_cal" name="Pregunta_cal" type="hidden"  value="<?php echo !empty($preguntaCal)?$preguntaCal:'';?>">
			</form>
		</div>
	</div>



    <div class="tab-content">
	<br><br>
	  
			<div class="row">
		  
				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("NumerosOnOffShelf","")' >
						<div class="panel-heading "><span class="badge badge">1</span></div>
						<div class="panel-body" id=PNumerosOnOffShelf>		  
							N&uacute;meros ON-OFF shelf
							<br><br>
							<small>estad&iacute;sticas de los buceos ON-OFF shelf</small>
							<br><br>
						</div>
					</div>
					
				</div>

				<div class="col-sm-4" >
					<div class="panel panel-consulta" onclick='datosMres("NumerosSegunDonde","")' >
						<div class="panel-heading ">
							<span class="badge badge">2</span> </div>
						<div class="panel-body" id=PNumerosSegunDonde>
							N&uacute;meros ON-OFF shelf (ida/vuelta)
							<br><br>
							<small>estad&iacute;sticas de los buceos ON-OFF shelf, separando la plataforma en ida y vuelta, donde fue posible....</small>
							<br><br>
						</div>
					</div>
					
				</div>
			</div>
					
					
				
				
					
<?php
if (!empty($_POST)) {
	if ($salidax=="pantalla") {
		
		
		echo '           <div class="panel-body">
                    <div class="panel panel-info" >';
		if (vacio($tipoTempo)){
			$tt="";
		}
		else{
			$tt=" para temporada $tipoTempo";
		}
		

		switch ($preguntaCal)  {	

			case "NumerosOnOffShelf":
				echo '              <div class="panel-heading" >N&uacute;meros ON-OFF shelf'.$tt.'</div>
									<div class="panel-body" style="overflow:auto"> ';
				$sql ="CALL `Buc-Numeros00-resultado completo`(1,'".$tipoTempo."');"; 
				$r=muestraMultipleRS($sql,"botonno");		
				break;

			case "NumerosSegunDonde":			
				echo '              <div class="panel-heading" >N&uacute;meros ON-OFF shelf (ida/vuelta)'.$tt.'</div>
									<div class="panel-body" style="overflow:auto"> ';
				$sql ="CALL `Buc-Numeros00-resultado completo`(2,'".$tipoTempo."');"; 
				$r=muestraMultipleRS($sql,"botonno");		
				break;
			
		}   /*fin switch*/
		
		echo '					</div>				
					</div>				
				</div>';
		
		
	}
}



			
			
?>			
			
	


    </div>			


</div>			
	










<script src="js/w3.js"></script> 
<script>
w3.includeHTML();
</script> 


</BODY>
</HTML>