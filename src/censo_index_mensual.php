<?php
 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';


?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

  <link rel="stylesheet" href="login/style/main.css">

	
	
<script src="js/imiei.js"></script>


	
    <link   href="css/miSideBar.css" rel="stylesheet">




<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       ini_datatable ('#tablax' , 'censo_tb_mensual.php',15,0) ;
} );

</script>


<script src="js/aiuta.js"></script>	
	

</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_censo.html"></div>
	
	
<button type="button" class="botonayuda" style="background-color: #344560" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

    <div class="container well"  style="text-align:left;width:600px">
            <div class="row">
                <h4>Censos MENSUALES de elefantes marinos entre 1995 y 2000</h4>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">censos</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>Fecha censo</th>
                          <th>Tipo</th>
                          <th>Fecha total</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                

    </div> <!-- /container -->


<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script> 
	
  </body>


</html>