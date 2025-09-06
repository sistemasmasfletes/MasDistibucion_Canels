<?php

class Warehouseman_InventoryWarehousemanController extends JController{
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }
    
    public function indexAction() {}
    
    //Inventario de almacenista
    public function getInventoryWarehousemanAction(){
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
            $inventoryWarehousemanRepo = $em->getRepository('DefaultDb_Entities_PackageToOrder');
            $inventoryWarehouseman=$inventoryWarehousemanRepo->getInventoryWarehousemanListDQL($page,$rowsPerPage,$sortField,$sortDir,$sidx);
            
            echo json_encode($inventoryWarehouseman);
        } catch (Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir');
            $this->logAndResolveException($ex,$params);
        }
    }
    
}