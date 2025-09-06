<?php
use com\masfletes\db\DBUtil;

class OperationController_ScheduleController extends JController {
    
     public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();              
    }
    
    public function indexAction() {}
    
    public function getSchedulesAction(){
        
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = (int)$this->getArrayValue('id', $params);
        $filter= $this->getArrayValue('filter',$params);
        $routeName = $this->getArrayValue('routeName', $filter);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Programación', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');
            $resultsets = $scheduleRepo->getSchedules($page,$limit,$sidx,$sord,$id,$routeName, $this->currentUserId);

            echo json_encode($resultsets);
           

        } catch (Exception $ex) {
            $params = compact('page', 'limit', 'sidx', 'sord', 'id');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    public function saveAction(){
        $params = $this->getRequest()->getPostJson();
        $id = (int)$this->getArrayValue('id', $params);
        $route_id = (int)$this->getArrayValue('route_id', $params);
        $vehicle_id = (int)$this->getArrayValue('vehicle_id', $params);
        $user_id = $this->getArrayValue('user_id', $params);
        $start_date = $this->getArrayValue('start_date', $params);
        $status = (int)$this->getArrayValue('status', $params);
        $monday = (int)$this->getArrayValue('monday', $params);
        $tuesday = (int)$this->getArrayValue('tuesday', $params);
        $wednesday = (int)$this->getArrayValue('wednesday', $params);
        $thursday = (int)$this->getArrayValue('thursday', $params);
        $friday = (int)$this->getArrayValue('friday', $params);
        $saturday = (int)$this->getArrayValue('saturday', $params);
        $sunday = (int)$this->getArrayValue('sunday', $params);
        $recurrent = (int)$this->getArrayValue('recurrent', $params);
        $week = (int)$this->getArrayValue('week', $params);
        $scheduleParent_id = $this->getArrayValue('scheduleParent_id', $params);
        $end_date = $this->getArrayValue('end_date', $params);        

        try {
            
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');
            
            $schedulesCreated = $scheduleRepo->save($id,$route_id,$vehicle_id,$user_id,$start_date,$status,$monday,$tuesday,$wednesday,$thursday,$friday,
                                $saturday,$sunday,$recurrent,$week,$scheduleParent_id,$end_date);
           
            if($schedulesCreated>0)                
                $this->sendUserMessage("Registro guardado con éxito. Se crearon ".$schedulesCreated." registros de fechas programadas.");
            else
                $this->sendUserMessage("Registro guardado con éxito. No se crearon registros de programaciones.");

        } catch (Exception $ex) {
            $params = compact('id', 'route_id', 'vehicle_id', 'user_id', 'start_date', 'status', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'recurrent', 'week', 'scheduleParent_id', 'end_date');
            $this->logAndResolveException($ex,$params);
        }       
    }
    
    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $params['id'];
          
          try {
            if($id==null)
                $this->generateUserException("Debe proporcionar un identificador de programación válido");
            
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'eliminar');
            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');
            $resultsets = $scheduleRepo->delete($id);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('id');            
            $this->logAndResolveException($ex,$params);
        }
    }

    public function getScheduleDetailAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $routeId = $this->getArrayValue('routeId', $params);
        $id = $this->getArrayValue('id', $params);
        $filter= $this->getArrayValue('filter',$params);
        $routeName = $this->getArrayValue('routeName', $filter);

        try{
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');

            $resultsets = $scheduleRepo->getScheduleDetail($page,$rowsPerPage,$sortField,$sortDir,$id,$routeId);
            
            echo json_encode($resultsets);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'id','routeId');
            $this->logAndResolveException($ex,$params);        
        }

    }

    public function getScheduledDatesAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);        
        $scheduleId = $this->getArrayValue('scheduleId', $params);

        try{
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');

            $resultsets = $scheduleRepo->getScheduledDatesList($page,$rowsPerPage,$sortField,$sortDir,$scheduleId);
            
            echo json_encode($resultsets);
        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'scheduleId');
            $this->logAndResolveException($ex,$params);        
        }

    }

    public function getScheduledDateAction(){
        $params = $this->getRequest()->getPostJson();
        $scheduledDateId = $this->getArrayValue('scheduledDateId', $params);

        try{
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');

            $resultsets = $scheduleRepo->getScheduledDate($scheduledDateId);
            
            echo json_encode($resultsets);
        }catch(Exception $ex){
            $this->logAndResolveException($ex,$params);        
        }

    }

    public function updateScheduledDateAction(){
        $params = $this->getRequest()->getPostJson();
        if($params["id"]==null) $this->generateUserException("Debe proporcionar información correcta");

        try{
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');

            $result = $scheduleRepo->updateScheduledDate($params);
            
            echo json_encode($result);
        }catch(Exception $ex){
            $this->logAndResolveException($ex,$params);        
        }
    }
}