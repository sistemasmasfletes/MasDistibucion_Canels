<?php

if (isset($_FILES['imagen'])){
	
	$cantidad= count($_FILES["imagen"]["tmp_name"]);
	
	for ($i=0; $i<$cantidad; $i++){
		//Comprobamos si el fichero es una imagen
		if ($_FILES['imagen']['type'][$i]=='image/png' || $_FILES['imagen']['type'][$i]=='image/jpeg'){
		
		//Subimos el fichero al servidor
			if(move_uploaded_file($_FILES["imagen"]["tmp_name"][$i], $_FILES["imagen"]["name"][$i])){
		
			//if(file_put_contents($_FILES["imagen"]["tmp_name"][$i], $_FILES["imagen"]["name"][$i])){
			
				/*****Funciones optimizar imagenes*******/
			
				//Parametros optimizacion, resoluci�n m�xima permitida
				$max_ancho = 1024;
				$max_alto = 780;
			
				//Redimensionar
				$rtOriginal=$_FILES["imagen"]["name"][$i];
			
				$original = imagecreatefrompng($rtOriginal);
			
				list($ancho,$alto)=getimagesize($rtOriginal);
			
				$x_ratio = $max_ancho / $ancho;
				$y_ratio = $max_alto / $alto;
			
				if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){
					$ancho_final = $ancho;
					$alto_final = $alto;
				}elseif (($x_ratio * $alto) < $max_alto){
					$alto_final = ceil($x_ratio * $alto);
					$ancho_final = $max_ancho;
				}else{
					$ancho_final = ceil($y_ratio * $ancho);
					$alto_final = $max_alto;
				}
			
				$lienzo=imagecreatetruecolor($ancho_final,$alto_final);
			
				imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
				//imagepng($lienzo,$filedir);
				$res =	imagejpeg($lienzo,$_FILES["imagen"]["name"][$i], 75);
			}
			/*****Terminan Funciones optimizar imagenes*******/
		
		$validar=true;
		}else{
			$validar=false;
		}
	}
}

?>
<form method="post" action="?" enctype="multipart/form-data">
<input type="file" name="imagen[]" value="" multiple><br>

<input type="submit" value="Subir Imagen">
</form>


<?php  /*if (isset($_FILES['imagen']) && $validar==true){ ?>
<?php $cantidad= count($_FILES["imagen"]["tmp_name"]);
	
	for ($i=0; $i<$cantidad; $i++){?>
<h1><?php echo $_FILES["imagen"]["name"][$i] ?></h1>
<img src="<?php echo $_FILES["imagen"]["name"][$i] ?>" width="100">
<?php } }*/ ?>