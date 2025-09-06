<?php

class Warehouseman_WarehousemanController extends JController{
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }
    
    public function indexAction() {}
    
    //Funcion rutas que pasan por un CI
    public function getViewWarehousemanRoutesAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $sidx = $this->getArrayValue('sortField', $params);
        
        if (!$sidx)
            $sidx = 1;
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            
            $em = $this->getEntityManager('DefaultDb');
            $storerRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            $storer=$storerRepo->getViewWarehousemanRoutesListDQL($page,$rowsPerPage,$sortField,$sortDir,$sidx);
            
            echo json_encode($storer);
        } catch (Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx');
            $this->logAndResolveException($ex,$params);
        }   
    }
    
    //Función programaciones de la ruta
    public function getWarehouseAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $routeId = $this->getArrayValue('routeId',$params);
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $schedules=$scheduleRepo->getWarehouseListDQL($page,$rowsPerPage,$sortField,$sortDir,
                    $routeId);

            echo json_encode($schedules);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'routeId');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //Funcion actividades del CI
    public function getWarehousePacksAction (){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $scheduleRouteId = $this->getArrayValue('scheduleRouteId',$params);
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $schedules=$scheduleRepo->getWarehousePacksListDQL($page,$rowsPerPage,$sortField,$sortDir,
                    $scheduleRouteId);

            echo json_encode($schedules);
        } catch (Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'scheduledRouteId');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //Función paquetes entrega o recolección del CI
    public function getWarehousemanActivityAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $scheduleRouteId = $this->getArrayValue('scheduleRouteId',$params);
        $routePointId = $this->getArrayValue('routePointId', $params);
        $id = $this->getArrayValue('id', $params);
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            $em = $this->getEntityManager('DefaultDb');
            $activityRepo=$em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $activities=$activityRepo->getWarehousemanActivityListDQL($page,$rowsPerPage,$sortField,$sortDir,
                    $scheduleRouteId,
                    $routePointId,
                    $id);
            
            echo json_encode($activities);
        }  catch (Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'scheduledRouteId','routePointId',
                    'id');
            $this->logAndResolveException($ex,$params);
        }
    }
            
    //Función salvar transferencia
    public function saveAction(){
        $params = $this->getRequest()->getPostJson();
        
        $id = $this->getArrayValue('id', $params);
        $userDelivery = $this->getArrayValue('userDelivery', $params);
        $userReceiving = $this->getArrayValue('userReceiving', $params);
        $status = $this->getArrayValue('status', $params);
        //$status = 1;
        $userAbsence = $this->getArrayValue('userAbsence', $params);
        $statusReason = $this->getArrayValue('statusReason', $params);
        $entityFrom = $this->getArrayValue('entityFrom', $params);
        $entityTo = $this->getArrayValue('entityTo', $params);
        $OCId = $this->getArrayValue('orderId', $params);
        $RPId = $this->getArrayValue('routePointId', $params);
        
        
        try{
            //$this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'agregar');
            
        	$this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'agregar');
        	
            $em = $this->getEntityManager('DefaultDb');
            $warehouseRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
            
        	if($status == 2){
        		
        		$dataTrns = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId' => $OCId));
        		$dataSqAct = $em->getRepository('DefaultDb_Entities_SequentialActivities')->findOneBy(array('order' => $OCId, 'routePoint' => $RPId));
        		$warehouseRepo->delARouting($OCId,$id,$dataTrns->getId(),$dataSqAct->getId());
        	}
        	
            $warehouse=$warehouseRepo->saveWarehousemanTransfer(
                    $id,
                    $userDelivery,
                    $userReceiving,
                    $status,
                    $userAbsence,
                    $statusReason,
                    $entityFrom,
                    $entityTo);
            
            echo json_encode($warehouse);
        }  catch (Exception $ex){
            $params = compact('id', 'userDelivery', 'userReceiving', 'status', 'userAbsence', 'statusReason', 'entityFrom', 'entityTo');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //Rastreo de paquetes
    public function getPackageTrackingWarehousemanAction (){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $orderId = (int)$this->getArrayValue('orderId',$params);
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            
            if(!$orderId)
                $orderId = (int)$this->getArrayValue('orderId',$filter);

            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $tracking=$scheduleRepo->getPackageTrackingWarehousemanListDQL($page,$rowsPerPage,$sortField,$sortDir,
                    $orderId);

            echo json_encode($tracking);
        } catch (Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'orderId');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //FUNCION LISTAR CATALOGO DE USUARIOS A RECIBIR 
    //EN PANTALLA SALVAR TRANSFERENCIA MODULO ALMACENISTA
    //FUNCIÓN CATALOGO DE USUARIOS EN PANTALA SALVAR TRANFERENCIA MODULO CHOFER
    public function getUserDeliveryAction(){
        try {
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('u')
                ->from('DefaultDb_Entities_User', 'u')
                ->where('u.type=2 or u.type=6');
            $query=$em->createQuery($dql);
            $users = $query->getResult();

            $arrUserDelivery = array();
            foreach($users as $uDelivery){
                $arrUserDelivery[] = array(
                    "id" => $uDelivery->getId(),
                    "firstName" => $uDelivery->getFirstName(),
                    "role" => $uDelivery->getType());
            }
            
            echo json_encode($arrUserDelivery);
        } catch (Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);
        }
    }
    
    /**********************PAQUETES RECHAZADOS****************************************/
    public function getPackageRejectedAction (){
    	$params = $this->getRequest()->getPostJson();
    
    	$page = $this->getArrayValue('page', $params);
    	$rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
    	$sortField = $this->getArrayValue('sortField', $params);
    	$sortDir = $this->getArrayValue('sortDir', $params);
    	$filter= $this->getArrayValue('filter',$params);
    	$orderId = (int)$this->getArrayValue('orderId',$params);
    
    	try {

    		$em = $this->getEntityManager('DefaultDb');
    		$Orders = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
    
    		$rejected=$Orders->getOrdersRejected();
    
    		echo json_encode(array('data'=>$rejected,'meta'=>array('totalRecords'=>0)));
    	} catch (Exception $ex){
    		$params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'orderId');
    		$this->logAndResolveException($ex,$params);
		}
    }
    
    public function getPackageRejectedByIdAction (){
    	$params = $this->getRequest()->getPostJson();
    
    	$orderId = (int)$this->getArrayValue('Oid',$params);
    
    	try {

    		$em = $this->getEntityManager('DefaultDb');
    		$Orders = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
    
    		$rejected=$Orders->getOrdersRejected($orderId);
    		
    		echo json_encode(array('data'=>$rejected,'meta'=>array('totalRecords'=>0)));
    	} catch (Exception $ex){
    		$params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'orderId');
    		$this->logAndResolveException($ex,$params);
    	}
    }
    
    public function getScheduleRejectedAction()
    {
    	$em =  $this->getEntityManager('DefaultDb');
    	$scheduleAdapter =  $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
    		$post = $this->getRequest()->getPostJson();
    		
    		$originPoint = (int)$this->getArrayValue('pointId',$post);
    		
    		$schedulerejected = $scheduleAdapter->getScheduledRejected($originPoint,new DateTime());
    		
    		echo json_encode($schedulerejected);
    }    
    
    public function setScheduleRejectedAction()
    {
    	$params = $this->getRequest()->getPostJson();
    	
    	$orderId = (int)$this->getArrayValue('Oid',$params);
    	
    	$em =  $this->getEntityManager('DefaultDb');
    	
        //$routePointActivitiRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
        $sequentialActivitiesRepo = $em->getRepository('DefaultDb_Entities_SequentialActivities');
        $transactionData = $em->getRepository('DefaultDb_Entities_Transactions');
        $routePointActivity = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
        
        
        //Obtener las actividades secuenciales de la orden de compra original (puntos de ruta)
        $sequentialActivities = $sequentialActivitiesRepo->findBy(array('order'=>$orderId));
        $transactionId = $transactionData->findOneBy(array('transactionId'=>$orderId, 'transactionType' => 1));
        
        try{
	        //Generar la actividad por punto de venta para la orden de compra original
	        foreach ($sequentialActivities as $sa) {
	        	
	        	$dql = "SELECT sr FROM DefaultDb_Entities_ScheduledRoute sr WHERE DATE(sr.scheduledDate) = DATE(:scheduledDate) and sr.route = :route ";
	        	$query=$em->createQuery($dql);
	        	$query->setParameter('scheduledDate',$sa->getShippingDate());
	        	$query->setParameter('route', $sa->getRoutePoint()->getRoute()->getId());
	        	$scheduledRoute = $query->getOneOrNullResult();
	        	
	        	$schedulerejected = $routePointActivity->saveRejActiv($sa->getShippingDate(),$sa->getRoutePoint()->getId(),$sa->getType(),$transactionId->getId(),$scheduledRoute->getId());
	        	$arrayres[]=array( "results"=>$schedulerejected);
	        	
	        }
	        $res = $arrayres;
	 	} catch (Exception $ex){
        	$res = $ex;
        	$this->logAndResolveException($ex,$params);
        }
    	echo json_encode($res);
     
    }
    /**********************PAQUETES RECHAZADOS****************************************/
   
}
