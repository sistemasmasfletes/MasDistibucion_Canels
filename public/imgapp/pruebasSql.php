
<form action="">
	<label for="">Proveedor</label><br>
	<select name="" id="">
		<?php 
		
			include ('conexion.php');
			
			$id_empresa = $_SESSION['id_empresa'];
			$cantidad=$_SESSION['cantidad'];
			$tipo=$_SESSION['tipo'];
			$fecha=$_SESSION['FechaRec'];
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

			
			/*
			$contador =0;
			
			$sql = "select * from relationship where id_company1='1'";
			$result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
			if ($result->num_rows>0) {
				while ($row = mysqli_fetch_array($result)) {
					$id_relationship = $row['id_relationship'];
					$id_company2 = $row['id_company2'];
					$relationship_type = $row['relationship_type'];
					$creation_date = $row['creation_date'];
					$id_company = $row['id_company'];
					$id_user = $row['id_user'];
					//echo "<option value='$id_relationship'>  $id_company </option>";
					$sqlNombre = "	'$id_company2' ";
					$result2 = mysqli_query($conn, $sqlNombre) or die(mysqli_error($conn));
					if($result2->num_rows>0){
						while ($row2 = mysqli_fetch_array($result2)) {
							$nombre = $row2['business_name'];
							$contador++;
							echo "<option value='$id_relationship'>  $nombre  y núermo $contador</option>";
						}
					}	
				}
			}
			*/
		?>		
	</select>
</form>
