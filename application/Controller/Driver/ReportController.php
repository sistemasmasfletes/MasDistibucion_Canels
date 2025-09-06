<?php
use com\masfletes\db\DBUtil;

class Driver_ReportController extends JController
{   
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();              
    }
    
    public function indexAction() {}
    
    public function getReportAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $sidx = $this->getArrayValue('sortField', $params);

        $id = $this->getArrayValue('id', $filter);
        $name = $this->getArrayValue('name', $filter);
        $progHour = $this->getArrayValue('progHour', $filter);
        $currHour = $this->getArrayValue('currHour', $filter);
        $comments = $this->getArrayValue('comments', $filter);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $reportRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
            $report=$reportRepo->getReportListDQL($page,$rowsPerPage,$sortField,$sortDir,            
                $id,
                $name,
                $progHour,
                $currHour,
                $comments);
            
            echo json_encode($report);
        } catch (Exception $ex) {
            throw new Exception("Error Processing Request", 1);
            
            echo $ex;
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx',
                'id', 'name', 'progHour', 'currHour', 'comments');
            $this->logAndResolveException($ex,$params);
        }
    }
}