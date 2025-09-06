<?php

use com\masfletes\db\DBUtil;

class OperationController_ContractsController extends JController {

    private $userSessionId;

    public function init() {

        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = Model3_Auth::getCredentials('id');
    }

    public function indexAction() {}
    
    public function getContractsAction()
    {
    	$em =  $this->getEntityManager('DefaultDb');
    	$contracts =  $em->getRepository('DefaultDb_Entities_Configuration')->findAll();
    	$userdata =  $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $this->userSessionId));
    	$res = array();
    	
    	 
		foreach ($contracts as $data){
			
			$terms = str_replace("[COMPANY]", $userdata->getCommercialName(), $data->getTerms());
			$privacy = str_replace("[COMPANY]", $userdata->getCommercialName(), $data->getPrivacy());
			$service = str_replace("[COMPANY]", $userdata->getCommercialName(), $data->getService());
			
    		$res[] =array(
    				"Terms" => $terms,
    				"Privacy" => $privacy,
    				"Service" => $service,
    				"TermsAc" => $userdata->getTerms(),
    				"PrivacyAc" => $userdata->getPrivacy(),
    				"ServiceAc" => $userdata->getService(),
    				"Usrtype" => $userdata->getData()
    		);
		}
    	echo json_encode($res);
    }    
    
    public function setAceptAction()
    {
    	$params = $this->getRequest()->getPostJson();
    	
    	$tac = ($this->getArrayValue('tac', $params)=== true)?'1':'0';
    	$pac = ($this->getArrayValue('pac', $params)=== true)?'1':'0';
    	$sac = ($this->getArrayValue('sac', $params)=== true)?'1':'0';
    	 
    	$em =  $this->getEntityManager('DefaultDb');
    	$userdata =  $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $this->userSessionId));

    	try {
    		
	    	$userdata->setTerms($tac);
	    	$userdata->setPrivacy($pac);
	    	$userdata->setService($sac);
	    	$em->persist($userdata);
	    	$em->flush();
	    	
	    	echo json_encode(array('res' => TRUE));
    	}catch(Exception $ex){
    		$params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'routeId');
    		$this->logAndResolveException($ex,$params);
    	}
    	 
    }
    
    public function upContractAction()
    {
    	$params = $this->getRequest()->getPostJson();
    	
    	$em =  $this->getEntityManager('DefaultDb');
    	$userdata =  $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $this->userSessionId));
    	if($userdata->getData()['role'] === 1){
    		$contracts =  $em->getRepository('DefaultDb_Entities_Configuration')->findOneBy(array('id' => 1));
    		$users =  $em->getRepository('DefaultDb_Entities_User')->findAll();
    		try{
	    		switch ($this->getArrayValue('contrId', $params)){
	    			case 'btnServ':
	    				$contracts->setService($this->getArrayValue('contr', $params));
	    				foreach ($users as $data){
	    					$data->setService('0');
	    					$em->persist($data);
	    				}
	    				break;
	    					
	    			case 'btnPriv':
	    				$contracts->setPrivacy($this->getArrayValue('contr', $params));
	    			    foreach ($users as $data){
	    					$data->setPrivacy('0');
	    					$em->persist($data);
	    			    }
	    				break;
	    			
	    			case 'btnTerm':
	    				$contracts->setTerms($this->getArrayValue('contr', $params));
	    			    foreach ($users as $data){
	    					$data->setTerms('0');
	    					$em->persist($data);
	    			    }
	    				break;
	    		}
	    		$em->persist($userdata);
	    		$em->flush();
	    		echo json_encode(array('res' => TRUE));
    		}catch(Exception $ex){
    			$params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'routeId');
    			$this->logAndResolveException($ex,$params);
    		}
    	}
    }
    
}
