<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php 
		//Reemplazar la cantidad con el número de páginas
		$cantidad = 154;
		
		for ($i=0; $i < $cantidad; $i++) {
			if($i<10){

				echo "<img src='https://libros.conaliteg.gob.mx/c/P2FCA/00$i.jpg' alt=''>";
			} elseif($i<100){
				echo "<img src='https://libros.conaliteg.gob.mx/c/P2FCA/0$i.jpg' alt=''>";
			} elseif ($i<1000) {
				echo "<img src='https://libros.conaliteg.gob.mx/c/P2FCA/$i.jpg' alt=''>";
			}
		}
	 ?>
</body>
</html>