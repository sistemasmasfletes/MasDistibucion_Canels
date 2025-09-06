<?php

class Warehouseman_DashboardController extends JController
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
        $this->setResponseJSON(false);
        
        $this->view->setTemplate('GeneralContentLayout');
    }

}