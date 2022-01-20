<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'censo';

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
	
	
    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $tipoError = null;
        $fechaTotalError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
		
        $fecha = limpia($_POST['fecha']);
        $tipo = limpia($_POST['tipo']);
        $fechaTotal = limpia($_POST['fechaTotal']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_fecha=limpia($_POST['pk_fecha']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

		$fechaError = validar_fechaCenso ($fecha,$valid,true);  
        $tipoError = validar_tipo ($tipo,$valid,true);
		$fechaTotalError = validar_fechaCenso ($fechaTotal,$valid,true);  

        // validacion entre campos
        $eError = validarForm_censo ($fecha,$tipo,$fechaTotal,$comentario,$valid);

        // actualizar los datos
        if ($valid) {
			
            // campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE censo set fecha=?,tipo=?,fechaTotal=?,comentario=? WHERE fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$tipo,$fechaTotal,$SQLcomentario,$pk_fecha));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: censo_index.php');*/
                /*exit;*/

                 }
            }
        }
        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM censo where fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_fecha));
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
                       $tipo = $data['tipo'];
                       $fechaTotal = $data['fechaTotal'];
                       $comentario = $data['comentario'];

                       $pk_fecha = $fecha;

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
	
<?php if (!edita()  or  $tipo=='MENSUAL' or !editaCenso($fechaTotal) ) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>

  	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRcenso')">

<?php else : ?>

  	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body >
<?php endif ?>

    <div class="container" style="width:90%">
		<?php if (!edita()  or  $tipo=='MENSUAL' or !editaCenso($fechaTotal) ) : ?>
		<?php else : ?>	
		<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
			<span class="glyphicon glyphicon-question-sign"></span>
		</button>
		<?php endif ?>

      <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">censo</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRcenso" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <div class="form-group ">
                        <label for="fecha">Fecha censo</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD" oninput="minus(this)" pattern="<?php echo PATRON_fecha;?>" maxlength="10" required  data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors">Fecha en que se recorre una zona a censar de 1 a varios sectores.</div>	
                            <p id="JSErrorfecha"></p>
							
                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
							<?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>


                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                    </div>

                    <div class="form-group ">
                        <label for="tipo">Tipo de censo</label>
                            <select class="form-control input-sm" style="width: auto" id="tipo" data-error="Seleccionar un elemento de la lista" required  data-error="Seleccionar un elemento de la lista" name="tipo">
                                <option value="" <?php if ($tipo == "") {echo " selected";}?> ></option>
                                <option value="PARCIAL" <?php if ($tipo == "PARCIAL") {echo " selected";}?> >PARCIAL</option>
                                <option value="PVALDES" <?php if ($tipo == "PVALDES") {echo " selected";}?> >PVALDES</option>
                                <option value="MUDA" <?php if ($tipo == "MUDA") {echo " selected";}?> >MUDA</option>
							<?php if ($tipo=='MENSUAL' ): ?>
								<option value="MENSUAL" <?php if ($tipo == "MENSUAL") {echo " selected";}?> >MENSUAL</option>
                            <?php endif; ?>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipo"></p>
                            <?php if (!empty($tipoError)): ?>
                                <span class="help-inline"><?php echo $tipoError;?></span>
                            <?php endif; ?>


                    </div>

                    <div class="form-group ">
                        <label for="fechaTotal">Fecha total</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fechaTotal" name="fechaTotal" placeholder="AAAA-MM-DD" oninput="minus(this)" pattern="<?php echo PATRON_fecha;?>" maxlength="10" required data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fechaTotal)?$fechaTotal:'';?>" >
                            <div class="help-block with-errors">Un censo puede llevar m&aacute;s de un d&iacute;a<br>Esta es la fecha con que se va a identificar el censo final.</div>
                            <p id="JSErrorfechaTotal"></p>

                            <?php if (!empty($fechaTotalError) and !(strpos($fechaTotalError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaTotalError;?></span>
							<?php elseif (!empty($fechaTotalError)) : ?>
                                <span class="help-inline"><?php echo $fechaTotalError;?></span>
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
                        <?php if (edita() and  $tipo<>'MENSUAL' and editaCenso($fechaTotal) ): ?>
							<button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>
						
                        <?php if ($tipo=='MENSUAL' ): ?>
							<a class="btn btn-default btn-sm" onclick=parent.cierroM("censo_index_mensual.php");>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php else:?>
							<a class="btn btn-default btn-sm" onclick=parent.cierroM("censo_index.php");>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>
						
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