<?php
session_start();
// remover todas las vars
session_unset();

// destroy la sesion
session_destroy();

require_once 'tb_dbconecta.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <link   href="css/bootstrap.css" rel="stylesheet">
</head>

<body>
<br><br><br><br>
<H2 style="text-align:center"><?php echo Grupete?></H2>
    <div class="container" style="width:50%">
     
						<div class="jumbotron">
							<p></p>
							<h2>Sesi&oacute;n inactiva por m&aacute;s de 30 minutos.</h2>
							<h2><a class=vincu target="_parent" href="login/index.php">Reiniciar sesi&oacute;n</a></h2>
							<p></p>
						  </div>					 
 

    </div> <!-- /container -->



</body>
</html>
 
