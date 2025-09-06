<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>MasFletes</title>
	<link rel="stylesheet" href="choferdata.css">
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
	include 'conexion.php';
	if (session_status() == PHP_SESSION_NONE  || session_id() == '') {
        session_start();
    }
    $now = date("Y")."-".date("m")."-".date("d");
	date_default_timezone_set('timezone_identifier');

	$id = $_POST['event'];
	if ($id != null) {
		$_SESSION['id'] = $_POST['event'];
	}else{
		$id = $_SESSION['id'];
	}
	$sql = "SELECT * FROM transportoperation_details WHERE id_operation = '$id'";
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	if ($result->num_rows>0) {
		while ($row = mysqli_fetch_array($result)) {
			$FechaOrg = $row['rrd'];
			$HoraOrg = $row['rrt'];
			$FechaRec = $row['erd'];
			$HoraRec = $row['ert'];
			$FechaExtOrg = $row['rdd'];
			$HoraExtOrg = $row['rtd'];
			$FechaDest = $row['rda'];
			$HoraDest = $row['rta'];
			$FechaDes = $row['ede'];
			$HoraDes = $row['ete'];
			$FechaExtDes = $row['rde'];
			$HoraExtDes = $row['rte'];
			$receiver = $row['receiver'];
		}
	}

	if($FechaOrg == null || $FechaOrg=="0000-00-00" ){
		$FechaOrg = $now;
	}
	if($FechaRec == null || $FechaRec=="0000-00-00" ){
		$FechaRec = $now;
	}
	if($FechaExtOrg == null || $FechaExtOrg == "0000-00-00" ){
		$FechaExtOrg = $now;
	}
	if($FechaDest == null || $FechaDest== "0000-00-00"){
		$FechaDest = $now;
	}
	if($FechaDes == null || $FechaDes== "0000-00-00"){
		$FechaDes = $now;
	}
	if($FechaExtDes == null || $FechaExtDes== "0000-00-00"){
		$FechaExtDes = $now;
	}


	if ($HoraOrg == null) {
		$HoraOrg = "now";
	}
	if ($HoraRec == null) {
		$HoraRec = "now";
	}
	if ($HoraExtOrg == null) {
		$HoraExtOrg = "now";
	}
	if ($HoraDest == null) {
		$HoraDest = "now";
	}
	if ($HoraDes == null) {
		$HoraDes = "now";
	}
	if ($HoraExtDes == null) {
		$HoraExtDes = "now";
	}

	$sql = "SELECT * FROM transport_operations WHERE id_transport_operation = '$id'";
	$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
	if ($result->num_rows>0) {
		while ($row = mysqli_fetch_array($result)) {
			$status = $row['status'];
		}
	}
	if ($status == 3) {
		$status_name = "Transito-Carga";
	}else if ($status == 4) {
		$status_name = "Vigilancia-O";
	}else if ($status == 5) {
		$status_name = "Cargando";
	}else if ($status == 6) {
		$status_name = "Transito-Entrega";
	}else if ($status == 7) {
		$status_name = "Vigilancia-D";
	}else if ($status == 8) {
		$status_name = "Descargando";
	}else if ($status == 2) {
		$status_name = "Terminado";
	}
	echo "
		<script>
			var status = '".$status."';
		</script>
		";
 ?>
<body>
	<section>
		<!--Comenzar-->
		<form method="POST" novalidate id="comenzar" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input hidden="" type="number" value="3">
			<input class="submitButton" type="submit" value="Comenzar Operación" name="comenzar">
		</form>
		<?php 
			if (isset($_POST['comenzar'])) {
				$sql = "UPDATE transport_operations SET status='3' WHERE id_transport_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					echo "correcto";
					header("Refresh:0");
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}
			}
			
		 ?>
		
		<!--Vigilancia-->
		<form method="POST" novalidate  id="llegada" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<p class="yellowtext"><?php echo $status_name; ?></p>
			<h1 class="white"><input class="submitClick" type="submit" id="llegadabutton" name="Llegada" value=" "> Fecha y Hora de Llegada</h1>
			<div class="dateContainer">
				<div>
					<input id="fechaorg" type="date" name="fechaorg" value="<?php echo $FechaOrg; ?>">
					<input id="horaorg" type="time"  name="horaorg" value="<?php echo $HoraOrg; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="llegadacambiar" name="LlegadaCambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Llegada'])) {
				$FechaOrg = $_POST['fechaorg'];
				$HoraOrg = $_POST['horaorg'];
				$sql = "UPDATE transportoperation_details SET rrd ='$FechaOrg' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rrt ='$HoraOrg' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='4' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								header("Refresh:0");
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['LlegadaCambiar'])) {
				$FechaOrg = $_POST['fechaorg'];
				$HoraOrg = $_POST['horaorg'];
				$sql = "UPDATE transportoperation_details SET rrd ='$FechaOrg' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rrt ='$HoraOrg' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						header("Refresh:0");
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

		 <!--Cargando-->
		 <form method="POST" novalidate  id="cargando" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<h1 class="white"><input class="submitClick" type="submit" id="recbutton" name="Recoleccion" value=" "> Fecha y Hora de Recolección</h1>
			<div class="dateContainer">
				<div>
					<input id="FechaRec" type="date" name="FechaRec" value="<?php echo $FechaRec; ?>">
					<input id="HoraRec" type="time"  name="HoraRec" value="<?php echo $HoraRec; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="reccambiar" name="RecCambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Recoleccion'])) {
				$FechaRec = $_POST['FechaRec'];
				$HoraRec = $_POST['HoraRec'];
				$sql = "UPDATE transportoperation_details SET erd ='$FechaRec' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET ert ='$HoraRec' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='5' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								header("Refresh:0");
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['RecCambiar'])) {
				$FechaRec = $_POST['FechaRec'];
				$HoraRec = $_POST['HoraRec'];
				$sql = "UPDATE transportoperation_details SET erd ='$FechaRec' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET ert ='$HoraRec' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						header("Refresh:0");
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

		 <!--Transito-Entrega-->
		 <form method="POST" novalidate  id="Transito-Entrega" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<h1 class="white"><input class="submitClick" type="submit" id="Transito-Entregabutton" name="Transito-Entrega" value=" "> Fecha y Hora de Salida del origen</h1>
			<div class="dateContainer">
				<div>
					<input id="FechaExtOrg" type="date" name="FechaExtOrg" value="<?php echo $FechaExtOrg; ?>">
					<input id="HoraExtOrg" type="time"  name="HoraExtOrg" value="<?php echo $HoraExtOrg; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="transitocambiar" name="transitocambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Transito-Entrega'])) {
				$FechaExtOrg = $_POST['FechaExtOrg'];
				$HoraExtOrg = $_POST['HoraExtOrg'];
				$sql = "UPDATE transportoperation_details SET rdd ='$FechaExtOrg' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rtd ='$HoraExtOrg' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='6' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								header("Refresh:0");
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['transitocambiar'])) {
				$FechaExtOrg = $_POST['FechaExtOrg'];
				$HoraExtOrg = $_POST['HoraExtOrg'];
				$sql = "UPDATE transportoperation_details SET rdd ='$FechaExtOrg' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rtd ='$HoraExtOrg' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						header("Refresh:0");
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

		 <!--Vigilancia-D-->
		 <form method="POST" novalidate  id="Vigilancia-D" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<h1 class="white"><input class="submitClick" type="submit" id="Vigilancia-Dbutton" name="Vigilancia-D" value=" "> Fecha y Hora de llegada al destino</h1>
			<div class="dateContainer">
				<div>
					<input id="FechaDest" type="date" name="FechaDest" value="<?php echo $FechaDest; ?>">
					<input id="HoraDest" type="time"  name="HoraDest" value="<?php echo $HoraDest; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="vigilanciacambiar" name="vigilanciacambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Vigilancia-D'])) {
				$FechaDest = $_POST['FechaDest'];
				$HoraDest = $_POST['HoraDest'];
				$sql = "UPDATE transportoperation_details SET rda ='$FechaDest' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rta ='$HoraDest' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='7' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								header("Refresh:0");
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['vigilanciacambiar'])) {
				$FechaDest = $_POST['FechaDest'];
				$HoraDest = $_POST['HoraDest'];
				$sql = "UPDATE transportoperation_details SET rda ='$FechaDest' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rta ='$HoraDest' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						header("Refresh:0");
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

		 <!--Descargando-->
		 <form method="POST" novalidate  id="Descargando" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		 	
			<h1 class="white"><input class="submitClick" type="submit" id="Descargandobutton" name="Descargando" value=" "> Fecha y Hora de Descarga</h1>
			<div class="dateContainer">
				<div>
					<input id="FechaDes" type="date" name="FechaDes" value="<?php echo $FechaDes; ?>">
					<input id="HoraDes" type="time"  name="HoraDes" value="<?php echo $HoraDes; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="Descargandocambiar" name="Descargandocambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Descargando'])) {
				$FechaDes = $_POST['FechaDes'];
				$HoraDes = $_POST['HoraDes'];
				$sql = "UPDATE transportoperation_details SET ede ='$FechaDes' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET ete ='$HoraDes' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='8' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								echo "<meta http-equiv='refresh' content='0'>";
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['Descargandocambiar'])) {
				$FechaDes = $_POST['FechaDes'];
				$HoraDes = $_POST['HoraDes'];
				$sql = "UPDATE transportoperation_details SET ede ='$FechaDes' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET ete ='$HoraDes' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						echo "<meta http-equiv='refresh' content='0'>";
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

			
			<br>
		 <form id="nameR" method="POST" action="<?php 	echo $_SERVER['PHP_SELF']; ?>">
		 	<div class="dateContainer">	
		 		<div>
				 	<label for="recibe">Recibió:</label>
				 	<input name="recibe" type="text" value="<?php echo $receiver; ?>">
		 		</div>
			 	<input type="submit" class="submitButton" name="cambiarnombre">
		 	</div>
		 </form>
		 <br>
		 <?php
	 		if (isset($_POST['cambiarnombre'])) {
	 		 		$Recibe = $_POST['recibe'];
	 		 		$sql = "UPDATE transportoperation_details set receiver = '$Recibe' WHERE id_operation = '$id'";
	 		 		if ($conn->query($sql)===TRUE){
	 		 			echo "correcto";
						echo "<meta http-equiv='refresh' content='0'>";
	 		 		}else{
	 		 			echo "Error: ".$sql."<br>".$conn->error;
	 		 		}
	 		 	} 	
		  ?>

		  <!--Terminado-->
		 <form method="POST" novalidate  id="Terminado" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<h1 class="white"><input class="submitClick" type="submit" id="Terminadobutton" name="Terminado" value=" "> Fecha y Hora de salida del destino</h1>
			<div class="dateContainer">
				<div>
					<input id="FechaExtDes" type="date" name="FechaExtDes" value="<?php echo $FechaExtDes; ?>">
					<input id="HoraExtDes" type="time"  name="HoraExtDes" value="<?php echo $HoraExtDes; ?>">
				</div>
				<br>
				<input class="submitButton" type="submit" id="Terminadocambiar" name="Terminadocambiar" value="Cambiar">
			</div>
		</form>
		<?php 
			if (isset($_POST['Terminado'])) {
				$FechaExtDes = $_POST['FechaExtDes'];
				$HoraExtDes = $_POST['HoraExtDes'];
				$sql = "UPDATE transportoperation_details SET rde ='$FechaExtDes' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rte ='$HoraExtDes' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						$sql = "UPDATE transport_operations SET status='2' WHERE id_transport_operation = '$id'";
							if ($conn->query($sql)===TRUE) {
								echo "correcto";
								echo "<meta http-equiv='refresh' content='0'>";
							}else{
								echo "Error: ".$sql."<br>".$conn->error;
							}
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
			if (isset($_POST['Terminadocambiar'])) {
				$FechaExtDes = $_POST['FechaDes'];
				$HoraExtDes = $_POST['HoraExtDes'];
				$sql = "UPDATE transportoperation_details SET rde ='$FechaExtDes' WHERE id_operation = '$id'";
				if ($conn->query($sql)===TRUE) {
					$sql = "UPDATE transportoperation_details SET rte ='$HoraExtDes' WHERE id_operation = '$id'";
					if ($conn->query($sql)===TRUE) {
						echo "Correcto";
						echo "<meta http-equiv='refresh' content='0'>";
					}else{
						echo "Error: ".$sql."<br>".$conn->error;
					}
				}else{
					echo "Error: ".$sql."<br>".$conn->error;
				}

			}
		 ?>

	</section>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<script>
		$(function(){     
		  var d = new Date(),        
		      h = d.getHours(),
		      m = d.getMinutes();
		  if(h < 10) h = '0' + h; 
		  if(m < 10) m = '0' + m; 
		  $('input[type="time" ][value="now"]').each(function(){ 
		    $(this).attr({'value': h + ':' + m});
		  });
		  $('input[type="date"][value="now"]').each(function(){
			$(this).val(new Date().toDateInputValue());
		  });
		});
		if(status == 2){
			$("#comenzar").hide();
		}
		if(status < 3){
			$("#llegada").hide();
		}
		if (status >= 3 ) {
			$("#comenzar").hide();
		}
		if (status > 3) {
			$("#llegadabutton").css("background-color","#006699");
		}
		if (status < 4) {
			$("#llegadacambiar").hide();
			$("#cargando").hide();
		}
		if (status > 4){
			$("#recbutton").css("background-color","#006699");
		}
		if (status < 5) {
			$("#reccambiar").hide();
			$("#Transito-Entrega").hide();
		}
		if (status < 6) {
			$("#transitocambiar").hide();
			$("#Vigilancia-D").hide();
		}else{
			$("#Transito-Entregabutton").css("background-color","#006699");
		}
		if(status < 7){
			$("#vigilanciacambiar").hide();
			$("#Descargando").hide();
			$("#Terminado").hide();
		}else{
			$("#Vigilancia-Dbutton").css("background-color","#006699");
		}
		if(status < 8){
			$("#Descargandocambiar").hide();
			$("#nameR").hide();
		}else{
			$("#Descargandobutton").css("background-color","#006699");
		}
		if(status == 8){
			$("#Terminadocambiarm").hide();
		}
	</script>
</body>
</html>