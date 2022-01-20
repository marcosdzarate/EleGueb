<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

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



<style>
kbd {
  background-color: #adadad;
}
</style>


<script>

var mIcon = L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi1.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] });
</script>

</head>

<body> 
    <div class="container" style="width:732px">


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">ubicaci&oacute;n</h3>
            </div>
            <div class="panel-body" name="map" id="map" style="width:700px;height:500px">

            </div>
        </div>
    </div> <!-- /container -->
        <div class="modal-footer">		  
			<div class="row">
				<div class="col-sm-9" style="margin-left:-30px; margin-top:-10px;color:#444444;">
				Medidas: <kbd>Esc</kbd> o <b>doble Click</b> termina y comienza medici&oacute;n. <kbd>Esc</kbd> <kbd>Esc</kbd>: fin medici&oacute;n.
				</div>
				<div class="col-sm-2">
					<button type="button" class="btn btn-default" onclick="map.fitBounds(lim);">ajustar zoom</button>
				</div>
				<div class="col-sm-1">
					<button type="button" class="btn btn-default" onclick="parent.cierroMNO()">cerrar</button>
				</div>
			</div>
        </div>


<script>
var map;
var playas=[];
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
	map.setView(new L.LatLng(-47, -50),4);
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
}


function agregaPlayasYtramos() {
 	
		function armaCapa(qTipo){
			for(var i=ni; i < playas.length; i++) {
				if (playas[i]['tipo']==qTipo){
				  arr[ne]= omnivore.wkt.parse(playas[i]['geomTex']).bindPopup("<b>ID:" + playas[i]['IDplaya'] + "</b><br>nombre:" + playas[i]['nombre'] );
					ne++;
				mxLat=Math.max(mxLat,playas[i]['lati']);
				mnLat=Math.min(mnLat,playas[i]['lati']);
				mxLon=Math.max(mxLon,playas[i]['longi']);
				mnLon=Math.min(mnLon,playas[i]['longi']);
				}
				else {
					ni=i;
					break;
				}
			 }
		}
	
	// PUNTOS
	arr=[];
	ne=0;
	L.Marker.prototype.options.icon=mIcon;
	armaCapa("PUNTO");
	var capaPuntos=L.layerGroup(arr);
	// TRAMOS
	arr=[];
	ne=0;
	//L.Marker.prototype.options.icon=mIcon;
	armaCapa("TRAMO");
	var capaTramos=L.layerGroup(arr);

	


	// capas y control de capas
	var capas = {
			"Puntos": capaPuntos,
			"Tramos": capaTramos
		};
		
	map.addLayer(capaPuntos);
	map.addLayer(capaTramos);
	L.control.layers([], capas).addTo(map);
		
	// limite inicial
	p1 = L.latLng(mxLat,mxLon),
	p2 = L.latLng(mnLat,mnLon);
	lim= L.latLngBounds(p1,p2);
	map.fitBounds(lim);
	L.control.scale({imperial:false}).addTo(map);
 
 
 
 
}





$( document ).ready(function() {
 initmap();
 
 var xmlhttp = new XMLHttpRequest();
 xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        playas = JSON.parse(this.responseText);
		agregaPlayasYtramos();
    }
 };
 xmlhttp.open("GET", "mapin_playas_pun.php", true);
 xmlhttp.send(); 
 
 
});
</script>
  </body>
</html>