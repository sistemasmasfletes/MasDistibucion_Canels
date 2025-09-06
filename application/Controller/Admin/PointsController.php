<?php

class Admin_PointsController extends Model3_Scaffold_Controller
{
    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_Point();
        parent::__construct($request);
    }    
    
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }  
    }
}