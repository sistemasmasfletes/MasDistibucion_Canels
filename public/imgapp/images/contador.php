<?php 
	  $count = 0; 
	  foreach (glob('../../../../*.*') as $file) { $count++; } 
	  //http://backend.masfletes.com/public/images/imagesop/operation_7084
	  //echo "el contador es $count\n";
	  $directorio = opendir("../../../../masfletes.com"); //ruta actual
	  
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
?>