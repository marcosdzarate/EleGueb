<?php 
require('includes/config.php');
//require_once 'tb_sesion_aca.php';
require_once '../tb_validar.php';

//require_once ('correito.php');
if(!$user->is_logged_in()){ header('Location: indexlogin.php'); } 

	/* sin permiso de administrador, fuera*/
	$v = isset($_SESSION['permiso']) && $_SESSION['permiso'] == "administrar";
	siErrorFuera($v);

$permiso=null;
//if form has been submitted process it
if(isset($_POST['submit'])){
    
	//very basic validation	
	$vUser=true;
	$v=true;
	$e = validar_userpass($_POST['username'],$vUser,"usuario");
	if (!$vUser) {
		$error[] = "Usuario: ".$e;
	}
	
	$e = validar_userpass($_POST['password'],$v,"password");
	if (!$v) {
		$error[] = "Password: ".$e;
	}	
	$e = validar_userpass($_POST['passwordConfirm'],$v,"confirmaci&oacute;n");
	if (!$v) {
		$error[] = "Password-repetici&oacute;n: ".$e;
	}	

	if($_POST['password'] != $_POST['passwordConfirm']){
		$error[] = 'Passwords no son iguales.';
	}
	
	if($vUser) {
		$stmt = $db->prepare('SELECT username FROM members WHERE BINARY username = :username');
		$stmt->execute(array(':username' => $_POST['username']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['username'])){
			$error[] = 'Ya existe ese usuario en el sistema.';
		}

	}


	//email validation
	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
	    $error[] = 'Ingres&aacute; una email v&aacute;lido';
	} else {
		$stmt = $db->prepare('SELECT email FROM members WHERE email = :email');
		$stmt->execute(array(':email' => $_POST['email']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if(!empty($row['email'])){
			$error[] = 'El e-mail ya est&aacute; registrado.';
		}

	}

    $permiso=$_POST['permiso'];
	
	//if no errors have been created carry on
	if(!isset($error)){

		//hash the password
		$hashedpassword = $user->password_hash($_POST['password'], PASSWORD_BCRYPT);

		//create the activasion code
		$activasion = md5(uniqid(rand(),true));

		try {

			//insert into database with a prepared statement
			$stmt = $db->prepare('INSERT INTO members (username,password,email,active,permiso) VALUES (:username, :password, :email, :active, :permiso)');
			$stmt->execute(array(
				':username' => $_POST['username'],
				':password' => $hashedpassword,
				':email' => $_POST['email'],
				':active' => $activasion,
				':permiso' => $_POST['permiso']
			));
			$id = $db->lastInsertId('memberID');

			$agregado=true;
			
		/*	//send email  con funcion correito() desde boton!!
		
			$para = $_POST['email'];
			$paraN= "";
			$motivo = "Confirma registro";
			$cuerpo = "<p>Thank you for registering at demo site.</p>
			<p>To activate your account, please click on this link: <a href='".DIR."activate.php?x=$id&y=$activasion'>".DIR."activate.php?x=$id&y=$activasion</a></p>
			<p>Regards Site Admin</p>";

			correito($para,$paraN,$motivo,$cuerpo);  

			//redirect to index page
			header('Location: indexlogin.php?action=joined');
			exit;  */

		//else catch the exception and show the error.
		} catch(PDOException $e) {
		    $error[] = $e->getMessage();
		}

	}

}

//define page title
$title = 'Demo';

//include header template
require('layout/header.php');
?>


<div class="container">

        <div class="panel panel-azulino">
            <div class="panel-heading">
                <h3 class="panel-title">datos del nuevo usuario</h3>
				
            </div>
            <div class="panel-body">



			<!--<form role="form" method="post" action="" autocomplete="off">-->
			<form data-toggle="validator" id="CRmembers" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" autocomplete="off">


				<div class="form-group">
                        <label for="username">usuario </label>
						<a href="#" class="btn btn-info" 
						title="TIP: nombre y apellido, sin espacios; case-sensitive; permitidos A-Za-z0-9.-_ (Pipo Perex como PipoPerex05)">
						<span class="glyphicon glyphicon-question-sign"></span>
						</a>
						
					<input type="text" name="username" id="username" pattern="<?php echo PATRON_usuario?>" data-error="<?php echo PATRON_usuario_men?>" class="form-control input-sm" required value="<?php echo isset($_POST['username'])?$_POST['username']:''; ?>" tabindex="1">
                            <div class="help-block with-errors"></div>
		
				</div>
				<div class="form-group">
                        <label for="email">e-mail</label>
						<input type="email" name="email" id="email" required class="form-control input-sm" 
						value="<?php echo isset($_POST['email'])?$_POST['email']:""; ?>" tabindex="2">
                            <div class="help-block with-errors"></div>
				</div>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-6">
                        <label for="password">password</label>
						<!--data-placement="bottom" data-toggle="tooltip" -->
						<a href="#" class="btn btn-info" 
						title="es case-sensitive; permitidos A-Za-z0-9.-_">
						<span class="glyphicon glyphicon-question-sign"></span>
						</a>

						<div class="form-group">
							<input type="password" name="password" id="password" pattern="<?php echo PATRON_usuario?>" data-error="<?php echo PATRON_usuario_men?>" required class="form-control input-sm" tabindex="3">
                            <div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
                        <label for="passwordConfirm">repetir password para confirmar</label>					
						<div class="form-group">
							<input type="password" name="passwordConfirm" pattern="<?php echo PATRON_usuario?>" data-error="<?php echo PATRON_usuario_men?>"  required id="passwordConfirm" class="form-control input-sm" tabindex="4">
                            <div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-6">
                        <label for="permiso">permiso</label>					
						<div class="form-group">
                            <select class="form-control input-sm" style="width: auto" id="permiso" required   name="permiso">
                                <option value="noEditar" <?php if ($permiso == "noEditar") {echo " selected";}?> >noEditar</option>
                                <option value="editar" <?php if ($permiso == "editar") {echo " selected";}?> >editar</option>
                                <option value="administrar" <?php if ($permiso == "administrar") {echo " selected";}?> >administrar</option>
                            </select>
						</div>
					</div>


					</div>

				<div class="row">
					<div class="col-xs-6 col-md-6"><input type="submit" name="submit" value="registrar" class="btn btn-primary btn-block btn-sm" tabindex="5"></div>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("usuarios_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
				</div>
				<?php
				//check for any errors
				if(isset($error)){
					foreach($error as $error){
						echo '<div class="alert alert-danger">'.$error.'</div>';
					}
				}

				//if action is joined show sucess
				if(isset($agregado) && $agregado){
					echo "<h2 class='bg-success'>Pendiente el env&iacute;o de mail de activaci&oacute;n.</h2>";
				}
				?>				
			</form>
		</div>
	</div>

</div>

<?php
//include header template
//require('layout/footer.php');
?>
