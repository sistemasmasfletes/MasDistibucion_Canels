<?php

class OperationController_PackageRateController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }
    
    public function getRatesAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $elementId = $this->getArrayValue('elementId', $params);

        try{                       
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            
            $rates=$packageRateRepo->getPackageRate($page,$rowsPerPage,$sortField,$sortDir,$elementId);

            echo json_encode($rates);

        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir');
            $this->logAndResolveException($ex,$params);
        }
    }

    public function getElementsForRatesAction(){
        $params = $this->getRequest()->getPostJson();
        $elementName = $this->getArrayValue('elementName', $params);
        $elementType = $this->getArrayValue('elementType', $params);

        try{                       
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            
            $rates=$packageRateRepo->getElementsForRates($elementName, $elementType);

            echo json_encode($rates);

        }catch(Exception $ex){
            $params = compact('elementName', 'elementType');
            $this->logAndResolveException($ex,$params);
        }
    }

    public function getRoutesWithRatesAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $routeName = $this->getArrayValue('routeName', $params);
        $rateId = $this->getArrayValue('rateId', $params);
        $routeId = $this->getArrayValue('routeId', $params);
        
        $userId = $this->currentUserId; 

        try{                       
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            
            $routes=$packageRateRepo->getRoutesWithRates($page,$rowsPerPage,$sortField,$sortDir,$userId,$routeName,$rateId, $routeId);

            echo json_encode($routes);

        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir','userId','routeName');
            $this->logAndResolveException($ex,$params);
        }       
    }

    public function getPointsWithRatesAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $routeId = $this->getArrayValue('routeId', $params);
        $pointName = $this->getArrayValue('pointName', $params);
        $rateId = $this->getArrayValue('rateId', $params);
        $routePointId = $this->getArrayValue('routePointId', $params);
        
        $userId = $this->currentUserId; 

        try{                       
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            
            $routes=$packageRateRepo->getPointsWithRates($page,$rowsPerPage,$sortField,$sortDir,$userId,$pointName,$rateId,$routeId,$routePointId);

            echo json_encode($routes);

        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir','userId','pointName');
            $this->logAndResolveException($ex,$params);
        }       
    }

    public function saveAction(){
    	$params = $this->getRequest()->getPostJson();
    	$rateId = $this->getArrayValue('rateId', $params);
    	$elementId = $this->getArrayValue('element_id', $params);
    	$elementType = $this->getArrayValue('element_type', $params);
    	$date = $this->getArrayValue('date', $params);
    	$clientRate = $this->getArrayValue('client_rate', $params);
    	$providerFee = $this->getArrayValue('provider_fee', $params);

    	$dateRate = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    	try{
    		$this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            $rate = $packageRateRepo->save($rateId,$elementId,$elementType,$dateRate,$clientRate,$providerFee);
            echo json_encode($rate);
    	}catch(Exception $ex){
            $params = compact('rateId', 'elementId', 'elementType', 'date','clientRate','providerFee');
            $this->logAndResolveException($ex,$params);
        } 

    }

    public function getRateByElementAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $elementId = $this->getArrayValue('elementId', $params);
        $elementType = $this->getArrayValue('elementType', $params);
        $userId = $this->currentUserId; 

        try{                       
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $packageRateRepo = $em->getRepository('DefaultDb_Entities_PackageRate');
            
            $rates=$packageRateRepo->getRateByElement($page,$rowsPerPage,$sortField,$sortDir,$userId,$elementId,$elementType);

            echo json_encode($rates);

        }catch(Exception $ex){
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir','userId','pointName');
            $this->logAndResolveException($ex,$params);
        }   
    }
}