<?php
    /* 
     * Script:    DataTables server-side script for PHP and MySQL
     * Copyright: 2010 - Allan Jardine, 2012 - Chris Wright
     * License:   GPL v2 or BSD (3-point)
     */

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables 
     */

    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array('fecha','tipo','fechaTotal' );

    $xFiltro = " WHERE (tipo='MENSUAL') ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "censo";

    require_once 'tb_dbconecta.php';
    require_once 'tb_sesion.php';

    /* Database connection information */
    $gaSql['user']       = elUser;
    $gaSql['password']   = elPassword;
    $gaSql['db']         = elDB;
    $gaSql['server']     = elServer;

/* codigo comun a las tablas */
require_once 'tb_script_comun.php';


/*salida */
        $output = array(
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

	$fTotal_ant="";

while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];
        $row[] = htmlspecialchars ($aRow[ $aCol ]);

    }
	
	$tdetalle=""; /*para vinculo a lista completa sectores*/
	if ($fTotal_ant<>$aRow['fechaTotal']) {
		$tdetalle= '<a class="btn btn-info" title="detalle de sectores en Fecha total " href="vw_censo_sector_index.php?fechaTotal='.urlencode($aRow['fechaTotal']).'&xtrx='.urlencode($aRow['tipo']).'">'.
               '<span class="glyphicon glyphicon-list"></span></a>';
	     $fTotal_ant= $aRow['fechaTotal']; 
	}
	/*                                                                     onclick... enlugar del href y el ) antes del >'.*/
	
    /*if (edita()) {
      $row[] = '<a class="btn btn-success" title="editar datos b&aacute;sicos de censo" onclick=ventanaM("censo_editar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title="eliminar censo" onclick=ventanaM("censo_borrar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-erase"></span></a> '.
			   $tdetalle;
    }
    else  {*/
      $row[] = '<a class="btn btn-default" title="vista de datos b&aacute;sico de censo" onclick=ventanaM("censo_editar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-eye-open"></span></a> '.
			   $tdetalle;
    /*}*/

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>