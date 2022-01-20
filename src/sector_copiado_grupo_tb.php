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
    $aColumns = array('fecha','libreta','orden','playa','referencia','conformadoOK','tipoPlaya','geomTex' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";

    /* DB table to use */
    $sTable = "grupo";

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
           $pQuery = "SELECT nombre FROM playa WHERE IDplaya=?";
            }

        if ($pQuery <> "") {
			/*con prepare statement */
			$stmtx= mysqli_stmt_init ($gaSql['link']);
			$stmtx = mysqli_prepare($gaSql['link'], $pQuery);
			if ($stmtx <> false){
				$r=mysqli_stmt_bind_param($stmtx, "s", $aRow[ $aCol ]) or fatal_error( 'stmt_bind' );
				$r=mysqli_stmt_execute($stmtx) or fatal_error( 'stmt_execute' );
				$r=mysqli_stmt_bind_result($stmtx,$nom)  or fatal_error( 'stmt_get_result' ) ;
			} else{
				fatal_error( 'Error en prepare tb_script comun.' );
			}			
			$r = mysqli_stmt_fetch($stmtx);
		  	if ($r){
                 $row[] = htmlspecialchars ($nom);
              }
              else{
                 $row[] = htmlspecialchars ($aRow[ $aCol ]);
              }
			  $r=mysqli_stmt_close($stmtx);			  
			}
         else {
		   if ($aCol=="conformadoOK") {
			  if ($aRow[ $aCol ]=='SI'){
			     $row[] = '<span class="glyphicon glyphicon-ok" style="color:green"></span>';} 
				else{
			     $row[] = '<span class="glyphicon glyphicon-remove" style="color:red"></span>';}
		   }
			  else{
				  $row[] = htmlspecialchars ($aRow[ $aCol ]);
			  }
          }

    }
	$accion='no';
	if ($accion=='no') {   /* OJO esto lo hago para no sacar el código*/
		$row[] ="-";
	}
	else{
		if (edita() and $_SESSION['tipocen']<>'MENSUAL') {
		  $row[] = /*'<a class="btn btn-default" title="vista de datos del grupo" onclick=ventanaM("grupo_ver.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-eye-open"></span></a> '.*/
				   '<a class="btn btn-success" title="editar datos del grupo" onclick=ventanaM("grupo_editar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-edit"></span></a> '.
				   '<a class="btn btn-danger" title="eliminar grupo" onclick=ventanaM("grupo_borrar.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-erase"></span></a> '.
				   '<a class="btn btn-info" title="composici&oacute;n del grupo" onclick=ventanaM("recuento_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'&xtrx='.urlencode($aRow['referencia']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-list"></span></a>';
		}
		else  {
		  $row[] = '<a class="btn btn-default" title="vista de datos del grupo" onclick=ventanaM("grupo_ver.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-eye-open"></span></a> '.
				   '<a class="btn btn-info" title="composici&oacute;n del grupo" onclick=ventanaM("recuento_index.php?fecha='.urlencode($aRow['fecha']).'&libreta='.urlencode($aRow['libreta']).'&orden='.urlencode($aRow['orden']).'&xtrx='.urlencode($aRow['referencia']).'",this.title)>'.
				   '<span class="glyphicon glyphicon-list"></span></a>';
		}
    }
    $output['aaData'][] = $row;
}

echo json_encode( $output );
?>