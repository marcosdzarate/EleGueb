<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'anestesia';

        /* sin permiso de edición, fuera*/
        siErrorFuera(edita());

        /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $hora = null;
        $tipo = null;
        $aplicacion = null;
        $droga = null;
        $droga_ml = null;
        $partida = null;
        $anotacion = null;

    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["claveU"]) ){
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
        if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) ){
			$v=false;
        }
	}
    siErrorFuera($v);



    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $horaError = null;
        $tipoError = null;
        $aplicacionError = null;
        $drogaError = null;
        $droga_mlError = null;
        $partidaError = null;
        $anotacionError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $hora = limpia($_POST['hora']);
        $tipo = limpia($_POST['tipo']);
        $aplicacion = limpia($_POST['aplicacion']);
        $droga = limpia($_POST['droga']);
        $droga_ml = limpia($_POST['droga_ml']);
        $partida = limpia($_POST['partida']);
        $anotacion = limpia($_POST['anotacion']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $horaError = validar_hora ($hora,$valid,true);
        $tipoError = validar_tipo ($tipo,$valid,true);
        $aplicacionError = validar_aplicacion ($aplicacion,$valid,true);
        $drogaError = validar_droga ($droga,$valid,true);
        $droga_mlError = validar_droga_ml ($droga_ml,$valid,false);
        $partidaError = validar_partida ($partida,$valid,false);

    // validacion entre campos
        $eError = validarForm_anestesia ($claveU,$fecha,$hora,$tipo,$aplicacion,$droga,$droga_ml,$partida,$anotacion,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLdroga_ml = ($droga_ml == '' ? NULL : $droga_ml);
            $SQLpartida = ($partida == '' ? NULL : $partida);
            $SQLanotacion = ($anotacion == '' ? NULL : $anotacion);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO anestesia (claveU,fecha,hora,tipo,aplicacion,droga,droga_ml,partida,anotacion) values(?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$hora,$tipo,$aplicacion,$droga,$SQLdroga_ml,$SQLpartida,$SQLanotacion));

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
	    $param0="?claveU=".$claveU."&fecha=".$fecha;

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

<body onload="setAplicacionDrogaPartida(document.getElementById('tipo'));">
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de anestesia</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRanestesia" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

				<div class="row">
  				  <div class="col-sm-3">
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
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="fecha">Fecha</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>
                            <?php if (!empty($fechaError)): ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="hora">Hora</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="hora" name="hora" placeholder="HH:mm:ss"  pattern="<?php echo PATRON_hora;?>" maxlength="8" required  data-error="<?php echo PATRON_hora_men;?>" value="<?php echo !empty($hora)?$hora:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorhora"></p>
                            <?php if (!empty($horaError)): ?>
                                <span class="help-inline"><?php echo $horaError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="tipo">Etapa</label>
                            <select class="form-control input-sm" style="width: auto" id="tipo" data-error="Seleccionar un elemento de la lista" oninput="setAplicacionDrogaPartida(this)"  required  data-error="Seleccionar un elemento de la lista" name="tipo">
                                <option value="" <?php if ($tipo == "") {echo " selected";}?> ></option>
                                <option value="INICIAL" <?php if ($tipo == "INICIAL") {echo " selected";}?> >INICIAL</option>
                                <option value="SIGUIENTE" <?php if ($tipo == "SIGUIENTE") {echo " selected";}?> >SIGUIENTE</option>
                                <option value="COMENTA" <?php if ($tipo == "COMENTA") {echo " selected";}?> >COMENTA</option>
                                <option value="INDUCCION" <?php if ($tipo == "INDUCCION") {echo " selected";}?> >INDUCCION</option>
                                <option value="FIN" <?php if ($tipo == "FIN") {echo " selected";}?> >FIN</option>
                                <option value="FIN1" <?php if ($tipo == "FIN1") {echo " selected";}?> >FIN1</option>
                                <option value="FIN2" <?php if ($tipo == "FIN2") {echo " selected";}?> >FIN2</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipo"></p>
                            <?php if (!empty($tipoError)): ?>
                                <span class="help-inline"><?php echo $tipoError;?></span>
                            <?php endif; ?>
                    </div>
				  </div>
			  </div>

				<div class="row">
  				  <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="aplicacion">Aplicaci&oacute;n</label>
                            <select class="form-control input-sm" style="width: auto" id="aplicacion" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="aplicacion">
                                <option value="" <?php if ($aplicacion == "") {echo " selected";}?> ></option>
                                <option value="no corresponde" <?php if ($aplicacion == "no corresponde") {echo " selected";}?> >no corresponde</option>
                                <option value="IM" <?php if ($aplicacion == "IM") {echo " selected";}?> >IM</option>
                                <option value="IV" <?php if ($aplicacion == "IV") {echo " selected";}?> >IV</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErroraplicacion"></p>
                            <?php if (!empty($aplicacionError)): ?>
                                <span class="help-inline"><?php echo $aplicacionError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
 			  </div>
				<div class="row">
  				  <div class="col-sm-4">
                    <div class="form-group ">
                        <label for="droga">Droga suministrada</label>
                            <select class="form-control input-sm" style="width: auto" id="droga" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="droga">
                                <option value="" <?php if ($droga == "") {echo " selected";}?> ></option>
                                <option value="no corresponde" <?php if ($droga == "no corresponde") {echo " selected";}?> >no corresponde</option>
                                <option value="TELAZOL" <?php if ($droga == "TELAZOL") {echo " selected";}?> >TELAZOL</option>
                                <option value="VIVIRANT" <?php if ($droga == "VIVIRANT") {echo " selected";}?> >VIVIRANT</option>
                                <option value="DOPRAM" <?php if ($droga == "DOPRAM") {echo " selected";}?> >DOPRAM</option>
                                <option value="KETAMINA" <?php if ($droga == "KETAMINA") {echo " selected";}?> >KETAMINA</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrordroga"></p>
                            <?php if (!empty($drogaError)): ?>
                                <span class="help-inline"><?php echo $drogaError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
  				  <div class="col-sm-4">
                    <div class="form-group ">
                        <label for="droga_ml">ml/cm3 suministrados</label>
                            <input type="text" class="form-control input-sm" style="width:105px" id="droga_ml" name="droga_ml" onblur="tdecim(this, 1);" data-pdecimal 
							data-dmin="<?php echo CONST_droga_ml_min?>" data-dmax="<?php echo CONST_droga_ml_max?>" 
							data-error="<?php echo CONST_droga_ml_men?>" value="<?php echo !empty($droga_ml)?$droga_ml:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrordroga_ml"></p>
                            <?php if (!empty($droga_mlError)): ?>
                                <span class="help-inline"><?php echo $droga_mlError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
  				  <div class="col-sm-4">
                    <div class="form-group ">
                        <label for="partida">Partida</label>
                            <input type="text" class="form-control input-sm" id="partida" name="partida"  oninput="mayus(this);" pattern="<?php echo PATRON_partida;?>" maxlength="45" data-error="<?php echo PATRON_partida_men;?>" value="<?php echo !empty($partida)?$partida:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorpartida"></p>
                            <?php if (!empty($partidaError)): ?>
                                <span class="help-inline"><?php echo $partidaError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
			  </div>

					<div class="form-group ">
                        <label for="anotacion">Anotaci&oacute;n</label>
                            <textarea class="form-control input-sm" id="anotacion" name="anotacion" rows="2" maxlength="200"      ><?php echo !empty($anotacion)?$anotacion:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErroranotacion"></p>
                            <?php if (!empty($anotacionError)): ?>
                                <span class="help-inline"><?php echo $anotacionError;?></span>
                            <?php endif; ?>
                    </div>

                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <a class="btn btn-default btn-sm" href="anestesia_index.php<?php echo $param0;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                         <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro agregado</h5></span>
                          <?php endif;?>


                    </div>
                </form>
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

            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>