<?php

class Admin_ConversionController extends Model3_Scaffold_Controller {

    public function __construct($request) {
        $this->_sc = new Scaffold_DefaultDb_Conversion();
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
