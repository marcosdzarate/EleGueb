<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'criadestetado';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $fechaDestete = null;
        $edadDestete = null;
        $estimada = null;
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
        $fechaDesteteError = null;
        $edadDesteteError = null;
        $estimadaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $fechaDestete = limpia($_POST['fechaDestete']);
        $edadDestete = limpia($_POST['edadDestete']);
        $estimada = limpia($_POST['estimada']);
        $comentario = limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $fechaDesteteError = validar_fechaPartoDeste ($fecha,$fechaDestete,$valid,true);
        $estimadaError = validar_estimada ($estimada,$valid,true);
		$edadDesteteError = validar_edadPupDeste ($edadDestete,$valid,false);

    // validacion entre campos
        $eError = validarForm_criadestetado ($claveU,$fecha,$fechaDestete,$estimada,$edadDestete,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLedadDestete = ($edadDestete == '' ? NULL : $edadDestete);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO criadestetado (claveU,fecha,fechaDestete,estimada,edadDestete,comentario) values(?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$fechaDestete,$estimada,$SQLedadDestete,$SQLcomentario));

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
	 $conDestete = tieneDestete($claveU);

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
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de destete</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRcriadestetado" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

				<div class="row">
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="claveU">ClaveU</label>
                            <input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"
may&uacute;sculas" value="<?php echo !empty($claveU)?$claveU:'';?>" >
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
   				  <div class="col-sm-6">
					<?php if($conDestete<>"" and $guardado==""): ?>
					<div class="well well-sm">
						<h5>
						  <span class="glyphicon glyphicon-alert text-warning "></span>&nbsp;<?php echo $conDestete ?>
						</h5>
					</div>
					<?php endif; ?>

                  </div>				  
				  
                </div>

				<div class="row">
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="fechaDestete">Fecha del destete</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fechaDestete" name="fechaDestete" placeholder="AAAA-MM-DD" required pattern="<?php echo PATRON_fecha;?>" maxlength="10" data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fechaDestete)?$fechaDestete:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfechaDestete"></p>
                            <?php if (!empty($fechaDesteteError)): ?>
                                <span class="help-inline"><?php echo $fechaDesteteError;?></span>
                            <?php endif; ?>
                    </div>

				  </div>
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="estimada">Es estimada?</label>
                            <select class="form-control input-sm" style="width: auto" id="estimada" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="estimada">
                                <option value="" <?php if ($estimada == "") {echo " selected";}?> ></option>
                                <option value="SI" <?php if ($estimada == "SI") {echo " selected";}?> >SI</option>
                                <option value="NO" <?php if ($estimada == "NO") {echo " selected";}?> >NO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorestimada"></p>
                            <?php if (!empty($estimadaError)): ?>
                                <span class="help-inline"><?php echo $estimadaError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>
						<div class="col-sm-4">
							<div class="form-group ">
								<label for="edadDestete">Edad del destete (d&iacute;as)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="edadDestete" name="edadDestete" data-pentero 
									data-dmin="<?php echo CONST_edadPupDeste_min?>" data-dmax="<?php echo CONST_edadPupDeste_max?>"  
									data-error="<?php echo CONST_edadPupDeste_men?>" value="<?php echo !empty($edadDestete)?$edadDestete:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErroredadDestete"></p>
									<?php if (!empty($edadDesteteError)): ?>
										<span class="help-inline"><?php echo $edadDesteteError;?></span>
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
		pentero: function($el) {
			var r = validatorEntero ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		}
   }
});
</script>	
	
	
</body>
</html>