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
    $aColumns = array('memberID','username','email','active','resetComplete','permiso' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "members";

    require_once '../tb_dbconecta.php';
    require_once '../tb_validar.php';
    require_once 'tb_sesion_aca.php';
	
	/* sin permiso de administrador, fuera*/
	siErrorFuera(es_administrador());

    /* Database connection information */
    $gaSql['user']       = elUser;
    $gaSql['password']   = elPassword;
    $gaSql['db']         = elDB;
    $gaSql['server']     = elServer;

/* codigo comun a las tablas */
require_once '../tb_script_comun.php';


/*salida */
        $output = array(
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );

while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];
		if ($aCol<>'active' or ($aCol=='active' and $aRow[ $aCol ]=='Yes') ) {
            $row[] = htmlspecialchars ($aRow[ $aCol ]);
		}
		else {
			$row[] = substr($aRow[ $aCol ],0,10).'...';
		}

    }
	
	 if ($aRow[ 'active' ]=='Yes') {
      $row[] = '<a class="btn btn-success" title="editar datos del usuario" onclick=ventanaM("usuarios_editar.php?memberID='.urlencode($aRow['memberID']).'",this.title)>'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title="eliminar usuario " onclick=ventanaM("usuarios_borrar.php?memberID='.urlencode($aRow['memberID']).'&username='.urlencode($aRow['username']).'",this.title)>'.
               '<span class="glyphicon glyphicon-erase"></span></a> ';
	 }
	 else
     {			   
      $row[] = '<a class="btn btn-success" title="editar datos del usuario" onclick=ventanaM("usuarios_editar.php?memberID='.urlencode($aRow['memberID']).'",this.title)>'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title="eliminar usuario " onclick=ventanaM("usuarios_borrar.php?memberID='.urlencode($aRow['memberID']).'&username='.urlencode($aRow['username']).'",this.title)>'.
               '<span class="glyphicon glyphicon-erase"></span></a> '.
			   '<a class="btn btn-warning" title="enviar mail de activaci&oacute;n" onclick=ventanaM("usuarios_mail_activa.php?memberID='.urlencode($aRow['memberID']).'",this.title)>'.
               '<span class="glyphicon glyphicon-envelope"></span></a> '.
                 '';
	  }

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>