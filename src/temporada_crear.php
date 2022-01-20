<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'temporada';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
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


    $sexx = null;
    if (isset($_GET["sexx"])) {
        $sexx=$_GET["sexx"];
        $m = validar_sexo ($sexx,$v,true);
    }
    else{
        if (!isset($_GET["sexx"]) and !isset($_POST["sexx"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);
    
    $xtrx = "";
    if (isset($_GET['xtrx'])) {
        $xtrx =$_GET['xtrx'];
    }
        $temporada = date("Y");  /* null;*/
    	$temporadaActual=$temporada;
        $fhoy = date("Y-m-d");  	//string
		$mhoy = date("m");		    //string
        $eok=true;
        $r=validar_fecha ($fhoy,$eok,true);
        if($r==='En REPRO. ' or $r==='En REPRO. En MUDA. '){
            $tipoTempo = 'reproP';
            if ($xtrx=="nuevo"){
                $tipoTempo='REPRO';
            }
			if($mhoy>=11) {
				$temporadaActual=$temporada+1;
			}			
        }
        else
        {
            if($r==='En MUDA. '){
				if($mhoy==12) {
					$temporada=$temporada+1;
				}
				if($mhoy>=11) {
					$temporadaActual=$temporadaActual+1;
				}
                $tipoTempo = 'mudaP';
                if ($xtrx=="nuevo"){
                    $tipoTempo='MUDA';
                }                           
            }else
            {
            /* en FUERA */
                $tipoTempo = 'fueraP';
                if ($xtrx=="nuevo"){
                    $tipoTempo='FUERA';
                }           
            }
        }
        
        $categoria = null;
        $comentario = null;

    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $temporadaError = null;
        $tipoTempoError = null;
        $categoriaError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $temporada = limpia($_POST['temporada']);
        $tipoTempo = limpia($_POST['tipoTempo']);
        $categoria = limpia($_POST['categoria']);
        $comentario = limpia($_POST['comentario']);

        $xtrx = limpia($_POST['xtrx']);     
        $sexx = limpia($_POST['sexx']);     
        $temporadaActual = limpia($_POST['temporadaActual']);
        
        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $temporadaError = validar_temporada ($temporada,$claveU,$valid,true);
        $tipoTempoError = validar_tipoTempo ($tipoTempo,$valid,true);
        $categoriaError = validar_categoria ($categoria,$valid,true);

    // validacion entre campos
        if($valid) {
            $eError = validarForm_temporada ($claveU,$temporada,$tipoTempo,$categoria,$comentario,$valid,$sexx);
        }
        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO temporada (claveU,temporada,tipoTempo,categoria,comentario) values(?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$temporada,$tipoTempo,$categoria,$SQLcomentario));

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

     $param = "?claveU=".$claveU."&temporada=".$temporada."&tipoTempo=".$tipoTempo;
     if ($xtrx<>""){
         $param .="&xtrx=nuevo";
     }
     
     $param0 = "?claveU=".$claveU;


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
function disCria(vtt){
    /* categoria CRIA solo para REPRO */
    if (vtt=="REPRO") { 
        document.getElementById("categoria").options[1].style.display = "block";
        }
     else{
       document.getElementById("categoria").options[1].style.display = "none";
     }

	/* PRIM y PRAD solo para REPRO o reproP para HEMBRA */
	 var sex=document.getElementById("sexx").value;
	 if ( sex=='HEMBRA' ) {
		 if( vtt=="REPRO" || vtt=="reproP" ) {
		   document.getElementById("categoria").options[8].style.display = "block";
		   document.getElementById("categoria").options[9].style.display = "block";	   
		 }
		 else{
		   document.getElementById("categoria").options[8].style.display = "none";
		   document.getElementById("categoria").options[9].style.display = "none";	   
		 }
	 }
	 
}
         
</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body onload=disCria(document.getElementById("tipoTempo").value);>
    <div class="container" style="width:90%">
	
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


	
        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">datos para la temporada</h3>
            </div>
            <div class="panel-body">
                <form data-toggle="validator" id="CRtemporada" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

                <input name="xtrx" type="hidden"  value="<?php echo $xtrx;?>"> 
                <input name="sexx" type="hidden" id="sexx" value="<?php echo $sexx;?>"> 

                <div class=row>             
                  <div class=col-sm-3>
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
                  <div class=col-sm-6>      
                        <div class="well well-sm" ><strong>Atenci&oacute;n:</strong> verificar que <strong>Temporada y Tipo de temporada</strong> sean correctos; no podr&aacute;n modificarse.</div>
                  </div>    

                </div>

                <div class=row>             
                  <div class=col-sm-3>

                    <div class="form-group ">
                        <label for="temporada">A&ntilde;o</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="temporada" name="temporada"  
							data-dmin="<?php echo CONST_temporadaMin_min?>" data-dmax=<?php echo $temporadaActual; ?> data-pentero required  
							data-error="<?php echo CONST_temporadaMin_men?> y &lt= <?php echo $temporadaActual?>" value="<?php echo !empty($temporada)?$temporada:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortemporada"></p>
                            <?php if (!empty($temporadaError)): ?>
                                <span class="help-inline"><?php echo $temporadaError;?></span>
                            <?php endif; ?>
							<input name="temporadaActual" type="hidden"  value="<?php echo $temporadaActual;?>"> 
                    </div>
                  </div>
                  <div class="col-sm-3">        

                    <div class="form-group ">
                        <label for="tipoTempo">Tipo de temporada</label>
                            <select class="form-control input-sm" style="width: auto" id="tipoTempo" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="tipoTempo"   onchange=disCria(this.value);>
                                <option value="" <?php if ($tipoTempo == "") {echo " selected";}?> ></option>
                                <?php if ($xtrx=="nuevo"): ?> 
                                    <option value="MUDA" <?php if ($tipoTempo == "MUDA") {echo " selected";}?> >MUDA</option>
                                    <option value="FUERA" <?php if ($tipoTempo == "FUERA") {echo " selected";}?> >FUERA</option>
                                    <option value="REPRO" <?php if ($tipoTempo == "REPRO") {echo " selected";}?> >REPRO</option>
                                <?php else: ?> 
                                <option value="mudaP" <?php if ($tipoTempo == "mudaP") {echo " selected";}?> >mudaP</option>
                                <option value="fueraP" <?php if ($tipoTempo == "fueraP") {echo " selected";}?> >fueraP</option>
                                <option value="reproP" <?php if ($tipoTempo == "reproP") {echo " selected";}?> >reproP</option>
                                <?php endif ?> 
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipoTempo"></p>
                            <?php if (!empty($tipoTempoError)): ?>
                                <span class="help-inline"><?php echo $tipoTempoError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>
                </div>

                <div class=row>             
                  <div class="col-sm-5">        

                    <div class="form-group ">
                        <label for="categoria">Categor&iacute;a</label>
                            <select class="form-control input-sm" style=width:auto id="categoria" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="categoria">
                                <option value="" <?php if ($categoria == "") {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
        $b="";
        if ($xtrx=="nuevo"){
            $b="OR ordenCate=99";
        }
        $sql = "SELECT IDcategoria, cateDesc,sexoc FROM categoria WHERE ((ordenCate>0 and ordenCate<90) $b) and (sexoc='$sexx' or sexoc='CUALQUIERA') ORDER BY ordenCate";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
        while ( $aCat = $q->fetch(PDO::FETCH_ASSOC) ) {
				echo str_repeat(" ",32). '<option value="' .$aCat['IDcategoria']. '"';
				if ($categoria == $aCat['IDcategoria']) {
						 echo " selected";
				}
				if ($aCat['sexoc']=='CUALQUIERA') {
				echo '>' .$aCat['cateDesc'].'</option>'."\n";
				}
				else{
				 echo '>' .$aCat['cateDesc']. '&nbsp;&nbsp;&nbsp;('.$aCat['sexoc'].')</option>'."\n";
				}
			
        }
        Database::disconnect();
        ?>
                            </select>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcategoria"></p>
                            <?php if (!empty($categoriaError)): ?>
                                <span class="help-inline"><?php echo $categoriaError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>
                  <div class="col-sm-7">        

                    <div class="form-group ">
                        <label for="comentario">Comentario</label>
                            <textarea class="form-control input-sm" id="comentario" name="comentario" rows="2" maxlength="500"      ><?php echo !empty($comentario)?$comentario:'';?></textarea>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrorcomentario"></p>
                            <?php if (!empty($comentarioError)): ?>
                                <span class="help-inline"><?php echo $comentarioError;?></span>
                            <?php endif; ?>
                    </div>
                  </div>
                </div>

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
                                    <a class="btn btn-info btn-lg" href="observado_crear.php<?php echo $param;?>" >siguiente&nbsp;&nbsp;<span class="glyphicon glyphicon-arrow-right"></span></a>
                               </div>
                               
                           </div>

                            
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
	
</body>
</html>