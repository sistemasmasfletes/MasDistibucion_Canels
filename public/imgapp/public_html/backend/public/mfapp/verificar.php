<?php 
	/*
	$dirSearchZip ='../../../backend/public/images/imagesop/operation_7084/7084.zip';
	$res = $zip->open($dirSearchZip, ZipArchive::CREATE);
	if ($res === TRUE) {
    	$zip->close();
    	echo 'ok';
	} else {
    	echo 'falló';
    }
    */
    $zip = new ZipArchive();
    $dirSearchZip ='../../../backend/public/images/imagesop/operation_7084/prueba.zip';
    $nuevo = "../../../backend/public/images/imagesop/operation_7084/7084_13.jpg";
    if(file_exists($dirSearchZip)){
    	$zip->open($dirSearchZip);
    	$zip->addFile($nuevo);
    	echo "existe";
    	$zip->close();
    } else{
    	echo "No existe";
    	$zip->open($dirSearchZip, ZIPARCHIVE::CREATE);
    	$zip->addFile($nuevo);
    	$zip->close();
    }

    
?>