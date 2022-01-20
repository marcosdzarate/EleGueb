<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';


        $queTabla = 'tag';

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

    $pk_tag = null;
    if (isset($_GET["tag"])) {
        $pk_tag=$_GET["tag"];
        $m = validar_tag ($pk_tag,$v,true);
    }
    else{
        if (!isset($_GET["tag"]) and !isset($_POST["pk_tag"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);


	$validFecha = true;

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $tagError = null;
        $borradoTempoError = null;
        $encontradoTempoError = null;
        $encontradoPlayaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $tag = limpia($_POST['tag']);
        $borradoTempo = limpia($_POST['borradoTempo']);
        $encontradoTempo = limpia($_POST['encontradoTempo']);
        $encontradoPlaya = limpia($_POST['encontradoPlaya']);
        $comentario = limpia($_POST['comentario']);

        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);
        $pk_tag=limpia($_POST['pk_tag']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;
        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$validFecha,true);
		$valid= ($valid and $validFecha);
        $tagError = validar_tag ($tag,$valid,true);		
		if ($validFecha) {
			$borradoTempoError = validar_borradoTempo ($borradoTempo,$fecha,$valid,false);
			$encontradoTempoError = validar_encontradoTempo ($encontradoTempo,$fecha,$valid,false);
		}
        $encontradoPlayaError = validar_playa ($encontradoPlaya,$valid,false);

        // validacion entre campos
		if($valid) {
			$eError = validarForm_tag ($claveU,$fecha,$tag,$borradoTempo,$encontradoTempo,$encontradoPlaya,$comentario,$valid);
		}

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLborradoTempo = ($borradoTempo == '' ? NULL : $borradoTempo);
            $SQLencontradoTempo = ($encontradoTempo == '' ? NULL : $encontradoTempo);
            $SQLencontradoPlaya = ($encontradoPlaya == '' ? NULL : $encontradoPlaya);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE tag set claveU=?,fecha=?,tag=?,borradoTempo=?,encontradoTempo=?,encontradoPlaya=?,comentario=? WHERE claveU=? AND fecha=? AND tag=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$tag,$SQLborradoTempo,$SQLencontradoTempo,$SQLencontradoPlaya,$SQLcomentario,$pk_claveU,$pk_fecha,$pk_tag));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: tag_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM tag where claveU=? AND fecha=? AND tag=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_fecha,$pk_tag));
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
                       $fecha = $data['fecha'];
                       $tag = $data['tag'];
                       $borradoTempo = $data['borradoTempo'];
                       $encontradoTempo = $data['encontradoTempo'];
                       $encontradoPlaya = $data['encontradoPlaya'];
                       $comentario = $data['comentario'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;
                       $pk_tag = $tag;

                       Database::disconnect();
                   }
                }
        }

		    $param0="?claveU=".$claveU."&fecha=".$fecha;
			
		/* valores min-max de temporadas borrado/encontrado en form */
        $anioAc= date("Y");
		if($validFecha) {
			$anioMin= date_format(date_create_from_format("Y-m-d", $fecha),"Y");
		}
		else{
			$anioMin=1984;
		}
		$anioMen= "Debe estar entre $anioMin y $anioAc";
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
<body onload="aVerVer('CRtag')">

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



        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">tag</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRtag" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

					<div class="row">
						<div class="col-sm-3">
				
							<div class="form-group ">
								<label for="claveU">ClaveU</label>
									<input type="text" class="form-control input-lg" style="width: 100px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorclaveU"></p>
									<?php if (!empty($claveUError)): ?>
										<span class="help-inline"><?php echo $claveUError;?></span>
									<?php endif; ?>


									<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
							</div>
						</div>
						<div class="col-sm-3">

							<div class="form-group ">
								<label for="fecha">Fecha</label>
									<input type="text" class="form-control input-lg" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha"></p>
									<?php if (!empty($fechaError)): ?>
										<span class="help-inline"><?php echo $fechaError;?></span>
									<?php endif; ?>


									<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
							</div>
						</div>
						
						<div class="col-sm-1">
						</div>
						
						<div class="col-sm-6">

							<div class="form-group ">
								<label for="tag">Tag</label>
									<input type="text" class="form-control input-lg" style="width:120px" id="tag" name="tag"  oninput="mayus(this);" pattern="<?php echo PATRON_tag?>" data-error="<?php echo PATRON_tag_men?>" maxlength="8" required  value="<?php echo !empty($tag)?$tag:'';?>"  >
									<div class="help-block with-errors"></div>
									<p id="JSErrortag"></p>
									<?php if (!empty($tagError)): ?>
										<span class="help-inline"><?php echo $tagError;?></span>
									<?php endif; ?>


									<input name="pk_tag" type="hidden"  value="<?php echo !empty($pk_tag)?$pk_tag:'';?>"> <!-- pk, clave anterior -->
							</div>

						</div>
					</div>

					<div class="row">
						<div class="col-sm-6">					
							
							<div class="form-group ">
								<label for="borradoTempo">A&ntilde;o en que aparece poco legible</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="borradoTempo" name="borradoTempo"  
									data-dmin="<?php echo $anioMin?>"  data-dmax="<?php echo $anioAc?>"  data-pentero data-error="<?php echo $anioMen; ?>" value="<?php echo !empty($borradoTempo)?$borradoTempo:'';?>"  >
									<div class="help-block with-errors"></div>
									<p id="JSErrorborradoTempo"></p>
									<?php if (!empty($borradoTempoError)): ?>
										<span class="help-inline"><?php echo $borradoTempoError;?></span>
									<?php endif; ?>


							</div>
						</div>

					</div>

					
					<label for="encontradoPlaya" style="color:#464fa0">Tag perdido/Se encuentra el tag en alg&uacute;n lugar de la costa</label>
				
					<div class=row>
						<div class="col-sm-3">

							<div class="form-group ">
								<label for="encontradoTempo">A&ntilde;o</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="encontradoTempo" name="encontradoTempo"  
									data-dmin="<?php echo $anioMin?>"  data-dmax="<?php echo $anioAc?>" data-pentero data-error="<?php echo $anioMen; ?>" value="<?php echo !empty($encontradoTempo)?$encontradoTempo:'';?>"  >
									<div class="help-block with-errors"></div>
									<p id="JSErrorencontradoTempo"></p>
									<?php if (!empty($encontradoTempoError)): ?>
										<span class="help-inline"><?php echo $encontradoTempoError;?></span>
									<?php endif; ?>
							</div>
						</div>					
						<div class="col-sm-6">
							<div class="form-group ">
								<label for="encontradoPlaya">Playa donde se lo encontr&oacute;</label>
									<select class="form-control input-sm" id="encontradoPlaya" data-error="C&oacute;digo incorrecto"   name="encontradoPlaya"   >
										<option value="" <?php if ($encontradoPlaya == "") {echo " selected";}?> ></option>
				<?php $pdo = Database::connect();
				$sql = "SELECT IDplaya, nombre,tipo FROM playa WHERE tipo in('TRAMO','PUNTO') and IDplaya<> 'Z999' ORDER BY norteSur";
				$q = $pdo->prepare($sql);
				$q->execute();
				if ($q->rowCount()==0) {
						Database::disconnect();
						echo 'no hay registros!!!';
						exit;
						}
				while ( $aPla = $q->fetch(PDO::FETCH_ASSOC) ) {
					 echo str_repeat(" ",32). '<option value="' .$aPla['IDplaya']. '"';
								 if ($encontradoPlaya == $aPla['IDplaya']) {
										 echo " selected";
								}
							  echo '>' .$aPla['nombre']. '&nbsp;&nbsp;&nbsp;('.$aPla['tipo'].')</option>'."\n";
				}
				Database::disconnect();
				?>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorencontradoPlaya"></p>
									<?php if (!empty($encontradoPlayaError)): ?>
										<span class="help-inline"><?php echo $encontradoPlayaError;?></span>
									<?php endif; ?>


							</div> 
						</div>
						<div class="col-sm-3">
								<label for="nPlaya">&nbsp;</label><br>
								<a class="btn btn-primary btn-sm" href="playa_index.php" target="_blank" title="agregar playa"><span class="glyphicon glyphicon-plus"></span></a>						
								<button class="btn btn-default btn-sm" onclick="window.location.reload();" title="recargar"><span class="glyphicon glyphicon-refresh"></span></button>
						</div>
						
					</div>   

							<div class="form-group ">
								<label for="comentario">Comentario</label>
									<textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
									<div class="help-block with-errors"></div>
									<p id="JSErrorcomentario"></p>
									<?php if (!empty($comentarioError)): ?>
										<span class="help-inline"><?php echo $comentarioError;?></span>
									<?php endif; ?>


							</div>

							
							<div class="form-actions">

							<?php if (edita()) : ?>
								<button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<?php endif ?>

								<a class="btn btn-default btn-sm" href="tag_index.php<?php echo $param0;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


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