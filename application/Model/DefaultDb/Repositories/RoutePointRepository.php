<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_RoutePointRepository extends EntityRepository
{
    /**
     * Devuelve los puntos de la ruta recibida
     * @param int $routeId
     * @return array
     */
    public function getRoutesPointsByRoute($routeId)
    {
        $em = $this->getEntityManager();
        $dql = '
                SELECT rp.id, p.id as idPoint, p.name, p.code, p.type
                FROM DefaultDb_Entities_RoutePoint rp 
                JOIN rp.point p
                JOIN rp.route r
                WHERE r.id = '.$routeId.' 
                AND rp.status != 0  
                ORDER BY rp.order ASC';
        
        $query = $em->createQuery($dql);
        $routePoints = $query->getResult();
        return $routePoints;
    }
    
    /**
     * Regresa solo los puntos de intercambio de una ruta
     * @param type $route
     * @return type
     */
    public function getPointExchangeByRoute($route)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = 
        'SELECT rp.id routePointId,p.idpoint, p.name, rp.order_number
        FROM (
            SELECT id idpoint,name 
            FROM points
            WHERE type = '.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'
            AND status = ' .DefaultDb_Entities_Point::STATUS_NORMAL. '
        ) p 
        JOIN ( 
            SELECT id,route_id, point_id, order_number
            FROM route_points
            WHERE route_id ='.$route->getId().'
                AND status = '.DefaultDb_Entities_RoutePoint::STATUS_NORMAL.'
            ) rp
	ON p.idpoint = rp.point_id 
        WHERE rp.route_id ='.$route->getId();
//        echo $query;
        $res = $conn->executeQuery($query);
        $res = $res->fetchAll(PDO::FETCH_NAMED);
        return $res;
    }
    
    public function getRoutesPointsByRouteAndPointType($routeId, $pointType)
    {
        $em = $this->getEntityManager();
        $dql = '
            SELECT rp 
            FROM DefaultDb_Entities_RoutePoint rp 
            JOIN rp.point p
            JOIN rp.route r
            WHERE r.id = '.$routeId.'
            AND p.type = '.$pointType.'
            ORDER BY p.name ASC';
        $query = $em->createQuery($dql);
        $routePoints = $query->getResult();
        return $routePoints;
    }
    
    /**
     * Regresa el orden inicial de una ruta ya que no siempre sera uno
     */
    public function getOrderInitialByRoute($route){
        
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
            SELECT min(rp.order_number) ordernumber 
            FROM route_points rp
            WHERE rp.route_id = '.$route->getId();
        $res = $conn->executeQuery($query);
        $res = $res->fetch(PDO::FETCH_OBJ);
        return $res;
    }
    
    public function getLastOrderNumberByRoute($route)
    {
        $orderNumber = 0;
        $criteria = array('route' => $route);
        $orderBy = array('order' => 'DESC');
        $limit = 1;
        $routePoints = $this->findBy($criteria, $orderBy, $limit);
        if(is_array($routePoints) == true && count($routePoints) > 0)
            $orderNumber = $routePoints[0]->getOrder();
        return $orderNumber;
    }
    
    /**
     *
     * @param integer $routeId
     * @param integer $orderNumber
     * @return DefaultDb_Entities_RoutePoint
     */
    public function getStepUpRoutePoint($routeId, $orderNumber)
    {
        $stepUpRoutePoint = false;
        $em = $this->getEntityManager();
        $dql = '
            SELECT rp 
            FROM DefaultDb_Entities_RoutePoint rp 
            JOIN rp.route r
            WHERE r.id = '.$routeId.'
            AND rp.order < '.$orderNumber.'
            ORDER BY rp.order DESC';
        $query = $em->createQuery($dql);
        $query->setMaxResults(1);
        $routePoints = $query->getResult();
        if(is_array($routePoints) == true && count($routePoints) > 0)
            $stepUpRoutePoint = $routePoints[0];
        return $stepUpRoutePoint;
    }
    
    /**
     *
     * @param integer $routeId
     * @param integer $orderNumber
     * @return DefaultDb_Entities_RoutePoint
     */
    public function getStepDownRoutePoint($routeId, $orderNumber)
    {
        $stepUpRoutePoint = false;
        $em = $this->getEntityManager();
        $dql = '
            SELECT rp 
            FROM DefaultDb_Entities_RoutePoint rp 
            JOIN rp.route r
            WHERE r.id = '.$routeId.'
            AND rp.order > '.$orderNumber.'
            ORDER BY rp.order ASC';
        $query = $em->createQuery($dql);
        $query->setMaxResults(1);
        $routePoints = $query->getResult();
        if(is_array($routePoints) == true && count($routePoints) > 0)
            $stepUpRoutePoint = $routePoints[0];
        return $stepUpRoutePoint;
    }
    
    public function getRoutesByPoint($point)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
            SELECT DISTINCT route_id from route_points where point_id='.$point->getId().' AND status = ' . DefaultDb_Entities_Route::STATUS_ACTIVE ;
        $res = $conn->executeQuery($query);
        $res = $res->fetchAll();
        return $res;
    }
    
    public function getSecuencialActivityByDateByRoute($starDate,$route,$dateStartRoute=null)
    {
        $validStatus = array(
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_TO_SHIPPED,
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_SHIPPED,
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_IN_INTERCHANGE_CENTER,
            DefaultDb_Entities_M3CommerceOrder::SHIPPING_STATUS_DELIVERED
        );
        $statusStr = implode(',', $validStatus);
        $date = new DateTime($starDate->format('Y-m-d 00:00:00'));
        $date->modify('+1 day');
        $em = $this->getEntityManager();
                
        $dql ='
        SELECT rps, seq FROM
        (
            SELECT rp FROM DefaultDb_Entities_RoutePoint rp
            WHERE rp.route ='.$route->getId().'
            AND rp.status = '. DefaultDb_Entities_RoutePoint::STATUS_NORMAL .'    
        ) rps
        LEFT JOIN
        (
            SELECT s
            FROM DefaultDb_Entities_SequentialActivities s
            JOIN s.order o
            JOIN s.routePoint rp
            JOIN rp.route r
            JOIN rp.point p
            WHERE r.id =' . 
                $route->getId() . ($dateStartRoute===null ?
                ' AND s.shippingDate >= \''.$starDate->format("Y-m-d").'\'
                AND s.shippingDate <= \''.$date->format("Y-m-d").'\''
                        :
                    ' AND s.routeDate=\''.$dateStartRoute.'\''
                ).
            ' AND o.shippingStatus IN ('.$statusStr.')
            ORDER BY s.shippingDate ASC
        ) seq ON rps.id=seq.routePoint_id
        ';
        $dql ='
            SELECT s
            FROM DefaultDb_Entities_SequentialActivities s
            JOIN s.order o
            JOIN s.routePoint rp
            JOIN rp.route r
            JOIN rp.point p
            WHERE r.id =' . 
                $route->getId() . ($dateStartRoute===null ?
                ' AND s.shippingDate >= \''.$starDate->format("Y-m-d").'\'
                AND s.shippingDate <= \''.$date->format("Y-m-d").'\''
                        :
                    ' AND s.routeDate=\''.$dateStartRoute.'\''
                ).
            ' AND o.shippingStatus IN ('.$statusStr.')
            ORDER BY s.shippingDate ASC
        ';
        $query = $em->createQuery($dql);
        //echo '<pre>'.$query->getSQL();
        $routePoints = $query->getResult();
        return $routePoints;
        
//        $query = $em->createQuery($dql);
//        echo '<pre>'.$query->getSQL();
//        $routePoints = $query->getResult();
//        return $routePoints;
//        
//        $em = $this->getEntityManager();
//        $conn = $em->getConnection();
//        $query = '
//            SELECT s0_.id AS id0, s0_.type AS type1, s0_.shipping_date 
//        AS shipping_date2, s0_.route_date AS route_date3, s0_.routePoint_id 
//        AS routePoint_id4, s0_.order_id AS order_id5 
//        FROM sequential_activities s0_ 
//        INNER JOIN m3_commerce_order m1_ ON s0_.order_id = m1_.id 
//        INNER JOIN route_points r2_ ON s0_.routePoint_id = r2_.id 
//        INNER JOIN routes r3_ ON r2_.route_id = r3_.id 
//        INNER JOIN points p4_ ON r2_.point_id = p4_.id 
//        WHERE r3_.id = 1 AND s0_.route_date = \'2012-11-05 22:00:00\' 
//        AND m1_.shipping_status IN (1, 2) ORDER BY s0_.shipping_date ASC;
//        SELECT * FROM route_points WHERE route_id = 1';
//        $res = $conn->executeQuery($query);
//        $res = $res->fetchAll();
//        return $res;
    }

    public function getRoutePointsFromRoute($page,$rowsPerPage,$sortField,$sortDir,$routeId,$srch){
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
         
        $offset = ($page-1)*$rowsPerPage;
        $em = $this->getEntityManager();

        $selectFields = "rp.id, rp.route_id, rp.point_id, rp.order_number, rp.status, 	
                        date_format(rp.arrival_time,'%i') arrival_min, date_format(rp.arrival_time,'%s') arrival_seg, rp.required, 
        				#concat('[',p.code,'] ',p.name ) nombre,
        				p.name  nombre,
        		        concat_ws(', ', adr.address,p.extNumber,p.neighborhood) direccion,
        				p.urlGoogleMaps as url, p.type pointType,
        				case p.type when 1 then 'PV' else 'CI' end tipo 
                        ";
        $sql = "SELECT [FIELDS] 
                FROM route_points rp 
        		INNER JOIN points p ON rp.point_id = p.id
        		LEFT JOIN address adr ON p.address_id = adr.id
                WHERE rp.route_id = :routeId";
        
        if($srch != ""){
        	$sql .= " AND p.name LIKE '%".$srch."%'  OR adr.address LIKE '%".$srch."%' OR p.neighborhood LIKE '%".$srch."%' OR p.extNumber LIKE '%".$srch."%'";
        }
        
        $sql .= " order by order_number";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) records,  '.$page.' page, CEIL(COUNT(*)/'.$rowsPerPage.') totalpages ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect.=" LIMIT ".$rowsPerPage.' OFFSET '.$offset;

        $querys = $querySelect.";".$queryCount;
        $querys.=";
                SELECT r.name,
                (SELECT COUNT(*) FROM route_points WHERE route_id = :routeId AND status = 1 )cantPoints,
                (SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = :routeId AND p.type = 1 AND rp.STATUS = 1 )pointSales,
                (SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = :routeId AND p.type = 2 AND rp.required = 1 AND rp.STATUS = 1 )exchangeCenters,
                (SELECT COUNT(*) FROM route_points WHERE route_id = :routeId AND required = 1  AND STATUS = 1 )required,
                (SELECT SUM(DATE_FORMAT(arrival_time,'%s'))/60 FROM route_points WHERE route_id = :routeId AND STATUS = 1 )totalTime,
                CASE CLOSE WHEN 0 THEN 'ABIERTA' WHEN 1 THEN 'CERRADA' END estatus
            FROM routes r 
            WHERE r.id = :routeId;";
            
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":routeId",$routeId);
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);        
        return $result;

    }
    
    public function getClientsByRoute($routeId,$userid)
    {
    	$em = $this->getEntityManager();
    	$conn = $em->getConnection();
    	$query = "
			SELECT rp.id as rpid, bu.id as buid, bu.client_id FROM route_points rp
			left join branches_user bu on bu.point_id = rp.point_id
			where route_id = ".$routeId." and bu.client_id <> 24 and bu.client_id <> ".$userid." group by rp.point_id order by rp.order_number asc    			
        ";
    	
    	$stmt = $conn->prepare($query);
    	/*$stmt->bindValue(":clientId",$clientId);
    	$stmt->bindValue(":pointId",$pointId);
    	$stmt->bindValue(":dateParam",$dateParam->format('Y-m-d'));*/
    	$stmt->execute();
    	$array = $stmt->fetchAll(PDO::FETCH_NAMED);
    	return $array;    	
    	
    }    
    
    
    
}