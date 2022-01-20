<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'macho';

    /* sin permiso de edición, fuera*/
    siErrorFuera(edita());

    /*parametros inválidos, fuera*/
        $claveU = null;
        $fecha = null;
        $status = null;
        $estadoFisico = null;
        $entornoAlAlfa = null;
        $alAlfaCu = null;
        $haremHembras = null;
        $haremPups = null;
        $comentario = null;

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

    $fecha = null;
    if (isset($_GET["fecha"])) {
        $fecha=$_GET["fecha"];
        $m = validar_fecha ($fecha,$v,true);
    }
    else{
        if (!isset($_GET["fecha"]) and !isset($_POST["fecha"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);


    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $statusError = null;
        $estadoFisicoError = null;
        $entornoAlAlfaError = null;
		$alAlfaCu = null;
        $haremHembrasError = null;
        $haremPupsError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $status = limpia($_POST['status']);
        $estadoFisico = limpia($_POST['estadoFisico']);
        $entornoAlAlfa = limpia($_POST['entornoAlAlfa']);
        $alAlfaCu = limpia($_POST['alAlfaCu']);
        $haremHembras = limpia($_POST['haremHembras']);
        $haremPups = limpia($_POST['haremPups']);
        $comentario = limpia($_POST['comentario']);

		// segun status....
	    $aSta=array('ALFA','SOLO','sd');
		if (in_array($status,$aSta)) {
			$entornoAlAlfa = null;
			$alAlfaCu = null;
		}
		if ($status <>'ALFA') {
			$haremHembras = null;
			$haremPups = null;
		}

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $statusError = validar_status ($status,$valid,true);
        $estadoFisicoError = validar_estadoFisico ($estadoFisico,$valid,false);
		if (!in_array($status,$aSta)) {
			$entornoAlAlfaError = validar_entornoAlAlfaCu ($entornoAlAlfa,$alAlfaCu,$fecha,$valid);
		}
        if ($status == 'ALFA') {
			$haremHembrasError = validar_haremHembras ($haremHembras,$valid,false);
			$haremPupsError = validar_haremPups ($haremPups,$valid,false);
		}

    // validacion entre campos
        $eError = validarForm_macho ($claveU,$fecha,$status,$estadoFisico,$entornoAlAlfa,$alAlfaCu,$haremHembras,$haremPups,$comentario,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {

			// campos que pueden tener NULL
            $SQLestadoFisico = ($estadoFisico == '' ? NULL : $estadoFisico);
            $SQLentornoAlAlfa = ($entornoAlAlfa == '' ? NULL : $entornoAlAlfa);
            $SQLalAlfaCu = ($alAlfaCu == '' ? NULL : $alAlfaCu);
            $SQLharemHembras = ($haremHembras == '' ? NULL : $haremHembras);
            $SQLharemPups = ($haremPups == '' ? NULL : $haremPups);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // inserta
            $pdo = Database::connect();
            $sql = "INSERT INTO macho (claveU,fecha,status,estadoFisico,entornoAlAlfa,alAlfaCu,haremHembras,haremPups,comentario) values(?,?,?,?,?,?,?,?,?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$status,$SQLestadoFisico,$SQLentornoAlAlfa,$SQLalAlfaCu,$SQLharemHembras,$SQLharemPups,$SQLcomentario));

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

	 $temporada=date($fecha);
     $ctempox = 'condi=temporada="'.$temporada.'" AND sexo="MACHO" AND claveU<>"'.$claveU.'"';

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">

	<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">

    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>

    <link rel="stylesheet" href="login/style/main.css">

<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
<script>

function EsAlfa(c,t){
	document.getElementById('entornoAlAlfa').value=t;
	document.getElementById('alAlfaCu').value=c;
	document.getElementById("ver").href="tr_resumen_individuo.php?claveU="+c;
	$('#ver').removeClass('disabled');
}

function alfaManual(t){
	mayus(t);
	document.getElementById("alAlfaCu").value='';
	document.getElementById("ver").href="";
	$('#ver').addClass('disabled');
}

function dameResto(v){
	if(v=='sd' || v=='SOLO' || v=="") {
		$('#pAlfa').attr("hidden",true);
		$('#pOtro').attr("hidden",true);
	}else{
		if(v=='ALFA') {
			$('#pAlfa').attr("hidden",false);
			$('#pOtro').attr("hidden",true);
		}else{
			$('#pAlfa').attr("hidden",true);
			$('#pOtro').attr("hidden",false);
			
		}
	}
}
function inicial(){
	a=document.getElementById('status').value;
	dameResto(a);
	c=document.getElementById('alAlfaCu').value;
	if (c!=""){
	   document.getElementById("ver").href="tr_resumen_individuo.php?claveU="+c;
	   $('#ver').removeClass('disabled');
	}
	
	
}
</script>


<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 
    
    var tablin = $("#tablax").DataTable( {
     
     "sAjaxSource": 'macho_repro_tb.php?<?php echo $ctempox?>',
     stateSave: true,
     "columns": [
     { "width": "10px"},
     { "width": "10px"},
     { "width": "10px"},
     { "width": "10px"},
     { "width": "100px"},
     { "width": "150px"},
     { "width": "150px"},
    null
    ],
	"columnDefs": [
			{"orderable": false, "targets": 7 }
	],	
     "stateDuration": -1, 
     "pageLength": 4,
     "lengthMenu": [ 4,10,15,20, 40, 80, 100 ],
     "pagingType": "full",
     "language": {
         "emptyTable": "No hay machos reproductivos en esta temporada",
         "lengthMenu": "Mostrar _MENU_ registros",
         "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
         "infoFiltered":   "(filtrado de _MAX_ registros totales)",
         "infoEmpty":      "L&iacute;nea 0 de 0",
         "loadingRecords": "Cargando...",
         "processing":    "Procesando...",
         "search":         "Buscar:",
         "zeroRecords":    "No se encuentran registros",
         "paginate": {
            "first":      "|<<",
            "last":       ">>|",
            "next":       ">",
            "previous":   "<"
            },   
         "aria": {
            "sortAscending":  ": activar para ordenar la columna ascendente",
            "sortDescending": ": activar para ordenar la columna descendente"
            }   
         }

    } );
    
    $('#tablax').DataTable().column( 0 ).visible( false );
    $('#tablax').DataTable().column( 1 ).visible( false );
    $('#tablax').DataTable().column( 2 ).visible( false );
    $('#tablax').DataTable().column( 3 ).visible( false );
    $('#tablax').DataTable().column( 4 ).visible( false );


        
       
} );

</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body onload=inicial()>
    <div class="container" style="width:90%">
	
<button type="button" class="botonayuda"  title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">nuevo registro de macho reproductivo</h3>
            </div>
            <div class="panel-body">

                <form data-toggle="validator" id="CRmacho" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

					<div class="row">
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="claveU">ClaveU</label>
									<input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorclaveU"></p>
									<?php if (!empty($claveUError)): ?>
										<span class="help-inline"><?php echo $claveUError;?></span>
									<?php endif; ?>
							</div>

						</div>
						<div class="col-sm-2">
							<div class="form-group ">
								<label for="fecha">Fecha</label>
									<input type="text" class="form-control input-sm" style="width:100px" id="fecha" name="fecha" placeholder="AAAA-MM-DD"  pattern="<?php echo PATRON_fecha;?>" maxlength="10" required readonly data-error="<?php echo PATRON_fecha_men;?>" value="<?php echo !empty($fecha)?$fecha:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorfecha"></p>
									<?php if (!empty($fechaError)): ?>
										<span class="help-inline"><?php echo $fechaError;?></span>
									<?php endif; ?>
							</div>
						</div>
						<div class="col-sm-2">
						</div>
					
						<div class="col-sm-3">
							<div class="form-group ">
								<label for="status">Status</label>
									<select class="form-control input-sm" style="width: auto" id="status" data-error="Seleccionar un elemento de la lista"   required  data-error="Seleccionar un elemento de la lista" name="status" onchange="dameResto(this.value);">
										<option value="" <?php if ($status == "") {echo " selected";}?> ></option>
										<option value="sd" <?php if ($status == "sd") {echo " selected";}?> >sd</option>
										<option value="ALFA" <?php if ($status == "ALFA") {echo " selected";}?> >ALFA</option>
										<option value="PERIFERICO" <?php if ($status == "PERIFERICO") {echo " selected";}?> >PERIFERICO</option>
										<option value="CERCANO" <?php if ($status == "CERCANO") {echo " selected";}?> >CERCANO</option>
										<option value="LEJANO" <?php if ($status == "LEJANO") {echo " selected";}?> >LEJANO</option>
										<option value="SOLO" <?php if ($status == "SOLO") {echo " selected";}?> >SOLO</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorstatus"></p>
									<?php if (!empty($statusError)): ?>
										<span class="help-inline"><?php echo $statusError;?></span>
									<?php endif; ?>
							</div>
						</div>
						
						<div class="col-sm-3">							
							<div class="form-group ">
								<label for="estadoFisico">Estado f&iacute;sico</label>
									<select class="form-control input-sm" style="width: auto" id="estadoFisico"  data-error="Seleccionar un elemento de la lista" name="estadoFisico" >
										<option value="" <?php if ($estadoFisico == "") {echo " selected";}?> ></option>
										<option value="BUENO" <?php if ($estadoFisico == "BUENO") {echo " selected";}?> >BUENO</option>
										<option value="LASTIMADO" <?php if ($estadoFisico == "LASTIMADO") {echo " selected";}?> >LASTIMADO</option>
										<option value="LASTIMADO+" <?php if ($estadoFisico == "LASTIMADO+") {echo " selected";}?> >LASTIMADO+</option>
										<option value="FLACO" <?php if ($estadoFisico == "FLACO") {echo " selected";}?> >FLACO</option>
										<option value="FLACO+" <?php if ($estadoFisico == "FLACO+") {echo " selected";}?> >FLACO+</option>
									</select>
									<div class="help-block with-errors"></div>
									<p id="JSErrorestadoFisico"></p>
									<?php if (!empty($estadoFisicoError)): ?>
										<span class="help-inline"><?php echo $estadoFisicoError;?></span>
									<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="row" id=pOtro >
						<div class="col-sm-4">							
							<div class="form-group ">
								<label style="color:#464fa0">Entorno al alfa</label>
								<label for="conCual">marca o tag original</label>
									<input type="text" class="form-control input-sm" id="entornoAlAlfa" name="entornoAlAlfa"  pattern="<?php echo PATRON_marcaOtag;?>" maxlength="30" data-error="<?php echo PATRON_marcaOtag_men;?>" value="<?php echo !empty($entornoAlAlfa)?$entornoAlAlfa:'';?>" oninput="alfaManual(this);">
									<div class="help-block with-errors"></div>
									<p id="JSErrorentornoAlAlfa"></p>
									<?php if (!empty($entornoAlAlfaError)): ?>
										<span class="help-inline"><?php echo $entornoAlAlfaError;?></span>
									<?php endif; ?>
							</div>
								<label for="alAlfaCu">claveU desde "machos reproductivos"</label>
							<div class="form-group ">
								  <div class="row">
  								   <div class="col-sm-5">
									<input type="text" class="form-control input-sm" style="width:80px" id="alAlfaCu" name="alAlfaCu" readonly  value="<?php echo	!empty($alAlfaCu)?$alAlfaCu:'';?>" >
  								   </div>
								   
  								   <div class="col-sm-2">
									<a class="btn btn-default btn-sm disabled" title="abrir ficha"  id=ver  target="_blank"> <span class="glyphicon glyphicon-folder-open"></span></a>
  								   </div>
								  </div>
									<div class="help-block with-errors"></div>
									<p id="JSErroralAlfaCu"></p>
									<?php if (!empty($alAlfaCuError)): ?>
										<span class="help-inline"><?php echo $alAlfaCuError;?></span>
									<?php endif; ?>
							</div>							
						</div>
                    <div class=col-sm-8>
                        <div class="container" style="width:100%">                   
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">machos reproductivos en la temporada </h3>
                                </div>
                                <div class="panel-body">

                                    <table id="tablax" style="width:100%" class="display table table-striped table-bordered">
                                      <thead>
                                        <tr>
                                          <th>ClaveU</th>
                                          <th>Temp.</th>
                                          <th>Tipo</th>
                                          <th>Categor&iacute;a</th>
                                          <th>Sexo</th>
                                          <th>Tags</th>
                                          <th>Marcas</th>
                                          <th>sel</th>
                                        </tr>
                                      </thead>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>							
					</div>

					<div class="row" id=pAlfa >
						<div class="col-sm-3">							
							<div class="form-group ">
								<label for="haremHembras">Cantidad de hembras en el harem</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="haremHembras" name="haremHembras"  data-pentero 
									data-dmin="<?php echo CONST_haremHembras_min?>" data-dmax="<?php echo CONST_haremHembras_max?>"   
									data-error="<?php echo CONST_haremHembras_men?>" value="<?php echo !empty($haremHembras)?$haremHembras:'';?>">
									<div class="help-block with-errors"></div>
									<p id="JSErrorharemHembras"></p>
									<?php if (!empty($haremHembrasError)): ?>
										<span class="help-inline"><?php echo $haremHembrasError;?></span>
									<?php endif; ?>
							</div>
						</div>

						<div class="col-sm-3">							
							<div class="form-group ">
								<label for="haremPups">Cantidad de pups en el harem</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="haremPups" name="haremPups" data-pentero 
									data-dmin="<?php echo CONST_haremPups_min?>" data-dmax="<?php echo CONST_haremPups_max?>"   
									data-error="<?php echo CONST_haremPups_men?>" value="<?php echo !empty($haremPups)?$haremPups:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorharemPups"></p>
									<?php if (!empty($haremPupsError)): ?>
										<span class="help-inline"><?php echo $haremPupsError;?></span>
									<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="row" >
						<div class="col-sm-4">							
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

						   <div class="form-actions">
							<div class="col-sm-4">							
									<br>
								<?php if (empty($guardado)): ?>                                     
									<button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>
								<?php endif; ?>

									<a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</div>							
							<div class="col-sm-4">							

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