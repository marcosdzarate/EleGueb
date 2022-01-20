<?php 

require_once 'tb_dbconecta.php';
require_once 'tb_sesion.php';

?> 
<!DOCTYPE html>
<html lang="es">
<head>
  <!-- PAGINA made with Mobirise Website Builder v4.2.4, https://mobirise.com -->

  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" href="imas/favicon.png" type="image/x-icon">
<title><?php echo siglaGrupe?></title>

  <meta name="generator" content="Mobirise v3.12.1, mobirise.com">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">

	
  <link rel="stylesheet" href="assetsMobi/et-line-font-plugin/style.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
  <link rel="stylesheet" href="assetsMobi/web/assets/mobirise-icons/mobirise-icons.css">
  <link rel="stylesheet" href="assetsMobi/bootstrap-material-design-font/css/material.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic&subset=latin">
  <link rel="stylesheet" href="assetsMobi/tether/tether.min.css">
  <link rel="stylesheet" href="assetsMobi/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assetsMobi/dropdown/css/style.css">
  <link rel="stylesheet" href="assetsMobi/animate.css/animate.min.css">
  <link rel="stylesheet" href="assetsMobi/theme/css/style.css">
  <link rel="stylesheet" href="assetsMobi/mobirise/css/mbr-additional.css" type="text/css">
  
  
    <link   href="css/solo-glyphicons.css" rel="stylesheet">   <!--mrm-->
  
<style>
/* sombra */
.sombrita {
	box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
	margin-bottom: 25px;
}
</style> 
  
</head>
<body>

<section id="menu-k" data-rv-view="0">


    <nav class="navbar navbar-dropdown transparent navbar-fixed-top bg-color">
        <div class="container">

            <div class="mbr-table">
                <div class="mbr-table-cell">

                    <div class="navbar-brand">
                        <a class="navbar-caption" href="aprincipal.php"><?php echo siglaGrupe." - ".$_SESSION['username']?></a>
                    </div>

                </div>
                <div class="mbr-table-cell">

                    <button class="navbar-toggler pull-xs-right hidden-md-up" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
                        <div class="hamburger-icon"></div>
                    </button>

                    <ul class="nav-dropdown collapse pull-xs-right nav navbar-nav navbar-toggleable-sm" id="exCollapsingNavbar">
						<?php if (es_administrador()) :?> 
							<li class="nav-item"><a class="nav-link link" href="login/cosasAdmin.php"><span class="glyphicon glyphicon-wrench"></span> admin</a></li>
						<?php endif; ?>						
					   <li class="nav-item"><a class="nav-link link" href="aprincipal.php"><span class="glyphicon glyphicon-home"></span></a></li>
					   

					   <li class="nav-item"><a class="nav-link link" target=black href="aayudote/ele-DocUsuario.html"><span class="glyphicon glyphicon-question-sign"></span></a></li>					   
					   
					   
					   <li class="nav-item"><a class="nav-link link" href="tb_logout.php"><span class="glyphicon glyphicon-log-out"></span> salir</a></li></ul>
                    <button hidden="" class="navbar-toggler navbar-close" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar">
                        <div class="close-icon"></div>
                    </button>

                </div>
            </div>

        </div>
    </nav>

</section>
<section class="mbr-section mbr-section-hero mbr-section-full header2" id="header2-9" style="background-color: rgb(98, 87, 80);">

    

    <div class="mbr-table mbr-table-full">
        <div class="mbr-table-cell">

            <div class="container">
                <div class="mbr-section row">
                    <div class="mbr-table-md-up">
                        
                        
                        

                        <div class="mbr-table-cell col-md-5 content-size text-xs-center text-md-right">

                            <h3 class="mbr-section-title display-2"><span style="font-weight: normal;"><em><?php echo siglaGrupe?></em></span><br><br><span style="font-weight: normal;"><?php echo Grupete?></span><br><span style="font-weight: normal;"></span></h3>

                            <div class="mbr-section-text">
                                <p><br><br><em>CESIMAR-CENPAT-CONICET</em></p>
                            </div>

                            

                        </div>
                        <div class="mbr-table-cell mbr-valign-top mbr-left-padding-md-up col-md-7 image-size" style="width: 50%;">
                            <div class="mbr-figure"><img class="img-circle sombrita" style="width:75%" src="imas/playa.PNG"></div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="mbr-arrow mbr-arrow-floating hidden-sm-down" aria-hidden="true"><a href="#features7-g"><i class="mbr-arrow-icon"></i></a></div>

</section>

<!-- <section class="engine"><a rel="external" href="https://mobirise.com">mobirise.com</a></section> -->
<section class="mbr-cards mbr-section mbr-section-nopadding" id="features7-g" style="background-color: rgb(224, 226, 228);">

    

    <div class="mbr-cards-row row">
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="censo_menu0.php" class="mbri-growing-chart mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">censos de EMS</span></h4>
                        <h5 class="card-subtitle">en Pen&iacute;nsula Vald&eacute;s y desde Punta Ninfas hacia el sur</h5>
                        <p class="card-text">En temporadas de reproducci&oacute;n y muda: ingresos, totales y comparaciones<br><br>Pelos de Vald&eacute;s<br><br>Mensuales entre septiembre de 1995 y febrero de 2000&nbsp;&nbsp;(estos datos no son editables)<br><br></p>
                        <div class="card-btn"><a href="censo_menu0.php" class="btn btn-info">ir</a> </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="oterrestre_menu0.php" class="mbri-preview mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">observaci&oacute;n terrestre de EMS&nbsp;</span></h4>
                        <h5 class="card-subtitle">en Pen&iacute;nsula Vald&eacutes y donde hayan estado </h5>
                        <p class="card-text">Datos registrados relativos a los ciclos terrestres de reproducci&oacute;n y de muda del elefantes marino del sur <br><br>Respuesta a preguntas preestablecidas que fueron formuladas al implementar la base de datos original<br><br></p>
                        <div class="card-btn"><a href="oterrestre_menu0.php" class="btn btn-info">ir</a></div>
                    </div>
                </div>
          </div>
        </div>
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="oremota_menu0.php" class="mbri-android mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">observaci&oacute;n remota de EMS</span></h4>
                        <h5 class="card-subtitle">actividad en el mar</h5>
                        <p class="card-text">Localizaciones<br><br></p>
                        <p class="card-text">Buceos<br><br></p>
                        <p class="card-text">Fichas de viajes<br><br></p>
                        <div class="card-btn"><a href="oremota_menu0.php" class="btn btn-info">ir</a></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="publicaciones_menu0.php" class="etl-icon icon-book-open mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">publicaciones</span></h4>
                        <h5 class="card-subtitle">nuestras y de otros</h5>
                        <p class="card-text">Papers<br>Reportes<br>Posters<br>Presentaciones...<br><br></p>
                        <div class="card-btn"><a href="publicaciones_menu0.php" class="btn btn-info">ir</a> </div>
                    </div>
                </div>
            </div>
        </div>		
        
        
        
    </div>
</section>

<section class="mbr-cards mbr-section mbr-section-nopadding" id="features7-i" style="background-color: rgb(150, 137, 127);">

    

    <div class="mbr-cards-row row">
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="mdatos_menu0.php" class="etl-icon icon-tools mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">m&aacute;s datos</span></h4>
                        <h5 class="card-subtitle">no menos importante &nbsp;</h5>
                        <p class="card-text">Las categor&iacute;as de edad del EMS
						<br>Playas o tramos en los que se desarrollan las actividades
						<br>Vecindarios en que se pueden agrupar playas o tramos
						<br>Datos de los colaboradores con el grupo
						<br>Instrumentos<br><br></p>
                        <div class="card-btn"><a href="mdatos_menu0.php" class="btn btn-info">ir</a></div>
                    </div>
                </div>
          </div>
        </div>
        <div class="mbr-cards-col col-xs-12 col-lg-3" style="padding-top: 80px; padding-bottom: 80px;">
            <div class="container">
                <div class="card cart-block">
                    <div class="card-img iconbox"><a href="#features7-i" class="mbri-plus mbr-iconfont mbr-iconfont-features7" style="color: rgb(255, 255, 255);"></a></div>
                    <div class="card-block">
                        <h4 class="card-title"><span style="font-weight: normal;">y m&aacute;s ...</span></h4>
                        <h5 class="card-subtitle">no menos importante</h5>
                        <p class="card-text">se aceptan aportes e ideas<br><br></p>
                        <div class="card-btn"><a href="#features7-i" class="btn btn-info">ir</a></div>
                    </div>
                </div>
            </div>
        </div>
        
        
        
    </div>
</section>

  <script src="assetsMobi/web/assets/jquery/jquery.min.js"></script>
  <script src="assetsMobi/tether/tether.min.js"></script>
  <script src="assetsMobi/bootstrap/js/bootstrap.min.js"></script>
  <script src="assetsMobi/dropdown/js/script.min.js"></script>
  <script src="assetsMobi/touch-swipe/jquery.touch-swipe.min.js"></script>
  <script src="assetsMobi/viewport-checker/jquery.viewportchecker.js"></script>
  <script src="assetsMobi/smooth-scroll/smooth-scroll.js"></script>
  <script src="assetsMobi/theme/js/script.js"></script>
  
  
  
  <input name="animation" type="hidden">
  
  
  
   <div id="scrollToTop" class="scrollToTop mbr-arrow-up"><a style="text-align: center;"><i class="mbr-arrow-up-icon"></i></a>
   </div>
   
 
   
  </body>
</html>