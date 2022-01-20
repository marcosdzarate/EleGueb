<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'viaje_config';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $viajeID = null;
        $claveU = null;
        $temporada = null;
		$tipoTempo= null;
        $profundidad = null;
        $profundidad_intervalo = null;
        $temperatura = null;
        $temperatura_intervalo = null;
        $luz = null;
        $luz_intervalo = null;
        $posicionamiento = null;
        $angulo_pitch = null;
        $angulo_roll = null;
        $angulo_yaw = null;
        $camara = null;
        $argos_trans = null;
        $fin_viaje = null;
        $estrategia = null;
        $fecha = null;

    $v=true;


    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["claveU"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    $temporada = null;
    if (isset($_GET["temporada"])) {
        $temporada=$_GET["temporada"];
        $m = validar_temporada ($temporada,$claveU,$v,true);
    }
    else{
        if (!isset($_GET["temporada"]) and !isset($_POST["temporada"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);
	
    $tipoTempo = null;
    if (isset($_GET["tipoTempo"])) {
        $tipoTempo=$_GET["tipoTempo"];
        $m = validar_tipoTempo ($tipoTempo,$v,true);
    }
    else{
        if (!isset($_GET["tipoTempo"]) and !isset($_POST["tipoTempo"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    $fecha = null;  /* es la fecha_colocacion */
    if (isset($_GET["fecha"])) {
        $fecha=$_GET["fecha"];
        $m = validar_fecha ($fecha,$v,true);
    }
    else{
        if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) ){
			$v=false;
        }
	}
    siErrorFuera($v);

    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $viajeIDError = null;
        $claveUError = null;
        $temporadaError = null;
        $tipoTempoError = null;
        $profundidadError = null;
        $profundidad_intervaloError = null;
        $temperaturaError = null;
        $temperatura_intervaloError = null;
        $luzError = null;
        $luz_intervaloError = null;
        $posicionamientoError = null;
        $angulo_pitchError = null;
        $angulo_rollError = null;
        $angulo_yawError = null;
        $camaraError = null;
        $argos_transError = null;
        $fin_viajeError = null;
        $estrategiaError = null;
        $comentarioError = null;

        $fecha = null;

        $eError = null;

        // los campos a validar
        // no esta en formulario al inicio $viajeID = limpia($_POST['viajeID']);
        $claveU = limpia($_POST['claveU']);
        $temporada = limpia($_POST['temporada']);
        $tipoTempo = limpia($_POST['tipoTempo']);
        $profundidad = limpia($_POST['profundidad']);
        $profundidad_intervalo = limpia($_POST['profundidad_intervalo']);
        $temperatura = limpia($_POST['temperatura']);
        $temperatura_intervalo = limpia($_POST['temperatura_intervalo']);
        $luz = limpia($_POST['luz']);
        $luz_intervalo = limpia($_POST['luz_intervalo']);
        $posicionamiento = limpia($_POST['posicionamiento']);
        $angulo_pitch = limpia($_POST['angulo_pitch']);
        $angulo_roll = limpia($_POST['angulo_roll']);
        $angulo_yaw = limpia($_POST['angulo_yaw']);
        $camara = limpia($_POST['camara']);
        $argos_trans = limpia($_POST['argos_trans']);
        $fin_viaje = limpia($_POST['fin_viaje']);
        $estrategia = limpia($_POST['estrategia']);
        $comentario = limpia($_POST['comentario']);

        $fecha = limpia($_POST['fecha']);  

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $viajeIDError = validar_viajeID ($viajeID,$valid,true);
        $claveUError = validar_claveU ($claveU,$valid,true);
        $profundidadError = validar_profundidad ($profundidad,$valid,false);
        $profundidad_intervaloError = validar_profundidad_intervalo ($profundidad_intervalo,$valid,false);
        $temperaturaError = validar_temperatura ($temperatura,$valid,false);
        $temperatura_intervaloError = validar_temperatura_intervalo ($temperatura_intervalo,$valid,false);
        $luzError = validar_luz ($luz,$valid,false);
        $luz_intervaloError = validar_luz_intervalo ($luz_intervalo,$valid,false);
        $posicionamientoError = validar_posicionamiento ($posicionamiento,$valid,false);
        $angulo_pitchError = validar_angulo_pitch ($angulo_pitch,$valid,false);
        $angulo_rollError = validar_angulo_roll ($angulo_roll,$valid,false);
        $angulo_yawError = validar_angulo_yaw ($angulo_yaw,$valid,false);
        $camaraError = validar_camara ($camara,$valid,false);
        $fin_viajeError = validar_fin_viaje ($fin_viaje,$valid,false);
        $estrategiaError = validar_estrategia ($estrategia,$valid,false);
        $fechaError = validar_fecha ($fecha,$valid,true);

        $eError = validar_temporada ($temporada,$claveU,$valid,true); //son hidden
        $eError .= validar_tipoTempo ($tipoTempo,$valid,true);

    // validacion entre campos  //$viajeID
        $eError .= validarForm_viaje_config($viajeID,$claveU,$temporada,$tipoTempo,$fecha, $profundidad,$profundidad_intervalo, $temperatura,$temperatura_intervalo,$luz,$luz_intervalo,$posicionamiento,$angulo_pitch,$angulo_roll,$angulo_yaw,$camara,$argos_trans,$fin_viaje,$estrategia,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLprofundidad = ($profundidad == '' ? NULL : $profundidad);
            $SQLprofundidad_intervalo = ($profundidad_intervalo == '' ? NULL : $profundidad_intervalo);
            $SQLtemperatura = ($temperatura == '' ? NULL : $temperatura);
            $SQLtemperatura_intervalo = ($temperatura_intervalo == '' ? NULL : $temperatura_intervalo);
            $SQLluz = ($luz == '' ? NULL : $luz);
            $SQLluz_intervalo = ($luz_intervalo == '' ? NULL : $luz_intervalo);
            $SQLposicionamiento = ($posicionamiento == '' ? NULL : $posicionamiento);
            $SQLangulo_pitch = ($angulo_pitch == '' ? NULL : $angulo_pitch);
            $SQLangulo_roll = ($angulo_roll == '' ? NULL : $angulo_roll);
            $SQLangulo_yaw = ($angulo_yaw == '' ? NULL : $angulo_yaw);
            $SQLcamara = ($camara == '' ? NULL : $camara);
            $SQLargos_trans = ($argos_trans == '' ? NULL : $argos_trans);
            // $SQLfin_viaje = ($fin_viaje == '' ? NULL : $fin_viaje);
            $SQLestrategia = ($estrategia == '' ? NULL : $estrategia);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO viaje_config(claveU,fecha_colocacion,profundidad, profundidad_intervalo,temperatura,temperatura_intervalo,luz,luz_intervalo, posicionamiento,angulo_pitch,angulo_roll,angulo_yaw,camara,argos_trans, fin_viaje, 
			estrategia,comentario) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
			$q->execute(array($claveU,$fecha,$SQLprofundidad,$SQLprofundidad_intervalo, $SQLtemperatura,$SQLtemperatura_intervalo,$SQLluz, $SQLluz_intervalo,$SQLposicionamiento,$SQLangulo_pitch,$SQLangulo_roll,$SQLangulo_yaw, $SQLcamara,$SQLargos_trans,$fin_viaje,$SQLestrategia,$SQLcomentario));

            $arr = $q->errorInfo();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
				$viajeID = $pdo->lastInsertId();
                $guardado='ok';

            }
            Database::disconnect();



        }
    }

     $param0 = "?claveU=".$claveU;
     $paramV = "?claveU=$claveU&temporada=$temporada&tipoTempo=$tipoTempo&fecha=$fecha";


?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">

    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>

<style>
.borde {
	border-right: #cdcdcd 1px solid;
}

.parp {
	margin: 5px;
	padding-left:5px;
}
.filam {
	margin-top:-5px;
}

</style>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		

</head>

<body>
    <div class="container" style="width:94%">
	
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">configuraci&oacute;n y estado del viaje </h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRviaje_config" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            <input name="temporada" type="hidden"  value="<?php echo !empty($temporada)?$temporada:'';?>">
                            <input name="tipoTempo" type="hidden"  value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">							
				
					
					<div class="row filam">
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="claveU">ClaveU</label>
									<input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorclaveU"></p>
									<?php if (!empty($claveUError)): ?>
										<span class="help-inline"><?php echo $claveUError;?></span>
									<?php endif; ?>
							</div>

						</div>
						
						<div class="col-sm-3">
							<div class="form-group ">
								<label for="fecha">Fecha colocaci&oacute;n</label>
									<input type="text" class="form-control input-sm" style="width:100px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha"></p>
									<?php if (!empty($fechaError)): ?>
										<span class="help-inline"><?php echo $fechaError;?></span>
									<?php endif; ?>
							</div>
						</div>
						
					<?php if ($guardado=="ok"): ?>
						<div class="col-sm-3">		

							<div class="form-group ">
								 <label for="viajeID">ID de viaje</label> 
									<input type="text" class="form-control input-sm" style="width:90px" id="viajeID" readonly  name="viajeID" value="<?php echo !empty($viajeID)?$viajeID:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorviajeID"></p>
									<?php if (!empty($viajeIDError)): ?>
										<span class="help-inline"><?php echo $viajeIDError;?></span>
									<?php endif; ?>


							</div>
						</div>			
						<div class="col-sm-4">	
                              <!--  <a href="instrumentos_colocados_index.php?viajeID=<?php echo $viajeID;?>" target="_blank" class="btn btn-info btn-lg"> 
                                <span class="glyphicon glyphicon-music"></span> qu&eacute; instrumentos</a> -->
								
                                <a onclick=ventanaMIns("instrumentos_colocados_index.php?viajeID=<?php echo $viajeID;?>",this.title) title="instrumentos que lleva el elefante en su viaje, fecha de colocaci&oacute;n <?php echo $fecha;?>" class="btn btn-info btn-lg"> 
                                <span class="glyphicon glyphicon-music"></span> qu&eacute; instrumentos</a>
								
								
						</div>				
					<?php endif ?>
						
						
					</div>

				<div class="well" style="padding:10px">
					
					<label style="color:#464fa0" class="filam" >Los Par&aacute;metros</label>
					<button type="button" class="btn btn-info btn-sm" title="acerca de los valores siguientes" data-toggle="collapse" data-target="#info">
					<span class="glyphicon glyphicon-info-sign"></span></button>
					<div id="info" class="collapse">
						<div class="row filam">
							<div class="col-sm-1">				
							</div>
							<div class="col-sm-3">				
										<p class="parp"><strong>NO</strong>: no se registra</p>						
										<p class="parp" style="padding-bottom:10px"><strong>SI</strong>: se registra</p>
							</div>

							<div class="col-sm-7">				
										<p class="parp"><strong>MAL</strong>: el registro es incorrecto o incompleto al final del viaje</p>					
										<p class="parp" style="padding-bottom:10px"><strong>OK</strong>: el registro es correcto al final del viaje</p>
							</div>
						</div>		
					</div>
					
			

					
					
					<div class="row">
						<div class="col-sm-2 borde" >
							<div class="form-group ">
								<label for="profundidad">Profundidad</label>
									<select class="form-control input-sm" style="width: auto" id="profundidad" data-error="Seleccionar un elemento de la lista"     data-error="Seleccionar un elemento de la lista" name="profundidad">
										<option value="" <?php if ($profundidad == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($profundidad == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($profundidad == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($profundidad == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($profundidad == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorprofundidad"></p>
									<?php if (!empty($profundidadError)): ?>
										<span class="help-inline"><?php echo $profundidadError;?></span>
									<?php endif; ?>
							</div>

							<div class="form-group" style="margin-top:-10px">
								<label for="profundidad_intervalo">inter (seg)&nbsp;</label><span title="intervalo de muestreo en segundos" class="glyphicon glyphicon-question-sign text-info"></span>
									<input type="text" class="form-control input-sm" style="width:90px" id="profundidad_intervalo" name="profundidad_intervalo"  
									data-dmin="<?php echo CONST_intervalo_min;?>" data-dmax="<?php echo CONST_intervalo_max;?>"  data-pentero 
									data-error="<?php echo CONST_intervalo_men;?>" 
									value="<?php echo !empty($profundidad_intervalo)?$profundidad_intervalo:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorprofundidad_intervalo"></p>
									<?php if (!empty($profundidad_intervaloError)): ?>
										<span class="help-inline"><?php echo $profundidad_intervaloError;?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-2  borde">
							<div class="form-group ">
								<label for="temperatura">Temperatura</label>
									<select class="form-control input-sm" style="width: auto" id="temperatura" data-error="Seleccionar un elemento de la lista" name="temperatura">
										<option value="" <?php if ($temperatura == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($temperatura == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($temperatura == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($temperatura == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($temperatura == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrortemperatura"></p>
									<?php if (!empty($temperaturaError)): ?>
										<span class="help-inline"><?php echo $temperaturaError;?></span>
									<?php endif; ?>
							</div>

							<div class="form-group " style="margin-top:-10px">
								<label for="temperatura_intervalo">inter (seg)&nbsp;</label><span title="intervalo de muestreo en segundos" class="glyphicon glyphicon-question-sign text-info"></span>
									<input type="text" class="form-control input-sm" style="width:90px" id="temperatura_intervalo" name="temperatura_intervalo" 
									data-dmin="<?php echo CONST_intervalo_min;?>" data-dmax="<?php echo CONST_intervalo_max;?>"  data-pentero 
									data-error="<?php echo CONST_intervalo_men;?>"   
									value="<?php echo !empty($temperatura_intervalo)?$temperatura_intervalo:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrortemperatura_intervalo"></p>
									<?php if (!empty($temperatura_intervaloError)): ?>
										<span class="help-inline"><?php echo $temperatura_intervaloError;?></span>
									<?php endif; ?>
							</div>

						</div>

						<div class="col-sm-2   borde">
							<div class="form-group ">
								<label for="luz">Luz</label>
									<select class="form-control input-sm" style="width: auto" id="luz" data-error="Seleccionar un elemento de la lista"     data-error="Seleccionar un elemento de la lista" name="luz">
										<option value="" <?php if ($luz == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($luz == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($luz == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($luz == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($luz == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorluz"></p>
									<?php if (!empty($luzError)): ?>
										<span class="help-inline"><?php echo $luzError;?></span>
									<?php endif; ?>
							</div>

							<div class="form-group " style="margin-top:-10px">
								<label for="luz_intervalo" >inter (seg)&nbsp;</label><span title="intervalo de muestreo en segundos" class="glyphicon glyphicon-question-sign text-info"></span>
									<input type="text" class="form-control input-sm" style="width:90px" id="luz_intervalo" name="luz_intervalo"  
									data-dmin="<?php echo CONST_intervalo_min;?>" data-dmax="<?php echo CONST_intervalo_max;?>"  data-pentero 
									data-error="<?php echo CONST_intervalo_men;?>" 
									value="<?php echo !empty($luz_intervalo)?$luz_intervalo:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorluz_intervalo"></p>
									<?php if (!empty($luz_intervaloError)): ?>
										<span class="help-inline"><?php echo $luz_intervaloError;?></span>
									<?php endif; ?>
							</div>

						</div>
						
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="angulo_pitch">Angulo pitch</label>
									<select class="form-control input-sm" style="width: auto" id="angulo_pitch" data-error="Seleccionar un elemento de la lista" name="angulo_pitch">
										<option value="" <?php if ($angulo_pitch == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($angulo_pitch == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($angulo_pitch == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($angulo_pitch == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($angulo_pitch == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorangulo_pitch"></p>
									<?php if (!empty($angulo_pitchError)): ?>
										<span class="help-inline"><?php echo $angulo_pitchError;?></span>
									<?php endif; ?>
							</div>
							
							<div class="form-group " style="margin-top:-10px">
								<label for="camara">C&aacute;mara</label>
									<select class="form-control input-sm" style="width: auto" id="camara" data-error="Seleccionar un elemento de la lista"  name="camara">
										<option value="" <?php if ($camara == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($camara == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($camara == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($camara == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($camara == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorcamara"></p>
									<?php if (!empty($camaraError)): ?>
										<span class="help-inline"><?php echo $camaraError;?></span>
									<?php endif; ?>
							</div>								
						</div>

						<div class="col-sm-2">
							<div class="form-group ">
								<label for="angulo_roll">Angulo roll</label>
									<select class="form-control input-sm" style="width: auto" id="angulo_roll" data-error="Seleccionar un elemento de la lista"  name="angulo_roll">
										<option value="" <?php if ($angulo_roll == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($angulo_roll == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($angulo_roll == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($angulo_roll == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($angulo_roll == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorangulo_roll"></p>
									<?php if (!empty($angulo_rollError)): ?>
										<span class="help-inline"><?php echo $angulo_rollError;?></span>
									<?php endif; ?>
							</div>
						

						</div>

						<div class="col-sm-2">
							<div class="form-group ">
								<label for="angulo_yaw">Angulo yaw</label>
									<select class="form-control input-sm" style="width: auto" id="angulo_yaw" data-error="Seleccionar un elemento de la lista"  name="angulo_yaw">
										<option value="" <?php if ($angulo_yaw == "") {echo " selected";}?> ></option>
										<option value="NO" <?php if ($angulo_yaw == "NO") {echo " selected";}?> >NO</option>
										<option value="SI" <?php if ($angulo_yaw == "SI") {echo " selected";}?> >SI</option>
										<option value="REGISTRO INCORRECTO" <?php if ($angulo_yaw == "REGISTRO INCORRECTO") {echo " selected";}?> >MAL</option>
										<option value="REGISTRO CORRECTO" <?php if ($angulo_yaw == "REGISTRO CORRECTO") {echo " selected";}?> >OK</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorangulo_yaw"></p>
									<?php if (!empty($angulo_yawError)): ?>
										<span class="help-inline"><?php echo $angulo_yawError;?></span>
									<?php endif; ?>
							</div>

						</div>						
						
						
					</div>
				</div>

					<div class="row filam">
						<div class="col-sm-6">
							<div class="form-group ">
								<label for="posicionamiento">Tipo de posicionamiento</label>
									<select class="form-control input-sm" style="width: auto" id="posicionamiento" data-error="Seleccionar un elemento de la lista" name="posicionamiento">
										<option value="" <?php if ($posicionamiento == "") {echo " selected";}?> ></option>
										<option value="GEOLOCACION" <?php if ($posicionamiento == "GEOLOCACION") {echo " selected";}?> >GEOLOCACION</option>
										<option value="SATELITAL" <?php if ($posicionamiento == "SATELITAL") {echo " selected";}?> >SATELITAL</option>
										<option value="GPS" <?php if ($posicionamiento == "GPS") {echo " selected";}?> >GPS</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorposicionamiento"></p>
									<?php if (!empty($posicionamientoError)): ?>
										<span class="help-inline"><?php echo $posicionamientoError;?></span>
									<?php endif; ?>
							</div>
							<div class="form-group ">
								<label for="argos_trans">Argos: protocolo de transmisi&oacute;n </label>
									<textarea class="form-control input-sm" id="argos_trans" name="argos_trans" rows="1" maxlength="200"
									data-patron="<?php echo PATRON_protoArgos?>" data-error="<?php echo PATRON_protoArgos_men?>"><?php echo !empty($argos_trans)?$argos_trans:'';?></textarea>
									<div class="help-block with-errors"></div>
									<p id="JSErrorargos_trans"></p>
									<?php if (!empty($argos_transError)): ?>
										<span class="help-inline"><?php echo $argos_transError;?></span>
									<?php endif; ?>
							</div>							
						</div>
						
						
						<div class="col-sm-6" >
						    <div class="row">
								<div class="col-sm-6" >
									<div class="form-group ">
										<label for="fin_viaje">Estado del viaje</label>
											<select class="form-control input-sm" style="width: auto" required id="fin_viaje" data-error="Seleccionar un elemento de la lista"   name="fin_viaje">
												<option value="" <?php if ($fin_viaje == "") {echo " selected";}?> ></option>
												<option value="VIAJANDO" <?php if ($fin_viaje == "VIAJANDO") {echo " selected";}?> >EN VIAJE</option>
												<option value="VIAJE COMPLETO" <?php if ($fin_viaje == "VIAJE COMPLETO") {echo " selected";}?> >VIAJE COMPLETO</option>
												<option value="VIAJE INCOMPLETO" <?php if ($fin_viaje == "VIAJE INCOMPLETO") {echo " selected";}?> >VIAJE INCOMPLETO</option>
											</select>
											<div class="help-block with-errors"></div>
											<p id="JSErrorfin_viaje"></p>
											<?php if (!empty($fin_viajeError)): ?>
												<span class="help-inline"><?php echo $fin_viajeError;?></span>
											<?php endif; ?>
									</div>

								</div>
								<div class="col-sm-6" >
									<div class="form-group ">
										<label for="estrategia">Estrategia&nbsp;</label><a href="publicaciones/estrategia_doc.pdf" target="_blank"><span class="glyphicon glyphicon-file"></span></a> 
											<select class="form-control input-sm" style="width: auto" id="estrategia" data-error="Seleccionar un elemento de la lista"   name="estrategia">
												<option value="" <?php if ($estrategia == "") {echo " selected";}?> ></option>
												<option value="NODET" <?php if ($estrategia == "NODET") {echo " selected";}?> >NODET</option>
												<option value="PLATAFORMA" <?php if ($estrategia == "PLATAFORMA") {echo " selected";}?> >PLATAFORMA</option>
												<option value="TALUD" <?php if ($estrategia == "TALUD") {echo " selected";}?> >TALUD</option>
												<option value="CUENCA" <?php if ($estrategia == "CUENCA") {echo " selected";}?> >CUENCA</option>
												<option value="VIAJERO" <?php if ($estrategia == "VIAJERO") {echo " selected";}?> >VIAJERO</option>
											</select>
											<div class="help-block with-errors"></div>
											<p id="JSErrorestrategia"></p>
											<?php if (!empty($estrategiaError)): ?>
												<span class="help-inline"><?php echo $estrategiaError;?></span>
											<?php endif; ?>
									</div>

								</div>	
							</div>										

							<div class="row filam">

								<div class="col-sm-12" >
									<div class="form-group ">
										<label for="comentario">Comentario</label>
											<textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
											<div class="help-block with-errors"></div>
											<p id="JSErrorcomentario"></p>
											<?php if (!empty($comentarioError)): ?>
												<span class="help-inline"><?php echo $comentarioError;?></span>
											<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					
					</div>



                    <div class="form-actions">
						<div class="row">
							<div class="col-sm-6">
					
								<?php if (empty($guardado)): ?>
								<button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<?php  endif; ?>

								<a class="btn btn-default btn-sm" href="viaje_editar.php<?php echo $paramV;?>")>volver a etapa</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a class="btn btn-default btn-sm" onclick=parent.cierroMiniSI("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>
							</div>


							<div class="col-sm-6">

								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h6><?php echo $eError;?></h6></span>
								  <?php endif;?>
								 <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
									<span class="alert alert-success"><h5>registro agregado</h5></span>
								  <?php endif;?>
							</div>
						</div>


                    </div>
                </form>
<script>
$('form[data-toggle="validator"]').validator({
    custom: {
		pentero: function($el) {
			var r = validatorEntero ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		},
		patron: function($el) {
			var pat = new RegExp($el.data("patron"));
			var le = $el.val().length;
			if (le>0){
			   var res =pat.test($el.val());
			   if (!res) {
				  return $el.data("error")
				 }
				 else{
				   var lm=$el.val().match(pat)[0].length;
				   if (le>lm){
					 return $el.data("error")
				   }
				 }
		   }
		}		
   }
});
</script>	

            </div>
        </div>
    </div> <!-- /container -->
<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniInstrumentos.html"></div>

<script>
w3.includeHTML();
</script>
	
</body>
</html>