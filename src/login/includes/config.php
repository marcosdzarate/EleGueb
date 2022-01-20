<?php
ob_start();
session_start();

//set timezone
//date_default_timezone_set('America/Argentina/Buenos_Aires');

//database credentials
require_once('../tb_dbconecta.php');
define('DBHOST',elServer);   
define('DBUSER',elUser);     
define('DBPASS',elPassword); 
define('DBNAME',elDB);
define('DBport',elPort);

//application address LOGIN
define('DIR',elSitio);  

try {

	//create PDO connection
	$db = new PDO("mysql:host=".DBHOST.";port=".DBport.",;dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    //muestra error
    require('layout/headerror.php'); 	
    echo $e->getMessage();
    exit;
}

//include the user class, pass in the database connection
include('classes/user.php');
$user = new User($db);
?>
