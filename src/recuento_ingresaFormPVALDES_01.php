<?php

	/* se describen las columnas de la libreta de censo PVALDES 2015					*/
	
	/* todos los arreglos la misma cantidad de elementos, cada elemento corresponde 	*/
	/* a una columna de recuento (cantidades de individuos) en la libreta de censo 		*/
	/* en el mismo orden de la libreta													*/
	/* todos los arreglos tienen la misma longitud 										*/
	
	/* las categorias segun tabla "categoria" */
	$recPlanillaCate = array('ADUO','SA04','SA03','SA02','SA01',
						     'PRAD','CRIA','CMUE','DEST','JUVE', 
							 'ADUO','SA04','SA03','SA02','SA01',
							 'ADUO','SA04','SA03','SA02','SA01',
							 'ADUO','SA04','SA03','SA02','SA01');
	
	/* las descripciones de la categorias segun tabla "categoria" */
	$recPlanillaDesc = array('ADULTO','SUBADULTO 4','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1',
							 'PRIMIPARA-ADULTA','CRIA','CRIA MUERTA','DESTETADO','JUVENIL',
							 'ADULTO','SUBADULTO 4','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1',
							 'ADULTO','SUBADULTO 4','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1',
							 'ADULTO','SUBADULTO 4','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1');
	
	/* el sexo que corresponde a cada categoria segun libreta */
	$recPlanillaSexo = array('MACHO','MACHO','MACHO','MACHO','MACHO',
							 'HEMBRA','NODET','NODET','NODET','NODET',
							 'MACHO','MACHO','MACHO','MACHO','MACHO',
							 'MACHO','MACHO','MACHO','MACHO','MACHO',
							 'MACHO','MACHO','MACHO','MACHO','MACHO');
	
	/* el status (solo macho) que corresponde a cada categoria */
	$recPlanillaStatus = array('ALFA','ALFA','ALFA','ALFA','ALFA',
							   'no corresponde','no corresponde','no corresponde','no corresponde','no corresponde',
							   'PERIFERICO','PERIFERICO','PERIFERICO','PERIFERICO','PERIFERICO',
							   'CERCANO','CERCANO','CERCANO','CERCANO','CERCANO',
							   'LEJANO','LEJANO','LEJANO','LEJANO','LEJANO');
	
	/* la etiqueta que aparece x pantalla */
	$recPlanillaLabel = array('Alfa<BR>Adul','Alfa<BR>Sam4','Alfa<BR>Sam3','Alfa<BR>Sam2','Alfa<BR>Sam1',
							  'Hembras<BR>Adultas','Crias<BR>Vivas','Crias<BR>Muertas','<BR>Destetados','<BR>Juveniles',
							  'Adul<BR>Periferico','Sam4<BR>Periferico','Sam3<BR>Periferico','Sam2<BR>Periferico','Sam1<BR>Periferico',
							  'Adul<BR>Cercano','Sam4<BR>Cercano','Sam3<BR>Cercano','Sam2<BR>Cercano','Sam1<BR>Cercano',
							  'Adul<BR>Lejano','Sam4<BR>Lejano','Sam3<BR>Lejano','Sam2<BR>Lejano','Sam1<BR>Lejano');
	
	/* cambio de fila para la aparicion de los elementos en la pantalla */
	/* cambia el numero y se abre una nueva fila */
	/* el arreglo definido a continuacion es equivalente a */
		$recPlanillaFilas =array(1,1,1,1,1,2,2,2,2,2,3,3,3,3,3,4,4,4,4,4,5,5,5,5,5);
	

