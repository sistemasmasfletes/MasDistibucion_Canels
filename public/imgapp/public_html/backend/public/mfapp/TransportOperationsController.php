<?php

/**
 *
 * @author Emmanuel Armando Arroyo López
 * Fecha: 29 de Julio del 2015
 */
class Coordinator_TransportOperationsController extends Model3_Controller
{
	private $_credentials;
	// protected $_config;

	public function init(){
		if(!Model3_Auth::isAuth())
			$this->redirect();
		else{
			$role = Model3_Auth::getCredentials('user_type');
			if( $role !== DefaultDb_Entity_User::TYPE_COORDINATOR ){
				Model3_Auth::deleteCredentials();
				$this->redirect();
			}
		}
		$this->_credentials = Model3_Auth::getCredentials();
		$em = $this->getEntityManager('DefaultDb');
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//USUARIO LOGEADO
		$this->view->setTemplate('Coordinator');
		switch ($user->getCompany()){
			case 1:
				$this->view->intermensaje = "MENSAJE PARA LOS COORDINADORES DE LAS EMPRESAS MASFLETES Y ELLIOT";
				break;
			case 163:
				$this->view->intermensaje = "MENSAJE PARA LOS COORDINADORES DE LAS EMPRESAS MASFLETES Y ELLIOT";
				break;
			default:
				$this->view->intermensaje ="";
		}
	}

	public function indexAction(){
	}
    
    public function sendMailOperationsAction(){            
	}

/*	public function getListTransportOpAction(){//FUNCION PARA OBTENER EL LISTADO DE LAS OPERACIONES REGISTRADAS EN LA BASE DE DATOS
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
		$em = $this->getEntityManager('DefaultDb');

		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//USUARIO LOGEADO
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
		
		$relationship = $em->getRepository('DefaultDb_Entity_Relationship')->findBy(array('type' => 1, 'id_company2' => $company->getId()));//CONSULTA A LA TABLA relationship PARA OBTENER A LOS CLIENTES DE LA EMPRESA A LA QUE PERTENECE EL USUARIO LOGEADO
		
		$arrJson = array();
		$idshipments = array();
		
		foreach($relationship as $idrelation){
			$shipments= $em->getRepository('DefaultDb_Entity_Article')->findBy(array('id_relationship' => $idrelation->getId()));//CONSULTA A LA TABLA shipment 
			foreach($shipments as $idshipment){
				$idshipments[] = array(
						"id_shipment" => $idshipment->getId(),
						);
			}
		}
		
		foreach($idshipments as $item){
			
			$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findBy(array('shipment' => $item["id_shipment"], 'user' => $user->getId()), array('operationDate' => 'ASC')/*,array('limit' => 10)*///);
			//$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findBy(array('id' => 3405), array('operationDate' => 'ASC'),array('limit' => 1));
				
	/*		foreach($transportop as $item1){
				$l= $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item1->getRelationship()));//USUARIO LOGEADO
				$companycust= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $l->getCompany1()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
				$note = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $item1->getNote()));
				$notef = ($note != null) ? $note->getFolio() : "";
				$loadorder = $em->getRepository('DefaultDb_Entity_LoadOrder')->findOneBy(array('id_operation' => $item1->getId()));
				$loadorderf = ($loadorder != null) ? $loadorder->getFolio() : "";
				$shipment = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $item1->getShipment()));
				$type = $em->getRepository('DefaultDb_Entity_OperationType')->findOneBy(array('id' => $item1->getType()));
				$detailsProvider = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $item1->getId()));
                $relationshipcomp = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item1->getRelationship()));
				if ($detailsProvider != NULL) {
                	$provider= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $detailsProvider->getProvider()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
                    $providerName = $provider->getBusinessName();
                    $providerId = $provider->getId();
             	}else{  
                	$providerName = ""; 
                    $providerId = 0;
              	}
                                        
                if($note != null){
                	$zip = $em->getRepository('DefaultDb_Entity_Bill')->findOneBy(array('id' => $note->getBill()));
                    $zipPath = ($zip != null) ? $zip->getZip() : "";
                    $pay = ($zip != null) ? $zip->getPayment() : "";
              	}else{$zipPath = "";}                                        
                                        
				if($item1->getFiscal()==1){$fiscal="Si";}
				else{$fiscal="No";}

				if($item1->getReport()==1){$report="Si";}
				else{$report="No";}

				switch ($item1->getStatus()){
                                    case '0':$status="Cancelado";break;
                                    case '1':$status="Solicitado";break;
                                    case '2':$status="Terminado";break;
                                    case '3':$status="Transito-Carga";break;
                                    case '4':$status="Vigilacia-O";break;
                                    case '5':$status="Cargando";break;
                                    case '6':$status="Transito-Entrega";break;
                                    case '7':$status="Vigilacina-D";break;
                                    case '8':$status="Descargando";break;
                                    case '9':$status="Facturacion";break;
                                    case '10':$status="Por Cobrar";break;
                                    case '11':$status="Pagado";break;                
				}						
                                
				$arrJson[] = array(
					"idTransportOp" => $item1->getId(),
					"opdate" => $item1->getOperationDate(),
					"applicant" => $item1->getApplicant(),
					"comments" => $item1->getComments(),
					"description" => $item1->getDescription(),
					"documentation" => $item1->getDocumentation(),
					"price" => $item1->getPrice(),
					"quantity" => $item1->getQuantity(),
					"total" => $item1->getTotal(),
					"fiscal" => $fiscal,
					"report" => $report,
					"status" => $status,
					"regdate" => $item1->getDate(),
					"idshipment" => $item1->getShipment(),
					"shipment" => $shipment->getReference(),
					"user" => $item1->getUser(),
					"company" => $item1->getCompany(),
					"idcustomer" => $l->getCompany1(),
					"companycust" => $companycust->getBusinessName()."(".$relationshipcomp->getComments().")",
					//"companycust" => $companycust->getBusinessName(),
                	"idprovider" => $providerId,
					"provider" => $providerName,
					"idnote" => $item1->getNote(),
					"note" => $notef,
					"idtype" => $item1->getType(),
					"type" => $type->getName(),
					"idrelationship" => $item1->getRelationship(),
                    "idorder" => ($loadorder != NULL)? $loadorder->getId() : 0,
					"order" => $loadorderf,
					"iva" => $item1->getIva(),
					"retention" => $item1->getRetention(),
					"zip" => $zipPath,
					"chat" => $item1->getChat()
					);
				}
		}
		rsort($arrJson);
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ 
	}*/

	public function getListTransportOpAction(){//FUNCION PARA OBTENER EL LISTADO DE LAS OPERACIONES REGISTRADAS EN LA BASE DE DATOS
			
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$em = $this->getEntityManager('DefaultDb');
	
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//USUARIO LOGEADO
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
	
		$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->getTrOperations($company->getId(),$user->getId());
		foreach($transportop as $item1){

			$loadorder = $em->getRepository('DefaultDb_Entity_LoadOrder')->findOneBy(array('id_operation' => $item1['id']));
			$loadorderf = ($loadorder != null) ? $loadorder->getFolio() : "";
			
			$detailsProvider = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $item1['id']));
			$relationshipcomp = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item1['idrelationship']));
			
			$pay = "";
			if($item1['id_note'] != null){
				$zip = $em->getRepository('DefaultDb_Entity_Bill')->findOneBy(array('id' => $item1['idbill']));
				$zipPath = ($zip != null) ? $zip->getZip() : "";
				$pay = ($zip != null) ? $zip->getPayment() : "";
			}else{$zipPath = "";}
	
			$dir ='images/imagesop/operation_'.$item1['id'].'/';
			if(file_exists($dir)){$files = true;}else{$files = false;}
					
			if ($detailsProvider != NULL) {
				$provider= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $detailsProvider->getProvider()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
				$providerName = $provider->getBusinessName();
				$providerId = $provider->getId();
			}
			else {
				$providerName = "";
				$providerId = 0;
			}
			
			if($item1['fiscal']==1){$fiscal="Si";}
			else{$fiscal="No";}
	
			if($item1['report']==1){$report="Si";}
			else{$report="No";}
	
			switch ($item1['status']){
                            case '0':$status="Cancelado";break;
                            case '1':$status="Solicitado";break;
                            case '2':$status="Terminado";break;
                            case '3':$status="Transito-Carga";break;
                            case '4':$status="Vigilacia-O";break;
                            case '5':$status="Cargando";break;
                            case '6':$status="Transito-Entrega";break;
                            case '7':$status="Vigilacina-D";break;
                            case '8':$status="Descargando";break;
                            case '9':$status="Facturacion";break;
                            case '10':$status="Por Cobrar";break;
                            case '11':$status="Pagado";break;                
                        }
                        
                        $arrJson[] = array(
							
						"idTransportOp" => $item1['id'],
						"opdate" => $item1['opdate'],
						"applicant" => $item1['applicant'],
						"comments" => $item1['comments'],
						"description" => $item1['description'],
						"documentation" => $item1['documentation'],
						"price" => $item1['price'],
						"quantity" => $item1['quantity'],
						"total" => $item1['total'],
						"fiscal" => $fiscal,
						"report" => $report,
						"status" => $status,
						"regdate" => $item1['regdate'],
						"idshipment" => $item1['idshipment'],
						"shipment" => $item1['shipment'],
						"user" => $item1['user'],
						"coordinator" => $item1['coordinator'],
						"company" => $item1['company'],
						"idcustomer" => $item1['idcustomer'],
						"companycust" => $item1['companycust']."(".$relationshipcomp->getComments().")",
						//"companycust" => $item1['companycust'],
						"idprovider" => $providerId,
						"provider" => $providerName,
						"idnote" => $item1['id_note'],
						"note" => $item1['folio'],
						"idtype" => $item1['idtype'],
						"type" => $item1['type'],
						"idrelationship" => $item1['idrelationship'],
                                                "idorder" => ($loadorder != NULL)? $loadorder->getId() : 0,
						"order" => $loadorderf,
						"iva" => $item1['iva'],
						"retention" => $item1['retention'],
						"zip" => $zipPath,
						"pay" => $pay,
						"files" => $files,
						"chat" => $item1['chat']
				);
			}

			/*$dateini = strtotime ( '-1 year' , strtotime(date('Y-m-j')));
			$dateini = date('Y-m-j', $dateini);
				var_dump($dateini);*/
                //var_dump($arrJson);
		rsort($arrJson);
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	

        
        public function saveTransportOpAction(){//FUNCION QUE PERMITE GUARDAR LOS DATOS DE UN NUEVO REGISTRO
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
		$post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
	
		$em = $this->getEntityManager('DefaultDb');
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
						
		$item = new DefaultDb_Entity_TransportOperation();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Operation
		
		$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
		try{
			$mon = substr($post['opdate'], 4 , 3);
			switch ($mon){
                            case "Jan":$month = "01";break;
                            case "Feb":$month = "02";break;
                            case "Mar":$month = "03";break;
                            case "Apr":$month = "04";break;
                            case "May":$month = "05";break;
                            case "Jun":$month = "06";break;
                            case "Jul":$month = "07";break;
                            case "Aug":$month = "08";break;
                            case "Sep":$month = "09";break;
                            case "Oct":$month = "10";break;
                            case "Nov":$month = "11";break;
                            case "Dec":$month = "12";break;
                        }
			
			$ldate = substr($post['opdate'], 11 , 4). "-" .$month. "-" .substr($post['opdate'], 8 , 2);
						
			$item->setOperationDate($ldate);
			$item->setApplicant($post['applicant']);
			$item->setComments($post['comments']);
			$item->setDescription($post['description']);
			$item->setDocumentation($post['documentation']);
			$item->setPrice($post['price']);
			$item->setQuantity($post['quantity']);
			$item->setTotal($post['price'] * $post['quantity']);
			$item->setFiscal(0);
			$item->setReport(0);
			//$item->setStatus($post['status']);
			$item->setStatus('1');
			$item->setDate(date('Y-m-d H:m:s'));
			$item->setUser($user->getId());
			$item->setCompany($company->getId());
			$item->setShipment($post['shipment']);
			$item->setRelationship($post['companycust']);
			$item->setType($post['type']);
			$item->setIva($post['iva']);
			$item->setRetention($post['retention']);
			$item->setChat($post['chat']);

			$em->persist($item);
			$em->flush();

			$l= $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item->getRelationship()));//USUARIO LOGEADO
			$companycust= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $l->getCompany1()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
			$note = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $item->getNote()));
			$notef = ($note != null) ? $note->getFolio() : "";
			$shipment = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $item->getShipment()));
			$type = $em->getRepository('DefaultDb_Entity_OperationType')->findOneBy(array('id' => $item->getType()));
			$relationshipcomp = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item->getRelationship()));
				
			if($item->getFiscal()==1){$fiscal="Si";}
			else{$fiscal="No";}
			
			if($item->getReport()==1){$report="Si";}
			else{$report="No";}
				
			switch ($item->getStatus()){
                            case '0':$status="Cancelado";break;
                            case '1':$status="Solicitado";break;
                            case '2':$status="Terminado";break;
                            case '3':$status="Transito-Carga";break;
                            case '4':$status="Vigilacia-O";break;
                            case '5':$status="Cargando";break;
                            case '6':$status="Transito-Entrega";break;
                            case '7':$status="Vigilacina-D";break;
                            case '8':$status="Descargando";break;
                            case '9':$status="Facturacion";break;
                            case '10':$status="Por Cobrar";break;
                            case '11':$status="Pagado";break;                
                        }
			/*if($item->getStatus()==1){$status="Activo";}
			else{$status="Cancelado";}*/
			
			$arrJson[] = array(
					"idTransportOp" => $item->getId(),
					"opdate" => $item->getOperationDate(),
					"applicant" => $item->getApplicant(),
					"comments" => $item->getComments(),
					"description" => $item->getDescription(),
					"documentation" => $item->getDocumentation(),
					"price" => $item->getPrice(),
					"quantity" => $item->getQuantity(),
					"total" => $item->getTotal(),
					"fiscal" => $fiscal,
					"report" => $report,
					"status" => $status,
					"regdate" => $item->getDate(),
					"idshipment" => $item->getShipment(),
					"shipment" => $shipment->getReference(),
					"user" => $item->getUser(),
					"company" => $item->getCompany(),
					"idcustomer" => $l->getCompany1(),
					"companycust" => $companycust->getBusinessName()."(".$relationshipcomp->getComments().")",
					//"companycust" => $companycust->getBusinessName(),
					"idnote" => $item->getNote(),
					"note" => $notef,
					"idtype" => $item->getType(),
					"type" => $type->getName(),
					"idrelationship" => $item->getRelationship(),
					"iva" => $item->getIva(),
					"retention" => $item->getRetention(),
					"chat" => $item->getChat()
						
			);			
			
			$em->getConnection()->commit();
		}
		catch(Exception $e)//EXCEPCIÓN PARA CUANDO HAY ALGUN ERROR AL GUARDAR LA NUEVA INFORMCIÓN
		{
			$em->getConnection()->rollback();
			$em->close();
			echo "errorsote";
			$arrJson[] = array("result" => $e->getMessage());
		}
		
		//$arrJson[] = array("result" => substr($company->getRfc(),0,3).date('Y').$item2->getId());
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
		
	}
	
	public function formEditAction()
	{
		$this->view->setUseTemplate(FALSE);
		
		$post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
			
		$em = $this->getEntityManager('DefaultDb');
		
    	$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
    	$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (PROVEEDOR)

    	$clientes = $em->getRepository('DefaultDb_Entity_Relationship')->findBy(array('type' => 1, 'id_company2' => $company->getId()));
    	$combo = "";
		foreach($clientes as $item)
		{
    		$customer= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $item->getCompany1()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (PROVEEDOR)
			
    		$combo .= "<option value='".$item->getId()."'";
			if($item->getCompany1()==$post['idcustomer']){$combo.= " selected";}
			$combo .=">".$customer->getBusinessName()."</option> \n";
		}

		$articulos = $em->getRepository('DefaultDb_Entity_Article')->findBy(array('id_company_customer' => $post['idcustomer'], 'id_company_provider' => $company->getId()));
		$combo1 = "";
		foreach($articulos as $item)
		{
			$combo1 .= "<option value='".$item->getId()."'";
			if($item->getId()==$post['idshipment']){$combo1.= " selected";}
			$combo1 .=">".$item->getReference()."</option> \n";
		}
		
		$tiposop = $em->getRepository('DefaultDb_Entity_OperationType')->findAll();
		$combo2 = "";
		foreach($tiposop as $item)
		{
			$combo2 .= "<option value='".$item->getId()."'";
			if($item->getId()==$post['idtype']){$combo2.= " selected";}
			$combo2 .=">".$item->getName()."</option> \n";
		}
		$mon = substr($post['opdate'], 4 , 3);
		
		switch ($mon){
                    case "Jan":$month = "01";break;
                    case "Feb":$month = "02";break;
                    case "Mar":$month = "03";break;
                    case "Apr":$month = "04";break;
                    case "May":$month = "05";break;
                    case "Jun":$month = "06";break;
                    case "Jul":$month = "07";break;
                    case "Aug":$month = "08";break;
                    case "Sep":$month = "09";break;
                    case "Oct":$month = "10";break;
                    case "Nov":$month = "11";break;
                    case "Dec":$month = "12";break;
                }
			
		$ldate = substr($post['opdate'], 11 , 4). "-" .$month. "-" .substr($post['opdate'], 8 , 2);
		
		$form = '<form id="formulario">
					<p><label><strong>Operaci&oacute;n:</strong></label><input type="text" id="" name="" value="'.$post['idOp'].'" />
					<label><strong>Fecha de la Operaci&oacute;n:</strong></label><input class="date" type="text" name="eda" id="eda" value="'.$ldate.'" style="width:20%;" />
					<label><strong>Cantidad:</strong></label><input type="number" class="num" min="1" step="1" id="" name="" value="'.$post['quantity'].'" /></p>
					<p><label><strong>Tipo de Operaci&oacute;n:</strong></label><select id="" name="">'.$combo2.'</select>
					<label><strong>Cliente:</strong></label><select id="" name="" onchange="getArticles(this.value)">'.$combo.'</select>
					<label><strong>Art&iacute;culo:</strong></label><select id="shipment" name="shipment">'.$combo1.'" </select></p>
					<p><label><strong>Comentarios:</strong></label><textarea id="comments" name="comments" style="width:50%;">'.$post['comments'].'</textarea></p>
					<p><label><strong>Descripci&oacute;n:</strong></label><textarea id="description" name="description" style="width:50%;">'.$post['description'].'</textarea></p>
					<p><label><strong>Documentaci&oacute;n:</strong></label><textarea id="documentation" name="documentation" style="width:50%;">'.$post['documentation'].'</textarea></p>
					<p><label><strong>Precio:</strong></label><span class="input-group-addon">$</span><input class="num" type="number" min="0" id="price" name="" value="'.$post['price'].'" />
					<label><strong>Total:</strong></label><span class="input-group-addon">$</span><input type="number" class="num" id="" name="" value="'.$post['total'].'" /></p>
					<p><label><strong>Solicitante:</strong></label><input type="text" id="" name="" value="'.$post['applicant'].'" /></p>
				</form>';
		
		echo $form; 
		
	}
	

	public function editTransportOpAction(){//FUNCION QUE PERMITE EDITAR INFORMACION ALMANCENADA EN LA TABLA operations
	
            $this->view->setUseTemplate(FALSE);
            header("Content-Type: application/json");
	
            $post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE EDICION
	
            $em= $this->getEntityManager('DefaultDb');
		 
            $item= ($post['idTransportOp']) ? $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' =>$post['idTransportOp'])) : new DefaultDb_Entity_TransportOperation();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD TransportOperation TOMANDO EL PARAMETRO ID DEL REGISTRO AL QUE SE EDITARAN LOS DATOS
	
            //primero se verifica el tipo de valor enviado
            //dependiendo del resultado se vuelve a verificar el valor o se asigna directamente el valor enviado en la varible 

            $idshipment = (is_numeric($post['shipment'])==true) ? $post['shipment']:$post['idshipment'];//SI ES VERDADERO EL REGISTRO SE MODIFICO Y SE TOMA EL NUEVO VALOR NUMERICO, SI ES FALSO SE TOMA EL VALOR NUMERICO QUE YA EXISTE 
            $idtype = (is_numeric($post['type'])==true) ? $post['type'] : $post['idtype'];
            if(is_numeric($post['status'])==true){
            	$statusval= $post['status'];
            }else{
		switch ($post['status']){
                    case 'Solicitado':$statusval=1;break;
                    case 'Terminado':$statusval=2;break;
                    case 'Cancelado':$statusval=0;break;
                    case 'Transito-Carga':$statusval=3;break;
                    case 'Vigilacia-O':$statusval=4;break;
                    case 'Cargando':$statusval=5;break;
                    case 'Transito-Entrega':$statusval=6;break;
                    case 'Vigilacina-D':$statusval=7;break;
                    case 'Descargando':$statusval=8;break;
                    case 'Facturacion':$statusval=9;break;
                    case 'Por Cobrar':$statusval=10;break;
                    case 'Pagado':$statusval=11;break;
                    case 'Cancelado':$statusval=0;break;
                }
            }		

            /****************** Verificación de los valores correspondientes a los combos con solo dos posibles valores enviados en la edición del registros ***********************/

            $mon = substr($post['opdate'], 4 , 3);
		
            switch ($mon){
                case "Jan":$month = "01";break;
                case "Feb":$month = "02";break;
                case "Mar":$month = "03";break;
                case "Apr":$month = "04";break;
                case "May":$month = "05";break;
                case "Jun":$month = "06";break;
                case "Jul":$month = "07";break;
                case "Aug":$month = "08";break;
                case "Sep":$month = "09";break;
                case "Oct":$month = "10";break;
                case "Nov":$month = "11";break;
                case "Dec":$month = "12";break;
            }
		 
            $ldate = substr($post['opdate'], 11 , 4). "-" .$month. "-" .substr($post['opdate'], 8 , 2);

            $item->setStatus($statusval);
            $item->setShipment($idshipment);
            $item->setType($idtype);
            $item->setOperationDate($ldate);
            $item->setApplicant($post['applicant']);
            $item->setDocumentation($post['documentation']);
            $item->setDescription($post['description']);
            $item->setComments($post['comments']);
            $item->setPrice($post['price']);
            $item->setQuantity($post['quantity']);
            $item->setTotal($post['price'] * $post['quantity']);
            $item->setRetention($post['retention']);
            $item->setIva($post['iva']);
            $item->setChat($post['chat']);
            if(is_numeric($post['companycust'])){//SI SE CUMPLE EL VALOR QUE CONTIENE ES EL ID DE LA RELATIONSHIP
                $item->setRelationship($post['companycust']);
            }
				
            $em->persist($item);
            $em->flush();

            $change = false;
            $l= $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item->getRelationship()));
            $companycust= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $l->getCompany1()));
            
            $note = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $item->getNote()));
            //$notef = ($note != null) ? $note->getFolio() : "";
            if($note != null){//SI SE CUMPLE ENTONCES LA OPERACION TIENE UNA CARTA PORTE
                $notef = $note->getFolio();
                if(is_numeric($post['companycust'])){//SI SE CUMPLE EL VALOR QUE CONTIENE ES EL ID DE LA RELATIONSHIP E INDICA UN CAMBIO DE CLIENTE
                    $note->setCustomer($l->getCompany1());//SE INDICA EL NUEVO CLIENTE CON LA REFERENCIA DEL RELATIONSHIP EN LA CARTA PORTE
                    $em->persist($note);
                    $em->flush();

                    $detls= $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $item->getId()));
                    if($detls != null){//SI SE CUMPLE ENTONCES SE ACTUALIZAN LOS DETALLES REFERENTES A LA OPERACION
                        $detls->setCustomer($l->getCompany1());
                        $detls->setSource(NULL);
                        $detls->setDestination(NULL);
                        $em->persist($detls);
                        $em->flush();
                    }
                    $change = true;
                }
            }else{
                $notef = "";
            }
            
            $shipment = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $item->getShipment()));
            $type = $em->getRepository('DefaultDb_Entity_OperationType')->findOneBy(array('id' => $item->getType()));
            //$relationshipcomp = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $item->getRelationship()));
			
            if($item->getFiscal()==1){$fiscal="Si";}
            else{$fiscal="No";}
			
            if($item->getReport()==1){$report="Si";}
            else{$report="No";}
			
            switch ($item->getStatus()){
		case '0':$status="Cancelado";break;
		case '1':$status="Solicitado";break;
                case '2':$status="Terminado";break;
                case '3':$status="Transito-Carga";break;
                case '4':$status="Vigilacia-O";break;
                case '5':$status="Cargando";break;
                case '6':$status="Transito-Entrega";break;
                case '7':$status="Vigilacina-D";break;
                case '8':$status="Descargando";break;
                case '9':$status="Facturacion";break;
                case '10':$status="Por Cobrar";break;
                case '11':$status="Pagado";break;                
            }
				
            $arrJson[] = array(
                "idTransportOp" => $item->getId(),
		"opdate" => $item->getOperationDate(),
		"applicant" => $item->getApplicant(),
		"comments" => $item->getComments(),
		"description" => $item->getDescription(),
		"documentation" => $item->getDocumentation(),
		"price" => $item->getPrice(),
		"quantity" => $item->getQuantity(),
		"total" => $item->getTotal(),
		"fiscal" => $fiscal,
		"report" => $report,
		"status" => $status,
		"regdate" => $item->getDate(),
		"idshipment" => $item->getShipment(),
		"shipment" => $shipment->getReference(),
		"user" => $item->getUser(),
		"company" => $item->getCompany(),
		"idcustomer" => $l->getCompany1(),
		"companycust" => $companycust->getBusinessName()."(".$l->getComments().")",
		//"companycust" => $companycust->getBusinessName(),
		"idnote" => $item->getNote(),
		"note" => $notef,
		"idtype" => $item->getType(),
		"type" => $type->getName(),
		"idrelationship" => $item->getRelationship(),
		"iva" => $item->getIva(),
		"retention" => $item->getRetention(),
		"chat" => $item->getChat(),
                "change" => $change
            );			
            $this->view->result=json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}
	
	public function delTransportOpAction()//FUNCION QUE PERMITE ELIMINAR UN REGISTRO DE LA TABLA operations
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
	
    	$arrJson[] = array(
    			"respuesta" => "borrado"
    	);
    	$this->view->result=json_encode($arrJson);    	
    	
	}

	function GridExpensesAction()
	{
		 
		$this->view->setUseTemplate(FALSE);
	
		$idOperacion = $this->getRequest()->getParam('idop');
		 
		$em = $this->getEntityManager('DefaultDb');
	
		$form = '
    	<section id="ventana" name="ventana" style="display:none">
				<h3>Operaci&oacute;n:' . $idOperacion . '</h3>
				<form name="formulario" id="GridExpenses" style="background-color:#fff; width:100%;">
				</form>
				<center><input name="CANCELAR" type="button" class="boton2" id="Button2" value="Cerrar" 
				onClick="close_details()" style="margin-top:40px"></center>				
		</section>
    	<section id="modal" class="modal" style="display:none">
		</section>
				';

		echo utf8_encode($form);
	
	}	
	
	/*********************************************FOMULARIO PARA DETALLES *******************************************************************/
	function form_detailsAction()
	{
		$this->view->setUseTemplate(FALSE);
	
		$idOperation = $this->getRequest()->getParam('idcom');
		 
		$em = $this->getEntityManager('DefaultDb');
	
		$item = $em->getRepository('DefaultDb_Entity_Operation')->findOneBy(array('id' => $idOperation));//CONSULTA A LA TABLA agent_commission

		$shipment= $em->getRepository('DefaultDb_Entity_Shipment')->findOneBy(array('id' => $item->getShipment()));//CONSULTA A LA TABLA shipment TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA operations
		$optype= $em->getRepository('DefaultDb_Entity_OperationType')->findOneBy(array('id' => $item->getOperationType()));//CONSULTA A LA TABLA shipment TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA operations
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $item->getUser()));//CONSULTA A LA TABLA users TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA operations
		$unit= $em->getRepository('DefaultDb_Entity_Unit')->findOneBy(array('id' => $item->getUnit()));//CONSULTA A LA TABLA units TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA operations
		$route= $em->getRepository('DefaultDb_Entity_Route')->findOneBy(array('id' => $item->getRoute()));//CONSULTA A LA TABLA routes TOMANDO COMO PARAMETRO EL ID DE LA RUTA ALMACENADA EN LA TABLA operations
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $item->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
			
		if($item->getAssured()==1){$shipassured="Si";}
		else{$shipassured="No";}
		
		if($item->getCustody()==1){$custody="Si";}
		else{$custody="No";}
			
		if($item->getStatus()==1){$status="Activo";}
		else{$status="Cancelado";}
		
		 $form = '
		<section id="ventana" style="display:none">
		<form id="formulario">
		<h3>Detalles de la Operaci&oacute;n ' . $item->getId() .'</h3>
		<!--img src="images/logo.jpg"-->
		<br/>';
				
		$form .= '<p>
		<label>Asegurado:</label> <input type="text" name="shipassured" id="shipassured" value="' . $shipassured .'" readonly="readonly" size="40">
		<label>Custodia:</label> <input type="text" name="custody" id="custody" value="' . $custody. '" readonly="readonly" size="40">
		<label>Calificaci&oacute;n del enviador:</label> <input type="text" name="sender_cal" id="sender_cal" value="' . $item->getSenderCalif() .'" readonly="readonly" size="40">
		 </p>';
				
		$form .= '<p>		
		<label>Operaci&oacute;n del cliente:</label> <input type="text" name="cust_op" id="cust_op" value="' . $item->getCustomerOperation(). '" readonly="readonly" size="40">
		<label>Operaci&oacute;n del proveedor:</label> <input type="text" name="prov_op" id="prov_op" value="' . $item->getProviderOperation() . '" readonly="readonly" size="40">
		<label>Unidad:</label> <input type="text" name="unit" id="unit" value="' . $unit->getModel() . '" readonly="readonly" size="40">
		</p>';
				
		$form .='<p><label>Comentarios:</label>
    			<textarea name="comments" id="comments" style="width:40%; height:40px; padding: .5em;" readonly="readonly">' . $item->getComments();
		$form .= '</textarea></p>
				<br />';		
	
		$form .='</form>		
		<br />';
	
		$form .='<input name="CANCELAR" type="button" class="boton2" id="Button2" value="Cerrar Detalles" onClick="close_details()" style="margin-top:1em;">
		<!--input name="" type="button" class="boton" id="Button" value="Tamaño" onClick="ventana_tam()" style="margin-top:1em;"-->
		</section>
		 
		<section id="modal" class="modal" style="display:none">
		</section>
		 
		';

		echo utf8_encode($form);
	
	}
	/******************************************FIN FOMULARIO PARA DETALLES *******************************************************************/

	function GridOperationDetailsAction()//FUNCION PARA GENERAR UNA VENTANA EMERGENTE DONDE SE CAPTUREN LOS DETALLES DE LA OPERACIÓN
	{
		$this->view->setUseTemplate(FALSE);
	
		$idOp = $this->getRequest()->getParam('idop');
	
		$form = '
    	<!--section id="ventana" name="ventana"-->
				<h3>Detalles sobre la Operaci&oacute;n : ' .$idOp. '</h3>
				<form name="formulario" id="GridOperationDetails" >
				</form>
				<center><button type="button" class="closeButton" id="close"
				onClick="close_window()" style="margin-top:40px">Cerrar</button></center>
		<!--/section>
    	<section id="modal" class="modal" style="display:none">
		</section-->
				';
	
		echo utf8_encode($form);
	}	

	public function getListOperationDetailsAction()//FUNCION QUE OBTIENE LA LISTA ENVIOS DE AGENTES ALMACENADA EN LA BASE DE DATOS
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$em = $this->getEntityManager('DefaultDb');
	
		switch ($this->getRequest()->isPost())
		{
			 
			case false:
				if($this->getRequest()->numParam() != 0)// SI SE CUMPLE QUIERE DECIR QUE LA FUNCIÓN SE CONVOCO DESDE UNA VENTANA EMERGENTE
				{
					$param = $this->getRequest()->getParam('param');
					$pos = strpos($param, ':');
					$idop = substr($param,0,$pos);
					$lista = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findBy(array('operation' => $idop ));//CONSULTA A LA TABLA 
				}
				else// EN ESTE CASO LA FUNCIÓN SE CONVOCO DESDE UN CATÁLOGO
				{
	
	
	
					$lista = $em->getRepository('DefaultDb_Entity_Shipment')->findAll();//CONSULTA A LA TABLA shipments
				}
	
				break;
				 
			case true://CASO EN QUE LA FUNCIÓN ES CONVOCADA PARA UN COMBO DEPENDIENTE
				 
				$post = $this->getRequest()->getPost();
				$idrelationship = $post["filter"]["filters"]["0"]["value"];
	
				$lista = $em->getRepository('DefaultDb_Entity_Shipment')->findBy(array('id_relationship' => $idrelationship));//CONSULTA A LA TABLA shipments
				 
				break;
				 
		}
		 
		$arrJson = array();//ARREGLO QUE SE USARA PARA ALMACENAR EL LISTADO OBTENIDO EN LA CONSULTA
		foreach($lista as $item)//CICLO QUE PERMITIRA PASAR LOS DATOS DEL LISTADO EN EL ARREGLO DECLARADO
		{
			$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $item->getUser()));//CONSULTA A LA TABLA users TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
			$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
			
			if($item->getAssured()==1){$shipassured="Si";}
			else{$shipassured="No";}
				
			if($item->getCustody()==1){$custody="Si";}
			else{$custody="No";}
						
			$arrJson[] = array(
					"idTransportOpdetail" => $item->getId(),
					"custody" => $custody,
					"assured" => $shipassured,
    				"idcompany" => $item->getCompany(),
    				"company" => $company->getBusinessName(),
					"operation" => $item->getOperation(),
					"regdate" => $item->getDate(),
					"user" => $item->getUser(),
					"company" => $item->getCompany()
			);
		}
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO CON LOS DATOS OBTENIDOS DE LA CONSULTA PARA SER USADOS POR KENDO
	}
	
	public function saveOperationsDetailsAction()
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
		
		$post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
		 
		$em = $this->getEntityManager('DefaultDb');
		
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//EMPRESA DEL USUARIO LOGEADO (PROVEEDOR)
		
		$param = $this->getRequest()->getParam('param');//VARIABLE QUE TOMA LOS VALORES DE LA OPERACION A LA QUE PERTENECEN LOS DETALLES
		$pos = strpos($param, ':');                     /////////////////////////////////////
		//$idrelationship = substr($param, $pos + 1 );    /// DESCOMPOSICIÓN DE LA VARIABLE ///
		$idop = substr($param,0,$pos);             /////////////////////////////////////
		
		$item = new DefaultDb_Entity_TransportOperationDetail();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Contacts
		
		$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
		try
		{
			$item->setCustody($post['custody']);
			$item->setAssured($post['assured']);
			$item->setOperation($idop);
			$item->setDate(date('Y-m-d H:m:s'));
			$item->setUser($user->getId());//id del usuario que hizo el registro
			$item->setCompany($company->getId());//id de la empresa a la que pertenece el usuario
				
			$em->persist($item);
			$em->flush();
			
			if($item->getAssured()==1){$shipassured="Si";}
			else{$shipassured="No";}
				
			if($item->getCustody()==1){$custody="Si";}
			else{$custody="No";}
						
			$arrJson[] = array(
					"idTransportOpdetail" => $item->getId(),
					"custody" => $custody,
					"assured" => $shipassured,
    				"idcompany" => $item->getCompany(),
    				"company" => $company->getBusinessName(),
					"operation" => $item->getOperation(),
					"regdate" => $item->getDate(),
					"user" => $item->getUser(),
					"company" => $item->getCompany()
			);
			$em->getConnection()->commit();
		}
		catch(Exception $e)//EXCEPCIÓN PARA CUANDO HAY ALGUN ERROR AL GUARDAR LA NUEVA INFORMCIÓN
		{
			$em->getConnection()->rollback();
			$em->close();
			echo "errorsote";
			$arrJson[] = array("result" => $e->getMessage());
		}
		
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}

	public function editOperationsDetailsAction()//FUNCION QUE PERMITE EDITAR INFORMACION ALMANCENADA EN LA TABLA contacts
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE EDICION
	
		$em= $this->getEntityManager('DefaultDb');
		 
		$item= ($post['idTransportOpdetail']) ? $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id' =>$post['idTransportOpdetail'])) : new DefaultDb_Entity_TransportOperationDetail();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD TransportOperationDetail TOMANDO EL PARAMETRO ID DEL REGISTRO AL QUE SE EDITARAN LOS DATOS

		/****************** Verificación de los valores correspondientes a los combos con solo dos posibles valores enviados en la edición del registros ***********************/
		//primero se verifica el tipo de valor enviado
		//dependiendo del resultado se vuelve a verificar el valor o se asigna directamente el valor enviado en la varible}
		 
		if(is_numeric($post['custody'])==true)
		{
			$custody= $post['custody'];
		}
		else
		{
			if($post['custody']=="Si"){$custody=1;}
			else{$custody=0;}
		}
		 
		if(is_numeric($post['assured'])==true)
		{
			$assured= $post['assured'];
		}
		else
		{
			if($post['assured']=="Si"){$assured=1;}
			else{$assured=0;}
		}
		
		/****************** Verificación de los valores correspondientes a los combos con solo dos posibles valores enviados en la edición del registros ***********************/
		
			$item->setCustody($custody);
			$item->setAssured($assured);
				
			$em->persist($item);
			$em->flush();
			
			if($item->getAssured()==1){$shipassured="Si";}
			else{$shipassured="No";}
				
			if($item->getCustody()==1){$custody="Si";}
			else{$custody="No";}
					
			$arrJson[] = array(
					"idTransportOpdetail" => $item->getId(),
					"custody" => $custody,
					"assured" => $shipassured,
					"operation" => $item->getOperation(),
					"regdate" => $item->getDate(),
					"user" => $item->getUser(),
					"company" => $item->getCompany()
			);
		$this->view->result=json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	
	
	public function delOperationsDetailsAction()//FUNCION QUE PERMITE ELIMINAR UN REGISTRO DE LA TABLA operations
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
	
		$em= $this->getEntityManager('DefaultDb');
	
		$item= ($post['idTransportOpdetail']) ? $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id' =>$post['idTransportOpdetail'])) : new DefaultDb_Entity_TransportOperationDetail();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD TransportOperationDetail TOMANDO EL PARAMETRO ID DEL REGISTRO AL QUE SE EDITARAN LOS DATOS
			
		$em->remove($item);//FUNCION QUE REALIZA LA ELIMINACION DEL DATO REQUERIDO
		$em->flush();
		 
		$arrJson[] = array(
				"respuesta" => "borrado"
		);
		$this->view->result=json_encode($arrJson);
	}	

	public function getNoteInfAction(){//FUNCION PARA OBTENER EL LISTADO DE LAS OPERACIONES REGISTRADAS EN LA BASE DE DATOS
			
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");

		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
		
		$em = $this->getEntityManager('DefaultDb');
	
		$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findBy(array('note' => $post['note']));
		
		$arrJson = array();
		foreach($transportop as $item)
		{
			$arrJson[] = array(//ARREGLO QUE ALMACENA LOS DATOS SOLICITADOS
					"idOperation" => $item->getId(),
					"description" => $item->getDescription(),
					"applicant" => $item->getApplicant(),
					"comments" => $item->getComments(),
					"description" => $item->getDescription(),
					"documentation" => $item->getDocumentation(),
					"price" => $item->getPrice(),
					"quantity" => $item->getQuantity(),
					"total" => $item->getPrice() * $item->getQuantity()
				);
		}
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	
	
	function RecordNoteAction()//FUNCION PARA GENERAR UNA VENTANA EMERGENTE DONDE SE CAPTUREN LOS DETALLES DE LA OPERACIÓN
	{
		$this->view->setUseTemplate(FALSE);
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE EDICION

		$em = $this->getEntityManager('DefaultDb');

		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
		
		$form = '
    			<h3>3.1.5 Carta Porte
                         &nbsp;&nbsp;&nbsp;&nbsp;  
                         <a href="#" id="315"  data-toggle="tooltip" > <i class="icon-large icon-info-sign"></i><input type="hidden" value="3.1.5" id="tooltip_1" /></a>
       
                        </h3>
				<form name="formulario" id="formulario" >';
		$btnpdf = '';
		
		if($post['idnote'] != "")
		{
			$note = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $post['idnote']));
			$form .= '<p>
						<input type="hidden" name="id_note" id="id_note" value="'. $note->getId() .'" readonly size="40">
						<label>Folio:</label> <input type="text" name="folio" id="folio" value="'. $note->getFolio() .'" readonly size="40">
						<label>Cliente:</label> <input type="text" name="customer" id="customer" value="'. $post['customer'].'" readonly size="40">
						<label>Fecha:</label> <input name="date" id="date" value="'. $note->getDate() .'" style="width:10%;">
					  </p>			
					  <!--p><label>Comentario 1:</label--><textarea id="comments1" name="comments1" class="btnhidden" readonly style="width:50%;">'. utf8_decode($note->getComments()) .'</textarea>
						 <!--button style="margin-left:3%; width:10%;" type="button" class="editButton" id="comment1" onClick="actv_comment(this.id)" >Editar</button>
						 <button style="margin-left:3%; width:10%;" type="button" class="btnhidden" id="save1" onClick="save_comment(this.id)" >Guardar</button>
					  </p-->
					  <!--p><label>Comentario 2:</label--><textarea id="comments2" name="comments2" class="btnhidden" readonly style="width:50%;">'. utf8_decode($note->getComments2()) .'</textarea>
						 <!--button style="margin-left:3%; width:10%;" type="button" class="editButton" id="comment2" onClick="actv_comment(this.id)" >Editar</button>
						 <button style="margin-left:3%; width:10%;" type="button" class="btnhidden" id="save2" onClick="save_comment(this.id)" >Guardar</button>
					  </p-->
					  <!--p><label>Comentario 3:</label--><textarea id="comments3" name="comments3" class="btnhidden" readonly style="width:50%;">'. utf8_decode($note->getComments3()) .'</textarea>
						 <!--button style="margin-left:3%; width:10%;" type="button" class="editButton" id="comment3" onClick="actv_comment(this.id)" >Editar</button>
				 		 <button style="margin-left:3%; width:10%;" type="button" class="btnhidden" id="save3" onClick="save_comment(this.id)" >Guardar</button>
					  </p-->
					  <br /><br /><br />';
			$gridhead = '<input type="text" value="OPERACIONES INCLUIDAS EN LA CARTA" readonly style="width:99%; margin:0; text-align:center"/>';
			
			$operation = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('note' => $post['idnote'], "type" => 1));
			$idoperation = ($operation != null) ? $operation->getId() : "";
				
			if($idoperation == "")
			{
					$btnpdf .='<button type="button" class="btnpdf" id="pdf"
							onClick="pdf('.$post['idnote'].',1)" style="margin-top:40px">PDF</button><button type="button" class="closeButton" id="close"
						onClick="close_window()" style="margin-top:40px">Cerrar</button>';
			}
			else
			{
				$operationdetails = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $idoperation));
				$idoperationdetails = ($operationdetails != null) ? $operationdetails->getId() : "";
					
				if($idoperationdetails == "")
				{
					$btnpdf .='<button type="button" class="btnpdf" id="pdf"
							onClick="pdf('.$post['idnote'].',2)" style="margin-top:40px">PDF</button><button type="button" class="closeButton" id="close"
						onClick="close_window()" style="margin-top:40px">Cerrar</button>';
				}
				else
				{
					$btnpdf .='<button type="button" class="btnpdf" id="pdf"
							onClick="pdf('.$post['idnote'].',0)" style="margin-top:40px">PDF</button><button type="button" class="closeButton" id="close"
						onClick="close_window()" style="margin-top:40px">Cerrar</button>';
				}
			} 
	}else{
			$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' => $post['idop']));
			
			$combo_notes = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
			if($transportop->getType()==1){//////////////SE OBTIENEN LAS CARTAS PORTE QUE NO ESTEN INCLUIDAS EN UNA OPERACION DE TIPO FLETE/////////////
				$notes = $em->getRepository('DefaultDb_Entity_LetterBearing')->getLetter($post['idcust'],$company->getId());
				
				foreach($notes as $note){
					$combo_notes .= "<option value='".$note['id_letter_bearing']."'";
					$combo_notes .= ">".utf8_decode($note['folio'])."</option> \n";
				}			
			}else{////////////////SE OBTIENEN TODAS LAS CARTAS PORTE GENERADAS PARA ESE CLIENTE///////////////////////////////////////////////////////// 
				$notes = $em->getRepository('DefaultDb_Entity_LetterBearing')->findBy(array('customer' => $post['idcust'], 'company' => $company->getId()));
				foreach($notes as $note){
					$combo_notes .= "<option value='".$note->getId()."'";
					$combo_notes .= ">".utf8_decode($note->getFolio())."</option> \n";
				}			
			}
		
			$form .= '<fieldset><legend>Datos de Operaci&oacute;n:</legend>';
			$form .= '<p>
					<label>Fecha:</label> <input type="text" value="'. $transportop->getOperationDate() .'" readonly size="40">
					<label>Cliente:</label> <input type="text" value="'. $post['customer']. '"size="40" readonly>
					<label>Solicitante:</label> <input type="text" value="' . $transportop->getApplicant() . '" readonly size="40">
				  	</p>';			
			$form .= '</fieldset>';
				
			$form .= '<fieldset><legend>Datos de Nota:</legend>';
			$form .= '<div class="alert alert-warning" style="text-align:left">SOLO SE PUEDE INCLUIR UNA OPERACI&Oacute;N DE TIPO FLETE PARA CADA CARTA PORTE</div>';
			$form .= '<label>¿Que deseas hacer?</label><br />
                        <input type="radio" name="animal" value="1" onClick="check_option(this.value)"/>Agregar la Operaci&oacute;n a Nota
                        <input type="radio" name="animal" value="2" onClick="check_option(this.value)" />Generar Nueva Nota<br /><br />';
			
			$form .= '<div id="d_note" name="d_note">';
			$form .= '<p><select name="note" id="note" style="width:20%;" onchange="note_info(this.value)">';
					$form .= $combo_notes;
			$form .= '	</select>';
			$form .= '<button type="button" class="saveButton" id="add_op" onClick="save_note(this.id)" >Agregar a Nota</button>';
			//$form .= '<input type="button" id="add_op" value="Agregar a Nota" onClick="save_note(this.id)" />';
			$form .= '</p><br /><br />
					  <p id="info_note" name="info_note"></p><br />
						<button type="button" class="closeButton" id="close" onClick="close_window()" style="margin-top:40px">Cerrar</button>
					  </div>';
				
			$form .= '<section id="data_note" name="data_note">
						<p><label>Fecha:</label> <input type="text" name="date" id="date" value="" size="40"></p>
						<!--p><label>Comentario 1:</label--><textarea id="comments1" name="comments1" class="btnhidden" style="width:50%;"></textarea></p>
						<!--p><label>Comemtario 2:</label--><textarea id="comments2" name="comments2" class="btnhidden" style="width:50%;"></textarea></p>
						<!--p><label>Comentario 3:</label--><textarea id="comments3" name="comments3" class="btnhidden" style="width:50%;"></textarea></p>
						<center><button type="button" class="saveButton" id="save_n"
						onClick="save_note(this.id)" style="margin-top:40px">Guardar Nota</button>
						<button type="button" class="closeButton" id="close"
						onClick="close_window()" style="margin-top:40px">Cerrar</button></center>
						<!--center><input name="" id="save_n" type="button" id="save" value="Guardar Nota"
						onClick="save_note(this.id)" style="margin-top:40px"></center-->
					</p></section>';
			$form .= '</fieldset>';

			$gridhead = "";
				
		}

		$form .= '</form>
				<div id="grid1">'.$gridhead.'</div>
				<center>
				<!--button type="button" class="closeButton" id="close"
				onClick="close_window()" style="margin-top:40px">Cerrar</button-->';
		$form .= $btnpdf;
		$form .='</center>
				<input type="hidden" name="idop" id="idop" value="'. $post['idop'] .'">
				<input type="hidden" name="idcustomer" id="idcustomer" value="'. $post['idcust'] . '"size="40">
				';
	
		echo utf8_encode($form);
	}
	
	function infoNoteAction()
	{
		$this->view->setUseTemplate(FALSE);

		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS
		
		$em = $this->getEntityManager('DefaultDb');
		
		$note = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $post['idnote']));
		
		$form = "";
		$form .= '<label>Fecha de Captura:</label> <input type="text" value="'. $note->getDate() .'" size="40" readonly />
				  <br />
				  <p><label>Comentario 1:</label><textarea style="width:40%;" readonly>'. utf8_decode($note->getComments()) .'</textarea></p>
				  <p><label>Comentario 2:</label><textarea style="width:40%;" readonly>'. utf8_decode($note->getComments2()) .'</textarea></p>
				  <p><label>Comentario 3:</label><textarea style="width:40%;" readonly>'. utf8_decode($note->getComments3()) .'</textarea></p>
				  ';		
		echo utf8_encode($form);
	}
	
	function saveNoteAction()
	{
		$this->view->setUseTemplate(FALSE);
		
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS

		$em = $this->getEntityManager('DefaultDb');

		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
		
		if($post['action'] == 'save_n'){
			
			/*$preletter= $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('company' => $company->getId()),//Where
					array('id' => 'DESC'),	//OrderBy
					1,						//Limit
					1						//Offset
			);*/
			
			$preletter= $em->getRepository('DefaultDb_Entity_LetterBearing')->getMaxCP($company->getId());
			//var_dump($preletter[0]['folio']);	

			if($preletter != NULL){
				//$folnum = ((substr($preletter->getFolio(), -5)) * 1) + 1;
				$folnum = $preletter[0]['folio'] + 1; 
			}else{
				$folnum = "1";
			}				
			
			$note = new DefaultDb_Entity_LetterBearing();//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD Contacts
			$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
			try{
				
				$note->setDate(date('Y-m-d H:m:s'));
				$note->setComments($post['comments1']);
				$note->setComments2($post['comments2']);
				$note->setComments3($post['comments3']);
				$note->setCustomer($post['idcustomer']);
				$note->setFolio("");
				$note->setStatus('1');
				$note->setCompany($company->getId());
				$note->setUser($user->getId());
				$note->setType('1');

				$em->persist($note);
				$em->flush();
			
				$idnote = $note->getId();
				$arrJson[] = array("result" => $idnote );
				$em->getConnection()->commit();
				
			}
			catch(Exception $e)//EXCEPCIÓN PARA CUANDO HAY ALGUN ERROR AL GUARDAR LA NUEVA INFORMCIÓN
			{
				$em->getConnection()->rollback();
				$em->close();
				echo "errorsote";
				$arrJson[] = array("result" => $e->getMessage());
			}
			
			switch (strlen($folnum)){
				case 5:
					$pref = "";
					break;
				case 4:
					$pref = "0";
					break;
					
				case 3:
					$pref = "00";
					break;
				case 2:
					$pref = "000";
					break;
				case 1:
					$pref = "0000";
					break;
						
			}
			$note->setFolio(substr($company->getRfc(),0,7) . "-CP" . $pref . $folnum);
			$em->persist($note);
			$em->flush();	
			
		}else{
			$idnote = $post['note'];
			$arrJson[] = array("result" => $idnote );
		}
		
		$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' => $post['idop']));
		$transportop->setNote($idnote);
		$em->persist($transportop);
		$em->flush();
		
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	
	
	public function editCommentAction()
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
		
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
		
		$em= $this->getEntityManager('DefaultDb');
		$item= $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' =>$post['idnote']));//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD OperatingExpense TOMANDO EL PARAMETRO ID DEL REGISTRO AL QUE SE EDITARAN LOS DATOS
		
		switch ($post['suf'])
		{
			case '1':
				$item->setComments($post['comment']);
			break;
			case '2':
				$item->setComments2($post['comment']);
			break;
			case '3':
				$item->setComments3($post['comment']);
			break;
		}
		$em->persist($item);
		$em->flush();
			
		$arrJson[] = array(
				"respuesta" => "Commentario Editado"
		);
		$this->view->result=json_encode($arrJson);
	}

	public function delOpfromNoteAction()//FUNCION QUE PERMITE ELIMINAR UN REGISTRO DE LA TABLA operations
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
	
		$em= $this->getEntityManager('DefaultDb');	
		$item= $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' =>$post['idOperation']));//VARIABLE QUE SE CONVIERTE EN EL OBJETO QUE REFERENCIA A LA ENTIDAD OperatingExpense TOMANDO EL PARAMETRO ID DEL REGISTRO AL QUE SE EDITARAN LOS DATOS
	
		$item->setNote(null);
		$em->persist($item);
		$em->flush();
		 
		$arrJson[] = array(
				"respuesta" => "borrado"
		);
		$this->view->result=json_encode($arrJson);
	}	

	public function infoArticleAction()//FUNCION QUE PERMITE ELIMINAR UN REGISTRO DE LA TABLA operations
	{
		$this->view->setUseTemplate(FALSE);

		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS
		
		$em = $this->getEntityManager('DefaultDb');
		
		$item = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $post['idArticle']));
		
		$arrJson = array();
		
		$arrJson[] = array(//ARREGLO QUE ALMACENA LOS DATOS SOLICITADOS
				"idAritcle" => $item->getId(),
				"reference" => $item->getReference(),
				"price" => $item->getPrice(),
				"comments" => $item->getComments(),
				"description" => $item->getDescription(),
				"iva" => $item->getIva(),
				"retention" => $item->getRetention()
		);
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	

	public function getOperationTypesAction(){//FUNCION PARA OBTENER EL LISTADO DE LAS ORDENES DE CARGA REGISTRADAS EN LA BASE DE DATOS
	
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
	
		$em = $this->getEntityManager('DefaultDb');
		$lista = $em->getRepository('DefaultDb_Entity_OperationType')->findAll();//CONSULTA A LA TABLA letter_bearing
	
		$arrJson = array();
		foreach($lista as $item)
		{
			$arrJson[] = array(//ARREGLO QUE ALMACENA LOS DATOS SOLICITADOS
					"idOperationType" => $item->getId(),
					"description" => $item->getDescription(),
					"name" => $item->getName(),
					"iduser" => $item->getUser(),
					"idcompany" => $item->getCompany(),
			);
		}
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}	

	function GridExpenseDetailsAction()//FUNCION PARA GENERAR UNA VENTANA EMERGENTE DONDE SE CAPTUREN LOS DETALLES DE LA OPERACIÓN
	{
		$this->view->setUseTemplate(FALSE);
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS
	
		$em = $this->getEntityManager('DefaultDb');
		
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));
		
		$transportop = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' => $post['idop'] ));
		$operationdet = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $post['idop'] ));
		$kilometers = $em->getRepository('DefaultDb_Entity_UnitKilometer')->findOneBy(array('operation' => $post['idop'], 'type' => 1));

		$idexp = 0;

		$unit = 0;
		$driver = 0;
		$idoperationdet = 0;
		
		$idsource = 0;
		$edt = "";
		$edd = "";
		$rtd = "";
		$rdd = "";
		$ert = "";
		$erd = "";
		$rrt = "";
		$rrd = "";
		
		$iddestination = 0;
		$eta = "";
		$eda = "";
		$rta = "";
		$rda = "";
		$ete = "";
		$ede = "";
		$rte = "";
		$rde = "";

		$iddistribution1 = 0;
		$etad1 = "";
		$edad1 = "";
		$rtad1 = "";
		$rdad1 = "";
		$eted1 = "";
		$eded1 = "";
		$rted1 = "";
		$rded1 = "";
		
		$iddistribution2 = 0;
		$etad2 = "";
		$edad2 = "";
		$rtad2 = "";
		$rdad2 = "";
		$eted2 = "";
		$eded2 = "";
		$rted2 = "";
		$rded2 = "";
		
		
		$receiver ="";
		$kilometersval = 0;

		
		if(count($operationdet) != 0)
		{
			$unit = $operationdet->getUnit();
			$driver = $operationdet->getDriver();
			$idoperationdet = $operationdet->getId();
			
			$idsource = $operationdet->getSource();
			$edt = $operationdet->getEdt();
			$edd = $operationdet->getEdd();
			$rtd = $operationdet->getRtd();
			$rdd = $operationdet->getRdd();
			$ert = $operationdet->getErt();
			$erd = $operationdet->getErd();
			$rrt = $operationdet->getRrt();
			$rrd = $operationdet->getRrd();
				
			$iddestination = $operationdet->getDestination();
			$eta = $operationdet->getEta();
			$eda = $operationdet->getEda();
			$rta = $operationdet->getRta();
			$rda = $operationdet->getRda();
			$ete = $operationdet->getEte();
			$ede = $operationdet->getEde();
			$rte = $operationdet->getRte();
			$rde = $operationdet->getRde();

			$iddistribution1 = $operationdet->getDistribution1();
			$etad1 = $operationdet->getEtad1();
			$edad1 = $operationdet->getEdad1();
			$rtad1 = $operationdet->getRtad1();
			$rdad1 = $operationdet->getRdad1();
			$eted1 = $operationdet->getEted1();
			$eded1 = $operationdet->getEded1();
			$rted1 = $operationdet->getRted1();
			$rded1 = $operationdet->getRded1();
			
			$iddistribution2 = $operationdet->getDistribution2();
			$etad2 = $operationdet->getEtad2();
			$edad2 = $operationdet->getEdad2();
			$rtad2 = $operationdet->getRtad2();
			$rdad2 = $operationdet->getRdad2();
			$eted2 = $operationdet->getEted2();
			$eded2 = $operationdet->getEded2();
			$rted2 = $operationdet->getRted2();
			$rded2 = $operationdet->getRded2();
				
			$receiver = $operationdet->getReceiver();
			$idprov = $operationdet->getProvider();
		}
		else 
		{
			$provider = $em->getRepository('DefaultDb_Entity_OperatingExpense')->getProvExpense($post['idop'],$company->getId());//CONSULTA A LA TABLA operating_expenses
			$idprov = (count($provider) != 0) ? $provider[0]['prov'] : $company->getId();
		}
		
		($kilometers != null) ? $kilometersval = $kilometers->getQuantity() : $kilometersval = $kilometersval; 
		/*if($idprov != "")
		{*/

			$relationship = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id_company1' => $company->getId(), 'id_company2' => $idprov)); ///////////////////////////////////////////////////////////////////////////////////
			$relationship_type = ($relationship != null) ? $relationship->getType() : 2;
		 
			$relationshipCust = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $transportop->getRelationship())); ///////////////////////////////////////////////////////////////////////////////////
					
			$companyprov= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $idprov));
			$unidades = $em->getRepository('DefaultDb_Entity_Unit')->findBy(array('company' => $company->getId(),'relationship_type' => $relationship_type, 'company_prov' => $idprov/*, 'status' => '1'*/), array('status' => 'DESC','economicNumber' => 'ASC'));
			//$drivers = $em->getRepository('DefaultDb_Entity_Driver')->findBy(array('company' => $company->getId(),'relationship_type' => $relationship_type, 'company_prov' => $idprov ), array('id' => 'ASC'));
			//$drivers = $em->getRepository('DefaultDb_Entity_Driver')->findBy(array('company_prov' => $idprov ), array('id' => 'ASC'));
			if($idprov == $company->getId()){
				$drivers = $em->getRepository('DefaultDb_Entity_Driver2')->getDrivers($company->getId());
			}else{
				//$drivers = $em->getRepository('DefaultDb_Entity_Driver2')->getDriversl($idprov);
				$drivers = $em->getRepository('DefaultDb_Entity_Driver2')->getDriversl($idprov,$company->getId());
			}
				
			$locations = $em->getRepository('DefaultDb_Entity_Location')->findBy(array('id_relationship' => $relationshipCust->getId())); ///////////////////////////////////////////////////////////////////////////////////
			//$locations = $em->getRepository('DefaultDb_Entity_Location')->findBy(array('id_relationship' => $post['idrelationship'])); ///////////////////////////////////////////////////////////////////////////////////
			//$relationship = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $post['idrelationship'])); ///////////////////////////////////////////////////////////////////////////////////
			
			/*$spending = $em->getRepository('DefaultDb_Entity_OperatingExpense')->findOneBy(array('id' => $idexp));							///////////////////////////////////////////////////////////////////////////////////
			$opertion = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' => $spending->getOperation()));		/////CON ESTAS CONSULTAS SE OBTIENEN LAS LOCACIOENS DEL CLIENTE DE LA OPERACION////
			$locations = $em->getRepository('DefaultDb_Entity_Location')->findBy(array('id_relationship' => $opertion->getRelationship())); ///////////////////////////////////////////////////////////////////////////////////
			$relationship = $em->getRepository('DefaultDb_Entity_Relationship')->findOneBy(array('id' => $opertion->getRelationship())); ///////////////////////////////////////////////////////////////////////////////////
			*/
			$combo = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
			foreach($unidades as $item)
			{
				$combo .= "<option value='".$item->getId()."'";
				if($item->getId()==$unit){$combo.= " selected";}
				if($item->getStatus()=="0"){$combo.=' disabled="disabled" style="background-color:#C7D0D5;"';}
				$combo .=">".$item->getEconomicNum()."</option> \n";
			}
		
			$combo_drivers = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
			$driver_dat = "";
			foreach($drivers as $item1)
			{
				$comp="";
				if(isset($post['comp'])){$comp='../';}
				
				if($item1['status'] != 0){
					if($item1['relationship_type'] == 2){$reltype = "Contrato";}else{$reltype = "Subcontrato";}
					if($company->getId() == $item1['id_company1']){
						$compcontr =  $company->getBusinessName();
						$color =  "#CEF6F5";
					
					}else{
						$compcontr = $companyprov->getBusinessName();
						$color =  "#A9D0F5";
					}
					$combo_drivers .= '<option value="'.$item1['id_driver'].'" style="background-color:'.$color.'; color:#000;"';
					if($item1['id_driver']==$driver){
						$combo_drivers.= " selected";
					
					
						//$driver = $em->getRepository('DefaultDb_Entity_Driver2')->findOneBy(array('id' => $post['param']), array('id' => 'ASC'));
						$driver_data1 = $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $item1['id_company_data']));
						$documents1 = $em->getRepository('DefaultDb_Entity_Document')->findBy(array('company' => $item1['id_company_data']));
						$listdoc1 = "";
						if(count($documents1) > 0){
							foreach($documents1 as $doc1){
								if($doc1->getPath() != ""){
									$listdoc1 .= "<a href='../../../".$comp.$doc1->getPath()."' class='btn btn-secondary' target='_blank'>".$doc1->getName()."<a/><br /><br />";
								}
							}
						}
						$driver_dat .= '<div id="flotante">';
						$driver_dat .= '<div id="A"><img src="../../../'.$comp.$driver_data1->getLogo().'" class="img-responsive" style="width:40%;" alt=""></div>';
						$driver_dat .= '<div id="B">'.$listdoc1.'</div>';
						$driver_dat .= '</div>';
						$driver_dat .= '<p>';
						$driver_dat .= '<label>RFC:</label><label><strong>'. $item1['rfc'].'</strong></label>';
						$driver_dat .= '</p>';
						$driver_dat .= '<p>';
						$driver_dat .= '<label>Nombre(s):</label><label><strong>'. utf8_decode($item1['names']).'</strong></label>';
						$driver_dat .= '<label>Apellido(s):</label><label><strong>'. utf8_decode($item1['last_names']).'</strong></label>';
						$driver_dat .= '<label>C&eacute;lular:</label><label><strong>'. $driver_data1->getPhone().'</strong></label>';
						$driver_dat .= '</p>';
						$driver_dat .= '<p>';
						$driver_dat .= '<label>Licencia:</label><label><strong>'. $item1['license'].'</strong></label>';
						$driver_dat .= '<label>N&uacute;mero de Licencia:</label><label><strong>'. $item1['license_number'].'</strong></label>';
						$driver_dat .= '<label>Vigencia:</label><label><strong>'. $item1['license_duration'].'</strong></label>';
						$driver_dat .= '</p>';
					
					}
					$combo_drivers .= ">".utf8_decode($item1['names']." ".$item1['last_names'])." - ".$reltype." en ". $compcontr ."</option> \n";
				}
				/*$combo_drivers .= "<option value='".$item1->getId()."'";
				if($item1->getId()==$driver){$combo_drivers.= " selected";}
				$combo_drivers .= ">".utf8_decode($item1->getName())."</option> \n";*/
			}
		
			$combo_source = "<option value='0'>Seleccione una Locaci&oacute;n</option> \n";
			$source_dat = "";
			foreach($locations as $item2)
			{
				$combo_source .= "<option value='".$item2->getId()."'";
				if($item2->getId()==$idsource){
					$combo_source.= " selected";
					$location = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $idsource));
					$country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $location->getCountry()));
					$state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $location->getState()));
					$city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $location->getCity()));
					
					$source_dat = '<p>';
					$source_dat .= '<label>Descripci&oacute;n:</label><label><strong>'. utf8_decode($location->getDescription()).'</strong></label>';
					$source_dat .= '</p>';
					$source_dat .= '<p>';
					$source_dat .= '<label>Pa&iacute;s:</label><label><strong>'. utf8_decode($country->getName()) .'</strong></label>';
					$source_dat .= '<label>Estado:</label><label><strong>'. utf8_decode($state->getName()) .'</strong></label>';
					$source_dat .= '<label>Ciudad:</label><label><strong>'. utf8_decode($city->getName()) .'</strong></label>';
					$source_dat .= '</p>';
					$source_dat .= '<p>';
					$source_dat .= '<label>Colonia:</label><label><strong>'.utf8_decode($location->getSuburb()).'</strong></label>';
					$source_dat .= '<label>Calle:</label><label><strong>'. utf8_decode($location->getStreet()).'</strong></label>';
					$source_dat .= '<label>N&uacute;mero:</label><label><strong>'. $location->getNumber().'</strong></label>';
					$source_dat .= '</p>';
					$source_dat .= '<p>';
					$source_dat .= '<label>C&oacute;digo Postal:</label><label><strong>'. $location->getZip().'</strong></label>';
					if($location->getUrl() != ""){
                                            $source_dat .= '<label>Url Google:</label><label><strong>'. utf8_decode($location->getUrl()) .'</strong></label>';
                                            $source_dat .= '<button type="button" class="plusButton" name="'. utf8_decode($location->getUrl()) .'" onClick="window.open(this.name)">Ver Mapa</button>';
                                        }
                                        $source_dat .= '</p>';
				}
				$combo_source .= ">".utf8_decode($item2->getDescription())."</option> \n";
			}
		
			$combo_destination = "<option value='0'>Seleccione una Locaci&oacute;n</option> \n";
			$dest_dat = "";
			foreach($locations as $item3)
			{
				$combo_destination .= "<option value='".$item3->getId()."'";
				if($item3->getId()==$iddestination){
					$combo_destination.= " selected";

					$location = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $iddestination));
					$country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $location->getCountry()));
					$state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $location->getState()));
					$city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $location->getCity()));
						
					$dest_dat .= '<p>';
					$dest_dat .= '<label>Descripci&oacute;n:</label><label><strong>'. utf8_decode($location->getDescription()).'</strong></label>';
					$dest_dat .= '</p>';
					$dest_dat .= '<p>';
					$dest_dat .= '<label>Pa&iacute;s:</label><label><strong>'. utf8_decode($country->getName()) .'</strong></label>';
					$dest_dat .= '<label>Estado:</label><label><strong>'. utf8_decode($state->getName()) .'</strong></label>';
					$dest_dat .= '<label>Ciudad:</label><label><strong>'. utf8_decode($city->getName()) .'</strong></label>';
					$dest_dat .= '</p>';
					$dest_dat .= '<p>';
					$dest_dat .= '<label>Colonia:</label><label><strong>'.utf8_decode($location->getSuburb()).'</strong></label>';
					$dest_dat .= '<label>Calle:</label><label><strong>'. utf8_decode($location->getStreet()).'</strong></label>';
					$dest_dat .= '<label>N&uacute;mero:</label><label><strong>'. $location->getNumber().'</strong></label>';
					$dest_dat .= '</p>';
					$dest_dat .= '<p>';
					$dest_dat .= '<label>C&oacute;digo Postal:</label><label><strong>'. $location->getZip().'</strong></label>';
                                        if($location->getUrl() != ""){
                                            $dest_dat .= '<label>Url Google:</label><label><strong>'. utf8_decode($location->getUrl()) .'</strong></label>';
                                            $dest_dat .= '<button type="button" class="plusButton" name="'. utf8_decode($location->getUrl()) .'" onClick="window.open(this.name)">Ver Mapa</button>';
                                        }
                                        $dest_dat .= '</p>';
				}
				$combo_destination .= ">".utf8_decode($item3->getDescription())."</option> \n";
			}
	
			/*$combo_distribution1 = "<option value='0'>Seleccione una Locación</option> \n";
			foreach($locations as $item4)
			{
				$combo_distribution1 .= "<option value='".$item4->getId()."'";
				if($item4->getId()==$iddistribution1){$combo_distribution1.= " selected";}
				$combo_distribution1 .= ">".utf8_decode($item4->getDescription())."</option> \n";
			}
	
			$combo_distribution2 = "<option value='0'>Seleccione una Locación</option> \n";
			foreach($locations as $item5)
			{
				$combo_distribution2 .= "<option value='".$item5->getId()."'";
				if($item5->getId()==$iddistribution2){$combo_distribution2.= " selected";}
				$combo_distribution2 .= ">".utf8_decode($item5->getDescription())."</option> \n";
			}*/		
			
			$form = '<div style="color: #000080;"><h4>Detalles de la Operaci&oacute;n '.$transportop->getId().': '.utf8_decode($transportop->getDescription()).'
					                       &nbsp;&nbsp;&nbsp;&nbsp;  
                                           <a href="#" id="3132"  data-toggle="tooltip" > <i class="icon-large icon-info-sign"></i><input type="hidden" value="3.1.3.2" id="tooltip_1" /></a>
       			<form name="formulario" id="formulario" >';
			
			$form .= '<div class="demo-section k-content">
                        	<div id="tabstrip">
                            	<ul>';
			
			//if($unidades != null){
			
				$form .= '              <li class="k-state-active">
		                                    Veh&iacute;culo
		                                </li>';
			//}
				
			//if($drivers != null){
			
			$form .= '              <li>
			Conductor
			</li>';
			//}
				
			//if($source != null){
			
				$form .= '              <li>
		                                    Origen
		                                </li>';
			//}
				
			//if($destination != null){
			
				$form .='                <li>
		                                    Destino
		                                </li>';
			//}
			
			$form .='                <li>
	                                    Evidencias
	                                </li>';
				
			$form .= '</ul>';
			
			
			$form .= '<div><h5>Datos de Veh&iacute;culo de la Empresa '. $companyprov->getBusinessName() .'</h5>';
			$form .= '<p>';
			$form .= '<label>Unidad:</label>
								<select name="unit" id="unit" onchange="unit_data(this.value)">';
			$form .= $combo;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="save_u" onClick="capture_unit()">Nuevo Veh&iacute;culo</button>';
			$form .= '</p>';
			$form .= '<div id="data" name="data">';
			$form .= '</div>';
			//$form .= '<fieldset><legend></legend>';
			if($company->getId() == $idprov)
			{
				$form .= '<p>';
				$form .= '<label class="">Kilometraje Inicial:</label><input class="" type="text" name="kilometers" id="kilometers" value="'. $kilometersval .'" />';
				$form .= '</p>';
			}
			//$form .= '</fieldset>';
			$form .= '</div>';
			
			$form .= '<div style="display:none">Datos del Conductor de la Empresa <h5>'. $companyprov->getBusinessName() .'</h5>:';
			$form .= '<p>';
			$form .= '<label>Conductor:</label>
								<select name="driver" id="driver" onchange="driver_data(this.value)">';
			$form .= $combo_drivers;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="save_d" onClick="capture_driver('.$idprov.')">Nuevo Conductor</button>';
			$form .= '</p>';
			$form .= '<div id="data_driver" name="data_driver">';
			$form .=  $driver_dat;
			$form .= '</div>';
			$form .= '</div>';
			
			$form .= '<div style="display:none">Datos de Origen:';
			$form .= '<p>';
			$form .= '<label>Ubicaci&oacute;n:</label>
								<select name="source" id="source" onchange="location_data(this.value,this.id)">';
			$form .= $combo_source;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="capture_source" onClick="capture_location(this.id)">Nueva Ubicaci&oacute;n</button>';
			$form .= '</p>';
			$form .= '<div id="data_source" name="data_source">';
			$form .= $source_dat;
			$form .= '</div>';
			$form .= '<p>';
			$form .= '<label><strong>Horarios:</strong></label>';
			$form .= '<button type="button" class="plusButton" id="btn_hrs_source" onClick="view_data(this.id)"></button>';
			$form .= '<button type="button" class="minusButton hidden" id="btn1_hrs_source" onClick="view_data(this.id)"></button>';
			$form .= '</p>';
			$form .= '<div id="hrs_source" class="hrs_hidden">';
			//$form .= '<div id="hrs_source">';
			$form .= '<p>';
			$form .= '<label>Fecha progragada:</label><input class="date" type="text" name="edd" id="edd" value="'. $edd .'" />';
			$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="edt" id="edt" value="'. $edt .'" />';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Fecha real de salida:</label><input class="date" type="text" name="rdd" id="rdd" value="'. $rdd .'" />';
			$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="rtd" id="rtd" value="'. $rtd .'" />';
			$form .= '</p>';
			$form .= '<p class="hidden">';
			$form .= '<label class="log_label">Fecha programada de regreso:</label><input class="date" type="text" name="erd" id="erd" value="'. $erd .'" />';
			$form .= '<label>Hora programada de regreso:</label><input type="text" class="time" name="ert" id="ert" value="'. $ert .'" />';
			$form .= '</p>';
			$form .= '<p class="hidden">';
			$form .= '<label class="log_label">Fecha real de regreso:</label><input class="date" type="text" name="rrd" id="rrd" value="'. $rrd .'" />';
			$form .= '<label>Hora real de regreso:</label><input type="text" class="time" name="rrt" id="rrt" value="'. $rrt .'" />';
			$form .= '</p>';
			$form .= '</div>';
			$form .= '</div>';
				
			
			$form .= '<div style="display:none">Datos de Destino:';
			$form .= '<p>';
			$form .= '<label>Ubicaci&oacute;n:</label>
								<select name="destination" id="destination" onchange="location_data(this.value,this.id)">';
			$form .= $combo_destination;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="capture_destination" onClick="capture_location(this.id)">Nueva Ubicaci&oacute;n</button>';
			$form .= '</p>';
			$form .= '<div id="data_destination" name="data_destination">';
			$form .= $dest_dat;
			$form .= '</div>';
			$form .= '<p>';
			$form .= '<label><strong>Horarios:</strong></label>';
			$form .= '<button type="button" class="plusButton" id="btn_hrs_destination" onClick="view_data(this.id)"></button>';
			$form .= '<button type="button" class="minusButton hidden" id="btn1_hrs_destination" onClick="view_data(this.id)"></button>';
			$form .= '</p>';
			//$form .= '<div id="hrs_destination">';
			$form .= '<div id="hrs_destination" class="hrs_hidden">';
			$form .= '<p>';
			$form .= '<label>Fecha programada:</label><input class="date" type="text" name="eda" id="eda" value="'. $eda .'" />';
			$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="eta" id="eta" value="'. $eta .'" />';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Fecha real de llegada:</label><input class="date" type="text" name="rda" id="rda" value="'. $rda .'" />';
			$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="rta" id="rta" value="'. $rta .'" />';
			$form .= '</p>';
			$form .= '<p class="hidden">';
			$form .= '<label class="log_label">Fecha estimada de salida:</label><input class="date" type="text" name="ede" id="ede" value="'. $ede .'" />';
			$form .= '<label>Hora estimada de salida:</label><input type="text" class="time" name="ete" id="ete" value="'. $ete .'" />';
			$form .= '</p>';
			$form .= '<p class="hidden">';
			$form .= '<label class="log_label">Fecha real de salida:</label><input class="date" type="text" name="rde" id="rde" value="'. $rde .'" />';
			$form .= '<label>Hora real de salida:</label><input type="text" class="time" name="rte" id="rte" value="'. $rte .'" />';
			$form .= '</p>';
			$form .= '</div>';
			$form .= '</div>';
				
			
			$form .= '<div style="display:none">';
			
			$form .= '<div><input name="files" id="files" type="file" accept="image/*"/></div>';
			
			$dir ='images/imagesop/operation_'.$transportop->getId().'/';
			$form .= '<div id="evidences">';
			$divimages = '<div id="divimg">';
			$divlist = '<div id="divlist">';
			if(file_exists($dir)) {
				$form .= '<a id="Buttondown" class="downButton"
					href="../../../images/imagesop/operation_'.$transportop->getId().'/'.$transportop->getId().'.zip">Descargar Evidencias</a>';
					
				$explorar = scandir($dir);
				$idx = 0;
				foreach ($explorar as $file){
					if($idx > 2){
						$fil = explode('.', $file);
						if($fil[1] == "png" || $fil[1] == "jpg" || $fil[1] == "gif"){
							$divimages .= '<a href="'.$this->view->getBaseUrl().'/images/imagesop/operation_'.$transportop->getId().'/'.$file.'" target="_blank"><img src="'.$this->view->getBaseUrl().'/images/imagesop/operation_'.$transportop->getId().'/'.$file.'" height="200" width="200"></a>';
						}else{
							$divlist .='<a href="'.$this->view->getBaseUrl().'/images/imagesop/operation_'.$transportop->getId().'/'.$file.'" target="_blank">'.$file.'</a><br />';
						}
					}
					$idx++;
				}
			}
			$divimages .= "</div>";
			$divlist .= "</div>";
			$form .= $divimages.'<br />';
			$form .= $divlist;
			$form .= '</div>';
				
			$form .='</div>';
			
			$form  .='</div>
                	</div>';
				
				
			/*$form = '
					<h4>Detalles de la Operación '.$transportop->getId().': '.utf8_decode($transportop->getDescription()).'
                                           &nbsp;&nbsp;&nbsp;&nbsp;  
                                           <a href="#" id="3132"  data-toggle="tooltip" > <i class="icon-large icon-info-sign"></i><input type="hidden" value="3.1.3.2" id="tooltip_1" /></a>
       
                                        </h4>
					<form name="formulario" id="formulario" >';*/
		
		
			
			
			
		
			/*$form .= '<fieldset><legend>Reparto 1:</legend>';
			$form .= '<p>';
			$form .= '<label><strong>Horarios:</strong></label>';
			$form .= '<button type="button" class="plusButton" id="btn_hrs_distribution1" onClick="view_data(this.id)"></button>';
			$form .= '<button type="button" class="minusButton hidden" id="btn1_hrs_distribution1" onClick="view_data(this.id)"></button>';
			$form .= '</p>';
			$form .= '<div id="hrs_distribution1" class="hrs_hidden">';
				$form .= '<p>';
				$form .= '<label>Fecha programada:</label><input class="date" type="text" name="edad1" id="edad1" value="'. $edad1 .'" />';
				$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="etad1" id="etad1" value="'. $etad1 .'" />';
				$form .= '</p>';
				$form .= '<p>';
				$form .= '<label>Fecha real de llegada:</label><input class="date" type="text" name="rdad1" id="rdad1" value="'. $rdad1 .'" />';
				$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="rtad1" id="rtad1" value="'. $rtad1 .'" />';
				$form .= '</p>';
				$form .= '<p class="hidden">';
				$form .= '<label>Hora estimada de salida:</label><input type="text" class="time" name="eted1" id="eted1" value="'. $eted1 .'" />';
				$form .= '<label class="log_label">Fecha estimada de salida:</label><input class="date" type="text" name="eded1" id="eded1" value="'. $eded1 .'" />';
				$form .= '</p>';
				$form .= '<p class="hidden">';
				$form .= '<label>Hora real de salida:</label><input type="text" class="time" name="rted1" id="rted1" value="'. $rted1 .'" />';
				$form .= '<label class="log_label">Fecha real de salida:</label><input class="date" type="text" name="rded1" id="rded1" value="'. $rded1 .'" />';
				$form .= '</p>';
			$form .= '</div>';
			$form .= '<p>';
			$form .= '<label>Ubicación:</label>
						<select name="distribution1" id="distribution1" onchange="location_data(this.value,this.id)">';
			$form .= $combo_distribution1;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="capture_distribution1" onClick="capture_location(this.id)">Nueva Ubicación</button>';
			$form .= '</p>';
			$form .= '<div id="data_distribution1" name="data_distribution1">';
			$form .= '</div>';
			$form .= '</fieldset>';
	
			$form .= '<fieldset><legend>Reparto 2:</legend>';
			$form .= '<p>';
			$form .= '<label><strong>Horarios:</strong></label>';
			$form .= '<button type="button" class="plusButton" id="btn_hrs_distribution2" onClick="view_data(this.id)"></button>';
			$form .= '<button type="button" class="minusButton hidden" id="btn1_hrs_distribution2" onClick="view_data(this.id)"></button>';
			$form .= '</p>';
			$form .= '<div id="hrs_distribution2" class="hrs_hidden">';
				$form .= '<p>';
				$form .= '<label>Fecha programada</label><input class="date" type="text" name="edad2" id="edad2" value="'. $edad2 .'" />';
				$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="etad2" id="etad2" value="'. $etad2 .'" />';
				$form .= '</p>';
				$form .= '<p>';
				$form .= '<label>Fecha real de llegada:</label><input class="date" type="text" name="rdad2" id="rdad2" value="'. $rdad2 .'" />';
				$form .= '<label class="log_label">Hora:</label><input type="text" class="time" name="rtad2" id="rtad2" value="'. $rtad2 .'" />';
				$form .= '</p>';
				$form .= '<p class="hidden">';
				$form .= '<label>Hora estimada de salida:</label><input type="text" class="time" name="eted2" id="eted2" value="'. $eted2 .'" />';
				$form .= '<label class="log_label">Fecha estimada de salida:</label><input class="date" type="text" name="eded2" id="eded2" value="'. $eded2 .'" />';
				$form .= '</p>';
				$form .= '<p class="hidden">';
				$form .= '<label>Hora real de salida:</label><input type="text" class="time" name="rted2" id="rted2" value="'. $rted2 .'" />';
				$form .= '<label class="log_label">Fecha real de salida:</label><input class="date" type="text" name="rded2" id="rded2" value="'. $rded2 .'" />';
				$form .= '</p>';
			$form .= '</div>';
			$form .= '<p>';
			$form .= '<label>Ubicación:</label>
						<select name="distribution2" id="distribution2" onchange="location_data(this.value,this.id)">';
			$form .= $combo_distribution2;
			$form .='	</select>';
			$form .= '<button type="button" class="plusButton" id="capture_distribution2" onClick="capture_location(this.id)">Nueva Ubicaci&oacute;n</button>';
			$form .= '</p>';
			$form .= '<div id="data_distribution2" name="data_distribution2">';
			$form .= '</div>';
			$form .= '</fieldset>';*/
	
			$form .= '<p>';
			$form .= '<label>RECIBI&Oacute;:</label><input type="text" name="receiver" id="receiver" value="'. $receiver .'" style="width:30%" />';
			$form .= '</p>';
			
			$form .= '<p style="text-align: rigth;">';
			$form .= '<button name="save_det" type="button" class="saveButton" id="capture_destination" onClick="save_details('.$idoperationdet.')">Guardar Detalles</button><br />';
			$form .= '<div id="data_hidden" name="data_hidden">';
			$form .= '<input type="hidden" id="transportop" name="transportop" value="'.$transportop->getId().'" />';
			$form .= '<input type="hidden" id="company_prov" name="company_prov" value="'.$idprov.'" />';
			$form .= '<input type="hidden" id="relationship" name="relationship" value="'.$relationship_type.'" />';
			$form .= '<input type="hidden" id="idrelationship" name="idrelationship" value="'.$relationshipCust->getId().'" />';
			$form .= '<input type="hidden" id="customer" name="customer" value="'.$relationshipCust->getCompany1().'" />';
			$form .= '</div>';
			$form .= '</p>';
	
		/*}
		else 
		{
			$form = '<center><h3>AVISO:No se ha indicado un proveedor de transporte para la operacion</h3>
						<p>Para registrar detalles de una operación de flete es necesario indicar quien es el proveedor de transporte, esto se hace en la sección de gastos de la operación</p>
					</center>';
				
		}*/
		$form .='</form>';
		$form .= '<center><button type="button" class="closeButton" id="close"
				onClick="close_window()" style="margin-top:40px">Cancelar</button></center>';
	
		echo utf8_encode($form);
	}
	
	/********************* INICIAN FUNCIONES PARA AGREGAR LOCACIONES A LOS DETALLES DE LA OPERACION ****************************/
	function CaptureLocationAction()
	{
		$this->view->setUseTemplate(FALSE);
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO
	
		$em = $this->getEntityManager('DefaultDb');
	
		$countries = $em->getRepository('DefaultDb_Entity_Country')->findAll();
		//$states = $em->getRepository('DefaultDb_Entity_State')->findAll();
	
		$combocountries = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
		foreach($countries as $item)
		{
			$combocountries .= "<option value='".$item->getId()."'>".utf8_decode($item->getName())."</option> \n";
		}
	
		/*$combostates = "<option value='0'>Seleccione una Opción</option> \n";
			foreach($states as $item)
			{
		$combostates .= "<option value='".$item->getId()."'>".utf8_decode($item->getName())."</option> \n";
		}*/

		$form = '<p>';
		$form .= '<label>Descripci&oacute;n:</label> <input type="text" name="description" id="description" value="">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>Pa&iacute;s:</label>
					<select name="country" id="country" onchange="getstate(this.value,'. "'" .$post['param']. "'" .')">';
		$form .= $combocountries;
		$form .='	</select>';
		$form .= '<label>Estado:</label>
					<select name="states'.$post['param'].'" id="states'.$post['param'].'" onchange="getcity(this.value,'. "'" .$post['param']. "'" .')">';
		$form .= "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
		//$form .= $combostates;
		$form .='	</select>';
		$form .= '<label>Ciudad:</label>
					<select name="city'.$post['param'].'" id="city'.$post['param'].'">';
		$form .= "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
		$form .='	</select></p>';
		$form .= '<p>';
		$form .= '<label>Colonia:</label> <input type="text" name="suburb" id="suburb" value="">';
		$form .= '<label>Calle:</label> <input type="text" name="street" id="street" value="">';
		$form .= '<label>N&uacute;mero:</label> <input type="text" name="number" id="number" value="">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>C&oacute;digo Postal:</label> <input type="text" name="zip" id="zip" value="">';
		$form .= '<label>Url Google:</label> <input type="text" name="url" id="url" value="">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<button type="button" id="'.$post['param'].'" class="saveButton"  onClick="save_location(this.id)">Guardar Ubicaci&oacute;n</button>';
		//$form .= '<input type="button" id="'.$post['param'].'"  value="Guardar Ubicacin" onClick="save_location(this.id)" />';
		$form .= '</p>';
	
		echo utf8_encode($form);
	}
	
	function FormDataLocationAction()//FUNCION PARA GENERAR UNA VENTANA EMERGENTE DONDE SE CAPTUREN LOS DETALLES DE LA OPERACIÓN
	{
		$this->view->setUseTemplate(FALSE);
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
	
		if($post['param'] != 0)
		{
			$em = $this->getEntityManager('DefaultDb');
			$location = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $post['param']));
			$country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $location->getCountry()));
			$state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $location->getState()));
			$city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $location->getCity()));
	
			$form = '<p>';
			$form .= '<label>Descripci&oacute;n:</label><label><strong>'. utf8_decode($location->getDescription()).'</strong></label>';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Pa&iacute;s:</label><label><strong>'. utf8_decode($country->getName()) .'</strong></label>';
			$form .= '<label>Estado:</label><label><strong>'. utf8_decode($state->getName()) .'</strong></label>';
			$form .= '<label>Ciudad:</label><label><strong>'. utf8_decode($city->getName()) .'</strong></label>';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Colonia:</label><label><strong>'.utf8_decode($location->getSuburb()).'</strong></label>';
			$form .= '<label>Calle:</label><label><strong>'. utf8_decode($location->getStreet()).'</strong></label>';
			$form .= '<label>N&uacute;mero:</label><label><strong>'. $location->getNumber().'</strong></label>';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>C&oacute;digo Postal:</label><label><strong>'. $location->getZip().'</strong></label>';
                        if($location->getUrl() != ""){
                            $form .= '<label>Url Google:</label><label><strong>'. utf8_decode($location->getUrl()) .'</strong></label>';
                            $form .= '<button type="button" class="plusButton" name="'. utf8_decode($location->getUrl()) .'" onClick="window.open(this.name)">Ver Mapa</button>';
                        }
                        $form .= '</p>';
				
		}
		else{$form = "";}
	
		echo utf8_encode($form);
	}
	/********************* TERMINAN FUNCIONES PARA AGREGAR LOCACIONES A LOS DETALLES DE LA OPERACION ****************************/
	
	/********************* INICIAN FUNCIONES PARA AGREGAR CONDUCTORES A LOS DETALLES DE LA OPERACION ****************************/
	function FormDataDriverAction(){
            
            $this->view->setUseTemplate(FALSE);
	
            $post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO
	
            if($post['param'] != 0){
                $comp="";
		if(isset($post['comp'])){$comp='../';}
                $em = $this->getEntityManager('DefaultDb');
                $driver = $em->getRepository('DefaultDb_Entity_Driver2')->findOneBy(array('id' => $post['param']), array('id' => 'ASC'));
                $driver_data = $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $driver->getCompanyData()));
                $documents = $em->getRepository('DefaultDb_Entity_Document')->findBy(array('company' => $driver->getCompanyData()));
                $listdoc = "";
                if(count($documents) > 0){
                    foreach($documents as $doc){
                        if($doc->getPath() != ""){
                            $listdoc .= "<a href='../../../".$comp.$doc->getPath()."' class='btn btn-secondary' target='_blank'>".$doc->getName()."<a/><br /><br />";
                        }
                    } 	
                }
                        
                $form ="";
                if($driver_data->getLogo() == ""){
                    $form .= '<br /><div><input name="filesP" id="filesP" type="file" accept="image/*"/></div>';
                }

                $form .= '<div id="flotante">';
                $form .= '<div id="A"><img src="../../../'.$comp.$driver_data->getLogo().'" class="img-responsive" style="width:40%;" alt=""></div>';
                $form .= '<div id="B">'.$listdoc.'</div>';
                $form .= '</div>';
                $form .= '<p>';
                $form .= '<label>RFC:</label><label><strong>'. $driver->getRFC().'</strong></label>';
                $form .= '</p>';
                $form .= '<p>';
                $form .= '<label>Nombre(s):</label><label><strong>'. utf8_decode($driver->getNames()).'</strong></label>';
                $form .= '<label>Apellido(s):</label><label><strong>'. utf8_decode($driver->getLastNames()).'</strong></label>';
                $form .= '<label>C&eacute;lular:</label><label><strong>'. $driver_data->getPhone().'</strong></label>';
                //$form .= '<label>Fecha de Nacimiento:</label> <input type="text" name="birth_date" id="birth_date" value="'.$driver->getBirthDate().'" disabled="disabled">';
                $form .= '</p>';
                $form .= '<p>';
                $form .= '<label>Licencia:</label><label><strong>'. $driver->getLicense().'</strong></label>';
                $form .= '<label>N&uacute;mero de Licencia:</label><label><strong>'. $driver->getLicenseNumber().'</strong></label>';
                $form .= '<label>Vigencia:</label><label><strong>'. $driver->getLicenseDuration().'</strong></label>';
                $form .= '</p>';
                $form .= '<p>';
                //$form .= '<label>Célular2:</label><input type="text" name="cell2" id="cell2" value="'. $driver->getCell2().'" disabled="disabled">';
                $form .= '</p>';
            }else{$form = "";}
            echo utf8_encode($form);
	}

	function CaptureDriverAction()
	{
		$this->view->setUseTemplate(FALSE);
	
		$em = $this->getEntityManager('DefaultDb');
		
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));
		
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO
		
		$form = '<p name="rfc_p">';
		$form .= '<label>RFC:</label> <input type="text" name="rfc" id="rfc" value="" placeholder="Ser&aacute; el usuario del Conductor">';
		//$form .= '<label>Contraseña:</label> <input type="password" name="pwd" id="pwd" value="123" readonly>(123 por default)';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>Nombre:</label> <input type="text" name="names" id="names" value="">';
		$form .= '<label>Apellido(s):</label> <input type="text" name="last_names" id="last_names" value="">';
		$form .= '<label>C&eacute;lular:</label><input type="text" name="cell" id="cell" value="">';
		//$form .= '<label>Fecha de Nacimiento:</label> <input type="text" name="birth_date" id="birth_date" value="">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>Licencia:</label> <input type="text" name="license" id="license" value="">';
		$form .= '<label>N&uacute;mero de Licencia:</label> <input type="text" name="lic_number" id="lic_number" value="">';
		$form .= '<label>Vigencia:</label> <input type="text" name="lic_duration" id="lic_duration" value="">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<!--label>C&eacute;lular2:</label--><input type="hidden" name="cell2" id="cell2" value="">';
		if($post['idprov'] != $company->getId()){
			$p= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $post['idprov']));
			$form .= '<div class="alert alert-warning" style="text-align:center;">Este conductor pertenecera al cat&aacute;logo de empleados de la empresa:'.$p->getBusinessName().'</div>';
			$form .= '<input type="hidden" name="relationship" id="relationship" value="2">';
		}else{				
			$form .= '<label>Relaci&oacute;n con '.$company->getBusinessName().':</label>
						<select name="relationship" id="relationship">
							<option value="">Seleccione una Opci&oacute;n</option>
							<option value="2">Contrato</option>
							<option value="20">Subcontrato</option>
						</select>';
		}
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<button type="button" class="saveButton"  onClick="save_driver()">Guardar Conductor</button>';
		//$form .= '<input type="button" value="Guardar Conductor" onClick="save_driver()">';
		$form .= '</p>';
	
		echo utf8_encode($form);
	}
	
	/********************* TERMINAN FUNCIONES PARA AGREGAR CONDUCTORES A LOS DETALLES DE LA OPERACION ****************************/
	
	/********************* INICIAN FUNCIONES PARA AGREGAR UNIDADES A LOS DETALLES DE LA OPERACION ****************************/
	function CaptureDataAction()
	{
		$this->view->setUseTemplate(FALSE);
	
		$em = $this->getEntityManager('DefaultDb');
		$vehiclet = $em->getRepository('DefaultDb_Entity_VehicleType')->findAll();
		$vehiclesubt = $em->getRepository('DefaultDb_Entity_VehicleSubtype')->findAll();
		//$comments = $em->getRepository('DefaultDb_Entity_Comment')->findAll();
	
		$combovehiclet = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
		foreach($vehiclet as $item)
		{
			$combovehiclet .= "<option value='".$item->getId()."'>".utf8_decode($item->getName())."</option> \n";
		}
	
		$combovehiclesubt = "<option value='0'>Seleccione una Opci&oacute;n</option> \n";
		foreach($vehiclesubt as $item)
		{
			$combovehiclesubt .= "<option value='".$item->getId()."'>".utf8_decode($item->getName())."</option> \n";
		}
	
		/*$combocomments = "<option value='0'>Seleccione una Opción</option> \n";
			foreach($comments as $item)
			{
		$combocomments .= "<option value='".$item->getId()."'>".$item->getComment()."</option> \n";
		}*/
	
		$form = '<p>';
		$form .= '<label>Marca:</label> <input type="text" name="brand" id="brand" value="" size="40">';
		$form .= '<label>Modelo:</label> <input type="text" name="model" id="model" value="" size="40">';
		$form .= '<label>Color:</label> <input type="text" name="color" id="color" value="" size="40">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>N&uacute;mero Econ&oacute;mico:</label> <input type="text" name="economic_n" id="economic_n" value="" size="40">';
		$form .= '<label>Placas:</label><input type="text" name="plates" id="plates" value="" size="40">';
		$form .= '<label>Placas del Remolque:</label> <input type="text" name="trailer_plates" id="trailer_plates" value="" size="40">';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>N&uacute;mero de Rastreo:</label><input type="text" name="tracking_n" id="traking_n" value="" size="40">';
		$form .= '<label>Tipo de Veh&iacute;culo:</label>
					<select name="type" id="type">';
		$form .= $combovehiclet;
		$form .='	</select>';
		$form .= '<label>Subtipo de Veh&iacute;culo:</label>
					<select name="subtype" id="subtype">';
		$form .= $combovehiclesubt;
		$form .='	</select>';
		$form .= '</p>';
	
		$form .= '<p>';
		$form .= '<label>Condici&oacute;n Mec&aacute;nica:</label>
					<select name="condition_mechanical" id="condition_mechanical">
						<option value="">Seleccione una Opci&oacute;n</option>
						<option value="No">No</option>
						<option value="Buena">Buena</option>
						<option value="Regular">Regular</option>
						<option value="Mala">Mala</option>
					</select>';
		$form .= '<label>Condici&oacute;n de Llantas:</label>
					<select name="condition_tires" id="condition_tires">
						<option value="0">Seleccione una Opci&oacute;n</option>
						<option value="1">Buena</option>
						<option value="2">Regular</option>
						<option value="3">Mala</option>
					</select>';
		$form .= '<label>VIN:</label> <input type="text" name="vin" id="vin" value="" size="40">';
		$form .= '</p>';
	
		$form .= '<p>';
		$form .= '<label>Condici&oacute;n de Lona:</label>
					<select name="condition_sailcloth" id="condition_sailcloth">
						<option value="">Seleccione una Opci&oacute;n</option>
						<option value="No">No</option>
						<option value="Buena">Buena</option>
						<option value="Regular">Regular</option>
						<option value="Mala">Mala</option>
					</select>';
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<label>Comentarios:</label> <input type="text" name="comments" id="comments" value="" size="40">';
		/*$form .= '<label>Comentarios:</label>
		 <select name="comments" id="comments">';
		$form .= $combocomments;
		$form .='	</select>';*/
		$form .= '</p>';
		$form .= '<p>';
		$form .= '<button type="button" class="saveButton"  onClick="save_unit()">Guardar Unidad</button>';
		$form .= '</p>';
	
		echo utf8_encode($form);
	}

	function FormDataAction()//FUNCION PARA GENERAR UNA VENTANA EMERGENTE DONDE SE CAPTUREN LOS DETALLES DE LA OPERACIÓN
	{
		$this->view->setUseTemplate(FALSE);
	
		$post= $this->getRequest()->getPost();//VARIABLE QUE TOMA EL ID DEL REGISTRO QUE SE DESEA BORRAR
	
		if($post['param'] != 0)
		{
			$em = $this->getEntityManager('DefaultDb');
			$unit = $em->getRepository('DefaultDb_Entity_Unit')->findOneBy(array('id' => $post['param']), array('id' => 'ASC'));
	
			$datamax = $em->getRepository('DefaultDb_Entity_UnitKilometer')->getMaxKm($unit->getId());
			
			if(count($datamax) > 0){
				$remaintraveled = floatval($datamax[0]['remain_service']) - (floatval($datamax[0]['quantity']) - floatval($datamax[0]['last_service']));
			}else{$remaintraveled ="";}
			
			//$form = '<p><input id="remainkm" type="hidden" value="'.$unit->getRemain().'">';
			$form = '<p><input id="remainkm" type="hidden" value="'.$remaintraveled.'">';
			$form .= '<label>Marca:</label> <input type="text" name="brand" id="brand" value="'. $unit->getBrand().'" disabled="disabled" />';
			$form .= '<label>Modelo:</label> <input type="text" name="model" id="model" value="'. $unit->getModel().'" disabled="disabled" />';
			$form .= '<label>Color:</label> <input type="text" name="color" id="color" value="'. $unit->getColor().'" disabled="disabled" />';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>N&uacute;mero Econ&oacute;mico:</label><input type="text" name="economic_number" id="economic_number" value="'. $unit->getEconomicNum().'" disabled="disabled" />';
			$form .= '<label>Placas:</label><input type="text" name="plates" id="plates" value="'. $unit->getPlates().'" disabled="disabled" />';
			$form .= '<label>Placas de Remolque:</label><input type="text" name="trailer_plates" id="trailer_plates" value="'. $unit->getTrailerPlates().'" disabled="disabled" />';
			$form .= '</p>';
	
			$type = $em->getRepository('DefaultDb_Entity_VehicleType')->findOneBy(array('id' => $unit->getVehiclet()));
			$typedes = ($type != null) ? $type->getName() : "";
			$subtype = $em->getRepository('DefaultDb_Entity_VehicleSubtype')->findOneBy(array('id' => $unit->getVehiclest()));
			$subtypedes = ($subtype != null) ? $subtype->getName() : "";
			//$comment = $em->getRepository('DefaultDb_Entity_Comment')->findOneBy(array('id' => $unit->getComments()));
			
			switch ($unit->getConditionTires())
			{
				case '1':
					$tires = "Buena";
					break;
	
				case '2':
					$tires = "Regular";
					break;
	
				case '3':
					$tires = "Mala";
					break;
				default:
					$tires = "";	
					
			}
	
			$form .= '<p>';
			$form .= '<label>N&uacute;mero de Rastreo:</label><input type="text" name="traking_number" id="traking_number" value="'.$unit->getTrackinN().'" disabled="disabled" />';
			$form .= '<label>Tipo de Veh&iacute;culo:</label><input type="text" name="unit_type" id="unit_type" value="'. $typedes.'" disabled="disabled">';
			$form .= '<label>Subtipo de Veh&iacute;culo:</label><input type="text" name="unit_subtype" id="unit_subtype" value="'. $subtypedes .'" disabled="disabled">';
			$form .= '</p>';
	
			$form .= '<p>';
			$form .= '<label>VIN:</label></label><input type="text" name="vin" id="vin" value="'. $unit->getVin().'" disabled="disabled">';
			$form .= '<label>Condici&oacute;n Mec&aacute;nica:</label></label><input type="text" name="condition_mechanical" id="condition_mechanical" value="'. $unit->getConditionMec().'" disabled="disabled">';
			$form .= '<label>Condici&oacute;n de Llantas:</label><input type="text" name="condition_tires" id="condition_tires" value="'. $tires.'" disabled="disabled">';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Condici&oacute;n de Lona:</label></label><input type="text" name="condition_sailcloth" id="condition_sailcloth" value="'.$unit->getConditionSailcloth().'" disabled="disabled">';
			$form .= '</p>';
			$form .= '<p>';
			$form .= '<label>Comentarios:</label><input type="text" name="comments" id="comments" value="'. $unit->getComments().'" disabled="disabled">';
			$form .= '</p>';
		}
		else{$form = "";}
	
		echo utf8_encode($form);
	}	
	/********************* TERMINAN FUNCIONES PARA AGREGAR UNIDADES A LOS DETALLES DE LA OPERACION ****************************/

	
	/*************************** INICIA FUNCION PARA GUARDAR O MODIFICAR LOS DETALLES DE UNA OPERACION *****************************/
	
	public function saveTExpenseDetailAction()//FUNCION QUE PERMITE GUARDAR LOS DATOS DE UN NUEVO REGISTRO
	{
		$this->view->setUseTemplate(FALSE);
		header("Content-Type: application/json");
			
		$post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
	
		//var_dump($post);
		
		$em = $this->getEntityManager('DefaultDb');
	
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));
	
		$item= (isset($post['action'])) ? $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id' =>$post['action'])) : $item = new DefaultDb_Entity_TransportOperationDetail();
	
		$em->getConnection()->beginTransaction();//SE COMIENZA CON EL PROCESO DE GUARDAR LOS NUEVOS DATOS
		try
		{
			if(!isset($post['action']))
			{
				$item->setDate(date('Y-m-d H:m:s'));
				$item->setOperation($post['transportop']);
				$item->setProvider($post['company_prov']);
				$item->setCustomer($post['customer']);
				$item->setUser($user->getId());
				$item->setCompany($company->getId());
			}
			$item->setUnit(($post['unit'] != 0)?$post['unit']:null);
			$item->setDriver(($post['driver'] != 0)?$post['driver']:null);
			
			$item->setSource(($post['source'] != 0)?$post['source']:null);
			$item->setEdt(($post['edt'])!=""?$post['edt']:'00:00:00');
			$item->setEdd(($post['edd'])!=""?$post['edd']:'0000-00-00');
			$item->setRtd(($post['rtd'])!=""?$post['rtd']:'00:00:00');
			$item->setRdd(($post['rdd'])!=""?$post['rdd']:'0000-00-00');
			$item->setErt(($post['ert'])!=""?$post['ert']:'00:00:00');
			$item->setErd(($post['erd'])!=""?$post['erd']:'0000-00-00');
			$item->setRrt(($post['rrt'])!=""?$post['rrt']:'00:00:00');
			$item->setRrd(($post['rrd'])!=""?$post['rrd']:'0000-00-00');
																
			$item->setDestination(($post['destination'] != 0)?$post['destination']:null);
			$item->setEta(($post['eta'])!=""?$post['eta']:'00:00:00');
			$item->setEda(($post['eda'])!=""?$post['eda']:'0000-00-00');
			$item->setRta(($post['rta'])!=""?$post['rta']:'00:00:00');
			$item->setRda(($post['rda'])!=""?$post['rda']:'0000-00-00');
			$item->setEte(($post['ete'])!=""?$post['ete']:'00:00:00');
			$item->setEde(($post['ede'])!=""?$post['ede']:'0000-00-00');
			$item->setRte(($post['rte'])!=""?$post['rte']:'00:00:00');
			$item->setRde(($post['rde'])!=""?$post['rde']:'0000-00-00');
			
			/*$item->setDistribution1(($post['distribution1'] != 0)?$post['distribution1']:null);
			$item->setEtad1(($post['etad1'])!=""?$post['etad1']:'00:00:00');
			$item->setRtad1(($post['rtad1'])!=""?$post['rtad1']:'00:00:00');
			$item->setEted1(($post['eted1'])!=""?$post['eted1']:'00:00:00');
			$item->setRted1(($post['rted1'])!=""?$post['rted1']:'00:00:00');
			$item->setEdad1(($post['edad1'])!=""?$post['edad1']:'0000-00-00');
			$item->setRdad1(($post['rdad1'])!=""?$post['rdad1']:'0000-00-00');
			$item->setEded1(($post['eded1'])!=""?$post['eded1']:'0000-00-00');
			$item->setRded1(($post['rded1'])!=""?$post['rded1']:'0000-00-00');
								
			$item->setDistribution2(($post['distribution2'] != 0)?$post['distribution2']:null);
			$item->setEtad2(($post['etad2'])!=""?$post['etad2']:'00:00:00');
			$item->setRtad2(($post['rtad2'])!=""?$post['rtad2']:'00:00:00');
			$item->setEted2(($post['eted2'])!=""?$post['eted2']:'00:00:00');
			$item->setRted2(($post['rted2'])!=""?$post['rted2']:'00:00:00');
			$item->setEdad2(($post['edad2'])!=""?$post['edad2']:'0000-00-00');
			$item->setRdad2(($post['rdad2'])!=""?$post['rdad2']:'0000-00-00');
			$item->setEded2(($post['eded2'])!=""?$post['eded2']:'0000-00-00');
			$item->setRded2(($post['rded2'])!=""?$post['rded2']:'0000-00-00');*/
				
			$item->setReceiver($post['receiver']);
				
			$em->persist($item);
			$em->flush();
			if(isset($post['kilometers'])){
				if($post['kilometers'] != "")
				{
					$kilometers = $em->getRepository('DefaultDb_Entity_UnitKilometer')->findOneBy(array('operation' => $post['transportop'], 'type' => 1));
					if($kilometers == null)
					{
						$kilometer = new DefaultDb_Entity_UnitKilometer();
						
						$kilometer->setCompany($item->getCompany());
						$kilometer->setDate(date('Y-m-d H:m:s'));
						$kilometer->setOperation($post['transportop']);
						$kilometer->setQuantity($post['kilometers']);
						$kilometer->setUnit($post['unit']);
						$kilometer->setUser($item->getUser());
						$kilometer->setType(1);
							
						
						$em->persist($kilometer);
						$em->flush();
					}
					else 
					{
						$kilometers->setQuantity($post['kilometers']);
						$em->persist($kilometers);
						$em->flush();
							
					}
				}
			}
	
			//$unit= $em->getRepository('DefaultDb_Entity_Unit')->findOneBy(array('id' => $item->getUnit()));//CONSULTA A LA TABLA providers TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA operating_expenses
	
			$arrJson[] = array(//ARREGLO QUE ALMACENA LOS DATOS SOLICITADOS
					"idTransportOpdetail" => $item->getId(),
					//"unit" => $unit->getModel(),
					"update" => substr($item->getDate(),0,10),
					"operation" => $item->getOperation(),
					"user" => $item->getUser(),
					"company" => $item->getCompany()
			);
	
			$em->getConnection()->commit();
		}
		catch(Exception $e)//EXCEPCIÓN PARA CUANDO HAY ALGUN ERROR AL GUARDAR LA NUEVA INFORMCIÓN
		{
			$em->getConnection()->rollback();
			$em->close();
			echo "errorsote";
			$arrJson[] = array("result" => $e->getMessage());
		}
		$this->view->result = json_encode($arrJson);//FORMATEO DEL ARREGLO QUE CONTIENE LA INFORMACIÓN SOLICITADA DE LA BASE DE DATOS YA QUE KENDO SOLICITA COMO RESPUESTA UN ARREGLO EN FORMATO JSON PARA MOSTRAR LA INFORMACIÓN EN LA TABLA DE LA INTERFAZ
	}

	/*************************** TERMINA FUNCION PARA GUARDAR O MODIFICAR LOS DETALLES DE UNA OPERACION *****************************/
 function tracking_dataAction(){
        	
	error_reporting(E_ALL ^ E_WARNING);
    
    $this->view->setUseTemplate(FALSE);
    $em = $this->getEntityManager('DefaultDb');        
    $post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
	$idop = $post['idop'];
      		        
    //$item = new DefaultDb_Entity_CompanyTracking();  
       
    $arrJson = array();                 
    $lista = $em->getRepository('DefaultDb_Entity_CompanyTracking')->getDataCom($idop);      
      //var_dump($lista);
    foreach($lista as $item){
    	$arrJson[] = array(
        	"typeComunication" =>$item['typeComunication'],
            "operationId" => $item['operationId'],                                        
            "trackingCompanyId" => $item['trackingCompanyId'],
            "imeI"=>$item['imeI'],
             //"tokenId"=>$item['tokenId'],
            "masterkId"=>$item['masterkId'],      
            "unitId"=>$item['unitId']       
   		);
        //echo $lista[$item['unitId']]; 
            
		switch($item['typeComunication']){    
  			case "Curl":   
  				define('MULTIPART_BOUNDARY','--------------------------'.microtime(true));
				$header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
				define('FORM_FIELD', 'uploaded_file'); 
				$filename = "../public/webfile/m4wss100.xml";
				$file_contents = file_get_contents($filename); 
				$destination = "http://www.tecnologistikgps.com/wss/m4wss10.php";  

				$content =  "--".MULTIPART_BOUNDARY."\r\n".
				            "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
				            "Content-Type: application/zip\r\n\r\n".
				            $file_contents."\r\n";

				// add some POST fields to the request too: $_POST['foo'] = 'bar'
				$content .= "--".MULTIPART_BOUNDARY."\r\n".
				            "Content-Disposition: form-data; name=\"foo\"\r\n\r\n".
				            "bar\r\n";

				// signal end of request (note the trailing "--")
				$content .= "--".MULTIPART_BOUNDARY."--\r\n";

				$context = stream_context_create(array(
				    'http' => array(
				          'method' => 'POST',
				          'header' => $header,
				          'content' => $content,
				    )
				));

				$xml = file_get_contents($destination, false, $context);
				//echo $xml;
				$array = preg_split("/-?[^0-9.|^0-9]+/","$xml");
				$arraylat=$array[13]/1000;

				$arrayInicio=substr($arraylat,0,2);
				$arrayIniciofloat= floatval($arrayInicio);

				$arrayFin=substr($arraylat, 2);
				$arrayFin/60;
				$arrayLatitud=$arrayInicio." ".$arrayFin;
				$arrayFinFloat= floatval($arrayFin);
				$arrayFinFloat = $arrayFinFloat/60;

				if($arrayIniciofloat>0){
	 	 	 		$arrayIniciofloat=$arrayIniciofloat+$arrayFinFloat;
   				}else{
    				$arrayIniciofloat=$arrayIniciofloat-$arrayFinFloat;   
    			}
   				//echo  $arrayIniciofloat;

				//tratamiento del array, para la parte de longitud que tendra el mapa
				$arrayLon=$array[14]/1000;

				$arrayLongInicio=substr($arrayLon,0, 3);

				$arrayLongIniciofloat= floatval($arrayLongInicio);

				$arrayLongFin=substr($arrayLon, 2);
				$arrayLongFinFloat= floatval($arrayLongFin);
				$arrayLongFinFloat = $arrayLongFinFloat/60;
				//echo "<br>";

    			$arrayLongIniciofloat=$arrayLongIniciofloat+$arrayLongFinFloat;

				$arrayLongitud=$arrayLongInicio." ".$arrayLongFin;

				//regreso de datos al formulario que contiene el esqueleto del mapa
				echo $arrayIniciofloat.'/-'.$arrayLongIniciofloat;

				//echo $arrayLatitud.'/-'.$arrayLongitud;
				//$lenght=$arrayLongitud;
				$latitude=$arrayIniciofloat;
				$lenght=$arrayLongIniciofloat;
				//$latitude=$arrayLongitud;
				
				$relationunit=$item['unitId'];
				
				//inicia proceso de Grabacion a BD
				$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
				$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO

				$item = new DefaultDb_Entity_TrackingUnit();
				$em->getConnection()->beginTransaction();

        		$item->setCurrentDate(date('Y-m-d H:m:s'));
		        $item->setLatitude($latitude);
		        $item->setLenght($lenght);
		        //$item->setStatus($status);
		        $item->setRelationunit($relationunit);

		        $em->persist($item);
		        $em->flush();

		        $em->getConnection()->commit();
		        //$em->getConnection()->rollback();
		        $em->close();
      		break;
   
			case "Soap":   
						//$url='http://www.gpsttc.mx/WS/WSTrack2.asmx?op=GetCurrentPositionByIMEIWithAddressAndDateLatLong';
				$url='http://cs.wialon.com/svcs/unitinfo';
	    
				$SecurityToken=$item['masterkId'];
				$imei=$item['imeI'];
				
				$xml = <<<EOD
				<?xml version='1.0' encoding='utf-8'?>
				<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
				  <soap:Body>
				    <GetUnitInfo xmlns='http://www.tempuri.org/'>
				      <SecurityToken>'$SecurityToken'</SecurityToken>
				      <imei>'$imei'</imei>
				    </GetUnitInfo>
				  </soap:Body>
				</soap:Envelope>
EOD;

				/*$header=array('POST /WS/WSTrack2.asmx HTTP/1.1','Host: www.gpsttc.mx',
				               'Content-Type: text/xml; charset=utf-8',
				               'Content-Length:'.strlen($xml),
				               'SOAPAction:"http://www.tempuri.org/GetCurrentPositionByIMEIWithAddressAndDateLatLong"');*/
				
				/*$header=array('POST /svcs/unitinfo HTTP/1.1','Host: cs.wialon.com',
				 'Content-Type: text/xml; charset=utf-8',
						'Content-Length:'.strlen($xml),
						'SOAPAction: "http://www.tempuri.org/GetUnitInfo"');
				
				$soap_do = curl_init();
				curl_setopt($soap_do, CURLOPT_URL,$url);
				curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
				curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
				curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($soap_do, CURLOPT_POST,true );            
				curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
				curl_setopt($soap_do, CURLOPT_VERBOSE, true);
				curl_setopt($soap_do,CURLINFO_HEADER_OUT,true);
				curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header); 
				$xmldata=curl_exec($soap_do);
				curl_close($soap_do);*/
				
				$data = array("token" => $SecurityToken,"imei" => $imei);
				//url contra la que atacamos
				$ch = curl_init($url);
				//a true, obtendremos una respuesta de la url, en otro caso,
				//true si es correcto, false si no lo es
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				//establecemos el verbo http que queremos utilizar para la petición
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				//enviamos el array data
				curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
				//obtenemos la respuesta
				$xmldata = curl_exec($ch);
				// Se cierra el recurso CURL y se liberan los recursos del sistema
				curl_close($ch);
				if(!$xmldata) {
					return false;
				}else{
					if(strlen($xmldata) > 33){
						//if ($xmldata == "OK") { echo 'se ejecuto algo '; } else { echo 'ni se ejecuto nada'; }
						$array1 = explode('<Latitude>',$xmldata);
						$array2 = explode('<Longitude>',$xmldata);
						
						$lat = substr($array1[1],0,strpos($array1[1],'</'));
						$long = substr($array2[1],0,strpos($array2[1],'</'));
						
						$array = preg_split("/-?[^0-9.|^0-9]+/","$xmldata");
					
						//var_dump($array);
						//echo $array[24].'/-'.$array[25];
						//echo $array[10].'/-'.$array[11];
						echo $lat.'/'.$long; 
						/*$latitude=$array[10];
						$lenght=$array[11];*/
						$relationunit=$item['unitId'];
						
						//inicia proceso de Grabacion en BD
						$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
						$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
						
						$item = new DefaultDb_Entity_TrackingUnit();
						$em->getConnection()->beginTransaction();
			
				        $item->setCurrentDate(date('Y-m-d H:m:s'));
				        /*$item->setLatitude($latitude);
				        $item->setLenght($lenght);*/
				        $item->setLatitude($lat);
				        $item->setLenght($long);
				        //$item->setStatus($status);
				        $item->setRelationunit($relationunit);
			
				        $em->persist($item);
				        $em->flush();
				
				        $em->getConnection()->commit();
				        //$em->getConnection()->rollback();
				        $em->close();          
					}                      
				}        
			break;
   
    		default:
       			exit();   
       		break;
    		//echo "Opcion no Valida";
    	}    
	} 
}
 /*
   public function sendTrackingMailAction()
         {
//     require ('../library/Model3/PHPMailerAutoload.php');

        $this->view->setUseTemplate(FALSE);
        $em = $this->getEntityManager('DefaultDb');        
        $post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
	$idop = $post['idop'];
        $type = $post['type'];
        $description = $post['description'];
        $companycust = $post['companycust'];
        $opdate=$post['opdate'];
        
        //var_dump($idop);
        //var_dump($companycust);
        //var_dump($type);
        //var_dump($description);                
        $item = new DefaultDb_Entity_CompanyTracking();
        $lista = $em->getRepository('DefaultDb_Entity_CompanyTracking')->getDataMail($idop);
        //echo $lista;
        //var_dump($lista);
      foreach($lista as $item)
        {
            $arrJson[] = array(
                         "typeCommunication" =>$item['typeCommunication'],
                         "operationId" => $item['operationId'],
                         "companycust" => $item['companycust'],
                         "description" => $item['description'],
                         "opdate" => $item['opdate'],                
                         "imeI"=>$item['imeI'],
                         "masterkId"=>$item['masterkId'],   
                         "trackingCompanyId" => $item['trackingCompanyId'],
                         "masterkId"=>$item['masterkId'],
                         "typeCommunication"=>$item[' typeCommunication'],                          
                         "email"=>$item['email'],
                         "notifications"=>$item['notifications'],                
                         //"tokenId"=>$item['tokenId'],                            
                         "unitId"=>$item['unitId']       
                );
             $email=$item['email'];
             $description=$item['description'];
             $companycust=$item['companycust'];
             $opdate=$item['opdate'];
             
        //echo $email; 
            
switch($item['typeCommunication'])
 {    
  case "Curl":   
  define('MULTIPART_BOUNDARY','--------------------------'.microtime(true));
$header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
define('FORM_FIELD', 'uploaded_file'); 
$filename = "../webfile/m4wss100.xml";
$file_contents = file_get_contents($filename); 
$destination = "http://www.tecnologistikgps.com/wss/m4wss10.php";  

$content =  "--".MULTIPART_BOUNDARY."\r\n".
            "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
            "Content-Type: application/zip\r\n\r\n".
            $file_contents."\r\n";

// add some POST fields to the request too: $_POST['foo'] = 'bar'
$content .= "--".MULTIPART_BOUNDARY."\r\n".
            "Content-Disposition: form-data; name=\"foo\"\r\n\r\n".
            "bar\r\n";

// signal end of request (note the trailing "--")
$content .= "--".MULTIPART_BOUNDARY."--\r\n";

$context = stream_context_create(array(
    'http' => array(
          'method' => 'POST',
          'header' => $header,
          'content' => $content,
    )
));

$xml = file_get_contents($destination, false, $context);
//echo $xml;
$array = preg_split("/-?[^0-9.|^0-9]+/","$xml");
//echo $array;
$arraylat=$array[13]/1000;
//echo $arraylat;
//echo "<br>";

$arrayInicio=substr($arraylat,0,2);
//echo $arrayInicio;
//echo "<br>";


$arrayIniciofloat= floatval($arrayInicio);
//echo $arrayIniciofloat;
//echo"<br>";

$arrayFin=substr($arraylat, 2);
$arrayFin/60;
$arrayLatitud=$arrayInicio." ".$arrayFin;
$arrayFinFloat= floatval($arrayFin);
$arrayFinFloat = $arrayFinFloat/60;

if($arrayIniciofloat>0)
{
   $arrayIniciofloat=$arrayIniciofloat+$arrayFinFloat;
   
}
else
    {
    $arrayIniciofloat=$arrayIniciofloat-$arrayFinFloat;   
    
    }   
$arrayLon=$array[14]/1000;

$arrayLongInicio=substr($arrayLon,0, 3);

$arrayLongIniciofloat= floatval($arrayLongInicio);
//echo $arrayLongIniciofloat;



$arrayLongFin=substr($arrayLon, 2);
$arrayLongFinFloat= floatval($arrayLongFin);
$arrayLongFinFloat = $arrayLongFinFloat/60;


    $arrayLongIniciofloat=$arrayLongIniciofloat+$arrayLongFinFloat;
    //$arrayLongitud=$arrayLongIniciofloat;    
     //echo $arrayLongIniciofloat;




$arrayLongitud=$arrayLongInicio." ".$arrayLongFin;

//regreso de datos al formulario que contiene el esqueleto del mapa
$arrayIniciofloat.'/-'.$arrayLongIniciofloat;

$latitude=$arrayIniciofloat;
$lenght=$arrayLongIniciofloat;
$marker="&zoom=12&size=640x6400&markers=color:blue%7Clabel:S%7C$latitude,-$lenght";


$correo='<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head><body bgcolor="#F5F5F5" leftmargin="18px" topmargin="10px" rightmargin="10px" bottommargin="10px">
            <h3 style="color:#AF080F;text-align:left;">:::::: Notificaci&oacute;n de  de MasFletes.com ::::::</h3><p style="font-family:Arial;font-size:12px;line-height:16px;">
            <strong>Estimado '.$companycust.':<br /><br />Se te ha enviado la ubicacion de tu unidad segun la Operacion <strong>(No. '. $idop.')</strong> que es un <strong>'.$type.'</strong> que Solicitaste.<br />
            <strong>'.$description.' en la fecha '.$opdate.'.</strong>
            <br/>Ubicacion rapida.&nbsp;
            <a href="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.','.-$lenght.''.$marker.'" "target="_blank">Google Maps</a><br>                               
            <br />Inicia sesi&oacute;n en el Sistema para  m&aacute;s detalles.&nbsp;
            <a href="backend.masfletes.com" target="_blank">masfletes.com</a><br></br><br>
            <br/><br/> No olvides que esta informaci&oacute;n tambien estar&aacute; disponible en tu panel dentro de tu sesi&oacute;n.</p></body></html>';
    
    $emailAdd="";
    //$email=""; 
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mail.masdistribucion.com.mx';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "admin@masdistribucion.com.mx";
    $mail->Password = "distribucion2900";
    $mail->From = "administrador@masfletes.com";
    $mail->FromName = "Notificaciones De MasFletes.Com";
    $mail->AddAddress($email,"");
    if($emailAdd != ""){ $mail->AddBCC(''.$emailAdd.'',"Usuario Mas Fletes"); }
    $mail->Subject = "Notificaciones Operaciones de Flete";
    $mail->MsgHTML($correo);
    $mail->Send();
    exit(); 
    break;
        
   
case "Soap":   
$url='http://www.gpsttc.mx/WS/WSTrack2.asmx?op=GetCurrentPositionByIMEIWithAddressAndDateLatLong';

    
$SecurityToken=$item['masterkId'];
$imei=$item['imeI'];
$xml = <<<EOD
<?xml version='1.0' encoding='utf-8'?>
<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
  <soap:Body>
    <GetCurrentPositionByIMEIWithAddressAndDateLatLong xmlns='http://www.tempuri.org/'>
      <SecurityToken>$SecurityToken</SecurityToken>
      <IMEI>$imei</IMEI>
    </GetCurrentPositionByIMEIWithAddressAndDateLatLong>
  </soap:Body>
</soap:Envelope>
EOD;


$header=array('POST /WS/WSTrack2.asmx HTTP/1.1','Host: www.gpsttc.mx',
               'Content-Type: text/xml; charset=utf-8',
               'Content-Length:'.strlen($xml),
               'SOAPAction:"http://www.tempuri.org/GetCurrentPositionByIMEIWithAddressAndDateLatLong"');
    
$soap_do = curl_init();
curl_setopt($soap_do, CURLOPT_URL,$url);
curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($soap_do, CURLOPT_POST,true );            
curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
curl_setopt($soap_do, CURLOPT_VERBOSE, true);
//curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8','POST /WS/WSTrack2.asmx HTTP/1.1', 'Host: www.gpsttc.mx','Content-Length: lenght','SOAPAction:"http://www.tempuri.org/GetCurrentPositionByIMEIWithAddressAndDateLatLong"'));  
curl_setopt($soap_do,CURLINFO_HEADER_OUT,true);
curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header); 
$xmldata=curl_exec($soap_do);
curl_close($soap_do);

$array = preg_split("/-?[^0-9.|^0-9]+/","$xmldata");

echo $array[24].'/-'.$array[25];
$latitude=$array[24];
$lenght=$array[25];
$marker="&zoom=14&size=640x6400&markers=color:blue%7Clabel:S%7C$latitude,-$lenght";

    $correo='<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head><body bgcolor="#F5F5F5" leftmargin="18px" topmargin="10px" rightmargin="10px" bottommargin="10px">
            <h3 style="color:#AF080F;text-align:left;">:::::: Notificaci&oacute;n de  de MasFletes.com ::::::</h3><p style="font-family:Arial;font-size:12px;line-height:16px;">
            <strong>Estimado '.$companycust.':<br /><br />Se te ha enviado la ubicacion de tu unidad segun la Operacion <strong>(No. '. $idop.')</strong> que es un <strong>'.$type.'</strong> que Solicitaste.<br />
            <strong>'.$description.' en la fecha '.$opdate.'.</strong><br><br>
            <br />Ubicacion rapida.&nbsp;
            <a href="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.','.-$lenght.''.$marker.'" target="_blank">Google Maps</a><br>         
            <br />Inicia sesi&oacute;n en el Sistema para  m&aacute;s detalles.&nbsp;
            <a href="backend.masfletes.com" target="_blank">masfletes.com</a><br></br>
            <br/><br/> No olvides que esta informaci&oacute;n tambien estar&aacute; disponible en tu panel dentro de tu sesi&oacute;n.</p></body></html>';           
        
    $emailAdd="";
    //$email=""; 
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mail.masdistribucion.com.mx';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "admin@masdistribucion.com.mx";
    $mail->Password = "distribucion2900";
    $mail->From = "administrador@masfletes.com";
    $mail->FromName = "Notificaciones De MasFletes.Com";
    $mail->AddAddress($email,"");
    if($emailAdd != ""){ $mail->AddBCC(''.$emailAdd.'',"Usuario Mas Fletes"); }
    $mail->Subject = "Notificaciones Operaciones de Flete";
    $mail->MsgHTML($correo);
    $mail->Send();
    break;       
default:
   echo "Opcion no Valida";
    break;
    exit();
   }                  
   }
   }
  */   
  public function saveNotificationAction(){
        $this->view->setUseTemplate(FALSE);
        //header("Content-Type: application/json");
        $em = $this->getEntityManager('DefaultDb'); 
        
        $post = $this->getRequest()->getPost();//VARIABLE QUE TOMA LOS VALORES ENVIDADOS DESDE EL FORMULARIO DE CAPTURA
	$id = $post['mail'];
        $idop = $post['idOper'];
        //echo $idop;
        $mail=$id;
        //echo $mail;
       
                        
        //echo "soy un $notifications";        
      $item = new DefaultDb_Entity_CompanyTracking();                               
      $lista = $em->getRepository('DefaultDb_Entity_CompanyTracking')->getDataCom($idop);      
     $arrJson = array();
     //print_r($lista);
      foreach($lista as $item)
        {
            $arrJson[] = array(
                         "operationId"=>$item['operationId'],
                         "idDetails" =>$item['idDetails']                         
                );
            $id_operation=$item['operationId'];
            $idDetails=$item['idDetails'];        
        }
        
        
        $lista2 = $em->getRepository('DefaultDb_Entity_CompanyTracking')->setDetailTransportOperations($id_operation,$idDetails,$mail);                                         
        
        $user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO       
        $company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO

        //$item2 = new DefaultDb_Entity_TransportOperationDetail();
        $operationdet=$em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findBy(array('id_operation' => $idop));       	        
                                                                               
    }
public function sendMailOperationDetailAction()
     {
        $this->view->setUseTemplate(FALSE);
        //header("Content-Type: application/json");
        $em = $this->getEntityManager('DefaultDb'); 
        
       $item = new DefaultDb_Entity_CompanyTracking();
       $lista = $em->getRepository('DefaultDb_Entity_CompanyTracking')->notificationsMails();
        var_dump($lista);  
        $arrJson = array();
       foreach($lista as $item)
        {
            $arrJson[] = array(
                
                         "operationId" => $item['operationId'],
                         "operetationDetail"=>$item['operetationDetail'],
                         "companycust" => $item['companycust'],
                         "tipoOperacion"=>$item['tipoOperacion'],
                         "description" => $item['description'],
                         "opdate" => $item['opdate'],                
                         "imeI"=>$item['imeI'],
                         "masterkId"=>$item['masterkId'],   
                         "trackingCompanyId" => $item['trackingCompanyId'],
                         "masterkId"=>$item['masterkId'],
                         "typeCommunication"=>$item['typeCommunication'],                          
                         "mail"=>$item['mail'],                
                         "tokenId"=>$item['tokenId']                                                           
                );
             $idop=$item['operationId'];
             $type=$item['tipoOperacion'];
             $email=$item['mail'];
             $description=$item['description'];
             $companycust=$item['companycust'];
             $opdate=$item['opdate']; 
             
            
switch($item['typeCommunication'])
 {    
  case "Curl":   
  define('MULTIPART_BOUNDARY','--------------------------'.microtime(true));
$header = 'Content-Type: multipart/form-data; boundary='.MULTIPART_BOUNDARY;
define('FORM_FIELD', 'uploaded_file'); 
$filename = "../webfile/m4wss100.xml";
$file_contents = file_get_contents($filename); 
$destination = "http://www.tecnologistikgps.com/wss/m4wss10.php";  

$content =  "--".MULTIPART_BOUNDARY."\r\n".
            "Content-Disposition: form-data; name=\"".FORM_FIELD."\"; filename=\"".basename($filename)."\"\r\n".
            "Content-Type: application/zip\r\n\r\n".
            $file_contents."\r\n";

// add some POST fields to the request too: $_POST['foo'] = 'bar'
$content .= "--".MULTIPART_BOUNDARY."\r\n".
            "Content-Disposition: form-data; name=\"foo\"\r\n\r\n".
            "bar\r\n";

// signal end of request (note the trailing "--")
$content .= "--".MULTIPART_BOUNDARY."--\r\n";

$context = stream_context_create(array(
    'http' => array(
          'method' => 'POST',
          'header' => $header,
          'content' => $content,
    )
));

$xml = file_get_contents($destination, false, $context);
//echo $xml;
$array = preg_split("/-?[^0-9.|^0-9]+/","$xml");
//echo $array;
$arraylat=$array[13]/1000;
//echo $arraylat;
//echo "<br>";

$arrayInicio=substr($arraylat,0,2);
//echo $arrayInicio;
//echo "<br>";


$arrayIniciofloat= floatval($arrayInicio);
//echo $arrayIniciofloat;
//echo"<br>";

$arrayFin=substr($arraylat, 2);
$arrayFin/60;
$arrayLatitud=$arrayInicio." ".$arrayFin;
$arrayFinFloat= floatval($arrayFin);
$arrayFinFloat = $arrayFinFloat/60;

if($arrayIniciofloat>0)
{
   $arrayIniciofloat=$arrayIniciofloat+$arrayFinFloat;
   
}
else
    {
    $arrayIniciofloat=$arrayIniciofloat-$arrayFinFloat;   
    
    }   
$arrayLon=$array[14]/1000;

$arrayLongInicio=substr($arrayLon,0, 3);

$arrayLongIniciofloat= floatval($arrayLongInicio);
//echo $arrayLongIniciofloat;



$arrayLongFin=substr($arrayLon, 2);
$arrayLongFinFloat= floatval($arrayLongFin);
$arrayLongFinFloat = $arrayLongFinFloat/60;


    $arrayLongIniciofloat=$arrayLongIniciofloat+$arrayLongFinFloat;
    //$arrayLongitud=$arrayLongIniciofloat;    
     //echo $arrayLongIniciofloat;




$arrayLongitud=$arrayLongInicio." ".$arrayLongFin;

//regreso de datos al formulario que contiene el esqueleto del mapa
$arrayIniciofloat.'/-'.$arrayLongIniciofloat;

$latitude=$arrayIniciofloat;
$lenght=$arrayLongIniciofloat;
$marker="&zoom=12&size=640x6400&markers=color:blue%7Clabel:S%7C$latitude,-$lenght";


$correo='<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head><body bgcolor="#F5F5F5" leftmargin="18px" topmargin="10px" rightmargin="10px" bottommargin="10px">
            <h3 style="color:#AF080F;text-align:left;">:::::: Notificaci&oacute;n de  de MasFletes.com ::::::</h3><p style="font-family:Arial;font-size:12px;line-height:16px;">
            <strong>Estimado '.$companycust.':<br /><br />Se te ha enviado la ubicacion de tu unidad segun la Operacion <strong>(No.'.$idop.')</strong> que es un <strong>'.$type.'</strong> que Solicitaste.<br />
            <strong>'.$description.' en la fecha '.$opdate.'.</strong>
           <br/>Ubicacion rapida.&nbsp;
            <a href="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.','.-$lenght.''.$marker.'" target="_blank">Google Maps</a><br>         
            <br>Vista Completa en Google Maps.&nbsp;
            <a href="https://www.google.com.mx/maps/@'.$latitude.','.-$lenght.',15z "target="_blank">Google Maps Ubicacion.</a></br><br>
            <br>Inicia sesi&oacute;n en el Sistema para  m&aacute;s detalles.&nbsp;
            <a href="backend.masfletes.com" target="_blank">masfletes.com</a></br>
            <br/><br/> No olvides que esta informaci&oacute;n tambien estar&aacute; disponible en tu panel dentro de tu sesi&oacute;n.</p></body></html>';
    
    $emailAdd="";
    //$email=""; 
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mail.masdistribucion.com.mx';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "admin@masdistribucion.com.mx";
    $mail->Password = "distribucion2900";
    $mail->From = "administrador@masfletes.com";
    $mail->FromName = "Notificaciones De MasFletes.Com";
    $mail->AddAddress($email,"");
    if($emailAdd != ""){ $mail->AddBCC(''.$emailAdd.'',"Usuario Mas Fletes"); }
    $mail->Subject = "Notificaciones Operaciones de Flete";
    $mail->MsgHTML($correo);
    $mail->Send();
    
    exit(); 
    break;
        
   
case "Soap":   
$url='http://www.gpsttc.mx/WS/WSTrack2.asmx?op=GetCurrentPositionByIMEIWithAddressAndDateLatLong';

    
$SecurityToken=$item['masterkId'];
$imei=$item['imeI'];
$xml = <<<EOD
<?xml version='1.0' encoding='utf-8'?>
<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
  <soap:Body>
    <GetCurrentPositionByIMEIWithAddressAndDateLatLong xmlns='http://www.tempuri.org/'>
      <SecurityToken>$SecurityToken</SecurityToken>
      <IMEI>$imei</IMEI>
    </GetCurrentPositionByIMEIWithAddressAndDateLatLong>
  </soap:Body>
</soap:Envelope>
EOD;


$header=array('POST /WS/WSTrack2.asmx HTTP/1.1','Host: www.gpsttc.mx',
               'Content-Type: text/xml; charset=utf-8',
               'Content-Length:'.strlen($xml),
               'SOAPAction:"http://www.tempuri.org/GetCurrentPositionByIMEIWithAddressAndDateLatLong"');
    
$soap_do = curl_init();
curl_setopt($soap_do, CURLOPT_URL,$url);
curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($soap_do, CURLOPT_TIMEOUT,        10);
curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($soap_do, CURLOPT_POST,true );            
curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
curl_setopt($soap_do, CURLOPT_VERBOSE, true);
//curl_setopt($soap_do, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset=utf-8','POST /WS/WSTrack2.asmx HTTP/1.1', 'Host: www.gpsttc.mx','Content-Length: lenght','SOAPAction:"http://www.tempuri.org/GetCurrentPositionByIMEIWithAddressAndDateLatLong"'));  
curl_setopt($soap_do,CURLINFO_HEADER_OUT,true);
curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header); 
$xmldata=curl_exec($soap_do);
curl_close($soap_do);

$array = preg_split("/-?[^0-9.|^0-9]+/","$xmldata");

echo $array[24].'/-'.$array[25];
$latitude=$array[24];
$lenght=$array[25];
$marker="&zoom=14&size=640x6400&markers=color:blue%7Clabel:S%7C$latitude,-$lenght";

    $correo='<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head><body bgcolor="#F5F5F5" leftmargin="18px" topmargin="10px" rightmargin="10px" bottommargin="10px">
            <h3 style="color:#AF080F;text-align:left;">:::::: Notificaci&oacute;n de  de MasFletes.com ::::::</h3><p style="font-family:Arial;font-size:12px;line-height:16px;">
            <strong>Estimado '.$companycust.':<br /><br />Se te ha enviado la ubicacion de tu unidad segun la Operacion <strong>(No. '.$idop.')</strong> que es un <strong>'.$type.'</strong> que Solicitaste.<br />
            <strong>'.$description.' en la fecha '.$opdate.'.</strong><br><br>
            <br />Ubicacion rapida.&nbsp;
            <a href="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.','.-$lenght.''.$marker.'" target="_blank">Google Maps</a><br>         
            <br>Vista Completa en Google Maps.&nbsp;
            <a href="https://www.google.com.mx/maps/@'.$latitude.','.-$lenght.',15z "target="_blank">Google Maps Ubicacion.</a></br><br>
            <br>Inicia sesi&oacute;n en el Sistema para  m&aacute;s detalles.&nbsp;
            <a href="backend.masfletes.com" target="_blank">masfletes.com</a></br>
            <br/><br/> No olvides que esta informaci&oacute;n tambien estar&aacute; disponible en tu panel dentro de tu sesi&oacute;n.</p></body></html>';           
        
    $emailAdd="";
    //$email=""; 
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = 'mail.masdistribucion.com.mx';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "admin@masdistribucion.com.mx";
    $mail->Password = "distribucion2900";
    $mail->From = "administrador@masfletes.com";
    $mail->FromName = "Notificaciones De MasFletes.Com";
    
    if($email!=""){
                   $mailExp=explode(";",$email);
                   foreach ($mailExp as $value)
                       {
                       echo $value;
                         $mail->AddBCC(''.$emailAdd.'',"Usuario Mas Fletes");
                         $mail->AddAddress($value,"");
                         $mail->Subject = "Notificaciones Operaciones de Flete";
                         $mail->MsgHTML($correo);
                         $mail->Send();
        }                
    }
    //if($emailAdd != ""){ $mail->AddBCC(''.$emailAdd.'',"Usuario Mas Fletes"); }
    
    break;
default:
   echo "Opcion no Valida";
    break;
    exit();
    }                  
   }  
  }
  
    public function gridSendLoadAction() {
            $this->view->setUseTemplate(FALSE);
            //header("Content-Type: application/json");
            $post= $this->getRequest()->getPost();
            error_reporting(E_ALL ^ E_WARNING);
           
            $idBuy = $post['idOrder'];
            $buyOrder =  $post['buyOrder'];
            $nameP = $post['nameP'];
            $idP = $post['idP'];
            $operation = $post['operation'];
            $em = $this->getEntityManager('DefaultDb');
            $user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
            $company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
            $lista = $em->getRepository('DefaultDb_Entity_DetalleContabilidad')->findOneBy(array('id_company' => $user->getCompany(), 'id_provider' => $idP));//CONSULTA A LA TABLA DETALLES CONTABILIDAD INCLUYENDO FILTROS
            $link="RouteProviders";                                                        
            if ($lista != NULL){
                if ($lista->getCorreo() != "")  {   $form = $this->getFormEmailOrder($buyOrder,$lista->getCorreo(),$nameP,$company->getBusinessName(),$idP,$idBuy,$operation,$company->getRfc());   }  
                else    {   $form = $this->getProviderErrorMail($nameP,$link);  }
            }
            if ($lista == NULL) {   $form = $this->getProviderErrorMail($nameP,$link); }
           
           
  echo utf8_encode($form);
        }
        
    public function gridSendLetterAction() {
            $this->view->setUseTemplate(FALSE);
            //header("Content-Type: application/json");
            $post= $this->getRequest()->getPost();
            error_reporting(E_ALL ^ E_WARNING);
           
            $idLetter = $post['idOrder'];
            $folioLetter =  $post['buyOrder'];
            $nameC = $post['nameP'];
            $idC = utf8_decode($post['idP']);
            $operation = $post['operation'];
            $em = $this->getEntityManager('DefaultDb');
            $user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));
            $company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//CONSULTA A LA TABLA companies TOMANDO COMO PARAMETRO EL ID DEL USUARIO ALMACENADO EN LA TABLA shipments
            $lista = $em->getRepository('DefaultDb_Entity_DetalleContabilidad')->findOneBy(array('id_company' => $user->getCompany(), 'id_provider' => $idC));//CONSULTA A LA TABLA DETALLES CONTABILIDAD INCLUYENDO FILTROS
            $link="RouteProviders";                                                        
            if ($lista != NULL){
                if ($lista->getCorreo() != "")  
                    {   $form = $this->getFormEmailLetterBearings($folioLetter,$lista->getCorreo(),$nameC,$company->getBusinessName(),$idC,$idLetter,$operation,$company->getRfc());   }  
                else    
                    {   $form = $this->getProviderErrorMail($nameC,$link);  }
            }
            if ($lista == NULL) {   $form = $this->getProviderErrorMail($nameC,$link); }
           
           
  echo utf8_encode($form);
        }
        
    private function getFormEmailLetterBearings($buyOrder,$getCorreo,$nameP,$businessName,$idP,$idBuy,$operation,$rfc) {
            error_reporting(E_ALL ^ E_WARNING);
            if (file_exists("tempFiles/".$rfc)) {
                        $dir = "tempFiles/".$rfc."/".$buyOrder.".pdf";
                        $this->generateLetterPDFTemp($idBuy,$dir);
                    } 
            else {    
                $this->createPathTemp("tempFiles/".$rfc);   
                $dir = "tempFiles/".$rfc."/".$buyOrder.".pdf";
                $this->generateLetterPDFTemp($idBuy,$dir);
            }
            $form = '
                   <div class="col-md-6">
                   <form id="emailFiles" name="emailFiles" class="form-horizontal form-actions ">
                   <h5>Carta Porte: '.$buyOrder.'</h5><br />
                    <input type="hidden" name="idp" value="'.$idP.'" />
                    <input type="hidden" name="dir" value="'.$dir.'" />
                    <input type="hidden" name="order" value="'.$buyOrder.'" />
                    <input type="hidden" name="type" value="Carta Porte" />
                     <input type="hidden" name="option" value="1" />

                       <div class="control-group">
                           <label class="control-label"  for="">Cliente:</label>
                           <div class="controls">
                               <input type="text" name="proveedor" class="span4 input-xlarge uneditable-input" readonly value="'.$nameP.'"   />
                           </div>
                           <br />
                           <label class="control-label"  for="">Para:</label>
                           <div class="controls">
                               <input type="text" name="email" class="span4 input-xlarge" value="'.$getCorreo.'"  />
                           </div>
                            <br />
                           <label class="control-label"  for="">CC Para:</label>
                           <div class="controls">
                                <input type="text" name="emailCC" class="span4 form-control input-xlarge" placeholder="Separar correos por ; correo1@ejem.com;correo2@ejem.com "></select>
                              </div>
                            <br />
                           <label class="control-label"  for="">Asunto:</label>
                          <div class="controls">
                               <input  type="text" name="asunto" class="span4 input-xlarge" value="Env&iacute;o Carta Porte: '.$buyOrder.'">
                           </div>
                           <br />
                           <label class="control-label"  for="">Mensaje:</label>
                           <div class="controls">
                             <textarea rows="8"  name="mensaje" class=" span4 input-xlarge form-control"  placeholder="" >

Atentamente: '.$businessName.'
Saludos
                              </textarea>

                           </div>


                       </div> 
                       <div class=" form-actions">
                         <button type="button" class="btn btn-primary"  id="send" onClick="sendFiles()">Enviar</button>
                         <button type="button" class="btn" id="close" onClick="close_modal()" ">Cerrar</button>

                       </div>


                   </form>
                   </div>
                   ';
            
            
            
            return $form;
        }
        
    private function getFormEmailOrder($buyOrder,$getCorreo,$nameP,$businessName,$idP,$idBuy,$operationId,$rfc) {
            error_reporting(E_ALL ^ E_WARNING);
            if (file_exists("tempFiles/".$rfc)) {
                        $dir = "tempFiles/".$rfc."/".$buyOrder.".pdf";
                        $this->generateBuyingPDFTemp($idBuy,$dir);
                    } 
            else {    
                $this->createPathTemp("tempFiles/".$rfc);   
                $dir = "tempFiles/".$rfc."/".$buyOrder.".pdf";
                $this->generateBuyingPDFTemp($idBuy,$dir);
            }
            $form = '
                   <div class="col-md-6">
                   <form id="emailFiles" name="emailFiles" class="form-horizontal form-actions ">
                   <h5>Orden de Carga: '.$buyOrder.'</h5><br />
                    <input type="hidden" name="idp" value="'.$idP.'" />
                    <input type="hidden" name="dir" value="'.$dir.'" />
                    <input type="hidden" name="order" value="'.$buyOrder.'" />
                    <input type="hidden" name="type" value="Orden de Carga" />
                    <input type="hidden" name="option" value="2" />

                       <div class="control-group">
                           <label class="control-label"  for="">Proveedor:</label>
                           <div class="controls">
                               <input type="text" name="proveedor" class="span4 input-xlarge uneditable-input" readonly value="'.$nameP.'"   />
                           </div>
                           <br />
                           <label class="control-label"  for="">Para:</label>
                           <div class="controls">
                               <input type="email" name="email" class="span4 input-xlarge"   value="'.$getCorreo.'"  />
                           </div>
                            <br />
                           <label class="control-label"  for="">CC Para:</label>
                           <div class="controls">
                                <input type="text" name="emailCC" class="span4 form-control input-xlarge" placeholder="Separar correos por ; correo1@ejem.com;correo2@ejem.com "></select>
                              </div>
                            <br />
                           <label class="control-label"  for="">Asunto:</label>
                           <div class="controls">
                               <input  type="text" name="asunto" class="span4 input-xlarge"  placeholder="" value="Env&iacute;o Orden de Carga: '.$buyOrder.'">
                           </div>
                           <br />
                           <label class="control-label"  for="">Mensaje:</label>
                           <div class="controls">
                             <textarea rows="8"  name="mensaje" class=" span4 input-xlarge form-control"  placeholder="" >

Atentamente: '.$businessName.'
Saludos
                              </textarea>

                           </div>


                       </div> 
                       <div class=" form-actions">
                         <button type="button" class="btn btn-primary"  id="send" onClick="sendFiles()">Enviar</button>
                         <button type="button" class="btn" id="close" onClick="close_modal()" ">Cerrar</button>
                       </div>


                   </form>
                   </div>
                   ';
            
            
            
            return $form;
        }
		
    public function generateBuyingPDFTemp($idOrder,$dir){
		include_once "../library/mPDF/mpdf.php";
		header("Content-type: text/html; charset=utf8");
		//$this->view->setUseTemplate(FALSE);
		setlocale(LC_ALL,"es_ES");
            
		$mpdf=new mPDF('c','Letter');
	
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$html = file_get_contents("../application/View/Scripts/Admin/LoadOrders/getPDF.php");
	
//		$dia = date("d");
//		$mes = $meses[date("n") - 1];
//		$anio = date("Y");
//		$fecha = $dia . " de " . $mes . " del " . $anio;
//	
		$em = $this->getEntityManager('DefaultDb');
	
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
	
		$order = $em->getRepository('DefaultDb_Entity_LoadOrder')->findOneBy(array('id' => $idOrder));
		$operation = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('id' => $order->getOperation()));
		$operationdetails = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $operation->getId()));
//		$idopdetails = ($operationdetails != null) ? $operationdetails->getId() : "";
		
		$date = str_replace($vowels, "-", $operation->getDate());
		$date = explode('-', $date);
		$mifecha = gmmktime(0,0,0,$date[1],$date[2] + 1,$date[0]);
		$date1 = strftime("%A, %d de %B de %Y", $mifecha);
		
		
		$html = str_replace("[FECHA]", utf8_encode($date1), $html);
		
//		
//		if($idopdetails == "")
//		{
//			echo "AVISO: NO SE HAN ESTABLECIDO DETALLES PARA LA OPERACI&Oacute;N, COMO EL CONDUCTOR Y/&Oacute; LA UNIDAD POR LO QUE NO SE PUEDE GENERAR EL ARCHIVO PDF DE LA ORDEN DE CARGA";
//			return false;
//		}		
		
		$coordinator= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $operationdetails->getCompany()));
		$provider= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $operationdetails->getProvider()));
                $customer= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $operationdetails->getCustomer()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
		$driver= $em->getRepository('DefaultDb_Entity_Driver')->findOneBy(array('id' => $operationdetails->getDriver()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)	
		$unit= $em->getRepository('DefaultDb_Entity_Unit')->findOneBy(array('id' => $operationdetails->getUnit()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
		
		$tabla = '<table class="operaciones">
					<thead><tr>
						<td>Cant.</td>
						<td>Descripci&oacute;n</td>
						<td>Precio Unitario</td>
						<td>Importe</td>
				</tr></thead><tbody>';
		$subtotal = 0;
		$descuento = 0;
		$traslados = 0;
		$retenidos = 0;
		
		$lista = $em->getRepository('DefaultDb_Entity_OperatingExpense')->findBy(array('id_operation' => $order->getOperation(), 'idBuyOrder' => $idOrder));//CONSULTA A LA TABLA operating_expenses
              
                foreach($lista as $item)
		{
			$article = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $item->getArticle()));
			//$tabla .= "<tr><td>".$item->getQuantity()."</td><td>".$article->getId()." - ".$article->getDescription()."</td><td>".$article->getPrice()."</td><td>".$article->getPrice() * $item->getQuantity()."</td></tr>";
			$tabla .= "<tr><td>".$item->getQuantity()."</td><td>".$article->getId()." - ".$article->getDescription()."</td><td>".$item->getValor()."</td><td>".$item->getValor() * $item->getQuantity()."</td></tr>";
			//$tabla .= "<tr><td>".$item->getQuantity()."</td><td>".$item->getId()." - ".$item->getDescription()."</td><td>".$item->getValor()."</td><td>".$item->getValor() * $item->getQuantity()."</td></tr>";
			//$subtotal = $subtotal + ($article->getPrice() * $item->getQuantity());
			$subtotal = $subtotal + ($item->getValor() * $item->getQuantity());
			//$traslados = $traslados + ($article->getIva() * $item->getQuantity());
			$traslados = $traslados + ($item->getIva() * $item->getQuantity());
			//$retenidos = $retenidos + ($article->getRetention() * $item->getQuantity());
			$retenidos = $retenidos + ($item->getRetention() * $item->getQuantity());
		}
		$tabla .= "</tbody></table>";
	
		$total = $subtotal + $traslados - $retenidos;
	
		$city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $company->getCity()));
		$state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $company->getState()));
		$country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $company->getCountry()));
	
		$source = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getSource()));
		$destination = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDestination()));
		$distribution1 = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDistribution1()));
		$distribution2 = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDistribution2()));
		
		$logo = htmlentities($coordinator->getLogo());
		$html = str_replace("[LOGO]",$logo, $html);
		$ordcont = htmlentities($coordinator->getOrdContract());
		$html = str_replace("[CONTRATO]",$ordcont, $html);
		$companyName = htmlentities($coordinator->getBusinessName());
		$html = str_replace("[COMPANY]",$companyName, $html);
		$rfc = htmlentities($coordinator->getRfc());
		$html = str_replace("[RFC]",$rfc, $html);
		$address = htmlentities($company->getStreet() ." ". $company->getNum() ." ". $company->getSuburb() ." C.P. " . $company->getCP() .", Loc. " . $city->getName() ." ". $state->getName() .", " .$country->getName());
		$html = str_replace("[ADDRESS]",$address, $html);
		$email = htmlentities($company->getFiscalMail());
		$html = str_replace("[EMAIL]",$email, $html);
		$phone = htmlentities("Tel. " . $company->getPhone());
		$html = str_replace("[PHONE]",$phone, $html);
		$providerName = htmlentities($provider->getBusinessName());
		$html = str_replace("[PROVIDER]",$providerName, $html);
		$customerName = htmlentities($customer->getBusinessName());
		$html = str_replace("[CUSTOMER]",$customerName, $html);
		$driverName = ($driver != null) ? htmlentities($driver->getName() ." ". $driver->getLastName()) : "";
		$html = str_replace("[DRIVERNAME]",$driverName, $html);
		$driverLicenset = ($driver != null) ? htmlentities($driver->getLicense()) : "";
		$html = str_replace("[LICENSETYPE]",$driverLicenset, $html);
		$driverLicense = ($driver != null) ? htmlentities($driver->getLicenseNumber()) : "";
		$html = str_replace("[LICENSE]",$driverLicense, $html);
		$driverPhone = ($driver != null) ? htmlentities($driver->getCell()) : "";
		$html = str_replace("[CELPHONE]",$driverPhone, $html);
		$unitbrand = ($unit != null) ? htmlentities($unit->getBrand()) : "";
		$html = str_replace("[BRAND]",$unitbrand, $html);
		$unitmodel = ($unit != null) ? htmlentities($unit->getModel()) : "";
		$html = str_replace("[MODEL]",$unitmodel, $html);
		$unitplate = ($unit != null) ? htmlentities($unit->getPlates()) : "";
		$html = str_replace("[PLATES]",$unitplate, $html);
		$unitcolor = ($unit != null) ? htmlentities($unit->getColor()) : "";
		$html = str_replace("[COLOR]",$unitcolor, $html);
		
		if($source != null)
		{
			if($source->getCity() != null)
			{
				if($operationdetails->getEdd() != '0000-00-00')
				{
					$date = str_replace($vowels, "-", $operationdetails->getEdd());
					$date = explode('-', $date);
					$mifecha = gmmktime(0,0,0,$date[1],$date[2] + 1,$date[0]);
					$date1 = strftime("%A, %d de %B de %Y", $mifecha);
				}
				$sourcecity = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $source->getCity()));
				$sourcestate = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $source->getState()));
				$sourcecountry = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $source->getCountry()));
				$sourceaddress  = $source->getDescription()." ";
				$sourceaddress .= htmlentities($source->getStreet() ." ". $source->getNumber() ." ". $source->getSuburb() ." C.P. " . $source->getZip() .", Loc. " . $sourcecity->getName() ." ". $sourcestate->getName() .", " .$sourcecountry->getName());
				$sourceaddress .= "<br />(".utf8_encode($date1)." ".$operationdetails->getEdt().")";
			}
			else{$sourceaddress="";}
		}
		else{$sourceaddress="";}
		$html = str_replace("[SOURCEADDRESS]",$sourceaddress, $html);
		
		if($destination != null)
		{
			if($destination->getCity() != null)
			{
				if($operationdetails->getEda() != '0000-00-00')
				{
					$date2 = str_replace($vowels, "-", $operationdetails->getEda());
					$date2 = explode('-', $date2);
					$mifecha = gmmktime(0,0,0,$date2[1],$date2[2] + 1,$date2[0]);
					$date3 = strftime("%A, %d de %B de %Y", $mifecha);
				}
				$destinationcity = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $destination->getCity()));
				$destinationstate = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $destination->getState()));
				$destinationcountry = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $destination->getCountry()));
				$destinationaddress = $destination->getDescription()." ";
				$destinationaddress .= htmlentities($destination->getStreet() ." ". $destination->getNumber() ." ". $destination->getSuburb() ." C.P. " . $destination->getZip() .", Loc. " . $destinationcity->getName() ." ". $destinationstate->getName() .", " .$destinationcountry->getName());
				$destinationaddress .= "<br />(".utf8_encode($date3)." ".$operationdetails->getEta().")";
			}
			else{$destinationaddress="";}
		}
		else{$destinationaddress="";}
		$html = str_replace("[DESTADDRESS]",$destinationaddress, $html);
		
		if($distribution1 != null)
		{
			if($distribution1->getCity() != null)
			{
				$dis1city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $distribution1->getCity()));
				$dis1state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $distribution1->getState()));
				$dis1country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $distribution1->getCountry()));
				$dis1address = $distribution1->getDescription()." ";
				$dis1address .= htmlentities($distribution1->getStreet() ." ". $distribution1->getNumber() ." ". $distribution1->getSuburb() ." C.P. " . $distribution1->getZip() .", Loc. " . $dis1city->getName() ." ". $dis1state->getName() .", " .$dis1country->getName());
				$dis1address .= "<br />(".$operationdetails->getDate().")";
			}
			else{$dis1address ="";}
		}
		else{$dis1address ="";}
		$html = str_replace("[DIS1ADDRESS]",$dis1address, $html);

		if($distribution2 != null)
		{
			if($distribution2->getCity() != null)
			{
				$dis2city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $distribution2->getCity()));
				$dis2state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $distribution2->getState()));
				$dis2country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $distribution2->getCountry()));
				$dis2address = $distribution2->getDescription()." ";
				$dis2address .= htmlentities($distribution2->getStreet() ." ". $distribution2->getNumber() ." ". $distribution2->getSuburb() ." C.P. " . $distribution2->getZip() .", Loc. " . $dis2city->getName() ." ". $dis2state->getName() .", " .$dis2country->getName());
				$dis2address .= "<br />(".$operationdetails->getDate().")";
			}
			else{$dis2address ="";}
		}
		else{$dis2address ="";}
		$html = str_replace("[DIS2ADDRESS]",$dis2address, $html);
		
		$folioO = htmlentities($order->getFolio());
		$html = str_replace("[FOLIORDEN]",$folioO, $html);
	
		$html = str_replace("[TABLA]", $tabla, $html);
	
                $comments= "";
                $html = str_replace("[COMMENTS]",$comments, $html);
                $commentsTravel= "";
                $html = str_replace("[COMMENTSTRAVEL]",$commentsTravel, $html);
                $html = str_replace("[DOCUMENTACION]",$operation->getDocumentation(), $html);
                
		$html = str_replace("[SUBTOTAL]",number_format($subtotal,2), $html);
		$html = str_replace("[DESCUENTO]",number_format($descuento,2), $html);
		$html = str_replace("[TRASLADOS]",number_format($traslados,2), $html);
		$html = str_replace("[RETENIDOS]",number_format($retenidos,2), $html);
		$html = str_replace("[TOTAL]",number_format($total,2), $html);
	
		$V = new EnLetras();
		$precioLetras=strtoupper($V->ValorEnLetras($subtotal,"pesos"));
		$html = str_replace("[LETRAS]", $precioLetras, $html);
	
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetAuthor("Mas Fletes");
		$mpdf->WriteHTML($html);	// Separate Paragraphs  defined by font
		$mpdf->Output($dir,'F');
	
	}
    
    
    public function generateLetterPDFTemp($idLetter,$dir){
		include_once "../library/mPDF/mpdf.php";
		header("Content-type: text/html; charset=utf8");
		$this->view->setUseTemplate(FALSE);
		setlocale(LC_ALL,"es_ES");
	
		//$idLetter = $this->getRequest()->getParam('idNote');
	
		$mpdf=new mPDF('c','Letter');
	
		$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$html = file_get_contents("../application/View/Scripts/Accountant/LetterBearings/getPDF.php");
	
		$dia = date("d");
		$mes = $meses[date("n") - 1];
		$anio = date("Y");
		$fecha = $dia . " de " . $mes . " del " . $anio;
		//$html = str_replace("[FECHA]", $fecha, $html);
	
		$em = $this->getEntityManager('DefaultDb');
	
		$user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
		$company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
	
		$letter = $em->getRepository('DefaultDb_Entity_LetterBearing')->findOneBy(array('id' => $idLetter));
	
		$tabla = '<table class="operaciones">
					<thead><tr>
						<td>Cant.</td>
						<td>Descripci&oacute;n</td>
						<td>Precio Unitario</td>
						<td>Importe es esta</td>
				 </tr></thead><tbody>';
		$subtotal = 0;
		$descuento = 0;
		$traslados = 0;
		$retenidos = 0;
		$documentation = "";
		$comments = "";

		$operation = $em->getRepository('DefaultDb_Entity_TransportOperation')->findOneBy(array('note' => $letter->getId(), "type" => 1));
		$idoperation = ($operation != null) ? $operation->getId() : "";
		
		if($idoperation == "")
		{
			echo "AVISO: LA CARTA PORTE A LA QUE SE HACE REFERENCIA NO TIENE INCLUIDA UNA OPERACION DE TIPO FLETE POR LO QUE NO SE PUEDE GENERAR EL ARCHIVO PDF";
			return false;
		}

		$date = str_replace($vowels, "-", $operation->getOperationDate());
		$date = explode('-', $date);
		$mifecha = gmmktime(0,0,0,$date[1],$date[2] + 1,$date[0]);
		$date1 = strftime("%A, %d de %B de %Y", $mifecha);
		
		$html = str_replace("[FECHA]",  "<strong style='font-size:1em;'>".utf8_encode($date1)."</strong>", $html);
		
		$operationdetails = $em->getRepository('DefaultDb_Entity_TransportOperationDetail')->findOneBy(array('id_operation' => $idoperation));
		$idopdetails = ($operationdetails != null) ? $operationdetails->getId() : "";
		$usercoord= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $operation->getUser()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
		
		if($idopdetails == "")
		{
			echo "AVISO: NO SE HAN ESTABLECIDO DETALLES PARA LA OPERACI&Oacute;N DE TIPO FLETE INCLUIDA EN LA CARTA PORTE POR LO QUE NO SE PUEDE GENERAR EL ARCHIVO PDF";
			return false;
		}
		
		$coordinator= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $operationdetails->getCompany()));
		$driver= $em->getRepository('DefaultDb_Entity_Driver2')->findOneBy(array('id' => $operationdetails->getDriver()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
    	if($driver != null){
			$driverdata =$em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $driver->getCompanyData()));
		}
		$unit= $em->getRepository('DefaultDb_Entity_Unit')->findOneBy(array('id' => $operationdetails->getUnit()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
		
		$operations = $em->getRepository('DefaultDb_Entity_TransportOperation')->findBy(array('note' => $letter->getId()));

		foreach($operations as $item)
		{
			$article = $em->getRepository('DefaultDb_Entity_Article')->findOneBy(array('id' => $item->getShipment()));
			
			//$tabla .= "<tr><td>".$item->getQuantity()."</td><td>".$item->getId()." - ".$item->getDescription()."</td><td>".$article->getPrice()."</td><td>".$article->getPrice() * $item->getQuantity()."</td></tr>";
			$tabla .= "<tr><td>".$item->getQuantity()."</td><td>".$item->getId()." - ".$item->getDescription()."</td><td>N/A</td><td>N/A</td></tr>";
			$subtotal = $subtotal + ($item->getPrice() * $item->getQuantity());
			$traslados = $traslados + ($item->getIva() * $item->getQuantity());
			$retenidos = $retenidos + ($item->getRetention() * $item->getQuantity());
			$documentation .= (strlen($item->getDocumentation()) > 0)?$item->getDocumentation().",":""; 
			$comments .= (strlen($item->getComments()) > 0)?$item->getComments().",":"";
		}
		
		$tabla .= "<tr><td colspan=4 style='text-align:center;'>".$letter->getComments()."</td></tr>";
		$tabla .= "</tbody></table>";

		$html = str_replace("[DOCUMENTACION]",  $documentation, $html);
		
		$total = $subtotal + $traslados - $retenidos;
	
		$city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $coordinator->getCity()));
		$state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $coordinator->getState()));
		$country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $coordinator->getCountry()));

		$source = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getSource()));
		$destination = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDestination()));
		$distribution1 = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDistribution1()));
		$distribution2 = $em->getRepository('DefaultDb_Entity_Location')->findOneBy(array('id' => $operationdetails->getDistribution2()));
		
		$customer= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $letter->getCustomer()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
		//var_dump($customer->getBusinessName());
		$vowels = array(" ", ":");
		
		if($source != null)
		{
			if($source->getCity() != null)
			{
				if($operationdetails->getEdd() != '0000-00-00')
				{
					$date = str_replace($vowels, "-", $operationdetails->getEdd());
					$date = explode('-', $date);
					$mifecha = gmmktime(0,0,0,$date[1],$date[2] + 1,$date[0]);
					$date1_1 = strftime("%A, %d de %B de %Y", $mifecha);
				}
				$sourcecity = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $source->getCity()));
				$sourcestate = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $source->getState()));
				$sourcecountry = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $source->getCountry()));
				$sourceaddress  = $source->getDescription()." ";
				$sourceaddress .= htmlentities($source->getStreet() ." ". $source->getNumber() ." ". $source->getSuburb() ." C.P. " . $source->getZip() .", Loc. " . $sourcecity->getName() ." ". $sourcestate->getName() .", " .$sourcecountry->getName());
				$sourceaddress .= "<br /><strong style='font-size:1.2em;'>(".utf8_encode($date1_1)." ".$operationdetails->getEdt().")</strong>";
			}
			else{$sourceaddress="";}
		}
		else{$sourceaddress="";}
		$html = str_replace("[SOURCEADDRESS]",$sourceaddress, $html);
		
		if($destination != null)
		{
			if($destination->getCity() != null)
			{
				if($operationdetails->getEda() != '0000-00-00')
				{
					$date2 = str_replace($vowels, "-", $operationdetails->getEda());
					$date2 = explode('-', $date2);
					$mifecha = gmmktime(0,0,0,$date2[1],$date2[2] + 1,$date2[0]);
					$date3 = strftime("%A, %d de %B de %Y", $mifecha);
				}
				$destinationcity = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $destination->getCity()));
				$destinationstate = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $destination->getState()));
				$destinationcountry = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $destination->getCountry()));
				$destinationaddress = $destination->getDescription()." ";
				$destinationaddress .= htmlentities($destination->getStreet() ." ". $destination->getNumber() ." ". $destination->getSuburb() ." C.P. " . $destination->getZip() .", Loc. " . $destinationcity->getName() ." ". $destinationstate->getName() .", " .$destinationcountry->getName());
				$destinationaddress .= "<br /><strong style='font-size:1.2em;'>(".utf8_encode($date3)." ".$operationdetails->getEta().")</strong>";
			}
			else{$destinationaddress="";}
		}
		else{$destinationaddress="";}
		$html = str_replace("[DESTADDRESS]",$destinationaddress, $html);
		
		if($distribution1 != null)
		{
			if($distribution1->getCity() != null)
			{
				$dis1city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $distribution1->getCity()));
				$dis1state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $distribution1->getState()));
				$dis1country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $distribution1->getCountry()));
				$dis1address = $distribution1->getDescription()." ";
				$dis1address .= htmlentities($distribution1->getStreet() ." ". $distribution1->getNumber() ." ". $distribution1->getSuburb() ." C.P. " . $distribution1->getZip() .", Loc. " . $dis1city->getName() ." ". $dis1state->getName() .", " .$dis1country->getName());
				//$dis1address .= "<br /><strong style='font-size:1.2em;'>(".utf8_encode($operationdetails->getDate()).")</strong>";
			}
			else{$dis1address ="";}
		}
		else{$dis1address ="";}
		$html = str_replace("[DIS1ADDRESS]",$dis1address, $html);

		if($distribution2 != null)
		{
			if($distribution2->getCity() != null)
			{
				$dis2city = $em->getRepository('DefaultDb_Entity_City')->findOneBy(array('id_city' => $distribution2->getCity()));
				$dis2state = $em->getRepository('DefaultDb_Entity_State')->findOneBy(array('id' => $distribution2->getState()));
				$dis2country = $em->getRepository('DefaultDb_Entity_Country')->findOneBy(array('id' => $distribution2->getCountry()));
				$dis2address = $distribution2->getDescription()." ";
				$dis2address .= htmlentities($distribution2->getStreet() ." ". $distribution2->getNumber() ." ". $distribution2->getSuburb() ." C.P. " . $distribution2->getZip() .", Loc. " . $dis2city->getName() ." ". $dis2state->getName() .", " .$dis2country->getName());
				//$dis2address .= "<br /><strong style='font-size:1.2em;'>(".utf8_encode($operationdetails->getDate()).")</strong>";
			}
			else{$dis2address ="";}
		}
		else{$dis2address ="";}
		$html = str_replace("[DIS2ADDRESS]",$dis2address, $html);
		
		$logo = htmlentities($coordinator->getLogo());
		$html = str_replace("[LOGO]",$logo, $html);
		$companyName = htmlentities($coordinator->getBusinessName());
		$html = str_replace("[COMPANY]",$companyName, $html);
		$rfc = htmlentities($coordinator->getRfc());
		$html = str_replace("[RFC]",$rfc, $html);
		$address = htmlentities($coordinator->getStreet() ." ". $coordinator->getNum() ." ". $coordinator->getSuburb() ." C.P. " . $coordinator->getCP() .", Loc. " . $city->getName() ." ". $state->getName() .", " .$country->getName());
		$html = str_replace("[ADDRESS]",$address, $html);
		$email = htmlentities($coordinator->getFiscalMail());
		$html = str_replace("[EMAIL]",$email, $html);
		$phone = htmlentities("Tel. " . $coordinator->getPhone());
		$html = str_replace("[PHONE]",$phone, $html);
		if($driver != null)
		{
			$driverName = htmlentities($driver->getNames() ." ". $driver->getLastNames());
			$driverLicenset = htmlentities($driver->getLicense());
			$driverLicense = htmlentities($driver->getLicenseNumber());
			$driverPhone = htmlentities($driverdata->getPhone());
            if ($driverdata->getLogo() != ""){
            	$driverPhoto = '<tr><td><img src="../public/'.$driverdata->getLogo().'" height="95" width="75" alt="Operador'.$driverName.'"></td></tr>'; 
                $complement = '<tr><td height="90" ></td></tr>'; 
           }else{$driverPhoto = '';   $complement="";}
		}else{
			$driverName = "";
			$driverLicenset = "";
			$driverLicense = "";
			$driverPhone = "";
            $driverPhoto = "";
            $complement = "";
		}
        $html = str_replace("[COMPLEMNET]", $complement , $html);
        $html = str_replace("[DRIVERPHOTO]", $driverPhoto , $html);
		$html = str_replace("[DRIVERNAME]",$driverName, $html);
		$html = str_replace("[LICENSETYPE]",$driverLicenset, $html);
		$html = str_replace("[LICENSE]",$driverLicense, $html);
		$html = str_replace("[CELPHONE]",$driverPhone, $html);
		if($unit != null)
		{
		$unitbrand = htmlentities($unit->getBrand());
		$unitmodel = htmlentities($unit->getModel());
		$unitplate = htmlentities($unit->getPlates());
		$unitcolor = htmlentities($unit->getColor());
		}
		else 
		{
			$unitbrand = "";
			$unitmodel = "";
			$unitplate = "";
			$unitcolor = "";
			
		}
		$html = str_replace("[BRAND]",$unitbrand, $html);
		$html = str_replace("[MODEL]",$unitmodel, $html);
		$html = str_replace("[PLATES]",$unitplate, $html);
		$html = str_replace("[COLOR]",$unitcolor, $html);
		$folioC = htmlentities($letter->getFolio());
		$html = str_replace("[FOLIOCARTA]",$folioC, $html);
	
		$html = str_replace("[TABLA]", $tabla, $html);
	
		$html = str_replace("[SUBTOTAL]","N/A", $html);
		$html = str_replace("[DESCUENTO]","N/A", $html);
		$html = str_replace("[TRASLADOS]","N/A", $html);
		$html = str_replace("[RETENIDOS]","N/A", $html);
		$html = str_replace("[TOTAL]","N/A", $html);
		$html = str_replace("[MONEDA]","N/A", $html);
		
		/*$html = str_replace("[SUBTOTAL]",number_format($subtotal,2), $html);
		$html = str_replace("[DESCUENTO]",number_format($descuento,2), $html);
		$html = str_replace("[TRASLADOS]",number_format($traslados,2), $html);
		$html = str_replace("[RETENIDOS]",number_format($retenidos,2), $html);
		$html = str_replace("[TOTAL]",number_format($total,2), $html);*/
	
		$V = new EnLetras();
		$precioLetras=strtoupper($V->ValorEnLetras($total,"pesos"));
		$html = str_replace("[LETRAS]", '', $html);
		//$comments=strtoupper($letter->getComments());
		$html = str_replace("[COMMENTS]", $comments, $html);
		$html = str_replace("[COORDINADOR]", $usercoord->getNames()." ".$usercoord->getLastNames(), $html);
		
		//echo $html;
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetAuthor("Mas Fletes");
		$mpdf->WriteHTML($html);	// Separate Paragraphs  defined by font
		$mpdf->Output($dir,'F');
    	
	}
        
      
        public function sendMailFilesAction() {
        error_reporting(E_ALL ^ E_WARNING);
        $this->view->setUseTemplate(FALSE);
        $post = $this->getRequest()->getPost();
        $em= $this->getEntityManager('DefaultDb');

        $subject = $post['asunto']; 
        $message = $post['mensaje']; 
        $mailProv =  $post['email'];
        $mailCC = $post['emailCC'];
        $proveedor = $post['proveedor'];
        $pdf = $post['dir'];
        $order = $post['order'];
        $type = $post['type'];
        $option = $post['option'];

        $user= $em->getRepository('DefaultDb_Entity_User')->findOneBy(array('id_users' => $this->_credentials['id_users']));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
        $company= $em->getRepository('DefaultDb_Entity_Company')->findOneBy(array('id_companies' => $user->getCompany()));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO (CLIENTE)
         if ($option == "1"){//Carta Porte
            $proveedorN = $company->getBusinessName();
            $cliente = $proveedor;
            $cte =$company->getBusinessName();
            $prov = $proveedor;
        }
        if ($option == "2"){//Orden de Carga
            $proveedorN = $proveedor;
            $cliente = $company->getBusinessName();
            $cte = $company->getBusinessName();
            $prov = $proveedor;
        }
         $correo=utf8_encode('<html>
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                </head>
                <body bgcolor="#F5F5F5" leftmargin="18px" topmargin="10px" rightmargin="10px" bottommargin="10px">
                <h3 style="color:#AF080F;text-align:left;">::: '.$type.' :::</h3>
              
                <fieldset style="border: 3px double;color:#666666;">
                <legend style="color:#AF080F;text-align:center;font-size:15px;font-weight: bold;"></legend>
                <label style=" padding-left:15px; color:#666666;font-size:15px; ">Proveedor:                 <strong>'.$proveedorN.'</label><br />
                <label style=" padding-left:15px;color:#666666;font-size:15px; ">Cliente:                 <strong>'.$cliente.'</label><br />
                <label style=" padding-left:15px;color:#666666;font-size:15px; ">'.$type.' :           <strong>'.$order.'</strong></label><br /><br />
                <label style=" padding-left:15px;color:#666666;font-size:15px; ">'.$message.'</label><br />
                </fieldset>
                <p>&#171;&#171; NOTA: Este correo es emitido de forma autom&aacute;tica y para fines informativos, favor de no responder al mismo.&#187;&#187;</p>
                </body></html>');
         
       $this->sendOrderPdf($correo,$subject,$mailProv,$mailCC,$pdf,$prov,$cte,$user->getUsername(),$type );
       
        //echo utf8_decode($form);
        //echo utf8_decode($correo);
        }
        
        public function save_imgAction()
        {
        	$this->view->setUseTemplate(FALSE);
        	$idop = $this->getRequest()->getParam('param');
        
        	$dir ='images/imagesop/operation_'.$idop.'/';
        	if(!file_exists($dir)) {
        		mkdir($dir, 0777, true);
        		$tfiles = 0;
        	}else{
				//$tfiles = count(glob($dir.'{*.jpg,*.gif,*.png}',GLOB_BRACE));
				$explorar = scandir($dir);
				/*$idx = 0;
				foreach ($explorar as $file){
					if($idx > 2){
						$fil = explode('.', $file);
						var_dump( $fil[1]);
					}
					$idx++; 
				}*/
				
				$tfiles = count($explorar) - 3;
        	}
        	$tfiles = $tfiles + 1;
        	if (!isset($_FILES['files'])) {
        		$image = $_POST['image'];
        		$_FILES['files'] = base64_decode($image);
        	}
        	$pic = $_FILES['files'];
        	$exten = explode('.', $pic['name']);
        	$data = array('success' => true);
        	//Validamos si la copio correctamente
        	//if(copy($pic['tmp_name'],$dir.$idop."_".$pic['name'])){
        	if(copy($pic['tmp_name'],$dir.$idop."_".$tfiles.'.'.strtolower($exten[1]))){
        		$data = array('success' => true, 'name' => $idop."_".$tfiles.'.'.strtolower($exten[1]));
        	}
        
        	$filename = $dir.$idop.'.zip';
        	$zip = new ZipArchive();
        	if($zip->open($filename,ZIPARCHIVE::CREATE)===true) {
        		//$zip->addFile($dir.$idop."_".$pic['name']);
        		$zip->addFile($dir.$idop."_".$tfiles.'.'.$exten[1]);
        		$zip->close();
        	}
        
        	//Codificamos el array a JSON (Esta sera la respuesta AJAX)
        	$this->view->result = json_encode($data);
        }
    
        
}	
       
		
		
class EnLetras
	{
		var $Void = "";
		var $SP = " ";
		var $Dot = ".";
		var $Zero = "0";
		var $Neg = "Menos";
	
		function ValorEnLetras($x, $Moneda ){
			$s="";
			$Ent="";
			$Frc="";
			$Signo="";
	
			if(floatVal($x) < 0)
				$Signo = $this->Neg . " ";
			else
				$Signo = "";
	
			if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
				$s = number_format($x,2,'.','');
			else
				$s = number_format($x,2,'.','');
	
			$Pto = strpos($s, $this->Dot);
	
			if ($Pto === false)    {
				$Ent = $s;
				$Frc = $this->Void;
			}
			else    {
				$Ent = substr($s, 0, $Pto );
				$Frc =  substr($s, $Pto+1);
			}
	
			if($Ent == $this->Zero || $Ent == $this->Void)
				$s = "Cero ";
			elseif( strlen($Ent) > 7)    {
				$s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) .
				"Millones " . $this->SubValLetra(intval(substr($Ent,-6, 6)));
			}
			else    {
				$s = $this->SubValLetra(intval($Ent));
			}
	
			if (substr($s,-9, 9) == "Millones " || substr($s,-7, 7) == "Mill?n ")
				$s = $s . "de ";
	
			$s = $s . $Moneda;
	
			if($Frc != $this->Void)    {
				$s = $s . " " . $Frc. "/100";
				//$s = $s . " " . $Frc . "/100";
			}
			$letrass=$Signo . $s . " M.N.";
			return ($Signo . $s . " M.N.");
		}
	
	
		function SubValLetra($numero){
			$Ptr="";
			$n=0;
			$i=0;
			$x ="";
			$Rtn ="";
			$Tem ="";
	
			$x = trim("$numero");
			$n = strlen($x);
	
			$Tem = $this->Void;
			$i = $n;
	
			while( $i > 0){
				$Tem = $this->Parte(intval(substr($x, $n - $i, 1).
						str_repeat($this->Zero, $i - 1 )));
				If( $Tem != "Cero" )
				$Rtn .= $Tem . $this->SP;
				$i = $i - 1;
			}
	
	
			//--------------------- GoSub FiltroMil ------------------------------
			$Rtn=str_replace(" Mil Mil", " Un Mil", $Rtn );
			while(1){
				$Ptr = strpos($Rtn, "Mil ");
				If(!($Ptr===false)){
					If(! (strpos($Rtn, "Mil ",$Ptr + 1) === false ))
					$this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
					Else
					break;
				}
				else break;
			}
	
			//--------------------- GoSub FiltroCiento ------------------------------
			$Ptr = -1;
			do{
				$Ptr = strpos($Rtn, "Cien ", $Ptr+1);
				if(!($Ptr===false)){
					$Tem = substr($Rtn, $Ptr + 5 ,1);
					if( $Tem == "M" || $Tem == $this->Void)
						;
						else
							$this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
				}
			}while(!($Ptr === false));
	
			//--------------------- FiltroEspeciales ------------------------------
			$Rtn=str_replace("Diez Un", "Once", $Rtn );
			$Rtn=str_replace("Diez Dos", "Doce", $Rtn );
			$Rtn=str_replace("Diez Tres", "Trece", $Rtn );
			$Rtn=str_replace("Diez Cuatro", "Catorce", $Rtn );
			$Rtn=str_replace("Diez Cinco", "Quince", $Rtn );
			$Rtn=str_replace("Diez Seis", "Dieciseis", $Rtn );
			$Rtn=str_replace("Diez Siete", "Diecisiete", $Rtn );
			$Rtn=str_replace("Diez Ocho", "Dieciocho", $Rtn );
			$Rtn=str_replace("Diez Nueve", "Diecinueve", $Rtn );
			$Rtn=str_replace("Veinte Un", "Veintiun", $Rtn );
			$Rtn=str_replace("Veinte Dos", "Veintidos", $Rtn );
			$Rtn=str_replace("Veinte Tres", "Veintitres", $Rtn );
			$Rtn=str_replace("Veinte Cuatro", "Veinticuatro", $Rtn );
			$Rtn=str_replace("Veinte Cinco", "Veinticinco", $Rtn );
			$Rtn=str_replace("Veinte Seis", "Veintise?s", $Rtn );
			$Rtn=str_replace("Veinte Siete", "Veintisiete", $Rtn );
			$Rtn=str_replace("Veinte Ocho", "Veintiocho", $Rtn );
			$Rtn=str_replace("Veinte Nueve", "Veintinueve", $Rtn );
	
			//--------------------- FiltroUn ------------------------------
			If(substr($Rtn,0,1) == "M") $Rtn = "Un " . $Rtn;
			//--------------------- Adicionar Y ------------------------------
			for($i=65; $i<=88; $i++)
			{
				If($i != 77)
				$Rtn=str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
			}
			$Rtn=str_replace("*", "a" , $Rtn);
			return($Rtn);
		}
	
	
		function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr){
			$x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
		}
	
		function Parte($x){
			$Rtn='';
			$t='';
			$i='';
			Do
			{
				switch($x)
				{
					Case 0:  $t = "Cero";break;
					Case 1:  $t = "Un";break;
					Case 2:  $t = "Dos";break;
					Case 3:  $t = "Tres";break;
					Case 4:  $t = "Cuatro";break;
					Case 5:  $t = "Cinco";break;
					Case 6:  $t = "Seis";break;
					Case 7:  $t = "Siete";break;
					Case 8:  $t = "Ocho";break;
					Case 9:  $t = "Nueve";break;
					Case 10: $t = "Diez";break;
					Case 20: $t = "Veinte";break;
					Case 30: $t = "Treinta";break;
					Case 40: $t = "Cuarenta";break;
					Case 50: $t = "Cincuenta";break;
					Case 60: $t = "Sesenta";break;
					Case 70: $t = "Setenta";break;
					Case 80: $t = "Ochenta";break;
					Case 90: $t = "Noventa";break;
					Case 100: $t = "Cien";break;
					Case 200: $t = "Doscientos";break;
					Case 300: $t = "Trescientos";break;
					Case 400: $t = "Cuatrocientos";break;
					Case 500: $t = "Quinientos";break;
					Case 600: $t = "Seiscientos";break;
					Case 700: $t = "Setecientos";break;
					Case 800: $t = "Ochocientos";break;
					Case 900: $t = "Novecientos";break;
					Case 1000: $t = "Mil";break;
					Case 1000000: $t = "Mill?n";break;
				}
	
				If($t == $this->Void)
				{
					$i = $i + 1;
					$x = $x / 1000;
					If($x== 0) $i = 0;
				}
				else
					break;
	
			}while($i != 0);
	
			$Rtn = $t;
			Switch($i)
			{
				Case 0: $t = $this->Void;break;
				Case 1: $t = " Mil";break;
				Case 2: $t = " Millones";break;
				Case 3: $t = " Billones";break;
			}
			return($Rtn . $t);
		}
	}     
     
   
                                                                                                                  