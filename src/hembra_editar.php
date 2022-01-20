<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'hembra';

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

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);

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

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomportamientoMaternal = ($comportamientoMaternal == '' ? NULL : $comportamientoMaternal);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLfechaParto = ($fechaParto == '' ? NULL : $fechaParto);
            $SQLestimada = ($estimada == '' ? NULL : $estimada);
            $SQLedadPup = ($edadPup == '' ? NULL : $edadPup);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE hembra set claveU=?,fecha=?,estado=?,fechaParto=?,estimada=?,edadPup=?,comportamientoMaternal=?,comentario=? WHERE claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$estado,$SQLfechaParto,$SQLestimada,$SQLedadPup,$SQLcomportamientoMaternal,$SQLcomentario,$pk_claveU,$pk_fecha));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: hembra_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM hembra where claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_fecha));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);

                       $claveU = $data['claveU'];
                       $fecha = $data['fecha'];
                       $estado = $data['estado'];
                       $comportamientoMaternal = $data['comportamientoMaternal'];
                       $comentario = $data['comentario'];
                       $fechaParto = $data['fechaParto'];
                       $estimada = $data['estimada'];
                       $edadPup = $data['edadPup'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;

                       Database::disconnect();
                   }
                }
        }

     $param  = "?claveU=".$claveU."&fecha=".$fecha;
     $param0 = "?claveU=".$claveU;

	 /* si la hembra ya tiene fecha de parto en otro registro... */
	 $nParto=0;  /*cuantos EN PARTO*/
	 $nPup=0;  /*cuantos CON PUP*/
	 $mensa = tieneFechaParto($claveU,$fecha,$nParto,$nPup);
	 $nsuma = $nParto+$nPup;
	 /* tiene vinculo con pup? */
	 $conVinculoPup = tieneVinculoPup($claveU,$fecha);
	 
	 /* estado EN PARTO o CON PUP puede eliminar/modificar estado si no tiene vinculo con pup o tiene pero $nParto+$nPup>1 */
	 $puedeHacer = true;
	 $disa="";
	 if ($estado == 'EN PARTO' OR $estado == 'CON PUP') {
		 if ((!$conVinculoPup) OR ($conVinculoPup and ($nsuma>1))) {
			$puedeHacer = true;
			$disa="";
		 }
		 else {
			 $puedeHacer = false;
			 $disa=" disabled";
			 $mensa.="<br>Hay v&iacute;nculo con cr&iacute;a: no puede eliminar este registro y el cambio de estado est&aacute; limitado.";
		 }
	 }
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
function aCamposIni(vi,v){
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
			if(vi=='sta'){
				document.getElementById('fechaParto').value=document.getElementById('fecha').value;
				document.getElementById('estimada').value='NO';
				document.getElementById('edadPup').value=0;
			}
			document.getElementById('fechaParto').readOnly=true;
			document.getElementById('estimada').readOnly=true;
			document.getElementById("estimada").options[0].disabled = true;
			document.getElementById("estimada").options[1].disabled = true;
			document.getElementById('edadPup').readOnly=true;
		}
		else {
			if(vi=='sta'){
				document.getElementById('fechaParto').value="";
				document.getElementById('estimada').value="";
				document.getElementById('edadPup').value="";
			}
			if(v=="CON PUP"){
				document.getElementById('fechaParto').readOnly=false;
				document.getElementById('estimada').readOnly=false;
				document.getElementById("estimada").options[0].disabled = false;
				document.getElementById("estimada").options[1].disabled = false;
				document.getElementById('edadPup').readOnly=false;				
			}
		}
<?php if (!edita()) : ?>
	$('#CRhembra').find(':input').attr('disabled', 'disabled');
<?php endif ?>	
}
</script>	

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body onload="aCamposIni('ini',document.getElementById('estado').value)">

    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">registro de hembra</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRhembra" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

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


                            <input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
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


                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                    </div>
                  </div>

   				  <div class="col-sm-7">
					<?php if($mensa<>""): ?>
					<div class="well well-sm">
						<h5>
						  <span class="glyphicon glyphicon-alert text-warning "></span>&nbsp;<?php echo $mensa ?>
						</h5>
					</div>
					<?php endif; ?>

                  </div>

				  
                </div>

				<div class="row">
					<div class="col-sm-3">
						<div class="form-group ">
							<label for="estado">Estado de la hembra</label>
								<select class="form-control input-sm" style="width: auto" id="estado" data-error="Seleccionar un elemento de la lista"  required  onchange="aCamposIni('sta',this.value)"; name="estado">
									<option value="" <?php if ($estado == "") {echo " selected";}; echo $disa?> ></option>
									<option value="VISTA" <?php if ($estado == "VISTA") {echo " selected";}; echo $disa?> >VISTA</option>
									<option value="NO PRENADA" <?php if ($estado == "NO PRENADA") {echo " selected";}; echo $disa?> >NO PRE&Ntilde;ADA</option>
									<option value="PRENADA" <?php if ($estado == "PRENADA") {echo " selected";}; echo $disa?> >PRE&Ntilde;ADA</option>
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

					<div class="col-sm-9" id="conPup">
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


					

                    <div class="form-group" ID='comportamientoMaternalDIV'>
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

					<?php if (edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
					<?php endif ?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a><span style="display:inline-block; width: 150px;"></span>

					<?php if (edita()) : ?>
						<?php if ($puedeHacer) : ?>
							<a class="btn btn-danger btn-sm"  onclick=ventanaMini("hembra_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
						<?php else: ?>
							<button class="btn btn-danger btn-sm" disabled>eliminar</button><span style="display:inline-block; width: 20px;"></span>
						<?php endif ?>
					<?php endif ?>
                        

                        
                        
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>cambios guardados</h5></span>
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

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>



  </body>
</html>