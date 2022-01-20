<?php
/* 2018 */
/* PROCESA DE UN ARCHIVO DESCARGADO DE ARGOS CON LOCALIZACIONES SATELITALES */
/* EN FORMATO DIAG 															*/
/* 
 Prog 06080

 161606 Date : 27.02.18 11:42:48  LC : B  IQ : 00
      Lat1 : 42.416S  Lon1 :  64.993W  Lat2 : 42.416S  Lon2 :  64.993W
      Nb mes : 002  Nb mes>-120dB : 000  Best level : -135 dB
      Pass duration : 090s   NOPC : 3
      Calcul freq : 401 677430.5 Hz   Altitude :    0 m
              06          172          210          186
              94           75           36           49
              87           20          240           21
              00           83          255          253
              72           00           03           27
             173          127          192
.....
.....			 
*/
/* SOLO SE UTILIZAN LAS LINEAS QUE CONTIENEN Date .... Y Lat1....				*/
/* INSERTA EN LA BASE DE DATOS EN LA TABLA Localizaciones						*/
/* APLICA EL FILTRO THREE-STAGE AUSTIN 											*/
/* https://onlinelibrary.wiley.com/doi/epdf/10.1111/j.1748-7692.2003.tb01115.x 	*/
/* A THREE-STAGE ALGORITHM FOR FILTERING ERRONEOUS ARGOS SATELLITE  LOCATIONS	*/
/* DEBORAH AUSTIN																*/
/* SOLO SOBRE LAS NUEVAS LOCALIZACIONES Y 2 ANTERIORES (SI FUERA POSIBLE). LAS 	*/
/* LOCALIZACIONES CON LQ=Z QUEDAN FUERA DEL ANALISIS Y DE LA BASE DE DATOS.		*/
/* UNA VEZ FILTRADO, SE ACTUALIZA CADA REGISTRO, PONIENDO ESTADO=0 SI NO PASA 	*/
/* EL FILTRO O ESTADO=1 SI LA LOCALIZACION PASA EL FILTRO. EN ESTE ULTIMO CASO	*/
/* SE ACTUALIZA TAMBIEN CON LOS DATOS OBTENIDOS CON EL FILTRO: velocidadKM_p,	*/
/* distanciaKM_p, velocidadFiltro; ESTE ULTIMO ES EL VALOR <= CONST_Vmax PARA 	*/
/* EL FILTRO. distanciaKM_p, velocidadKM_p PUEDEN TENER VALORES QUE SUPEREN LOS	*/
/* LIMETES REALES. TENER PRESENTE ESTO PARA FUTUROS CALCULOS CON ESTAS COLUMNAS	*/
/* DE MANERA DE EXCLUIRLOS */

    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	
/***************************************************************************/

	function fechaArgosSis($f){
		/* la fecha en DIAG viene como dd.mm.aa 
		   se lleva a AAAA-mm-dd */
		$s=explode(".",$f);
		$f=2000+$s[2].'-'.$s[1].'-'.$s[0];
		return $f;
	}

	function AniosT($fa,$fc) {
		/* verifica que el año de la fecha argos (fa) 
		   sea igual al año de la fecha de colocación (fc) o
		   al año de fc +1 */
		$afa = substr($fa,0,4);
		$afc = substr($fc,0,4);
		$r=false;
		if ($afa == $afc or ($afa == $afc+1) ) {
			$r=true;
		}
		return $r;
		
	}
	
	function llArgosSis($ll){
		/* lleva lat/lon en formato argos a decimal */
		$a=preg_split("/[NSEW]/", $ll);
		$r=$a[0];
		if($a[1]='S' or $a[1]='W') {
			$r='-'.$r;
		}
		return $r;
	}
	
	function pttEnInstrumentos($p,$qi) {
		/* verifica si el ptt (p) esta en el string de instrumentos (qi) */
		$s=strpos($qi,$p);
		$r=true;
		if($s===false){
			$r=false;
		}
		return $r;
	}
	
	
	
	
/********************************************************************************/
/* filtro implementado 															*/
/* https://onlinelibrary.wiley.com/doi/epdf/10.1111/j.1748-7692.2003.tb01115.x 	*/
/* A THREE-STAGE ALGORITHM FOR FILTERING ERRONEOUS ARGOS SATELLITE  LOCATIONS	*/
/* DEBORAH AUSTIN																*/



function calculaDistanciaGC ($llo1, $lla1, $llo2, $lla2 ) {
/* Calculo distancia GREAT CIRCLE DISTANCE entre dos puntos sucesivos */
if ($llo1==$llo2 and $lla1==$lla2) {
	return 0.0;
}
$radtierra = 6371.2;
// GRADOS A RADIANES ....
   $la1 = deg2rad ( $lla1 );
   $lo1 = deg2rad ( $llo1 );
   $la2 = deg2rad ( $lla2 );
   $lo2 = deg2rad ( $llo2 );

// CALCULO ANGULO = ( (la1.sin * la2.sin) + (la1.cos * la2.cos * (lo1 - lo2).cos )).acos
   $t1   = sin($la1) * sin($la2);
   $lo12 = $lo1 - $lo2;
   $t2   = cos($la1) * cos($la2) * cos($lo12);
   $t = $t1 + $t2;
   $angulo = acos($t);
   $dgc = $radtierra * $angulo;

return $dgc;
}


function calculaTiempo ($f1,$h1,$f2,$h2) {
/* calcula tiempo entre dos fecha-hora en horas */

	$t0 = date_create($f1.' '.$h1);
	$t1 = date_create($f2.' '.$h2);
	if ($t1<$t0) {  /* siempre fecha menor es t0 */
		$tx=$t0;
		$t0=$t1;
		$t1=$tx;
	}

	$dh = date_interval_format(date_diff($t0,$t1),"%h")+
		 (date_interval_format(date_diff($t0,$t1),"%i")/60) + 
		 (date_interval_format(date_diff($t0,$t1),"%s")/60/60);		 
	$dh=$dh+(date_interval_format(date_diff($t0,$t1),"%a")*24);
	$dh=abs($dh);
	
	return $dh;
}

function calculaVelocidad ($tiempoH,$distanciaKM) {
/* Calculo Velocidad entre dos puntos dados el tiempo y la distancia */
// tiempo en horas
// distancia en km

	IF ($tiempoH>0) {
		 $dvel = ($distanciaKM / $tiempoH);
	}
	else
	{
		 $dvel = 0.00;
	}
	
	return $dvel;
}


function Filtro_3Stages_Austin($con,$datosViajes,&$eError)
{
	/* parametros para filtros */
	define ("CONST_Vmax",10);		// velocidad maxima en km/h
	define ("CONST_Dmin",0.021);	// tiempo minimo aceptable en horas entre puntos
	define ("CONST_disMax",457);    // 75 percentil de distancias semanales loc OK en agua 2006 < 1600KM

		$lon0=-63.63640;			//para calculo distanciaPDelgada, referencia: Mareografo
		$lat0=-42.76750;	

		$ndv=count($datosViajes);
	
/* que viajes y ptt hay que filtar (un viaje puede tener mas de un satelital) */
/* NO SE PROCESAN LOS TRACKS ANTERIORES A 2018 */
	$sql="SELECT DISTINCT viajeID,ptt FROM localizaciones WHERE ultimo='' AND YEAR(FECHA)>=2018";
	$result=mysqli_query($con, $sql);
	if($result===false){
		$eError="Problemas con SELECT viajeId";
		return;
	}
	/* $viajes=mysqli_fetch_all ( $result,MYSQLI_NUM ); NO SIRVE EN SERVER!!! */
	    $viajes=array();
		while ($fila = mysqli_fetch_assoc($result)) {
		  $viajes[] = $fila;
		}		
	
	
	$nv = count($viajes);

    for ($i = 0; $i < $nv; $i++)   /* todos los viajes con nuevos puntos */
//						    for ($i = 0; $i < 1; $i++) /* solo el primero */
	{
		
		// proceso el filtro del viaje completo por si se agregaron puntos intermedios a los ya existentes
		// luego, solo hago el UPDATE en la tabla desde el primer "ultimo=' '" y 2 anteriores, esto ultimo si es factible...

		$via = $viajes[$i]["viajeID"];
		$ptt = $viajes[$i]["ptt"];

		$sql="SELECT viajeID,ptt,fecha,hora,locQuality,lon,lat,lon2,lat2,estado,distanciaPDelgada,distanciaSalida,distanciaKM_p,velocidadKM_p,tiempoH_p,velocidadFiltro,ultimo FROM localizaciones WHERE viajeID=$via AND ptt=$ptt ORDER BY viajeID,ptt,fecha,hora";
		$result=mysqli_query($con, $sql);
		if($result===false) {
			$eError="Problemas con SELECT puntox indice:$i viaje:$via ptt:$ptt";
			return;
			
		}
		
		// llevo todos los puntos de un viaje a un array ($track)
		// una vez filtrado todo, actualizo la tabla localizaciones
		/* $track=mysqli_fetch_all ( $result,MYSQLI_ASSOC); NO VA EN SERVER !!!*/
		while ($fila = mysqli_fetch_assoc($result)) {
		  $track[] = $fila;
		}		
		
		$np = count($track);

		// los puntos que quedan fuera del track al filtrar
		$track_fuera = array();


		/* desde donde se proceso??? */
		/* desde el primer "ultimo=' '"   */
		if ($np<=4) {
			$f0=0;
		}
		else {
			$f0=0;
			for ($xf=0; $xf <=$np-1; $xf++)	{
				if ($track[$xf]['ultimo']==' ') {
					$f0=$xf;
				}
			}
			if ($f0<=2){
				$f0=2;
			}
		}		

		$fInicial=$f0;
		
echo  "ptt: $ptt    viaje: $via   ".count($track)." puntos totales    ";
		
		/* solo aplico filtro si la cantidad de puntos es >= 5*/
		if ($np>=5) {
			$np1 = $np-1;
			$np2 = $np-2;					

			for ($t=0; $t<$np; $t++)
			{
				$track[$t]['distanciaPDelgada']=0;
				$track[$t]['distanciaSalida']=0;
				$track[$t]['distanciaKM_p']=0;
				$track[$t]['velocidadKM_p']=0;
				$track[$t]['tiempoH_p']=0;
				$track[$t]['velocidadFiltro']=0;
				$track[$t]['estado']=0;
			}
			

			$f=$fInicial;
					// puntos que se usan para calculos y filtro
					// siendo f el actual    fp1 ---> fp2 --->  f  ---> fp3 ---> fp4
					//                                       fInicial      
					// si el filtro elimina el punto f		

			/* parte 1 */
			$dis2=0;
			$dur2=99;
			$velo2=0;
			$dur2OK = true;
			
			while ($f<$np2) 
			{
				$sigo=true;
				while ($sigo)
				{
					$fp1=$f-2;
					$fp2=$f-1;
					$fp3=$f+1;
					$fp4=$f+2;
					$lonf=$track[$f]['lon'];
					$latf=$track[$f]['lat'];
					$fef =$track[$f]['fecha'];
					$hof =$track[$f]['hora'];
					
					// ahora calculo -- siempre que pueda ...
					// para f-fp1
					$velo1 = 0;
					if ($fp1>=0) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp1]['lon'], $track[$fp1]['lat']);
						$tiempo = calculaTiempo ($track[$fp1]['fecha'], $track[$fp1]['hora'], $fef, $hof);
						$velo1 = calculaVelocidad($tiempo,$distGC);
					};
					
					// para f-fp2
					$velo2 = 0;
					$dis2 = 0;
					$dur2 = 0;  
					$dur2OK = true;
					if ($fp2>=0) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp2]['lon'], $track[$fp2]['lat']);
						$dis2 = $distGC;
						$tiempo = calculaTiempo ($track[$fp2]['fecha'], $track[$fp2]['hora'], $fef, $hof);
						$dur2 = $tiempo;
						if ($dur2 < CONST_Dmin) {
							$dur2OK = false;
						}
						$velo2 = calculaVelocidad($tiempo,$distGC);
					}			
					
					// para f-fp3
					$velo3 = 0;
					if ($fp3<=$np2) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp3]['lon'], $track[$fp3]['lat']);
						$tiempo = calculaTiempo ($track[$fp3]['fecha'], $track[$fp3]['hora'], $fef, $hof);
						$velo3 = calculaVelocidad($tiempo,$distGC);
					}
					
					// para f-fp4
					$velo4 = 0;
					if ($fp4<=$np1) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp4]['lon'], $track[$fp4]['lat']);
						$tiempo = calculaTiempo ($track[$fp4]['fecha'], $track[$fp4]['hora'], $fef, $hof);
						$velo4 = calculaVelocidad($tiempo,$distGC);
					}

					// la condicion....
					$velMc = $velo2;
					if (($fp1>=0) and ($fp2>=0) and ($fp3<=$np2) and ($fp4<=$np1)) {
						
					}
					else
					{
						// 1-2 del principio o a alguno de los 2 del final
						$sigo = false;
					}

					$lqf = $track[$f]['locQuality'];
					
					$conTodas=  !(($velo1>CONST_Vmax) and ($velo2>CONST_Vmax) and ($velo3>CONST_Vmax) and ($velo4>CONST_Vmax));
			  
					//la ultima condicion es para asegurarme que queden puntos finales coherentes en cuanto a velocidad
			
					if ( (($conTodas and ($sigo==true) and ($dur2OK) ) OR ( ($sigo==false) and ($velo2<=CONST_Vmax)  ) )  ) 
					{
						// el punto queda, salgo de este bucle
						$sigo = false;
						$f=$fp3;
					}
					else
					{
						// punto fuera -- al eliminar el punto el nuevo $f va a ser $fp3
						$track_fuera[]=$track[$f];
						array_splice($track,$f,1);
						$np = count($track);
						$np1 = $np-1;
						$np2 = $np-2;
					}
					
					
				}  /* sigo */	
				
			}  /* $f */
			
			/* partes 2-3  */
			$np = count($track);
			$np1 = $np-1;
			$np2 = $np-2;
			$dis2=0;
			$dis1=0;
			$dis3=0;
			$dis4=0;
			$dur2=99;
			$velo2=0;
			$f = $fInicial;
			while ($f<$np2) 
			{
				$sigo=true;
				while ($sigo)
				{
					$fp1=$f-2;
					$fp2=$f-1;
					$fp3=$f+1;
					$fp4=$f+2;

					$lonf=$track[$f]['lon'];
					$latf=$track[$f]['lat'];
					$fef =$track[$f]['fecha'];
					$hof =$track[$f]['hora'];

					// ahora calculo -- siempre que pueda ...
					// para f-fp1
					$velo1 = 0;
					$dis1 = 0;
					if ($fp1>=0) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp1]['lon'], $track[$fp1]['lat']);
						$tiempo = calculaTiempo ($track[$fp1]['fecha'], $track[$fp1]['hora'], $fef, $hof);
						$velo1 = calculaVelocidad($tiempo,$distGC);
						$dis1 = $distGC;
					};

					// para f-fp2
					$velo2 = 0;
					$dis2 = 0;
					$dur2 = 0;  
					if ($fp2>=0) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp2]['lon'], $track[$fp2]['lat']);
						$dis2 = $distGC;
						$tiempo = calculaTiempo ($track[$fp2]['fecha'], $track[$fp2]['hora'], $fef, $hof);
						$dur2 = $tiempo;
						$velo2 = calculaVelocidad($tiempo,$distGC);
					}			

					// para f-fp3
					$velo3 = 0;
					$dis3 = 0;
					if ($fp3<=$np2) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp3]['lon'], $track[$fp3]['lat']);
						$tiempo = calculaTiempo ($track[$fp3]['fecha'], $track[$fp3]['hora'], $fef, $hof);
						$velo3 = calculaVelocidad($tiempo,$distGC);
						$dis3 = $distGC;
					}
					
					// para f-fp4
					$velo4 = 0;
					$dis4 = 0;
					if ($fp4<=$np1) {
						$distGC   = calculaDistanciaGC($lonf, $latf, $track[$fp4]['lon'], $track[$fp4]['lat']);
						$tiempo = calculaTiempo ($track[$fp4]['fecha'], $track[$fp4]['hora'], $fef, $hof);
						$velo4 = calculaVelocidad($tiempo,$distGC);
						$dis4 = $distGC;
					}
					
					//ahora calculo la velocidad media segun McConnell para filtrar ... si se puede
					$velMc = $velo2;
				
					if (($fp1>=0) and ($fp2>=0) and ($fp3<=$np2) and ($fp4<=$np1)) {
						//$v2 = ($velo1**2) + ($velo2**2) + ($velo3**2) + ($velo4**2);
						$v2 = pow($velo1,2) + pow($velo2,2) + pow($velo3,2) + pow($velo4,2);
						$v3 = 0.25*$v2;
						$velMc = sqrt($v3);
					}
					else
					{
						//estoy a 2   reg del principio o a 2 reg del final de la seleccion
						$sigo = false;
					}				

					$conTodas = ($velMc<=CONST_Vmax);
					$dmed4 = ($dis1+$dis2+$dis3+$dis4)/4.0;

					
			//viajeID,ptt,fecha,hora,locQuality,lon,lat,lon2,lat2,estado,distanciaPDelgada,distanciaKM_p,velocidadKM_p,tiempoH_p,velocidadFiltro,ultimo
					
					if ( ($conTodas and ($dmed4<=CONST_disMax) ) OR   ($sigo=false) ) 
					{
						// el punto queda, salgo de este bucle
						$sigo = false;
						$track[$f]["velocidadFiltro"] = $velMc;
						$track[$f]["distanciaKM_p"] = $dis2;
						$track[$f]["velocidadKM_p"] = $velo2;
						$track[$f]["tiempoH_p"] = $dur2;
						$f=$fp3;
					}			
					else {
						// punto fuera -- al eliminar el punto el nuevo $f va a ser $fp3
						$track_fuera[]=$track[$f];
						array_splice($track,$f,1);
						$np = count($track);
						$np1 = $np-1;
						$np2 = $np-2;
					}
					
				}  /* sigo */	
		
			}	/* $f (2-3) */
			
		}  /* np>=5 */
		
		/* fin filtro */
		
		/* los 2 primeros puntos DEL TRACK quedan fuera del calculo del filtro 		*/
		/* idem los 2 del final														*/
		/* calculo velocidad,distancia,tiempo										*/
		
		$np = count($track);
echo  count($track)." filtro OK     ";
echo  count($track_fuera)." filtro fuera<br>";
		if ($np>=2) {
			/* principio */
			$track[1]["distanciaKM_p"] = NULL;
			$track[1]["velocidadKM_p"] = NULL;
			$track[1]["tiempoH_p"] = NULL;
			$track[1]["velocidadFiltro"] = NULL;
		}
		if ($np>=3)
			/* final */
			$np2 = $np-2;
			$np3 = $np-3;
			for ($xf=$np3; $xf <=$np2; $xf++)
			{
				$track[$xf+1]["distanciaKM_p"] = NULL;
				$track[$xf+1]["velocidadKM_p"] = NULL;
				$track[$xf+1]["tiempoH_p"] = NULL;
				$track[$xf+1]["velocidadFiltro"] = NULL;			
		}
		
		/* ahora se actualiza la tabla */
		/* desde donde se actualiza (UPDATE)??? */
		/* desde el 2 antes al primer "ultimo=' '"   */
		if ($np<=4) {
			$f0=0;
		}
		else {
			$f0=0;
			for ($xf=0; $xf <=$np-1; $xf++)	{
				if ($track[$xf]['ultimo']==' ') {
					$f0=$xf;
				}
			}
			if ($f0<=2){
				$f0=0;
			}
			else{
				$f0=$f0-2;
			}
		}
		
		

		/* prepare ... */
		$sql="UPDATE localizaciones SET estado=?,
					distanciaPDelgada=?,distanciaSalida=?,distanciaKM_p=?,
					velocidadKM_p=?,tiempoH_p=?,
					velocidadFiltro=?,ultimo=? 
					WHERE viajeID=? AND fecha=? AND hora=? AND ptt=?";
		$sentencia = mysqli_prepare($con, $sql);
		if($sentencia===false){
			$eError="Problemas con prepare (punto dentro) viaje $via, ptt $ptt, fecha $fecha, hora $hora";
			return;
		}
		mysqli_stmt_bind_param($sentencia,"sddddddsissi",$esta,$dPDel,$dSal,$dist,$veKM,$tiem,$velF,$ulti,$via,$fecha,$hora,$ptt);
		
		
		
		/* busco el viaje en datosViajes para el calculo de distanciaSalida */
		$sLat=NULL;
		$sLon=NULL;
		for ($v = 0; $v < $ndv; $v++) {
			if($via==$datosViajes[$v]['viajeID']) {
				$sLat=$datosViajes[$v]['lati'];
				$sLon=$datosViajes[$v]['longi'];
				$sPla=$datosViajes[$v]['nombre'];
				break;
			}
		}
		
		
		
		$esta="1";
		/* puntos OK quedan */
		for ($f=$f0; $f <=$np-1; $f++)
		{		
			
			$dPDel = calculaDistanciaGC($lon0,$lat0,$track[$f]['lon'],$track[$f]['lat']); /* distancia a Punta Delgada */
			$dSal  = calculaDistanciaGC($sLon,$sLat,$track[$f]['lon'],$track[$f]['lat']); /* distancia a salida */
			$fecha = $track[$f]['fecha'];
			$hora = $track[$f]['hora'];
			$dist = $track[$f]['distanciaKM_p'];
			$veKM = $track[$f]['velocidadKM_p'];
			$tiem = $track[$f]['tiempoH_p'];
			$velF = $track[$f]['velocidadFiltro'];
			$ulti = $track[$f]['ultimo'];
			/* x son los puntos mas viejos, u son los ultimos agregados-filtrados, " " si agregados y sin filtrar*/
			if ($ulti=="u") {
				$ulti='x';
			}
			elseif ($ulti=="") {
				$ulti="u";
			}
			mysqli_stmt_execute($sentencia);
			if($sentencia===false){
				$eError="Problemas con stmt_execute (punto dentro) viaje $via, fecha $fecha, hora $hora";
				return;
			}
//echo " $via $ptt $fecha $hora $dist $veKM $velF $ulti dentro<br>";
		}

		
		/* puntos fuera */
		$esta="0";
		$np=count($track_fuera);
		for ($f=0; $f <=$np-1; $f++)
		{		
			
			$dPDel = calculaDistanciaGC($lon0,$lat0,$track_fuera[$f]['lon'],$track_fuera[$f]['lat']); /* distancia a Punta Delgada */
			$dSal  = calculaDistanciaGC($sLon,$sLat,$track_fuera[$f]['lon'],$track_fuera[$f]['lat']); /* distancia a salida */
			$fecha = $track_fuera[$f]['fecha'];
			$hora = $track_fuera[$f]['hora'];
			$dist = NULL;
			$veKM = NULL;
			$tiem = NULL;
			$velF = NULL;
			$ulti = $track_fuera[$f]['ultimo'];
			/* x son los puntos mas viejos, u son los ultimos agregados-filtrados, " " si agregados y sin filtrar*/
			if ($ulti=="u") {
				$ulti='x';
			}
			elseif ($ulti=="") {
				$ulti="u";
			}
			mysqli_stmt_execute($sentencia);
			if($sentencia===false){
				$eError="Problemas con stmt_execute (punto fuera) viaje $via, fecha $fecha, hora $hora";
				return;
			}
//echo " $via $ptt $fecha $hora $dist $veKM $velF $ulti fuera <br>";
			
		}		
		
	} /* viajes */

return;
}	
	
	
	
	
	
	
	
	
	
	
/**************************************************************************/	
	$tamanio_archivo=CONST_tamanioMaxArchivo;
	    
	/*sin permiso de edición, fuera*/
	//siErrorFuera(edita()); 

	$viajeID = null;
	$ptt = null;
	$fecha = null;
	$hora = null;

	$archivoAsubir=null;
	$eError=null;
	$archivo=null;
	$valid = false;
	
    if ( !empty($_POST)) {

		if( isset($_POST["procesar"])) {
			$eError = null;
			$archivo = limpia($_POST['archivo']);
			
			$valid = true;

			/* el archivo TXT de ARGOS DIAG */				
			$eError  = validar_ArgosArchivo ($archivo,$valid,true);				
			if (is_null($eError)) {
				$ueb_dir = dirArgos; 		/* "localizacionesTMP/"; */
				$ueb_Archi = $ueb_dir . basename($_FILES["archivoAsubir"]["name"]);
				$ArchiType = pathinfo($ueb_Archi,PATHINFO_EXTENSION);
				
				$txtError = "";
				$check = filesize($_FILES["archivoAsubir"]["tmp_name"]);
				if($check === false) {
					$txtError .= "Por favor, seleccionar archivo. ";
					$valid = false;
				}

				// tamaño archivo
				if ($_FILES["archivoAsubir"]["size"] > $tamanio_archivo) {
					$txtError .= CONST_tamanioMaxArchivo_men;
					$valid = false;
				}

				// formatos soportados
				if ($ArchiType != "txt" and $ArchiType != "TXT") {
					$txtError .= "Solo archivos de texto .txt";
					$valid = false;

				}
				if (!empty($txtError)) {
					$eError = $txtError;
				}
			}

			if ($valid) {	
				// todo OK hasta acá, probamos subir el archivo
				if (!move_uploaded_file($_FILES["archivoAsubir"]["tmp_name"], $ueb_Archi)) {
						$eError =  "Ocurri&oacute; un error al subir el archivo!!";
						$valid = false;
					} 
					
			}


		}  /* subir */
    }	/* POST */
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>


  <link rel="stylesheet" href="assetsMobi/web/assets/mobirise-icons/mobirise-icons.css">
  <link rel="stylesheet" href="assetsMobi/bootstrap/css/bootstrap.min.css">

    
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    
    <script src="js/imiei.js"></script>
    

    <link rel="stylesheet" href="login/style/main.css">
	
	
	
    <link   href="css/miSideBar.css" rel="stylesheet">

	
<style>
 input[type="checkbox"] {
    margin: 0px 0 0}
</style>	


	<script> 
		function archis(){
			ar=document.getElementById("archivoAsubir");	
			if (ar.files[0].size> <?php echo CONST_tamanioMaxArchivo ?> ) {
				alert ("<?php echo CONST_tamanioMaxArchivo_men ?>");
				return;
			}
			arNombre=ar.files[0].name;
			arnom=arNombre.toUpperCase();
			if ('files' in ar) {
				if (ar.files.length == 0) {
					alert("Seleccionar un archivo, por favor");
					return;
				} else {
					if (!arnom.endsWith(".TXT")) {
						alert("Seleccionar un archivo .txt") ;
						return;
					};
					/* */
					var patt = new RegExp("<?php echo PATRON_archiArgos ?>");
					if (patt.test(arNombre)) {
						document.getElementById("archivo").value=arNombre;
					}
					else{
						document.getElementById("archivo").value="";					
						alert(arNombre+"\n"+"<?php echo PATRON_archiArgos_men ?>");
					}
				}
			} 
			else {
					alert("seleccionar un archivo");
				}
					
					
			
		}
	</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
		
</head>

<body  class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>
    <div w3-include-html="sideBar_oremota.html"></div>

	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
<BR><BR><BR><BR>
<form data-toggle="validator" id="CRArgos" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post" enctype="multipart/form-data">              

	<input name="ueb_Archi" type="hidden"  value="<?php echo !empty($ueb_Archi)?$ueb_Archi:'';?>">

	
   <div class="container" style="width:90%">

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Ingreso de localizaciones de ARGOS formato DIAG</h3>
            </div>
            <div class="panel-body" >
			
  <a href="#demo" class="btn btn-info" data-toggle="collapse">ejemplo DIAG</a>
  <div id="demo" class="collapse">
			
<pre>
 A&Ntilde;O 2018
 ATENCI&Oacute;N: ESTE FORMATO ES ESTRICTO!! 
 SI ARGOS CAMBIA EL FORMATO DEBER&Aacute; MODIFICARSE EL C&Oacute;DIGO DE ESTE SCRIPT.
 
<span class="glyphicon glyphicon-arrow-down" style="margin-left: -5px;"></span><small>columna 1</small>
 Prog 06080

 161606 Date : 27.02.18 11:42:48  LC : B  IQ : 00
      Lat1 : 42.416S  Lon1 :  64.993W  Lat2 : 42.416S  Lon2 :  64.993W
      Nb mes : 002  Nb mes>-120dB : 000  Best level : -135 dB
      Pass duration : 090s   NOPC : 3
      Calcul freq : 401 677430.5 Hz   Altitude :    0 m
              06          172          210          186
              94           75           36           49
.....
.....			 
</pre>			
	  </div>
			
<br><br>
			
			
			
					<!-- archivo -->
                    <div class="row">     
                        <div class="col-sm-2"> 
                            <label style="width:100%" class="btn btn-warning btn-sm btn-file">
                                <span class="glyphicon glyphicon-file"></span> archivo
                                <input type="file" style="display: none;"  accept=".txt,.TXT" name="archivoAsubir" id="archivoAsubir" onchange=archis()>
                            </label>
                        </div>
                        
                        <div class="col-sm-8">
                            <div class="form-group ">
                        
                                <input type="text" class="form-control input-sm" id=archivo name=archivo required onkeydown="return false;"
								value="<?php echo !empty($archivo)?$archivo:'';?>">
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorarchivo"></p>

							</div>
                        </div>
                    </div>
                    <div class="row">                             
                        <div class="col-sm-2"> 
							<button type="submit" class="btn btn-primary btn-sm" id="procesar" name="procesar">procesar archivo
                                <span class="glyphicon glyphicon-upload"></span> 
							</button>						
                        </div>
                    </div>
 
            </div>  
        </div>
	</div>                
    

  
	<div class="container" style="width:90%">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"></h3>
            </div>

			<div class="panel-body" >
				<div class="row">
				
					<div class="col-sm-12">
		
<?php
			if ($valid and isset($_POST["procesar"])) {
				/* proceso archivo */

				$gestor = @fopen($ueb_Archi, "r");
				if($gestor===false) {
					$valid = false;
					$eError = "Error creando gestor archivo";
				}
				
				
				$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
				if (mysqli_connect_errno())
					{
					  $valid=false;
					  $eError = "No se puede conectar a MySQL: " . mysqli_connect_error();
					}
                else
				{					
					$condi="''";
					/* genera vtResultado, tabla temporaria usada por los siguientes procedures */
					$sql = "CALL `locx-viajes Seleccion`($condi)";
					if (mysqli_query($con,$sql)===false){
						$eError="Problemas con CALL 0. ";
						$valid=false;
					}
					
					/* la siguiente llamada da una tablita con datos por viaje */					
					$sql="CALL `locx-viajes y tracks`()";
					if(mysqli_real_query($con, $sql)===false){
					  $valid=false;
					  $eError = "Problemas con CALL 1.".mysqli_error($con);
					}
					else
					{
						$result=mysqli_store_result($con);
						/* $datosViajes=mysqli_fetch_all ($result,MYSQLI_ASSOC); NO VA EN SERVER!!! */
						while ($fila = mysqli_fetch_assoc($result)) {
						  $datosViajes[] = $fila;
						}		
						
						mysqli_free_result($result);
						$ndv=count($datosViajes);
								/* vacio result sets ocultos */
								while (mysqli_more_results($con) && mysqli_next_result($con)) 
								{
									if($result=mysqli_store_result($con)) {
										mysqli_free_result($result);
									}
								}	
						mysqli_close($con);
														
						$nPuntos =0;
						$ptt = null;
						$fec = null;
				$iValid=true;		//si true procesa para ese ptt
						$aDatos1 = array();	//para linea con Date...
						$aDatos2 = array();	//para lineas con Lat...
						echo "<pre>";
						echo "  ptt   primer fecha   viajeID    claveU    fecha de   marcas<br>";
						echo "         en archivo                        colocaci&oacute;n<br>";
						echo "-----------------------------------------------------------------------------<br>";
						while (!feof($gestor)) 
						{
							$buf = fgets($gestor);
							$elemTXT = preg_split("/[\s,]+/", $buf);
							$ne = count($elemTXT);
							if($ne>=3) {
								if ($elemTXT[2]=='Date') {
									if ($elemTXT[1]!==$ptt) {
										$ptt=$elemTXT[1];
										$fec=$elemTXT[4];
										$fec=fechaArgosSis($fec);
										echo str_pad($ptt,6," ")."   $fec    "; 
										/* info en datosViajes ...
											viajeID,claveU,fecha_colocacion,marcas,
											idplaya, nombre, lati, longi (de la playa),
											fin_viaje (null/viaje completo/viaje incompleto),
											ptt, conLoc (si tiene ya localizaciones:null/tiene),
											que_instrumentos (lista de los instrumentos colocados)
											
											Se asume que los puntos que se ingresan corresponden a viajes hoy en el agua!!!
											
											Los viajes que cuentan son los que cumplen
											-fin_viaje=null (iniciado)
											-el ptt en elemTXT corresponde con un viaje registrado
											-el año en elemTXT es igual al año de la fecha_colocacion[+1]
											-la fecha en elemTXT es mayor o igual a la fecha_colocacion 
										 
										 */
										$conViaje=0;
										$espacios="  ";
										$qVia=NULL;
							$iValid=true;
										$NoViajando=true;
										for ($v=0; $v<$ndv; $v++) {
											
//											if( is_null($datosViajes[$v]['fin_viaje']) and
//											if( $datosViajes[$v]['fin_viaje']=='VIAJANDO' and
											if(($ptt==$datosViajes[$v]['ptt'] or 
												pttEnInstrumentos($ptt,$datosViajes[$v]['que_instrumentos'])) and
												AniosT($fec,$datosViajes[$v]['fecha_colocacion']) and
												$fec>=$datosViajes[$v]['fecha_colocacion'] )
											{
												$qVia=$datosViajes[$v]['viajeID'];
												$conViaje+=1;
												echo  $espacios.$datosViajes[$v]['viajeID'].'   ';
												echo '    <a title="ir a la ficha" target=_blank href="tr_resumen_individuo.php?claveU='.$datosViajes[$v]['claveU'].'">'.
												$datosViajes[$v]['claveU'].'</a>    '.
												$datosViajes[$v]['fecha_colocacion'].'  '.
												$datosViajes[$v]['marcas']."<br>";
												$espacios= str_repeat(' ',25);
												if($datosViajes[$v]['fin_viaje']=='VIAJANDO'){
													$NoViajando=false;
												}
											}
										}

										if($NoViajando and $conViaje>0){
											$iValid=false;
											echo '<br>   El <strong>estado del viaje es COMPLETO/INCOMPLETO.</strong><br><br>';
										}
										if($conViaje==0)
										{
											$iValid=false;
											echo '<br>   El individuo al que corresponde el viaje no fue ingresado<br>';
											echo '   o no tiene viaje establecido.<br>';
							//	$valid=false;
											
										}elseif ($conViaje>1)
										{
							//	$valid=false;
											$iValid=false;
											echo '   Debe haber un &uacute;nico viaje activo para el ptt.<br>';
											echo '   Al individuo que corresponda, en la <strong>fecha de colocaci&oacute;n</strong><br>';
											echo '   ir a <strong>viaje/etapa/configuración y estado del viaje</strong>,<br>';
											echo '   y modificar el <strong>"estado del viaje"</strong> a VIAJE COMPLETO/INCOMPLETO<br>';
										}
										echo '<br>';
									}
								}
								if ($iValid) {
									if ($elemTXT[2]=='Date') {
										$aDatos1[]=$elemTXT;
										$nPuntos +=1;
//echo 'Date: '.$nPuntos.'  ';										
									}
									if ($elemTXT[1]=='Lat1') {
										$elemTXT[]=$qVia;		//el viaje al final de la linea..
										$aDatos2[]=$elemTXT;
//echo 'Lat1: '.$qVia.'<br>';										
									}
								}
							}
						}
						echo "</pre>";

						fclose($gestor);
						if ($nPuntos==0) {
							$valid=false;
							$eError="No hay puntos para procesar.";
						}
					}
				}
				
				
			} /*valid procesar */
			
			
			
//$valid=false;			





			if ($valid and isset($_POST["procesar"]) ){

				/* en el ciclo anterior se completaron los arreglos aDatos1 y aDatos2 
				   con las lineas leidas con Date... y Lat1... respectivamente 
				   Cada elemento de estos arreglos es un arreglo con el siguiente contenido 
				   0|   1  |    | |   4    |    5   |  | |8|...... 					aDatos1[]
				    |161606|Date|:|27.02.18|11:42:48|LC|:|B|IQ|:|00|
					
				   0|    | |   3   |    | |   6   |    | |   9   |    | |   12   | |...aDatos2[]
				    |Lat1|:|42.416S|Lon1|:|64.993W|Lat2|:|42.416S|Lon2|:|64.993W | | #viaje
					
				*/
				
				echo '<br><br>';
				echo "<pre>";
				echo "INGRESO <br>";
				
				$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
				if (mysqli_connect_errno())
					{
					  $valid=false;
					  $eError = "No se puede conectar a MySQL: " . mysqli_connect_error();
					}
                else
				{		
					$nZ=0;
					$nEstaba=0;
					$nNuevos=0;
					$nErrores=0;
					for ($p=0; $p<$nPuntos; $p++){
						$lq=$aDatos1[$p][8];
						if ($lq=='Z'){
							$nZ+=1;
							//echo 'fuera '.$aDatos2[$p][14].' '.$aDatos1[$p][1].' '.
							//		$aDatos1[$p][4].' '.$aDatos1[$p][5].' '.
							//		$lq.' '.$aDatos2[$p][3].' '.$aDatos2[$p][6].' '.$aDatos2[$p][9].' '.$aDatos2[$p][12].'<br>';
						}
						else
						{
							$sql = "INSERT INTO localizaciones (viajeID,ptt,fecha,hora,locQuality,lon,lat,lon2,lat2) VALUES(".
							$aDatos2[$p][14].",".
							$aDatos1[$p][1] .",'".
							fechaArgosSis($aDatos1[$p][4])."','".
							$aDatos1[$p][5]."','".
							$aDatos1[$p][8]."',".
							llArgosSis($aDatos2[$p][6]).",".
							llArgosSis($aDatos2[$p][3]).",".
							llArgosSis($aDatos2[$p][12]).",".
							llArgosSis($aDatos2[$p][9]).")";  
							if (!mysqli_query($con,$sql)) {
								if(mysqli_errno($con)==1062) {
									$nEstaba +=1;									
								}
								else {
									$nErrores +=1;
									echo '    Error: '.$aDatos2[$p][14].' '.$aDatos1[$p][1].' '.
									$aDatos1[$p][4].' '.$aDatos1[$p][5].' '.mysqli_errno($con).' '.mysqli_error($con)."<br>";
								}
							}
							else {
								$nNuevos +=1;
							}
						}
					}
					echo '   Localizaciones que quedan fuera con LQ=Z... '.$nZ."<br>";
					echo '                                ya estaban ... '.$nEstaba."<br>";
					echo '                               con errores ... '.$nErrores."<br>";
					echo '                                     nuevas... '.$nNuevos."<br>";
					
					
					/* filtro por THREE-STAGE AUSTIN */
					echo "<br><br>FILTRO <br>";
					Filtro_3Stages_Austin($con,$datosViajes,$eError);
					
					mysqli_close($con);			
				}
				echo "</pre>";
				
			}
?>		
		
		
					</div>
		
				</div>
				<div class="row">
					<div class="col-sm-1">
					</div>
					<div class="col-sm-8">
						<?php if (!empty($eError)): ?>
							<span class="help-inline"><?php echo $eError;?></span>
						<?php endif; ?>

					</div>
				</div>                
				
			</div>
		</div>
	</div>

</form>

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

</body>
</html>