<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'idpapers';

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

	/* ID de la publicacion IDpublicacion*/
    $pk_ID = null;
    if (isset($_GET["ID"])) {
        $pk_ID=$_GET["ID"];
        $m = validar_ID ($pk_ID,$v,true);
    }
    else{
        if (!isset($_GET["ID"]) and !isset($_POST["pk_ID"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);



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

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_ID=limpia($_POST['pk_ID']);

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

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE idpapers set identificaciones=? WHERE claveU=? AND IDpublicacion=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($SQLidentificaciones,$pk_claveU,$pk_ID));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM idpapers where claveU=? AND IDpublicacion=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_ID));
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
                       $ID = $data['IDpublicacion'];
                       $identificaciones = $data['identificaciones'];

                       $pk_claveU = $claveU;
                       $pk_ID = $ID;

                       Database::disconnect();
                   }
                }
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
function cierroAca(){
	cam=parent.document.getElementById("queHizo");
	if(cam!=null) {
	   cam.value='';
	}
	parent.cierroMNO();
	if (cam==null){  /* cuando edita desde lista */
		parent.reDibu();
	}	
}
function completa(){
	f=window.frameElement;
	p=parent.document;
	
		document.getElementById("indiTitu").innerHTML=p.getElementById("indiTitu").innerHTML;
		document.getElementById("IDpubliTitu").innerHTML=p.getElementById("IDpubliTitu").innerHTML;
	
}	
</script>


<?php if (!edita()) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>
		
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRidpapers');completa()">

<?php else : ?>
		
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>
<body onload="completa()" >
<?php endif ?>

    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">entre</h3>

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


                            <input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
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

									<input name="pk_ID" type="hidden"  value="<?php echo !empty($pk_ID)?$pk_ID:'';?>"> <!-- pk, clave anterior -->
									
									
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

                    <div class="form-actions">
					<?php if (edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
					<?php endif ?>

                        
						<a class="btn btn-default btn-sm" onclick='cierroAca()'>atr&aacute;s</a>
						<span style="display:inline-block; width: 150px;"></span>

					<?php if (edita()) : ?>
                        <a class="btn btn-danger btn-sm"  onclick=ventanaMini("vincular_IndiPubli_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
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

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>



  </body>
</html>