<!DOCTYPE html>
<html>
<body>

<?php
		$f1a = "9999-99-99";
		$ffa = "";
		$s = "2015-10-11";
		$f1a = min($f1a,substr($s,5,5));
		$ffa = max($ffa,substr($s,5,5));
		echo $s ."<br>";
		echo $f1a." min <br>";
		echo $ffa." max <br>";
		$s = "2017-11-11";
		$f1a = min($f1a,substr($s,5,5));
		$ffa = max($ffa,substr($s,5,5));
		echo $s ."<br>";
		echo $f1a." min <br>";
		echo $ffa." max <br>";
		$s = "2014-05-11";
		$f1a = min($f1a,substr($s,5,5));
		$ffa = max($ffa,substr($s,5,5));
		echo $s ."<br>";
		echo $f1a." min <br>";
		echo $ffa." max";
?>

</body>
</html>