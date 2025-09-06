<?php 
	include 'conexion2.php';
	$user = $_GET["user"];
	$pass = md5($_GET["pass"]);
	$response;
	$json_array = array();
	$values = array();
	$colorChange = false;
	//$user = "coordinador1.masfletes@gmail.com";
	//$user = "HEMM910517";
	//$pass = md5(123);
	// Se obtiene el usuario y la contraseña
	$sql = "SELECT * FROM users WHERE username = '$user'";
	$result = $conn->query($sql);
	if ($result->num_rows>0){
		$response = true;
		while($row = $result->fetch_assoc()){
			$id_company = $row['id_companie'];
			$id_user = $row['id_users'];
			$custprov = $row['custprov'];
			$user_type = $row['user_type'];

		}
	}else{
		//Si no se encuentra envía false a la app
		$response = false;
		echo json_encode($response);
	}
	//funcion para busqueda sql
	function searchSQL($sql, $conn, $data){
		//echo $response;
		$result = mysqli_query($conn, $sql) or die (mysqli_error($conn));
		if ($result->num_rows>0) {
			while($row = mysqli_fetch_array($result)){
				$response = json_encode($row[$data]);
			}
		}else{
			$response = json_encode("NO");
		}

		return $response;
	}

	// Si se encuentra el usuario se buscan los datos
	if ($custprov == 2 || $user_type == 3 || $user_type == 1 || $user_type == 2 || $user_type == 4) {
		if ($response == true) {
			$sql = "SELECT * FROM transport_operations WHERE id_user = $id_user AND status<'9' ORDER BY operation_date DESC LIMIT 15";
			$result = mysqli_query($conn, $sql) or die (mysqli_error($conn));
			while($row = mysqli_fetch_array($result)){
				//Estatus
				//Buscar compañias
				$sqlIn = "SELECT business_name FROM companies WHERE id_companies = '$id_company'";
				$business_name = searchSQL($sqlIn, $conn, 'business_name');
				//Buscar tipo de operación
				$id_type = $row['id_type'];
				$sqlIn = "SELECT name FROM operation_types WHERE id_operation_types = '$id_type'";
				$operation_types = searchSQL($sqlIn, $conn, 'id_type');
				//Buscar Folio
				$id_note = $row['id_note'];
				$sqlIn = "SELECT * FROM letter_bearing WHERE id_letter_bearing = '$id_note'";
				$folio = searchSQL($sqlIn, $conn, 'folio');
				//Buscar Detalles
				$id_operation = $row['id_transport_operation'];
				$sqlIn = "SELECT * FROM transportoperation_details WHERE id_operation = '$id_operation'";
				$resultIn = mysqli_query($conn, $sqlIn) or die (mysqli_error($conn));
				if ($resultIn->num_rows>0) {
					while($rowIn = mysqli_fetch_array($resultIn)){
						//Fecha real llegada a origen
						$rrd = json_encode($rowIn['rrd']);
						//Hora real llegada a origen
						$rrt = json_encode($rowIn['rrt']);
						//Fecha real de recolección
						$erd = json_encode($rowIn['erd']);
						//Hora real de recolección
						$ert = json_encode($rowIn['ert']);
						//Fecha real de salida del origen
						$rdd = json_encode($rowIn['rdd']);
						//Hora real de salida del origen
						$rtd = json_encode($rowIn['rtd']);
						//Fecha real de llegada al destino
						$rda = json_encode($rowIn['rda']);
						//Hora real de llegada al destino
						$rta = json_encode($rowIn['rta']);
						//Fecha real de descarga
						$ede = json_encode($rowIn['ede']);
						//Hora real de descarga
						$ete = json_encode($rowIn['ete']);
						//Fecha real de salida de destino(fin)
						$rde = json_encode($rowIn['rde']);
						//Hora real de salida de destino(fin)
						$rte = json_encode($rowIn['rte']);
						//Recibió
						$receiver = json_encode($rowIn['receiver']);
						//Estado del vieaje
						//Deprecated//$travel_status = json_encode($rowIn['travel_status']);
						//$travel_status = json_encode("2");
					}
				}else{
					$rrd = json_encode("");
					$rrt = json_encode("");
					$erd = json_encode("");
					$ert = json_encode("");
					$rdd = json_encode("");
					$rtd = json_encode("");
					$rda = json_encode("");
					$rta = json_encode("");
					$ede = json_encode("");
					$ete = json_encode("");
					$rde = json_encode("");
					$rte = json_encode("");
					$receiver = json_encode("");
					//Deprecated -> $travel_status = json_encode("2");
				}
				//Info extra para la app
				if ($colorChange != true) {
					$colorTable = "#3399cc";
					$colorChange = true;
				}else{
					$colorTable = "#66ccff";
					$colorChange = false;
				}
				//Crear array
				array_push($json_array,
					array(
						'user_type'=>"1",
						'id'=>$row['id_transport_operation'],
						'fecha'=>json_encode($row['operation_date']),
						'cantidad'=>json_encode($row['quantity']),
						'tipo'=>$operation_types,
						'empresa'=>$business_name,
						'comentario'=>json_encode($row['comments']),
						'descripcion'=>json_encode($row['description']),
						'documentacion'=>json_encode($row['documentation']),
						'solicitante'=>json_encode($row['applicant']),
						'estatus'=>$row['status'],
						'folio'=>$folio,
						'color'=>$colorTable,
						'FechaOrg'=>$rrd,
						'HoraOrg'=>$rrt,
						'FechaRec'=>$erd,
						'HoraRec'=>$ert,
						'FechaExtOrg'=>$rdd,
						'HoraExtOrg'=>$rtd,
						'FechaDest'=>$rda,
						'HoraDest'=>$rta,
						'FechaDes'=>$ede,
						'HoraDes'=>$ete,
						'FechaDes'=>$ete,
						'FechaExtDes'=>$rde,
						'HoraExtDes'=>$rte,
						'recibio'=>$receiver,
						'viaje'=>$row['status']
						//$bills_status,
						//$bills_stamp,
						//$bills_payment
					)
				);
			}
			//echo json_encode($json_array);
			echo json_encode($json_array, JSON_UNESCAPED_UNICODE);
		}
		
	}else if($custprov == 1){

	}else if($user_type == 5){
		$sql = "SELECT id_driver FROM drivers2 WHERE id_user = $id_user";
		$result = mysqli_query($conn, $sql) or die (mysqli_error($conn));
		if ($result->num_rows>0) {
			while($row = mysqli_fetch_array($result)){
				$id_driver = $row['id_driver'];
			}
		}else{
			$response = "NO_user_type";
			echo json_encode($response);
			return 0;
		}
		$sql = "SELECT transportoperation_details.* FROM transportoperation_details INNER JOIN transport_operations ON transportoperation_details.id_operation = transport_operations.id_transport_operation WHERE transportoperation_details.id_driver = $id_driver AND transport_operations.status > 0 AND transport_operations.status < 8 AND transport_operations.status != '2' ORDER BY date DESC LIMIT 1";
		$result = mysqli_query($conn, $sql) or die (mysqli_error($conn));
		if ($result->num_rows>0) {
			while($row = mysqli_fetch_array($result)){
				$id_driver = $row['id_driver'];
				$id_operation = $row['id_operation'];
				$id_location = $row['id_destination'];
				$id_company = $row['id_company'];
				$sqlIn = "SELECT status FROM transport_operations WHERE id_transport_operation = $id_operation";
				//$status = searchSQL($sqlIn, $conn, 'status');
				$resultIn = mysqli_query($conn, $sqlIn) or die(mysqli_error($conn));
				if ($resultIn->num_rows>0) {
					while($rowIn = mysqli_fetch_array($resultIn)){
						$status = $rowIn['status'];
					}
				}
				$sqlIn = "SELECT * FROM locations WHERE id_locations = '$id_location'";
				$resultIn = mysqli_query($conn, $sqlIn) or die (mysqli_error($conn));
				if ($resultIn->num_rows>0) {
					while($rowIn = mysqli_fetch_array($resultIn)){
						$street = json_encode($rowIn['street']);
						$suburb = json_encode($rowIn['suburb']);
						$num = json_encode($rowIn['num']);
						$description = json_encode($rowIn['description']);
						$city = $rowIn['id_city'];
						$state = $rowIn['id_state'];
						$country = $rowIn['id_country'];
						$id_operation_type = $rowIn['id_type'];
					}
				}
				$sqlIn = "SELECT business_name FROM companies WHERE id_companies = $id_company";
				$empresa = searchSQL($sqlIn, $conn, 'business_name');
				$sqlIn = "SELECT name FROM operation_types WHERE id_operation_types = '$id_operation_type'";
				$tipo = searchSQL($sqlIn, $conn, 'name');
				$sqlIn = "SELECT name FROM countries WHERE id_country = '$country'";
				$pais = searchSQL($sqlIn, $conn, 'name');
				$sqlIn = "SELECT name FROM states WHERE id_states = '$state'";
				$estado = searchSQL($sqlIn, $conn, 'name');
				$sqlIn = "SELECT name FROM city WHERE id_city = '$city'";
				$ciudad = searchSQL($sqlIn, $conn, 'name');
				if ($colorChange != true) {
					$colorTable = "#3399cc";
					$colorChange = true;
				}else{
					$colorTable = "#66ccff";
					$colorChange = false;
				}


				$sqlcomment = "select * from comment where id_operation='$id_operation' and status ='1' and mostrar='1'";
				$resultComment = mysqli_query($conn, $sqlcomment) or die(mysqli_error($conn));
				$comments="";
				while ($row2 = mysqli_fetch_array($resultComment)){
					$id_user_comment = $row2['id_user'];
					$id_driver_comment = $row2['id_driver'];
					$id_customer_comment = $row2['id_customer'];
					$text_comment = $row2['comment'];
					$date_comment = $row2['date'];

					//Nombre de quien realizó el comentario
					$sql2 = "select * from users where id_users = '$id_user_comment'";
					$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
					while ($row3 = mysqli_fetch_array($result2)) {
						$name_comment = $row3['names'];
						$last_name_comment = $row3['last_name'];
					}

					//Nombre de la compañía del cliente
					$sql2 = "select * from companies where id_companies = '$id_customer_comment' ";
					$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
					while ($row3 = mysqli_fetch_array($result2)) {
						$name_customer_comment = $row3['business_name'];
					}

					//Nombre del conductor
					$sql2 = "select * from users where id_users = '$id_driver_comment' ";
					$result2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
					while ($row3 = mysqli_fetch_array($result2)) {
						$name_driver_comment = $row3['names'];
						$last_name_driver_comment = $row3['last_name'];
					}
					$comments="$comments\n\nEscrito por: $name_comment $last_name_comment\nComentario: $text_comment\nFecha: $date_comment\nCliente: $name_customer_comment\nConductor: $name_driver_comment $last_name_driver_comment";

				}

				$sqlUrl = "select * from transportoperation_details where id_operation ='$id_operation'";
				$resultUrl = mysqli_query($conn, $sqlUrl) or die(mysqli_error($conn));
				if ($resultUrl->num_rows>0){
					while ($row = mysqli_fetch_array($resultUrl)){
						$id_destination = $row['id_destination'];
					}
				}
				$sqlUrl= "select * from locations where id_locations = '$id_destination'";
				$resultUrl = mysqli_query($conn, $sqlUrl) or die(mysqli_error($conn));
				if ($resultUrl->num_rows>0){
					while ($row = mysqli_fetch_array($resultUrl)){
						$map = $row['url_maps'];
					}
				}
				
				$sqlCliente= "select id_relationship from transport_operations where id_transport_operation = '$id_operation'";
				$resultCliente = mysqli_query($conn, $sqlCliente) or die(mysqli_error($conn));
				if ($resultCliente->num_rows>0){
					while ($row = mysqli_fetch_array($resultCliente)){
						$id_relationship = $row['id_relationship'];
					}
				}
				
				$sqlCliente= "select id_relationship from transport_operations where id_transport_operation = '$id_operation'";
				$resultCliente = mysqli_query($conn, $sqlCliente) or die(mysqli_error($conn));
				if ($resultCliente->num_rows>0){
					while ($row = mysqli_fetch_array($resultCliente)){
						$id_relationship = $row['id_relationship'];
					}
				}
				$sqlCliente= "select id_company1 from relationship where id_relationship='$id_relationship' ";
				$resultCliente = mysqli_query($conn, $sqlCliente) or die(mysqli_error($conn));
				if ($resultCliente->num_rows>0){
					while ($row = mysqli_fetch_array($resultCliente)){
						$company_find = $row['id_company1'];
					}
				}
				$sqlCliente= "select business_name from companies where id_companies='$company_find'";
				$resultCliente = mysqli_query($conn, $sqlCliente) or die(mysqli_error($conn));
				if ($resultCliente->num_rows>0){
					while ($row = mysqli_fetch_array($resultCliente)){
						$Cliente = $row['business_name'];
					}
				}


				array_push($json_array,
					array(
						'user_type'=>"2",
						'date'=>json_encode($row['date']),
						'empresa'=>$Cliente,
						'tipo'=>$tipo,
						'id'=>$id_operation,
						'status'=>$status,
						'street'=>$street,
						'suburb'=>$suburb,
						'num'=>$num,
						'description'=>$description,
						'pais'=>$pais,
						'estado'=>$estado,
						'ciudad'=>$ciudad,
						'fechaOrgPro'=>$row['eda'],
						'horaOrgPro'=>$row['eta'],
						'fechaDestPro'=>$row['edd'],
						'horaDestPro'=>$row['edt'],
						'FechaOrg'=>$row['rrd'],
						'HoraOrg'=>$row['rrt'],
						'FechaRec'=>$row['erd'],
						'HoraRec'=>$row['ert'],
						'FechaExtOrg'=>$row['rdd'],
						'HoraExtOrg'=>$row['rtd'],
						'FechaDest'=>$row['rda'],
						'HoraDest'=>$row['rta'],
						'FechaDes'=>$row['ede'],
						'HoraDes'=>$row['ete'],
						'FechaExtDes'=>$row['rde'],
						'HoraExtDes'=>$row['rte'],
						'color'=>$colorTable,
						'recibio'=>$row['receiver'],
						'viaje'=>$status,
						'comments'=>$comments,
						'url'=>$map
					)
				);
			}
			echo json_encode($json_array);
		}else{
			$sql = "SELECT transportoperation_details.* FROM transportoperation_details INNER JOIN transport_operations ON transportoperation_details.id_operation = transport_operations.id_transport_operation WHERE transportoperation_details.id_driver = $id_driver  ORDER BY date DESC LIMIT 20";
			$result = mysqli_query($conn, $sql) or die (mysqli_error($conn));
			if ($result->num_rows>0) {
				while($row = mysqli_fetch_array($result)){
					$id_driver = $row['id_driver'];
					$id_operation = $row['id_operation'];
					$id_location = $row['id_destination'];
					$id_company = $row['id_company'];
					$sqlIn = "SELECT status FROM transport_operations WHERE id_transport_operation = $id_operation";
					//$status = searchSQL($sqlIn, $conn, 'status');
					$resultIn = mysqli_query($conn, $sqlIn) or die(mysqli_error($conn));
					if ($resultIn->num_rows>0) {
						while($rowIn = mysqli_fetch_array($resultIn)){
							$status = $rowIn['status'];
						}
					}
					$sqlIn = "SELECT * FROM locations WHERE id_locations = '$id_location'";
					$resultIn = mysqli_query($conn, $sqlIn) or die (mysqli_error($conn));
					if ($resultIn->num_rows>0) {
						while($rowIn = mysqli_fetch_array($resultIn)){
							$street = json_encode($rowIn['street']);
							$suburb = json_encode($rowIn['suburb']);
							$num = json_encode($rowIn['num']);
							$description = json_encode($rowIn['description']);
							$city = $rowIn['id_city'];
							$state = $rowIn['id_state'];
							$country = $rowIn['id_country'];
							$id_operation_type = $rowIn['id_type'];
						}
					}
					$sqlIn = "SELECT business_name FROM companies WHERE id_companies = $id_company";
					$empresa = searchSQL($sqlIn, $conn, 'business_name');
					$sqlIn = "SELECT name FROM operation_types WHERE id_operation_types = '$id_operation_type'";
					$tipo = searchSQL($sqlIn, $conn, 'name');
					$sqlIn = "SELECT name FROM countries WHERE id_country = '$country'";
					$pais = searchSQL($sqlIn, $conn, 'name');
					$sqlIn = "SELECT name FROM states WHERE id_states = '$state'";
					$estado = searchSQL($sqlIn, $conn, 'name');
					$sqlIn = "SELECT name FROM city WHERE id_city = '$city'";
					$ciudad = searchSQL($sqlIn, $conn, 'name');
					if ($colorChange != true) {
						$colorTable = "#3399cc";
						$colorChange = true;
					}else{
						$colorTable = "#66ccff";
						$colorChange = false;
					}
					array_push($json_array,
						array(
							'user_type'=>"2",
							'date'=>json_encode($row['date']),
							'empresa'=>$empresa,
							'tipo'=>$tipo,
							'id'=>$id_operation,
							'status'=>$status,
							'street'=>$street,
							'suburb'=>$suburb,
							'num'=>$num,
							'description'=>$description,
							'pais'=>$pais,
							'estado'=>$estado,
							'ciudad'=>$ciudad,
							'fechaOrgPro'=>$row['eda'],
							'horaOrgPro'=>$row['eta'],
							'fechaDestPro'=>$row['edd'],
							'horaDestPro'=>$row['edt'],
							'FechaOrg'=>$row['rrd'],
							'HoraOrg'=>$row['rrt'],
							'FechaRec'=>$row['erd'],
							'HoraRec'=>$row['ert'],
							'FechaExtOrg'=>$row['rdd'],
							'HoraExtOrg'=>$row['rtd'],
							'FechaDest'=>$row['rda'],
							'HoraDest'=>$row['rta'],
							'FechaDes'=>$row['ede'],
							'HoraDes'=>$row['ete'],
							'FechaExtDes'=>$row['rde'],
							'HoraExtDes'=>$row['rte'],
							'color'=>$colorTable,
							'recibio'=>$row['receiver'],
							'viaje'=>$status
						)
					);
				}
				//echo json_encode($json_array);
				echo json_encode($json_array, JSON_UNESCAPED_UNICODE);
			}

		}
	}else if($response == true){
		$response = "NO_user_type";
		//echo json_encode($response);
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}
	
	//echo "<pre>";
	//	echo print_r($json_array);
	//echo "</pre>";

	mysqli_close($conn);
 ?>