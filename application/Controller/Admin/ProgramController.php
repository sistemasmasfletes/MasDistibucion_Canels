<?php

class Admin_ProgramController extends Model3_Controller
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
        $this->view->getJsManager()->addJs('application/Admin/Schedule/datepickerProgram.js');
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $em = $this->getEntityManager('DefaultDb');
            $conn = $em->getConnection();
            /**
             * @todo Cambiar de lugar esta consulta
             */
            $query = 'SELECT s.*, r.name FROM schedule s 
                INNER JOIN routes r ON s.route_id = r.id 
                WHERE DATE(start_date) >=  \''.$post['date'].'\' 
                AND DATE(start_date) <= \''.$post['dateSecond'].'\' 
                AND s.status = 1';
            $res = $conn->executeQuery($query);
            $schedules = $res->fetchAll();
            $this->view->schedules = $schedules;
        }
    }
    
    public function asignAction()
    {
        $idSchedule = $this->getRequest()->getParam('id');
        $em = $this->getEntityManager('DefaultDb');
        $scheduleRepo = $em->getRepository('DefaultDb_Entities_Schedule');
        $userRepo = $em->getRepository('DefaultDb_Entities_User');
        $vehicleRepo = $em->getRepository('DefaultDb_Entities_Vehicle');
        
        $schedule = $scheduleRepo->find($idSchedule);
        $this->view->users = $userRepo->findBy(array('type' => DefaultDb_Entities_User::USER_DRIVER));
        $this->view->vehicles = $vehicleRepo->findAll();
         
        $this->view->res = false;
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            
            
            $schedule->setUser($userRepo->find($post['user']));
            $schedule->setVehicle($vehicleRepo->find($post['vehicle']));
            $em->flush();
            $this->view->res = true;
        }
    }

}