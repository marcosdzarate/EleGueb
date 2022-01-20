<?php

	/* se describen las columnas de la libreta de censo MUDA							*/
	
	/* todos los arreglos la misma cantidad de elementos, cada elemento corresponde 	*/
	/* a una columna de recuento (cantidades de individuos) en la libreta de censo 		*/
	/* en el mismo orden de la libreta													*/
	/* todos los arreglos tienen la misma longitud 										*/
	
	/* las categorias segun tabla "categoria" */
	$recPlanillaCate = array('PRAD','DEST','S4AD','SA03','SA02','SA01','JUCH','JUCH','JUCH','JUGR','JUGR','JUGR','YEAR','SINC');
	
	/* las descripciones de la categorias segun tabla "categoria" */
	$recPlanillaDesc = array('PRIMIPARA-ADULTA','DESTETADO','SUBADULTO 4-ADULTO','SUBADULTO 3','SUBADULTO 2','SUBADULTO 1','JUVENIL CHICO','JUVENIL CHICO','JUVENIL CHICO','JUVENIL GRANDE','JUVENIL GRANDE','JUVENIL GRANDE','YEARLING','SIN CLASIFICAR');
	
	/* el sexo que corresponde a cada categoria segun libreta */
	$recPlanillaSexo = array('HEMBRA','NODET','MACHO','MACHO','MACHO','MACHO','MACHO','HEMBRA','NODET','MACHO','HEMBRA','NODET','NODET','NODET');
	
	/* el status (solo macho) que corresponde a cada categoria */
	$recPlanillaStatus = array('no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde','no corresponde');
	
	/* la etiqueta que aparece x pantalla */
	$recPlanillaLabel = array('Hembras<br>Adultas','<br>Destetados','Sam4/Ad','Sam3','Sam2','Sam1','Juv Chicos<br>Machos','Juv Chicos<br>Hembras','Juv Chicos<br>NoDet','Juv Grandes<br>Machos','Juv Grandes<br>Hembras','Juv Grandes<br>NoDet','<br>Yearlings','Sin<br>clasificar'
);
	
	/* cambio de fila para la aparicion de los elementos en la pantalla */
	/* cambia el numero y se abre una nueva fila */
	/* el arreglo definido a continuacion es equivalente a */
		$recPlanillaFilas =array(1,1,2,2,2,2,3,3,3,3,3,3,4,4);
	
