<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

	
	$filx=$_GET["fil"];
	$filx=str_replace("("," OR (vw1.viajeID=",$filx);
	$filx=str_replace(","," AND vw1.ptt=",$filx);
	$filx=substr($filx,4);
	
	
	$clon=CENTRO_lon;
	$clat=CENTRO_lat;

        $pdo = Database::connect();
		$sql = "SELECT vw1.viajeID,vw1.ptt,vw1.fecha,lon,lat,		
		         GreatCircleDist($clon,$clat,lon,lat,'km') as disCentro,marcas
		            FROM vw_tracks_unoxdia vw1, vw_viaje_marca vwm WHERE ($filx) and estado='1' 
					     and  vw1.viajeID=vwm.viajeID ORDER BY vw1.viajeID,vw1.fecha,vw1.hora";					
					
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
