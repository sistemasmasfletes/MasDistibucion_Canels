<?php 
	include('conexion.php');
	$user=$_POST['user'];
	$change = $_POST['change'];
	

	$sqlChange = "update users set custprov = '$change' where username = '$user'";
	//echo "$sqlChange";
	if($conn->query($sqlChange)){
		$response['success']=true;
	}else{
		$response['success']=false;
	}

	echo json_encode($response);
?>