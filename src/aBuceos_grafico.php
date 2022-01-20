<?php 
	require_once 'tb_sesion.php';
	require_once 'tb_dbconecta.php';
	require_once 'tb_validar.php';
	
	
	$claveU = limpia($_GET['claveU']);
	$nroViaje = limpia($_GET['nroViaje']); 
	$xde =  limpia($_GET['xde']); 
	
	$valid = true;

	$eError="";
	$eError = validar_claveU ($claveU,$valid,true);
	$eError = validar_entero ($nroViaje,1,100,$valid,true);
	siErrorFuera($valid);
	
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">

<title><?php echo siglaGrupe ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">


    <script src="js/jquery-3.1.1.js" type="text/javascript"></script>
    <script src="js/validator.js"></script>
    <link   href="css/bootstrap.css" rel="stylesheet">
    <script src="js/bootstrap.js"></script>
    <script src="js/imiei.js"></script>

    <link rel="stylesheet" href="login/style/main.css">


	
    <link rel="stylesheet" href="dygraphs/dygraph.css">
    <script type="text/javascript" src="dygraphs/dygraph.js"></script>
    <script type="text/javascript" src="dygraphs/synchronizer.js"></script>


	
	
	
	
	
    <style type="text/css">
	
	.cf0{
		height:190px;
		margin-bottom: 5px;
		margin-right: 15px
		}
		
	.cf{
		height:150px;
		margin-bottom: 5px;
		margin-right: 15px
		}		
	
	  .affix {
		  top: 0px;
		  z-index: 9999 !important;
		  padding:10px;
		  background-color:#cccccc;
	  }
	
	.dygraph-label,.dygraph-axis-label {   /* labels */
		font-size:12px;
	}
	
	.bchico {
		padding:2px 6px;
		line-height:1;
	}	
	
	
	 </style>
	
	
<link   href="css/aiuta.css" rel="stylesheet">	
<script src="js/aiuta.js"></script>		
		
	
	
  </head>
  <body>
	 

	
<button type="button" class="botonayuda" style="top:90px" title="ayuda" onclick=ayudote(location.pathname)>
	<span class="glyphicon glyphicon-question-sign"></span>
</button>

  <div class="container" data-spy="affix">
		<div class="row">
			<div class="col-sm-10" id="leyenda"><h5>resumen de buceos de <?php echo $xde;?></h5></div>
		</div>
		<div class="row">
			<div class="col-sm-1">diaJul</div>
			<div class="col-sm-2" id="leyenda0"></div>
			<div class="col-sm-2" id="leyenda1"></div>
			<div class="col-sm-2" id="leyenda2"></div>
			<div class="col-sm-2" id="botones">			
					<a onclick="restaura()" class="btn btn-info bchico" title="restaurar diaJul" ><span class="glyphicon glyphicon-refresh"></a>
				<a onclick="window.close()" class="btn btn-warning bchico" title="cerrar" ><span class="glyphicon glyphicon-remove"></span></a>
			</div>
			
		</div> 	
  </div>
  
	
    <div class="container-fluid" >
      <div id="div0" class="container-fluid cf0"></div>
      <div id="div1" class="container-fluid cf"></div>
      <div id="div2" class="container-fluid cf"></div>
    </div> 	
	
	

    <script type="text/javascript">
	
 
	  
	  
	  var sync;
	  /*cero: solo para el rangeSelector*/
	  var colus=["profundidad (m)","temperatura (ºC)","tiempos (min)"];
	  var url0="aBuceos_grafico_buc.php?cu=<?php echo $claveU;?>&nv=<?php echo $nroViaje;?>&";
      gs = [];
	  var uid=50;
	  
	datos=url0+"var=0";  /* este con rangeSelector, datos de profundidadMax*/
	gs.push(
	  new Dygraph(
		document.getElementById("div0"),
		datos,
		{
			labelsDiv:"leyenda0",
			legend: 'always',			
			showRangeSelector: true,
			xlabel:"diaJul",
			ylabel:colus[0],
            rangeSelectorHeight: 20,
				axes: {
				  x: {
					axisLabelWidth: uid,
				  },
				  y: {
					axisLabelWidth: uid,
					digitsAfterDecimal:1
				  }
				},
			}
		)
	);	  
	  
	  
	  
     /* el resto de las variables */
      for (var i = 1; i <= 2; i++) {		  
		datos=url0+"var="+i;
        gs.push(
          new Dygraph(
            document.getElementById("div" + i),
            datos,
			{
				labelsDiv:"leyenda"+i,
				legend: 'always',
				ylabel:colus[i],
				axes: {
				  x: {
					axisLabelWidth: uid,
				  },
				  y: {
					axisLabelWidth: uid,
					digitsAfterDecimal:1
				  }
				},
			}
			)
        );
      }	 


	  
	  setTimeout(function() {
	  
		sync = Dygraph.synchronize(gs,
				{
					selection: true,
					zoom: true,
					range: false
				}
			);
	  
	
	  },1000);
	  

  function restaura() {
        gs[0].updateOptions({
          dateWindow: null,
          valueRange: null
        });
      } 
	  
	  
    </script>
  </body>
</html>
