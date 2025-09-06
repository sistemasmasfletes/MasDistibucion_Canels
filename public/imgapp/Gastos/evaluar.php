<?php 
	session_start();
	/*$_SESSION['cantidad']=$_POST['cantidad'];
	$_SESSION['tipo']=$_POST['tipo'];
	$_SESSION['FechaRec']=$_POST['FechaRec'];*/
	include '../conexion.php';
	if (isset($_SESSION['cantidad']) && isset($_SESSION['tipo']) && isset($_SESSION['FechaRec'])  ) {

		$id =  $_SESSION['id'];
		$sqlEmpresa = "select * from transport_operations where id_transport_operation = '$id' ";
		$resultEmpresa = mysqli_query($conn, $sqlEmpresa) or die(mysqli_error($conn));
		if ($resultEmpresa->num_rows>0) {
			while ($rowEmpresa = mysqli_fetch_array($resultEmpresa)) {
				$id_empresa = $rowEmpresa['id_company'];
			}
			$_SESSION['id_empresa'] = $id_empresa;
			$cantidad=$_SESSION['cantidad'];
			$tipo=$_SESSION['tipo'];
			$fecha=$_SESSION['FechaRec'];
			$id_empresa = $_SESSION['id_empresa'];
			$sql2="select * from expense_types where visibility_d='1' and id_companie='$id_empresa' and id_expensetypes='$tipo' ";
			$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
			if ($result2->num_rows>0) {
					while ($row2 = mysqli_fetch_array($result2)) {
						$tipo_de_evento = $row2['unit_event'];
					}
			}
		if($tipo_de_evento=='1'){
			header("location: proveedor.php");
		}
		else{
			header("location: proveedor2.php");
		}
	}
	}
	else{
		header("location: cantidad.php");	
	}

?>