<?php
/* con los $_GET pasados arma */
/* $param	un string de parametros para la url de llamada a los scripts para */
/* 			Editar,Ver,Borrar y Detalle, de manera de poder volver a la mismo filtro */
/* $param0	idem $param sin el ultimo */
/* $condi 	condición que puede agregarase ini_datatable ('#tablax' , 'tabla_tb.php?condi=<...)*/
/*			(la estoy eliminando cuando no se usa en una tabla particular) */
 
$cant = count($_GET);
$condi='';
$xtrx = ""; /*parámetros extras que no son clave de acceso a tabla*/
if ( $cant <> 0){
	$condi='';
	//armo condicion inicial tablax para filtro
	foreach ($_GET as $cla=>$val){
	    if ($cla<>"xtrx") {
		    $condi .= $cla.'="'.$val.'" AND ';
	    }
		else{
		    $xtrx = $val;
		}
	}
	$condi = substr($condi,0,strrpos($condi," AND "));
}

if (empty($condi)) {
	$condi='true';
}




$param='';   /*todos los parametros (excluido xtrx)*/
$param0='';  /*todos los parametros sin el ultimo (excluido xtrx)*/
if ( $cant <> 0){
	// lista de parametros para url agregar 
	foreach ($_GET as $cla=>$val){
	    if ($cla<>"xtrx") {
           $param .= $cla.'='.$val.'&';
		}
	}
	$param = "?".substr($param,0,strrpos($param,"&"));
	/*elimino el ultimo de los parametros*/
    $param0=$param;
	if (empty($par_elimina)) {
		$par_elimina=1;
	}
	for ($i = 1; $i <= $par_elimina; $i++) {
	    $param0= substr($param0,0,strrpos($param0,"&"));
	}
}


/*
echo "<BR>condicion ",$condi;
echo "<BR>param ",$param;
echo "<BR>par_elimina ",$par_elimina;
echo "<BR>param0 ",$param0;
echo "<BR>xtrx ",$xtrx;
*/

?>