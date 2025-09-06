<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

</head>
<body>
	
<?php 
	include ('conexion.php');
	$sql = "select * from transportoperation_details where id_operation ='7620'";
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	if ($result->num_rows>0){
		while ($row = mysqli_fetch_array($result)){
			$id_relationship = $row['id_destination'];
		}
	}
	$sql = "select * from locations where id_locations = '$id_relationship' ";
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	if ($result->num_rows>0){
		while ($row = mysqli_fetch_array($result)){
			$map = $row['url_maps'];
		}
	}

	echo "la relacion es: $id_relationship <br>";
	
	echo "<a href='$map'>EL LINK</a>";
?>
</body>
</html>