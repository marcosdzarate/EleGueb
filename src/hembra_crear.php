<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'hembra';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $estado = null;
        $comportamientoMaternal = null;
        $comentario = null;
        $fechaParto = null;
        $estimada = null;
        $edadPup = null;

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
        $estadoError = null;
        $comportamientoMaternalError = null;
        $comentarioError = null;
		$fechaPartoError = null;
		$estimadaError = null;
		$edadPupError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $estado = limpia($_POST['estado']);
        $comportamientoMaternal = limpia($_POST['comportamientoMaternal']);
        $comentario = limpia($_POST['comentario']);
        $fechaParto = limpia($_POST['fechaParto']);
        $estimada = limpia($_POST['estimada']);
        $edadPup = limpia($_POST['edadPup']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $estadoError = validar_estado ($estado,$valid,true);
        $fechaPartoError = validar_fechaPartoDeste ($fecha,$fechaParto,$valid,false);
        $estimadaError = validar_estimada ($estimada,$valid,false);
        $edadPupError = validar_edadPupDeste ($edadPup,$valid,false);

    // validacion entre campos
        $eError = validarForm_hembra ($claveU,$fecha,$estado,$fechaParto,$estimada,$edadPup,$comportamientoMaternal,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomportamientoMaternal = ($comportamientoMaternal == '' ? NULL : $comportamientoMaternal);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLfechaParto = ($fechaParto == '' ? NULL : $fechaParto);
            $SQLestimada = ($estimada == '' ? NULL : $estimada);
            $SQLedadPup = ($edadPup == '' ? NULL : $edadPup);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO hembra (claveU,fecha,estado,fechaParto,estimada,edadPup,comportamientoMaternal,comentario) values(?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$estado,$SQLfechaParto,$SQLestimada,$SQLedadPup,$SQLcomportamientoMaternal,$SQLcomentario));

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

	 /* si la hembra ya tiene fecha de parto en otro registro... */
	 $nParto=0;  /*cuantos EN PARTO*/
	 $nPup=0;  /*cuantos CON PUP*/
	 $conFecParto = tieneFechaParto($claveU,$fecha,$nParto,$nPup);
   
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
function aCampos(v){
	var c= document.getElementById('comportamientoMaternalDIV');
	var p= document.getElementById('conPup');
	if (v=='CON PUP' || v=='EN PARTO'){
		c.style.display = "block";
		p.style.display = "block";
	}else{
		c.style.display = "none";
		p.style.display = "none";
	}
		if (v=="EN PARTO") {
			<?php if (empty($_POST)):?>
			document.getElementById('fechaParto').value=document.getElementById('fecha').value;
			document.getElementById('estimada').value='NO';
			document.getElementById('edadPup').value=0;
			<?php endif; ?>
			document.getElementById('fechaParto').readOnly=true;
			document.getElementById('estimada').readOnly=true;
			document.getElementById("estimada").options[0].disabled = true;
			document.getElementById("estimada").options[1].disabled = true;
			document.getElementById('edadPup').readOnly=true;
		}
		else {
			<?php if (empty($_POST)):?>
			document.getElementById('fechaParto').value="";
			document.getElementById('estimada').value="";
			document.getElementById('edadPup').value="";
			<?php endif; ?>
			if(v=="CON PUP"){
				document.getElementById('fechaParto').readOnly=false;
				document.getElementById('estimada').readOnly=false;
				document.getElementById("estimada").options[0].disabled = false;
				document.getElementById("estimada").options[1].disabled = false;
				document.getElementById('edadPup').readOnly=false;				
			}
		}
	
}
</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body onload=aCampos(document.getElementById('estado').value) >

    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de hembra</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRhembra" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

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
					<?php if($conFecParto<>""): ?>
					<div class="well well-sm">
						<h5>
						  <span class="glyphicon glyphicon-alert text-warning "></span>&nbsp;<?php echo $conFecParto ?>
						</h5>
					</div>
					<?php endif; ?>

                  </div>

			  </div>


				<div class="row">
					<div class="col-sm-3">
						<div class="form-group ">
							<label for="estado">Estado de la hembra</label>
								<select class="form-control input-sm" style="width: auto" id="estado" data-error="Seleccionar un elemento de la lista"   required  onchange="aCampos(this.value)"; name="estado">
									<option value="" <?php if ($estado == "") {echo " selected";}?> ></option>
									<option value="VISTA" <?php if ($estado == "VISTA") {echo " selected";}?> >VISTA</option>
									<option value="NO PRENADA" <?php if ($estado == "NO PRENADA") {echo " selected";}?> >NO PRE&Ntilde;ADA</option>
									<option value="PRENADA" <?php if ($estado == "PRENADA") {echo " selected";}?> >PRE&Ntilde;ADA</option>
									<option value="EN PARTO" <?php if ($estado == "EN PARTO") {echo " selected";}?> >EN PARTO</option>
									<option value="CON PUP" <?php if ($estado == "CON PUP") {echo " selected";}?> >CON PUP</option>
								</select>
								<div class="help-block with-errors"></div>
								<p id="JSErrorestado"></p>
								<?php if (!empty($estadoError)): ?>
									<span class="help-inline"><?php echo $estadoError;?></span>
								<?php endif; ?>
						</div>
					</div>


					<div class="col-sm-9" id="conPup" display="none">
						<div class="col-sm-4">
							<div class="form-group ">
								<label for="fechaParto">Fecha de parto</label>
									<input type="text" class="form-control input-sm" style="width:150px" id="fechaParto" name="fechaParto" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fechaParto)?$fechaParto:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfechaParto"></p>
									<?php if (!empty($fechaPartoError)): ?>
										<span class="help-inline"><?php echo $fechaPartoError;?></span>
									<?php endif; ?>
							</div>
						</div>
						
						<div class="col-sm-4">
							<div class="form-group ">
								<label for="estimada">estimada?</label>
									<select class="form-control input-sm" style="width: auto" id="estimada" data-error="Seleccionar un elemento de la lista" name="estimada">
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
								<label for="edadPup">Edad del pup (d&iacute;as)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="edadPup" name="edadPup" data-pentero 
									data-dmin="<?php echo CONST_edadPupDeste_min?>" data-dmax="<?php echo CONST_edadPupDeste_max?>"  
									data-error="<?php echo CONST_edadPupDeste_men?>" value="<?php echo !empty($edadPup)?$edadPup:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErroredadPup"></p>
									<?php if (!empty($edadPupError)): ?>
										<span class="help-inline"><?php echo $edadPupError;?></span>
									<?php endif; ?>
							</div>
						</div>
						
					</div>
				  
				</div>

				
				
				
				
                    <div class="form-group" id='comportamientoMaternalDIV'>
                        <label for="comportamientoMaternal">Comportamiento maternal</label>
                            <textarea class="form-control input-sm" id="comportamientoMaternal" name="comportamientoMaternal" rows="2" maxlength="500"      ><?php echo !empty($comportamientoMaternal)?$comportamientoMaternal:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomportamientoMaternal"></p>
                            <?php if (!empty($comportamientoMaternalError)): ?>
                                <span class="help-inline"><?php echo $comportamientoMaternalError;?></span>
                            <?php endif; ?>
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