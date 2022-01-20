<?php  
?>
<!-- Navbar -->
<nav class="paralogin navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
	  <?php if (es_administrador()) :?>
         <span class="icon-bar"></span>
	  <?php endif; ?>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="aprincipal.php"><?php echo siglaGrupe." - ".$_SESSION['username']?></a>
    </div>
	
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
	  <?php if (es_administrador()) :?>
        <li><a href="login/cosasAdmin.php"><span class="glyphicon glyphicon-wrench"></span>admin</a></li>	  
	  <?php endif; ?>

        <li><a href="aprincipal.php"><span class="glyphicon glyphicon-home"></span></a></li>	  
		
		
        <li><a target=blank href="aayudote/ele-DocUsuario.html"><span class="glyphicon glyphicon-question-sign"></span></a></li>	  		
		
	  
        <li><a href="censo_menu0.php"><span><img src="imas/censo_r.png"> censos</a></li>	  

        <li><a href="oterrestre_menu0.php"><span class="glyphicon glyphicon-eye-open"></span>tierra</a></li>	  

        <li><a href="oremota_menu0.php"><span><img src="imas/andro_r.png"> </span>remota</a></li>	  

        <li><a href="publicaciones_menu0.php"><span class="glyphicon glyphicon-book"></span>publicaciones</a></li>	  

        <li><a href="mdatos_menu0.php"><span class="glyphicon glyphicon-plus"></span>datos</a></li>	  


		
		
        <li><a href="tb_logout.php"><span class="glyphicon glyphicon-log-out"></span>salir</a></li>
      </ul>
    </div>
  </div>
</nav>
<script>
$(document).ready(function(){
  $('.dropdown-submenu a.subme').on("click", function(e){
    $(this).next('ul').toggle();
    e.stopPropagation();
    e.preventDefault();
  });
});
</script>
<div style="height:70px"> </div>