<?php

class OperationController_ConfigurationController extends JController {
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
           $this->createResponse401();
    }
    
    public function getConfigurationAction() {
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
            
            $em = $this->getEntityManager('DefaultDb');
            $confRepo = $em->getRepository('DefaultDb_Entities_Configuration');
            $resultsets = $confRepo->getConfiguration();
            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('page', 'limit', 'sidx', 'sord', 'id');
            $this->logAndResolveException($ex,$params);
        }
    }

    public function saveAction(){
        $params = $this->getRequest()->getPostJson();
        $id = (int)$this->getArrayValue('id', $params);
        $minutesPerPoint=(int)$this->getArrayValue('minutesPerPoint', $params);
        $basePackageSize=(int)$this->getArrayValue('basePackageSize', $params);
        $powerFactor=(float)$this->getArrayValue('powerFactor', $params);

        try {
            
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $confRepo = $em->getRepository('DefaultDb_Entities_Configuration');
            
            $confRepo->save($id,$minutesPerPoint,$basePackageSize,$powerFactor);
        } catch (Exception $ex) {
            $params = compact('id','minutesPerPoint','basePackageSize','powerFactor');
            $this->logAndResolveException($ex,$params);
        } 
    }

}