<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';


        $queTabla = 'tag';

        /* sin permiso de edición, fuera*/
        siErrorFuera(edita());          

        /*parametros inválidos, fuera*/        
        $claveU = null;
        $fecha = null;
        $tag = null;
        $borradoTempo = null;
        $encontradoTempo = null;
        $encontradoPlaya = null;
        $comentario = null;

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

    $fecha = null;
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

        $guardado="";

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
        $comentario = limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $tagError = validar_tag ($tag,$valid,true);

    // validacion entre campos
        if ($valid){
			$eError = validarForm_tag ($claveU,$fecha,$tag,$borradoTempo,$encontradoTempo,$encontradoPlaya,$comentario,$valid);
		}

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLborradoTempo = ($borradoTempo == '' ? NULL : $borradoTempo);
            $SQLencontradoTempo = ($encontradoTempo == '' ? NULL : $encontradoTempo);
            $SQLencontradoPlaya = ($encontradoPlaya == '' ? NULL : $encontradoPlaya);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
        
            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO tag (claveU,fecha,tag,borradoTempo,encontradoTempo,encontradoPlaya,comentario) values(?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$tag,$SQLborradoTempo,$SQLencontradoTempo,$SQLencontradoPlaya,$SQLcomentario));

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
	
    $param0="?claveU=".$claveU."&fecha=".$fecha;
	
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



        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo tag</h3>
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
							</div>
						</div>
						<div class="col-sm-1">
						</div>
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="tag">Tag</label>
									<input type="text" class="form-control input-lg" style="width:120px" id="tag" name="tag"  oninput="mayus(this);" pattern="<?php echo PATRON_tag?>" data-error="<?php echo PATRON_tag_men?>" maxlength="8" required  value="<?php echo !empty($tag)?$tag:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrortag"></p>
									<?php if (!empty($tagError)): ?>
										<span class="help-inline"><?php echo $tagError;?></span>
									<?php endif; ?>
							</div>
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

							<br>
							<div class="form-actions">
								<button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

								<a class="btn btn-default btn-sm" href="tag_index.php<?php echo $param0;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
								 <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
									<span class="alert alert-success"><h5>tag agregado</h5></span>
								  <?php endif;?>


							</div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>