<?php

/* algunos numeros respecto de las localizaciones y viajes*/

    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	
function llamarProc ($con,$sql,&$arre) {
/* llama a un procedimiento y devuelve un arreglo con el resultSet que sale del call */

	if(mysqli_real_query($con, $sql)==false){
	  return false;
	}
	else
	{
		$result=mysqli_store_result($con);
		/* $arre=mysqli_fetch_all ($result,MYSQLI_ASSOC); NO ME SIRVE EN SERVER*/
		while ($fila = mysqli_fetch_assoc($result)) {
		  $arre[] = $fila;
		}		
		mysqli_free_result($result);
				/* vacio result sets ocultos */
				while (mysqli_more_results($con) && mysqli_next_result($con)) 
				{
					if($result=mysqli_store_result($con)) {
						mysqli_free_result($result);
					}
				}	
	
	}
	return true;
}	
function padbla($n,$s){
	$s=str_pad($s,$n,"*",STR_PAD_LEFT);
	$s=str_replace("*","&nbsp;",$s);
	return $s;
}
	
	$eError='';
	$valid = true;



	$qSexo='';
	$qReproductiva='';
	$qTipoTempo='';
	$qFin_Viaje='';
	$qEtapa='';
	$qTieneLoc='';
	$qTieneBuc='';
	$condi="''";

    if (isset($_POST['filtrar'])) {
		/* si hay condiciones para filtrar, armo condicion para llamada a procedimientos en MYSQL*/
		$qSexoError = null;
		$qReproductivaError = null;
		$qTipoTempoError = null;
		$qFin_ViajeError = null;
		$qTieneLocError = null;
		$qTieneBucError = null;
		$qEtapaError = null;
		
        // los campos a validar	   
		$qSexo = limpia($_POST['qSexo']);
		$qReproductiva = limpia($_POST['qReproductiva']);
		$qTipoTempo = limpia($_POST['qTipoTempo']);
		$qFin_Viaje = limpia($_POST['qFin_Viaje']);
		$qTieneLoc = limpia($_POST['qTieneLoc']);
		$qTieneBuc = limpia($_POST['qTieneBuc']);
		$qEtapa = limpia($_POST['qEtapa']);

		// validar
		$valid = true;
        $qSexoError = validar_sexo ($qSexo,$valid,false);
        $qReproductivaError = validar_SIoNO ($qReproductiva,$valid,false);
        $qTipoTempoError = validar_tipoTempo ($qTipoTempo,$valid,false);
        $qFin_ViajeError = validar_fin_viaje ($qFin_Viaje,$valid,false);
		$qTieneLocError  = validar_SIoNO ($qTieneLoc,$valid,false);
		$qTieneBucError  = validar_SIoNO ($qTieneBuc,$valid,false);
		$qEtapaError  = validar_etapa ($qEtapa,$valid,false);
		$ti="";
		if($valid){
			/* armo condicion */
			/* $condi="'WHERE sexo=\'$qSexo\' AND reproductiva=\'$qReproductiva\''";  */
			$c1=array();
			$ti="";
			if(!vacio($qSexo)){
				$c1[]= "sexo=\'$qSexo\'";
				$ti.=$qSexo.'S';
			}
			if(!vacio($qReproductiva)){
				$c1[]= "reproductiva=\'$qReproductiva\'";
				if($qReproductiva=='NO'){
					$ti.='-JUVENILES';
				}
				else{
					$ti.='-ADULTOS';
				}
				
			}
			if(!vacio($qTipoTempo)){
				$tt=substr($qTipoTempo,0,3);
				$c1[]= "UCASE(SUBSTR(tipoTempo,1,3))=\'$tt\'";
				$ti.='-'.$qTipoTempo;
			}
			if(!vacio($qFin_Viaje)){
				$c1[]= "fin_viaje=\'$qFin_Viaje\'";
				$ti.='-'.$qFin_Viaje;
			}
			if(!vacio($qTieneLoc)){
				if($qTieneLoc=='SI'){
					$cl='tiene';
				}
				else{
					$cl='';
				}
				$c1[]= "conLoc=\'$cl\'";
			}
			if(!vacio($qTieneBuc)){
				if($qTieneBuc=='SI'){
					$cl='conBuc IS NOT NULL';
				}
				else{
					$cl='conBuc IS NULL';
				}
				$c1[]= $cl;
			}
			
			if(!vacio($qEtapa)){
				$c1[]= "LOCATE(\'$qEtapa\',Qetapas)>0";
			}




			
			$condi=implode(' AND ',$c1);
			if (!vacio($condi)){
				$condi="'WHERE $condi'"; //"'WHERE sexo=\'$qSexo\' AND reproductiva=\'$qReproductiva\''"
			}
			else {
				$condi="''";
			}
			// para controlllll $eError=$condi;

		}
	
	}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>


  <link rel="stylesheet" href="assetsMobi/web/assets/mobirise-icons/mobirise-icons.css">
  <link rel="stylesheet" href="assetsMobi/bootstrap/css/bootstrap.min.css">

    
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>
    

    <link rel="stylesheet" href="login/style/main.css">
	
	
	
    <link   href="css/miSideBar.css" rel="stylesheet">

	
<style>

th {
    text-align:center;
}	


.paraArriba {
    cursor: pointer;
    position: fixed;
    bottom: 20px;
    right: 20px;
    display:none;
}

select.input-sm {
    height: 20px;
}
.input-sm {
    padding: 0px 0px;
    font-size: 11px;
}
.bchico {
	padding:0px 4px;
	line-height:1;
}


</style>	

		
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
	
	
</head>

<body  class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>
    <div w3-include-html="sideBar_oremota.html"></div>

<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


<BR><BR><BR><BR>
  
	<div class="container" style="width:90%">
        <div class="panel panel-primary" style="background-color:transparent">
            <div class="panel-heading" >
                <h3 class="panel-title">fichas de viajes</h3>
            </div>

			<div class="panel-body" style="font-family:monospace;" >
			
			
			<div class="well">
			<h5 class="bg-info">selecci&oacute;n</h5>
			
			<form data-toggle="validator" id="CRtotalCenso" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            
				<div class="row">      <!--  -->

					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qSexo">sexo</label>
							   <select class="form-control input-sm" style="width: auto" id="qSexo" data-error="Seleccionar un elemento de la lista" name="qSexo" >
									<option value="" <?php if ($qSexo == "") {echo " selected";}?> ></option>
									<option value="HEMBRA" <?php if ($qSexo == "HEMBRA") {echo " selected";}?> >HEMBRA</option>
									<option value="MACHO"  <?php if ($qSexo == "MACHO") {echo " selected";}?>  >MACHO</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqSexo"></p>
							<?php if (!empty($qSexoError)): ?>
								<span class="help-inline"><?php echo $qSexoError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qReproductiva">categor&iacute;a</label>
							   <select class="form-control input-sm" style="width: auto" id="qReproductiva" data-error="Seleccionar un elemento de la lista" name="qReproductiva" >
									<option value="" <?php if ($qReproductiva == "") {echo " selected";}?> ></option>
									<option value="SI" <?php if ($qReproductiva == "SI") {echo " selected";}?> >adulto</option>
									<option value="NO"  <?php if ($qReproductiva == "NO") {echo " selected";}?>  >juvenil</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqReproductiva"></p>
							<?php if (!empty($qReproductivaError)): ?>
								<span class="help-inline"><?php echo $qReproductivaError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qTipoTempo">temporada</label>
							   <select class="form-control input-sm" style="width: auto" id="qTipoTempo" data-error="Seleccionar un elemento de la lista" name="qTipoTempo" >
									<option value="" <?php if ($qTipoTempo == "") {echo " selected";}?> ></option>
									<option value="MUDA" <?php if ($qTipoTempo == "MUDA") {echo " selected";}?> >muda</option>
									<option value="FUERA"  <?php if ($qTipoTempo == "FUERA") {echo " selected";}?>  >fuera</option>
									<option value="REPRO"  <?php if ($qTipoTempo == "REPRO") {echo " selected";}?>  >repro</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqTipoTempo"></p>
							<?php if (!empty($qTipoTempoError)): ?>
								<span class="help-inline"><?php echo $qTipoTempoError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qFin_Viaje">estado viaje</label>
							   <select class="form-control input-sm" style="width: auto" id="qFin_Viaje" data-error="Seleccionar un elemento de la lista" name="qFin_Viaje" >
									<option value="" <?php if ($qFin_Viaje == "") {echo " selected";}?> ></option>
									<option value="VIAJANDO" <?php if ($qFin_Viaje == "VIAJANDO") {echo " selected";}?> >viajando</option>
									<option value="VIAJE COMPLETO" <?php if ($qFin_Viaje == "VIAJE COMPLETO") {echo " selected";}?> >completo</option>
									<option value="VIAJE INCOMPLETO"  <?php if ($qFin_Viaje == "VIAJE INCOMPLETO") {echo " selected";}?>  >incompleto</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqFin_Viaje"></p>
							<?php if (!empty($qFin_ViajeError)): ?>
								<span class="help-inline"><?php echo $qFin_ViajeError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qTieneLoc">localizaciones</label>
							   <select class="form-control input-sm" style="width: auto" id="qTieneLoc" data-error="Seleccionar un elemento de la lista" name="qTieneLoc" >
									<option value="" <?php if ($qTieneLoc == "") {echo " selected";}?> ></option>
									<option value="SI" <?php if ($qTieneLoc == "SI") {echo " selected";}?> >SI</option>
									<option value="NO" <?php if ($qTieneLoc == "NO") {echo " selected";}?> >NO</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqTieneLoc"></p>
							<?php if (!empty($qTieneLocError)): ?>
								<span class="help-inline"><?php echo $qTieneLocError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-2">      <!--  -->
						<div class="form-group ">
							<label for="qTieneBuc">buceos</label>
							   <select class="form-control input-sm" style="width: auto" id="qTieneBuc" data-error="Seleccionar un elemento de la lista" name="qTieneBuc" >
									<option value="" <?php if ($qTieneBuc == "") {echo " selected";}?> ></option>
									<option value="SI" <?php if ($qTieneBuc == "SI") {echo " selected";}?> >SI</option>
									<option value="NO" <?php if ($qTieneBuc == "NO") {echo " selected";}?> >NO</option>
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqTieneBuc"></p>
							<?php if (!empty($qTieneBucError)): ?>
								<span class="help-inline"><?php echo $qTieneBucError;?></span>
							<?php endif; ?>
						</div>
                    </div>

					
				</div>

				<div class="row">      <!--  -->

					<div class="col-sm-5">      <!--  -->
						<div class="form-group ">
							<label for="qEtapa">etapa</label>
							   <select class="form-control input-sm" id="qEtapa" data-error="Seleccionar un elemento de la lista" name="qEtapa" >
									<option value="" <?php if ($qEtapa == "") {echo " selected";}?> ></option>
									<option value="COSTA ARG" <?php if ($qEtapa == "COSTA ARG") {echo " selected";}?> >COSTA ARGENTINA continental - seg&uacute;n seguimiento</option>
									<option value="COSTA" <?php if ($qEtapa == "COSTA") {echo " selected";}?> >COSTA (islas u otro país) - seg&uacute;n seguimiento</option>
									<option value="AJUSTE" <?php if ($qEtapa == "AJUSTE") {echo " selected";}?> >AJUSTE - se hace un ajuste de los instrumentos para que reinicie viaje</option>
									<option value="REINICIA" <?php if ($qEtapa == "REINICIA") {echo " selected";}?> >REINICIA VIAJE - seg&uacute;n seguimiento</option>
									<option value="SIN RECUPERACION" <?php if ($qEtapa == "SIN RECUPERACION") {echo " selected";}?> >SIN RECUPERACION - fin de viaje sin recuperaci&oacute;n del animal</option>
									<option value="RECUPERADO" <?php if ($qEtapa == "RECUPERADO") {echo " selected";}?> >RECUPERADO - fin de viaje con animal recuperado</option>
									
								</select>
							
							<div class="help-block with-errors"></div>
							<p id="JSErrorqEtapa"></p>
							<?php if (!empty($qEtapa)): ?>
								<span class="help-inline"><?php echo $qEtapaError;?></span>
							<?php endif; ?>
						</div>
                    </div>
			
					<div class="col-sm-6">      <!--  -->
                    </div>

			
				</div>

				
                     <div class="form-actions">
						<div class="row">
							<div class="col-sm-2">
								<button type="submit" name=filtrar class="btn btn-primary btn-sm" >filtrar</button>
							</div>
							<div class="col-sm-2">
								<button type="submit" name=todos  class="btn btn-primary btn-sm">todos</button>
							</div>
							<div class="col-sm-4">			   
								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
							</div>
						</div>

                    </div>


				
				
				
			</form>
			</div>
			
		
<?php
				
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
		{
		  $valid=false;
		  $eError = "No se puede conectar a MySQL: " . mysqli_connect_error();
		}
	else
	{

		/* genera vtResultado, tabla temporaria usada por los siguientes procedures */
		$sql = "CALL `locx-viajes Seleccion`($condi)";
		if (mysqli_query($con,$sql)===false){
			$eError="Problemas con CALL 0. ";
			$valid=false;
		}



		/* datos de la etapa de colocacion */
		$sql = "CALL `locx-viajes y tracks`()";
		$viaVT=array();
		$r=llamarProc ($con,$sql,$viaVT);
		if($r==false){
			$eError="Problemas con CALL 1. ";
			$valid=false;
		}
		
		$nVT = count($viaVT);

	    /* para mapeo de seleccion sii $qTieneLoc=='SI' y !vacio($qTipoTempo)*/
		$cSel="";
		if($qTieneLoc=='SI' and !vacio($qTipoTempo) and $nVT>0){
			for ($e=0; $e<$nVT; $e++){
				$v=$viaVT[$e]["viajeID"];
				$p=$viaVT[$e]["ptt"];
				$cSel.="($v,$p)";
			}
		}
		
		
		/* datos de las otras etapas */
		$sql = "CALL `locx-viajes y etapas`()";
		$viaET=array();
		$r=llamarProc ($con,$sql,$viaET);
		if($r==false){
			$eError.="Problemas con CALL 2. ";
			$valid=false;
		}
		$nET = count($viaET);

		
		/* numeros por viaje ptt sale en temporaria vtLocQuality */
		$sql = "CALL `locx-viajes LQ`";
		$viaLQ=array();
		$r=llamarProc ($con,$sql,$viaLQ);
		if($r==false){
			$eError.="Problemas con CALL 3. ";
			$valid=false;
		}
		$nLQ = count($viaLQ);

		
		
		/* numeros por viaje viaje temporada categoria...*/
		$sql = "CALL `locx-viajes y tracks totales`()";
		$viaTT=array();
		$r=llamarProc ($con,$sql,$viaTT);
		if($r==false){
			$eError.="Problemas con CALL 4. ";
			$valid=false;
		}
		$nTT = count($viaTT);

		/* numeros relativos a viajes con mas de un ptt e individuos con mas de un viaje*/
		$sql = "CALL `locx-viajes y tracks totales otros`()";
		$viaTO=array();
		$r=llamarProc ($con,$sql,$viaTO);
		if($r==false){
			$eError.="Problemas con CALL 5. ";
			$valid=false;
		}
		$nTO = count($viaTO);
				
				
		/* estadisticas de buceo ON-OFF*/
		$sql = "CALL `Buc-Numeros00-resultado completo`(1,'')";
		$bucST=array();
		$r=llamarProc ($con,$sql,$bucST);
		if($r==false){
			$eError.="Problemas con CALL 6. ";
			$valid=false;
		}
		$nST = count($bucST);
		
		
		if ($valid){
			$viajeA=-1;
			
			/* algunos max */
			//total	filtrOK	filtrOUT	LQ3	LQ2	LQ1	LQ0	LQA	LQB	maxDisPDelgada	maxDisSalida	kmTrack	fecha0	fechaU	difDias
			$max_disPDelgada=-999;
			$max_disSalida=-999;
			$max_recorrido=-999;
			$max_dias=-999;
			$max_filtrOK=-999;
			$sum_filtrOK=0;
			$sum_filtrOUT=0;
			$sum_recorrido=0;
			$sum_dias=0;
			for ($e=0; $e<$nLQ; $e++){
				$max_disPDelgada=max($max_disPDelgada,$viaLQ[$e]["maxDisPDelgada"]);
				$max_disSalida=max($max_disSalida,$viaLQ[$e]["maxDisSalida"]);
				$max_recorrido=max($max_recorrido,$viaLQ[$e]["kmTrack"]);
				$max_dias=max($max_dias,$viaLQ[$e]["difDias"]);
				$max_filtrOK=max($max_filtrOK,$viaLQ[$e]["filtrOK"]);
				$sum_filtrOK +=$viaLQ[$e]["filtrOK"];
				$sum_filtrOUT +=$viaLQ[$e]["filtrOUT"];
				$sum_recorrido +=$viaLQ[$e]["kmTrack"];
				$sum_dias +=$viaLQ[$e]["difDias"];
			}

			$Via_max_disPDelgada=-999;
			$Via_max_disSalida=-999;
			$Via_max_recorrido=-999;
			$Via_max_dias=-999;
			$Via_max_filtrOK=-999;

			
			for ($e=0; $e<$nLQ; $e++){
				if ($viaLQ[$e]["maxDisPDelgada"]==$max_disPDelgada) {
					$Via_max_disPDelgada=$viaLQ[$e]["viajeID"];
				}
				if ($viaLQ[$e]["maxDisSalida"]==$max_disSalida) {
					$Via_max_disSalida=$viaLQ[$e]["viajeID"];
				}
				if ($viaLQ[$e]["kmTrack"]==$max_recorrido) {
					$Via_max_recorrido=$viaLQ[$e]["viajeID"];
				}
				if ($viaLQ[$e]["difDias"]==$max_dias) {
					$Via_max_dias=$viaLQ[$e]["viajeID"];
				}
				if ($viaLQ[$e]["filtrOK"]==$max_filtrOK) {
					$Via_max_filtrOK=$viaLQ[$e]["viajeID"];
				}
				
			}
			

			echo '<div class="well">'."\n\n";
			echo '<h3 class="bg-primary">N&uacute;meros relativos a los viajes</h3>'."\n\n";
			

			echo '   <table class="table" style="text-align:center">'."\n\n";
			echo '     <thead >
						  <tr>
							<th></th>
							<th></th>
							<th></th>
							<th colspan=2 >hembras </th>
							<th colspan=2>machos</th>
						  </tr>
						</thead>'."\n\n";
			echo '     <thead >
						  <tr>
							<th>estado del<br> viaje</th>
							<th>tipo de<br>temporada</th>
							<th>categor&iacute;a</th>
							<th>con<br>localizaciones</th>
							<th>sin<br>localizaciones</th>
							<th>con<br>localizaciones</th>
							<th>sin<br>localizaciones</th>
						  </tr>
						</thead>'."\n\n";
			echo '		<tbody>'."\n\n";
						
			$t0=0;
			$t1=0;
			$t2=0;
			$t3=0;
			for ($t=0;$t<$nTT;$t++) {
				
			    if($viaTT[$t]["estado_viaje"]=='VIAJE COMPLETO') {
					$c="success";
				}
				else
				if($viaTT[$t]["estado_viaje"]=='VIAJE INCOMPLETO') {
					$c="danger";
				}
				else {
					$c="info";
				};			

				echo "	 		<tr class='$c'>"."\n\n";
				echo '				<td>'.$viaTT[$t]["estado_viaje"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["tipo_tempo"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["categoria"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["Them_con"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["Them_sin"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["Tmac_con"].'</td>'."\n\n";
				echo '				<td>'.$viaTT[$t]["Tmac_sin"].'</td>'."\n\n";
				echo '			</tr>'."\n\n";

				$t0 +=$viaTT[$t]["Them_con"];
				$t1 +=$viaTT[$t]["Them_sin"];
				$t2 +=$viaTT[$t]["Tmac_con"];
				$t3 +=$viaTT[$t]["Tmac_sin"];
			}
				$xt=$t0+$t1+$t2+$t3;
				$c="default";		//linea de total
				echo "	 		<tr class='$c'>"."\n\n";
				echo '				<td colspan=3 style="text-align:right">total '.$xt.'</td>'."\n\n";
				echo '				<td>'.$t0.'</td>'."\n\n";
				echo '				<td>'.$t1.'</td>'."\n\n";
				echo '				<td>'.$t2.'</td>'."\n\n";
				echo '				<td>'.$t3.'</td>'."\n\n";
				echo '			</tr>'."\n\n";
			
			
			
			echo '	    </tbody>
					</table>';

					
			echo '<br><h5 class="text-primary">Individuos con m&aacute;s de un viaje</h5>'."\n\n";		
			$t=0;
			while ($t<$nTO and $viaTO[$t]["queEs"]=='indiv')
			{
				echo "<p>".$viaTO[$t]["ref"];
				$a=explode("|",$viaTO[$t]["queVa"]);
				for ($xa=0;$xa<count($a);$xa++){
					$q=trim($a[$xa]);
					echo " <a  href='#".str_replace(' ','',$q)."' class='btn btn-info'>".$q."</a>";
				}
				
				echo "</p>"."\n\n";
				$t +=1;				
			}
			
			
			echo '<br><h5 class="text-primary">Viajes con m&aacute;s de un dispositivo de rastreo</h5>'."\n\n";		
			echo "<p>";
			while ($t<$nTO and $viaTO[$t]["queEs"]=='viaje')
			{
				$q=trim($viaTO[$t]["queVa"]);
				echo " <a  href='#".str_replace(' ','',$q)."' class='btn btn-info'>".$q."</a>";
				$t +=1;				
			}
				
			echo "</p>"."\n\n";
						
			
			echo '<br><h5 class="text-primary">Viajes con reingresos a zona PV</h5>'."\n\n";		
			echo "<p>";
			$va=-999;
			for ($t=0;$t<$nET;$t++)
			{
				if ($va<>$viaET[$t]["viajeID"]) {
                   if ($viaET[$t]["Oetapa"]<>"RECUPERADO" and
						$viaET[$t]["Oetapa"]<>"SIN RECUPERACION")
					{
						echo " <a  href='#viajeID".$viaET[$t]["viajeID"]."' class='btn btn-info'>viajeID ".$viaET[$t]["viajeID"]."</a>";
						$va = $viaET[$t]["viajeID"];
					}	
				}
				$t +=1;
			}
			echo "</p>"."\n\n";


			echo '</div>'."\n\n";
			echo '<div class="well">'."\n\n";
			
			echo '<h3 class="bg-primary">N&uacute;meros relativos a las localizaciones</h3>'."\n\n";

			echo '<h5 class="text-primary">Los n&uacute;meros que siguen se calculan exclusivamente con las localizaciones filtradas OK (agua y tierra). Las distancias se calculan en usando la f&oacute;rmula <a href="https://en.wikipedia.org/wiki/Great-circle_distance" target="_blank">Great Circle Distance</a>.</h5>'."\n\n";						
			
			

			echo '<div class="row">'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>localizaciones OK/OUT</u><br>".$sum_filtrOK."/".$sum_filtrOUT."\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>recorrido</u><br>".$sum_recorrido." km\n\n";
					echo '  </div>'."\n\n";
				
					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>d&iacute;as</u><br>".$sum_dias."\n\n";
					echo '  </div>'."\n\n";
			echo '</div>'."\n\n";




			echo '<h6 class="text-primary">M&aacute;ximos<h6>'."\n\n";
			echo '<div class="row">'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>localizaciones OK</u><br>".$max_filtrOK."\n\n";
					echo "<br><a  href='#viajeID$Via_max_filtrOK' class='btn btn-info'>viajeID $Via_max_filtrOK</a>"."<br>\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>recorrido</u><br>".$max_recorrido." km\n\n";
					echo "<br><a  href='#viajeID$Via_max_recorrido' class='btn btn-info'>viajeID $Via_max_recorrido</a>"."<br>\n\n";
					echo '  </div>'."\n\n";
				
					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>d&iacute;as</u><br>".$max_dias."\n\n";
					echo "<br><a  href='#viajeID$Via_max_dias' class='btn btn-info'>viajeID $Via_max_dias</a>"."<br>\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>distancia a PDelgada</u><br>".$max_disPDelgada." km\n\n";
					echo "<br><a  href='#viajeID$Via_max_disPDelgada' class='btn btn-info'>viajeID $Via_max_disPDelgada</a>"."<br>\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>distancia a la salida</u><br>".$max_disSalida." km\n\n";
					echo "<br><a  href='#viajeID$Via_max_disSalida' class='btn btn-info'>viajeID $Via_max_disSalida</a>"."<br>\n\n";
					echo '  </div>'."\n\n";

			echo '</div>'."\n\n";
			echo '</div>'."\n\n";


			echo '<div class="well">'."\n\n";
			echo '<h3 >Fichas de viaje';
			
			/* para mapeo de seleccion sii $qTieneLoc=='SI' y !vacio($qTipoTempo)*/
			if($qTieneLoc=='SI' and !vacio($qTipoTempo) and $nVT>0){
					echo ' <a title="'.$ti.'" onclick=ventanaM("mapin_localFiltro.php?fil='.$cSel.'",this.title) class="btn btn-info btn-sm"><span class="glyphicon glyphicon-map-marker"></span> mapa de la selecci&oacute;n (una loc/d&iacute;a)</a>';
			}
			
			echo '</h3>'."\n\n";
			echo '</div>'."\n\n";
			
			for ($v=0;$v<$nVT; $v++) {
				if ($viaVT[$v]["viajeID"]<>$viajeA){	/* cambia viaje */
					if($viajeA<>-1)	{
						echo '</div>'."\n\n";
					};
					echo '<p id=viajeID'.$viaVT[$v]["viajeID"].'></p><br>';
					echo '<div class="well" >'."\n\n";
					$viajeA =$viaVT[$v]["viajeID"];					
					
					echo '<div class="row">'."\n\n";				

					echo '	<div class="col-sm-4">'."\n\n";
					echo '<h5><span class="label label-info"> viajeID '.$viaVT[$v]["viajeID"]."</span><br><br>","\n\n";
					echo $viaVT[$v]["fin_viaje"]."<br><br>"."\n\n";
					echo ' claveU: <a title="ir a la ficha" target=_blank href="tr_resumen_individuo.php?claveU='.$viaVT[$v]['claveU'].'">'.$viaVT[$v]['claveU']."</a><br>"."\n\n";
					echo ' sexo &nbsp;: '.$viaVT[$v]["sexo"]."<br>"."\n\n";
					echo ' marcas: '.$viaVT[$v]["marcas"]."<br>"."\n\n";
					echo ' categor&iacute;a: '.$viaVT[$v]["categoria"]."\n\n";
					echo '  </h5></div>'."\n\n";
					

					echo '	<div class="col-sm-8">'."\n\n";
					echo '<div class="row" style="font-size:12px;">'."\n\n";
					echo '   <div class="col-sm-8">'."\n\n";
			
					echo ' <u>COLOCACI&Oacute;N</u>'."\n\n";
					echo $viaVT[$v]["fecha_colocacion"].' ('.$viaVT[$v]["tipoTempo"].") "."\n\n";
					echo $viaVT[$v]["nombre"].'<br>'."\n\n";
					//echo ' long/lat: ('.$viaVT[$v]["longi"].','.$viaVT[$v]["lati"].")<br>"."\n\n";
					echo ' instrumentos: '.$viaVT[$v]["que_instrumentos"]."<br>"."\n\n";

					$sql="SELECT * FROM viaje_config WHERE viajeID=".$viaVT[$v]["viajeID"].' LIMIT 1';
					$result=mysqli_query($con, $sql);									
					if($result==false){
						$eError="Problemas con SELECT viaje_config";
						break;
					}
					$viaConf=mysqli_fetch_array ( $result,MYSQLI_ASSOC );
					if(is_null($viaConf)){
						$eError="Sin configuraci&oacute;n para viaje ".$viaVT[$v]["viajeID"]."!!!";
						break;						
					}
					echo '   </div>'."\n\n";
					
					echo '   <div class="col-sm-2">'."\n\n";
					echo '   </div>'."\n\n";
					echo '   <div class="col-sm-2">'."\n\n";
					echo '   </div>'."\n\n";
					
					echo '   </div>'."\n\n";

					
					echo '<div class="row" style="font-size:12px;"><br>&nbsp;&nbsp<u>configuraci&oacute;n</u><br>'."\n\n";
					echo '   <div class="col-sm-12">'."\n\n";
					echo 'posicionamiento: '.$viaConf['posicionamiento'];
					echo '   </div>'."\n\n";
					echo '</div>'."\n\n";
					
					echo '<div class="row" style="font-size:12px;">'."\n\n";
					echo '   <div class="col-sm-12">'."\n\n";
					echo 'protocolo de transmisi&oacute;n: '.$viaConf['argos_trans'];
					echo '   </div>'."\n\n";
					echo '</div>'."\n\n";

					echo '<div class="row" style="font-size:12px">'."\n\n";
					echo '   <div class="col-sm-6">'."\n\n";
					echo 'temp.: '.$viaConf['temperatura'].'/'.$viaConf['temperatura_intervalo']."<br>\n\n";
					echo 'prof.: '.$viaConf['profundidad'].'/'.$viaConf['profundidad_intervalo']."<br>\n\n";
					echo 'luz  &nbsp;: '.$viaConf['luz'].'/'.$viaConf['luz_intervalo']."\n\n";
					echo '   </div>'."\n\n";
					echo '   <div class="col-sm-6">'."\n\n";
					echo 'pitch: '.$viaConf['angulo_pitch']."<br>\n\n";
					echo 'roll : '.$viaConf['angulo_roll']."<br>\n\n";
					echo 'yaw &nbsp;: '.$viaConf['angulo_yaw']."\n\n";
					echo '   </div>'."\n\n";
					echo '</div>'."\n\n";

					echo '<div class="row" style="font-size:12px;">'."\n\n";
					echo '   <div class="col-sm-12">'."\n\n";
					echo 'c&aacute;mara: '.$viaConf['camara'];
					echo '   </div>'."\n\n";
					echo '</div>'."\n\n";

					
					
					
					
					echo '  </div>'."\n\n";

					echo '</div>'."\n\n";
					
					/* busco en viaET por claveU y fecha_colocacion */
					$ee=-1;
					for ($e=0; $e<$nET; $e++){
						if ( $viaVT[$v]["claveU"]==$viaET[$e]["claveU"]  and
							 $viaVT[$v]["fecha_colocacion"]==$viaET[$e]["f_coloca"] ) {
								 $ee=$e;
								 break;
							 }
					}
					if ($ee>=0) {
						/* datos de las etapas */
					echo '<div class="row">'."\n\n";				

					echo '	<div class="col-sm-4">'."\n\n";
					echo ' </div>'."\n\n";
					

					echo '	<div class="col-sm-8"> '."\n\n";
					echo '   <br><u>otras etapas</u><br> ';
					do {
						echo ' '.$viaET[$ee]["Oetapa"].' '. $viaET[$ee]["Ofecha"]. ' ('.$viaET[$ee]["tipoTempo"].') ';
						echo $viaET[$ee]["nombre"]."<br>"."\n\n";
						$ee+=1;
					} while ($ee<$nET and $viaVT[$v]["claveU"]==$viaET[$ee]["claveU"]  and
							 $viaVT[$v]["fecha_colocacion"]==$viaET[$ee]["f_coloca"]);
					echo '  </div>'."\n\n";
					



					echo '</div>'."\n\n";									
						
					}
				
				}	/* fin cambia viaje */
				
				/* busco en viaLq por viajeID y ptt */
				$ee=-1;
				for ($e=0; $e<$nLQ; $e++){
					if ( $viaVT[$v]["viajeID"]==$viaLQ[$e]["viajeID"]  and
						 $viaVT[$v]["ptt"]==$viaLQ[$e]["ptt"] ) {
							 $ee=$e;
							 break;
						 }
				}
				if ($ee>=0) {
					/* datos de las LQ */
					echo '   <br><h6 class="text-primary">n&uacute;meros respecto de las localizaciones OK';
					if($viaVT[$v]["ptt"]<>0) {
						echo '   para instrumento '.$viaVT[$v]["ptt"];
					}
					echo ' <a title="localizaciones para " onclick=ventanaM("mapin_local.php?ID='.$viaVT[$v]["viajeID"].'&mar='.str_replace(", ","_",$viaVT[$v]["marcas"]).'&ptt='.str_replace(", ","_",$viaVT[$v]["ptt"]).'",this.title) class="btn btn-info btn-sm"><span class="glyphicon glyphicon-map-marker"></span> mapa</a>';
					
					echo '</h6> ';
					echo '<div class="row">'."\n\n";				
					
					echo '	<div class="col-sm-2">'."\n\n";
					echo 'total: '.padbla(5,$viaLQ[$ee]['total'])."<br>\n\n";
					echo '<h6 class="text-primary">OK &nbsp;&nbsp;: '.padbla(5,$viaLQ[$ee]['filtrOK'])."</h6>\n\n";
					echo 'fuera: '.padbla(5,$viaLQ[$ee]['filtrOUT'])."<br>\n\n";					
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "LQ3". padbla(5,$viaLQ[$ee]['LQ3'])."<br>\n\n";
					echo "LQ2". padbla(5,$viaLQ[$ee]['LQ2'])."<br>\n\n";
					echo "LQ1". padbla(5,$viaLQ[$ee]['LQ1'])."<br>\n\n";
					echo "LQ0". padbla(5,$viaLQ[$ee]['LQ0'])."<br>\n\n";
					echo "LQA". padbla(5,$viaLQ[$ee]['LQA'])."<br>\n\n";
					echo "LQB". padbla(5,$viaLQ[$ee]['LQB']);
					echo '  </div>'."\n\n";

					
					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>per&iacute;odo</u><br>".$viaLQ[$ee]['fecha0'].'<br>'.$viaLQ[$ee]['fechaU'].'<br>'.$viaLQ[$ee]['difDias']." d&iacute;as\n\n";
					echo '  </div>'."\n\n";
					
					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>maxDist PDelgada</u><br>".$viaLQ[$ee]['maxDisPDelgada']." km\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>maxDist salida</u><br>".$viaLQ[$ee]['maxDisSalida']." km\n\n";
					echo '  </div>'."\n\n";

					echo '	<div class="col-sm-2">'."\n\n";
					echo "<u>recorrido</u><br>".$viaLQ[$ee]['kmTrack']." km\n\n";
					echo '  </div>'."\n\n";




					echo '</div>'."\n\n";									
					
				}
				


				/* busco en bucST por viajeID las estadisticas de buceo asociadas */
				$ee=-1;
				for ($e=0; $e<$nST; $e++){
					if ( $viaVT[$v]["viajeID"]==$bucST[$e]["viajeID"] ){
							 $ee=$e;
							 break;
						 }
				}
				if ($ee>=0) {
					$mMal="";
					if($bucST[$ee]["claveU"] =="ABVM" or $bucST[$ee]["claveU"]=="AAQJ"){
						$mMal="(el instrumento no registr&oacute; correctamente la profundidad fuera de plataforma)";	
					}					
					/* estadisticas basicas para el viaje de los buceos - puede haber mas de un tramo */
					echo '   <br><h6 class="text-primary">estad&iacute;sticas b&aacute;sicas de los buceos';
						
					echo '</h6> ';
					echo '<table class="table" style="text-align:center">'."\n\n";			//encabezados tablita
					echo '<thead>'."\n\n";
					echo '   <tr>'."\n\n";
					echo '	   <th></th>'."\n\n";
					echo '	   <th>tabla de detalle</th>'."\n\n";
					echo '	   <th>shelf</th>'."\n\n";
					echo '	   <th>#buceos</th>'."\n\n";
					echo '	   <th>d&iacute;as reg.</th>'."\n\n";
					echo '	   <th>prof med</th>'."\n\n";
					echo '	   <th>prof max</th>'."\n\n";
					echo '	   <th>dur med</th>'."\n\n";
					echo '	   <th>dur max</th>'."\n\n";
					echo "     <th>med IS<=10'</th>"."\n\n";
					echo "	   <th># IS>10'</th>"."\n\n";
					echo '    </tr>'."\n\n";
					echo '  </thead>'."\n\n";
					echo '<tbody>'."\n\n";
					

					$nvia=0;
					while (($ee<$nST) and ($viaVT[$v]["viajeID"]==$bucST[$ee]["viajeID"])){
						echo '    <tr>'."\n\n";
						$xcu=$viaVT[$v]['claveU'];
						$xde=$bucST[$ee]["tablaDetalle"];
						$nvia+=1;
						echo '	   <td>'." <a href='aBuceos_grafico.php?claveU=$xcu&nroViaje=$nvia&xde=$xde' target=_blank class='btn btn-warning btn-sm bchico'>buc</a>".'</td>'."\n\n";
						echo '	   <td>'.$xde.'</td>'."\n\n";
						echo '	   <td>'."ON".'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["cantBuceos"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["diasRegistrados"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["profMaxMed"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["profMaxMax"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["duraMed"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["duraMax"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["inSupMed"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["inSup"].'</td>'."\n\n";
						echo '    </tr>'."\n\n";
						echo '    <tr>'."\n\n";
						echo '	   <td>'."".'</td>'."\n\n";
						echo '	   <td>'."".'</td>'."\n\n";
						echo '	   <td>'."OFF".'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["cantBuceosOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["diasRegistradosOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["profMaxMedOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["profMaxMaxOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["duraMedOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["duraMaxOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["inSupMedOff"].'</td>'."\n\n";
						echo '	   <td>'.$bucST[$ee]["inSupOff"].'</td>'."\n\n";
						echo '    </tr>'."\n\n";

						$ee+=1;
					}
					echo '  </tbody>'."\n\n";
					echo '</table>'."\n\n";
					echo $mMal;
					
				}
				

				
				

				
				
			}
			echo '</div>'."\n\n";  /* well */
			
		}
		else {} /* valid */
		mysqli_close($con);
		
	}
	
	


?>		
		
		
				
				<div class="row">
					<div class="col-sm-1">
					</div>
					<div class="col-sm-8">
						<?php if (!empty($eError)): ?>
							<span class="help-inline"><?php echo $eError;?></span>
						<?php endif; ?>

					</div>
				</div>                
						
<!--
			</div>
		</div>
-->
<a id="paraArriba" href="#" class="btn btn-primary btn-lg paraArriba" role="button" title="Click para volver arriba" data-toggle="tooltip" data-placement="left"><span class="glyphicon glyphicon-chevron-up"></span></a>		
		
<!--
	</div>
-->	


<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>



<script type="text/javascript">
$(document).ready(function(){
     $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#paraArriba').fadeIn();
            } else {
                $('#paraArriba').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#paraArriba').click(function () {
            $('#paraArriba').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
        
        $('#paraArriba').tooltip('show');

});
</script>

</body>
</html>