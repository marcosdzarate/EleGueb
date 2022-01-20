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



    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

    <link rel="stylesheet" href="login/style/main.css">
	
	
	
    <link   href="css/miSideBar.css" rel="stylesheet">

	
	
<script src="js/imiei.js"></script>



	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	


</head>

<body  class=bodycolor >
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_publicaciones.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



	
    <div class="container"  style="text-align:left;width:500px">


    </div> <!-- /container -->



 <script src="js/w3.js"></script> 
 
<script>
w3.includeHTML(menuCB);
function menuCB() {
	
   setTimeout(function(){document.getElementById('botonmenu').click();},100);
   
}
</script> 
	
  </body>


</html>