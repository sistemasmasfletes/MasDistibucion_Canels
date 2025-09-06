<?php

class OperationController_ActivityReportController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }
    
    public function indexAction(){}
    
    public function getScheduledRouteAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $scheduleId = $this->getArrayValue('scheduleId',$params);
        $groupedByRoute = (int)$this->getArrayValue('groupedByRoute',$params);
        $routeName = $this->getArrayValue('routeName',$filter);
        $vehicleName = $this->getArrayValue('vehicleName',$filter);
        $driverName = $this->getArrayValue('driverName',$filter);
        $startDate = $this->getArrayValue('startDate',$filter);
        $endDate = $this->getArrayValue('endDate',$filter);

        try{
            $startDate = ($startDate!=null)? substr(str_replace('T', ' ', $startDate),0,10) : null;
            $endDate = ($endDate!=null)? substr(str_replace('T', ' ', $endDate),0,10) : null;
            
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $schedules=$scheduleRepo->getScheduledRoute($page,$rowsPerPage,$sortField,$sortDir
                ,$groupedByRoute==0 ? false : true
                ,$scheduleId
                ,$startDate,
                $endDate,
                $routeName,
                $vehicleName,
                $driverName, 
                $this->currentUserId);

            echo json_encode($schedules);

        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'groupedByRoute','scheduleId','startDate','endDate'
                ,'routeName','vehicleName','driverName');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    public function getScheduledRouteActivityDetailAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $scheduledRouteId = $this->getArrayValue('scheduledRouteId',$params);

        try{
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $schedules=$scheduleRepo->getScheduledRouteActivityDetail($page,$rowsPerPage,$sortField,$sortDir
                ,$scheduledRouteId
                );

            echo json_encode($schedules);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'scheduledRouteId');
            $this->logAndResolveException($ex,$params);
        }
    }

    public function getRoutePointActivityAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $scheduledRouteId = $this->getArrayValue('scheduledRouteId',$params);
        $routePointId = $this->getArrayValue('routePointId',$params);

        try{
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $schedules=$scheduleRepo->getActivityDetail($page,$rowsPerPage,$sortField,$sortDir
                ,$scheduledRouteId
                ,$routePointId
                );
    
            echo json_encode($schedules);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'scheduledRouteId','routePointId');
            $this->logAndResolveException($ex,$params);
        }
    }

    public function getPackageTrackingAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $orderId = (int)$this->getArrayValue('orderId',$params);

        try{
            if(!$orderId)
                $orderId = (int)$this->getArrayValue('orderId',$filter);

            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            
            $tracking=$scheduleRepo->getPackageTracking($page,$rowsPerPage,$sortField,$sortDir
                ,$orderId
                );

            echo json_encode($tracking);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'orderId');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    
    public  function stopPackageAction(){
        $params = $this->getRequest()->getPostJson();
        $routePointActivityId = $this->getArrayValue('routePointActivityId', $params);
        $transaction_id = $this->getArrayValue('transaction_id', $params);
        
        try {
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');
            $pack = $scheduleRepo->stopPackage($routePointActivityId);
            echo json_encode($pack);
        } catch (Exception $ex) {
            $params = (compact('$routePointActivityId'));
            $this->logAndResolveException($ex, $params);
        }
        
    }
}