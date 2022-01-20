<?php
/* backup de la base de datos elefasur/gemma

/* USA CON ALGUNOS CAMBIOS:
	BackupMySQL.php — 2017-V-7 — Francisco Cascales
 	— Backup a MySQL database only with PHP (without mysqldump)
	— https://github.com/fcocascales/phpbackupmysql
	— Version 1.13b
*/

require_once 'backup_class_BackupMySQL.php';

require_once 'tb_sesion_bkp.php';
require_once '../tb_dbconecta.php';
require_once '../tb_validar.php';

	/* sin permiso de administrador, fuera*/
	siErrorFuera(es_administrador(),"p");

	$queShow="1";
	$queTab="";
	
	
	/* los .sql generedos quedan en tempoBKP */
    array_map( "unlink", glob( "tempoBKP/*.sql" ) );
	/* los .sql generedos quedan en tempoZIP */
    array_map( "unlink", glob( "tempoZIP/*.zip" ) );

			$connection = [
				'host'=> elServer,
				'database'=> elDB,
				'user'=> elUser,
				'password'=> elPassword,
				'port'=> elPort,
			];

			/* */
			
			
			$tables = [];
			$queTabTxt="";

			$show = ['TABLES'];  //INCLUYE ESTRUCTURA INDICES TRIGGERS
			$queShowTxt = $show[0];
			$salida=elDB."0_".$queTabTxt.$queShowTxt;
			$backup = new BackupMySQL($connection, $tables, $show, $salida);
			$backup->setFolder ('tempoBKP');
			$backup->run();
			$lasTablas=$backup->dameTablas();    /*arreglo contodas las TABLE de la DB*/
		$backup=NULL;
			//echo "CREADO $salida (estructura de tablas, indices y triggers)"."<br>";

			
			
			
			/*	DATA de todas las tablas   */
			$noVan = array("geomEsp","geom"); /* no van a la salida de datos los tipo GEOMETRY*/
			$show = ['DATA'];				
			$salida="";
			$nt=count($lasTablas)-1;
			//echo "CREADO el archivo de datos correspondiente a cada tabla."."<br>";
			for  ($i = 0; $i <= $nt; $i++) {  
				$ltab = $lasTablas[$i];
				$salida=elDB."1_".$ltab."_DATA";
				$tables[0]=$ltab;
				$backup = new BackupMySQL($connection, $tables, $show, $salida);
				$backup->set_keyQueNo($noVan);
				$backup->setFolder ('tempoBKP');
				$backup->run();
				$backup=NULL;
				// echo "&emsp;&emsp;&emsp;&emsp;$salida"."<br>";
			}
			/*		*/

			
			$tables = [];
			$show = ['FOREIGN'];				
			$queShowTxt = $show[0];
			$salida=elDB."2_".$queTabTxt.$queShowTxt;
			$backup = new BackupMySQL($connection, $tables, $show, $salida);
			$backup->setFolder ('tempoBKP');			
			$backup->run();			
		$backup=NULL;
			//echo "CREADO $salida (definiciones de CONSTRAINTs)"."<br>";

			
			
/* NO LO HAGO X ACA POR QUE EL ORDEN DE CREACION IMPORTA
	DADO QUE ALGUNAS VISTAS USAN OTRAS PREVIAMENTE CREADAS 
	
	EN phpMyAdmin USAR PROCEDURE
	vw_CREAR VISTAS
	
	NO OLVIDAR DE ACTUALIZARLA, AGREGANDO AL FINAL NUEVAS VISTAS!!!!
	
	
			$tables = [];
			$show = ['VIEWS'];				
			$queShowTxt = $show[0];
			$salida=elDB."3_".$queTabTxt.$queShowTxt;
			$backup = new BackupMySQL($connection, $tables, $show, $salida);
			$backup->setFolder ('tempoBKP');
			$backup->run();
		$backup=NULL;
			//echo "CREADO $salida (definiciones de vistas)"."<br>";
			
*/

			
			$tables = [];
			$show = ['PROGRAMS'];				
			$queShowTxt = $show[0];
			$salida=elDB."4_".$queTabTxt.$queShowTxt;
			$backup = new BackupMySQL($connection, $tables, $show, $salida);
			$backup->setFolder ('tempoBKP');
			$backup->run();
			//echo "CREADO $salida (procedures y functions)"." "."<br>";

		$backup=NULL;			
			
			

			$show = ['TRIGGERS'];  //INCLUYE TRIGGERS
			$queShowTxt = $show[0];
			$salida=elDB."5_".$queTabTxt.$queShowTxt;
			$backup = new BackupMySQL($connection, $tables, $show, $salida);
			$backup->setFolder ('tempoBKP');
			$backup->run();
			$lasTablas=$backup->dameTablas();    /*arreglo contodas las TABLE de la DB*/
		$backup=NULL;
			//echo "CREADO $salida (triggers)"."<br>";
			
			
			
			
			
		/* zip con todos los archivos sql en tempoZIP*/
			$base="BACKUP_".siglaGrupe."_".date('Y-m-d');
			$bkp="BACKUP_".elDB;  
			$zipSale = "tempoZIP/".$base.".zip";
			$dirSale=$base."/".$bkp."/";
			$zip = new ZipArchive();
			$ret = $zip->open($zipSale, ZipArchive::OVERWRITE | ZipArchive::CREATE);

			if ($ret !== TRUE) {
				echo 'Error código '.$ret;
			} else {
				$opt = array('add_path' => $dirSale, 'remove_all_path' => TRUE);
				$zip->addGlob('tempoBKP/*.sql', GLOB_BRACE, $opt );
				
				$opt = array('add_path' => $base."/".'localizacionesTMP/', 'remove_all_path' => TRUE);
				$zip->addGlob('../localizacionesTMP/*.*', GLOB_BRACE, $opt );
				
				$opt = array('add_path' => $base."/".'publicaciones/', 'remove_all_path' => TRUE);
				$zip->addGlob('../publicaciones/*.*', GLOB_BRACE, $opt );
				
				$opt = array('add_path' => $base."/".'fotosYvideos/', 'remove_all_path' => TRUE);
				$zip->addGlob('../fotosYvideos/*.*', GLOB_BRACE, $opt );
				
				$zip->close();
			};
			
		/* download */
		

			header("Content-type: application/zip"); 
			header("Content-Disposition: attachment; filename=$base.zip");
			header("Content-length: " . filesize($zipSale));
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
			readfile("$zipSale");  /* del manual php: Nota: readfile() no presentará problemas de memoria,...LEER http://php.net/manual/es/function.readfile.php/ */

						
    array_map( "unlink", glob( "tempoBKP/*.sql" ) ); 


?>