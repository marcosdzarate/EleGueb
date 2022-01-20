<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'individuo';

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



    $sexoA = null;  /* para cambio de sexo */
    if (!isset($_GET["claveU"]) and !isset($_POST["sexoA"]) ){
            $v=false;
        }
    siErrorFuera($v);	
	
	
    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $sexoError = null;
        $nuestroError = null;
        $muertoError = null;
        $muertoTempoError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $sexo = limpia($_POST['sexo']);
        $nuestro = limpia($_POST['nuestro']);
        $muerto = limpia($_POST['muerto']);
        $muertoTempo = limpia($_POST['muertoTempo']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave y cambio de sexo
        $pk_claveU=limpia($_POST['pk_claveU']);
        $sexoA=limpia($_POST['sexoA']);
		

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $sexoError = validar_sexo ($sexo,$valid,true);
        $nuestroError = validar_nuestro ($nuestro,$valid,true);
        $muertoError = validar_muerto ($muerto,$valid,true);
        $muertoTempoError = validar_muertoTempo ($muertoTempo,$claveU,$valid,false);

            
		/* antes de actualizar, trato posible cambio de sexo */
		if ($sexoError == "" AND $sexo <> $sexoA AND $sexoA <> 'NODET') {
			/* verifico que nuevo sexo MACHO  no tenga registros de HEMBRA ni COPULA y
						que nuevo sexo HEMBRA no tenga registros de MACHO ni COPULA y
						que nuevo sexo NODET  no tenga registros de MACHO ni HEMBRA ni COPULA */
			$pdo = Database::connect();
			$sql = "SELECT claveU FROM hembra WHERE claveU = ? UNION SELECT claveU FROM macho WHERE claveU = ? UNION SELECT claveU FROM copula WHERE claveU = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($pk_claveU,$pk_claveU,$pk_claveU));
			$arr = $q->errorInfo();
			$t=$q->rowCount();
			Database::disconnect();
			if ($t>0) {
				$valid = false;
				$sexoError = "El cambio de sexo se permite solo si el individuo <br>no tiene registros en HEMBRA y/o MACHO y/o C&Oacute;PULA";
			}
		}
		
		
        // validacion entre campos
        $eError = validarForm_individuo ($claveU,$sexo,$nuestro,$muerto,$muertoTempo,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLmuertoTempo = ($muertoTempo == '' ? NULL : $muertoTempo);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
			
			
            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE individuo set claveU=?,sexo=?,nuestro=?,muerto=?,muertoTempo=?,comentario=? WHERE claveU=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$sexo,$nuestro,$muerto,$SQLmuertoTempo,$SQLcomentario,$pk_claveU));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: individuo_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM individuo where claveU=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU));
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
                       $sexo = $data['sexo'];
					   $sexoA = $sexo;
                       $nuestro = $data['nuestro'];
                       $muerto = $data['muerto'];
                       $muertoTempo = $data['muertoTempo'];
                       $comentario = $data['comentario'];

                       $pk_claveU = $claveU;

                       Database::disconnect();
                   }
                }
        }

     $param0 = "?claveU=".$claveU;

	$tipoT="";
	$tActual = temporadaAnio_actual($tipoT);

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
<body onload="aVerVer('CRindividuo')">

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
                <h3 class="panel-title">datos b&aacute;sicos del individuo en la BD</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRindividuo" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                <div class=row>
                  <div class=col-sm-3>				
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
               </div>

                <div class=row>
                  <div class=col-sm-3>					
                    <div class="form-group ">
						<br>
                        <label for="sexo">Sexo</label>
                            <select class="form-control input-sm" style="width: auto" id="sexo" data-error="Seleccionar un elemento de la lista"  required  data-error="Seleccionar un elemento de la lista" name="sexo">
                                <option value="" <?php if ($sexo == "") {echo " selected";}?> ></option>
                                <option value="HEMBRA" <?php if ($sexo == "HEMBRA") {echo " selected";}?> >HEMBRA</option>
                                <option value="MACHO" <?php if ($sexo == "MACHO") {echo " selected";}?> >MACHO</option>
                                <option value="NODET" <?php if ($sexo == "NODET") {echo " selected";}?> >NODET</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorsexo"></p>
                            <?php if (!empty($sexoError)): ?>
                                <span class="help-inline"><?php echo $sexoError;?></span>
                            <?php endif; ?>

                            <input name="sexoA" type="hidden"  value="<?php echo !empty($sexoA)?$sexoA:'';?>"> 
                    </div>

                  </div>

                  <div class=col-sm-3>					
                    <div class="form-group ">
					<br>
                        <label for="nuestro">Nuestro?</label>
                            <select class="form-control input-sm" style="width: auto" id="nuestro" data-error="Seleccionar un elemento de la lista"  required  data-error="Seleccionar un elemento de la lista" name="nuestro">
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

                  <div class=col-sm-3>
                    <div class="form-group ">
					    <br>
                        <label for="muerto">Muerto?</label>
                            <select class="form-control input-sm" style="width: auto" id="muerto" data-error="Seleccionar un elemento de la lista"  required  data-error="Seleccionar un elemento de la lista" name="muerto">
                                <option value="" <?php if ($muerto == "") {echo " selected";}?> ></option>
                                <option value="SI" <?php if ($muerto == "SI") {echo " selected";}?> >SI</option>
                                <option value="NO" <?php if ($muerto == "NO") {echo " selected";}?> >NO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrormuerto"></p>
                            <?php if (!empty($muertoError)): ?>
                                <span class="help-inline"><?php echo $muertoError;?></span>
                            <?php endif; ?>


                    </div>

                  </div>

                  <div class=col-sm-3>					
                    <div class="form-group ">
                        <label for="muertoTempo">A&ntilde;o en que se lo encuentra muerto</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="muertoTempo" name="muertoTempo"  data-pentero
							data-dmin="<?php echo CONST_temporadaMin_min?>" data-dmax=<?php echo $tActual; ?>  
							data-error="<?php echo CONST_temporadaMin_men?> y &lt;= <?php echo $tActual?>"value="<?php echo !empty($muertoTempo)?$muertoTempo:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrormuertoTempo"></p>
                            <?php if (!empty($muertoTempoError)): ?>
                                <span class="help-inline"><?php echo $muertoTempoError;?></span>
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

                   <div class="form-actions">
					<?php if (edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
					<?php endif ?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a><span style="display:inline-block; width: 150px;"></span>
                        
                        
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>cambios guardados</h5></span>
                          <?php endif;?>
                    </div>
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