<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Schedule
 * Helper utilizado para verificar las proximas fechas validas de los Schedules
 * en el caso de las salidas programadas periodicamente.
 *
 * @author Usiel
 */
class Helper_Schedule
{

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $_em;
    private $_routesRevised = array();
    private $_pointsRevised = array();
    private $_pointsRoute = array();
    private $_arrayEntrega = array();
    private $_arrayRecoleccion = array();
    private $_dateInitRoute = null;
    private $_order;
    private $_routesFactor = array();
    private $_routePointActivity = array();

    public function __construct($em = null)
    {
        $this->_em = $em;
    }

    /**
     * verifica las fechas que le llegan si alguna es recurrente toma la siguiente fecha valida para esa ruta que se repita
     * @param array $arrayDates Arreglo de schedules
     * @param DateTime $dateParameter Fecha con la cual se compara en caso de ser null se verifica con la fecha actual
     * @return array regresa las fechas validas de las que son recurrentes
     */
    function getNextDatesValid($arrayDates, $dateParameter = null)
    {
        $schedulesRepo = $this->_em->getRepository('DefaultDb_Entities_Schedule');

        foreach ($arrayDates as $key => $re)
        {
            $schedule = $schedulesRepo->find($re['id']);
            if ($schedule->getRecurrent() == DefaultDb_Entities_Schedule::RECURRENT_ACTIVE) //es recurrente
            {
                $aux = $this->getNextDatesInRecurrent($schedule, $dateParameter);
                if ($aux)
                    $arrayDates[$key]['start_date'] = $aux->format('Y-m-d H:i:s');

//                if ($dateParameter === null)
//                {
//                    $date = new DateTime();
//                    //$date->setTime(0,0,0);
//                }
//                else
//                {
//                    $date = new DateTime($dateParameter->format('Y-m-d H:i:s'));
//                    var_dump($date);
//                    echo '<hr/>';
//                    var_dump($re['start_date']);
//                    echo '<hr/>';
//                }
//                $nextDay = $this->getDay($re, $date); //monday';
//                $dateBD = new DateTime($re['start_date']);
//
//                if ($nextDay !== false)
//                {
//                    if ($date > $dateBD)
//                    {
//                        if (strtolower($date->format('l')) != $nextDay)
//                        {
//                            $date->modify('next ' . $nextDay);
//                        }
//                        else
//                        {
//                            echo '**1';
//                            $date->setTime(0, 0, 0); //solucion a la hora actual
//                            if ($date < new DateTime())
//                            {
//                                //$nextDay = $this->getDay($re,$date);//monday';
//                                $date->modify('next ' . $nextDay);
//                            }
//                        }
//                        $timeTmp = explode(':', $dateBD->format('H:i:s'));
//                        $timeTmp = (int) $timeTmp['0'] * 3600 + (int) $timeTmp['1'] * 60;
//                        $interval = new DateInterval('PT' . $timeTmp . 'S');
//                        $date->add($interval);
//                        $arrayDates[$key]['start_date'] = $date->format('Y-m-d H:i:s');
//                    }
//                    else
//                    {
//                        $date = clone $dateBD; // Lo clonamos para no modificarlo
//                        if (strtolower($date->format('l')) != $nextDay)
//                        {
//                            $date->modify('next ' . $nextDay);
//                        }
//                        $timeTmp = explode(':', $dateBD->format('H:i:s'));
//                        $timeTmp = (int) $timeTmp['0'] * 3600 + (int) $timeTmp['1'] * 60;
//                        $interval = new DateInterval('PT' . $timeTmp . 'S');
//                        $date->add($interval);
//                        $arrayDates[$key]['start_date'] = $date->format('Y-m-d H:i:s');
//                    }
//                }
//                else
//                {
//                    unset($arrayDates[$key]); //borarlo ya que no nos sirve esa fecha
//                }
            }
            else
            {
                //quita los generados por el sistema ya que ya lo traemos y solo lo usamos para recorrer la ruta
                if ($re['scheduleParent_id'] !== null)
                    unset($arrayDates[$key]);
            }
        }
        // se ordena por fecha de menor a mayor para el caso de que encuentra una ruta que este antes de alguna otra ya que no se
        //organizan las recurrentes con las nuevas fechas que podria traer
        usort($arrayDates, array($this, 'datecmp'));

        return $arrayDates;
    }

    /**
     * Metodo usado en las fechas recurrentes que nos regresa el proximo dia que se puede usar en la ruta
     * si la ruta dice que puede ser lunes y miercoles y la fecha que se la pasa por segund parametro
     * es martes nos regresa el miercoles
     * si es jueves nos regresa el proximo lunes
     * Tambien verifica si la funcion esta recurrente por semana si no es recurrente por semana verifica 
     * que el dia proximo a regresar nos de el proximo valido siempre y cuando se pueda en esa semana
     * 
     * @param type $row registro el cual trae toda la informacion de los schedules
     * @param type $date fecha donde trae la fecha ya sea actual o la que se le pase por parametro
     * @return string|boolean regresa una cadena con el dia, en caso contrario regresa false
     */
    private function getDay($row, $date)
    {
        $week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        $inicio = (int) $date->Format('w');
        $key = $inicio;
        $numDays = 0;
        $first = true;
        while ($first || $key != $inicio)
        {
            $first = false;
            if ($key == 7)
            {
                $key = 0;
            }
            if ($row [$week [$key]] == DefaultDb_Entities_Schedule::DAY_ACTIVE)
            {
                if ($row['week'] == DefaultDb_Entities_Schedule::RECURRENT_WEEK_ACTIVE)
                {
                    return $week [$key];
                }
                else
                { //Como es solo valido para esa semana verificamos si esta semana alcanza fecha valida
                    $dateRangeMax = new DateTime($row['start_date']);
                    $dateRangeMax->modify('next monday');
                    $dateTemporalActual = new DateTime($date->Format('Y-m-d H:i:s'));
                    $dateTemporalActual->modify('+' . $numDays . ' day');
                    if ($dateTemporalActual < $dateRangeMax)
                    {
                        return $week [$key];
                    }
                }
            }
            if ($key == 0 && $row['week'] == DefaultDb_Entities_Schedule::RECURRENT_WEEK_INACTIVE)
            {
                break; //rompemos el ciclo
            }
            $key++;
            $numDays++;
        }
        return false;
    }

    /**
     * Metodo para comparar las fechas, usado para ordenar por fechas
     * @param string $a
     * @param string $b
     * @return int
     */
    public function datecmp($a, $b)
    {
        $date1 = new DateTime($a['start_date']);
        $date2 = new DateTime($b['start_date']);
        if ($date1 < $date2)
            return -1;
        else if ($date1 > $date2)
            return 1;
        return 0;
    }

    /**
     * Calcula la Recoleccion
     * @param int $idSchedule
     * @param type $route
     * @param type $MyRoutePoint
     * @return type
     */
    public function calculateRecolection($shippingDate, $route, $MyRoutePoint)
    {
        if ($this->_em !== null)
        {
            $scheduleAdapter = $this->_em->getRepository('DefaultDb_Entities_Schedule');
            $timeInSeconds = $scheduleAdapter->calculeShippidTime($route->getId(), $MyRoutePoint->getOrder());

            $timeInSeconds['timeInSeconds'] = isset($timeInSeconds['timeInSeconds']) ? $timeInSeconds['timeInSeconds'] : 0;
            $interval = new DateInterval('PT' . $timeInSeconds['timeInSeconds'] . 'S');
            $shippingDate = $shippingDate->add($interval);
            return $shippingDate;
        }
    }

    /**
     * 
     * @param DefaultDb_Entities_User $buyer Comprador
     * @param Datetime $shippingDate fecha
     * @param DefaultDb_Entities_Route $route Ruta
     * @param DefaultDb_Entities_Route $routeBuyer ruta del comprador
     * @param DefaultDb_Entities_Point $MyRoutePoint Mi Punto desde donde esta mi ruta
     * @param DefaultDb_Entities_User $buyerRoutePoint Punto donde esta el comprador
     * @return Datetime 
     */
    public function calculateDelivery($buyer, $shippingDate, $route, $routeBuyer, $MyRoutePoint, $buyerRoutePoint)
    {
        if ($this->_em !== null)
        {
            $scheduleAdapter = $this->_em->getRepository('DefaultDb_Entities_Schedule');
            $timeInSeconds = array('timeInSeconds' => 0); //usar con false si no se calcula
            //$shippingDate = $this->findRouteRecursive($route, $routeBuyer, $shippingDate, $buyerRoutePoint, $shippingDate->format('Y-m-d H:i'));
            $shippingDate = $this->findRouteRecursive2($route, $MyRoutePoint, $routeBuyer, $buyerRoutePoint, $shippingDate);
            return $shippingDate;
        }
    }

    public function findRouteRecursive2($routeOrigin, $pointOrigin, $routeDestiny, $pointDestiny, $startRoute)
    {
        if (in_array($routeOrigin->getId(), $this->_routesRevised) && $routeOrigin->getId() !== $routeDestiny->getId())
        {
//            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Repetida<br/>';
            return false;
        }
        else
        {
            array_push($this->_routesRevised, $routeOrigin->getId());
        }

//        echo '<br/>';
//        echo 'Inicia Ruta ' . $routeOrigin->getId() . '<br/>';
//        echo 'Inicia Punto ' . $pointOrigin->getId() . '<br/>';
//        echo 'Fin Ruta ' . $routeDestiny->getId() . '<br/>';
//        echo 'Fin Punto ' . $pointDestiny->getId() . '<br/>';

        $scheduleRepo = $this->_em->getRepository('DefaultDb_Entities_Schedule');
        $routesRepo = $this->_em->getRepository('DefaultDb_Entities_Route');

        if ($routeOrigin->getId() === $routeDestiny->getId())
        {
            if ($this->firstPointBeforeSecondPoint($routeOrigin, $pointOrigin, $pointDestiny))
            {
                $schedule = $this->findNextSchedule($routeOrigin, $startRoute);
                if ($schedule)
                {
                    $timeInSeconds = $scheduleRepo->calculeShippidTime($routeOrigin->getId(), $pointDestiny->getOrder());
                    $delivery = $schedule->add(new DateInterval('PT' . $timeInSeconds[0] . 'S'));
//                    echo $delivery->format('Y-m-d H:i:s');
//                    echo '<hr/>';
                    return $delivery;
                }
                return false;
            }
        }

        $routePoints = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getPointExchangeByRoute($routeOrigin);
        foreach ($routePoints as $routePoint)
        {
            if ($routePoint['order_number'] == 1)
                continue;

            $routePointOrigen = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('route' => $routeOrigin, 'point' => $pointOrigin));

            $point = $this->_em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $routePoint['idpoint']));
            
            if($pointOrigin instanceof  DefaultDb_Entities_RoutePoint)
                $timeInSeconds = $scheduleRepo->calculeShippidTime($routeOrigin->getId(), $routePoint['order_number'],$pointOrigin->getOrder());
            else
                $timeInSeconds = $scheduleRepo->calculeShippidTime($routeOrigin->getId(), $routePoint['order_number'],$routePointOrigen->getOrder());
            
            $seconds = $timeInSeconds[0] == null ? 0 : $timeInSeconds[0];

            $tempStartDate =  clone $startRoute;
            $deliveryExchange = $tempStartDate->add(new DateInterval('PT' . $seconds . 'S'));
         
            if (in_array($point->getId(), $this->_pointsRevised))
            {
                continue;
            }
            else
            {
                array_push($this->_pointsRevised, $point->getId());
            }

            $routes = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getRoutesByPoint($point);
            foreach ($routes as $routeReg)
            {
                $route = $routesRepo->find($routeReg['route_id']);

//                echo '&nbsp;&nbsp;&nbsp;&nbsp;Probando Ruta ' . $routeReg['route_id'] . '<br/>';

                $schedule = $this->findNextSchedule($route, $deliveryExchange);
                if ($schedule)
                {
                    $pointInNewRoute = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('route' => $route, 'point' => $point));
                    $timeInSeconds = $scheduleRepo->calculeShippidTime($route->getId(), $pointInNewRoute->getOrder());
                    $seconds = $timeInSeconds[0] == null ? 0 : $timeInSeconds[0];
                    $schedule->add(new DateInterval('PT' . $seconds . 'S'));                    
                    
                    $scheduleAux = clone $schedule;
                    $result = $this->findRouteRecursive2($route, $point, $routeDestiny, $pointDestiny, $schedule);
                    if ($result)
                    {

                        /**
                         * Guardamos la entrega
                         */
                        $this->_pointsRoute[] = array('start_date' => $deliveryExchange->format('Y-m-d H:i:s'),
                            'type' => 'Entrega Centro de Intercambio ',
                            'name' => $point->getName()
                            ,'routeId'=> $routeOrigin->getId()
                            ,'pointId'=> $point->getId()
                            ,'routePoint'=>(int)$routePoint['routePointId']
                            );
                        $this->_arrayEntrega[] = $point->getId();

                        $this->_routePointActivity[] = array(
                            'orderId'=>$this->_order ? $this->_order->getId() : null
                            ,'start_date'=>$deliveryExchange->format('Y-m-d H:i:s')
                            ,'routePointId'=>(int)$routePoint['routePointId']
                            ,'activityTypeId'=>2
                            );


                        /**
                         * Guardamos la recolección
                         */
                        $this->_routesFactor[] = array('route' => $route->getId(), 'factor' => $route->getFactor());
                        $this->_pointsRoute[] = array('start_date' => $scheduleAux->format('Y-m-d H:i:s')
                            , 'type' => 'Recolección Centro de Intercambio RA->' . $routeOrigin->getId() . ' RN->' . $route->getId() . ' '
                            , 'name' => $point->getName()
                            ,'routeId'=> $route->getId()
                            ,'pointId'=> $point->getId()
                            ,'routePoint'=> $pointInNewRoute->getId()
                            );
                        $this->_arrayRecoleccion[] = $point->getId();

                        $this->_routePointActivity[] = array(
                            'orderId'=>$this->_order ? $this->_order->getId() : null
                            ,'start_date'=>$scheduleAux->format('Y-m-d H:i:s')
                            ,'routePointId'=>$pointInNewRoute->getId()
                            ,'activityTypeId'=>1
                            );
//                        echo 'Entrega: ';
//                        echo $deliveryExchange->format('Y-m-d H:i:s') . '<br/>';
//                        echo '<hr/>';
//                        echo 'Nueva Ruta: ';
//                        echo $route->getId();
//                        echo '<br/>';
//                        echo 'Recoleccion: ';
//                        echo $scheduleAux->format('Y-m-d H:i:s') . '<br/>';
//                        echo '<hr/>';
//                        echo '<hr/>';
                        return $result;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @todo Realizar pruebas con puntos de intercambio eliminados rutas eliminadas 
     *       routepoints eliminados y puntos de ventas eliminados y schedules eliminados
     * Funcion recursiva que sirve para conocer la ruta por la cual se va a ir el camion
     * @param DefaultDb_Entities_Route $routeactual
     * @param DefaultDb_Entities_Route $routeBuyer
     * @param date $shippingDate
     * @param DefaultDb_Entities_RoutePoint:: $buyerRoutePoint
     * @return boolean
     */
    public function findRouteRecursive($routeactual, $routeBuyer, $shippingDate, $buyerRoutePoint, $dateInitRoute)
    {
//        var_dump($routeactual->getName());
        if ($this->_em !== null)
        {
            //var_dump($this->_routesRevised);
            if (in_array($routeactual->getId(), $this->_routesRevised)) //&& $routeactual->getId()!==$routeBuyer->getId())
            {
//                var_dump('ME REGRESO CON' . $routeactual->getName());
                return false;
            }
            else  //if($routeactual->getId()!=$routeBuyer->getId())
                array_push($this->_routesRevised, $routeactual->getId());
            if ($routeactual->getId() === $routeBuyer->getId())
            {
//                var_dump('GANAMOS');
                $this->_routesFactor[] = array('route' => $routeactual->getId(), 'factor' => $routeactual->getFactor());
                $orderInitialRoute = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getOrderInitialByRoute($routeactual);
                $timeInSeconds = $this->_em->getRepository('DefaultDb_Entities_Schedule')->calculeShippidTime($routeactual->getId(), $buyerRoutePoint->getOrder(), $orderInitialRoute->ordernumber);
                $timeInSeconds['timeInSeconds'] = isset($timeInSeconds['timeInSeconds']) ? $timeInSeconds['timeInSeconds'] : 0;
                $interval = new DateInterval('PT' . $timeInSeconds['timeInSeconds'] . 'S');
                $schedules = $this->_em->getRepository('DefaultDb_Entities_Schedule')->getNextSchedulesByRouteByDate($routeBuyer, $shippingDate);
                $schedules = $this->getNextDatesValid($schedules, $shippingDate);
                if (is_array($schedules))
                {
                    foreach ($schedules as $schedule)
                    {
                        $shippingDateRoute = new DateTime($schedule['start_date']);
                        $this->_dateInitRoute = new DateTime($schedule['start_date']);
                        $shippingDateRoute = $shippingDateRoute->add($interval);
                        $dateInitRoute = isset($this->_pointsRoute[0]['start_date']) ? new DateTime($this->_pointsRoute[0]['start_date']) : null;
                        //var_dump($dateInitRoute);
                        //var_dump($shippingDateRoute,$shippingDate);
                        //Todo: agregar caso para la misma ruta con puntos de intercambio repetidos
                        if ($shippingDateRoute >= $shippingDate && $dateInitRoute && $shippingDateRoute >= $dateInitRoute) //VERIFICAR PARA QUE HIZE EL DATEINITROUTE
                        {
                            return $shippingDateRoute;
                        }
                    }
                }
                return false;
            }
            else
            {
                var_dump('REVISANDO ' . $routeactual->getName());
                $points = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getPointExchangeByRoute($routeactual);
                var_dump('CENTROS DE INTERCAMBIS');
//                var_dump($points);
                foreach ($points as $point)
                {
                    var_dump('PUNTO', $point);
                    $point = $this->_em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $point['idpoint']));
                    $routes = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getRoutesByPoint($point);
                    var_dump('RUTAS', $routes);
                    foreach ($routes as $route)
                    {
                        if (((int) $route['route_id']) !== $routeactual->getId())
                        {
                            $routeNext = $this->_em->getRepository('DefaultDb_Entities_Route')->find($route['route_id']);
                            $orderInitialRoute = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getOrderInitialByRoute($routeactual);
                            $orderFinal = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findBy(array('point' => $point, 'route' => $routeactual), array('order' => 'DESC'), 1);
                            $routeNextResp = $routeNext;
                            //Realiza una busqueda de la ruta antes de avanzar a la siguiente ruta para verificar que exista por algun punto
                            //Intermedio
//                            var_dump('------------------------------',$routeactual->getName(),$routeNext->getName(),'------------------------------');
                            $routePointNext = $this->findRouteBefore($routeactual, $routeNext, $routeBuyer);
//                            var_dump($routePointNext);
                            if ($routePointNext === false)
                            {//Si no encuentra entonces que continue recursivamente y busque el siguiente punto
                                $routeNext = $routeNextResp;
                                $timeInSeconds = $this->_em->getRepository('DefaultDb_Entities_Schedule')->calculeShippidTime($routeactual->getId(), $orderFinal[0]->getOrder(), $orderInitialRoute->ordernumber);
                            }
                            else
                            {
                                $routeNext = $routePointNext->getRoute();
                                $orderInitialRoute = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->getOrderInitialByRoute($routeNext);
                                $orderFinal = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findBy(array('point' => $routePointNext->getPoint(), 'route' => $routeactual), array('order' => 'DESC'), 1);
                                $timeInSeconds = $this->_em->getRepository('DefaultDb_Entities_Schedule')->calculeShippidTime($routeactual->getId(), $orderFinal[0]->getOrder(), $orderInitialRoute->ordernumber);
                            }

                            $timeInSeconds['timeInSeconds'] = isset($timeInSeconds['timeInSeconds']) ? $timeInSeconds['timeInSeconds'] : 0;
                            $interval = new DateInterval('PT' . $timeInSeconds['timeInSeconds'] . 'S');
                            $newshippingDate = new DateTime($shippingDate->format('Y-m-d H:i:s'));
                            $newshippingDate->add($interval);
                            $oldnewshippingDate = new DateTime($newshippingDate->format('Y-m-d H:i:s'));
//                            var_dump('Ruta Actual '. $routeactual->getName(),'Ruta Siguiente ' . $routeNext->getName());
                            $schedules = $this->_em->getRepository('DefaultDb_Entities_Schedule')->getNextSchedulesByRouteByDate($routeNext, $shippingDate);
//                            var_dump(count($schedules));
                            $schedules = $this->getNextDatesValid($schedules, $shippingDate);
//                            var_dump(count($schedules));

                            echo '----------------INI---<br/>';
                            var_dump($schedules);
                            echo '---FIN----------------<br/>';

                            if (count($schedules) > 0)
                            {
//                                var_dump($orderFinal[0]->getPoint()->getName());
//                                if(! in_array($point->getId(), $this->_arrayEntrega) ) 
//                                {
//                                    $this->_pointsRoute[] = array('start_date'=>$newshippingDate->format('Y-m-d H:i:s'),
//                                                                'type'=>'Entrega Centro de Intercambio ',
//                                                                'name'=>$orderFinal[0]->getPoint()->getName());
//                                    $this->_arrayEntrega[] = $orderFinal[0]->getPoint()->getId();
//                                    if($this->_order)
//                                    {
//                                        $sequentialActivitiesDelivery = new DefaultDb_Entities_SequentialActivities;
//                                        //$routePointBuyer = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('point'=>$orderFinal[0]->getPoint()));
//                                        //debe ser la ruta en la que me encuentro
//                                        $sequentialActivitiesDelivery->setRoutePoint($orderFinal[0]);
//                                        $sequentialActivitiesDelivery->setShippingDate($newshippingDate);
//                                        $sequentialActivitiesDelivery->setType(DefaultDb_Entities_SequentialActivities::TYPE_DELIVERY);
//        //                                $sequentialActivitiesDelivery->setStatus(DefaultDb_Entities_SequentialActivities::STATUS_INACTIVE);
//                                        $sequentialActivitiesDelivery->setOrder($this->_order);
//                                        $sequentialActivitiesDelivery->setRouteDate(new DateTime($dateInitRoute));
//                                        $this->_em->persist($sequentialActivitiesDelivery);
//                                        $this->_em->flush();
//                                    }
//                                }
                                foreach ($schedules as $schedule)
                                {
//                                    var_dump('SCHEDULES',$schedules);
                                    $scheduleSelect = $this->_em->getRepository('DefaultDb_Entities_Schedule')->find($schedule['id']);
                                    $starDateRouteNext = $scheduleSelect->getStartDateRecurrent($newshippingDate);
                                    $respaldo = $dateInitRoute;
                                    $dateInitRoute = $schedule['start_date'];
                                    if ($newshippingDate <= $starDateRouteNext && $shippingDate <= $starDateRouteNext)
                                    {
                                        $result = $this->findRouteRecursive($routeNext, $routeBuyer, $starDateRouteNext, $buyerRoutePoint, $dateInitRoute);
                                        if ($result == false || $result == null)
                                        {
                                            $dateInitRoute = $respaldo;
                                            //No hace nada, solo regresamos la ultima fecha que tenemos guardada para que continue con esa
                                        }
                                        else
                                        {
                                            if (!in_array($point->getId(), $this->_arrayEntrega))
                                            {
                                                $this->_pointsRoute[] = array('start_date' => $oldnewshippingDate->format('Y-m-d H:i:s'),
                                                    'type' => 'Entrega Centro de Intercambio ',
                                                    'name' => $orderFinal[0]->getPoint()->getName());
                                                $this->_arrayEntrega[] = $orderFinal[0]->getPoint()->getId();
                                                if ($this->_order)
                                                {
                                                    $sequentialActivitiesDelivery = new DefaultDb_Entities_SequentialActivities;
                                                    //$routePointBuyer = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('point'=>$orderFinal[0]->getPoint()));
                                                    //debe ser la ruta en la que me encuentro
                                                    $sequentialActivitiesDelivery->setRoutePoint($orderFinal[0]);
                                                    $sequentialActivitiesDelivery->setShippingDate($oldnewshippingDate);
                                                    $sequentialActivitiesDelivery->setType(DefaultDb_Entities_SequentialActivities::TYPE_DELIVERY);
                                                    //                                $sequentialActivitiesDelivery->setStatus(DefaultDb_Entities_SequentialActivities::STATUS_INACTIVE);
                                                    $sequentialActivitiesDelivery->setOrder($this->_order);
                                                    $sequentialActivitiesDelivery->setRouteDate(new DateTime($dateInitRoute));
                                                    $this->_em->persist($sequentialActivitiesDelivery);
                                                    $this->_em->flush();
                                                }
                                            }
//                                            var_dump($routeactual->getName(),$routeactual->getId(),$this->_arrayRecoleccion,'------------------');
                                            if (!in_array($routeNext->getId(), $this->_arrayRecoleccion))
                                            {
                                                $dateInitRcolecctionShipp = new DateTime($dateInitRoute);
//                                                var_dump($orderFinal[0]->getId(),$orderFinal[0]->getOrder());
                                                $this->_routesFactor[] = array('route' => $routeNext->getId(), 'factor' => $routeactual->getFactor());
                                                $this->_pointsRoute[] = array('start_date' => $dateInitRcolecctionShipp->format('Y-m-d H:i:s')
                                                    , 'type' => 'Recolección Centro de Intercambio RA->' . $routeactual->getId() . ' RN->' . $routeNext->getId() . ' '
                                                    , 'name' => $orderFinal[0]->getPoint()->getName());
//                                                var_dump('RECOLECANTADO EN '.$point->getName());
                                                $this->_arrayRecoleccion[] = $orderFinal[0]->getPoint()->getId();
                                                if ($this->_order)
                                                {
                                                    $sequentialActivitiesDelivery = new DefaultDb_Entities_SequentialActivities;
                                                    $routePointBuyer = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('route' => $routeNext, 'point' => $orderFinal[0]->getPoint()));
                                                    $sequentialActivitiesDelivery->setRoutePoint($routePointBuyer);
                                                    $sequentialActivitiesDelivery->setShippingDate($dateInitRcolecctionShipp);
                                                    $sequentialActivitiesDelivery->setType(DefaultDb_Entities_SequentialActivities::TYPE_RECOLECTION);
                                                    //                                            $sequentialActivitiesDelivery->setStatus(DefaultDb_Entities_SequentialActivities::STATUS_INACTIVE);
                                                    $sequentialActivitiesDelivery->setOrder($this->_order);
                                                    $sequentialActivitiesDelivery->setRouteDate($dateInitRcolecctionShipp);
                                                    $this->_em->persist($sequentialActivitiesDelivery);
                                                    $this->_em->flush();
                                                }
                                            }
                                            return $result;
                                        }
                                    }
                                }
                            }
//                            else{
//                                var_dump('rompiendo recu');
//                            *  Este else suspende la recursivad y si se pone deja de funcionar alguinos casos
//                                return false;
//                            }
                        }
                    }
                }
            }
        }
    }

    function getPointsRoute()
    {
        usort($this->_pointsRoute, array($this, 'datecmp'));
        return $this->_pointsRoute;
    }

    function getRoutesFactor()
    {
        return $this->_routesFactor;
    }

    function getDateInitRoute()
    {
        return $this->_dateInitRoute;
    }

    function setDateInitRoute($date)
    {
        $this->_dateInitRoute = $date;
    }

    function setOrder($order)
    {
        $this->_order = $order;
    }

    function addPointRoute($dateString, $typeString, $name)
    {
        $this->_pointsRoute[] = array('start_date' => $dateString, 'type' => $typeString, 'name' => $name);
    }

    /**
     * Esta funcion realiza una busqueda de una ruta antes de continuar algun punto recursivo
     * @param type $routeActual
     * @param type $routeNext
     * @param type $routeBuyer
     * @return boolean
     */
    function findRouteBefore($routeActual, $routeNext, $routeBuyer)
    {
        if ($routeNext->getId() != $routeBuyer->getId())
        {
            $intesection = $this->_em->getRepository('DefaultDb_Entities_Schedule')->getIntersectionAndMaxRoutePointExchangeCenter($routeActual, $routeBuyer);
            $routesPoints = $this->_em->getRepository('DefaultDb_Entities_RoutePoint')->findBy(array('point' => $intesection['idpoint'], 'status' => DefaultDb_Entities_Point::STATUS_NORMAL));
            //Verificar por que se salta al siguiente punto sin que este ligado
            foreach ($routesPoints as $routepoint)
            {
                if ($routepoint->getRoute()->getId() === $routeBuyer->getId())
                {
                    return $routepoint;
                }
            }
        }
        return false;
    }

    public function firstPointBeforeSecondPoint($route, $pointOrigin, $pointDestiny)
    {
        $routePointRepo = $this->_em->getRepository('DefaultDb_Entities_RoutePoint');
//var_dump($route->getId());
//var_dump($pointOrigin->getId());
//var_dump($this->_routesRevised);
//var_dump($this->_pointsRevised);
//var_dump($this->_pointsRoute);
        if($pointOrigin instanceof DefaultDb_Entities_Point)
            $routePointOrigin = $routePointRepo->findOneBy(array('route' => $route, 'point' => $pointOrigin));
        else
            $routePointOrigin = $pointOrigin;

        if ($pointDestiny instanceof DefaultDb_Entities_Point)
        {
            $routePointDestiny = $routePointRepo->findOneBy(array('route' => $route, 'point' => $pointDestiny));
        }
        else
        {
            $routePointDestiny = $pointDestiny;
        }

        if ($routePointOrigin->getOrder() < $routePointDestiny->getOrder())
            return true;
        return false;
    }

    public function findNextSchedule($route, $deliveryExchange)
    {
        $result = null;
        $scheduleAdapter = $this->_em->getRepository('DefaultDb_Entities_Schedule');
        $result = $scheduleAdapter->getNextScheduledDate($route->getId(),$deliveryExchange);
        
        return $result;
    }

    function getNextDatesInRecurrent($schedule, $dateStart = null)
    {
        $result = null;
        $date = clone $schedule->getStartDate();
        $day = $date->format('N');

        while ($result == null)
        {
            $aux = 0;
            switch ($day)
            {
                case 1:
                    $aux = $schedule->getMonday();
                    break;
                case 2:
                    $aux = $schedule->getTuesday();
                    break;
                case 3:
                    $aux = $schedule->getWednesday();
                    break;
                case 4:
                    $aux = $schedule->getThursday();
                    break;
                case 5:
                    $aux = $schedule->getFriday();
                    break;
                case 6:
                    $aux = $schedule->getSaturday();
                    break;
                case 7:
                    $aux = $schedule->getSunday();
                    break;
            }

            if ($aux == 1)
            {
                if ($dateStart == null || $date >= $dateStart)
                {
                    $result = $date;
                    break;
                }
            }

            $day++;
            if ($day > 7)
                $day = 1;
            $date->add(new DateInterval('P1D'));
        }
        return $result;
    }

    public function getRoutesRevised(){
        return $this->_routesRevised;
    }

    public function getPointsRevised(){
        return $this->_pointsRevised;
    }

    public function getArrayEntrega(){
        return $this->_arrayEntrega;
    }

    public function getArrayRecoleccion(){
        return $this->_arrayRecoleccion;
    }

    public function setRoutePointActivity($orderId,$date,$routePointId,$activityTypeId){
        array_push($this->_routePointActivity, array(
            'orderId'=>$orderId
            ,'start_date'=>$date
            ,'routePointId'=>$routePointId
            ,'activityTypeId'=>$activityTypeId
        ));
    }

    public function getRoutePointActivity(){
        usort($this->_routePointActivity, array($this, 'datecmp'));
        return $this->_routePointActivity;
    }
}

?>
