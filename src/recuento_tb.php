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



	 $aColumns = array('fecha','libreta','orden','categoria','sexo','status','cantidad' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";
    /* DB table to use */
    $sTable = "recuento";

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
/*require_once 'tb_script_comun.php';*/


   /*idem tb_script_comun pero agrega codigo para el manejo de 
   /* busqueda cuando se trata de un campo texto basado en tabla codigos (playa, colaborado, categoria...)
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
	 /*echo json_encode( array( "error" => 'Select: '.$GLOBALS['miQ'].' xFiltro: '.$GLOBALS['xFiltro'].' '.$sErrorMessage ) );*/
	 /*en produ*/
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
$otraTabla=",categoria";

    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {

        if (array_search("categoria",$aColumns)<>false){
			/*$otraTabla .= ",categoria";  /*busqueda x descripcion categoria*/
			$otrosWhereY .= " AND ($sTable.categoria=categoria.IDcategoria) "; 
			$otrosWhereO .= " OR (categoria.cateDesc  LIKE '%" .mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%')";
			
		}


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
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(",".$sTable.".", $aColumns))." 
        FROM   $sTable
		$otraTabla
        $sWhere AND (recuento.categoria=categoria.IDcategoria) 
        ORDER BY oIngreso,ordenCate
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

        $pQuery = "";
        if ($aCol == 'categoria') {
           $pQuery = "SELECT cateDesc FROM categoria WHERE IDcategoria=? LIMIT 1";
            }
			
        if ($pQuery <> "") {
			/*con prepare statement */
			$stmtx= mysqli_stmt_init ($gaSql['link']);
			$stmtx = mysqli_prepare($gaSql['link'], $pQuery);
			if ($stmtx <> false){
				$r=mysqli_stmt_bind_param($stmtx, "s", $aRow[ $aCol ]) or fatal_error( 'stmt_bind' );	/*parametro*/
				$r=mysqli_stmt_execute($stmtx) or fatal_error( 'stmt_execute' );							/* ejecutar la consulta */
				$r = mysqli_stmt_bind_result($stmtx,$caDe)  or fatal_error( 'stmt_get_result' ) ;
			} else{
				fatal_error( 'Error en prepare tb_script comun.' );
			}			
			
			$r = mysqli_stmt_fetch($stmtx);
		  	if ($r){
                 $row[] = htmlspecialchars ($caDe);
              }
              else{
                 $row[] = htmlspecialchars ($aRow[ $aCol ]);
              }
			  $r=mysqli_stmt_close($stmtx);
			}
         else {
           $row[] = htmlspecialchars ($aRow[ $aCol ]);
          }

    }
    if (edita()  and $_SESSION['tipocen']<>'MENSUAL'   and editaCenso($aRow['fecha']) ) {
      $row[] = '<a class="btn btn-success" title="editar componente"
href="recuento_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'&categoria='.urlencode($aRow['categoria']).'&sexo='.urlencode($aRow['sexo']).'&status='.urlencode($aRow['status']).'">'.
               '<span class="glyphicon glyphicon-edit"></span></a> '.
               '<a class="btn btn-danger" title="eliminar componente"
href="recuento_borrar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'&categoria='.urlencode($aRow['categoria']).'&sexo='.urlencode($aRow['sexo']).'&status='.urlencode($aRow['status']).'">'.
               '<span class="glyphicon glyphicon-erase"></span></a> '.'';
    }
    else  {
      $row[] = '<a class="btn btn-default" title="ver" href="recuento_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'&categoria='.urlencode($aRow['categoria']).'&sexo='.urlencode($aRow['sexo']).'&status='.urlencode($aRow['status']).'">'.
               '<span class="glyphicon glyphicon-eye-open"></span></a> '.'';
    }

    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>