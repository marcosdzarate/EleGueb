<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/
	
    $pk_fecha = null;
	$v=true;
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
	
    $eError = null;

    $borrado = 'no';

	$nGrupos = 0;
	if (empty($_POST)) {
		/* cantidad de grupos que se borrarian */
        $pdo = Database::connect();
        $sql = "SELECT COUNT(*) FROM grupo WHERE fecha=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha));
        $arr = $q->errorInfo();
        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
				$nGrupos = $q->fetchColumn(0);  /* cantidad de grupos del censo */
				/* intento de eliminar censo que no fue vaciado de grupos */
				$nG=true;
				if($nGrupos>0){
					$nG=false;
				};
				siErrorFuera ($nG); 
			}		
        Database::disconnect();
	}
	
	
	
	

    if ( !empty($_POST)) {
        // elimina registro
        $pk_fecha = limpia($_POST['pk_fecha']);

        $pdo = Database::connect();
        $sql = "DELETE FROM censo WHERE fecha=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha));

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
                  <h3 class="panel-title">BORRAR CENSO PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha en que se hace el censo: <?php echo $pk_fecha;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
						<div class="col-sm-12 lead alert alert-danger-derecha">Se elimina el censo para esta fecha.<BR>Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>

						
                        <div class="form-actions">
							<div class="row">
								<div class="col-sm-6">
									<button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								</div>
								<div class="col-sm-6">																	
									<a class="btn btn-default btn-lg" onclick=parent.cierroM("censo_index.php")>No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php if (!empty($eError)): ?>
										<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
									<?php endif;?>
								</div>
							</div>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>censo eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("censo_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>