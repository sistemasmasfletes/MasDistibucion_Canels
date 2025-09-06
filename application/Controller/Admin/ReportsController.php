<?php

class Admin_ReportsController extends Model3_Controller
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
        
    }

    public function secuencialActivityAction()
    {
        $startDate = new DateTime();        
        $em = $this->getEntityManager('DefaultDb');
        $routes = $em->getRepository('DefaultDb_Entities_Route')->findAll();
        $criteria = array();
        $orderBy = array('routeDate');
        //$routesHour = $em->getRepository('DefaultDb_Entities_SequentialActivities')->findBy($criteria, $orderBy);
        $date= new DateTime();
        $dateSecond = new DateTime();
        $dateSecond->modify('+1 day');
        $query = $em->getRepository('DefaultDb_Entities_SequentialActivities')->createQueryBuilder('s')
        ->select('DISTINCT s.routeDate')
        ->Where('s.shippingDate >= \''.$date->format('Y-m-d').'\'')
        ->andWhere('s.shippingDate <= \''.$dateSecond->format('Y-m-d').'\'')
        ->orderBy('s.shippingDate')
        ->getQuery();
//        var_dump($query->getSQL());
        
        $routesHour = $query->getResult();
        $this->view->startDate = $startDate;
        $this->view->routes = $routes;
        $this->view->routesHour = $routesHour;

        // archivos
        $url = $this->view->url(array('module'=>false,'controller'=>'Ajax_Reports','action'=>'secuencialActivity'));
        $urlGetOptionsHour = $this->view->url(array('module'=>false,'controller'=>'Ajax_Reports','action'=>'getOptionsHour'));
        $this->view->getJsManager()->addJsVar('urlGetOptionsHour',json_encode($urlGetOptionsHour));
        $this->view->getJsManager()->addJsVar('urlGetSecuencialActivity', json_encode($url));
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Reports/secuencialActivity'.VERSION_JS.'.js');
    }

    public function ordersAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $order = false;

        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            
            if(isset ($post['numOrden']))
            {
                $em = $this->getEntityManager('DefaultDb');
                $packagesAdapter = $em->getRepository('DefaultDb_Entities_PackageToOrder');
                
                $order = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['numOrden']);
                
                if($order instanceof DefaultDb_Entities_M3CommerceOrder)
                {
                    $packages = $packs = $packagesAdapter->findBy(array('order'=>$order));
                    $this->view->packges =  $packages;
                }
                
            }
            
        }
        $this->view->order = $order;
    }

}