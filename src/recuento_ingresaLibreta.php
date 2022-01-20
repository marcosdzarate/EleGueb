<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

if (isset($_GET["mod"])) {
		$modelo=$_GET["mod"];
	}
	else {
		if (isset($_POST["mod"]) ) {
			$modelo=$_POST["mod"];
			}
		else {
			$modelo=0;
		}
	}
	
		
if ($_SESSION['tipocen']=='PVALDES'){
	if ($modelo==0) {
		require_once 'recuento_ingresaFormPVALDES_00.php';
	}
	else {
		require_once 'recuento_ingresaFormPVALDES_01.php';
	}
}
else{
	if ($modelo==0) {
		require_once 'recuento_ingresaFormMUDA_00.php';
	}
	else {
		require_once 'recuento_ingresaFormMUDA_01.php';
	}
}


$recPlanillaLongis = count($recPlanillaCate);
$cmin=CONST_cantidad_min;
$cmax=CONST_cantidad_max;
$cmen=CONST_cantidad_men;

        $queTabla = 'recuento';

        $fecha = null;
        $libreta = null;
        $orden = null;
		
        $categoria = null;
        $sexo = null;
        $status = null;
        $cantidad = null;


for($x = 0; $x < $recPlanillaLongis; $x++) {
	$v="rec_".$x;
	$$v=null;			/* la variable cantidad se llaman rec_0 rec_1....*/
	$vError=$v."Error";
	$$vError=null;		/* el error de cada cantidad se llama rec_0Error, rec1_Error....*/
}		
		
		
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

	
    if ( !empty($_POST)) {
         // para errores de validacion
		$fechaError = null;
        $libretaError = null;
        $ordenError = null;

        $eError = null;

        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $orden = limpia($_POST['orden']);
        $valid = true;

        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta ($libreta,$valid,true);
        $ordenError = validar_orden ($orden,$valid,true);
		
		
		for($x = 0; $x < $recPlanillaLongis; $x++) {
			$v="rec_".$x;
			$$v=limpia($_POST[$v]);		/* la variable cantidad se llaman rec_0 rec_1....*/
			$vError=$v."Error";
			$$vError=validar_cantidad ($$v,$valid,false);		/* el error de cada cantidad se llama rec_0Error, rec1_Error....*/
		}   

		// validacion entre campos
		
		if($valid) {
			/* debe haber al menos una cantidad ingresada */
			$suma=0;
			for($x = 0; $x < $recPlanillaLongis; $x++) {
				$v="rec_".$x;
				$suma+=$$v;
			}
			if ($suma==0){
				$valid =false;
				$eError="Ingresar al menos una cantidad";
			}
		}

		
        // nuevo registro
        if ($valid) {
            // inserta
			$nIngreso=0;
            $pdo = Database::connect();
			for($x = 0; $x < $recPlanillaLongis; $x++) {
				$nIngreso = $nIngreso+1;
				$v="rec_".$x;
				$cantidad=$$v;
				if(!empty($cantidad)) {
					$categoria=$recPlanillaCate[$x];
					$sexo=$recPlanillaSexo[$x];
					$status=$recPlanillaStatus[$x];				
					$sql = "INSERT INTO recuento (fecha,libreta,orden,categoria,sexo,status,cantidad,oIngreso) values(?,?,?,?,?,?,?,?)";
					$q = $pdo->prepare($sql);
					$q->execute(array($fecha,$libreta,$orden,$categoria,$sexo,$status,$cantidad,$nIngreso));
					$arr = $q->errorInfo();
					if ($arr[0] <> '00000') {
						$eError = "Error MySQL: ".$arr[1]." ".$arr[2];
						$valid=false;
						break;
					}
				}
			}
			if($valid) {
                /* actualizo conformadoOK en grupo */ 
                $sql = "UPDATE grupo SET conformadoOK=VerificaConformacionGrupo(?,?,?,?) WHERE fecha=? and libreta=? and orden=?";
                $q = $pdo->prepare($sql);
                $q->execute(array($fecha,$libreta,$orden,$_SESSION['referencia'],$fecha,$libreta,$orden));
                $arr = $q->errorInfo();
                if ($arr[0] <> '00000') {
                    $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                    }				
			}
            Database::disconnect();
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
</head>

<body>
    <div class="container" style="width:98%">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">ingreso de la composici&oacute;n de grupo por l&iacute;nea de libreta</h3>
							
            </div>
            <div class="panel-body">
			
                <form data-toggle="validator" id="CRrecuento" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

					<input type="hidden" id="mod" name="mod" value="<?php echo !empty($modelo)?$modelo:'';?>" >
 			
				
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

	<?php 
		$elemenFila=$recPlanillaFilas[0];
		echo "\r\n".'		<div class="row">';	
		for($x = 0; $x < $recPlanillaLongis; $x++) {
					$v="rec_".$x;
					$vError=$v."Error";
					if ($elemenFila<>$recPlanillaFilas[$x]) {
						echo "\r\n".'		</div>';
						echo "\r\n".'		<div class="row">';						
						$elemenFila=$recPlanillaFilas[$x];
					}
					echo "\r\n".'			<div class="col-sm-2">';
					echo "\r\n".'				<div class="form-group ">';
					echo "\r\n"." 					<label for='$v'>$recPlanillaLabel[$x]</label>";
					echo "\r\n"."					<input type='text' class='form-control input-sm' style='width:90px' id='$v' name='$v' data-pentero data-dmin='$cmin' data-dmax='$cmax' data-error='$cmen' value='".$$v."'>";
					echo "\r\n".'					<div class="help-block with-errors"></div>';
					echo "\r\n"."					<p id='JSError$v'></p>";
					if (!empty($$vError)) {
						echo "\r\n"."					<span class='help-inline'>".$$vError."</span>";
					}
					echo "\r\n".'				</div>';
					echo "\r\n".'			</div>';

		}
					echo "\r\n".'		</div>';
		echo "\r\n";
		?>

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
                            <span class="alert alert-success"><h5>registros agregados</h5></span>
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