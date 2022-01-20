<?php
/* lista no editable de los sectores (libretas) para una fecha dada*/
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
/*    $aColumns = array('fecha','libreta','horaInicio','horaFin','zonaRecorrida','direccionRecorrida','marea' );*/
    $aColumns = array('fechaTotal','fecha','libreta','zonaRecorrida' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "vw_censo_sector";

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

$esCopia = false;
while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    $esCopia = false;
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];
        if ($aCol=="zonaRecorrida" AND  strpos(trim($aRow[ $aCol ]),"sector faltante: es copia//")!==false) {
            $esCopia=true;
        }
        $row[] = htmlspecialchars ($aRow[ $aCol ]);

    }
    
    if ($esCopia==false) {
        if (edita()  and $_SESSION['tipocen']<>'MENSUAL'  and editaCenso($aRow['fecha']) ) {
          $row[] = '<a class="btn btn-success" title="editar datos del sector" onclick=ventanaM("sector_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-edit"></span></a> '.
                   '<a class="btn btn-danger" title="eliminar datos del sector" onclick=ventanaM("sector_borrar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-erase"></span></a> '.
                   '<a class="btn btn-info" title="detalle de los grupos" href="grupo_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'">'.
                   '<span class="glyphicon glyphicon-list"></span></a> '.
                   '<a class="btn btn-warning" title="censistas del sector" onclick=ventanaM("censista_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-user"></span></a>';
        }
        else  {
          $row[] = '<a class="btn btn-default" title="vista de datos del sector" onclick=ventanaM("sector_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-eye-open"></span></a> '.
                   '<a class="btn btn-info" title="detalle" href="grupo_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'">'.
                   '<span class="glyphicon glyphicon-list"></span></a> '.
                   '<a class="btn btn-warning" title="censistas" onclick=ventanaM("censista_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-user"></span></a>';
        }
    }
    else
       { /*ES SECTOR COPIADO DE OTRO CENSO*/
        if (edita() and $_SESSION['tipocen']<>'MENSUAL'  and editaCenso($aRow['fecha'])) {
          $row[] = '<a class="btn btn-success" title="editar los datos para completar sector faltante" onclick=ventanaM("sector_copiado_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-edit"></span></a> '.
                   '<a class="btn btn-danger" title="eliminar datos de copia" onclick=ventanaM("sector_copiado_borrar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-erase"></span></a> '.
                   '<a class="btn btn-warning" title="detalle de los grupos" href="sector_copiado_grupo_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'">'.
                   '<span class="glyphicon glyphicon-list"></span></a> ';
        }
        else  {
          $row[] = '<a class="btn btn-default" title="vista de datos del sector" onclick=ventanaM("sector_copiado_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'",this.title)>'.
                   '<span class="glyphicon glyphicon-eye-open"></span></a> '.
                   '<a class="btn btn-warning" title="detalle" href="sector_copiado_grupo_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'">'.
                   '<span class="glyphicon glyphicon-list"></span></a> ';
        }
    }       
    
    
    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>