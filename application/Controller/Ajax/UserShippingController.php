<?php
use com\masfletes\db\DBUtil;

class Ajax_UserShippingController extends Model3_Controller
{
    private $dateInitRoute=null;
    private $order;
    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function calculateShippingPerRouteAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $em = $this->getEntityManager('DefaultDb');
        $scheduleAdapter = $em->getRepository('DefaultDb_Entities_Schedule');
        $routePointsAdapter = $em->getRepository('DefaultDb_Entities_RoutePoint');
        $user = $em->find('DefaultDb_Entities_User', $credentials['id']);
        $userRepos = $em->getRepository('DefaultDb_Entities_User');
        $response = new stdClass();
        $response->res = false;
        $response->factors=0;
        $response->costingConfig =  new stdClass();
        $response->costingConfig->hasDeliveryDate=true;
        $response->message = 'No se ha podido calcular la hora de recolecci&oacuten';
        $dias = array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        
        if($this->getRequest()->isPost() && $user instanceof DefaultDb_Entities_User)
        {
            $post = $this->getRequest()->getPost();
            $idSchedule = $post['idRoute'];//? es idschedule
            $orderId = isset($post['orderId']) ? (int)$post['orderId'] : null;
            $selectedScheduledDate = isset($post['selectedScheduledDate']) ? DateTime::createFromFormat('Y-m-d H:i:s',$post['selectedScheduledDate']) : null;
            if($selectedScheduledDate) $selectedScheduledDate->setTime ( 0, 0, 0 );
            if($orderId)
            {
                $this->order = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($orderId);
                if($user->getId()!==$this->order->getSeller()->getId())
                {
                    $user = $this->order->getSeller(); //Cambiamos el usuario ya que el que hace el pedido es el comprador
                }
            }
            else
            {
                $this->order = null;
                $idUser = $post['idSeller'];
                $user = $userRepos->find($idUser);
            }
            
            $scheduleSelect = $scheduleAdapter->find($idSchedule);
            $scheduleHelper = new Helper_Schedule($em);
            
            if($scheduleSelect instanceof  DefaultDb_Entities_Schedule)
            {
                $route = $scheduleSelect->getRoute();
                $point = $em->find('DefaultDb_Entities_BranchesUser', $post['pointSeller']);
                $point = $point->getPoint();
                $MyRoutePoint = $routePointsAdapter->findOneBy(array('route'=>$route,'point'=>$point));
                if($MyRoutePoint instanceof DefaultDb_Entities_RoutePoint )
                {
                    $shippingDate = $scheduleHelper->findNextSchedule($scheduleSelect->getRoute(), $selectedScheduledDate);
                    $this->dateInitRoute = new DateTime($shippingDate->format('Y-m-d H:i:s'));
                    $newShippingDate = new DateTime($shippingDate->format('Y-m-d H:i:s'));                    
                    $scheduleHelper->setOrder($this->order);
                    $scheduleHelper->setDateInitRoute($this->dateInitRoute);
                    $shippingDate = $scheduleHelper->calculateRecolection($shippingDate,$route,$MyRoutePoint);

                    $response->res = true;
                    $response->message = '<span style="color: #000; font-size:1.5em;">La fecha estimada de recolecci&oacute;n es el d&iacute;a: </span>'
                    				.'<span style="font-size:1.5em;">'.$dias[intval($shippingDate->format('w'))]
   									." ".$shippingDate->format('d')." de ".$meses[intval($shippingDate->format('m')-1)]
                    				." a las ".$shippingDate->format('H:i')."</span><br />";
                    $response->date = $shippingDate->format('Y-m-d H:i:s');
                    
                    /*BUYER*/
                    //buscar la ruta y si existe la eliminamos :D
                    $entities=$em->getRepository('DefaultDb_Entities_SequentialActivities')->findBy(array('order'=>$this->order));
                    foreach ($entities as $entity)
                        $em->remove($entity);
                    $em->flush();
                    
                    //Calculo de tiempos para el comprador
                    $idBuyer = (isset($post['buyerId'])? $post['buyerId']: $credentials['id']);
                    $buyer = $em->find('DefaultDb_Entities_User', $idBuyer);
                    $branches= $em->find('DefaultDb_Entities_BranchesUser',$post['pointBuyer']);
                    $buyerPoint = $branches->getPoint();
                    
                    $scheduleHelper->addPointRoute($response->date,'Recolección ',$MyRoutePoint->getPoint()->getName()); //Punto inicial
                    $scheduleHelper->setRoutePointActivity($orderId,$response->date,$MyRoutePoint->getId(),1);

                    $routePointBuyer = $routePointsAdapter->findBy(array('point'=>$buyerPoint, 'route'=>$route));
                    if($routePointBuyer) {
                        $routePointBuyer = $routePointBuyer[count($routePointBuyer)-1];
                    }
                    else {
                        $routePointBuyer = $routePointsAdapter->findOneBy(array('point'=>$buyerPoint));
                    }
                    $routeBuyer = $routePointBuyer->getRoute();
                    $shippingDateDelivery = $scheduleHelper->calculateDelivery($buyer,$shippingDate,$route, $routeBuyer, $MyRoutePoint, $routePointBuyer);

                    $this->dateInitRoute = $scheduleHelper->getDateInitRoute();
                    if( !$shippingDateDelivery )
                    {
                        $response->message .= '<br/>No se ha podido calcular la hora de entrega';
                        $response->res = false;
                        $response->costingConfig->hasDeliveryDate = false;
                    }
                    else
                    {//BUYER
                        
                    	$fechaActual = date('d-m-Y H:i:s');
  						$fecha1 = new DateTime($fechaActual);
  						$diff = $fecha1->diff($shippingDateDelivery);
                    	
                    	$response->message .= '<br/><span style="color: #000; font-size:1.5em;"> La fecha estimada de entrega es el d&iacute;a: </span>'
                        		.'<span style="font-size:1.5em;">'.$dias[intval($shippingDateDelivery->format('w'))]
                        		." ".$shippingDateDelivery->format('d')." de ".$meses[intval($shippingDateDelivery->format('m')-1)]
                        		." a las ".$shippingDateDelivery->format('H:i').'</span>';
                        $response->dateDelivery = $shippingDateDelivery->format('Y-m-d H:i:s');
                        //$response->message .= '<br/><p style="font-weight:bold; border-bottom:1px dotted #ccc">Puntos Intermedios:</p>';
                        $response->message .= '<br/><p style="font-weight:bold; border-bottom:1px dotted #ccc"></p><br >';
  						$response->message .= '<span style="color: #3a87ad; font-size:1.3em;">Quedan '.$diff->days.' D&iacute;as para la entrega de su paquete</span><br />';
  						//$response->message .= '<span style="color: #3a87ad; font-size:1.3em;">Quedan '.$diff->days.' D&iacute;as con '.$diff->h.' Horas y '.$diff->i.' Minutos para la entrega de su paquete</span><br />';
                        //Punto final
                        $scheduleHelper->addPointRoute($shippingDateDelivery->format('Y-m-d H:i:s'),'Entrega ', $buyerPoint->getName()); //Punto inicial
                        $scheduleHelper->setRoutePointActivity($orderId,$shippingDateDelivery->format('Y-m-d H:i:s'),$routePointBuyer->getId(),2);

                        $pointsRoutesH = $scheduleHelper->getPointsRoute();

                        //foreach($pointsRoutesH as $pointRoute)
                        //    $response->message .= '<br/>['. $pointRoute['start_date'].'] '.$pointRoute['type'].$pointRoute['name'];
                        $response->factors=$scheduleHelper->getRoutesFactor();

                        $routeDate = $this->dateInitRoute;                        
                        $arrRoutePointActivity = $scheduleHelper->getRoutePointActivity();
                        $currentOrder = $this->order;
                        foreach ($arrRoutePointActivity as $key => $activity) {
                            $arrRoutePoints[] = $activity['routePointId'];
                        }
                        
                        $dql = "SELECT partial rp.{id} from DefaultDb_Entities_RoutePoint rp where rp in (:routePoints)";
                        $query=$em->createQuery($dql);
                        $query->setParameter('routePoints',$arrRoutePoints);
                        $routePoints = $query->getResult();
                        
                        $em->transactional(function($em) use ($arrRoutePointActivity, $routePoints, $routeDate,$currentOrder){
                            foreach ($arrRoutePointActivity as $key => $activity) {                                
                                $sequentialActivity = new DefaultDb_Entities_SequentialActivities;

                                $routePointId = $activity['routePointId'];
                                $routePoint = null; 
                                foreach ($routePoints as $rp) {
                                    if($rp->getId()==$routePointId){
                                        $routePoint = $rp;
                                    }
                                }

                                $sequentialActivity->setRoutePoint($routePoint);
                                $sequentialActivity->setShippingDate(DateTime::createFromFormat('Y-m-d H:i:s', $activity['start_date']));
                                $sequentialActivity->setType($activity['activityTypeId']);
                                $sequentialActivity->setOrder($currentOrder);
                                $sequentialActivity->setRouteDate($routeDate);

                                $em->persist($sequentialActivity);
                            }
                        });

                        // ==============================================================
                        // Obtenter tablas de tarifa para el costeo
                        $obConf = $em->find('DefaultDb_Entities_Configuration',1 /*Configuración 1*/);
                        $response->costingConfig->basePackageSize = $obConf->getBasePackageSize();
                        $response->costingConfig->powerFactor = $obConf->getPowerFactor();

                        // Obtener resumen de tarifas por actividad, PV                       
                        $routePointActvityRepo =  $em->getRepository('DefaultDb_Entities_RoutePointActivity');                        
                        $ratesConfig = $routePointActvityRepo->getSumRatesByRoutePoint($currentOrder->getId());
                        $response->costingConfig->hasFullRatesCaptured = $ratesConfig["hasFullRatesCaptured"];
                        $response->costingConfig->totalAmount = $ratesConfig["totalAmount"];
                        $response->costingConfig->totalRoutePoint = $ratesConfig["totalRoutePoint"];
                        $response->costingConfig->promotionCosting = $obConf->getPromotionCost();

                        // Preparación de detalle de actividades secuenciales                        
                        $tableRate =  $routePointActvityRepo->getRoutePointRatesByOrder($currentOrder->getId());
                        $response->tableRateActivities = $tableRate;
                        // ==============================================================
                        
                        if($this->verifyCapacity($tableRate,$em,(int)$post['tvol'],(int)$post['tweight'])==FALSE){//CON ESTA FUNCION VERIFICAMOS VOLUMEN Y CAPACIDAD DE CARGA DE LAS UNIDADES QUE TRASLADARAN EL PAQUETE 
                        	$response->message .= '<br/><div style="font-size:1.5em; color: #FF4500;">Atencion!!!, La unidad para la fecha selecionada esta llena, por favor seleccione otra fecha</div>';
                        	$response->res = false;
                        	$response->costingConfig->hasDeliveryDate = false;
                        }
                    }
                }
            }
        }
        $this->view->response = json_encode($response);
    }
    
    public function verifyCapacity($tableRate,$em,$tvol,$tweight){
    	
    	$result = TRUE;
    	$routes = array();
    	$points = array();
    	
    	foreach ($tableRate as $item){
    		if($item["element_type"] == 1){
    			$routes[] = array("idr" => $item["routeId"]);//SE GURADAN LOS ID DE LAS RUTAS POR LAS QUE PASARA EL PAQUETE
    		}else{
    			$points[] = array("idrp" => $item["pv"], "idp" => $item["element_id"], "idr" => $item["routeId"], "shipDate" => $item["shippingDate"]);//SE GUARDAN LOS DATOS ESPECIFICOS DE CADA PUNTO POR DONDE PASA EL PAQUETE
    		}
    	}

    	$sch = array();
    	foreach ($routes as $r){
    		$i = 0;
    		foreach ($points as $p){
    			if($p["idr"] == $r["idr"]){
    				$mod = $i%2;
	    			if($mod == 0){//SOLO SE BUSCA OBTENER LA INFORMACION DEL PRIMER PUNTO PARA CADA RUTA POR LA QUE PASA EL PAQUETE
	    				$SchRid = $em->getRepository('DefaultDb_Entities_ScheduledRoute')->findOneBy(array('route' => $r["idr"], 'scheduledDate' => new DateTime($p["shipDate"])));
	    				//$esq .="esquedula:".$SchRid->getId(). "<br />";
	    				$Vehicle = $em->getRepository('DefaultDb_Entities_Vehicle')->findOneBy(array('id' => $SchRid->getVehicle()->getId()));
	    				$VeV = $Vehicle->getVolume();
	    				$VeC = $Vehicle->getCapacity();
	    				$sch[] = array("scid" => $SchRid->getId(), "idr" => $r["idr"], "VehiV" => $VeV, "VehiC" => $VeC);
	    			}
	    			$i++;
	    		} 
    		}
    	}
    	
    	$arr0 = array();
    	$arr1 = array();
    	$RPonit = $em->getRepository('DefaultDb_Entities_RoutePoint');
    	$RPActv = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
    	$PtOrd = $em->getRepository('DefaultDb_Entities_PackageToOrder');
    	 
    	foreach ($sch as $s){
    		$inx = 0;
    		foreach ($tableRate as $item){
    			if($item["element_type"] == 2 && $item["routeId"] == $s["idr"]){
    				//$Order = $RPonit->findOneBy(array('id' => $item["pv"]));///OBTENER LA POSICION EN LA RUTA DEL PUNTO
    				//$fOrder = (int)$Order->getOrder();///OBTENER LA POSICION DEL PUNTO DE ENTREGA DEL PAQUETE
    				$fOrder = $RPActv->getOrderPoints($item["pv"]);//se usa esta consulta directa ya que el metodo de arriba presenta problemas particularmente cuando se busca la posicion del route_point con id 2151
    				$arr1[$inx] = array("idrp" => $item["pv"], "idp" => $item["element_id"], "order" => (int)$fOrder[0]['order_number'], "rpschid" =>$s["scid"]);
    				$inx++;
    			}    			
    		}
    		$arr0[] = array("scid" => $s["scid"])+$arr1+array("idr" => $s["idr"])+array("VehiV" => $s["VehiV"])+array("VehiC" => $s["VehiC"]);
    	}
    	
    	$i=0;
    	foreach ($arr0 as $con){
    		
    		$RPActvl = $RPActv->findBy(array('scheduledRoute' => $con['scid']));//SE CONSULTA LA INFORMACION DE TODAS LAS ACTIVIDADES EN LA RUTA EL DIA ESPECIFICADO
    		$arrpoints = array();
    		foreach ($RPActvl as $item){
    			if($item->getActivityType() != NULL){
    				$arrpoints[] = array('id' => $item->getRoutePoint()->getId(), 'acType' => $item->getActivityType()->getId(), 'orId' => $item->getTransaction()->getTransactionId());
    			}
    		}

    		$i++;
    		if($i == 1){//SI ES LA PRIMER RUTA POR DONDE PASA EL PAQUETE SE VERIFICA QUE EXISTA ESPACIO EN LA UNIDAD AL LLEGAR AL PUNTO DE RECOLECCION
    			$Routeseg = $RPonit->createQueryBuilder('f')///OBTIENE LOS PUNTOS ENTRE INICIO DE LA RUTA Y RECOLECCION DEL PAQUETE
    			->where("f.route = :rid AND f.order BETWEEN :colpoint AND :delpoint")
    			->orderBy('f.order', 'ASC')
    			->setParameter('rid',$con["idr"])
    			->setParameter('colpoint',1)
    			->setParameter('delpoint',$con[0]["order"])
    			->getQuery();
    			
    			$vol = 0;
    			$weight = 0;
    			foreach ($arrpoints as $arr){
    				foreach ($Routeseg->getResult() as $Rs){
						if($arr['acType'] == 1 || $arr['acType'] == 2){//EL TIPO DE ACTIVADAD 1 Y 2 INDICAN QUE SE TRATA DE UN PAQUETE CON PESO Y VOLUMEN
	    					if($arr['id'] == $Rs->getId()){	
	    						$zd = $PtOrd->findOneBy(array('order' => $arr["orId"]));	
	    						$volp = $zd->getWidth()*$zd->getHeight()*$zd->getDepth();	
	    						if($arr['acType'] == 1){	
	    							$vol = $vol + $volp;	
	    							$weight = $weight + $zd->getWeight();	
	    						}else{	
	    							$vol = $vol - $volp;	
	    							$weight = $weight - $zd->getWeight();	
	    						}	
	    					}
						}
    				}
    			}
    			
    			$vol = $vol + $tvol;
    			$weight = $weight + $tweight;
    		 	if($vol > ($con["VehiV"] - ($con["VehiV"]*.3))){//SI SE CUMPLE EL VOLUMEN PERMITIDO PARA LA UNIDAD ESTA REBASADO
	    			$result = FALSE;
	    			break;
	    		}
	    		if($weight > ($con["VehiC"] - ($con["VehiC"]*.3))){//SI SE CUMPLE LA CAPACIDAD DE CARGA PERMITIDA PARA LA UNIDAD ESTA REBASADO
	    			$result = FALSE;
	    			break;
	    		}
    		}
    		
    		if(count($con)  == 8 && $i == 2){//SI SE CUMPLE QUIERE DECIR QUE EL PAQUETE HACE UN RECORRIDO POR LA MISMA RUTA PERO EN DOS FECHAS DIFERENTES
    			$con0 = $con[2]["order"];
    			$con1 = $con[3]["order"];
    		}else{
    			$con0 = $con[0]["order"];
    			$con1 = $con[1]["order"];
    		}

    		$Routeseg = $RPonit->createQueryBuilder('f')///OBTIENE LOS PUNTOS ENTRE RECOLECCION Y ENTREGA DEL PAQUETE PARA CADA RUTA EN LA FECHA POR LA QUE PASA EL PAQUETE
    		->where("f.route = :rid AND f.order BETWEEN :colpoint AND :delpoint")
    		->orderBy('f.order', 'ASC')
    		->setParameter('rid',$con["idr"])
    		//->setParameter('colpoint',$con[0]["order"])
    		//->setParameter('delpoint',$con[1]["order"]-1)
    		->setParameter('colpoint',$con0)
    		->setParameter('delpoint',$con1-1)
    		->getQuery();
    		
    		$vol = 0;
    		$weight = 0;
    		foreach ($arrpoints as $arr){
    			foreach ($Routeseg->getResult() as $Rs){    				if($arr['acType'] == 1 || $arr['acType'] == 2){//EL TIPO DE ACTIVADAD 1 Y 2 INDICAN QUE SE TRATA DE UN PAQUETE CON PESO Y VOLUMEN
	    				if($arr['id'] == $Rs->getId()){	
	    					$zd = $PtOrd->findOneBy(array('order' => $arr["orId"]));	
	    					$volp = $zd->getWidth()*$zd->getHeight()*$zd->getDepth();	
	    					if($arr['acType'] == 1){	
	    						$vol = $vol + $volp;	
	    						$weight = $weight + $zd->getWeight();	
	    					}else{	
	    						$vol = $vol - $volp;	
	    						$weight = $weight - $zd->getWeight();	
	    					}	
	    				}    				}
    			}
    		}
    		
    		$vol = $vol + $tvol;
    		$weight = $weight + $tweight;
    		if($vol > ($con["VehiV"] - ($con["VehiV"]*.3))){//SI SE CUMPLE EL VOLUMEN PERMITIDO PARA LA UNIDAD ESTA REBASADO
    			$result = FALSE;
    			break;
    		}
	    	if($weight > ($con["VehiC"] - ($con["VehiC"]*.3))){//SI SE CUMPLE LA CAPACIDAD DE CARGA PERMITIDA PARA LA UNIDAD ESTA REBASADO
    			$result = FALSE;
    			break;
    		}
    	}//NOTA: LA CAPACIDAD DE LA UNIDAD EN TANTO AL VOLUMEN Y PESO CONTEMPLA TENER LIBRE SIEMPRE UN 30%

    	if($result == FALSE){
    		//buscar la ruta y si existe la eliminamos :D
    		$entities=$em->getRepository('DefaultDb_Entities_SequentialActivities')->findBy(array('order'=>$this->order));
    		 foreach ($entities as $entity)
    			$em->remove($entity);
    		$em->flush();
    	}
    	return $result;
    }
    
    public function getSchedulesByOriginAction()
    {
        $em =  $this->getEntityManager('DefaultDb');
        $scheduleAdapter =  $em->getRepository('DefaultDb_Entities_Schedule');
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            if(isset($post['originPoint']))
            {
                $branchUser = $em->find('DefaultDb_Entities_BranchesUser', $post['originPoint']);
                if($branchUser)
                {
                    if($branchUser->getPoint())
                    {                        
                        $helper = new Helper_Schedule($em);
                        $this->view->schedules = $scheduleAdapter->getScheduledDates($branchUser->getClient()->getId(),$branchUser->getPoint()->getId(),new DateTime());
                        //var_dump($this->view->schedules);
                    }
                }
            }
        }
    }

    public function calculatePromotionDeliveryAction(){
        $result=array('dates'=>array(),'routepointid'=>null);
        if($this->getRequest()->getPostJson()!=null)
        {
            $credentials = Model3_Auth::getCredentials();
            $post = $this->getRequest()->getPostJson();            
            $em = $this->getEntityManager('DefaultDb');
            $scheduleHelper = new Helper_Schedule($em);
            $scheduleAdapter =  $em->getRepository('DefaultDb_Entities_Schedule');
            $branchid = $post["branchid"];
            $isRegisteredClient = ($post["isRegistered"]=="true");

            if(!$branchid) {$this->view->response=$result; return;}
            $branch = $em->find('DefaultDb_Entities_BranchesUser',($isRegisteredClient ? $branchid : -1));
            if($isRegisteredClient && (!$branch || !$branch->getPoint())) {$this->view->response=$result; return;}
            
            //Obtener el punto ligado a una ruta a partir del punto indicado en la sucursal
            $dql = "SELECT rp FROM DefaultDb_Entities_RoutePoint rp WHERE rp.point=:pointid AND rp.status=1";
            $query = $em->createQuery($dql);

            $query->setParameter("pointid", $isRegisteredClient ? $branch->getPoint()->getId() : $branchid);
            $routePointCollection = $query->setMaxResults(1)->setFirstResult(0)->getResult();
            if(!$routePointCollection) {$this->view->response=$result; return;}
            $routePointFromBranch = $routePointCollection[0];

            //Obtener el 1er punto de la ruta obtenida
            $dql = "SELECT partial rp.{id} FROM DefaultDb_Entities_RoutePoint rp WHERE rp.route=:routeid AND rp.status=1 ORDER BY rp.order ASC";
            $query = $em->createQuery($dql);
            $query->setParameter("routeid", $routePointFromBranch->getRoute()->getId());
            $routePointCollection = $query->setMaxResults(1)->setFirstResult(0)->getResult();
            if(!$routePointCollection) {$this->view->response=$result; return;}
            $firstRoutePoint = $routePointCollection[0];
                       
            $buyer = null;
            $routeSeller = $routePointFromBranch->getRoute();
            $routeBuyer = $routeSeller;
            $routePointBuyer = $routePointFromBranch;
            $pointSeller = $firstRoutePoint->getPoint();

            $schedules = $scheduleAdapter->getScheduledDates($isRegisteredClient ? $branch->getClient()->getId() : null,$isRegisteredClient ? $branch->getPoint()->getId() : $branchid,new DateTime());
            $dates = array();
            foreach ($schedules as $key => $schedule) {
                $startDate = $schedule['start_date'];
                $shippingDate = DateTime::createFromFormat('Y-m-d H:i:s', $startDate);
                $shippingDateDelivery = $scheduleHelper->calculateDelivery($buyer,$shippingDate,$routeSeller, $routeBuyer, $pointSeller, $routePointBuyer);
                if($shippingDateDelivery)
                    $dates[]=$shippingDateDelivery->format('Y-m-d H:i:s');
                else
                    continue;
            }

            $result = array('dates' => $dates, 'routepointid'=>$routePointFromBranch->getId());
                       
        }
        $this->view->response=$result;
    }

    public function savePromotionScheduleAction(){
        $result=array('success'=>false,'message'=>'Ocurrió un error al generar la promoción. Favor de contactar al Administrador del Sistema.');
        
        if($this->getRequest()->getPostJson()!=null)
        {
            $post = $this->getRequest()->getPostJson();
            $promotionid = intval($post['promotionid']);
            $userid = intval($post['userid']);
            $clientid =intval($post['clientid']);
            $routepointid = intval($post['routepointid']);
            $promotionDate = $post['promotionDate'];
            $totalCost = floatval($post['totalCost']);
            $creationDate = new DateTime();

            $shippingDate = DateTime::createFromFormat('Y-m-d H:i:s', $promotionDate);
            $em = $this->getEntityManager('DefaultDb');

            $em->getConnection()->beginTransaction();
            try {                
                $routePointActivitiRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');            
                $routePoint = $em->find('DefaultDb_Entities_RoutePoint',$routepointid);
                $promotion = $em->find('DefaultDb_Entities_Promotion',$promotionid);
                $user = $em->find('DefaultDb_Entities_User',$userid);
                $client = $em->find('DefaultDb_Entities_User',$clientid);

                //Generar el registro de la programación.
                $promotionSchedule = new DefaultDb_Entities_PromotionSchedule();
                $promotionSchedule->setPromotion($promotion);
                $promotionSchedule->setCreationDate($creationDate);
                $promotionSchedule->setPromotionDate($shippingDate);
                $promotionSchedule->setUser($user);
                $promotionSchedule->setClient($client);
                $promotionSchedule->setPoint($routePoint->getPoint());
                $promotionSchedule->setPackageOrder(null);
                $em->persist($promotionSchedule);
                //Se guarda la promoción debido a que la actividad por punto requiere el id de la transacción.
                $em->flush();

                //Se crea la actividad en punto.
                $routepointActivity[] = array( "routePointId"=>$routepointid
                                            ,"shippingDate"=>$shippingDate
                                            ,"routeId"=>$routePoint->getRoute()->getId()
                                            ,"orderId"=>$promotionSchedule->getId()
                                            ,"activityType"=>6
                                            );
                
                $arrRp = $routePointActivitiRepo->createRoutePointActivites($routepointActivity,2);

                // ******************************************** Generar pago ********************************************
                $this->payPromotion($em,$promotionSchedule,$totalCost);

                if(count($arrRp)==1){
                    $promotionSchedule->setActivityPoint($arrRp[0]);
                    $em->flush();

                    $em->getConnection()->commit();

                    $result["success"]=true;
                    $result["message"]='Programación creada con éxito';
                }else{
                    $result["success"]=false;
                    $result["message"]='Error al crear la programación.';
                }
            }catch(Exception $ex){
                $em->getConnection()->rollback();
                $errMsg = $ex->getMessage();        
                $conn = $this->getConnection();
                $requestUrl = $this->getRequest()->getRequestUri(); 
                $params = array('sessionId'=>$this->getUserSessionId()) + $post;
                $errorCode = null;
                if($ex->getCode()==5000){
                    $errorCode = $ex->getCode();
                } else if ($ex->getCode() == 1457) {
                    $errorCode = $ex->getCode();
                }elseif (property_exists($ex, "errorInfo")) {
                    $errorCode = $ex->errorInfo[1];
                }
                DBUtil::addLog($conn,$requestUrl, $params,"code:".$errorCode."  msg:".$errMsg);
            }
        }
        $this->view->response=$result;
    }

    function payPromotion($em,$promotionSchedule,$totalCost){        
        $ESTATUS_PENDIENTE = 2;
        $CONGELAR_CREDITOS = 1;
        $NEGAR_CREDITOS = 2;
        $TIPO_CONCEPTO_COMPRA_PROMOCION = 7;
        $MONEDA_DEFAULT="MXN";

        $user = $promotionSchedule->getUser();
        $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($TIPO_CONCEPTO_COMPRA_PROMOCION);
        $esSaldoNegativo = ($totalCost > $user->getCredito());
        $tipoDebitar = $esSaldoNegativo ? $NEGAR_CREDITOS : $CONGELAR_CREDITOS;
        $tipoDebito = $em->getRepository('DefaultDb_Entities_TipoDebito')->find($tipoDebitar);
        $userMoneda = $user->getMoneda(); 
        $monedaMXN = $em->getRepository('DefaultDb_Entities_TipoMonedas')->findBy(array("currencyCode"=>$MONEDA_DEFAULT));
        $moneda = $userMoneda ? $userMoneda : ($monedaMXN ? $monedaMXN[0] : null);
        $totalMoneda = $pagosRepo->fncCalculaCreditos($moneda->getId(), $totalCost);

        $pagosJSON = array();
        $pagosJSON["usuario"] = $promotionSchedule->getUser(); 
        $pagosJSON["cliente"] = null; //No hay cliente ya que se está comprando una promoción
        $pagosJSON["orden"] = $promotionSchedule->getId();
        $pagosJSON["montoCompra"] = $totalMoneda;
        $pagosJSON["montoCreditos"] =$totalCost;
        $pagosJSON["fecha"] = new DateTime( );
        $pagosJSON["estatus"] = $ESTATUS_PENDIENTE;
        $pagosJSON["compraCreditos"] = null;
        $pagosJSON["compraVenta"] = null;
        $pagosJSON["promocion"] = $promotionSchedule;
        $pagosJSON["tipoConcepto"] = $tipoConcepto;
        $pagosJSON["tipoDebito"] = $tipoDebito;
        $pagosJSON["descripcion"] = "Compra de promoción";
        
        $pago = $pagosRepo->fncAgregarPagoPromocion($pagosJSON);
    }
}
?>
