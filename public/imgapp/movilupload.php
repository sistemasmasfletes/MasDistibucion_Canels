<?php
	$idop = $_GET['id_operation'];
	$dir ='../../../backend/public/images/imagesop/operation_'.$idop.'/';
	if(!file_exists($dir)) {//si no existe el directorio se crea y se pone en 0 la variable con la que se nombrara el elemento a agregar
		mkdir($dir, 0777, true);
		$tfiles = 0;
	}
	$dirSearch ='../../../backend/public/images/imagesop/operation_'.$idop.'/*.*';
	$dirSearchZip ='../../../backend/public/images/imagesop/operation_'.$idop.'/'.$idop.'.zip';
	$count = 0;
	$countZip = 0;
	foreach (glob($dirSearch) as $file) { $count++; }
	foreach (glob($dirSearchZip) as $file) { $countZip++; }
	$count-=$countZip;
	$namecount = $count+1;
	$name = $idop."_".$namecount.".jpg";
	$image = $_POST['photo'];
	$tarjet_dir = $dir.$name;
	
	
        //$uploaddir = '../public/images/evidencias/';
        //$nombre =$_POST['nombre'];
		
        //$ext='.png';
        //$filedir= $uploaddir.$nombre.$ext;
        //$imagen = base64_decode($_POST['action']);
       	//if(file_put_contents($filedir,$imagen)){
		
	$zip = new ZipArchive();
		
       	if(file_put_contents($tarjet_dir,base64_decode($image))){

        /*****Funciones optimizar imagenes*******/
        
        //Parametros optimizacion, resoluci�n m�xima permitida
        $max_ancho = 1024;
        $max_alto = 780;
        
        	//Redimensionar
        	//$rtOriginal=$filedir;
        	$rtOriginal=$tarjet_dir;

			$original = imagecreatefromjpeg($rtOriginal);
	   		//$original = imagecreatefrompng($rtOriginal);
        
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
        	//$res = imagepng($lienzo,$filedir);
        	$res =	imagejpeg($lienzo,$tarjet_dir,150);
			
		if(file_exists($dirSearchZip)){
			$zip->open($dirSearchZip);
			$zip->addFile($tarjet_dir);
		}else{
			$zip->open($dirSearchZip, ZIPARCHIVE::CREATE);
    		$zip->addFile($tarjet_dir);
		}
		$response= true;
			
			
       	}
       	/*****Terminan Funciones optimizar imagenes*******/	
	
	/*if(file_put_contents($tarjet_dir, base64_decode($image))){
	}*/
	else{
		//$response['message'] = false;
		$response= false;
	}
	$zip->close();
	echo json_encode($response);

	/*
	$directorio = opendir("../../../backend/public/images/imagesop/");
	  while ($archivo = readdir($directorio)){
	    if (is_dir($archivo))//verificamos si es o no un directorio
	    {
	        echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
	    }
	    else
	    {
	        echo $archivo . "<br />";
	    }
	}
	echo getcwd();*/
?>
