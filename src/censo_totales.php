<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_validar.php';

    $queTabla="totalCenso";
	
        $tCenso = "PVALDES";
        $tEsquema = 151; 
        $tZonaCopia = 'SI';
        $tAnio = null;
        $tQparcial = null; 
        $valid=null;
		$resultParcial=null;

     
    if ( !empty($_POST)) {
        
            // para errores de validacion
            $tCensoError = null;
            $tEsquemaError = null;
            $tZonaCopiaError = null;
            $tAnioError = null;
            $tQparcialError = null;

            $eError = null;

 
           // los campos a validar
		   
			$tCenso = limpia($_POST['tCenso']);
			$tEsquema = limpia($_POST['tEsquema']); 
			$tZonaCopia = limpia($_POST['tZonaCopia']);
			$tAnio = limpia($_POST['tAnio']);
			$tQparcial = limpia($_POST['tQparcial']); 		   
			$resultParcial = limpia($_POST['resultParcial']);  //filtro de vecindarios
		
            // validacion: devuelve el error; los dos primeros parametros pasan x referencia
            //             el ultimo: true debe estar - false puede faltar
            $valid = true;

            $tCensoError = validar_tipo ($tCenso,$valid,true);
            $tEsquemaError = validar_IDesquema ($tEsquema,$valid,true);
			
            $tZonaCopiaError = validar_tZonaCopia ($tZonaCopia,$valid,true);
            $tAnioError = validar_tAnio ($tAnio,$valid,true);
            $tQparcialError = validar_tQparcial ($tQparcial,$valid,true);

			$eError = validar_IDvecindarioFiltro ($resultParcial,$valid,false) ;
			
            if ($valid) {
			
				if (isset($_POST['descarga'])) //Generate text file on the fly
				{
					$pasaArch=$tCenso."_".$tAnio."_".$tEsquema."_Copiados".$tZonaCopia."_".$tQparcial;

					/* descargas */
					$pasaSQL ="CALL `censo-totales por anio y tipo censo NO MENSUAL`('".$tCenso."', ".$tEsquema.", '".$tZonaCopia."', ".$tAnio.", '".$tQparcial."','".$resultParcial."');";
				$pasaSQL .='SELECT "gs0" as GrupoRespuesta,zz_totalcenso.* FROM zz_totalcenso;';
					$pasaSQL .='SELECT "gs" as GrupoRespuesta,zz_grupoRespuesta.* FROM zz_grupoRespuesta;';
					$pasaSQL .='SELECT "gs2" as GrupoRespuesta,zz_pivot_grupoRespuesta.* FROM zz_pivot_grupoRespuesta;';   /* para descargar*/
					//echo $sql;
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


  <!--fin para lista desplegable de vecindarios-->	
<style>
.checkbox-inline {font-size: 14px;}
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

<body class=bodycolor>
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
            <h3 class="panel-title">n&uacute;meros totales de censos</h3>
        </div>
        <br>
		<label >&nbsp;&nbsp;&nbsp;Seleccionar un esquema de vecindario</label>					

   <div class="container">		
	<div class="row">

		<div class="col-sm-5" style="height:370px;overflow:auto">   <!-- contenedor acordion esquemas -->
  
			<div id="accordion">      <!-- contenedor acordion -->

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
				
			$sql = "SELECT IDesquema, IDvecindario,tipo FROM vecindario where IDesquema<> 999 ORDER BY tipo DESC,IDesquema,NSdesde ";
			$q=mysqli_query( $con, $sql );
			if (mysqli_num_rows($q)==0) {
					echo 'No hay registros!!!. '.mysqli_error($con);
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
            
				<div class="panel-body">      <!--  -->



                    <div class="form-group ">
                        <label for="tCenso">Censo de tipo</label>


                           <select class="form-control input-sm" style="width: auto" id="tCenso" data-error="Seleccionar un elemento de la lista" required  data-error="Seleccionar un elemento de la lista" name="tCenso" onclick="if(tCenso.value!='PARCIAL'){tQparcial.value='ftotal'}" >
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
						<label for="tAnio">Totales para la temporada </label>
						<input type="text" class="form-control input-sm" style="width: 100px" id="tAnio" name="tAnio"  required
						data-pentero
						data-dmin="<?php echo CONST_tAnio_min;?>" data-dmax="<?php echo CONST_tAnio_max;?>"
						data-error="<?php echo CONST_tAnio_men;?>"
						value="<?php echo !empty($tAnio)?$tAnio:'';?>" >
						<div class="help-block with-errors"></div>
						<p id="JSErrortAnio"></p>
						<?php if (!empty($tAnioError)): ?>
							<span class="help-inline"><?php echo $tAnioError;?></span>
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
                            <select class="form-control input-sm" style="width: auto" id="tQparcial" required data-error="Seleccionar un elemento de la lista" name="tQparcial" onclick="if(tCenso.value!='PARCIAL'){this.value='ftotal'}" >
                                <option value="ftotal" <?php if ($tQparcial == "ftotal") {echo " selected";}?> >por "fecha total"</option>
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
						<div class="row">
							<div class="col-sm-2">
								<button type="submit" name=calcular class="btn btn-primary btn-sm" >calcular</button>
							</div>
							<div class="col-sm-2">
								<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
							</div>
							<div class="col-sm-4">			   
								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
							</div>
						</div>

                    </div>
					<br><br>
					
        </form>
		
		
		
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
                $sql ="CALL `censo-totales por anio y tipo censo NO MENSUAL`('".$tCenso."', ".$tEsquema.", '".$tZonaCopia."', ".$tAnio.", '".$tQparcial."','".$resultParcial."');";
                $r=muestraMultipleRS($sql,"botonno");
				if ($tCenso == "PVALDES") {
					if ($r>0) {
						graficoCensoPico($sql);}
					else{
						echo " No hay datos";
					}
				}
				if ($tCenso == "PARCIAL" or $tCenso == "MUDA") {
					graficoCensoParcial($sql,$tCenso);
				}
				
			?>                          
                        </div>
                    </div>                      
            </div>
			
<?php endif;?>


				


<script>
$('form[data-toggle="validator"]').validator({
    custom: {
		pentero: function($el) {
				var r = validatorEntero ($el);     /* en imiei.js */
				if (!r) {
					return $el.data("error");
				}
					
			}
  }
});
</script>



<script src="js/w3.js"></script> 
 
<script>
w3.includeHTML();

</script> 


</body>
</html>