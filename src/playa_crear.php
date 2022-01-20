<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    $par_elimina=1;

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	
        $queTabla = 'playa';

        $IDplaya = null;
        $tipo = null;
        $nombre = null;
        $norteSur = null;
        $geomTex = null;
        $comentario = null;


    if ( !empty($_POST)) {
        // para errores de validacion
        $IDplayaError = null;
        $tipoError = null;
        $nombreError = null;
        $norteSurError = null;
        $geomTexError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $IDplaya = limpia($_POST['IDplaya']);
        $tipo = limpia($_POST['tipo']);
        $nombre = limpia($_POST['nombre']);
        $norteSur = limpia($_POST['norteSur']);
        $geomTex = limpia($_POST['geomTex']);
        $comentario =  limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $IDplayaError = validar_IDplaya ($IDplaya,$valid,true);
        $tipoError = validar_tipo ($tipo,$valid,true);
        $nombreError = validar_nombre ($nombre,$valid,true);
        $norteSurError = validar_norteSur ($norteSur,$valid,true);
        $geomTexError = validar_geomTex ($geomTex,$valid,false);

    // validacion entre campos
        $eError = validarForm_playa ($IDplaya,$tipo,$nombre,$norteSur,$geomTex,$comentario,$valid);

        // nuevo registro
        if ($valid) {
            // campos que pueden tener NULL
            $SQLgeomTex = ($geomTex == '' ? NULL : $geomTex);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO playa (IDplaya,tipo,nombre,norteSur,geomTex,comentario) values(?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($IDplaya,$tipo,$nombre,$norteSur,$SQLgeomTex,$SQLcomentario));

            $arr = $q->errorInfo();

			
			
            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: playa_index.php'); */
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
                <h3 class="panel-title">playa o tramo</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRplaya" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">


            <div class="row">               
                <div class="col-sm-6">              
				
                    <div class="form-group ">
                        <label for="IDplaya">ID de playa (c&oacute;digo)</label>
                            <input type="text" class="form-control input-sm" style="width: 75px" id="IDplaya" name="IDplaya"  oninput="mayus(this);" maxlength="5" required  pattern="<?php echo PATRON_IDplaya?>" data-error="<?php echo PATRON_IDplaya_men?>" value="<?php echo !empty($IDplaya)?$IDplaya:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorIDplaya"></p>
                            <?php if (!empty($IDplayaError)): ?>
                                <span class="help-inline"><?php echo $IDplayaError;?></span>
                            <?php endif; ?>
                    </div>
				</div>               
                <div class="col-sm-6">              

                    <div class="form-group ">
                        <label for="tipo">Tipo</label>
                            <select class="form-control input-sm" style="width: auto" id="tipo" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="tipo">
                                <option value="" <?php if ($tipo == "") {echo " selected";}?> ></option>
                                <option value="PUNTO" <?php if ($tipo == "PUNTO") {echo " selected";}?> >PUNTO</option>
                                <option value="TRAMO" <?php if ($tipo == "TRAMO") {echo " selected";}?> >TRAMO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipo"></p>
                            <?php if (!empty($tipoError)): ?>
                                <span class="help-inline"><?php echo $tipoError;?></span>
                            <?php endif; ?>
                    </div>
                </div>              
			</div>


            <div class="row">               
                <div class="col-sm-9">              

                    <div class="form-group ">
                        <label for="nombre">Nombre</label>
                            <input type="text" class="form-control input-sm" id="nombre" name="nombre"  oninput="mayus(this);" pattern="<?php echo PATRON_nombrePlaya?>" data-error="<?php echo PATRON_nombrePlaya_men?>" maxlength="60" required value="<?php echo !empty($nombre)?$nombre:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrornombre"></p>
                            <?php if (!empty($nombreError)): ?>
                                <span class="help-inline"><?php echo $nombreError;?></span>
                            <?php endif; ?>
                    </div>
				</div>               
                <div class="col-sm-3">              

                    <div class="form-group ">
                        <label for="norteSur">Orden Norte-Sur</label>
                            <input type="text" class="form-control input-sm" style="width:105px" id="norteSur" name="norteSur" onblur="tdecim(this, 2);" data-pdecimal 
							data-dmin="<?php echo CONST_norteSur_min;?>" 
							data-dmax="<?php echo CONST_norteSur_max;?>"   required  
							data-error="<?php echo CONST_norteSur_men;?>" value="<?php echo !empty($norteSur)?$norteSur:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrornorteSur"></p>
                            <?php if (!empty($norteSurError)): ?>
                                <span class="help-inline"><?php echo $norteSurError;?></span>
                            <?php endif; ?>
                    </div>
				</div>
			</div>
                    <div class="form-group ">
                        <label for="geomTex">Localizaci&oacute;n: POINT o POLYGON (formatoWKT)</label>
                            <textarea class="form-control input-sm" id="geomTex" name="geomTex" rows="2" maxlength="3000" oninput="mayus(this);" data-patron="<?php echo PATRON_geoPOLYGON_POINT?>" data-error="<?php echo PATRON_geoPOLYGON_POINT_men?>"><?php echo !empty($geomTex)?$geomTex:'';?></textarea> 
                            <div class="help-block with-errors">POINT(lon lat) o POLYGON((lon lat,lon lat...)): punto decimal, hasta 14 decimales</div>
                            <p id="JSErrorgeomTex"></p>
                            <?php if (!empty($geomTexError)): ?>
                                <span class="help-inline"><?php echo $geomTexError;?></span>
                            <?php endif; ?>
                    </div>

                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>
                    </div>

                    <br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("playa_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

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