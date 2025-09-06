<?php 
	session_start();
	include('../conexion.php');
	$_SESSION['comentarios']=$_POST['comentarios'];
	$comentarios=$_SESSION['comentarios'];
	$id_operation = $_SESSION['id'];
	$valor = $_SESSION['valor'];
	$iva_total = $_SESSION['iva_total'];


	$retencion = $_SESSION['retencion'];
	if($retencion==''){
		$retencion='0.00';
	}
	$cantidad = $_SESSION['cantidad'];
	$total = $_SESSION['total'];
	$proveedor = $_SESSION['id_provider'];
	$descripcion = $_SESSION['descripcion'];
	$fecha = $_SESSION['FechaRec'];
	$id_article=$_SESSION['id_articles'];
	$sql0="select * from transport_operations where id_transport_operation = '$id_operation' ";
	$result = mysqli_query($conn, $sql0) or die(mysqli_error($conn));
	if ($result->num_rows>0){
		while ($row = mysqli_fetch_array($result)){
			$id_user = $row['id_user'];
			$id_companie= $row['id_company'];
			$id_relationshiptype = $row['id_relationship'];
		}
	}

	$id_expensetype= $_SESSION['tipo'];


	$sqlR =  "select * from relationship where id_relationship='$id_relationshiptype' ";
	$resultado =  mysqli_query($conn, $sqlR) or die(mysqli_error($conn));
	if ($resultado->num_rows>0) {
		while ($row3 = mysqli_fetch_array($resultado)){
			$id_correcto=$row3['relationship_type'];
		}
	}

	$sql="insert into operating_expenses (id_operation, valor, iva, retention, quantity, total, id_provider, description, date_creation, id_user, id_companie, id_relationshiptype, id_article, inorder,id_expensetype, comments) values ( '$id_operation',  '$valor', '$iva_total' ,'$retencion', '$cantidad', '$total', '$proveedor', '$descripcion', '$fecha', '$id_user', '$id_companie', $id_correcto, '$id_article', '1', '$id_expensetype',  '$comentarios')";
	//echo "el sql es $sql";
	$result2 = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	$id_obtenido = $conn->insert_id;




	//ésto sólo entrará cuando la lectura de odómetro exista
	/*
	if(isset($_SESSION['odometro'])){
		$sqlUnit="select id_unit from transportoperation_details where id_operation = '".$_SESSION['id']."'";
		$resultadoUnit =  mysqli_query($conn, $sqlR) or die(mysqli_error($conn));
		if ($resultadoUnit->num_rows>0) {
			while ($row4 = mysqli_fetch_array($resultadoUnit)){
				$id_unit=$row4['id_unit'];
			}
		}
		$odometro = $_SESSION['odometro'];
		//sql para obtener id_company


		$sqlOdometro = "insert into unit_kilometers (id_unit, quantity, date, id_operation, id_expense, id_company, id_user, type) values('$id_unit', '$odometro', '$id_operation', '$id_obtenido', '$id_companie', '$id_user', '2' ) ";
		$resultOdometro =mysqli_query($conn, $sqlOdometro) or die(mysqli_error($conn));
		if($resultOdometro){
			header('location: cantidad.php');	
		} else{
			header('Location: error.php');
		}
	}

	*/
	if(isset($_SESSION['odometro'])){
		$odometro=$_SESSION['odometro'];
		$sqlUnit="select id_unit from transportoperation_details where id_operation = '".$_SESSION['id']."'";
		$resultadoUnit =  mysqli_query($conn, $sqlUnit) or die(mysqli_error($conn));
		if ($resultadoUnit->num_rows>0) {
			while ($row4 = mysqli_fetch_array($resultadoUnit)){
				$id_unit=$row4['id_unit'];
			}
		}
		$sqlOdometro = "insert into unit_kilometers (id_unit, quantity, date, id_operation, id_expense, id_company, id_user, type) values('$id_unit', '$odometro', '$fecha','$id_operation', '$id_obtenido', '$id_companie', '$id_user', '9' ) ";
		
		
		$resultOdometro =mysqli_query($conn, $sqlOdometro) or die(mysqli_error($conn));
		if($resultOdometro){
			header('location: cantidad.php');	
		} else{
			header('Location: error.php');
		}
	}

	if($result2){
		header('location: cantidad.php');
	}else{
		header('Location: error.php');

	}
?>