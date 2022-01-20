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

    $pk_tag = null;
    if (isset($_GET["tag"])) {
        $pk_tag=$_GET["tag"];
        $m = validar_tag ($pk_tag,$v,true);
    }
    else{
        if (!isset($_GET["tag"]) and !isset($_POST["pk_tag"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);


    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_tag = limpia($_POST['pk_tag']);
        $pk_claveU = limpia($_POST['pk_claveU']);
        $pk_fecha = limpia($_POST['pk_fecha']);

        $pdo = Database::connect();
        $sql = "DELETE FROM tag WHERE tag=? AND claveU=? AND fecha=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_tag,$pk_claveU,$pk_fecha));

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
        
   $param0="?claveU=".$pk_claveU."&fecha=".$pk_fecha;
        
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
                  <h3 class="panel-title">Eliminar el siguiente tag:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tag: <?php echo $pk_tag;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ClaveU: <?php echo $pk_claveU;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha: <?php echo $pk_fecha;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_tag" value="<?php echo $pk_tag;?>"/>
                    <input type="hidden" name="pk_claveU" value="<?php echo $pk_claveU;?>"/>
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="lead alert alert-danger-derecha"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" href="tag_index.php<?php echo $param0;?>">No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                            <a class="btn btn-default btn-sm" href="tag_index.php<?php echo $param0;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>