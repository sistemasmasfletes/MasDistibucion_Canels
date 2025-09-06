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
	$zip = new ZipArchive();
	if(file_put_contents($tarjet_dir, base64_decode($image))){
		if(file_exists($dirSearchZip)){
			$zip->open($dirSearchZip);
			$zip->addFile($tarjet_dir);
		}else{
			$zip->open($dirSearchZip, ZIPARCHIVE::CREATE);
    		$zip->addFile($tarjet_dir);
		}
		$response= true;
	}else{
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
