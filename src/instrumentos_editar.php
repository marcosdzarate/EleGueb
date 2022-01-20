<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'instrumentos';

    /* uso el mimso form para "ver" cuando no tiene habilitada la edición */
                /* sin permiso de edición, fuera*/
                /* siErrorFuera(edita()); */


    /*parametros inválidos, fuera*/
    $v=true;

    $pk_instrumentoNRO = null;
    if (isset($_GET["instrumentoNRO"])) {
        $pk_instrumentoNRO=$_GET["instrumentoNRO"];
        $m = validar_instrumentoNRO ($pk_instrumentoNRO,$v,true);
    }
    else{
        if (!isset($_GET["instrumentoNRO"]) and !isset($_POST["pk_instrumentoNRO"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);



    if ( !empty($_POST)) {
        // para errores de validacion
        $instrumentoNROError = null;
        $tipoError = null;
        $identificacionError = null;
        $serial_numError = null;
        $modeloError = null;
        $fabricanteError = null;
        $nuestroError = null;
        $disponibleError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $instrumentoNRO = limpia($_POST['instrumentoNRO']);
        $tipo = limpia($_POST['tipo']);
        $identificacion = limpia($_POST['identificacion']);
        $serial_num = limpia($_POST['serial_num']);
        $modelo = limpia($_POST['modelo']);
        $fabricante = limpia($_POST['fabricante']);
        $nuestro = limpia($_POST['nuestro']);
        $disponible = limpia($_POST['disponible']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_instrumentoNRO=limpia($_POST['pk_instrumentoNRO']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $instrumentoNROError = validar_instrumentoNRO ($instrumentoNRO,$valid,true);
        $tipoError = validar_tipo ($tipo,$valid,true);
        $nuestroError = validar_nuestro ($nuestro,$valid,true);
        $disponibleError = validar_disponible ($disponible,$valid,true);

        $identificacionError = validar_identificacion ($identificacion,$valid,false);
        $serial_numError = validar_serial_num ($serial_num,$valid,false);
        $modeloError = validar_modelo ($modelo,$valid,false);
        $fabricanteError = validar_fabricante ($fabricante,$valid,false);
		
        // validacion entre campos
        $eError = validarForm_instrumentos ($instrumentoNRO,$tipo,$identificacion,$serial_num,$modelo,$fabricante,$nuestro,$disponible,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLidentificacion = ($identificacion == '' ? NULL : $identificacion);
            $SQLserial_num = ($serial_num == '' ? NULL : $serial_num);
            $SQLmodelo = ($modelo == '' ? NULL : $modelo);
            $SQLfabricante = ($fabricante == '' ? NULL : $fabricante);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE instrumentos set tipo=?,identificacion=?,serial_num=?,modelo=?,fabricante=?,nuestro=?,disponible=?,comentario=? WHERE instrumentoNRO=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($tipo,$SQLidentificacion,$SQLserial_num,$SQLmodelo,$SQLfabricante,$nuestro,$disponible,$SQLcomentario,$pk_instrumentoNRO));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: instrumentos_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM instrumentos where instrumentoNRO=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_instrumentoNRO));
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

                       $instrumentoNRO = $data['instrumentoNRO'];
                       $tipo = $data['tipo'];
                       $identificacion = $data['identificacion'];
                       $serial_num = $data['serial_num'];
                       $modelo = $data['modelo'];
                       $fabricante = $data['fabricante'];
                       $nuestro = $data['nuestro'];
                       $disponible = $data['disponible'];
                       $comentario = $data['comentario'];

                       $pk_instrumentoNRO = $instrumentoNRO;

                       Database::disconnect();
                   }
                }
        }

     $param0 = "?instrumentoNRO=".$instrumentoNRO;


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
<body onload="aVerVer('CRinstrumentos')">

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
        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">instrumento</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRinstrumentos" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
 
                    <div class="row">
                        <div class="col-sm-6">
                
                            <!-- es autoincrement -->
                            <div class="form-group ">
                                <label for="instrumentoNRO">N&uacute;mero de instrumento</label>
                                    <input type="text" class="form-control input-sm" style="width:90px" readonly id="instrumentoNRO" name="instrumentoNRO"  min="<?php echo CONST_instrumentoNRO_min;?>" max="<?php echo CONST_instrumentoNRO_max;?>" required  
									data-error="<?php echo CONST_instrumentoNRO_men;?>" value="<?php echo !empty($instrumentoNRO)?$instrumentoNRO:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorinstrumentoNRO"></p>
                                    <?php if (!empty($instrumentoNROError)): ?>
                                        <span class="help-inline"><?php echo $instrumentoNROError;?></span>
                                    <?php endif; ?>


                                    <input name="pk_instrumentoNRO" type="hidden"  value="<?php echo !empty($pk_instrumentoNRO)?$pk_instrumentoNRO:'';?>"> <!-- pk, clave anterior -->
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="tipo">Tipo</label>
                                    <select class="form-control input-sm" style="width: auto" id="tipo" data-error="Seleccionar un elemento de la lista"  required  name="tipo">
                                        <option value="" <?php if ($tipo == "") {echo " selected";}?> ></option>
                                        <option value="SATELITAL" <?php if ($tipo == "SATELITAL") {echo " selected";}?> >SATELITAL</option>
                                        <option value="TDR" <?php if ($tipo == "TDR") {echo " selected";}?> >TDR</option>
                                        <option value="TDR-ANG" <?php if ($tipo == "TDR-ANG") {echo " selected";}?> >TDR-ANG</option>
                                        <option value="LTL" <?php if ($tipo == "LTL") {echo " selected";}?> >LTL</option>
                                        <option value="CAMARA" <?php if ($tipo == "CAMARA") {echo " selected";}?> >CAMARA</option>
                                        <option value="RADIO" <?php if ($tipo == "RADIO") {echo " selected";}?> >RADIO</option>
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
                                <label for="identificacion">Identificaci&oacute;n (c&oacute;digo)</label>
                                    <input type="text" class="form-control input-sm" id="identificacion" name="identificacion"   
									pattern="<?php echo PATRON_variosInstru?>" maxlength="45" data-error="<?php echo PATRON_variosInstru_men?>"  value="<?php echo !empty($identificacion)?$identificacion:'';?>" >
                                    <div class="help-block with-errors">En general: satelitales= PTT, TDR=ddd/AA, LTL=ddd  radio=frecuenca; sino un c&oacute;digo</div>
                                    <p id="JSErroridentificacion"></p>
                                    <?php if (!empty($identificacionError)): ?>
                                        <span class="help-inline"><?php echo $identificacionError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="serial_num">N&uacute;mero de serie</label>
                                    <input type="text" class="form-control input-sm" id="serial_num" name="serial_num"   pattern="<?php echo PATRON_variosInstru?>" maxlength="45" data-error="<?php echo PATRON_variosInstru_men?>"  
									value="<?php echo !empty($serial_num)?$serial_num:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorserial_num"></p>
                                    <?php if (!empty($serial_numError)): ?>
                                        <span class="help-inline"><?php echo $serial_numError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="modelo">Modelo</label>
                                    <input type="text" class="form-control input-sm" id="modelo" name="modelo"    pattern="<?php echo PATRON_variosInstru?>" maxlength="45" data-error="<?php echo PATRON_variosInstru_men?>" 
									value="<?php echo !empty($modelo)?$modelo:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrormodelo"></p>
                                    <?php if (!empty($modeloError)): ?>
                                        <span class="help-inline"><?php echo $modeloError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="form-group ">
                                <label for="fabricante">Fabricante</label>
                                    <input type="text" class="form-control input-sm" id="fabricante" name="fabricante"  pattern="<?php echo PATRON_variosInstru?>" maxlength="45" data-error="<?php echo PATRON_variosInstru_men?>" 
									value="<?php echo !empty($fabricante)?$fabricante:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorfabricante"></p>
                                    <?php if (!empty($fabricanteError)): ?>
                                        <span class="help-inline"><?php echo $fabricanteError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="form-group ">
                                <label for="nuestro">Nuestro?</label>
                                    <select class="form-control input-sm" style="width: auto" id="nuestro" data-error="Seleccionar un elemento de la lista"  required  name="nuestro">
                                        <option value="" <?php if ($nuestro == "") {echo " selected";}?> ></option>
                                        <option value="SI" <?php if ($nuestro == "SI") {echo " selected";}?> >SI</option>
                                        <option value="NO" <?php if ($nuestro == "NO") {echo " selected";}?> >NO</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrornuestro"></p>
                                    <?php if (!empty($nuestroError)): ?>
                                        <span class="help-inline"><?php echo $nuestroError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                        <div class="col-sm-3">
                            <div class="form-group ">
                                <label for="disponible">Disponibilidad HOY</label>
                                    <select class="form-control input-sm" style="width: auto" id="disponible" data-error="Seleccionar un elemento de la lista"  required name="disponible">
                                        <option value="" <?php if ($disponible == "") {echo " selected";}?> ></option>
                                        <option value="SI" <?php if ($disponible == "SI") {echo " selected";}?> >SI</option>
                                        <option value="NO" <?php if ($disponible == "NO") {echo " selected";}?> >NO</option>
                                        <option value="VIAJANDO" <?php if ($disponible == "VIAJANDO") {echo " selected";}?> >VIAJANDO</option>
                                        <option value="REPARACION" <?php if ($disponible == "REPARACION") {echo " selected";}?> >EN REPARACION</option>
                                        <option value="PRESTADO" <?php if ($disponible == "PRESTADO") {echo " selected";}?> >PRESTADO</option>
                                        <option value="PERDIDO" <?php if ($disponible == "PERDIDO") {echo " selected";}?> >PERDIDO</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrordisponible"></p>
                                    <?php if (!empty($disponibleError)): ?>
                                        <span class="help-inline"><?php echo $disponibleError;?></span>
                                    <?php endif; ?>


                            </div>

                        </div>
                        <div class="col-sm-7">
                            <div class="form-group ">
                                <label for="comentario">Comentario</label>
                                    <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorcomentario"></p>
                                    <?php if (!empty($comentarioError)): ?>
                                        <span class="help-inline"><?php echo $comentarioError;?></span>
                                    <?php endif; ?>


                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="form-actions">

                    <?php if (edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php endif ?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("instrumentos_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


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




  </body>
</html>