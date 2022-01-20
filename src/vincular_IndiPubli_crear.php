<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'idpapers';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $ID = null;
        $identificaciones = null;
	
    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["claveU"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

	/* ID de la publicacion IDpublicacion*/
    $ID = null;
    if (isset($_GET["ID"])) {
        $ID=$_GET["ID"];
        $m = validar_ID ($ID,$v,true);
    }
    else{
        if (!isset($_GET["ID"]) and !isset($_POST["ID"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $guardado='';
	$hayVinculo=false;  /*para comprobar vinculo previo */
	
    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $IDError = null;
        $identificacionesError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $ID = limpia($_POST['ID']);
        $identificaciones = limpia($_POST['identificaciones']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $IDError = validar_ID ($ID,$valid,true);
        $identificacionesError = validar_identificaciones ($identificaciones,$valid,false);

        // validacion entre campos
        $eError = validarForm_idpapers ($claveU,$ID,$identificaciones,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL

            $SQLidentificaciones = ($identificaciones == '' ? NULL : $identificaciones);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO idpapers (claveU,IDpublicacion,identificaciones) values (?,?,?) ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$ID,$SQLidentificaciones));

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
		
	else{
		/* comprueba que no exista ya el vinculo */
		$pdo = Database::connect();
		$sql = "SELECT * FROM idpapers where claveU=? AND IDpublicacion=? ";
		$q = $pdo->prepare($sql);
		$q->execute(array($claveU,$ID));
		$arr = $q->errorInfo();
		if ($q->rowCount()<>0) {
			$hayVinculo=true;
		}	
		Database::disconnect();
	}

     $param  = "?claveU=".$claveU."&ID=".$ID;
		

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

<script>
function cerrarTodo(){
	parent.cierroMiniNO();
	parent.parent.cierroMNO();
	parent.parent.queHizo='';
}

function completa(){
	f=window.frameElement;
	p=parent.document;
	document.getElementById("indiTitu"). innerHTML=p.getElementById("indiTitu").innerHTML;
	document.getElementById("IDpubliTitu"). innerHTML=p.getElementById("IDpubliTitu").innerHTML;
}	
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="completa()";>


    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">creando v&iacute;nculo entre</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRidpapers" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

            <div class="panel panel-grisDark">
                <div class="panel-heading">
				    <div class="row">
						<div class="col-sm-2">
							<h3 class="panel-title">individuo:</h3>
							<h3 class="panel-title">publicaci&oacute;n:</h3>
						</div>
						<div class="col-sm-9">
							<div class="row">
								<div class="col-sm-12">
									<h3 class="panel-title" id="indiTitu">&nbsp;</h3>
								</div>  
							</div>  
							<div class="row">
								<div class="col-sm-12">
									<h3 class="panel-title" id="IDpubliTitu">&nbsp;</h3>
								</div>  
							</div>  
						</div>  
					</div>
						

					
                </div>
            </div>	

<?php if(!$hayVinculo) :?>
				
			<div class="row">	
				<div class="col-sm-2">	
                    <div class="form-group ">
                        <label for="claveU">ClaveU</label>
                            <input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>" 
							value="<?php echo !empty($claveU)?$claveU:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorclaveU"></p>
                            <?php if (!empty($claveUError)): ?>
                                <span class="help-inline"><?php echo $claveUError;?></span>
                            <?php endif; ?>

                    </div>

                </div>

				<div class="col-sm-4">						
							<div class="form-group ">
								<label for="ID">publicaci&oacute;n</label>
									<input type="text" class="form-control input-sm" style="width: 75px" id="ID" name="ID"  maxlength="5" readonly data-error="No puede faltar" value="<?php echo !empty($ID)?$ID:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorID"></p>
									<?php if (!empty($IDError)): ?>
										<span class="help-inline"><?php echo $ID?></span>
									<?php endif; ?>
									
							</div>
                </div>
				<div class="col-sm-6">						
                    <div class="form-group ">
                        <label for="identificaciones">identificaci&oacute;n en la publicaci&oacute;n</label>
                            <input type="text" class="form-control input-sm" id="identificaciones" name="identificaciones"  oninput="mayus(this);" pattern="<?php echo PATRON_identiPub?>" data-error="<?php echo PATRON_identiPub_men?>" maxlength="30" value="<?php echo !empty($identificaciones)?$identificaciones:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErroridentificaciones"></p>
                            <?php if (!empty($identificacionesError)): ?>
                                <span class="help-inline"><?php echo $identificacionesError;?></span>
                            <?php endif; ?>


                    </div>
                </div>
            </div>


                    <br>
                    <div class="form-actions">
					<?php if (edita() and $guardado=='') : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
					<?php endif ?>

<?php endif; ?>
                        
						<a class="btn btn-default btn-sm" onclick='parent.cierroMNO()'>atr&aacute;s</a>
						<span style="display:inline-block; width: 150px;"></span>

                        
                        
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>v&iacute;nculo creado</h5></span>
                          <?php endif;?>

                          <?php if ($hayVinculo): ?>
                               <span class="alert alert-success"><h5>ya est&aacute;n vinculadas</h5></span>
                          <?php endif;?>
                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->



  </body>
</html>