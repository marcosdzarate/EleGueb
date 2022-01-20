<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

	
	$v=true;

    $ID = null;
	if (isset($_GET["ID"])) {
		$ID=$_GET["ID"];
		$m = validar_ID ($ID,$v,true);
	}
	else{
			$v=false;
		}
		
	siErrorFuera($v);

    $mar = null;
	if (isset($_GET["mar"])) {
		$mar=$_GET["mar"];
		if (vacio($mar)) {
			$v=false;
		}
		else	
			if (preg_match("/"."^[0-9A-ZÑ\-\/_]{1,60}$"."/", $mar, $a) <> 1) {
				$v=false;
			}
	}
	else{
			$v=false;
		}
	siErrorFuera($v);
	
    $ptt = null;
	if (isset($_GET["ptt"])) {
		$ptt=$_GET["ptt"];
		if($ptt<>0){
		  $m = validar_identificacion ($ptt,$v,true);
		}
	}
	else{
			$v=false;
		}
	siErrorFuera($v);

	$mptt=" // viajeID=$ID ";
	if($ptt<>0){
		$mptt.=" // instrumento=".$ptt;
	}
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
 var disCen=<?php echo CENTRO_dis; ?>;  // para determinar puntos costeros
 
 var mIconCos = L.icon({
	iconUrl: 'leaflet/images/marker-mi-cos.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7],
	});
 var maxIcon = 3;
 var mIconVia = [L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi1.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] }),
	L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi2.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] }),
	L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi3.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] }),
	L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi4.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] })
	];

</script>

</head>

<body> 
    <div class="container" style="width:732px">


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo str_replace("_"," - ",$mar).$mptt ?></h3>
            </div>
            <div class="panel-body" name="map" id="map" style="width:700px;height:500px">

            </div>
        </div>
    </div> <!-- /container -->
        <div class="modal-footer">		  
			<div class="row">
				<div class="col-sm-9" style="margin-left:-30px; margin-top:-10px;color:#444444;">
				Medidas: <kbd>Esc</kbd> o <b>doble Click</b> termina y comienza medici&oacute;n. <kbd>Esc</kbd> <kbd>Esc</kbd>: fin medici&oacute;n. <br>
				Puntos: <img src="leaflet/images/marker-mi-cos.png" style="width:10px;" class="img-rounded" alt="gris">, vecino a PV;
						<img src="leaflet/images/marker-mi-vi1.png" style="width:10px;" class="img-rounded" alt="azul">
						<img src="leaflet/images/marker-mi-vi2.png" style="width:10px;" class="img-rounded" alt="verde">
						<img src="leaflet/images/marker-mi-vi3.png" style="width:10px;" class="img-rounded" alt="rojo">
						<img src="leaflet/images/marker-mi-vi4.png" style="width:10px;" class="img-rounded" alt="amarillo">, 
						secuencia de puntos fuera de PV.
				</div>
				<div class="col-sm-2">
					<button type="button" class="btn btn-default" onclick="map.fitBounds(linea.getBounds());">ajustar zoom</button>
				</div>
				<div class="col-sm-1">
					<button type="button" class="btn btn-default" onclick="parent.cierroMNO()">cerrar</button>
				</div>
			</div>
        </div>


<script>
var map;
var linea;
var puntos=[];

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
	L.control.mousePosition({position:"topright",prefix:"Lat:",separator:" Lon:"}).addTo(map);
	
}


function agregaPuntos() {
 var mIcon=null;
 var latlngs=[];
 
 var nv=-1;
 var dCenlAnt=-1;
 mIcon=mIconCos;

 for(var i=0; i < puntos.length; i++) {
  if(puntos[i]['disCentro']<disCen) {
	  mIcon=mIconCos;
  }
  else {
	  if (dCenlAnt<disCen) nv=nv+1;	  // otro viaje
	  if (nv>maxIcon)nv=0;
	  mIcon=mIconVia[nv];
	  }


  dCenlAnt=puntos[i]['disCentro'];
    

  var marker = L.marker( [puntos[i]['lat'], puntos[i]['lon']], {icon: mIcon} ).addTo(map);
  marker.bindPopup( "<b>fecha:" + puntos[i]['fecha'] + "</b><br>hora:" + puntos[i]['hora'] 
                                                     + "<br>LQ: " + puntos[i]['locQuality']
                                                     + "<br>km a PDelgada: " + puntos[i]['distanciaPDelgada']
                                                     + "<br>km a la salida: " + puntos[i]['distanciaSalida']);
  latlngs[i]=[puntos[i]['lat'], puntos[i]['lon']];
 } 
   

linea = L.polyline(latlngs, {color: '#3c77c9',weight:2}).addTo(map);
// zoom the map to the polyline
map.fitBounds(linea.getBounds());
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
 xmlhttp.open("GET", "mapin_local_pun.php?ID=<?php echo $ID?>&pt=<?php echo $ptt?>", true);
 xmlhttp.send(); 
 
 
});
</script>
  </body>
</html>