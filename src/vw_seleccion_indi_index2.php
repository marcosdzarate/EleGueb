<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_param.php';
		require_once 'tb_validar.php';

$arBusqueda = "";
$parBus = null;
$sDonde = "";
if ( !empty($_POST) and isset($_POST['descarga']) ) {
	
	$arBusqueda = explode ("^",$_POST['parBus']);	
	/* armo consulta segun filtro en pantalla para vista vw_seleccion_indi */
    $aColumnas = array('claveU','sexo','nuestro','muerto','tags','marcas','rango','cantTempo',
	                  'titempos','ttcate','tteta','tfoto','tanes','tmues','tmedi','ts3d');			  
					  
	/* filtros que se aplican a vw_seleccion_indi */
	/* filtro global = todas las columnas */
    $sDonde = "";
	
	/* para salida de la condicion en archivo xlsx */
	/* string ... */
	$sCondi = "";
	/* arreglo de textos de columnas */
	$txtColu = array('claveU','sexo','nuestro','muerto','tags','marcas','rango','cantTempo',
	                  'tipoTempo','categorias','etapasViaje','fotogrametria','anestesia','muestras','medidas','scan3D');
	
    if ( $arBusqueda[0] != "" )
    {
		$sCondi = "Global: ".$arBusqueda[0];
        $sDonde = "WHERE (";
        for ( $i=0 ; $i<count($aColumnas) ; $i++ )
        {
			$sDonde .= $aColumnas[$i]." LIKE BINARY '%".$arBusqueda[0]."%' OR ";			
        }
        $sDonde = substr_replace( $sDonde, "", -3 );
        $sDonde .= ')';
    }    
	 
    /* filtro por columnas con entrada al pie */
    for ( $i=0 ; $i<count($aColumnas)  ; $i++ )
    {
        if ( $arBusqueda[$i+1] != "" )
        {
            if ( $sDonde == "" )
            {
                $sDonde = "WHERE ";
            }
            else
            {
                $sDonde .= " AND ";
            }
			if( $arBusqueda[$i+1] <> "%-") {
			   $sCondi .= "  ".$txtColu[$i].": ".$arBusqueda[$i+1];
               $sDonde .= $aColumnas[$i]." LIKE BINARY '%".$arBusqueda[$i+1]."%' ";
			}
			else{
			   /* pide NULL */
               $sDonde .= $aColumnas[$i]." IS NULL ";
			}
		}
    }    
	
	
	
	
	
	/* con datos del filtrado de vw_seleccion_indi creo
	tabla temporaria tabtem_cuSelec */

	$pasaArch = "seleccion";

	$sql= "";
	
	/* opción seleccionada para descargar */
	if(!empty($_POST)){
		
		$sel = $_POST['qued'];
		/* 1-selección de claveU hecha x el usuario */
		$sql.= 'CALL `vw_tabtem_cuSelec`("'.$sDonde.'");';
		/* 2-ficha de todos los individuos en tabtem_cuSelec */
		$sql.= "CALL `vw_tabtem_individuo_observado`();";
		$sql .= "SELECT 'CONDICION' AS datosDe,'$sCondi' as condicion; ";
 
		switch ($sel) {
			case "bas":
				$sql.= "SELECT 'BASICOS' as datosDe,individuo.*,tags,marcas,rango as rangoTemporadas,cantTempo FROM individuo,vw_seleccion_indi,tabtem_cuSelec where individuo.claveU=tabtem_cuSelec.claveU and individuo.claveU=vw_seleccion_indi.claveU;";
				$pasaArch.="_BASICOS";
				break;
				
			case "fic":
				$sql.= "SELECT 'FICHA' as datosDe,vw_individuo_observado.* FROM vw_individuo_observado;";
				$pasaArch.="_FICHAS";
				break;
				
			default:
				/* datos de temporada y observacion de todos los individuos */
				$sql.= "CALL `vw_tabtem_observacion`('9999');";			
				// opciones posibles: tabla y titulos
				$queTabla = array ('tag','marca','muestras','medidas','muda','copula','macho','hembra','criadestetado','anestesia','viaje','scan3d');
				$masTitu = array ('TAGS','MARCAS','MUESTRAS','MEDIDAS','MUDAS','COPULAS','MACHOS','HEMBRAS','DESTETE','ANESTESIAS','VIAJES','ESCANEO3D');
				$laTabla=$queTabla[$sel];
				$txt=$masTitu[$sel];
				include 'tablas_fichacol.php';
				$cam=implode(",", $tColu);
				$sql.= "SELECT '$txt' as datosDe,tabtem_observacion.*,$cam FROM tabtem_observacion,$laTabla,tabtem_cuSelec WHERE tabtem_cuSelec.claveu=tabtem_observacion.claveU and tabtem_observacion.claveU = $laTabla.claveU and  tabtem_observacion.fecha= $laTabla.fecha;";
				/* si es viaje, incluyo viaje_config e instrumentos colocados */
				if ($laTabla=='viaje'){
					$sql.= "SELECT 'gs' as GrupoRespuesta,'VIAConfig' as datosDe,viaje_config.* FROM viaje_config,tabtem_cuSelec WHERE viaje_config.claveU = tabtem_cuSelec.claveU;";
					$sql.= "SELECT 'gs' as GrupoRespuesta,'VIAInstru' as datosDe,viaje_config.claveU,instrumentos_colocados.viajeID,instrumentos_colocados.instrumentoNRO,instrumentos.tipo,instrumentos.identificacion,instrumentos.modelo,instrumentos.fabricante,instrumentos_colocados.fecha_recuperacion,instrumentos_colocados.comentario FROM viaje_config,instrumentos_colocados,instrumentos,tabtem_cuSelec WHERE tabtem_cuSelec.claveU= viaje_config.claveU and instrumentos_colocados.viajeID=viaje_config.viajeID and instrumentos_colocados.instrumentoNRO=instrumentos.instrumentoNRO;";
					
				}
				$pasaArch.="_$txt";
				break;
		
		}	
		
	}
		
	XlsXOnDeFlai($sql,$pasaArch);
		//csvOnDeFlai($sql,$pasaArch);
		
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

    <link rel="stylesheet" href="login/style/main.css">
	
	
	
    <link   href="css/miSideBar.css" rel="stylesheet">   

<style>
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>	
<script src="js/imiei.js"></script>    
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">

	/* MODIFICAR: si se agrega una columna a la vista, esto indica
	              dónde hay algo que modificar/cambiar */
	/* MODIFICAR: ultima columna en que queda el header Ficha en la tabla de la interfaz */
	/*            si agregás "n" columnas sumale "n" */
	var colFicha=16;
$(document).ready(function() { 
    // Setup - add a text input to each footer cell
    $('#tablax tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
    } );
    

	

	
    var tablin = $("#tablax").DataTable( {
     "serverSide": true,
     "sAjaxSource": 'vw_seleccion_indi_tb2.php',
     stateSave: true,
	 "ordering": true,
	 "search": {
		"caseInsensitive": false
		},
	 "columns": [
	 { "width": "70px" },
	 { "width": "80px" },
    { className: "ccen","width": "70px" },
	{ className: "ccen","width": "70px" },
    null,
	null,
    { "width": "70px" },
    { className: "ccen", "width": "60px" },
	null,
	{ "width": "70px" }, /* ttcate */
	{ "width": "70px" }, /* tteta */
	{ className: "ccen","width": "70px" }, /* tfoto */
	{ className: "ccen","width": "70px" }, /* tanes */
	{ className: "ccen","width": "70px" }, /* tmues */
	{ className: "ccen","width": "70px" }, /* tmedi */
	{ className: "ccen","width": "70px" }, /* ts3d */ /*MODIFICAR agregar...*/
	{ "width": "70px" }  /* fijo Ficha */
    ],
	"columnDefs": [
			{"orderable": false, "targets": colFicha }  /* Ficha */
	],	
     "stateDuration": -1, 
     "pageLength": 10,
     "lengthMenu": [ 5,10,15,20, 40, 80, 100 ],
     "pagingType": "full_numbers",
     "language": {
         "emptyTable": "No hay datos disponibles en la tabla",
         "lengthMenu": "Mostrar _MENU_ registros",
         "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
         "infoFiltered":   "(filtrado de _MAX_ registros totales)",
         "infoEmpty":      "L&iacute;nea 0 de 0",
         "loadingRecords": "Cargando...",
         "processing":    "Procesando...",
         "search":         "Buscar general:",
         "zeroRecords":    "No se encuentran registros",
         "paginate": {
            "first":      "|<<",
            "last":       ">>|",
            "next":       ">",
            "previous":   "<"
            },   
         "aria": {
            "sortAscending":  ": activar para ordenar la columna ascendente",
            "sortDescending": ": activar para ordenar la columna descendente"
            }   
         },

    "initComplete":function(settings, json) {
				/* restaura valores del filtro de columna */
				var state = tablin.state.loaded();
				if (state) {
					tablin.columns().eq(0).each(function (colIdx) {
						if(colIdx!=colFicha) /*(Ficha) no sacar esta condicion!!! */
						{
							var colSearch = state.columns[colIdx].search;
							if (colSearch.search) {
								$('input', tablin.column(colIdx).footer()).val(colSearch.search);
								// alert(colIdx+' '+colSearch.search);
							}
						}
					});
					
				}



	
	}
	} );
   


	
	
	
    // aplico busque con ENTER
	// global
	$('#tablax_filter input').unbind();
	$('#tablax_filter input').bind('keyup', function(e) {
		if(e.keyCode == 13) {
		tablin.search(this.value).draw();
	}
	}); 
	
	// por columna
	    tablin.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup', function (e) {
            if(e.keyCode == 13) {
                that
                    .search( this.value )
                    .draw();
					
            }
        } );
    } );	
	        
		
       
} );


var subArre = function() {
	/* arma string con argumentos de busqueda previo al submit */
	/* argumento global */
	var buscar = $('#tablax').DataTable().search();
	var i;
	/* argumentos que corresponden a las columnas */
	for ( i=0 ; i<colFicha; i++ )
		{ 
			buscar = buscar + "^" + $('#tablax').DataTable().column(i).search();
		}
	   document.getElementById("parBus").value = buscar;
	   //alert (buscar);
   };
</script>


<!--


-->






<style>
.ccen{
        text-align:center;
    }

.sepa {
	padding-top:2px;
	color:#7a5757;
	}
.ffo {
	font-size:14px;
}
</style>
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_oterrestre.html"></div>

			<button type="button" class="botonayuda" style="top:240px" title="ayuda" onclick=ayudote(location.pathname)>
				<span class="glyphicon glyphicon-question-sign"></span>
			</button>
	
			<button type="button" class="botonayuda" style="top:180px;background-color: #d59f31" title="c&oacute;mo buscar o filtrar" data-toggle="collapse" data-target="#info">
					<span class="glyphicon glyphicon-search"></span></button>
					<div id="info" class="container well collapse ffo" style="text-align:left;width:800px;line-height:1.5">

					
					
<?php // echo $sDonde."<BR>";?>
<?php //echo $sel."<BR>";?>
<?php // echo $sql."<BR>";?>					
					
					    <p >C&Oacute;MO BUSCAR O FILTRAR</p>	
						<div class="row sepa ">
							<div class="col-sm-12">
							Escribir el argumento y presionar <kbd>ENTER</kbd>. Es sensible a may&uacute;sculas y min&uacute;sculas.
							</div>
						</div>		

						
						<div class="row sepa">
							<div class="col-sm-2 text-info">
								General
							</div>

							<div class="col-sm-10">
								Se hace la b&uacute;squeda en todas las columnas.
							</div>
						</div>
						
						<div class="row sepa">
							<div class="col-sm-2 text-info">
								Por columna
							</div>

							<div class="col-sm-10">				
								Puede buscarse por una o m&aacute;s columnas.
							</div>
						</div>		

						<div class="row sepa">
							<div class="col-sm-2 text-info">
								Tips
							</div>
							<div class="col-sm-10">				
								(1)=en columna basta una letra
							</div>							

 
						</div>						
							
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								temporada
							</div>
							<div class="col-sm-10">	
								 MUDA, FUERA, REPRO, mudaP, fueraP, reproP o combinaci&oacute;n de ellas separadas por coma sin espacio
							</div>
						</div>

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								categor&iacute;as
							</div>
							<div class="col-sm-10">	
								   los c&oacute;digos CRIA, DEST, YEAR, ADJO, SA02, etc. <a class="btn btn-link" title="ver categor&iacute;as" href="categoria_index.php" target="_blank">(ver)</a>
							</div>
						</div>
						
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								etapa de viaje
							</div>
							<div class="col-sm-10">	
								 COLOCACION, INICIA, COSTA, COSTA ARG, AJUSTE, REINICIA, RECUPERADO, SIN RECUPERACION
							</div>
						</div>
							
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								fotogrametr&iacute;a
							</div>
							<div class="col-sm-10">	
								 FOTOGRAMETRIA (1)
							</div>
						</div>							
							
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								anestesiados
							</div>
							<div class="col-sm-10">	
								 ANESTESIA (1)
							</div>
						</div>
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								muestras
							</div>
							<div class="col-sm-10">	
								 MUESTRAS (1)
							</div>
						</div>
						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								medidas
							</div>
							<div class="col-sm-10">	
								 MEDIDAS (1)
							</div>
						</div>

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								scan3D
							</div>
							<div class="col-sm-10">	
								 ESCAN (1)
							</div>
						</div>
						
						<div class="row sepa">
							<div class="col-sm-12">
							Para eliminar el filtro, borrar el argumento y presionar <kbd>ENTER</kbd>.
							</div>
						</div>							
						
						
						
						
					</div>
	
            <div class="container well"   style="text-align:left;width:1200px">
            <div class="row">
                <h4>relevamiento de mirounga</h4>
            </div>

            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">selecci&oacute;n del individuo </h3>
					
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
					
                      <thead>
                        <tr> <!-- MODIFICAR: header -->
                          <th>ClaveU</th>
                          <th>Sexo</th>
                          <th>Nuestro</th>
                          <th >Muerto</th>
                          <th>Tags</th>
                          <th>Marcas</th>
                          <th>entre a&ntilde;os</th>
                          <th >#temps.</th>
						  <th>temporadas</th>
						  <th>categorias</th>
						  <th>etapas viajes</th>
						  <th>fotogra metria</th>
						  <th>anes tesia</th>
						  <th>mues tra</th>
						  <th>medi das</th>
						  <th>scan 3D</th>
                          <th>Ficha</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>ClaveU</th>
                          <th>Sexo</th>
                          <th>Nuestro</th>
                          <th >Muerto</th>	
                          <th>Tag</th>
                          <th>Marca</th>
                          <th>entre a&ntilde;os</th>
                          <th>#temps.</th>
						  <th>temporadas</th>
						  <th>categorias</th>
						  <th>etapas viajes</th>
						  <th>fotogra metria</th>
						  <th>anestesia</th>
						  <th>muestra</th>
						  <th>medidas</th>
						  <th>scan3D</th>
                          
                        </tr>
                      </tfoot>                    
                    </table>

                </div>
            </div>
			
            <div class="row">
				<div class="col-sm-2">			
                   <?php if (edita()) :?>
                         <a class="btn btn-info btn-sm" title="nuevo individuo" onclick=ventanaM("individuo_crear.php","")><span class="glyphicon glyphicon-plus"></span> individuo</a>&nbsp;&nbsp;
                    <?php endif?>
				</div>
				<div class="col-sm-10">					
					<form id="aDescarga" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" onsubmit="return subArre();">
					
							<input type="hidden" id="parBus" name="parBus" value="<?php echo $parBus;?>" >
							
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
							
							<label class="radio-inline" title="datos básicos">
							  <input type="radio" name="qued"  value="bas" checked>bas
							</label>								
							<label class="radio-inline" title="fichas">
							  <input type="radio" name="qued"  value="fic" >fic
							</label>
							<label class="radio-inline" title="datos de tags">
							  <input type="radio" name="qued"  value="0">tag
							</label>
							<label class="radio-inline" title="datos de marcas">
							  <input type="radio" name="qued"  value="1">mar
							</label>
							<label class="radio-inline" title="datos de muestras">
							  <input type="radio"  name="qued" value="2">mue
							</label>
							<label class="radio-inline" title="datos de medidas">
							  <input type="radio"  name="qued" value="3">med
							</label>
							<label class="radio-inline" title="datos de mudas">
							  <input type="radio" name="qued"  value="4">mud
							</label>
							<label class="radio-inline" title="datos de copulas">
							  <input type="radio" name="qued"  value="5">cop
							</label>
							<label class="radio-inline" title="datos de machos">
							  <input type="radio"  name="qued" value="6">mac
							</label>
							<label class="radio-inline" title="datos de hembra">
							  <input type="radio" name="qued"  value="7">hem
							</label>
							<label class="radio-inline" title="datos de destete">
							  <input type="radio" name="qued"  value="8">des
							</label>
							<label class="radio-inline" title="datos de anestesias">
							  <input type="radio" name="qued"  value="9">ane
							</label>
							<label class="radio-inline" title="datos de viajes">
							  <input type="radio" name="qued"  value="10">via
							</label>
							<label class="radio-inline" title="datos de escaneo3D">
							  <input type="radio" name="qued"  value="11">s3D
							</label>
	

						</form>					

				</div>
			</div>
    </div> <!-- /container -->

<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaM.html"></div>
 
<script>
w3.includeHTML();
</script>
    
  </body>


</html>