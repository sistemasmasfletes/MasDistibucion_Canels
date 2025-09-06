<?php

class Admin_MantenimientoController extends Model3_Controller
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
       //$this->view->getJsManager()->addJs('application/Admin/Mantenimiento/mantenimientoFacturas.js');
       //$this->view->getJsManager()->addJsVar('urlMantenimiento', '"'.$this->view->getBaseUrl().'/Ajax/Mantenimiento/index'.'"');
       
   }
   public function schedulesAction()
   {
       //Se hace el llamado a la funcio para cortar las cuentas exedidas en creditos
       // es decir a los usuarios que tiene facturas no pagadas desde hace mas de quince dias se les bloquea automaticamente
       $this->cutAccount();
       $numerDays=3;
       if(count($this->getRequest()->getParams()) > 0)
            $numerDays = intval ($this->getRequest()->getParam('days'));
        $em = $this->getEntityManager('DefaultDb');
        $scheduleAdapter = $em->getRepository('DefaultDb_Entities_Schedule');
        $schedules = $scheduleAdapter->findBy(array('status'=> DefaultDb_Entities_Schedule::STATUS_ACTIVE,'recurrent'=>  DefaultDb_Entities_Schedule::RECURRENT_ACTIVE));
        //var_dump($scheduleAdapter);
        //$schedules = $scheduleAdapter->findBy(array('status'=> DefaultDb_Entities_Schedule::STATUS_ACTIVE,'recurrent'=>  DefaultDb_Entities_Schedule::RECURRENT_INACTIVE));
        $this->view->newSchedules = 0;
        $date = new DateTime();
        $date = new DateTime($date->format('Y-m-d'));
        $date->modify('+'.$numerDays.' day');
        $dateResp=new DateTime($date->format('Y-m-d'));
        $this->view->dateAdded = $date->format ('Y-m-d');
        $dateOne = new DateTime($date->format('Y-m-d'));
        $dateOne->modify('+1 day');
        $arrayRutas= array();
       foreach($schedules as $schedule)
       {
            $dateFo = $schedule->getStartDateRecurrent($date);
            $date=$dateResp;
            if($dateFo >= $date && $dateFo < $dateOne){
                $arrayFind = array(
                    'route'=>$schedule->getRoute(),
                    'startDate'=>$dateFo,
                    'status'=> DefaultDb_Entities_Schedule::STATUS_ACTIVE,
                    'recurrent'=> DefaultDb_Entities_Schedule::RECURRENT_INACTIVE,
                    'week'=>DefaultDb_Entities_Schedule::RECURRENT_WEEK_INACTIVE,
                    'scheduleParent' => $schedule
                    );
                $result = $scheduleAdapter->findBy($arrayFind); //buscamos que no exista antes               
                if(is_array($result) && empty($result)){
                    //Creamos los nuevos pero sin que queden como recurrentes        
                    $scheduleNew = new DefaultDb_Entities_Schedule();
                    $scheduleNew->setRoute($schedule->getRoute());
                    $scheduleNew->setStartDate($dateFo);
                    $scheduleNew->setStatus(DefaultDb_Entities_Schedule::STATUS_ACTIVE);
                    $scheduleNew->setRecurrent(DefaultDb_Entities_Schedule::RECURRENT_INACTIVE);//estos estatus los inactivo
                    $scheduleNew->setWeek(DefaultDb_Entities_Schedule::RECURRENT_WEEK_INACTIVE);//estos los inactivo
                    $scheduleNew->setScheduleParent($schedule);
                    $em->persist($scheduleNew);
                    $em->flush();
                    $this->view->newSchedules +=1;
                    $scheduleRoutearray=array('route'=>$schedule->getRoute()->getName(),'date'=>$dateFo->format('Y-m-d H:i'));
                    $arrayRutas[]=$scheduleRoutearray;
                }
            }
            
       }
       $this->view->routesNews=$arrayRutas;
       $this->view->getJsManager()->addJs('application/Admin/Mantenimiento/mantenimientoFacturas.js');
       $this->view->getJsManager()->addJsVar('urlMantenimiento', '"'.$this->view->getBaseUrl().'/Ajax/Mantenimiento/index'.'"');
       
       
   }
   
   function cutAccount()
   {
       $em = $this->getEntityManager('DefaultDb');
       $invoiceRepos = $em->getRepository('DefaultDb_Entities_Invoices');
       $dateOfCourt = new DateTime();//date('Y-m-d',strtotime('-15 day'));
       $dateOfCourt->modify('-15 day');
       $invoicesExpired = $invoiceRepos->getInvoiceExpired($dateOfCourt);
       foreach ($invoicesExpired as $iE)
       {
           $user = $iE->getClient();
           $user->setStatus(DefaultDb_Entities_User::STATUS_BLOCK);
           $em->persist($user);
       }
       $em->flush();
       
   }
   
}