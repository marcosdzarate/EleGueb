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
		$laTabla='publicaciones';
		$pasaArch="Datos_de_".$laTabla;	
		$sql= "SELECT * FROM $laTabla $xFil ORDER BY 1;";
		XlsXOnDeFlai($sql,$pasaArch);
	} 
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
.table > tfoot > tr > th > input {
	width:100%
}
</style>
   
    
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 
    // Setup - add a text input to each footer cell
    $('#tablax tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Buscar '+title+'" />' );
    } );
    
    var tablin = $("#tablax").DataTable( {
     "serverSide": true,
     "sAjaxSource": 'publicaciones_tb.php',
     stateSave: true,
	"columnDefs": [
			{ "targets":[4,6,7,8],
				"visible":false },
			{"orderable": false, "targets": 9 },
				{"width": "120px", "targets": 9}
				],
     "stateDuration": -1, 
     "pageLength": 5,
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
         "search":         "Buscar por cualquier t&eacute;rmino:",
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
    

    // Apply the search
    tablin.columns().every( function () {
        var that = this;
 
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );    
    

 
        
       
} );

</script>   

	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
	


</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>

    <div w3-include-html="sideBar_publicaciones.html"></div>
	
<button type="button" class="botonayuda" style="top:180px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

    <div class="container well"  style="width:80%">
            <div class="row">
                <h4>nuestras publicaciones</h4>
            </div>	

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">lista</h3>
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
                      <tfoot>
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
                          
                        </tr>
                      </tfoot>                    
                    </table>

                </div>
            </div>
                <div class="row">
					<div class="col-sm-3">
						<?php if (edita()) : ?>
							<a title="agregar publicaci&oacute;n" onclick=ventanaM("publicaciones_crear.php",this.title) class="btn btn-primary btn-sm" >nueva</a>&nbsp;&nbsp;
						<?php endif ?>
					</div>
					
					<div class="col-sm-3">					
						<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">						
							<button type="submit" name=descarga  class="btn btn-warning btn-sm">descargar</button>
						</form>
					</div>
				</div>

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