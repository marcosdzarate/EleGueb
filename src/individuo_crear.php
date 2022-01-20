<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'individuo';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $sexo = null;
        $nuestro = null;
        $muerto = null;
        $muertoTempo = null;
        $comentario = null;

    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $v=false;
    }
    
    siErrorFuera($v);


    $guardado='';

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
        /* no se valida claveU x que se genera en mysql, en un trigger de individuo $claveU = limpia($_POST['claveU']); */
        $sexo = limpia($_POST['sexo']);
        $nuestro = limpia($_POST['nuestro']);
        $comentario = limpia($_POST['comentario']);
        $muerto = "NO";           	//asumimos animal vivo al ingresar al sistema				
        $muertoTempo = null;		

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        /* no se valida claveU x que se genera en mysql, en un trigger de individuo $claveUError = validar_claveU ($claveU,$valid,true); */
        $sexoError = validar_sexo ($sexo,$valid,true);
        $nuestroError = validar_nuestro ($nuestro,$valid,true);
        $muertoError = validar_muerto ($muerto,$valid,true);

    // validacion entre campos
        $eError = validarForm_individuo ($claveU,$sexo,$nuestro,$muerto,$muertoTempo,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLmuertoTempo = ($muertoTempo == '' ? NULL : $muertoTempo);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            
            $pdo -> exec('LOCK TABLES individuo WRITE, claveunica WRITE');
            
            $sql = "INSERT INTO individuo (claveU,sexo,nuestro,muerto,muertoTempo,comentario) values(?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array("9999",$sexo,$nuestro,$muerto,$SQLmuertoTempo,$SQLcomentario));

            $arr = $q->errorInfo();         

            if ($arr[0] <> '00000') {
                $eError = "individuo-Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                $guardado='ok';
                /* nueva cu???? */
                $sql = "SELECT cunica FROM claveunica WHERE ID=1";
                $q = $pdo->prepare($sql);
                $q->execute(array());
                $arr = $q->errorInfo();
                if ($q->rowCount()==0) {
                        Database::disconnect();
                        $eError="No hay registro en claveunica!!!!!";
                        }
                    else{
                       if ($arr[0] <> '00000') {
                           $eError = "claveunica-Error MySQL: ".$arr[1]." ".$arr[2];
                          }
                       else{
                           $data = $q->fetch(PDO::FETCH_ASSOC);
                           $claveU = $data['cunica'];
                        }
                    }               
            }
            
            $pdo -> exec('UNLOCK TABLES');
            Database::disconnect();


        }
    }

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


	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-info">
            <div class="panel-heading" style="background-color:var(--elinfo);color:black">
                <h3 class="panel-title">ingreso de nuevo individuo a la BD - datos b&aacute;sicos </h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRindividuo" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                            <input name="claveU" type="hidden"  value="<?php echo !empty($claveU)?$claveU:'';?>"> 

 

 
                <div class=row>
                  <div class=col-sm-3>				
                    <div class="form-group ">
 						<br>
                       <label for="sexo">Sexo</label>
                            <select class="form-control input-sm" style="width: auto" id="sexo" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="sexo">
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
                    </div>
                  </div>

                  <div class=col-sm-3>					
                    <div class="form-group ">
						<br>
                        <label for="nuestro">Nuestro?</label>
                            <select class="form-control input-sm" style="width: auto" id="nuestro" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="nuestro">
                                <option value="" <?php if ($nuestro == "") {echo " selected";}?> ></option>
                                <option value="SI" <?php if ($nuestro == "SI") {echo " selected";}?> selected >SI</option>
                                <option value="NO" <?php if ($nuestro == "NO") {echo " selected";}?> >NO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrornuestro"></p>
                            <?php if (!empty($nuestroError)): ?>
                                <span class="help-inline"><?php echo $nuestroError;?></span>
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
                        <?php if (empty($guardado)): ?>                                     
                        <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_seleccion_indi_index2.php")>a selecci&oacute;n</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php  endif; ?>


                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>

                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                            <div class="row">
                               <div class="col-sm-6">
								   <a href="#" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> OK</a>
                               </div>
                               <div class="col-sm-6 text-right">
                                    <a class="btn btn-info btn-lg" href="temporada_crear.php?claveU=<?php echo $claveU;?>&xtrx=nuevo&sexx=<?php echo $sexo;?>">siguiente&nbsp;&nbsp;<span class="glyphicon glyphicon-arrow-right"></span></a>
                               </div>
                           </div>
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
</body>
</html>