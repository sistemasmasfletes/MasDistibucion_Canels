<?php

class Admin_VehiclesController extends Model3_Scaffold_Controller
{

    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_Vehicle();
        parent::__construct($request);
    }

    public function indexAction()
    {
        
    }
    
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }  
    }
    
}