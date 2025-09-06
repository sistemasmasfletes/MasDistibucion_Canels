<?php

class User_PromotionScheduleController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
           $this->createResponse401();
    }
    
    public function indexAction() {}

    public function getPromotionScheduleAction(){
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
        
        //Setear filtros por defecto si no los hay
        if(!$filter) $filter = array();
        $filter["id"] = $this->getArrayValue('id',$filter) ? $this->getArrayValue('id',$filter) : null;
        $filter["clientid"] = $this->getArrayValue('clientid',$filter) ? $this->getArrayValue('clientid',$filter) : null;
        $filter["userid"] = Model3_Auth::getCredentials("id");

        $parametros["filter"] = $filter;        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');

            $promotionRepo = $em->getRepository('DefaultDb_Entities_PromotionSchedule');
            $promotion = $promotionRepo->getPromotionSchedule($parametros);
            echo json_encode($promotion);
        }
        catch (Exception $ex) 
        {

            $this->logAndResolveException($ex,$parametros);
        }       
    }

    private function getSortField($params){
        $sortField = $this->getArrayValue('sortField', $params);
        if(!$sortField)
            $sortField = "p.creationDate";
        else
            if($sortField=='promotionName')
                $sortField = "pr.name";
            else
                $sortField = "p.".$sortField;
        return $sortField;
    }

    public function saveSurveyAction(){
        $params = $this->getRequest()->getPostJson();        

        $paramsSave = array();
        $paramsSave["id"] = $this->getArrayValue('id', $params);
        $paramsSave["consumerType"] = $this->getArrayValue('consumerType', $params);
        $paramsSave["interestLevel"] = $this->getArrayValue('interestLevel', $params);
        $paramsSave["request"] = $this->getArrayValue('request', $params);
        $paramsSave["telephone"] = $this->getArrayValue('telephone', $params);
        $paramsSave["receivingUser"] = $this->getArrayValue('receivingUser', $params);
        $paramsSave["comments"] = $this->getArrayValue('comments', $params);

        $em = $this->getEntityManager('DefaultDb');
        try{
            //$this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');
            //$this->hasPermission($this->getUserSessionId(), 'encuesta', 'guadar');
            $promotionRepo = $em->getRepository('DefaultDb_Entities_PromotionSchedule');
            $promotionRepo->saveSurvey($paramsSave);
        }catch (Exception $ex){
            $this->logAndResolveException($ex,$paramsSave);
        } 
    }

    public function updateStatusPromotionAction(){
        $params = $this->getRequest()->getPostJson();
        if($params["routePointActivityId"]==null) return;
        if($params["promotionScheduleId"]==null) return;
        if($params["tipoCobro"]==null) return;
        
        try {
            $em = $this->getEntityManager('DefaultDb');
            $this->hasPermission($this->getUserSessionId(), 'promociones', 'listar');
            $promotionRepo = $em->getRepository('DefaultDb_Entities_PromotionSchedule');
            $result = $promotionRepo->updateStatusPromotion($params);
            echo json_encode($result);

        } catch (Exception $ex) {
            $this->logAndResolveException($ex,$params);
        }
    }
}