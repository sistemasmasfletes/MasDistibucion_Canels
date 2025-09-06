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
	<script>
		Date.prototype.toDateInputValue = (function() {
		    var local = new Date(this);
		    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
		    return local.toJSON().slice(0,10);
		});
	</script>
</head>
<?php 
	include 'conexion.php';
	if (session_status() == PHP_SESSION_NONE  || session_id() == '') {
        session_start();
    }

	
?>
<body>
	<section>
		<form action="articulos.php" method="POST" novalidate>
			<center>
			<label for="">Proveedor</label>
			<center><select name="proveedor" id="proveedor">
				<?php 
					include ('../conexion.php');

					$id_empresa = $_SESSION['id_empresa'];
					$cantidad=$_SESSION['cantidad'];
					$tipo=$_SESSION['tipo'];
					$fecha=$_SESSION['FechaRec'];

					mysqli_query("SET NAMES 'utf8'");
					//echo "ahora leo $id_empresa, $cantidad, $tipo y $fecha";
					$sql = "select relationship.id_company2, relationship.id_relationship, companies.business_name, companies.id_companies from relationship, companies where (relationship.id_company2=companies.id_companies) and relationship.id_company1='1' order by companies.business_name ASC";
					$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
					if ($result->num_rows>0) {
						while ($row = mysqli_fetch_array($result)) {
							$id_relationship = $row['id_relationship'];
							$nombre = $row['business_name'];
							echo "<option value='$id_relationship'>$nombre </option>";
						}
					}

				?>
			</select></center>
			<br>
		   <input type="submit" value="siguiente"  class="submitButton">
		</center>
		</form>
		<a href="cantidad.php">Volver</a>
	</section>
</body>
</html>