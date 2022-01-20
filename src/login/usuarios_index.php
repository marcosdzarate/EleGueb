<?php
require_once '../tb_dbconecta.php';
require_once '../tb_validar.php';
require_once 'tb_sesion_aca.php';

	siErrorFuera(es_administrador());

require_once '../tb_param.php';


?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">
<link rel="shortcut icon" href="../imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe?></title>
<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">


    <script src="../js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="../css/bootstrap.css" rel="stylesheet">
    <script src="../js/bootstrap.js"></script>
	
  <link rel="stylesheet" href="style/main.css">
	

<script src="../js/imiei.js"></script>


<script type="text/javascript" language="javascript" src="../js/jquery.dataTables.js"></script>


<script type="text/javascript" language="javascript" class="init">

<?php
  if ($_SESSION["permiso"]=="administrar") {
  echo "$(document).ready(function() {
       ini_datatable ('#tablax' , 'usuarios_tb.php?condi=".$condi."',10,0) ;
} ); ";
  }
?>

</script>
</head>

<body class=bodycolor>
<?php
require_once 'tb_barraAdmin.php';
?>
    <div class="container well"  style="width:800px">
            <div class="row">
                <h4>Usuarios del sistema </h4>
            </div>

            <div class="panel panel-azulino">
                <div class="panel-heading">
                    <h3 class="panel-title">lista</h3>
                </div>
                <div class="panel-body">

				
                <?php if ($_SESSION['permiso']=="administrar"): ?>
				
				
                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Usuario</th>
                          <th>e-mail</th>
                          <th>Activo</th>
                          <th>reset completo</th>
                          <th>permiso</th>
						  <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>
					
					<?php else : ?>
						<div class="jumbotron">
							<h1>solo para administradores</h1>
							<p></p>
						  </div>					 
					<?php endif; ?>
					
                </div>
            </div>
                <p>
                    <a title="agregar usuario" onclick=ventanaM("usuarios_nuevo.php<?php echo $param;?>",this.title) class="btn btn-primary btn-sm" >nuevo</a>&nbsp;&nbsp;
                        

                    </p>

    </div> <!-- /container -->


<!-- para ventanas MODAL include mas abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="../js/w3.js"></script>

 <div w3-include-html="../tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

  </body>


</html>