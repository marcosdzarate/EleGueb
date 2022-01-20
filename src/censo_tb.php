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
	 

    $aColumns = array('fecha','tipo','fechaTotal','zona','nFechaSec' );

    $xFiltro = " WHERE ( tt_censo_ampliado.tipo<>'MENSUAL')";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    

    require_once 'tb_dbconecta.php';
    require_once 'tb_sesion.php';
		

    /* Database connection information */
    $gaSql['user']       = elUser;
    $gaSql['password']   = elPassword;
    $gaSql['db']         = elDB;
    $gaSql['server']     = elServer;
	
	$sTable = "tt_censo_ampliado";  /* tabla temporaria */
	
   /*idem tb_script_comun pero agrega codigo para el manejo de 
    /* basado en
     * Script:    DataTables server-side script for PHP and MySQL
     * Copyright: 2010 - Allan Jardine, 2012 - Chris Wright
     * License:   GPL v2 or BSD (3-point)
     */
     

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
    $miQ="";
    /*
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {  
	 /*para desarrollo*/
	/* echo json_encode( array( "error" => 'Select: '.$GLOBALS['miQ'].' xFiltro: '.$GLOBALS['xFiltro'].' registros:'.$GLOBALS['iFilteredTotal'].' '.$sErrorMessage ) );*/
	 /*en produ */
	  echo json_encode( array( "error" => $sErrorMessage ) );
	 exit(0);
    }
 
     
    /*
     * MySQL connection
     */
	 
    $gaSql['link'] = mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password'], $gaSql['db'], elPort );
    if ( !$gaSql['link'] ) 
    {
        fatal_error( 'No se puede conectar con el server' );
    }
 
				
			/* mensajes en castellano*/
			if(!mysqli_query( $gaSql['link'], "SET lc_messages=es_AR" ))
			{
				/* no doy exit! que siga.... */
			}	
			
	/* cargo UTF8 */
 
	 if (!mysqli_set_charset($gaSql['link'], "utf8")) {
		fatal_error("Error cargando el conjunto de caracteres utf8" );		
	}


    $sql =  "CALL `vw_tabtem_censo_ampliado`();";
	if (!mysqli_query($gaSql['link'],$sql)) {
		fatal_error("Error al crear temporaria vw_tabtem_censo_ampliado`" );
	}		
	
	
	
     
    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
            intval( $_GET['iDisplayLength'] );
    }
     
     
    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
            }
        }
         
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
     
     
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
	 * INCORPORA FILTRO INICAL xFiltro
     
    $sWhere = "";*/
	$sWhere = $xFiltro;
	
$otrasTablas= "";  /*busqueda en texto, no en codigo (categoria, playa,...)*/
$otrosWhereY="";   /* para conectar con la tabla de codigos*/
$otrosWhereO="";   /* para la busqueda en el texto*/
$otraTabla="";

    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {


		$sWhere .= $otrosWhereY;		
        $sWhere .= " AND (";   /* decia WHERE ( */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
            {
                $sWhere .= $sTable.'.'.$aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= $otrosWhereO. ') ';
     
    
    }

  
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND (";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch_'.$i])."%' ";
            $sWhere .= ')';
        }
    }
     
     
    /*
     * SQL queries
     * Get data to display
     */
	 
$_SESSION['publiQWhere']=$sWhere;  //para descargar lo filtrado
	 
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(",".$sTable.".", $aColumns))."
        FROM   $sTable $otraTabla $sWhere $sOrder $sLimit
    ";
	$miQ=$sQuery;


	$rResult = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
     
    /* Total data set length  .... decia... SELECT COUNT(".$sIndexColumn.") */
    $sQuery = "
        SELECT COUNT(*)
        FROM   $sTable
    ";
    $rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
    $aResultTotal = mysqli_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];
     

	

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
    for ( $i=0 ; $i<count($aColumns)-1 ; $i++ )
    {
        $aCol = $aColumns[$i];
        $row[] = htmlspecialchars ($aRow[ $aCol ]);

    }
	/* se puede eliminar censo solo si en la fecha no hay registros en la tabla "sectores" */
	/* o es un sector copiado completo */
	$td = ' title="elimina los sectores a mano" disabled ';
	$z=trim($aRow['nFechaSec']);
	$t=trim($aRow['zona']);
	if ($z==0 and $t<>'con sector faltante') {
		$td = ' title="eliminar censo" ';
	}

	  
	$tdetalle=""; /*para vinculo a lista completa sectores*/
	if ($fTotal_ant<>$aRow['fechaTotal']) {
		$tdetalle= '<a class="btn btn-info" title="detalle de sectores en Fecha total " href="vw_censo_sector_index.php?fechaTotal='.urlencode($aRow['fechaTotal']).'&xtrx='.urlencode($aRow['tipo']).'">'.
               '<span class="glyphicon glyphicon-list"></span></a>';
	     $fTotal_ant= $aRow['fechaTotal']; 
	}
	/*       onclick... enlugar del href y el ) antes del >'.*/
	
    if (edita() and editaCenso($aRow['fechaTotal']) ) {
      $row[] = '<a class="btn btn-success" title="editar datos b&aacute;sicos de censo" onclick=ventanaM("censo_editar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<button class="btn btn-danger" '.$td.' onclick=ventanaM("censo_borrar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-erase"></span></button> '.
			   $tdetalle;
    }
    else  {
      $row[] = '<a class="btn btn-default" title="vista de datos b&aacute;sico de censo" onclick=ventanaM("censo_editar.php?fecha='.urlencode($aRow['fecha']).'",this.title)>'.
               '<span class="glyphicon glyphicon-eye-open"></span></a> '.
			   $tdetalle;
    }

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>