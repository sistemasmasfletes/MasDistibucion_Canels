<?php
use com\masfletes\db\DBUtil;

class OperationController_RoutesController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
           $this->createResponse401();
    }
    
    public function indexAction() {}
    
    public function getRoutesAction() {
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = $this->getArrayValue('id', $params);
        
        if (!$sidx)
            $sidx = 1;
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');           
            $conn = $this->getConnection();
         
            $parameters = array($this->getUserSessionId(), $page, $limit, $sidx, $sord, $id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routes_listar', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('page','limit','sidx','sord','id');            
            $this->logAndResolveException($ex,$params);
        }
        
    }
    
    public function saveAction(){
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);        
        $code = $this->getArrayValue('code', $params);
        $name = $this->getArrayValue('name', $params);
        $status = $this->getArrayValue('status', $params);
        $capacity = $this->getArrayValue('capacity', $params);
        $factor = $this->getArrayValue('factor', $params);
        $close = $this->getArrayValue('close', $params) == null ? 0 : $this->getArrayValue('close', $params);
        $zone_id = $this->getArrayValue('zone_id', $params);
        $franchisee_id = $this->getArrayValue('franchisee_id', $params);
         $controllerId = Model3_Auth::getCredentials("id");
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', ($id==null) ? 'agregar' : 'editar');
            
            $conn = $this->getConnection();
            $parameters = array($id, $code, $name, $status, $capacity, $factor, $close, $controllerId, $zone_id, $franchisee_id);
//            print_r($parameters);exit;
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routes_guardar', $parameters, PDO::FETCH_NAMED);
            print_r($resultsets);exit;
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('id','code','name','status','capacity','factor','close','controllerId', 'zone_id');
            $this->logAndResolveException($ex,$params);
        }        
    }
    
    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();
        $routeId = $params['id'];
        
        try {
            if($routeId==null)
                $this->generateUserException("Debe proporcionar una ruta válida");

            $this->hasPermission($this->getUserSessionId(), 'rutas', 'eliminar');
            
            $conn = $this->getConnection();
            $parameters = array($routeId);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routes_borrar', $parameters, PDO::FETCH_NAMED);
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {            
            $params = compact('routeId');
            $this->logAndResolveException($ex,$params);
        }
     }
     
     public function getRoutePointsAction(){
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $idRoute = $this->getArrayValue('route_id', $params);
        $srch = $this->getArrayValue('srch', $params);
        
        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');            
            $em = $this->getEntityManager('DefaultDb');
            $routePointRepo = $em->getRepository('DefaultDb_Entities_RoutePoint');
            $resultsets = $routePointRepo->getRoutePointsFromRoute($page, $limit, $sidx, $sord, $idRoute, $srch);

            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('page','limit','sidx','sord','idRoute');
            $this->logAndResolveException($ex,$params);
        }        
    }
    
    public function routePointSaveAction() {
        $params = $this->getRequest()->getPostJson();
        
        $id = $this->getArrayValue('id', $params);        
        $route_id = $this->getArrayValue('route_id', $params);
        $point_id = $this->getArrayValue('point_id', $params);
        $order_number = ($this->getArrayValue('order_number', $params)=="")?null:$this->getArrayValue('order_number', $params);
        $status = $this->getArrayValue('status', $params);
        //$arrival_time = $this->getArrayValue('arrival_time', $params);
        $arrival_min = (int)$this->getArrayValue('arrival_min', $params);
        $arrival_seg = (int)$this->getArrayValue('arrival_seg', $params);
        $required = $this->getArrayValue('required', $params);
		$arrival_time = $arrival_min; 
        try {

            /*if($point_id==null || $arrival_time==null)
                $this->generateUserException("El punto de venta y el tiempo de llegada no pueden estar en blanco.");
           
            if($arrival_time<0||$arrival_time>59)
                $this->generateUserException("El tiempo de llegada debe estar entre 0 y 59 minutos");*/

            $this->hasPermission($this->getUserSessionId(), 'rutas', ($id==null) ? 'agregar' : 'editar');
            
            $conn = $this->getConnection();
           $parameters = array($id, $route_id, $point_id, $order_number, $status, $arrival_time, $required, $arrival_seg);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routePoint_save', $parameters, PDO::FETCH_NAMED);

            $em = $this->getEntityManager('DefaultDb');
            
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            $ratexist = $packageRateRepo->findBy(array('elementId' => $point_id, 'elementType' => '2'));
            if(count($ratexist) === 0){
            	$dateRate = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
            	$rate = $packageRateRepo->save(null,$point_id,'2',$dateRate,2.1,1.6);
            }
            
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('id','route_id','point_id','order_number','arrival_time','required');			var_dump($ex);
            $this->logAndResolveException($ex,$params);
        }
        
    }
    
    public function routePointDeleteAction() {
        $params = $this->getRequest()->getPostJson();
        $routePointId = $this->getArrayValue('id', $params);
        
        try {
            if ($routePointId == null)
                $this->generateUserException("Debe proporcionar un id de punto de venta válido");

            $this->hasPermission($this->getUserSessionId(), 'rutas', 'eliminar');

            $conn = $this->getConnection();
            $parameters = array($routePointId);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routePoint_delete', $parameters, PDO::FETCH_NAMED);
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('routePointId');
            $this->logAndResolveException($ex,$params);
        }        
    }
    
    public function routePointChangeOrderAction(){
        $params = $this->getRequest()->getPostJson();
        $routePointId = $this->getArrayValue('id', $params);
        $dir = $this->getArrayValue('dir', $params);
                
        try {
            if ($routePointId == null)
                 $this->generateUserException("Debe proporcionar un id de punto de venta válido");

            $this->hasPermission($this->getUserSessionId(), 'rutas', 'agregar');
            
            $conn = $this->getConnection();
            $parameters = array($routePointId, $dir);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routePoint_changeOrder', $parameters, PDO::FETCH_NAMED);
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('routePointId','dir');            
            $this->logAndResolveException($ex,$params);
        }        
    }
    
    public function routeToggleOpenCloseAction(){
        $params = $this->getRequest()->getPostJson();
        $routeId = $this->getArrayValue('id', $params);
        $close = $this->getArrayValue('close', $params);
        
        try {
            if ($routeId == null)
                $this->generateUserException("Debe proporcionar un id de ruta válido");

            $this->hasPermission($this->getUserSessionId(), 'rutas', 'editar');
            $conn = $this->getConnection();
            $parameters = array($routeId, $close);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routes_toggleOpenClose', $parameters, PDO::FETCH_NAMED);
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('routeId','close');            
            $this->logAndResolveException($ex,$params);
        }
    }
    
    function getRouteCatalogAction(){
        $params = $this->getRequest()->getPostJson();
        $routeName = $this->getArrayValue('param1', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $conn = $this->getConnection();
            $parameters = array($this->getUserSessionId(),$routeName);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_routeCatalog_list', $parameters, PDO::FETCH_NAMED);
            unset($conn);
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('routeName');
            $this->logAndResolveException($ex,$params);
        }        
    }
}