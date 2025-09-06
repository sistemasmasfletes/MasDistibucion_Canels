<?php

class Admin_ReportsUsersController extends Model3_Controller
{
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }  
    }
    
    public function indexAction()
    {
        $this->view->user=false;
        $branchesPending = false;
        if(count($this->getRequest()->getParams())>0)
        {
            $idUser=$this->getRequest()->getParam('id');
            $em=$this->getEntityManager('DefaultDb');
            $user=$em->find('DefaultDb_Entities_User',$idUser);
            if($this->getRequest()->getParam('bP'))
                $branchesPending = true;
            $this->view->branchesPending = $branchesPending;
            $this->view->user=$user;
            
        }
        $this->view->getJsManager()->addJs('application/Admin/ReportsUsers/index'.VERSION_JS.'.js');

        $this->view->ajaxSearch = $this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'search'));
        $this->view->getJsManager()->addJs('jquery/jquery.form.310.js');

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'details')));
        $this->view->getJsManager()->addJsVar('urlGetDetails', $url );

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'hitoricalOrders')));
        $this->view->getJsManager()->addJsVar('urlGetHistorical', $url );
        
        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'catalogs')));
        $this->view->getJsManager()->addJsVar('urlGetCatalogs', $url );

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'blockUser')));
        $this->view->getJsManager()->addJsVar('urlGetBlock', $url );

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'getOrderDetails')));
        $this->view->getJsManager()->addJsVar('getOrderDetails', $url );

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'changeStatusProduct')));
        $this->view->getJsManager()->addJsVar('urlChangeStatus', $url );

        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'changeStatusUser')));
        $this->view->getJsManager()->addJsVar('urlChangeStatusUser', $url );
        
        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'invoicesUser')));
        $this->view->getJsManager()->addJsVar('urlGetInvoices', $url );
        
        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'changeStatusInvoices')));
        $this->view->getJsManager()->addJsVar('urlChangeInvoices', $url );
        
        $url = json_encode($this->view->url(array('module'=>'Ajax','controller'=>'ReportsUsers','action'=>'branchesUser')));
        $this->view->getJsManager()->addJsVar('urlBranchesUser', $url );

        $url = json_encode($this->view->url(array('module' => 'Ajax', 'controller' => 'ReportsUsers', 'action' => 'saveChangeBranche')));
        $this->view->getJsManager()->addJsVar('urlSaveChange',$url);
        $this->view->getJsManager()->addJs('application/User/BackStore/ordersRecurrent'.VERSION_JS.'.js');
        $this->view->getJsManager()->addJsVar('urlOrderRecurrent', '\''.$this->view->url(array('module' => 'Ajax','controller' => 'BackStore', 'action' => 'getDataOrder')).'\'');
        $this->view->getJsManager()->addJsVar('urlGetPointsByCategory', json_encode($this->view->url(array('module'=>'Ajax','controller'=>'BackStore','action'=>'getPointsByCategory'))));
        $this->view->getJsManager()->addJsVar('urlSearch', json_encode($this->view->url(array('module'=>'Ajax','controller'=>'BackStore','action'=>'searchClients'))));
        $this->view->getCssManager()->addCss('view/scripts/User/BackStore/listSearch' . VERSION_CSS . '.css');
        $this->view->getJsManager()->addJsVar('urlGetDataUser','\''.$this->view->url(array('module'=>'Ajax','controller'=>'Users','action'=>'getDataUser')).'\'');
        $this->view->getJsManager()->addJs('view/scripts/User/BackStore/createOrder' . VERSION_JS . '.js');
    }
    
    public function branchesPendingAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $branchesPending = $branchesRepos->getBranchesPending();
        $branchesDistinct = $branchesRepos->getBranchesDistinct(); 
        $this->view->branchesPending = $branchesPending;
        
    }
    public function branchesAllAction()    {    	$credentials = Model3_Auth::getCredentials();    	$em = $this->getEntityManager('DefaultDb');    	$branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');    	$branchesPending = $branchesRepos->getBranchesAll();    	
    	$this->view->branchesPending = $branchesPending;
    	$routepoint = $em->getRepository('DefaultDb_Entities_RoutePoint');
    	$schedules = $em->getRepository('DefaultDb_Entities_Schedule');
    	$this->view->schedules = $schedules;
    	$this->view->routepoint = $routepoint;
   	}
}  