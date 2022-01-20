<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

$par_elimina=1;

	$v=true;

    $ID = null;
	if (isset($_GET["ID"])) {
		$ID=$_GET["ID"];
		$m = validar_ID ($ID,$v,true);
	}
	else{
			$v=false;
		}
	
	siErrorFuera($v);

     $eError = null;

    if (  ( null==$ID ) ) {
        /* sin parametros, da logout*/
        header("Location: tb_logout.php");
        exit;
    } else {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM publicaciones where  ID =?";
        $q = $pdo->prepare($sql);
        $q->execute(array($ID));
        $data = $q->fetch(PDO::FETCH_ASSOC);

        $arr = $q->errorInfo();

        Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            if (!$data) {
                $eError = "No hay datos para esta condici&oacute;n";
            }
    }
    foreach ($data as &$d){
		$d=htmlspecialchars($d);
	}	
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.css" rel="stylesheet">
	
<style>
.well {
	padding: 5px;
	margin-bottom: 10px;
}
</style>	
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body>
    <div class="container"  style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



    <div class="panel panel-primary">
            <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $data['titulo'];?>
</h3>
            </div>
            <div class="panel-body">


                <div class="row" >
                    <div class="col-sm-2">
                        <label class="control-label">ID: </label>
                        <?php echo $data['ID'];?>
                    </div>

                    <div class="col-sm-6">
                        <label class="control-label">Tipo: </label>
						<?php echo $data['tipoPublicacion'];?>
                    </div>

                    <div class="col-sm-3">
                        <label class="control-label">A&ntilde;o: </label>
                        <?php echo $data['anio'];?>
                    </div>
                </div>

                <div class="well" >
                        <label class="control-label">DOI: </label>
                        <?php echo $data['doi'];?>
                </div>

				<div class="well" >
							<label class="control-label">Autores</label>
							<br>
							<?php echo $data['autores'];?>
                </div>

				<div class="well" >
							<label class="control-label">Abstract, revista, palabras claves....</label>
							<br>
							<?php echo $data['abstractYmas'];?>
				</div>
				
                <div class="row" >
                    <div class="col-sm-3">
                        <label class="control-label">archivo</label>
                    </div>
                    <div class="col-sm-9">
                        <?php echo $data['archivo'];?>
                    </div>
                </div>
				
                <div class="row" >
                    <div class="col-sm-3">
                        <label class="control-label">tipo archivo</label>
                    </div>
                    <div class="col-sm-9">
                        <?php echo $data['tipoArchivo'];?>
                    </div>
                </div>
				

            </div>
        </div>



        <div class="form-actions">
            <a class="btn btn-default btn-sm" onclick=parent.cierroMNO()>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                        <?php endif;?>
        </div>


     </div> <!-- /container -->
  </body>
</html>