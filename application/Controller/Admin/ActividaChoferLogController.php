<?php

class Admin_ActividaChoferLogController extends Model3_Scaffold_Controller {

    public function __construct($request) {
        $this->_sc = new Scaffold_DefaultDb_ActividaChoferLog();
        parent::__construct($request);
    }

    public function init() {
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
    }

    public function indexAction() {
      
    }

    public function addAction() {


        parent::addAction();
    }

}
