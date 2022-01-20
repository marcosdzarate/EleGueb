<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'viaje';

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

    $temporada = null;
    if (isset($_GET["temporada"])) {
        $temporada=$_GET["temporada"];
        $m = validar_temporada ($temporada,$_GET["claveU"],$v,true);
    }
    else{
        if (!isset($_GET["temporada"]) and !isset($_POST["temporada"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);
    
    $tipoTempo = null;
    if (isset($_GET["tipoTempo"])) {
        $tipoTempo=$_GET["tipoTempo"];
        $m = validar_tipoTempo ($tipoTempo,$v,true);
    }
    else{
        if (!isset($_GET["tipoTempo"]) and !isset($_POST["tipoTempo"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $etapaError = null;
        $temporadaError = null;
        $tipoTempoError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $temporada = limpia($_POST['temporada']);
        $tipoTempo = limpia($_POST['tipoTempo']);
        $etapa = limpia($_POST['etapa']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);
        $etapaA=limpia($_POST['etapaA']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $etapaError = validar_etapa ($etapa,$valid,true);

        $eError = validar_temporada ($temporada,$claveU,$valid,true); //hidden
        $eError .= validar_tipoTempo ($tipoTempo,$valid,true);

        // validacion entre campos
            $eError .= validarForm_viaje ($claveU,$fecha,$temporada,$tipoTempo,$etapa,$etapaA,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE viaje set claveU=?,fecha=?,etapa=?,comentario=? WHERE claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$etapa,$SQLcomentario,$pk_claveU,$pk_fecha));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: viaje_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM viaje where claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_fecha));
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

                       $claveU = $data['claveU'];
                       $fecha = $data['fecha'];
                       $etapa = $data['etapa'];
                       $comentario = $data['comentario'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;
                       $etapaA = $etapa;

                       Database::disconnect();
                   }
                }
        }

     $param  = "?claveU=$claveU&fecha=$fecha&etapa=$etapa";
     $param0 = "?claveU=$claveU";
     $paramV = "?claveU=$claveU&temporada=$temporada&tipoTempo=$tipoTempo&fecha=$fecha";

     
            /* solo permite viaje_config para etapa de COLOCACION 
               verifico si tiene registro en viaje_config */
            $tieneConfigViaje=false;
            if ($etapa=='COLOCACION') {
                $pdo = Database::connect();
                $sql = "SELECT * FROM viaje_config where claveU=? AND fecha_colocacion=?";
                $q = $pdo->prepare($sql);
                $q->execute(array($pk_claveU,$pk_fecha));
                $arr = $q->errorInfo();
                $r=$q->rowCount();
                if ($arr[0] <> '00000') {
                   $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                } 
                else 
                {
                    if ($r>0){
                        $tieneConfigViaje=true;
                    }
                }
                Database::disconnect();      
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


<?php if (!edita()) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRviaje')">

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
                <h3 class="panel-title">etapa</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRviaje" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                            <input name="temporada" type="hidden"  value="<?php echo !empty($temporada)?$temporada:'';?>">
                            <input name="tipoTempo" type="hidden"  value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">                          
                <div class="row">
                    <div class="col-sm-2">
                
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

                    <div class="col-sm-2">
                            <div class="form-group ">
                                <label for="fecha">Fecha</label>
                                    <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorfecha"></p>
                                    <?php if (!empty($fechaError)): ?>
                                        <span class="help-inline" style="display:inline"><?php echo $fechaError;?></span>
                                    <?php endif; ?>


                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                        </div>

                    </div>
                    
                    <div class="col-sm-1">
                    </div>
                    
                    <div class="col-sm-7">
                    
                        <?php if ($etapaA=='COLOCACION') :?>
                            <?php if (($tieneConfigViaje) or (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid )): ?>
                                <a href="viaje_config_editar.php<?php echo $paramV?>" class="btn btn-info btn-lg"> 
                                <span class="glyphicon glyphicon-cog"></span> configuraci&oacute;n y estado del viaje</a>
                            <?php else: ?>
                                <a href="viaje_config.php<?php echo $paramV?>" class="btn btn-info btn-lg"> 
                                <span class="glyphicon glyphicon-cog"></span> configuraci&oacute;n y estado del viaje</a>
                            <?php endif;?>
                        <?php endif;?>
                    
                    </div>
                    
                    
                    
                    
                </div>

                    <div class="form-group ">
                        <label for="etapa">Etapa del viaje</label>
                            <select class="form-control input-sm" style="width: auto" id="etapa" data-error="Seleccionar un elemento de la lista"   required name="etapa">
                                <option value="" <?php if ($etapa == "") {echo " selected";}?> ></option>
                                <option value="COLOCACION" <?php if ($etapa == "COLOCACION") {echo " selected";}?> >COLOCACION - puesta de los instrumentos</option>
								<?php if($etapaA<>"COLOCACION") : ?>
									<option value="INICIA" <?php if ($etapa == "INICIA") {echo " selected";}?> >INICIA VIAJE - seg&uacute;n seguimiento </option>
									<option value="COSTA ARG" <?php if ($etapa == "COSTA ARG") {echo " selected";}?> >COSTA ARGENTINA continental - seg&uacute;n seguimiento </option>
									<option value="COSTA" <?php if ($etapa == "COSTA") {echo " selected";}?> >COSTA (islas u otro país) - seg&uacute;n seguimiento </option>
									<option value="AJUSTE" <?php if ($etapa == "AJUSTE") {echo " selected";}?> >AJUSTE - se hace un ajuste de los instrumentos para que reinicie viaje</option>
									<option value="REINICIA" <?php if ($etapa == "REINICIA") {echo " selected";}?> >REINICIA VIAJE - seg&uacute;n seguimiento </option>
									<option value="SIN RECUPERACION" <?php if ($etapa == "SIN RECUPERACION") {echo " selected";}?> >SIN RECUPERACION - fin de viaje sin recuperaci&oacute;n del animal</option>
									<option value="RECUPERADO" <?php if ($etapa == "RECUPERADO") {echo " selected";}?> >RECUPERADO - fin de viaje con animal recuperado</option>
</option>
								<?php endif ?>
                            </select>
                            
                            <input name="etapaA" type="hidden"  value="<?php echo !empty($etapaA)?$etapaA:'';?>"> 
                            
                            <div class="help-block with-errors"></div>
                            <p id="JSErroretapa"></p>
                            <?php if (!empty($etapaError)): ?>
                                <span class="help-inline"><?php echo $etapaError;?></span>
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
                        <a class="btn btn-danger btn-sm"  onclick=ventanaMini("viaje_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
					<?php endif ?>

                        

                                              
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h6><?php echo $eError;?></h6></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>cambios guardados</h5></span>
                          <?php endif;?>
                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->


<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>



  </body>
</html>