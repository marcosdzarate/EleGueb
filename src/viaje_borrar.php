<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_claveU = null;
    if (isset($_GET["claveU"])) {
        $pk_claveU=$_GET["claveU"];
        $m = validar_claveU ($pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["pk_claveU"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

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
    siErrorFuera($v);

    $pk_etapa = null;
    if (isset($_GET["etapa"])) {
        $pk_etapa=$_GET["etapa"];
        $m = validar_etapa ($pk_etapa,$v,true);
    }
    else{
        if (!isset($_GET["etapa"]) and !isset($_POST["pk_etapa"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);	


    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_claveU = limpia($_POST['pk_claveU']);
        $pk_fecha = limpia($_POST['pk_fecha']);
        $pk_etapa = limpia($_POST['pk_etapa']);

        $pdo = Database::connect();
		
        $sql = "DELETE FROM viaje WHERE claveU=? AND fecha=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_claveU,$pk_fecha));

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


     $param  = "?claveU=".$pk_claveU."&fecha=".$pk_fecha;
     $param0 = "?claveU=".$pk_claveU;



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
	
	
<button type="button" class="botonayuda" style="width:34px;top:0px;left:-10px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
	
                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_claveU" value="<?php echo $pk_claveU;?>"/>
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_etapa" value="<?php echo $pk_etapa;?>"/>

                    <BR>

                     <?php if ($borrado=='no'): ?>
					    <?php if ($pk_etapa=='COLOCACION') :?>
						      <h4> OJO, es etapa de COLOCACION: se eliminan tambi&eacute;n la configuraci&oacute;n de par&aacute;metros medidos e instrumentos </h4><br>
                        <?php endif;?>
						    <BR>
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
							<br><br><br><br>
                            <div class="alert alert-success"><h5>registro eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroMiniSI("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>
                        <?php endif;?>

                </form>

    </div>

  </body>
</html>