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


	
?>
<body>
	<section>
		
			<label for="articulo">A ocurrido un error con los datos proporcionados, por favor, inténtelo de nuevo.</label>
			<center><a href="cantidad.php" >Volver</a></center>
		</form>
	</section>
</body>