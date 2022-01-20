<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_clavePup = null;
    if (isset($_GET["clavePup"])) {
        $pk_clavePup=$_GET["clavePup"];
        $m = validar_claveU ($pk_clavePup,$v,true);
    }
    else{
        if (!isset($_GET["clavePup"]) and !isset($_POST["pk_clavePup"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_clavePup = limpia($_POST['pk_clavePup']);

        $pdo = Database::connect();
        $sql = "DELETE FROM madrehijo WHERE clavePup=?";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_clavePup));

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


     $param0 = "?claveU=".$pk_clavePup;



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>	
		
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
</head>

<body>
    <div class="container" style="width:100%">
	
	
<button type="button" class="botonayuda" style="top:100px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_clavePup" value="<?php echo $pk_clavePup;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="form-actions">
						&nbsp;&nbsp;
                            <button type="submit" class="btn btn-danger btn-sm">Si</button><span style="display:inline-block; width: 150px;"></span>

                            <a class="btn btn-default btn-sm" onclick=parent.cierroMiniNO()>No</a>
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
							<script>
							parent.titux.innerHTML = " ";
							</script>
                            <div class="alert alert-success"><h5>V&iacute;nculo eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroMiniSI("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>
                        <?php endif;?>

                </form>

    </div>

  </body>
</html>