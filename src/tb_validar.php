<?php

// contiene constantes declaradas para validación
require_once 'tb_validar_Constantes.php';



// forms - VALIDACIONES CRUZADAS DENTRO DE UN FORMULARIO
//validarForm_usuarios ($nombre_apellido,$email,$alias,$clave,$permiso,$valid);
function validarForm_usuarios ($nomape,$ema,$ali,$cla,$per,&$ok) {
    
}


//validarForm_anestesia ($claveU,$fecha,$hora,$tipo,$aplicacion,$droga,$droga_ml,$partida,$anotacion,$valid)//
function validarForm_anestesia ($cu,$fec,$hor,$tip,$apli,$dro,$dro_ml,$par,$anot,&$ok) {
 If ( ( ($tip=="COMENTA") OR ($tip=="INDUCCION") OR (strpos($tip,"FIN")<>false) ) AND 
       ( ($apli<>"no corresponde") OR ($dro<>"no corresponde") OR (!vacio($dro_ml)) OR (!vacio($par)) ) )
       {
        $ok=false;
        return 'Etapa '.trim($tip).": uno o m&aacute;s campos con valores faltantes";
  }
 If ( ( ($tip=="INICIAL") OR ($tip=="SIGUIENTE")  ) AND 
       ( (($apli<>"IM") and ($apli<>"IV")) or (vacio($dro)) or (vacio($dro_ml)) ) ) 
       {
        $ok=false;
        return 'Etapa '.trim($tip).": uno o m&aacute;s campos con valores faltantes";
  }  

}

//validarForm_categoria ($IDcategoria,$cateDesc,$grupoEdad,$reproductiva,$ordenCate,$edadRelativa,$sexoc,$dOjo,$valid)//
function validarForm_categoria ($IDcat,$cDesc,$gEdad,$rep,$oCate,$eRel,$sex,$dOjo,&$ok){

}

//validarForm_censista ($fecha,$libreta,$IDcolaborador,$valid)//
function validarForm_censista ($fec,$lib,$IDcol,&$ok){
    
}

//validarForm_censo ($fecha,$tipo,$fechaTotal,$comentario,$valid)//
function validarForm_censo ($fec,$tip,$fecTot,$com,&$ok) {
    $lafec  = date_create_from_format('Y-m-d', $fec);
    $lafecT = date_create_from_format('Y-m-d', $fecTot);

    if (date_interval_format(date_diff($lafec,$lafecT),"%a")>10) {
            $ok=false;
            return 'La diferencia entre fechas no puede ser mayor a 10 dias';     
        }
    
    
}

//validarForm_colaborador ($IDcolaborador,$apellido,$nombre,$email,$comentario,$valid)//
function validarForm_colaborador ($IDcol,$ape,$nom,$ema,$com,&$ok) {
    if (empty($ape) and empty($nom)) {
            $ok=false;
            return 'No pueden faltar ambos apellido y nombre';          
    }
}

//validarForm_copula ($claveU,$fecha,$hora,$conCual,$duracion,$comentario,$valid)//
function validarForm_copula ($cU,$fec,$hor,$cCual,$dura,$com,&$ok) {
    
}

//validarForm_criadestetado ($claveU,$fecha,$fechaDestete,$estimada,$edadDestete,$comentario,$valid);//
function validarForm_criadestetado ($cU,$fec,$fDes,$esti,$edadP,$com,&$ok) {
    
}


//validarForm_equipotrabajo ($fecha,$IDcolaborador,$valid)//
function validarForm_equipotrabajo ($fec,$IDcol,&$ok) {
    
}

//validarForm_grupo ($fecha,$libreta,$orden,$playa,$referencia,$desde,$tipoPlaya,$geomTex,$comentario,$longi,$lati,$longiH,$latiH,$distancia,$valid)//
function validarForm_grupo ($fec,$lib,$ord,$pla,$ref,$des,$tPla,$gTex,$com,$llo,$lla,$lloH,$llaH,$dis,&$ok) {
    
}

//validarForm_hembra ($claveU,$fecha,$estado,$fechaParto,$estimada,$edadPup,$comportamientoMaternal,$comentario,$valid)//
function validarForm_hembra ($cU,$fec,$est,$fecPar,$esti,$edadP,$cMat,$com,&$ok) {
    
}


//validarForm_idpapers ($claveU,$id,$identificaciones,$valid)//
function validarForm_idpapers ($cU,$idPap,$ides,&$ok) {
    
}

//validarForm_individuo ($claveU,$sexo,$nuestro,$muerto,$muertoTempo,$comentario,$valid)//
function validarForm_individuo ($cU,$sex,$nue,$mue,$mueT,$com,&$ok) {
    
}

//validarForm_instrumentos_colocados ($viajeID,$instrumentoNRO,$fecha_recuperacion,$comentario,$valid)//
function validarForm_instrumentos_colocados ($viaID,$instru,$fecharec,$com,&$ok) {
	if (!vacio($fecharec)) {
		/* fecha de colocacion para el viaje */
		$pdo = Database::connect();
		$sql = "SELECT fecha_colocacion FROM viaje_config WHERE viajeID=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($viaID));
		if ($q->rowCount()<>1) {
				Database::disconnect();
				$ok=false;
				return "OO, este viaje no existe en viaje_config!! (".$viaID.")";
				}
		$a = $q->fetch(PDO::FETCH_ASSOC);
		$fcolo =$a['fecha_colocacion'];
		Database::disconnect();	
	
		/* verifico que la fecha de recuperacion no sea anterior a la de colocacion */
		if ($fcolo >= $fecharec){
			$ok=false;
			return "La recuperac&iacute;on no puede ser anterior a la colocac&iacute;on: ".$a['fecha_colocacion'];
		}
		
		/* verifico que entre la fecha de colocacion y de recuperacion no haya más de 360 días */
		$fco  = date_create_from_format('Y-m-d', $fcolo);
		$fre  = date_create_from_format('Y-m-d', $fecharec);
		if (intval(date_interval_format(date_diff($fco,$fre),"%a")) > 360) {
			$ok=false;
			return "M&aacute;s de 360 días entre la colocac&iacute;on y recuperac&iacute;on";
		
		}
		
	}
}

function instrumentos_colocados_intervalo_fechas ($viaID,$mdias,&$ok,&$actDisp) {
	/* fecha de colocacion para el viaje */
	/* mdias es el margen de dias hacia el pasado desde la fecha de hoy */
	
	$pdo = Database::connect();
	$sql = "SELECT fecha_colocacion FROM viaje_config WHERE viajeID=?";
	$q = $pdo->prepare($sql);
	$q->execute(array($viaID));
	if ($q->rowCount()<>1) {
			Database::disconnect();
			$ok=false;
			return "OO, este viaje no existe en viaje_config!! (".$viaID.")";
			}
	$a = $q->fetch(PDO::FETCH_ASSOC);
	$fcolo =$a['fecha_colocacion'];
	Database::disconnect();	
	
	$actDisp=false;  /* el margen es de mas de "mdias" */
	$fco  = date_create_from_format('Y-m-d', $fcolo);
	$fhoy = date_create(date('Y-m-d')); 
	if (intval(date_interval_format(date_diff($fco,$fhoy),"%a")) <= $mdias) {
			$actDisp=true; /* el margen es de menos de "mdias" */
	}
}



/* para fechas de colocacion y recuperacion - no la uso*/
function instrumentos_colocados_intervalo_fechas0 ($viaID,$fecharec,$mdias,&$ok,&$actDisp) {
	/* fecha de colocacion para el viaje */
	/* mdias es el margen de dias hacia el pasado desde la fecha de hoy */
	
	$pdo = Database::connect();
	$sql = "SELECT fecha_colocacion FROM viaje_config WHERE viajeID=?";
	$q = $pdo->prepare($sql);
	$q->execute(array($viaID));
	if ($q->rowCount()<>1) {
			Database::disconnect();
			$ok=false;
			return "O O, este viaje no existe en viaje_config!! (".$viaID.")";
			}
	$a = $q->fetch(PDO::FETCH_ASSOC);
	$fcolo =$a['fecha_colocacion'];
	Database::disconnect();	
	
	$actDisp=false;
	if (!vacio($fecharec)) {			
		/* se puede actualizar "disponible" si la fecha de recuperacion no es de mas de "mdias" dias respecto de hoy*/
		$frec  = date_create_from_format('Y-m-d', $fecharec);
		$fhoy = date_create(date('Y-m-d')); 
		if (intval(date_interval_format(date_diff($frec,$fhoy),"%a")) <= $mdias) {
				$actDisp=true;
		}	
	}
	else {
		/* cuando agrega instrumento, se puede actualizar "disponible" si la fecha de colocacion no es de mas de "mdias" dias respecto de hoy*/
		$fco  = date_create_from_format('Y-m-d', $fcolo);
		$fhoy = date_create(date('Y-m-d')); 
		if (intval(date_interval_format(date_diff($fco,$fhoy),"%a")) <= $mdias) {
				$actDisp=true;
		}
	}
}

//validarForm_instrumentos ($instrumentoNRO,$tipo,$identificacion,$serial_num,$modelo,$fabricante,$nuestro,$disponible,$comentario,$valid)//
function validarForm_instrumentos ($insN,$tip,$ide,$snum,$mod,$fab,$nue,$disp,$com,&$ok) {
    
}
//validarForm_macho ($claveU,$fecha,$status,$estadoFisico,$entornoAlAlfa,$alAlfaCu,$haremHembras,$haremPups,$comentario,$valid)//
function validarForm_macho ($cU,$fec,$sta,$estF,$eAlfa,$eAlfaCu,$hHem,$hPups,$com,&$ok) {
	if ($sta == 'ALFA'){
		if($hHem<$hPups){
			$ok=false;
			return "La cantidad de pups no puede ser mayor a la de hembras";
		}
	}    
}


//validarForm_madrehijo ($clavePup,$temporada,$claveMam,$valid)//
function validarForm_madrehijo ($cPup,$temp,$cMam,&$ok) {
    
}


//validarForm_marca ($claveU,$fecha,$marca,$comentario,$valid)//
function validarForm_marca ($cU,$fec,$mar,$com,&$ok) {
    
}

//validarForm_medidas ($claveU,$fecha,$largoStd,$largoCurva,$circunferencia,$peso,$nostril,$largo_aleta_ext,$largo_aleta_int,edadMedida,$comentario,$valid)//
function validarForm_medidas ($cU,$fec,$lStd,$lCur,$circ,$pes,$nost,$laext,$laint,$edadM,$com,&$ok) {
    $s=$lStd+$lCur+$circ+$pes+$nost+$laext+$laint+$edadM;
    if ($s==0){
        $ok=false;
        return "Debe ingresarse al menos una medida. ";
    }
}

//validarForm_muda ($claveU,$fecha,$porcentaje,$estado,$comentario,$valid)//
function validarForm_muda ($cU,$fec,$porc,$esta,$com,&$ok) {
    if ($porc == '' and $esta== "") {
        $ok=false;
        return "Debe ingresarse porcentaje y/o estado. ";
    }
    
}

//validarForm_muestras ($claveU,$fecha,$codigoADN,$sangre,$bigotes,$pelos,$fotogrametria,$distancia,$comentario,$valid)//
function validarForm_muestras ($cU,$fec,$cADN,$sang,$bigo,$pel,&$fotog,$dist,$com,&$ok) {
	if($dist>0){
			$fotog='SI';
		}
	if ($cADN=="" and $sang=="NO" and $bigo=="NO" and $pel=="NO" and $fotog=="NO") {
        $ok=false;
        return "Indicar al menos un tipo de muestra";		
	}
    
}

//validarForm_observado validarForm_observado ($claveU,$fecha,$temporada,$tipoTempo,$playa,$comentario,$valid)//
function validarForm_observado ($cU,$fec,$temp,$tTemp,$pla,$com,&$ok) {
    
}

//validarForm_playa validarForm_playa ($IDplaya,$tipo,$nombre,$norteSur,$geomTex,$comentario,$valid)//
function validarForm_playa ($IDpla,$tip,$nom,$norSur,$gTex,$com,&$ok) {
    
}

//validarForm_recuento ($fecha,$libreta,$orden,$categoria,$sexo,$status,$cantidad,$valid)//
function validarForm_recuento ($fec,$lib,$ord,$cate,$sex,$sta,$cant,&$ok) {
	$o=true;
	$r=validar_sexoVScategoria($cate,$sex,$o,$repro);
	if(!$o){
		$ok=false;
		return $r;
	}

    /*sexo vs status*/
    if ((( $sex=='HEMBRA' or $sex=='NODET' ) and ($sta<>'no corresponde')) OR
        (( $sex=='MACHO') and ($sta=='no corresponde') and (repro=='SI') and ($_SESSION['tipocen']<>'MUDA'))){
        $ok=false;
        return "No corresponde el status ($sta) para la categor&iacute;a";
        }       
    
    return;
}



function validar_sexoVScategoria($ca,$se,&$o,&$repro){
	/*sexo ($se) vs categoria ($ca)*/
    $pdo = Database::connect();
    $sql = "SELECT IDcategoria, sexoc,reproductiva FROM categoria WHERE IDcategoria=?";
    $q = $pdo->prepare($sql);
    $q->execute(array($ca));;
    if ($q->rowCount()<>1) {
            Database::disconnect();
            $o=false;
            return "No existe esa categor&iacute;a ($ca)";
            }
    $aCat = $q->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();
    $d=$aCat['sexoc'];
	$repro==$aCat['reproductiva'];
    if ($d=='CUALQUIERA'){
        $d='NODET';
    }
    if ( $d<>'NODET' and $d<>$se ) {
        $o=false;
        return "No corresponde el sexo ($se) para la categor&iacute;a";
        }
}




//validarForm_sector_copiado ($fecha,$libreta,$zona_copia,$fecha_copia,$libreta_copia,$orden_desde,$orden_hasta,$comentario,$valid)//
function validarForm_sector_copiado ($fec,$lib,$zcopia,$fecopia,$libcopia,$orddesde,$ordhasta,$com,&$ok) {
    
}

//validarForm_sector ($fecha,$libreta,$horaInicio,$horaFin,$geomTex,$zonaRecorrida,$direccionRecorrida,$marea,$clima,$comentario,$valid)//
function validarForm_sector ($fec,$lib,$hIni,$hFin,$geo,$zonaR,$dir,$mar,$cli,$com,&$ok) {
    if ((!empty($hIni)) and (!empty($hFin))) {
        $f0 ='2017-01-01 '.$hIni;
        $f1 ='2017-01-01 '.$hFin;
        $t0 = date_create_from_format('Y-m-d H:i:s', $f0);
        $t1 = date_create_from_format('Y-m-d H:i:s', $f1);
        $sig = date_interval_format(date_diff($t0,$t1),"%R");
        $dh = date_interval_format(date_diff($t0,$t1),"%h")+
             (date_interval_format(date_diff($t0,$t1),"%i")/60) + 
             (date_interval_format(date_diff($t0,$t1),"%s")/60/60);
        if (($sig=="-") or ($dh>7)) {
            $ok=false;
            return 'Hora fin posterior a hora inicio, o, intervalo inicio-fin mayor a 7 horas.';
        }
    }     
}

//validarForm_tag ($tag,$claveU,$fecha,$borradoTempo,$encontradoTempo,$encontradoPlaya,$comentario,$valid)//
function validarForm_tag ($ta,$cU,$fec,$borTemp,$encTemp,$encPla,$com,&$ok) {
	if(!vacio($borTemp) and !vacio($encTemp) ) {
		if($borTemp > $encTemp) {
			$ok=false;
			return 'A&ntilde;o en que aparece poco legible no puede ser anterior al a&ntilde;o en que se lo encuentra suelto';
		}
	}
   
}




// SOLO CUANDO AGREGAR TEMPORADA
//validarForm_temporada ($claveU,$temporada,$tipoTempo,$categoria,$comentario,$valid)//
function validarForm_temporada ($cU,$temp,$tTemp,$cate,$com,&$ok,$sex) {
	$o=true;
	$r=validar_sexoVScategoria($cate,$sex,$o,$repro);
	if(!$o){
		$ok=false;
		return $r;
	}
	
	
	/* veamos respecto de la fecha de hoy ... */
	if ($temp == date("Y")) {
		$fhoy = date("Y-m-d");  /* donde cae esta fecha? */
		$eok=true;
		$r=validar_fecha ($fhoy,$eok,true);

		if($r==='En REPRO. ' or $r==='En REPRO. En MUDA. '){
			/* dentro del año el tipo de temporada puede ser cualquiera, seguimos validando*/
		}
		if($r==='En MUDA. ' and !($tTemp=="MUDA" or $tTemp=="mudaP") ){
		  $ok=false;
		  return "El tipo de temporada solo puede ser de muda. ";
		}
		if($r==='En FUERA. ' and ($tTemp=="REPRO" or $tTemp=="reproP") ){
		  $ok=false;
		  return "El tipo de temporada no puede ser reproductiva. ";
		}
	}
		
	/* tiene temporada de marca? */
    $pdo = Database::connect();
    $sql = "SELECT DISTINCT tipoTempo FROM temporada WHERE claveU=? ORDER BY 1";
    $q = $pdo->prepare($sql);
    $q->execute(array($cU));
    if ($q->rowCount()==0) {
        /* ok corresponde con nueva temporada marca */
        Database::disconnect();
        return;
    }   
    $tMarca = $q->fetchColumn(0);  /* temporada de marca */	

    $a=array("REPRO","MUDA","FUERA");
    if (in_array($tTemp,$a)) {
            Database::disconnect();
            $ok=false;
            return 'Ya tiene temporada de marca '.$tMarca;
    }
	
    /* si existe combinacion temporada-tipo...*/
    $tt=substr( strtoupper($tTemp),0,3 ); 
    $sql = "SELECT COUNT(*) FROM temporada WHERE claveU=? and temporada=? and UCASE(SUBSTRING(tipoTempo,1,3))=? ";
    $q = $pdo->prepare($sql);
    $q->execute(array($cU,$temp,$tt));
    if ($q->rowCount()==0) {
        /* error!! */
            Database::disconnect();
            $ok=false;
            return 'ERROR validarForm_temporada: sin respuesta!!!';
    }
    $cant = $q->fetchColumn(0);
    if($cant<>0){
        Database::disconnect();
        $ok=false;
        return "Para la temporada $temp ya tiene este tipo de temporada";
    }
	
    /* ojo a la secuencia respecto de las temporada+tipoTempo de marca!!!  mudaP fueraP reproP*/
    /* tempo MUDA fueraP reproP */
    /* tempo FUERA reproP */
    /* tempo REPRO (no puede agregar tipoTempo */
	
    $sql = "SELECT DISTINCT tipoTempo FROM temporada WHERE claveU=? and temporada=? ORDER BY 1";
    $q = $pdo->prepare($sql);
    $q->execute(array($cU,$temp));
    if ($q->rowCount()==0) {
        /* ok la combinacion corresponde con nueva temporada marca u otra*/
        Database::disconnect();
        return;
    }   
    $priT = $q->fetchColumn(0);  /* primer tipoTempo en temporada*/
    Database::disconnect();

    if (!in_array($priT,$a)) {
        /* ok - agrega en temporada que no es de marca */
        return;
    }
    	
    if  (!(($priT=="MUDA" and in_array($tTemp,array("fueraP","reproP")) ) OR
         ($priT=="FUERA" and $tTemp=="reproP") ) ) {
        $ok=false;
        return "El tipo de temporada $tTemp no puede ingresarse <br> para temporada de marca $temp-$priT";
    }
	

}

    
// SOLO CUANDO EDITA TEMPORADA
//validarForm_temporadaEdita ($claveU,$temporada,$tipoTempo,$categoria,$comentario,$valid,$sexx)//
function validarForm_temporadaEdita ($cU,$temp,$tTemp,$cate,$com,&$ok,$sex) {
	$r=validar_sexoVScategoria($cate,$sex,$ok,$repro);
	return $r;
}



    

//validarForm_vecindario ($IDesquema,$IDvecindario,$tipo,$NSdesde,$NShasta,$valid)//
function validarForm_vecindario ($IDesq,$IDvec,$tip,$NSdes,$NShas,&$ok) {
    if ($NSdes>$NShas) {
        $ok=false;
        return 'Playa o tramo inicial (norte) debe ser anterior a la final (sur)';
        }
	if ($tip=='PVALDES') {
		$r=validar_entero ($IDesq,150,500,$ok,true);
		return $r;
	}
	if ($tip<>'PVALDES') {
		$r=validar_entero ($IDesq,1,149,$ok,true);
		return $r;
	}
    
}

//validarForm_viaje_config ($viajeID,$claveU,$temporada,$tipoTempo, $fecha, $profundidad,$profundidad_intervalo,$temperatura,$temperatura_intervalo, $luz,$luz_intervalo,$posicionamiento,$angulo_pitch,$angulo_roll, $angulo_yaw,$camara,$argos_trans,$fin_viaje,$estrategia,$comentario,$valid)//
function validarForm_viaje_config ($viaID,$cU,$temp,$tipT,$fec, $prof,$prof_int,$tempe,$tempe_int,$lux,$lux_int,$posic,$ang_p,$ang_r,$ang_y,$cam,$argos_t,$fin_via,$estra,$com,&$ok) {
	if( vacio($prof) and vacio($prof_int) and vacio($tempe) and vacio($tempe_int) 
		and vacio($lux) and vacio($lux_int) and vacio($posic) and vacio($ang_p) and 
		vacio($ang_r) and vacio($ang_y) and vacio($cam) and vacio($argos_t) and 
		vacio($fin_via) and vacio($estra) and vacio($com) ) {
        $ok=false;
        return 'Debe haber al menos un dato';
		}
	$m="";
	if (!vacio($prof_int) and vacio($prof)){
        $ok=false;
        $m.='Falta respuesta en profundidad. ';		
	}
	if (!vacio($prof_int) and $prof=='NO'){
        $ok=false;
        $m.='Profundidad es NO y tiene intervalo. ';		
	}
	if (!vacio($tempe_int) and vacio($tempe)){
        $ok=false;
        $m.='Falta respuesta en temperatura. ';		
	}
	if (!vacio($tempe_int) and $tempe=='NO'){
        $ok=false;
        $m.='Temperatura es NO y tiene intervalo. ';		
	}
	if (!vacio($lux_int) and vacio($lux)){
        $ok=false;
        $m.='Falta respuesta en luz. ';		
	}
	if (!vacio($lux_int) and $lux=='NO'){
        $ok=false;
        $m.='Luz es NO y tiene intervalo. ';		
	}
    
	return $m;
}

//validarForm_viaje ($claveU,$fecha,$temporada,$tipoTempo,$etapa,$comentario,$valid)//
function validarForm_viaje ($cU,$fec,$temp,$tipoT,$eta,$etaA,$com,&$ok) {
    /* verifico que no haya etapa de COLOCACION en la temporada */
	if ($eta=='COLOCACION' and $eta<>$etaA){
		$pdo = Database::connect();
		$sql = "SELECT * FROM vw_viaje_tempo_colocacion WHERE claveU=? and temporada=? and tipoTempo=? ";
		$q = $pdo->prepare($sql);
		$q->execute(array($cU,$temp,$tipoT));
		$r=$q->rowCount();
		Database::disconnect();
		if ($r>=1) {
			$ok=false;
			return "Ya tiene etapa de COLOCACION en la temporada $temp - $tipoT";
			}
		
	}
}



//validarForm_publicaciones ($ID,$tipoPublicacion,$anio,$titulo,$doi,$autores,$abstractYmas,$archivo,$tipoArchivo,$valid);
function validarForm_publicaciones ($ID,$tpub,$ani,$tit,$doix,$aut,$abs,$arc,$tarc,&$ok){
}

// scan3d
// validarForm_scan3d ($claveU,$fecha,$hora,$clima,$tipoPlaya,$escaneo3D,$IDarchivo,$video,$distancia,
//		$modelo,$modeloVolumen,$modeloPeso,$modeloCategoria,$modeloNota,$comentario,$valid);
function validarForm_scan3d($cU,$fec,$ho,$cli,$tPlaya,$es3D,$IDar,$vid,$dist,$modVol,$modPeso,$modCate,$modNota,$com,&$ok) {
}



// *****************************************************
// campos individuales 
// $ok viene con true desde el principal y solo se cambia a false
//
//tablas: anestesia, copula, criadestetado, hembra, idpapers, individuo, macho, marca, medidas, muda, muestras, observado, tag, temporada, viaje, viaje_config,madrehijo
function validar_claveU($cu,&$ok,$DebeEstar) {
    if (vacio($cu) and $DebeEstar) {
        $ok=false;
        return 'Clave &uacute;nica no puede faltar';
        }   
    if (!vacio($cu)) {
        if (strlen($cu)<>4) {
            $ok=false;
            return PATRON_claveU_men;
        }
        if (preg_match("/".PATRON_claveU."/", $cu, $a) <> 1) {
            $ok=false;
            return PATRON_claveU_men;
        }   
    }
    
}


//tablas: anestesia, censista, censo, copula, criadestetado, equipotrabajo, grupo, hembra, macho, marca, medidas, muda, muestras, observado, recuento, sector, sector_copiado, tag, viaje
function validar_fecha ($fec,&$ok,$DebeEstar) {
    $m = "";
    if (vacio($fec) and $DebeEstar ) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!vacio($fec)) {
        if (strlen($fec)<>10) {
            $ok=false;
            return 'Fecha inv&aacute;lida (1); el formato debe ser AAAA-MM-DD';
        }
        if (preg_match("/".PATRON_fecha."/", $fec, $a) <> 1) {
            $ok=false;
            return PATRON_fecha_men;
        }
        if (substr($fec,8,2)=="00"){
            /* fecha con dia 00 = dia desconocido */
            $fec=rtrim($fec,"00");
            $fec.="01";
        }
        
        $lafec = date_create_from_format('Y-m-d', $fec);
        if (!($lafec and date_format($lafec,'Y-m-d') === $fec)) {
            $ok=false;
            return PATRON_fecha_men;     
        }
        if ($fec > date("Y-m-d")) {
            $ok=false;
            return 'La fecha no puede ser mayor a la fecha de hoy';         
        }
        $fec_r0=date_format($lafec,'Y')."-08-01";
        $fec_r1=date_format($lafec,'Y')."-11-30";
        
        if (date_format($lafec,'m')>4) {
            $fec_m0=date_format($lafec,'Y')."-11-01";
            $fec_m1=(intval(date_format($lafec,'Y'))+1)."-04-30";
        }
        else{
            $fec_m0=(intval(date_format($lafec,'Y'))-1)."-11-01";
            $fec_m1=intval(date_format($lafec,'Y'))."-04-30";           
        }
        
        if (($fec_r0 <= $fec) and ($fec <= $fec_r1)) {
            $m .= "En REPRO. ";
        }
        if (($fec_m0 <= $fec) and ($fec <= $fec_m1)) {
            $m .= "En MUDA. ";
        }
        if (empty($m)) {
            $m="En FUERA. ";
        }
    }
    
    return $m;
}

//tablas: anestesia, copula, scan3d
//
function validar_hora ($hor,&$ok,$DebeEstar) {
    if (vacio($hor) and $DebeEstar) {
        $ok=false;
        return 'La hora no puede faltar';
        }
    if (!empty($hor)) {
        if (strlen($hor)<>8) {
            $ok=false;
            return 'Hora inv&aacute;lida (1); el formato debe ser HH:MM:SS';
        }
        if (preg_match("/".PATRON_hora."/", $hor, $a) <> 1) {
            $ok=false;
            return PATRON_hora_men;
        }
    }   
}


//
//tablas: censista, colaborador, equipotrabajo
//^[A-Z]{2,3}$
function validar_IDcolaborador ($IDcol,&$ok,$DebeEstar) {
    if (vacio($IDcol) and $DebeEstar ) {
        $ok=false;
        return 'Colaborador no puede faltar';
        }
	if (preg_match("/".PATRON_IDcolaborador."/", $IDcol, $a) <> 1) {
            $ok=false;
            return PATRON_IDcolaborador_men;
        }
            
}


//
//tablas: recuento, temporada
//
function validar_categoria ($cate,&$ok,$DebeEstar) {
    /*en validarForm_recuento*/
	$r=validar_IDcategoria ($cate,$ok,$DebeEstar);
	return $r;
}


//
//tablas: hembra, muda
//
function validar_estado ($esta,&$ok,$DebeEstar) {
    switch ($GLOBALS['queTabla']) {
        case 'muda':
             $a=array("","EN GRUPO","SOLO");
             if (!in_array($esta,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        case 'hembra':
             $a=array("VISTA","NO PRENADA","PRENADA","EN PARTO","CON PUP");
             if (!in_array($esta,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        default: 
          return ;      
    }	
}


//
//tablas: criadestetado,hembra
//
function validar_estimada ($esti,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($esti,$ok,$DebeEstar);
	return $r;
}


//
//tablas: playa, sector
//
function validar_geomTex ($geo,&$ok,$DebeEstar) {
    if (vacio($geo) and $DebeEstar) {
        $ok=false;
        return 'Geometr&iacute;a WKT no puede faltar';
        }
    if (!empty($geo)) {
        switch ($GLOBALS['queTabla']) {
            case 'sector_copiado':
                if ( (strlen($geo)>40) or (preg_match("/".PATRON_geoPOINT."/", $geo, $a) <> 1) ) {
                   $ok=false;
                   return PATRON_geoPOINT_men;
                }                           
             break;
            case 'playa':   
                if ( (strlen($geo)>3000) or (preg_match("/".PATRON_geoPOLYGON_POINT."/", $geo, $a) <> 1) ){
                   $ok=false;
                   return PATRON_geoPOLYGON_POINT_men;
                }               
             break;
            default: 
              return ;      
        }
    }
    
}


//
//tablas: instrumentos, instrumentos_colocados
//
function validar_instrumentoNRO ($instNRO,&$ok,$DebeEstar) {
	$r=validar_entero ($instNRO,CONST_instrumentoNRO_min,CONST_instrumentoNRO_max,$ok,$DebeEstar);
	return $r;
	
	
}


//
//tablas: censista, grupo, recuento, sector, sector_copiado
//
function validar_libreta ($lib,&$ok,$DebeEstar) {
	if ($_SESSION['tipocen']<>'MENSUAL') {
		if (preg_match("/".PATRON_libreta."/", $lib, $a) <> 1) {
		   $ok=false;
		   return PATRON_libreta_men;
		}
	}
	else{
		if (preg_match("/".PATRON_libretaViejoMensual."/", $lib, $a) <> 1) {
		   $ok=false;
		   return PATRON_libretaViejoMensual_men;
		}
	}
		
        
}



//
//tablas: colaborador, playa
//
function validar_nombre ($nom,&$ok,$DebeEstar) {
    if (vacio($nom) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!empty($nom)) {
        switch ($GLOBALS['queTabla']) {
            case 'playa':
                if ( (preg_match("/".PATRON_nombrePlaya."/", $nom, $a) <> 1) ){
                   $ok=false;
                   return PATRON_nombrePlaya_men;
                }               
             break;
            case 'colaborador':
                if ( (preg_match("/".PATRON_apeYnom."/", $nom, $a) <> 1) ){
                   $ok=false;
                   return PATRON_apeYnom_men;
                }          
             break;
        }
    
    }
}


//
//tablas: individuo, instrumentos
//
function validar_nuestro ($nues,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($nues,$ok,$DebeEstar);
	return $r;
	
}

// tabla: hembra
// verifica si tiene fecha de parto en otro registro para la temporada
function tieneFechaParto($cU,$fec,&$sParto,&$sPup) {
	$t='';
	$temp = substr($fec,0,4);
	$pdo = Database::connect();
	$sql = "SELECT sum(if(estado='EN PARTO',1,0)) AS sParto, sum(if(estado='CON PUP',1,0)) AS sPup,COUNT(DISTINCT fechaParto) as cFecha FROM hembra WHERE claveU=? and YEAR(fecha)=? and estado in ('EN PARTO','CON PUP')";
	$q = $pdo->prepare($sql);
	$q->execute(array($cU,$temp));
	$a = $q->fetch(PDO::FETCH_ASSOC);
	$sParto =$a['sParto'];
	if (is_null($sParto)) {
		$sParto=0;
	}
	$sPup =$a['sPup'];
	if (is_null($sPup)) {
		$sPup=0;
	}
	$cFecha =$a['cFecha'];
	Database::disconnect();
	if ($sParto>=1) {
		$t="Tiene registrado estado EN PARTO.<BR>";
	}
	if ($cFecha>=1) {
		$t.="Tiene registrada fecha de parto. ";
	}
	return $t;
}


// verifica si tiene vinculo con cria en la temporada
function tieneVinculoPup($cU,$fec) {
	$t=false;
	$temp = substr($fec,0,4);
	$pdo = Database::connect();
	$sql = "SELECT * FROM madrehijo WHERE claveMam=? and temporada=?";
	$q = $pdo->prepare($sql);
	$q->execute(array($cU,$temp));
	$r=$q->rowCount();
	Database::disconnect();	
	if ($r>=1) {
		$t=true; /* tiene vinculo */
	}
	return $t;
}




//tablas: hembra,criadestetado
function validar_edadPupDeste ($di,&$ok,$DebeEstar) {
	$r=validar_entero ($di,CONST_edadPupDeste_min,CONST_edadPupDeste_max,$ok,$DebeEstar);
	return $r;
}

function validar_fechaPartoDeste ($fec,$fecParDes,&$ok,$DebeEstar) {
    $bien=true;
    $xm = validar_fecha ($fecParDes,$bien,$DebeEstar);
	if(!$bien) {
		$ok=false;
		return $xm;
	}
	if(vacio($fecParDes)) {
		return;
	}
	if ($fecParDes>$fec) {
		$ok=false;
		return "No puede ser posterior a Fecha";
	}
	$ff = date_create_from_format('Y-m-d', $fec);
	$fp = date_create_from_format('Y-m-d', $fecParDes);
	$mdias=35;
	if (intval(date_interval_format(date_diff($ff,$fp),"%a")) >$mdias) {
		$ok=false;
		return "No puede ser anterior a Fecha-$mdias d&iacute;as";
	}	
}
	


//
//tablas: grupo, recuento
//
function validar_orden ($ord,&$ok,$DebeEstar) {
	$r=validar_entero ($ord,CONST_ordenRec_min,CONST_ordenRec_max,$ok,$DebeEstar);
	return $r;
}


//
//tablas: grupo, observado, tag
//
function validar_playa ($pla,&$ok,$DebeEstar) {
    if (vacio($pla) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!vacio($pla) ) {
		$pdo = Database::connect();
		$sql = "SELECT IDplaya FROM playa WHERE IDplaya=? ";
		$q = $pdo->prepare($sql);
		$q->execute(array($pla));
		if ($q->rowCount()<>1) {
				Database::disconnect();
				$ok=false;
				return "No existe esa playa.";
				}
		$aCat = $q->fetch(PDO::FETCH_ASSOC);
		Database::disconnect();
	}
}


//
//tablas: individuo, recuento
//
function validar_sexo ($sex,&$ok,$DebeEstar) {
    if (vacio($sex) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }       

    $v=vacio($sex);
    if ($v and !$DebeEstar) {
        return;
    }	
	
    if (($sex<>"HEMBRA") AND ($sex<>"MACHO") AND ($sex<>"NODET")) {
        $ok=false;
        return 'Debe ser HEMBRA, MACHO o NODET ('.$sex.')';       
    }
    
}



//
//tablas: macho, recuento
//
function validar_status ($stat,&$ok,$DebeEstar) {
    switch ($GLOBALS['queTabla']) {
        case 'recuento':
             $a=array("sd","ALFA","PERIFERICO","CERCANO","LEJANO","SOLO","no corresponde");
             if (!in_array($stat,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        case 'macho':
             $a=array("sd","ALFA","PERIFERICO","CERCANO","LEJANO","SOLO","no corresponde");
             if (!in_array($stat,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        default: 
          return ;      
    }
    
    
}


//
//tablas: madrehijo, observado, temporada
//
function validar_temporada ($tempo,$cu,&$ok,$DebeEstar) {
    if (vacio($tempo) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }       

    $v=vacio($tempo);
    if ($v and !$DebeEstar) {
        return;
    }
	$tipoT="";
	$tActual = temporadaAnio_actual($tipoT);
    $cant=intval($tempo);
    if ($v or $cant<CONST_temporadaMin_min or $cant>$tActual) {
        $ok=false;
        return CONST_temporadaMin_men." y &lt;= $tActual ";
    }   
  
    $pdo = Database::connect();
    $sql = "SELECT MIN(temporada) as mini FROM temporada WHERE claveU=? ";
    $q = $pdo->prepare($sql);
    $q->execute(array($cu));
    if ($q->rowCount()<>1) {
        /* error!!! */
            Database::disconnect();
            $ok=false;
            return 'ERROR validar_temporada: sin repuesta!!!';
            }
    $tmin = $q->fetchColumn(0);
    Database::disconnect();
    if ($tempo < $tmin ){
        $ok=false;
        return "Debe ser &gt;= a la primer temporada del individuo ($tmin) y &lt;= $tActual "; 
    }
}   



//
//tablas: anestesia, censo, instrumentos, playa, vecindario
//el campo de tabla es tipo enum, que se traduce en un select en el formulario
//$GLOBALS['queTabla']
function validar_tipo ($tip,&$ok,$DebeEstar) {
    if (vacio($tip) and $DebeEstar) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
        }
    switch ($GLOBALS['queTabla']) {
        case 'anestesia':
             $a=array("INICIAL","SIGUIENTE","COMENTA","INDUCCION","FIN","FIN1","FIN2");
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        case 'censo':
             $a=array("PARCIAL","PVALDES","MENSUAL","MUDA"); 
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        case 'instrumentos':
             $a=array("SATELITAL","TDR","TDR-ANG","LTL","CAMARA","RADIO");
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }      
             break;
        case 'playa':
             $a=array("MENSUAL","PUNTO","TRAMO","VIEJOTRAMO","VIEJOPUNTO");
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }      
             break;
        case 'totalCenso':           /*sin break!!*/
        case 'vecindario':
             $a=array("PARCIAL","PVALDES","MUDA");
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
             break;
        case 'preguntas':
        case 'buceos':			/* buceos numeros */
             $a=array("REPRO","MUDA","FUERA");
             if (!in_array($tip,$a)) {
                $ok=false;
                return 'Seleccionar un elemento de la lista';
             }
        default: 
          return ;      
    }
}


//
//tablas: observado, temporada
//
function validar_tipoTempo ($tip,&$ok,$DebeEstar) {
    if (vacio($tip) and $DebeEstar) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
        }
    $v=vacio($tip);
    if ($v and !$DebeEstar) {
        return;
    }
    $a=array("MUDA","FUERA","REPRO","mudaP","fueraP","reproP");
    if (!in_array($tip,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
    }   
}


//
//tablas: instrumentos_colocados, viaje_config
//
function validar_viajeID ($viajeID,&$ok,$DebeEstar) {
}



/* anestesia */
function validar_aplicacion ($apli,&$ok,$DebeEstar) {
    $a=array("no corresponde","IM","IV");
    if (in_array($apli,$a)==false) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
    }
}


function validar_droga ($dro,&$ok,$DebeEstar) {
    $a=array("no corresponde","TELAZOL","VIVIRANT","DOPRAM","KETAMINA");
    if (!in_array($dro,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
    }
}

function validar_droga_ml ($dro_ml,&$ok,$DebeEstar) {
	$r= validar_decimal($dro_ml,CONST_droga_ml_min,CONST_droga_ml_max,$ok,$DebeEstar);
	return $r;
}

function validar_partida ($par,&$ok,$DebeEstar) {
    if ((!vacio($par)) and (preg_match("/".PATRON_partida."/", $par, $a) <> 1)) {
        $ok=false;
        return PATRON_partida_men;
    }       
}


//
//tabla: categoria
//
function validar_IDcategoria ($IDcat,&$ok,$DebeEstar) {
    if (vacio($IDcat) and $DebeEstar ) {
        $ok=false;
        return 'No puede faltar';
        }
    if ((!empty($IDcat)) and (preg_match("/".PATRON_IDcategoria."/", $IDcat, $a) <> 1)) {
        $ok=false;
        return PATRON_IDcategoria_men;
    }           
}


function validar_cateDesc ($cateD,&$ok,$DebeEstar) {
    if (vacio($cateD) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if ((!empty($cateD)) and (preg_match("/".PATRON_cateDesc."/", $cateD, $a) <> 1) ) {
        $ok=false;
        return PATRON_cateDesc_men;
    }       
}

function validar_grupoEdad ($grupoE,&$ok,$DebeEstar) {
    /*$a=array("PUP","YEARLING-JUVENIL","SUBADULTO","SUBADULTO-ADULTO","ADULTO","HAREN","GRUPO DE HARENES","HAREN SIN ALFA","PAREJA SOLITARIA","SOLOS","SD");*/
    $a=array("PUP","YEARLING-JUVENIL","SUBADULTO","SUBADULTO-ADULTO","ADULTO","SD","sistema");
    if (!in_array($grupoE,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }  
}


function validar_edadRelativa ($edadR,&$ok,$DebeEstar) {
   $r = validar_decimal($edadR,CONST_edadRelativa_min,CONST_edadRelativa_max,$ok,$DebeEstar);
   return $r;
}


function validar_ordenCate ($ordenC,&$ok,$DebeEstar) {
   $r = validar_decimal($ordenC,CONST_ordenCate_min,CONST_ordenCate_max,$ok,$DebeEstar);
   return $r;    
}


function validar_reproductiva ($repro,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($repro,$ok,$DebeEstar);
	return $r;
    
}


function validar_sexoc ($sec,&$ok,$DebeEstar) {
    if (($sec<>"HEMBRA") AND ($sec<>"MACHO") AND ($sec<>"CUALQUIERA")) {
        $ok=false;
        return 'Debe ser HEMBRA, MACHO o CUALQUIERA';       
    }
}

function validar_detalleOjo ($o,&$ok,$DebeEstar) {
    if (($o<>"precisa") AND ($o<>"grupo")) {
        $ok=false;
        return 'Seleccionar de la lista';       
    }
}


//
//tabla: censo
//
function validar_fechaTotal ($fec,&$ok,$DebeEstar) {
    $xm = validar_fecha ($fec,$ok,$DebeEstar);
    return $xm;
    
}


function validar_fechaCenso ($fec,&$ok,$DebeEstar){
	$vok = true;
	$xm = validar_fecha ($fec,$vok,$DebeEstar);
	if($vok) {
		if (!editaCenso($fec)) {
			$vok=false;
			$xm="El año tiene que ser a lo sumo 2 años antes que el actual.";
		}
	}
	$ok = ($ok and $vok);
	return $xm;
}

//
//tabla: colaborador, usuarios (members)
//
function validar_apellido ($ape,&$ok,$DebeEstar) {
    $r = validar_nombre ($ape,$ok,$DebeEstar);
    return $r;
}


function validar_email ($ema,&$ok,$DebeEstar) {
   if (vacio($ema) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if ((!empty($ema)) and (preg_match("/".PATRON_email."/", $ema, $a) <> 1) ) {
        $ok=false;
        return PATRON_email_men;
    }   
    
}

function validar_token($tok,&$ok,$DebeEstar){
    if (vacio($tok) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
    }
    if (!ctype_xdigit($tok)) {
        $ok=false;
        return 'Inv&aacute;lido';       
    }
        
}

//
//tabla: copula
//
function validar_conCualyCu ($cCual,$cCualCu,$fec,$sex,&$ok) {

    if ((!empty($cCual)) and (preg_match("/".PATRON_marcaOtag."/", $cCual, $a) <> 1) ) {
        $ok=false;
        return PATRON_marcaOtag_men;
    }
	if(vacio($cCual) and vacio($cCualCu)) {
		return;
	}
	
	if($sex=='MACHO') {
		$sexOp='HEMBRA';
	}
	else{
		$sexOp='MACHO';
	}
	
	$anio=substr($fec,0,4);
    $pdo = Database::connect();
	if( vacio($cCual) and !vacio($cCualCu) ) {
		$conError=true;
		$t = "No hay $sexOp con la siguiente claveU en la temporada";		
		$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo=? and claveU=? and temporada=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($sexOp,$cCualCu,$anio));
		//$t.=$sql.$cCualCu.$anio;
	}
	else {
		if( !vacio($cCual) and !vacio($cCualCu) ) {
			$conError=true;
			$t = "Marca o tag no se corresponde con la siguiente claveU";		
			$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo=? and claveU=? and temporada=? and 
								  (LOCATE('$cCual',tags)>0 or LOCATE('$cCual',marcas)>0)";
			$q = $pdo->prepare($sql);
			$q->execute(array($sexOp,$cCualCu,$anio));
		}
		else {
			$conError=false;
			$t = "<span class='glyphicon glyphicon-exclamation-sign'></span> No hay $sexOp con estas marca o tag en la temporada";	
			$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo=? and temporada=? and 
								  (LOCATE('$cCual',tags)>0 or LOCATE('$cCual',marcas)>0)";
			$q = $pdo->prepare($sql);
			$q->execute(array($sexOp,$anio));
		}
	}
    if ($q->rowCount()<>1) {
            Database::disconnect();
            $ok=false;
            return " Oh Oh algo anda mal...".$sql;
            }
    $a = $q->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();
    if ($a['cant'] == 0 and $conError) {
		$ok=false;
		return $t;
	}
	if ($a['cant'] == 0 and !$conError) {
		return $t;
	}

}


function validar_duracion ($dur,&$ok,$DebeEstar) {
   $r=validar_entero($dur,CONST_duracion_min,CONST_duracion_max,$ok,$DebeEstar);
   return $r;
}


//
//tabla: criadestetado
//

// verifica si tiene registro de destete e informa
function tieneDestete($cU) {
	$pdo = Database::connect();
	$sql = "SELECT fecha FROM criadestetado WHERE claveU=?";
	$q = $pdo->prepare($sql);
	$q->execute(array($cU));
	if ($q->rowCount()==0) {
		Database::disconnect();
		return;
	}
	$a = $q->fetch(PDO::FETCH_ASSOC);
	$sfec =$a['fecha'];
	Database::disconnect();
	$t="Tiene registrado destete el $sfec";
	return $t;
}




//
//tabla: grupo
//
function validar_desde ($des,&$ok,$DebeEstar) {
    $des=trim($des);
    if (!empty($des) and $des<> 'BAJO' and $des<> 'ALTO' ) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
    }
    
}


function validar_referencia ($refer,&$ok,$DebeEstar) {
    $a=array("TOTAL","TOTAL HARENES","SOLOS","PAREJA SOLITARIA","HAREN","GRUPO DE HARENES","HAREN SIN ALFA","ASOCIADOS");
    if (!in_array($refer,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }
    
}


function validar_tipoPlaya ($tPlaya,&$ok,$DebeEstar) {
    $a=array("","AGUA","ARENA","CANTO RODADO","MEZCLA","RESTINGA");
    if (!in_array($tPlaya,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }

}

function validar_distancia ($dis,&$ok,$DebeEstar) {
	$r=validar_entero($dis,CONST_distancia_min,CONST_distancia_max,$ok,$DebeEstar);
	return $r;
}


//
//tabla: idpapers
//
function validar_identificaciones ($idens,&$ok,$DebeEstar) {
	$q = $idens;
	$v = vacio($q);
	if ($v and $DebeEstar ) {
		$ok=false;
		return 'No puede faltar';
	}
	if (!$v and preg_match("/".PATRON_identiPub."/", $q, $a) <> 1) {
		$ok=false;
		return PATRON_identiPub_men;
	}		

}


//
//tabla: individuo
//
function validar_muerto ($muer,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($muer,$ok,$DebeEstar);
	return $r;
		
}


function validar_muertoTempo ($muertoTem,$cu,&$ok,$DebeEstar) {
    if (vacio($muertoTem) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }       

    $v=vacio($muertoTem);
    if ($v and !$DebeEstar) {
        return;
    }
	$tipoT="";
	$tActual = temporadaAnio_actual($tipoT);
    $cant=intval($muertoTem);
    if ($v or $cant<CONST_temporadaMin_min or $cant>$tActual) {
        $ok=false;
        return CONST_temporadaMin_men." y &lt;= $tActual ";
    }   


    $pdo = Database::connect();
    $sql = "SELECT MAX(temporada) as maxi FROM temporada WHERE claveU=? ";
    $q = $pdo->prepare($sql);
    $q->execute(array($cu));
    if ($q->rowCount()<>1) {
        /* error!!! */
            Database::disconnect();
            $ok=false;
            return 'ERROR validar_temporada: sin repuesta!!!';
            }
    $tmax = $q->fetchColumn(0);
    Database::disconnect();
    if ($muertoTem < $tmax or $muertoTem > $tActual ){
        $ok=false;
        return "Debe ser &gt;= a la &uacute;ltima temporada del individuo ($tmax) y &lt;= $tActual"; 
    }
	
	
	
	
	
}


//
//tabla: instrumentos
//
function validar_disponible ($dispo,&$ok,$DebeEstar) {
	$a=array("SI","NO","VIAJANDO","EN REPARACION","PRESTADO","PERDIDO");
	if (!in_array($dispo,$a)) {
		$ok=false;
		return 'Seleccionar un elemento de la lista';
	}
}

function validar_identificacion ($ide,&$ok,$DebeEstar)  {	
	$q = $ide;
	$v = vacio($q);
	if ($v and $DebeEstar ) {
		$ok=false;
		return 'No puede faltar';
	}
	if (!$v and preg_match("/".PATRON_variosInstru."/", $q, $a) <> 1) {
		$ok=false;
		return PATRON_variosInstru_men;
	}
}

function validar_serial_num ($serial,&$ok,$DebeEstar)  {
	$q = $serial;
	$v = vacio($q);
	if ($v and $DebeEstar ) {
		$ok=false;
		return 'No puede faltar';
	}
	if (!$v and preg_match("/".PATRON_variosInstru."/", $q, $a) <> 1) {
		$ok=false;
		return PATRON_variosInstru_men;
	}
}
function validar_modelo ($mode,&$ok,$DebeEstar) {
	$q = $mode;
	$v = vacio($q);
	if ($v and $DebeEstar ) {
		$ok=false;
		return 'No puede faltar';
	}
	if (!$v and preg_match("/".PATRON_variosInstru."/", $q, $a) <> 1) {
		$ok=false;
		return PATRON_variosInstru_men;
	}
}
function validar_fabricante ($fabri,&$ok,$DebeEstar) {
	$q = $fabri;
	$v = vacio($q);
	if ($v and $DebeEstar ) {
		$ok=false;
		return 'No puede faltar';
	}
	if (!$v and preg_match("/".PATRON_variosInstru."/", $q, $a) <> 1) {
		$ok=false;
		return PATRON_variosInstru_men;
	}
	
}



//
//tabla: instrumentos_colocados
//
function validar_fecha_recuperacion ($fecharec,&$ok,$DebeEstar) {
	$Ook=true;
	$m = validar_fecha ($fecharec,$Ook,$DebeEstar);
	if(!$Ook) {
		$ok=false;
		return $m;  /* solo si hay error en la fecha!! */
	}
}

function validar_actualizaDispo ($ri,&$ok,$DebeEstar) {
/* no está en la tabla sino que se genera en el script*/
	$r=validar_SIoNO ($ri,$ok,$DebeEstar);
	return $r;
    
}


//
//tabla: macho
//
function validar_entornoAlAlfaCu ($idenAlfa,$AlfaCu,$fec,&$ok) {
    if ((!empty($idenAlfa)) and (preg_match("/".PATRON_marcaOtag."/", $idenAlfa, $a) <> 1) ) {
        $ok=false;
        return PATRON_marcaOtag_men;
    }

	if(vacio($idenAlfa) and vacio($AlfaCu)) {
		return;
	}
	$anio=substr($fec,0,4);
    $pdo = Database::connect();
	if( vacio($idenAlfa) and !vacio($AlfaCu) ) {
		$conError=true;
		$t = "No hay un macho con la siguiente claveU en la temporada";
		$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo='MACHO' and claveU=? and temporada=?";
		$q = $pdo->prepare($sql);
		$q->execute(array($AlfaCu,$anio));
		//$t.=$sql.$AlfaCu.$anio;
	}
	else {
		if( !vacio($idenAlfa) and !vacio($AlfaCu) ) {
			$conError=true;
			$t = "Marca o tag no se corresponde con la siguiente claveU";		
			$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo='MACHO' and claveU=? and temporada=? and 
								  (LOCATE('$idenAlfa',tags)>0 or LOCATE('$idenAlfa',marcas)>0)";
			$q = $pdo->prepare($sql);
			$q->execute(array($AlfaCu,$anio));
		}
		else {
			$conError=false;
			$t = "<span class='glyphicon glyphicon-exclamation-sign'></span> Marca o tag no est&aacute; en la temporada";	
			$sql = "SELECT count(*) as cant FROM vw_pareja_potencial WHERE tipoTempo in('REPRO','reproP') and sexo='MACHO' and temporada=? and 
								  (LOCATE('$idenAlfa',tags)>0 or LOCATE('$idenAlfa',marcas)>0)";
			$q = $pdo->prepare($sql);
			$q->execute(array($anio));
		}
	}
    if ($q->rowCount()<>1) {
            Database::disconnect();
            $ok=false;
            return " Oh Oh algo anda mal...".$sql;
            }
    $a = $q->fetch(PDO::FETCH_ASSOC);
    Database::disconnect();
    if ($a['cant'] == 0 and $conError) {
		$ok=false;
		return $t;
	}
	if ($a['cant'] == 0 and !$conError) {
		return $t;
	}
	
	
}


function validar_estadoFisico ($estFis,&$ok,$DebeEstar) {
	 $a=array("","BUENO","LASTIMADO","LASTIMADO+","FLACO","FLACO+");
	 if (!in_array($estFis,$a)) {
		$ok=false;
		return 'Seleccionar un elemento de la lista';
	 }	
}


function validar_haremHembras ($harHem,&$ok,$DebeEstar) {
	$r=validar_entero($harHem,CONST_haremHembras_min,CONST_haremHembras_max,$ok,$DebeEstar);
	return $r;
}


function validar_haremPups ($harPups,&$ok,$DebeEstar) {
	$r=validar_entero($harPups,CONST_haremPups_min,CONST_haremPups_max,$ok,$DebeEstar);
	return $r;
}


//
//tabla: madrehijo
//


//
//tabla: marca
//
function validar_marca ($mar,&$ok,$DebeEstar) {
    if (vacio($mar) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
		
    if (!empty($mar)) {
        if (strlen($mar)>30) {
            $ok=false;
            return PATRON_marca_men;
        }
        if (preg_match("/".PATRON_marca."/", $mar, $a) <> 1) {
            $ok=false;
            return PATRON_marca_men;
        }
    }   	
	
}


//
//tabla: medidas  mmm_max mmm_min son constantes declaradas en tb_validar_ConstantesDeAlgunosCampos.php
//
function validar_circunferencia ($circun,&$ok,$DebeEstar) {
	$r = validar_decimal ($circun,CONST_circunferencia_min,CONST_circunferencia_max,$ok,$DebeEstar);
	return $r;
}


function validar_largoCurva ($larCur,&$ok,$DebeEstar) {
	$r = validar_decimal ($larCur,CONST_largoCurva_min,CONST_largoCurva_max,$ok,$DebeEstar);
	return $r;
}


function validar_largoStd ($larStd,&$ok,$DebeEstar) {
	$r = validar_decimal ($larStd,CONST_largoStd_min,CONST_largoStd_max,$ok,$DebeEstar);
	return $r;
}


function validar_largo_aleta_ext ($lar_alext,&$ok,$DebeEstar) {
	$r = validar_decimal ($lar_alext,CONST_largo_aleta_ext_min,CONST_largo_aleta_ext_max,$ok,$DebeEstar);
	return $r;
}


function validar_largo_aleta_int ($lar_alint,&$ok,$DebeEstar) {
	$r = validar_decimal ($lar_alint,CONST_largo_aleta_int_min,CONST_largo_aleta_int_max,$ok,$DebeEstar);
	return $r;
}


function validar_nostril ($nost,&$ok,$DebeEstar) {
	$r = validar_decimal ($nost,CONST_nostril_min,CONST_nostril_max,$ok,$DebeEstar);
	return $r;
}


function validar_peso ($pes,&$ok,$DebeEstar) {
	$r = validar_decimal ($pes,CONST_peso_min,CONST_peso_max,$ok,$DebeEstar);
	return $r;
}

function validar_edadMedida($di,&$ok,$DebeEstar) {
	$r = validar_entero ($di,CONST_edadMedida_min,CONST_edadMedida_max,$ok,$DebeEstar);
	return $r;
}



//
//tabla: muda
//
function validar_porcentaje ($por,&$ok,$DebeEstar) {
	$r=validar_entero($por,CONST_porcentaje_min,CONST_porcentaje_max,$ok,$DebeEstar);
	return $r;
}


//
//tabla: muestras
//
function validar_codigoADN ($ADN,&$ok,$DebeEstar) {
    if (vacio($ADN) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }

    if (!empty($ADN)) {
        if (strlen($ADN)>30) {
            $ok=false;
            return PATRON_codADN;
        }
        if (preg_match("/".PATRON_codADN."/", $ADN, $a) <> 1) {
            $ok=false;
            return PATRON_codADN_men;
        }
    }	
	
	
}
function validar_sangre ($sangre,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($sangre,$ok,$DebeEstar);
	return $r;
}

function validar_bigotes ($bigos,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($bigos,$ok,$DebeEstar);
	return $r;
}

function validar_pelos ($pel,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($pel,$ok,$DebeEstar);
	return $r;
		
}

function validar_fotogrametria ($fotog,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($fotog,$ok,$DebeEstar);
	return $r;
	
	
}





//
//tabla: playa
//
function validar_IDplaya ($IDpla,&$ok,$DebeEstar) {
    if (vacio($IDpla) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (preg_match("/".PATRON_IDplaya."/", $IDpla, $a) <> 1) {
            $ok=false;
            return PATRON_IDplaya_men;
        }
    
    
}


function validar_norteSur ($nS,&$ok,$DebeEstar) {
    $r = validar_decimal($nS,CONST_norteSur_min,CONST_norteSur_max,$ok,$DebeEstar);
	return $r;
}


//
//tabla: recuento
//
function validar_cantidad ($cant,&$ok,$DebeEstar) {
    $r = validar_entero($cant,CONST_cantidad_min,CONST_cantidad_max,$ok,$DebeEstar);
	return $r;
}


//
//tabla: sector
//
function validar_horaInicio ($hor,&$ok,$DebeEstar) {
    $xm=validar_hora($hor,$ok,$DebeEstar);
    return $xm;
}

function validar_horaFin ($hor,&$ok,$DebeEstar) {
    $xm=validar_hora($hor,$ok,$DebeEstar);
    return $xm;
}

function validar_direccionRecorrida ($dir,&$ok,$DebeEstar) {
     $a=array("","N-S","S-N","E-O","O-E");
     $dir=trim($dir);
     if (!in_array($dir,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      
}

function validar_marea ($mar,&$ok,$DebeEstar) {
     $a=array("","MUY BAJA","BAJA","BAJA BAJANDO","BAJA SUBIENDO","MEDIA BAJANDO","MEDIA","MEDIA SUBIENDO","ALTA BAJANDO","ALTA SUBIENDO","ALTA","MUY ALTA");
     $mar=trim($mar);
     if (!in_array($mar,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      
}


//
//tabla: sector_copiado
//
function validar_fecha_copia ($fcopia,&$ok,$DebeEstar) {
}


function validar_libreta_virtual ($lib,&$ok,$DebeEstar) {
    if (preg_match("/".PATRON_libretaVirtual."/", $lib, $a) <> 1) {       
       $ok=false;
       return PATRON_libretaVirtual_men;
    }                       
}


//
//tabla: tag
//
function validar_borradoTempo ($borrTempo,$fec,&$ok,$DebeEstar) {
	$v=vacio($borrTempo);
	if ($v and $DebeEstar) {
		$ok=false;
		return 'No puede faltar';
    }
    $anioAc= date("Y");
	$anioMin= date_format(date_create_from_format("Y-m-d", $fec),"Y");
	$anioMen= "Debe estar entre $anioMin y $anioAc";	

    $cant=intval($borrTempo);
    if (!$v and ($cant<$anioMin or $cant>$anioAc)) {
        $ok=false;
        return $anioMen;
    }

}


function validar_encontradoTempo ($encoTempo,$fec,&$ok,$DebeEstar) {
	$v=vacio($encoTempo);
	if ($v and $DebeEstar) {
		$ok=false;
		return 'No puede faltar';
    }
    $anioAc= date("Y");
	$anioMin= date_format(date_create_from_format("Y-m-d", $fec),"Y");
	$anioMen= "Debe estar entre $anioMin y $anioAc";	

    $cant=intval($encoTempo);
    if (!$v and ($cant<$anioMin or $cant>$anioAc)) {
        $ok=false;
        return $anioMen;
    }
}



function validar_tag ($tag,&$ok,$DebeEstar) {
    if (vacio($tag) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
	$col=substr($tag,0,1);
	$ult=substr($tag,5,1);
	/* A azul,N naranja,Y amarillo,P rosa,B blanco,V verde,R rojo,K */
	$colores = array('A','N','Y','P','B','V','R','K','X');
    if (!in_array($col,$colores)) {
		$ok=false;
		return "Color puede ser: A azul, N naranja, Y amarillo, P rosa, B blanco, V verde, R rojo, K";
    };
	
	if ($ult<>'D' and $ult <>'I') {
		$ok=false;
		return "Final debe ser D derecha o I izquierda";
	}
	
    if (!vacio($tag)) {
        if (preg_match("/".PATRON_tag."/", $tag, $a) <> 1) {
            $ok=false;
            return PATRON_tag_men;
        }   
    }  	
	
}


//
//tabla: vecindario
//
function validar_IDesquema ($IDesq,&$ok,$DebeEstar) {
	$r = validar_entero($IDesq,CONST_IDesquema_min,CONST_IDesquema_max,$ok,$DebeEstar);
    return $r;     
}


function validar_IDvecindario ($IDvecin,&$ok,$DebeEstar) {
    if (vacio($IDvecin) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!vacio($IDvecin)) {
        if (preg_match("/".PATRON_IDvecin."/", $IDvecin, $a) <> 1) {
            $ok=false;
            return PATRON_IDvecin_men;
        }   
    }       
        
}

function validar_NSdesdehasta ($NSdh,&$ok,$DebeEstar) {
    $r = validar_decimal($NSdh,CONST_norteSur_min,CONST_norteSur_max,$ok,$DebeEstar);
	return $r;
}

//
// para censo_totales.php: son campos de entrada para calcular totales
//
function validar_txtEsquema ($txtEsq,&$ok,$DebeEstar) {
    if (vacio($txtEsq) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
}

function validar_tZonaCopia ($tzc,&$ok,$DebeEstar) {
	$r=validar_SIoNO ($tzc,$ok,$DebeEstar);
	return $r;

}

function validar_tAnio ($tAn,&$ok,$DebeEstar) {
	$r=validar_entero($tAn,CONST_tAnio_min,CONST_tAnio_max,$ok,$DebeEstar);
	return $r;
}

function validar_tQparcial ($tqp,&$ok,$DebeEstar) {
	$a=array("ftotal","DMS","semana");
	if (!in_array($tqp,$a)) {
		$ok=false;
		return 'Seleccionar un elemento de la lista';
	}
}    
function validar_IDvecindarioFiltro ($IIDDvecin,&$ok,$DebeEstar) {
    if (vacio($IIDDvecin) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!vacio($IIDDvecin)) {
        if (preg_match("/".PATRON_IDvecinFiltro."/", $IIDDvecin, $a) <> 1) {
            $ok=false;
            return PATRON_IDvecinFiltro_men;
        }   
    }       
        
}





//
//tabla: viaje
//
function validar_etapa ($eta,&$ok,$DebeEstar) {
    if (vacio($eta) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar';
        }
    if (!vacio($eta)) {
		$a=array("COLOCACION","INICIA","COSTA ARG","COSTA","AJUSTE","REINICIA","SIN RECUPERACION","RECUPERADO");
		if (!in_array($eta,$a)) {
			$ok=false;
			return 'Seleccionar un elemento de la lista';
		}
	}
}


//
//tabla: viaje_config
//

function validar_profundidad ($prof,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($prof,$ok,$DebeEstar);
	return $r;
}


function validar_profundidad_intervalo ($profundidad_intervalo,&$ok,$DebeEstar) {
	$r=validar_entero($profundidad_intervalo,CONST_intervalo_min,CONST_intervalo_max,$ok,$DebeEstar);
	return $r;
}


function validar_temperatura ($tempe,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($tempe,$ok,$DebeEstar);
	return $r;
}


function validar_temperatura_intervalo ($temperatura_intervalo,&$ok,$DebeEstar) {
	$r=validar_entero($temperatura_intervalo,CONST_intervalo_min,CONST_intervalo_max,$ok,$DebeEstar);
	return $r;
}


function validar_luz ($lux,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($lux,$ok,$DebeEstar);
	return $r;
}


function validar_luz_intervalo ($luz_intervalo,&$ok,$DebeEstar) {
	$r=validar_entero($luz_intervalo,CONST_intervalo_min,CONST_intervalo_max,$ok,$DebeEstar);
	return $r;
}

function validar_angulo_pitch ($angulo_pitch,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($angulo_pitch,$ok,$DebeEstar);
	return $r;
}


function validar_angulo_roll ($angulo_roll,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($angulo_roll,$ok,$DebeEstar);
	return $r;
}


function validar_angulo_yaw ($angulo_yaw,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($angulo_yaw,$ok,$DebeEstar);
	return $r;	
}


function validar_camara ($camara,&$ok,$DebeEstar) {
    $r=validar_RespuestaParametros($camara,$ok,$DebeEstar);
	return $r;	
}


function validar_estrategia ($estrategia,&$ok,$DebeEstar) {
	$v=vacio($estrategia);
	if ($v and $DebeEstar) {
		$ok=false;
		return "No puede faltar";
    }
	if ($v and !$DebeEstar){
		return;
	}
	
     $a=array("NODET","PLATAFORMA","TALUD","CUENCA","VIAJERO");
     if (!in_array($estrategia,$a)) { 
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      	
	
}



function validar_fin_viaje ($fin_viaje,&$ok,$DebeEstar) {
	$v=vacio($fin_viaje);
	if ($v and $DebeEstar) {
		$ok=false;
		return "No puede faltar";
    }
	if ($v and !$DebeEstar){
		return;
	}
	
     $a=array("VIAJANDO","VIAJE COMPLETO","VIAJE INCOMPLETO");
     if (!in_array($fin_viaje,$a)) { 
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      	
	
}


function validar_posicionamiento ($posicionamiento,&$ok,$DebeEstar) {
	$v=vacio($posicionamiento);
	if ($v and $DebeEstar) {
		$ok=false;
		return "No puede faltar";
    }
	if ($v and !$DebeEstar){
		return;
	}
	
     $a=array("GEOLOCACION","SATELITAL","GPS");
     if (!in_array($posicionamiento,$a)) { 
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      	
}


// validar NO o SI o MAL (REGISTRO INCORRECTO) o OK (REGISTRO CORRECTO) respuesta en viaje_config y viaje_config_editar
function validar_RespuestaParametros ($sn,&$ok,$DebeEstar) {
	$v=vacio($sn);
	if ($v and $DebeEstar) {
		$ok=false;
		return "No puede faltar";
    }
	if ($v and !$DebeEstar){
		return;
	}
	
     $a=array("NO","SI","REGISTRO INCORRECTO","REGISTRO CORRECTO");
     if (!in_array($sn,$a)) { 
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      	
}



// 
// tabla usuarios (members)
//
function validar_memberID($cant,&$ok,$DebeEstar){
    $v=vacio($cant);
    $cant=intval($cant);
    if ($v or $cant<1) {
        $ok=false;
        return 'Debe ser un entero positivo';
    }   
}
function  validar_userpass ($ali,&$ok,$que) {
   if (vacio($ali)) {
        $ok=false;
        return 'No puede faltar';
        }
    if ( preg_match("/".PATRON_usuario."/", $ali, $a) <> 1) {
        $ok=false;
        if ($que=="login") {
            return 'Formato de usuario y/o password incorrecto';
        }
        else {
            return PATRON_usuario_men;   //$que.
        }
    }
}


function validar_permiso ($per,&$ok,$DebeEstar) {
    $a=array("noEditar","editar","administrar");
    if (!in_array($per,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }
    
}




/* para publicaciones */
function validar_ID($id,&$ok,$DebeEstar) {
    if (vacio($id) and $DebeEstar) {
        $ok=false;
        return 'ID no puede faltar';
        }
    if (!is_numeric($id)  or  is_float($id + 0)){
        $ok=false;
        return 'ID debe ser n&uacutemero entero';
        }       
}


function validar_anioPublicacion ($ani,&$ok,$DebeEstar) {
	$r=validar_entero($ani,CONST_anioPub_min,CONST_anioPub_max,$ok,$DebeEstar);
	return $r;
    
}


function validar_tipoPublicacion ($tpub,&$ok,$DebeEstar) {
     $a=array("PAPER","INFORME","LIBRO","CAPITULO DE LIBRO","ARTICULO","POSTER","PRESENTACION","NOTA","DATA PAPER","OTRO");
     $tpub=trim($tpub);
     if (!in_array($tpub,$a)) { 
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }      
}

function validar_titulo($tit,&$ok,$DebeEstar) {
    if (vacio($tit)) {
        $ok=false;
        return 'No puede faltar el t&iacute;tulo';
    }
        
    if  (preg_match("/".PATRON_tituPubli."/", $tit, $a) <> 1) {
           $ok=false;
           return PATRON_tituPubli_men;
        }                   
}       
        
        

function validar_autores($aut,&$ok,$DebeEstar) {
    if (vacio($aut) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar.';
        } 
        
    if  (preg_match("/".PATRON_autoresPubli."/", $aut, $a) <> 1) {
           $ok=false;
           return PATRON_autoresPubli_men;
    }
}

function validar_archivo($arc,&$ok,$DebeEstar) {
    if (vacio($arc) and $DebeEstar) {
        $ok=false;
        return 'No puede faltar el archivo';
        } 
}

function validar_tipoArchivo($tarc,&$ok,$DebeEstar) {
     $a=array("PDF","PowerPoint");
     $tarc=trim($tarc);
     if (!in_array($tarc,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista.';
     }
}

function validar_publiArchivo($arc,$tarc,&$ok,$DebeEstar) {
    $r="";
    if (vacio($arc) and $DebeEstar) {
        $ok=false;
        $r .='No puede faltar el archivo';
        } 
    if  (preg_match("/".PATRON_archiPubli."/", $arc, $a) <> 1) {
           $ok=false;
           return PATRON_archiPubli_men;
        }           
     $a=array("PDF","PowerPoint");
     $tarc=trim($tarc);
     if (!in_array($tarc,$a)) {
        $ok=false;
        $r .='El tipo de archivo no es v&aacute;lido. ';
     }
     if ($ok) {
         $r = null;
     }
     return $r;
}



/* para tabla scan3d */

function validar_escaneo3D ($es3D,&$ok,$DebeEstar) {
    $a=array("NO SE HIZO","INCOMPLETO","COMPLETO");
    if (!in_array($es3D,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }
}
function validar_video ($vid,&$ok,$DebeEstar) {
    $a=array("NO SE HIZO","INCOMPLETO","COMPLETO");
    if (!in_array($vid,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }
}

function validar_distanciaVideo ($disV,&$ok,$DebeEstar) {
	$r=validar_decimal($disV,CONST_distanciaVideo_min,CONST_distanciaVideo_max,$ok,$DebeEstar);
	return $r;
}
function validar_modelo3D ($mod,&$ok,$DebeEstar) {
    $a=array("","MALO","REGULAR","BUENO");
    if (!in_array($mod,$a)) {
        $ok=false;
        return 'Seleccionar un elemento de la lista';
     }
}

function validar_modeloVolumen($modV,&$ok,$DebeEstar) {
	$r= validar_decimal($modV,CONST_modeloVolumen_min,CONST_modeloVolumen_max,$ok,$DebeEstar);
	return $r;
}

// *****************************************************
// *****************************************************
/* varios */
function limpia($dato) {
  $dato = trim($dato);
  $dato = stripslashes($dato);
 /* $dato = htmlspecialchars($dato);*/
  return $dato;
}

/*devuleve true si el campo está vacío y el string limpio de espacios*/
function vacio(&$campo) {
    $campo=trim($campo);
    $r=false;
    if (empty($campo)) {
       $r=true;
    }
    return $r;
}

// *****************************************************
// *****************************************************
/* funcion para ejecutar multi_query y mostrar                      */
/* los resultados  en tabla                                         */
/* $elSQL es el query multiple                                      */
/* $boton: "botonsi" agrega en la linea un boton con onclick=haceAlgo(a,b,c..) */
/*          donde a,b,c... son los valores de la linea de la tabla*/
/*         "monoscape" la tabla incluye style="font-family:Lucida Console, Monaco, monospace */
//ejemplo
//$sql ="CALL `pp-zzrespuesta`();";
//$sql .="SELECT * FROM zz_gruporespuesta LIMIT 20;";
//$sql .="SELECT * FROM categoria";

function muestraMultipleRS ($elSQL,$boton) {
   // echo $elSQL;
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
{
    echo "No se puede conectar a MySQL: " . mysqli_connect_error();
    exit;
}
            
                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
            
            
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8. " );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
    do {
        /* x cada Result Set */
        if ($result = mysqli_store_result($con)) {
           if ($boton<>"monoscape") {
                echo '  <table class="table table-condensed table-hover" id="tablaResu">
                <thead>
                <tr>
                ';
           }
           else
           {
               echo '  <table class="table table-condensed table-hover" style="font-family:Lucida Console, Monaco, monospace" id="LaTabla">
               <thead>
               <tr>'; 
           }
           $elEnca=array();
           $encabezado=mysqli_fetch_fields($result);
           foreach ($encabezado as $enca) {
                echo "<th>".$enca->name."</th>";
                $elEnca[]=$enca->name;
            }
            if ($boton=="botonsi") {
                echo "<td></td>";
            }
            echo "</tr></thead><tbody>";
            $cuenta=0;
            while ($row = mysqli_fetch_row($result)) {
				if($row[0] <>"#NOVA") { /* lineas que no van en la salida */
					$cuenta +=1;
					echo "
					<tr>";
					$hAlgo="";
					for ( $i=0 ; $i<count($row) ; $i++ )
					  {
						echo "<td title=$elEnca[$i]>".htmlspecialchars($row[$i])."</td>";
						$hAlgo .= "'".$row[$i]."',";
					 }
					if ($boton=="botonsi") {
						$hAlgo = substr($hAlgo,0,strlen($hAlgo)-1);
						echo '<td><a class="btn btn-warning" title="click" onclick="haceAlgo( '.$hAlgo.' )">'.
					   '<span class="glyphicon glyphicon-stop"></span></a> </td>';                  
					}
					
					echo "</tr>";
				}
            }
            mysqli_free_result($result);
            echo "
            </tbody>
            </table>
            
            ";
        }
        /* mostrar divisor */
        if (mysqli_more_results($con)) {
            echo "<br>
            
            ";
        }
    } while (mysqli_more_results($con) and mysqli_next_result($con));
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
}
    

    mysqli_close($con);
return $cuenta;    
}




// *****************************************************
// *****************************************************
/* funcion para ejecutar multi_query y descargar                    */
/* los resultados  en UN archivo XLSX                               */
/* con una hoja resultados y otra con el grupo  respuesta (si hay)  */
/* $elSQL es el query multiple                                      */
/* $arch: nombre base para el archivo xls				            */
//ejemplo
//$sql ="CALL `pp-zzrespuesta`();";
//$sql .="SELECT * FROM zz_gruporespuesta LIMIT 20;";
//$sql .="SELECT * FROM categoria";
//$sql .="SELECT * FROM categoria";

function XlsXOnDeFlai ($elSQL,$arch) {
        
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }

                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
          
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        /* no doy exit! que siga.... */
    }         
          
/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
    
    /**  BASADO EN
	*  https://github.com/PHPOffice/PHPExcel
     * PHPExcel
     * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
     * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
    */
    /*define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');*/
    /** Error reporting */
    /*  error_reporting(E_ALL);*/
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    /** Include PHPExcel */
    require_once 'PHPExcelClasses/PHPExcel.php';

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("GEEM@-web")
                                 ->setLastModifiedBy("GEEM@-web")
                                 ->setTitle("Resultados")
                                 ->setSubject("Resultados")
                                 ->setDescription("Resultados, generado en GEEM@-web")
                                 ->setKeywords("GEEM@-web")
                                 ->setCategory("Resultados");


    // Add some data
    // hoja Resultados...
    $objPHPExcel->getActiveSheet()->setTitle('Resultados de la consulta');

    // x si hay más respuestas
    $clonedBlanca = clone $objPHPExcel->getActiveSheet();
    $clonedBlanca->setTitle('blanca');
    $objPHPExcel->addSheet($clonedBlanca);
    
    $nGrupos = 1;
    
    $xFila=1;
    $iInicial=0;
    do {
        /* x cada Result Set */
        if ($result = mysqli_store_result($con)) {
            $encabezado=mysqli_fetch_fields($result);
            $objPHPExcel->setActiveSheetIndex(0);
            $xColumna=0;

            foreach ($encabezado as $enca) {
                if ($enca->name=="GrupoRespuesta"){
                    $nGrupos=$nGrupos+1;
                    $clonedHoja = clone $objPHPExcel->getSheetByName("blanca");
                    $clonedHoja->setTitle('Respuesta_'.$nGrupos);
                    $objPHPExcel->addSheet($clonedHoja);
                    $hIndex = $objPHPExcel->getIndex($objPHPExcel->getSheetByName('Respuesta_'.$nGrupos));
                    $objPHPExcel->setActiveSheetIndex($hIndex);
                    $xFila =1;
                    $iInicial=1;
                    break;                  
                }

                
            }
            
            // linea de encabezados de columna
            foreach ($encabezado as $enca) {
                if ($enca->name<>"GrupoRespuesta" ){
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($xColumna, $xFila, $enca->name);
                    $xColumna +=1;
                }
            }
            // lineas de resultados
            while ($row = mysqli_fetch_row($result)) {
                $xFila +=1;
                $xColumna=0;
                for ( $i=$iInicial ; $i<count($row) ; $i++ )
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($xColumna, $xFila, $row[$i]);
                        $xColumna +=1;
                    }
            }
            $xFila +=2;
            mysqli_free_result($result);
        }
        if (mysqli_more_results($con)) {
            }
    } while (mysqli_more_results($con) and mysqli_next_result($con));
}

$hIndex = $objPHPExcel->getIndex($objPHPExcel->getSheetByName('blanca'));
$objPHPExcel->removeSheetByIndex($hIndex);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$arch.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');


    mysqli_close($con);
exit;
}




// *****************************************************
// *****************************************************
/* funcion para ejecutar multi_query y descargar                    */
/* los resultados  en un unico archivo csv                         		    */
/* $elSQL es el query multiple                                      */
/* $arch: nombre base para el archivos csv                          */
//ejemplo
//$sql ="CALL `pp-zzrespuesta`();";
//$sql .="SELECT * FROM zz_gruporespuesta LIMIT 20;";
//$sql .="SELECT * FROM categoria";
//$sql .="SELECT * FROM categoria";

function csvOnDeFlai ($elSQL,$arch) {
        
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }

                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
          
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        /* no doy exit! que siga.... */
    }         
          
	/* ejecutar multi consulta */
	if (mysqli_multi_query($con, $elSQL)) {
		
		// archivo de resultados
		$salida=$arch;
		$nGrupos = 0;
		$fp = fopen('php://output', 'w');
		$blibli = array("","");
		if ($fp) {
			
		ob_start();
	
			do {
				/* x cada Result Set */
				if ($result = mysqli_store_result($con)) {
					$encabezado=mysqli_fetch_fields($result);
					$shift=false;
					foreach ($encabezado as $enca) {
						if ($enca->name=="GrupoRespuesta"){
							$nGrupos=$nGrupos+1;
							$salida = $arch.'_Respuesta_'.$nGrupos;
							fputcsv($fp, $blibli);
							fputcsv($fp, $blibli);
							fputcsv($fp,array($salida,""));
							$shift = true;
							break;                  
						}
					}
					
					$titulos = array();
					// linea de encabezados de columna
					foreach ($encabezado as $enca) {
						if ($enca->name<>"GrupoRespuesta" ){
							$titulos[] = $enca->name;
						}
					}
					
					
					// resultados
						fputcsv($fp, $titulos);
						while ($row = $result->fetch_array(MYSQLI_NUM)) {
							if($shift) {
								$av=array_shift($row);							
								fputcsv($fp, array_values($row));
							}
							else {
								fputcsv($fp, array_values($row));
							}
						}						
						fputcsv($fp, $blibli);	/*2 lineas en blanco */
						fputcsv($fp, $blibli);
						
						mysqli_free_result($result);

					if (mysqli_more_results($con)) {
						}
				}
			} while (mysqli_more_results($con) and mysqli_next_result($con));

			fputcsv($fp, $blibli);
			fputcsv($fp, $blibli);
			
			$str = ob_get_clean();
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$arch.'.csv"');
			header('Pragma: no-cache');
			header('Expires: 0');
			mysqli_close($con);
			exit($str);
		}
		
		/*fclose($fp);*/
	}

}








// *****************************************************
// *****************************************************
/* crea el excel fichaCampo.xlsx con tag-nombre para trabajo en campo */
/* tag-marca x 6 = 12 columnas */
/* la salida es a partir de un template en xlstempla/pla_fichaCampo-template.xlsx */
/* para hoja A4 portrait con margenes estrechos */
/* y se escribe un encabezado intermedio para que al doblar la hoja, */
/* quede en formato A5 utilizable */

function plaFichaCampo() {
		
	/* la consulta que genera la tabla */
	$elSQL ="CALL `pla-ficha trabajo campo tag-nombre`();";
	$aEncabe = array('tag','marca','tag','marca','tag','marca','tag','marca','tag','marca','tag','marca');
		 
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
	{
		echo "No se puede conectar a MySQL: " . mysqli_connect_error();
		exit;
	}


	/* mensajes en castellano*/
	if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
	{
		echo("Error lc_messages castellano. " );
		/* no doy exit! que siga.... */
	}

	/* cargo UTF8 */
		if (!mysqli_set_charset($con, "utf8")) {
		echo("Error cargando el conjunto de caracteres utf8" );
		/* no doy exit! que siga.... */
	}         


	/**  BASADO EN https://github.com/PHPOffice/PHPExcel
		
	/* ejecuta consulta */
	if ($result=mysqli_query($con, $elSQL)) {
		
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
 
		/** Include PHPExcel */
		require_once 'PHPExcelClasses/PHPExcel.php';

		/* carga template */
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load("xlstempla/pla_fichaCampo-template.xlsx");
		
		// agregamos filas y datos
		
		$xFila=2;
		$iInicial=0;
		$objPHPExcel->setActiveSheetIndex(0);
		$xColumna=0;
		
		// lineas de resultados
		$nFilasIns=1;
		while ($row = mysqli_fetch_row($result)) {
			$xFila +=1;
			$xColumna=0;
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($xFila,1);
			if ($nFilasIns==24){
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($xFila,1);
				
				$ce  = PHPExcel_Cell::stringFromColumnIndex($xColumna).$xFila;
				$objPHPExcel->getActiveSheet()->fromArray($aEncabe,NULL,$ce); //encabezado intermedio
				$ce2 = PHPExcel_Cell::stringFromColumnIndex($xColumna+11).$xFila; 
				$objPHPExcel->getActiveSheet()->getStyle("$ce:$ce2")->getFont()->setBold( true );
				$objPHPExcel->getActiveSheet()->getStyle("$ce:$ce2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle("$ce:$ce2")->getBorders()->applyFromArray(
         array(
             'allborders' => array(
                 'style' => PHPExcel_Style_Border::BORDER_MEDIUM)
         )
 );
				
				
				
				
				$xFila +=1;
			}
			if ($nFilasIns==48){
				$nFilasIns=0;
			}

			for ( $i=$iInicial ; $i<count($row) ; $i++ )
				{
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($xColumna, $xFila, $row[$i]);
					$xColumna +=1;
				}
			$nFilasIns+=1;
		}
				
		mysqli_free_result($result);
	}


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);


	// Redirect output to a client’s web browser (Excel2007)
	ob_end_clean();
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.'fichaCampo.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');

		mysqli_close($con);
	exit;
}






// *****************************************************
// *****************************************************
/* crea el excel arch.xlsx de hoja unica*/
/* usando el template provisto */
/* en base a la consulta elSQLn */

/*function XlsXOnDeFlai_Template($elSQL,$arch,$template) {*/
/* uso func_get_arg x que el server tiene una php 5.4*/
function XlsXOnDeFlai_Template() {
	$elSQL=func_get_arg (0);
	$arch=func_get_arg (1);
	$template=func_get_arg (2);
	$xFila=2;   // desde que fila del xlsx
	if(func_num_args()==4){
		$xFila=func_get_arg (3);
	}
		
		 
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
	{
		echo "No se puede conectar a MySQL: " . mysqli_connect_error();
		exit;
	}


	/* mensajes en castellano*/
	if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
	{
		echo("Error lc_messages castellano. " );
		/* no doy exit! que siga.... */
	}

	/* cargo UTF8 */
		if (!mysqli_set_charset($con, "utf8")) {
		echo("Error cargando el conjunto de caracteres utf8" );
		/* no doy exit! que siga.... */
	}         


	/**  BASADO EN https://github.com/PHPOffice/PHPExcel
	/* ejecuta consulta */
		/* NOTA MRM: habría que migrar a PhpSpreadsheet */
	
	if ($result=mysqli_query($con, $elSQL)) {
		
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
 
		/** Include PHPExcel */
		require_once 'PHPExcelClasses/PHPExcel.php';

		/* carga template */
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load("xlstempla/$template");
		
		// agregamos filas y datos
		
		$iInicial=0;
		$objPHPExcel->setActiveSheetIndex(0);
		$xColumna=0;
		
		// lineas de resultados
		while ($row = mysqli_fetch_row($result)) {
			$xFila +=1;
			$xColumna=0;
						//no inserta!!! $objPHPExcel->getActiveSheet()->insertNewRowBefore($xFila,1);
			for ( $i=$iInicial ; $i<count($row) ; $i++ )
				{
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($xColumna, $xFila, $row[$i]);
					$xColumna +=1;
				}
		}
				
		mysqli_free_result($result);
	}


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);


	// Redirect output to a client’s web browser (Excel2007)
	ob_end_clean();
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$arch.'.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');

		mysqli_close($con);
	exit;
}












/* dada una latitud o longitud en algún formato válido  */
/* ggg.gggggg gg mm.mmmmmm  gg mm ss.s                  */
/* la lleva a formato decimal                           */
/* devuelve true si el string está vacío                */
/* devuelve false y $err si hay error                    */
/* sino, devuelve la entrada convertida a grados decimal*/
/*    y en $LaLo la cadena de entrada limpia            */
Function converLatLon (&$LaLo,$ques,&$err) {
    $err="";
    $ll = trim($LaLo);
    if (empty($ll)) {
        return true;
    }
    /*$errForma="Formato incorrecto. Debe ser ggg.gggggg   o   gg mm.mmmmmm   o   gg mm ss.s";*/
    $errForma="Formato incorrecto. ";
    /*elimino cualquier caracter excepto el punto si hubiera*/
    $punto=substr_count($ll,".");
    if ($punto>1) {
        $err = $errForma;
        return false;
    }
    $punto = strpos($ll,".");
    $signo = strpos($ll,"-");
    /* elimino todo lo que no es dígito, luego repongo . y signo */
    $ll = preg_replace('/\D/',' ',trim($LaLo));
    if ($punto!==false){
        $ll = substr_replace($ll,".",$punto,1);
    }
    $ll=trim($ll);
    /* termino de limpiar blancos */
    $ll = preg_replace("/(\s){2,}/",'$1',$ll);  
//    $ll = str_replace(" .",".",$ll);
//    $ll = str_replace(". ",".",$ll);

    /* Debe ser ggg.gggggg   o   gg mm.mmmmmm   o   gg mm ss.s: 1, 2 o 3 partes */
    $parte = preg_split("/[\s]/",$ll);   /* verifico partes de formatos aceptados */
    $npartes = count($parte);
   
    if ($npartes>3 or empty($ll)) {
        $err = $errForma;
        return false;
    }

    $elSigno=1;
    if ($signo===0){   /*se asumi signo solo en grados*/
        $ll="-".$ll;
        $elSigno = -1;
    }   
   
//echo "<br>".$npartes." xxx ".$parte[0]." xxx ".$parte[1]." xxx ".$parte[2];

    switch ($npartes) {
        case 1:
            // fmt = gg.gggggg
            $g = floatval($parte[0]);
            if ( ($ques=='lat' and abs($g)>90) or
               ($ques=='lon' and abs($g)>180) ) {
                $err = "Fuera de rango.";
                return false;
             }
            $LaLo = $ll;
            return round($g*$elSigno,6);
          
        
        case 2:
            // fmt = gg mm.mmmm
            if (strpos($parte[0],".")!==false) {
                $err = $errForma;
                return false;
            }
            $g = floatval($parte[0]);
            if (($ques=='lat' and abs($g)>90) or
               ($ques=='lon' and abs($g)>180)) {
                $err .= "Grados fuera de rango.";
            }
            $m = floatval($parte[1]);
            if ( $m>=60 ) {
                  $err .= " Minutos fuera de rango.";
            }        
            if (empty($err)){
                $LaLo = $ll;
                return round(($g + ($m/60.))*$elSigno,6);
            }
            else{
                return false;
            }
          
        
        case 3:
            // fmt = gg mm ss.s
            if (strpos($parte[0],".")!==false) {
                $err = $errForma;
                return false;
            }
            $g = floatval($parte[0]);
            if (($ques=='lat' and abs($g)>90) or
               ($ques=='lon' and abs($g)>180)) {
                $err .= "Grados fuera de rango.";
            }
            if (strpos($parte[1],".")!==false) {
                $err .= $errForma;
                return false;
            }
            $m = floatval($parte[1]);
            if ( $m>=60 ) {
                  $err .= " Minutos fuera de rango.";
            }  
            $s = floatval($parte[2]);
            if ( $s>=60 ) {
                  $err .= " Segundos fuera de rango.";
            }
            if (empty($err)){
                $LaLo = $ll;
                return round(($g + ($m/60.)+ ($s/3600.))*$elSigno,6);
            }
            else{
                return false;
            }
      
    }
}

/* validación basica para condi en los scripts tabla_tb.php */
/* $acol    el arreglo de columnas que acepta el script */
/* $str     condi                                       */
/* $xl      la longitud maxima permitida para un valor  */
/* devuelve true si todo ok o false en caso contrario   */
function val_condi($acol,$str,$xl){
	if ($str=="true"){
		return true;
	}
    $arr = explode(" AND ",$str);
    $xe = true;
    foreach ($arr as $a) {
        $cv=explode("=",$a);
        if (!in_array(trim($cv[0]," <>"),$acol)  or strlen(trim($cv[1]))> $xl) {
            $xe = false;
            break;
        }
    }   
    return $xe;
}


/* si hay error (evalua false) logout */
function siErrorFuera ($v, $fol = ""){
    if (!$v){
		if($fol<>""){
			$fol="../";
		}
		$h="Location:".$fol."tb_logout.php";
        header($h); // echo  'fuera';
        exit;          
      } 
}




// *****************************************************
// *****************************************************
/* ejecuta consulta con multi_query que corresponde al censo PICO   */
/* CALL `censo-totales por anio y tipo censo NO MENSUAL .....       */
/* y muestra los resultados  en un grafico SOLO el primer ResultSet */
/* (la tabla que se ve por pantalla)                                */
/* $elSQL es el query multiple                                      */
/* el grafico: genera codigo javascript usando Chart.js             */
/* SE PRODUCE UN GRAFICO DE BARRAS POR CATEGORIA                    */

function graficoCensoPico ($elSQL) {
$sq=strpos($elSQL,"CALL `censo-totales por anio y tipo censo NO MENSUAL");
if ($sq === false or $sq>0) {
    echo "<br> No corresponde la consulta (graficoCensoPico)";
    exit;
}
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }
                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
            
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
        /* para primer ResultSet */
    if ($result = mysqli_store_result($con)) {

        echo '<div id="container" style="width: 100%;">
                <canvas id="myChart" width="600" height="400"></canvas>
            </div>
            <script type="text/javascript" src="Chartjs/Chart.min.js"></script>     
            <script type="text/javascript" src="Chartjs/maifunc.js"></script>
            <script>
            var labelX=[';
            
        /* label X para el grafico de barras de los nombres de columnas*/
        $encabezado = mysqli_fetch_fields($result);
        $ne = count($encabezado);
        $e = array_shift($encabezado);   /* labelX no incluye Tcenso vecindario fechaTotal*/
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $a = array();
        $c = array();
        $nc=-1;
        foreach ($encabezado as $enca) {
            $a[]=$enca->name;
            $nc+=1;
            $c[]=$nc;
        }
        $e= '"'.implode('","',$a).'"];';
        echo $e." 
            ";
        
        /* cada fila del RS, un DataSet (DS) para el grafico */
        $ds= 0;
        $dsLabel = array();
        while ($row = mysqli_fetch_row($result)) {
            $ds +=1;
            $e = array_shift($row);             /*PVALDES*/
            $dsLabel[] = array_shift($row);     /*label del DS*/
            $gFecha = array_shift($row);        /*fecha (la misma para todos los DS)*/
            $e = implode(",",$row);             /* los valores de cada columna */
            echo "
                var DS$ds = [$e];";
        }
        mysqli_free_result($result);

        if ($ds==0) {
            echo "</script>No hay datos";
            return;
        }       
        
        echo '          
            var Manda = [';
        $e= implode(',',$c).'];';       
        echo $e." 
            ";
        
        /* configuramos grafico chartjs */
        echo '
            var DatosChart = {
                    labels: labelX ,
                    datasets: [';
        /* DS1 */
        echo "                  {
                        label: '$dsLabel[0]',"."
                        data: DS1,
                        backgroundColor: maiColores[0],
                        borderColor: maiColores[0],
                        borderWidth: 1,
                        lineTension: 0
                    }";
        /* DS2 .... */
        for ( $i=1 ; $i<$ds ; $i++ )
            {
                $j=$i+1;
                echo ",
                    {
                        label: '$dsLabel[$i]',"."
                        data: DS$j,
                        backgroundColor: maiColores[$i],
                        borderColor: maiColores[$i],
                        borderWidth: 1,
                        lineTension: 0
                    }";
            }
        echo "]
                };";
        echo "          var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: DatosChart,
                options: {
                    maintainAspectRatio:false,
                    title:{
                        text: 'Censo en pico de temporada reproductiva $gFecha',
                        display: true,
                        },              
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,                           
                            fontColor: 'rgb(0, 0, 0)'
                        }
                    }       
                }
            });";
        echo "
            /*copia de datos para reponer*/
            var clabelX= labelX.slice(0);";
        for ( $i=1 ; $i<$ds+1 ; $i++ )  {   
            echo "
            var cDS$i = DS$i.slice(0);";
        }
        
        /* fin javascript */
        echo " 
            </script> <br>
            ";
        echo '
                        M&aacute;ximo del eje Y en: &nbsp;&nbsp;&nbsp;<input type="number" class="input-sm" style="width:100px" id="maxY" name="maxY" onchange=limiteY(this.value)>&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick=limiteYauto() class="btn btn-primary btn-xs">auto</button> 

        <br><br>';
        echo "
        Para ocultar/mostrar datos de un vecindarios (DS), hacer click sobre la leyenda correspondiente arriba. ";
        echo '
                Tambi&eacute;n &nbsp;&nbsp;
                <button type="button" onclick=DSInvierteSeleccion(myChart) class="btn btn-primary btn-xs">invertir selecci&oacute;n de DS</button>&nbsp;&nbsp;
                <button type="button" onclick=DSponeSacaTodos(myChart,this) class="btn btn-primary btn-xs">ocultar todos los DS</button>&nbsp;&nbsp;';
            
        echo '
        <br><br>Para ocultar/mostrar datos de una categor&iacute;a, hacer click sobre la correspondiente a continuci&oacute;n. Tambi&eacute;n &nbsp;&nbsp;
                <button type="button" onclick=CXponeSaca(myChart,DatosChart,this) class="btn btn-primary btn-xs">ocultar todas las categor&iacute;as</button><br><br>';
                    
        for ( $i=0 ; $i<$nc+1 ; $i++ )  {   
            echo "
                <label class='checkbox-inline'><input class=chbx type=checkbox name=cat$i onclick=SacaPone(this) value=$i checked>$a[$i]</label>";
        }
    }
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
}
    

    mysqli_close($con);
    
}






// *****************************************************
// *****************************************************
/* ejecuta consulta con multi_query que corresponde al censo PARCIAL*/
/* CALL `censo-totales por anio y tipo censo NO MENSUAL .....       */
/* y muestra los resultados  en un grafico SOLO el primer ResultSet */
/* (la tabla que se ve por pantalla)                                */
/* $elSQL es el query multiple                                      */
/* el grafico: genera codigo javascript usando Chart.js             */
/* SE PRODUCE UN GRAFICO DE LINEAS EN EL TIEMPO                     */

function graficoCensoParcial ($elSQL,$qtcenso) {
$sq=strpos($elSQL,"CALL `censo-totales por anio y tipo censo NO MENSUAL");
if ($sq === false or $sq>0) {
    echo "<br> No corresponde la consulta (graficoCensoPico)";
    exit;
}
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }
                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
            
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
    /* para primer ResultSet */
    if ($result = mysqli_store_result($con)) {

        /* encabezado/nombres de columnas/categorias: ahora van como dataset!)*/
        $encabezado = mysqli_fetch_fields($result);
        $e = array_shift($encabezado);   /* fuera Tcenso vecindario fechaTotal*/
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $nDS = count($encabezado);      /* numero de DS */
        $enc = array();
        foreach ($encabezado as $enca) {
            $enc[]=$enca->name;
        }       
        
        
        /* llevo el resultado a un arreglo de 2 dimensiones */
        $a = array();       
        $sVecin = "";
        while ($row = mysqli_fetch_row($result)) {
            $e = array_shift($row);         /*PARCIAL fuera*/
            if ($sVecin==""){
                $sVecin = $row[0];  /* para el titulo */    
            }
            $e = array_shift($row);
            $a[] = $row;
        }
        mysqli_free_result($result);    
        if (count($a)==0) {
            echo "No hay datos";
            return;
        }
        /* $a tiene en la primer columna las fechas; el resto de las
              columnas tiene las "categorias", que ahora son los DS;
              la última de Total no se grafica*/
        $nf = count($a)-1; 
        
        
        echo '
        
        <div id="container" style="width: 100%;">
                <canvas id="myChart" width="600" height="400"></canvas>
            </div>
            <script type="text/javascript" src="Chartjs/moment.js"></script>
            <script type="text/javascript" src="Chartjs/Chart.min.js"></script>     
            <script type="text/javascript" src="Chartjs/maifunc.js"></script>
            <script>
            ';
        
        $f1a= $a[0][0];             /* en formato AAAA-MM-DD */
        $ffa= $a[$nf-1][0];
        $f = explode ("-",$f1a);        /* en formato MM/DD/AAAA */
        $f1 = $f[1]."/".$f[2]."/".$f[0];
        $f = explode ("-",$ffa);        
        $ff = $f[1]."/".$f[2]."/".$f[0];
        
        echo "
            var labelX = [
                cfecha('$f1',-2),
                cfecha('$ff',+2) ];
                ";

        /* $a[i][0] tiene la fecha, $a[i][j>0] (columnna) los valores para la linea DS*/
        for ( $j=1 ; $j<$nDS+1 ; $j++ )
        {
            $tx= $a[0][0];
            $ty= $a[0][$j];
			if(is_null($ty)){
				$ty="null";
			}
            echo "
            var dDS$j = [
                {x: '$tx',y: $ty }";
            for ( $i=1 ; $i<$nf ; $i++ ) {
                $tx= $a[$i][0];
                $ty= $a[$i][$j];
				if(is_null($ty)){
					$ty="null";
				}				
                echo ",
                {x: '$tx',y: $ty}";
            }
            echo "
            ];";
        }

        
        /* configuramos grafico chartjs */
        echo '
            var tiempoChartData = {
                    labels: labelX ,
                    datasets: [
                    ';
        /* dDS1 */
        echo "      {
                        label: '$enc[0]',
                        data: dDS1,
                        backgroundColor: maiColores[0],
                        borderColor: maiColores[0],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
        /* dDS2 .... */
        for ( $j=1 ; $j<$nDS ; $j++ )
            {
                $k=$j+1;
                echo ",
                    {
                        label: '$enc[$j]',
                        data: dDS$k,
                        backgroundColor: maiColores[$j],
                        borderColor: maiColores[$j],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
            }
        echo "]
                };";
        echo "
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: tiempoChartData,
                options: {
                    maintainAspectRatio:false,
                    title:{
                        text: 'Censo $qtcenso en $sVecin entre $f1a y $ffa',
                        display: true,
                            },
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    format: 'YYYY-MM-DD',
                                    tooltipFormat: 'MMM D',
                                    unit: 'day',
                                    unitStepSize: 7,
                                    displayFormats: {
                                        day: 'MMM D'
                                        }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'fecha'
                                }
                            }, ],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'cantidad'
                                }
                            }]
                        },
                        legend: {
                            display: true,
                            
                            position: 'bottom',
                            labels: {
                                boxWidth: 20,
                                fontColor: 'rgb(0, 0, 0)'
                            }
                    }       

                }
            });";
        
        /* fin javascript */
        echo " 
            </script> <br>
            ";
        echo '
                        M&aacute;ximo del eje Y en: &nbsp;&nbsp;&nbsp;<input type="number" class="input-sm" style="width:100px" id="maxY" name="maxY" onchange=limiteY(this.value)>&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick=limiteYauto() class="btn btn-primary btn-xs">auto</button> 

        <br><br>';
            
        echo "Para ocultar/mostrar la l&iacute;nea de una categor&iacute;a (DS), hacer click sobre la leyenda correspondiente<br><br>";
        echo '
                <br>Tambi&eacute;n 
                <button type="button" onclick=DSInvierteSeleccion(myChart) class="btn btn-primary btn-xs">invertir selecci&oacute;n de DS</button> 
                <button type="button" onclick=DSponeSacaTodos(myChart,this) class="btn btn-primary btn-xs">ocultar todos los DS</button>';      
    }
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
}
    

    mysqli_close($con);
    
}





// *****************************************************  no se usa
// *****************************************************
/* ejecuta consulta con multi_query que corresponde al censo PVALDES*/
/* CALL `censo-compara NO MENSUAL .....                             */
/* y muestra los resultados en un grafico SOLO de la columna "total"*/
/* (el resto se maneja con JAVASCRIPT en la página)                 */
/* $elSQL es el query multiple                                      */
/* el grafico: genera codigo javascript usando Chart.js             */
/* SE PRODUCE UN GRAFICO DE LINEAS EN EL TIEMPO                     */

function graficoCensoComparaPicoSOLO ($elSQL) {
$sq=strpos($elSQL,"CALL `censo-compara NO MENSUAL");
if ($sq === false or $sq>0) {
    echo "<br> No corresponde la consulta (graficoCensoCompara)";
    exit;
}
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }
                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
            
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
        /* para primer ResultSet */
    if ($result = mysqli_store_result($con)) {

        /* encabezado/nombres de columnas/categorias: ahora van como dataset!)*/
        $encabezado = mysqli_fetch_fields($result);
        $e = array_shift($encabezado);   /* fuera Tcenso IDvecindario fechaTotal Tanio*/
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $enc = array();
        foreach ($encabezado as $enca) {
            $enc[]=$enca->name;
        }       
        $nc = count($enc);
        
        /* llevo el resultado a un arreglo de 2 dimensiones */
        $a = array();       
        $f1a = "9999-99-99";   //"99-99";
        $ffa = "";
        while ($row = mysqli_fetch_row($result)) {
            $e = array_shift($row);         /*Tcenso fuera*/
            $a[] = $row;
            $f1a = min($f1a,$row[1]);       /* fechas minima y maxima */
            $ffa = max($ffa,$row[1]);       
        }
        mysqli_free_result($result);    
        
        mysqli_close($con);
        
        /* $a tiene IDvecindario, fechaTotal, Tanio y el resto de las
              columnas tiene las "categorias" */
        /* graficar una linea por vecindario (DS) */
        $nf = count($a); 
        
        
        echo '
        
        <div id="container" style="width: 100%;">
                <canvas id="myChart" width="600" height="400"></canvas>
            </div>
            <script type="text/javascript" src="Chartjs/moment.js"></script>
            <script type="text/javascript" src="Chartjs/Chart.min.js"></script>     
            <script type="text/javascript" src="Chartjs/maifunc.js"></script>
            <script>
            ';
        
        $f1a= $f1a;                     /* fecha de formato 2017-MM-DD */
        $ffa= $ffa;
        $f = explode ("-",$f1a);        /* en formato MM/DD/AAAA */
        $f1 = $f[1]."/".$f[2]."/".$f[0];
        $f1 = "01/06/".$f[0];
        $f = explode ("-",$ffa);        
        $ff = $f[1]."/".$f[2]."/".$f[0];
        $ff = "12/30/".$f[0];
        $af1a = substr($f1a,0,4);
        $affa = substr($ffa,0,4);
        echo "
            var labelX = [
                cfecha('$f1',-2),
                cfecha('$ff',+2) ];
                ";

        /* $a[i][1] tiene la fecha, $a[i][3] es la columnna de "total" */
        /* cada cambio de vecindario [1] nuevo DS*/
        $jCompa=3; 
        $vecin =array();
        $vecin[] = $a[0][0];
        $nvec=0;

        $tx= $a[0][1];      //"2017-".substr($a[0][1],5,5);     /*fecha de formato 2017-MM-DD*/
        $ty= $a[0][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
        
        echo "
        var dDS0 = [
                {x: '$tx',y: $ty}";
        for ( $i=1 ; $i<$nf ; $i++ ) {
            $tx= $a[$i][1];         //"2017-".substr($a[$i][1],5,5);
            $ty= $a[$i][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
			
            if($vecin[$nvec]<>$a[$i][0]) {
                $nvec +=1;
                $vecin[]=$a[$i][0];
                echo "
                ];
        var dDS$nvec = [";              
            }
            else {
                echo ",";
            }
            echo "
                {x: '$tx',y: $ty}";
            }
            echo "
            ];";

        
        /* configuramos grafico chartjs */
        echo '
            var tiempoChartData = {
                    labels: labelX ,
                    datasets: [
                    ';
        /* dDS0 */
        echo "      {
                        label: '$vecin[0]',
                        data: dDS0,
                        backgroundColor: maiColores[0],
                        borderColor: maiColores[0],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
        /* dDS2 .... */
        for ( $j=1 ; $j<=$nvec ; $j++ )
            {
                echo ",
                    {
                        label: '$vecin[$j]',
                        data: dDS$j,
                        backgroundColor: maiColores[$j],
                        borderColor: maiColores[$j],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
            }
        echo "]
                };";
        $cenc = '"'.$enc[0].'"';
        echo "
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: tiempoChartData,
                options: {
                    maintainAspectRatio:false,
                    title:{
                        text: 'Censo en pico de temporada reproductiva - comparativo $cenc entre $af1a y $affa',
                        display: true,
                            },
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    format: 'YYYY-MM-DD',
                                    tooltipFormat: 'MMM D',
                                    unit: 'year',
                                    unitStepSize: 1,
                                    displayFormats: {
                                        day: 'MMM D'
                                        }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'fecha'
                                }
                            }, ],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'cantidad'
                                },
                                ticks: {
                                    min: 0
                                }                               
                            }]
                        },
                        legend: {
                            display: true,
                            
                            position: 'bottom',
                            labels: {
                                boxWidth: 20,
                                fontColor: 'rgb(0, 0, 0)'
                            }
                    }       

                }
            });";
        
        /* fin javascript */
        echo " 
            </script> <br>
            ";
        echo "Para ocultar/mostrar la l&iacute;nea de vecindario (DS), hacer click sobre la leyenda correspondiente<br><br>";
        echo "Para cambiar la variable que se compara, hacer click en una de las categor&iacute;as a continuci&oacute;n<br><br>";
        echo "
                <label class='radio-inline'><input type=radio name=optCate onclick=CambiaCate(this.value) value=0 checked>$enc[0] </label>";
        for ( $i=1 ; $i<$nc ; $i++ )    {   
            echo "
                <label class='radio-inline'><input type=radio name=optCate onclick=CambiaCate(this.value) value=$i>$enc[$i]</label>";
        }
        echo '
                <br><br>Tambi&eacute;n 
                <button type="button" onclick=DSInvierteSeleccion(myChart) class="btn btn-primary btn-xs">invertir selecci&oacute;n de DS</button> 
                <button type="button" onclick=DSponeSacaTodos(myChart,this) class="btn btn-primary btn-xs">ocultar todos los DS</button>';      
    }
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
    mysqli_close($con);
}
    

    
}


/* */

// *****************************************************
// *****************************************************
/* ejecuta consulta con multi_query que corresponde al censo PVALDES*/
/* CALL `censo-compara NO MENSUAL .....                             */
/* y muestra los resultados en un grafico SOLO de la columna "total"*/
/* (el resto se maneja con JAVASCRIPT en la página)                 */
/* $elSQL es el query multiple                                      */
/* el grafico: genera codigo javascript usando Chart.js             */
/* SE PRODUCE UN GRAFICO DE LINEAS EN EL TIEMPO                     */

function graficoCensoComparaPico ($elSQL) {
$sq=strpos($elSQL,"CALL `censo-compara NO MENSUAL");
if ($sq === false or $sq>0) {
    echo "<br> No corresponde la consulta (graficoCensoCompara)";
    exit;
}
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }

                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }         
          
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
        /* para primer ResultSet */
    if ($result = mysqli_store_result($con)) {

        /* la tabla de numeros */
        echo '  <table class="table table-condensed table-hover" id="tablaResu">
        <thead>
        <tr>
        ';
        /* para tabla */
        $elEnca=array();
        $encabezado=mysqli_fetch_fields($result);
        foreach ($encabezado as $enca) {
            echo "<th>".$enca->name."</th>";
            $elEnca[]=$enca->name;
        }
        /* para grafico */
        /* encabezado/nombres de columnas/categorias: ahora van como dataset!)*/
        $e = array_shift($encabezado);   /* fuera Tcenso IDvecindario fechaTotal Tanio*/
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $enc = array();
        foreach ($encabezado as $enca) {
            $enc[]=$enca->name;
        }
        $nc = count($enc);
        $a = array();       
        $f1a = "9999-99-99"; 
        $ffa = "";


        echo "</tr></thead><tbody>";
        while ($row = mysqli_fetch_row($result)) {
            /* para tabla... a pantalla */
            echo "
            <tr>";
            $hAlgo="";
            for ( $i=0 ; $i<count($row) ; $i++ )
              {
                echo "<td title=$elEnca[$i]>".htmlspecialchars($row[$i])."</td>";
                $hAlgo .= "'".$row[$i]."',";
             }
            
            echo "</tr>";

            /* para grafico... llevo el resultado a un arreglo de 2 dimensiones */
            $e = array_shift($row);         /*Tcenso fuera*/
            $a[] = $row;
            $f1a = min($f1a,$row[1]);       /* fechas minima y maxima */
            $ffa = max($ffa,$row[1]);       
        }
        mysqli_free_result($result);
        echo "
        </tbody>
        </table>
        
        ";

        mysqli_close($con);
        
        /* sigue solo para grafico */
        /* $a tiene IDvecindario, fechaTotal, Tanio y el resto de las
              columnas tiene las "categorias" */
        /* graficar una linea por vecindario (DS) */
        $nf = count($a); 
        
        
        echo '
        
        <div id="container" style="width: 100%;">
                <canvas id="myChart" width="600" height="400"></canvas>
            </div>
            <script type="text/javascript" src="Chartjs/moment.js"></script>
            <script type="text/javascript" src="Chartjs/Chart.min.js"></script>     
            <script type="text/javascript" src="Chartjs/maifunc.js"></script>
            <script>
            ';
        
        $f1a= $f1a;                     
        $ffa= $ffa;
        $f = explode ("-",$f1a);        /* en formato MM/DD/AAAA */
        $f1 = $f[1]."/".$f[2]."/".$f[0];
        $f1 = "01/06/".$f[0];
        $f = explode ("-",$ffa);        
        $ff = $f[1]."/".$f[2]."/".$f[0];
        $ff = "12/30/".$f[0];
        $af1a = substr($f1a,0,4);
        $affa = substr($ffa,0,4);
        echo "
            var labelX = [
                cfecha('$f1',-2),
                cfecha('$ff',+2) ];
                ";

        /* $a[i][1] tiene la fecha, $a[i][3] es la columnna de "total" */
        /* cada cambio de vecindario [1] nuevo DS*/
        $jCompa=3; 
        $vecin =array();
        $vecin[] = $a[0][0];
        $nvec=0;

        $tx= $a[0][1];          /*fecha de formato 2017-MM-DD*/
        $ty= $a[0][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
        
        echo "
        var dDS0 = [
                {x: '$tx',y: $ty}";
        for ( $i=1 ; $i<$nf ; $i++ ) {
            $tx= $a[$i][1];         
            $ty= $a[$i][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
			
            if($vecin[$nvec]<>$a[$i][0]) {
                $nvec +=1;
                $vecin[]=$a[$i][0];
                echo "
                ];
        var dDS$nvec = [";              
            }
            else {
                echo ",";
            }
            echo "
                {x: '$tx',y: $ty}";
            }
            echo "
            ];";

        
        /* configuramos grafico chartjs */
        echo '
            var tiempoChartData = {
                    labels: labelX ,
                    datasets: [
                    ';
        /* dDS0 */
        echo "      {
                        label: '$vecin[0]',
                        data: dDS0,
                        backgroundColor: maiColores[0],
                        borderColor: maiColores[0],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
        /* dDS2 .... */
        for ( $j=1 ; $j<=$nvec ; $j++ )
            {
                echo ",
                    {
                        label: '$vecin[$j]',
                        data: dDS$j,
                        backgroundColor: maiColores[$j],
                        borderColor: maiColores[$j],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
            }
        echo "]
                };";
        $cenc = '"'.$enc[0].'"';
        echo "
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: tiempoChartData,
                options: {
                    maintainAspectRatio:false,
                    title:{
                        text: 'Censo en pico de temporada reproductiva - comparativo $cenc entre $af1a y $affa',
                        display: true,
                            },
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    format: 'YYYY-MM-DD',
                                    tooltipFormat: 'MMM D',
                                    unit: 'year',
                                    unitStepSize: 1,
                                    displayFormats: {
                                        day: 'MMM D'
                                        }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'fecha'
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'cantidad'
                                },
                                ticks: {
                                    min: 0
                                }                               
                            }]
                        },
                        legend: {
                            display: true,
                            
                            position: 'bottom',
                            labels: {
                                boxWidth: 20,
                                fontColor: 'rgb(0, 0, 0)'
                            }
                    }       

                }
            });";
        
        /* fin javascript */
        echo " 
            </script> <br>
            ";
        echo '
                        M&aacute;ximo del eje Y en: &nbsp;&nbsp;&nbsp;<input type="number" class="input-sm" style="width:100px" id="maxY" name="maxY" onchange=limiteY(this.value)>&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick=limiteYauto() class="btn btn-primary btn-xs">auto</button> 

        <br><br>';
        echo "Para ocultar/mostrar la l&iacute;nea de vecindario (DS), hacer click sobre la leyenda correspondiente. ";
        echo 'Tambi&eacute;n 
                <button type="button" onclick=DSInvierteSeleccion(myChart) class="btn btn-primary btn-xs">invertir selecci&oacute;n de DS</button> 
                <button type="button" onclick=DSponeSacaTodos(myChart,this) class="btn btn-primary btn-xs">ocultar todos los DS</button><br><br>';      
        echo "Para cambiar la variable que se compara, hacer click en una de las categor&iacute;as a continuci&oacute;n<br><br>";
        echo "
                <label class='radio-inline'><input type=radio name=optCate onclick=CambiaCate(this.value) value=0 checked>$enc[0] </label>";
        for ( $i=1 ; $i<$nc ; $i++ )    {   
            echo "
                <label class='radio-inline'><input type=radio class=chbx name=optCate onclick=CambiaCate(this.value) value=$i>$enc[$i]</label>";
        }
    }
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
    mysqli_close($con);
}
    

    
}









// *****************************************************
// *****************************************************
/* ejecuta consulta con multi_query que corresponde al censo PARCIAL*/
/* CALL `censo-compara NO MENSUAL .....                             */
/* y muestra los resultados en un grafico SOLO de la columna "total"*/
/* (el resto se maneja con JAVASCRIPT en la página)                 */
/* $elSQL es el query multiple                                      */
/* el grafico: genera codigo javascript usando Chart.js             */
/* SE PRODUCE UN GRAFICO DE LINEAS EN EL TIEMPO                     */
/* como es un unico vencidario las fechas se llevan a un            */
/**         formato 2017-MM-DD  para PARCIAL                        */
/**     o   formato 2016-MM-DD  2017-MM-DD para MUDA según el mes   */

function graficoCensoComparaParcial ($elSQL,$qtcenso) {
$sq=strpos($elSQL,"CALL `censo-compara NO MENSUAL");
if ($sq === false or $sq>0) {
    echo "<br> No corresponde la consulta (graficoCensoCompara)";
    exit;
}
$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
if (mysqli_connect_errno())
         {
          echo "No se puede conectar a MySQL: " . mysqli_connect_error();
          exit;
          }
                
            /* mensajes en castellano*/
            if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
            {
                echo("Error lc_messages castellano. " );
                /* no doy exit! que siga.... */
            }
            
     /* cargo UTF8 */
     if (!mysqli_set_charset($con, "utf8")) {
        echo("Error cargando el conjunto de caracteres utf8" );
        echo mysqli_error($con);
        /* no doy exit! que siga.... */
    }

/* ejecutar multi consulta */
if (mysqli_multi_query($con, $elSQL)) {
        /* para primer ResultSet */
    if ($result = mysqli_store_result($con)) {

        /* la tabla de numeros */
        echo '  <table class="table table-condensed table-hover" id="tablaResu">
        <thead>
        <tr>
        ';
        /* para tabla */
        $elEnca=array();
        $encabezado=mysqli_fetch_fields($result);
        foreach ($encabezado as $enca) {
            echo "<th>".$enca->name."</th>";
            $elEnca[]=$enca->name;
        }
        /* para grafico */
        /* encabezado/nombres de columnas/categorias: ahora van como dataset!)*/
        $e = array_shift($encabezado);   /* fuera Tcenso IDvecindario fechaTotal Tanio*/
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $e = array_shift($encabezado);
        $enc = array();
        foreach ($encabezado as $enca) {
            $enc[]=$enca->name;
        }
        $nc = count($enc);
        $a = array();       
        $f1a = "2017-99-99";    //"99-99";
        $ffa = "";
        if($qtcenso=="MUDA"){
            $f1a = "2016-99-99";            
        }


        echo "</tr></thead><tbody>";
        while ($row = mysqli_fetch_row($result)) {
            if ($row[1]=="Total"){
                break;
            }
            /* para tabla... a pantalla */
            echo "
            <tr>";
            $hAlgo="";
            for ( $i=0 ; $i<count($row) ; $i++ )
              {
                echo "<td title=$elEnca[$i]>".htmlspecialchars($row[$i])."</td>";
                $hAlgo .= "'".$row[$i]."',";
             }
            
            echo "</tr>";

            /* para grafico... llevo el resultado a un arreglo de 2 dimensiones */
            $e = array_shift($row);         /*Tcenso fuera*/
            $a[] = $row;
            $fe0 = substr($row[1],5,5);
            if(substr($fe0,0,2)>="10" and $qtcenso=="MUDA"){
                $fe0 = "2016-".$fe0;
            }
            else{
                $fe0 = "2017-".$fe0;                
            }
//          $f1a = min($f1a,substr($row[1],5,5));       /* fechas (MM-DD) minima y maxima */
//          $ffa = max($ffa,substr($row[1],5,5));       
            $f1a = min($f1a,$fe0);      /* fechas (AAAA-MM-DD) minima y maxima */
            $ffa = max($ffa,$fe0);      
        }
        mysqli_free_result($result);
        echo "
        </tbody>
        </table>
        
        ";

        mysqli_close($con);
        
        /* sigue solo para grafico */
        /* $a tiene IDvecindario, fechaTotal, Tanio y el resto de las
              columnas tiene las "categorias" */
        /* graficar una linea por vecindario (DS) */
        $nf = count($a); 
        
        
        echo '
        
        <div id="container" style="width: 100%;">
                <canvas id="myChart" width="600" height="400"></canvas>
            </div>
            <script type="text/javascript" src="Chartjs/moment.js"></script>
            <script type="text/javascript" src="Chartjs/Chart.min.js"></script>     
            <script type="text/javascript" src="Chartjs/maifunc.js"></script>
            <script>
            ';
        
//      $f1a= "2017-".$f1a;             /* fechas minima y max de formato 2016/2017-MM-DD */
//      $ffa= "2017-".$ffa;
        $f = explode ("-",$f1a);        /* en formato MM/DD/AAAA */
        $f1 = $f[1]."/".$f[2]."/".$f[0];
        $f = explode ("-",$ffa);        
        $ff = $f[1]."/".$f[2]."/".$f[0];
        echo "
            var labelX = [
                cfecha('$f1',-2),
                cfecha('$ff',+2) ];
                ";

        /* $a[i][1] tiene la fecha, $a[i][3] es la columnna de "total" */
        /* cada cambio de año [2] nuevo DS*/
        $jCompa=3; 
        $anio =array();
        $anio[] = $a[0][2];
        $nAni=0;
        
        if($qtcenso=="MUDA" and substr($a[0][1],5,2)>="10") {
            $tx= "2016-".substr($a[0][1],5,5);      /*fecha de formato 2016-MM-DD*/
        }
        else{
            $tx= "2017-".substr($a[0][1],5,5);      /*fecha de formato 2017-MM-DD*/
        }
        $ty= $a[0][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
        
        echo "
        var dDS0 = [
                {x: '$tx',y: $ty}";
        for ( $i=1 ; $i<$nf ; $i++ ) {
            if ($a[$i][0]=="Total"){
                break;
            }
            if($qtcenso=="MUDA" and substr($a[$i][1],5,2)>="10") {
                $tx= "2016-".substr($a[$i][1],5,5);     /*fecha de formato 2016-MM-DD*/
            }
            else{
                $tx= "2017-".substr($a[$i][1],5,5);     /*fecha de formato 2017-MM-DD*/
            }                       
            $ty= $a[$i][$jCompa];
			if(is_null($ty)){
				$ty="null";
			}
			
            if($anio[$nAni]<>$a[$i][2]) {
                $nAni +=1;
                $anio[]=$a[$i][2];
                echo "
                ];
        var dDS$nAni = [";              
            }
            else {
                echo ",";
            }
            echo "
                {x: '$tx',y: $ty}";
            }
            echo "
            ];";

        $af1a = $anio[0];
        $affa = $anio[$nAni];
            
        
        /* configuramos grafico chartjs */
        echo '
            var tiempoChartData = {
                    labels: labelX ,
                    datasets: [
                    ';
        /* dDS0 */
        echo "      {
                        label: '$anio[0]',
                        data: dDS0,
                        backgroundColor: maiColores[0],
                        borderColor: maiColores[0],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
        /* dDS2 .... */
        for ( $j=1 ; $j<=$nAni ; $j++ )
            {
                echo ",
                    {
                        label: '$anio[$j]',
                        data: dDS$j,
                        backgroundColor: maiColores[$j],
                        borderColor: maiColores[$j],
                        borderWidth: 1,
                        lineTension: 0,
                        fill:false
                    }";
            }
        echo "]
                };";
        $cenc = '"'.$enc[0].'"';
        echo "
            var ctx = document.getElementById('myChart');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: tiempoChartData,
                options: {
                    maintainAspectRatio:false,
                    title:{
                        text: 'Censo $qtcenso - comparativo $cenc entre $af1a y $affa',
                        display: true,
                            },
                        scales: {
                            xAxes: [{
                                type: 'time',
                                time: {
                                    format: 'YYYY-MM-DD',
                                    tooltipFormat: 'MMM D',
                                    unit: 'day',
                                    unitStepSize: 7,
                                    displayFormats: {
                                        day: 'MMM D'
                                        }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: 'fecha'
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'cantidad'
                                },
                                ticks: {
                                    min: 0
                                }                               
                            }]
                        },
                        legend: {
                            display: true,
                            
                            position: 'bottom',
                            labels: {
                                boxWidth: 20,
                                fontColor: 'rgb(0, 0, 0)'
                            }
                    }       

                }
            });";
        
        /* fin javascript */
        echo " 
            </script> <br>
            ";
        echo '
                        M&aacute;ximo del eje Y en: &nbsp;&nbsp;&nbsp;<input type="number" class="input-sm" style="width:100px" id="maxY" name="maxY" onchange=limiteY(this.value)>&nbsp;&nbsp;&nbsp;
                        <button type="button" onclick=limiteYauto() class="btn btn-primary btn-xs">auto</button> 

        <br><br>';
        echo "Para ocultar/mostrar la l&iacute;nea de vecindario (DS), hacer click sobre la leyenda correspondiente. ";
        echo 'Tambi&eacute;n 
                <button type="button" onclick=DSInvierteSeleccion(myChart) class="btn btn-primary btn-xs">invertir selecci&oacute;n de DS</button> 
                <button type="button" onclick=DSponeSacaTodos(myChart,this) class="btn btn-primary btn-xs">ocultar todos los DS</button><br><br>';      
        echo "Para cambiar la variable que se compara, hacer click en una de las categor&iacute;as a continuci&oacute;n<br><br>";
        echo "
                <label class='radio-inline'><input type=radio name=optCate onclick=CambiaCateAnio(this.value) value=0 checked>$enc[0] </label>";
        for ( $i=1 ; $i<$nc ; $i++ )    {   
            echo "
                <label class='radio-inline'><input type=radio class=chbx name=optCate onclick=CambiaCateAnio(this.value) value=$i>$enc[$i]</label>";
        }
    }
}
else {
    echo 'Falla query!!!   ';
    echo mysqli_error($con);
    mysqli_close($con);
}
    

    
}


//
// valido entero
//
function validar_entero ($e,$min,$max,&$ok,$NoVacio){
	if(!is_numeric($e) and $NoVacio) {
		$ok=false;
		return "Ingresar un n&uacute;mero";		
	}
	$v=vacio($e);
	if ($v and !$NoVacio) {
		return;
	}	
	if (preg_match("/^-?\d*$/", $e, $a) <> 1)	{
		$ok=false;
		return "Ingresar un n&uacute;mero";
	}
    $cant=intval($e);
    if (!$v and ($cant<$min or $cant>$max)) {
        $ok=false;
        return "Entre $min y $max";     
    }  	
}
  
// valido decimal  
function validar_decimal ($d,$min,$max,&$ok,$NoVacio) {
	if(!is_numeric($d) and $NoVacio) {
		$ok=false;
		return "Ingresar un n&uacute;mero";		
	}
	$v=vacio($d);
	if ($v and !$NoVacio) {
		return;
	}	
	if (preg_match("/^-?\d*\.?\d*$/", $d, $a) <> 1)	{
		$ok=false;
		return "Ingresar un n&uacute;mero";
	}
    if ((!vacio($d)) and ((floatval($d)<$min) or (floatval($d)>$max)) ) {
        $ok=false;
        return "Entre $min y $max";        
    }
}

// validar SI o NO
function validar_SIoNO ($sn,&$ok,$DebeEstar) {
	$v=vacio($sn);
	if ($v and $DebeEstar) {
		$ok=false;
		return "No puede faltar";
    }
	if ($v and !$DebeEstar){
		return;
	}
    if ($sn <> 'SI' and $sn <> 'NO') {
        $ok=false;
        return 'Debe ser SI o NO';      
	}	
}




// devuelve dada la fecha de hoy a que temporada (en el return) y tipo corresponde (en el parametro)
function temporadaAnio_actual (&$tipoT) {
	$temp = date("Y");
	$tempActual=$temp;
	$fhoy = date("Y-m-d");  	//string
	$mhoy = date("m");		    //string
	$eok=true;
	$r=validar_fecha ($fhoy,$eok,true);
	if($r==='En REPRO. ' or $r==='En REPRO. En MUDA. '){
		$tipoT = 'Repro';
		if($mhoy>=11) {
			$tempActual=$temp+1;
		}			
	}
	else
	{
		if($r==='En MUDA. '){
			if($mhoy==12) {
				$temp=$temp+1;
			}
			if($mhoy>=11) {
				$tempActual=$tempActual+1;
			}
			$tipoT = 'Muda';
		}else
		{
			$tipoT = 'Fuera';
		}
	}
	return $tempActual;
}

/* para localizaciones ARGOS */
function validar_ArgosArchivo($arc,&$ok,$DebeEstar) {
    $r="";
    if (vacio($arc) and $DebeEstar) {
        $ok=false;
        $r .='No puede faltar el archivo';
        } 
    if  (preg_match("/".PATRON_archiArgos."/", $arc, $a) <> 1) {
           $ok=false;
           return PATRON_archiArgos_men;
        }           
     if ($ok) {
         $r = null;
     }
     return $r;
}

//de wkt POINT a lon-lat devuelve array lon-lat
function WKTaLonLat($gWKT){
	$gWKT = str_replace("POINT","",$gWKT);
	$gWKT = str_replace("(","",$gWKT);
	$gWKT = str_replace(")","",$gWKT);
	return explode(" ",$gWKT);
}
?>