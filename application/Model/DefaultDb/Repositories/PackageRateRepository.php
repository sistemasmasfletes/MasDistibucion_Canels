<?php
use Doctrine\ORM\EntityRepository;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_PackageRateRepository extends EntityRepository
{
    
    public function getPackageRate($page,$rowsPerPage,$sortField,$sortDir,$rateId)
    {
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
       
        $sqlSelect = "  id, element_id,element_type,date,client_rate,provider_fee, CASE element_type WHEN 1 THEN tipo1 WHEN 2 THEN tipo2 END element ";
        $query="
            SET @num := 0, @group := '';
            SELECT [FIELDS] FROM (
                SELECT pr.id, pr.element_id,pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                CASE WHEN pr.element_type=1 THEN r.name ELSE NULL END tipo1,
                CASE WHEN pr.element_type=2 THEN p.name ELSE NULL END tipo2,
                @num := if(@group = element_id, @num + 1, 1) row_number,
                @group := element_id gpName        
                FROM package_rate pr
                    LEFT JOIN routes r ON pr.element_id=r.id
                    LEFT JOIN points p ON pr.element_id=p.id                
                ORDER BY element_id,element_type,date DESC
            )groupedRates                    
            WHERE groupedRates.row_number=1
            AND (:elementId IS NULL OR id=:elementId)       
            [LIMT]";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
            
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue('elementId',$elementId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
         
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function getPackageRate1($page,$rowsPerPage,$sortField,$sortDir,$elementId)
    {
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
       
        $sqlSelect = "  id, element_id,element_type,date,client_rate,provider_fee, CASE element_type WHEN 1 THEN tipo1 WHEN 2 THEN tipo2 END element ";
        $query="
            SET @num := 0, @group := '';
            SELECT [FIELDS] FROM (
                SELECT pr.id, pr.element_id,pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                CASE WHEN pr.element_type=1 THEN r.name ELSE NULL END tipo1,
                CASE WHEN pr.element_type=2 THEN p.name ELSE NULL END tipo2,
                @num := if(@group = element_id, @num + 1, 1) row_number,
                @group := element_id gpName        
                FROM package_rate pr
                    LEFT JOIN routes r ON pr.element_id=r.id
                    LEFT JOIN points p ON pr.element_id=p.id                
                ORDER BY element_id,element_type,date DESC
            )groupedRates                    
            WHERE groupedRates.row_number=1
            AND (:elementId IS NULL OR id=:elementId)       
            [LIMT]";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
            
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue('elementId',$elementId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
         
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function getElementsForRates($elementName, $elementType){
        $em = $this->getEntityManager();

        if($elementType ==1) //Rutas
            $query = " SELECT id, CONCAT('[',code,']',' ',name) name from routes 
                        WHERE name like :elementName OR code like :elementName 
                        AND status=1";   
        else //Puntos de venta
            $query = " SELECT id, CONCAT('[',code,']',' ',name) name from points 
                        WHERE name like :elementName OR code like :elementName 
                        AND status=1";
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindValue('elementName','%'.$elementName.'%');
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result);
    }

    public function getRoutesWithRates($page,$rowsPerPage,$sortField,$sortDir,$userId,$routeName,$rateId,$routeId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = " r.id, CONCAT('[',r.code,'] ',r.name) name, 
                    CASE WHEN rates.element_id IS NULL THEN 0 ELSE 1 END hasRate,
                    rates.rateId,IFNULL(rates.element_id,r.id) element_id, IFNULL(rates.element_type,1) element_type, rates.date, rates.client_rate, rates.provider_fee  ";

        $query = "
                    SET @num := 0, @group := '';
                    SELECT [FIELDS]
                    FROM  routes r
                        LEFT JOIN
                        (SELECT id rateId,element_id, element_type,date,client_rate,provider_fee
                            FROM  
                                # Última tarifa agrupada por ruta
                                (SELECT pr.id,pr.element_id, pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                                    @num := if(@group = element_id, @num + 1, 1) row_number,
                                    @group := element_id gpName 
                                FROM package_rate pr 
                                WHERE pr.element_type = 1  # Tipo Ruta
                                ORDER BY element_id,date DESC
                                ) lastRate
                            WHERE row_number=1
                        ) rates
                        ON r.id = rates.element_id
                    WHERE  (:routeName is null OR (r.name like :routeName OR r.code like :routeName ))
                    AND (:rateId IS NULL OR rates.rateId = :rateId)
                    AND (:routeId IS NULL OR r.id = :routeId)
                    AND r.status=1
                    [ORDERBY]
                    [LIMIT]
                    ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) records ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
            
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue('userId',$userId);
        $stmt->bindValue('routeName','%'.$routeName.'%');
        $stmt->bindValue('rateId',$rateId);
        $stmt->bindValue('routeId',$routeId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return $result;        
    }

    public function getPointsWithRates($page,$rowsPerPage,$sortField,$sortDir,$userId,$pointName,$rateId,$routeId,$routePointId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = " rp.id, CONCAT('[',p.code,'] ',p.name) name, rp.order_number position,
                    CASE WHEN rates.element_id IS NULL THEN 0 ELSE 1 END hasRate,
                    rates.rateId,IFNULL(rates.element_id,p.id) element_id, IFNULL(rates.element_type,2) element_type, rates.date, rates.client_rate, rates.provider_fee  ";

        $query = "
                    SET @num := 0, @group := '';
                    SELECT [FIELDS]
                    FROM  route_points rp 
                        INNER JOIN points p on rp.point_id = p.id
                        LEFT JOIN
                        (SELECT id rateId,element_id, element_type,date,client_rate,provider_fee
                            FROM  
                                # Última tarifa agrupada por punto de venta
                                (SELECT pr.id,pr.element_id, pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                                    @num := if(@group = element_id, @num + 1, 1) row_number,
                                    @group := element_id gpName 
                                FROM package_rate pr 
                                WHERE pr.element_type = 2  # Tipo Punto de venta
                                ORDER BY element_id,date DESC
                                ) lastRate
                            WHERE row_number=1
                        ) rates
                        ON p.id = rates.element_id
                    WHERE 1=1 # p.controller_id = :userId TODO
                    AND (:routeId IS NULL OR rp.route_id=:routeId)
                    AND rp.status=1
                    AND (:pointName is null OR (p.name like :pointName OR p.code like :pointName ))
                    AND p.status=1                  
                    AND (:rateId IS NULL OR rates.rateId = :rateId)
                    AND (:routePointId IS NULL OR p.id = :routePointId)
                    ORDER BY rp.order_number
                    [LIMIT]
                    ";
 
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) records ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
            
        $querys = $querySelect."; ".$queryCount;
        $querys.= "; SELECT CONCAT('[',r.code,'] ',r.name) routeName FROM routes r WHERE r.id=:routeId ";
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue('userId',$userId);
        $stmt->bindValue('pointName','%'.$pointName.'%');
        $stmt->bindValue('rateId',$rateId);
        $stmt->bindValue('routeId',$routeId);
        $stmt->bindValue('routePointId',$routePointId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return $result;  
    }

    public function save($rateId,$elementId,$elementType,$dateRate,$clientRate,$providerFee){
        $em = $this->getEntityManager();
        
        $obRate = null;
        if($rateId == null){
            $obRate = new DefaultDb_Entities_PackageRate();
            $obRate->setElementId($elementId);
            $obRate->setElementType($elementType);
            $obRate->setDate($dateRate);
            $obRate->setClientRate($clientRate);
            $obRate->setProviderFee($providerFee);
            $em->persist($obRate);
        }else{
            $obRate = $em->find('DefaultDb_Entities_PackageRate',$rateId);
            $obRate->setDate($dateRate);
            $obRate->setClientRate($clientRate);
            $obRate->setProviderFee($providerFee);
        }

        $em->flush();
        return $obRate;
    }

    public function getRateByElement($page,$rowsPerPage,$sortField,$sortDir,$userId,$elementId,$elementType){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
 
        $sqlSelect = " pr.id, pr.element_id,pr.element_type, pr.date, pr.client_rate, pr.provider_fee ";

        $query = "
                SELECT [FIELDS] FROM package_rate pr
                WHERE pr.element_type = :elementType
                AND pr.element_id = :elementId
                ORDER BY date desc 
                [LIMIT] ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) records ', $query);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;
        $querys.="; ";
        $querys.= $elementType==1 ? " SELECT CONCAT('[',r.code,'] ',r.name) name FROM routes r WHERE r.id=:elementId " : 
            "SELECT CONCAT('[',p.code,'] ',p.name) name FROM points p WHERE p.id=:elementId ";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue('elementType',$elementType);
        $stmt->bindValue('elementId',$elementId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        return $result;
    }

    public function getPackageRateByRoutePoint($arrRoutePoint,$getDetail){
        $em = $this->getEntityManager();

        $inStatement = "";
        //$inStatement = implode(',',array_fill(0, count($arrRoutePoint), '?'));
        for($i=0;$i<count($arrRoutePoint);$i++)
            $inStatement .= ($i==0 ? "" : ",").":p".($i+1);

        $query = "
            SET @num := 0, @groupElement := '', @groupElementType:='';
            SELECT elements.element_id, elements.element_type, rates.client_rate FROM (
                SELECT routes.id element_id, 1 element_type FROM(
                    SELECT DISTINCT  r.id FROM route_points rp 
                    INNER JOIN routes  r on rp.route_id = r.id
                    WHERE rp.id in(".$inStatement.")
                )routes 
                UNION
                SELECT points.id element_id, 2 element_type FROM(
                    SELECT DISTINCT  p.id FROM route_points rp 
                    INNER JOIN points p on rp.point_id = p.id
                    WHERE rp.id in(".$inStatement.")
                )points 
            )elements
            LEFT JOIN
            (   
                select lastRates.* FROM (
                    SELECT pr.id,pr.element_id, pr.element_type,pr.date,pr.client_rate,pr.provider_fee,
                        @num := if(@groupElement = element_id AND @groupElementType = element_type, @num + 1, 1) row_number,
                        @groupElement := element_id gpName,
                        @groupElementType := element_type gpname2
                    FROM package_rate pr 
                    WHERE pr.element_type in (1,2)
                    ORDER BY element_id,element_type,date DESC
                )lastRates  WHERE row_number=1  
            )rates  
            ON elements.element_id=rates.element_id AND elements.element_type = rates.element_type
            ORDER BY elements.element_type
        ";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        foreach ($arrRoutePoint as $index => $id)
            $stmt->bindValue("p".($index+1),$id);
        

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        $totalRate = 0;
        $totalClientRate = 0;
        $rateByRoutePoint = $result[0];
        $i=0;
        foreach($rateByRoutePoint as $key=>$rate){
            $i++;
            $totalRate += $rate["client_rate"];
            $totalClientRate += $rate["client_rate"]!=null ? 1 : 0;
        }
        $returnResponse = array("hasFullRatesCaptured"=>($totalClientRate==$i),"totalAmount"=>$totalRate,"totalRoutePoint"=>$i);
        if($getDetail)
            $returnResponse["tableRate"]=$rateByRoutePoint;

        return $returnResponse;
    }
}