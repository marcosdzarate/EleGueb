<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'recuento';

        $fecha = null;
        $libreta = null;
        $orden = null;
        $categoria = null;
        $sexo = null;
        $status = null;
        $cantidad = null;

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/
	$v=true;
	if (isset($_GET["fecha"])) {
		$fecha=$_GET["fecha"];
		$m = validar_fecha ($fecha,$v,true);
	}
	else{
		if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);
	if (isset($_GET["fecha"])) {
		/* intento de eliminar censo fuera del flujo programado */
		siErrorFuera (editaCenso($fecha));
	}
	
	
	
	
	if (isset($_GET["libreta"])) {
		$libreta=$_GET["libreta"];
		$m = validar_libreta ($libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);
	
	if (isset($_GET["orden"])) {
		$orden=$_GET["orden"];
		$m = validar_orden ($orden,$v,true);
	}
	else{
		if (!isset($_GET["orden"]) and !isset($_POST["orden"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);	


	/* el orden de los registros es el orden de ingreso, guardado en oIngreso en la base de datos */
	/* con cada nuevo recuento se determina el ultimo oIngreso y se suma uno al insertar*/
    if ( empty($_POST))	{
            $pdo = Database::connect();
            $sql = "SELECT max(oIngreso) as MAXoIngreso FROM recuento where fecha=? AND libreta=? AND orden=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$orden));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
					$nIngreso = 0;
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);
                       $nIngreso= $data['MAXoIngreso'];

                       Database::disconnect();
                   }
                }
       	
	
		}
	
	
	
	
	
	
    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $libretaError = null;
        $ordenError = null;
        $categoriaError = null;
        $sexoError = null;
        $statusError = null;
        $cantidadError = null;

        $eError = null;

        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $orden = limpia($_POST['orden']);
        $categoria = limpia($_POST['categoria']);
        $sexo = limpia($_POST['sexo']);
		if ($_SESSION['tipocen']<>'MUDA') {
			$status = limpia($_POST['status']);
		}
		else{
			/* censo MUDA */
			$status = 'no corresponde';
		}
        $cantidad = limpia($_POST['cantidad']);

        $nIngreso = limpia($_POST['oIngreso']);
		
		
        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta ($libreta,$valid,true);
        $ordenError = validar_orden ($orden,$valid,true);
        $categoriaError = validar_categoria ($categoria,$valid,true);
        $sexoError = validar_sexo ($sexo,$valid,true);
        $statusError = validar_status ($status,$valid,true);
        $cantidadError = validar_cantidad ($cantidad,$valid,true);

    // validacion entre campos
        $eError = validarForm_recuento ($fecha,$libreta,$orden,$categoria,$sexo,$status,$cantidad,$valid);

        // nuevo registro
        if ($valid) {
            // campos que pueden tener NULL
            // inserta
			$nIngreso = $nIngreso+1;
            $pdo = Database::connect();
            $sql = "INSERT INTO recuento (fecha,libreta,orden,categoria,sexo,status,cantidad,oIngreso) values(?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$orden,$categoria,$sexo,$status,$cantidad,$nIngreso));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            else {
                /* actualizo conformadoOK en grupo */ 
                $pdo = Database::connect();
                $sql = "UPDATE grupo SET conformadoOK=VerificaConformacionGrupo(?,?,?,?) WHERE fecha=? and libreta=? and orden=?";
                $q = $pdo->prepare($sql);
                $q->execute(array($fecha,$libreta,$orden,$_SESSION['referencia'],$fecha,$libreta,$orden));

                $arr = $q->errorInfo();

                Database::disconnect();
                if ($arr[0] <> '00000') {
                    $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                    }
                else {
                 /*header('Location: recuento_index.php?fecha='.$fecha."&libreta=".$libreta."&orden=".$orden);*/
                /*exit;*/
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
    <div class="container" style="width:80%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">agregar componente al grupo</h3>
				
<?php /*echo 'nIngreso= '.$nIngreso*/ ?>
				
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRrecuento" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

				<input name="oIngreso" type="hidden"  value="<?php echo !empty($nIngreso)?$nIngreso:'';?>"> 			
				
				<div class=row>

					<div class="col-sm-4">				
						<div class="form-group ">
							<label for="fecha">Fecha del censo</label>
								<input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
								<div class="help-block with-errors"></div>
								<p id="JSErrorfecha"></p>


								<?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
									<span class="mensa"><?php echo $fechaError;?></span>
								<?php elseif (!empty($fechaError)) : ?>
									<span class="help-inline"><?php echo $fechaError;?></span>
								<?php endif; ?>

						</div>
					</div>

					<div class="col-sm-4">				
						<div class="form-group ">
							<label for="libreta">Libreta</label>
								<input type="text" class="form-control input-sm" style="width: 45px" id="libreta" name="libreta"  oninput="mayus(this);" pattern="<?php echo PATRON_libreta;?>" maxlength="3" required readonly data-error="<?php echo PATRON_libreta_men;?>" value="<?php echo !empty($libreta)?$libreta:'';?>" >
								<div class="help-block with-errors"></div>
								<p id="JSErrorlibreta"></p>
								<?php if (!empty($libretaError)): ?>
									<span class="help-inline"><?php echo $libretaError;?></span>
								<?php endif; ?>
						</div>
					</div>

					<div class="col-sm-4">				
						<div class="form-group ">
							<label for="orden">Orden</label>
								<input type="number" class="form-control input-sm" style="width:90px" id="orden" name="orden"  min="0" max="999" required readonly data-error="Debe estar entre 0 y 999" value="<?php echo !empty($orden)?$orden:'';?>" >
								<div class="help-block with-errors"></div>
								<p id="JSErrororden"></p>
								<?php if (!empty($ordenError)): ?>
									<span class="help-inline"><?php echo $ordenError;?></span>
								<?php endif; ?>
						</div>
					</div>
				</div>

                    <div class="form-group ">
                        <label for="categoria">Categor&iacute;a</label>
                            <select class="form-control input-sm" style=width:auto id="categoria" data-error="Seleccionar un elemento de la lista"  oninput="setSexoStatus(this,'change')" required  data-error="Seleccionar un elemento de la lista" name="categoria">
                                <option value="" <?php if ($categoria == "") {echo " selected";}?> ></option>
        <?php 
		$c="";
		$pdo = Database::connect();   
		/* primipara-adulta pasa al "precisa" para censo y es la unica de hembra reproductiva*/
		if ($_SESSION['referencia']<>'TOTAL') {
			$sql = "SELECT IDcategoria, cateDesc,sexoc,if(ordenCate=21,1,detalleOjo+0) as detalleOjo FROM categoria WHERE ordenCate not IN(20,22,23) AND (ordenCate<90 OR ordenCate=99)  ORDER BY detalleOjo,ordenCate";
		}
		else{
			/*incluye HAREM cuando es referencia TOTAL */
			$sql = "SELECT IDcategoria, cateDesc,sexoc,if(ordenCate=21,1,detalleOjo+0) as detalleOjo FROM categoria WHERE ordenCate not IN(20,22,23) AND (ordenCate<91 OR ordenCate=99)  ORDER BY detalleOjo,ordenCate";
		}
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
	    $p=true;
        while ( $aCat = $q->fetch(PDO::FETCH_ASSOC) ) {
			 if ($p and $aCat['detalleOjo']=="2") {        /*'grupo'*/
                 echo "<option  disabled >------ ------</option>";
				 $p=false;
			 }
             echo str_repeat(" ",32). '<option value="' .$aCat['IDcategoria']. '"';
                         if ($categoria == $aCat['IDcategoria']) {
                                 echo " selected";
                        }
                        if ($aCat['sexoc']=='CUALQUIERA') {
                        echo '>' .$aCat['cateDesc'].'</option>'."\n";
                        }
                        else{
                         echo '>' .$aCat['cateDesc']. '&nbsp;&nbsp;&nbsp;('.$aCat['sexoc'].')</option>'."\n";
                        }
        }
        Database::disconnect();
        ?>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcategoria"></p>
                            <?php if (!empty($categoriaError)): ?>
                                <span class="help-inline"><?php echo $categoriaError;?></span>
                            <?php endif; ?>
                    </div>

                    <div class="form-group ">
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

				<?php if ($_SESSION['tipocen']<>'MUDA'):?>
                    <div class="form-group ">
                        <label for="status">Status macho</label>
                            <select class="form-control input-sm" style="width: auto" id="status" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="status">
                                <option value="" <?php if ($status == "") {echo " selected";}?> ></option>
                                <option value="sd" <?php if ($status == "sd") {echo " selected";}?> >sd</option>
                                <option value="ALFA" <?php if ($status == "ALFA") {echo " selected";}?> >ALFA</option>
                                <option value="PERIFERICO" <?php if ($status == "PERIFERICO") {echo " selected";}?> >PERIFERICO</option>
                                <option value="CERCANO" <?php if ($status == "CERCANO") {echo " selected";}?> >CERCANO</option>
                                <option value="LEJANO" <?php if ($status == "LEJANO") {echo " selected";}?> >LEJANO</option>
                                <option value="SOLO" <?php if ($status == "SOLO") {echo " selected";}?> >SOLO</option>
                                <option value="no corresponde" <?php if ($status == "no corresponde") {echo " selected";}?> >no corresponde</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorstatus"></p>
                            <?php if (!empty($statusError)): ?>
                                <span class="help-inline"><?php echo $statusError;?></span>
                            <?php endif; ?>
                    </div>
				<?php endif;?>

                    <div class="form-group ">
                        <label for="cantidad">Cantidad</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="cantidad" name="cantidad"  
							data-pentero 
							data-dmin="<?php echo CONST_cantidad_min;?>" data-dmax="<?php echo CONST_cantidad_max;?>" required  
							data-error="<?php echo CONST_cantidad_men;?>" 
							value="<?php echo !empty($cantidad)?$cantidad:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcantidad"></p>
                            <?php if (!empty($cantidadError)): ?>
                                <span class="help-inline"><?php echo $cantidadError;?></span>
                            <?php endif; ?>
                    </div>

                    <br>
                    <div class="form-actions">
					
					<?php /*if (empty($guardado)): */?>
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php /* endif; */?>
						
                        <a class="btn btn-default btn-sm" href="recuento_index.php?fecha=<?php echo $fecha;?>&libreta=<?php echo $libreta;?>&orden=<?php echo $orden;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro agregado</h5></span>
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