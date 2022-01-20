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
    $aColumns = array('IDcategoria','cateDesc','grupoEdad','reproductiva','ordenCate','edadRelativa','sexoc' );

    $xFiltro = " WHERE ( true ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "categoria";

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
while ( $aRow = mysqli_fetch_array( $rResult ) )
{
	$conBotones=true;
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];

        $row[] = htmlspecialchars ($aRow[ $aCol ]);
		
		if($aCol=="ordenCate" and intval($aRow[ $aCol ])>89) {
			$conBotones=false;
		}

    }
	if ($conBotones) {
		if (edita()) {
		  $row[] = '<a class="btn btn-success" title="editar la categor&iacute;a" onclick=ventanaM("categoria_editar.php?IDcategoria='.urlencode($aRow['IDcategoria']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-edit"></span></a> '.
				   '<a class="btn btn-danger" title="eliminar la categor&iacute;a" onclick=ventanaM("categoria_borrar.php?IDcategoria='.urlencode($aRow['IDcategoria']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-erase"></span></a> '.
					 '';
				   '<span class="glyphicon glyphicon-list"></span></a>';
		}
		else  {	
		  $row[] = '<a class="btn btn-default" title="vista de los datos de la categoria" onclick=ventanaM("categoria_editar.php?IDcategoria='.urlencode($aRow['IDcategoria']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-eye-open"></span></a> '.
					 '';
				   '<span class="glyphicon glyphicon-list"></span></a>';
		}
	}
	else{
		$row[] = '';
	}
	
    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>