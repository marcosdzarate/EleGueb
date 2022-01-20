<?php
// declaracion de constantes que se usan en validacion y en los forms que se presentan al usuario
// 
/* tamaño maximo de subida archivos */
	define ("CONST_tamanioMaxArchivo",24000000);
	define ("CONST_tamanioMaxArchivoM","24M");
	define ("CONST_tamanioMaxArchivo_men","Archivo muy grande, max ".CONST_tamanioMaxArchivoM);

// en general, temporada minima
	define ("CONST_temporadaMin_min","1984");
	define ("CONST_temporadaMin_men","&gt;= a ".CONST_temporadaMin_min);
	/* la maxima se determina con la function temporadaAnio_actual, dependiendo de la fecha de hoy */
	define ("CONST_selElemente_men","Seleccionar un elemento de la listas");

// RANGOS
// grupo
	define ("CONST_distancia_min","1");
	define ("CONST_distancia_max","999");
	define ("CONST_distancia_men","De ".CONST_distancia_min." a ".CONST_distancia_max." metros ");

	define ("CONST_ordenRec_min","0");
	define ("CONST_ordenRec_max","999");
	define ("CONST_ordenRec_men","De ".CONST_ordenRec_min." a ".CONST_ordenRec_max."");

	
// categoria
	define ("CONST_ordenCate_min","0.0");
	define ("CONST_ordenCate_max","89.0");
	define ("CONST_ordenCate_men","De ".CONST_ordenCate_min." a ".CONST_ordenCate_max." ");

	define ("CONST_edadRelativa_min","-1.0");
	define ("CONST_edadRelativa_max","15.0");
	define ("CONST_edadRelativa_men","De ".CONST_edadRelativa_min." a ".CONST_edadRelativa_max." ");

	
// anestesia
	define ("CONST_droga_ml_min","0.1");
	define ("CONST_droga_ml_max","99.9");
	define ("CONST_droga_ml_men","De ".CONST_droga_ml_min." a ".CONST_droga_ml_max." ml=cm3");


// hembra,criadestetado
	define ("CONST_edadPupDeste_min","0");
	define ("CONST_edadPupDeste_max","35");
	define ("CONST_edadPupDeste_men","De ".CONST_edadPupDeste_min." a ".CONST_edadPupDeste_max." d&iacute;as");

	
// muda
	define ("CONST_porcentaje_min","0");
	define ("CONST_porcentaje_max","100");
	define ("CONST_porcentaje_men","De ".CONST_porcentaje_min." a ".CONST_porcentaje_max." ");

	
// macho
	define ("CONST_haremHembras_min","1");
	define ("CONST_haremHembras_max","400");
	define ("CONST_haremHembras_men","De ".CONST_haremHembras_min." a ".CONST_haremHembras_max." ");

	define ("CONST_haremPups_min","1");
	define ("CONST_haremPups_max","400");
	define ("CONST_haremPups_men","De ".CONST_haremPups_min." a ".CONST_haremPups_max." ");

	
// copula
	define ("CONST_duracion_min","1");
	define ("CONST_duracion_max","45");
	define ("CONST_duracion_men","De ".CONST_duracion_min." a ".CONST_duracion_max." minutos");


// configuracion viajes
	define ("CONST_intervalo_min","1");
	define ("CONST_intervalo_max","43200");
	define ("CONST_intervalo_men","De ".CONST_intervalo_min." a ".CONST_intervalo_max." segundos");
	
	
// medidas
	define ("CONST_largoStd_min","0.60");
	define ("CONST_largoStd_max","6.00");
	define ("CONST_largoStd_men","De ".CONST_largoStd_min." a ".CONST_largoStd_max." metros");
	
	define ("CONST_largoCurva_min","0.60");
	define ("CONST_largoCurva_max","8.00");
	define ("CONST_largoCurva_men","De ".CONST_largoCurva_min." a ".CONST_largoCurva_max." metros");
	
	define ("CONST_circunferencia_min","0.80");
	define ("CONST_circunferencia_max","6.00");
	define ("CONST_circunferencia_men","De ".CONST_circunferencia_min." a ".CONST_circunferencia_max." metros");
	
    define ("CONST_peso_min","70.00");
	define ("CONST_peso_max","4000.00");
	define ("CONST_peso_men","De ".CONST_peso_min." a ".CONST_peso_max." kilos");

	define ("CONST_nostril_min","3.0");
	define ("CONST_nostril_max","40.0");
	define ("CONST_nostril_men","De ".CONST_nostril_min." a ".CONST_nostril_max." cent&iacute;metros");
	
	define ("CONST_largo_aleta_ext_min","20.0");
	define ("CONST_largo_aleta_ext_max","70.0");
	define ("CONST_largo_aleta_ext_men","De ".CONST_largo_aleta_ext_min." a ".CONST_largo_aleta_ext_max." cent&iacute;metros");

	define ("CONST_largo_aleta_int_min","20.0");
	define ("CONST_largo_aleta_int_max","70.0");
	define ("CONST_largo_aleta_int_men","De ".CONST_largo_aleta_int_min." a ".CONST_largo_aleta_int_max." cent&iacute;metros");

	define ("CONST_edadMedida_min","0");
	define ("CONST_edadMedida_max","180");
	define ("CONST_edadMedida_men","De ".CONST_edadMedida_min." a ".CONST_edadMedida_max." d&iacute;as");

// instrumentos	
	define ("CONST_instrumentoNRO_min","1");
	define ("CONST_instrumentoNRO_max","9999");
	define ("CONST_instrumentoNRO_men","De ".CONST_instrumentoNRO_min." a ".CONST_instrumentoNRO_max." ");

	
// playa
	define ("CONST_norteSur_min","0.00");
	define ("CONST_norteSur_max","999.00");
	define ("CONST_norteSur_men","De ".CONST_norteSur_min." a ".CONST_norteSur_max." ");

	
// recuento	
	define ("CONST_cantidad_min","1");
	define ("CONST_cantidad_max","700");
	define ("CONST_cantidad_men","De ".CONST_cantidad_min." a ".CONST_cantidad_max." ");


// vecindario
	define ("CONST_IDesquema_min","1");
	define ("CONST_IDesquema_max","999");
	define ("CONST_IDesquema_men","De ".CONST_IDesquema_min." a ".CONST_IDesquema_max." ");

// censo_totales.php: campos de entrada para calcular totales)
	define ("CONST_tAnio_min","1960");
	define ("CONST_tAnio_max",date('Y')+1);
	define ("CONST_tAnio_men","De ".CONST_tAnio_min." a ".CONST_tAnio_max." ");

// publicaciones
	define ("CONST_anioPub_min","1800");
	define ("CONST_anioPub_max",date('Y'));
	define ("CONST_anioPub_men","De ".CONST_anioPub_min." a ".CONST_anioPub_max." ");
	
// scan3d
// grupo
	define ("CONST_distanciaVideo_min","1.0");
	define ("CONST_distanciaVideo_max","10.0");
	define ("CONST_distanciaVideo_men","De ".CONST_distanciaVideo_min." a ".CONST_distanciaVideo_max." metros ");

	define ("CONST_modeloVolumen_min","0.05");
	define ("CONST_modeloVolumen_max","30.00");
	define ("CONST_modeloVolumen_men","De ".CONST_modeloVolumen_min." a ".CONST_modeloVolumen_max." m3");
    
// PATRONES
	define ("PATRON_claveU","^[A-Z]{4}$");
	define ("PATRON_claveU_men","Clave &uacute;nica: 4 letras may&uacute;sculas");
	
	define ("PATRON_fecha","^[0-9]{4}-[0-9]{2}-[0-9]{2}$");
	define ("PATRON_fecha_men","Formato debe ser AAAA-MM-DD");
	
	
	define ("PATRON_hora","^(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}$");
	define ("PATRON_hora_men","Formato debe ser HH:mm:ss, HH:00-23");

	define ("PATRON_partida","^[0-9A-Z\/\- .]{2,45}$");
	define ("PATRON_partida_men","2 a 45 caracteres: 0-9 A-Z / - . espacios");
	
	define ("PATRON_IDcategoria","^[A-Z]{1}[A-Za-z0-9]{1,3}$");
	define ("PATRON_IDcategoria_men","Letra may&uacute;scula seguida de hasta 3 letras y/o d&iacute;gitos");
	
	define ("PATRON_cateDesc","^[0-9A-ZÑÁÉÍÓÚÜ\-\s.]{2,30}$");
	define ("PATRON_cateDesc_men","2 a 30 caracteres: letras n&uacute;meros - . espacios");
	
	define ("PATRON_libreta","^[0|\d][\d][A-BD-Z]?$");	/* para censos actuales */
	define ("PATRON_libreta_men","Formato dd o ddL, d=d&iacute;gito L=letra que no sea C");

	define ("PATRON_libretaViejoMensual","^[M][\d][\d|A-Z]?$");	/* para censos viejos mensuales */
	define ("PATRON_libretaViejoMensual_men","Formato M seguida de dd o dL, d=d&iacute;gito L=letra");
	
	define ("PATRON_libretaVirtual","^[0|\d][\d][C]$");	/* libreta de sector copiado, termina en C!!! */
	define ("PATRON_libretaVirtual_men","Formato ddC, 2 d&iacute;gitos seguidos de C");

	define ("PATRON_IDcolaborador","^[A-Z]{2,3}$");
	define ("PATRON_IDcolaborador_men","2 a 3 letras");

	define ("PATRON_apeYnom","^[A-ZÑÁÉÍÓÚÄËÏÜÖÀÂÇÈÊÎÔÛÙ'\-\s.]{2,45}$");
	define ("PATRON_apeYnom_men","2 a 45 caracteres: letras [con ´ ¨ ^] ' - . espacio");

	define ("PATRON_email","^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,3}$");
	define ("PATRON_email_men","Formato de email inv&aacute;lido");
		
	define ("PATRON_marcaOtag","^[0-9A-ZÑ, \-\/]{1,30}$");
	define ("PATRON_marcaOtag_men","1 a 30 caracteres conformando un tag o una marca");
	
	define ("PATRON_prefiFotos","^[0-9A-Z\s_\-]{1,25}$");
	define ("PATRON_prefiFotos_men","De 1 a 25 letras n&uacute;meros - _ espacio");

//	define ("PATRON_archiFotos","^[A-Za-z0-9\-_&\\\s.]{1,146}.[A-Za-z0-9]{3,4}$");
	define ("PATRON_archiFotos","^[A-Za-z0-9\-_& .]{1,146}.[A-Za-z0-9]{3,4}$");
	define ("PATRON_archiFotos_men","Solo letras, numeros, espacios & . - _");	
	
	define ("PATRON_variosInstru","^[0-9A-Za-z'\/\-\s().]{2,45}$");
	define ("PATRON_variosInstru_men","2 a 45 letras n&uacute;meros espacio ' / - ()");

	define ("PATRON_marca","^[0-9A-ZÑ\-\/]{1,30}$");
	define ("PATRON_marca_men","De 1 a 30 caracteres: 0-9 A-Z &Ntilde; / -");

	define ("PATRON_codADN","^[0-9A-ZÑ\-\/]{1,30}$");
	define ("PATRON_codADN_men","De 1 a 30 caracteres: 0-9 A-Z &Ntilde; / -");

	define ("PATRON_IDplaya","^[#A-Z]{1}[*A-Z]{1}[A-Z0-9]{1,3}$");
	define ("PATRON_IDplaya_men","C&oacute;digo incorrecto");

	define ("PATRON_nombrePlaya","^[0-9A-ZÑÁÉÍÓÚÜ'*>\-\s().]{2,60}$");
	define ("PATRON_nombrePlaya_men","2 a 60 caracteres: letras n&uacute;meros * &gt; ( ) - . espacio");
	
	define ("PATRON_geoPOINT","^POINT\(-?\d{1,3}(\.(\d+)?)?\s-?\d{1,3}(\.(\d+)?)?\)");
	define ("PATRON_geoPOINT_men","Lat-long WKT incorrecto o supera los 40 caracteres");

	define ("PATRON_geoPOLYGON_POINT","(^POLYGON\(\((-?\d{1,3}(\.(\d+)?)?\s-?\d{1,3}(\.(\d+)?)?,){2,49}(-?\d{1,3}(\.(\d+)?)?\s-?\d{1,3}(\.(\d+)?)?){1}\)\)|^POINT\(-?\d{1,3}(\.(\d+)?)?\s-?\d{1,3}(\.(\d+)?)?\))");
	define ("PATRON_geoPOLYGON_POINT_men","Formato de geometry WKT POINT o POLYGON incorrecto");

	define ("PATRON_tag","^[A-Z]{1}[A-Z0-9]{1}(\s\s\s|\d\s\s|\d\d\s|\d\d\d)(I|D)$");
	define ("PATRON_tag_men","Tag inv&aacute;lido");
	
	define ("PATRON_identiPub","^[0-9A-ZÑ\/\-\s.]{1,30}$");
	define ("PATRON_identiPub_men","De 1 a 30 letras n&uacute;meros / - . espacio");
	
//	define ("PATRON_archiPubli","^[A-Za-z0-9\-_&.\\\s]{1,146}.[A-Za-z]{3,4}$");
	define ("PATRON_archiPubli","^[A-Za-z0-9\-_&. ]{1,146}.[A-Za-z]{3,4}$");
	define ("PATRON_archiPubli_men","Solo letras, numeros, espacios & . - _");	
	
	define ("PATRON_tituPubli","^[A-ZÑÁÉÍÓÚÄËÏÜÖÀÂÇÈÊÎÔÛÙ'\/\-\s\.,:;%&@?¿º=)\(0-9]{2,700}$");
	define ("PATRON_tituPubli_men","De 2 a 700 caracteres: letras [con ´ ¨ ^] n&uacute;meros '/-.,:;%&@?¿º=() espacio");	

	define ("PATRON_autoresPubli","^[A-ZÑÁÉÍÓÚÄËÏÜÖÀÂÇÈÊÎÔÛÙ'\-\s.,;]{2,1500}$");
	define ("PATRON_autoresPubli_men","De 2 a 1500 caracteres: letras [con ´ ¨ ^] , - ' . ; espacio");	

	define ("PATRON_IDvecin","^[0-9A-ZÑÁÉÍÓÚÜ'>\-\s().]{2,100}$");
	define ("PATRON_IDvecin_men","De 2 a 100 caracteres: letras [´ Ü] &gt; - ( ) . espacio");	

	define ("PATRON_IDvecinFiltro","^[0-9A-ZÑÁÉÍÓÚÜ'\">\-\s().,]{2,3000}$");
	define ("PATRON_IDvecinFiltro_men","Filtro de vecindarios tiene caracteres inv&aacute;lidos o es muy largo");	

	define ("PATRON_protoArgos","^[0-9A-Za-z\-\/.\s]{2,200}");
	define ("PATRON_protoArgos_men","2 a 200 Letras n&uacute;meros espacios . - /");	

	define ("PATRON_archiArgos","^[A-Za-z0-9\-_]{1,30}.(TXT|txt)$");
	define ("PATRON_archiArgos_men","Solo letras, numeros, - _ y extesi&oacute;n TXT");		

	define ("PATRON_archiScan3D","^[0-9A-Z\-\/]{1,60}$");
	define ("PATRON_archiScan3D_men","De 1 a 60 caracteres: 0-9 A-Z / -");


	
// para usuarios
	define ("PATRON_usuario","^[A-Za-z0-9._-]{6,30}$");
	define ("PATRON_usuario_men","6 a 30 caracteres A-Za-z0-9._-");
	
//	define ("PATRON_","");
//	define ("PATRON_men","");

// mapa de localizaciones: punto central de PV. Las localizaciones a menos de CENTRO_dis km
// se consideran "costeras" (es para intentar determinar reincio de viaje)
    define ("CENTRO_lat",-42.481424);
    define ("CENTRO_lon",-63.922351);
	define ("CENTRO_dis",90);
	
?>