<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'criadestetado';

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
        $fechaDesteteError = null;
        $estimadaError = null;
        $edadDesteteError = null;		
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $fechaDestete = limpia($_POST['fechaDestete']);
        $estimada = limpia($_POST['estimada']);
        $edadDestete = limpia($_POST['edadDestete']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);

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

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLedadDestete = ($edadDestete == '' ? NULL : $edadDestete);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE criadestetado set claveU=?,fecha=?,fechaDestete=?,estimada=?,edadDestete=?,comentario=? WHERE claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$fechaDestete,$estimada,$SQLedadDestete,$SQLcomentario,$pk_claveU,$pk_fecha));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: criadestetado_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM criadestetado where claveU=? AND fecha=? ";
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
                       $fechaDestete = $data['fechaDestete'];
                       $estimada = $data['estimada'];
                       $edadDestete = $data['edadDestete'];
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

<?php if (!edita()) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRcriadestetado')">

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

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">registro de destete</h3>
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
                </div>

				<div class="row">
  				  <div class="col-sm-3">
                    <div class="form-group ">
                        <label for="fechaDestete">Fecha del destete</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fechaDestete" name="fechaDestete" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10"   data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fechaDestete)?$fechaDestete:'';?>" >
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
                            <select class="form-control input-sm" style="width: auto" id="estimada" data-error="Seleccionar un elemento de la lista"  required  data-error="Seleccionar un elemento de la lista" name="estimada">
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
                        <a class="btn btn-danger btn-sm"  onclick=ventanaMini("criades_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
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