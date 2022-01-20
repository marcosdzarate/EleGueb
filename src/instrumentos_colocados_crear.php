<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'instrumentos_colocados';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $viajeID = null;
        $instrumentoNRO = null;
        $fecha_recuperacion = null;
        $comentario = null;

    $v=true;

    $viajeID = null;
    if (isset($_GET["viajeID"])) {
        $viajeID=$_GET["viajeID"];
        $m = validar_viajeID ($viajeID,$v,true);
    }
    else{
        if (!isset($_GET["viajeID"]) and !isset($_POST["viajeID"]) ) {
            $v=false;
        }
    }
    siErrorFuera($v);


    $valid=true;
    $eError = null;

    $guardado='';
    
    $xdias=30;
    if (isset($_GET["viajeID"])) {
        $actDisponible=false;
        $eError=instrumentos_colocados_intervalo_fechas($viajeID,$xdias,$valid,$actDisponible);
        if($valid) {
            /* la actualizacion de instrumentos.disponible a VIAJANDO la determina el usuario 
               pero establecemos el default de la respuesta segun margen hoy-fecha colocacion*/
            if(!$actDisponible) {
                /* la fecha de colocacion es de hace mas de xdias*/
                $actualizaDispo='NO';
                $userDispo='SI';
            }
            else {
                /* la fecha de colocacion es de hace menos de xdias*/
                $actualizaDispo='SI';
                $userDispo='NO';
            }
        }
    }
        
    if ( !empty($_POST)) {
        // para errores de validacion
        $viajeIDError = null;
        $instrumentoNROError = null;
        $fecha_recuperacionError = null;
        $comentarioError = null;
        $actualizaDispoError = null;


        // los campos a validar
        $viajeID = limpia($_POST['viajeID']);
        $instrumentoNRO = limpia($_POST['instrumentoNRO']);
        $fecha_recuperacion = limpia($_POST['fecha_recuperacion']);
        $comentario = limpia($_POST['comentario']);
        
        $actualizaDispo = limpia($_POST['actualizaDispo']);

        $userDispo = limpia($_POST['userDispo']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar

        $viajeIDError = validar_viajeID ($viajeID,$valid,true);
        $instrumentoNROError = validar_instrumentoNRO ($instrumentoNRO,$valid,true);
        $fecha_recuperacionError = validar_fecha_recuperacion ($fecha_recuperacion,$valid,false);

        $actualizaDispoError = validar_actualizaDispo ($actualizaDispo,$valid,true);
        

    // validacion entre campos
        $eError = validarForm_instrumentos_colocados ($viajeID,$instrumentoNRO,$fecha_recuperacion,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLfecha_recuperacion = ($fecha_recuperacion == '' ? NULL : $fecha_recuperacion);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO instrumentos_colocados (viajeID,instrumentoNRO,fecha_recuperacion,comentario) values(?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($viajeID,$instrumentoNRO,$SQLfecha_recuperacion,$SQLcomentario));

            $arr = $q->errorInfo();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                $guardado='ok';
                if($actualizaDispo=='SI') {
                    /* actualiza "disponible" sii la fecha de colocacion esta entre hoy y "mdias" dias antes (ver validarForm_instrumentos_colocados) */
                    $sql="UPDATE instrumentos SET disponible='VIAJANDO' WHERE disponible='SI' AND instrumentoNRO=?";
                    $q = $pdo->prepare($sql);
                    $q->execute(array($instrumentoNRO));
                    $arr = $q->errorInfo();
                    if ($arr[0] <> '00000') {
                        $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                    }
                }
            }
            Database::disconnect();



        }
    }
/*   
if($guardado=='ok') {
    header("Location: instrumentos_colocados_index.php?viajeID=$viajeID");
    exit;
}*/ 
    
    
    
     $param0 = "?viajeID=".$viajeID."&instrumentoNRO=".$instrumentoNRO;


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

    
<style>
 .escon {
     display:none;
 }
</style>
    
<script>
function verInstru(instru){

    if (instru.viejo!="") {
        var j="ipre"+instru.viejo;
        document.getElementById(j).style.display = "none";
    }
    if (instru.value!="") {
        var i="ipre"+instru.value;
        document.getElementById(i).style.display = "block";
        instru.viejo=instru.value;
    }
    
}
    


</script>
<style>
.mark, mark {
    padding: .2em;
    background-color: #ffd600;
}
.lh13{
    line-height:1.3;
}
</style>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body >
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo instrumento colocado</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRinstrumentos_colocados" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <input type="hidden" class="form-control input-sm" style="width:150px" id="fecha_recuperacion" name="fecha_recuperacion" maxlength="10"   data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha_recuperacion)?$fecha_recuperacion:'';?>" >
                
                

                    
                    <div class="row">
						
                        <div class="col-xs-7">
                            <div class="form-group">
                                <label for="instrumentoNRO">Instrumentos</label>
                                    <select required <?php if($guardado=='ok') {echo " disabled ";} ?>
                                    class="form-control input-sm" style="width:auto"  id="instrumentoNRO" data-error="Seleccionar un elemento de la lista" name="instrumentoNRO" onfocus="this.viejo=this.value;" onchange="verInstru(this);">
                                        <option value="" <?php if ($instrumentoNRO == "") {echo " selected";}?> ></option>
             <?php
                $pdo = Database::connect();
                if ($userDispo=='SI') {
                    $sql = "SELECT instrumentoNRO, tipo, identificacion,modelo,disponible FROM instrumentos ORDER BY disponible,2,3,4";
                    }
                else{
                    $sql = "SELECT instrumentoNRO, tipo, identificacion,modelo,disponible FROM instrumentos WHERE disponible='SI' or instrumentoNRO=? ORDER BY disponible,2,3,4";
                }
                $q = $pdo->prepare($sql);
                $q->execute(array($instrumentoNRO));
                if ($q->rowCount()==0) {
                        Database::disconnect();
                        echo 'no hay registros!!!';
                        exit;
                        }
                $da="";
                while ( $aCola = $q->fetch(PDO::FETCH_ASSOC) ) {
                    if ($da <> $aCola['disponible']) {
                        $da = $aCola['disponible'];
                        echo str_repeat(" ",32). "<option disabled>- - - - - - disponible=$da</option>"."\n";
                    }                   
                     echo str_repeat(" ",32). '<option value="' .$aCola['instrumentoNRO']. '"';
                                 if ($instrumentoNRO == $aCola['instrumentoNRO']) {
                                         echo " selected";
                                }
                              echo '>' .str_pad($aCola["tipo"],15). " ".str_pad($aCola["identificacion"],20). " ".str_pad($aCola["modelo"],20).'</option>'."\n";
                }
                Database::disconnect();
                ?>                              
                                    </select>
                                    
                    
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorinstrumentoNRO"></p>
                                    <?php if (!empty($instrumentoNROError)): ?>
                                        <span class="help-inline"><?php echo $instrumentoNROError;?></span>
                                    <?php endif; ?>
                                
                            </div>
                                                        
                        </div>
                        
                        <div class="col-xs-5">
                            <div class="form-group ">
                                <label for="viajeID">ID de viaje</label>
                                    <input type="number" class="form-control input-sm" style="width:90px" id="viajeID" name="viajeID"  min="1" max="9999" required readonly data-error="Debe estar entre 1 y 9999" value="<?php echo !empty($viajeID)?$viajeID:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorviajeID"></p>
                                    <?php if (!empty($viajeIDError)): ?>
                                        <span class="help-inline"><?php echo $viajeIDError;?></span>
                                    <?php endif; ?>
                            </div>

                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xs-7">

                    
             <?php
                $pdo = Database::connect();
                if ($guardado=='ok') {
                    $sql = "SELECT * FROM instrumentos WHERE instrumentoNRO=?";
                    $esc="";
                }
                else{
                    $sql = "SELECT * FROM instrumentos ORDER BY disponible,2,3,4";
                    $esc=' class="escon" ';
                }
                $q = $pdo->prepare($sql);
                $q->execute(array($instrumentoNRO));
                if ($q->rowCount()==0) {
                        Database::disconnect();
                        echo 'no hay registros!!!';
                        exit;
                        }
                while ( $aCola = $q->fetch(PDO::FETCH_ASSOC) ) {
                     echo '<pre '.$esc.' id="ipre'.$aCola['instrumentoNRO'].'">';
                     echo 'Nro de instrumento: '.$aCola['instrumentoNRO']."\n";
                     echo 'Tipo:             : '.$aCola['tipo']."\n";
                     echo 'Identificaci&oacute;n    : '.$aCola['identificacion']."\n";
                     echo 'N&uacute;mero de serie   : '.$aCola['serial_num']."\n";
                     echo 'Modelo            : '.$aCola['modelo']."\n";
                     echo 'Fabricante        : '.$aCola['fabricante']."\n";
                     echo 'Nuestro           : '.$aCola['nuestro']."\n";
                     echo '<mark>Disponibilidad HOY: '.$aCola['disponible']."</mark>\n";
                     echo 'Comentario        : '.$aCola['comentario']."\n";
                     echo '</pre>'."\n";
                }
                Database::disconnect();
                ?>                          
                    
                    
                    
                         </div>
                        
                        <div class="col-xs-5">
                   
                            <div class="form-group ">
                                <label for="comentario">Comentario</label>
                                    <textarea class="form-control input-sm" id="comentario" name="comentario" rows="3" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorcomentario"></p>
                                    <?php if (!empty($comentarioError)): ?>
                                        <span class="help-inline"><?php echo $comentarioError;?></span>
                                    <?php endif?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-7">
                                <input type="hidden" id="userDispo" name="userDispo" value="<?php echo !empty($userDispo)?$userDispo:'';?>" >
                                <input type="hidden" id="actualizaDispo" name="actualizaDispo" value="<?php echo !empty($actualizaDispo)?$actualizaDispo:'';?>" >
                                    
                                <?php if ($userDispo=='SI'): ?>
                                    <label class="lh13" for="actualizaDispo">La fecha de colocaci&oacute;n es de hace <mark>M&Aacute;S</mark> de <?php echo $xdias;?> dias.</label>
                                    
                                <?php else: ?>
                                    <label class="lh13 inline"for="actualizaDispo">La fecha de colocaci&oacute;n es de hace <mark>MENOS</mark> de <?php echo $xdias;?> dias. <br>Se actualiza disponibilidad HOY a <mark>VIAJANDO</mark></label>
                                <?php endif?>
                                    <br>
                                    <label >Para administrar instrumentos </label>
									<a href="instrumentos_index.php" target="_blank" title="ir a instrumentos" style="font-size:14px"> <span class="glyphicon glyphicon-music"></span></a>
									luego <button class="btn btn-default btn-sm" style="padding: 2px 0px;" onclick="window.location.reload();" title="recargar esta ventana"><span class="glyphicon glyphicon-refresh"></span></button>
                                
                        </div>

                        <div class="col-xs-5">
                            <div class="form-group ">
                                        <div class="help-block with-errors"></div>
                                        <p id="JSErroractualizaDispo"></p>
                                        <?php if (!empty($actualizaDispoError)): ?>
                                            <span class="help-inline"><?php echo $actualizaDispoError;?></span>
                                        <?php endif?>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-xs-5">
                               <?php  if (empty($guardado=='ok')): ?>                                     
                                    <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                               <?php endif?>

                                <a class="btn btn-default btn-sm" href="instrumentos_colocados_index.php?viajeID=<?php echo $viajeID;?>">a lista</a>

                            </div>
                            <div class="col-xs-7">
                                  <?php if (!empty($eError)): ?>
                                    <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                                  <?php endif;?>
                                 <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                                 
                                    <span class="alert alert-success"><h5>instrumento agregado</h5></span>
                                  <?php endif;?>
                            </div>
                        </div>


                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>