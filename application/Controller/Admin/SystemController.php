<?php

class Admin_SystemController extends JController{
    public function init() {
        parent::init();
        //$this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getMenuAction(){
        $params = $this->getRequest()->getPostJson();

        $user = $this->getArrayValue('user', $params);
        $password = $this->getArrayValue('password', $params);

        $user = rtrim(ltrim($user));
        $password = rtrim(ltrim($password));

        $auth = new Model3_Auth();

        if ($auth->authenticate($user, md5($password)))
            echo json_encode($_SESSION['USERSESSIONID']);
        else{
            header("Failed-Login:1");
            $this->createHttpResponse(403);
        }
        return;
    }
}
