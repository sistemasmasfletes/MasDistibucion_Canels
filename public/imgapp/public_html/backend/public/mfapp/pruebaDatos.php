<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php 
	include('conexion.php');
	$id_operation = "7084";
	$contador = 0;
	$sql1 = "select * from transportoperation_details where id_operation= '$id_operation'";
	$result = mysqli_query($conn, $sql1) or die(mysqli_error($conn));
	if ($result->num_rows>0){
		while ($row = mysqli_fetch_array($result)){
			$id_customer = $row['id_company_customer'];
		}
	}
	echo "el cliente es $id_customer\n";
	$sql2 = "select * from users where id_companie ='$id_customer'";
	$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
	if($result2->num_rows>0){
		while ($row = mysqli_fetch_array($result2)) {
			$id_users[$contador] = $row['id_users'];
			$names[$contador] = $row['names'];
			$last_names[$contador] = $row['last_name'];
			$contador++;
		}
	}
	echo "y el otro es: ";
	for ($i=0; $i < $contador; $i++) { 

		echo "<h3>Id:$id_users[$i] nombre: $names[$i] apellidos: $last_names[$i]</h3><br>";
	}
?>

</body>
</html>