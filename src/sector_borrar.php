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



    $eError = null;

    $borrado = 'no';

	if (empty($_POST)) {
		/* cantidad de grupos que se borrarian */
        $pdo = Database::connect();
        $sql = "SELECT COUNT(*) FROM grupo WHERE fecha=? AND libreta=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta));
        $arr = $q->errorInfo();
        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
				$nGrupos = $q->fetchColumn(0);  /* cantidad de grupos en el sector */
			}

		/* constraint con sector_copiado??? */
		$sql = "SELECT GROUP_CONCAT(CONCAT('fecha: ',fecha,' libreta:',libreta,'<br>') separator '') as fl_ori FROM sector_copiado WHERE fecha_copia=?  AND libreta_copia=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta));
        $arr = $q->errorInfo();
        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
				$secCopia = $q->fetchColumn(0);  /* fecha y libreta sector copiado */
				if(!empty($secCopia)) {
					$borrado = 'copia';
				}
			}
			
        Database::disconnect();
	}

    if ( !empty($_POST)) {
        // elimina registro
        $pk_fecha = limpia($_POST['pk_fecha']);
        $pk_libreta = limpia($_POST['pk_libreta']);

        $pdo = Database::connect();
        $sql = "DELETE FROM sector WHERE fecha=? AND libreta=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_fecha,$pk_libreta));

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
	
	
<button type="button" class="botonayuda" style="top:100px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



	<div class="panel panel-primary">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR SECTOR COMPLETO PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha del censo: <?php echo $pk_fecha;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Libreta: <?php echo $pk_libreta;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_libreta" value="<?php echo $pk_libreta;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='copia'): ?>
					 <div class="well"><h4>Los datos de este sector se est&aacute;n usando en los siguientes sectores copiados:<BR><BR><small><?php echo $secCopia ?></small><BR> Para borrarlo, primero eliminar los sectores copiados.</h4></div>					 
										<div class="col-sm-6">																	
											<a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>atr&aacute;s</a>
										</div>
                     <?php elseif ($borrado=='no'): ?>
						 <div class="col-sm-12 lead alert alert-danger-derecha">Se elimina la libreta completa: los datos del sector y de los <STRONG><?php echo $nGrupos?></STRONG> grupos de individuos descriptos.<BR>Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
						 
								<div class="form-actions"><br><br>
									<div class="row">
										<div class="col-sm-6">
											<button type="submit" class="btn btn-danger btn-sm">Si</button>
										</div>
										<div class="col-sm-6">																	
											<a class="btn btn-default btn-lg" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>No</a>
											<?php if (!empty($eError)): ?>
												<span class="alert alert-danger-derecha"><?php echo $eError;?></span>
											<?php endif;?>
										</div>
									</div>
								</div>

							<?php else: ?>
							
								<div class="alert alert-success"> <h5>registro eliminado</h5></div>
								<a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>