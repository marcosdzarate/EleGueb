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
	
    $aColumns = array('IDpublicacion','titulo','identificaciones','marcas','claveU' );
    $qTabla   = array('idpapers','publicaciones','idpapers','vw_seleccion_indi','vw_seleccion_indi');
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";


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
     echo json_encode( array( "error" => $sErrorMessage ) );
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
				$t=$qTabla[$i].".";
				$sWhere .= $t.$aColumns[$i]." LIKE '%".mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%' OR ";			
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
     
	 

   /*
     * SQL queries
     * Get data to display
     */
	 
	 $sCondi = " idpapers.claveU=vw_seleccion_indi.claveU and idpapers.IDpublicacion=publicaciones.ID ";
	 if (empty($sWhere)) {
		 $sWhere = "WHERE ".$sCondi;
	 }
	 else{
		 $sWhere .= " AND ".$sCondi;
	 }

	 
	 

$_SESSION['publiQWhere']=$sWhere;  //para descargar lo filtrado




	 $sQuery = "
			SELECT SQL_CALC_FOUND_ROWS idpapers.IDpublicacion,publicaciones.titulo,idpapers.identificaciones,vw_seleccion_indi.marcas,vw_seleccion_indi.claveU
			FROM   idpapers,vw_seleccion_indi,publicaciones
			$sWhere
			$sOrder
			$sLimit
		";

	$miQ=$sQuery;
    $rResult = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: '.$miQ . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
     
    /* Total data set length  .... decia... SELECT COUNT(".$sIndexColumn.") */
/*    $sQuery = "
        SELECT COUNT(*)
        FROM   $sTable
    ";
    $rResultTotal = mysqli_query( $gaSql['link'], $sQuery ) or fatal_error( 'MySQL Error: ' . mysqli_errno( $gaSql['link'] )." ". mysqli_error( $gaSql['link'] ) );
    $aResultTotal = mysqli_fetch_array($rResultTotal);  
    $iTotal = $aResultTotal[0];*/
	$iTotal = 99999;
     

    
     
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


	$rVincula="";
    if (edita()) {	
		$rVincula = '<a class="btn btn-warning" title="editar/quitar v&iacute;nculo" onclick=ventanaM("vincular_IndiPubli_editar.php?ID='.urlencode($aRow['IDpublicacion']).'&claveU='.urlencode($aRow['claveU']).'",this.title)>'.
               '<span class="glyphicon glyphicon-edit"></span></a> ';
	}
	
      $row[] = '<a class="btn btn-default" title="datos de la publicaci&oacute;n" target="_blank" onclick=ventanaM("publicaciones_ver.php?ID='.urlencode($aRow['IDpublicacion']).'",this.title)>'.
               '<span class="glyphicon glyphicon-book"></span></a> '.
			   '<a class="btn btn-default" target="_blank" title="ficha del individuo" href="tr_resumen_individuo.php?claveU='.urlencode($aRow['claveU']).'">'.
			   '<span class="glyphicon glyphicon-option-horizontal"></span></a> '.
			   $rVincula;



    $output['aaData'][] = $row;
}

echo json_encode( $output );

?>