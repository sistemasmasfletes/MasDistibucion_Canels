<?php 
//session_start();
require("data.php");

$acc = @$_REQUEST['option']; //Variable que traera las acciones
if (isset($acc)) {
    $db = new Db();
    $arrJson = array();
    switch ($acc) {
	
		case 'operation_comments':
        		$rows = $db->operation_comments($_REQUEST["idop"]);

				foreach($rows as $item){
        			$arrJson[] = array(
        					"comment" => $item['comment'],
        					"names" => $item['names'],
							"user_type" => $item['name']
					);
				}
		
		break;
	
        case 'provider_operations':
        		$rows = $db->provider_operations($_REQUEST["iduser"]);
        		
        		foreach($rows as $item){
					$date = strtotime($item['operation_date']); 
					$date = date('Y-m-d', $date); 

					$llegadacargar = $item['rrd']	." a las ".$item['rrt'];
					$ingresoplanta =  $item['erd']." a las ".$item['ert'];
					$salidadecarga = $item['rdd']." a las ".$item['rtd'];
					$llegadadestino = $item['rda']." a las ".$item['rta'];
					$ingresodescarga =  $item['ede']." a las  ".$item['ete'];
					$salidadescarga = $item['rde']." a las ".$item['rte'];

					switch($item['status']){
						case "0":$status = "Cancelado";break;
						case "2":$status = "Terminado";break;
						default:$status = "En Proceso";
					}
					
        			$arrJson[] = array(
        					"idoperation" => $item['id_transport_operation'],
        					"dateop" => $date,
        					"documentation" => $item['documentation'],
        					"comments" => $item['comments'],
							"customer" => $item['business_name'],
							"folioletter" => $item['folio'],
							"status" => $status,
							"description" => $item['description'],
							"llegadacarga" => $llegadacargar,
							"ingresoplanta" => $ingresoplanta,
							"salidadecarga" => $salidadecarga,
							"llegadadestino" => $llegadadestino,
							"ingresodescarga" => $ingresodescarga,
							"salidadescarga" => $salidadescarga
        			);
				}
        break;

		case 'customer_operations':
        		$rows = $db->customer_operations($_REQUEST["idcompany"]);
        		
        		foreach($rows as $item){
					$date = strtotime($item['operation_date']); 
					$date = date('Y-m-d', $date); 

					$llegadacargar = $item['rrd']	." a las ".$item['rrt'];
					$ingresoplanta =  $item['erd']." a las ".$item['ert'];
					$salidadecarga = $item['rdd']." a las ".$item['rtd'];
					$llegadadestino = $item['rda']." a las ".$item['rta'];
					$ingresodescarga =  $item['ede']." a las  ".$item['ete'];
					$salidadescarga = $item['rde']." a las ".$item['rte'];

					switch($item['status']){
						case "0":$status = "Cancelado";break;
						case "2":$status = "Terminado";break;
						default:$status = "En Proceso";
					}
					
        			$arrJson[] = array(
        					"idoperation" => $item['id_transport_operation'],
        					"dateop" => $date,
        					"documentation" => $item['documentation'],
        					"comments" => $item['comments'],
							"provider" => $item['business_name'],
							"folioletter" => $item['folio'],
							"status" => $status,
							"description" => $item['description'],
							"llegadacarga" => $llegadacargar,
							"ingresoplanta" => $ingresoplanta,
							"salidadecarga" => $salidadecarga,
							"llegadadestino" => $llegadadestino,
							"ingresodescarga" => $ingresodescarga,
							"salidadescarga" => $salidadescarga
        			);
				}
        break;
        
        case 'driver_operations':
		
				$rows = $db->driver_operations($_REQUEST["iduser"],TRUE);
        		
				if(count($rows) == 0 || count($rows) > 1 ){
					$rows = $db->driver_operations($_REQUEST["iduser"],FALSE);
				}
				
        		foreach($rows as $item){
					$date = strtotime($item['operation_date']); 
					$date = date('Y-m-d h:i', $date); 
				
					$llegadacargar = $item['rrd']	." a las ".$item['rrt'];
					$ingresoplanta =  $item['erd']." a las ".$item['ert'];
					$salidadecarga = $item['rdd']." a las ".$item['rtd'];
					$llegadadestino = $item['rda']." a las ".$item['rta'];
					$ingresodescarga =  $item['ede']." a las  ".$item['ete'];
					$salidadescarga = $item['rde']." a las ".$item['rte'];

        			$arrJson[] = array(
        					"idoperation" => $item['id_transport_operation'],
        					"dateop" => $date,
							"status" => $item['status'],
							"description" => $item['description'],
							"llegadacarga" => $llegadacargar,
							"ingresoplanta" => $ingresoplanta,
							"salidadecarga" => $salidadecarga,
							"llegadadestino" => $llegadadestino,
							"ingresodescarga" => $ingresodescarga,
							"salidadescarga" => $salidadescarga
        			);
        		}
        break;

        case 'operation_location':
        	$rows = $db->operation_location($_REQUEST["idop"]);
        
        	foreach($rows as $item){
        		$arrJson[] = array(
        				"ordescription" => $item['ordescription'],
        				"orgurl" => ($item['orgurl'] != "")?$item['orgurl']:"false",
        				"d1" => ($item['d1'] != "")?$item['d1']:"false",
        				"destdescription" => ($item['destdescription'] != "")?$item['destdescription']:"false",
        				"desturl" => ($item['desturl'] != "")?$item['desturl']:"false",
        				"d2" => ($item['d2'] != "")?$item['d2']:"false"
        		);
        	}
        break;
        
        case 'login':
        	
        	$rows = $db->login($_REQUEST["user"],$_REQUEST["password"]);

        	$success = (count($rows)>0)?TRUE:FALSE;
        	
        	if($success){
				foreach($rows as $item){
        			$tokenup = $db->tokenup($_REQUEST["token"],$item["id"]);
					$arrJson[] = array(
							"id" => $item["id"],
							"username" => $item["username"],
							"password" => $item["password"],
							"success" => $success
					);
				}
			}else{
					$arrJson[] = array("success" => $success);
			}
        	break;
    }
    
    echo json_encode($arrJson);
        
    
}