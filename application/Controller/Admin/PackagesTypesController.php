<?php

class Admin_PackagesTypesController extends Model3_Scaffold_Controller
{

    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_PackageTypes();
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