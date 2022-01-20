<?php

	/* se describen las columnas de la libreta de censo PVALDES 2015					*/
	
	/* todos los arreglos la misma cantidad de elementos, cada elemento corresponde 	*/
	/* a una columna de recuento (cantidades de individuos) en la libreta de censo 		*/
	/* en el mismo orden de la libreta													*/
	/* todos los arreglos tienen la misma longitud 										*/
	
	/* las categorias segun tabla "categoria" */
	$recPlanillaCate = array('S4AD','SA13','PRAD','CRIA','CMUE','DEST','JUVE','S4AD','S4AD','S4AD','SA13','SA13','SA13');
	
	/* las descripciones de la categorias segun tabla "categoria" */
	$recPlanillaDesc = array('SUBADULTO 4-ADULTO','SUBADULTO 1-3','PRIMIPARA-ADULTA','CRIA','CRIA MUERTA','DESTETADO','JUVENIL','SUBADULTO 4-ADULTO','SUBADULTO 4-ADULTO','SUBADULTO 4-ADULTO','SUBADULTO 1-3','SUBADULTO 1-3','SUBADULTO 1-3');
	
	/* el sexo que corresponde a cada categoria segun libreta */
	$recPlanillaSexo = array('MACHO','MACHO','HEMBRA','NODET','NODET','NODET','NODET','MACHO','MACHO','MACHO','MACHO','MACHO','MACHO');
	
	/* el status (solo macho) que corresponde a cada categoria */
	$recPlanillaStatus = array('ALFA','ALFA','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','PERIFERICO','CERCANO','LEJANO','PERIFERICO','CERCANO','LEJANO');
	
	/* la etiqueta que aparece x pantalla */
	$recPlanillaLabel = array('Alfa<BR>S4/Ad','Alfa<BR>otros Sams','Hembras<BR>Adultas','Crias Vivas','Crias Muertas','Destetados','Juveniles','S4/Ad<BR>Periferico','S4/Ad<BR>Cercano','S4/Ad<BR>Lejano','Otros Sams<BR>Periferico','Otros Sams<BR>Cercano','Otros Sams<BR>Lejano');;
	
	/* cambio de fila para la aparicion de los elementos en la pantalla */
	/* cambia el numero y se abre una nueva fila */
	/* el arreglo definido a continuacion es equivalente a */
	/* 	$recPlanillaFilas =array(1,1,1,0,0,0,0,1,1,1,0,0,0); */
		$recPlanillaFilas =array(1,1,1,2,2,2,2,3,3,3,4,4,4);
	

