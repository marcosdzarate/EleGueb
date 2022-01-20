<?php 
require('includes/config.php');
require_once ('correito.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: ../aprincipal.php'); }

$erre="r";

if ( isset($_GET['a']) and $_GET['a']=="activa") {
	  $erre="";
}


//if form has been submitted process it
if(isset($_POST['submit'])){
    $erre =$_POST['a'];
	//email validation
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Ingres&aacute; un email v&aacute;lido.';
	} else {
		$stmt = $db->prepare('SELECT email,active,username FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(empty($row['email'])){
			$error[] = 'El email no est&aacute; registrado.';
		}
		else {
			if($row['active'] <> 'Yes'){
				$error[] = 'Tu cuenta no est&aacute; activa.';
			}			
		}

	}

	//if no errors have been created carry on
	if(!isset($error)){
        $xuser =$row['username'];
		//create the activasion code
		$token = md5(uniqid(rand(),true));

		try {

			$stmt = $db->prepare("UPDATE members SET resetToken = :token, resetComplete='No' WHERE email = :email");
			$stmt->execute(array(
				':email' => $row['email'],
				':token' => $token
			));

			//send email  con funcion correito()		
			
			$para = $row['email'];
			$paraN= "";
			$motivo = $erre."establecer password-".siglaGrupe;
			$cuerpo = "<h2>".$erre."establecer password</h2>".
			"<p>Hola usuario <b>$xuser</b>.".
			"<p>Para ".$erre."establecer tu password, hac&eacute; click en el siguiente link. </p>". 
			"<div style='background-color:#85d5f7;text-align:center'>".
			"<p>&nbsp;</p>".
				   "<a href='".elSitio."resetPassword.php?key=$token'>".$erre."establecer password</a>".
			"<p>&nbsp;</p>".
			"</div>".
			"<p></p>";
			
			$arre=correito($para,$paraN,$motivo,$cuerpo);
			

	if (count($arre) > 0) {
		if ($arre[0]=="Mensaje enviado") {
			$e="<div class='alert alert-success lead' >";
			$e.= "<h4>Busc&aacute; en tu correo un mail con instrucciones para restablecer password.</h4>";
			$e.="</div>";
	
		}
		else{
			$e =$e="<div class='alert alert-danger' >";
			foreach ($arre as $resu) {
			  $e .="$resu\n";
			}
		}
	}			


		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php echo siglaGrupe ?></title>
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
      <a class="navbar-brand" href="#"><?php echo Grupete?> <small>- <?php echo $erre?>establecer password</small></a>
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
	<div class="row" >
	    <div class="col-xs-12">
		    <h3><?php if(isset($_GET['a']) and $_GET['a']=="activa"): ?>Tu cuenta ha sido activada. <?php endif ?>
			Para establecer la nueva password se enviar&aacute;n indicaciones al mail registrado.</h3>
			
			<form role="form" method="post" action="" autocomplete="off">

				<?php
				//check for any errors
				if(isset($error)){
					$e="<div class='alert alert-danger' >";
					foreach($error as $error){
						$e .= '<p class="bg-danger">'.$error.'</p>';
					}
				$e.="</div>";
					
				}

				?>

				<div class="form-group">
                    <h2>email:</h2>								
					<input type="email" name="email" id="email" class="form-control input-lg" value="<?php echo $_POST['email'] ?>" tabindex="1">
					<input type="hidden" name="a" id="a" value="<?php echo $erre;?>">
				</div>

				<div class="row">
					<div class="col-xs-12">
						 <a href='indexlogin.php'><small>Volver al login</small></a>
					</div>
					
				</div>				
				<hr>
				<div class="row">
					<div class="col-lg-12"><input type="submit" name="submit" value="<?php echo $erre?>establecer mi password" class="btn btn-primary btn-block btn-lg" tabindex="2"></div>
				</div>

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

