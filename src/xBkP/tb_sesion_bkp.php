<?php
/* control de sesion iniciada y expirada */
session_start();
if (!isset($_SESSION['loggedin']) or $_SESSION['loggedin'] <> true)  {	
    header("Location: ../login/index.php");
    exit ;
   }

if ($_SESSION['ultima_actividad'] < time()-$_SESSION['tiempo_expira'] ) {
    //tiempo inactivo alcanzado, redirect al logout
    header('Location: ../tb_timeout.php');
	exit;
   } else
     { //ok tiempo, sesion continua
         $_SESSION['ultima_actividad'] = time(); // tiempo ahora
}


	function es_administrador(){
		if(isset($_SESSION['permiso']) && $_SESSION['permiso'] == "administrar"){
			return true;
		}
	}


?>