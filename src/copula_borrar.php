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

    $pk_hora = null;
    if (isset($_GET["hora"])) {
        $pk_hora=$_GET["hora"];
        $m = validar_hora ($pk_hora,$v,true);
    }
    else{
        if (!isset($_GET["hora"]) and !isset($_POST["pk_hora"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $xtrx = null;  /* sexo */
    if (isset($_GET["xtrx"])) {
        $xtrx=$_GET["xtrx"];
        $v = ($xtrx=="HEMBRA" or $xtrx=="MACHO");
    }
    else{
        if (!isset($_GET["xtrx"]) and !isset($_POST["xtrx"]) )
        {
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
        $pk_hora = limpia($_POST['pk_hora']);

		$xtrx = limpia($_POST['xtrx']); /* sexo */
		
        $pdo = Database::connect();
        $sql = "DELETE FROM copula WHERE claveU=? AND fecha=? AND hora=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_claveU,$pk_fecha,$pk_hora));

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


     $param0 = "?claveU=".$pk_claveU."&fecha=".$pk_fecha;



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
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

    <div class="panel panel-naranjino">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">Eliminar registro de c&oacute;pula para:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ClaveU: <?php echo $pk_claveU;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha: <?php echo $pk_fecha;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hora: <?php echo $pk_hora;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_claveU" value="<?php echo $pk_claveU;?>"/>
                    <input type="hidden" name="pk_fecha" value="<?php echo $pk_fecha;?>"/>
                    <input type="hidden" name="pk_hora" value="<?php echo $pk_hora;?>"/>
					<input name="xtrx" type="hidden"  value="<?php echo !empty($xtrx)?$xtrx:'';?>">

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="lead alert alert-danger-derecha"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" href="copula_index.php<?php echo $param0.'&xtrx='.$xtrx;?>">No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                        <a class="btn btn-default btn-sm" href="copula_index.php<?php echo $param0.'&xtrx='.$xtrx;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>