<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());


        $queTabla = 'categoria';

        $IDcategoria = null;
        $cateDesc = null;
        $grupoEdad = null;
        $reproductiva = null;
        $ordenCate = null;
        $edadRelativa = null;
        $sexoc = null;
		$detalleOjo = null;
        

    if ( !empty($_POST)) {
        // para errores de validacion
        $IDcategoriaError = null;
        $cateDescError = null;
        $grupoEdadError = null;
        $reproductivaError = null;
        $ordenCateError = null;
        $edadRelativaError = null;
        $sexocError = null;
        $detalleOjoError = null;

        $eError = null;

        // los campos a validar
        $IDcategoria = limpia($_POST['IDcategoria']);
        $cateDesc = limpia($_POST['cateDesc']);
        $grupoEdad = limpia($_POST['grupoEdad']);
        $reproductiva = limpia($_POST['reproductiva']);
        $ordenCate = limpia($_POST['ordenCate']);
        $edadRelativa = limpia($_POST['edadRelativa']);
        $sexoc = limpia($_POST['sexoc']);
        $detalleOjo = limpia($_POST['detalleOjo']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $IDcategoriaError = validar_IDcategoria ($IDcategoria,$valid,true);
        $cateDescError = validar_cateDesc ($cateDesc,$valid,true);
        $grupoEdadError = validar_grupoEdad ($grupoEdad,$valid,true);
        $reproductivaError = validar_reproductiva ($reproductiva,$valid,true);
        $ordenCateError = validar_ordenCate ($ordenCate,$valid,true);
        $edadRelativaError = validar_edadRelativa ($edadRelativa,$valid,true);
        $sexocError = validar_sexoc ($sexoc,$valid,true);
        $detalleOjoError = validar_detalleOjo ($detalleOjo,$valid,true);

    // validacion entre campos
        $eError = validarForm_categoria ($IDcategoria,$cateDesc,$grupoEdad,$reproductiva,$ordenCate,$edadRelativa,$sexoc,$detalleOjo,$valid);

        // nuevo registro
        if ($valid) {
            // campos que pueden tener NULL
            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO categoria (IDcategoria,cateDesc,grupoEdad,reproductiva,ordenCate,edadRelativa,sexoc,detalleOjo) values(?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($IDcategoria,$cateDesc,$grupoEdad,$reproductiva,$ordenCate,$edadRelativa,$sexoc,$detalleOjo));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: categoria_index.php');*/
                /*exit;*/
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
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">categor&iacute;a</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRcategoria" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <div class="form-group ">
                        <label for="IDcategoria">ID de la categor&iacute;a (c&oacute;digo)</label>
                            <input type="text" class="form-control input-sm" style="width: 60px" id="IDcategoria" name="IDcategoria"   pattern="<?php echo PATRON_IDcategoria;?>" maxlength="4" required  data-error="<?php echo PATRON_IDcategoria_men;?>" value="<?php echo !empty($IDcategoria)?$IDcategoria:'';?>" >
                            <div class="help-block with-errors">Letra may&uacute;scula seguida de hasta 3 letras y/o d&iacute;gitos</div>
                            <p id="JSErrorIDcategoria"></p>
                            <?php if (!empty($IDcategoriaError)): ?>
                                <span class="help-inline"><?php echo $IDcategoriaError;?></span>
                            <?php endif; ?>
                    </div>

                    <div class="form-group ">
                        <label for="cateDesc">Descripci&oacute;n</label>
                            <input type="text" class="form-control input-sm" id="cateDesc" name="cateDesc"  oninput="mayus(this);" pattern="<?php echo PATRON_cateDesc;?>" maxlength="30" required  data-error="<?php echo PATRON_cateDesc_men;?>" value="<?php echo !empty($cateDesc)?$cateDesc:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcateDesc"></p>
                            <?php if (!empty($cateDescError)): ?>
                                <span class="help-inline"><?php echo $cateDescError;?></span>
                            <?php endif; ?>
                    </div>

			<div class=row>

				<div class="col-sm-6">
			
                    <div class="form-group ">
                        <label for="grupoEdad">Grupo de edad</label>
                            <select class="form-control input-sm" style="width: auto" id="tipo" required  data-error="Seleccionar un elemento de la lista" name="grupoEdad">
                                <option value=""  <?php if ($grupoEdad == "") {echo " selected";}?>  ></option>
                                <option value="PUP" <?php if ($grupoEdad == "PUP") {echo " selected";}?> >PUP</option>
                                <option value="YEARLING-JUVENIL" <?php if ($grupoEdad == "YEARLING-JUVENIL") {echo " selected";}?> >YEARLING-JUVENIL</option>
                                <option value="SUBADULTO" <?php if ($grupoEdad == "SUBADULTO") {echo " selected";}?> >SUBADULTO</option>
                                <option value="SUBADULTO-ADULTO" <?php if ($grupoEdad == "SUBADULTO-ADULTO") {echo " selected";}?>  >SUBADULTO-ADULTO</option>
                                <option value="ADULTO" <?php if ($grupoEdad == "ADULTO") {echo " selected";}?> >ADULTO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorgrupoEdad"></p>
                            <?php if (!empty($grupoEdadError)): ?>
                                <span class="help-inline"><?php echo $grupoEdadError;?></span>
                            <?php endif; ?>
                            

                    </div>					

				</div>					
					
				<div class="col-sm-6">

                    <div class="form-group ">
                        <label for="reproductiva">Es categor&iacute;a reproductiva?</label>
                            <select class="form-control input-sm" style="width: auto" id="reproductiva" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="reproductiva">
                                <option value="" <?php if ($reproductiva == "") {echo " selected";}?> ></option>
                                <option value="SI" <?php if ($reproductiva == "SI") {echo " selected";}?> >SI</option>
                                <option value="NO" <?php if ($reproductiva == "NO") {echo " selected";}?> >NO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorreproductiva"></p>
                            <?php if (!empty($reproductivaError)): ?>
                                <span class="help-inline"><?php echo $reproductivaError;?></span>
                            <?php endif; ?>
                    </div>
				</div>					
					

			</div>
			<div class=row>
					
				<div class="col-sm-4">

                    <div class="form-group ">
                        <label for="ordenCate">Orden</label>
                            <input type="text" class="form-control input-sm" style="width:105px" id="ordenCate" name="ordenCate" onblur="tdecim(this, 1);" data-pdecimal 
							data-dmin="<?php echo CONST_ordenCate_min?>" data-dmax="<?php echo CONST_ordenCate_max?>"   required  
							data-error="<?php echo CONST_ordenCate_men?>"  value="<?php echo !empty($ordenCate)?$ordenCate:'';?>" >
                            <div class="help-block with-errors">en el pool general</div>
                            <p id="JSErrorordenCate"></p>
                            <?php if (!empty($ordenCateError)): ?>
                                <span class="help-inline"><?php echo $ordenCateError;?></span>
                            <?php endif; ?>
                    </div>

				</div>					
					
				<div class="col-sm-4">

                    <div class="form-group ">
                        <label for="edadRelativa">Edad relativa</label>
                            <input type="text" class="form-control input-sm" style="width:105px" id="edadRelativa" name="edadRelativa" onblur="tdecim(this, 1);" data-pdecimal 
							data-dmin="<?php echo CONST_edadRelativa_min?>" data-dmax="<?php echo CONST_edadRelativa_max?>"   required  
							data-error="<?php echo CONST_edadRelativa_men?>" value="<?php echo !empty($edadRelativa)?$edadRelativa:'';?>" >
                            <div class="help-block with-errors">a la que corresponde</div>
                            <p id="JSErroredadRelativa"></p>
                            <?php if (!empty($edadRelativaError)): ?>
                                <span class="help-inline"><?php echo $edadRelativaError;?></span>
                            <?php endif; ?>
                    </div>
				</div>					
				<div class="col-sm-4">
					
                    <div class="form-group ">
                        <label for="sexoc">Sexo a la que corresponde</label>
                            <select class="form-control input-sm" style="width: auto" id="sexoc" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="sexoc">
                                <option value="" <?php if ($sexoc == "") {echo " selected";}?> ></option>
                                <option value="HEMBRA" <?php if ($sexoc == "HEMBRA") {echo " selected";}?> >HEMBRA</option>
                                <option value="MACHO" <?php if ($sexoc == "MACHO") {echo " selected";}?> >MACHO</option>
                                <option value="CUALQUIERA" <?php if ($sexoc == "CUALQUIERA") {echo " selected";}?> >CUALQUIERA</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorsexoc"></p>
                            <?php if (!empty($sexocError)): ?>
                                <span class="help-inline"><?php echo $sexocError;?></span>
                            <?php endif; ?>
                    </div>

				</div>						

			</div>
			
			
			<div class=row>

				<div class="col-sm-5">
					
                    <div class="form-group ">
                        <label for="detalle">La clasificaci&oacute;n es</label>
							<ul>
							  <li>precisa: solo una categor&iacute;a</li>
							  <li>grupal: un rango de categor&iacute;as</li>
							</ul>
                    </div>

				</div>							
							
		
				<div class="col-sm-5">
					
                    <div class="form-group ">
                        <label for="detalleOjo"></label>
                            <select class="form-control input-sm" style="width: auto" id="detalleOjo" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="detalleOjo">
                                <option value="" <?php if ($detalleOjo == "") {echo " selected";}?> ></option>
                                <option value="precisa" <?php if ($detalleOjo == "precisa") {echo " selected";}?> >precisa</option>
                                <option value="grupo" <?php if ($detalleOjo == "grupo") {echo " selected";}?> >grupal</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrordetalleOjo"></p>
                            <?php if (!empty($detalleOjoError)): ?>
                                <span class="help-inline"><?php echo $detalleOjoError;?></span>
                            <?php endif; ?>
                    </div>

				</div>						
                    
			</div>
                    
                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("categoria_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

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
    pdecimal: function($el) {
        if ($el.val().length>0){
            if (isNaN($el.val())) {
               return $el.data("error");
               }
            var dnum = Number($el.val());
            var dmin = Number($el.data("dmin"));
            var dmax = Number($el.data("dmax"));
            if ((dmin>dnum) || (dnum>dmax)) {
                 return $el.data("error");
                 }
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