<?php

class Ajax_ReportsController extends Model3_Controller
{
    public function init()
    {
        $this->view->setUseTemplate(false);
    }
    
    public function indexAction()
    {
        
    }

    public function secuencialActivityAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $ordersRepository = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');
        $routesRepository = $em->getRepository('DefaultDb_Entities_Route');
        $routesPointsRepository = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $configRepository = $em->getRepository('DefaultDb_Entities_Configuration');
        $configurations = $configRepository->find(1);
        $minutesConfiguration = $configurations->getMinutesPerPoint();
        $registers = false;
        $routepointsByRoute = false;
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $starDate = new DateTime($post['starDate']);
            $route = $routesRepository->find($post['route']);
            $routepointsByRoute = $routesPointsRepository->findBy( array('route' => $post['route'],'status'=>1),array('order' => 'asc'));
            $hour = isset($post['hourRoute']) && $post['hourRoute'] ? $post['hourRoute']: null;
            $registers = $ordersRepository->getSecuencialActivityByDateByRoute($starDate,$route,$hour);
        }
        $this->view->startDate = isset($post['hourRoute']) && $post['hourRoute'] ? new DateTime($post['hourRoute']): $starDate;
        $this->view->minutesConfiguration = $minutesConfiguration;
        $this->view->routePointsByRoute = $routepointsByRoute;
        $this->view->registers = $registers;
    }
    
    public function getOptionsHourAction()
    {
        $this->view->result=false;
        if($this->getRequest()->isPost())
        {
            $em = $this->getEntityManager('DefaultDb');
            $post = $this->getRequest()->getPost();
            $date= new DateTime($post['date']);
            
            $dateSecond = new DateTime($post['date']);
            $dateSecond->modify('+1 day');
            
            $query = $em->getRepository('DefaultDb_Entities_SequentialActivities')->createQueryBuilder('s')
            ->select('DISTINCT s.routeDate')
            ->join('s.routePoint','rp')
            ->join('rp.route', 'r')
            ->Where('s.shippingDate >= \''.$date->format('Y-m-d 00:00:00').'\'')
            ->andWhere('s.shippingDate < \''.$dateSecond->format('Y-m-d 00:00:00').'\'')
            ->andWhere('r.id = '.$post['route'])
            ->andWhere('r.status = 1')
            ->orderBy('s.shippingDate','ASC')
            ->getQuery();
    //var_dump($query->getSQL());
            $routesHour = $query->getResult();
            $this->view->result = true;
            $this->view->routesHours = $routesHour;
        }
    }

}