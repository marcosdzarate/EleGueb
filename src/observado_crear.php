<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'observado';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $temporada = null;
        $tipoTempo = null;
        $playa = null;
        $comentario = null;
        $geomTex = null;
        $longi = null;
        $lati = null;

    $v=true;

    $claveU = null;
    if (isset($_GET["claveU"])) {
        $claveU=$_GET["claveU"];
        $m = validar_claveU ($claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["claveU"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    $temporada = null;
    if (isset($_GET["temporada"])) {
        $temporada=$_GET["temporada"];
        $m = validar_temporada ($temporada,$claveU,$v,true);
    }
    else{
        if (!isset($_GET["temporada"]) and !isset($_POST["temporada"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    $tipoTempo = null;
    if (isset($_GET["tipoTempo"])) {
        $tipoTempo=$_GET["tipoTempo"];
        $m = validar_tipoTempo ($tipoTempo,$v,true);
    }
    else{
        if (!isset($_GET["tipoTempo"]) and !isset($_POST["tipoTempo"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    $xtrx = "";
    if (isset($_GET['xtrx'])) {
        $xtrx =$_GET['xtrx'];
    }

    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $temporadaError = null;
        $tipoTempoError = null;
        $playaError = null;
        $comentarioError = null;
        $geomTexError = null;
        $longiError = null;
        $latiError = null;

        
        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $temporada = limpia($_POST['temporada']);
        $tipoTempo = limpia($_POST['tipoTempo']);
        $playa = limpia($_POST['playa']);
        $comentario = limpia($_POST['comentario']);
        $geomTex = limpia($_POST['geomTex']);
        $longi = limpia($_POST['longi']);
        $lati = limpia($_POST['lati']);

        $xtrx = limpia($_POST['xtrx']);



        
        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $temporadaError = validar_temporada ($temporada,$claveU,$valid,true);
        $tipoTempoError = validar_tipoTempo ($tipoTempo,$valid,true);
		$playaError = "";
        if (!isset($_POST['segunLonLat'])) {
			$playaError = validar_playa ($playa,$valid,true);
		}

//validacion y conversión de latitud y longitud
/* converLatLon devuelve true si el string está vacío   */
/* devuelve false y $...Error si hay error              */
/* sino, devuelve la entrada convertida a grados decimal*/
/*    y en $laPrimerVariable la cadena de entrada limpia*/
        $hayDesde=false;
        $blaDesde=false;
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


        
        if ($blaDesde){
                $geomTex="";
            }

        if ($hayDesde) {
            $geomTex="POINT(".$cLongi." ".$cLati.")";
        }


        
    // validacion entre campos
        $eError = validarForm_observado ($claveU,$fecha,$temporada,$tipoTempo,$playa,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);
            $SQLgeomTex = ($geomTex == '' ? NULL : $geomTex);
            $SQLlati = ($lati == '' ? NULL : $lati);
            $SQLlongi = ($longi == '' ? NULL : $longi);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO observado (claveU,fecha,temporada,tipoTempo,playa,geomTex,lati,longi,comentario) values(?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$temporada,$tipoTempo,$playa,$SQLgeomTex,$SQLlati,$SQLlongi,$SQLcomentario));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                $guardado='ok';

            }



        }
    }

     $param0 = "?claveU=".$claveU;
     $param = $param0."&fecha=".$fecha;

	$tipoT="";
	$tActual = temporadaAnio_actual($tipoT);

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

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nueva fecha y lugar de observaciones y/o tareas</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRobservado" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                    <input name="xtrx" type="hidden"  value="<?php echo $xtrx;?>">
                <div class=row>             
                  <div class=col-sm-2>

                    <div class="form-group ">
                        <label for="claveU">ClaveU</label>
                            <input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"
may&uacute;sculas" value="<?php echo !empty($claveU)?$claveU:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorclaveU"></p>
                            <?php if (!empty($claveUError)): ?>
                                <span class="help-inline"><?php echo $claveUError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>                
                  <div class=col-sm-2>

                    <div class="form-group ">
                        <label for="temporada">Temporada</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="temporada" name="temporada" required readonly data-pentero
							data-dmin="<?php echo CONST_temporadaMin_min?>" data-dmax=<?php echo $tActual; ?>  
							data-error="<?php echo CONST_temporadaMin_men?> y &lt= <?php echo $tActual?>"
							value="<?php echo !empty($temporada)?$temporada:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortemporada"></p>
                            <?php if (!empty($temporadaError)): ?>
                                <span class="help-inline"><?php echo $temporadaError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>                
                  <div class=col-sm-2>

                    <div class="form-group ">
                        <label for="tipoTempo">Tipo</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="tipoTempo" required readonly name="tipoTempo" value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>">
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipoTempo"></p>
                            <?php if (!empty($tipoTempoError)): ?>
                                <span class="help-inline"><?php echo $tipoTempoError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>
                  
                  <div class=col-sm-6>              
                
                    <div class="form-group ">
                        <label for="fecha">Fecha</label>
                            <input type="text" class="form-control input-sm" style="width:120px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorfecha"></p>
                            <?php if (!empty($fechaError)): ?>
                                <span class="help-inline"><?php echo $fechaError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>                
                  
                  
                </div>

        <label style="color:#464fa0">Formato de longitud/latitud: [-]g.g&nbsp;&nbsp;&nbsp;[-]g m.m&nbsp;&nbsp;&nbsp;[-]g m s.s</label>
    
        <div class=row> 
            <div class="col-sm-4">          
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
              <div class="col-sm-4">            

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
                 <div class="col-sm-4">            
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

        </div>
        
        
                      <label >Playa o tramo (establecer con una de las dos alternativas propuestas)</label>
                    
 <div class=row>
        <div class="col-sm-6">
                    <div class="form-group ">
                        <label for="playa" style="color:#464fa0">Seleccionar de la lista</label>
                            <select class="form-control input-sm" id="playa" data-error="C&oacute;digo incorrecto"   name="playa">
                                <option value="" <?php if ($playa == "") {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
        $sql = "SELECT IDplaya, nombre,tipo FROM playa WHERE tipo='PUNTO' and IDplaya<> 'Z999' ORDER BY norteSur";
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
        <div class="col-sm-1" style="padding-left:2px">
								<label for="nPlaya">&nbsp;</label><br>
								<a class="btn btn-primary btn-sm" style="padding: 2px 0px;" href="playa_index.php" target="_blank" title="agregar playa"><span class="glyphicon glyphicon-plus"></span></a>						
								<button class="btn btn-default btn-sm" style="padding: 2px 0px;" onclick="window.location.reload();" title="recargar"><span class="glyphicon glyphicon-refresh"></span></button>		
        </div>
                  
        <div class="col-sm-5">
                        <label style="color:#464fa0">En funci&oacute;n de la longitud y latitud</label>
                            <div class="form-actions">
                                 <button type="submit" class="btn btn-warning btn-sm" id="segunLonLat" name="segunLonLat" value="<?php echo !empty($segunLonLat)?$segunLonLat:'segunLonLat';?>">ver las propuestas / seleccionar</button>
                              </div>
<?php
   if (isset($_POST['segunLonLat']) and empty($eError) and !empty($valid) and $valid and !empty($longi) ){
                $sql ="CALL PuntoEnPlayaPunto(ST_GeomFromText(\"$geomTex\"),@t,@p);";
                /*$sql.="select @t as como_Tramo;";
                $sql.="select @p as como_Playa;";*/
                $sql.="select @p as nombre,'playa' as tipo;";
                muestraMultipleRS($sql,"botonsi");
   }
?>  
            </div>                  
                    
</div>          
        
        
        
            

            <div class=row>
                <div class="col-sm-6">
                    
                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="1" maxlength="500"><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>
                    </div>
                </div>


                <div class="col-sm-6">

                    <br>
                    <div class="form-actions">
                        <?php if (empty($guardado)): ?>
                            <button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php if ($xtrx<>"nuevo"): ?>
                                  <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php  endif; ?>
                        <?php  endif; ?>



                        <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                        <?php endif;?>

                        <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>

                            <div class="row">
                               <div class="col-sm-6">
                                   <a href="#" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> OK</a>
                               </div>
                                    <div class="col-sm-6 text-right">
                                <?php if ($xtrx=="nuevo"): ?>
                                        <a class="btn btn-info btn-lg" href="tag_crear.php<?php echo $param;?>" >siguiente&nbsp;&nbsp;<span class="glyphicon glyphicon-arrow-right"></span></a>
                                <?php else:?>
                                    <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <?php endif;?>
                                    </div>

                           </div>


                        <?php endif;?>


                    </div>
                    </div>
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
	
</body>
</html>