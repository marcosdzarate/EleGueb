<?php
    /*require_once '../tb_sesion.php';*/
    require_once '../tb_dbconecta.php';
    /* NO NO NOrequire_once '../tb_database.php';*/
    /*require_once '../tb_validar.php';*/


//get connection
$mysqli = new mysqli(elServer, elUser, elPassword, elDB);

if(!$mysqli){
	die("Falla conexion con BD: " . $mysqli->error);
}
$query = "SELECT claveU,largoStd FROM medidas WHERE NOT ISNULL(largoStd) order by 1 ";
$resultado = $mysqli->query($query);
if ($resultado===false) {
	die("Falla query: " . $mysqli->error);
}

header('Content-Type: application/json');
$data = array();
foreach ($resultado as $row) {
	$data[] = $row;
}

//free memory associated with result
$resultado->close();

//close connection
$mysqli->close();
print json_encode($data);
?>