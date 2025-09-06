<?php 

	//$host = "masdistribucion.com.mx";
	//$user = "masdistr_app";
	//pass = "4ppt3st*/2018";
	//$BD = "masdistr_mf_pruebas";

	$host = "masdistribucion.com.mx";
	$user = "masdistr_fletes";
	$pass = "d15tr1buc10n/*";
	$BD = "masdistr_masfletes_mf";

	$conn = new mysqli($host, $user, $pass, $BD);

	if ($conn->connect_error) {
		die("conection failed".$conn->connect_error);
	}
 ?>