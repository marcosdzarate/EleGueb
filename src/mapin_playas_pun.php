<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  


        $pdo = Database::connect();
        $sql = "SELECT IDplaya,nombre,norteSur,geomTex,tipo,lati,longi
		            FROM playa WHERE geomEsp IS NOT NULL and lati IS NOT NULL and norteSur>0 and tipo<>'MENSUAL' ORDER BY tipo,norteSur";
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
