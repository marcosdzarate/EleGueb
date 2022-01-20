<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_viajeID = null;
    if (isset($_GET["viajeID"])) {
        $pk_viajeID=$_GET["viajeID"];
        $m = validar_viajeID ($pk_viajeID,$v,true);
    }
    else{
        if (!isset($_GET["viajeID"]) and !isset($_POST["pk_viajeID"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_instrumentoNRO = null;
    if (isset($_GET["instrumentoNRO"])) {
        $pk_instrumentoNRO=$_GET["instrumentoNRO"];
        $m = validar_instrumentoNRO ($pk_instrumentoNRO,$v,true);
    }
    else{
        if (!isset($_GET["instrumentoNRO"]) and !isset($_POST["pk_instrumentoNRO"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_instrumentoRef = null;
    if (isset($_GET["instrumentoRef"])) {
        $pk_instrumentoRef=$_GET["instrumentoRef"];
    }
    else{
        if (!isset($_GET["instrumentoRef"]) and !isset($_POST["pk_instrumentoRef"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);


    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_viajeID = limpia($_POST['pk_viajeID']);
        $pk_instrumentoNRO = limpia($_POST['pk_instrumentoNRO']);
        $pk_instrumentoRef = limpia($_POST['pk_instrumentoRef']);

        $pdo = Database::connect();
        $sql = "DELETE FROM instrumentos_colocados WHERE viajeID=? AND instrumentoNRO=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_viajeID,$pk_instrumentoNRO));

        $arr = $q->errorInfo();

        $rc = $q->rowCount();

        if ($arr[0] <> '00000') {
            $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                if ($rc==0) {
                    $eError="Nada para eliminar";
                }
                else {
					/* en la tabla de instrumentos el instrumentoNRO queda con disponible=SI */
					$borrado = 'si';
					$sql = "UPDATE instrumentos SET disponible='SI' WHERE instrumentoNRO=?";
					$q = $pdo->prepare($sql);
					$q->execute(array($pk_instrumentoNRO));
					$arr = $q->errorInfo();
					if ($arr[0] <> '00000') {
						$eError = "Error MySQL: ".$arr[1]." ".$arr[2];
					}
				Database::disconnect();					
					
                }
            }
    }


     $param0 = "?viajeID=".$pk_viajeID."&instrumentoNRO=".$pk_instrumentoNRO;



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
    <div class="container"  style="width:90%">
	
<button type="button" class="botonayuda" style="top:100px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

    <div class="panel panel-naranjino">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR REGISTRO/S PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID de viaje (c&oacute;digo): <?php echo $pk_viajeID;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero de instrumento: <?php echo $pk_instrumentoNRO;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Instrumento: <?php echo $pk_instrumentoRef;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_viajeID" value="<?php echo $pk_viajeID;?>"/>
                    <input type="hidden" name="pk_instrumentoNRO" value="<?php echo $pk_instrumentoNRO;?>"/>
                    <input type="hidden" name="pk_instrumentoRef" value="<?php echo $pk_instrumentoRef;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="lead alert alert-danger-derecha"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                        <a class="btn btn-default btn-sm" href="instrumentos_colocados_index.php?viajeID=<?php echo $pk_viajeID;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                        <a class="btn btn-default btn-sm" href="instrumentos_colocados_index.php?viajeID=<?php echo $pk_viajeID;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>