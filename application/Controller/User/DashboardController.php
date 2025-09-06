<?php

class User_DashboardController extends Model3_Controller {

    public function init() 
    {
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];

        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        $branchRepo = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $user = $userRepos->findBy(array('id' => $credentials['id'], 'status' => DefaultDb_Entities_User::STATUS_ACTIVE));
        if (!$user) /* Si no existe el usuario esta bloqueado o esta inactivo */
            $this->deleteCredentials();
        $this->view->user = $user;

        $branchesPending = 0;        
        $dql = 'SELECT COUNT(bu.id) FROM DefaultDb_Entities_BranchesUser bu WHERE bu.client=:clientId AND bu.point IS NULL';
        $query=$em->createQuery($dql);
        $query->setParameter('clientId',($user ? $user[0]->getId() : 0));
        $branchesPending = $query->getSingleScalarResult();
        $this->view->branchesPending = $branchesPending;  
    }

    public function indexAction() {
        
    }

    function deleteCredentials() {
        Model3_Auth::deleteCredentials();
        $this->redirect('/Index/bloquedUser');
    }
        

}