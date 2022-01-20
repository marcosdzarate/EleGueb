<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'recuento';
		
	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/
	$v=true;
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
	siErrorFuera ($v);
		
	if (isset($_GET["fecha"])) {
		/* intento de eliminar censo fuera del flujo programado */
		siErrorFuera (editaCenso($pk_fecha));
	}	
	
	
    $pk_libreta = null;
	if (isset($_GET["libreta"])) {
		$pk_libreta=$_GET["libreta"];
		$m = validar_libreta ($pk_libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["pk_libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);

    $pk_orden = null;
	if (isset($_GET["orden"])) {
		$pk_orden=$_GET["orden"];
		$m = validar_orden ($pk_orden,$v,true);
	}
	else{
		if (!isset($_GET["orden"]) and !isset($_POST["pk_orden"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);

    $pk_categoria = null;
	if (isset($_GET["categoria"])) {
		$pk_categoria=$_GET["categoria"];
		$m = validar_IDcategoria ($pk_categoria,$v,true);
	}
	else{
		if (!isset($_GET["categoria"]) and !isset($_POST["pk_categoria"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);	
	
    $pk_sexo = null;
	if (isset($_GET["sexo"])) {
		$pk_sexo=$_GET["sexo"];
		$m = validar_sexo ($pk_sexo,$v,true);
	}
	else{
		if (!isset($_GET["sexo"]) and !isset($_POST["pk_sexo"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);
	

    $pk_status = null;
	if (isset($_GET["status"])) {
		$pk_status=$_GET["status"];
		$m = validar_status ($pk_status,$v,true);
	}
	else{
		if (!isset($_GET["status"]) and !isset($_POST["pk_status"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);


    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_fecha = limpia($_POST['pk_fecha']);
        $pk_libreta = limpia($_POST['pk_libreta']);
        $pk_orden = limpia($_POST['pk_orden']);
        $pk_categoria = limpia($_POST['pk_categoria']);
        $pk_sexo = limpia($_POST['pk_sexo']);
        $pk_status = limpia($_POST['pk_status']);

        $pdo = Database::connect();
        $sql = "DELETE FROM recuento WHERE fecha=? AND libreta=? AND orden=? AND categoria=? AND sexo=? AND status=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta,$pk_orden,$pk_categoria,$pk_sexo,$pk_status));

        $arr = $q->errorInfo();

		$rc = $q->rowCount();

        
        Database::disconnect();

        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            else {
				if ($rc==0) { 
					$eError="Nada para eliminar";
				}
				else {
                    $borrado = 'si';
					/* actualizo conformadoOK en grupo */ 
					$pdo = Database::connect();
					$sql = "UPDATE grupo SET conformadoOK=VerificaConformacionGrupo(?,?,?,?) WHERE fecha=? and libreta=? and orden=?";
					$q = $pdo->prepare($sql);
					$q->execute(array($pk_fecha,$pk_libreta,$pk_orden,$_SESSION['referencia'],$pk_fecha,$pk_libreta,$pk_orden));

					$arr = $q->errorInfo();

					Database::disconnect();
					if ($arr[0] <> '00000') {
						$eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
						}					
				}
			}
    }

	?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body>
    <div class="container"  style="width:80%">
	
	
<button type="button" class="botonayuda" style="top:100px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


	
         <div class="panel panel-primary">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR REGISTRO/S PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha del censo: <?php echo $pk_fecha;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Libreta: <?php echo $pk_libreta;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Orden: <?php echo $pk_orden;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Categor&iacute;a: <?php echo $pk_categoria;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sexo: <?php echo $pk_sexo;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status macho: <?php echo $pk_status;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_libreta" value="<?php echo $pk_libreta;?>"/>
                    <input type="hidden" name="pk_orden" value="<?php echo $pk_orden;?>"/>
                    <input type="hidden" name="pk_categoria" value="<?php echo $pk_categoria;?>"/>
                    <input type="hidden" name="pk_sexo" value="<?php echo $pk_sexo;?>"/>
                    <input type="hidden" name="pk_status" value="<?php echo $pk_status;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="col-sm-12 alert alert-danger-derecha"><h4> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</h4></div>
                        <div class="form-actions">
							<div class="row">
								<div class="col-sm-6">						
									<button type="submit" class="btn btn-danger btn-sm">Si</button>
								</div>
								<div class="col-sm-6">
									<a class="btn btn-default btn-lg" href="recuento_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>&orden=<?php echo $pk_orden;?>">No</a>
										<?php if (!empty($eError)): ?>
											<span class="alert alert-danger-derecha"><?php echo $eError;?></span>
										<?php endif;?>
								</div>
							</div>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                        <a class="btn btn-default btn-sm" href="recuento_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>&orden=<?php echo $pk_orden;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>