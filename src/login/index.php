<?php
// originalmene login.php
//include config

//check if already logged in move to home page
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){ 
	header('Location: ../aprincipal.php');
	} 
else{
	header("Location: indexlogin.php");
	}
exit;
?>


