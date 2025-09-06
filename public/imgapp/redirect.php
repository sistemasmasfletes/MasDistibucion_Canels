<?php 
	//header('location: /home/masdistr/public_html/backend/public/images/imagesop/');
$directorio = opendir("/home/masdistr/public_html/backend/public/images/imagesop/");
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
	echo getcwd();
?>