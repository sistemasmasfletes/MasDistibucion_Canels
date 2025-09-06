<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailController
 *
 * @author Us
 */
class Admin_EmailController extends Model3_Controller {
    
    public function indexAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $userAdapter = $em->getRepository('DefaultDb_Entities_User');
        $users = $userAdapter->findBy(array('type'=>  DefaultDb_Entities_User::USER_SECRETARY));
        $this->view->receptionists = $users;
        $url= $this->view->url(array('module'=>'Ajax','controller'=>'Email','action'=>'changeOptions'));
        $this->view->getJsManager()->addJsVar('url', json_encode($url));
    }
}

?>
