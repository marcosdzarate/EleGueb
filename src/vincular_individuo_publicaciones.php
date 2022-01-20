<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';


$claveUindi=null;
$IDpubli=null;

?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe ?></title>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>

    <link rel="stylesheet" href="login/style/main.css">

	<script src="js/imiei.js"></script>

	
    <link   href="css/miSideBar.css" rel="stylesheet">

<style>
.ccen{
        text-align:center;
    }

.sepa {
	padding-top:2px;
	color:#7a5757;
	}

.ffo {
	font-size:14px;
}		

</style> 
    
						  
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
'claveU','sexo','nuestro','tags','marcas','rango','cantTempo','titempos','tteta' 
<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 

   /* para tabla individuos */
   var tablinIndi = $("#tablaxIndi").DataTable( {
     "serverSide": true,
     "sAjaxSource": 'vincular_IndiPubli_individuo_tb.php',
     stateSave: true,
	"columnDefs": [
			{ "targets":[2,5,6,7,8],
			"visible":false },
			{"orderable": false, "targets": 9 }
  ],				
	 "ordering": true,
	 "search": {
		"caseInsensitive": true
		},
	 "columns": [
	 { "width": "70px" },
	 { "width": "80px" },
    { className: "ccen","width": "70px" },
    null,
	null,
    { "width": "70px" },
    { className: "ccen", "width": "60px" },
	null,
	null,
	null
    ],
     "stateDuration": -1, 
     "pageLength": 10,
     "lengthMenu": [ 5,10,15,20, 40, 80, 100 ],
     "pagingType": "full_numbers",
     "language": {
         "emptyTable": "No hay datos disponibles en la tabla",
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
    

	
	
    // aplico busque con ENTER - INDIVIDUOS
	$('#tablaxIndi_filter input').unbind();
	$('#tablaxIndi_filter input').bind('keyup', function(e) {
		if(e.keyCode == 13) {
		tablinIndi.search(this.value).draw();
        $("#indiTitu").html("&nbsp;"); 
		$("#claveUindi").val("");
		botonVincular();
	}
	}); 
	
	
	// resaltar fila seleccionada
    $('#tablaxIndi tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            
        }
        else {
            tablinIndi.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
	        $("#indiTitu").html($(this).find('td')[0].innerHTML+" -- " +
			                    $(this).find('td')[2].innerHTML + " -- "+$(this).find('td')[3].innerHTML); /*muestra claveU,tags,marcas */
			$("#claveUindi").val($(this).find('td')[0].innerHTML);
        }
		botonVincular();
		
    } );	
	
	
	/* para tabla publicaciones */
	var sphp='vincular_IndiPubli_publicaciones_tb.php?queIndi=';
    var tablin = $("#tablax").DataTable( {
     "serverSide": true,
     "sAjaxSource": sphp,
     stateSave: true,
	"columnDefs": [
			{ "targets":[4,5,6,7,8],
				"visible":false },
			{"orderable": false, "targets": 9 }
				],
     "stateDuration": -1, 
     "pageLength": 10,
     "lengthMenu": [ 5,10, 20, 40, 80, 100 ],
     "pagingType": "full_numbers",
     "language": {
         "emptyTable": "No hay datos disponibles en la tabla",
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
    
    // aplico busque con ENTER - PUBLICACIONES
	$('#tablax_filter input').unbind();
	$('#tablax_filter input').bind('keyup', function(e) {
		if(e.keyCode == 13) {
		tablin.search(this.value).draw();   
		vinculadas("","");		/* publicaciones sin filtrar pero el individuo seleccionado permanece */
        $("#IDpubliTitu").html("&nbsp;"); 
		$("#IDpubli").val("");
		botonVincular();

	}
	});   
    

	// resaltar fila seleccionada
    $('#tablax tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            
        }
        else {
            tablin.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
	        $("#IDpubliTitu").html($(this).find('td')[0].innerHTML+" -- "+
			                    $(this).find('td')[3].innerHTML); /*muestra id y titulo */
			$("#IDpubli").val($(this).find('td')[0].innerHTML);
        }
		botonVincular();		
    } );	 
        
   	botonVincular();		

} );

</script>   

<script>
function vinculadas(cu,publisTxt) {
	/* actualiza la tabla de las publicaciones  */
	/* si "cu"="" muestra todas*/
	var queindi = cu;
	var string = ('vincular_IndiPubli_publicaciones_tb.php?queIndi='+queindi);
	document.getElementById("claveUvincu").value='';
	$('#tablax').DataTable().ajax.url(string).load();
	$('#tablax').DataTable().ajax.reload();
	if (document.getElementById("queHizo").value!='borro'){
		$("#IDpubliTitu").html("&nbsp;"); 
		$("#IDpubli").val("");
		if(publisTxt!=""){
			$("#publis").html('publicaci&oacute;nes vinculadas a: '+publisTxt);
			document.getElementById("claveUvincu").value=cu;
		}
		else{
			$("#publis").html('publicaci&oacute;nes'); 
		}
	}
	document.getElementById("queHizo").value='';
};

function botonVincular() {
	b=document.getElementById("botonVincular");
	if ($("#IDpubli").val()!="" && $("#claveUindi").val()!="" &&
			document.getElementById("claveUindi").value!=document.getElementById("claveUvincu").value) {
		b.style.display="block";
	}
	else{
		b.style.display="none";
	}
	
}

function creandoVinculo(tit){
	argu = "vincular_IndiPubli_crear.php?"+
			     "ID="+document.getElementById("IDpubli").value+
			"&claveU="+document.getElementById("claveUindi").value;
	ventanaM(argu,tit);
}




</script>

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body class=bodycolor  >
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_publicaciones.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

	
    <div class="container well"  style="width:80%">
            <div class="row">
                <h4>vincular individuo y publicaciones</h4>
            </div>	


								
		
            <div class="panel panel-grisDark">
                <div class="panel-heading">
					<div class="row">
						<div class="col-sm-11">
							<div class="row">
								<div class="col-sm-2">
									<h3 class="panel-title" id="indiTituTxt">individuo:</h3>
								</div>
								<div class="col-sm-9">
									<h3 class="panel-title" id="indiTitu">&nbsp;</h3>
								</div>  
							</div>
							
							
							<div class="row">
								<div class="col-sm-2">
									<h3 class="panel-title" id="IDpubliTituTxt">publicaci&oacute;n:</h3>

								</div>
								<div class="col-sm-9">
									<h3 class="panel-title" id="IDpubliTitu">&nbsp;</h3>
								</div>  
							</div>					
						</div>	
						<div class="col-sm-1" id="botonVincular">
							<a class="btn btn-info btn-lg" title="vincular individuo a publicaci&oacute;n"
							id="botonVincularA" 
							onclick="creandoVinculo(this.title)" ><span class="glyphicon glyphicon-link"></span></a>
						</div>
						
					</div>					
					
					
						
					<input name="claveUindi" id="claveUindi" type="hidden"  style="color:black" value="">
					<input name="IDpubli" id="IDpubli" type="hidden"  style="color:black"   value="">
					<input name="queHizo" id="queHizo" type="hidden"  style="color:black"   value="">
					<input name="claveUvincu" id="claveUvincu" type="hidden"  style="color:black" value="">

					
                </div>
            </div>	


<div class="row">
	<div class="col-sm-6">
			
            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">individuos</h3>
					
                </div>
                <div class="panel-body">

                    <table id="tablaxIndi" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>ClaveU</th>
                          <th>Sexo</th>
                          <th>Nuestro</th>
                          <th>Tags</th>
                          <th>Marcas</th>
                          <th>entre a&ntilde;os</th>
                          <th >#temps.</th>
						  <th>temporadas</th>
						  <th>etapasviajes</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>			
			
			

	</div>
	<div class="col-sm-6">
			
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title" id="publis">publicaciones</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>publi</th>
                          <th>a&ntilde;o</th>
                          <th>t&iacute;tulo</th>
                          <th>DOI</th>
                          <th>autores</th>
                          <th>abstract y dem&aacute;s</th>
                          <th>tipo archi</th>
                          <th>archivo</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
			
	</div>
</div>
			
                <p>

    </div> <!-- /container -->


    



<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

  </body>


</html>