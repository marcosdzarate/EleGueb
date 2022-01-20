<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'muestras';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $codigoADN = null;
        $sangre = null;
        $bigotes = null;
        $pelos = null;
        $fotogrametria = null;
        $distancia = null;
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
        $codigoADNError = null;
        $sangreError = null;
        $bigotesError = null;
        $pelosError = null;
        $fotogrametriaError = null;
        $distanciaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $codigoADN = limpia($_POST['codigoADN']);
        $sangre = limpia($_POST['sangre']);
        $bigotes = limpia($_POST['bigotes']);
        $pelos = limpia($_POST['pelos']);
        $fotogrametria = limpia($_POST['fotogrametria']);
        $distancia = limpia($_POST['distancia']);
        $comentario = limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $codigoADNError = validar_codigoADN ($codigoADN,$valid,false);
        $sangreError = validar_sangre ($sangre,$valid,true);
        $bigotesError = validar_bigotes ($bigotes,$valid,true);
        $pelosError = validar_pelos ($pelos,$valid,true);
        $fotogrametriaError = validar_fotogrametria ($fotogrametria,$valid,true);
        $distanciaError = validar_distanciaVideo ($distancia,$valid,false);

    // validacion entre campos
        $eError = validarForm_muestras ($claveU,$fecha,$codigoADN,$sangre,$bigotes,$pelos,$fotogrametria,$distancia,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL

            $SQLcodigoADN = ($codigoADN == '' ? NULL : $codigoADN);
            $SQLdistancia = ($distancia == '' ? NULL : $distancia);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO muestras (claveU,fecha,codigoADN,sangre,bigotes,pelos,fotogrametria,distancia,comentario) values(?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$SQLcodigoADN,$sangre,$bigotes,$pelos,$fotogrametria,$SQLdistancia,$SQLcomentario));

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
	else{
		$sangre = "NO";
        $bigotes = "NO";
        $pelos = "NO";
        $fotogrametria = "NO";
	}

     $param0 = "?claveU=".$claveU;

     $param  = "?claveU=".$claveU."&fecha=".$fecha;
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

<script>
function abrirVentana() {
window.open("fotos.php<?php echo $param?>","_blank","");
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de muestras</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmuestras" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

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
							</div>

					</div>
					<div class="col-sm-3">
							<div class="form-group ">
								<label for="fecha">Fecha</label>
									<input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha"></p>
									<?php if (!empty($fechaError)): ?>
										<span class="help-inline"><?php echo $fechaError;?></span>
									<?php endif; ?>
							</div>
					</div>
					
					<div class="col-sm-7">
						<?php if ($guardado=='ok'): ?>
						<a onclick="abrirVentana()" class="btn btn-info btn-lg"> 
                                <span class="glyphicon glyphicon-film"></span> fotos/videos (fotogrametr&iacute;a y m&aacute;s)</a>
						<?php endif ?>
					</div>
					
				</div>
				<div class="row">
					<div class="col-sm-6">

							<div class="form-group ">
								<label for="codigoADN">C&oacute;digo para ADN</label>
									<input type="text" class="form-control input-sm" id="codigoADN" name="codigoADN"  oninput="mayus(this);" pattern="<?php echo PATRON_codADN;?>" maxlength="30" data-error="<?php echo PATRON_codADN_men;?>"
									value="<?php echo !empty($codigoADN)?$codigoADN:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorcodigoADN"></p>
									<?php if (!empty($codigoADNError)): ?>
										<span class="help-inline"><?php echo $codigoADNError;?></span>
									<?php endif; ?>
							</div>
					</div>
				</div>

					<div class="row">
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="sangre">Sangre</label>
									<select class="form-control input-sm" style="width: auto" id="sangre" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="sangre">
										<option value="" <?php if ($sangre == "") {echo " selected";}?> ></option>
										<option value="SI" <?php if ($sangre == "SI") {echo " selected";}?> >SI</option>
										<option value="NO" <?php if ($sangre == "NO") {echo " selected";}?> >NO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorsangre"></p>
									<?php if (!empty($sangreError)): ?>
										<span class="help-inline"><?php echo $sangreError;?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-2">

							<div class="form-group ">
								<label for="bigotes">Bigotes</label>
									<select class="form-control input-sm" style="width: auto" id="bigotes" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="bigotes">
										<option value="" <?php if ($bigotes == "") {echo " selected";}?> ></option>
										<option value="SI" <?php if ($bigotes == "SI") {echo " selected";}?> >SI</option>
										<option value="NO" <?php if ($bigotes == "NO") {echo " selected";}?> >NO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorbigotes"></p>
									<?php if (!empty($bigotesError)): ?>
										<span class="help-inline"><?php echo $bigotesError;?></span>
									<?php endif; ?>
							</div>
						</div>
						<div class="col-sm-2">

							<div class="form-group ">
								<label for="pelos">Pelos</label>
									<select class="form-control input-sm" style="width: auto" id="pelos" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="pelos">
										<option value="" <?php if ($pelos == "") {echo " selected";}?> ></option>
										<option value="SI" <?php if ($pelos == "SI") {echo " selected";}?> >SI</option>
										<option value="NO" <?php if ($pelos == "NO") {echo " selected";}?> >NO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorpelos"></p>
									<?php if (!empty($pelosError)): ?>
										<span class="help-inline"><?php echo $pelosError;?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-2">

							<div class="form-group ">
								<label for="fotogrametria">Fotogrametr&iacute;a</label>
									<select class="form-control input-sm" style="width: auto" id="fotogrametria" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="fotogrametria">
										<option value="" <?php if ($fotogrametria == "") {echo " selected";}?> ></option>
										<option value="SI" <?php if ($fotogrametria == "SI") {echo " selected";}?> >SI</option>
										<option value="NO" <?php if ($fotogrametria == "NO") {echo " selected";}?> >NO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorfotogrametria"></p>
									<?php if (!empty($fotogrametriaError)): ?>
										<span class="help-inline"><?php echo $fotogrametriaError;?></span>
									<?php endif; ?>
							</div>
						</div>

                        <div class="col-sm-4">
							<div class="form-group ">
								<label for="distancia">Distancia fotogrametr&iacute;a (m)</label>
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
						
						
					</div>

                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>
                    </div>

                    <br>
                    <div class="form-actions">
                    <?php if (empty($guardado)): ?>                                     
                        <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endif; ?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                         <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro agregado</h5></span>
                          <?php endif;?>


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