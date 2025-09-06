<?php

class Ajax_OrderRecurrentsController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }
    
    public function chageOrderStatusAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        
        if($this->getRequest()->isPost())
        {
            $orderRepos = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
            $post = $this->getRequest()->getPost();
            $idOrder = $post['id'];
            $newStatus = $post['status'];
            $order = $orderRepos->find($idOrder);
            $order->setOrderStatus($newStatus);
            $em->persist($order);
            $em->flush();
        }
    }
}