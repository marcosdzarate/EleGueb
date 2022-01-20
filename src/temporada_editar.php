<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'temporada';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */


    /*parametros inválidos, fuera*/
    $v=true;

    $pk_claveU = null;
    if (isset($_GET["claveU"])) {
        $pk_claveU=$_GET["claveU"];
        $m = validar_claveU ($pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["pk_claveU"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_temporada = null;
    if (isset($_GET["temporada"])) {
        $pk_temporada=$_GET["temporada"];
        $m = validar_temporada ($pk_temporada,$pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["temporada"]) and !isset($_POST["pk_temporada"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_tipoTempo = null;
    if (isset($_GET["tipoTempo"])) {
        $pk_tipoTempo=$_GET["tipoTempo"];
        $m = validar_tipoTempo ($pk_tipoTempo,$v,true);
    }
    else{
        if (!isset($_GET["tipoTempo"]) and !isset($_POST["pk_tipoTempo"]) ){
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

        $sexx = limpia($_POST['sexx']);
		
		
        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_temporada=limpia($_POST['pk_temporada']);
        $pk_tipoTempo=limpia($_POST['pk_tipoTempo']);
		
		// valor referidos a la categoria
		$cate_anterior = limpia($_POST['cate_anterior']); 			//categoria original
		$cate_reproductiva=limpia($_POST['cate_reproductiva']);		//categoria original reproductiva?
		$cate_reproductiva_nueva=limpia($_POST['cate_reproductiva_nueva']);//categoria nueva reproductiva?
		
		

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $temporadaError = validar_temporada ($temporada,$claveU,$valid,true);
        $tipoTempoError = validar_tipoTempo ($tipoTempo,$valid,true);
        $categoriaError = validar_categoria ($categoria,$valid,true);

		
		
		if( $cate_reproductiva <> $cate_reproductiva_nueva AND 
			($tipoTempo='REPRO' OR $tipoTempo='reproP')) {
			/* la categoría original es reproductiva y la nueva no 					*/
			/* o viceversa                                                          */
			/* antes de actualizar, vemos la compatibilidad del cambio de categoria */
			/* verificando que, para la temporada:                                  */
			/* si la categoria original es reproductiva y la nueva es no reproductiva 
				solo se puede hacer el cambio si no tiene datos en MACHO, HEMBRA ni COPULA
			   si la categoria original es no reproductiva y la nueva es reproductiva 
				solo se puede hacer el cambio si no tiene datos en DESTETE          */
			$pdo = Database::connect();	
			if($cate_reproductiva=='SI') 
			{
				$sql = "SELECT claveU FROM vw_individuo_observado WHERE claveU = ?  AND temporada=? AND tipoTempo=? AND ( Nhembra IS NOT NULL OR Nmacho IS NOT NULL OR Ncopula IS NOT NULL)";
				$me = "No se puede cambiar la categoria reproductiva a no-reproductiva: el animal tiene datos en macho/hembra reproductivo y/o copula.";
			}
			else
			{
				$sql = "SELECT claveU FROM vw_individuo_observado WHERE claveU = ?  AND temporada=? AND tipoTempo=? AND Ncriades IS NOT NULL";
				$me = "No se puede cambiar la categoria no reproductiva a reproductiva: el animal tiene datos en destete.";
			}
			$q = $pdo->prepare($sql);
			$q->execute(array($pk_claveU,$pk_temporada,$pk_tipoTempo));
			$arr = $q->errorInfo();
			$t=$q->rowCount();
			Database::disconnect();
			if ($t>0) {
				$valid = false;
				$categoriaError = $me;
			}		

		}
		
		
		
        // validacion entre campos
        $eError = validarForm_temporadaEdita ($claveU,$temporada,$tipoTempo,$categoria,$comentario,$valid,$sexx);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE temporada set claveU=?,temporada=?,tipoTempo=?,categoria=?,comentario=? WHERE claveU=? AND temporada=? AND tipoTempo=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$temporada,$tipoTempo,$categoria,$SQLcomentario,$pk_claveU,$pk_temporada,$pk_tipoTempo));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: temporada_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM temporada where claveU=? AND temporada=? AND tipoTempo=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_temporada,$pk_tipoTempo));
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

                       $claveU = $data['claveU'];
                       $temporada = $data['temporada'];
                       $tipoTempo = $data['tipoTempo'];
                       $categoria = $data['categoria'];
                       $comentario = $data['comentario'];

                       $pk_claveU = $claveU;
                       $pk_temporada = $temporada;
                       $pk_tipoTempo = $tipoTempo;

                       Database::disconnect();
                   }
                }
        }

     $param = "?claveU=".$claveU."&temporada=".$temporada."&tipoTempo=".$tipoTempo;
     $param0 = "?claveU=".$claveU;
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
function disCria(vtt){
    /* categoria CRIA solo para REPRO */
    if (vtt=="REPRO") { 
        document.getElementById("categoria").options[1].style.display = "block";
        }
     else{
       document.getElementById("categoria").options[1].style.display = "none";
     }
}
         
</script>

<script>
function cambiaCate(c){
	/* si la categoría elegida tiene (sexo) en la descripción es reproductiva */
	desc=c.options[c.selectedIndex].text;
	reproNue=document.getElementById("cate_reproductiva_nueva");
	if (desc.indexOf("(")<0){
		reproNue.value='NO';
		}			
	else {
		reproNue.value='SI';	
		}
}
</script>


<?php if (!edita()) : ?>
<script>
function aVerVer() {
	$('#CRtemporada').find(':input').attr('disabled', 'disabled'); 
}
</script>

<?php endif ?>

		
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body onload='disCria(document.getElementById("tipoTempo").value); <?php echo !edita()?"aVerVer()":""?>'>
    <div class="container" style="width:90%">
	
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>




        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">registro de temporada</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRtemporada" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <input name="sexx" type="hidden"  value="<?php echo !empty($sexx)?$sexx:'';?>"> <!-- pk, clave anterior -->

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


                            <input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
                    </div>
                  </div>
				  <div class=col-sm-6>		
				  </div>	

				</div>

                <div class=row>				
				  <div class=col-sm-3>
	
                    <div class="form-group ">
                        <label for="temporada">A&ntilde;o</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="temporada" name="temporada" readonly required data-pentero
							data-dmin="<?php echo CONST_temporadaMin_min?>" data-dmax=<?php echo $tActual; ?>  
							data-error="<?php echo CONST_temporadaMin_men?> y &lt= <?php echo $tActual?>" value="<?php echo !empty($temporada)?$temporada:'';?>" >
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortemporada"></p>
                            <?php if (!empty($temporadaError)): ?>
                                <span class="help-inline"><?php echo $temporadaError;?></span>
                            <?php endif; ?>


                            <input name="pk_temporada" type="hidden"  value="<?php echo !empty($pk_temporada)?$pk_temporada:'';?>"> <!-- pk, clave anterior -->
                    </div>

    			  </div>
				  <div class="col-sm-3">		

                    <div class="form-group ">
                        <label for="tipoTempo">Tipo de temporada</label>
                            <input type="text" class="form-control input-sm" style="width:90px" id="tipoTempo" readonly  required  name="tipoTempo" value="<?php echo !empty($tipoTempo)?$tipoTempo:'';?>"   onchange=disCria(this.value);>
                            <div class="help-block with-errors"></div>
                            <p id="JSErrortipoTempo"></p>
                            <?php if (!empty($tipoTempoError)): ?>
                                <span class="help-inline"><?php echo $tipoTempoError;?></span>
                            <?php endif; ?>


                            <input name="pk_tipoTempo" type="hidden"  value="<?php echo !empty($pk_tipoTempo)?$pk_tipoTempo:'';?>"> <!-- pk, clave anterior -->
                    </div>
				  </div>
				</div>

                <div class=row>				
				  <div class="col-sm-5">		


                    <div class="form-group ">
                        <label for="categoria">Categor&iacute;a</label>
                            <select class="form-control input-sm" style=width:auto id="categoria" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="categoria" 
							onchange=cambiaCate(this)>
                                <option value="" <?php if ($categoria == "") {echo " selected";}?> ></option>
        <?php $pdo = Database::connect();
		$sql = "SELECT IDcategoria, cateDesc,sexoc,reproductiva FROM categoria WHERE (ordenCate>0 and ordenCate<90) and (sexoc='$sexx' or sexoc='CUALQUIERA') ORDER BY ordenCate";
        $q = $pdo->prepare($sql);
        $q->execute();
        if ($q->rowCount()==0) {
                Database::disconnect();
                echo 'no hay registros!!!';
                exit;
                }
        while ( $aCat = $q->fetch(PDO::FETCH_ASSOC) ) {
			if( !( ($aCat['IDcategoria'] == "PRAD" OR $aCat['IDcategoria'] == "PRIM") and substr(strtoupper($tipoTempo),0,1)<> 'R' ) ){
				echo str_repeat(" ",32). '<option value="' .$aCat['IDcategoria']. '"';
				 if ($categoria == $aCat['IDcategoria']) {
					 $cate_reproductiva=$aCat['reproductiva'];
					 $cate_anterior=$aCat['IDcategoria'];
					 $cate_reproductiva_nueva=$cate_reproductiva;
						 echo " selected";
				}
				if ($aCat['sexoc']=='CUALQUIERA') {
				echo '>' .$aCat['cateDesc'].'</option>'."\n";
				}
				else{
				 /* OJO: no sacar sexoc de la descripción - se usa en el html!!!!!! */
				 echo '>' .$aCat['cateDesc']. '&nbsp;&nbsp;&nbsp;('.$aCat['sexoc'].')</option>'."\n";
				}
			}
        }
        Database::disconnect();
        ?>
                            </select>
					<input name="cate_anterior" type="hidden"  value="<?php echo !empty($cate_anterior)?$cate_anterior:'';?>"> <!-- la categoria antes del cambio -->
					<input name="cate_reproductiva" type="hidden"  value="<?php echo !empty($cate_reproductiva)?$cate_reproductiva:'';?>"> <!-- si la categoria original es reproductiva -->
					<input name="cate_reproductiva_nueva" id="cate_reproductiva_nueva" type="hidden"  value="<?php echo !empty($cate_reproductiva_nueva)?$cate_reproductiva_nueva:'';?>"> <!-- si la categoria nueva es reproductiva -->
							
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
						<?php if ( edita()) : ?>
                        <button type="submit" id="guardar" name="guardar"class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php endif;?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                               <span class="alert alert-success"><h5>cambios guardados</h5></span>
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