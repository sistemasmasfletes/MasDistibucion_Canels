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
	//echo "recibo ".$_POST['proveedor'];
	
	include '../conexion.php';
	if (session_status() == PHP_SESSION_NONE  || session_id() == '') {
        session_start();
    }

	date_default_timezone_set('timezone_identifier');
	if(isset($_POST['odometro'])){
		$_SESSION['odometro']=$_POST['odometro'];
	}
	$_SESSION['id_relacion'] = $_POST['proveedor'];
	$id_relacion=$_SESSION['id_relacion'];

	
?>
<body>
	<section>
		<form action="final.php" method="POST">
			<label for="articulo">Artículo</label>
			<select name="id_articles" id="id_article">
				<?php 
					include('../conexion.php');
					$id_relacion= $_SESSION['id_relacion'];
					$sql = "select * from articles where id_relationship ='$id_relacion' ";
					$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
					if ($result->num_rows>0) {
						while ($row = mysqli_fetch_array($result)) {
							$id_articles = $row['id_articles'];
							$descripcion = $row['description'];
							$reference = $row['reference'];
							echo "<option value='$id_articles'>$reference con el id $id_articles</option>";
						}
					}
				?>
			</select><br>
			<center><input type="submit" value="siguiente"></center><br>
			<center><a href="cantidad.php" >Volver</a></center>
		</form>
	</section>
</body>