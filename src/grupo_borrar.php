<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

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
	
	
    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_fecha = limpia($_POST['pk_fecha']);
        $pk_libreta = limpia($_POST['pk_libreta']);
        $pk_orden = limpia($_POST['pk_orden']);

        $pdo = Database::connect();
        $sql = "DELETE FROM grupo WHERE fecha=? AND libreta=? AND orden=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta,$pk_orden));

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
				}
			}
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>	
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body>
    <div class="container"  style="width:80%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	<div class="panel panel-primary">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR GRUPO COMPLETO PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha del censo: <?php echo $pk_fecha;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Libreta: <?php echo $pk_libreta;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Orden: <?php echo $pk_orden;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_libreta" value="<?php echo $pk_libreta;?>"/>
                    <input type="hidden" name="pk_orden" value="<?php echo $pk_orden;?>"/>

                    <BR><BR>
                     <?php if ($borrado=='no'): ?>
							<div class="col-sm-12 lead alert alert-danger-derecha">Se eliminan la descripci&oacute;n del grupo y la composici&oacute;n del mismo.<BR> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?
							</div>
                        <div class="form-actions">
							<div class="row">
								<div class="col-sm-6">
									<button type="submit" class="btn btn-danger btn-sm">Si</button>
								</div>
								<div class="col-sm-6">							
									<a class="btn btn-default btn-lg" onclick=parent.cierroM("grupo_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>")>No</a>
									<?php if (!empty($eError)): ?>
										<span class="alert alert-danger-derecha"><?php echo $eError;?></span>
									<?php endif;?>
								</div>
							</div>

                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                             <a class="btn btn-default btn-sm" onclick=parent.cierroM("grupo_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>