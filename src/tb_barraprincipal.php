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
	  
	
		
        <li><a href="tb_logout.php"><span class="glyphicon glyphicon-log-out"></span>salir</a></li>
      </ul>
    </div>
  </div>
</nav>

<div style="height:70px"> </div>