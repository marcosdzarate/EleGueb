<?php
/* se incluye en tablas_index u tablas_tb */
    switch ($laTabla) {
		case "tag":	
			$tColu = array('tag','borradoTempo','encontradoTempo','encontradoPlaya','comentario' );
			break;
		case "marca":
			$tColu = array('marca','comentario');
			break;
		case "muestras":
			$tColu = array('codigoADN','sangre','bigotes','pelos','fotogrametria','comentario');
			break;
		case "medidas":
			$tColu = array('largoStd','largoCurva','circunferencia','peso','nostril','largo_aleta_ext','largo_aleta_int','edadMedida','comentario');
			break;
		case "muda":
			$tColu = array('porcentaje','estado','comentario');
			break;
		case "copula":
			$tColu = array('hora','duracion','conCual','conCualCu','comentario');
			break;
		case "macho":
			$tColu = array('status','estadoFisico','entornoAlAlfa','alAlfaCu','haremHembras','haremPups','comentario');
			break;
		case "hembra";
			$tColu = array('estado','fechaParto','estimada','edadPup','comentario');
			break;
		case "criadestetado":
			$tColu = array('fechaDestete','estimada','edadDestete','comentario');
			break;
		case "anestesia";
			$tColu = array('hora','tipo','aplicacion','droga','droga_ml','partida','anotacion');
			break;
		case "viaje";
			$tColu = array('etapa','comentario');
			break;
		case "scan3d";
			$tColu = array('hora','clima','tipoPlaya','escaneo3D','IDarchivo','video','distancia','comentario','modelo','modeloVolumen','modeloPeso','modeloNota');
			break;
	}			
?>