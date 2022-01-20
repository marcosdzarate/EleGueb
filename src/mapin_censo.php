<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  
	
	
	$v=true;
    $fec = null;  //esta es fechaTotal
	if (isset($_GET["fec"])) {
		$fec=$_GET["fec"];
		$m = validar_fecha ($fec,$v,true);
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
		

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
	iconUrl: 'leaflet/images/marker-icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
	});

var mIconAzul = mIconGru;

 var mIconGru = L.icon({
	iconUrl: 'leaflet/images/marker-mi-rojoi.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
	});
	
var mIconRojo = mIconGru;



</script>

</head>

<body> 


    <div class="container" style="width:732px">


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">todos los grupos de censo <?php echo $_SESSION['tipocen'];?> entre fechas <?php echo $_SESSION['entreFechas'];?></h3>
            </div>
            <div class="panel-body" name="map" id="map" style="width:700px;height:500px">

            </div>
        </div>
		
			<div class="row">
				<div class="col-sm-9" style="margin-top:-10px;color:#444444;">
				Medidas: <kbd>Esc</kbd> o <b>doble Click</b> termina y comienza medici&oacute;n. <kbd>Esc</kbd> <kbd>Esc</kbd>: fin medici&oacute;n.
				</div>
				
				<div class="col-sm-3">
					<button type="button" class="btn btn-default" onclick="map.fitBounds(lim,{padding:[10,10]});">ajustar zoom</button>
					<button type="button" class="btn btn-default" onclick="parent.cierroMNO();">cerrar</button>
				</div>
			</div>
    </div> <!-- /container -->


<script>
var map;
var puntos=[];
var lim;
var p1,p2;
var arr=[];

var ne=0;
var ni=0;	
function initmap() {
	// set up the map
	map = new L.Map('map');

	// create the tile layer with correct attribution
	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	var osmAttrib='Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
	var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 20, attribution: osmAttrib});		
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


 	
function armaCapa(){

	for(var i=0; i < puntos.length; i++) {
		
		  Lma = omnivore.wkt.parse(puntos[i]['geomTex']);		  
		  arr[ne]= Lma.bindPopup("<b>fecha: " + puntos[i]['fecha'] + "</b><br>libreta:" + puntos[i]['libreta']+"<br>orden:" + puntos[i]['orden'] +"<br>refer.:" + puntos[i]['referencia'] );
		  ne++;
	}

}
	
    

$( document ).ready(function() {
 initmap();
 
 var xmlhttp = new XMLHttpRequest();
 xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        puntos = JSON.parse(this.responseText);
		ne = 0;
		arr.length=0;
		    L.Marker.prototype.options.icon=mIconAzul;		//icono azul
		armaCapa();  

		// capa de puntos Georef
		var capaG=L.layerGroup(arr);
		map.addLayer(capaG);		
		
    }
 };
 xmlhttp.open("GET", "mapin_censo_pun.php?fec=<?php echo $fec;?>", true);
 xmlhttp.send(); 
 
 
 /* para sectores copiados/faltantes */
 var xmlhttpFC = new XMLHttpRequest();
 xmlhttpFC.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        puntos = JSON.parse(this.responseText);
		ne = 0;
		arr.length=0;
		    L.Marker.prototype.options.icon=mIconRojo;		//icono rojo
		armaCapa();  
		// capa de puntos Georef
		var capaGFC=L.layerGroup(arr);
		map.addLayer(capaGFC);		
		
    }
 };
 xmlhttpFC.open("GET", "mapin_censo_punfal.php?fec=<?php echo $fec;?>", true);
 xmlhttpFC.send(); 

 	// limite inicial
	p1 = L.latLng(-41.9,-63.5),
	p2 = L.latLng(-43.65,-65.5);
	lim= L.latLngBounds(p1,p2);
	map.fitBounds(lim);
	L.control.scale({imperial:false}).addTo(map);
 
 
 
});
</script>
  </body>
</html>