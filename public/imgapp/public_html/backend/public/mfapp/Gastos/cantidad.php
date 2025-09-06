<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../Viewers/choferdata.css">
	<link rel="stylesheet" href="../css/style.css">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://code.jquery.com/ui/3.3.1/jquery-ui.js"></script>
	
</head>
<script>
		Date.prototype.toDateInputValue = (function() {
		    var local = new Date(this);
		    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
		    return local.toJSON().slice(0,10);
		});
	</script>
<?php 
	include '../Viewers/conexion.php';
	if (session_status() == PHP_SESSION_NONE  || session_id() == '') {
        session_start();
    }

	date_default_timezone_set('timezone_identifier');

	$id = $_POST['event'];
	if ($id != null) {
		$_SESSION['id'] = $_POST['event'];
	}else{
		$id = $_SESSION['id'];
	}


	$_SESSION['FechaRec']=date("Y")."-".date("m")."-".date("d");
	$FechaRec=$_SESSION['FechaRec'];
?>
<body>
	<section>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" novalidate>
			<br><br>
			<center><label for="cantidad">Cantidad</label>
			<input id="cantidad" name="cantidad" type="number" required="true">
			<br></center><br><br>
			<center><label for="tipo">Tipo</label>
				<select name="tipo" id="tipo" required="true">
					<?php
						
						$id =  $_SESSION['id'];
						$sqlEmpresa = "select * from transport_operations where id_transport_operation = '$id' ";
						$resultEmpresa = mysqli_query($conn, $sqlEmpresa) or die(mysqli_error($conn));
						if ($resultEmpresa->num_rows>0) {
							while ($rowEmpresa = mysqli_fetch_array($resultEmpresa)) {
								$id_empresa = $rowEmpresa['id_company'];
								$_SESSION['id_empresa'] = $id_empresa;
							}
						}
						$sql2="select * from expense_types where visibility_d='1' and id_companie='$id_empresa' order by name ASC ";
						$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
						if ($result2->num_rows>0) {
							while ($row2 = mysqli_fetch_array($result2)) {
								$tipo = $row2['unit_event'];
								$name = $row2['name'];
								$id_expensetypes = $row2['id_expensetypes'];
								echo "<option value='$id_expensetypes'>$name</option>";
							}
						}
						$id =  $_SESSION['id'];
						$sqlEmpresa = "select * from transport_operations where id_transport_operation = '$id' ";
						$resultEmpresa = mysqli_query($conn, $sqlEmpresa) or die(mysqli_error($conn));
					?>
				</select>
			</center> 	
			<br>
			<center><label for="fecha">Fecha</label></center>
			<div class="dateContainer">
				<div>
					<input id="FechaRec" required="true" type="date" name="FechaRec" value="<?php echo $FechaRec; ?>" >
				</div>
			</div>	
			<br>
			<center> <input type="submit" value="siguiente" class="submitButton"></center>
		</form>
		<br>
	</section>
	<?php 
		if(isset($_POST['cantidad'])){
			$_SESSION['cantidad']=$_POST['cantidad'];
			$_SESSION['tipo']=$_POST['tipo'];
			$_SESSION['FechaRec']=$_POST['FechaRec'];
			header('Location: evaluar.php');
			echo "tengo ".$_SESSION['cantidad'];
			echo "tengo ".$_SESSION['tipo'];
			echo "tengo ".$_SESSION['FechaRec'];
		}
	?>
</body>
</html>