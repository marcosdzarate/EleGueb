<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

	
	$ID=$_GET["ID"];
	$pt=$_GET["pt"];
	$clon=CENTRO_lon;
	$clat=CENTRO_lat;

        $pdo = Database::connect();
        $sql = "SELECT viajeID,ptt,fecha,hora,locQuality,lon,lat,distanciaPDelgada,distanciaSalida,
		         GreatCircleDist($clon,$clat,lon,lat,'km') as disCentro
		            FROM localizaciones WHERE viajeID=$ID and ptt=$pt and estado='1' ORDER BY viajeID,fecha,hora";
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
