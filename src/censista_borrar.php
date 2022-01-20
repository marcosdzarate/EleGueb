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

    $pk_IDcolaborador = null;
	if (isset($_GET["IDcolaborador"])) {
		$pk_IDcolaborador=$_GET["IDcolaborador"];
		$m = validar_IDcolaborador ($pk_IDcolaborador,$v,true);
	}
	else{
		if (!isset($_GET["IDcolaborador"]) and !isset($_POST["pk_IDcolaborador"]) ){
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
        $pk_IDcolaborador = limpia($_POST['pk_IDcolaborador']);

        $pdo = Database::connect();
        $sql = "DELETE FROM censista WHERE fecha=? AND libreta=? AND IDcolaborador=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta,$pk_IDcolaborador));
        $arr = $q->errorInfo();

		
		$rc = $q->rowCount();

        
        Database::disconnect();

        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
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
                  <h3 class="panel-title">BORRAR REGISTRO/S PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha: <?php echo $pk_fecha;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Libreta: <?php echo $pk_libreta;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Colaborador: <?php echo $pk_IDcolaborador;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_libreta" value="<?php echo $pk_libreta;?>"/>
                    <input type="hidden" name="pk_IDcolaborador" value="<?php echo $pk_IDcolaborador;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="alert alert-warning"><h4>Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</h4></div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a class="btn btn-default btn-sm" href="censista_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>">No</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                            <a class="btn btn-default btn-sm" href="censista_index.php?fecha=<?php echo $pk_fecha;?>&libreta=<?php echo $pk_libreta;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>