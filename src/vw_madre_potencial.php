<?php

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';
require_once 'tb_param.php';

$ctempox = 'condi=temporada="'.$_GET['temporada'].'"';

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
	
	
	
    <link   href="css/miSideBar.css" rel="stylesheet">   

<style>
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>	
<script src="js/imiei.js"></script>    
<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>

<script type="text/javascript" language="javascript" class="init">
$(document).ready(function() { 


    
    var tablin = $("#tablax").DataTable( {
     
     "sAjaxSource": 'vw_madre_potencial_tb.php?<?php echo $ctempox?>',
     stateSave: true,
	 "columns": [
	 { "width": "50px"},
	 { "width": "50px"},
    null,
    null,
	null
    ],
     "stateDuration": -1, 
     "pageLength": 5,
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
    
	$('#tablax').DataTable().column( 0 ).visible( false );


	
	


 
        
       
} );

</script>

<style>
.ccen{
        text-align:center;
    }
</style>
</head>

<body class=bodycolor>
<?php
require_once 'tb_barramenu.php';
?>


    <div w3-include-html="sideBar_oterrestre.html"></div>


	
    <div class="container well"   style="text-align:left;width:450px">
            <div class="row">
                <h4>madres potenciales</h4>
            </div>
            <div class="panel panel-naranjino">
                <div class="panel-heading">
                    <h3 class="panel-title">selecci&oacute;n del individuo</h3>
                </div>
                <div class="panel-body">

                    <table id="tablax" class="display table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>ClaveU</th>
                          <th>Temporada</th>
                          <th>Tags</th>
                          <th>Marcas</th>
                          <th>FecParto</th>
                          <th>sel</th>
                        </tr>
                      </thead>
                    </table>

                </div>
            </div>
			
<button onclick=""> bli bli </button> 


                   <?php if (edita()) :?>

                    <?php endif?>
    </div> <!-- /container -->


    
  </body>


</html>