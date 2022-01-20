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
    $aColumns = array('viajeID','instrumentoNRO','fecha_recuperacion' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "instrumentos_colocados";

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
        if (($aCol == 'instrumentoNRO') and ($sTable=='instrumentos_colocados')){
           $pQuery = 'SELECT CONCAT(TRIM(tipo)," ",IF(ISNULL(identificacion),"",TRIM(identificacion))," ",if(ISNULL(modelo),"",TRIM(modelo))," ",IF(ISNULL(fabricante),"",TRIM(fabricante))) FROM instrumentos WHERE instrumentoNRO='.$aRow[ $aCol ];
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
      $row[] = '<a class="btn btn-success" title=" " href="instrumentos_colocados_editar.php?viajeID='.urlencode($aRow['viajeID']).'&instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'">'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title=" " href="instrumentos_colocados_borrar.php?viajeID='.urlencode($aRow['viajeID']).'&instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'&instrumentoRef='.urlencode($fila[0]).'">'.
               '<span class="glyphicon glyphicon-erase"></span></a> '.
                 '';
               '<span class="glyphicon glyphicon-list"></span></a>';
    }
    else  {
      $row[] = '<a class="btn btn-default" title=" " href="instrumentos_colocados_editar.php?viajeID='.urlencode($aRow['viajeID']).'&instrumentoNRO='.urlencode($aRow['instrumentoNRO']).'">'.
               '<span class="glyphicon glyphicon-eye-open"></span></a> '.
                 '';
               '<span class="glyphicon glyphicon-list"></span></a>';
    }

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>