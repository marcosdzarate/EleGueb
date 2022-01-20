<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  
	
	$m="";
	$v=true;
    $fec = null;
	if (isset($_GET["fec"])) {
		$fec=$_GET["fec"];
		$m = validar_fecha ($fec,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
		
	$v=true;
    $lib = null;
	if (isset($_GET["lib"])) {
		$lib=$_GET["lib"];
		$m = validar_libreta ($lib,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
		
	$v=true;
    $ord = null;
	if (isset($_GET["ord"])) {
		$ord=$_GET["ord"];
		$m = validar_orden ($ord,$v,false);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
		
	$v=true;
    $geoLo = null;
	if (isset($_GET["geoLo"])) {
		$geoLo=str_replace("_"," ",$_GET["geoLo"]);
	}
	else{
			$v=false;
		}
	
	$v=true;
    $geoLa = null;
	if (isset($_GET["geoLa"])) {
		$geoLa=str_replace("_"," ",$_GET["geoLa"]);
	}
	else{
			$v=false;
		}
		
	/* puede venir Formato de longitud/latitud: [-]g.g   [-]g m.m   [-]g m s.s
	   las lleva a formato decimal */
	$geoLoR=converLatLon ($geoLo,"lon",$m);
	$geoLaR=converLatLon ($geoLa,"lat",$m);
	siErrorFuera($v);

    $geo="POINT($geoLoR $geoLaR)";
		
    $g=WKTaLonLat($geo);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">

    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>
	
<link rel="stylesheet" type="text/css" href="leaflet/leaflet.css" />
<script type="text/javascript" src="leaflet/leaflet.js"></script>
<script type="text/javascript" src="leaflet/leaflet-omnivore-v0.3.1.min.js"></script>

<link rel="stylesheet" type="text/css" href="leaflet/leaflet-ruler.css">
<script src="leaflet/leaflet-ruler.js"></script>

<link rel="stylesheet" type="text/css" href="leaflet/L.Control.MousePosition.css" />
<script type="text/javascript" src="leaflet/L.Control.MousePosition.js"></script>


<style>
kbd {
  background-color: #adadad;
}
</style>


<script>
 var mIconGru = L.icon({
	iconUrl: 'leaflet/images/marker-mi-rojoi.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
	});
	
var mIcon = mIconGru;

</script>

</head>

<body> 


    <div class="container" style="width:632px">


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">ubicaci&oacute;n del grupo en <?php echo "$fec $lib $ord $geo"?></h3>
            </div>
            <div class="panel-body" name="map" id="map" style="width:600px;height:400px">

            </div>
        </div>
		
			<div class="row">
				<div class="col-sm-3" style="margin-top:-10px;color:#444444;">
				Medidas: <kbd>Esc</kbd> o <b>doble Click</b> termina y comienza medici&oacute;n. <kbd>Esc</kbd> <kbd>Esc</kbd>: fin medici&oacute;n.
				</div>
				
				<div class="col-sm-3">
					<button type="button" class="btn btn-default" onclick="map.fitBounds(lim);">ajustar zoom</button>
				</div>
			</div>
    </div> <!-- /container -->


<script>
var map;
var puntos=[];
var lim;
var p1,p2;
var arr=[];

var mxLat=-999,mxLon=-999,mnLat=999,mnLon=999,ni=0;

var ne=0;
var ni=0;	
function initmap() {
	// set up the map
	map = new L.Map('map');

	// create the tile layer with correct attribution
	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
	var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 20, attribution: osmAttrib});		
////	map.setView(new L.LatLng(-47, -50),4);
	map.addLayer(osm);
	
	

	// para herramienta de medida
	var opt ={
	  position: 'bottomleft',
      circleMarker: {               // Leaflet circle marker options for points used in this plugin
        color: 'grey',
        radius: 1,
      },
      lineStyle: {                  // Leaflet polyline options for lines used in this plugin
        color: 'gray',
		dashArray: '1,5'
      },
      lengthUnit: {                 // You can use custom length units. Default unit is kilometers.
        display: 'km',              // This is the display value will be shown on the screen. Example: 'meters'
        decimal: 1,                 // Distance result will be fixed to this value. 
        factor: null,               // This value will be used to convert from kilometers. Example: 1000 (from kilometers to meters)  
        label: 'distancia:'           
      },
      angleUnit: {
        display: '&deg;',           // This is the display value will be shown on the screen. Example: 'Gradian'
        decimal: 1,                 // Bearing result will be fixed to this value.
        label: 'bearing:'
      }		
	}
	L.control.ruler(opt).addTo(map);
	L.control.mousePosition({position:"topright",prefix:"Lat:",separator:" Lon:"}).addTo(map);
	
}


function agregaPuntos() {
 	var ref="";
		function armaCapa(ord){
			for(var i=0; i < puntos.length; i++) {
				if (puntos[i]['orden']!=ord){
				  arr[ne]= omnivore.wkt.parse(puntos[i]['geomTex']).bindPopup("<b>fecha: " + puntos[i]['fecha'] + "</b><br>libreta:" + puntos[i]['libreta']+"<br>orden:" + puntos[i]['orden'] +"<br>refer.:" + puntos[i]['referencia'] );
					ne++;
				mxLat=Math.max(mxLat,puntos[i]['lati']);
				mnLat=Math.min(mnLat,puntos[i]['lati']);
				mxLon=Math.max(mxLon,puntos[i]['longi']);
				mnLon=Math.min(mnLon,puntos[i]['longi']);		

				}
				else {
					no=i;
					ref=puntos[i]['referencia'];
				}
			 }
		}
	
    
	var la0=<?php echo $g[1];?>;
	var lo0=<?php echo $g[0];?>;
	mxLat=la0;
	mnLat=la0;
	mxLon=lo0;
	mnLon=lo0;
	
						
	
	arr=[];
	armaCapa(<?php echo $ord;?>);
	
	// el grupo que lanzó esta mapa
	var marker = L.marker( [la0,lo0], {icon: mIcon} ).addTo(map);
	marker.bindPopup("<b>fecha: " + "<?php echo $fec?>" + "</b><br>libreta:" + "<?php echo $lib?>" +"<br>orden:" +  <?php echo $ord?> +"<br>refer.:" + ref ).addTo(map);
  
  
  
	// los otros Grupos
	var capaOtros=L.layerGroup(arr);
	

	// capas y control de capas
	var capas = {
			"otros grupos": capaOtros
		};
		
	map.addLayer(capaOtros);
	L.control.layers([], capas).addTo(map);
		
	// limite inicial
	var dl=0.1;
									
	p1 = L.latLng(mxLat-dl,mxLon+dl),
	p2 = L.latLng(mnLat+dl,mnLon+dl);
	lim= L.latLngBounds(p1,p2);
	map.fitBounds(lim);
	L.control.scale({imperial:false}).addTo(map);
 
 
 
 
}





$( document ).ready(function() {
 initmap();
 
 var xmlhttp = new XMLHttpRequest();
 xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        puntos = JSON.parse(this.responseText);
		agregaPuntos();
    }
 };
 xmlhttp.open("GET", "mapin_grupo_pun.php?fec=<?php echo $fec;?>&lib=<?php echo $lib;?>", true);
 xmlhttp.send(); 
 
 
});
</script>
  </body>
</html>