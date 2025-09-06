<?php

class Admin_InvoicesUsersController extends Model3_Controller
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
        $this->view->getJsManager()->addJs('application/Admin/InvoicesUsers/datepickers.js');
        $this->view->getJsManager()->addJsVar('urlFindInvocesUser', '"'.$this->view->getBaseUrl().'/Ajax/InvoicesUsers/index'.'"');
        $invoicesUsers = null;
        $this->view->invoicesUsers = $invoicesUsers;
        
    }
    
   

    

}