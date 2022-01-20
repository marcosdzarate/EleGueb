<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

	$tamanio_archivo=CONST_tamanioMaxArchivo;
	$dir_imagenes=dirImagenes;

    /*parametros inválidos, fuera*/
    $v=true;

    $pk_claveU = null;
    if (isset($_GET["claveU"]) ) {
        $pk_claveU=$_GET["claveU"];
        $m = validar_claveU ($pk_claveU,$v,true);
    }
    else{
        if (!isset($_GET["claveU"]) and !isset($_POST["pk_claveU"])  ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $pk_fecha = null;		/* fecha puede faltar, cuando viene desde ficha, SOLO VER FOTOS Y VIDEOS*/
	$conFecha=false;	
    if (isset($_GET["fecha"]) ) {
		$conFecha=true;		
        $pk_fecha=$_GET["fecha"];
        $m = validar_fecha ($pk_fecha,$v,true);
    }
    siErrorFuera($v);
	if (isset($_POST["pk_fecha"])){
		$conFecha=true;				
	}
	$archivoError = null;
	$prefijoError = null;
	$eError = null;
	$anue=null;
	$ueb_Archi=null;

	/* para manejo de archivos */
	$extensiones_imagenes = ['png', 'jpg', 'jpeg', 'gif'] ;
	$extensiones_video = ['mp4'];
	$extensiones_permitidas = array_merge ($extensiones_imagenes,$extensiones_video);   /*['png', 'jpg', 'jpeg', 'gif', 'mp4'];*/
	$extensiones_may = array_map('strtoupper', $extensiones_permitidas);
    $ext_array = array_merge($extensiones_permitidas,$extensiones_may);
	$f_accept = ".".implode(",.",$extensiones_permitidas);				/* esto es para el form */
	$f_glob   = implode(",",$ext_array); 
	$f_js     = "['".implode("','",$extensiones_may)."']";
	
	/* prefijo de claveU y fecha */
	if (!empty($pk_fecha)) {
		$prefi_cf = $pk_claveU."-".str_replace("-","",$pk_fecha).'-';
	}
	else {
		$prefi_cf = $pk_claveU."-";
	}

	
    if ( !empty($_POST)) {
		$archivoError = null;

		$eError = null;

		// los campos a validar
		$archivo = limpia($_POST['archivo']);
		$pk_claveU = limpia($_POST['pk_claveU']);
		$pk_fecha = limpia($_POST['pk_fecha']);
		$prefijo = limpia($_POST['prefijo']);

		if (!empty($pk_fecha)) {
			$prefi_cf = $pk_claveU."-".str_replace("-","",$pk_fecha).'-';
		}
		else {
			$prefi_cf = $pk_claveU."-";
		}
		
		// validacion: devuelve el error; los dos primeros parametros pasan x referencia
		//             el ultimo: true debe estar - false puede faltar
		$valid = true;

		/* validando archivo  */
		if  (preg_match("/".PATRON_archiFotos."/", $archivo, $a) <> 1) {
			   $valid=false;
			   $archivoError = PATRON_archiFotos_men;
			}           
		$qex = strtoupper(substr($archivo,strrpos($archivo,'.')+1,10));
		if (!in_array($qex,$extensiones_may)) {
			$valid=false;
			$archivoError .='El tipo de archivo no es v&aacute;lido. ';
		}
	
		/* validando prefijo  */				
		if(!empty($prefijo)) {
			if  (preg_match("/".PATRON_prefiFotos."/", $prefijo, $a) <> 1) {
			   $valid=false;
			   $prefijoError = PATRON_prefiFotos_men;
			}
		}
		
		if ($valid) {
		
			if (is_null($archivoError)) {
				$ueb_dir = $dir_imagenes;
				$ueb_Archi = $ueb_dir . basename($_FILES["archivoAsubir"]["name"]);
				
				$txtError = "";
				$check = filesize($_FILES["archivoAsubir"]["tmp_name"]);
				if($check === false) {
					$txtError .= "Por favor, seleccionar archivo. ";
					$valid = false;
				}
				// tamaño archivo
				if ($_FILES["archivoAsubir"]["size"] > $tamanio_archivo) {
					$txtError .= CONST_tamanioMaxArchivo_men;
					$valid = false;
				}
				// formatos soportados
				if (!empty($txtError)) {
					$archivoError = $txtError;
				}
			}

			if ($valid) {
				
				/* todo OK hasta acá, probamos subir el archivo */
				if (!move_uploaded_file($_FILES["archivoAsubir"]["tmp_name"], $ueb_Archi)) {
						$eError =  "Ocurri&oacute; un error al subir el archivo!!";
						$valid = false;
					}
				
				if($valid) {
					
					/* prefijo de claveU y fecha */
					if (!empty($pk_fecha)) {
						$prefi_cf = $pk_claveU."-".str_replace("-","",$pk_fecha).'-';
					}
					else {
						$prefi_cf = $pk_claveU."-";
					}
					
					/* renombrar archivo */
					$pref = $prefi_cf;
					if(!empty($prefijo) ){
						$pref .= $prefijo."-";
					}
					$anue = str_replace($dir_imagenes,$dir_imagenes.$pref,$ueb_Archi);
					rename($ueb_Archi,$anue);
					
					$archivo="";
					
				}
				
					
			}
		
		
		
		
		}
		
		
	}		



	
     $param  = "?claveU=".$pk_claveU."&fecha=".$pk_fecha;
	
	
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>
    
    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    
    <script src="js/imiei.js"></script>
    
    <link rel="stylesheet" href="login/style/main.css">
    
    <style>
    body {
        color:white;
    }
    
    a img {
        background-color:#FFF;
        margin: 20px 0;
        padding:10px;
        border-radius:4px;
        -moz-border-radius:4px;
        -webkit-border-radius:4px;
        box-shadow: 0 0 8px #5F5F5F;
        -moz-box-shadow: 0 0 8px #5F5F5F;
        -webkit-box-shadow: 0 0 8px #5F5F5F;
    }
	.caption > p{
		font-size:12px;
	}
	
video {
    width: 100%;
    height: auto;
}	
    </style>
    
    <script> 
		function cambriaPre() {
			if (document.getElementById("archivoRenombrado").innerHTML!="") {
				document.getElementById("archivo").value=arNombre;
				var pre=document.getElementById("prefijo").value+"-";
				if (pre=="-") {
					pre="";
				}
				document.getElementById("archivoRenombrado").innerHTML = "<?php echo $prefi_cf?>"+pre+arNombre;document.getElementById("archivoRenombrado").innerHTML;
			}
		}
		
        function archis(){
			var aExtensiones = <?php echo $f_js; ?>;  /* extensiones permitidas */
            ar=document.getElementById("archivoAsubir");    
            if (ar.files[0].size> <?php echo CONST_tamanioMaxArchivo ?> ) {
                alert ("archivo demasiado grande, max <?php echo CONST_tamanioMaxArchivoM ?>");
                return;
            }
            arNombre=ar.files[0].name;
            arnom=arNombre.toUpperCase();
            if ('files' in ar) {
                if (ar.files.length == 0) {
                    alert("seleccionar un archivo");
                    return;
                } else {
					var fExten = arnom.slice(arnom.lastIndexOf(".")+1, 100);
					if( aExtensiones.indexOf(fExten) < 0){
						alert("Ese tipo de archivo no se permite.");
					}
					else{
						/* */
						var patt = new RegExp("<?php echo PATRON_archiFotos ?>");
						if (patt.test(arNombre)) {
							document.getElementById("archivo").value=arNombre;
							var pre=document.getElementById("prefijo").value+"-";
							if (pre=="-") {
								pre="";
							}
							document.getElementById("archivoRenombrado").innerHTML = "<?php echo $prefi_cf?>"+pre+arNombre;
						}
						else {
							document.getElementById("archivo").value="";
							document.getElementById("archivoRenombrado").innerHTML ="";
							alert(arNombre+"\n"+"<?php echo PATRON_archiFotos_men ?>");
						}
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
<body class=bodycolor>
<?php
require_once 'tb_barracerrar.php';
?>



<form data-toggle="validator" id="CRfotos" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post" enctype="multipart/form-data">              
	<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>">
	<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>">
	
   <div class="container" style="width:90%">
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">fotos/videos (fotogrametr&iacute;a y m&aacute;s) - claveU: <?php echo $pk_claveU;?> <?php echo $conFecha?"- fecha: ".$pk_fecha:"";?></h3>
            </div>
            <div class="panel-body" >
					<div class="row">     
                        <div class="col-sm-4"> 
												
							<div class="form-group ">
								<label for="prefijo" style="color:black">prefijo para renombrar archivo:</label>

									<input type="text" class="form-control input-sm" onchange="cambriaPre()" id="prefijo" name="prefijo" maxlength="25" oninput="mayus(this);" pattern="<?php echo PATRON_prefiFotos;?>" data-error="<?php echo PATRON_prefiFotos_men;?>" value="<?php echo !empty($prefijo)?$prefijo:'';?>"	>
									<div class="help-block with-errors"></div>
									<p id="JSErrorprefijo"></p>
									<?php if (!empty($prefijoError)): ?>
										<span class="help-inline"><?php echo $prefijoError;?></span>
									<?php endif; ?>
							</div>
                        </div>


						

                    </div> 
					<!-- archivo -->
                    <div class="row">     
                        <div class="col-sm-2"> 
                            <label style="width:100%" class="btn btn-warning btn-sm btn-file">
                                <span class="glyphicon glyphicon-picture"></span> imagen/video
                                <input type="file" style="display: none;"  accept="<?php echo $f_accept;?>" name="archivoAsubir" id="archivoAsubir" onchange=archis()>
                            </label>
                        </div>
                        
                        <div class="col-sm-8">
                            <div class="form-group ">
                        
                                <input type="text" class="form-control input-sm" id=archivo name=archivo required onkeydown="return false;"
								value="<?php echo !empty($archivo)?$archivo:'';?>">
                                    <div class="help-block with-errors"></div>
                                    <p id="JSErrorarchivo"></p>
                            <?php if (!empty($archivoError)): ?>
                                <span class="help-inline"><?php echo $archivoError;?></span>
                            <?php endif; ?>

							</div>
                        </div>
                        
                        <div class="col-sm-2"> 
							<button type="submit" class="btn btn-primary btn-sm" id="subir">subir archivo
                                <span class="glyphicon glyphicon-upload"></span> 
							</button>						
                        </div>
                    </div>

                    <div class="row">     
                        <div class="col-sm-2"> 
                            <label style="color:black" >
                                se renombra a
                            </label>
                        </div>
                        
                        <div class="col-sm-8">
                                    <p id="archivoRenombrado" style="color:black;font-size:12px"></p>

						</div>
                    </div>
 
            </div>  
        </div>
    </div>
                
</form>






    <div class="container">

        <div class="row">
        <?php
        $path = $dir_imagenes; // path to images folder
        $file_count = count(glob($path . $prefi_cf. "*.{".$f_glob."}", GLOB_BRACE));
        if($file_count > 0)
        {
            $fp = opendir($path);
            $ncol=0;
            while($file = readdir($fp))
            {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, $ext_array) and strpos($file, $prefi_cf) === 0   ) /* solo las que comienzan con claveU y fecha */
                {
					$ext_minus= strtolower($ext);
                    $file_path = $path . $file;?>
                    <div class="col-sm-4">
					
					   <?php if (in_array($ext_minus, $extensiones_imagenes)) : ?>
							<a href="<?php echo $file_path; ?>" title="" data-gallery target="_blank"><img src="<?php echo $file_path; ?>" class="img-responsive" /></a>

						<?php else : ?>
							<video width="400" controls>
							  <source src="<?php echo $file_path; ?>" type="video/mp4">
							  Your browser does not support HTML5 video.
							</video>						
						<?php endif ?>
						
						<?php if (edita()) : ?>
						<button class="btn btn-danger btn-sm" style="position:absolute;top:40px;left:30px" title="eliminar este archivo" onclick='ventanaMini("fotos_borrar.php<?php echo $param ?>&archivo=<?php echo $file; ?>","eliminar imagen/video");'><span class="glyphicon glyphicon-erase"></span></button> 
						<?php endif ?>
						
        <div class="caption" >
          <p><?php echo $file; ?></p>
        </div>                      
                    </div>
                    
                <?php
                $ncol +=1;
                if($ncol==3) {
                    ?>
        </div>

        <div class="row">
                    <?php
                }
                }
            }
            closedir($fp);
        }
        else
        {
            echo "No hay im&aacute;genes";
        }
        ?>
        </div>
    </div>
	
	
<?php
/*
	echo $extensiones_permitidas." extensiones_permitidas<br>";
	echo $extensiones_may." extensiones_may<br>";
	echo $f_accept." f_accept<br>";
	echo $f_glob." f_glob<br>"; 
	
	echo $ueb_Archi." ueb_Archi<br>";  
	echo $prefijo."  prefijo<br>";
	echo $anue." anue<br>";
	echo $path." path<br>";
	echo $dir_imagenes." dir_imagenes<br>";
	echo $prefi_cf." prefi_cf<br>";
*/
?>
	
	
<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>	
	
</body>
</html>