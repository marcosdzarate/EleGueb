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

	$v=true;
    $pk_memberID = null;		
	if (isset($_GET["memberID"])) {
		$pk_memberID=$_GET["memberID"];
		$m = validar_memberID ($pk_memberID,$v,true);
	}
	SiErrorFuera ($v);
	
    if ( !isset($_POST['enviarmail'])) {
		
		/*seleccion inicial*/
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
    else {
		
		// envia mail
		$username = $_POST['username'];
		$email = $_POST['email'];
		$permiso = $_POST['permiso'];

		$memberID = $_POST['memberID'];
		$password = $_POST['password'];
		$active = $_POST['active'];
		
		$resetToken = $_POST['resetToken'];
		$resetComplete = $_POST['resetComplete'];
				
		// valores anteriores de campos clave
		$pk_memberID=$_POST['pk_memberID'];

		
		/* mail de activacion? */
		$qmensa="cambios guardados";
		if(isset($_POST['enviarmail']))
			{
				 /* mail con funcion correito()*/		
				$para = $_POST['email'];
				$paraN= $_POST['username'];
				$motivo = "Pase al sitio CESIMAR-".siglaGrupe;
				$cuerpo = "<h2>Activ&aacute; tu cuenta</h2>".
				"<p>Este es tu pase al sitio del ".Grupete.".</p>". 
				"<p>Tu usuarios es:<b> $username</b></p>".
				"<p>Tu permiso es:<b> $permiso</b></p>".
				"<p>Para activarla, hac&eacute; click en el siguiente link.</p>".
				"<div style='background-color:#85d5f7;text-align:center'>".
				"<p>&nbsp;</p>".
					   "<a href='".elSitio."activate.php?x=$memberID&y=$active'>activar cuenta</a>".
				"<p>&nbsp;</p>".
				"</div>";
				
				$a=array();
				$a=correito($para,$paraN,$motivo,$cuerpo);  
				//echo $cuerpo;
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
                <h3 class="panel-title">activaci&oacuten de cuenta</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmembers" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<fieldset class="form-inline">
                    <div class="form-group">
                            <label for="memberID">ID</label> 
                            <input type="text" class="form-control input-sm" style="width:90px" id="memberID" name="memberID"   readonly   value="<?php echo !empty($memberID)?$memberID:'';?>" >							
                            <input name="pk_memberID" type="hidden"  value="<?php echo !empty($pk_memberID)?$pk_memberID:'';?>"> <!-- pk, clave anterior -->


                    </div>

                    <div class="form-group ">
					     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label for="username">usuario</label>						
                        <input type="text" name="username" id="username" class="form-control input-sm" readonly   value="<?php echo !empty($username)?$username:'';?>" >
						


                    </div>
</fieldset>                    
                    <div class="form-group ">
                        <label for="email">e-mail</label>
                            <input type="email" name="email" id="email" class="form-control input-sm"  readonly  value="<?php echo !empty($email)?$email:'';?>" >
                    </div>

                    <div class="form-group ">
                    
                        <label for="email">permiso</label>                  
                        <div class="form-group">
                            <input type="text" name="permiso" id="permiso" class="form-control input-sm" readonly   value="<?php echo !empty($permiso)?$permiso:'';?>" >
                        </div>                  
                    </div>
                    
                    
                    
                    <div class="container well well-sm " style="width:80%">                   
                        
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

                       <button type="submit" class="btn btn-warning btn-sm" name="enviarmail">enviar mail de activaci&oacute;n</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						
						
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("usuarios_index.php<?php echo $param0;?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <div class="alert alert-danger"><h5><?php echo $eError;?></h5></div>
                          <?php endif;?>
                          <?php if (isset($qmensa)): ?>
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