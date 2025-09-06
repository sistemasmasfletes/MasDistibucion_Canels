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
	
	include ('../conexion.php');
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
	$_SESSION['id_articulo']=$_POST['id_articulo'];
	//$_SESSION['id']="5022";
	$id = $_SESSION['id'];
	$_SESSION['id_relacion'] = $_POST['proveedor'];
	$id_relacion=$_SESSION['id_relacion'];
	$_SESSION['id_articles']=$_POST['id_articles'];
	include('../conexion.php');
				$id_articles=$_SESSION['id_articles'];
				$sql = "select * from articles where id_articles = '$id_articles' ";
				$result =  mysqli_query($conn, $sql) or die(mysqli_error($conn));
				if ($result->num_rows>0){
					while ($row = mysqli_fetch_array($result)){
						$nombre_articulo = $row['reference'];
						$precio = $row['price'];
						$iva = $row['iva'];
						$retention = $row['retention'];
						$comentarios = $row['comments'];
					}
				}
?>
<body>
	<section>
		<form action="evalReg.php" method="POST">
			<?php 
				session_start();
				include('../conexion.php');
				$id_articles=$_SESSION['id_articles'];
				$sql = "select * from articles where id_articles = '$id_articles' ";
				$result =  mysqli_query($conn, $sql) or die(mysqli_error($conn));
				if ($result->num_rows>0){
					while ($row = mysqli_fetch_array($result)){
						$nombre_articulo = $row['reference'];
						$precio = $row['price'];
						$iva = $row['iva'];
						$retention = $row['retention'];
						$descripcion=$row['description'];
						$id_provider=$row['id_company_provider'];
					}
				}

				$cantidad = $_SESSION['cantidad'];
				$iva_total = $cantidad*$iva;

				$total =(($precio*$cantidad)+$iva_total)+$retention;
				$_SESSION['valor']=$precio;
				$_SESSION['iva_total']=$iva_total;
				$_SESSION['total']=$total;
				$_SESSION['descripcion']=$descripcion;
				$_SESSION['retencion']=$retention;
				$_SESSION['id_provider']=$id_provider;

				echo "
					<label for='valor'>Valor:</label><br>
					<label for=''>$0$precio</label><br>
					<label for='IVA'>IVA:</label><br>
					<label for=''>$0$iva_total</label><br>
					<label for='retencion'>Retención:</label><br>
					<label for=''>$0$retention</label><br>
					<label for='total'>Total:</label><br>
					<label for=''>$0$total</label><br>
					<label for='descripcion:'>Descripción:</label><br>
					<label for=''>$descripcion</label><br>
					<label for='comentarios'>Comentarios a proveedor</label><br>
					<input type='text' name='comentarios'	><br>
					<input type='submit' value='guardar' class='submitButton' ><br>
				"; 
			?>
			
		</form>
		<a href="cantidad.php" >Volver</a>
	</section>
</body>