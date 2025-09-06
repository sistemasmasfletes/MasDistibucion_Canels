<?php

class OperationController_CardOperatorsController extends JController{
    
    public function init(){
        parent::init();
        if(!Model3_Auth::isAuth()){
            $this->createResponse401();
        }
    }
    
    public function indexAction(){}
    
    public function getCardOperatorsAction(){
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
        $filter["cardOperatorId"] = $this->getArrayValue('cardOperatorId',$filter) ? $this->getArrayValue('cardOperatorId',$filter) : null;
        $filter["chrOperator"] = $this->getArrayValue('chrOperator',$filter) ? $this->getArrayValue('chrOperator',$filter) : null;

        $parametros["filter"] = $filter;        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $this->hasPermission($this->getUserSessionId(), 'CardOperators', 'listar');

            $cardOperatorsRepo = $em->getRepository('DefaultDb_Entities_CardOperators');
            $cardOperator = $cardOperatorsRepo->getCardOperators($parametros);
            echo json_encode($cardOperator);
        }
        catch (Exception $ex) 
        {

            $this->logAndResolveException($ex,$parametros);
        }       
    }
    
    
     public function saveAction() {
        $request = $this->getRequest()->getPost(); 
        $aCardOperator = array(
            "id" => $this->getArrayValue("cardOperatorId", $request),
            "chrOperator" => $this->getArrayValue("chrOperator", $request)
        );
        $aCardOperator["id"] = $aCardOperator["id"] == "undefined" ? null : intval($aCardOperator["id"]);
        try 
        {
            $em = $this->getEntityManager('DefaultDb');
            $action = is_null($aCardOperator["id"]) ? "agregar" : "editar";

            $this->hasPermission($this->getUserSessionId(), 'CardOperators', $action);

            $cardOperatorsRepo = $em->getRepository('DefaultDb_Entities_CardOperators');
            $cardOperator = $cardOperatorsRepo->save($aCardOperator, $this->currentUserId);
            echo json_encode($cardOperator);
        }
        catch (Exception $ex) 
        {
            $this->logAndResolveException($ex,$aCardOperator);
        }        
    }
    
    public function deleteAction(){
        $params = $this->getRequest()->getPostJson();
        $cardOperatorId = $this->getArrayValue("cardOperatorId",$params);

        try {
            $em = $this->getEntityManager('DefaultDb');
            $this->hasPermission($this->getUserSessionId(), 'CardOperators', 'eliminar');
            $cardOperatorRepo = $em->getRepository('DefaultDb_Entities_CardOperators');
            $result = $cardOperatorRepo->delete($cardOperatorId);
            echo json_encode($result);
        } catch (Exception $e) {
            
        }
    }
    
    
    private function getSortField($params){
        $sortField = $this->getArrayValue('sortField', $params);
        if(!$sortField)
            $sortField = "p.chrOperator";
        else
            $sortField = "p.".$sortField;
        return $sortField;
    }
    
    
    
}