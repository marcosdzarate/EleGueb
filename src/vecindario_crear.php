<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    $par_elimina=1;

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

        $queTabla = 'vecindario';

        $IDesquema = null;
        $IDvecindario = null;
        $tipo = null;
        $NSdesde = null;
        $NShasta = null;


    if ( !empty($_POST)) {
        // para errores de validacion
        $IDesquemaError = null;
        $IDvecindarioError = null;
        $tipoError = null;
        $NSdesdeError = null;
        $NShastaError = null;

        $eError = null;

        // los campos a validar
        $IDesquema = limpia($_POST['IDesquema']);
        $IDvecindario = limpia($_POST['IDvecindario']);
        $tipo = limpia($_POST['tipo']);
        $NSdesde = limpia($_POST['NSdesde']);
        $NShasta = limpia($_POST['NShasta']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $IDesquemaError = validar_IDesquema ($IDesquema,$valid,true);
        $IDvecindarioError = validar_IDvecindario ($IDvecindario,$valid,true);
        $tipoError = validar_tipo ($tipo,$valid,true);
        $NSdesdeError = validar_NSdesdehasta ($NSdesde,$valid,true);
        $NShastaError = validar_NSdesdehasta ($NShasta,$valid,true);

    // validacion entre campos
        $eError = validarForm_vecindario ($IDesquema,$IDvecindario,$tipo,$NSdesde,$NShasta,$valid);

        // nuevo registro
        if ($valid) {
            // si es tipo PARCIAL o MUDA solo un registro por esquema
			$pdo = Database::connect();
            if($tipo=='PARCIAL' OR $tipo=='MUDA'){
				$sql = "SELECT * FROM vecindario where IDesquema=? and tipo=?";
				$q = $pdo->prepare($sql);
				$q->execute(array($IDesquema,$tipo));
				$arr = $q->errorInfo();
				if ($q->rowCount()<>0) {
                    Database::disconnect();
					$valid=false;
                    $eError="Tipo PARCIAL o MUDA: un vecindario por esquema";
                }
				else{
					if ($arr[0] <> '00000') {
						$eError = "Error MySQL: ".$arr[1]." ".$arr[2];
						$valid=false;
					}
				}
			}
            if ($valid){
				// inserta
				$sql = "INSERT INTO vecindario (IDesquema,IDvecindario,tipo,NSdesde,NShasta) values(?,?,?,?,?)";
				$q = $pdo->prepare($sql);
				$q->execute(array($IDesquema,$IDvecindario,$tipo,$NSdesde,$NShasta));

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
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body>
    <div class="container" style="width:90%">

	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">vecindario</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRvecindario" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <div class="form-group ">
                        <label for="IDesquema">IDesquema</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="IDesquema" name="IDesquema"  
							data-pentero 
							data-dmin="<?php echo CONST_IDesquema_min;?>" data-dmax="<?php echo CONST_IDesquema_max;?>" required  
							data-error="<?php echo CONST_IDesquema_men;?>" value="<?php echo !empty($IDesquema)?$IDesquema:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorIDesquema"></p>
                            <?php if (!empty($IDesquemaError)): ?>
                                <span class="help-inline"><?php echo $IDesquemaError;?></span>
                            <?php endif; ?>
                    </div>

                    <div class="form-group ">
                        <label for="IDvecindario">IDvecindario</label>
                            <textarea class="form-control input-sm" id="IDvecindario" name="IDvecindario" rows="1" maxlength="100" oninput="mayus(this);"  required  
							data-patron="<?php echo PATRON_IDvecin?>" data-error="<?php echo PATRON_IDvecin_men?>" ><?php echo !empty($IDvecindario)?$IDvecindario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorIDvecindario"></p>
                            <?php if (!empty($IDvecindarioError)): ?>
                                <span class="help-inline"><?php echo $IDvecindarioError;?></span>
                            <?php endif; ?>
                    </div>

                    <div class="form-group ">
                        <label for="tipo">Tipo</label>
                            <select class="form-control input-sm" style="width: auto" id="tipo" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="tipo">
                                <option value="" <?php if ($tipo == "") {echo " selected";}?> ></option>
                                <option value="PARCIAL" <?php if ($tipo == "PARCIAL") {echo " selected";}?> >PARCIAL</option>
                                <option value="PVALDES" <?php if ($tipo == "PVALDES") {echo " selected";}?> >PVALDES</option>
                                <option value="MUDA" <?php if ($tipo == "MUDA") {echo " selected";}?> >MUDA</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipo"></p>
                            <?php if (!empty($tipoError)): ?>
                                <span class="help-inline"><?php echo $tipoError;?></span>
                            <?php endif; ?>
                    </div>

<fieldset class="form-inline">

					<p ><strong>Playa o tramo donde inicia el vecindario (norte)</strong></p>
					
                    <div class="form-group ">
                            <select class="form-control input-sm" style=width:auto id="playa" required  data-error="No puede faltar." name="playa" onchange='getElementById("NSdesde").value=this.value'>
                                <option value="" <?php if (!isset($NSdesde)) {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
        $sql = "SELECT IDplaya, nombre,tipo,norteSur FROM playa WHERE tipo <> ('MENSUAL') ORDER BY norteSur";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
        while ( $aPla = $q->fetch(PDO::FETCH_ASSOC) ) {
             echo str_repeat(" ",32). '<option value="' .$aPla['norteSur']. '"';
                         if ($NSdesde == $aPla['norteSur']) {
                                 echo " selected";
                        }
                      echo '>' .$aPla['nombre']. '&nbsp;&nbsp;&nbsp;('.$aPla['tipo'].')</option>'."\n";
        }
        Database::disconnect();
        ?>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p ></p>

                    </div>					
					
                    <div class="form-group ">
                        <label for="NSdesde">&nbsp;&nbsp;&nbsp;&nbsp;</label>
						
                            <input type="text" class="form-control input-sm" style="width:105px" id="NSdesde" name="NSdesde" onblur="tdecim(this, 2);" 
							data-pdecimal data-dmin="<?php echo CONST_norteSur_min;?>" data-dmax="<?php echo CONST_norteSur_max;?>"  readonly required  
							data-error="<?php echo CONST_norteSur_men;?>" value="<?php echo !empty($NSdesde)?$NSdesde:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorNSdesde"></p>
                            <?php if (!empty($NSdesdeError)): ?>
                                <span class="help-inline"><?php echo $NSdesdeError;?></span>
                            <?php endif; ?>

                    </div>
                    <div class="form-group" style="margin-top:-10px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="btn btn-primary btn-sm" href="playa_index.php" target="_blank" title="agregar playa"><span class="glyphicon glyphicon-plus"></span></a>	<button class="btn btn-default btn-sm" onclick="window.location.reload();" title="recargar"><span class="glyphicon glyphicon-refresh"></span></button>
                    </div>
</fieldset>


<fieldset class="form-inline">
					
					
					<p><strong>Playa o tramo donde termina el vecindario (hacia el sur)</strong></p>
                    <div class="form-group ">
                            <select class="form-control input-sm" style=width:auto id="playa2" required  data-error="No puede faltar" name="playa2" onchange='getElementById("NShasta").value=this.value'>
                                <option value="" <?php if (!isset($NShasta)) {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
        $sql = "SELECT IDplaya, nombre,tipo,norteSur FROM playa WHERE tipo <> ('MENSUAL') ORDER BY norteSur";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
        while ( $aPla = $q->fetch(PDO::FETCH_ASSOC) ) {
             echo str_repeat(" ",32). '<option value="' .$aPla['norteSur']. '"';
                         if ($NShasta == $aPla['norteSur']) {
                                 echo " selected";
                        }
                      echo '>' .$aPla['nombre']. '&nbsp;&nbsp;&nbsp;('.$aPla['tipo'].')</option>'."\n";
        }
        Database::disconnect();
        ?>
                            </select>
                            <div class="help-block with-errors"></div>
							
                            <p ></p>


                    </div>					
					
					
					
                    <div class="form-group ">
                        <label for="NShasta">&nbsp;&nbsp;&nbsp;&nbsp;</label>					
                            <input type="text" class="form-control input-sm" style="width:105px" id="NShasta" name="NShasta" onblur="tdecim(this, 2);" 
							data-pdecimal 
							data-dmin="<?php echo CONST_norteSur_min;?>" data-dmax="<?php echo CONST_norteSur_max;?>"  readonly required  
							data-error="<?php echo CONST_norteSur_men;?>" 
							value="<?php echo !empty($NShasta)?$NShasta:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorNShasta"></p>
                            <?php if (!empty($NShastaError)): ?>
                                <span class="help-inline"><?php echo $NShastaError;?></span>
                            <?php endif; ?>


                    </div>
</fieldset>
                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("vecindario_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro agregado</h5></span>
                          <?php endif;?>
                                                
                                        
                    </div>
                </form>
<script>
$('form[data-toggle="validator"]').validator({
    custom: {
      patron: function($el) {
        var pat = new RegExp($el.data("patron"));
        var le = $el.val().length;
        if (le>0){
           var res =pat.test($el.val());
           if (!res) {
              return $el.data("error")
             }
             else{
               var lm=$el.val().match(pat)[0].length;
               if (le>lm){
                 return $el.data("error")
               }
             }
       }
   },
    pdecimal: function($el) {
			var r = validatorDecimal ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		},
	pentero: function($el) {
			var r = validatorEntero ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}
				
		}
  }
});
</script>

            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>