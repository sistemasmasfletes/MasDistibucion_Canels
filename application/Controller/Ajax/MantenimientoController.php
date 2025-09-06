<?php

class Ajax_MantenimientoController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function indexAction()
    {
        $invoicesUsers = null;
        $clientsArray = array();
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            //Se llama hacen las ordenes recurrentes
            $this->ordersRecurrents($post);
            $day = $post['day'];
            $em = $this->getEntityManager('DefaultDb');
            $userRepos = $em->getRepository('DefaultDb_Entities_User');
            $invoicesUsersRepos = $em->getRepository('DefaultDb_Entities_PackageToOrder');
//            $users = $userRepos->findBy(array('dateInvoice' => $day));
            $date = new DateTime();
            $dateString = $date->format('Y-m-d');
            $invoicesUsers = $invoicesUsersRepos->getInvoicesUntilDateAndDateInvoice($dateString, $day);
            foreach ($invoicesUsers as $iU)
            {
                $client = $userRepos->find($iU['packagingGenerated_id']);
                if ($client)
                {
                    $clientsArray[] = $client;
                    $invoice = new DefaultDb_Entities_Invoices();
                    $invoice->setCutDate($date);
                    $invoice->setClient($client);
                    $invoice->setNumOrders($iU['num_orders']); 
                    $invoice->setStatus(0);
                    $invoice->setGeneratedInvoice($date);
                    $invoice->setPriceTotal($iU['total_price']);
                    $em->persist($invoice);

                    $packages = $invoicesUsersRepos->getPackagesToOrdersUntilDateNotInvoice($dateString, $client);
                    foreach ($packages as $package)
                    {
                        $package->setInvoice($invoice);
                    }
                }
                $em->flush();
            }
            $userAdapter = $em->getRepository('DefaultDb_Entities_User');
            $receptionists = $userAdapter->findBy(array('type'=>  DefaultDb_Entities_User::USER_SECRETARY));
            if($invoicesUsers)
            {
                foreach($receptionists as $receptionist)
                {
                    if($receptionist->getStatus()==DefaultDb_Entities_User::STATUS_ACTIVE)
                    {
                        $mailHelper=new Helper_MailHelper();
                        $result = $mailHelper->sendMailToSecretary($receptionist,$clientsArray);
                    }
                }
            }
        }
        $this->view->invoicesUsers = $invoicesUsers;
    }

    private function ordersRecurrents($post)
    {
        $em = $this->getEntityManager('DefaultDb');
        $packageToOrderRepos = $em->getRepository('DefaultDb_Entities_PackageToOrder');
        $ordersRecurrentRepos = $em->getRepository('DefaultDb_Entities_M3CommerceOrder');

        $orderRecurrents = $ordersRecurrentRepos->findBy(array('recurrent' => DefaultDb_Entities_M3CommerceOrder::RECURRENT_ACTIVE, 'orderStatus' => DefaultDb_Entities_M3CommerceOrder::ORDER_STATUS_ACTIVE, 'shippingStatus' => DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED));     
        $day = $post['day'];
        $flag = false;
        //var_dump($day);
        $scheduleHelper = new Helper_Schedule;
        
        if (is_array($orderRecurrents) && count($orderRecurrents) > 0)
        {
            foreach ($orderRecurrents as $oR)
            {
                $packages = $packageToOrderRepos->findBy(array('order' => $oR->getId()));
                $monday = $oR->getMonday();
                $tuesday = $oR->getTuesday();
                $wednesday = $oR->getWednesday();
                $thursday = $oR->getThursday();
                $friday = $oR->getFriday();
                $saturday = $oR->getSaturday();
                $sunday = $oR->getSunday();
                switch($day)
                {
                    case DefaultDb_Entities_User::DAY_MONDAY:
                        if($wednesday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_TUESDAY:
                        if($thursday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_WEDNESDAY:
                        if($friday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_THURSDAY:
                        if($saturday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_FRIDAY:
                        if($sunday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_SATURDAR:
                        if($monday == 1)
                            $flag = true;
                        break;
                    case DefaultDb_Entities_User::DAY_SUNDAY:
                        if($tuesday == 1)
                            $flag = true;
                        break;
                }
                if($flag) 
                {
                    //var_dump('Se genero recurrente : nota quitar este vardump y poner mas especifico cuantas ordenes se generan');
                    $orderSon = new DefaultDb_Entities_M3CommerceOrder();
                    //Primero la guardamos para ver que realiza
                    $orderSon->setBuyer($oR->getBuyer());
                    $orderSon->setSeller($oR->getSeller());
                    $orderSon->setCreationDate(new DateTime());
                    $orderSon->setComments($oR->getComments());
                    $orderSon->setOrderParent($oR);
                    /* Establecemos la nueva fecha para el shippingDate */
//                    $orderSon->setShippingDate($shippingDate);
                    $orderSon->setShippingStatus(DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_NOT_SHIPPED);
                    // Establecemos el schedule mas reciente
//                    $orderSon->setSchedule($schedule);
                    $orderSon->setPointBuyer($oR->getPointBuyer());
                    $orderSon->setPointSeller($oR->getPointSeller());
                    $orderSon->setOrderStatus(0);
                    $em->persist($orderSon);
                    $em->flush();
                    
                    
                    $scheduleAdapter = $em->getRepository('DefaultDb_Entities_Schedule');
                    $routesAdapter = $em->getRepository('DefaultDb_Entities_Route');
                    $routePointsAdapter = $em->getRepository('DefaultDb_Entities_RoutePoint');
                    /* Calculo de la siguiente fecha valida*/
                    $date = new DateTime();
                    $date->setTime(0, 0, 0);
                    $date->modify('+3 day');
                    $schedules = $scheduleAdapter->getNextSchedulesByRouteAndPointBranchAndDate($oR->getSeller()->getId(),$oR->getPointSeller()->getId(),$date,20,true); //traemos las proximas 20 rutas
                    if(is_array($schedules) && !empty($schedules))
                    {
                        $scheduleHelper = new Helper_Schedule($em);
                        $schedules = $scheduleHelper->getNextDatesValid($schedules,$date);
                        $schedule = $scheduleAdapter->find($schedules[0]['id']);
                        /* Schedule es la proxima ruta valida*/

                        /* Datos necesarios */
                        $newShippingDate = new DateTime($schedules[0]['start_date']);
                        $scheduleHelper->setOrder($orderSon);
                        $scheduleHelper->setDateInitRoute($newShippingDate);
                        $shippingDateInitRoute = new DateTime($schedules[0]['start_date']);
                        $route = $routesAdapter->find($schedules[0]['route_id']);

                        $MyRoutePoint = $routePointsAdapter->findOneBy(array('route'=>$route,'point'=>$oR->getPointSeller()));
                        $routePointBuyer = $routePointsAdapter->findOneBy(array('point'=>$oR->getPointBuyer()));
                        $routeBuyer = $routePointBuyer->getRoute();

                        /* CALCULAMOS LA RECOLECCION */
                        $shippingDate = $scheduleHelper->calculateRecolection($newShippingDate, $route, $MyRoutePoint);
                        /* CALCULAMOS TODO REFERENRE A LA ENTREGA */
                        $scheduleHelper->addPointRoute($shippingDateInitRoute->format('Y-m-d H:i:s'),'Recoleccion ',$MyRoutePoint->getPoint()->getName()); //Punto inicial
    
                        /* CALCULAMOS EL PRIMERO PUNTO SEQUENCIAL*/
                        $sequentialActivities = new DefaultDb_Entities_SequentialActivities;
                        $sequentialActivities->setRoutePoint($MyRoutePoint);
                        $sequentialActivities->setShippingDate($shippingDate);
                        $sequentialActivities->setType(DefaultDb_Entities_SequentialActivities::TYPE_RECOLECTION);
                        $sequentialActivities->setOrder($orderSon);
                        $sequentialActivities->setRouteDate($shippingDateInitRoute);
                        $em->persist($sequentialActivities);
                        /*CALCULAMOS TODOS LOS PUNTOS INTERMEDIOS DE LA SECUENCIAL*/
                        $shippingDateDelivery = $scheduleHelper->calculateDelivery($oR->getBuyer(),$shippingDateInitRoute,$route, $routeBuyer, $MyRoutePoint, $routePointBuyer);
                        /*GUARDAMOS EL ULTIMO PUNTO DE LA SECUENCIAL*/
                        $sequentialActivitiesDelivery = new DefaultDb_Entities_SequentialActivities;
                        $sequentialActivitiesDelivery->setRoutePoint($routePointBuyer);
                        $sequentialActivitiesDelivery->setShippingDate($shippingDateDelivery);
                        $sequentialActivitiesDelivery->setType(DefaultDb_Entities_SequentialActivities::TYPE_DELIVERY);
                        $sequentialActivitiesDelivery->setOrder($orderSon);
                        $sequentialActivitiesDelivery->setRouteDate($scheduleHelper->getDateInitRoute());
                        $em->persist($sequentialActivitiesDelivery);


                        /* GUARDAMOS LA ORDEN*/
    //                    $orderSon->setBuyer($oR->getBuyer());
    //                    $orderSon->setSeller($oR->getSeller());
    //                    $orderSon->setCreationDate(new DateTime());
    //                    $orderSon->setComments($oR->getComments());
    //                    $orderSon->setOrderParent($oR);
                        /* Establecemos la nueva fecha para el shippingDate */
                        $orderSon->setShippingDate($shippingDate);
                        $orderSon->setShippingStatus($oR->getShippingStatus());
    //                    $orderSon->setShippingStatus($oR->getShippingStatus());
                        // Establecemos el schedule mas reciente
                        $orderSon->setSchedule($schedule);

    //                    $orderSon->setOrderStatus(0);
                        $em->persist($orderSon);
                        foreach($packages as $p)
                        {
                            $newPackage = new DefaultDb_Entities_PackageToOrder();
                            $newPackage->setNumPackage($p->getNumPackage());
                            $newPackage->setTotalPrice($p->getTotalPrice());
                            $newPackage->setPrice($p->getPrice());
                            $newPackage->setDateSend(new DateTime());
                            $newPackage->setPackagingGenerated($p->getPackagingGenerated());
                            $newPackage->setWeight($p->getWeight());
                            $newPackage->setHeight($p->getHeight());
                            $newPackage->setWidth($p->getWidth());
                            $newPackage->setDepth($p->getDepth());
                            $newPackage->setOrder($orderSon);
                            $newPackage->setNamePackage($p->getNamePackage());
                            $em->persist($newPackage);
                        }
                        $em->flush();
                    }
                }
            }
        }
    }
}

?>
