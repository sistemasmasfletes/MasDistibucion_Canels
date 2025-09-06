<?php

class Admin_CompraCreditosController extends Model3_Scaffold_Controller {

    public function __construct($request) {
        $this->_sc = new Scaffold_DefaultDb_CompraCreditos();
        parent::__construct($request);
    }

    public function init() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Calendar/datepickers.js');
    }

    public function indexAction() {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $branchesUserRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $branches = $branchesUserRepos->findBy(array('client' => $credentials['id']));
        $this->view->branches = $branches;
    }

    public function addAction() {

        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        
      
        
        if ($this->getRequest()->isPost()) {
            $this->_post = $this->getRequest()->getPost();
            $this->_post['client'] = $userRepos->find($credentials['id']);
            
//            conversion de creditos
            //$query=  $this->_post['moneda'];
            //print_r($query);
            //$q= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.compra')->Where('c.id ='.$query)->getQuery()->getResult();
            //$q2= $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('c')->select('c.creditos')->Where('c.id ='.$query)->getQuery()->getResult();
            $this->_post['creditos']=(($this->_post['montoCompra'])*3);//*$q2[0]['creditos'])/($q[0]['compra']);
            
            $this->_post['path']='MAS_FLETES\public\Documents\PDF';
            $this->_post['estado']='Hold off';
            
            if (isset($this->_post['fecha']) && $this->_post['fecha'] != '') {
                $this->_post['fecha'] = new DateTime($this->_post['fecha']);
            }
        }
        
        parent::addAction();
    }

    public function editAction() {

       $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        if ($this->getRequest()->isPost()) {
            $this->_post = $this->getRequest()->getPost();
            $this->_post['client'] = $userRepos->find($credentials['id']);
            
            if (isset($this->_post['fecha']) && $this->_post['fecha'] != '') {
                $this->_post['fecha'] = new DateTime($this->_post['fecha']);
            }
        }
        parent::editAction();

}
}