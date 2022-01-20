<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	$tamanio_archivo=CONST_tamanioMaxArchivo;
	
    $par_elimina=1;
    
	/* TODOS PUEDEN AGREGAR PUBLICACIONES /*
	/*sin permiso de edición, fuera*/
	/*siErrorFuera(edita());   */

	$queTabla = 'publicaciones';

	$ID = null;
	$tipoPublicacion = null;
	$anio = null;
	$titulo = null;
	$autores = null;
	$abstractYmas = null;
	$doi = null;
	$tipoArchivo = null;
	$archivo = null;


    if ( !empty($_POST)) {
		$IDError = null;
		$tipoPublicacionError = null;
		$anioError = null;
		$tituloError = null;
		$autoresError = null;
		$abstractYmasError = null;
		$doiError = null;
		$tipoArchivoError = null;
		$archivoError = null;

		$eError = null;

		// los campos a validar
		$ID = limpia($_POST['ID']);
		$tipoPublicacion = limpia($_POST['tipoPublicacion']);
		$anio = limpia($_POST['anio']);
		$titulo = limpia($_POST['titulo']);
		$autores = limpia($_POST['autores']);
		$abstractYmas = limpia($_POST['abstractYmas']);
		$doi = limpia($_POST['doi']);
		$tipoArchivo = limpia($_POST['tipoArchivo']);
		$archivo = limpia($_POST['archivo']);

		
		// validacion: devuelve el error; los dos primeros parametros pasan x referencia
		//             el ultimo: true debe estar - false puede faltar
		$valid = true;

		/*$IDError  = validar_ID ( $ID , $valid,true);*/
		
		$anioError = validar_anioPublicacion ($anio, $valid, true) ;
		$tipoPublicacionError  = validar_tipoPublicacion ($tipoPublicacion , $valid,true);
		$tituloError  = validar_titulo ($titulo, $valid,true);
		$autoresError  = validar_autores ( $autores  , $valid,true);

		/* el archivo de la publicacion */				
		$archivoError  = validar_publiArchivo ($archivo,$tipoArchivo,$valid,true);				
		if (is_null($archivoError)) {
			$ueb_dir = dirPublicaciones; /* "publicaciones/"; */
			$ueb_Archi = $ueb_dir . basename($_FILES["archivoAsubir"]["name"]);
			$ArchiType = pathinfo($ueb_Archi,PATHINFO_EXTENSION);
			
			$txtError = "";
			$check = filesize($_FILES["archivoAsubir"]["tmp_name"]);
			if($check === false) {
				$txtError .= "Por favor, seleccionar archivo. ";
				$valid = false;
			}
/*echo $ueb_Archi." ".$ArchiType," ".$check;*/
			// tamaño archivo
			if ($_FILES["archivoAsubir"]["size"] > $tamanio_archivo) {
				$txtError .= CONST_tamanioMaxArchivo_men;
				$valid = false;
			}

			// formatos soportados
			if ($ArchiType != "pdf" && $ArchiType != "xlsx" && $ArchiType != "xls" ) {
				$txtError .= "Solo PDF o Power Point.";
				$valid = false;

			}
			if (!empty($txtError)) {
				$archivoError = $txtError;
			}
		}

		if ($valid) {
			
			// todo OK hasta acá, probamos subir el archivo
			if (!move_uploaded_file($_FILES["archivoAsubir"]["tmp_name"], $ueb_Archi)) {
					$eError =  "Ocurri&oacute; un error al subir el archivo!!";
					$valid = false;
				} 
				
		}

		if ($valid) {
			// nuevo registro
			// campos que pueden tener NULL
			$SQLdoi = ($doi == '' ? NULL : $doi);
			$SQLabstractYmas = ($abstractYmas == '' ? NULL : $abstractYmas);

			// inserta
			$tituH=MD5($titulo);
			$pdo = Database::connect();
			$sql = "INSERT INTO publicaciones (tipoPublicacion,anio,titulo,doi,autores,abstractYmas,archivo,tipoArchivo,tituloH) values(?,?,?,?,?,?,?,?,?)";
			$q = $pdo->prepare($sql);
			$q->execute(array($tipoPublicacion,$anio,$titulo,$SQLdoi,$autores,$SQLabstractYmas,$archivo,$tipoArchivo,$tituH));

			$arr = $q->errorInfo();

			Database::disconnect();

			if ($arr[0] <> '00000') {
				$eError = "Error MySQL: ".$arr[1];    //." ".$arr[2];
				}
			else {
				/*header('href="javascript:history.back()"');*  modelo */
				/*header('Location: x_index.php'); */
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
		function archis(){
			ar=document.getElementById("archivoAsubir");	
			if (ar.files[0].size> <?php echo CONST_tamanioMaxArchivo ?> ) {
				alert ("<?php echo CONST_tamanioMaxArchivo_men ?>");
				return;
			}
			arNombre=ar.files[0].name;
			arnom=arNombre.toUpperCase();
			document.getElementById("tipoArchivo").value = "";			
			if ('files' in ar) {
				if (ar.files.length == 0) {
					alert("seleccionar un archivo");
					return;
				} else {
					if (arnom.endsWith(".PDF")) {
						document.getElementById("tipoArchivo").value="PDF";
						
					} else if (arnom.endsWith(".PPT") || arnom.endsWith(".PPTX")) {
							document.getElementById("tipoArchivo").value="PowerPoint";
						} else {
							alert("seleccionar un archivo PDF o Power Point") ;
							return;
							};
					/* */
					var patt = new RegExp("<?php echo PATRON_archiPubli ?>");
					if (patt.test(arNombre)) {
						document.getElementById("archivo").value=arNombre;
					}
					else{
						document.getElementById("archivo").value="";					
						alert(arNombre+"\n"+"<?php echo PATRON_archiPubli_men ?>");
					}
				}
			} 
			else {
					alert("seleccionar un archivo");
				}
					
					
			
		}
	</script>
	

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

	
</head>

<body >
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">publicaci&oacute;n</h3>
            </div>
            <div class="panel-body">

				<!-- archivo -->
				<form data-toggle="validator" id="CRpublicacion" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post" enctype="multipart/form-data">				
					<div class="row">     
						<div class="col-sm-2"> 
							<label style="width:100%" class="btn btn-warning btn-sm btn-file">
								<span class="glyphicon glyphicon-file"></span> archivo
								<input type="file" style="display: none;"  accept=".pdf,.pptx,.ppt" name="archivoAsubir" id="archivoAsubir" onchange=archis()>
							</label>
						</div>
						
						<div class="col-sm-10">
							<div class="form-group ">
						
								<input type="text" class="form-control input-sm" id=archivo name=archivo required onkeydown="return false;" value="<?php echo !empty($archivo)?$archivo:'';?>">
									<div class="help-block with-errors"></div>
									<p id="JSErrorarchivo"></p>
							
							<?php if (!empty($archivoError)): ?>
								<span class="help-inline"><?php echo $archivoError;?></span>
							<?php endif; ?>
							</div>
						</div>
						
						

					</div>			


					<input type="hidden" id="tipoArchivo" name="tipoArchivo" value="<?php echo !empty($tipoArchivo)?$tipoArchivo:'';?>" >

						
						
						
					<div class="row">               
						<div class="col-sm-3">              
						
							<div class="form-group ">
								<label for="ID">IDentificador (auto)</label>
									<input type="text" class="form-control input-sm" style="width: 75px" id="ID" name="ID"  maxlength="5" readonly data-error="No puede faltar" value="<?php echo !empty($ID)?$ID:'';?>">
									<div class="help-block with-errors"></div>
									<p id="JSErrorID"></p>
									<?php if (!empty($IDError)): ?>
										<span class="help-inline"><?php echo $ID?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-5">              				
							<div class="form-group ">
								<label for="tipoPublicacion">Tipo publicaci&oacute;n</label>
									<select class="form-control input-sm" style="width: auto" id="tipoPublicacion" required  data-error="Seleccionar un elemento de la lista" name="tipoPublicacion">
										<option value="" <?php if ($tipoPublicacion == "") {echo " selected";}?> ></option>
										<option value="PAPER" <?php if ($tipoPublicacion == "PAPER") {echo " selected";}?> >PAPER</option>
										<option value="INFORME" <?php if ($tipoPublicacion == "INFORME") {echo " selected";}?> >INFORME O REPORTE</option>
										<option value="LIBRO" <?php if ($tipoPublicacion == "LIBRO") {echo " selected";}?> >LIBRO</option>
										<option value="CAPITULO DE LIBRO" <?php if ($tipoPublicacion == "CAPITULO DE LIBRO") {echo " selected";}?> >CAPITULO DE LIBRO</option>
										<option value="ARTICULO" <?php if ($tipoPublicacion == "ARTICULO") {echo " selected";}?> >ARTICULO DE DIVULGACI&Oacute;N</option>
										<option value="POSTER" <?php if ($tipoPublicacion == "POSTER") {echo " selected";}?> >POSTER</option>
										<option value="PRESENTACION" <?php if ($tipoPublicacion == "PRESENTACION") {echo " selected";}?> >PRESENTACION ORAL</option>
										<option value="NOTA" <?php if ($tipoPublicacion == "NOTA") {echo " selected";}?> >NOTA CIENT&Iacute;FICA</option>
										<option value="DATA PAPER" <?php if ($tipoPublicacion == "DATA PAPER") {echo " selected";}?> >DATA PAPER</option>
										<option value="OTRO" <?php if ($tipoPublicacion == "OTRO") {echo " selected";}?> >otro</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrortipoPublicacion"></p>
									<?php if (!empty($tipoPublicacionError)): ?>
										<span class="help-inline"><?php echo $tipoPublicacionError;?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-4">              
							
							<div class="form-group ">
								<label for="anio">A&ntilde;o</label>
									<input type="text" class="form-control input-sm" style="width:105px" id="anio" name="anio" 		data-dmin="<?php echo CONST_anioPub_min?>" data-dmax="<?php echo CONST_anioPub_max?>" data-pentero 
									data-error="<?php echo CONST_anioPub_men?>"
									required value="<?php echo !empty($anio)?$anio:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErroranio"></p>
									<?php if (!empty($anioError)): ?>
										<span class="help-inline"><?php echo $anioError;?></span>
									<?php endif; ?>
							</div>
						</div>
					</div>


						
						
							<div class="form-group ">
								<label for="titulo">T&iacute;tulo</label>
									<textarea class="form-control input-sm" id="titulo" name="titulo" rows="2" maxlength="700" required 
									oninput="mayus(this);" data-patron="<?php echo PATRON_tituPubli?>" data-error="<?php echo PATRON_tituPubli_men?>" ><?php echo !empty($titulo)?$titulo:'';?></textarea>						
									<div class="help-block with-errors"></div>
									<p id="JSErrortitulo"></p>
									<?php if (!empty($tituloError)): ?>
										<span class="help-inline"><?php echo $tituloError;?></span>
									<?php endif; ?>
							</div>				
						
						
							<div class="form-group ">
								<label for="autores">Autores</label>
									<textarea class="form-control input-sm" id="autores" name="autores" rows="2" maxlength="1500"  required 
									oninput="mayus(this);" data-patron="<?php echo PATRON_autoresPubli?>" data-error="<?php echo PATRON_autoresPubli_men?>" ><?php echo !empty($autores)?$autores:'';?></textarea>						
									<div class="help-block with-errors"></div>
									<p id="JSErrorautores"></p>
									<?php if (!empty($autoresError)): ?>
										<span class="help-inline"><?php echo $autoresError;?></span>
									<?php endif; ?>
							</div>				

						
							<div class="form-group ">
								<label for="abstractYmas">Palabras claves, revista, conferencia, etc.... o cualquier otro dato que sirva a una b&uacute;squeda</label>
									<textarea class="form-control input-sm" id="abstractYmas" name="abstractYmas" rows="2" maxlength="5000" ><?php echo !empty($abstractYmas)?$abstractYmas:'';?></textarea>						
									<div class="help-block with-errors"></div>
									<p id="JSErrorabstractYmas"></p>
									<?php if (!empty($abstractYmasError)): ?>
										<span class="help-inline"><?php echo $abstractYmasError;?></span>
									<?php endif; ?>
							</div>					

							
							<div class="form-group ">
								<label for="doi">DOI</label>
									<input type="text" class="form-control input-sm" id="doi" name="doi" maxlength="120" value="<?php echo !empty($doi)?$doi:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrordoi"></p>
									<?php if (!empty($doiError)): ?>
										<span class="help-inline"><?php echo $doiError;?></span>
									<?php endif; ?>
							</div>				
						
						
							
		 
					
					
 

                    <br>
                    <div class="form-actions">
                        <?php if (!empty($eError) or empty($valid) or !$valid ): ?>
							<button type="submit" class="btn btn-primary btn-sm">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                          <?php endif;?>

                        <a class="btn btn-default btn-sm" onclick=parent.cierroM("publicaciones_index.php")>atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                          <?php if (empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>registro agregado</h5></span>
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
		pentero: function($el) {
			var r = validatorEntero ($el);     /* en imiei.js */
			if (!r) {
				return $el.data("error");
			}				
		}	  
	}
});
</script>
            </div>
        </div>
    </div> <!-- /container -->
</body>
</html>