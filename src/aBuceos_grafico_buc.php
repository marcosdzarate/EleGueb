<?php 
/* seleccion de datos paara grafico de buceo*/
/* para claveU ($cu) y nroViaje ($nv)		*/
/* variable $var (arreglo qvar) 	*/

    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_validar.php';

	$cu = limpia($_GET['cu']);
	$nv = limpia($_GET['nv']); 
	$var= limpia($_GET['var']);
	
	$valid=true;
	
	$eError="";
	$eError = validar_claveU ($cu,$valid,true);
	$eError = validar_entero ($nv,0,100,$valid,true);
	
	siErrorFuera($valid);


	
	$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
	if (mysqli_connect_errno())
	{
		echo "No se puede conectar a MySQL: " . mysqli_connect_error();
		exit;
	}

	if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
	{
		echo("Error lc_messages castellano. " );	/* no doy exit! que siga.... */
	}         
          
	/* cargo UTF8 */
	if (!mysqli_set_charset($con, "utf8")) 
	{
		echo("Error cargando el conjunto de caracteres utf8" );
		echo mysqli_error($con); /* no doy exit! que siga.... */
	}

	
	/* como se seleccionan los grupos de variables de la tabla de buceos */
	/* signos: doy vuelta la profundidad */
	$qvar=array("-profundidadMax as y,-profundidadMed as y2","temperaturaFondo as y,temperaturaSuperficie as y2","duracion as y,intervaloEnSuperficie as y2");
	/* para la salida gráfica */
	$qvarOut=array("profMax,profMed","tempeFondo,tempeSuper","duracion,interSuper");
	$sele=$qvar[$var];
	$laVarOut=$qvarOut[$var];
	
	$elSQL ="SELECT julianoIni as x,$sele FROM a0001_buceos WHERE claveU='$cu' AND nroViaje= $nv ORDER BY julianoIni";
	
	
	
	if ($result = mysqli_query($con, $elSQL))
	{
		echo "diaJul,$laVarOut"."\n";
		foreach ($result as $row) {
		   echo $row['x'].",".$row['y'].",".$row['y2']."\n";
		}
	}
	mysqli_free_result($result);
    mysqli_close($con);
	
    

 
?>