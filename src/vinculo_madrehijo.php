<?php
    require_once 'tb_sesion.php';
    require_once 'tb_dbconecta.php';
    require_once 'tb_database.php';
    require_once 'tb_validar.php';

        $queTabla = 'madrehijo';

	/* uso el mimso form para "ver" cuando no tiene habilitada la edición */
				/* sin permiso de edición, fuera*/
				/* siErrorFuera(edita()); */



    /*parametros inválidos, fuera*/
    $v=true;

    $clavePup = null;
    if (isset($_GET["clavePup"])) {
        $clavePup=$_GET["clavePup"];
        $m = validar_claveU ($clavePup,$v,true);
    }
    else{
        if (!isset($_GET["clavePup"]) and !isset($_POST["clavePup"]) ){
            $v=false;
        }
    }   
//echo "clavePup ".$v." ".$clavePup." ";
    siErrorFuera($v);   

    $xmam = null;
    if (isset($_GET["xmam"])) {
        $xmam=$_GET["xmam"];
        $v = ($xmam=='no' OR $xmam=='si');
    }
    else{
        if (!isset($_GET["xmam"]) and !isset($_POST["xmam"]) ){
            $v=false;
        }
		else{
			$xmam=$_POST["xmam"];
		}
    }   
//echo "xmam ".$v." ".$xmam." ";
    siErrorFuera($v);   

    $tempo1 = null;
    if (isset($_GET["te1"])) {
        $tempo1=$_GET["te1"];
        $m = validar_temporada($tempo1,$clavePup,$v,false);      
    }
    else{
        if (!isset($_GET["te1"]) and !isset($_POST["te1"]) ){
            $v=false;
        }
    }   
//echo "te1-tempo1 ".$v." ".$tempo1." ";
    siErrorFuera($v);   

    $pFecha = null;  /* primer fecha de individuo */
    if (isset($_GET["pF"])) {
        $pFecha=$_GET["pF"];
        $m = validar_fecha ($pFecha,$v,false);
    }
    else{
        if (!isset($_GET["pF"]) and !isset($_POST["pF"]) ){
            $v=false;
        }
    }   
//echo "pF-pFecha ".$v." ".$pFecha." ";
    siErrorFuera($v);  	
	
	
    $temporada = null;
    if($xmam=='no'){
        $temporada=$tempo1;
        $titu="creando v&iacute;nculo con la madre";
    }
    else{
        $titu="v&iacute;nculo con la madre";
    }
    $claveMam = null;

    $v=true;

    $guardado='';

    if ( !empty($_POST)) {
        // para errores de validacion
        $clavePupError = null;
        $temporadaError = null;
        $pFechaError = null;		
        $claveMamError = null;

        $eError = null;

        // los campos a validar
        $clavePup = limpia($_POST['clavePup']);
        $temporada = limpia($_POST['temporada']);
        $claveMam = limpia($_POST['claveMam']);
        $pFecha = limpia($_POST['pFecha']);

        // validacion: devuelve el error; los dos primeros parametros pasan x referencia
        //             el ultimo: true debe estar - false puede faltar
        $valid = true;

        $clavePupError = validar_claveU ($clavePup,$valid,true);
        $temporadaError = validar_temporada ($temporada,$clavePup,$valid,true);
        $claveMamError = validar_claveU ($claveMam,$valid,true);

    // validacion entre campos
        $eError = validarForm_madrehijo ($clavePup,$temporada,$claveMam,$valid);

        // nuevo registro
        if ($valid and isset($_POST['guardar'])) {
            // campos que pueden tener NULL

            // inserta
			// un trigger en la DB completa las colummasn tipoTempoMam y tipoTempoPup
            $pdo = Database::connect();
            if ($xmam=='no'){
                $sql = "INSERT INTO madrehijo (clavePup,temporada,claveMam) values(?,?,?)";
                $q = $pdo->prepare($sql);
                $q->execute(array($clavePup,$temporada,$claveMam));
                }
            else{
				// un trigger en la DB actualiza las columnas tipoTempoMam y tipoTempoPup
                $sql = "UPDATE madrehijo SET claveMam=? WHERE clavePup=? AND temporada=?";
                $q = $pdo->prepare($sql);
                $q->execute(array($claveMam,$clavePup,$temporada));
            }

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
    else {
        if($xmam=='si')
        { /*seleccion inicial*/
            $pdo = Database::connect();
            $sql = "SELECT * FROM madrehijo where clavePup=?";
            $q = $pdo->prepare($sql);
            $q->execute(array($clavePup));
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
                       $temporada = $data['temporada'];
                       $claveMam = $data['claveMam'];
                   }
                }
        }
    }

     $param0 = "?claveU=".$clavePup;
     $paramP0 = "?clavePup=".$clavePup;
     $ctempox = 'condi=temporada="'.$temporada.'"';

	$tipoT="";
	$tActual = temporadaAnio_actual($tipoT);
	 

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
function EsLaMadre(c,f){
	document.getElementById('claveMam').value=c;
	if(f>document.getElementById('pFecha').value) {
		alert('OjO que la fecha de parto es posterior a la 1er. fecha del pup!');
	}
}
</script>
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 


    
    var tablin = $("#tablax").DataTable( {
     
     "sAjaxSource": 'vinculo_madrehijo_tb.php?<?php echo $ctempox?>',
     stateSave: true,
	 "order": [[ 0, 'des' ], [ 2, 'asc' ]],
     "columns": [
     { "width": "100px"},
     { "width": "100px"},
     { "width": "130px"},
     { "width": "130px"},	 
     { "width": "150px"},
    null
    ],
	"columnDefs": [
			{"orderable": false, "targets": 5}
	],	
     "stateDuration": -1, 
     "pageLength": 5,
     "lengthMenu": [ 5,10,15,20, 40, 80, 100 ],
     "pagingType": "full",
     "language": {
         "emptyTable": "No hay hembras sin pup en esta temporada",
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


    
    


 
        
       
} );

</script>
<?php if (!edita()) : ?>
<script>
function aVerVer(elFormu) {
	$('#'+elFormu).find(':input').attr('disabled', 'disabled'); 
}
</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		

</head>
<body onload="aVerVer('CRmadrehijo')">

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


        <div class="panel panel-naranjino">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $titu ?></h3>
            </div>
            <div class="panel-body">

                <div class=row>
                    <div class=col-sm-3>
                      <form data-toggle="validator" id="CRmadrehijo" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                        <input name="xmam" hidden value="<?php echo !empty($xmam)?$xmam:'';?>"> 
                        <input name="pF" hidden value="<?php echo !empty($pFecha)?$pFecha:'';?>"> 
                        <input name="te1" hidden value="<?php echo !empty($tempo1)?$tempo1:'';?>"> 						
                        <div class="form-group ">
                            <label for="clavePup">Clave pup</label>
                                <input type="text" class="form-control input-sm" style="width: 60px" id="clavePup" name="clavePup"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" required readonly data-error="Clave &uacute;nica incorrrecta" value="<?php echo !empty($clavePup)?$clavePup:'';?>" >
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorclavePup"></p>
                                <?php if (!empty($clavePupError)): ?>
                                    <span class="help-inline"><?php echo $clavePupError;?></span>
                                <?php endif; ?>
                        </div>
                        <div class="form-group ">
                            <label for="temporada">Temporada</label>
                                <input type="text" class="form-control input-sm" style="width:90px" id="temporada" name="temporada"   readonly required data-pentero
							data-dmin="<?php echo CONST_temporadaMin_min?>" data-dmax=<?php echo $tActual; ?>  
							data-error="<?php echo CONST_temporadaMin_men?> y &lt= <?php echo $tActual?>"
								value="<?php echo !empty($temporada)?$temporada:'';?>" >
                                <div class="help-block with-errors"></div>
                                <p id="JSErrortemporada"></p>
                                <?php if (!empty($temporadaError)): ?>
                                    <span class="help-inline"><?php echo $temporadaError;?></span>
                                <?php endif; ?>
                        </div>
						
                        <div class="form-group ">
                            <label for="pFecha">1er. fecha</label>
                                <input type="text" class="form-control input-sm" style="width:100px" id="pFecha" name="pFecha" readonly required 
								value="<?php echo !empty($pFecha)?$pFecha:'';?>" >
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorpFecha"></p>
                                <?php if (!empty($pFechaError)): ?>
                                    <span class="help-inline"><?php echo $pFechaError;?></span>
                                <?php endif; ?>
                        </div>						
						
						
						
                        <div class="form-group ">
                            <label for="claveMam">Clave madre</label>
                                <input type="text" class="form-control input-sm" style="width: 60px" id="claveMam" name="claveMam"  oninput="mayus(this);" pattern="<?php echo PATRON_claveU;?>" maxlength="4" readonly required data-error="Clave &uacute;nica incorrrecta" value="<?php echo !empty($claveMam)?$claveMam:'';?>" >
                                <div class="help-block with-errors"></div>
                                <p id="JSErrorclaveMam"></p>
                                <?php if (!empty($claveMamError)): ?>
                                    <span class="help-inline"><?php echo $claveMamError;?></span>
                                <?php endif; ?>

                        </div>	

                    <br>
                    <div class="form-actions">
					<?php if (edita()) : ?>
					   <?php if ($xmam=='si' or ($xmam=="no" and $guardado<>'ok')) : ?>
							<button type="submit" class="btn btn-primary btn-sm" name="guardar" id="guardar">guardar</button>&nbsp;&nbsp;
						<?php endif ?>					
					<?php endif ?>					

					<a class="btn btn-default btn-sm" onclick=parent.cierroM("tr_resumen_individuo.php<?php echo $param0;?>")>a ficha</a>

					<?php if (edita()) : ?>
						<?php if ($xmam=="si"): ?>
						  <br><br>
							<a class="btn btn-danger btn-sm"  onclick=ventanaMini("vinculo_madrehijo_borrar.php<?php echo $paramP0;?>","")>eliminar v&iacute;nculo</a><span style="display:inline-block; width: 20px;"></span>
						<?php endif; ?>&nbsp;
					<?php endif ?>



                    </div>




                   </form>
						
                    </div>

                    <div class=col-sm-9>
					<?php if (edita()) : ?>
                        <div class="container" style="width:98%">                   
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">hembras sin pup en la temporada</h3>
                                </div>
                                <div class="panel-body">

                                    <table id="tablax" style="width:90%" class="display table table-striped table-bordered">
                                      <thead>
                                        <tr>
                                          <th>ClaveU</th>
                                          <th>Temporada</th>
                                          <th>Tags</th>
                                          <th>Marcas</th>
											<th>FParto</th>										  
                                          <th>sel</th>
                                        </tr>
                                      </thead>
                                    </table>

                                </div>
                            </div>
                        </div>
                          <?php if (!empty($eError)): ?>
                            <span class="alert alert-danger-derecha"><h5><?php echo $eError;?></h5></span>
                          <?php endif;?>
                         <?php if (isset($_POST['guardar']) and empty($eError) and !empty($valid) and $valid ): ?>
                            <span class="alert alert-success"><h5>
							         <?php if ($xmam=="no"): ?>v&iacute;nculo agregado
									 <?php else: ?> v&iacute;nculo modificado
									 <?php endif;?></h5></span>
                          <?php endif;?>
                    </div>
					<?php endif ?>

                </div>
					



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
	
<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script> 
 
 <div w3-include-html="tr_ventanaMiniBorra.html"></div>

<script>
w3.includeHTML();
</script>
	
	
	
</body>
</html>