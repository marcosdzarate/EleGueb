<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

	
	$fec=$_GET["fec"];
	$lib=$_GET["lib"];

        $pdo = Database::connect();
		/* lati y longi son varchar con el texto originalmente escrito x el usuario */
		/* por eso uso las funciones ST_X y ST_Y */
        $sql = "SELECT fecha,libreta,orden,geomTex,ST_Y(geomEsp) as lati,ST_X(geomEsp) as longi,referencia
		            FROM grupo WHERE geomEsp IS NOT NULL and lati IS NOT NULL 
					   AND fecha='$fec' AND libreta='$lib' ORDER BY fecha,libreta,orden";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
		$aPuntos=array();
        while ( $aLoc = $q->fetch(PDO::FETCH_ASSOC) ) {
             $aPuntos[]=$aLoc;
        }
        Database::disconnect();
       
	echo json_encode($aPuntos);
?>
