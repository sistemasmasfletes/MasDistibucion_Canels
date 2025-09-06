<?php
if($_FILES){
	foreach($_FILES as $imagen => $value){
		if($value['error'] == 0){
			
			echo $value['name']."<br />";
			/*if(is_uploaded_file($value['tmp_name'])){
				$tmpNombreImagen = explode(".", $value["name"]);
				$nombreImagen = $_POST['nombreImagen'] ."_".$imagen.".". end($tmpNombreImagen);
				move_uploaded_file($value['tmp_name'], "../imagenes_pre_inscripcion/$nombreImagen");
			}*/
		}
	}
}