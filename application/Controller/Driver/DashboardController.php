<?php
/**
 * Description of DashboardController
 *
 * @author drackor
 */
class Driver_DashboardController extends JController
{
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
    }

    public function indexAction()
    {
        $this->setResponseJSON(false);
        
        $this->view->setTemplate('GeneralContentLayout');
    }

    public function resourcesAction()
    {
        $userId = Model3_Auth::getCredentials('id');
        $em = $this->getEntityManager('DefaultDb');
        
        $user = $em->find('DefaultDb_Entities_User', $userId);
        
        $this->view->user = $user;
        $scheduleAdapter = $em->getRepository('DefaultDb_Entities_Schedule');
        $routesAdapter = $em->getRepository('DefaultDb_Entities_Route');
        $ordersAdapter = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
        $routePointAdapter = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $date=new DateTime();
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
//            var_dump($post);
            //cambiamos el estatus que recibimos de la orden solo cambia de "en centro de intercambio" a "en ruta" o viceversa
            if(is_array($post) && isset($post['toInterchangeCenter']) && isset($post['idOrder']))
            {
                $status = $post['toInterchangeCenter'];
                $idOrder = $post['idOrder'];
                $order = $ordersAdapter->find($idOrder);
//                var_dump($order);
//                var_dump($status);
                if($order)
                {
                    if($status == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER )
                    {
                        
                        switch($order->getShippingStatus())
                        {
                            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED:
                            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED:
                            //var_dump($status);
                            $order->setShippingStatus($status);
                            $em->persist($order);
                                break;
                        }
                    }
                    else if($status == DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED)
                    {
//                        var_dump($status);
                        switch($order->getShippingStatus())
                        {
                            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED:
                            case DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER:
                            //var_dump($status);
                            $order->setShippingStatus($status);
                            $em->persist($order);
                                break;
                        }
                    }
                    $em->flush();
                }
            }            
        }
        $schedule = $scheduleAdapter->getScheduleByUserAndDate($user,$date);
        $registers=false;
        if($schedule)
        {
            $startDate = new DateTime($date->format('Y-m-d')); //Esta se debe usar para la fecha actual
            $route = $schedule->getRoute();//$routesAdapter->find($schedule->getRoute()->getId());
            $hourDate = $schedule->getStartDate();
            $hour = $hourDate->format('H');
            $registers = $routePointAdapter->getSecuencialActivityByDateByRoute($startDate,$route,$schedule->getStartDate()->format('Y-m-d H:i:s'));
            //var_dump($registers);
            //$registers = $ordersAdapter->getSecuencialActivityByDateByRoute($startDate,$route,$schedule->getStartDate()->format('Y-m-d H:i:s'));
        }
        $this->view->registers = $registers;
    }
    
    public function selectReasonClientAction()
    {
        $this->view->result=false;
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            
            if(isset($post['clientCode']))
            {
                $clientCode = $post['clientCode'];
                $em = $this->getEntityManager('DefaultDb');
                //Obtiene el usuario cliente
                $userRepos = $em->getRepository('DefaultDb_Entities_User');
                $user = $userRepos->findOneBy(array('code'=>$clientCode));
                if($user){
                    $viewUser = new Model3_Session_Namespace('viewUser');
                    $viewUser->id = $user->getId();
                    $this->view->user = $user;
                    $this->view->result=true;
                }
            }
        }
        $this->view->getCssManager()->addCss('view/scripts/Driver/Dashboard/selectReasonClient'.VERSION_CSS.'.css');
    }
    
    public function redirectRecolectionAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['clientCode']))
            {
                $clientCode = $post['clientCode'];
                $em = $this->getEntityManager('DefaultDb');
                $userRepos = $em->getRepository('DefaultDb_Entities_User');
                $user = $userRepos->findOneBy(array('code'=>$clientCode));
                if($user){
                    $viewUser = new Model3_Session_Namespace('viewUser');
                    $viewUser->id = $user->getId();
                    $this->redirect('User/BackStore/orders');
                }
            }
        }
        $this->redirect('Driver/Dashboard/index');
    }
    
    public function redirectShippingAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['clientCode']))
            {
                $clientCode = $post['clientCode'];
                $em = $this->getEntityManager('DefaultDb');
                $userRepos = $em->getRepository('DefaultDb_Entities_User');
                $user = $userRepos->findOneBy(array('code'=>$clientCode));
                if($user){
                    $viewUser = new Model3_Session_Namespace('viewUser');
                    $viewUser->id = $user->getId();
                    $this->redirect('User/BackStore/shopping');
                }
            }
        }
        $this->redirect('Driver/Dashboard/index');
    }
    
    public function redirectVisitAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['clientCode']))
            {
                $clientCode = $post['clientCode'];
                $em = $this->getEntityManager('DefaultDb');
                $userRepos = $em->getRepository('DefaultDb_Entities_User');
                $user = $userRepos->findOneBy(array('code'=>$clientCode));
                if($user){
                    $viewUser = new Model3_Session_Namespace('viewUser');
                    $viewUser->id = $user->getId();
                    $this->redirect('Driver/FavoriteUsers/index');
                }
            }
        }
        $this->redirect('Driver/Dashboard/index');
    }
    
    public function changeStatusToInterchangeCenterAction()
    {
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if( is_array($post) && in_array('toInterchangeCenter') )
            {
                $status = $post['toInterchangeCenter'];
                $em = $this->getEntityManager('DefaultDb');
                $userRepos = $em->getRepository('DefaultDb_Entities_User');
                $user = $userRepos->findOneBy(array('code'=>$clientCode));
                if($user){
                    $viewUser = new Model3_Session_Namespace('viewUser');
                    $viewUser->id = $user->getId();
                }
            }
        }
        $this->redirect('Driver/Dashboard/index');
    }
    
    
    /*@todo: meter cuando se agrega la orden a centro de intercambio y cuando se recolecta de centro de intercambio*/
}