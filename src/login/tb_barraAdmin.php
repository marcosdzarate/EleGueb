<?php  
?>
<!-- Navbar -->
<nav class="paralogin navbar navbar-default">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
		</button>
      <a class="navbar-brand" href="cosasAdmin.php"><?php echo siglaGrupe." - ".$_SESSION['username']?></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="cosasAdmin.php"><span class="glyphicon glyphicon-wrench"></span>admin</a></li>
        <li><a href="../aprincipal.php"><span class="glyphicon glyphicon-home"></span></a></li>
        <li><a href="../tb_logout.php"><span class="glyphicon glyphicon-log-out"></span>salir</a></li>
      </ul>
    </div>
  </div>
</nav>
<br>