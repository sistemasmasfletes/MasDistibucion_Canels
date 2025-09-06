<?php
   $tarjet_dir = "fotos/";
   $image = $_POST['image'];
   if(!file_exists($tarjet_dir)){
      mkdir($tarjet_dir, 0777, true);
   }
   $tarjet_dir = $tarjet_dir."nueva2.jpeg";
   if(file_put_contents($tarjet_dir, base64_decode($image))){
      $zip = new ZipArchive;
      if ($zip->open('fotos/test_new.zip', ZipArchive::CREATE) === TRUE){
         $zip->addFile('fotos/nueva.jpeg');
         $zip->close();
      }


      $response['message'] = true;
   }else{
      $response['message'] = false;
   }
   echo json_encode($response);
?>