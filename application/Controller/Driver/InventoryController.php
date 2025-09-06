<?php
use com\masfletes\db\DBUtil;

class Driver_InventoryController extends JController
{
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();              
    }
    
    public function indexAction() {}
    
    //FUNCION DE INVENTARIO DE VEHÍCULO
    public function getInventoryAction(){
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $sidx = $this->getArrayValue('sortField', $params);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $inventoryRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
            $inventory=$inventoryRepo->getInventoryListDQL($page,$rowsPerPage,$sortField,$sortDir,$sidx);
            
            echo json_encode($inventory);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //Función dos inventario de vehículo
    public function getInventoryPacksAction (){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);
        $sidx = $this->getArrayValue('sortField', $params);
        
        $idrow = $this->getArrayValue('idrow', $params);
        $stateParams= $this->getArrayValue('stateParams', $params);
        $id = $this->getArrayValue('id', $stateParams);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $inventoryPackRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
            $inventory=$inventoryPackRepo->getInventoryPacksListDQL($page,$rowsPerPage,$sortField,$sortDir,$sidx,
                    $idrow,$stateParams,$id);
            
            echo json_encode($inventory);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx',
                    'idrow','stateParams','id');
            $this->logAndResolveException($ex,$params);
        }
    }
}