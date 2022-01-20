<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'censista';
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
		$m = validar_libreta ($pk_libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["pk_libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);

    $pk_IDcolaborador = null;	
	if (isset($_GET["IDcolaborador"])) {
		$pk_IDcolaborador=$_GET["IDcolaborador"];
		$m = validar_IDcolaborador ($pk_IDcolaborador,$v,true);
	}
	else{
		if (!isset($_GET["IDcolaborador"]) and !isset($_POST["pk_IDcolaborador"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);  
	  

    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $libretaError = null;
        $IDcolaboradorError = null;

        $eError = null;

        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $IDcolaborador = limpia($_POST['IDcolaborador']);

        // valores anteriores de campos clave
        $pk_fecha=limpia($_POST['pk_fecha']);
        $pk_libreta=limpia($_POST['pk_libreta']);
        $pk_IDcolaborador=limpia($_POST['pk_IDcolaborador']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta ($libreta,$valid,true);
        $IDcolaboradorError = validar_IDcolaborador ($IDcolaborador,$valid,true);

        // validacion entre campos
        $eError = validarForm_censista ($fecha,$libreta,$IDcolaborador,$valid);

        // actualizar los datos
        if ($valid) {
            // campos que pueden tener NULL
            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE censista set fecha=?,libreta=?,IDcolaborador=? WHERE fecha=? AND libreta=? AND IDcolaborador=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$IDcolaborador,$pk_fecha,$pk_libreta,$pk_IDcolaborador));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: censista_index.php?fecha='.$pk_fecha.'&libreta='.$pk_libreta);*/
                /*exit;*/

                 }
            }
        }
        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM censista where fecha=? AND libreta=? AND IDcolaborador=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_fecha,$pk_libreta,$pk_IDcolaborador));
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
                       $fecha = $data['fecha'];
                       $libreta = $data['libreta'];
                       $IDcolaborador = $data['IDcolaborador'];

                       $pk_fecha = $fecha;
                       $pk_libreta = $libreta;
                       $pk_IDcolaborador = $IDcolaborador;

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

<?php if (!edita() or $_SESSION['tipocen']=='MENSUAL' ) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	


</head>
<body onload="aVerVer('CRcensista')">

<?php else : ?>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		

</head>
<body >
<?php endif ?>

    <div class="container" style="width:90%">

	
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">datos del censista</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRcensista" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <div class="form-group ">
                        <label for="fecha">Fecha</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
							
                            <p id="JSErrorfecha"></p>
                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
							<?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>


                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                    </div>

                    <div class="form-group ">
                        <label for="libreta">Libreta</label>
                            <input type="text" class="form-control input-sm" style="width: 45px" id="libreta" name="libreta"  oninput="mayus(this);" 
							pattern="<?php echo PATRON_libreta;?>" maxlength="3" required readonly data-error="<?php echo PATRON_libreta_men;?>" value="<?php echo !empty($libreta)?$libreta:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlibreta"></p>
                            <?php if (!empty($libretaError)): ?>
                                <span class="help-inline"><?php echo $libretaError;?></span>
                            <?php endif; ?>


                            <input name="pk_libreta" type="hidden"  value="<?php echo !empty($pk_libreta)?$pk_libreta:'';?>"> <!-- pk, clave anterior -->
                    </div>

					<div class="row">
						<div class="col-sm-6">
								<div class="form-group ">
									<label for="IDcolaborador">Censista</label>
										<select class="form-control input-sm"  id="IDcolaborador" data-error="Seleccionar de la lista"   required  data-error="Seleccionar de la lista" name="IDcolaborador">
											<option value="" <?php if ($IDcolaborador == "") {echo " selected";}?> ></option>
				 <?php
					$pdo = Database::connect();
					$sql = "SELECT IDcolaborador, nombre,apellido FROM colaborador ORDER BY 2";
					$q = $pdo->prepare($sql);
					$q->execute();
					if ($q->rowCount()==0) {
							Database::disconnect();
							echo 'no hay registros!!!';
							exit;
							}
					while ( $aCola = $q->fetch(PDO::FETCH_ASSOC) ) {
						 echo str_repeat(" ",32). '<option value="' .$aCola['IDcolaborador']. '"';
									 if ($IDcolaborador == $aCola['IDcolaborador']) {
											 echo " selected";
									}
								  echo '>' .trim($aCola["nombre"]). " ".trim($aCola["apellido"]).'</option>'."\n";
					}
					Database::disconnect();
					?>
										</select>
										<div class="help-block with-errors"></div>
										<p id="JSErrorIDcolaborador"></p>
										<?php if (!empty($IDcolaboradorError)): ?>
											<span class="help-inline"><?php echo $IDcolaboradorError;?></span>
										<?php endif; ?>


										<input name="pk_IDcolaborador" type="hidden"  value="<?php echo !empty($pk_IDcolaborador)?$pk_IDcolaborador:'';?>"> <!-- pk, clave anterior -->
								</div>

 						</div>
						<div class="col-sm-6">
										
											<label for="otro">&nbsp;</label><br>
											<a class="btn btn-primary btn-sm" href="colaborador_index.php" target="_blank" title="nuevo"><span class="glyphicon glyphicon-plus"></span></a>						
											<button class="btn btn-default btn-sm" onclick="window.location.reload();" title="recargar"><span class="glyphicon glyphicon-refresh"></span></button>
						</div>
					</div>                   <br>
                    <div class="form-actions">
						<?php if (edita() and $_SESSION['tipocen']<>'MENSUAL') : ?>
							<button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php endif ?>
							
						
                        <a class="btn btn-default btn-sm" href="censista_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 
 						<br>
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