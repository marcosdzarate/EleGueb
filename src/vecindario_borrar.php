<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    $par_elimina=2;

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/	
 	$v=true;
   $pk_IDesquema = null;	
	if (isset($_GET["IDesquema"])) {
		$pk_IDesquema=$_GET["IDesquema"];
		$m = validar_IDesquema ($pk_IDesquema,$v,true);
	}
	else{
		if (!isset($_GET["IDesquema"]) and !isset($_POST["pk_IDesquema"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);

   $pk_IDvecindario = null;	
	if (isset($_GET["IDvecindario"])) {
		$pk_IDvecindario=$_GET["IDvecindario"];
		$m = validar_IDvecindario ($pk_IDvecindario,$v,true);
	}
	else{
		if (!isset($_GET["IDvecindario"]) and !isset($_POST["pk_IDvecindario"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);


    $eError = null;

    $borrado = 'no';

    if ( !empty($_POST)) {
        // elimina registro
        $pk_IDesquema = limpia($_POST['pk_IDesquema']);
        $pk_IDvecindario = limpia($_POST['pk_IDvecindario']);

        $pdo = Database::connect();
        $sql = "DELETE FROM vecindario WHERE IDesquema=? AND IDvecindario=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_IDesquema,$pk_IDvecindario));

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


    <div class="panel panel-grisDark">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR REGISTRO/S PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IDesquema: <?php echo $pk_IDesquema;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IDvecindario: <?php echo $pk_IDvecindario;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_IDesquema" value="<?php echo $pk_IDesquema;?>"/>
                    <input type="hidden" name="pk_IDvecindario" value="<?php echo $pk_IDvecindario;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="alert alert-danger"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("vecindario_index.php")>No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("vecindario_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>