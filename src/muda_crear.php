<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'muda';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $porcentaje = null;
        $estado = null;
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
        $porcentajeError = null;
        $estadoError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $porcentaje = limpia($_POST['porcentaje']);
        $estado = limpia($_POST['estado']);
        $comentario = limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $porcentajeError = validar_porcentaje ($porcentaje,$valid,false);
        $estadoError = validar_estado ($estado,$valid,false);

    // validacion entre campos
        $eError = validarForm_muda ($claveU,$fecha,$porcentaje,$estado,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLporcentaje = ($porcentaje == '' ? NULL : $porcentaje);
            $SQLestado = ($estado == '' ? NULL : $estado);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO muda (claveU,fecha,porcentaje,estado,comentario) values(?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$SQLporcentaje,$SQLestado,$SQLcomentario));

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
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de muda</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmuda" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

					<div class="row">
						<div class="col-sm-3">
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
					</div>

					<div class="row">
						<div class="col-sm-3">					
							<div class="form-group ">
								<label for="porcentaje">Porcentaje mudado</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="porcentaje" name="porcentaje"  
									data-dmin="<?php echo CONST_porcentaje_min?>" data-dmax="<?php echo CONST_porcentaje_max?>"  data-pentero 
									data-error="<?php echo CONST_porcentaje_men?>" value="<?php echo !empty($porcentaje)?$porcentaje:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorporcentaje"></p>
									<?php if (!empty($porcentajeError)): ?>
										<span class="help-inline"><?php echo $porcentajeError;?></span>
									<?php endif; ?>
							</div>

						</div>

						<div class="col-sm-3">
							
							<div class="form-group ">
								<label for="estado">Estado</label>
									<select class="form-control input-sm" style="width: auto" id="estado" data-error="Seleccionar un elemento de la lista"     data-error="Seleccionar un elemento de la lista" name="estado">
										<option value="" <?php if ($estado == "") {echo " selected";}?> ></option>
										<option value="EN GRUPO" <?php if ($estado == "EN GRUPO") {echo " selected";}?> >EN GRUPO</option>
										<option value="SOLO" <?php if ($estado == "SOLO") {echo " selected";}?> >SOLO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorestado"></p>
									<?php if (!empty($estadoError)): ?>
										<span class="help-inline"><?php echo $estadoError;?></span>
									<?php endif; ?>
							</div>
						</div>

					</div>
							
                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="2" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
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