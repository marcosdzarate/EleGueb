<?php
// originalmene login.php
//include config
require_once('includes/config.php');
require_once ('../tb_validar.php');


	if (file_exists("AAAAenMantenimiento.xxx")) {
		header("Location: indexloginMante.php");
	}		
	
	
	
//check if already logged in move to home page
if( $user->is_logged_in() ){ header('Location: ../aprincipal.php'); } 

//process login form if submitted
if(isset($_POST['submit'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
	
	$v = true;
	$e = validar_userpass($username,$v,"login");
	$e = validar_userpass($password,$v,"login");
	if (!$v) {
		$error[] = 'Formato de usuario y/o password incorrecto: 6 a 30 caracteres A-Z a-z 0-9._-';
	}
	else {
		if($user->login($username,$password)){ 
			$_SESSION['username'] = $username;
			header('Location: ../aprincipal.php');
			exit;
		
		} else {
			$error[] = 'Usuario y/o password incorrectos o <br>la cuenta no ha sido activada o<br>no se termin&oacute; la recuperaci&oacute;n de password.';
		}
	}

}//end if submit

//check for any errors
if(isset($error)){
	$e="<div class='alert alert-danger' >";
	foreach($error as $error){
		$e .= '<p class="bg-danger">'.$error.'</p>';
	}
	$e.="</div>";
}

if(isset($_GET['action'])){
	$e="<div class='alert alert-success lead' >";
	//check the action
	switch ($_GET['action']) {
		case 'active':
			header('Location: reset.php?a=activa');
			exit;		
			/*$e.= "<p>Tu cuenta ha sido activada. <br><a href='reset.php?a=activa'><span class='glyphicon glyphicon-hand-right'></span> Ahora establece la password.</a></p>";*/
			break;
		case 'resetAccount':
			$e.= "<p><span class='glyphicon glyphicon-thumbs-up'></span> La password se estableci&oacute;. Pod&eacute;s iniciar la sesi&oacute;n.</p>";
			break;
		case 'noseactiva':
			$e= "<div class='alert alert-warning lead'><p><span class='glyphicon glyphicon-exclamation-sign'></span> Tu cuenta no puede ser activada. Ya estar&aacute; activada? Prob&aacute; a ingresar.</p>";
			break;
	}
	$e .="</div>";

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

        <div class="col-xs-12">
            <form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" autocomplete="off">

				<?php if (!isset($_GET['action']) or $_GET['action']='resetAccount' or $_GET['action']='noseactiva'): ?>
					<div class="form-group">
						<h2>usuario:</h2>               
						<input type="text" name="username" id="username" class="form-control input-lg" value="<?php if(isset($error)){ echo $_POST['username']; } ?>" tabindex="1">
					</div>

					<div class="form-group">
						<h2>password:</h2>              
						<input type="password" name="password" id="password" class="form-control input-lg"  tabindex="3">
					</div>
					
					<div class="row">
						<div class="col-xs-12">
							 <a href='reset.php'><small>Olvidaste usuario y/o password?</small></a>
						</div>
					</div>
					
					<hr>
					<div class="row">
						<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="Login" class="btn btn-primary btn-block btn-lg" tabindex="5"></div>
					</div>          
                <?php endif; ?>
<br>
 <?php if (isset($e)) {
      echo $e;
     } 
?>              
            </form>
        </div>
    </div>
   </div>    
 </div>
</div>



<?php
//include header template
require('layout/footer.php');
?>
