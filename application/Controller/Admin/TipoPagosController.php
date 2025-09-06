<?php

class Admin_TipoPagosController extends Model3_Scaffold_Controller {

    public function __construct($request) {
        $this->_sc = new Scaffold_DefaultDb_TipoPagos();
        parent::__construct($request);
    }

    public function init() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
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
            //    var_dump($this->_post['client']);
        }
        parent::addAction();
    }

}
