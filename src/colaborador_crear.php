<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'colaborador';

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

        $IDcolaborador = null;
        $apellido = null;
        $nombre = null;
        $email = null;
        $comentario = null;


    if ( !empty($_POST)) {
        // para errores de validacion
        $IDcolaboradorError = null;
        $apellidoError = null;
        $nombreError = null;
        $emailError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $IDcolaborador = limpia($_POST['IDcolaborador']);
        $apellido = limpia($_POST['apellido']);
        $nombre = limpia($_POST['nombre']);
        $email = limpia($_POST['email']);
        $comentario =  limpia($_POST['comentario']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $IDcolaboradorError = validar_IDcolaborador ($IDcolaborador,$valid,true);
        $apellidoError = validar_apellido ($apellido,$valid,false);
        $nombreError = validar_nombre ($nombre,$valid,false);
        $emailError = validar_email ($email,$valid,false);

    // validacion entre campos
        $eError = validarForm_colaborador ($IDcolaborador,$apellido,$nombre,$email,$comentario,$valid);

        // nuevo registro
        if ($valid) {
            // campos que pueden tener NULL
            $SQLapellido = ($apellido == '' ? NULL : $apellido);
            $SQLnombre = ($nombre == '' ? NULL : $nombre);
            $SQLemail = ($email == '' ? NULL : $email);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO colaborador (IDcolaborador,apellido,nombre,email,comentario) values(?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($IDcolaborador,$SQLapellido,$SQLnombre,$SQLemail,$SQLcomentario));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: colaborador_index.php'); */
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
                <h3 class="panel-title">participante</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRcolaborador" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <div class="form-group ">
                        <label for="IDcolaborador">ID del colaborador (c&oacute;digo)</label>
                            <input type="text" required class="form-control input-sm" style="width: 80px" id="IDcolaborador" name="IDcolaborador"  oninput="mayus(this);" pattern="<?php echo PATRON_IDcolaborador;?>" maxlength="3"   data-error="<?php echo PATRON_IDcolaborador_men;?>" value="<?php echo !empty($IDcolaborador)?$IDcolaborador:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorIDcolaborador"></p>
                            <?php if (!empty($IDcolaboradorError)): ?>
                                <span class="help-inline"><?php echo $IDcolaboradorError;?></span>
                            <?php endif; ?>
                    </div>


            <div class="row">               
                <div class="col-sm-6">      										

                    <div class="form-group ">
                        <label for="apellido">Apellido</label>
                            <input type="text" class="form-control input-sm" id="apellido" name="apellido"  oninput="mayus(this);" pattern="<?php echo PATRON_apeYnom;?>" maxlength="45"   data-error="<?php echo PATRON_apeYnom_men;?>" value="<?php echo !empty($apellido)?$apellido:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorapellido"></p>
                            <?php if (!empty($apellidoError)): ?>
                                <span class="help-inline"><?php echo $apellidoError;?></span>
                            <?php endif; ?>
                    </div>
                </div>


                <div class="col-sm-6"> 
                    <div class="form-group ">
                        <label for="nombre">Nombre</label>
                            <input type="text" class="form-control input-sm" id="nombre" name="nombre" required oninput="mayus(this);" pattern="<?php echo PATRON_apeYnom;?>" maxlength="45"  data-error="<?php echo PATRON_apeYnom_men;?>" value="<?php echo !empty($nombre)?$nombre:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrornombre"></p>
                            <?php if (!empty($nombreError)): ?>
                                <span class="help-inline"><?php echo $nombreError;?></span>
                            <?php endif; ?>
                    </div>
                </div>
            </div>

                    <div class="form-group ">
                        <label for="email">e-mail</label>
                            <textarea class="form-control input-sm" id="email" name="email" rows="1" maxlength="100"     data-patron="<?php echo PATRON_email;?>" data-error="<?php echo PATRON_email_men;?>"><?php echo !empty($email)?$email:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErroremail"></p>
                            <?php if (!empty($emailError)): ?>
                                <span class="help-inline"><?php echo $emailError;?></span>
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

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("colaborador_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

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
  }
});
</script>

            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>