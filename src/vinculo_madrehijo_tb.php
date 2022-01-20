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
    $aColumns = array('claveU','temporada','tags','marcas', 'mfecha' );

	$xfil = $_GET['condi'];
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "vw_madre_potencial";

    require_once 'tb_dbconecta.php';
    require_once 'tb_sesion.php';

    /* Database connection information */
    $gaSql['user']       = elUser;
    $gaSql['password']   = elPassword;
    $gaSql['db']         = elDB;
    $gaSql['server']     = elServer;

    /*
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {  
     /*para desarrollo*/
     echo json_encode( array( "error" => 'Select: '.$GLOBALS['miQ'].' sWhere: '.$GLOBALS['sWhere'].' '.$sErrorMessage ) );
     /*en produ*/
     /*echo json_encode( array( "error" => $sErrorMessage ) );*/
     exit(0);
    }

	
    /*
     * MySQL connection
     */
     
    $gaSql['link'] = mysqli_connect( $gaSql['server'], $gaSql['user'], $gaSql['password'], $gaSql['db'], elPort  );
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
     */
    $sWhere = "";
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%' OR ";			
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
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
                $sWhere .= " AND ";
            }
			if( $_GET['sSearch_'.$i] <> "%-") {
               $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch_'.$i])."%' ";
			}
			else{
			   /* pide NULL */
               $sWhere .= $aColumns[$i]." IS NULL ";
			}
 
        }
    }    

    if ( $sWhere == "" ) {
		$sWhere = " WHERE ( " . $xfil . " ) ";
	}
	else {
		$sWhere .= " AND ( " . $xfil . " ) ";
	}
     	

   /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
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

while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];
        $row[] = htmlspecialchars ($aRow[ $aCol ]);

    }

      $row[] = '<button class="btn btn-default" title="&eacute;sta es la madre" onclick=EsLaMadre("'.urlencode($aRow['claveU']).'","'.urlencode($aRow['mfecha']).'")>'.
               '<span class="glyphicon glyphicon-option-horizontal"></span></a> ';

    $output['aaData'][] = $row;
}

echo json_encode( $output );

?>