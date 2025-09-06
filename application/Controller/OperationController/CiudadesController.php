<?php

use com\masfletes\db\DBUtil;

class OperationController_CiudadesController extends JController{
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->createResponse401();
        }
    }
    
    public function indexAction() {}
    
    public function getStatesByCountryIdAction(){
        $params = $this->getRequest()->getPostJson();


        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = (int)$this->getArrayValue('id', $params);
        $countryId = (int)$this->getArrayValue('countryId', $params);
 
        if(!$sidx){
            $sidx = 1;
        }
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $estadosRepo = $em->getRepository('DefaultDb_Entities_City');
            $resultSets = $estadosRepo->getEstadosById($sidx, $sord, $id, $countryId);
 
            echo json_encode($resultSets);
        } catch (Exception $ex) {
            $params = compact('page', 'limit', 'sidx', 'sord', 'id');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    public function getCiudadesAction(){
        $params = $this->getRequest()->getPostJson();
 
        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = (int)$this->getArrayValue('id', $params);
        $filter= $this->getArrayValue('filter',$params);
        $name = $this->getArrayValue('name', $filter);
        $state = $this->getArrayValue('state', $filter);
        $country = $this->getArrayValue('country', $filter);
 
        if(!$sidx){
            $sidx = 1;
        }
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Ciudades', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $ciudadesRepo = $em->getRepository('DefaultDb_Entities_City');
            $resultSets = $ciudadesRepo->getCiudades($page, $limit, $sidx, $sord, $id, $name,$state,$country, $this->currentUserId);
 
            echo json_encode($resultSets);
        } catch (Exception $ex) {
            $params = compact('page', 'limit', 'sidx', 'sord', 'id');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    public function getCiudadByIdAction(){
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'Ciudades', 'Listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $ciudadIdRepo = $em->getRepository('DefaultDb_Entities_City');
            $ciudadId = $ciudadIdRepo->getCiudadById($id);
            
            echo json_encode($ciudadId);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex,$params);
        }
    }    
    
    
    public function getEstadoByIdAction(){
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'Listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $estadosIdRepo = $em->getRepository('DefaultDb_Entities_State');
            $estadoId = $estadosIdRepo->getEstadoById($id);
            
            echo json_encode($estadoId);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    public function saveAction(){
        $params  = $this->getRequest()->getPostJson();
        
        $id = $this->getArrayValue('id', $params);
        $countryId = $this->getArrayValue('countryId', $params);
        $stateId = $this->getArrayValue('stateId', $params);
        $name = $this->getArrayValue('name', $params);
        $chrEstatus = $this->getArrayValue('chrEstatus', $params);
        
        try{
            $em = $this->getEntityManager('DefaultDb');
            $ciudadRepo = $em->getRepository('DefaultDb_Entities_City');
            
            $ciudadEntity = $ciudadRepo->saveCiudad($id, $countryId, $stateId, $name,$chrEstatus);
            
            echo json_encode($ciudadEntity);
        } catch (Exception $ex) {
            $params = compact('id', 'countryId', 'stateId', 'name','chrEstatus');
            $this->logAndResolveException($ex, $params);
        }
    }
    
    public function deleteAction(){
        $params = $this->getRequest()->getPostJson();
        
        $id = $this->getArrayValue('id', $params);
        
        try{
            $this->hasPermission($this->getUserSessionId(), 'Ciudades', 'Eliminar');
            $em = $this->getEntityManager('DefaultDb');
            $ciudadRepo = $em->getRepository('DefaultDb_Entities_City');
            $ciudad = $ciudadRepo->delete($id);
            
            echo json_encode($ciudad);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex, $params);
        }
    }
    
    public function getCitiesByStateIdAction(){
        $params = $this->getRequest()->getPost();
        $stateId = $this->getArrayValue('stateId', $params);
        $ACTIVO = 1;
 
        try{
            $this->hasPermission($this->getUserSessionId(), 'Ciudades', 'Listar');
            $em = $this->getEntityManager('DefaultDb');
            $ciudadRepo = $em->getRepository('DefaultDb_Entities_City');
            $ciudades = $ciudadRepo->findBy(array('state' => $stateId, 'estatus' => $ACTIVO));
            $respuesta = array();
            foreach($ciudades as $ciudad){
                $respuesta[] = array('id' => $ciudad->getId(),
                    'name' => $ciudad->getName() );
            }
            echo json_encode($respuesta);
        } catch (Exception $ex) {
            $params = compact('stateId');
            $this->logAndResolveException($ex, $params);
        }
    }
}
