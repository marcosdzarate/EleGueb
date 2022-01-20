<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

    $par_elimina=1;

        $queTabla = 'sector_copiado';

        $fecha = null;
        $libreta = null;  //virtual
        $zona_copia = null;
        $fecha_copia = null;
        $libreta_copia = null;  //desde la que se copia
        $orden_desde = null;
        $orden_hasta = null;
        $comentario = null;
        
        $punto1 = null;
        $punto2 = null;

	/* sin permiso de edición, fuera*/
	siErrorFuera(edita());

	/*parametros inválidos, fuera*/
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

	
	
    if ( !empty($_POST)) {
        
        if (isset($_POST['alternativas'])  ) {
            // obtengo posibilidades para presentar al usuario tomadas de otros años
            // en función de la zona definida por el rectángulo
//echo "alter alter=".$_POST['alternativas']." guarda= ".$_POST['guardar'].time();
            $valid = true;
            $punto1Error = null;
            $punto2Error = null;
            $fechaError = null;
            $libretaError = null;
            $zona_copiaError = null;

            $eError = null;

            $fecha = limpia($_POST['fecha']);
            $punto1 = limpia($_POST['punto1']);
            $punto2 = limpia($_POST['punto2']);
            $libreta = limpia($_POST['libreta']);
            $zona_copia = limpia($_POST['zona_copia']);
            $alternativas = limpia($_POST['alternativas']);
            
            $fechaError = validar_fecha ($fecha,$valid,true);
            $libretaError = validar_libreta_virtual ($libreta,$valid,true);
            $punto1Error = validar_geomTex ($punto1,$valid,true);
            $punto2Error = validar_geomTex ($punto2,$valid,true);
            // sigue en el cuerpo del form
            
        }
        else {
            // guardar: por acá agregamos el registro
            // para errores de validacion
//echo "nono alter=".$_POST['alternativas']." guarda= ".$_POST['guardar'].time();
            $fechaError = null;
            $libretaError = null;
            $zona_copiaError = null;
            $fecha_copiaError = null;
            $libreta_copiaError = null;
            $orden_desdeError = null;
            $orden_hastaError = null;
            $comentarioError = null;

            $eError = null;

            // los campos a validar
            $fecha = limpia($_POST['fecha']);
            $punto1 = limpia($_POST['punto1']);
            $punto2 = limpia($_POST['punto2']);

            $libreta = limpia($_POST['libreta']);
            $zona_copia =  limpia($_POST['zona_copia']);
            $fecha_copia = limpia($_POST['fecha_copia']);
            $libreta_copia = limpia($_POST['libreta_copia']);
            $orden_desde = limpia($_POST['orden_desde']);
            $orden_hasta = limpia($_POST['orden_hasta']);
            $comentario =  limpia($_POST['comentario']);

            // validacion: devuelve el error; los dos primeros parametros pasan x referencia
            //             el ultimo: true debe estar - false puede faltar
            $valid = true;

            $fechaError = validar_fecha ($fecha,$valid,true);
            $libretaError = validar_libreta_virtual ($libreta,$valid,true);
            $fecha_copiaError = validar_fecha ($fecha_copia,$valid,true);
            $libreta_copiaError = validar_libreta ($libreta_copia,$valid,true);
            $orden_desdeError = validar_orden ($orden_desde,$valid,true);
            $orden_hastaError = validar_orden ($orden_hasta,$valid,true);

        // validacion entre campos
            $eError = validarForm_sector_copiado ($fecha,$libreta,$zona_copia,$fecha_copia,$libreta_copia,$orden_desde,$orden_hasta,$comentario,$valid);

            // nuevo registro
            if ($valid) {
			
                // campos que pueden tener NULL
                $SQLcomentario = ($comentario == '' ? NULL : $comentario);
                // inserta
                $pdo = Database::connect();
                $sql = "INSERT INTO sector_copiado (fecha,libreta,zona_copia,fecha_copia,libreta_copia,orden_desde,orden_hasta,comentario) values(?,?,?,?,?,?,?,?)";
                $q = $pdo->prepare($sql);
                $q->execute(array($fecha,$libreta,$zona_copia,$fecha_copia,$libreta_copia,$orden_desde,$orden_hasta,$SQLcomentario));

                $arr = $q->errorInfo();

                Database::disconnect();

                if ($arr[0] <> '00000') {
                    $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                    }
                else {
                }
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
function haceAlgo(cfecha,clibreta,cDesde,cHasta,cnGrupos) {
	
	document.getElementById("fecha_copia").value = cfecha;
	document.getElementById("libreta_copia").value = clibreta;
	document.getElementById("orden_desde").value = cDesde;
	document.getElementById("orden_hasta").value = cHasta;
	
}

</script>
	
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	
	
</head>

<body>
  <div class="container" style="width:90%">

	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>



    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">datos para completar el nuevo sector no censado</h3>
        </div>
            
        <form data-toggle="validator" id="CRsector_copiado" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            
            <div class="panel-body">      <!-- fecha y coordenadas -->

              <div class="row">

                 <div class="col-sm-6">
                    <div class="form-group">
                        <label for="fecha">Fecha a la que corresponde el censo faltante</label>
                            <input type="text" class="form-control input-sm" style="width:150px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" readonly  data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>

                            <?php if (!empty($fechaError) and !(strpos($fechaError,"Fecha en ")===false)): ?>
                                <span class="mensa"><?php echo $fechaError;?></span>
                            <?php elseif (!empty($fechaError)) : ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>
                    </div>
                 </div>

                 <div class="col-sm-6">
                        <div class="form-group ">
                            <label for="libreta">Libreta (virtual)</label>
                                <input type="text" class="form-control input-sm" style="width: 45px" id="libreta" name="libreta"  oninput="mayus(this);" pattern="<?php echo PATRON_libretaVirtual;?>" maxlength="3" required  data-error="<?php echo PATRON_libretaVirtual_men;?>" 
								value="<?php echo !empty($libreta)?$libreta:'';?>" >
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorlibreta"></p>
                                <?php if (!empty($libretaError)): ?>
                                    <span class="help-inline"><?php echo $libretaError;?></span>
                                <?php endif; ?>
                        </div>
                 </div>
              </div>
                        
                        
                        <div class="form-group ">
                            <label for="zona_copia">Sector faltante desde-hasta (texto)</label>
                                <textarea class="form-control input-sm" id="zona_copia" data-error="Escrib&iacute; una breve descripci&oacute;n" name="zona_copia" rows="1" maxlength="100"   ><?php echo !empty($zona_copia)?$zona_copia:'';?></textarea>
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorzona_copia"></p>
                                <?php if (!empty($zona_copiaError)): ?>
                                    <span class="help-inline"><?php echo $zona_copiaError;?></span>
                                <?php endif; ?>
                        </div>

					
					
					
					
                    <div class="panel panel-info">
                        <div class="panel-heading">DEFINIR EL RECT&Aacute;NGULO QUE CONTIENE EL SECTOR NO CENSADO QUE SE QUIERE COMPLETAR</div>
                            <div class="panel-body">            
                              <div class="row">

                                <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label for="punto1">punto superior derecho</label>
                                                <input type="text" class="form-control input-sm" id="punto1" name="punto1"  oninput="mayus(this);" pattern="<?php echo PATRON_geoPOINT?>" data-error="<?php echo PATRON_geoPOINT_men?>" required placeholder="POINT(long lati) WKT" maxlength="40" title="punto superior derecho del rect&aacute;ngulo"   value="<?php echo !empty($punto1)?$punto1:'';?>" >
                                                <div class="help-block with-errors"></div>
                                                <p id="JSErrorpunto1"></p>
                                                <?php if (!empty($punto1Error)): ?>
                                                    <span class="help-inline"><?php echo $punto1Error;?></span>
                                                <?php endif; ?>
                                       </div>
                                </div>

                                <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label for="punto2">punto inferior izquierdo</label>
                                                <input type="text" class="form-control input-sm" id="punto2" name="punto2"  oninput="mayus(this);" pattern="<?php echo PATRON_geoPOINT?>" data-error="<?php echo PATRON_geoPOINT_men?>"  maxlength="40" required placeholder="POINT(long lati) WKT" title="punto inferior izquierdo del rect&aacute;ngulo" value="<?php echo !empty($punto2)?$punto2:'';?>" >
                                                <div class="help-block with-errors"></div>
                                                <p id="JSErrorpunto2"></p>
                                                <?php if (!empty($punto2Error)): ?>
                                                    <span class="help-inline"><?php echo $punto2Error;?></span>
                                                <?php endif; ?>
                                       </div>
                                </div>

                              </div>
                              
                              
                              <div class="form-actions">
                                       <button type="submit" class="btn btn-warning btn-sm" id="alternativas" name="alternativas" value="<?php echo !empty($alternativas)?$alternativas:'alternativas';?>">mostrar alternativas</button>
                              </div>
                            </div>
                    </div>                      

           
              <!--  </form> -->
            </div>


<?php if (isset($_POST['alternativas']) or isset($_POST['guardar'])) :?> 

            <div class="panel-body">

                    <div class="panel panel-info">
                        <div class="panel-heading">La tabla siguiente muestra otros censos en los que hay datos dentro del rect&aacute;ngulo dado. nGrupos es la cantidad de grupos. Click en el bot&oacute;n de la derecha para seleccionar.</div>
                        <div class="panel-body">            

<?php
/* para el rectangulo definido por punto1 punto2 encuentra los grupos contenidos en ese rectangulo en cada año y para PVALDES*/
                $sql ="CALL PuntosDentroSectorParaCopia(ST_GeomFromText(\"$punto1\"),ST_GeomFromText(\"$punto2\"),2200);";
                $sql.="SELECT * FROM zz_gruporespuesta;";
                muestraMultipleRS($sql,"botonsi");

?>                          

                        </div>
                    </div>                      

            </div>


            <div class="panel-body">

                <div class="panel panel-info">
                    <div class="panel-heading">SE COMPLETA CON DATOS OBTENIDOS DE</div>
                    <div class="panel-body">    

                                                                        
                        <div class="row">
                            <div class="col-sm-6">        
                                <div class="form-group ">
                                    <label for="fecha_copia">Censo de fecha</label>
                                        <input type="text" class="form-control input-sm" style="width:150px" id="fecha_copia" name="fecha_copia"  readonly  
										value="<?php echo !empty($fecha_copia)?$fecha_copia:'';?>" >
                                        <div class="help-block with-errors"></div>
                                        <p id="JSErrorfecha_copia"></p>
                                        <?php if (!empty($fecha_copiaError)): ?>
                                            <span class="help-inline"><?php echo $fecha_copiaError;?></span>
                                        <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-sm-6">        
                                <div class="form-group ">
                                    <label for="libreta_copia">Libreta desde la que se obtienen los datos</label>
                                        <input type="text" class="form-control input-sm" style="width: 45px" id="libreta_copia" name="libreta_copia" readonly 
										value="<?php echo !empty($libreta_copia)?$libreta_copia:'';?>" >
                                        <div class="help-block with-errors"></div>
                                        <p id="JSErrorlibreta_copia"></p>
                                        <?php if (!empty($libreta_copiaError)): ?>
                                            <span class="help-inline"><?php echo $libreta_copiaError;?></span>
                                        <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    

                        <fieldset class="form-inline">                  
                            <div class="form-group ">
                                <label for="orden_desde">Orden: desde</label>
                                    <input type="number" class="form-control input-sm" style="width:90px" id="orden_desde" name="orden_desde" value="<?php echo !empty($orden_desde)?$orden_desde:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrororden_desde"></p>
                                    <?php if (!empty($orden_desdeError)): ?>
                                        <span class="help-inline"><?php echo $orden_desdeError;?></span>
                                    <?php endif; ?>
                            </div>

                            <div class="form-group ">
                                <label for="orden_hasta">...hasta</label>
                                    <input type="number" class="form-control input-sm" style="width:90px" id="orden_hasta" name="orden_hasta"  value="<?php echo !empty($orden_hasta)?$orden_hasta:'';?>" >
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrororden_hasta"></p>
                                    <?php if (!empty($orden_hastaError)): ?>
                                        <span class="help-inline"><?php echo $orden_hastaError;?></span>
                                    <?php endif; ?>
                            </div>

                        </fieldset>                 
                    
                      
                        
                        <div class="form-group ">
                            <label for="comentario">Comentario</label>
                                <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorcomentario"></p>
                                <?php if (!empty($comentarioError)): ?>
                                    <span class="help-inline"><?php echo $comentarioError;?></span>
                                <?php endif; ?>
                        </div>

                        <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-sm" name="guardar" value="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                              <?php if (!empty($eError)): ?>
                                <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                              <?php endif;?>
                              <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                                <span class="alert alert-success"><h5>registro agregado</h5></span>
                              <?php endif;?>
                            

                        </div>
                                    
                    </div>
                </div>
            </div>
<?php endif;?>          
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-default btn-sm" onclick=parent.cierroM("vw_censo_sector_index.php?fechaTotal=<?php echo $_SESSION['fecTot'];?>")>atr&aacute;s</a> <br> <br>

							
        </form>
    </div> 
	
  </div> <!-- /container -->
</body>
</html>