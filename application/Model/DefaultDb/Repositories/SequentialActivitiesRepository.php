<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_SequentialActivitiesRepository extends EntityRepository
{
    /*
    Función que devuelve un listado de rutas que tienen actividad de acuerdo a filtros proporcionados.    
    */
    public function getScheduleListDQL($page,$rowsPerPage,$sortField,$sortDir,$iniDate, $endDate,$status,$routeName,$vehicleName,$driverName){
        $em = $this->getEntityManager();
        
        $offset = ($page-1)*$rowsPerPage;
        $dql="
            SELECT s.id id, s.startDate startDate,s.status status,
                r.id routeId,CONCAT(CONCAT(CONCAT(CONCAT('[',r.code),']'),' '),r.name) routeName, 
                v.id vehicleId, v.name vehicleName,
                u.id userId, CONCAT(CONCAT(u.firstName,' '),u.lastName) driverName
            FROM  
               DefaultDb_Entities_Schedule s
               LEFT JOIN s.route r
               LEFT JOIN s.user u
               LEFT JOIN s.vehicle v
            WHERE 
                (:id IS NULL OR s.id=:id)
            AND (:routeId IS NULL OR s.route=:routeId)
            AND (:vehicleId IS NULL OR s.vehicle=:vehicleId)
            AND (:userId IS NULL OR s.user=:userId)
            AND (   
                    (:iniDate IS NOT NULL OR :endDate IS NULL OR DATE(s.startDate)=:endDate)
                AND
                    (:iniDate IS NULL OR :endDate IS NOT NULL OR DATE(s.startDate)=:iniDate)
                AND 
                    (:iniDate IS NULL OR :endDate IS NULL OR DATE(s.startDate) BETWEEN :iniDate and :endDate)   
                ) 
            AND (:status IS NULL or s.status=:status)
            AND (:monday IS NULL or s.monday=:monday)
            AND (:tuesday IS NULL or s.tuesday=:tuesday)
            AND (:wednesday IS NULL or s.wednesday=:wednesday)
            AND (:thursday IS NULL or s.thursday=:thursday)
            AND (:friday IS NULL or s.friday=:friday)
            AND (:saturday IS NULL or s.saturday=:saturday)
            AND (:sunday IS NULL or s.sunday=:sunday)
            AND (:recurrent IS NULL or s.recurrent=:recurrent)
            AND (:week IS NULL or s.week=:week)
            AND (:routeName IS NULL OR CONCAT(r.code,r.name) LIKE :routeName)
            AND (:vehicleName IS NULL OR v.name LIKE :vehicleName)
            AND (:driverName IS NULL OR CONCAT(u.firstName,u.lastName) LIKE :driverName)
        ";

        $dql.=' ORDER BY '.$sortField.' '.$sortDir;
       
        $query=$em->createQuery($dql)->setFirstResult($offset)->setMaxResults($rowsPerPage);
                
        $query->setParameter('id',$id);
        $query->setParameter('routeId',$routeId);
        $query->setParameter('vehicleId',$vehicleId);
        $query->setParameter('userId',$userId);
        $query->setParameter('iniDate',$iniDate);
        $query->setParameter('endDate',$endDate);        
        $query->setParameter('status',$status);
        $query->setParameter('monday',$monday);
        $query->setParameter('tuesday',$tuesday);
        $query->setParameter('wednesday',$wednesday);
        $query->setParameter('thursday',$thursday);
        $query->setParameter('friday',$friday);
        $query->setParameter('saturday',$saturday);
        $query->setParameter('sunday',$sunday);
        $query->setParameter('recurrent',$recurrent);
        $query->setParameter('week',$week);
        $query->setParameter('routeName',($routeName==null) ? null : '%'.$routeName.'%');
        $query->setParameter('vehicleName',($vehicleName==null) ? null : '%'.$vehicleName.'%');
        $query->setParameter('driverName',($driverName==null) ? null : '%'.$driverName.'%');


        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($paginator);
        
        $schedules=array();
        foreach ($paginator as $schedule) {
            $schedules[]=$schedule;
        }
        
        return array('data'=>$schedules,'meta'=>array('totalRecords'=>$totalRecords));
    }

    public function getScheduleList($page,$rowsPerPage,$sortField,$sortDir,$iniDate, $endDate,$status,$routeName,$vehicleName,$driverName){

        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
/*
select   ro.id routeId, sa.id sequentialActivityId,co.id commerceOrderId,sc.id scheduleId, 
    us.id driverId,ve.id vehicleId, ro.code routeCode, ro.name routeName, us.first_name driverName, ve.name vehicleName,sa.shipping_date
    from sequential_activities  sa  
    left join m3_commerce_order co on sa.order_id = co.id
    left join schedule sc on co.schedule_id=sc.id
    left join routes ro on sc.route_id=ro.id
    left join users us on sc.user_id = us.id
    left join vehicles ve on sc.vehicle_id=ve.id    
    -- where  date(sa.shipping_date)='2015-04-10' --
    
    order by co.id desc
*/
        $sqlSelect = " (@row_num:=@row_num+1)id,ro.id routeId, sa.id sequentialActivityId,co.id commerceOrderId,sc.id scheduleId,
                us.id driverId,ve.id vehicleId, CONCAT('[',ro.code,']',' ',ro.name) routeName, CONCAT(us.first_name,' ',us.last_name) driverName, ve.name vehicleName,sa.shipping_date
                ";

        $query=" set @row_num=0;
            SELECT [FIELDS]
            FROM  
               sequential_activities  sa
               LEFT JOIN m3_commerce_order co on sa.order_id = co.id
               LEFT JOIN schedule sc on co.schedule_id=sc.id
               LEFT JOIN routes ro on sc.route_id=ro.id
               LEFT JOIN users us on sc.user_id = us.id
               LEFT JOIN vehicles ve on sc.vehicle_id=ve.id
            WHERE            
                (   
                    (:iniDate IS NOT NULL OR :endDate IS NULL OR DATE(sa.shipping_date)=:endDate)
                AND
                    (:iniDate IS NULL OR :endDate IS NOT NULL OR DATE(sa.shipping_date)=:iniDate)
                AND 
                    (:iniDate IS NULL OR :endDate IS NULL OR DATE(sa.shipping_date) BETWEEN :iniDate and :endDate)   
                ) 
            AND (:status IS NULL or co.shipping_status=:status)
            AND (:routeName IS NULL OR CONCAT(ro.code,ro.name) LIKE :routeName)
            AND (:vehicleName IS NULL OR ve.name LIKE :vehicleName)
            AND (:driverName IS NULL OR CONCAT(us.first_name,us.last_name) LIKE :driverName)
            AND co.shipping_status IN(1,2)
            [ORDERBY]
            [LIMT]
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);


        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]', ($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(':iniDate',$iniDate);
        $stmt->bindValue(':endDate',$endDate);        
        $stmt->bindValue(':status',$status);
        $stmt->bindValue(':routeName',($routeName==null) ? null : '%'.$routeName.'%');
        $stmt->bindValue(':vehicleName',($vehicleName==null) ? null : '%'.$vehicleName.'%');
        $stmt->bindValue(':driverName',($driverName==null) ? null : '%'.$driverName.'%');

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
}

