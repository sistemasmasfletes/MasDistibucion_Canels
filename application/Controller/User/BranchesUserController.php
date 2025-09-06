<?php

class User_BranchesUserController extends Model3_Scaffold_Controller
{
    
    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_BranchesUser();
        parent::__construct($request);
    }
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
    } 
    public function indexAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $branchesUserRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $branches = $branchesUserRepos->findBy(array('client' => $credentials['id']));
        
        $routepoint = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $schedules = $em->getRepository('DefaultDb_Entities_Schedule');
        
        $sales = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findBy(array('seller' => $credentials['id']));
        $purchases = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findBy(array('buyer' => $credentials['id']));;
        
        $products = $em->getRepository('DefaultDb_Entities_Product');
        
        $stores = $products->createQueryBuilder('p')
        ->leftJoin('p.client','u')
		->where('u.id not in(216,84,173)')
        ->orderBy("u.id","DESC")
        ->groupBy("u.id")
        ->getQuery()
        ->getResult();
        
		$this->view->stores = $stores;
        $this->view->sales = count($sales);
        $this->view->purchases = count($purchases);
        $this->view->credentials = $credentials;
        $this->view->schedules = $schedules;
        $this->view->branches = $branches;
        $this->view->routepoint = $routepoint;
        $this->view->is_mobile = $this->is_mobile();
        
        if($_SESSION['firstlog'] == 1){
        	$this->view->fistlog = true;
        	$_SESSION['firstlog'] = 0;
        }else{
        	$this->view->fistlog = 0;
        }        
        
    }
    
    public function addAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        if ($this->getRequest()->isPost())
        {
            $this->_post = $this->getRequest()->getPost();
            $this->_post['client'] = $userRepos->find($credentials['id']);
        //    var_dump($this->_post['client']);
        }
        parent::addAction();
    }
    
    public function generatePDFAction() {
    	
    	include '../library/phpqrcode/phpqrcode.php';
    	$this->view->setUseTemplate(false);
    	 
    	try {
    	$PNG_TEMP_DIR = dirname('..\public\images') . DIRECTORY_SEPARATOR . 'qrcode' . DIRECTORY_SEPARATOR;
    	$PNG_WEB_DIR = '../public/qrcode/'; //html PNG location prefix
    	if (!file_exists($PNG_TEMP_DIR)) {
    		mkdir($PNG_TEMP_DIR);
    	}
    
    	$id = (int)$this->getRequest()->getParam('id');

    	$em = $this->getEntityManager('DefaultDb');
    	$pointsRepo = $em->getRepository('DefaultDb_Entities_Point');
    	$codePoint = $pointsRepo->getCodePointListDQL($id,TRUE);
    	
    	$routepoint = $em->getRepository('DefaultDb_Entities_RoutePoint');
    	$schedules = $em->getRepository('DefaultDb_Entities_Schedule');
    	 
    	$arrCode = array();
    	foreach ($codePoint as $code) {
    		$arrCode = array(
    				"code" => $code["code"]
    		);
    	}
    
    	$pointCode = $code["code"];
    	error_log("PNG_TEMP_DIR ->".$PNG_TEMP_DIR);
    	error_log("pointCode ->".$pointCode);
    	$filename = $PNG_TEMP_DIR . $pointCode . '.png';
    	$errorCorrectionLevel = 'M';
    	$matrixPointSize = 10;
    	$list = "";
    	 
       	$lista = $routepoint->createQueryBuilder('l')
       		->where("l.point = :point")
	        ->setParameter('point',$id)
	        ->orderBy("l.route","DESC")
	        ->groupBy("l.route")
	        ->getQuery()
	        ->getResult();
	                   	
	        foreach ($lista as $route){
	        	$list .= $route->getRoute()->getName()."<br />";

	        	$listaschedule = $schedules->createQueryBuilder('l')
	           		->where("l.route = :route")
	           		->setParameter('route',$route->getRoute()->getId())
	           		->orderBy("l.id","DESC")
	           		->groupBy("l.route")
	           		->getQuery()
	           		->getResult();
	                    		
            		$listday = "";
	           		foreach ($listaschedule as $days){
	                    			
	        			$listday .= ($days->getMonday()==1)?"&nbsp;&nbsp;&bull;Lunes<br />":"";
	           			$listday .= ($days->getTuesday()==1)?"&nbsp;&nbsp;&bull;Martes<br />":"";
	          			$listday .= ($days->getWednesday()==1)?"&nbsp;&nbsp;&bull;Mi&eacute;rcoles<br />":"";
	           			$listday .= ($days->getThursday()==1)?"&nbsp;&nbsp;&bull;Jueves<br />":"";
	           			$listday .= ($days->getFriday()==1)?"&nbsp;&nbsp;&bull;Viernes<br />":"";
	           			$listday .= ($days->getSaturday()==1)?"&nbsp;&nbsp;&bull;S&aacute;bado<br />":"";
	           			$listday .= ($days->getSunday()==1)?"&nbsp;&nbsp;&bull;Domingo":"";
	                    			
	           		}
	          		$list .= $listday; 
	           	}

    	          	
    	          	
    	QRcode::png($pointCode, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    	
    	$content = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		    <html xmlns="http://www.w3.org/1999/xhtml"><head>
		    <style>
		    body {
		        margin: 0;
		        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		        font-size: 13px;
		        line-height: 18px;
		        color: #333333;
		        background-color: #ffffff;
		      }
		    </style>
		    </head><body>
		     <div style="border-bottom:1px solid #B00700;padding-bottom:5px;">
		    	<img src="'.$filename.'" />
		     </div>'.
		     $pointCode.'<br />'.$list
		  	.'</body>
		   	</html>';    	
    	//echo $content;
    	$dompdf = new DOMPDF();
    	$dompdf->load_html($content);
    	$dompdf->render();
    	$dompdf->stream("QrPunto.pdf");
		//$this->redirect('User/BackStore/shopping');
    	} catch (Exception $e) {
    	echo $e->getMessage();
    	}
    }
    
    
    
}