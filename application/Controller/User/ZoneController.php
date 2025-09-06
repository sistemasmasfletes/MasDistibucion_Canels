<?php

class User_ZoneController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
           $this->createResponse401();
    }
    
    public function indexAction() {}

    public function getZoneAction(){
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);       
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter= $this->getArrayValue('filter',$params);

        $parametros = array();
        $parametros["page"] = $page ? $page : 1;
        $parametros["rowsPerPage"] = $rowsPerPage ? $rowsPerPage : 10;
        $parametros["sortField"] = $this->getSortField($params);
        $parametros["sortDir"] = $sortDir ? $sortDir : "ASC";
        
        if(!$filter) $filter = array();
        $filter["zoneId"] = $this->getArrayValue('zoneId',$filter) ? $this->getArrayValue('zoneId',$filter) : null;
        $filter["name"] = $this->getArrayValue('name',$filter) ? $this->getArrayValue('name',$filter) : null;

        $parametros["filter"] = $filter;        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $this->hasPermission($this->getUserSessionId(), 'zona', 'listar');

            $zoneRepo = $em->getRepository('DefaultDb_Entities_Zone');
            $zone = $zoneRepo->getZone($parametros);
            echo json_encode($zone);
        }
        catch (Exception $ex) 
        {

            $this->logAndResolveException($ex,$parametros);
        }       
    }

    
    private function getSortField($params){
        $sortField = $this->getArrayValue('sortField', $params);
        if(!$sortField)
            $sortField = "p.name";
        else
            $sortField = "p.".$sortField;
        return $sortField;
    }
    
    public function verificarAction(){
        $SI_EXISTE = 1;
        $NO_EXISTE = 0;
        
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        
        try{
            $name = $params['zona']['name'];
            $estatus = $em->getRepository('DefaultDb_Entities_Zone')->findOneBy(array('name' => $name));
            $result['existe'] = ($estatus === null) ? $NO_EXISTE : $SI_EXISTE;
        } catch (Exception $ex) {
            $result["error"] = $this->logAndResolveException($ex, $params);
        }
        echo json_encode($result);
    }

    public function saveAction() {
        $request = $this->getRequest()->getPost(); 

        $aZone = array(
            "id" => $this->getArrayValue("zoneId", $request),
            "name" => $this->getArrayValue("name", $request)
        );
        $aZone["id"] = $aZone["id"] == "undefined" ? null : intval($aZone["id"]);
        try 
        {
            $em = $this->getEntityManager('DefaultDb');
            $action = is_null($aZone["id"]) ? "agregar" : "editar";

            $this->hasPermission($this->getUserSessionId(), 'zona', $action);

            $zoneRepo = $em->getRepository('DefaultDb_Entities_Zone');
            $zone = $zoneRepo->save($aZone);
            echo json_encode($zone);
        }
        catch (Exception $ex) 
        {
            $this->logAndResolveException($ex,$aZone);
        }        
    }

    public function deleteAction(){
        $params = $this->getRequest()->getPostJson();
        $zoneId = $this->getArrayValue("id",$params);

        try {
            $em = $this->getEntityManager('DefaultDb');
            $this->hasPermission($this->getUserSessionId(), 'zona', 'eliminar');
            $zoneRepo = $em->getRepository('DefaultDb_Entities_Zone');
            $result = $zoneRepo->delete($zoneId);
            echo json_encode($result);
        } catch (Exception $e) {
            
        }
    }

}