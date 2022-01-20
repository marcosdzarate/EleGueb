<?php 
    require_once 'tb_sesion_aca.php';
    require_once '../tb_dbconecta.php';
    require_once '../tb_database.php';
    require_once '../tb_validar.php';
	
	/* sin permiso de administrador, fuera*/
	siErrorFuera(es_administrador(),"p");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">

<!--<meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0">-->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>

    <script src="../js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="../css/bootstrap.css" rel="stylesheet">
    <script src="../js/bootstrap.js"></script>

  <link rel="stylesheet" href="style/main.css">

<style>
.mar {
   margin-bottom:30px;
   border-radius:2px !important;}
</style>
</head>


<body class = "paralogin bg-5" >
<?php
require_once 'tb_barraAdmin.php';
?>
<br><br>
<div class="container-fluid bg-4 text-center">
  <div class="row">
    <div class="col-sm-4">
	  <h3>hola</h3>
	  <h1>
	  <span class="glyphicon glyphicon-wrench"></span>
	  <span class="glyphicon glyphicon-scissors"></span>
	  <span class="glyphicon glyphicon-pencil"></span>
	  </h1>
	</div>  
    <div class="col-sm-4">

	  <div class="btn-group-vertical">
		<a href="usuarios_index.php" type="button" class="btn btn-lg btn-primary mar" >administrar los usuarios</a>
		<a href="http://web.cenpat-conicet.gob.ar/phpmyadmin/index.php" target="_blank" type="button" class="btn btn-lg
		btn-warning mar">phpMyAdmin</a>
		<a href="../xBkP/backup_tot.php" type="button" class="btn btn-lg btn-success mar" >backup DB MySQL <?php echo elDB?>,<br> fotos, videos,<br>publicaciones, archivos DIAG </a>

	  </div>
	</div>

	  
  
  </div>
</div>





</BODY></HTML>