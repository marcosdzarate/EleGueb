<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	$queTabla = array('tag','marca','muestras','medidas','muda','copula','macho','hembra','criadestetado','anestesia','viaje','scan3d');
	$masTitu = array('TAGS','MARCAS','MUESTRAS','MEDIDAS','MUDAS','COPULAS','MACHOS','HEMBRAS','DESTETE','ANESTESIAS','VIAJES','ESCANEADO');

    $v=true;
    $claveU = null;
	
    if(!empty($_GET)) {
		if (isset($_GET["claveU"])) {
			$claveU=$_GET["claveU"];
			$m = validar_claveU ($claveU,$v,true);
		}
		else{
				$v=false;
			}
		siErrorFuera($v);

		$tt = null;
		if (isset($_GET["t"])) {
			$tt=$_GET["t"];
			$r=validar_entero($tt,0,11,$v,true);
			if ($v){
				$laTabla =$queTabla[$tt];
			}
		}
		else{
				$v=false;
			}
		 
		 siErrorFuera($v);
	}

	
	if (isset($_POST['descarga'])) 
	{
		$laTabla=$_POST["laTabla"];
		$claveU=$_POST["claveU"];
		$pasaArch=$claveU."_".$laTabla;	
		require_once 'tablas_fichacol.php';
		$cam=implode(",", $tColu);
		$txt=$masTitu[$_POST['tt']];
		$sql= "SELECT 'BASICOS' as datosDe,individuo.*,tags,marcas,rango as rangoTemporadas,cantTempo FROM individuo,vw_seleccion_indi  where individuo.claveU='$claveU' and individuo.claveU=vw_seleccion_indi.claveU LIMIT 1;";
		$sql.= "CALL `vw_tabtem_observacion`('$claveU');";
		$sql.= "SELECT '$txt' as datosDe,tabtem_observacion.*,$cam FROM tabtem_observacion,$laTabla WHERE tabtem_observacion.claveU = $laTabla.claveU and  tabtem_observacion.fecha= $laTabla.fecha;";

		/* si es viaje, incluyo viaje_config e instrumentos colocados */
		if ($laTabla=='viaje'){
			$sql.= "SELECT 'gs' as GrupoRespuesta, 'VIAConfig' as datosDe,viaje_config.* FROM viaje_config WHERE viaje_config.claveU = '$claveU';";
			$sql.= "SELECT 'gs' as GrupoRespuesta, 'VIAInstru' as datosDe,viaje_config.claveU,instrumentos_colocados.viajeID,instrumentos_colocados.instrumentoNRO,instrumentos.tipo,instrumentos.identificacion,instrumentos.modelo,instrumentos.fabricante,instrumentos_colocados.fecha_recuperacion,instrumentos_colocados.comentario FROM viaje_config,instrumentos_colocados,instrumentos WHERE viaje_config.claveU = '$claveU' and instrumentos_colocados.viajeID=viaje_config.viajeID and instrumentos_colocados.instrumentoNRO=instrumentos.instrumentoNRO;";
			
		}
		XlsXOnDeFlai($sql,$pasaArch);
	}
	
	
$condi="claveU='$claveU'";
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
       ini_datatable ('#tablax' , "tablas_tb.php?condi=<?php echo $condi;?>&t=<?php echo $laTabla;?>&c=<?php echo $claveU;?>",10,1.02) ;
       $('#tablax').DataTable().column( 0 ).visible( false );
       $('#tablax').DataTable().column( 3 ).visible( false );
       $('#tablax').DataTable().column( 6 ).visible( false );
       $('#tablax').DataTable().column( 8 ).visible( false );
       $('#tablax').DataTable().column( 9 ).visible( false );
       $('#tablax').DataTable().column( 10 ).visible( false );
       $('#tablax').DataTable().column( 11 ).visible( false );
	   
} );

</script>

</head>

<body>
    <div class="container well"  style="width:98%">
            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">todos los registros de <?php echo $masTitu[$tt] ?> para claveU <?php echo $claveU ?>  </h3>

				</div>
                <div class="panel-body">

				
                    <table id="tablax" class="display table table-striped table-bordered"  style="width:100%">
                      <thead>
                        <tr>
										<th>ClaveU</th>
										<th>Temp</th>
										<th>Tipo</th>
										<th>CategoriaID</th>
										<th>Categor&iacute;a</th>
										<th>Fecha</th>
										<th>PlayaID</th>
										<th>Playa</th>
										<th>latiPla</th>
										<th>longiPla</th>
										<th>lati</th>
										<th>longi</th>
							<?php switch ($laTabla) { 
									case "tag":		//tags ?>		
										<th>Tag</th>
										<th>A&ntilde;o borrado</th>
										<th>A&ntilde;o perdido<br>/encontrado</th>
										<th>Playa encontrado</th>
										<th>Nota</th>
								<?php	break;
									case "marca":		//marcas ?>
										<th>Marca</th>
										<th>Nota</th>
								<?php	break;
									case "muestras":		//muestras ?>
										<th>cod ADN</th>
										<th>Sangre</th>
										<th>Bigotes</th>
										<th>Pelos</th>
										<th>Fotogram</th>
										<th>Nota</th>
								<?php	break;
									case "medidas":		//medidas ?>
										<th>Largo Std</th>
										<th>Largo Curv</th>
										<th>Circunf</th>
										<th>Peso</th>
										<th>Nostril</th>
										<th>Largo AleExt</th>
										<th>Largo AleInt</th>
										<th>Edad (pup)</th>
										<th>Nota</th>
								<?php	break;
									case "muda":		//mudas ?>
										<th>Porcentaje</th>
										<th>Estado</th>
										<th>Nota</th>
								<?php	break;
									case "copula":		//copulas ?>
										<th>Hora</th>
										<th>Durac</th>
										<th>Pareja</th>
										<th>ParejaCu</th>
										<th>Nota</th>
								<?php	break;
									case "macho":		//machos ?>
										<th>Status</th>
										<th>Estado</th>
										<th>Alfa es</th>
										<th>Alfa es Cu</th>
										<th>Hembra en harem</th>
										<th>Pups en harem</th>
										<th>Nota</th>
								<?php	break;
									case "hembra": 	//hembras ?>
										<th>Estado</th>
										<th>Fec parto</th>
										<th>Estimada?</th>
										<th>Edad pup</th>
										<th>Nota</th>
								<?php	break;
									case "criadestetado":		//destete (unico)?>
										<th>Fecha destete</th>
										<th>Estimada?</th>
										<th>Edad pup</th>
										<th>Nota</th>
								<?php	break;
									case "anestesia":		//anestesias ?>
										<th>Hora</th>
										<th>Tipo</th>
										<th>Aplicac</th>
										<th>Droga</th>
										<th>ml</th>
										<th>Partida</th>
										<th>Nota</th>
								<?php	break;
									case "viaje":	//viajes ?>
										<th>Etapa</th>
										<th>Nota</th>
								<?php	break;
									case "scan3d":	//scan3d ?>
										<th>Hora</th>
										<th>Clima</th>
										<th>Sustrato</th>
										<th>Escaneo3D</th>
										<th>IDarchivo</th>
										<th>Video</th>
										<th>Distancia</th>
										<th>Nota</th>
										<th>Proceso</th>
										<th>Vol</th>
										<th>Peso</th>
										<th>Nota Proceso</th>
								<?php	break;
							 } ?>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
				
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
				<input type="hidden" name="claveU" value="<?php echo $claveU;?>"/>
				<input type="hidden" name="laTabla" value="<?php echo $laTabla;?>"/>
				<input type="hidden" name="tt" value="<?php echo $tt;?>"/>
				
                    <a class="btn btn-default  btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php?claveU=<?php ECHO $claveU;?>")>a ficha</a>
					
					<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>

				</form>
                </p>

    </div> <!-- /container -->


  </body>


</html>