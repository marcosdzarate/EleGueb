<?php
    require_once 'tb_sesion_aca.php';
    require_once '../tb_dbconecta.php';
    require_once '../tb_database.php';
    require_once '../tb_validar.php';

	/* sin permiso de administrador, fuera*/
	siErrorFuera(es_administrador());
		
    $par_elimina=1;
    require_once '../tb_param.php';

	require_once ('correito.php');

	/*parametros inválidos, fuera*/
 	$v=true;
    $pk_memberID = null;	
	if (isset($_GET["memberID"])) {
		$pk_memberID=$_GET["memberID"];
		$m = validar_memberID ($pk_memberID,$v,true);
	}
	else{
		if (!isset($_GET["memberID"]) and !isset($_POST["pk_memberID"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);
	
    $username = null;	
	if (isset($_GET["username"])) {
		$username=$_GET["username"];
		$m = validar_userpass ($username,$v,true);
	}
	else{
		if (!isset($_GET["username"]) and !isset($_POST["username"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);

	
    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_memberID = limpia($_POST['pk_memberID']);
        $username = limpia($_POST['username']);

		
        $pdo = Database::connect();
        $sql = "DELETE FROM members WHERE memberID=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_memberID));

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
    <link   href="../css/bootstrap.css" rel="stylesheet">
    <script src="../js/bootstrap.js"></script>
</head>

<body>
    <div class="container"  style="width:80%">

    <div class="panel panel-azulino">
            <div class="panel-heading">
                <BR>
                  <h3 class="panel-title">ELIMINAR CUENTA PARA :</h3> <BR>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ID: <?php echo $pk_memberID;?></h3>
                  <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Usuario: <?php echo $username;?></h3>
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_memberID" value="<?php echo $pk_memberID;?>"/>
                    <input type="hidden" name="username" value="<?php echo $username;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="alert alert-danger-derecha"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-danger btn-sm">Si</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("usuarios_index.php")>No</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><?php echo $eError;?></span>
                            <?php endif;?>
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> El registro fue eliminado</div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("usuarios_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>