<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

     require_once 'tb_validar.php';

    $par_elimina=1;
	$dir_imagenes=dirImagenes;

	/*sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/	
	$v=true;
    $pk_archivo = null;
	if (isset($_GET["archivo"])) {
		$pk_archivo=$_GET["archivo"];
		$m = validar_archivo ($pk_archivo,$v,true);
	}
	else{
		if (!isset($_GET["archivo"]) and !isset($_POST["pk_archivo"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);	


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
        $m = validar_fecha ($pk_fecha,$v,false);
    }
    else{
        if (!isset($_GET["fecha"]) and !isset($_POST["pk_fecha"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

	
	
	
	
    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
		if(isset($_POST["botonsi"]) ) {
			// elimina registro
			$pk_archivo = limpia($_POST['pk_archivo']);		
			$pk_claveU = limpia($_POST['pk_claveU']);		
			$pk_fecha = limpia($_POST['pk_fecha']);		
			/* borro archivo */
			$au= dirImagenes.$pk_archivo;
			unlink($au);
			$borrado='si';
		}
    }
     $param  = "?claveU=".$pk_claveU."&fecha=".$pk_fecha;
	 if (empty($pk_fecha)){
		$param  = "?claveU=".$pk_claveU;		 
	 }
	 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>	
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
</head>

<body <?php if($borrado=='si') echo "onload=document.getElementById('cerrame').click()" ?> >
    <div class="container"  style="width:100%">

    <div class="panel panel-grisDark">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title"><?php echo $pk_archivo;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_archivo" value="<?php echo !empty($pk_archivo)?$pk_archivo:'';?>"/>
					<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>">
					<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>">

                    <BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="form-actions">
                            <button type="submit"  class="btn btn-danger btn-sm" name="botonsi" id="botonsi" >Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" onclick=parent.cierroMiniNO("")>No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                            <button id="cerrame" class="btn btn-default btn-sm" onclick=parent.cierroMini("fotos.php<?php echo $param ?>")>atr&aacute;s</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                   
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>