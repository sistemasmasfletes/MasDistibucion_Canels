<?php

use com\masfletes\db\DBUtil;



class OperationController_CreteShippingController extends JController {

    private $userSessionId;
    private $opcion;
    public function init() {

        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = Model3_Auth::getCredentials('id');
    }

    public function indexAction() {  
    }
    
    public function recibiParametrosAction() 
    {
        $total = $_POST['total'];
        $creditos = $_POST['creditos'];
        
        $response = array();
        $response["opcion"] = 0;
        
        if($creditos == 0)
        {
            $response["opcion"] = 1;
            $this->opcion = 1;
        }
        else if($creditos < $total)
        {
            $response["opcion"] = 2;
            $this->opcion = 2;
        }        
        echo  json_encode( $response );
    } 
    
    public function enviarParametrosAction() 
    {
        $response = array();
        $response["opcion"] = $this->opcion;
        echo  json_encode( $response );
    } 
    public function enviarAction() {

        $response = array();
        $response["opcion"] = $this->opcion;
        echo  json_encode( $response );
    }
}
