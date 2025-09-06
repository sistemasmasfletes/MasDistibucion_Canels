<?php

class Admin_ScheduleController extends Model3_Controller
{
    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }  
    }
    
    public function routeAction()
    {
        $idRoute = $this->getRequest()->getParam('id');
        $em = $this->getEntityManager('DefaultDb');
        
        $routeRepo = $em->getRepository('DefaultDb_Entities_Route');
        $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');
        
        $route = $routeRepo->find($idRoute);
        $schedules = $scheduleRepo->findBy(array('route' => $route,'status' => DefaultDb_Entities_Schedule::STATUS_ACTIVE,'recurrent' => DefaultDb_Entities_Schedule::RECURRENT_INACTIVE ));
        $schedulesRecurrent = $scheduleRepo->findBy(array('route' => $route,'status' => DefaultDb_Entities_Schedule::STATUS_ACTIVE, 'recurrent' => DefaultDb_Entities_Schedule::RECURRENT_ACTIVE));
        
        $this->view->route = $route;
        $this->view->schedules =$schedules;
        $this->view->schedulesRecurrents = $schedulesRecurrent;
    }
    
    public function addRecurrentAction()
    {
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Schedule/datepickerRoot.js');
        
        $idRoute = $this->getRequest()->getParam('route');
        $em = $this->getEntityManager('DefaultDb');
        
        $routeRepo = $em->getRepository('DefaultDb_Entities_Route');
        $route = $routeRepo->find($idRoute);
        
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $startDate = new DateTime($post['date'].' '.$post['hours'].':'.$post['mins']);
            $week = 0;
            if(isset($post['week']) && $post['week'] == 'on')
                $week = 1;
            $monday = 0;
            if(isset($post['lu']) && $post['lu'] == 'on')
                $monday = 1;
            $tuesday = 0;
            if(isset($post['ma']) && $post['ma'] == 'on')
                $tuesday = 1;
            $wednesday = 0;
            if(isset($post['mi']) && $post['mi'] == 'on')
                $wednesday = 1;
            $thursday = 0;
            if(isset($post['ju']) && $post['ju'] == 'on')
                $thursday = 1;
            $friday = 0;
            if(isset($post['vi']) && $post['vi'] == 'on')
                $friday = 1;
            $saturday = 0;
            if(isset($post['sa']) && $post['sa'] == 'on')
                $saturday = 1;
            $sunday = 0;
            if(isset($post['do']) && $post['do'] == 'on')
                $sunday = 1;
            
            $schedule = new DefaultDb_Entities_Schedule();
            $schedule->setRoute($route);
            $schedule->setStartDate($startDate);
            $schedule->setWeek($week);
            $schedule->setMonday($monday);
            $schedule->setTuesday($tuesday);
            $schedule->setWednesday($wednesday);
            $schedule->setThursday($thursday);
            $schedule->setFriday($friday);
            $schedule->setSaturday($saturday);
            $schedule->setSunday($sunday);
            $schedule->setRecurrent(DefaultDb_Entities_Schedule::RECURRENT_ACTIVE);
            
            $em->persist($schedule);
            $em->flush();
            $this->view->result = true;
        }
                
        $this->view->route = $route;
    }
    
    public function addAction()
    {
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Schedule/datepickerRoot.js');
        
        $idRoute = $this->getRequest()->getParam('route');
        $em = $this->getEntityManager('DefaultDb');
        
        $routeRepo = $em->getRepository('DefaultDb_Entities_Route');
        $route = $routeRepo->find($idRoute);
        $this->view->result = null;
        if($this->getRequest()->isPost())
        { 
            $post = $this->getRequest()->getPost();
            $post['date']= str_replace('/', '-', $post['date']);
            try
            {
                $startDate = new DateTime($post['date'].' '.$post['hours'].':'.$post['mins']);

                $schedule = new DefaultDb_Entities_Schedule();
                $schedule->setRoute($route);
                $schedule->setStartDate($startDate);
                $em->persist($schedule);
                $em->flush();
                $this->view->result = true;
            }
            catch (Exception $e)
            {
                $this->view->result = false;
            }
        }
                
        $this->view->route = $route;
    }
    
    public function editAction()
    {
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Schedule/datepickerRoot.js');
        
        $idRoute = $this->getRequest()->getParam('route');
        $idScheduleDate = $this->getRequest()->getParam('id');
        
        $em = $this->getEntityManager('DefaultDb');
        $routeRepo = $em->getRepository('DefaultDb_Entities_Route');
        $scheduleDateRepo = $em->getRepository('DefaultDb_Entities_Schedule');
        
        $route = $routeRepo->find($idRoute);
        $scheduleDate = $scheduleDateRepo->find($idScheduleDate);
        
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $startDate = new DateTime($post['date'].' '.$post['hours'].':'.$post['mins']);

//            $schedule = new DefaultDb_Entities_Schedule();
            $scheduleDate->setRoute($route);
            $scheduleDate->setStartDate($startDate);
            $em->persist($scheduleDate);
            $em->flush();
           $this->view->result = true;
        }
                
        $this->view->route = $route;
        $this->view->scheduledate = $scheduleDate;
    }
    
    public function editRecurrentAction()
    {
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('application/Admin/Schedule/datepickerRoot.js');
        
        $idRoute = $this->getRequest()->getParam('route');
        $idScheduleDate = $this->getRequest()->getParam('id');
        
        $em = $this->getEntityManager('DefaultDb');
        $routeRepo = $em->getRepository('DefaultDb_Entities_Route');
        $scheduleDateRepo = $em->getRepository('DefaultDb_Entities_Schedule');
        
        $route = $routeRepo->find($idRoute);
        $schedule = $scheduleDateRepo->find($idScheduleDate);
        
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $startDate = new DateTime($post['date'].' '.$post['hours'].':'.$post['mins']);
            $week = 0;
            if(isset($post['week']) && $post['week'] == 'on')
                $week = 1;
            $monday = 0;
            if(isset($post['lu']) && $post['lu'] == 'on')
                $monday = 1;
            $tuesday = 0;
            if(isset($post['ma']) && $post['ma'] == 'on')
                $tuesday = 1;
            $wednesday = 0;
            if(isset($post['mi']) && $post['mi'] == 'on')
                $wednesday = 1;
            $thursday = 0;
            if(isset($post['ju']) && $post['ju'] == 'on')
                $thursday = 1;
            $friday = 0;
            if(isset($post['vi']) && $post['vi'] == 'on')
                $friday = 1;
            $saturday = 0;
            if(isset($post['sa']) && $post['sa'] == 'on')
                $saturday = 1;
            $sunday = 0;
            if(isset($post['do']) && $post['do'] == 'on')
                $sunday = 1;

            $schedule->setRoute($route);
            $schedule->setStartDate($startDate);
            $schedule->setWeek($week);
            $schedule->setMonday($monday);
            $schedule->setTuesday($tuesday);
            $schedule->setWednesday($wednesday);
            $schedule->setThursday($thursday);
            $schedule->setFriday($friday);
            $schedule->setSaturday($saturday);
            $schedule->setSunday($sunday);
            $schedule->setRecurrent(DefaultDb_Entities_Schedule::RECURRENT_ACTIVE);
            
            $em->persist($schedule);
            $em->flush();
            $this->view->result = true;
        }
                
        $this->view->route = $route;
        $this->view->scheduledate = $schedule;
    }
    
    public function deleteAction()
    {
        //Es un borrado logico se pasa el id de la ruta para poder regresar a la misma vista 
        $idRoute = $this->getRequest()->getParam('route');   
        $idScheduleDate = $this->getRequest()->getParam('id');
        $em = $this->getEntityManager('DefaultDb');
        $scheduleDateRepo = $em->getRepository('DefaultDb_Entities_Schedule');

        $scheduleDate = $scheduleDateRepo->find($idScheduleDate);
        $scheduleDate->setStatus(DefaultDb_Entities_Schedule::STATUS_INACTIVE);
        $em->persist($scheduleDate);
        $em->flush();
        
        $this->view->route = $idRoute;
    }
    
       
}
