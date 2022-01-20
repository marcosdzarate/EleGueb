<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'grupo';
	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/
        $fecha = null;
        $libreta = null;
        $orden = null;
        $playa = null;
        $referencia = null;
        $desde = null;
        $tipoPlaya = null;
        $geomTex = null;
        $comentario = null;
        $longi = null;
        $lati = null;
        $longiH = null;
        $latiH = null;
        $distancia = null;
        
	$v=true;
	if (isset($_GET["fecha"])) {
		$fecha=$_GET["fecha"];
		$m = validar_fecha ($fecha,$v,true);
	}
	else{
		if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);


	if (isset($_GET["fecha"])) {
		/* intento de eliminar censo fuera del flujo programado */
		siErrorFuera (editaCenso($fecha));
	}
	
	
	
	if (isset($_GET["libreta"])) {
		$libreta=$_GET["libreta"];
		$m = validar_libreta ($libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);

    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $libretaError = null;
        $ordenError = null;
        $playaError = null;
        $referenciaError = null;
        $desdeError = null;
        $tipoPlayaError = null;
        $geomTexError = null;
        $comentarioError = null;
        $longiError = null;
        $latiError = null;
        $longiHError = null;
        $latiHError = null;
        $distanciaError = null;

		
        $eError = null;

        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $orden = limpia($_POST['orden']);
        $playa = limpia($_POST['playa']);
        $referencia = limpia($_POST['referencia']);
        $desde = limpia($_POST['desde']);
        $tipoPlaya = limpia($_POST['tipoPlaya']);
        $geomTex = limpia($_POST['geomTex']);
        $comentario =  limpia($_POST['comentario']);
 
        $distancia =  limpia($_POST['distancia']);
		$longi = limpia($_POST['longi']);
        $lati = limpia($_POST['lati']);
        $longiH = limpia($_POST['longiH']);
        $latiH = limpia($_POST['latiH']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta ($libreta,$valid,true);
        $ordenError = validar_orden ($orden,$valid,true);
		$playaError = "";
        if (!isset($_POST['segunLonLat'])) {
			$playaError = validar_playa ($playa,$valid,true);
		}
        $referenciaError = validar_referencia ($referencia,$valid,true);
        $desdeError = validar_desde ($desde,$valid,false);
        $tipoPlayaError = validar_tipoPlaya ($tipoPlaya,$valid,false);
        $geomTexError = validar_geomTex ($geomTex,$valid,false);
        $distanciaError = validar_distancia ($distancia,$valid,false);

//validacion y conversión de latitud y longitud
/* converLatLon devuelve true si el string está vacío   */
/* devuelve false y $...Error si hay error              */
/* sino, devuelve la entrada convertida a grados decimal*/
/*    y en $laPrimerVariable la cadena de entrada limpia*/

		$hayDesde=false;
		$hayHasta=false;
		$blaDesde=false;
		$blaHasta=false;
		$cLati=converLatLon ($lati,"lat",$latiError);
		$cLongi=converLatLon ($longi,"lon",$longiError);
		if ($cLati===false or $cLongi===false) {
			//hay error
			$valid=false;
		}
		else {
			if ($cLati===true and $cLongi===true){
				$blaDesde=true;
			}
			else {
				if ($cLati===true){
					$valid=false;
					$latiError="No puede estar en blanco si informa Longitud.";
				} 
				else {
					if ($cLongi===true){
						$valid=false;
						$longiError="No puede estar en blanco si informa Latitud.";
					} 
					else {
						$hayDesde=true;  
					}
				}
			}
		}

		$cLatiH=converLatLon ($latiH,"lat",$latiHError);
		$cLongiH=converLatLon ($longiH,"lon",$longiHError);
		if ($cLatiH===false or $cLongiH===false) {
			//hay error
			$valid=false;
		}
		else {
			if ($cLatiH===true and $cLongiH===true){
				$blaHasta=true;
			}
			else {
				if ($cLatiH===true){
					$valid=false;
					$latiHError="No puede estar en blanco si informa Longitud.";
				} 
				else {
					if ($cLongiH===true){
						$valid=false;
						$longiHError="No puede estar en blanco si informa Latitud.";
					} 
					else {
						$hayHasta=true;  
					}
				}
			}
		}		

		if ($hayHasta and $blaDesde) {
			$valid=false;
			$longiError="No puede estar en blanco si informa hasta.";
			$latiError="No puede estar en blanco si informa hasta.";
		}
		
		if ($blaDesde and $blaHasta){
				$geomTex="";
			}

		if ($hayDesde and $blaHasta) {
			$geomTex="POINT(".$cLongi." ".$cLati.")";
		}
		if ($hayDesde and $hayHasta) {
			$geomTex= "LINESTRING(".$cLongi." ".$cLati.",".$cLongiH." ".$cLatiH.")";		
		}
		$geomTexBuscar="POINT(".$cLongi." ".$cLati.")"; /* se buscar x latitud y longitud del primer punto solo */
		
    // validacion entre campos
        $eError = validarForm_grupo ($fecha,$libreta,$orden,$playa,$referencia,$desde,$tipoPlaya,$geomTex,$comentario,$longi,$lati,$longiH,$latiH,$distancia,$valid);


        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLdesde = ($desde == '' ? NULL : $desde);
            $SQLtipoPlaya = ($tipoPlaya == '' ? NULL : $tipoPlaya);
            $SQLgeomTex = ($geomTex == '' ? NULL : $geomTex);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLlati = ($lati == '' ? NULL : $lati);
            $SQLlongi = ($longi == '' ? NULL : $longi);
            $SQLlatiH = ($latiH == '' ? NULL : $latiH);
            $SQLlongiH = ($longiH == '' ? NULL : $longiH);
            $SQLdistancia = ($distancia == '' ? NULL : $distancia);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO grupo (fecha,libreta,orden,playa,referencia,desde,tipoPlaya,geomTex,comentario,conformadoOK,lati,longi,latiH,longiH,distancia) values(?,?,?,?,?,?,?,?,?,VerificaConformacionGrupo(?,?,?,?),?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$orden,$playa,$referencia,$SQLdesde,$SQLtipoPlaya,$SQLgeomTex,$SQLcomentario,$fecha,$libreta,$orden,$referencia,$SQLlati,$SQLlongi,$SQLlatiH,$SQLlongiH,$SQLdistancia));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                $guardado='ok';
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: grupo_index.php?fecha='.$fecha.'&libreta='.$libreta);*/
                /*exit;*/
            }



        }
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


<script>
function haceAlgo(cplaya,tt) {
	/*onclick="haceAlgo( 'LA IRMA-I.PINGÜINO = #IRPI','tramo' )*/
	var n = cplaya.indexOf("=")+1;
	document.getElementById("playa").value = cplaya.substr(n,6).trim();	
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
	
</head>

<body>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">grupo - datos comunes</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRgrupo" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

		<div class=row>	
			
			<div class="col-sm-5">			
                    <div class="form-group ">
                        <label for="fecha">Fecha del censo</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>

                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
                            <?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>
                    </div>
            </div>
			<div class="col-sm-4">			

                    <div class="form-group ">
                        <label for="libreta">Libreta</label>
                            <input type="text" class="form-control input-sm" style="width: 45px" id="libreta" name="libreta"  oninput="mayus(this);" pattern="<?php echo PATRON_libreta;?>" maxlength="3" required readonly data-error="<?php echo PATRON_libreta_men;?>" value="<?php echo !empty($libreta)?$libreta:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlibreta"></p>
                            <?php if (!empty($libretaError)): ?>
                                <span class="help-inline"><?php echo $libretaError;?></span>
                            <?php endif; ?>
                    </div>
            </div>
			<div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="orden">N&uacute;mero de orden</label>
                            <input type="number" class="form-control input-sm" style="width:90px" id="orden" name="orden" data-pentero data-dmin="<?php echo CONST_ordenRec_min;?>" data-dmax="<?php echo CONST_ordenRec_max;?>" required  data-error="<?php echo CONST_ordenRec_men;?>" value="<?php echo !empty($orden)?$orden:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrororden"></p>
                            <?php if (!empty($ordenError)): ?>
                                <span class="help-inline"><?php echo $ordenError;?></span>
                            <?php endif; ?>
                    </div>
			</div>
		</div>
		 
		<div class=row>
			<div class="col-sm-4">			
 
                  <div class="form-group ">
                        <label for="desde">Desde d&oacute;nde se hace el censo</label>
                            <select class="form-control input-sm" style="width: auto" id="desde" data-error="Seleccionar un elemento de la lista" name="desde">
                                <option value="" <?php if ($desde == "") {echo " selected";}?> ></option>
                                <option value="BAJO" <?php if ($desde == "BAJO") {echo " selected";}?> >BAJO-desde la playa</option>
                                <option value="ALTO" <?php if ($desde == "ALTO") {echo " selected";}?> >ALTO-desde el acantilado</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrordesde"></p>
                            <?php if (!empty($desdeError)): ?>
                                <span class="help-inline"><?php echo $desdeError;?></span>
                            <?php endif; ?>
                    </div>
            </div>
			<div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="tipoPlaya">Tipo de playa/sustrato</label>
                            <select class="form-control input-sm" style="width: auto" id="tipoPlaya" data-error="Seleccionar un elemento de la lista"  name="tipoPlaya">
                                <option value="" <?php if ($tipoPlaya == "") {echo " selected";}?> ></option>
                                <option value="AGUA" <?php if ($tipoPlaya == "AGUA") {echo " selected";}?> >AGUA</option>
                                <option value="ARENA" <?php if ($tipoPlaya == "ARENA") {echo " selected";}?> >ARENA</option>
                                <option value="CANTO RODADO" <?php if ($tipoPlaya == "CANTO RODADO") {echo " selected";}?> >CANTO RODADO</option>
                                <option value="MEZCLA" <?php if ($tipoPlaya == "MEZCLA") {echo " selected";}?> >MEZCLA</option>
                                <option value="RESTINGA" <?php if ($tipoPlaya == "RESTINGA") {echo " selected";}?> >RESTINGA</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipoPlaya"></p>
                            <?php if (!empty($tipoPlayaError)): ?>
                                <span class="help-inline"><?php echo $tipoPlayaError;?></span>
                            <?php endif; ?>
                    </div>
              </div>
			<div class="col-sm-4">			
                    <div class="form-group ">
                        <label for="referencia">Referencia</label>
                            <select class="form-control input-sm" style="width: auto" id="referencia" required  data-error="Seleccionar un elemento de la lista" name="referencia">
						<?php if($_SESSION['tipocen']<>'MUDA'): ?>
                                <option value="" <?php if ($referencia == "") {echo " selected";}?> ></option>
                                <option value="HAREN" <?php if ($referencia == "HAREN") {echo " selected";}?> >HAREN</option>
                                <option value="GRUPO DE HARENES" <?php if ($referencia == "GRUPO DE HARENES") {echo " selected";}?> >GRUPO DE HARENES</option>
                                <option value="HAREN SIN ALFA" <?php if ($referencia == "HAREN SIN ALFA") {echo " selected";}?> >HAREN SIN ALFA</option>
                                <option value="PAREJA SOLITARIA" <?php if ($referencia == "PAREJA SOLITARIA") {echo " selected";}?> >PAREJA SOLITARIA</option>
                        <?php endif; ?>
                                <option value="SOLOS" <?php if ($referencia == "SOLOS") {echo " selected";}?> >SOLOS</option>
                                <option value="ASOCIADOS" <?php if ($referencia == "ASOCIADOS") {echo " selected";}?> >FAUNA o GENTE ASOCIADAS</option>
						<?php if($_SESSION['tipocen']=='PARCIALXXXX'): ?>
                                <option value="TOTAL" <?php if ($referencia == "TOTAL") {echo " selected";}?> >TOTAL</option>
                        <?php endif; ?>
<!--                                <option value="TOTAL HARENES" <?php if ($referencia == "TOTAL HARENES") {echo " selected";}?> >TOTAL HARENES</option> -->                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorreferencia"></p>
                            <?php if (!empty($referenciaError)): ?>
                                <span class="help-inline"><?php echo $referenciaError;?></span>
                            <?php endif; ?>
                    </div>
				</div>
		</div>

		<label >Posición del grupo: &uacute;nico punto o dos (desde-hasta)</label><br>
		<label style="color:#464fa0">Formato de longitud/latitud: [-]g.g&nbsp;&nbsp;&nbsp;[-]g m.m&nbsp;&nbsp;&nbsp;[-]g m s.s</label>
	
		<div class=row>	
			<div class="col-sm-3">			
                    <div class="form-group ">
                        <label for="longi">Longitud</label>
                            <input type="text" class="form-control input-sm" id="longi" name="longi"  value="<?php echo !empty($longi)?$longi:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlongi"></p>
                            <?php if (!empty($longiError)): ?>
                                <span class="help-inline"><?php echo $longiError;?></span>
                            <?php endif; ?>


                    </div>					
               </div>					
			  <div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="lati">Latitud</label>
                            <input type="text" class="form-control input-sm" id="lati" name="lati"  value="<?php echo !empty($lati)?$lati:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlati"></p>
                            <?php if (!empty($latiError)): ?>
                                <span class="help-inline"><?php echo $latiError;?></span>
                            <?php endif; ?>


                    </div>						
               </div>					
			<div class="col-sm-3">			
                    <div class="form-group ">
                        <label for="longiH">Longitud hasta</label>
                            <input type="text" class="form-control input-sm" id="longiH" name="longiH"  value="<?php echo !empty($longiH)?$longiH:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlongiH"></p>
                            <?php if (!empty($longiHError)): ?>
                                <span class="help-inline"><?php echo $longiHError;?></span>
                            <?php endif; ?>


                    </div>					
            </div>					
			<div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="latiH">Latitud hasta</label>
                            <input type="text" class="form-control input-sm" id="latiH" name="latiH"  value="<?php echo !empty($latiH)?$latiH:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlatiH"></p>
                            <?php if (!empty($latiHError)): ?>
                                <span class="help-inline"><?php echo $latiHError;?></span>
                            <?php endif; ?>


                    </div>						
            </div>		
		</div>

		<div class=row>
			<div class="col-sm-6">			   
                    <div class="form-group ">
                        <label for="geomTex">en Formato WKT</label>
                            <input type="text" class="form-control input-sm" id="geomTex" name="geomTex" readonly oninput="mayus(this);"  value="<?php echo !empty($geomTex)?$geomTex:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorgeomTex"></p>
                            <?php if (!empty($geomTexError)): ?>
                                <span class="help-inline"><?php echo $geomTexError;?></span>
                            <?php endif; ?>


                    </div>
               </div>

				<div class="col-sm-1">
                        <label for="geomTex">&nbsp;&nbsp;&nbsp;</label>
					
						<a title="grupo en mapa" onclick=ventanaMap("mapin_grupo.php?fec=<?php echo $fecha?>&lib=<?php echo$libreta?>&ord=<?php echo $orden?>&geoLo="+punLL('longi')+"&geoLa="+punLL('lati')) class="btn btn-info btn-sm"><span class="glyphicon glyphicon-map-marker"></span> mapa</a>

						
					
						
				</div>
				<div class="col-sm-1">
				</div>			   
			   
				<div class="col-sm-3">

                    <div class="form-group ">
                        <label for="distancia">Distancia al har&eacute;n (m.)</label>					
						
                            <input type="text" class="form-control input-sm" style="width: 50px" id="distancia" name="distancia" 
							data-pentero data-dmin="<?php echo CONST_distancia_min?>" data-dmax="<?php echo CONST_distancia_max?>"  
							data-error="<?php echo CONST_distancia_men?>" value="<?php echo !empty($distancia)?$distancia:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorlibreta"></p>
                            <?php if (!empty($distanciaError)): ?>
                                <span class="help-inline"><?php echo $distanciaError;?></span>
                            <?php endif; ?>

                    </div>

            </div>



			   
 </div>					
					
                       <label >Playa o tramo (establecer con una de las dos alternativas propuestas)</label>
					
 <div class=row>
		<div class="col-sm-4">
                    <div class="form-group ">
                        <label for="playa" style="color:#464fa0">Seleccionar de la lista</label>
                            <select class="form-control input-sm" id="playa" data-error="C&oacute;digo incorrecto"   name="playa">
                                <option value="" <?php if ($playa == "") {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
        $sql = "SELECT IDplaya, nombre,tipo FROM playa WHERE tipo IN ('TRAMO','PUNTO') and IDplaya<> 'Z999' ORDER BY norteSur";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
        while ( $aPla = $q->fetch(PDO::FETCH_ASSOC) ) {
             echo str_repeat(" ",32). '<option value="' .$aPla['IDplaya']. '"';
                         if ($playa == $aPla['IDplaya']) {
                                 echo " selected";
                        }
                      echo '>' .$aPla['nombre']. '&nbsp;&nbsp;&nbsp;('.$aPla['tipo'].')</option>'."\n";
        }
        Database::disconnect();
        ?>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorplaya"></p>
                            <?php if (!empty($playaError)): ?>
                                <span class="help-inline"><?php echo $playaError;?></span>
                            <?php endif; ?>


                    </div>					
            </div>
		<div class="col-sm-2">
				<label for="otro">&nbsp;</label><br>
				<a class="btn btn-primary btn-sm" href="playa_index.php" target="_blank" title="agregar playa"><span class="glyphicon glyphicon-plus"></span></a>						
				<button class="btn btn-default btn-sm" onclick="window.location.reload();" title="recargar"><span class="glyphicon glyphicon-refresh"></span></button>
		</div>			
		<div class="col-sm-6">
                        <label style="color:#464fa0">En funci&oacute;n de la longitud y latitud</label>
                            <div class="form-actions">
                                 <button type="submit" class="btn btn-warning btn-sm" id="segunLonLat" name="segunLonLat" value="<?php echo !empty($segunLonLat)?$segunLonLat:'segunLonLat';?>">Buscar por latitud y longitud</button>
                              </div>
<?php
   if (isset($_POST['segunLonLat']) and empty($eError) and !empty($valid) and $valid and !empty($longi) ){
                $sql ="CALL PuntoEnPlayaPunto(ST_GeomFromText(\"$geomTexBuscar\"),@t,@p);";
				/*$sql.="select @t as nombre,'tramo' as tipo UNION select @p as nombre,'playa' as tipo;";*/
				$sql.="select if(@t IS NULL,'#NOVA',@t) as nombre,'tramo' as tipo UNION select if(@p IS NULL,'#NOVA',@p) as nombre,'playa' as tipo;";
                muestraMultipleRS($sql,"botonsi");
   }
?>  
            </div>					
					
</div>	

 


                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>
                    </div>

                    
                    <div class="form-actions">
					<?php /*if (empty($guardado)): */?>
                        <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php /* endif; */?>
 
                    <a class="btn btn-default btn-sm" onclick=parent.cierroM("grupo_index.php?fecha=<?php echo $fecha;?>&libreta=<?php echo $libreta;?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
 						
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                         <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro de grupo agregado</h5></span>
                          <?php endif;?>

                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->
	
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
		
<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tm_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

</body>
</html>