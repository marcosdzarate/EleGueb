<?php 
require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	$salidax = "xlsx";
	$archi = "pla_datosVia";
	$sql ="CALL `pla-datos viajes`();";
	XlsXOnDeFlai_Template($sql,$archi,$archi."-template.xlsx");
			

?> 
