<?php  
?>
<!-- Navbar -->
<nav class="paralogin navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="aprincipal.php"><?php echo siglaGrupe." - ".$_SESSION['username']?></a>
    </div>
	
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">



		
		
        <li><a onclick="window.close()" class="btn " ><span class="glyphicon glyphicon-remove"></span>cerrar</a></li>
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