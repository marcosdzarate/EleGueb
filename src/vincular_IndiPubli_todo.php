<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_validar.php';

	if (isset($_POST['descarga']))
	{
		$xFil="";
		if (isset($_SESSION['publiQWhere'])) {
			$xFil=$_SESSION['publiQWhere'];
		}
		$pasaArch="Datos_de_IndividuosEnPubli";	
		$sql= "SELECT idpapers.IDpublicacion,publicaciones.titulo,idpapers.identificaciones,vw_seleccion_indi.marcas,vw_seleccion_indi.claveU FROM idpapers,vw_seleccion_indi,publicaciones $xFil order by 1;";
		XlsXOnDeFlai($sql,$pasaArch);
	} 


			


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

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 

	var sphp='vincular_IndiPubli_todo_tb.php';
    var tablin = $("#tablax").DataTable( {
     "serverSide": true,
     "sAjaxSource": sphp,
     stateSave: true,
    "drawCallback": function( settings ) {
		var fila=tablin.row( document.getElementById('filaSele').value ).node();
	    $(fila).addClass('selected');
    },
  "columnDefs": [
    { "orderable": false, "targets": 5 }
  ],	
     "stateDuration": -1, 
     "pageLength": 10,
     "lengthMenu": [ 5,10, 20, 40, 80, 100 ],
     "pagingType": "full_numbers",
     "language": {
         "emptyTable": "No hay datos disponibles en la tabla",
         "lengthMenu": "Mostrar _MENU_ registros",
         "info": "L&iacute;neas _START_ a _END_ de _TOTAL_ ",
         "infoFiltered":   "",
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
		}
	});   
	
	// resaltar fila seleccionada
    $('#tablax tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
			/**/
        }
        else {
            tablin.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
	        $("#indiTitu").html($(this).find('td')[4].innerHTML+" -- " +
			                    $(this).find('td')[3].innerHTML );
			$("#IDpubliTitu").html($(this).find('td')[0].innerHTML +" -- " + 
								 $(this).find('td')[1].innerHTML );
        }
		
    } );	
    
} );

</script>   

<script>
function reDibu() {
	var tab=$('#tablax').DataTable();
	var rs = tab.$('tr.selected').index();
	var p = tab.page.info().page;
	tab.page(p).draw(false);
	document.getElementById('filaSele').value=rs;
	
}
</script>   
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	

</head>

<body class=bodycolor >
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_publicaciones.html"></div>
	
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>


    <div class="container well"  style="width:80%">
            <div class="row">
                <h4>individuos en publicaciones</h4>
            </div>	
			
								
		
			
            <div class="panel panel-grisDark">
                <div class="panel-heading">
                    <h3 class="panel-title" id="publis">lista</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered" style="width:100%">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>t&iacute;tulo</th>
						  <th>iden. en la<br>publicaci&oacute;n</th>
                          <th>Marcas</th>
                          <th>ClaveU</th>
                          <th>acci&oacute;n</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
                <p>
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
						</form>

    </div> <!-- /container -->


    
					<input name="filaSele" id="filaSele" type="hidden"  style="color:black" value="">
					<input name="claveUindi" id="claveUindi" type="hidden"  style="color:black" value="">
					<input name="IDpubli" id="IDpubli" type="hidden"  style="color:black"   value="">
					<input name="claveUvincu" id="claveUvincu" type="hidden"  style="color:black" value="">
					<h3  style="display:none" id="indiTitu">&nbsp;</h3>
					<h3  style="display:none" id="IDpubliTitu">&nbsp;</h3>
					


<!-- para ventanas MODAL de aqui para abajo
 <script src="https://www.w3schools.com/lib/w3.js"></script> -->
 <script src="js/w3.js"></script>

 <div w3-include-html="tb_ventanaM.html"></div>
<script>
w3.includeHTML();
</script>

  </body>


</html>