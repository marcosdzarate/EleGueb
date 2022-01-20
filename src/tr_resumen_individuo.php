<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_database.php';
require_once 'tb_validar.php';

    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
		if(empty($_POST)){
            $v=false;
			}
        }
    siErrorFuera($v);

$sql=null;

	if (empty($_POST)){
		
		require_once 'tb_param.php';  /* por $condi y $param */

		$pdo = Database::connect();

		/* recupero info del individuo para el encabezado */
		
		$sql = "SELECT vw_seleccion_indi.*,muertoTempo FROM individuo,vw_seleccion_indi  where individuo.claveU=? and individuo.claveU=vw_seleccion_indi.claveU LIMIT 1";
		$q = $pdo->prepare($sql);
		$q->execute(array($claveU));
		$arr = $q->errorInfo();
		if ($q->rowCount()==0) {
				Database::disconnect();
				$eError="No hay registro!!!!!";
				}
			else{
			   if ($arr[0] <> '00000') {
				   $eError = "Error MySQL: ".$arr[1].$arr[2];
				   Database::disconnect();
				  }
			   else{
					$data = $q->fetch(PDO::FETCH_ASSOC);
					   
					$sexo = $data['sexo'];
					$nuestro = $data['nuestro'];
					$marcas = $data['marcas'];
					$tags= $data['tags'];
					$muerto = $data['muerto'];
					$muertoTempo = $data['muertoTempo'];
					
					/* cantidad de temporadas: si no tiene temporadas
					(por que se interrumpe la secuencia de nuevo individuo
					 al ingresarlo) +temporada incoporpora xtrx=nuevo*/
					$xt="";
					$sql = "SELECT * FROM temporada WHERE claveU=? order by temporada,tipoTempo";
					$q = $pdo->prepare($sql);
					$q->execute(array($claveU));
					$arr = $q->errorInfo();
					$dt1 = $q->fetch(PDO::FETCH_ASSOC); /* datos de la primer temporada */
					if ($q->rowCount()==0) {
						$xt="&xtrx=nuevo";   
					}

					
					/* tiene madre? */
					$xmam="no";
					$sql = "SELECT * FROM madrehijo WHERE clavePup=?";
					$q = $pdo->prepare($sql);
					$q->execute(array($claveU));
					$arr = $q->errorInfo();
					if ($q->rowCount()>0) {
						$xmam="si";   
					}
					
					/* fue madre? */
					$fmam="no";
					$sql = "SELECT * FROM madrehijo WHERE claveMam=?";
					$q = $pdo->prepare($sql);
					$q->execute(array($claveU));
					$arr = $q->errorInfo();
					if ($q->rowCount()>0) {
						$fmam="si";   
					}
					
					$disaEliminar=' title="eliminar individuo" ';
					if ($xmam=='si' or $fmam=='si') {
						$disaEliminar='disabled title="eliminar v&iacute;nculos madre-hijo para eliminar individuo" ';
					}
										
					
					/* no tiene madre pero puede tenerla SOLO en la temporada en que es cria */
					$disa="";
					$cti="";
					$te1="";
					if ($xmam=="no"){
						$te1=$dt1['temporada'];
						$tt1=$dt1['tipoTempo'];
						$ca1=$dt1['categoria'];
						if($tt1<>'REPRO' or 
						    ($ca1<>"CRIA" and $ca1<>"DEST") ){
							$disa='disabled';
							$cti=": no tiene categor&iacute;a cr&iacute;a";
						}

					}
					

					/* primer fecha del individuo */
					$pFecha="xx";
					$pTempo=$dt1['temporada'];					
					$sql = "SELECT min(fecha) as pF FROM observado WHERE claveU=? AND temporada=? GROUP BY claveU,temporada LIMIT 1";
					$q = $pdo->prepare($sql);
					$q->execute(array($claveU,$pTempo));
					$arr = $q->errorInfo();
					$pd = $q->fetch(PDO::FETCH_ASSOC); /* datos de la primer observacion */
					if ($q->rowCount()>0) {
						$pFecha = $pd['pF'];   
					}
	
					
					Database::disconnect();
			   }
			}
		$param0 = "?claveU=".$claveU;
	}
	else {
		if(isset($_POST['descarga'])){
						
			/* la ficha	*/
			$claveU=$_POST["claveU"];			
$param0 = "?claveU=".$claveU;
			$pasaArch = $claveU."_FICHA";
			$sql= "SELECT 'BASICOS' as datosDe,individuo.*,tags,marcas,rango as rangoTemporadas,cantTempo FROM individuo,vw_seleccion_indi  where individuo.claveU='$claveU' and individuo.claveU=vw_seleccion_indi.claveU LIMIT 1;";		
			$sql.= "CALL `vw_tabtem_individuo_observadoU`('$claveU');";
			$sql.= "SELECT 'FICHA' as datosDe,vw_individuo_observadoU.* FROM vw_individuo_observadoU;";
			/* si hay marcadas opciones de descarga de otros datos (que[]).... */
			if(!empty($_POST['qued'])){
				// Loop por las opciones elegidas  (no se incluye xMuerto!!!!)
				$queTabla = array('tag','marca','muestras','medidas','muda','copula','macho','hembra','criadestetado','anestesia','viaje','scan3d');
				$masTitu = array('TAGS','MARCAS','MUESTRAS','MEDIDAS','MUDAS','COPULAS','MACHOS','HEMBRAS','DESTETE','ANESTESIAS','VIAJES','ESCANEO3D');
				foreach($_POST['qued'] as $sel){
					$laTabla=$queTabla[$sel];
					$txt=$masTitu[$sel];
					include 'tablas_fichacol.php';
					$cam=implode(",", $tColu);
					$sql.= "CALL `vw_tabtem_observacion`('$claveU');";
					$sql.= "SELECT 'gs' as GrupoRespuesta,'$txt' as datosDe,tabtem_observacion.*,$cam FROM tabtem_observacion,$laTabla WHERE tabtem_observacion.claveU = $laTabla.claveU and  tabtem_observacion.fecha= $laTabla.fecha;";
					/* si es viaje, incluyo viaje_config e instrumentos colocados */
					if ($laTabla=='viaje'){
						$sql.= "SELECT 'gs' as GrupoRespuesta,'VIAConfig' as datosDe,viaje_config.* FROM viaje_config WHERE viaje_config.claveU = '$claveU';";
						$sql.= "SELECT 'gs' as GrupoRespuesta,'VIAInstru' as datosDe,viaje_config.claveU,instrumentos_colocados.viajeID,instrumentos_colocados.instrumentoNRO,instrumentos.tipo,instrumentos.identificacion,instrumentos.modelo,instrumentos.fabricante,instrumentos_colocados.fecha_recuperacion,instrumentos_colocados.comentario FROM viaje_config,instrumentos_colocados,instrumentos WHERE viaje_config.claveU = '$claveU' and instrumentos_colocados.viajeID=viaje_config.viajeID and instrumentos_colocados.instrumentoNRO=instrumentos.instrumentoNRO;";
						
					}

				}
				$pasaArch.="_plus";
			}

			
			XlsXOnDeFlai($sql,$pasaArch);
		}
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

<script src="js/imiei.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" class="init">


$(document).ready(function() {
       /*ini_datatable ('#tablax' , 'tr_resumen_individuo_tb.php?condi=<?php echo $condi;?>',-15,0) ;*/
/* inicializa datatable - textos en españos y demas
  qtable es #id_del_tag_table
  qphp el nombre del script php donde se "dibuja" la tabla
  plongi es la longitud de pagina inicial
    si plongi negativo, ordering=false; el valor absoluto va a plongi
  ordencolu columna para orden inicial, hasta 2 columnas si es entero una columna, si tiene punto los decimales son la segunda columna
  reemplazo "ajax" por "sAjaxSource"
*/
var qtabla='#tablax';
var qphp = 'tr_resumen_individuo_tb.php?sexx=<?php echo $sexo;?>&condi=<?php echo $condi;?>';
var plongi = -15;
var ordencolu= 0;
var selected = "";
var orden = true;
if (plongi<0) {
    orden= false;
    plongi=Math.abs(plongi);
}
if (Number.isInteger(ordencolu)){
    /* solo una columna */
    arreorden =[[ ordencolu, 'asc' ]];
    }
    else{
        ocol1 = Math.floor(ordencolu);
        ocol2 = Math.floor((ordencolu-ocol1)*100);
        arreorden = [[ocol1,  'asc' ], [ocol2,  'asc' ]];
    }
var cuid="30px";
var tablin = $(qtabla).dataTable( {
 "sAjaxSource": qphp,
 stateSave: true,
 "stateDuration": -1,
 "Processing": true,
 "ordering": orden,
 "order": arreorden,
 "serverSide": true,
 "pageLength": plongi,
     "columns": [
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid },
     { "width": cuid }
    ], 
 "lengthMenu": [ 5,15, 30, 60, 80, 100 ],
 "pagingType": "full_numbers",
 "language": {
     "emptyTable": "No hay datos disponibles en la tabla",
     "lengthMenu": "Mostrar _MENU_ registros",
     "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
     "infoFiltered":   "(filtrado de _MAX_ registros totales)",
     "infoEmpty":      "L&iacute;nea 0 de 0",
     "loadingRecords": "Cargando...",
     "processing":    "Procesando...",
     "search":         "Buscar:",
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
     }

} );
 
// aplico busque con ENTER
	$('#tablax_filter input').unbind();
	$('#tablax_filter input').bind('keyup', function(e) {
		if(e.keyCode == 13) {
		tablin.fnFilter(this.value);
	}
	}); 

       $('#tablax').DataTable().column( 0 ).visible( false );
       $('#tablax').DataTable().column( 7 ).visible( false );
       $('#tablax').DataTable().column( 8 ).visible( false );
	   
	   
	   
	   
	   
} );

</script>

<script>
function abrirVentana() {
window.open("fotos.php<?php echo $param0?>","_blank","");
}
</script>

<script>
function lisTodo (t) {
	/* abre una ventana con la lista completa de los datos de la tabla 
	   seleccionada al hacer click en el header */
	ventanaM('tablas_index.php?claveU=<?php echo $claveU?>&t='+t,'ampli1200');
}
</script>

<style>
td,th {
        text-align:center;
    }
.blargo {
    padding: 0px 8px;
}
.imax {
    width:25px;
}


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
 input[type="checkbox"] {
    margin: 0px 0 0}
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




	<button type="button" class="botonayuda" style="top:180px;background-color: #d59f31" title="buscar en la ficha" data-toggle="collapse" data-target="#info">
	<span class="glyphicon glyphicon-search"></span></button>
					
					<div id="info" class="container well collapse ffo" style="text-align:left;width:800px;line-height:1.5">
					    <p >BUSCAR EN LA FICHA</p>	
						<div class="row sepa ">
							<div class="col-sm-12">
							Escribir el argumento y presionar <kbd>ENTER</kbd> (may&uacute;scula o min&uacute;scula es lo mismo).<br>
							La b&uacute;squeda se hace en los datos de columnas <strong>a&ntilde;o, tipo, categor&iacute;a, fecha y playa</strong> y en datos <strong>ocultos</strong> en el resto la tabla. Para estos &uacute;ltimos, los argumentos que pueden ingresarse son:
							</div>
						</div>		
						

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
							</div>
							<div class="col-sm-10">	
								 un tag, una marca, un porcentaje de muda (como p%)
							</div>
						</div>
						<div class="row sepa ffo">
							<div class="col-sm-5 text-info">	
								 y palabras claves relacionadas a
							</div>
						</div>
						

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								muestras
							</div>
							<div class="col-sm-10">	
								ADN, SANGRE, BIGOTES, PELOS, FOTOGRAMETRIA
							</div>
						</div>

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								hembras
							</div>
							<div class="col-sm-10">	
								VISTA, PRENADA, NO PRENADA, EN PARTO, CON PUP
							</div>
						</div>

						<div class="row sepa ffo">
							<div class="col-sm-2 text-right text-success">	
								anestesia
							</div>
							<div class="col-sm-10">	
								INICIAL, SIGUIENTE, COMENTA, INDUCCION, FIN, FIN1, FIN2
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
							
						<div class="row sepa">
							<div class="col-sm-12">
							Para eliminar el filtro, borrar el argumento y presionar <kbd>ENTER</kbd>.
							</div>
						</div>							
						
						
						
						
					</div>	
	
	
	
	
	

    <div class="container well"  style="width:1200px">
            <div class="row">
               <div class="col-sm-4">  
                <h4>ficha para  
                    <?php 
                          echo " tags: ".$tags;
                          if ($nuestro=='NO') {
                              echo " (AJENO) ";
                          }
                          
                          if ($muerto=='SI'){
                              echo " - visto muerto en ".$muertoTempo;
                          }
                    ?>
                </h4>
                </div>

                <div class="col-sm-4">  
                <h4>
                <?php echo "marcas: ".$marcas;?>
                </h4>
                </div>

                <div class="col-sm-1">  
                <h5>
                <?php echo "sexo: ".$sexo;?>
                </h5>
                </div>

                <div class="col-sm-2 text-center">  
                <h5>
                <button class="btn btn-success btn-lg blargo" title="datos b&aacute;sicos" onclick=ventanaM("individuo_editar.php?claveU=<?php echo $claveU?>",this.title)>
			<?php if (edita()) :?>
                                 <span class="glyphicon glyphicon-edit"></span></button>
			<?php else: ?>
                                 <span class="glyphicon glyphicon-eye-open"></span></button>
			<?php endif ?>
			<?php if (edita()) :?>
                <button class="btn btn-danger btn-lg blargo" <?php echo $disaEliminar;?> onclick=ventanaM("individuo_borrar.php?claveU=<?php echo $claveU?>",this.title)>
                                 <span class="glyphicon glyphicon-erase"></span></button>
			<?php endif ?>								
                <button class="btn btn-default btn-lg blargo" <?php echo $disa;?> style="color:#8dd3b3" title="v&iacute;nculo con madre<?php echo $cti;?>" onclick=ventanaM("vinculo_madrehijo.php?clavePup=<?php echo $claveU?>&xmam=<?php echo $xmam?>&te1=<?php echo $te1?>&pF=<?php echo $pFecha?>",this.title)>
				            <?php if ($xmam=="si") : ?>
                                 <span class="glyphicon glyphicon-heart"></span></a> 
							<?php else: ?>
                                 <span class="glyphicon glyphicon-heart-empty"></span> 
							<?php endif ?>
				</button>			
				<a onclick="abrirVentana()" class="btn btn-info btn-lg blargo" title="todas las fotos y videos"> <span class="glyphicon glyphicon-film"></span></a>
							
							
                </h5>
                </div>

                <div class="col-sm-1">  
                <h5>
                <?php echo "claveU: ".$claveU;?>
                </h5>
                </div>
                
            </div>  
            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">observaciones y tareas en el tiempo</h3>

				</div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>claveU</th>
                          <th>A&ntilde;o</th>
                          <th>Tipo</th>
                          <th>Categor&iacute;a</th>
                          <th>acci&oacute;n</th>
                          <th>fecha</th>
                          <th>playa</th>
                          <th>longi</th>
                          <th>lati</th>
                          <th title="tags"><a class="btn btn-info" onclick=lisTodo(0)>tag</a></th>
                          <th title="marcas"><a class="btn btn-info" onclick=lisTodo(1)>mar</a></th>
                          <th title="muestras"><a class="btn btn-info" onclick=lisTodo(2)>mue</a></th>
                          <th title="medidas"><a class="btn btn-info" onclick=lisTodo(3)>med</a></th>
                          <th title="muda"><a class="btn btn-info" onclick=lisTodo(4)>mud</a></th>
                          <th title="c&oacute;pulas"><a class="btn btn-info" onclick=lisTodo(5)>cop</a></th>
                          <th title="macho"><a class="btn btn-info" onclick=lisTodo(6)>mac</a></th>
                          <th title="hembra"><a class="btn btn-info" onclick=lisTodo(7)>hem</a></th>
                          <th title="destete"><a class="btn btn-info" onclick=lisTodo(8)>des</a></th>
                          <th title="anestesias"><a class="btn btn-info" onclick=lisTodo(9)>ane</a></th>
                          <th title="viajes"><a class="btn btn-info" onclick=lisTodo(10)>via</a></th>
                          <th title="scan3d"><a class="btn btn-info" onclick=lisTodo(11)>s3D</a></th>
                          <th title="clave madre o pup">MoP</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <div class="row">
					<div class="col-sm-2">
					
					   <?php if (edita()) :?>
							 <a class="btn btn-info btn-sm" title="nueva temporada" onclick=ventanaM("temporada_crear.php<?php echo $param.$xt?>&sexx=<?php echo $sexo?>","")><span class="glyphicon glyphicon-plus"></span> temporada</a>&nbsp;&nbsp;
						<?php endif?>               
					</div>
					<div class="col-sm-8">
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
							<input type="hidden" name="claveU" value="<?php echo $claveU;?>"/>					
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
							<label class="checkbox-inline" title="datos de tags">
							  <input type="checkbox" name="qued[]" value="0">tag
							</label>
							<label class="checkbox-inline" title="datos de marcas">
							  <input type="checkbox" name="qued[]"  value="1">mar
							</label>
							<label class="checkbox-inline" title="datos de muestras">
							  <input type="checkbox"  name="qued[]" value="2">mue
							</label>
							<label class="checkbox-inline" title="datos de medidas">
							  <input type="checkbox"  name="qued[]" value="3">med
							</label>
							<label class="checkbox-inline" title="datos de mudas">
							  <input type="checkbox" name="qued[]"  value="4">mud
							</label>
							<label class="checkbox-inline" title="datos de copulas">
							  <input type="checkbox" name="qued[]"  value="5">cop
							</label>
							<label class="checkbox-inline" title="datos de machos">
							  <input type="checkbox"  name="qued[]" value="6">mac
							</label>
							<label class="checkbox-inline" title="datos de hembra">
							  <input type="checkbox" name="qued[]"  value="7">hem
							</label>
							<label class="checkbox-inline" title="datos de destete">
							  <input type="checkbox" name="qued[]"  value="8">des
							</label>
							<label class="checkbox-inline" title="datos de anestesias">
							  <input type="checkbox" name="qued[]"  value="9">ane
							</label>
							<label class="checkbox-inline" title="datos de viajes">
							  <input type="checkbox" name="qued[]"  value="10">via
							</label>
							<label class="checkbox-inline" title="datos de escaneo3D">
							  <input type="checkbox" name="qued[]"  value="11">s3D
							</label>
	
					</form>
					</div>
					<div class="col-sm-2">					
						<a class="btn btn-default btn-sm" href="vw_seleccion_indi_index2.php")>a selecci&oacute;n</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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