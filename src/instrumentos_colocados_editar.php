<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'instrumentos_colocados';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_viajeID = null;
    if (isset($_GET["viajeID"])) {
        $pk_viajeID=$_GET["viajeID"];
        $m = validar_viajeID ($pk_viajeID,$v,true);
    }
    else{
        if (!isset($_GET["viajeID"]) and !isset($_POST["pk_viajeID"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_instrumentoNRO = null;
    if (isset($_GET["instrumentoNRO"])) {
        $pk_instrumentoNRO=$_GET["instrumentoNRO"];
        $m = validar_instrumentoNRO ($pk_instrumentoNRO,$v,true);
    }
    else{
        if (!isset($_GET["instrumentoNRO"]) and !isset($_POST["pk_instrumentoNRO"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

	$valid=true;
    $eError = null;

    $guardado='';

	$qDisponible='';
	
	$xdias=30;	
	
	$actualizaDispo="NO";

    if ( !empty($_POST)) {
        // para errores de validacion
        $viajeIDError = null;
        $instrumentoNROError = null;
        $fecha_recuperacionError = null;
        $comentarioError = null;
		$actualizaDispoError = null;

        $eError = null;

        // los campos a validar
        $viajeID = limpia($_POST['viajeID']);
        $instrumentoNRO = limpia($_POST['instrumentoNRO']);
        $fecha_recuperacion = limpia($_POST['fecha_recuperacion']);
        $comentario = limpia($_POST['comentario']);

		$actualizaDispo = limpia($_POST['actualizaDispo']);

        // valores anteriores de campos clave
        $pk_viajeID=limpia($_POST['pk_viajeID']);
        $pk_instrumentoNRO=limpia($_POST['pk_instrumentoNRO']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $viajeIDError = validar_viajeID ($viajeID,$valid,true);
        $instrumentoNROError = validar_instrumentoNRO ($instrumentoNRO,$valid,true);
        $fecha_recuperacionError = validar_fecha_recuperacion ($fecha_recuperacion,$valid,false);

        // validacion entre campos
        $eError = validarForm_instrumentos_colocados ($viajeID,$instrumentoNRO,$fecha_recuperacion,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLfecha_recuperacion = ($fecha_recuperacion == '' ? NULL : $fecha_recuperacion);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE instrumentos_colocados set viajeID=?,instrumentoNRO=?,fecha_recuperacion=?,comentario=? WHERE viajeID=? and instrumentoNRO=?";
            $q = $pdo->prepare($sql);
            $q->execute(array($viajeID,$instrumentoNRO,$SQLfecha_recuperacion,$SQLcomentario,$pk_viajeID,$pk_instrumentoNRO));

            $arr = $q->errorInfo();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
				if($fecha_recuperacion<>'' and $actualizaDispo<>'NO'){
					/* actualiza "disponible" sii la fecha de recuperacion esta entre hoy y "mdias" dias antes (ver validarForm_instrumentos_colocados) */
					$sql="UPDATE instrumentos SET disponible=? WHERE  instrumentoNRO=?";
					$q = $pdo->prepare($sql);
					$q->execute(array($actualizaDispo, $instrumentoNRO));
					$arr = $q->errorInfo();
					if ($arr[0] <> '00000') {
						$eError = "Error MySQL: ".$arr[1]." ".$arr[2];
					}
				}			
            }
            Database::disconnect();
        }
			
    }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM instrumentos_colocados where viajeID=? AND instrumentoNRO=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_viajeID,$pk_instrumentoNRO));
            $arr = $q->errorInfo();
            if ($q->rowCount()==0) {
                    Database::disconnect();
                    $eError="No hay registro!!!!!";
                    }
                else{
                   if ($arr[0] <> '00000') {
                       $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                      }
                   else{
                       $data = $q->fetch(PDO::FETCH_ASSOC);

                       $viajeID = $data['viajeID'];
                       $instrumentoNRO = $data['instrumentoNRO'];
                       $fecha_recuperacion = $data['fecha_recuperacion'];
                       $comentario = $data['comentario'];

                       $pk_viajeID = $viajeID;
                       $pk_instrumentoNRO = $instrumentoNRO;

					   
                       Database::disconnect();
					   
                   }
                }
        }

     $param0 = "?viajeID=".$viajeID."&instrumentoNRO=".$instrumentoNRO;


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

<style>
.mark, mark {
    padding: .2em;
    background-color: #ffd600;
}
.lh13{
	line-height:1.3;
}
</style>

<script>

function actqDisponible() {
	var fecRecup=document.getElementById("fecha_recuperacion").value;
	var xDispo=document.getElementById("qDisponible").value;
	
	if (document.getElementById("guardar") !== null)  {
		document.getElementById("guardar").disabled=false;
	}
		
  
        /* vemos si fecha de recuperacion esta antes o despues del margen de xdias */
		var xdias=<?php echo $xdias; ?>;
		var Mpd = 1000 * 60 * 60 * 24;
		var fhoy = new Date();
		b = new Date(fecRecup);	
		var utc2 = Date.UTC(fhoy.getFullYear(), fhoy.getMonth(), fhoy.getDate());
		var utc1 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
		var diff = Math.floor((utc2 - utc1) / Mpd);
		var q = document.getElementById("actualizaDispo");		
		var x = q.getElementsByTagName("option");
		var i;
		if (diff > xdias || xDispo!="VIAJANDO") {
			document.getElementById("actualizaDispoMas").style.display = "block";
			document.getElementById("actualizaDispoMenos").style.display = "none";
			for (i = 0; i < x.length; i++) { /* select/option solo permito value=NO*/
				if (x[i].value!='NO') {
					x[i].disabled = true;
				}
			}
			q.readOnly=true;
		}
		else{
			document.getElementById("actualizaDispoMas").style.display = "none";
			document.getElementById("actualizaDispoMenos").style.display = "block";
			for (i = 0; i < x.length; i++) { /* select/option todas habilitadas*/
				x[i].disabled = false;
				if (x[i].value!= q.value) {
					x[i].selected = true;
				}
			}
			q.readOnly=false;
		}
		
		if(fecRecup === "") {
			document.getElementById("actualizaDispoMas").style.display = "none";
			document.getElementById("actualizaDispoMenos").style.display = "none";
		}
		
    
}


</script>

<?php if (!edita()) : ?>
<script>
function aVerVer() {
	$('#CRinstrumentos_colocados').find(':input').attr('disabled', 'disabled'); 
}
</script>
<?php endif ?>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body onload="actqDisponible(); <?php echo !edita()?'aVerVer()':''?>" >
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">instrumento colocado</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRinstrumentos_colocados" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
				
					<div class="row">
						
                        <div class="col-xs-8">

							<div class="form-group ">
								<label for="instrumentoNRO">Instrumento </label>
			 <?php
				$pdo = Database::connect(); /* solo el instrumento actual!!!! */
				$sql = "SELECT * FROM instrumentos WHERE instrumentoNRO=? ORDER BY 2,3,4";
				$q = $pdo->prepare($sql);
				$q->execute(array($pk_instrumentoNRO));
				if ($q->rowCount()<>1) {
						Database::disconnect();
						echo 'Error al recuperar instrumento!!!';
						exit;
						}
				$aCola = $q->fetch(PDO::FETCH_ASSOC);  /* solo el instrumento actual!!! */
			    $insTxt = trim($aCola["tipo"]). " ".trim($aCola["identificacion"]). " ".trim($aCola["modelo"])." ".trim($aCola["fabricante"]);
				Database::disconnect();
				?>								
								<input type="text" class="form-control input-sm" style="width:auto" id="insTxt" name="insTxt" readonly value="<?php echo !empty($insTxt)?$insTxt:'';?>"   >
									
								<div class="help-block with-errors"></div>
								<p id="JSErrorinstrumentoNRO"></p>
								<?php if (!empty($instrumentoNROError)): ?>
									<span class="help-inline"><?php echo $instrumentoNROError;?></span>
								<?php endif; ?>


								<input name="instrumentoNRO" type="hidden"  value="<?php echo !empty($instrumentoNRO)?$instrumentoNRO:'';?>"> 
								<input name="pk_instrumentoNRO" type="hidden"  value="<?php echo !empty($pk_instrumentoNRO)?$pk_instrumentoNRO:'';?>"> <!-- pk, clave anterior -->
							</div>
						</div>
                        <div class="col-xs-4">
							<div class="form-group ">
								<label for="viajeID">ID de viaje (c&oacute;digo)</label>
									<input type="number" class="form-control input-sm" style="width:90px" id="viajeID" name="viajeID"  min="1" max="9999" required readonly data-error="Debe estar entre 1 y 9999" value="<?php echo !empty($viajeID)?$viajeID:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorviajeID"></p>
									<?php if (!empty($viajeIDError)): ?>
										<span class="help-inline"><?php echo $viajeIDError;?></span>
									<?php endif; ?>


									<input name="pk_viajeID" type="hidden"  value="<?php echo !empty($pk_viajeID)?$pk_viajeID:'';?>"> <!-- pk, clave anterior -->
							</div>

						
						</div>
						
					</div>						
						
					
                    <div class="row">
                        <div class="col-xs-7">
					
			 <?php
					 echo '<pre id="ipre'.$aCola['instrumentoNRO'].'">';
					 echo 'Nro de instrumento: '.$aCola['instrumentoNRO']."\n";
					 echo 'Tipo:             : '.$aCola['tipo']."\n";
					 echo 'Identificaci&oacute;n    : '.$aCola['identificacion']."\n";
					 echo 'N&uacute;mero de serie   : '.$aCola['serial_num']."\n";
					 echo 'Modelo            : '.$aCola['modelo']."\n";
					 echo 'Fabricante        : '.$aCola['fabricante']."\n";
					 echo 'Nuestro           : '.$aCola['nuestro']."\n";
					 echo '<mark>Disponibilidad HOY: '.$aCola['disponible']."</mark>\n";
					 echo 'Comentario        : '.$aCola['comentario']."\n";
					 echo '</pre>'."\n";
					 $qDisponible=$aCola['disponible'];
				?>							
										
                         </div>
						<input type="hidden" id="qDisponible" name="qDisponible" value=<?php echo !empty($qDisponible)?$qDisponible:'';?> >
                        
                        <div class="col-xs-5">
					
					
							<div class="form-group ">
								<label for="comentario">Comentario</label>
									<textarea class="form-control input-sm" id="comentario" name="comentario" rows="2" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
									<div class="help-block with-errors"></div>
									<p id="JSErrorcomentario"></p>
									<?php if (!empty($comentarioError)): ?>
										<span class="help-inline"><?php echo $comentarioError;?></span>
									<?php endif; ?>
							</div>
							
							
							<div class="form-group ">
								<label for="fecha_recuperacion">Fecha de recuperaci&oacute;n</label>
									<input type="text" class="form-control input-sm" style="width:150px" id="fecha_recuperacion" name="fecha_recuperacion" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" data-error="<?php echo PATRON_fecha_men;?> o la fecha es posterior a la de hoy"  value="<?php echo !empty($fecha_recuperacion)?$fecha_recuperacion:'';?>" data-pfecha onchange="actqDisponible();">
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha_recuperacion"></p>
									<?php if (!empty($fecha_recuperacionError)): ?>
										<span class="help-inline" id=errrr><?php echo $fecha_recuperacionError;?></span>
									<?php endif; ?>


							</div>
							
							
						</div>
					</div>



					
						<div class="row">
							<div class="col-xs-7">
									<label class="lh13" id="actualizaDispoMas">La fecha de recuperaci&oacute;n es de hace <mark>M&Aacute;S</mark> de <?php echo $xdias;?> dias.</label>

									<label class="lh13" id="actualizaDispoMenos">La fecha de recuperaci&oacute;n es de hace <mark>MENOS</mark> de <?php echo $xdias;?> dias.</label>
									
                                    <br>
								<?php if (edita()) : ?>							
                                    <label >Para administrar instrumentos </label>
									<a href="instrumentos_index.php" target="_blank" title="ir a instrumentos" style="font-size:14px"> <span class="glyphicon glyphicon-music"></span></a>
									luego <button class="btn btn-default btn-sm" style="padding: 2px 0px;" onclick="window.location.reload();" title="recargar esta ventana"><span class="glyphicon glyphicon-refresh"></span></button>
								<?php endif; ?>


							</div>

							<div class="col-xs-5">
								<label class="lh13">Disponibilidad HOY</label>
								<select class="form-control input-sm" style="width:auto"  name="actualizaDispo" id="actualizaDispo" data-error="Seleccionar un elemento de la lista">									
									<option value="SI" <?php if ($actualizaDispo == "SI") {echo " selected";}?> >SI - recuperado y disponible</option>
									<option value="PERDIDO" <?php if ($actualizaDispo == "PERDIDO") {echo " selected";}?> >PERDIDO</option>
									<option value="NO" <?php if ($actualizaDispo == "NO") {echo " selected";}?> >NO ACTUALIZAR</option>
								</select>
						
													
									<div class="help-block with-errors"></div>
									<p id="JSErroractualizaDispo"></p>
									<?php if (!empty($actualizaDispoError)): ?>
										<span class="help-inline"><?php echo $actualizaDispoError;?></span>
									<?php endif?>
							</div>
						</div>
						
					

					

                    <br>
                    <div class="form-actions">

					<?php if (edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<?php endif ?>

                        <a class="btn btn-default btn-sm" href="instrumentos_colocados_index.php?viajeID=<?php echo $viajeID;?>">a lista</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>instrumento modificado</h5></span>
                          <?php endif;?>
                    </div>
                </form>

            </div>
        </div>
    </div> <!-- /container -->

<script>
$('form[data-toggle="validator"]').validator({
    custom: {
    pfecha: function($el) {
        if ($el.val().length>0){
				var xdias=<?php echo $xdias; ?>;
				var Mpd = 1000 * 60 * 60 * 24;
				var fhoy = new Date();
				b = new Date($el.val());	
				var utc2 = Date.UTC(fhoy.getFullYear(), fhoy.getMonth(), fhoy.getDate());
				var utc1 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());
				var diff = Math.floor((utc2 - utc1) / Mpd);
				if (diff<0){

				
		document.getElementById("actualizaDispoMas").style.display = "none";
		document.getElementById("actualizaDispoMenos").style.display = "none";
		var q = document.getElementById("actualizaDispo");		
		var x = q.getElementsByTagName("option");
		var i;
		for (i = 0; i < x.length; i++) { /* select/option solo permito value=NO*/
			if (x[i].value!='NO') {
				x[i].disabled = true;
			}
		}
		q.readOnly=true;
				


					return $el.data("error");
				}			
            }
			
			
      }
  }
});
</script>


  </body>
</html>