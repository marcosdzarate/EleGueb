<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'copula';

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

    $pk_hora = null;
    if (isset($_GET["hora"])) {
        $pk_hora=$_GET["hora"];
        $m = validar_hora ($pk_hora,$v,true);
    }
    else{
        if (!isset($_GET["hora"]) and !isset($_POST["pk_hora"]) ){
            $v=false;
        }
    }
    siErrorFuera($v);

    $xtrx = null;  /* sexo */
    if (isset($_GET["xtrx"])) {
        $xtrx=$_GET["xtrx"];
        $v = ($xtrx=="HEMBRA" or $xtrx=="MACHO");
    }
    else{
        if (!isset($_GET["xtrx"]) and !isset($_POST["xtrx"]) )
        {
            $v=false;
        }
    }
    siErrorFuera($v);

    if ( !empty($_POST)) {
        // para errores de validacion
        $claveUError = null;
        $fechaError = null;
        $horaError = null;
        $conCualError = null;
        $conCualCuError = null;
        $duracionError = null;
        $comentarioError = null;

        $eError = null;

        // los campos a validar
        $claveU = limpia($_POST['claveU']);
        $fecha = limpia($_POST['fecha']);
        $hora = limpia($_POST['hora']);
        $conCual = limpia($_POST['conCual']);
        $conCualCu = limpia($_POST['conCualCu']);
        $duracion = limpia($_POST['duracion']);
        $comentario = limpia($_POST['comentario']);
        
		$xtrx = limpia($_POST['xtrx']); /* sexo */

		
        // valores anteriores de campos clave
        $pk_claveU=limpia($_POST['pk_claveU']);
        $pk_fecha=limpia($_POST['pk_fecha']);
        $pk_hora=limpia($_POST['pk_hora']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $claveUError = validar_claveU ($claveU,$valid,true);
        $fechaError = validar_fecha ($fecha,$valid,true);
        $horaError = validar_hora ($hora,$valid,true);
        $conCualError = validar_conCualyCu ($conCual,$conCualCu,$fecha,$xtrx,$valid);
        $duracionError = validar_duracion ($duracion,$valid,false);

        // validacion entre campos
        $eError = validarForm_copula ($claveU,$fecha,$hora,$conCual,$duracion,$comentario,$valid);

        // actualizar los datos
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL
            $SQLconCual = ($conCual == '' ? NULL : $conCual);
            $SQLconCualCu = ($conCualCu == '' ? NULL : $conCualCu);
            $SQLduracion = ($duracion == '' ? NULL : $duracion);
            $SQLcomentario = ($comentario == '' ? NULL : $comentario);

            // actualiza
            $pdo = Database::connect();
            $sql = "UPDATE copula set claveU=?,fecha=?,hora=?,conCual=?,conCualCu=?,duracion=?,comentario=? WHERE claveU=? AND fecha=? AND hora=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($claveU,$fecha,$hora,$SQLconCual,$SQLconCualCu,$SQLduracion,$SQLcomentario,$pk_claveU,$pk_fecha,$pk_hora));

            $arr = $q->errorInfo();

            Database::disconnect();

            if ($arr[0] <> '00000') {
                $eError = "Error MySQL: ".$arr[1]." ".$arr[2];
                }
            else {
                /*header('href="javascript:history.back()"');*  modelo */
                /*header('Location: copula_index.php');*/
                /*exit;*/

                 }
            }
        }

        else { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM copula where claveU=? AND fecha=? AND hora=? ";
            $q = $pdo->prepare($sql);
            $q->execute(array($pk_claveU,$pk_fecha,$pk_hora));
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
                       $fecha = $data['fecha'];
                       $hora = $data['hora'];
                       $conCual = $data['conCual'];
                       $conCualCu = $data['conCualCu'];
                       $duracion = $data['duracion'];
                       $comentario = $data['comentario'];

                       $pk_claveU = $claveU;
                       $pk_fecha = $fecha;
                       $pk_hora = $hora;

                       Database::disconnect();
                   }
                }
        }

     $param0 = "?claveU=".$claveU."&fecha=".$fecha."&xtrx=".$xtrx;
	 if ($xtrx=='HEMBRA'){
		 $sexoO='MACHO';
	 }
	 else{
		 $sexoO='HEMBRA';		 
	 }
	 $temporada=date($fecha);
     $ctempox = 'condi=temporada="'.$temporada.'" AND sexo="'.$sexoO.'"';


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

function EsLaPareja(c,t){
	document.getElementById('conCual').value=t;
	document.getElementById('conCualCu').value=c;
	document.getElementById("ver").href="tr_resumen_individuo.php?claveU="+c;
	$('#ver').removeClass('disabled');
}

function noPareja(t){
	mayus(t);
	document.getElementById("conCualCu").value='';
	document.getElementById("ver").href="";
	$('#ver').addClass('disabled');
}

function inicial(){	
	c=document.getElementById('conCualCu').value;
	if (c!=""){
	   document.getElementById("ver").href="tr_resumen_individuo.php?claveU="+c;
	   $('#ver').removeClass('disabled');
	}
<?php if (!edita()) : ?>
	$('#CRcopula').find(':input').attr('disabled', 'disabled'); 	
<?php endif ?>	
	
}

</script>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 


    
    var tablin = $("#tablax").DataTable( {
     
     "sAjaxSource": 'copula_pareja_tb.php?<?php echo $ctempox?>',
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
     "pageLength": 5,
     "lengthMenu": [ 5,10,15,20, 40, 80, 100 ],
     "pagingType": "full",
     "language": {
         "emptyTable": "No hay pareja posible en esta temporada",
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
	
<button type="button" class="botonayuda" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title">registro de c&oacute;pula</h3>
            </div>            
			<div class="panel-body">

                <form data-toggle="validator" id="CRcopula" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<input name="xtrx" type="hidden"  value="<?php echo !empty($xtrx)?$xtrx:'';?>">

					
					<div class="row">
						
						<div class="col-sm-3">
							<div class="form-group ">
								<label for="claveU">ClaveU</label>
									<input type="text" class="form-control input-sm" style="width: 60px" id="claveU" name="claveU"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="<?php echo PATRON_claveU_men;?>"  value="<?php echo !empty($claveU)?$claveU:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorclaveU"></p>
									<?php if (!empty($claveUError)): ?>
										<span class="help-inline"><?php echo $claveUError;?></span>
									<?php endif; ?>


									<input name="pk_claveU" type="hidden"  value="<?php echo !empty($pk_claveU)?$pk_claveU:'';?>"> <!-- pk, clave anterior -->
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


									<input name="pk_fecha" type="hidden"  value="<?php echo !empty($pk_fecha)?$pk_fecha:'';?>"> <!-- pk, clave anterior -->
							</div>

						</div>

						<div class="col-sm-1">
						</div>
						
						<div class="col-sm-3">
							<div class="form-group ">
								<label for="hora">Hora</label>
									<input type="text" class="form-control input-sm" style="width:120px" id="hora" name="hora" placeholder="HH:mm:ss"  pattern="<?php echo PATRON_hora;?>" maxlength="8" required  data-error="<?php echo PATRON_hora_men;?>" value="<?php echo !empty($hora)?$hora:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorhora"></p>
									<?php if (!empty($horaError)): ?>
										<span class="help-inline"><?php echo $horaError;?></span>
									<?php endif; ?>


									<input name="pk_hora" type="hidden"  value="<?php echo !empty($pk_hora)?$pk_hora:'';?>"> <!-- pk, clave anterior -->
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group ">
								<label for="duracion">Duraci&oacute;n (minutos)</label>
									<input type="text" class="form-control input-sm" style="width:90px" id="duracion" name="duracion"  
									data-dmin="<?php echo CONST_duracion_min?>" data-dmax="<?php echo CONST_duracion_max?>" data-pentero 
									data-error="<?php echo CONST_duracion_men?>" value="<?php echo !empty($duracion)?$duracion:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorduracion"></p>
									<?php if (!empty($duracionError)): ?>
										<span class="help-inline"><?php echo $duracionError;?></span>
									<?php endif; ?>
							</div>						
						</div>
					</div>

					<div class="row">
						<div class="col-sm-4">

							<div class="form-group ">
								<label style="color:#464fa0">Con cu&aacute;l individuo</label>
								<label for="conCual">marca o tag original</label>
									<input type="text" class="form-control input-sm" id="conCual" name="conCual"  oninput="noPareja(this);" pattern="<?php echo PATRON_marcaOtag;?>" maxlength="30" data-error="<?php echo PATRON_marcaOtag_men;?>"  value="<?php echo !empty($conCual)?$conCual:'';?>" >
									<div class="help-block with-errors"></div>
									<p id="JSErrorconCual"></p>
									<?php if (!empty($conCualError)): ?>
										<span class="help-inline"><?php echo $conCualError;?></span>
									<?php endif; ?>
							</div>
							<div class="form-group ">
								<label for="conCualCu">claveU desde "parejas posibles"</label>
								  <div class="row">
  								   <div class="col-sm-5">
									<input type="text" class="form-control input-sm" style="width:80px" id="conCualCu" name="conCualCu" readonly  value="<?php echo	!empty($conCualCu)?$conCualCu:'';?>" >
  								   </div>
								   
  								   <div class="col-sm-2">
									<a class="btn btn-default btn-sm disabled" id=ver title="ir a a ficha de la pareja" target="_blank"> <span class="glyphicon glyphicon-open-file"></span></a>
  								   </div>
								  </div>
									<div class="help-block with-errors"></div>
									<p id="JSErrorconCualCu"></p>
									<?php if (!empty($conCualCuError)): ?>
										<span class="help-inline"><?php echo $conCualCuError;?></span>
									<?php endif; ?>
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
							<br>
							<div class="form-actions">
				<?php if (edita()) : ?>
								<button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<?php endif ?>

								<a class="btn btn-default btn-sm" href="copula_index.php<?php echo $param0;?>">atr&aacute;s</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

							</div>							
							
						</div>
                    <div class=col-sm-8>
				<?php if (edita()) : ?>
					
                        <div class="container" style="width:100%">                   
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">parejas posibles</h3>
                                </div>
                                <div class="panel-body">

                                    <table id="tablax" style="width:98%" class="display table table-striped table-bordered">
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
				<?php endif ?>
						
                    </div>						
				</div>
							  <div class="row">
							   <div class="col-sm-12">

								  <?php if (!empty($eError)): ?>
									<span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
								  <?php endif;?>
								 <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
									<span class="alert alert-success"><h5>registro modificado</h5></span>
								  <?php endif;?>
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