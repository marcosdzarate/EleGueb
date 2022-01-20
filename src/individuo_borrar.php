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



    $eError = null;

    $borrado = 'no';


    if ( !empty($_POST)) {
        // elimina registro
        $pk_claveU = limpia($_POST['pk_claveU']);

        $pdo = Database::connect();
        $sql = "DELETE FROM individuo WHERE claveU=? ";
        $q = $pdo->prepare($sql);
        $q->execute(array($pk_claveU));

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
					/* elimina imagenes, si hay */
					$au= dirImagenes.$pk_claveU.'*.*';
					array_map( "unlink", glob( $au) );
					
                }
            }
    }


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
    <div class="container"  style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

      <div class="panel panel-danger">
            <div class="panel-heading">
                <BR>
				  <h3 class="panel-title" style="text-align:right">ClaveU: <?php echo $pk_claveU;?></h3> <br>
                  <h3 class="panel-title">ATENCI&Oacute;N: se borrar&aacute;n absolutamente todos los datos en todas las temporadas: el animal desaparece de la base de datos.</h3> <BR>
                  
                <BR>

            </div>
            <div class="panel-body ">

                <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input type="hidden" name="pk_claveU" value="<?php echo $pk_claveU;?>"/>

                    <BR><BR>

                     <?php if ($borrado=='no'): ?>
                        <div class="lead alert alert-danger" style="text-align:center"> Seguro que eso es lo que quer&eacute;s hacer?</div><BR><BR>
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
                        </div>

                        <?php else: ?>
                            <div class="alert alert-success"> <h5>El individuo fue eliminado de la BD</h5></div>
                            <a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_seleccion_indi_index2.php")>a selecci&oacute;n</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                   
                </form>


            </div>
      </div>
    </div> <!-- /container -->
  </body>
</html>