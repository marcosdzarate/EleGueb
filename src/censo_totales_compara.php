<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_validar.php';

    $queTabla="totalCenso";
	
        $tCenso = "PVALDES";
        $tEsquema = 151; 
        $tZonaCopia = 'SI';
        $tQparcial = null; 
        $valid=null;
		$resultParcial=null;


		
    if ( !empty($_POST)) {
        
            // para errores de validacion
            $tCensoError = null;
            $tEsquemaError = null;
            $tZonaCopiaError = null;
            $tQparcialError = null;

            $eError = null;

 
           // los campos a validar
		   
			$tCenso = limpia($_POST['tCenso']);
			$tEsquema = limpia($_POST['tEsquema']); 
			$tZonaCopia = limpia($_POST['tZonaCopia']);
			$tQparcial = limpia($_POST['tQparcial']); 		   
			$resultParcial = limpia($_POST['resultParcial']);  //filtro de vecindarios

			
            // validacion: devuelve el error; los dos primeros parametros pasan x referencia
            //             el ultimo: true debe estar - false puede faltar
            $valid = true;

            $tCensoError = validar_tipo ($tCenso,$valid,true);
            $tEsquemaError = validar_IDesquema ($tEsquema,$valid,true);
			
            $tZonaCopiaError = validar_tZonaCopia ($tZonaCopia,$valid,true);
            $tQparcialError = validar_tQparcial ($tQparcial,$valid,true);

			$eError = validar_IDvecindarioFiltro ($resultParcial,$valid,false) ;
			
            if ($valid) {
				
				if (isset($_POST['descarga'])) //Generate text file on the fly
				{
		// Sleep 5 seconds...
		sleep(5);

		// Cookie will be expire after 20 seconds...
		setcookie("downloadToken", $_POST["downloadToken"], time() + 20, "");					
					$pasaArch=$tCenso."_".$tEsquema."_Copiados".$tZonaCopia."_".$tQparcial."_compara";

					/* descargas */
					$pasaSQL ="CALL `censo-compara NO MENSUAL`('".$tCenso."', ".$tEsquema.", '".$tZonaCopia."', '".$tQparcial."','SI','".$resultParcial."');";
				$pasaSQL .='SELECT "gs0" as GrupoRespuesta,zz_totalcenso.* FROM zz_totalcenso;';
				$pasaSQL .='SELECT "gs1" as GrupoRespuesta,tt_gruporespuesta.* FROM tt_gruporespuesta ORDER BY fecha,libreta,orden,categoria;';
				
					XlsXOnDeFlai($pasaSQL,$pasaArch);
				}
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


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>

        <link rel="stylesheet" href="login/style/main.css">
	
    <link   href="css/miSideBar.css" rel="stylesheet">
	
  <!--para lista de vecindarios  -->
  <link rel="stylesheet" href="UI2/jquery-ui.css">
  <script src="UI2/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#accordion" ).accordion({
      collapsible: true,
	  heightStyle: "content"
    });
	$( "#accordion" ).accordion({
      active: false
     });
	$('.ah3').click(function(){
    //do something on click0
			var result = $( "#resultParcial" ); 
			result.val("");
			$( "ol.oLista li").removeClass("ui-selected");
	   var inh = this.innerHTML.toUpperCase();
	   //alert(inh);
	   var n = inh.indexOf('</SPAN>')+7;
	   var res1 = inh.substr(n, 150);    //esquema y tipo
	   var n = res1.indexOf('-');
	   var rTip = res1.substr(n+1, 7);  //tipo
	   var rEsq = res1.substr(0,n);      //esquema
	   if (document.getElementById("tCenso").value=="PVALDES" || rTip=="PVALDES")  {
			document.getElementById("tCenso").value = rTip;
	   }
	   document.getElementById("tEsquema").value = rEsq;
	   if (rTip!="PARCIAL") {
			document.getElementById("tQparcial").value = "ftotal";				
			}
     });
  } );
 </script>



<script type="text/javascript">
/* para barra de progreso */
function staBarraProgreso() {
    downloadToken = new Date().getTime();
    $("[name='downloadToken']").val(downloadToken);
    $("form").trigger("submit");
    timer();
}

function timer() {
	var limite = 1000;
    var attempts = limite; /* As you required */
    var downloadTimer = window.setInterval(function () {
        var token = getCookie("downloadToken");
        attempts--;
	    
		var b=document.getElementById("barraProgreso");
        if (token == downloadToken || attempts == 0) {
			b.style.display="none";
            window.clearInterval(downloadTimer);
        }
        else {
document.getElementById("barraProgresoTxt").innerHTML=(limite-attempts+1)+" momentitos";
			b.style.display="block";

        }
    }, 1000);
}

function parse(str) {
    var obj = {};
    var pairs = str.split(/ *; */);
    var pair;
    if ('' == pairs[0]) return obj;
    for (var i = 0; i < pairs.length; ++i) {
        pair = pairs[i].split('=');
        obj[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
    }
    return obj;
}

function getCookie(name) {
    var parts = parse(document.cookie);
    return parts[name] === undefined ? null : parts[name];
}
/* barra de progreso fin */
</script>

  <!--fin para lista desplegable de vecindarios-->	
<style>
.radio-inline {font-size: 14px;}
</style>


<!-- para seleccionar/deseleccionar IDvecindario de un esquema y formar un filtro de vecindarios -->
  <style>
  #feedback { font-size: 14px; }
  #selectable .ui-selecting { background: #FECA40; }
  #selectable .ui-selected { background: #F39814; color: white; }
  #selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
  #selectable li { margin: 0px; padding: 0; font-size: 14px; height: 22px; }
  </style>
 
  
  <script> 
  
  function xFiltro(tsele) {
	var result = $( "#resultParcial" );
	result.val("");
	$( ".ui-selected", tsele ).each(function() {
		var index = $( "ol.oLista li" ).index( this );
		result.val(result.val()+',"'+ $("#li"+( index + 1 )).text()+'"');
	});	  
  }
		   
         $(function() {
            $( "ol.oLista" ).selectable({
				selected: function() {
					xFiltro(this);
				},
				unselecting: function () {
					xFiltro(this)
				}
            });
         });
   </script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
   
</head>

<body class=bodycolor >
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_censo.html"></div>

	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

  <div class="container well" style="width:1000px">

    <div class="panel panel-azulino">
        <div class="panel-heading">
            <h3 class="panel-title">comparando censos en el tiempo</h3>
        </div>
        <br>

		
   			<div class="progress center-block" id="barraProgreso" style="width:80%;display:none;height:auto">
				<div class="progress-bar progress-bar-striped progress-bar-warning active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height:30px;padding:10px;width:100%;font-size:15px" id=barraProgresoTxt>
				 calculando...
				</div>
			</div>

			
		<label >&nbsp;&nbsp;&nbsp;Seleccionar un esquema de vecindario</label>					

   <div class="container">		
	<div class="row">

		<div class="col-sm-5" style="height:370px;overflow:auto">   <!-- contenedor acordion esquemas -->
  
			<div id="accordion" >      <!-- contenedor acordion -->

        <?php 
			/*$pdo = Database::connect();*/
			$con = mysqli_connect( elServer, elUser, elPassword, elDB, elPort);
			if ( !$con ) 
				{
					echo( 'No se puede conectar con el server' );
					exit;
				}
				
				
			/* mensajes en castellano*/
			if(!mysqli_query( $con, "SET lc_messages=es_AR" ))
			{
				echo("Error lc_messages castellano. " );
				/* no doy exit! que siga.... */
			}

			
			/* cargo UTF8 */
			 if (!mysqli_set_charset($con, "utf8")) {
				echo("Error cargando el conjunto de caracteres utf8" );
				/* no doy exit! que siga.... */
			}				
				
			$sql = "SELECT IDesquema, IDvecindario,tipo FROM vecindario where IDesquema<>999 ORDER BY tipo DESC,IDesquema,NSdesde ";
			$q=mysqli_query( $con, $sql );
			if (mysqli_num_rows($q)==0) {
					echo 'No hay registros!!! '.mysqli_error($con);
					mysqli_close($con);
					exit;
					}
			$pri=true;
			$xIDes="bli";
			$liN = 0;
			while ( $aPla = mysqli_fetch_array( $q )) {
				if ($xIDes<>$aPla['IDesquema'] and $pri==false){
					echo "\n".'					</ol>';					
				}
				if ($xIDes<>$aPla['IDesquema']){
					$pri=false;
					$xIDes = htmlspecialchars($aPla['IDesquema']);
					$xtipo = htmlspecialchars($aPla['tipo']);
					if ($xtipo=="PARCIAL"){
						$xtipo .="/MUDA";
					}
					echo "\n"."				<h3 class='ah3' id=m$xIDes >$xIDes-$xtipo</h3>"."\n";
					echo "					<ol id='selectable' class='oLista' name=o$xIDes>";
				}
					$liN+=1;
					echo "      
						<li class='ui-widget-content' id=li$liN>".htmlspecialchars($aPla['IDvecindario']).'</li>';
			}
			echo "\n"."			    </ol>";
			mysqli_close($con);
        ?>			
			
			

			</div>    <!-- fin contenedor acordion -->
		</div>    <!-- fin contenedor acordion esquemas -->



		<div class="col-sm-7" >
		
						  

		
			<form data-toggle="validator" id="CRtotalCenso" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

			<input type="hidden" name="downloadToken"/>		
            
				<div class="panel-body">      <!--  -->



                    <div class="form-group ">
                        <label for="tCenso">Censo de tipo</label>


                           <select class="form-control input-sm" style="width: auto" id="tCenso" data-error="Seleccionar un elemento de la lista" required  data-error="Seleccionar un elemento de la lista" name="tCenso" onclick="if(tCenso.value!='PARCIAL'){tQparcial.value='ftotal'}">
                                <option value="" <?php if ($tCenso == "") {echo " selected";}?> ></option>
                                <option value="PARCIAL" <?php if ($tCenso == "PARCIAL") {echo " selected";}?> onclick="tEsquema.value=3;m3.click()" >PARCIAL</option>
                                <option value="PVALDES" <?php if ($tCenso == "PVALDES") {echo " selected";}?> onclick="tEsquema.value=151;m151.click()" >PVALDES</option>
                               <option value="MUDA" <?php if ($tCenso == "MUDA") {echo " selected";}?> onclick="tEsquema.value=3;m3.click()" >MUDA</option> 
                            </select>

						
						<div class="help-block with-errors"></div>
						<p id="JSErrortCenso"></p>
						<?php if (!empty($tCensoError)): ?>
							<span class="help-inline"><?php echo $tCensoError;?></span>
						<?php endif; ?>


                    </div>

					
                        
                        					
					
					
                    <div class="form-group ">
                        <label for="tEsquema">Esquema de vecindario</label>					
						<input type="text" class="form-control input-sm" style="width:100px" id="tEsquema" name="tEsquema"  readonly required 
						value="<?php echo !empty($tEsquema)?$tEsquema:'';?>" > 

                        <div class="help-block with-errors"></div>
						<p id="JSErrortEsquema"></p>
						<?php if (!empty($tEsquemaError)): ?>
							<span class="help-inline"><?php echo $tEsquemaError;?></span>
						<?php endif; ?>
				    </div>

						<input type="hidden" id="resultParcial" name="resultParcial" class="resultarea" value='<?php echo !empty($resultParcial)?$resultParcial:'';?>' style="width:400px">					
				 
                    <div class="form-group ">
                        <label for="tZonaCopia">Se incluyen sectores copiados en los totales?</label>
                            <select class="form-control input-sm" style="width: auto" id="tZonaCopia" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="tZonaCopia">
                                <option value="" <?php if ($tZonaCopia == "") {echo " selected";}?> ></option>
                                <option value="SI" <?php if ($tZonaCopia == "SI") {echo " selected";}?> >SI</option>
                                <option value="NO" <?php if ($tZonaCopia == "NO") {echo " selected";}?> >NO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortZonaCopia"></p>
                            <?php if (!empty($tZonaCopiaError)): ?>
                                <span class="help-inline"><?php echo $tZonaCopiaError;?></span>
                            <?php endif; ?>
                    </div>

					



                    <div class="form-group ">
                        <label for="tQparcial">Solo para censo PARCIAL: c&oacute;mo totalizar?</label>
                            <select class="form-control input-sm" style="width: auto" id="tQparcial" onclick="if(tCenso.value!='PARCIAL'){this.value='ftotal'}" required data-error="Seleccionar un elemento de la lista" name="tQparcial">
                                <option value="ftotal" <?php if ($tQparcial == "ftotal") {echo " selected";}?> >por "fecha de total"</option>
                                <option value="semana" <?php if ($tQparcial == "semana") {echo " selected";}?> >por total semanal calculado</option>
                                <option value="DMS" <?php if ($tQparcial == "DMS") {echo " selected";}?> >por total semanal registrado DMS (1991 a 1995)</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortQparcial"></p>
                            <?php if (!empty($tQparcialError)): ?>
                                <span class="help-inline"><?php echo $tQparcialError;?></span>
                            <?php endif; ?>
                    </div>
				
				
				
				</div>

         
                     <div class="form-actions">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name=calcular onclick=staBarraProgreso() class="btn btn-primary btn-sm">calcular</button>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name=descarga  onclick=staBarraProgreso()  class="btn btn-warning btn-sm">descargar</button>
			   
						
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                    </div>
					<br><br>
							
        </form>
		

			
			
			


		<br>
		<br><br>
		<br><br>
		
    </div> 
    </div> 
    </div> 
	
    </div> 
	

	
	
  </div> <!-- /container -->
<!-- RESULTADOS -->  
<?php if ($valid and isset($_POST['calcular'])) :?> 

            <div class="panel-body">

                    <div class="panel panel-info" >
                        <div class="panel-heading">Los totales</div>
                        <div class="panel-body" style="overflow:auto">            

			<?php
			/* Totales*/ 
				$sql ="CALL `censo-compara NO MENSUAL`('".$tCenso."', ".$tEsquema.", '".$tZonaCopia."', '".$tQparcial."','NO','".$resultParcial."');";
				if ($tCenso == "PVALDES") {
					graficoCensoComparaPico($sql);
				}
				if ($tCenso == "PARCIAL"  or $tCenso == "MUDA") {
					graficoCensoComparaParcial($sql,$tCenso);
				}
				

			?>                          

                        </div>
                    </div>                      
            </div>

<?php endif;?>

<script src="js/w3.js"></script> 
 
<script>
w3.includeHTML();

</script> 

<script> 

  
  function CambiaCate(optVal){
		var cate=Number(optVal)+4;
		var miTab = document.getElementById("tablaResu");
		var enc = miTab.rows[0].cells[cate].innerHTML;		//la variable
	  
		var p1=myChart.options.title.text.indexOf('"')+1;		//actualiza titulo de grafico
		var p2=myChart.options.title.text.lastIndexOf('"');
		myChart.options.title.text = myChart.options.title.text.substr(0,p1)+enc+myChart.options.title.text.substr(p2,25);
	  
		var queDS = -1;
		var antDS = "";		//el vecindario 
		var iy = -1;
		for (i = 1; i < miTab.rows.length; i++) {				// desde fila 1... datos
		    if (antDS != miTab.rows[i].cells[1].innerHTML) {
				antDS =  miTab.rows[i].cells[1].innerHTML;
				queDS += 1;
				iy = -1;
			}
			iy +=1;
			eval("dDS"+queDS)[iy]["y"]= miTab.rows[i].cells[cate].innerHTML ;		
		} 		  
	  
	  myChart.update();
  }
  
	function CambiaCateAnio(optVal){
		var cate=Number(optVal)+4;
		var miTab = document.getElementById("tablaResu");
		var enc = miTab.rows[0].cells[cate].innerHTML;		//la categoria
	  
		var p1=myChart.options.title.text.indexOf('"')+1;		//actualiza titulo de grafico
		var p2=myChart.options.title.text.lastIndexOf('"');
		myChart.options.title.text = myChart.options.title.text.substr(0,p1)+enc+myChart.options.title.text.substr(p2,25);
	  
		var queDS = -1;
		var antDS = "";		//el año 
		var iy = -1;
		for (i = 1; i < miTab.rows.length; i++) {				// desde fila 1... datos
		    if (antDS != miTab.rows[i].cells[3].innerHTML) {
				antDS =  miTab.rows[i].cells[3].innerHTML;
				queDS += 1;
				iy = -1;
			}
			iy +=1;
			eval("dDS"+queDS)[iy]["y"]= miTab.rows[i].cells[cate].innerHTML ;		
		} 		  
	  
	  myChart.update();
  }  
</script>
</body>
</html>