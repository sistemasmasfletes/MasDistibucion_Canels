<?php

use com\masfletes\db\DBUtil;

class OperationController_PointsController extends JController {

    const AUTHORIZED_ADDRESS_CODE = 1;
    const ROL_ADMIN_CODE = 1;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }

    public function indexAction() {
        
    }

    public function getPointsAction() {
        error_log("ENTRA A LOS PUNTOS DE VENTAS");
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $srch = $this->getArrayValue('srch', $params);
        
        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $pointsRepo = $em->getRepository('DefaultDb_Entities_Point');
            $points = $pointsRepo->getPointsListDQL($page, $rowsPerPage, $sortField, $sortDir, $sidx, $srch);

            echo json_encode($points);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getPointByIdAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $pointIdRepo = $em->getRepository('DefaultDb_Entities_Point');
            $pointId = $pointIdRepo->getPointByIdDQL($id);

            echo json_encode($pointId);
        } catch (Exception $e) {
            $params = compact('id');
            $this->logAndResolveException($e, $params);
        }
    }

    public function saveAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $code = $this->getArrayValue('code', $params);
        $name = $this->getArrayValue('name', $params);
        $type = $this->getArrayValue('type', $params);
        $status = $this->getArrayValue('status', $params);
        $controller = $this->getArrayValue('controller', $params);
        $openingTime = $this->getArrayValue('opening_time', $params);
        $closingTime = $this->getArrayValue('closing_time', $params);
        $acTime = $this->getArrayValue('activitytime', $params);
        $comments = $this->getArrayValue('comments', $params);
        $deleted = $this->getArrayValue('deleted', $params);
        $webpage = $this->getArrayValue('webpage', $params);
        $categoryId = $this->getArrayValue('categoryId_id', $params);
        $address = $this->getArrayValue('address_id', $params);
        $extNumber = intval($this->getArrayValue('extNumber', $params));
        $intNumber = $this->getArrayValue('intNumber', $params);
        $urlGoogleMaps = $this->getArrayValue('urlGoogleMaps', $params);
        $neighborhood = $this->getArrayValue('neighborhood', $params);
        $zipcode = intval($this->getArrayValue('zipcode', $params));
        $countryid = $this->getArrayValue('country_id', $params);
        $stateid = $this->getArrayValue('state_id', $params);
        $cityid = $this->getArrayValue('city_id', $params);
        $phone = $this->getArrayValue('phone', $params);
        $contact = $this->getArrayValue('contact', $params);
        $idroute = $this->getArrayValue('route_id', $params);
        $iduserbranche = $this->getArrayValue('userbancheid', $params);
        $idbranche = $this->getArrayValue('brancheid', $params);
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', ($id == null) ? 'agregar' : 'editar');

            $em = $this->getEntityManager('DefaultDb');
            $pointsRepo = $em->getRepository('DefaultDb_Entities_Point');

            $points = $pointsRepo->save(
                    $id,
            		$code,
            		$name,
            		$type,
            		$status,
            		$controller,
            		$openingTime,
            		$closingTime,
            		$acTime,
            		$comments,
            		$deleted,
            		$webpage,
            		$categoryId,
            		$address,
            		$extNumber,
            		$intNumber,
            		$urlGoogleMaps,
					$neighborhood,
		    		$zipcode,
		    		$countryid,
		    		$stateid,
		    		$cityid,
					$phone,
					$contact);
            
            if($id != ''){/*cambiar el nombre en los campos relacionados de la tabla branches_user*/            	
            	$pointsRepo->upBranchesP($id,$name);
            }
            
            if(isset($idroute)){
            	$lastposition = $pointsRepo->getmaxPosition($idroute);
            	$pointsRepo->createRoutepoint($idroute,$points,$lastposition[0]["lasto"]);

               	$packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            	$ratexist = $packageRateRepo->findBy(array('elementId' => $points, 'elementType' => '2'));
            	if(count($ratexist) === 0){
            		$dateRate = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
            		$rate = $packageRateRepo->save(null,$points,'2',$dateRate,2.1,1.6);
            	}
            }
            
            if(isset($idbranche)){/*****************ESTO ES PARA GENERAR LA RELACION PUNTO-USUARIO AUTOMATICAMENTE DESDE SUCURSALES POR APROBAR*************/
            	/*
				$branche = $em->getRepository('DefaultDb_Entities_BranchesUser')->findOneBy(array('id' => $idbranche));
            	$p = $em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $points));
            	$branche->setPoint($p);
            	$em->persist($branche);
            	$em->flush();
            	
            	$correo = "<html><body>
            			<span>Hola ".$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName().",<br />
            			es un gusto comunicarnos con usted para poder informarle que el alta de su ubicaci&oacute;n: ".$p->getName()."
            			en nuestro portal ya quedo REALIZADO CON &Eacute;XITO.<br >
            			".$p->getAddress()->getAddress().", ".$p->getExtNumber()." ".$p->getIntNumber().", ".$p->getNeighborhood()."<br />
            			podr&aacute;s ver los detalles ingresando masdistribucion.com con tu usuario y contrase&ntilde;a.<br >
            			Muchas gracias, esperamos que pronto comparta su experiencia en nuestro portal.<br >
            			Tambi&eacute;n es importante que conozca las tiendas con las que contamos.<br >
            			Que tenga un excelente d&iactue;a.<br >
            		</body></html>";
            	$mailsend = $this->sendMail($correo,"notificacionesmasdistribucion@gmail.com",$branche->getClient()->getMail()/*donde llega*//*,"Aviso sucursal en ruta");
            	$correo1 = "<html><body>
            			<span>Hola Controlador,<br />
            			la sucursal ".$p->getName()." del cliente ".$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName()."<br >
            			ha sido ingresada a una ruta exitosamente<br >
            			Que tenga un excelente d&iactue;a.<br >
            		</body></html>";
            	$mailsend1 = $this->sendMail($correo1,"notificacionesmasdistribucion@gmail.com","notificacionesmasdistribucion@gmail.com"/*donde llega*//*,"Aviso sucursal en ruta");
            	$controleruser= $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => 32));//OBTENCION DE LOS DATOS DEL USUARIO LOGEADO
            	
            	$data = array(
            			'title' => 'Sucursal en Ruta',
            			'body' => 'Se ha agregado el punto del cliente '.$branche->getClient()->getFirstName()." ".$branche->getClient()->getLastName().' a una ruta'
            	);
            	
            	$this->sendPushNotification($controleruser->getToken(),  $data);
            	
            	if($branche->getClient()->getToken() != ""){
            		$data1 = array(
            				'title' => 'Sucursal en Ruta',
            				'body' => 'Hola, te informamos que tu sucursal ya esta en ruta para recibir entregas'
            		);
            		$this->sendPushNotification($branche->getClient()->getToken(),  $data1);
            	}
				*/
            }
            
            $pointresult = json_encode(array("r",$points));
            echo $pointresult;
            
        } catch (Exception $ex) {
			echo  $ex->getMessage();
            $params = compact('id', 'code', 'name', 'type', 'status', 'address_id', 'categoryId_id', 'opening_time', 'opening_time', 'webpage', 'comments', 'deleted', 'extNumber', 'intNumber', 'urlGoogleMaps');
            $this->logAndResolveException($ex, $params);
        }
		
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $deleted = 0;

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', ($id == null) ? 'agregar' : 'editar');

            $em = $this->getEntityManager('DefaultDb');
            $pointsRepo = $em->getRepository('DefaultDb_Entities_Point');

            $points = $pointsRepo->delete($id, $deleted);

            echo json_encode($points);
        } catch (Exception $e) {
            $params = compact('id', 'deleted');
            $this->logAndResolveException($e, $params);
        }
    }

    public function getPointByNameAction() {
        $params = $this->getRequest()->getPostJson();
        $pointName = $this->getArrayValue('param1', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $conn = $this->getConnection();
            $parameters = array($pointName, $this->currentUserId);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_getPointByName', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('pointName');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function generatePDFAction() {
        include '../library/phpqrcode/phpqrcode.php';
        try {
            $PNG_TEMP_DIR = dirname('..\public\images') . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR;
            $PNG_WEB_DIR = '../public/qrcode/'; //html PNG location prefix
            if (!file_exists($PNG_TEMP_DIR)) {
                mkdir($PNG_TEMP_DIR);
            }

            $params = $this->getRequest()->getPostJson();
            $id = $this->getArrayValue('id', $params);

            $em = $this->getEntityManager('DefaultDb');
            $pointsRepo = $em->getRepository('DefaultDb_Entities_Point');
            $codePoint = $pointsRepo->getCodePointListDQL($id);

            $arrCode = array();
            foreach ($codePoint as $code) {
                $arrCode = array(
                    "code" => $code["code"]
                );
            }

            $pointCode = $code["code"];
            error_log("PNG_TEMP_DIR ->".$PNG_TEMP_DIR);
            error_log("pointCode ->".$pointCode);
            $filename = $PNG_TEMP_DIR . $pointCode . '.png';
            $errorCorrectionLevel = 'M';
            $matrixPointSize = 10;

            QRcode::png($pointCode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getAddressAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');

            $whereZones = "";

            // Obtener todas las zonas del usuario
            // Si no es administrador, se filtran las direcciones por zonas asignadas al usuario
            if ($_SESSION['__M3']['MasDistribucion']['Credentials']['role'] != self::ROL_ADMIN_CODE) {
                $dqlz = $em->createQueryBuilder();
                $dqlz->select('z')
                        ->from('DefaultDb_Entities_UserZone', 'z')->where('z.user = ' . $this->currentUserId . '');
                $query = $em->createQuery($dqlz);
                $zonas = $query->getResult();

                $numZonas = count($zonas);
                $counter = 0;


                $whereZones .= " (";
                foreach ($zonas as $zona) {
                    $counter += 1;
                    $zoneId = $zona->getZone()->getId();

                    if ($counter == $numZonas) {
                        $whereZones .= ' a.zoneId = ' . $zoneId . ' OR a.zoneId is null ';
                    } else {
                        $whereZones .= ' a.zoneId = ' . $zoneId . ' OR ';
                    }
                }
                $whereZones .= ')';
            }

//            print_r($whereZones);
            $dql = $em->createQueryBuilder();
            $dql->select('a')
                    ->from('DefaultDb_Entities_Address', 'a')
                    ->where('a.city=' . $id)
                    ->andWhere($whereZones)
                    ->andWhere('a.authorized=' . self::AUTHORIZED_ADDRESS_CODE)
					->orderBy('a.address','ASC');

            $query = $em->createQuery($dql);

            $addresses = $query->getResult();

            $arrAddress = array();
            foreach ($addresses as $address) {
                $arrAddress[] = array(
                    "id" => $address->getId(),
                    "address" => $address->getAddress() . "  , " . $address->getNeighborhood() . ", " . $address->getZipcode()
                );
            }

            echo json_encode($arrAddress);
        } catch (Exception $ex) {
            $params = array();
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getContactAction() {
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $id = $this->getArrayValue('pointId', $params);
        $sidx = $this->getArrayValue('sortField', $params);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $contactRepo = $em->getRepository('DefaultDb_Entities_Contact');
            $contact = $contactRepo->getContactListDQL($page, $rowsPerPage, $sortField, $sortDir, $id);

            echo json_encode($contact);
        } catch (Exception $e) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'id');
            $this->logAndResolveException($e, $params);
        }
    }

    public function getContactByIdAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $contactId = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $contactIdRepo = $em->getRepository('DefaultDb_Entities_Contact');
            $contact = $contactIdRepo->getContactByIdListDQL($page, $rowsPerPage, $sortField, $sortDir, $contactId);

            echo json_encode($contact);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'id');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function saveContactAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $pointId = $this->getArrayValue('point_id', $params);
        $name = $this->getArrayValue('name', $params);
        $job = $this->getArrayValue('job', $params);
        $email = $this->getArrayValue('email', $params);
        $phoneNumber = $this->getArrayValue('phone_number', $params);
        $status = $this->getArrayValue('status', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', ($id == null) ? 'agregar' : 'editar');

            $em = $this->getEntityManager('DefaultDb');
            $contactRepo = $em->getRepository('DefaultDb_Entities_Contact');
            $contact = $contactRepo->saveContact($id, $pointId, $name, $job, $email, $phoneNumber, $status);

            echo json_encode($contact);
        } catch (Exception $ex) {
            
        }
    }

    public function deleteContactAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $em = $this->getEntityManager('DefaultDb');
        $contactRepo = $em->getRepository('DefaultDb_Entities_Contact');
        $contact = $contactRepo->delete($id);

        echo json_encode($contact);
    }

    public function getClassificationByIdAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $classIdRepo = $em->getRepository('DefaultDb_Entities_Classification');
            $classId = $classIdRepo->getClassificationByIdListDQL($id);

            echo json_encode($classId);
        } catch (Exception $e) {
            $params = compact('id');
            $this->logAndResolveException($e, $params);
        }
    }

    public function saveClasifficationAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $size = $this->getArrayValue('size', $params);
        $activity = $this->getArrayValue('activity', $params);
        $consumption = $this->getArrayValue('consumption', $params);
        $idPoint = $this->getArrayValue('pointId', $params);

        try {

            $em = $this->getEntityManager('DefaultDb');
            $classificationRepo = $em->getRepository('DefaultDb_Entities_Classification');
            $classification = $classificationRepo->saveClasiffication($id, $size, $activity, $consumption, $idPoint);

            echo json_encode($classification);
        } catch (Exception $ex) {
            $params = compact('id', 'size', 'activity', 'consumption', 'idPoint');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getCountryAction() {
      $ACTIVO = 1;
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'LISTAR');
            
           

            $em = $this->getEntityManager('DefaultDb');
           // $countries = $query->getResult();
            $countries = $em->getRepository('DefaultDb_Entities_Paises')->findBy(array('estado' => $ACTIVO));  
            $arrCountry = array();
            foreach ($countries as $country) {
                $arrCountry[] = array(
                    "id" => $country->getId(),
                    "name" => $country->getNombre());
            }

            echo json_encode($arrCountry);
        } catch (Exception $ex) {
            $params = array();
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getStateAction() {
        $params = $this->getRequest()->getPostJson();
 
        $id = $this->getArrayValue('id', $params);
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'LISTAR');
            $ACTIVO =1;
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('s')
                    ->from('DefaultDb_Entities_State', 's')
                    ->where("s.country = $id")
                    ->andWhere('s.estatus ='.$ACTIVO);

            $query = $em->createQuery($dql);
            $states = $query->getResult();

            $arrStates = array();
            foreach ($states as $state) {
                $arrStates[] = array(
                    "id" => $state->getId(),
                    "name" => $state->getName());
            }

            echo json_encode($arrStates);
        } catch (Exception $ex) {
            $params = array();
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getCityAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'LISTAR');
            $ACTIVO = 1;
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('c')
                    ->from('DefaultDb_Entities_City', 'c')
                    ->where('c.state =' . $id)
                    ->andWhere('c.estatus ='.$ACTIVO);

            $query = $em->createQuery($dql);
            $cities = $query->getResult();

            $arrCities = array();
            foreach ($cities as $city) {
                $arrCities[] = array(
                    "id" => $city->getId(),
                    "name" => $city->getName());
            }

            echo json_encode($arrCities);
        } catch (Exception $ex) {
            $params = array();
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getRoutesAction() {
    	//$ACTIVO = 1;
    	try {
    		//$this->hasPermission($this->getUserSessionId(), 'rutas', 'LISTAR');
    
    		$em = $this->getEntityManager('DefaultDb');
    		$routes = $em->getRepository('DefaultDb_Entities_Route')->findAll();
    
    		$arrRoutes = array();
    		foreach ($routes as $route) {
    			$arrRoutes[] = array(
    					"id" => $route->getId(),
    					"name" => $route->getName());
    		}
    		echo json_encode($arrRoutes);
    	} catch (Exception $ex) {
    		$params = array();
    		echo $ex->getMessage();
    		$this->logAndResolveException($ex, $params);
    	}
    }    
}
