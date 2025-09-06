<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_RoutePointActivityRepository extends EntityRepository
{
    public function createRoutePointActivites($sequentialActivities,$transactionType){

        $em = $this->getEntityManager();
        $arrRpActivities = array();        
        $dql = "SELECT partial atype.{id} FROM  DefaultDb_Entities_ActivityType atype";
        $query=$em->createQuery($dql);
        $activityTypes = $query->getResult();

        $dql = "SELECT partial ttype.{id} FROM  DefaultDb_Entities_TransactionType ttype WHERE ttype=:transactionType";
        $query=$em->createQuery($dql);
        $query->setParameter('transactionType',$transactionType);
        $transactionTypes = $query->getOneOrNullResult();

        $arrIdRoutePoints = array();
        foreach ($sequentialActivities as $activity){
            $arrIdRoutePoints[] = $activity["routePointId"]; 
        }

        $dql = "SELECT partial rp.{id}, partial p.{id} FROM  DefaultDb_Entities_RoutePoint rp LEFT JOIN rp.point p WHERE rp IN(:routePointsId)";
        $query=$em->createQuery($dql);
        $query->setParameter('routePointsId',$arrIdRoutePoints);
        $routePoints = $query->getResult();

        $transaction = null;
        foreach ($sequentialActivities as $index=>$activity) {
            $dql = "SELECT sr FROM DefaultDb_Entities_ScheduledRoute sr WHERE DATE(sr.scheduledDate) = DATE(:scheduledDate) and sr.route = :route ";
            $query=$em->createQuery($dql);
            $query->setParameter('scheduledDate',$activity["shippingDate"]);
            $query->setParameter('route', $activity["routeId"]);
            $scheduledRoute = $query->getOneOrNullResult();
            
            $rpActivity = new DefaultDb_Entities_RoutePointActivity();
            
            if($transaction == null){
                $transaction = new DefaultDb_Entities_Transactions();
                $transaction->setTransactionId($activity["orderId"]);
                $transaction->setTransactionType($transactionTypes);
                $transaction->setStatus(1);
                $em->persist($transaction);
            }
            
            $activityType = null;
            foreach ($activityTypes as $at) {
                if($at->getId() == $activity["activityType"])
                    $activityType = $at;
            } 
            
            $currRutePoint = null;
            foreach ($routePoints as $rp) {
                if($rp->getId()==$activity["routePointId"])
                    $currRutePoint = $rp;
            }

            $rpActivity->setRoutePoint($currRutePoint);
            $rpActivity->setTransaction($transaction);
            $rpActivity->setDate($activity["shippingDate"]);
            $rpActivity->setActivityType($activityType);
            $rpActivity->setScheduledRoute($scheduledRoute);
            $arrRpActivities[] = $rpActivity;

            $em->persist($rpActivity);
        }
        return $arrRpActivities;
    }

    //De acuerdo a una Orden de Compra dada, obtiene la lista de actividades secuenciales y el costo para cada actividad (Punto de Venta).
    // Con Puntos de Venta repetidos, divide el costo.
    // Este método devuelve las tarifas por puntos de venta y por rutas
    /* Devuelve:

    - element_id    El id del elemento (Id de la entidad, puede ser Ruta o Punto de Venta)
    - element_type  Tipo de elemento (1.Ruta, 2.Punto de venta)
    - pv            El id del punto de venta único por ruta (Route Point)
    - activityType  Tipo de actividad 1. Recolecta, 2. Entrega
    - ptype         Tipo de punto de venta. 1 PV, 2. Centro de Intercambio
    - shippingDate  Fecha programada de la actividad
    - client_rate   Tarifa para el PV o Ruta
    - elementName   Nombre del PV o Ruta
    */
    public function getRoutePointRatesByOrder($orderId){
        $em = $this->getEntityManager();

        $query = "
            SET @num := 0, @groupElement := '', @groupElementType:='';
            SELECT elements.element_id, elements.element_type, elements.pv pv,elements.activityType,
            p.type ptype,elements.shippingDate,
            rates.client_rate, 
            #rates.factorCount,
            CASE elements.element_type WHEN 1 THEN r.name ELSE p.name END elementName,
            elements.routeId
            FROM(
                #### Puntos de venta #### 
                SELECT p.id element_id,2 element_type,rp.id pv,sa.type activityType,sa.shipping_date shippingDate,r.id routeId  
                FROM sequential_activities sa 
                    INNER JOIN route_points rp on sa.routePoint_id=rp.id
                    INNER JOIN points p on rp.point_id = p.id
                    INNER JOIN routes r on rp.route_id=r.id
                WHERE sa.order_id=:orderId
                UNION
                #### Rutas unicas que tocan los puntos de venta #### 
                SELECT DISTINCT r.id element_id,1 element_type,NULL pv,NULL activityType,NULL shippingDate,r.id routeId  
                FROM sequential_activities sa 
                    INNER JOIN route_points rp on sa.routePoint_id=rp.id    
                    INNER JOIN routes r on rp.route_id=r.id
                WHERE sa.order_id=:orderId
            )elements
            LEFT JOIN
            (   
                select rpactivities.id element_id,rpactivities.element_type,lastRates.client_rate/*/IFNULL(rpactivities.cant,1)*/ client_rate,rpactivities.cant factorCount FROM (
                    SELECT pr.id,pr.element_id, pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                    @num := if(@groupElement = element_id AND @groupElementType = element_type, @num + 1, 1) row_number,
                    @groupElement := element_id gpName,
                    @groupElementType := element_type gpname2
                    FROM package_rate pr 
                    WHERE pr.element_type in (1,2)
                    ORDER BY pr.element_id,pr.element_type,pr.date DESC
                )lastRates  
                LEFT JOIN
                (
                SELECT p.id, 2 element_type, count(p.id) cant
                FROM sequential_activities sa 
                    INNER JOIN route_points rp on sa.routePoint_id=rp.id
                    INNER JOIN points p on rp.point_id = p.id
                WHERE sa.order_id=:orderId
                GROUP BY p.id
                UNION
                SELECT r.id, 1 element_type, count(r.id) cant
                FROM sequential_activities sa 
                    INNER JOIN route_points rp on sa.routePoint_id=rp.id
                    INNER JOIN routes r on rp.route_id = r.id
                WHERE sa.order_id=:orderId
                GROUP BY r.id
                ) rpactivities
                ON lastRates.element_id = rpactivities.id and lastRates.element_type=rpactivities.element_type
                WHERE lastRates.row_number=1    
            )rates            
            ON elements.element_id=rates.element_id AND elements.element_type = rates.element_type
            LEFT JOIN points p ON elements.element_id=p.id and elements.element_type=2
            LEFT JOIN routes r ON elements.routeId=r.id and elements.element_type=1
            ORDER BY elements.element_type,elements.shippingDate
        ";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindValue("orderId",$orderId);

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $rateByRoutePoint = $result[0];

        return $rateByRoutePoint;
    }

 	//De acuerdo a una Orden de Compra dada, obtiene el resumen de costos por todas las actividades(Punto de Venta).
    public function getSumRatesByRoutePoint($orderId){
        $em = $this->getEntityManager();

        $query = "
            SET @num := 0, @groupElement := '', @groupElementType:='';
            SELECT rpactivities.element_id,rpactivities.element_type,rpactivities.ptype,rates.client_rate  FROM(
                SELECT r.id element_id,1 element_type,0 ptype
                FROM sequential_activities sa 
                INNER JOIN route_points rp on sa.routePoint_id=rp.id    
                INNER JOIN routes r on rp.route_id=r.id
                WHERE sa.order_id=:orderId AND sa.type=2                
                UNION ALL
                SELECT DISTINCT p.id element_id,2 element_type,p.type ptype
                FROM sequential_activities sa 
                INNER JOIN route_points rp on sa.routePoint_id=rp.id
                INNER JOIN points p on rp.point_id = p.id
                INNER JOIN routes r on rp.route_id=r.id
                WHERE sa.order_id=:orderId
            ) rpactivities
            LEFT JOIN
            (   
                select lastRates.* FROM (
                    SELECT pr.id,pr.element_id, pr.element_type,pr.client_rate,pr.provider_fee,
                    @num := if(@groupElement = element_id AND @groupElementType = element_type, @num + 1, 1) row_number,
                    @groupElement := element_id gpName,
                    @groupElementType := element_type gpname2
                    FROM package_rate pr 
                    WHERE pr.element_type in (1,2)
                    ORDER BY pr.element_id,pr.element_type,pr.date DESC
                )lastRates  WHERE row_number=1  
                )rates
                ON rpactivities.element_id=rates.element_id AND rpactivities.element_type = rates.element_type   
            ORDER BY rpactivities.element_type
        ";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindValue("orderId",$orderId);

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $rateByRoutePoint = $result[0];

        $sumTotalRate = 0;
        $totalRates = 0;
        $centroIntercambio = 0;
        
        $arrRatesCaptured = array_filter($rateByRoutePoint, function($el){
            return floatval($el['client_rate'])>0;            
        });
        
        foreach ($arrRatesCaptured as $key => $item) {
            //Para cada centro de intercambio, se multiplica * 2, ya que se cobra la recolecta y entrega.
            $currentRate = floatval($item['client_rate']);
            $esPuntodeVenta = intval($item['element_type'])==2;
            $esCentroIntercambio = intval($item['ptype'])==2;
            if($esPuntodeVenta && $esCentroIntercambio){
                $sumTotalRate +=$currentRate * 2;
                $centroIntercambio +=1;
            }else{
                $sumTotalRate += $currentRate;
            }
        }

        $response = array(
                    "hasFullRatesCaptured"=>count($rateByRoutePoint)==count($arrRatesCaptured),
                    "totalAmount"=>$sumTotalRate,
                    "totalRoutePoint"=>count($arrRatesCaptured)+$centroIntercambio
                    );

        return $response;
    }

// ----------------------------- CORPOGENIUS COMENTA ------------------------------------
    //FUNCIÓN QUE ALMACENA LA HORA EN EN QUE SE REALIZO LA LECTURA DE CODIGO DE BARRAS
    //AL LLEGAR AL PUNTO MARCADO POR LA RUTA.
    public function saveCurrentHourPoint ($id, $HoraActual){
        $em = $this->getEntityManager();
        
        $currentHour = $this->find($id);
        
        $currentHour->setHoraActual($HoraActual);
        
        $em->persist($currentHour);
        $em->flush();
        return;
    }

    //FUNCIÓN SALVAR TRANFERENCIA MODULO ALMACENISTA
    public function saveWarehousemanTransfer($id,$userDelivery,$userReceiving,$status,$userAbsence,$statusReason,$entityFrom,$entityTo){
        $em = $this->getEntityManager();
        
        if($id==null){
            $transfer = new DefaultDb_Entities_RoutePointActivity();
        }else{
            $transfer = $this->find($id);
        }
        if($userDelivery == null){
        } else {
            $uDelivery=$em->find("DefaultDb_Entities_User", $userDelivery);
            $transfer->setUserDelivery($uDelivery);
        }
        if($userReceiving == null){
        } else {
            $uReceiving=$em->find("DefaultDb_Entities_User", $userReceiving);
            $transfer->setUserReceiving($uReceiving);
        }
        
        $transfer->setStatus($status);
        $transfer->setUserAbsence($userAbsence);
        $transfer->setStatusReason($statusReason);
        $transfer->setEntityFrom($entityFrom);
        $transfer->setEntityTo($entityTo);
        
        $em->persist($transfer);
        $em->flush();
        return;
    }
// ----------------------------- CORPOGENIUS COMENTA ------------------------------------

    /**********************************************RECHAZO DE PAQUETES***********************************/
    public function getMaxPoints($routeId)
    {
    	
    	$cnx = $this->getEntityManager()->getConnection();
    	$routePoints = $cnx->executeQuery('	select id from route_points 
				where order_number = (select max(order_number) from route_points  where route_id ='.$routeId.')
				and route_id = '.$routeId);
    	return $routePoints->fetchAll();
    }
    
    public function getOrderPoints($rpId)
    {
    	
    	$cnx = $this->getEntityManager()->getConnection();
    	$routePoints = $cnx->executeQuery('	select order_number from route_points 
				where id ='.$rpId);
    	return $routePoints->fetchAll();
    }
    
    public function saveNewRPA($nuevafecha,$maxpointid,$transactionid,$scheduledid,$OCId,$routedate){

    	$em = $this->getEntityManager();
    	 
    	$SqAct = new DefaultDb_Entities_SequentialActivities;
    	$RPAct = new DefaultDb_Entities_RoutePointActivity;
    	
    	$Transaction = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId'=>$OCId, 'transactionType' => 1));
    	/*$t = $em->getRepository('DefaultDb_Entities_Transactions');
    	$Transaction = $t->createQueryBuilder('f')
    	->where("f.transactionId = :transactionId AND f.transactionType IS NOT NULL")
    	->setParameter('transactionId',$OCId)
    	->setMaxResults(1)
    	->getQuery()->getSingleResult();*/
    	
    	$OC = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id'=>$OCId));
    	$RP = $em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('id'=>$maxpointid));
    	$SR = $em->getRepository('DefaultDb_Entities_ScheduledRoute')->findOneBy(array('id'=>$scheduledid));
    	$AcTy = $em->getRepository('DefaultDb_Entities_ActivityType')->findOneBy(array('id'=>2));
    	 
    	$RPAct->setDate(new DateTime($nuevafecha));
    	$RPAct->setRoutePoint($RP);
    	$RPAct->setActivityType($AcTy);
    	$RPAct->setTransaction($Transaction);
    	$RPAct->setScheduledRoute($SR);

    	$em->persist($RPAct);
    	
    	$SqAct->setOrder($OC);
    	$SqAct->setType(2);
    	$SqAct->setShippingDate(new DateTime($nuevafecha));
    	$SqAct->setRouteDate(new DateTime($routedate));
    	$SqAct->setRoutePoint($RP);
    	
    	$em->persist($SqAct);
    	 
    	$Transaction->setStatus(6);
    	$em->persist($Transaction);

    	$em->flush();
    	 
    	/*$cnx = $this->getEntityManager()->getConnection();
    	$routePoints = $cnx->executeQuery('	
    			INSERT INTO routepoint_activity(date, routePoint_id, activityType_id, transaction_id, scheduledRoute_id)
    			values("'.$nuevafecha.'",'.$maxpointid.',2,'.$transactionid.','.$scheduledid.')
    			');
    	
    	$sequential = $cnx->executeQuery('
    			INSERT INTO sequential_activities(order_id, type, shipping_date, route_date, routePoint_id)
    			values('.$OCId.',2,"'.$nuevafecha.'","'.$routedate.'",'.$maxpointid.')
    			');
    	$transaction = $cnx->executeQuery('UPDATE transactions SET status = 6 WHERE transaction_id = '.$OCId);
    	*/
    	return;
   }    
    
   public function delARouting($OCId,$Id,$trnsId,$SqActId){
   	
	   	$em = $this->getEntityManager();
	   	
	   	$SqAct = $em->getRepository('DefaultDb_Entities_SequentialActivities');
	   	$Transaction = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId'=>$OCId, 'transactionType' => 1));
	   	/*$t = $em->getRepository('DefaultDb_Entities_Transactions');
	   	$Transaction = $t->createQueryBuilder('f')
	   	->where("f.transactionId = :transactionId AND f.transactionType IS NOT NULL")
	   	->setParameter('transactionId',$OCId)
	   	->setMaxResults(1)
	   	->getQuery()->getSingleResult();*/
	
	   	$criteria1 = "t.transaction = :transaction";
	   	$criteria2 = "t.order = :order";
	   	$criteria1 .= ' AND t.id > :id';
	   	$criteria2 .= ' AND t.id > :id';
	   	
	  	$RPAct = $this->createQueryBuilder('t')
	          	->where($criteria1)
	          	->setParameter('transaction', $Transaction->getId());
	  	
	  	$SqActs = $SqAct->createQueryBuilder('t')
	  			->where($criteria2)
	  			->setParameter('order', $OCId);
	  	 
	  	$RPAct->setParameter('id', $Id);
	  	$SqActs->setParameter('id', $SqActId);
	
	  	//ACTUALIZAR EL ESTATUS DE LA TRANSACCION
	  	$Transaction->setStatus(6);
	  	$em->persist($Transaction);
	  	      
	    $R = $RPAct->getQuery();
	    $S = $SqActs->getQuery();
	    
	  	foreach ($R->getResult() as $key) {
	  		$em->remove($key);
	  	}
	  	
	  	foreach ($S->getResult() as $key) {
	  		$em->remove($key);
	  	}

	  	$em->flush();
	  	
		return;
	   	 
	   	/*$cnx = $this->getEntityManager()->getConnection();
	   	$routePoints = $cnx->executeQuery('
	   					DELETE FROM routepoint_activity
	   					WHERE transaction_id = '.$trnsId.' AND id > '.$Id 
	    			 );
	   	 
	   	$sequential = $cnx->executeQuery('
	    				DELETE FROM sequential_activities
	    				WHERE order_id = '.$OCId.' AND id > '.$SqActId
	        		);
	   	$transaction = $cnx->executeQuery('UPDATE transactions SET status = 6 WHERE transaction_id = '.$OCId);
	   	 
	   	return;*/
   }
   
   public function saveRejActiv($nuevafecha,$maxpointid,$type,$transactionid,$scheduledid){//Genera las actividades para el chofer 
   	
   	$nuevafecha = $nuevafecha->format('Y-m-d H:i:s');
   	
   	$em = $this->getEntityManager();
   	
   	$RPAct = new DefaultDb_Entities_RoutePointActivity;
   	$Transaction = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('id'=>$transactionid));
   	$RP = $em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('id'=>$maxpointid));
   	$SR = $em->getRepository('DefaultDb_Entities_ScheduledRoute')->findOneBy(array('id'=>$scheduledid));
   	$AcTy = $em->getRepository('DefaultDb_Entities_ActivityType')->findOneBy(array('id'=>$type));
   	
   	$RPAct->setDate(new DateTime($nuevafecha));
   	$RPAct->setRoutePoint($RP);
   	$RPAct->setActivityType($AcTy);
   	$RPAct->setTransaction($Transaction);
   	$RPAct->setScheduledRoute($SR);
   	
    $em->persist($RPAct);
   	   	
   	$Transaction->setStatus(6);
   	$em->persist($Transaction);
   	
   	$em->flush();
   	
   	/*$cnx = $this->getEntityManager()->getConnection();
   	$routePoints = $cnx->executeQuery('
    			INSERT INTO routepoint_activity(date, routePoint_id, activityType_id, transaction_id, scheduledRoute_id)
    			values("'.$nuevafecha.'",'.$maxpointid.','.$type.','.$transactionid.','.$scheduledid.')
    			');
   	$transaction = $cnx->executeQuery('UPDATE transactions SET status = 1 WHERE id = '.$transactionid);//La orden esta reprogramada*/
   	
   	return;
   }
   /**********************************************RECHAZO DE PAQUETES***********************************/

   /**********************************************ACTIVIDADES SECUENCIALES ADICIONALES******************/
   public function aditionalSeq($rpadAc){
   	   	
	   	$em = $this->getEntityManager();
	   	
	   	$SqAct = new DefaultDb_Entities_SequentialActivities;
    	$OC = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id'=>$rpadAc["orderId"]));
    	//$AcTy = $em->getRepository('DefaultDb_Entities_ActivityType')->findOneBy(array('id'=>$rpadAc["activityType"]));
    	$RP = $em->getRepository('DefaultDb_Entities_RoutePoint')->findOneBy(array('id'=>$rpadAc["routePointId"]));
    	//$SR = $em->getRepository('DefaultDb_Entities_ScheduledRoute')->findOneBy(array('id'=>$scheduledid));

	   	 
	   	$SqAct->setOrder($OC);
	   	$SqAct->setType($rpadAc["activityType"]);
	   	$SqAct->setShippingDate(new DateTime($rpadAc["shippingDate"]->format('Y-m-d H:i:s')));
	   	$SqAct->setRouteDate(new DateTime($rpadAc["routeDate"]->format('Y-m-d H:i:s')));
	   	$SqAct->setRoutePoint($RP);
	   	 
	   	$em->persist($SqAct);
	   	
	   	$em->flush();
	   	//return $rpadAc["orderId"];   	
   		//return $rpadAc["activityType"];
   		return $rpadAc["routePointId"];
   }
   
   /**********************************************ACTIVIDADES SECUENCIALES ADICIONALES******************/
     
}