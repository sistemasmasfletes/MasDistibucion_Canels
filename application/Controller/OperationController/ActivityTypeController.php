<?php
use com\masfletes\db\DBUtil;

class OperationController_ActivityTypeController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getActivityTypeAction() {
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $id = $this->getArrayValue('id', $filter);
        $name = $this->getArrayValue('name', $filter);
        $description = $this->getArrayValue('description',$filter);
        
        $em = $this->getEntityManager('DefaultDb');
        $activityTypeRepo = $em->getRepository('DefaultDb_Entities_ActivityType');
        
        $status=null;
        
        $ActivityType=$activityTypeRepo->getActivityTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name,
            $description);
        
        echo json_encode($ActivityType);
    }
    
    public function saveAction (){
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $name = $this->getArrayValue('name', $params);
        $description = $this->getArrayValue('description', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $activityTypeRepo = $em->getRepository('DefaultDb_Entities_ActivityType');
        
        $activityTypeRepo->addActivityType($id,$name,$description);
        
        $ActivityType=$activityTypeRepo->getActivityTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id,
            $name,
            $description);
        
        echo json_encode($ActivityType); 
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        $activityTypeRepo = $em->getRepository('DefaultDb_Entities_ActivityType');
        
        $activityTypeRepo->deleteActivityType($id);
        
        $ActivityType=$activityTypeRepo->getActivityTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,            
            $id);
        
        echo json_encode($ActivityType);
    }
}