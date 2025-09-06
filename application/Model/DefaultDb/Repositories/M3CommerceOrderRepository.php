<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_M3CommerceOrderRepository extends EntityRepository
{
    /**
     * Metodo que busca entre las ordenes las que se encuentren entre fechas indicadas
     * ademas verifica que el shipping_status sea "Por recoleccion"
     * @param string $starDate
     * @param string $endDate
     */

    public function getSecuencialActivity($starDate, $endDate)
    {
        $validStatus = array(
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED,
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED,
        );
        $statusStr = implode(',', $validStatus);

        $em = $this->getEntityManager();
        $dql = '
                SELECT o
                FROM DefaultDb_Entities_M3CommerceOrder o
                JOIN o.schedule s
                WHERE o.shippingDate >= \''.$starDate->format("Y-m-d 00:00:00").'\'
                    AND o.shippingDate <= \''.$endDate->format("Y-m-d 11:59:59").'\'
                    AND o.shippingStatus IN ('.$statusStr.')
                ORDER BY s.id , o.shippingDate ASC
                ';
        $query = $em->createQuery($dql);
        $routePoints = $query->getResult();
        return $routePoints;
    }
    
    public function getOrdersByStatusShipping($userId, $status)
    {
        $em = $this->getEntityManager();
        $dql = '
            SELECT o
            FROM DefaultDb_Entities_M3CommerceOrder o
                JOIN o.buyer b
            WHERE b.id = '.$userId.' AND o.shippingStatus = '.$status;

        $query = $em->createQuery($dql);
        $orders = $query->getResult();
        
        return $orders;
    }
    
    public function getSecuencialActivityByDateByRoute($starDate,$route=null,$dateStartRoute=null)
    {
        $validStatus = array(
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED,
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED,
        );
        $statusStr = implode(',', $validStatus);
        $date = new DateTime($starDate->format('Y-m-d 00:00:00'));
        $date->modify('+1 day');
        $em = $this->getEntityManager();
        $where =
        'WHERE '. ($route === null ? '' : 'r.id ='.$route->getId().' AND ') .
         ( $dateStartRoute===null ?
            ' s.shippingDate >= \''.$starDate->format("Y-m-d").'\'
            AND s.shippingDate <= \''.$date->format("Y-m-d").'\' AND'
                    :
                ' s.routeDate=\''.$dateStartRoute.'\' AND'
            ).
        ' o.shippingStatus IN ('.$statusStr.')';
        $dql = 'SELECT s
        FROM DefaultDb_Entities_SequentialActivities s
        JOIN s.order o
        JOIN s.routePoint rp
        JOIN rp.route r
        JOIN rp.point p
        '.$where .'
        ORDER BY s.shippingDate ASC
        ';
        $query = $em->createQuery($dql);
        $routePoints = $query->getResult();
        return $routePoints;
    }
    
    /**********************PAQUETES RECHAZADOS****************************************/
    public function getOrdersRejected($OCId = NULL)
    {
    	$cnx = $this->getEntityManager()->getConnection();
    	$dql = 'select 
				m3_commerce_order.id as ordid, m3_commerce_order.buyer_id as b_id, m3_commerce_order.pointBuyer_id as pb_id, m3_commerce_order.pointSeller_id as ps_id, 
    			shipping_status as ship_st, transactions.id as t_id, transactions.status, status_reason, points.id as Cid,
    			brc1.id as brc_id, brc2.id as brc_id2, brc3.id as brc_id3,
                route_points.route_id as rt_id,
                pto.weight, (pto.width * pto.height * pto.depth) as tvol
				FROM m3_commerce_order
				left join transactions on m3_commerce_order.id = transactions.transaction_id
				left join routepoint_activity on routepoint_activity.transaction_id = transactions.id
    			left join route_points on routepoint_activity.routePoint_id = route_points.id
    			left join points on route_points.point_id = points.id
    			left join branches_user as brc1 on points.id = brc1.point_id
    			left join branches_user as brc2 on m3_commerce_order.pointBuyer_id = brc2.point_id
    			left join branches_user as brc3 on m3_commerce_order.pointSeller_id = brc3.point_id
                left join package_to_order pto on m3_commerce_order.id = pto.order_id
    			where shipping_status = 4 and transactions.status = 6 and routepoint_activity.status = 2 and brc1.client_id = 24 and brc2.client_id = 24 and brc3.client_id = 24';
    	
    	if($OCId != NULL){
    		$dql .=' and m3_commerce_order.id='.$OCId;
    	}

    	$result = $cnx->executeQuery($dql);
    	return $result->fetchAll();
    }
    
    public function getScheduledRejected($pointId,$dateParam){
    	$cnx = $this->getEntityManager()->getConnection();
    	$date = $dateParam->format('Y-m-d H:i:s');
    	$query = "
        SELECT s.schedule_id sch_id, s.scheduled_date start_date, s.route_id as r_id FROM scheduled_route s
        WHERE s.schedule_id in(
        SELECT s.id
                        FROM schedule s
                        INNER JOIN route_points rp ON rp.route_id = s.route_id
                        INNER JOIN branches_user u ON u.point_id = rp.point_id
                        WHERE
                        ( s.start_date > curdate() or curdate() BETWEEN s.start_date and s.end_date)
    			        AND u.client_id = 24
    			        AND rp.point_id =".$pointId."
                        AND rp.status = 1
                        AND s.status = 1
         )
         AND s.scheduled_date > DATE_ADD(curdate(),INTERVAL 1 DAY)
         AND s.statusRoute IS NULL 
    	 /*AND s.scheduled_date between DATE_ADD(curdate(),INTERVAL 1 DAY) and DATE_ADD(curdate(),INTERVAL 10 DAY)*/ 
         ORDER BY s.scheduled_date DESC /*LIMIT 1*/
        ";
    	$result = $cnx->executeQuery($query);
    	return $result->fetchAll();
    }
    
    /*public function getOrdersRejectedById($OCId)
    {
    	$cnx = $this->getEntityManager()->getConnection();
    	$dql = 'select
				m3_commerce_order.id, shipping_status, transactions.id, transactions.status, status_reason
				FROM m3_commerce_order
				left join transactions on m3_commerce_order.id = transactions.transaction_id
				left join routepoint_activity on routepoint_activity.transaction_id = transactions.id
				where shipping_status = 4 and transactions.status = 6 and routepoint_activity.status = 2
    			and m3_commerce_order.id='.$OCId;
    
    
    	$result = $cnx->executeQuery($dql);
    	return $result->fetchAll();
    }*/
    /**********************PAQUETES RECHAZADOS****************************************/
    
    
    public function getFavoriteOrder($productoId,$buyerId, $sellerId){
    	$cnx = $this->getEntityManager()->getConnection();
    	
    	$query = "
                SELECT 
                    m3_commerce_order.id orderId,
                    DATE(creation_date) fecha
                FROM m3_commerce_order
                LEFT JOIN m3_commerce_products_to_orders
                    ON m3_commerce_order.id = m3_commerce_products_to_orders.order_id
                WHERE m3_commerce_order.buyer_id = '".$buyerId."'
                    AND  m3_commerce_order.seller_id = '".$sellerId."'
                    AND (product_id = '".$productoId."' or '".$productoId."' = '')
                ORDER BY m3_commerce_order.id DESC
                LIMIT 1
        ";
    	$result = $cnx->executeQuery($query);
    	return $result->fetchAll();
    }

    public function getMaxml($userId){
    	$cnx = $this->getEntityManager()->getConnection();
    	$query = "
        SELECT  * 
    	FROM xml_exports
        WHERE  id_user = ".$userId."
    	 ORDER BY folio DESC LIMIT 1
        ";
    	$result = $cnx->executeQuery($query);
    	return $result->fetchAll();
    }
    
    public function newXml($iduser,$folio,$path){
    	$cnx = $this->getEntityManager()->getConnection();
    	$query = "
        	insert into xml_exports(id_user,path,date,folio) values(".$iduser.",'".$path."','".date('Y-m-d H:m:s')."',".$folio.")
        ";
    	$result = $cnx->executeQuery($query);
    	return $result;
    }
    
}