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
    $aColumns = array('instrumentoNRO','tipo','identificacion','serial_num','modelo','fabricante','nuestro','disponible' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "instrumentos";

    require_once 'tb_dbconecta.php';
    require_once 'tb_sesion.php';

        require_once 'tb_validar.php';
        siErrorFuera (val_condi($aColumns,$_GET['condi'],12));  

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
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];

        $pQuery = "";
        if (($aCol == 'playa') or ($aCol == 'encontradoPlaya')) {
           $pQuery = 'SELECT nombre FROM playa WHERE IDplaya="'.$aRow[ $aCol ].'"';
            }
        if ($pQuery <> "") {
           $pResu = mysqli_query( $gaSql['link'],$pQuery);
           $fila = mysqli_fetch_row($pResu);
           if (!is_null($fila)){
                 $row[] = htmlspecialchars ($fila[0]);
              }
              else{
                 $row[] = htmlspecialchars ($aRow[ $aCol ]);
              }
           }
         else {
           $row[] = htmlspecialchars ($aRow[ $aCol ]);
          }

    }
    if (edita()) {
      $row[] = '<a class="btn btn-success" title="datos de instrumentos" onclick=ventanaM("instrumentos_editar.php?instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'",this.title)>'.
	  '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title="eliminar " onclick=ventanaM("instrumentos_borrar.php?instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'",this.title)>'.
               '<span class="glyphicon glyphicon-erase"></span></a> '.
                 '';
               '<span class="glyphicon glyphicon-list"></span></a>';
    }
    else  {
      $row[] = '<a class="btn btn-default" title="vista de los datos del instrumento" onclick=ventanaM("instrumentos_editar.php?instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'",this.title)>'.
               '<span class="glyphicon glyphicon-eye-open"></span></a> '.
                 '';
               '<span class="glyphicon glyphicon-list"></span></a>';
    }

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>