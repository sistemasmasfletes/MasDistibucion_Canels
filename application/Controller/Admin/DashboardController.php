<?php

class Admin_DashboardController extends Model3_Controller
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
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
        $branchesPending = $branchesRepos->getBranchesPending();
        
        $this->view->branchesPending = $branchesPending;
        
        $addressRepos = $em->getRepository('DefaultDb_Entities_Address');
        $addressPending = $addressRepos->getPendingAddresses();
        $this->view->pendingAddress = $addressPending;

        
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        
        
    }
    
}