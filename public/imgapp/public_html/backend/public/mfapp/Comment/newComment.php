<?php
/*
	include ('../conexion.php');
	$id_operation = _POST['id_operation'];
	$id_driver = $_POST['id_driver'];
	$id_customer = $_POST['id_customer'];
	$comment = $_POST['comment'];
	//$sql = "insert into usuario "
	$month= date("m");
	$day= date("d");
	$year = date("Y");
	$date = "$year-$month-$day";
	$response['success']=false;
	
	$sql = "insert into comment(id_operation, id_driver, id_customer, comment, date, status, mostrar) values ('$id_operation', '$id_driver', '$id_customer', '$comment', '$date','1', '1')";
	if ($conn->query($sql)) {
		//echo "realizado con éxito";
		$response['success']=true;
	} else{
		$response['success']=false;
	}
	$conn->close();
	echo json_encode($response);*/

	include ('../conexion.php');
	$contador = 0;
	$month= date("m");
	$day= date("d");
	$year = date("Y");
	$date = "$year-$month-$day";
	$response['success']=false;
	$id_operation = $_POST['id_operation'];
	$id_user = $_POST['id_user'];
	$comment = $_POST['comment'];
	$sql1 = "select * from transportoperation_details where id_operation='$id_operation' ";
	$result = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
	while ($row = mysqli_fetch_array($result)){
			$id_customer = $row['id_company_customer'];
			$id_driver = $row['id_driver'];
	}
	/*$sql2 = "select * from users where id_companie ='$id_customer'";
	$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
	if($result2->num_rows>0){
		while ($row = mysqli_fetch_array($result2)) {
			$id_users[$contador] = $row['id_users'];
			$contador++;
		}
	}*/
	$sql2 = "insert into comment (id_operation, id_driver, id_customer, comment, date, status, mostrar, id_user) values ('$id_operation', '$id_driver', '$id_customer', '$comment', '$date', '1', '1', '$id_user' ) ";

	if ($conn->query($sql2)) {
		//echo "realizado con éxito";
		$response['success']=true;
	} else{
		$response['success']=false;
	}
	$conn->close();
	echo json_encode($response);
	
?>