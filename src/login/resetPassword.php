<?php
require('includes/config.php'); 
require_once ('../tb_validar.php');

//if logged in redirect to members page
if( $user->is_logged_in() ){ header('Location: ../aprincipal.php'); } 


if (!isset($_GET['key']) or !ctype_xdigit($_GET['key'])){
	 $error[] = 'Por favor, us&aacute; el link que se te envi&oacute; en el email.';
}
else {
	$stmt = $db->prepare('SELECT resetToken, resetComplete FROM members WHERE resetToken = :token');
	$stmt->execute(array(':token' => $_GET['key']));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	//if no token from db then kill the page
	if(empty($row['resetToken'])){
		$stop = 'Por favor, us&aacute; el link que se te envi&oacute; en el email.';
	} elseif($row['resetComplete'] == 'Yes') {
		$stop = "Tu password ya fue cambiada! <br><a href='indexlogin.php'><span class='glyphicon glyphicon-hand-right'></span> Inicia la sesi&oacute;n.</a></p>";
	}

	//if form has been submitted process it
	if(isset($_POST['submit'])){
		$v=true;
		$e = validar_userpass($_POST['password'],$v,"login");
		if (!$v) {
			$error[] = 'Formato de password incorrecto';
		}
		$v = true;
		$e = validar_userpass($_POST['passwordConfirm'],$v,"login");
		if (!$v) {
			$error[] = 'Formato de password de confirmaci&oacute;n incorrecto';
		}
	
		if($_POST['password'] != $_POST['passwordConfirm']){
			$error[] = 'Las passwords no son iguales.';
		}

		//if no errors have been created carry on
		if(!isset($error)){

			//hash the password
			$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);

			try {

				$stmt = $db->prepare("UPDATE members SET password = :hashedpassword, resetComplete = 'Yes'  WHERE resetToken = :token");
				$stmt->execute(array(
					':hashedpassword' => $hashedpassword,
					':token' => $row['resetToken']
				));

				//redirect to index page
				header('Location: indexlogin.php?action=resetAccount');
				exit;

			//else catch the exception and show the error.
			} catch(PDOException $e) {
				$error[] = $e->getMessage();
			}

		}

	}
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php echo siglaGrupe?></title>
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
      <a class="navbar-brand" href="#"><?php echo Grupete?> <small>- establecer password</small></a>
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


  <div class="col-sm-4 well well-lg" >
	<div class="row" >
    

        <div class="col-lg-12 ">

    
        
            <?php if(isset($stop)){
                echo '<div class="row">'."\n";
                echo "     <div class='alert alert-warning lead' style='text-align:center' ><p>$stop</p></div>\n";
                echo "</div>\n";
				
				
            } else { ?>

                <form role="form" method="post" action="" autocomplete="off">
                    

                    <?php
                    //check for any errors
					if(isset($error)){
						$e="<div class='alert alert-danger' >";
						foreach($error as $error){
							$e .= '<p class="bg-danger">'.$error.'</p>';
						}
					    $e .="</div>";
					}					
					
                    ?>

                            <div class="form-group">
								<h4>password:</h4>				
                                <input type="password" name="password" id="password" class="form-control input-lg"  tabindex="1">
                            </div>
                            <div class="form-group">
								<h4>repetir password:</h4>				
                                <input type="password" name="passwordConfirm" id="passwordConfirm" class="form-control input-lg"  tabindex="1">
                            </div>
                    
                    <hr>
                    <div class="row">
                        <div class="col-xs-12"><input type="submit" name="submit" value="cambiar password" class="btn btn-primary btn-block btn-lg" tabindex="3"></div>
                    </div>

					
					<?php if (isset($e)) {
						echo $e;
							} 
					?>		


					</form>

            <?php } ?>
        </div>
    </div>


</div>
</div>

</div>

<?php
//include header template
require('layout/footer.php');
?>

