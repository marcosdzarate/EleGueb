<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'medidas';

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

    $esCriaDes = null;
    if (isset($_GET["esCriaDes"])) {
        $esCriaDes=$_GET["esCriaDes"];
        $v = ($esCriaDes=='si' or $esCriaDes=='no');
    }
    else{
        if (!isset($_GET["esCriaDes"]) and !isset($_POST["esCriaDes"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $largoStdError = null;
        $largoCurvaError = null;
        $circunferenciaError = null;
        $pesoError = null;
        $nostrilError = null;
        $largo_aleta_extError = null;
        $largo_aleta_intError = null;
        $edadMedidaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $largoStd = limpia($_POST['largoStd']);
        $largoCurva = limpia($_POST['largoCurva']);
        $circunferencia = limpia($_POST['circunferencia']);
        $peso = limpia($_POST['peso']);
        $nostril = limpia($_POST['nostril']);
        $largo_aleta_ext = limpia($_POST['largo_aleta_ext']);
        $largo_aleta_int = limpia($_POST['largo_aleta_int']);
		$edadMedida = null;
        if (isset($_POST['edadMedida'])) {
            $edadMedida = limpia($_POST['edadMedida']);
        }		
        $comentario = limpia($_POST['comentario']);
        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);
        $esCriaDes=limpia($_POST['esCriaDes']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $largoStdError = validar_largoStd ($largoStd,$valid,false);
        $largoCurvaError = validar_largoCurva ($largoCurva,$valid,false);
        $circunferenciaError = validar_circunferencia ($circunferencia,$valid,false);
        $pesoError = validar_peso ($peso,$valid,false);
        $nostrilError = validar_nostril ($nostril,$valid,false);
        $largo_aleta_extError = validar_largo_aleta_ext ($largo_aleta_ext,$valid,false);
        $largo_aleta_intError = validar_largo_aleta_int ($largo_aleta_int,$valid,false);
        $edadMedidaError = validar_edadMedida($edadMedida,$valid,false);

        // validacion entre campos
        $eError = validarForm_medidas ($claveU,$fecha,$largoStd,$largoCurva,$circunferencia,$peso,$nostril,$largo_aleta_ext,$largo_aleta_int,$edadMedida,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLlargoStd = ($largoStd == '' ? NULL : $largoStd);
            $SQLlargoCurva = ($largoCurva == '' ? NULL : $largoCurva);
            $SQLcircunferencia = ($circunferencia == '' ? NULL : $circunferencia);
            $SQLpeso = ($peso == '' ? NULL : $peso);
            $SQLnostril = ($nostril == '' ? NULL : $nostril);
            $SQLlargo_aleta_ext = ($largo_aleta_ext == '' ? NULL : $largo_aleta_ext);
            $SQLlargo_aleta_int = ($largo_aleta_int == '' ? NULL : $largo_aleta_int);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLedadMedida = ($edadMedida == '' ? NULL : $edadMedida);
			

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE medidas set claveU=?,fecha=?,largoStd=?,largoCurva=?,circunferencia=?,peso=?,nostril=?,largo_aleta_ext=?,largo_aleta_int=?,comentario=?,edadMedida=? WHERE claveU=? AND fecha=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$SQLlargoStd,$SQLlargoCurva,$SQLcircunferencia,$SQLpeso,$SQLnostril,$SQLlargo_aleta_ext,$SQLlargo_aleta_int,$SQLcomentario,$SQLedadMedida,$pk_claveU,$pk_fecha));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM medidas where claveU=? AND fecha=? ";
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
                       $largoStd = $data['largoStd'];
                       $largoCurva = $data['largoCurva'];
                       $circunferencia = $data['circunferencia'];
                       $peso = $data['peso'];
                       $nostril = $data['nostril'];
                       $largo_aleta_ext = $data['largo_aleta_ext'];
                       $largo_aleta_int = $data['largo_aleta_int'];
                       $comentario = $data['comentario'];
					   $edadMedida = $data['edadMedida'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;

                       Database::disconnect();
                   }
                }
        }

     $param  = "?claveU=".$claveU."&fecha=".$fecha;
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

<?php if (!edita()) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRmedidas')">

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
                <h3 class="panel-title">registro de medidas</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmedidas" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<input name="esCriaDes" type="hidden"  value="<?php echo !empty($esCriaDes)?$esCriaDes:'';?>">

                    <div class="row">
                        <div class="col-sm-3">
					
							<div class="form-group ">
								<label for="claveU">ClaveU</label>
									<input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorclaveU"></p>
									<?php if (!empty($claveUError)): ?>
										<span class="help-inline"><?php echo $claveUError;?></span>
									<?php endif; ?>


									<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
							</div>

                        </div>
                        
                        <div class="col-sm-5">
							
							<div class="form-group ">
								<label for="fecha">Fecha</label>
									<input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha"></p>
									<?php if (!empty($fechaError)): ?>
										<span class="help-inline"><?php echo $fechaError;?></span>
									<?php endif; ?>


									<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
							</div>
						</div>
                    </div>
					
                    <div class="row">
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="largoStd">Largo standard<br> (m)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="largoStd" name="largoStd" onblur="tdecim(this, 2);" data-pdecimal data-dmin="<?php echo CONST_largoStd_min?>" data-dmax="<?php echo CONST_largoStd_max?>" 
									data-error="<?php echo CONST_largoStd_men?>" value="<?php echo !empty($largoStd)?$largoStd:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorlargoStd"></p>
									<?php if (!empty($largoStdError)): ?>
										<span class="help-inline"><?php echo $largoStdError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                        
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="largoCurva">Longitud curvil&iacute;nea<br> (m)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="largoCurva" name="largoCurva" onblur="tdecim(this, 2);" data-pdecimal data-dmin="<?php echo CONST_largoCurva_min?>" data-dmax="<?php echo CONST_largoCurva_max?>" 
									 data-error="<?php echo CONST_largoCurva_men?>" value="<?php echo !empty($largoCurva)?$largoCurva:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorlargoCurva"></p>
									<?php if (!empty($largoCurvaError)): ?>
										<span class="help-inline"><?php echo $largoCurvaError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                        
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="circunferencia">Circunferencia<br> (m)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="circunferencia" name="circunferencia" onblur="tdecim(this, 2);" data-pdecimal data-dmin="<?php echo CONST_circunferencia_min?>" data-dmax="<?php echo CONST_circunferencia_max?>" 
									data-error="<?php echo CONST_circunferencia_men?>" value="<?php echo !empty($circunferencia)?$circunferencia:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorcircunferencia"></p>
									<?php if (!empty($circunferenciaError)): ?>
										<span class="help-inline"><?php echo $circunferenciaError;?></span>
									<?php endif; ?>


							</div>

                        </div>
                        
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="peso">Peso<br> (kg)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="peso" name="peso" onblur="tdecim(this, 2);" data-pdecimal 
									data-dmin="<?php echo CONST_peso_min?>" data-dmax="<?php echo CONST_peso_max?>" 
									data-error="<?php echo CONST_peso_men?>" value="<?php echo !empty($peso)?$peso:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorpeso"></p>
									<?php if (!empty($pesoError)): ?>
										<span class="help-inline"><?php echo $pesoError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                    </div>
                        
                    <div class="row">

                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="nostril">Nostril<br> (cm)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="nostril" name="nostril" onblur="tdecim(this, 1);" data-pdecimal data-dmin="<?php echo CONST_nostril_min?>" data-dmax="<?php echo CONST_nostril_max?>" 
									data-error="<?php echo CONST_nostril_men?>" value="<?php echo !empty($nostril)?$nostril:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrornostril"></p>
									<?php if (!empty($nostrilError)): ?>
										<span class="help-inline"><?php echo $nostrilError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                        
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="largo_aleta_ext">Largo aleta externo<br> (cm)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="largo_aleta_ext" name="largo_aleta_ext" onblur="tdecim(this, 1);" data-pdecimal data-dmin="<?php echo CONST_largo_aleta_ext_min?>" data-dmax="<?php echo CONST_largo_aleta_ext_max?>" 
									data-error="<?php echo CONST_largo_aleta_ext_men?>" value="<?php echo !empty($largo_aleta_ext)?$largo_aleta_ext:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorlargo_aleta_ext"></p>
									<?php if (!empty($largo_aleta_extError)): ?>
										<span class="help-inline"><?php echo $largo_aleta_extError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                        
                        <div class="col-sm-3">

							<div class="form-group ">
								<label for="largo_aleta_int">Largo aleta interno<br> (cm)</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="largo_aleta_int" name="largo_aleta_int" onblur="tdecim(this, 1);" data-pdecimal data-dmin="<?php echo CONST_largo_aleta_int_min?>" data-dmax="<?php echo CONST_largo_aleta_int_max?>" 
									data-error="<?php echo CONST_largo_aleta_int_men?>" value="<?php echo !empty($largo_aleta_int)?$largo_aleta_int:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorlargo_aleta_int"></p>
									<?php if (!empty($largo_aleta_intError)): ?>
										<span class="help-inline"><?php echo $largo_aleta_intError;?></span>
									<?php endif; ?>


							</div>
                        </div>
                        
                        <div class="col-sm-3">
                            
                        <?php if ($esCriaDes=='si'): ?>
                            <div class="form-group ">
                                <label for="edadMedida">Edad<br> en d&iacute;as</label>
                                    <input type="text" class="form-control input-sm" style="width:105px" id="edadMedida" name="edadMedida" data-pdecimal 
									data-dmin="<?php echo CONST_edadMedida_min?>" data-dmax="<?php echo CONST_edadMedida_max?>" 
									data-error="<?php echo CONST_edadMedida_men?>" value="<?php echo !empty($edadMedida)?$edadMedida:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErroredadMedida"></p>
                                    <?php if (!empty($edadMedidaError)): ?>
                                        <span class="help-inline"><?php echo $edadMedidaError;?></span>
                                    <?php endif; ?>
                            </div>
                        <?php endif;?>
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
                        <div class="row">
							<?php if (edita()) : ?>
								<div class="col-sm-2">
									<button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button><span style="display:inline-block; width: 20px;"></span>
								</div>
							<?php endif ?>							
                            <div class="col-sm-3">

								<a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a><span style="display:inline-block; width: 150px;"></span>
							</div>

                            <div class="col-sm-2">

							<?php if (edita()) : ?>
								<a class="btn btn-danger btn-sm"  onclick=ventanaMini("medidas_borrar.php<?php echo $param;?>","")>eliminar</a><span style="display:inline-block; width: 20px;"></span>
							<?php endif ?>							
							</div>
								

								
                            <div class="col-sm-5">								
								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
								  <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
									   <span class="alert alert-success"><h5>cambios guardados</h5></span>
								  <?php endif;?>
							</div>
						</div>
                    </div>
                </form>
<script>
$('form[data-toggle="validator"]').validator({
    custom: {
		pdecimal: function($el) {
			var r = validatorDecimal ($el);     /* en imiei.js */
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

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>


  </body>
</html>