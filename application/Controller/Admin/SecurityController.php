<?php

class Admin_SecurityController extends JController{
    public function init() {
        parent::init();        
    }

    public function loginAction(){
        $params = $this->getRequest()->getPostJson();

        $user = $this->getArrayValue('user', $params);
        $password = $this->getArrayValue('password', $params);

        $user = rtrim(ltrim($user));
        $password = rtrim(ltrim($password));

        $auth = new Model3_Auth();

        if ($auth->authenticate($user, md5($password)))
            echo json_encode($_SESSION['USERSESSIONID']);
        else
           $this->createHttpResponse(401);
            exit;
        return;
    }

    public function forbiddenAction(){
        $this->setResponseJSON(FALSE);        
    }
}
