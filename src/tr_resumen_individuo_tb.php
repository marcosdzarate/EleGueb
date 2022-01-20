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



     $aColumns = array('claveU','temporada','tipoTempo','categoria','fecha','playa','longi','lati',
                        'Ntag','Nmarca','Nmuestras','Nmedidas','Nmuda','Ncopula','Nmacho','Nhembra','Ncriades','Nanestesia','Nviaje','Nscan3D','MamPup' );

    $xFiltro = " WHERE ( " . $_GET['condi'] . " ) ";  /* la condicion entre () o true */
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "no_la_uso";
    /* DB table to use */
    $sTable = "vw_individuo_observadoU";

    require_once 'tb_dbconecta.php';
    require_once 'tb_sesion.php';

    require_once 'tb_validar.php';
    siErrorFuera (val_condi($aColumns,$_GET['condi'],12));

    $sexx=$_GET['sexx'];

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


    /* tabla temporaria con la informacion del individuo vw_individuo_observadoU	*/
	/* extraigo la claveU del parametro ...?condi=claveU="AAAX"...*/
	$co=$_GET['condi'];
    $cu=substr($co,strpos($co,"claveU=")+8,4);	
    $sql =  "CALL `vw_tabtem_individuo_observadoU`('$cu');";
	if (!mysqli_query($gaSql['link'],$sql)) {
		fatal_error("Error al crear temporaria vw_tabtem_individuo_observadoU" );
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
        if (array_search("playa",$aColumns)<>false){
			$otraTabla .= ",playa";  /*busqueda x descripcion playa*/
			$otrosWhereY .= " AND ($sTable.playa=playa.IDplaya) "; 
			$otrosWhereO .= " OR (playa.nombre  LIKE '%" .mysqli_real_escape_string($gaSql['link'], $_GET['sSearch'] )."%')";
			
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
        $sWhere AND (vw_individuo_observadoU.categoria=categoria.IDcategoria)
        ORDER BY 1,2,3,5
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

$xAni="";
$xTip="";
$xrepro="";
$xordenC="";

/*redefino con "hacer"*/
     $aColumns = array('claveU','temporada','tipoTempo','categoria','hacer','fecha','playa','longi','lati',
                        'Ntag','Nmarca','Nmuestras','Nmedidas','Nmuda','Ncopula','Nmacho','Nhembra','Ncriades','Nanestesia','Nviaje','Nscan3D','MamPup');


while ( $aRow = mysqli_fetch_array( $rResult ) )
{
    $row = array();

    $qAni=$aRow[ 'temporada' ];   /* para dejar columnas en blanco cuando año, tipo tempo y categoria son iguales */
    $qTip=$aRow[ 'tipoTempo' ];
    $enBlanco=true;
    if ($xAni<>$qAni or $xTip<>$qTip){
        $enBlanco=false;
    }
    $xAni=$qAni;
    $xTip=$qTip;
    $pTip=substr(strtoupper($xTip),0,4);
    
	$disable="";
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        $aCol = $aColumns[$i];

        $pQuery = "";
        if ($aCol == 'categoria'){
            if (!$enBlanco) {
                $pQuery = "SELECT cateDesc,reproductiva,ordenCate,sexoc FROM categoria WHERE IDcategoria=? LIMIT 1";
            }
            else {
                $aRow[ $aCol ] = "";
            }
        }
        if ($aCol == 'temporada' and $enBlanco) {
            $aRow[ $aCol ] = "";
        }
        if ($aCol == 'tipoTempo' and $enBlanco) {
            $aRow[ $aCol ] = "";
        }


        if ($aCol == 'playa') {
           $pQuery = "SELECT nombre,'  ' as p1,'   ' as p2,'   ' as p3 FROM playa WHERE IDplaya=? LIMIT 1";
            }

        if ($pQuery <> "")
        {
            /*con prepare statement */
            $stmtx= mysqli_stmt_init ($gaSql['link']);
            $stmtx = mysqli_prepare($gaSql['link'], $pQuery);
            if ($stmtx <> false){
                    $r=mysqli_stmt_bind_param($stmtx, "s", $aRow[ $aCol ]) or fatal_error( 'stmt_bind' );   /*parametro*/
                    $r=mysqli_stmt_execute($stmtx) or fatal_error( 'stmt_execute' );                        /* ejecutar la consulta */
                    $r = mysqli_stmt_bind_result($stmtx,$caDe,$p1,$p2,$p3)  or fatal_error( 'stmt_get_result' ) ;  /* $repro, ordenC, sexoc nulos para "playa"!!!*/
            } else{
                    fatal_error( 'Error en prepare tb_script comun.' );
            }


            $r = mysqli_stmt_fetch($stmtx);

            if ($r){

                if ($aCol == 'categoria')
                {
                    $b = '<a class="btn btn-link" title="temporada" onclick=ventanaM("temporada_editar.php?claveU='.urlencode($aRow['claveU']).'&temporada='.urlencode($aRow['temporada']).'&tipoTempo='.urlencode($aRow['tipoTempo']).'&sexx='.urlencode($sexx).'","")>'. htmlspecialchars ($caDe).'</a> ';
                    $repro=$p1;
                    $ordenC=$p2;
                    $sexoC=$p3;
                    if($xordenC<>"" and $ordenC<$xordenC){
                        $b .='<span title="categor&iacute;a anterior a la precedente" class="glyphicon glyphicon-alert text-danger"></span>';
                    }
                    if($sexoC<>$sexx and $sexoC<>"CUALQUIERA"){
						$disable=" disabled ";
                        $b .='<span title="categor&iacute;a no es compatible con sexo" class="glyphicon glyphicon-alert text-danger"></span>';
                    }
                    $xordenC=$ordenC;
                    $row[] = $b;
                }
                else {
                    $row[] = htmlspecialchars ($caDe);
                }
             }
              else{
                 $row[] = htmlspecialchars ($aRow[ $aCol ]);
              }

            $r=mysqli_stmt_close($stmtx);
        }
         else 
		{
            if ($i>=9) {
                if($aCol=='MamPup'){
					/*$c=*/
					if ($aRow[ $aCol ] <> null and !$enBlanco) {
						$t="pup";
						if ($ordenC=='1.0' or $ordenC=='2.0'){
							$t="madre";
						}
						/* link a otro individuo madre o pup*/
						$row[] = '<a title="'.$t.'" href="tr_resumen_individuo.php?claveU='.$aRow[ $aCol ].'"><img src="imas/'.$t.'.png" class="img-rounded imax" alt="otro"></a> ';
					}
					else{
						$row[] = "";
					}
					continue;
				}
                $fCol = trim(substr($aCol,1,50));
                $tCol = $fCol;
                if ($tCol=="criades"){
                    $tCol="destete";
                }
				
				   $esCriaDes="";
					if ($fCol == "medidas") { 
					   /* para habilitar o no el campo edadMedida*/
					   $esCriaDes="&esCriaDes=no";
					   if ($ordenC=='1.0' or $ordenC=='2.0'){
							$esCriaDes="&esCriaDes=si";
					   }
				   }
				
				   $esCopula="";  
				   if ($fCol == "copula") {
					   /* para poder habilitar solo los individuos en la temporada y del sexo opuesto */
					   $esCopula = '&xtrx='.urlencode($sexx);
				   }
				
				   $esViaje="";  
				   if ($fCol == "viaje") {
					   /* paso mas parametros en la rl */
					   $esViaje = '&temporada='.urlencode($xAni).'&tipoTempo='.urlencode($xTip);
				   }
								
				
                if ($aRow[ $aCol ] <> null) {
                   $archiphp = $fCol."_editar.php";
                   /*"X" = tiene alguna informción*/
                   if ($aCol=='Ntag' or $aCol=='Nanestesia' or $aCol=='Ncopula') {
                       $archiphp = $fCol."_index.php";
                   }
				   $tC=$tCol;
				   if($aRow[ $aCol ]<>$aRow[ 'claveU' ]) {
					   $tC=$aRow[ $aCol ];
				   }
                   if (edita()) {
                       $row[] = '<button '.$disable.' class="btn btn-success" title="'.$tC.'"  onclick=ventanaM("'.$archiphp.'?claveU='.urlencode($aRow['claveU']).'&fecha='.urlencode($aRow['fecha']).$esCriaDes.$esCopula.$esViaje.'","")><span class="glyphicon glyphicon-pencil"></span></button> ';
                   }
                   else{
                       if ($aCol<>'Ntag' and $aCol<>'Nanestesia' and $aCol<>'Ncopula')
                       {
                           $archiphp = $fCol."_editar.php";
                       }
                       $row[] = '<button '.$disable.' class="btn btn-success" title="'.$tC.'" onclick=ventanaM("'.$archiphp.'?claveU='.urlencode($aRow['claveU']).'&fecha='.urlencode($aRow['fecha']).$esCriaDes.$esCopula.$esViaje.'","")><span class="glyphicon glyphicon-eye-open"></span></button> ';
                   }
                }
                else {
                   /* sin informacion, puede agregar registro*/
                   /* pero segun temporada,categoria y sexo*/
                   $puedeAgregar = 'si';
                   if ($fCol == 'copula' and ($pTip<>"REPR" or $repro=='NO' or $sexx=='NODET')){
                       $puedeAgregar='no';
                   }
                   if ($fCol == 'macho' and ($pTip<>"REPR" or $repro=='NO' or $sexx<>'MACHO') ){
                       $puedeAgregar='no';
                   }
                   if ($fCol == 'hembra' and ($pTip<>"REPR" or $repro=='NO' or $sexx<>'HEMBRA') ){
                       $puedeAgregar='no';
                   }
                   if ($fCol == 'criades' and $ordenC<>'1.0' and $ordenC<>'2.0'  ){
                       $puedeAgregar='no';
                   }
                   if ($fCol == 'muda' and ($pTip<>"MUDA")){
                       $puedeAgregar='no';
                   }

                   $archiphp = $fCol."_crear.php";
                   if (edita() and $puedeAgregar== 'si') {
                       $row[] = '<button '.$disable.' class="btn btn-gristr" title="'.$tCol.'" onclick=ventanaM("'.$archiphp.'?claveU='.urlencode($aRow['claveU']).'&fecha='.urlencode($aRow['fecha']).$esCriaDes.$esCopula.$esViaje.'","")><span class="glyphicon glyphicon-plus" ></button> ';
                   }
                   else{
                       $row[] = '<button '.$disable.' class="btn btn-gristr"><span class="glyphicon glyphicon-minus" ></button> ';
                   }

                }
            }
            else
            {
                if ($aCol=='hacer'){
                    if (!$enBlanco  and edita()) {
                        $b="";
						$dis="";
						if ($aRow['MamPup']<>""){
							$dis="disabled";
						}
                        if ($aRow['tipoTempo']<>strtoupper($aRow['tipoTempo'])) {
                            $b= '<a class="btn btn-danger '.$dis.'" title="eliminar temporada" onclick=ventanaM("temporada_borrar.php?claveU='.urlencode($aRow['claveU']).'&temporada='.urlencode($aRow['temporada']).'&tipoTempo='.urlencode($aRow['tipoTempo']).'","")><span class="glyphicon glyphicon-erase" ></a> ';
                        }
						$row[]=$b.'<a class="btn btn-info" title="agregar fecha y lugar de observaci&oacute;n o tarea" onclick=ventanaM("observado_crear.php?claveU='.urlencode($aRow['claveU']).'&temporada='.urlencode($aRow['temporada']).'&tipoTempo='.urlencode($aRow['tipoTempo']).'","")><span class="glyphicon glyphicon-calendar" ></a>';
                    }
                    else{
                        $row[] = "";
                    }
                }
                else
                {
                        if ($aCol == 'fecha') {
                            $f=$aRow['fecha'];
                            $vf = true;
                            $mf= validar_fecha ($f,$vf,true);
                            $mt= str_replace("Fecha en ","",$mf);
                            $mt= str_replace(".","",$mt);
							if ($vf===false){
								$disable=" disabled ";
							}
                            $b="";
                            if ($vf===false or ($vf===true and strpos($mt,$pTip)===false)) {
                                $b ="<span title='$mf' class='glyphicon glyphicon-alert text-danger'></span>";
                            }
                            if ($aRow['fecha']=="") {
                                $row[] = $b;
                            }
                            else{
								$eli="si"; /* habilita o no eliminar en el script*/
								if ($aRow['MamPup']<>""){
							       $eli="no";
						        }
                                $row[] = '<a class="btn btn-link" title="fecha y lugar de observaci&oacute;n o tarea" onclick=ventanaM("observado_editar.php?claveU='.urlencode($aRow['claveU']).'&fecha='.urlencode($aRow['fecha']).'&eli='.$eli.'","")>'. htmlspecialchars ($aRow[ $aCol ]).'</a> '.$b;
                            }
                        }
                        else{
                            $row[] = htmlspecialchars ($aRow[ $aCol ]);
                        }

                }
            }
        }

    }


    $output['aaData'][] = $row;

}

echo json_encode( $output );
?>