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

    $pk_temporada = null;
    if (isset($_GET["temporada"])) {
        $pk_temporada=$_GET["temporada"];
        $m = validar_temporada ($pk_temporada,$pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["temporada"]) and !isset($_POST["pk_temporada"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_tipoTempo = null;
    if (isset($_GET["tipoTempo"])) {
        $pk_tipoTempo=$_GET["tipoTempo"];
        $m = validar_tipoTempo ($pk_tipoTempo,$v,true);
    }
    else{
        if (!isset($_GET["tipoTempo"]) and !isset($_POST["pk_tipoTempo"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);



    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_claveU = limpia($_POST['pk_claveU']);
        $pk_temporada = limpia($_POST['pk_temporada']);
        $pk_tipoTempo = limpia($_POST['pk_tipoTempo']);

        $pdo = Database::connect();
		
		/* si hay imagenes asociadas a la temporada, reservo las fechas */
		$sql = "SELECT DISTINCT fecha FROM observado WHERE observado.claveU=? and observado.temporada=? and observado.tipoTempo=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($pk_claveU,$pk_temporada,$pk_tipoTempo));
		$ri=$q->rowCount();
		if ($ri<>0) {
			$tfechas=$q->fetchAll(PDO::FETCH_COLUMN, 0);
		}

        $sql = "DELETE FROM temporada WHERE claveU=? AND temporada=? AND tipoTempo=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_claveU,$pk_temporada,$pk_tipoTempo));
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
					if($ri>0){
						/* elimina imagenes, si hay */
						foreach ($tfechas as $tfe) {
							$au= dirImagenes.$pk_claveU."-".str_replace("-","",$tfe).'*.*';
							array_map( "unlink", glob( $au) );
						}
					}
					
                }
            }
    }


     $param = "?claveU=".$pk_claveU."&temporada=".$pk_temporada."&tipoTempo=".$pk_tipoTempo;
     $param0 = "?claveU=".$pk_claveU;



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
				  <h3 class="panel-title" style="text-align:right">ClaveU: <?php echo $pk_claveU;?></h3> <br>
                  <h3 class="panel-title">ATENCI&Oacute;N: Se eliminar&aacute;n todos los registros de observaciones y tareas realizadas durante la temporada <?php echo $pk_temporada;?>-<?php echo $pk_tipoTempo;?>.</h3>                   
                <BR>				

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_claveU" value="<?php echo $pk_claveU;?>"/>
                    <input type="hidden" name="pk_temporada" value="<?php echo $pk_temporada;?>"/>
                    <input type="hidden" name="pk_tipoTempo" value="<?php echo $pk_tipoTempo;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="lead alert alert-warning text-center"> Est&aacute;s seguro de que eso es lo que quer&eacute;s hacer?</div><BR><BR>
                        <div class="form-actions">

						  <div class="row">
							  <div class="col-sm-6 text-center">
								<button type="submit" class="btn btn-danger btn-lg">Si</button>
							  </div>
							  
							  <div class="col-sm-6 text-center">
								<a class="btn btn-default btn-lg" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>No</a>
							  </div>

								<?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><?php echo $eError;?></span>
								<?php endif;?>
                          </div>


                        <?php else: ?>
                            <div class="alert alert-success"> <h5>La temporada <?php echo $pk_temporada;?>-<?php echo $pk_tipoTempo;?> fue eliminada para este individuo</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                    </div>
                </form>


            </div>
        </div>
    </div> <!-- /container -->
  </body>
</html>