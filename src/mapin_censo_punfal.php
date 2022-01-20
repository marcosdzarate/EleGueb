<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  


	$fec=$_GET["fec"];

		$aPuntos=array();
        $pdo = Database::connect();
        $sql = "SELECT grupo.fecha,grupo.libreta,grupo.orden,grupo.geomTex,grupo.lati,grupo.longi,grupo.referencia
		            FROM censo,sector_copiado,grupo
					WHERE geomEsp IS NOT NULL and lati IS NOT NULL 
					   AND fechaTotal='$fec'
					   AND censo.fecha=sector_copiado.fecha AND
                           sector_copiado.fecha_copia=grupo.fecha AND
                           sector_copiado.libreta_copia=grupo.libreta AND
                           grupo.orden BETWEEN sector_copiado.orden_desde AND sector_copiado.orden_hasta
					   ORDER BY grupo.fecha,grupo.libreta,grupo.orden";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo json_encode($aPuntos); //echo 'no hay registros!!!';
                exit;
                }
        while ( $aLoc = $q->fetch(PDO::FETCH_ASSOC) ) {
			$err="";
			$cLati =converLatLon ($aLoc['lati'],"lat",$err);
			$cLongi=converLatLon ($aLoc['longi'],"lon",$err);
			$aLoc['lati']=$cLati;
			$aLoc['longi']=$cLongi;
			
             $aPuntos[]=$aLoc;
        }
        Database::disconnect();
       
	echo json_encode($aPuntos);
?>
