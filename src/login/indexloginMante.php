<?php
// originalmene login.php
//include config
require_once('includes/config.php');

	if (!file_exists("AAAAenMantenimiento.xxx")) {
		header("Location: indexlogin.php");
		}


                
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php echo Grupete?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="../imas/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="style/main.css">
  <script src="../js/jquery-3.1.1.js"></script>
  <script src="../js/bootstrap.js"></script>
 
</head>
<body class="paralogin">

<!-- Navbar -->
<nav class="navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><?php echo Grupete?> <small>- ingreso al sitio</small></a>
    </div>
  </div>
</nav>


<div class="container-fluid bg-1">
 <div class="row">
   <div class="col-sm-1 text-center">
   </div>

   <div class="col-sm-4 bg-2 text-center">
     <br>
     <h3 class="margin">sitio de <?php echo siglaGrupe?></h3>
     <img src="../imas/playa.PNG" class="img-responsive img-circle margin sombrita" style="display:inline" alt="<?php echo Grupete?>" width="200" height="200">
     <h3></h3>
     <br>
	 </div>
   <div class="col-sm-2 text-center">
   </div>


   <div class="col-sm-4 well" >
    <div class="row" style="max-WIDTH: 400px; margin:0 auto;">

        <div class="col-xs-12 text-center">
		 en mantenimiento  <br><span class="glyphicon glyphicon-wrench"></span>
		<span class="glyphicon glyphicon-erase"></span>
		<span class="glyphicon glyphicon-pushpin"></span>
		<span class="glyphicon glyphicon-pencil"></span>
<br>reintent&aacute; en un rato
        </div>
    </div>
   </div>    
 </div>
</div>



<?php
//include header template
require('layout/footer.php');
?>
