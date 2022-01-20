<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

     require_once 'tb_validar.php';

    $par_elimina=1;

	/* TODOS PUEDEN BORRAR PUBLICACIONES /*
	/*sin permiso de edición, fuera*/
	/*siErrorFuera(edita());   */

	/*parametros inválidos, fuera*/	
	$v=true;
    $pk_ID = null;
	if (isset($_GET["ID"])) {
		$pk_ID=$_GET["ID"];
		$m = validar_ID ($pk_ID,$v,true);
	}
	else{
		if (!isset($_GET["ID"]) and !isset($_POST["pk_ID"]) ){
			$v=false;
		}
	}
	siErrorFuera ($v);	


    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_ID = limpia($_POST['pk_ID']);
        $titulo = limpia($_POST['titulo']);
        $archivo = limpia($_POST['archivo']);
		
		/* borro archivo */
		$au= dirPublicaciones.$archivo;
		unlink($au);
        $pdo = Database::connect();
        $sql = "DELETE FROM publicaciones WHERE ID=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_ID));

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
	else{
            $pdo = Database::connect();		
            $sql = "SELECT titulo,archivo FROM publicaciones where ID=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_ID));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                      }
                   else{
						$data = $q->fetch(PDO::FETCH_ASSOC);					   
						$titulo = $data['titulo'];
						$archivo = $data['archivo'];					   					   

                       Database::disconnect();
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

    <div class="panel panel-grisDark">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">BORRAR REGISTRO/S PARA:</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID : <?php echo $pk_ID;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;titulo : <?php echo $titulo;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_ID" value="<?php echo $pk_ID;?>"/>
                    <input type="hidden" name="archivo" value="<?php echo $archivo;?>"/>
                    <input type="hidden" name="titulo" value="<?php echo $titulo;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="alert alert-danger-derecha"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("publicaciones_index.php")>No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>registro eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("publicaciones_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                   
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>