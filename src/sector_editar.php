<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'sector';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */

	/*parametros inválidos, fuera*/        
	$v=true;
    $pk_fecha = null;	
	if (isset($_GET["fecha"])) {
		$pk_fecha=$_GET["fecha"];
		$m = validar_fecha ($pk_fecha,$v,true);
	}
	else{
		if (!isset($_GET["fecha"]) and !isset($_POST["pk_fecha"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);	

    $pk_libreta = null;	
	if (isset($_GET["libreta"])) {
		$pk_libreta=$_GET["libreta"];
		$m = validar_libreta ($pk_libreta,$v,true);
	}
	else{
		if (!isset($_GET["libreta"]) and !isset($_POST["pk_libreta"]) ){
			$v=false;
		}
	}
	siErrorFuera($v);


    if ( !empty($_POST)) {
        // para errores de validacion
        $fechaError = null;
        $libretaError = null;
        $horaInicioError = null;
        $horaFinError = null;
        $geomTexError = null;
        $zonaRecorridaError = null;
        $direccionRecorridaError = null;
        $mareaError = null;
        $climaError = null;
        $comentarioError = null;
        $latiInicioError = null;
        $longInicioError = null;
        $latiFinError = null;
        $longFinError = null;		

        $eError = null;
        $valid = true;
		
        // los campos a validar
        $fecha = limpia($_POST['fecha']);
        $libreta = limpia($_POST['libreta']);
        $horaInicio = limpia($_POST['horaInicio']);
        $horaFin = limpia($_POST['horaFin']);
        $geomTex = limpia($_POST['geomTex']);
        $zonaRecorrida =  limpia($_POST['zonaRecorrida']);
        $direccionRecorrida = limpia($_POST['direccionRecorrida']);
        $marea = limpia($_POST['marea']);
        $clima =  limpia($_POST['clima']);
        $comentario = limpia($_POST['comentario']);
        $latiInicio =  limpia($_POST['latiInicio']);
        $longInicio =  limpia($_POST['longInicio']);
        $latiFin =  limpia($_POST['latiFin']);
        $longFin =  limpia($_POST['longFin']);
		
        // valores anteriores de campos clave
        $pk_fecha=limpia($_POST['pk_fecha']);
        $pk_libreta=limpia($_POST['pk_libreta']);

		$cLatiI =converLatLon ($latiInicio,"lat",$latiInicioError);
		$cLongI =converLatLon ($longInicio,"lon",$longInicioError);
		$lInicio = false;
		if ($cLatiI===false or $cLongI===false) {
			//hay error
			$valid=false;
		}
		else {
			if ($cLatiI===true and $cLongI===true){
				$lInicio=true;
			}
			else {
				if ($cLatiI===true){
					$valid=false;
					$latiInicioError="No puede estar en blanco si informa Longitud.";
				} 
				else {
					if ($cLongI===true){
						$valid=false;
						$longInicioError="No puede estar en blanco si informa Latitud.";
					} 

				}
			}
		}
		
		
		$cLatiF =converLatLon ($latiFin,"lat",$latiFinError);
		$cLongF =converLatLon ($longFin,"lon",$longFinError);		
		$lFin = false;
		if ($cLatiF===false or $cLongF===false) {
			//hay error
			$valid=false;
		}
		else {
			if ($cLatiF===true and $cLongF===true){
				$lFin = true;
			}
			else {
				if ($cLatiF===true){
					$valid=false;
					$latiFinError="No puede estar en blanco si informa Longitud.";
				} 
				else {
					if ($cLongF===true){
						$valid=false;
						$longFinError="No puede estar en blanco si informa Latitud.";
					} 
					
				}
			}
		}
		
		
		if ($valid) {
			if ($lInicio and $lFin){
				$geomTex = "";
			}
			else {
				if($lInicio and $lFin<>true){
					$latiFinError="Faltan lat-lon desde";
					$valid = false;
				}
				else {
					if($lInicio<>true and $lFin) {
						$latiInicioError="Faltan lat-lon hasta";
						$valid = false;
						}
						else{
							$geomTex= "LINESTRING(".$cLongI." ".$cLatiI.",".$cLongF." ".$cLatiF.")";
						}
				}	
			}
		}
				
		
        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar


        $fechaError = validar_fecha ($fecha,$valid,true);
        $libretaError = validar_libreta ($libreta,$valid,true);
        $horaInicioError = validar_horaInicio ($horaInicio,$valid,false);
        $horaFinError = validar_horaFin ($horaFin,$valid,false);
        $direccionRecorridaError = validar_direccionRecorrida ($direccionRecorrida,$valid,false);
        $mareaError = validar_marea ($marea,$valid,false);

        // validacion entre campos
        $eError = validarForm_sector ($fecha,$libreta,$horaInicio,$horaFin,$geomTex,$zonaRecorrida,$direccionRecorrida,$marea,$clima,$comentario,$valid);

        // actualizar los datos
        if ($valid) {
            // campos que pueden tener NULL
            $SQLhoraInicio = ($horaInicio == '' ? NULL : $horaInicio);
            $SQLhoraFin = ($horaFin == '' ? NULL : $horaFin);
            $SQLgeomTex = ($geomTex == '' ? NULL : $geomTex);
            $SQLzonaRecorrida = ($zonaRecorrida == '' ? NULL : $zonaRecorrida);
            $SQLdireccionRecorrida = ($direccionRecorrida == '' ? NULL : $direccionRecorrida);
            $SQLmarea = ($marea == '' ? NULL : $marea);
            $SQLclima = ($clima == '' ? NULL : $clima);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLlatiInicio = ($latiInicio == '' ? NULL : $latiInicio);
            $SQLlongInicio = ($longInicio == '' ? NULL : $longInicio);
            $SQLlatiFin = ($latiFin == '' ? NULL : $latiFin);
            $SQLlongFin = ($longFin == '' ? NULL : $longFin);
			
            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE sector set fecha=?,libreta=?,horaInicio=?,horaFin=?,geomTex=?,zonaRecorrida=?,direccionRecorrida=?,marea=?,clima=?,comentario=?,latiInicio=?,longInicio=?,latiFin=?,longFin=? WHERE fecha=? AND libreta=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($fecha,$libreta,$SQLhoraInicio,$SQLhoraFin,$SQLgeomTex,$SQLzonaRecorrida,$SQLdireccionRecorrida,$SQLmarea,$SQLclima,$SQLcomentario,$SQLlatiInicio,$SQLlongInicio,$SQLlatiFin,$SQLlongFin,$pk_fecha,$pk_libreta));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /* header('Location: vw_censo_sector_index.php?fechaTotal='.$_SESSION['fecTot']);*/
                /*exit;*/

                 }
            }
        }
        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM sector where fecha=? AND libreta=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_fecha,$pk_libreta));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);
                       $fecha = $data['fecha'];
                       $libreta = $data['libreta'];
                       $horaInicio = $data['horaInicio'];
                       $horaFin = $data['horaFin'];
                       $geomTex = $data['geomTex'];
                       $zonaRecorrida = $data['zonaRecorrida'];
                       $direccionRecorrida = $data['direccionRecorrida'];
                       $marea = $data['marea'];
                       $clima = $data['clima'];
                       $comentario = $data['comentario'];
                       $latiInicio = $data['latiInicio'];
                       $longInicio = $data['longInicio'];
                       $latiFin = $data['latiFin'];
                       $longFin = $data['longFin'];

                       $pk_fecha = $fecha;
                       $pk_libreta = $libreta;

                       Database::disconnect();
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

<?php if (!edita() or $_SESSION['tipocen']=='MENSUAL'   or  !editaCenso($fecha))  : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>
<body onload="aVerVer('CRsector')">

<?php else : ?>
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
</head>
<body >
<?php endif ?>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">sector</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRsector" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

		<div class=row>	
			<div class="col-sm-3">	
				
                    <div class="form-group ">
                        <label for="fecha">Fecha del censo</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>

                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
                            <?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>


                            <input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
                    </div>
            </div>


			<div class="col-sm-3">	

                    <div class="form-group ">
                        <label for="libreta">Libreta</label>
						<?php if ($_SESSION['tipocen']=='MENSUAL') : ?>	
                            <input type="text" class="form-control input-sm" style="width:60px" id="libreta" name="libreta"  oninput="mayus(this);" value="<?php echo !empty($libreta)?$libreta:'';?>" >
                        <?php else: ?>
                            <input type="text" class="form-control input-sm" style="width:60px" id="libreta" name="libreta"  oninput="mayus(this);" pattern="<?php echo PATRON_libreta;?>" maxlength="3" required  data-error="<?php echo PATRON_libreta_men;?>" value="<?php echo !empty($libreta)?$libreta:'';?>" >
                            <div class="help-block with-errors">Formato dd o ddL, d=d&iacute;gito L=letra</div>
                            <p id="JSErrorlibreta"></p>
                            <?php if (!empty($libretaError)): ?>
                                <span class="help-inline"><?php echo $libretaError;?></span>
                            <?php endif; ?>
                        <?php endif; ?>


                            <input name="pk_libreta" type="hidden"  value="<?php echo !empty($pk_libreta)?$pk_libreta:'';?>"> <!-- pk, clave anterior -->
                    </div>
            </div>
			<div class="col-sm-3">	
					<div class="form-group ">
                        <label for="horaInicio">Hora de inicio</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="horaInicio" name="horaInicio" placeholder="HH:mm:ss"  pattern="<?php echo PATRON_hora;?>" maxlength="8" onchange="aSegundos(this)"  data-error="<?php echo PATRON_hora_men;?>" value="<?php echo !empty($horaInicio)?$horaInicio:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorhoraInicio"></p>
                            <?php if (!empty($horaInicioError)): ?>
                                <span class="help-inline"><?php echo $horaInicioError;?></span>
                            <?php endif; ?>


                    </div>
            </div>

			<div class="col-sm-3">	
					
                    <div class="form-group ">
                        <label for="horaFin">Hora de finalizaci&oacute;n</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="horaFin" name="horaFin" placeholder="HH:mm:ss"  pattern="<?php echo PATRON_hora;?>" maxlength="8"  onchange="aSegundos(this)"  data-error="Hora inv&aacute;lida; el
formato debe ser HH:mm:ss, HH:00-23" value="<?php echo !empty($horaFin)?$horaFin:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorhoraFin"></p>
                            <?php if (!empty($horaFinError)): ?>
                                <span class="help-inline"><?php echo $horaFinError;?></span>
                            <?php endif; ?>


                    </div>
            </div>
        </div>

					<div class="form-group ">
                        <label for="zonaRecorrida">&Aacute;rea recorrida (indicada en la direcci&oacute;n censada)</label>
                            <textarea class="form-control input-sm" id="zonaRecorrida" name="zonaRecorrida" rows="1" maxlength="100"      ><?php echo !empty($zonaRecorrida)?$zonaRecorrida:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorzonaRecorrida"></p>
                            <?php if (!empty($zonaRecorridaError)): ?>
                                <span class="help-inline"><?php echo $zonaRecorridaError;?></span>
                            <?php endif; ?>


                    </div>		
		
<div class="help-block with-errors">formatos para ingresar latitud y longitud: [-]g.g &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [-]g m.m  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[-]g m s.s</div>


		<div class=row>	
			<div class="col-sm-3">			
                    <div class="form-group ">
                        <label for="longInicio">Longitud desde</label>
                            <input type="text" class="form-control input-sm" id="longInicio" name="longInicio"  value="<?php echo !empty($longInicio)?$longInicio:'';?>" >
                            <p id="JSErrorlongInicio"></p>
                            <?php if (!empty($longInicioError)): ?>
                                <span class="help-inline"><?php echo $longInicioError;?></span>
                            <?php endif; ?>


                    </div>					
               </div>					
			  <div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="latiInicio">Latitud desde</label>
                            <input type="text" class="form-control input-sm" id="latiInicio" name="latiInicio"  value="<?php echo !empty($latiInicio)?$latiInicio:'';?>" >
                            <p id="JSErrorlatInicio"></p>
                            <?php if (!empty($latiInicioError)): ?>
                                <span class="help-inline"><?php echo $latiInicioError;?></span>
                            <?php endif; ?>


                    </div>						
               </div>		
			<div class="col-sm-3">			
                    <div class="form-group ">
                        <label for="longFin">Longitud hasta</label>
                            <input type="text" class="form-control input-sm" id="longFin" name="longFin"  value="<?php echo !empty($longFin)?$longFin:'';?>" >
                            <p id="JSErrorlongFin"></p>
                            <?php if (!empty($longFinError)): ?>
                                <span class="help-inline"><?php echo $longFinError;?></span>
                            <?php endif; ?>


                    </div>					
               </div>					
			  <div class="col-sm-3">			

                    <div class="form-group ">
                        <label for="latiFin">Latitud hasta</label>
                            <input type="text" class="form-control input-sm" id="latiFin" name="latiFin"  value="<?php echo !empty($latiFin)?$latiFin:'';?>" >
                            <p id="JSErrorlatiFin"></p>
                            <?php if (!empty($latiFinError)): ?>
                                <span class="help-inline"><?php echo $latiFinError;?></span>
                            <?php endif; ?>


                    </div>						
               </div>	
			   
          </div>
		
		
  		<div class=row>	
			<div class="col-sm-6">						
                    <div class="form-group ">
                        <label for="geomTex">desde-hasta formatoWKT</label>
                            <textarea class="form-control input-sm" id="geomTex" name="geomTex" rows="1" maxlength="3000" readonly><?php echo !empty($geomTex)?$geomTex:'';?></textarea>
                    </div>
			</div>

			<div class="col-sm-2">	
					<div class="form-group ">
                        <label for="direccionRecorrida">Direcci&oacute;n</label>
                            <select class="form-control input-sm" style="width: auto" id="direccionRecorrida" data-error="Seleccionar un elemento de la lista" name="direccionRecorrida">
                                <option value="" <?php if ($direccionRecorrida == "") {echo " selected";}?> ></option>
                                <option value="N-S" <?php if ($direccionRecorrida == "N-S") {echo " selected";}?> >N-S</option>
                                <option value="S-N" <?php if ($direccionRecorrida == "S-N") {echo " selected";}?> >S-N</option>
                                <option value="E-O" <?php if ($direccionRecorrida == "E-O") {echo " selected";}?> >E-O</option>
                                <option value="O-E" <?php if ($direccionRecorrida == "O-E") {echo " selected";}?> >O-E</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrordireccionRecorrida"></p>
                            <?php if (!empty($direccionRecorridaError)): ?>
                                <span class="help-inline"><?php echo $direccionRecorridaError;?></span>
                            <?php endif; ?>
                    </div>

            </div>
			<div class="col-sm-4">	
                    <div class="form-group ">
                        <label for="marea">Estado de la marea</label>
                            <select class="form-control input-sm" style="width: auto" id="marea" data-error="Seleccionar un elemento de la lista"   name="marea">
                                <option value="" <?php if ($marea == "") {echo " selected";}?> ></option>
                                <option value="MUY BAJA" <?php if ($marea == "MUY BAJA") {echo " selected";}?> >MUY BAJA</option>
                                <option value="BAJA" <?php if ($marea == "BAJA") {echo " selected";}?> >BAJA</option>
                                <option value="BAJA BAJANDO" <?php if ($marea == "BAJA BAJANDO") {echo " selected";}?> >BAJA BAJANDO</option>
                                <option value="BAJA SUBIENDO" <?php if ($marea == "BAJA SUBIENDO") {echo " selected";}?> >BAJA SUBIENDO</option>
                                <option value="MEDIA BAJANDO" <?php if ($marea == "MEDIA BAJANDO") {echo " selected";}?> >MEDIA BAJANDO</option>
                                <option value="MEDIA" <?php if ($marea == "MEDIA") {echo " selected";}?> >MEDIA</option>
                                <option value="MEDIA SUBIENDO" <?php if ($marea == "MEDIA SUBIENDO") {echo " selected";}?> >MEDIA SUBIENDO</option>
                                <option value="ALTA BAJANDO" <?php if ($marea == "ALTA BAJANDO") {echo " selected";}?> >ALTA BAJANDO</option>
                                <option value="ALTA SUBIENDO" <?php if ($marea == "ALTA SUBIENDO") {echo " selected";}?> >ALTA SUBIENDO</option>
                                <option value="ALTA" <?php if ($marea == "ALTA") {echo " selected";}?> >ALTA</option>
                                <option value="MUY ALTA" <?php if ($marea == "MUY ALTA") {echo " selected";}?> >MUY ALTA</option>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrormarea"></p>
                            <?php if (!empty($mareaError)): ?>
                                <span class="help-inline"><?php echo $mareaError;?></span>
                            <?php endif; ?>


                    </div>

            </div>

        </div>

		<div class=row>	
			<div class="col-sm-6">			

                    <div class="form-group ">
                        <label for="clima">Clima</label>
                            <textarea class="form-control input-sm" id="clima" name="clima" rows="1" maxlength="100"      ><?php echo !empty($clima)?$clima:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorclima"></p>
                            <?php if (!empty($climaError)): ?>
                                <span class="help-inline"><?php echo $climaError;?></span>
                            <?php endif; ?>


                    </div>
            </div>
			<div class="col-sm-6">			
                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>


                    </div>
            </div>
        </div>

                    
                    <div class="form-actions">
					<?php if (edita() and $_SESSION['tipocen']<>'MENSUAL'   and editaCenso($fecha) ) : ?>
                        <button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endif ?>
						
                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

						
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>cambios guardados</h5></span>
                          <?php endif;?>

                    </div>
                </form>
<script>
$('form[data-toggle="validator"]').validator({
    custom: {
      patron: function($el) {
        var pat = new RegExp($el.data("patron"));
        var le = $el.val().length;
        if (le>0){
           var res =pat.test($el.val());
           if (!res) {
              return $el.data("error")
             }
             else{
               var lm=$el.val().match(pat)[0].length;
               if (le>lm){
                 return $el.data("error")
               }
             }
       }
   },
  }
});
</script>

            </div>
        </div>
    </div> <!-- /container -->




  </body>
</html>