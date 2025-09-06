<?php 
	include('conexion.php');
	$user = $_POST['user'];
	$password = $_POST['pass'];
	$pass = $password;
	$password = md5($password);
	if(isset($_POST['change'])){
		$newCustprov = $_POST['change'];
		$sqlChange = "update users set custprov = '$newCustprov' where username = '$user' and password ='$password'";
		if($conn->query($sqlChange)){

		}else{
			
		}

	}
	

	$sql = "SELECT * FROM users WHERE username = '$user' AND password = '$password'";
	$result = $conn-> query($sql);
	if ($result->num_rows>0) {
		$response['success']=true;
		while ($row = mysqli_fetch_array($result)) {
			$response['id_users'] = $row['id_users'];
			$response['username']=$row['username'];
			$response['user_type']=$row['user_type'];
			$response['date_creation']=$row['date_creation'];
			$response['id_status']=$row['id_status'];
			$response['id_status']=$row['id_status'];
			$response['id_companie']=$row['id_companie'];
			$response['names']=$row['names'];
			$response['last_name']=$row['last_name'];
			$response['custprov']=$row['custprov'];
			$response['pass']=$pass;
		}
	}
	else{
		$response['success']=false;
	}
	$result->close();
	echo json_encode($response);
?>