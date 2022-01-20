<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'scan3d';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_claveU = null;
    if (isset($_GET["claveU"])) {
        $pk_claveU=$_GET["claveU"];
        $m = validar_claveU ($pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["pk_claveU"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_fecha = null;
    if (isset($_GET["fecha"])) {
        $pk_fecha=$_GET["fecha"];
        $m = validar_fecha ($pk_fecha,$v,true);
    }
    else{
        if (!isset($_GET["fecha"]) and !isset($_POST["pk_fecha"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

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
		$modeloNotaError = null;
		$modeloCategoriaError = null;
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
        $modeloNota = limpia($_POST['modeloNota']);
        $modeloCategoria = limpia($_POST['modeloCategoria']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);
		
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

        // actualizar los datos
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
            $SQLmodeloNota = ($modeloNota == '' ? NULL : $modeloNota);
            $SQLmodeloCategoria = ($modeloCategoria == '' ? NULL : $modeloCategoria);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE scan3d set claveU=?,fecha=?,hora=?,clima=?,tipoPlaya=?,escaneo3D=?,IDarchivo=?,video=?,distancia=?,modelo=?,modeloVolumen=?,modeloPeso=?,modeloCategoria=?,modeloNota=?,comentario=? WHERE claveU=? AND fecha=? ";
			$q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$SQLhora,$SQLclima,$SQLtipoPlaya,$escaneo3D,$SQLIDarchivo,$video,$SQLdistancia,$SQLmodelo,$SQLmodeloVolumen,$SQLmodeloPeso,$SQLmodeloCategoria,$SQLmodeloNota,$SQLcomentario,$pk_claveU,$pk_fecha));
			
            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM scan3d where claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_fecha));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                      }
                   else{
						$data = $q->fetch(PDO::FETCH_ASSOC);

						$claveU = $data['claveU'];
						$fecha = $data['fecha'];			
						$hora = $data['hora'];
						$clima = $data['clima'];
						$tipoPlaya = $data['tipoPlaya'];
						$escaneo3D = $data['escaneo3D'];
						$IDarchivo = $data['IDarchivo'];
						$video = $data['video'];
						$distancia = $data['distancia'];
						$modelo = $data['modelo'];
						$modeloVolumen = $data['modeloVolumen'];
						$modeloPeso = $data['modeloPeso'];
						$modeloCategoria = $data['modeloCategoria'];
						$modeloNota = $data['modeloNota'];
						$comentario = $data['comentario'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;

                       Database::disconnect();
                   }
                }
        }

     $param  = "?claveU=".$claveU."&fecha=".$fecha;
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
                <h3 class="panel-title">registro de escaneo3D</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRescaneos" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group ">
                                <label for="claveU">ClaveU</label>
                                    <input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorclaveU"></p>
                                    <?php if (!empty($claveUError)): ?>
                                        <span class="help-inline"><?php echo $claveUError;?></span>
                                    <?php endif; ?>

									<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
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

									<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->

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
							<?php if (edita()) : ?>
								<div class="col-sm-2">
									<button type="submit" id="guardar" name="guardar" class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
								</div>
							<?php endif ?>							
                            <div class="col-sm-3">

								<a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a><span style="display:inline-block; width: 150px;"></span>
							</div>

                            <div class="col-sm-2">

							<?php if (edita()) : ?>
								<a class="btn btn-danger btn-sm"  onclick=ventanaMini("scan3D_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
							<?php endif ?>							
							</div>
								

								
                            <div class="col-sm-5">								
								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
								  <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
									   <span class="alert alert-success"><h5>cambios guardados</h5></span>
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

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>

</body>
</html>