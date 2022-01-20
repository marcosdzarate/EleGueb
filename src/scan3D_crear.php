<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'scan3d';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $hora = null;
        $clima = null;
        $tipoPlaya = null;
        $escaneo3D = null;
        $IDarchivo = null;
        $video = null;
        $distancia = null;
		$modelo = null;
		$modeloVolumen = null;
		$modeloPeso = null;
		$modeloCategoria = null;
		$modeloNota = null;
        $comentario = null;

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

    $fecha = null;
    if (isset($_GET["fecha"])) {
        $fecha=$_GET["fecha"];
        $m = validar_fecha ($fecha,$v,true);
    }
    else{
        if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    
    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $horaError = null;
        $climaError = null;
        $tipoPlayaError = null;
        $escaneo3DError = null;
        $IDarchivoError = null;
        $videoError = null;
        $distanciaError = null;
		$modeloError = null;
		$modeloVolumenError = null;
		$modeloPesoError = null;
		$modeloCategoriaError = null;
		$modeloNotaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $hora = limpia($_POST['hora']);
        $clima = limpia($_POST['clima']);
        $tipoPlaya = limpia($_POST['tipoPlaya']);
        $escaneo3D = limpia($_POST['escaneo3D']);
        $IDarchivo = limpia($_POST['IDarchivo']);
        $video = limpia($_POST['video']);
        $distancia = limpia($_POST['distancia']);
        $modelo = limpia($_POST['modelo']);
        $modeloVolumen = limpia($_POST['modeloVolumen']);
        $modeloPeso = limpia($_POST['modeloPeso']);
        $modeloCategoria = limpia($_POST['modeloCategoria']);
        $modeloNota = limpia($_POST['modeloNota']);
        $comentario = limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $horaError = validar_hora ($hora,$valid,false);
        $tipoPlayaError = validar_tipoPlaya ($tipoPlaya,$valid,false);
        $escaneo3DError = validar_escaneo3D ($escaneo3D,$valid,true);
        $videoError = validar_video ($video,$valid,true);
        $modeloError = validar_modelo3D ($modelo,$valid,false);
        $modeloVolumenError = validar_modeloVolumen ($modeloVolumen,$valid,false);
        $modeloPesoError = validar_peso ($modeloPeso,$valid,false);
        $distancia = validar_distanciaVideo ($distancia,$valid,false);
        
    // validacion entre campos
        $eError = validarForm_scan3d ($claveU,$fecha,$hora,$clima,$tipoPlaya,$escaneo3D,$IDarchivo,$video,$distancia,
		$modelo,$modeloVolumen,$modeloPeso,$modeloCategoria,$modeloNota,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLhora = ($hora == '' ? NULL : $hora);
            $SQLclima = ($clima == '' ? NULL : $clima);
            $SQLtipoPlaya = ($tipoPlaya == '' ? NULL : $tipoPlaya);
            $SQLIDarchivo = ($IDarchivo == '' ? NULL : $IDarchivo);
            $SQLdistancia = ($distancia == '' ? NULL : $distancia);
            $SQLmodelo = ($modelo == '' ? NULL : $modelo);
            $SQLmodeloVolumen = ($modeloVolumen == '' ? NULL : $modeloVolumen);
            $SQLmodeloPeso = ($modeloPeso == '' ? NULL : $modeloPeso);
            $SQLmodeloCategoria = ($modeloCategoria == '' ? NULL : $modeloCategoria);
            $SQLmodeloNota = ($modeloNota == '' ? NULL : $modeloNota);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO scan3d (claveU,fecha,hora,clima,tipoPlaya,escaneo3D,IDarchivo,video,distancia,modelo,modeloVolumen,modeloPeso,modeloCategoria,modeloNota,comentario) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$SQLhora,$SQLclima,$SQLtipoPlaya,$escaneo3D,$SQLIDarchivo,$video,$SQLdistancia,$SQLmodelo,$SQLmodeloVolumen,$SQLmodeloPeso,$SQLmodeloCategoria,$SQLmodeloNota,$SQLcomentario));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                $guardado='ok';

            }



        }
    }

     $param0 = "?claveU=".$claveU;


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

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		


</head>

<body>
    <div class="container" style="width:97%">
	
	
<button type="button" class="botonayuda" style="top:20px;left:-10px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


	

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de escaneo3D</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmedidas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group ">
                                <label for="claveU">ClaveU</label>
                                    <input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>" value="<?php echo !empty($claveU)?$claveU:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorclaveU"></p>
                                    <?php if (!empty($claveUError)): ?>
                                        <span class="help-inline"><?php echo $claveUError;?></span>
                                    <?php endif; ?>
                            </div>

                        </div>
                        
                        <div class="col-sm-2">
                            <div class="form-group ">
                                <label for="fecha">Fecha</label>
                                    <input type="text" class="form-control input-sm" style="width:90px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorfecha"></p>
                                    <?php if (!empty($fechaError)): ?> 
                                        <span class="help-inline"><?php echo $fechaError;?></span>
                                    <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-2">

							<div class="form-group ">
								<label for="hora">Hora</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="hora" name="hora" placeholder="HH:mm:ss"  pattern="<?php echo PATRON_hora;?>" maxlength="8" required  data-error="<?php echo PATRON_hora_men;?>" value="<?php echo !empty($hora)?$hora:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorhora"></p>
									<?php if (!empty($horaError)): ?>
										<span class="help-inline"><?php echo $horaError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                        
                        <div class="col-sm-3">
							<div class="form-group ">
								<label for="clima">Clima</label>
									<textarea class="form-control input-sm" id="clima" name="clima" rows="1" maxlength="100" ><?php echo !empty($clima)?$clima:'';?></textarea>
									<div class="help-block with-errors"></div>
									<p id="JSErrorclima"></p>
									<?php if (!empty($climaError)): ?>
										<span class="help-inline"><?php echo $climaError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                        
                        <div class="col-sm-3">
							<div class="form-group ">
								<label for="tipoPlaya">Sustrato</label>
									<select class="form-control input-sm" style="width: auto" id="tipoPlaya" data-error="Seleccionar un elemento de la lista"  name="tipoPlaya">
										<option value="" <?php if ($tipoPlaya == "") {echo " selected";}?> ></option>
										<option value="AGUA" <?php if ($tipoPlaya == "AGUA") {echo " selected";}?> >AGUA</option>
										<option value="ARENA" <?php if ($tipoPlaya == "ARENA") {echo " selected";}?> >ARENA</option>
										<option value="CANTO RODADO" <?php if ($tipoPlaya == "CANTO RODADO") {echo " selected";}?> >CANTO RODADO</option>
										<option value="MEZCLA" <?php if ($tipoPlaya == "MEZCLA") {echo " selected";}?> >MEZCLA</option>
										<option value="RESTINGA" <?php if ($tipoPlaya == "RESTINGA") {echo " selected";}?> >RESTINGA</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrortipoPlaya"></p>
									<?php if (!empty($tipoPlayaError)): ?>
										<span class="help-inline"><?php echo $tipoPlayaError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                    </div>

					<div class="row">                        
                        <div class="col-sm-3">
							<div class="form-group ">
								<label for="escaneo3D">escaneo3D</label>
									<select class="form-control input-sm" style="width: auto" id="escaneo3D" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="escaneo3D">
										<option value="" <?php if ($escaneo3D == "") {echo " selected";}?> ></option>
										<option value="NO SE HIZO" <?php if ($escaneo3D == "NO SE HIZO") {echo " selected";}?> >NO SE HIZO</option>
										<option value="INCOMPLETO" <?php if ($escaneo3D == "INCOMPLETO") {echo " selected";}?> >INCOMPLETO</option>
										<option value="COMPLETO" <?php if ($escaneo3D == "COMPLETO") {echo " selected";}?> >COMPLETO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorescaneo3D"></p>
									<?php if (!empty($escaneo3DError)): ?>
										<span class="help-inline"><?php echo $escaneo3DError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                        <div class="col-sm-6">						
                            <div class="form-group ">
								<label for="IDarchivo">IDarchivo</label>
									<input type="text" class="form-control input-sm" id="IDarchivo" name="IDarchivo"  oninput="mayus(this);" 
									pattern="<?php echo PATRON_archiScan3D;?>" maxlength="40" data-error="<?php echo PATRON_archiScan3D_men;?>"
									value="<?php echo !empty($IDarchivo)?$IDarchivo:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorIDarchivo"></p>
									<?php if (!empty($IDarchivoError)): ?>
										<span class="help-inline"><?php echo $IDarchivoError;?></span>
									<?php endif; ?>
							</div>						
                        </div>						
                    </div>						
					
					<div class="row">                        
                        <div class="col-sm-3">
							<div class="form-group ">
								<label for="video">Video</label>
									<select class="form-control input-sm" style="width: auto" id="video" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="video">
										<option value="" <?php if ($video == "") {echo " selected";}?> ></option>
										<option value="NO SE HIZO" <?php if ($video == "NO SE HIZO") {echo " selected";}?> >NO SE HIZO</option>
										<option value="INCOMPLETO" <?php if ($video == "INCOMPLETO") {echo " selected";}?> >INCOMPLETO</option>
										<option value="COMPLETO" <?php if ($video == "COMPLETO") {echo " selected";}?> >COMPLETO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorvideo"></p>
									<?php if (!empty($videoError)): ?>
										<span class="help-inline"><?php echo $videoError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                        <div class="col-sm-3">
							<div class="form-group ">
								<label for="distancia">Distancia (m)</label>
                                    <input type="text" class="form-control input-sm" style="width:105px" id="distancia" name="distancia" onblur="tdecim(this, 1);" data-pdecimal 
									data-dmin="<?php echo CONST_distanciaVideo_min?>" data-dmax="<?php echo CONST_distanciaVideo_max?>" 
									data-error="<?php echo CONST_distanciaVideo_men?>" value="<?php echo !empty($distancia)?$distancia:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErroredistancia"></p>
                                    <?php if (!empty($distanciaError)): ?>
                                        <span class="help-inline"><?php echo $distanciaError;?></span>
                                    <?php endif; ?>
							</div>
                        </div>
                        <div class="col-sm-6">
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
				<div class="well well-sm" style="padding:4px">
					<label > ------------ M O D E L O      3D ------------ </label>
                    <div class="row">

                        <div class="col-sm-2">
							<div class="form-group ">
								<label for="modelo">Result&oacute;</label>
									<select class="form-control input-sm" style="width:80px" id="modelo" data-error="Seleccionar un elemento de la lista" name="modelo">
										<option value="" <?php if ($modelo == "") {echo " selected";}?> ></option>
										<option value="MALO" <?php if ($modelo == "MALO") {echo " selected";}?> >MALO</option>
										<option value="REGULAR" <?php if ($modelo == "REGULAR") {echo " selected";}?> >REGULAR</option>
										<option value="BUENO" <?php if ($modelo == "BUENO") {echo " selected";}?> >BUENO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrormodelo"></p>
									<?php if (!empty($modeloError)): ?>
										<span class="help-inline"><?php echo $modeloError;?></span>
									<?php endif; ?>
							</div>
                        </div>
                        
                        <div class="col-sm-2">
							<div class="form-group">
								<label for="modeloVolumen">Volumen (m3)</label>
									<input type="text" class="form-control input-sm" style="width:80px" id="modeloVolumen" name="modeloVolumen" onblur="tdecim(this, 2);" data-pdecimal 
									data-dmin="<?php echo CONST_modeloVolumen_min?>" data-dmax="<?php echo CONST_modeloVolumen_max?>" 
									data-error="<?php echo CONST_modeloVolumen_men?>" value="<?php echo !empty($modeloVolumen)?$modeloVolumen:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrormodeloVolumen"></p>
									<?php if (!empty($modeloVolumenError)): ?>
										<span class="help-inline"><?php echo $modeloVolumenError;?></span>
									<?php endif; ?>                        
							</div>
                        </div>
                        <div class="col-sm-2">
							<div class="form-group ">
								<label for="modeloPeso">Peso (kg)</label>
									<input type="text" class="form-control input-sm" style="width:80px" id="modeloPeso" name="modeloPeso" onblur="tdecim(this, 2);" data-pdecimal 
									data-dmin="<?php echo CONST_peso_min?>" data-dmax="<?php echo CONST_peso_max?>" 
									data-error="<?php echo CONST_peso_men?>" value="<?php echo !empty($modeloPeso)?$modeloPeso:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrormodeloPeso"></p>
									<?php if (!empty($modeloPesoError)): ?>
										<span class="help-inline"><?php echo $modeloPesoError;?></span>
									<?php endif; ?>                        
							</div>
                        </div>		
                        <div class="col-sm-6">						
                            <div class="form-group ">
								<label for="modeloCategoria">Categor&iacute;a</label>
									<input type="text" class="form-control input-sm" id="modeloCategoria" name="modeloCategoria"   maxlength="40" value="<?php echo !empty($modeloCategoria)?$modeloCategoria:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrormodeloCategoria"></p>
									<?php if (!empty($modeloCategoriaError)): ?>
										<span class="help-inline"><?php echo $modeloCategoriaError;?></span>
									<?php endif; ?>
							</div>						
                        </div>						
                    </div>						
					
                    <div class="row">
						
                        <div class="col-sm-2">
                                <label for="modeloNota">Anotación</label>
                        </div>						
                        <div class="col-sm-10">
                            <div class="form-group ">
                                    <textarea class="form-control input-sm" id="modeloNota" name="modeloNota" rows="1" maxlength="500"      ><?php echo !empty($modeloNota)?$modeloNota:'';?></textarea>
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrormodeloNota"></p>
                                    <?php if (!empty($modeloNotaError)): ?>
                                        <span class="help-inline"><?php echo $modeloNotaError;?></span>
                                    <?php endif; ?>
                            </div>						
                        </div>						
						
                    </div>
                </div>
                        



                    <br>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if (empty($guardado)): ?>                                     
                                    <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-3">
									<a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>
                            </div>
                            <div class="col-sm-6">
                                  <?php if (!empty($eError)): ?>
                                    <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                                  <?php endif;?>
                                 <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                                    <span class="alert alert-success"><h5>registro agregado</h5></span>
                                  <?php endif;?>
                            </div>
                        </div>


                    </div>					
					
					
                </form>


            </div>
        </div>
    </div> <!-- /container -->

<script>
$('form[data-toggle="validator"]').validator({
    custom: {
		pdecimal: function($el) {
			var r = validatorDecimal ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		}
   }
});
</script>
</body>
</html>