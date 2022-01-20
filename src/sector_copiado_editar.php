<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    $par_elimina=1;

        $queTabla = 'sector_copiado';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */

	/*parametros inválidos, fuera*/		
	$v=true;
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

    $pk_libreta = null;	
	if (isset($_GET["libreta"])) {
		$pk_libreta=$_GET["libreta"];
		$m = validar_libreta_virtual ($pk_libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["pk_libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);



    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $libretaError = null;
        $zona_copiaError = null;
        $fecha_copiaError = null;
        $libreta_copiaError = null;
        $orden_desdeError = null;
        $orden_hastaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $zona_copia =  limpia($_POST['zona_copia']);
        $fecha_copia = limpia($_POST['fecha_copia']);
        $libreta_copia = limpia($_POST['libreta_copia']);
        $orden_desde = limpia($_POST['orden_desde']);
        $orden_hasta = limpia($_POST['orden_hasta']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_fecha=limpia($_POST['pk_fecha']);
        $pk_libreta=limpia($_POST['pk_libreta']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta_virtual ($libreta,$valid,true);
        $fecha_copiaError = validar_fecha ($fecha_copia,$valid,true);
        $libreta_copiaError = validar_libreta ($libreta_copia,$valid,true);
        $orden_desdeError = validar_orden ($orden_desde,$valid,true);
        $orden_hastaError = validar_orden ($orden_hasta,$valid,true);

        // validacion entre campos
        $eError = validarForm_sector_copiado ($fecha,$libreta,$zona_copia,$fecha_copia,$libreta_copia,$orden_desde,$orden_hasta,$comentario,$valid);

        // actualizar los datos
        if ($valid) {
    
		// campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE sector_copiado set fecha=?,libreta=?,zona_copia=?,fecha_copia=?,libreta_copia=?,orden_desde=?,orden_hasta=?,comentario=? WHERE fecha=? AND libreta=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$zona_copia,$fecha_copia,$libreta_copia,$orden_desde,$orden_hasta,$SQLcomentario,$pk_fecha,$pk_libreta));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySql: ".$arr[1]." ".$arr[2];
                }
            else {

                 }
            }
        }
        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM sector_copiado where fecha=? AND libreta=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_fecha,$pk_libreta));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySql: ".$arr[1]." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);
                       $fecha = $data['fecha'];
                       $libreta = $data['libreta'];
                       $zona_copia = $data['zona_copia'];
                       $fecha_copia = $data['fecha_copia'];
                       $libreta_copia = $data['libreta_copia'];
                       $orden_desde = $data['orden_desde'];
                       $orden_hasta = $data['orden_hasta'];
                       $comentario = $data['comentario'];

                       $pk_fecha = $fecha;
                       $pk_libreta = $libreta;

                       Database::disconnect();
                   }
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

<?php if (!edita()  or !editaCenso($fecha)  ) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>


	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>
<body onload="aVerVer('CRsector_copiado')">

<?php else : ?>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>
<body >
<?php endif ?>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">datos para completar el sector no censado</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRsector_copiado" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

				  <div class=row>
					<div class="col-sm-6">
				  
                      <div class="form-group">
                        <label for="fecha">Fecha a la que corresponde el censo faltante</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required  data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>
                            
                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
                            <?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>

                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                      </div>
                    </div>
					<div class="col-sm-6">

					  <div class="form-group ">
                        <label for="libreta">Libreta (virtual)</label>
                            <input type="text" class="form-control input-sm" style="width: 45px" id="libreta" name="libreta"  oninput="mayus(this);" pattern="<?php echo PATRON_libretaVirtual;?>" maxlength="3" required  data-error="<?php echo PATRON_libretaVirtual_men;?>" value="<?php echo !empty($libreta)?$libreta:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlibreta"></p>
                            <?php if (!empty($libretaError)): ?>
                                <span class="help-inline"><?php echo $libretaError;?></span>
                            <?php endif; ?>


                            <input name="pk_libreta" type="hidden"  value="<?php echo !empty($pk_libreta)?$pk_libreta:'';?>"> <!-- pk, clave anterior -->
                      </div>
                    </div>
                  </div>

                    <div class="form-group ">
                        <label for="zona_copia">Sector faltante desde-hasta (texto)</label>
                            <textarea class="form-control input-sm" id="zona_copia" name="zona_copia" rows="1" maxlength="100" ><?php echo !empty($zona_copia)?$zona_copia:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorzona_copia"></p>
                            <?php if (!empty($zona_copiaError)): ?>
                                <span class="help-inline"><?php echo $zona_copiaError;?></span>
                            <?php endif; ?>


                    </div>

<div class="panel panel-info">
      <div class="panel-heading">SE COMPLETA CON DATOS OBTENIDOS DE</div>
      <div class="panel-body">
		<div class=row>
			<div class="col-sm-6">
				<div class="form-group ">

                    <label for="fecha_copia">Censo de fecha</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha_copia" name="fecha_copia" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required  data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha_copia)?$fecha_copia:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha_copia"></p>
                            <?php if (!empty($fecha_copiaError)): ?>
                                <span class="help-inline"><?php echo $fecha_copiaError;?></span>
                            <?php endif; ?>


                    </div>
			</div>

			<div class="col-sm-6">
                    <div class="form-group ">
                        <label for="libreta_copia">Libreta desde la que se obtienen los datos</label>
                            <input type="text" class="form-control input-sm" style="width: 45px" id="libreta_copia" name="libreta_copia"  oninput="mayus(this);" pattern="<?php echo PATRON_libreta;?>" maxlength="3" required data-error="<?php echo PATRON_libreta_men;?>" value="<?php echo !empty($libreta_copia)?$libreta_copia:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlibreta_copia"></p>
                            <?php if (!empty($libreta_copiaError)): ?>
                                <span class="help-inline"><?php echo $libreta_copiaError;?></span>
                            <?php endif; ?>


                    </div>
			</div>
		</div>

<fieldset class="form-inline">

                    <div class="form-group ">
                        <label for="orden_desde">Orden: desde</label>
                            <input type="number" class="form-control input-sm" style="width:90px" id="orden_desde" name="orden_desde"  min="0" max="999" required  data-error="Debe estar entre 0 y 999" value="<?php echo
!empty($orden_desde)?$orden_desde:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrororden_desde"></p>
                            <?php if (!empty($orden_desdeError)): ?>
                                <span class="help-inline"><?php echo $orden_desdeError;?></span>
                            <?php endif; ?>


                    </div>

                    <div class="form-group ">
                        <label for="orden_hasta">...hasta</label>
                            <input type="number" class="form-control input-sm" style="width:90px" id="orden_hasta" name="orden_hasta"  min="0" max="999" required  data-error="Debe estar entre 0 y 999" value="<?php echo
!empty($orden_hasta)?$orden_hasta:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrororden_hasta"></p>
                            <?php if (!empty($orden_hastaError)): ?>
                                <span class="help-inline"><?php echo $orden_hastaError;?></span>
                            <?php endif; ?>


                    </div>
</fieldset>



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

    <div class="form-actions">
					<?php if (edita()   and editaCenso($fecha)   ) : ?>
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endif ?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>cambios guardados</h5></span>
                          <?php endif;?>

                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->




  </body>
</html>