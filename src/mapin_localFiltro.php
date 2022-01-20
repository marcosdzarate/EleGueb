<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';  

	
	$v=true;

    $fil = null;
	if (isset($_GET["fil"])) {
		$fil=$_GET["fil"];
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
 var mIconVia = L.icon({
	iconUrl: 'leaflet/images/marker-mi-vi1.png',
    iconSize: [10, 10],
    popupAnchor: [-3, -7] 
	});

</script>

</head>

<body> 
    <div class="container" style="width:732px">


        <div class="panel panel-grisDark">
            <div class="panel-heading">
                <h3 class="panel-title">una localizaci&oacute;n por d&iacute;a</h3>
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
						en viaje.
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
 
 mIcon=mIconCos;

 for(var i=0; i < puntos.length; i++) {
  if(puntos[i]['disCentro']<disCen) {
	  mIcon=mIconCos;
  }
  else {
	  mIcon=mIconVia;
	  }


    

  var marker = L.marker( [puntos[i]['lat'], puntos[i]['lon']], {icon: mIcon} ).addTo(map);
  marker.bindPopup( "<b>viajeID:" + puntos[i]['viajeID'] + "<br>instrumento: " + puntos[i]['ptt']
                                                     + "<br>fecha: " + puntos[i]['fecha']+"<br>marca: " + puntos[i]['marcas']);
 } 
   

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
 xmlhttp.open("GET", "mapin_localFiltro_pun.php?fil=<?php echo $fil?>", true);
 xmlhttp.send(); 
 
 
});
</script>
  </body>
</html>