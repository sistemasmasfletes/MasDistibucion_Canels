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
class Ajax_EmailController extends Model3_Controller {
    public function init() {
       $this->view->setUseTemplate(false);
    }
    
    public function changeOptionsAction(){
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $idUser = $post['idCliente'];
            $status = isset($post['status'])?$post['status']:DefaultDb_Entities_User::STATUS_INACTIVE;
            $em = $this->getEntityManager('DefaultDb');
            $user = $em->find('DefaultDb_Entities_User',$idUser);
            $user->setStatus($status);
            $em->persist($user);
            $em->flush();
            $this->view->result=true;
        }
    }
}

