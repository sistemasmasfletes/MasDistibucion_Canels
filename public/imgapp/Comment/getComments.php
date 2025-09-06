<?php
	include ('../conexion.php');
	$contador=0;
	$response = array();
	$id_operation=$_GET['id_operation'];
	$sql1 = "select * from comment where id_operation = '$id_operation' and status ='1' and mostrar='1' ";
	$result = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
	while ($row = mysqli_fetch_array($result)){
		$id_user = $row['id_user'];
		$id_driver = $row['id_driver'];
		$id_customer = $row['id_customer'];
		$text = $row['comment'];
		$date = $row['date'];

		//Nombre de quien realizó el comentario
		$sql2 = "select * from users where id_users = '$id_user' ";
		$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		while ($row2 = mysqli_fetch_array($result2)) {
			$name = $row2['names'];
			$last_name = $row2['last_name'];
		}

		//Nombre de la compañía del cliente
		$sql2 = "select * from companies where id_companies = '$id_customer' ";
		$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		while ($row2 = mysqli_fetch_array($result2)) {
			$name_customer = $row2['business_name'];
		}


		//Nombre del conductor
		$sql2 = "select * from users where id_users = '$id_driver' ";
		$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
		while ($row2 = mysqli_fetch_array($result2)) {
			$name_driver = $row2['names'];
			$last_name_driver = $row2['last_name'];
		}		

		array_push($response, 
			array(
				'user'=>''.$name.' '.$last_name,
				'text'=>$text,
				'date'=>$date,
				'customer'=>$name_customer,
				'driver'=>$name_driver.' '.$last_name_driver
			)
		);
	}
	echo json_encode($response);
?>