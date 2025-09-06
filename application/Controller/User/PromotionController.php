<?php

class User_PromotionController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
           $this->createResponse401();
    }
    
    public function indexAction() {}

    public function getPromotionAction(){
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);       
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);

        $parametros = array();
        $parametros["page"] = $page ? $page : 1;
        $parametros["rowsPerPage"] = $rowsPerPage ? $rowsPerPage : 10;
        $parametros["sortField"] = $this->getSortField($params);
        $parametros["sortDir"] = $sortDir ? $sortDir : "ASC";
        
        //Setear filtros por defecto si no los hay
        if(!$filter) $filter = array();
        $filter["promotionId"] = $this->getArrayValue('promotionId',$filter) ? $this->getArrayValue('promotionId',$filter) : null;
        $filter["name"] = $this->getArrayValue('name',$filter) ? $this->getArrayValue('name',$filter) : null;
        $filter["userid"] = Model3_Auth::getCredentials("id");
        
        $parametros["filter"] = $filter;        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');

            $promotionRepo = $em->getRepository('DefaultDb_Entities_Promotion');
            $promotion = $promotionRepo->getPromotion($parametros);
            echo json_encode($promotion);
        }
        catch (Exception $ex) 
        {

            $this->logAndResolveException($ex,$parametros);
        }       
    }

    public function getPromotionDetailAction(){
        $params = $this->getRequest()->getPostJson();
        $filter= $this->getArrayValue('filter',$params);

        $parametros = array();        
        if(!$filter)
             $filter = array("promotionId"=>0);
        $parametros["filter"] = $filter;


        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');

            $promotionRepo = $em->getRepository('DefaultDb_Entities_Promotion');
            $promotion = $promotionRepo->getPromotionDetail($parametros);
            
            //Dado que los archivos se enviarán por POST, se devuelve el tamaño máximo permitido para POST
            $promotion["postMaxSize"] =  $this->getPostMaxSize();
            $promotion["fileMaxSize"] =  $this->getMaxfileUploadInBytes();
            echo json_encode($promotion);
        }
        catch (Exception $ex) 
        {

            $this->logAndResolveException($ex,$parametros);
        }     

    }

    private function getSortField($params){
        $sortField = $this->getArrayValue('sortField', $params);
        if(!$sortField)
            $sortField = "p.name";
        else
            $sortField = "p.".$sortField;
        return $sortField;
    }

    public function saveAction() {
        $request = $this->getRequest()->getPost(); 
        $uploadFileError = false;

        $aPromotion = array(
            "id" => $this->getArrayValue("promotionId", $request),
            "name" => $this->getArrayValue("name", $request),
            "userid"=>Model3_Auth::getCredentials("id")
        );
        $aPromotion["id"] = $aPromotion["id"] == "undefined" ? null : intval($aPromotion["id"]);

        $documetRoot = $_SERVER['DOCUMENT_ROOT'];
        $publicDir = $this->getRequest()->getBaseUrl();
        $userDocuments = "data/documents";
        $currentUserId = $this->currentUserId;
        $userDocumentsPublic =  $publicDir . "/" . $userDocuments . "/" . "usr" . $currentUserId;
        $targetDir = $documetRoot . $userDocumentsPublic;

        $aItems = array();
        $items = $this->getArrayValue("items", $request);
        for($i=0;$i<$items;$i++){
            $id = intval($this->getArrayValue("id".$i, $request));            
            $resourceType = $this->getArrayValue("resourceType".$i, $request);
            $file = null;            
            if($resourceType==1 && $id<0)
                $file = $_FILES["filedata".$i];            
            $path = $this->getArrayValue("path".$i, $request);
            $path = (!strpos($path, "http://") ? "http://".$path : $path);
            $filename = preg_replace("/[^a-zA-Z0-9.]/", "", $file["name"]);
            $aItems[] = array(
                "id" => $id,
                "resourceType" => $resourceType,
                "name" => $this->getArrayValue("name".$i, $request),
                "path" => $path,
                "tmpfile" => $resourceType==1 && $id<0 ? array("tmpname"=> $file["tmp_name"],"name"=>$filename) : null
            );            
        }
  
        try 
        {
            if (!file_exists($targetDir) && !is_dir($targetDir))
                mkdir($targetDir, 0777, true);
            

            $em = $this->getEntityManager('DefaultDb');
            $action = is_null($aPromotion["id"]) ? "agregar" : "editar";

            $this->hasPermission($this->getUserSessionId(), 'promociones', $action);

            $promotionRepo = $em->getRepository('DefaultDb_Entities_Promotion');
            $promotion = $promotionRepo->save($aPromotion,$aItems,$targetDir,$userDocumentsPublic);
            echo json_encode($promotion);
        }
        catch (Exception $ex) 
        {
            $this->logAndResolveException($ex,$aPromotion);
        }        
    }

    public function deleteAction(){
        $params = $this->getRequest()->getPostJson();
        $promotionId = $this->getArrayValue("id",$params);

        $documetRoot = $_SERVER['DOCUMENT_ROOT'];
        $publicDir = $this->getRequest()->getBaseUrl();
        $userDocuments = "data/documents";
        $currentUserId = $this->currentUserId;
        $userDocumentsPublic =  $publicDir . "/" . $userDocuments . "/" . "usr" . $currentUserId;
        $targetDir = $documetRoot . $userDocumentsPublic;

        try {
            $em = $this->getEntityManager('DefaultDb');
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'eliminar');
            $promotionRepo = $em->getRepository('DefaultDb_Entities_Promotion');
            $result = $promotionRepo->delete($promotionId,$targetDir);
            echo json_encode($result);

        } catch (Exception $e) {
            
        }
    }

    public function getPromotionCostingAction(){
        $CONFIG_ID=1;
        $params = $this->getRequest()->getPostJson();
        try {
            $em = $this->getEntityManager('DefaultDb');
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');
            $promotionRepo = $em->getRepository('DefaultDb_Entities_Promotion');
            $promotionCosting=$promotionRepo->getPromotionCosting($CONFIG_ID);
            echo json_encode(array("promotionCosting"=>$promotionCosting));
        } catch (Exception $e) {
            $this->logAndResolveException($ex,array());
        }
    }

    public function savePromotionScheduleAction($data){

    		$userid = intval($data["userid"]);
    		$clientid =intval($data["clientid"]);
    		$promotionid = intval($data["promotionid"]);
    		//$data["branchid"]);
    		$routepointid = intval($data["routepointid"]);
    		$promotionDate = $data["promotionDate"];
    		$totalCost = floatval($data["totalCost"]);
    	
    		$creationDate = new DateTime();
    
    		$shippingDate = DateTime::createFromFormat('Y-m-d H:i:s', $promotionDate);
    		$em = $this->getEntityManager('DefaultDb');
    
    		$em->getConnection()->beginTransaction();
    		try {
    			$routePointActivitiRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
    			$routePoint = $em->find('DefaultDb_Entities_RoutePoint',$routepointid);
    			$promotion = $em->find('DefaultDb_Entities_Promotion',$promotionid);
    			$user = $em->find('DefaultDb_Entities_User',$userid);
    			$client = $em->find('DefaultDb_Entities_User',$clientid);
    
    			//Generar el registro de la programación.
    			$promotionSchedule = new DefaultDb_Entities_PromotionSchedule();
    			$promotionSchedule->setPromotion($promotion);
    			$promotionSchedule->setCreationDate($creationDate);
    			$promotionSchedule->setPromotionDate($shippingDate);
    			$promotionSchedule->setUser($user);
    			$promotionSchedule->setClient($client);
    			$promotionSchedule->setPoint($routePoint->getPoint());
    			$promotionSchedule->setPackageOrder(null);
    			$em->persist($promotionSchedule);
    			//Se guarda la promoción debido a que la actividad por punto requiere el id de la transacción.
    			$em->flush();
    
    			//Se crea la actividad en punto.
    			$routepointActivity[] = array( "routePointId"=>$routepointid
    					,"shippingDate"=>$shippingDate
    					,"routeId"=>$routePoint->getRoute()->getId()
    					,"orderId"=>$promotionSchedule->getId()
    					,"activityType"=>6
    			);
    
    			$arrRp = $routePointActivitiRepo->createRoutePointActivites($routepointActivity,2);
    
    			// ******************************************** Generar pago ********************************************
    			$this->payPromotion($em,$promotionSchedule,$totalCost);
    
    			if(count($arrRp)==1){
    				$promotionSchedule->setActivityPoint($arrRp[0]);
    				$em->flush();
    
    				$em->getConnection()->commit();
    
    				$result["success"]=true;
    				$result["message"]='Programación creada con éxito';
    			}else{
    				$result["success"]=false;
    				$result["message"]='Error al crear la programación.';
    			}
    		}catch(Exception $ex){
    			$em->getConnection()->rollback();
    			$errMsg = $ex->getMessage();
    			$conn = $this->getConnection();
    			$requestUrl = $this->getRequest()->getRequestUri();
    			$params = array('sessionId'=>$this->getUserSessionId()) + $data;
    			$errorCode = null;
    			if($ex->getCode()==5000){
    				$errorCode = $ex->getCode();
    			} else if ($ex->getCode() == 1457) {
    				$errorCode = $ex->getCode();
    			}elseif (property_exists($ex, "errorInfo")) {
    				$errorCode = $ex->errorInfo[1];
    			}
    			DBUtil::addLog($conn,$requestUrl, $params,"code:".$errorCode."  msg:".$errMsg);
    		}
    	$this->view->response=$result;
    }
    
    function payPromotion($em,$promotionSchedule,$totalCost){
    	$ESTATUS_PENDIENTE = 2;
    	$CONGELAR_CREDITOS = 1;
    	$NEGAR_CREDITOS = 2;
    	$TIPO_CONCEPTO_COMPRA_PROMOCION = 7;
    	$MONEDA_DEFAULT="MXN";
    
    	$user = $promotionSchedule->getUser();
    	$pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
    	$tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($TIPO_CONCEPTO_COMPRA_PROMOCION);
    	$esSaldoNegativo = ($totalCost > $user->getCredito());
    	$tipoDebitar = $esSaldoNegativo ? $NEGAR_CREDITOS : $CONGELAR_CREDITOS;
    	$tipoDebito = $em->getRepository('DefaultDb_Entities_TipoDebito')->find($tipoDebitar);
    	$userMoneda = $user->getMoneda();
    	$monedaMXN = $em->getRepository('DefaultDb_Entities_TipoMonedas')->findBy(array("currencyCode"=>$MONEDA_DEFAULT));
    	$moneda = $userMoneda ? $userMoneda : ($monedaMXN ? $monedaMXN[0] : null);
    	$totalMoneda = $pagosRepo->fncCalculaCreditos($moneda->getId(), $totalCost);
    
    	$pagosJSON = array();
    	$pagosJSON["usuario"] = $promotionSchedule->getUser();
    	$pagosJSON["cliente"] = null; //No hay cliente ya que se está comprando una promoción
    	$pagosJSON["orden"] = $promotionSchedule->getId();
    	$pagosJSON["montoCompra"] = $totalMoneda;
    	$pagosJSON["montoCreditos"] =$totalCost;
    	$pagosJSON["fecha"] = new DateTime( );
    	$pagosJSON["estatus"] = $ESTATUS_PENDIENTE;
    	$pagosJSON["compraCreditos"] = null;
    	$pagosJSON["compraVenta"] = null;
    	$pagosJSON["promocion"] = $promotionSchedule;
    	$pagosJSON["tipoConcepto"] = $tipoConcepto;
    	$pagosJSON["tipoDebito"] = $tipoDebito;
    	$pagosJSON["descripcion"] = "Compra de promoción";
    
    	$pago = $pagosRepo->fncAgregarPagoPromocion($pagosJSON);
    }    
    
    public function sendPromoAction(){
    
        	$params = $this->getRequest()->getPostJson();
        	$userid = Model3_Auth::getCredentials('id');
        	$routeId = $params["id"];
    		//$pdate = $params["pdate"];
    		$pid = $params["pid"];
    		$clientid = $params["idclient"];
    		$rpid = $params["idroutepoint"];
    		$userid = intval($userid);
    		$promotionid = intval($pid);
    		$result = array();
    		
    		$em = $this->getEntityManager('DefaultDb');
    		$promotion = $em->find('DefaultDb_Entities_Promotion',$promotionid);
    		$user = $em->find('DefaultDb_Entities_User',$userid);
			//$userprueba = $em->find('DefaultDb_Entities_User',88);
			   		
    		if($clientid){    		
    			$branchesusr = $em->find('DefaultDb_Entities_BranchesUser',$rpid);
    			$routePointclient = $em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('route' => $routeId, 'point' => $branchesusr->getPoint()));
				$clients[]=  array("rpid"=>$routePointclient->getId(),"client_id" =>$clientid);
			}else{
        		$clients = $em->getRepository('DefaultDb_Entities_RoutePoint')->getClientsByRoute($routeId,$userid);
			}
			
        	$correo = "<html><body>
			   		<span>Hola,
			    	<br />
			    	te informamos que,
			    	<br />
			    	el proveedor: ".$promotion->getUser()->getFirstName()." ".$promotion->getUser()->getLastName()."
			    	<br />
			    	tiene una nueva promoci&oacute;n que te puede interesar:
			    	<br />".
        	    	$promotion->getName()
        	    	."<br />
			    	Ingresa a tu cuenta en masdistribucion.com y visita su cat&aacute;logo de productos</span><br />
			    	</body></html>";
        	
        	$data = array(
        			'title' => 'Promoción '.$promotion->getName(). ' de '.$promotion->getUser()->getFirstName()." ".$promotion->getUser()->getLastName(),
        			'body' => 'ingresa a tu cuenta en masdistribucion.com y visita tu sección de promociones.',
        	);
        	 
	    	foreach ($clients as $item){
		    	//$this->savePromotionScheduleAction($data);
		    	$clientid =intval($item['client_id']);
		    	$routepointid = intval($item['rpid']);
		    	//$promotionDate = $pdate;
		    	$totalCost = floatval(0);
		    	 
		    	$creationDate = new DateTime();
		    	//$shippingDate = DateTime::createFromFormat('Y-m-d H:i:s', $promotionDate);
		    	
		    	$em->getConnection()->beginTransaction();
		    	try {
		    		//$routePointActivitiRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
		    		$routePoint = $em->find('DefaultDb_Entities_RoutePoint',$routepointid);
		    		$client = $em->find('DefaultDb_Entities_User',$clientid);
		    		$ttype = $em->find('DefaultDb_Entities_TransactionType',2);
		    		 
		    		//Generar el registro de la programación.
		    		$promotionSchedule = new DefaultDb_Entities_PromotionSchedule();
		    		$promotionSchedule->setPromotion($promotion);
		    		$promotionSchedule->setCreationDate($creationDate);
		    		$promotionSchedule->setPromotionDate($creationDate);
		    		$promotionSchedule->setUser($user);
		    		$promotionSchedule->setClient($client);
		    		$promotionSchedule->setPoint($routePoint->getPoint());
		    		$promotionSchedule->setPackageOrder(null);
		    		$em->persist($promotionSchedule);
		    		//Se guarda la promoción debido a que la actividad por punto requiere el id de la transacción.
		    		$em->flush();
		    	
	    		 	$transaction = new DefaultDb_Entities_Transactions();
		    		$transaction->setTransactionId($promotionSchedule->getId());
		    		$transaction->setTransactionType($ttype);
		    		$transaction->setStatus(1);
		    		$em->persist($transaction);
		    		$em->flush();

		    		$result["success"]=true;
		    		$result["message"]='Programación creada con éxito';

					$token = $client->getToken();

		    		//$result["mail"] = ($client->getMail() !=="")?$this->sendMail($correo,"masdistribucion.ventas@gmail.com",$client->getMail(),"Promocion"):"";
		    		$result["mail"] = "";//se tiene  que revisar el limite de envio de correos para esta parte,por ahora esta desactivado
					$result["mensajeapp"]= ($token !=="")?$this->sendPushNotification($token,  $data):"";
		    	
		    		$em->getConnection()->commit();
		    		
		    		}catch(Exception $ex){
		    			$em->getConnection()->rollback();
		    			$result["success"]=false;
		    			$result["error"]= $ex->getMessage();
		    		}
	    	}    
	    	echo json_encode($result);
    		//$this->savePromotionScheduleAction($data);
    }    
    
    function getCategoriesAction(){
    
    	try {
    		$em = $this->getEntityManager('DefaultDb');
    		
    		//$result = $em->getRepository('DefaultDb_Entities_Category')->findBy(array(),array('name' => 'asc'));
    		$qb = $em->createQueryBuilder();
    		
    		$qb->select('u')
    		->from('DefaultDb_Entities_User', 'u')
    		->leftJoin('u.category','c')
    		->where(' c.id is not null')
    		->groupBy('c.id')
    		->orderBy('c.name', 'ASC');
    		$query = $em->createQuery($qb);
    		$result = $query->getResult();
    		
    		foreach ($result as $item){
    			$res[] = array(
    					'id' => $item->getCategory()->getId(),
    					'name' => $item->getCategory()->getName()
    			);
    		}
    		echo json_encode(array('data' => $res,'meta' => 'nada'));
    	} catch (Exception $ex) {
    		echo $ex->getMessage();
    		//$this->logAndResolveException($ex,$params);
    	}
    }    
    
    function getClientsAction(){
    	$params = $this->getRequest()->getPostJson();
    	$userid = Model3_Auth::getCredentials('id');
		$categoryId = $params["id"];
    	
    	try {
    	$em = $this->getEntityManager('DefaultDb');
    	//$result = $em->getRepository('DefaultDb_Entities_User')->findBy(array('category' => $categoryId ),array('firstName'  => 'asc'));
    	$qb = $em->createQueryBuilder();
    	
    	$qb->select('br')
    	->from('DefaultDb_Entities_BranchesUser', 'br')
    	->leftJoin('br.client','c')
    	->where(' c.id <> 24')
    	->andWhere('c.id <> '.$userid)
    	->andWhere('br.point is not null')
    	->andWhere('c.category ='.$categoryId)
    	->groupBy('c.id')
    	->orderBy('c.firstName', 'ASC');
    	$query = $em->createQuery($qb);
    	$result = $query->getResult();
    	
    	foreach ($result as $item){
	    	$res[] = array(
	    	'id' => $item->getClient()->getId(),
	    	'name' => $item->getClient()->getFirstName()." ".$item->getClient()->getLastName()." - ".$item->getClient()->getCommercialName()
	    	);
    	}
    	echo json_encode(array('data' => $res,'meta' => 'nada'));
	    } catch (Exception $ex) {
	    	echo $ex->getMessage();
	    	$this->logAndResolveException($ex,$params);
	    }
    }
    
    public function getBranchesUserAction(){
    	$post = $this->getRequest()->getPostJson();
    	$clientid =  $post['id'];
    	    	
    	try{
    		$em = $this->getEntityManager('DefaultDb');
    		$branches = $em->getRepository('DefaultDb_Entities_BranchesUser')->getBrachesRoutePerUser($clientid);
    		//$branches = $branchesUserRepos->findBy(array('client' => $clientid));
    		$arrbranches=array();
    		foreach ($branches  as $key => $branch) {
    			$arrbranches[]=array(
    					"id" => $branch['id'],
    					"rid" => $branch['rid'],
    					"nameAddress"=>$branch['direction']
    			);
    		}
    		echo json_encode(array('data' => $arrbranches,'meta' => 'nada'));
    		} catch (Exception $ex) {
    			echo json_encode(array('data' => $ex->getMessage()));
    			//$this->logAndResolveException($ex,$params);
    		}
    }   
        
    function getRoutesAction(){
    	
    	//$params = $this->getRequest()->getPostJson();
    	/*if($params["routePointActivityId"]==null) return;
    	if($params["promotionScheduleId"]==null) return;
    	if($params["tipoCobro"]==null) return;*/
    	
    	try {
    		$em = $this->getEntityManager('DefaultDb');
    		//$this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');
    		$result = $em->getRepository('DefaultDb_Entities_Route')->findAll();
    		//$result = $promotionRepo->updateStatusPromotion($params);
    		
    		foreach ($result as $item){
    			$res[] = array(
    				'id' => $item->getId(),
    				'name' => $item->getName()
    			);
    		}
    		
    		echo json_encode(array('data' => $res,'meta' => 'nada'));
    	
    	} catch (Exception $ex) {
    		$this->logAndResolveException($ex,$params);
    	}    	
    	
    }    
    
    function getSchedulesAction(){
    	 
    	$params = $this->getRequest()->getPostJson();
    	$routeId = $params["id"];
    	
    	try {
	    	$em = $this->getEntityManager('DefaultDb');

	    	$schedules = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
	    	$today = date('Y-m-d');
	    	
	    	$schedulelist = $schedules->createQueryBuilder('f')
	    	->where("f.route = :rid")
	    	->andWhere("f.scheduledDate > '".$today."'")
	    	->orderBy('f.id', 'DESC')
	    	->setParameter('rid',$routeId)
	    	->getQuery();
	    	
	    	foreach ($schedulelist->getResult() as $item){
		    	$res[] = array(
		    	'id' => $item->getId(),
		    	'name' => $item->getScheduledDate()->format('Y-m-d H:i:s')
		    	);
	    	}
	    	
	    	echo json_encode(array('data' => $res,'meta' => 'nada'));
    	 
    	} catch (Exception $ex) {
    		echo $ex->getMessage();
    		$this->logAndResolveException($ex,$params);
    	}
     
    }    
    
}