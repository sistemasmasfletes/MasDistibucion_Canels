<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ScheduledRouteRepository extends EntityRepository
{    
    public function getScheduledRoute($page,$rowsPerPage,$sortField,$sortDir,$groupedByRoute,$scheduleId,$startDate,$endDate,$routeName,$vehicleName,$driverName, $userId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        if($groupedByRoute){
            $sqlSelect = "  DISTINCT sr.route_id id
                            ,CONCAT('[',r.code,'] ',r.name) routeName";
        }else{
                $sqlSelect = "  sr.*
                                ,CONCAT('[',r.code,'] ',r.name) routeName
                                ,v.name vehicleName
                                ,CONCAT(u.first_name,' ',u.last_name) driverName
                                ,sc.recurrent 
                ";
        }
        
        $sDate = new Datetime('now');
        $currentWeekDay=(int)$sDate->Format('w');
        $sDate->modify("-".$currentWeekDay." days");
        $eDate = clone $sDate;
        $eDate->modify("+90 days");///////CANTIDAD DE DIAS PARA MOSTRAR SCHEDULED ROUTE
        

        $query="
            SELECT [FIELDS]
            FROM scheduled_route sr
                LEFT JOIN vehicles v on sr.vehicle_id = v.id
                LEFT JOIN users u on sr.driver_id = u.id
                LEFT JOIN routes r on sr.route_id = r.id
                LEFT JOIN schedule sc on sr.schedule_id = sc.id
            WHERE
            r.controller_id=$userId
            AND (:scheduleId IS NULL OR sr.route_id = :scheduleId)
            AND (:routeName IS NULL OR concat(r.code,r.name) LIKE :routeName)
            AND (:vehicleName IS NULL OR v.name LIKE :vehicleName)
            AND (:driverName IS NULL OR CONCAT(u.first_name,u.last_name) LIKE :driverName)
            AND (  
                    (:startDate IS NOT NULL OR  :endDate IS NOT NULL OR DATE(sr.scheduled_date) BETWEEN :initialDate and :finalDate) 
                AND
                    (:startDate IS NOT NULL OR :endDate IS NULL OR DATE(sr.scheduled_date)=:endDate)
                AND
                    (:startDate IS NULL OR :endDate IS NOT NULL OR DATE(sr.scheduled_date)=:startDate)
                AND 
                    (:startDate IS NULL OR :endDate IS NULL OR DATE(sr.scheduled_date) BETWEEN :startDate and :endDate)                
                    
                ) 
            [ORDERBY]
            [LIMT]
        ";
        
        if($groupedByRoute){
            $queryCount = str_replace('[FIELDS]', $sqlSelect, $query);
            $queryCount ="SELECT COUNT(*) totalRecords FROM ( ".$queryCount." ) selectCount";
        }else{
            $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        }
        //echo $queryCount;
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        
        $stmt->bindValue(":initialDate","2020-01-01 00:00:00" /*$sDate->format('Y-m-d 00:00:00'*/);
        $stmt->bindValue(":finalDate",$eDate->format('Y-m-d 00:00:00'));        
        $stmt->bindValue(":scheduleId",$scheduleId);
        $stmt->bindValue(":startDate",$startDate);
        $stmt->bindValue(":endDate",$endDate);
        $stmt->bindValue(':routeName',($routeName==null) ? null : '%'.$routeName.'%');        
        $stmt->bindValue(':vehicleName',($vehicleName==null) ? null : '%'.$vehicleName.'%');
        $stmt->bindValue(':driverName',($driverName==null) ? null : '%'.$driverName.'%');
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
         
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function getScheduledRouteActivityDetail($page,$rowsPerPage,$sortField,$sortDir,$scheduledRouteId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = "  rp.*,TIME_TO_SEC(rp.arrival_time) arrtime, p.name pointName
                        ,CONCAT('[',r.code,'] ',r.name) routeName
                        ,sr.scheduled_date
                        ,CASE WHEN rpa.routePoint_id IS NULL THEN 0 ElSE 1 END hasActivity
        				,r.id as rid
        				,sr.id as srid
        				,rp.order_number as position
        				,rp.id as rpid
        				,TIME_TO_SEC(p.activitytime) ptime
                        ";   
 
        $query="
            SELECT [FIELDS]
            FROM scheduled_route sr
                LEFT JOIN routes r on sr.route_id = r.id
                LEFT JOIN route_points rp ON sr.route_id = rp.route_id
                LEFT JOIN points p ON rp.point_id=p.id
                LEFT JOIN (
                    SELECT DISTINCT rpa.routePoint_id 
                    FROM routepoint_activity rpa
                    WHERE rpa.scheduledRoute_id = :scheduledRouteId
                ) rpa on rp.id = rpa.routePoint_id
            WHERE
                sr.id = :scheduledRouteId
            AND rp.status = 1
            AND rpa.routePoint_id IS NOT NULL
            [ORDERBY]
            [LIMT]
        ";
        
        $additionalQuery = " 
            SELECT sr.scheduled_date
                ,CONCAT('[',r.code,'] ',r.name) routeName
        		,v.name vname, v.volume volume, v.capacity	capacity
                FROM scheduled_route sr 
        		LEFT JOIN routes r on sr.route_id = r.id
        		LEFT JOIN vehicles v on sr.vehicle_id = v.id 
            WHERE sr.id = :scheduledRouteId
        ";
       
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
       
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount); 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]'," ORDER BY rp.order_number ", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect
                ."; ".$queryCount
                ."; ".$additionalQuery;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
                   
        $stmt->bindValue(":scheduledRouteId",$scheduledRouteId);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        $rows = $result[0];
        $resultRows = array();

        
        /****************************************CALCULO DE LAS HORAS ESTIMADAS DE LLEGADA DE LA UNIDAD A CADA PUNTO***************************************/
        
        $RPActv = $em->getRepository('DefaultDb_Entities_RoutePointActivity');
        
        if($page == 1){
        	
        	$volt = 0;
        	$weit = 0;
        	foreach ($rows as $key=>$row) {///METODO ANTERIOR
        		if($key==0){
        			$currDate = $row['scheduled_date'];
        			$dt = DateTime::createFromFormat('Y-m-d H:i:s',$currDate);
        		}else{
        			$minutesToAdd = $row['arrtime'];
        			$dt->add(new DateInterval('PT' . $minutesToAdd . 'S'));
        		}
        		if($row['hasActivity'] == "1"){
        			$minutesToAdd = $row['arrtime'];
        			//$dt->add(new DateInterval('PT' . $minutesToAdd . 'S'));
        			$dt->add(new DateInterval('PT' . 2 . 'M'));
        		}
        		$row["datePoint"]=$dt->format('Y-m-d H:i:s');
        		
        		$rpvl = 0;
        		$rpvc = 0;
        		//$acttype = "";
        		$RPActvs = $RPActv->findBy(array('scheduledRoute' => $scheduledRouteId, 'routePoint' => $row["rpid"]));///OBTIENE TODAS LAS ACTIVIDADES EN EL PUNTO ESPECIFICO
        		
        		if($RPActvs){
        			foreach ($RPActvs as $act){
        				//$acttype .= $act->getActivityType()->getId()."<-->";
        				//$acttype .= $act->getTransaction()->getId()."<-->";
        				//$acttype .= $act->getTransaction()->getTransactionId();
        				$pqdat = $em->getRepository('DefaultDb_Entities_PackageToOrder')->findOneBy(array('order' => $act->getTransaction()->getTransactionId()));
        				//$acttype .= $pqdat->getWeight()."<--->";
        				
        				if($act->getActivityType()->getId() == 1){
        					$rpvc = $rpvc + (int)$pqdat->getWeight();
        					$rpvl = $rpvl + ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());
        				}
        				if($act->getActivityType()->getId() == 2){
        					$rpvc = $rpvc - (int)$pqdat->getWeight();
        					$rpvl = $rpvl - ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());        					 
        				}
        				$hactual = ($act->getHoraActual() != null)?$act->getHoraActual()->format('H:i:s'):"";
        			}
        		}
        		
        		$volt = $volt + $rpvl;
        		$weit = $weit + $rpvc;	
        		$row["volPoint"]=$result[2][0]['volume'] - $volt;
        		$row["capPoint"]=$result[2][0]['capacity'] - $weit;
        		$row["activetipe"]=$volt;
        		$row["hactual"]=$hactual;
        		$resultRows[]=$row;
        		
        	}
        }else{
        	$volt = 0;
        	$weit = 0;
        	$RPonit = $em->getRepository('DefaultDb_Entities_RoutePoint');
        	$h=0;
        	$m=0;
        	$s=0;
        	 
        	$Routeall = $RPonit->createQueryBuilder('f')///OBTIENE LOS PUNTOS ANTERIORES A LA RECOLECCION DEL PAQUETE
        	->where("f.route = :rid AND f.order BETWEEN :colpoint AND :delpoint")
        	->orderBy('f.order', 'ASC')
        	->setParameter('rid',$rows[0]["rid"])
        	->setParameter('colpoint',1)
        	->setParameter('delpoint',$rows[0]["order_number"]-1)
        	->getQuery();
        	 
        	//$RPActvb = $RPActv->findBy(array('scheduledRoute' => $scheduledRouteId));///OBTIENE TODAS LAS ACTIVIDADES DE UNA RUTA
        	
        	$RPActvb = $RPActv->createQueryBuilder('f')///OBTIENE TODAS LAS ACTIVIDADES DE UNA RUTA
        	->where("f.scheduledRoute = :scid")
        	->setParameter('scid',$scheduledRouteId)
        	->groupBy("f.routePoint")
        	->getQuery();
        	
        	$arrpoints = array();
        	foreach ($RPActvb->getResult() as $item){
        		if($item->getActivityType()->getId() != NULL){
        		$arrpoints[] = array(
        						'id' => $item->getRoutePoint()->getId(),
        						'type' => $item->getActivityType()->getId(),
        						'order' => $item->getTransaction()->getTransactionId(),
        						'time' => $item->getRoutePoint()->getPoint()->getAcTime()
        						);
        		}
        	}
        	
        	foreach ($Routeall->getResult() as $Rp){
        		$h= $h + (int)$Rp->getArrivalTime()->format('H');
        		$m= $m + (int)$Rp->getArrivalTime()->format('i');
        		$s= $s + (int)$Rp->getArrivalTime()->format('s');
        	
        		foreach ($arrpoints as $flag){///SE VALIDA SI EXISTEN ACTIVIDADES EN EL PUNTO DE LA RUTA
        			if($Rp->getId() == $flag["id"]){

        				$h= $h + (int)$flag["time"]->format('H');
        				$m= $m + (int)$flag["time"]->format('i');
        				$s= $s + (int)$flag["time"]->format('s');
        				
        				//$m= $m + 5;
        				$pqdat = $em->getRepository('DefaultDb_Entities_PackageToOrder')->findOneBy(array('order' => $flag['order'] ));
        				
        				if($flag['type'] == 1){
        					$weit = $weit + (int)$pqdat->getWeight();
    	    				$volt = $volt + ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());
        				}
        				if($flag['type'] == 2){
	        				$weit = $weit - (int)$pqdat->getWeight();
        					$volt = $volt - ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());
	        			}
        			}
        		}
        	}
        	
        	$nuevafecha = date('Y-m-d H:i:s', strtotime($result[2][0]["scheduled_date"]));
        	$nuevafecha = date('Y-m-d H:i:s', strtotime($nuevafecha.'+'.$h.' hour'));
        	$nuevafecha = date('Y-m-d H:i:s', strtotime($nuevafecha.'+'.$m.' minute'));
        	$nuevafecha = date('Y-m-d H:i:s', strtotime($nuevafecha.'+'.$s.' second'));

        	$dt = DateTime::createFromFormat('Y-m-d H:i:s',$nuevafecha);// ESTE ES EL CALCULO DE HORARIOS ANTES DE LA PAGINA ACTUAL SELECCIONADA
        	 
        	foreach ($rows as $key=>$row) {//SE TOMAN LOS DATOS DE LOS PUNTOS EN LA PAGINA ACTUAL ACTUAL SELECCIONADA
        	
       			$minutesToAdd = $row['arrtime'];
       			$dt->add(new DateInterval('PT' . $minutesToAdd . 'S'));
        		
        		$rpvl = 0;
        		$rpvc = 0;
        		$acttype = "";
        		$RPActvs = $RPActv->findBy(array('scheduledRoute' => $scheduledRouteId, 'routePoint' => $row["rpid"]));///OBTIENE TODAS LAS ACTIVIDADES EN EL PUNTO ESPECIFICO
        		
        		if($RPActvs){
        			
        			//if($row['hasActivity'] == "1"){
        			$minToadd = $row['ptime'];
        			$dt->add(new DateInterval('PT' . $minToadd . 'S'));
        			//}
        			
        			foreach ($RPActvs as $act){
        				//$acttype .= $act->getActivityType()->getId()."<-->";
        				//$acttype .= $act->getTransaction()->getId()."<-->";
        				//$acttype .= $act->getTransaction()->getTransactionId();
        				$pqdat = $em->getRepository('DefaultDb_Entities_PackageToOrder')->findOneBy(array('order' => $act->getTransaction()->getTransactionId()));
        				//$acttype .= $pqdat->getWeight()."<--->";
        				
        				if($act->getActivityType()->getId() == 1){
        					$rpvc = $rpvc + (int)$pqdat->getWeight();
        					$rpvl = $rpvl + ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());
        				}
        				if($act->getActivityType()->getId() == 2){
        					$rpvc = $rpvc - (int)$pqdat->getWeight();
        					$rpvl = $rpvl - ((int)$pqdat->getWidth()*(int)$pqdat->getHeight()*(int)$pqdat->getDepth());
        				}
        				$hactual = ($act->getHoraActual() != null)?$act->getHoraActual()->format('H:i:s'):"";
        			}
        		}

        		$row["datePoint"]=$dt->format('Y-m-d H:i:s');
        		
        		$volt = $volt + $rpvl;
        		$weit = $weit + $rpvc;
        		$row["volPoint"]=$result[2][0]['volume'] - $volt;
        		$row["capPoint"]=$result[2][0]['capacity'] - $weit;
        		$row["activetipe"]=$volt;
        		$row["hactual"]=$hactual;
        		$resultRows[]=$row;
        	}
        }
        /****************************************CALCULO DE LAS HORAS ESTIMADAS DE LLEGADA DE LA UNIDAD A CADA PUNTO***************************************/
        
        return array('data'=>array($resultRows,$result[2]),'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function getActivityDetail($page,$rowsPerPage,$sortField,$sortDir, $scheduledRouteId,$routePointId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = "
                rpa.scheduledRoute_id
                ,rpa.date
                ,tr.transaction_id
                ,CASE WHEN tr.status = 5 AND rpa.id >=(case when tr.statusPoint_id is null then 0 else tr.statusPoint_id end) THEN 0 ELSE 1 END isTransactionActive
                ,rpa.routePoint_id
                ,rp.point_id curpoint
                ,ptOrder00.id endpoint
                ,rpa.activityType_id
                ,tr.transactionType_id
                ,CASE WHEN tr.transactionType_id=1 then oc.id else ps.id end order_id
                ,rpa.id #CASE WHEN tr.transactionType_id=1 then pto.id else null end id
                ,CASE WHEN tr.transactionType_id=1 then pto.num_package else null end num_package
                ,CASE WHEN tr.transactionType_id=1 then CONCAT('',pto.namePackage) else CONCAT('',pr.name) end namePackage
                ,IFNULL(pto.promotion_id,0) orderPromotion
                ,IFNULL(promoOrder.name,'') promotionPackage
                ,at.name as activityType
                ,CONCAT(CASE WHEN tr.transactionType_id=1 then ptOrder01.name else IFNULL(seller.commercial_name,'Desconocido') end,
                    ' - ',
                CASE WHEN tr.transactionType_id=1 then ptOrder00.name else buyer.name end) originDestiny
                ,rpa.id routePointActivityId
            ";

        $query="
            SELECT [FIELDS]
            FROM routepoint_activity rpa
                LEFT JOIN activity_type at ON rpa.activityType_id = at.id
                LEFT JOIN transactions tr on rpa.transaction_id = tr.id
                LEFT JOIN m3_commerce_order oc ON tr.transaction_id = oc.id
                LEFT JOIN package_to_order pto ON oc.id = pto.order_id
                LEFT JOIN promotion promoOrder on pto.promotion_id=promoOrder.id
                LEFT JOIN points ptOrder00 on oc.pointBuyer_id = ptOrder00.id
                LEFT JOIN points ptOrder01 on oc.pointSeller_id = ptOrder01.id

                LEFT JOIN promotion_schedule ps on tr.transaction_id = ps.id
                LEFT JOIN users seller on ps.user_id=seller.id
                LEFT JOIN route_points rp on rpa.routePoint_id = rp.id                
                LEFT JOIN points buyer on rp.point_id=buyer.id
                LEFT JOIN promotion pr on ps.promotion_id = pr.id

            WHERE 
                rpa.scheduledRoute_id = :scheduledRouteId
                AND rpa.routePoint_id = :routePointId
            [ORDERBY]
            [LIMT]
        ";

        $additionalQuery = "            
            SELECT pt.name pointName FROM route_points rp LEFT JOIN points pt ON rp.point_id = pt.id
            WHERE rp.id = :routePointId
        ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect
                ."; ".$queryCount
                ."; ".$additionalQuery;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
       
        $stmt->bindValue(":scheduledRouteId",$scheduledRouteId);
        $stmt->bindValue(":routePointId",$routePointId);
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords),"SQL"=>$querySelect);
    }

    public function getPackageTracking($page,$rowsPerPage,$sortField,$sortDir, $orderId){
        $em = $this->getEntityManager();
       
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = "                                
                rpa.*, p.name pointName,atype.name activityType,t.status,r.name routeName
                ,CASE WHEN t.status = 5 AND rpa.id >=(case when t.statusPoint_id is null then 0 else t.statusPoint_id end) THEN 0 ELSE 1 END isTransactionActive
            ";

        $query="
            SELECT [FIELDS]
            FROM routepoint_activity rpa 
                LEFT JOIN transactions t on rpa.transaction_id = t.id
                LEFT JOIN route_points rp on rpa.routePoint_id = rp.id
                LEFT JOIN activity_type atype on rpa.activityType_id = atype.id
                LEFT JOIN points p on rp.point_id=p.id
                LEFT JOIN routes r on rp.route_id=r.id
            WHERE t.transaction_id = :orderId
            ORDER BY rpa.date
            [LIMT]
        ";

        $additionalQuery = "            
            SELECT pto.*  FROM package_to_order pto
            WHERE pto.order_id = :orderId
        ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]'," ", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect
                ."; ".$queryCount
                ."; ".$additionalQuery;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
       
        $stmt->bindValue(":orderId",$orderId);
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords));
    }

    public function stopPackage($routePointActivityId){
        $success = 0;
        $em = $this->getEntityManager();
       
        //Obtener referencia al objeto routePointActivity 
        $dql = "SELECT rpa,tr
                FROM DefaultDb_Entities_RoutePointActivity rpa LEFT JOIN rpa.transaction tr
        WHERE rpa.id = :routePointActivityId";

        $query=$em->createQuery($dql);
        $query->setParameter('routePointActivityId',$routePointActivityId);
        $routePointActivity = $query->getResult();        
        
        if(count($routePointActivity)>1) return;

        $transaction = $routePointActivity[0]->getTransaction();
        $transaction->setStatus(5); //Detenido
        $transaction->setStatusPoint($routePointActivity[0]);        
        $em->flush();
        $success = 1;
        return array("success"=>$success);
    }

/* ------------------------> Corpo Genius Comenta FUNCIONES MODULO CHOFER <------------------------- */

    public function getHourRouteSummary ($id,$startDate,$statusRoute){
        $em = $this->getEntityManager();
        
        $hour = $this->find($id);
        $hour->setStartDate($startDate);
        $hour->setStatusRoute($statusRoute);
        $em->persist($hour);
        $em->flush();
        return;
    }
    
    public function getEndHour($id,$endDate,$statusRoute){
        $em = $this->getEntityManager();
        
        $endHour = $this->find($id);
        $endHour->setEndDate($endDate);
        $endHour->setStatusRoute($statusRoute);
        $em->persist($endHour);
        $em->flush();
        return;
    }

    public function getScheduleRouteIdListDQL($id){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = "
                    sr.id
                ";
        $query="
            FROM logistic_masfletes.scheduled_route sr
            where sr.id='".$id."'
                ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function addProgress ($id, $progress){
        $em = $this->getEntityManager();
        
        $progreso = $this->find($id);
        
        $progreso->setProgress($progress);
        
        $em->persist($progreso);
        $em->flush();
        return;
    }
/* ------------------------> Corpo Genius Comenta FUNCIONES MODULO CHOFER <------------------------- */
    
/* ------------------------> Corpo Genius Comenta FUNCIONES MODULO Almacenista <------------------------- */
    //Consulta de rutas con actividad que pasan por el centro de intercambio
    public function getViewWarehousemanRoutesListDQL($page,$rowsPerPage,$sortField,$sortDir){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $usuario = "" + $this->_cr['id'];
        
        $sqlSelect = "
            distinct
                    rpa.scheduledRoute_id as scheduledRouteId,
                    rp.id as routePointId,
                    rpa.date as scheduledDate,
                    r.name as routeName,
                    concat(v.name,' [',v.volume, ' ft³','] ', '[',v.capacity,']') as vehicle,
                    u.first_name as driver,
                    uw.first_name as warehouseman,
                    p.name as warehouse,
                    sr.statusRoute as status
        ";
        $query="
                SELECT [FIELDS]
                from scheduled_route sr
                    inner join routes r on sr.route_id=r.id
                    inner join vehicles v on sr.vehicle_id=v.id
                    inner join users u on sr.driver_id=u.id
                    inner join route_points rp on r.id=rp.route_id
                    inner join points p on rp.point_id=p.id
                    inner join users uw on p.id=uw.point_id
                    inner join routepoint_activity rpa on rp.id=rpa.routePoint_id
                where uw.id=:usuario and rp.status=1 #and rpa.date = CURDATE() 
                group by rpa.date
                order by rpa.date asc
                [LIMT]  
            ";
        // where u.id = '".$usuario."' and rp.status = 1 and p.type = 2 and rpa.date >= CURDATE()
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
       
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":usuario",$usuario);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    //Programaciones de la ruta que pasan por el centro de intercambio
    public function getWarehouseListDQL($page,$rowsPerPage,$sortField,$sortDir,$routeId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect="
            distinct
                r.id as routeId,
                r.name as routeName,
                sr.id as scheduledRouteId,
                date_format(sr.scheduled_date, '%e-%b-%Y') as scheduledDate,
                u.first_name as driveName,
                v.name as vehicleName
        ";
        $query="
            SELECT [FIELDS]
            from scheduled_route sr
                inner join routes r on sr.route_id=r.id
                inner join users u on sr.driver_id=u.id
                inner join vehicles v on sr.vehicle_id=v.id
                where r.id=:routeId
            [LIMT]
        ";
        $additionalQuery = " 
            SELECT
                sr.scheduled_date,
                CONCAT('[',r.code,'] ',r.name) routeName 
            FROM scheduled_route sr
            LEFT JOIN routes r on sr.route_id = r.id
            WHERE sr.route_id = :routeId
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[LIMT]', " ", $queryCount); 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount."; ".$additionalQuery;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
                   
        $stmt->bindValue(":routeId",$routeId);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords));
        echo $totalRecords;
    }
    
    //Programaciones del centro de intercambio
    public function getWarehousePacksListDQL ($page,$rowsPerPage,$sortField,$sortDir,$scheduleRouteId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $usuario = "" + $this->_cr['id'];
        
        $sqlSelect="
            distinct
                rp.order_number as orderNumber,
                sr.id as scheduleRouteId,
                r.id as routeId,
                rp.id as routePointId,
                date_format(rpac.date, '%h:%i %p') as scheduleDate,
                r.name as routeName,
                p.name as pointName,
                CASE WHEN rpa.routePoint_id IS NULL THEN 0 ElSE 1 END hasActivity,
                u.id as userId
        ";
        $query="
            SELECT [FIELDS]
                from scheduled_route sr
                    left join scheduled_route_activity sra on sr.id=sra.scheduledRoute_id
                    left join routes r on sr.route_id=r.id
                    left join route_points rp on r.id=rp.route_id
                    left join points p on rp.point_id=p.id
                    left join users u on p.id=u.point_id
                    LEFT JOIN (
                                    SELECT DISTINCT rpa.routePoint_id 
                                    FROM scheduled_route_activity sra 
                                    LEFT JOIN routepoint_activity rpa ON sra.routepointactivity_id = rpa.id
                                    WHERE sra.scheduledRoute_id = :scheduleRouteId
                            ) rpa on rp.id = rpa.routePoint_id
                    left join routepoint_activity rpac on rp.id=rpac.routePoint_id
                where sr.id=:scheduleRouteId and u.id=:usuario and rp.status=1 and p.type=2
                group by rp.id
                order by rp.order_number asc
            [LIMT]
        ";
        $additionalQuery="
            SELECT
                sr.scheduled_date,
                CONCAT('[',r.code,'] ',r.name) routeName 
            FROM scheduled_route sr
            LEFT JOIN routes r on sr.route_id = r.id
            WHERE sr.id = :scheduleRouteId
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[LIMT]', " ", $queryCount); 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount."; ".$additionalQuery;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
                   
        $stmt->bindValue(":scheduleRouteId",$scheduleRouteId);
        $stmt->bindValue(":usuario",$usuario);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords));
        
    }
    
    //Función actividades de almacenista
    public function getWarehousemanActivityListDQL($page,$rowsPerPage,$sortField,$sortDir,$scheduleRouteId,$routePointId,$id){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect=" distinct
            rpa.id as id,
            rpa.scheduledRoute_id as scheduleRouteId,
            rp.id as routePointId,
            ord.id as orderId,
            p.namePackage as packName,
            case
            when typ.id = 1 Then 'Entregar'
            when typ.id = 2 Then 'Recolectar'
            else 'Sin actividad'
            end as activityWarehouseman,
            ad.date as activityHour,
            rpa.status as status,
            ad.status as packageStatus,
            case
            when typ.id=1 then uw.id
            when typ.id=2 then u.id
            end as userDelivery,
            case
            when typ.id=1 then u.id
            when typ.id=2 then uw.id
            end as userReceiving,
            case
            when typ.id=1 then po.name
            when typ.id=2 then v.name
            end as entityFrom,
            case
            when typ.id=1 then v.name
            when typ.id=2 then po.name
            end entityTo,
            r.name as routeName,
            po.name as warehouse
        ";
        $query="
            SELECT [FIELDS]
                from package_to_order p 
                    left join m3_commerce_order ord on p.order_id=ord.id
                    left join transactions trans on trans.transaction_id = ord.id 
                    left join routepoint_activity rpa on trans.id=rpa.transaction_id
                    left join activity_type typ on typ.id = rpa.activityType_id
                    left join route_points rp on rp.id = rpa.routePoint_id
                    left join activity_detail ad on rpa.id=ad.routePointActivity_id
                    left join points po on rp.point_id=po.id
                    left join scheduled_route sr on rpa.scheduledRoute_id=sr.id
                    left join users u on sr.driver_id=u.id
                    left join users uw on po.id=uw.point_id
                    left join vehicles v on sr.vehicle_id=v.id
                    left join routes r on sr.route_id=r.id
                where
                    ((:scheduleRouteId is null or rpa.scheduledRoute_id=:scheduleRouteId)
                and (:routePointId is null or rp.id =:routePointId))
                and (:id is null or rpa.id=:id)
                order by typ.id asc
            [LIMT]
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
       
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":scheduleRouteId",$scheduleRouteId);
        $stmt->bindValue(":routePointId",$routePointId);
        $stmt->bindValue(":id",$id);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    //Rastreo de paquetes
    public function getPackageTrackingWarehousemanListDQL ($page,$rowsPerPage,$sortField,$sortDir, $orderId){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect="
            distinct
                rpa.id,
                rpa.scheduledRoute_id,
                rpa.date as scheduleDate,
                p.name pointName,
                atype.name activityType,
                ad.status,
                r.name routeName,
                ad.date as hourActivity,
                CASE WHEN t.status = 5 AND rpa.id >=(case when t.statusPoint_id is null then 0 else t.statusPoint_id end) THEN 0 ELSE 1 END isTransactionActive
        ";
        $query="
            SELECT [FIELDS]
            FROM routepoint_activity rpa 
                LEFT JOIN transactions t on rpa.transaction_id = t.id
                LEFT JOIN route_points rp on rpa.routePoint_id = rp.id
                LEFT JOIN activity_type atype on rpa.activityType_id = atype.id
                LEFT JOIN points p on rp.point_id=p.id
                LEFT JOIN routes r on rp.route_id=r.id
                LEFT JOIN activity_detail ad on rpa.id=ad.routePointActivity_id
            WHERE t.transaction_id = :orderId
            ORDER BY rpa.date
            [LIMT]
        ";
        $additionalQuery="
            SELECT pto.*
            FROM package_to_order pto
            WHERE pto.order_id = :orderId
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]'," ", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount."; ".$additionalQuery;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
       
        $stmt->bindValue(":orderId",$orderId);
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords));
    }

    /* ------------------------> Corpo Genius Comenta FUNCIONES MODULO Almacenista <------------------------- */ 
}
