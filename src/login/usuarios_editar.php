<?php
    require_once 'tb_sesion_aca.php';
    require_once '../tb_dbconecta.php';
    require_once '../tb_database.php';
    require_once '../tb_validar.php';

	/* sin permiso de administrador, fuera*/
	siErrorFuera(es_administrador());


    $par_elimina=1;
    require_once '../tb_param.php';

	require_once ('correito.php');

        $queTabla = 'members';

	/*parametros inválidos, fuera*/			
	$v=true;
    $pk_memberID = null;		
	if (isset($_GET["memberID"])) {
		$pk_memberID=$_GET["memberID"];
		$m = validar_memberID ($pk_memberID,$v,true);
	}
	else{
		if (!isset($_GET["memberID"]) and !isset($_POST["pk_memberID"]) ){
			$v=false;
		}
	}	
	SiErrorFuera ($v);
	
	
    if ( !empty($_POST)) {
		
		// para errores de validacion
		$usernameError = null;
		$emailError = null;
		$permisoError = null;
		$memberIDError = null;

		$eError = null;
		
		// los campos a validar
		$memberID = limpia($_POST['memberID']);
		$username = limpia($_POST['username']);
		$permiso = limpia($_POST['permiso']);		
		$email = limpia($_POST['email']);

		$password = limpia($_POST['password']);
		$active = limpia($_POST['active']);
		$resetToken = limpia($_POST['resetToken']);
		$resetComplete = limpia($_POST['resetComplete']);
				
		// valores anteriores de campos clave
		$pk_memberID=limpia($_POST['pk_memberID']);

		// validacion: devuelve el error; los dos primeros parametros pasan x referencia
		//             el ultimo: true debe estar - false puede faltar
		$valid = true;
		$memberIDError = validar_memberID($memberID,$valid,true);
		$usernameError = validar_userpass($username,$valid,"usuario");
		$permisoError = validar_permiso($permiso,$valid,true);
		$emailError = validar_email($email,$valid,true);
		
		// actualizar los datos
		if ($valid) { 
			// verifico que no existan el mail y/o usuario para otro memberID
			$pdo = Database::connect();
			$sql = "SELECT username FROM members WHERE memberID<>? AND BINARY username=? ";
			$q = $pdo->prepare($sql);
			$q->execute(array($pk_memberID,$username));
			$row = $q->fetch(PDO::FETCH_ASSOC);
			if(!empty($row['username'])){
				$valid = false;
				$usernameError = 'Ya existe este usuario en el sistema.';
			}            
			$sql = "SELECT email FROM members WHERE memberID<>? AND email=? ";
			$q = $pdo->prepare($sql);
			$q->execute(array($pk_memberID,$email));
			$row = $q->fetch(PDO::FETCH_ASSOC);
			if(!empty($row['email'])){
				$valid = false;
				$emailError = 'Este e-mail est&aacute; asociado a otro usuario.';
			}            


			if ($valid)	{
				// actualiza
				$sql = "UPDATE members set username=?,email=?,permiso=? WHERE memberID=? ";
				$q = $pdo->prepare($sql);
				$q->execute(array($username,$email,$permiso,$pk_memberID));

				$arr = $q->errorInfo();

				Database::disconnect();

				if ($arr[0] <> '00000') {
					$eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
					}
				else {


					/* mail de activacion? LO DEJO PERO ESTÁ COMENTARIADO EL BOTON ENVIAR MAIL!!! VA POR BOTON EN USUARIOS_INDEX.PHP */
					$qmensa="cambios guardados";
					if(isset($_POST['enviarmail']))
					  {
						 /* mail con funcion correito()*/		
						$para = $_POST['email'];
						$paraN= $_POST['username'];
						$motivo = "Activación de cuenta-".siglaGrupe;
						$cuerpo = "<h2>Activar tu cuenta en sitio de ".Grupete."</h2>".
						"<p>Para activar tu cuenta, hac&eacute; click en el siguiente link.</p>".
						"<div style='background-color:#85d5f7;text-align:center'>".
						"<p>&nbsp;</p>".
						       "<a href='".elSitio."activate.php?x=$memberID&y=$active'>activar cuenta</a>".
						"<p>&nbsp;</p>".
						"</div>".
						"<p>Saludos!</p>".
						'<img src="cid:logocesimar" alt="CESIMAR-CONICET">';
						$a=array();
						$a=correito($para,$paraN,$motivo,$cuerpo);  
						if ($a[0]=="Mensaje enviado") {
							$qmensa="mail de activaci&oacute;n enviado";
						}
						else {
								foreach ($a as $ai) {
								  $eError .= $ai."\n";
								}
						}
					   }	
				
				   }
			   }
			   else {
					Database::disconnect();
			       }
			} 
		}
	
        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM members where memberID=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_memberID));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);
                       $memberID = $data['memberID'];
                       $username = $data['username'];
                       $password = $data['password'];
                       $email = $data['email'];
                       $active = $data['active'];
                       $resetToken = $data['resetToken'];
                       $resetComplete = $data['resetComplete'];
                       $permiso = $data['permiso'];

                       $pk_memberID = $memberID;

                       Database::disconnect();
                   }
                }
        }

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title><?php echo siglaGrupe ?></title>

    <script src="../js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="../js/validator.js"></script>
    <link   href="../css/bootstrap.css" rel="stylesheet">
    <script src="../js/bootstrap.js"></script>
    <script src="../js/imiei.js"></script>

<script>
function CargaBody() {
}
</script>

</head>

<body onload="CargaBody()">
    <div class="container" style="width:90%">


        <div class="panel panel-azulino">
            <div class="panel-heading">
                <h3 class="panel-title">datos de usuario</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmembers" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<div class=row>

                <div class="col-sm-4">          

                    <div class="form-group">
                            <label for="memberID">ID</label> 
                            <input type="text" class="form-control input-sm" style="width:90px" id="memberID" name="memberID"   readonly required  value="<?php echo !empty($memberID)?$memberID:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrormemberID"></p>
                            <?php if (!empty($memberIDError)): ?>
                                <span class="help-inline"><?php echo $memberIDError;?></span>
                            <?php endif; ?>
							
                            <input name="pk_memberID" type="hidden"  value="<?php echo !empty($pk_memberID)?$pk_memberID:'';?>"> <!-- pk, clave anterior -->


                    </div>

                </div>
					
					
                <div class="col-sm-4">          
					
                    <div class="form-group ">
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label for="username">usuario</label>						
						<!--data-placement="bottom" data-toggle="tooltip" -->
						<a href="#" class="btn btn-info" 
						title="TIP: nombre y apellido, sin espacios; case-sensitive; permitidos A-Za-z0-9.-_ (Pipo Perex como PipoPerex05)">
						<span class="glyphicon glyphicon-question-sign"></span>
						</a>
                        <input type="text" name="username" id="username" class="form-control input-sm" pattern="<?php echo PATRON_usuario?>"   required   value="<?php echo !empty($username)?$username:'';?>" data-error="<?php echo PATRON_usuario_men?>">
                        <span class="bg-info"></span>				
						
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorusername"></p>
                            <?php if (!empty($usernameError)): ?>
                                <span class="help-inline"><?php echo $usernameError;?></span>
                            <?php endif; ?>


                    </div>
                </div>

                <div class="col-sm-4">          

					
                    <div class="form-group ">
                    
                        <label for="email">permiso</label>                  
                        <div class="form-group">
                            <select class="form-control input-sm" style="width: auto" id="permiso" required   name="permiso">
                                <option value="noEditar" <?php if ($permiso == "noEditar") {echo " selected";}?> >noEditar</option>
                                <option value="editar" <?php if ($permiso == "editar") {echo " selected";}?> >editar</option>
                                <option value="administrar" <?php if ($permiso == "administrar") {echo " selected";}?> >administrar</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorpermiso"></p>
                            <?php if (!empty($permisoError)): ?>
                                <span class="help-inline"><?php echo $permisoError;?></span>
                            <?php endif; ?>							
                        </div>                  
                    </div>					
                </div>
					
					
</div>                     
                    <div class="form-group ">
                        <label for="email">e-mail</label>
                            <input type="email" name="email" id="email" class="form-control input-sm"   required  value="<?php echo !empty($email)?$email:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErroremail"></p>
                            <?php if (!empty($emailError)): ?>
                                <span class="help-inline"><?php echo $emailError;?></span>
                            <?php endif; ?>
                    </div>
                    
                    
                    
                    <div class="container well well-sm " style="width:80%">                   
                        <h5 class="bg-primary"><strong>El sistema se ocupa de las siguientes variables</strong></h5>
                        
                        <div class="form-group ">
                            <label for="password">password (hash!!! restaura el usuario) </label>
                            <input type="password" name="password" id="password" class="form-control input-sm" readonly  value="<?php echo !empty($password)?$password:'';?>" >
                        </div>

                        <div class="form-group ">
                            <label for="active">active (cuenta activada=Yes; pendiente=hash) </label>
                                <input class="form-control input-sm" id="active" name="active" readonly value="<?php echo !empty($active)?$active:'';?>" >
                        </div>
                        <div class="form-group ">
                            <label for="resetComplete">resetComplete (Yes/No)</label>
                                <input type="text" class="form-control input-sm" style="width: 60px" id="resetComplete" name="resetComplete"    maxlength="3" readonly   value="<?php echo !empty($resetComplete)?$resetComplete:'';?>" >
                        </div>
                        <div class="form-group ">
                            <label for="resetToken">resetToken (hash enviado para restaurar password)</label>
                                <input class="form-control input-sm" id="resetToken" name="resetToken" readonly value="<?php echo !empty($resetToken)?$resetToken:'';?>" >
                        </div>

                    </div>


                    <br>
                    <div class="form-group">

                        <button type="submit" class="btn btn-primary btn-sm" name="submit">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                       <!-- <button type="submit" class="btn btn-warning btn-sm" name="enviarmail">enviar mail de activaci&oacute;n</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
						
						
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("usuarios_index.php<?php echo $param0;?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <div class="alert alert-danger"><h5><?php echo $eError;?></h5></div>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <div class="alert alert-success"><h5><?php echo $qmensa;?></h5></div>
                          <?php endif;?>

                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>


  </body>
</html>