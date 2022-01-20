<?php

	/* se describen las columnas de la libreta de censo MUDA							*/
	
	/* todos los arreglos la misma cantidad de elementos, cada elemento corresponde 	*/
	/* a una columna de recuento (cantidades de individuos) en la libreta de censo 		*/
	/* en el mismo orden de la libreta													*/
	/* todos los arreglos tienen la misma longitud 										*/
	
	/* las categorias segun tabla "categoria" */
	$recPlanillaCate = array('PRAD','DEST','YEAR','JUCH','JUCH','JUCH','JUGR','JUGR','JUGR','SA03','SA02','SA01','S4AD','SINC');
	
	/* las descripciones de la categorias segun tabla "categoria" */
	$recPlanillaDesc = array('PRIMIPARA-ADULTA','DESTETADO','YEARLING','JUVENIL CHICO','JUVENIL CHICO','JUVENIL CHICO','JUVENIL GRANDE','JUVENIL GRANDE','JUVENIL GRANDE','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1','SUBADULTO 4-ADULTO','SIN CLASIFICAR');
	
	/* el sexo que corresponde a cada categoria segun libreta */
	$recPlanillaSexo = array('HEMBRA','NODET','NODET','MACHO','HEMBRA','NODET','MACHO','HEMBRA','NODET','MACHO','MACHO','MACHO','MACHO','NODET');
	
	/* el status (solo macho) que corresponde a cada categoria */
	$recPlanillaStatus = array('no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde');
	
	/* la etiqueta que aparece x pantalla */
	$recPlanillaLabel = array('Hembras<br>Adultas','<br>Destetados','<br>Yearlings','Juv Chicos<br>Machos','Juv Chicos<br>Hembras','Juv Chicos<br>NoDet','Juv Grandes<br>Machos','Juv Grandes<br>Hembras','Juv Grandes<br>NoDet','Sam3','Sam2','Sam1','Sam4/Ad','Sin<br>clasificar',
);
	
	/* cambio de fila para la aparicion de los elementos en la pantalla */
	/* cambia el numero y se abre una nueva fila */
	/* el arreglo definido a continuacion es equivalente a */
		$recPlanillaFilas =array(1,1,1,2,2,2,2,2,2,3,3,3,3,4);
	

