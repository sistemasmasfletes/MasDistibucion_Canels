<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_PointRepository extends EntityRepository
{
    
    const CENTRO_INTERCAMBIO_TYPE = 2;
    //METODOS CRUD CATÃ�LOGO PUNTOS DE VENTA, CORPOGENIUS COMENTA
    
    public function getPointsListDQL($page,$rowsPerPage,$sortField,$sortDir,$id, $srch){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        //CON ESTE CODIGO SACAMOS LA SESIÃ“N DEL USUARIO
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $controllerId = "" + $this->_cr['id'];
        
        $sqlSelect="
                p.id,p.code,p.name,p.type,p.status,p.controller_id, p.deleted, p.activitytime as actime,
                CONCAT(DATE_FORMAT(p.opening_time, '%H:%i'),' a ', DATE_FORMAT(p.closing_time,'%H:%i')) as workTime,
                CONCAT(a.address,' ', p.extNumber, ', ', p.neighborhood,' ', p.zipcode, ', ', st.name) as address,
                CONCAT(p.code,'.png') as image,
        		ct.title as category,
				(SELECT group_concat(rsub.name) rtsub FROM route_points rpsub 
				left join points psub on rpsub.point_id = psub.id
				left join routes rsub on rsub.id = rpsub.route_id
				where point_id in(p.id)) as routes
        ";
        
        $query="
            SELECT [FIELDS]
                FROM points p
                LEFT JOIN address a ON p.address_id=a.id
        		LEFT JOIN states st ON p.state_id = st.id
        		LEFT JOIN categories ct ON ct.id = p.categoryId_id
        		WHERE (p.controller_id=:controllerId AND p.deleted=true) ";
        if($srch != ""){
        	$query .= " AND p.code LIKE '%".$srch."%' OR p.name LIKE '%".$srch."%'  OR address LIKE '%".$srch."%' ";
        }
	    $query.=" OR (p.type = :centroIntercambio AND p.deleted=true) ";
	    $query.=" [ORDERBY]
            [LIMT]";
        
        //WHERE p.controller_id=:controllerId //USAR CONDICION
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
       
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]', ' ORDER BY '. $sortField . ' ' . $sortDir , $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":controllerId", $controllerId);
        $stmt->bindValue(":centroIntercambio", self::CENTRO_INTERCAMBIO_TYPE);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    public function getPointByIdDQL($id){
        $em = $this->getEntityManager();
        
        //CON ESTE CODIGO SACAMOS LA SESIÃ“N DEL USUARIO
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $controllerId = "" + $this->_cr['id'];
        
        $sqlSelect=" * ";
        $query="
            SELECT [FIELDS]
                FROM points p
                WHERE p.controller_id=:controllerId AND p.id=:id AND deleted=true
        ";
        
        //condicion WHERE p.controller_id=:controllerId AND p.id=:id
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":controllerId", $controllerId);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

//    ELIMINADO EN REPOSITORIO DEL CLIENTE DIC-19-2016
//    public function addPoints($id,$code,$name,$type,$status,$controller,$openingTime,$closingTime,$comments,$deleted,$webpage,$categoryId,$address,$extNumber,$intNumber,$urlGoogleMaps){
//        $em = $this->getEntityManager();
//        
//        $openingTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s', $openingTime);
//        $openingTimeFormat->setTime($openingTimeFormat->format('H'),$openingTimeFormat->format('i'),0);
//        $closingTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s', $closingTime);
//        $closingTimeFormat->setTime($closingTimeFormat->format('H'),$closingTimeFormat->format('i'),0);
//        
//        $this->_cr = Model3_Auth::getCredentials();
//        $dbs = Model3_Registry::getInstance()->get('databases');
//        $em1 = $dbs['DefaultDb'];
//        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
//        $controllerId = "" + $this->_cr['id'];
//        
//        if($controllerId){
//            $controller = $em->getRepository('DefaultDb_Entities_User')->find($controllerId);
//        }
//        
//        if($id == null){
//            $point = new DefaultDb_Entities_Point();
//        } else {
//            $point = $this->find($id);
//        }
//        
//        if($categoryId){
//            $categoryRepo = $em->find('DefaultDb_Entities_Category', $categoryId);
//        }
//
//        if($address){
//            $addressRepo = $em->find('DefaultDb_Entities_Address', $address);
//        }
//        
//        $point->setCode($code);
//        $point->setName($name);
//        $point->setType($type);
//        $point->setStatus($status);
//        $point->setController($controller);
//        $point->setOpeningTime($openingTimeFormat);
//        $point->setClosingTime($closingTimeFormat);
//        $point->setComments($comments);
//        $point->setDeleted(1);
//        $point->setWebpage($webpage);
//        $point->setExtNumber($extNumber);
//        $point->setIntNumber($intNumber);
//        $point->setUrlGoogleMaps($urlGoogleMaps);
//        $point->setCategotyId($categoryRepo);
//        $point->setAddress($addressRepo);
//        
//        $em->persist($point);
//        $em->flush();
//        return;
//    }
    
    public function delete($id,$deleted){
        $em = $this->getEntityManager();

		$point = $this->find($id);
        
        $branches = $em->getRepository('DefaultDb_Entities_BranchesUser')->findOneBy(array('point' => $point)); 

        $em->remove($branches);
        $em->flush();
		
        /*$point->setDeleted($deleted);
        
        $em->persist($point);
        $em->flush();*/

        $em->remove($point);
        $em->flush();


        return;
    }

    public function getPointByNameOrAddress($stringSearch,$state,$conSucursal,$city,$isbranche)
    {
        $em = $this->getEntityManager();
        $points = array();

        $dql ='
				SELECT p.id pid, bu.id buid, p.name, concat_ws(", ", ad.address, p.extNumber, p.intNumber, p.neighborhood) address, 
        		#bu.client_id, if( usr.id <> 24, concat_ws(" ",usr.first_name, last_name), "Sin usuario") usrname /*OBTIENE EL NOMBRE DEL USUARIO Y SI ES EL COMODIN LO MUESTRA Sin usuario*/
                bu.client_id,  concat_ws(" ",usr.first_name, last_name) usrname
                FROM route_points rp 
        		LEFT JOIN points p ON rp.point_id = p.id
                LEFT JOIN address ad ON p.address_id = ad.id
        		LEFT JOIN branches_user bu ON bu.point_id = p.id
                LEFT JOIN users usr ON bu.client_id = usr.id
                WHERE (p.name LIKE :search OR ad.address LIKE :search)
        		AND usr.id = 24 #PARA QUE SOLO USE EL USUARIO COMODIN EXCLUYE A LOS INDICADOS CON USUARIO
                AND p.type <> 2
                AND p.status = 1'; 
        
        /*$dql = 'SELECT rp,p
                FROM DefaultDb_Entities_RoutePoint rp
       			LEFT JOIN rp.point p
                LEFT JOIN p.address ad
                WHERE (p.name LIKE :search OR ad.address LIKE :search)
                AND p.type <> 2
                AND p.status = :status';*/
        if($city !=""){
        	$dql.= ' AND p.city_id = '.$city;
        }
        
        //$dql .= ($isbranche)?'':' GROUP BY bu.client_id';
        
        
        $dql.= ' GROUP BY p.id';
        
        /*$dql = 'SELECT *    //////////////////////////////////////CONSULTA PENDIENTE PARA RESTRICCION PUNTO POR USUARIO
                FROM route_points rp
       			LEFT JOIN points p ON p.id=rp.point_id 
                LEFT JOIN address ad ON ad.id = p.address_id
                WHERE p.id NOT IN(SELECT point_id FROM branches_user br WHERE br.client_id <> 24) 
        		AND (p.name LIKE :search OR ad.address LIKE :search)
                AND p.type <> 2
                AND p.status = :status
                GROUP BY p.id';*/
        
        /*$dql = 'SELECT rp,p
                FROM DefaultDb_Entities_RoutePoint rp INNER JOIN rp.point p 
                LEFT JOIN p.address ad
                WHERE (p.name LIKE :search OR ad.address LIKE :search )
                AND p.status = :status
        		AND p.type <> 2';*/
                //AND ad.state = :state';
        
        if ($conSucursal) {
            $queryConSucursal = "SELECT DISTINCT   
                                    p.id
                                    FROM route_points rp 
                                    INNER JOIN points p ON p.id = rp.point_id  
                                    INNER  JOIN address ad ON ad.id = p.address_id 
                                    INNER JOIN branches_user bu ON bu.point_id =p.id 
                                    INNER JOIN users u ON u.id = bu.client_id 
                                    INNER JOIN categories c ON c.id = u.category_id  
                                    WHERE (p.name LIKE :search OR ad.address LIKE :search ) 
                                              AND p.status = :status AND p.type <> 2 ";
            $conn = $em->getConnection()->getWrappedConnection();
            $stmt = $conn->prepare($queryConSucursal);
            $stmt->bindValue('search', '%' . $stringSearch . '%');
            $stmt->bindValue('status', DefaultDb_Entities_Point::STATUS_NORMAL);
            $stmt->execute();
            $result = $stmt->fetchAll();
            
            for ($i = 0; $i < count($result); $i++) {
                $point = $result[$i];
                $punto = $this->_em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $point['id']));
                $points[] = $punto;
            }
        } else {
        	
        	/*$conn = $em->getConnection()->getWrappedConnection();//////////////////////////////////////CONSULTA PENDIENTE PARA RESTRICCION PUNTO POR USUARIO
        	$stmt = $conn->prepare($dql);
        
        	$stmt->bindValue('search', '%' . $stringSearch . '%');
        	$stmt->bindValue('status', DefaultDb_Entities_Point::STATUS_NORMAL);
        	$stmt->execute();
        	$result = $stmt->fetchAll();
        	
        	for ($i = 0; $i < count($result); $i++) {
        		$point = $result[$i];
        		$punto = $this->_em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $point['point_id']));
        		$points[] = $punto;
        	}*/
        	
        	$conn = $em->getConnection()->getWrappedConnection();
        	$stmt = $conn->prepare($dql);
        	$stmt->bindValue('search', '%' . $stringSearch . '%');
        	$stmt->bindValue('status', DefaultDb_Entities_Point::STATUS_NORMAL);
        	$stmt->execute();
        	$result = $stmt->fetchAll();
        	
        	$points = $result;
        	
        	/*for ($i = 0; $i < count($result); $i++) {
        		$point = $result[$i];
        		$punto = $this->_em->getRepository('DefaultDb_Entities_Point')->findOneBy(array('id' => $point['id']));
        		$userp = $this->_em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $point['client_id']));
        		$points[] = $punto;
        	}*/        	
        	
            /*$query = $em->createQuery($dql);
            $query->setParameter('search', '%' . $stringSearch . '%');
            $query->setParameter('status', DefaultDb_Entities_Point::STATUS_NORMAL);
            //$query->setParameter('state', $state );
            $routePoints = $query->getResult();
            foreach ($routePoints as $key => $routePoint) {
                $points[] = $routePoint->getPoint();
            }*/
        }

        return $points;
    }
    
    public function save(
    		$id,
    		$code,
    		$name,
    		$type,
    		$status,
    		$controller,
    		$openingTime,
    		$closingTime,
    		$acTime,
    		$comments,
    		$deleted,
    		$webpage,
    		$categoryId,
    		$address,
    		$extNumber,
    		$intNumber,
    		$urlGoogleMaps,
    		$neighborhood,
    		$zipcode,
    		$countryid,
    		$stateid,
    		$cityid,
            $phone,
    		$contact){
        $em = $this->getEntityManager();
        
        $openingTime = explode(" ", $openingTime);
        $closingTime = explode(" ", $closingTime);
        $acTime = explode(" ", $acTime);
        $open = (count($openingTime) == 2)?$openingTime[1]:$openingTime[4];
        $close = (count($openingTime) == 2)?$closingTime[1]:$closingTime[4];
        $actime = (count($acTime) == 2)?$acTime[1]:$acTime[4];
        $openingTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s', '2000-01-01'.$open);
        $openingTimeFormat->setTime($openingTimeFormat->format('H'),$openingTimeFormat->format('i'),0);
        $closingTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s',  '2000-01-01'.$close);
        $closingTimeFormat->setTime($closingTimeFormat->format('H'),$closingTimeFormat->format('i'),0);
        $acTimeFormat = DateTime::createFromFormat('Y-m-d H:i:s',  '2000-01-01'.$actime);
        $acTimeFormat->setTime($acTimeFormat->format('H'),$acTimeFormat->format('i'),0);
        
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $controllerId = "" + $this->_cr['id'];
        
        if($controllerId){
            $controller = $em->getRepository('DefaultDb_Entities_User')->find($controllerId);
        }
        
        if($categoryId){
            $categoryRepo = $em->find('DefaultDb_Entities_Category', $categoryId);
        }
        $addressRepo = null;
        if($address){
            $addressRepo = $em->find('DefaultDb_Entities_Address', $address);
        }

        if($id==null){            
            $point = new DefaultDb_Entities_Point();
            $point->setCode($code);
            $point->setName($name);
            $point->setType($type);
            $point->setStatus($status);
            $point->setController($controller);
            $point->setOpeningTime($openingTimeFormat);
            $point->setClosingTime($closingTimeFormat);
            $point->setAcTime($acTimeFormat);
            $point->setComments($comments);
            $point->setDeleted(1);
            $point->setWebpage($webpage);
            $point->setExtNumber($extNumber);
            $point->setIntNumber($intNumber);
            $point->setUrlGoogleMaps($urlGoogleMaps);
            $point->setCategotyId($categoryRepo);
            $point->setAddress($addressRepo);
            $point->setZipcode($zipcode);
            $point->setNeighborhood($neighborhood);
            $point->setCountry($countryid);
            $point->setState($stateid);
            $point->setCity($cityid);
            $point->setPhone($phone);
            $point->setContact($contact);
            $em->persist($point);
            $em->flush();
            
            $branch = new DefaultDb_Entities_BranchesUser;
            $client = $em->getRepository('DefaultDb_Entities_User')->findOneByType(DefaultDb_Entities_User::USER_CLIENT_MAS_DISTRIBUCION); 
            if($client)
            {                
                $branch->setClient($client);
                $branch->setDirection($address);
                $branch->setName($name);
                $branch->setPoint($point);
                $em->persist($branch);
            	$em->flush();
            }
        }else{
            $point = $this->find($id);
            if($point){
                $point->setCode($code);
                $point->setName($name);
                $point->setType($type);
                $point->setStatus($status);
                $point->setController($controller);
	            $point->setOpeningTime($openingTimeFormat);
	            $point->setClosingTime($closingTimeFormat);
            	$point->setAcTime($acTimeFormat);
	            $point->setComments($comments);
                $point->setDeleted(1);
                $point->setWebpage($webpage);
                $point->setExtNumber($extNumber);
                $point->setIntNumber($intNumber);
                $point->setUrlGoogleMaps($urlGoogleMaps);
                $point->setCategotyId($categoryRepo);
                $point->setAddress($addressRepo);
                $point->setZipcode($zipcode);
                $point->setNeighborhood($neighborhood);
                $point->setCountry($countryid);
                $point->setState($stateid);
                $point->setCity($cityid);
            	$point->setPhone($phone);
            	$point->setContact($contact);
                $em->flush();
            }
        }
        return $point->getId();
    }
    
    public function getCodePointListDQL($id, $flag = NULL){
        $em = $this->getEntityManager();
                
        //CON ESTE CODIGO SACAMOS LA SESIÃ“N DEL USUARIO
        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $controllerId = "" + $this->_cr['id'];
        
        $query = '
            SELECT code
            FROM points
            WHERE';
		$query .= ' id=:id AND deleted=true';
		if($flag == NULL){
			$query .=' AND controller_id=:controllerId';
		}
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindValue(":id", $id);
		if($flag == NULL){
        	$stmt->bindValue(":controllerId", $controllerId);
		}
		$stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUnuserPoints($categoryId){///OBTENER LOS PUNTOS QUE NO ESTAN ASIGNADOS A UN USUARIO CLIENTE
    	$em = $this->getEntityManager();
    	 
    	$dbs = Model3_Registry::getInstance()->get('databases');
    	$em1 = $dbs['DefaultDb'];
    	 
        $query = "
            SELECT p.id,p.code,p.name,urlGoogleMaps,
                CONCAT(a.address,' ', p.extNumber, ', ', p.neighborhood,' ', p.zipcode, ', ', st.name) address
            FROM points p LEFT JOIN address a ON p.address_id=a.id
            LEFT JOIN states st ON p.state_id = st.id
            WHERE
                categoryId_id=:id
                AND p.id not in(select point_id from branches_user where client_id <> 24 and point_id is not NULL )
                AND p.id in(select distinct(point_id) FROM route_points)
        		ORDER BY name
            ";
    	 
    	$conn = $em->getConnection()->getWrappedConnection();
    	$stmt = $conn->prepare($query);
    	$stmt->bindValue(":id", $categoryId);
    	 
    	$stmt->execute();
    	return $stmt->fetchAll(PDO::FETCH_NAMED);
    }    public function upBranchesP($pid,$name){    	$em = $this->getEntityManager();        	$query="    			UPDATE branches_user bu SET bu.name = :name                WHERE bu.point_id=:pid        ";        	$conn = $em->getConnection()->getWrappedConnection();    	$stmt = $conn->prepare($query);        	$stmt->bindValue(":pid", $pid);    	$stmt->bindValue(":name", $name);        	$stmt->execute();    	$result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);        	return array('data'=>"",'meta'=>array('totalRecords'=>""));    }

    public function createRoutepoint($idroute,$idpoint,$last){
    	$em = $this->getEntityManager();
    	$query="
    			INSERT INTO route_points(route_id,point_id,order_number,status,arrival_time,required)
    			 VALUES(:route_id,:point_id,:order,:status,:arrival,:required)
    			";
    	$conn = $em->getConnection()->getWrappedConnection();
    	$stmt = $conn->prepare($query);
    	$stmt->bindValue(":route_id", $idroute);
    	$stmt->bindValue(":point_id", $idpoint);
    	$stmt->bindValue(":order", $last+1);
    	$stmt->bindValue(":status", '1');
    	$stmt->bindValue(":arrival", '00:00:10');
    	$stmt->bindValue(":required", '0');
    	$stmt->execute();
    	$result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
    	return array('data'=>"",'meta'=>array('totalRecords'=>""));
    }
    
    public function getmaxPosition($idroute){
    	$em = $this->getEntityManager();
    	$query = 'SELECT MAX(order_number) as lasto FROM route_points
    			WHERE route_id =:idroute
		';
    	$conn = $em->getConnection()->getWrappedConnection();
    	$stmt = $conn->prepare($query);
    	$stmt->bindValue(":idroute", $idroute);
    	$stmt->execute();
    	return $stmt->fetchAll();
    }
}
