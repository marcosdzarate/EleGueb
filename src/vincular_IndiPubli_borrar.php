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

	/* ID de la publicacion IDpublicacion*/
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
    siErrorFuera($v);



    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_claveU = limpia($_POST['pk_claveU']);
        $pk_ID = limpia($_POST['pk_ID']);

        $pdo = Database::connect();
        $sql = "DELETE FROM idpapers WHERE claveU=? AND IDpublicacion=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_claveU,$pk_ID));

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


     $param  = "?claveU=".$pk_claveU."&ID=".$pk_ID;
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
	
<script>
function cerrarTodo(t){
	p=parent.parent.document.getElementById("queHizo");
	if (p!=null){
		p.value=t;
	}
	parent.cierroMiniNO();
	if(t!=''){
		parent.parent.cierroMNO();   /* cuando hay vinculo eliminado */
		if(p!=null){		
			parent.parent.vinculadas(document.getElementById("pk_claveU").value,"");
		}
	}
	if (p==null){  /* cuando borra desde lista */
		parent.parent.reDibu();
	}
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
	
</head>

<body>
    <div class="container" style="width:100%">
	
	
<button type="button" class="botonayuda" style="top:100px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_claveU" id="pk_claveU" value="<?php echo $pk_claveU;?>"/>
                    <input type="hidden" name="pk_ID" value="<?php echo $pk_ID;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="form-actions">
						&nbsp;&nbsp;
                            <button type="submit" class="btn btn-danger btn-sm">Si</button><span style="display:inline-block; width: 150px;"></span>
                            <a class="btn btn-default btn-sm" onclick="cerrarTodo('')">No</a>
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
							<script>
							parent.titux.innerHTML = " ";
							</script>
                            <div class="alert alert-success"><h5>V&iacute;nculo eliminado</h5></div>
                            <a class="btn btn-default btn-sm" onclick="cerrarTodo('borro')";>atr&aacute;s</a>
                        <?php endif;?>

                </form>

    </div>

  </body>
</html>