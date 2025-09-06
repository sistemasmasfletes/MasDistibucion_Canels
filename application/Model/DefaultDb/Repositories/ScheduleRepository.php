<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ScheduleRepository extends EntityRepository
{

    public function getNextSchedulesByRouteAndPointBranch($userId, $pointIdOrigin, $limit = 3, $recurrent = true)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $stringRecurrent=$recurrent===false?'':' OR s.recurrent = 1 ';
        $query = '
                SELECT s.*
                FROM schedule s
                INNER JOIN route_points rp ON rp.route_id = s.route_id
                #INNER JOIN users u ON u.point_id = rp.point_id
                INNER JOIN branches_user u ON u.point_id = rp.point_id 
                WHERE 
                ( 
                TIMESTAMP(s.start_date) > DATE(NOW()) 
                ' . $stringRecurrent . '
                )
                    AND u.client_id = '.$userId.'
                    AND rp.point_id = '.$pointIdOrigin.'
                    AND rp.status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL . '
                    AND s.status = ' . DefaultDb_Entities_Schedule::STATUS_ACTIVE . '
                ORDER BY s.start_date 
                LIMIT '.$limit.'
            ';
  //      echo $query;
        $res = $conn->executeQuery($query);
        $array = $res->fetchAll();
        return $array;
    }

    public function getScheduledDates($clientId,$pointId,$dateParam){
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = "
        SELECT s.schedule_id id, s.scheduled_date start_date, s.route_id, r.name FROM scheduled_route s
        LEFT JOIN routes r ON s.route_id = r.id 
        WHERE s.schedule_id in(
        SELECT s.id
                        FROM schedule s
                        INNER JOIN route_points rp ON rp.route_id = s.route_id
                        LEFT JOIN branches_user u ON u.point_id = rp.point_id
                        WHERE 
                        ( s.start_date > :dateParam or :dateParam BETWEEN s.start_date and s.end_date)
                        AND (:clientId IS NULL OR u.client_id = :clientId)
                        AND rp.point_id = :pointId
                        AND rp.status = 1
                        AND s.status = 1
         )
        AND s.scheduled_date >=:dateParam /*between :dateParam and DATE_ADD(:dateParam,INTERVAL 2 DAY)*/ /*CON ESTO SE LIMITA LA CANTIDAD DE DIAS VISIBLES EN LA LISTA DE FECHAS A ELEGIR PARA PORGRAMAR UN PAQUETE*/
        AND s.statusRoute is null /* ESTO VERIFICA SI LA RUTA YA INICIO PARA LIMITAR EL LISTADO A MOSTRAR*/
        ORDER BY s.scheduled_date 
        ";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(":clientId",$clientId);
        $stmt->bindValue(":pointId",$pointId);
        //$stmt->bindValue(":dateParam",$dateParam->format('Y-m-d H:i:s')); ESTO LIMITA EL LISTADO VERIFICANDO LA HORA ACTUAL
        $stmt->bindValue(":dateParam",$dateParam->format('Y-m-d'));
        $stmt->execute();        
        $array = $stmt->fetchAll(PDO::FETCH_NAMED);
        return $array;        
    }
    
    /**
     * 
     * @param type $userId
     * @param type $pointIdOrigin
     * @param DateTime $date
     * @param type $limit
     * @param type $recurrent
     * @return type
     */
    public function getNextSchedulesByRouteAndPointBranchAndDate($userId, $pointIdOrigin, $date,$limit = 3, $recurrent = true)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $stringRecurrent=$recurrent===false?'':' OR s.recurrent = 1 ';
        $query = '
                SELECT s.*
                FROM schedule s
                INNER JOIN route_points rp ON rp.route_id = s.route_id
                INNER JOIN branches_user u ON u.point_id = rp.point_id 
                WHERE 
                ( 
                DATE(s.start_date) >= DATE(\''.$date->format('Y-m-d').'\') 
                ' . $stringRecurrent . '
                )
                    AND u.client_id = '.$userId.'
                    AND rp.point_id = '.$pointIdOrigin.'
                    AND rp.status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL . '
                ORDER BY s.start_date 
                LIMIT '.$limit.'
            ';
        $res = $conn->executeQuery($query);
        $res = $res->fetchAll();
        return $res;
    }
    
    
    public function getNextSchedulesByPointId($pointId) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
        SELECT s.*
    FROM schedule s
    INNER JOIN route_points rp ON rp.route_id = s.route_id
    WHERE date(s.start_date) > date(NOW())
        AND rp.point_id = ' . $pointId .'
    ORDER BY s.start_date';
        $res = $conn->executeQuery($query);
        $res = $res->fetchAll();
        return $res;
    }
    
    public function getNextSchedulesByRouteByDate($route,$date) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
        SELECT s.*
    FROM schedule s
    WHERE 
        ( timestamp(s.start_date) >= timestamp(\''.$date->format('Y-m-d H:i:s').'\')
                OR s.recurrent = 1 )
        AND s.route_id = '.$route->getId().'
                AND s.status = ' . DefaultDb_Entities_Schedule::STATUS_ACTIVE . '
    ORDER BY s.start_date';
        $res = $conn->executeQuery($query);
        $res = $res->fetchAll();
        return $res;
    }

    /**
     * Se calcula el tiempo a parti del siguiente algoritomo, SE REGRESA EN MILISEGUNDOS.
     * 1. se obtienen todos los puntos de mi ruta, ordenados hasta mi punto de distribucion ($myPointOrder)
     * 2. de ests puntos se suma el tiempo (NO se toma en cuenta el punto 1, porque el tiempo indica del ultimo punto hacia el )
     * 3. se cuentan cuantos puntos son hacia mi punto y se multiplica por la constante de tiempo de configuracion.
     *
     * @param integer $routeId
     * @param integer $myPointOrder
     * @param integer $orderInicial Se Utiliza para sabaer en que orden esta el punto inicial ya que no siempre es el 1
     * @return integer
     */

    public function calculeShippidTime($routeId,$myPointOrder,$orderInicial=1)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();

        $query = '
                SELECT # * # calculando el bussness
                    time + (numPoints * secondsPerPoint) timeInSeconds
                FROM (
                    SELECT SUM(time) time
                    FROM (
                        SELECT TIME_TO_SEC(arrival_time) time
                        FROM route_points
                        WHERE route_id = '.$routeId.'
                            # se descarta el punto inicial
                            AND order_number > '.$orderInicial.'
                            AND order_number <= '.$myPointOrder.'
                            AND status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL .'    
                        ORDER BY order_number
                        ) time
                ) time
                INNER JOIN
                (
                    SELECT count(id) numPoints
                    FROM route_points
                    WHERE route_id = '.$routeId.'
                        # se descarta el punto inicial y mi punto
                        AND order_number > '.$orderInicial.'
                        AND order_number <= '.$myPointOrder.'
                        AND status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL .'
                    ORDER BY order_number
                ) numPoints
                INNER JOIN
                (
                    SELECT minutes_per_point * 60  secondsPerPoint
                    FROM configurations
                    WHERE id = 1
                ) timePerPoint
            ';
        
        $res = $conn->executeQuery($query);
        $res = $res->fetch(PDO::FETCH_NUM);	
        return $res;
    }

    public function deleteSheduleDate($scheduled)
    {
        $em = $this->getEntityManager(); 
        $em->remove($scheduled);         
        $em->flush();
    }
    
     
    /**
     * Regresa el centro de intercambio donde se intersectan dos rutas, y el orden maximo de esa ruta
     * para posteriormente calcular el tiempo que tarda en dar la vuelta completa una ruta
     * @param type $route
     * @param type $secondRoute
     * @return type
     */
    public function getIntersectionAndMaxRoutePointExchangeCenter($route,$secondRoute)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
         #SELECCIONA LA INTERSECCION DE DOS RUTAS Y REGRESA EL PUNTO MAXIMO DE LA PRIMERA RUTA
        SELECT A.idpoint,A.name,A.type,A.idroutepoint,A.route_id,max(A.idroutepoint) AS max_id_routepoint from 
        (
                SELECT * FROM (
                        SELECT id AS idpoint,name,type from points WHERE type = '.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'
                        AND status = ' . DefaultDb_Entities_Point::STATUS_NORMAL . '
                        ) pa 
                LEFT JOIN (
                        SELECT id AS idroutepoint,route_id,point_id,order_number FROM route_points
                        WHERE route_id ='.$route->getId().'
                        AND status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL . '    
                        ) rta
                ON pa.idpoint = point_id WHERE rta.route_id ='.$route->getId().'
        ) A
        INNER JOIN 
        (
                SELECT * FROM (
                        SELECT id AS idpoint,name,type from points WHERE type = '.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'
                        AND status = ' . DefaultDb_Entities_Point::STATUS_NORMAL . '
                        ) pa 
                LEFT JOIN (
                        SELECT id as idroutepoint,route_id,point_id,order_number FROM route_points
                        WHERE route_id ='.$secondRoute->getId().'
                        AND status = ' . DefaultDb_Entities_RoutePoint::STATUS_NORMAL . '   
                        ) rta
                ON pa.idpoint = point_id WHERE rta.route_id ='.$secondRoute->getId().'
        )
        B
        ON A.idpoint = B.idpoint LIMIT 0,1';
        $res = $conn->executeQuery($query);
        $res = $res->fetch();
        return $res;
        
    }
    
    /**
     *  No esta siendo utilizado
     * @param type $route
     * @return type
     */
    public function getNextRoute($route)
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $query = '
        SELECT DISTINCT route_id 
        FROM route_points 
        WHERE point_id = (SELECT DISTINCT idpoint FROM (
                SELECT id AS idpoint,name,type from points WHERE type = '.DefaultDb_Entities_Point::TYPE_EXCHANGE_CENTER.'
                ) pa 
            LEFT JOIN (
                SELECT id AS idroutepoint,route_id,point_id,order_number FROM route_points
                WHERE route_id ='.$route->getId().'
                ) rta
            ON pa.idpoint = point_id WHERE rta.route_id ='.$route->getId().')
        and route_id <> '.$route->getId();
        
        $res = $conn->executeQuery($query);
        $res = $res->fetch();
        return $res;  
    }
    
    /**
     * Regresa un schedule por usuario y fecha del schedule
     * @param DefaultDb_Entities_User $user Usuario a buscar
     * @param dateTime $date Fecha a buscar 
     * @return mixed Regresa un schedule o null
     */
    public function getScheduleByUserAndDate($user,$date) {
        $em = $this->getEntityManager();      
        $dql = '
        SELECT s
        FROM DefaultDb_Entities_Schedule s 
        WHERE s.user = '.$user->getId().'
        AND( s.startDate BETWEEN \''.$date->format('Y-m-d 00:00:00').'\' AND\''.$date->format('Y-m-d 23:59:59').'\' )
        AND s.status = '.DefaultDb_Entities_Schedule::STATUS_ACTIVE.'
    ORDER BY s.startDate ';
        $query = $em->createQuery($dql)->setMaxResults(1);
        $res = $query->getOneOrNullResult();
        return $res;
    }

    //Obtiene la lista de rutas programadas
    public function getScheduledRouteList($user_id,$page,$rowsPerPage,$sortField,$sortDir,$routeName){
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        //********  Programaciones recurrentes  ******** 
        //Obtener las rutas que pertenecen al usuario logeado y sus programaciones recurrentes       
        

        $sql = "
            SELECT  s.id,r.id routeId,s.start_date,
            CONCAT('[',r.code,']',' ',r.name) routeName,v.id vehicleId,v.name vehicleName,u.id driverId,CONCAT(u.first_name,' ',u.last_name) driverName
            FROM schedule  s
            INNER JOIN
                (SELECT route_id,max(start_date) start_date 
                    FROM schedule GROUP BY route_id
                ) s1 ON s.route_id = s1.route_id AND s.start_date = s1.start_date
            LEFT JOIN routes r on s.route_id = r.id
            LEFT JOIN vehicles v on s.vehicle_id = v.id
            LEFT JOIN users u on s.user_id = u.id
            WHERE s.status=1
            AND r.controller_id=:controllerId
        ";
        $statement = $conn->prepare($sql);
        $statement->bindValue("controllerId",$user_id);
        $statement->execute();
        //DBUtil::getResultsetFromStatement($statement, \PDO::FETCH_NAMED);
        $rows = $statement->fetchAll(PDO::FETCH_NAMED);
        unset($conn);
        return $rows;
             
    }

    public function generateScheduleDetailList($scheduleRow,$scheduleStartDate){
        $scheduleId = isset($scheduleRow["id"]) ? $scheduleRow["id"] : null;
        $userId = isset($scheduleRow["user_id"]) ? $scheduleRow["user_id"] : null;        
        $vehicleId = isset($scheduleRow["vehicle_id"]) ? $scheduleRow["vehicle_id"] : null; 
        $routeId = isset($scheduleRow["route_id"]) ? $scheduleRow["route_id"] : null;
        $startDate = $scheduleRow["startDate"];
        $endDate = $scheduleRow["endDate"];
        $recurrent = (int)$scheduleRow["recurrent"];

        $week = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        
        $scheduledResult=array();        

        if($recurrent==0){
            $scheduledResult=array(
                    "scheduleId"=>$scheduleId
                    ,"scheduleNum"=>1
                    ,"scheduledDate"=>$startDate->format('Y-m-d H:i:s')
                    ,"routeId"=>$routeId
                    ,"userId"=>$userId
                    ,"vehicleId"=>$vehicleId
            );
            return array($scheduledResult);
        }

        $lastWeekDay = (int)$endDate->Format('w');

        $weekDay = (int) $scheduleStartDate->Format('w');
        $numDays = count($week);
        $numRecord=1;
        $endDate->modify("+1 day");
        while($scheduleStartDate<$endDate){         
            if($scheduleRow[$week[$weekDay]]==1){
                $scheduledResult[]=array(
                    "scheduleId"=>$scheduleId
                    ,"scheduleNum"=>$numRecord
                    ,"scheduledDate"=>$scheduleStartDate->format('Y-m-d H:i:s')
                    ,"routeId"=>$routeId
                    ,"userId"=>$userId
                    ,"vehicleId"=>$vehicleId);

                 $numRecord++;
            }
            $scheduleStartDate->modify("+1 day");            
            $weekDay=(int)$scheduleStartDate->Format('w');            
        }
        return $scheduledResult;
    }

    /*
    Genera el detalle de programaciones repetitivas dado un registro de programación y fecha
    */
    public function generateScheduleDetail($scheduleId,$scheduleStartDate=null){
        $em = $this->getEntityManager();
        
        $conn = $em->getConnection()->getWrappedConnection();
        $parameters = array($scheduleId);
        $resultsets = DBUtil::executeStoredProc($conn, 'sp_generateScheduledRoute', $parameters, PDO::FETCH_NAMED);
        unset($conn);
        return($resultsets[0][0]['schedulesCreated']);
    }       

    //Métodos para persistencia
    public function save($id,$route_id,$vehicle_id,$user_id,$start_date,$status,$monday,$tuesday,$wednesday,$thursday,$friday,
        $saturday,$sunday,$recurrent,$week,$scheduleParent_id,$end_date){

        $start_date_dt = DateTime::createFromFormat('Y-m-d H:i:s', $start_date);
        $end_date_dt = DateTime::createFromFormat('Y-m-d H:i:s', $end_date);

        $em = $this->getEntityManager();

        if($start_date_dt==null)
            DBUtil::generateUserExceptionWithMessage("Error en la fecha inicial.");

        $start_date_dt->setTime($start_date_dt->format('H'),$start_date_dt->format('i'),0);

        if($recurrent==1){
            if($start_date_dt > $end_date_dt)
                    DBUtil::generateUserExceptionWithMessage("La fecha inicial de la programación debe ser menor a la fecha final.");

            if(($sunday+$monday+$tuesday+$wednesday+$thursday+$friday+$saturday)==0)
                DBUtil::generateUserExceptionWithMessage("Cuando la programación es periódica debe seleccionar al menos un día de la semana.");

            $end_date_dt->setTime($end_date_dt->format('H'),$end_date_dt->format('i'),0);
        }
        

        $obSchedule = null;
        $generateScheduledDates = true;      

        if($id==null){
            //Verificar que no se traslapen fechas
            if($recurrent==1){
                //Determinar que las fechas no se traslapen
                $dql = "SELECT COUNT(s.id) cnt FROM DefaultDb_Entities_Schedule s 
                                WHERE 
                                    s.route = :route_id                        
                                AND ((DATE(:start_date) BETWEEN DATE(s.startDate) AND DATE(s.endDate))
                                OR (DATE(:end_date) BETWEEN DATE(s.startDate) AND DATE(s.endDate))
                                )";
                                
                $query = $em->createQuery($dql);
                $query->setParameter("route_id", $route_id);
                $query->setParameter("start_date", $start_date_dt);
                $query->setParameter("end_date", $end_date_dt);
                $count = $query->getSingleScalarResult();

                if($count>0){
                    DBUtil::generateUserExceptionWithMessage("Las fechas se traslapan con una programación previamente capturada");
                }
                    
            }else{
                //Verificar que la programación no esté repetida
                $dql = "SELECT COUNT(sr.id) FROM  DefaultDb_Entities_ScheduledRoute sr 
                        WHERE sr.route = :routeId AND DATE(sr.scheduledDate)=DATE(:scheduledDate)";

                $query = $em->createQuery($dql);
                $query->setParameter("routeId", $route_id);
                $query->setParameter("scheduledDate", $start_date_dt);
                $count = $query->getSingleScalarResult();
                if($count>0)
                    DBUtil::generateUserExceptionWithMessage("Ya existe una programación para la ruta y fecha dados.");
            }

            //Referencias Foreign keys
            $dql ="SELECT partial r.{id} FROM DefaultDb_Entities_Route r WHERE r = :route_id";
            $query = $em->createQuery($dql);
            $query->setParameter("route_id", $route_id);
            $arrRoute = $query->getResult();
            $obRoute = $arrRoute ? $arrRoute[0] : null;

            $dql ="SELECT partial v.{id} FROM DefaultDb_Entities_Vehicle v WHERE v = :vehicle_id";
            $query = $em->createQuery($dql);
            $query->setParameter("vehicle_id", $vehicle_id);
            $arrVehicle = $query->getResult();
            $obVehicle = $arrVehicle ? $arrVehicle[0] : null;

            $dql ="SELECT partial u.{id} FROM DefaultDb_Entities_User u WHERE u = :user_id";
            $query = $em->createQuery($dql);
            $query->setParameter("user_id", $user_id);
            $arrUser = $query->getResult();
            $obUser = $arrUser ? $arrUser[0] : null;

            $obSchedule = new DefaultDb_Entities_Schedule();
            $obSchedule->setRoute($obRoute);
            $obSchedule->setVehicle($obVehicle);
            $obSchedule->setUser($obUser);
            $obSchedule->setStartDate($start_date_dt);
            $obSchedule->setEndDate($recurrent==1 ? $end_date_dt: null);
            $obSchedule->setStatus($status);
            $obSchedule->setMonday($monday);
            $obSchedule->setTuesday($tuesday);
            $obSchedule->setWednesday($wednesday);
            $obSchedule->setThursday($thursday);
            $obSchedule->setFriday($friday);
            $obSchedule->setSaturday($saturday);
            $obSchedule->setSunday($sunday);
            $obSchedule->setRecurrent($recurrent);
            $obSchedule->setWeek($week);
            $obSchedule->setScheduleParent(null);

            $em->persist($obSchedule);
            $em->flush();          
        }else{

            $obSchedule = $this->find($id);

            if($recurrent==1){
                //Determinar que las fechas no se traslapen
                $dql = "SELECT COUNT(s.id) cnt FROM DefaultDb_Entities_Schedule s 
                                WHERE 
                                    s.route = :route_id
                                AND s.id<>:scheduleId
                                AND ((DATE(:start_date) BETWEEN DATE(s.startDate) AND DATE(s.endDate))
                                OR (DATE(:end_date) BETWEEN DATE(s.startDate) AND DATE(s.endDate))
                                )";
                                
                $query = $em->createQuery($dql);
                $query->setParameter("route_id", $route_id);
                $query->setParameter("start_date", $start_date_dt);
                $query->setParameter("end_date", $end_date_dt);
                $query->setParameter("scheduleId", $id);
                $count = $query->getSingleScalarResult();

                if($count>0)
                    DBUtil::generateUserExceptionWithMessage("Las fechas se traslapan con una programación previamente capturada");

                //Solo generar fechas programadas si cambian los valores con respecto al registro original
                if($obSchedule->getStartDate() != $start_date_dt
                    || $obSchedule->getEndDate() != $end_date_dt
                    || $obSchedule->getRecurrent()!=$recurrent
                    || $obSchedule->getSunday()!=$sunday
                    || $obSchedule->getMonday()!=$monday
                    || $obSchedule->getTuesday()!=$tuesday
                    || $obSchedule->getWednesday()!=$wednesday
                    || $obSchedule->getThursday()!=$thursday
                    || $obSchedule->getFriday()!=$friday
                    || $obSchedule->getSaturday()!=$saturday
                    || ($obSchedule->getUser() &&  $obSchedule->getUser()->getId()!=$user_id)
                    || (!$obSchedule->getUser() && $user_id>0)
                    || ($obSchedule->getVehicle() && $obSchedule->getVehicle()->getId()!=$vehicle_id) 
                    || (!$obSchedule->getVehicle() && $vehicle_id>0)
                    ){

                    $schedulewithActivity = $this->getScheduledRouteWithActivity($id);
                
                    $arrIds = array();
                    $placeholder = '';
                    foreach ($schedulewithActivity as $key=>$scheduledRouteId) {
                        $arrIds[] = $scheduledRouteId["id"];
                        $placeholder.= ($key==0 ? '':',').'?';
                    }

                    if(count($schedulewithActivity)>0)
                        $sql="DELETE FROM scheduled_route WHERE schedule_id = ? AND id NOT IN (".$placeholder.")";
                    else
                        $sql="DELETE FROM scheduled_route WHERE schedule_id = ?";
                    
                    $conn = $em->getConnection()->getWrappedConnection();
                    $stmt = $conn->prepare($sql);
                
                    $stmt->bindValue(1,$id);
                    if(count($schedulewithActivity)>0)
                        foreach ($schedulewithActivity as $key=>$scheduledRouteId)
                            $stmt->bindValue($key+2,$scheduledRouteId["id"]);
                            
                    $stmt->execute();
                }else{
                    $generateScheduledDates = false;
                }
            }else{
                if($obSchedule->getStartDate() != $start_date_dt
                    || ($obSchedule->getUser() &&  $obSchedule->getUser()->getId()!=$user_id)
                    || (!$obSchedule->getUser() && $user_id>0)
                    || ($obSchedule->getVehicle() && $obSchedule->getVehicle()->getId()!=$vehicle_id)
                    || (!$obSchedule->getVehicle() && $vehicle_id>0)
                    ){
                    $scheduledDates = $this->getScheduledRouteWithActivity($id);
                    $count = count($scheduledDates);

                    if($count>0)
                        DBUtil::generateUserExceptionWithMessage("No es posible editar la programación debido a que ya existen actividades dependientes.");

                    //Verificar que la programación no esté repetida
                    $dql = "SELECT COUNT(sr.id) FROM  DefaultDb_Entities_ScheduledRoute sr 
                            WHERE sr.route = :routeId AND DATE(sr.scheduledDate)=DATE(:scheduledDate) AND sr.schedule<>:id";

                    $query = $em->createQuery($dql);
                    $query->setParameter("routeId", $route_id);
                    $query->setParameter("scheduledDate", $start_date_dt);
                    $query->setParameter("id", $id);
                    $count = $query->getSingleScalarResult();

                    if($count>0)
                        DBUtil::generateUserExceptionWithMessage("Ya existe una programación para la ruta y fecha dados.");

                    $sql="DELETE FROM scheduled_route WHERE schedule_id = :id";
                    $conn = $em->getConnection()->getWrappedConnection();
                    $stmt = $conn->prepare($sql);
             
                    $stmt->bindValue(":id",$id);         
                    $stmt->execute();
                }else{
                    $generateScheduledDates = false;
                }
            }

            if($obSchedule){
                //Referencias Foreign keys
                $dql ="SELECT partial r.{id} FROM DefaultDb_Entities_Route r WHERE r = :route_id";
                $query = $em->createQuery($dql);
                $query->setParameter("route_id", $route_id);
                $arrRoute = $query->getResult();
                $obRoute = $arrRoute ? $arrRoute[0] : null;

                $dql ="SELECT partial v.{id} FROM DefaultDb_Entities_Vehicle v WHERE v = :vehicle_id";
                $query = $em->createQuery($dql);
                $query->setParameter("vehicle_id", $vehicle_id);
                $arrVehicle = $query->getResult();
                $obVehicle = $arrVehicle ? $arrVehicle[0] : null;

                $dql ="SELECT partial u.{id} FROM DefaultDb_Entities_User u WHERE u = :user_id";
                $query = $em->createQuery($dql);
                $query->setParameter("user_id", $user_id);
                $arrUser = $query->getResult();
                $obUser = $arrUser ? $arrUser[0] : null;

                $obSchedule->setRoute($obRoute);
                $obSchedule->setVehicle($obVehicle);
                $obSchedule->setUser($obUser);
                $obSchedule->setStartDate($start_date_dt);
                $obSchedule->setEndDate($recurrent==1 ? $end_date_dt: null);
                $obSchedule->setStatus($status);
                $obSchedule->setMonday($monday);
                $obSchedule->setTuesday($tuesday);
                $obSchedule->setWednesday($wednesday);
                $obSchedule->setThursday($thursday);
                $obSchedule->setFriday($friday);
                $obSchedule->setSaturday($saturday);
                $obSchedule->setSunday($sunday);
                $obSchedule->setRecurrent($recurrent);
                $obSchedule->setWeek($week);
                $obSchedule->setScheduleParent(null);

                $em->flush();
                
            }            
        }
        $schedulesCreated = 0;
        if($obSchedule && $generateScheduledDates)
            $schedulesCreated = $this->generateScheduleDetail($obSchedule->getId());
        
        return $schedulesCreated;

    }

    public function getSchedules($page,$rowsPerPage,$sortField,$sortDir,$id,$routeName, $userId){
     
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
         
        $offset = ($page-1)*$rowsPerPage;
        $em = $this->getEntityManager();
 
        $selectFields = "r.id, r.code,r.name, CONCAT('[',code,']',' ',name) routeName";
        $sql = " SELECT [FIELDS] FROM routes r
                WHERE (:id IS NULL OR :id=0 OR id=:id)
                AND (:routeName IS NULL OR CONCAT('[',code,']',' ',name) LIKE :routeName)
                AND  status=1 
                AND r.zone_id IN  (SELECT zone_id FROM user_zone WHERE user_id = $userId)";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect.= ($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "";
        $querySelect.=" LIMIT ".$rowsPerPage.' OFFSET '.$offset;
 
        $querys = $querySelect.";".$queryCount;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
 
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":routeName",'%'.$routeName.'%');
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$totalRecords));
 
    }

    public function getScheduleDetail($page,$rowsPerPage,$sortField,$sortDir,$id,$routeId){
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
         
        $offset = ($page-1)*$rowsPerPage;
        $em = $this->getEntityManager();

        $selectFields = "s.*,CONCAT('[',r.code,']',' ',r.name) route,CONCAT(u.first_name,' ',u.last_name) driver, v.name vehicle";
        $sql = " SELECT [FIELDS] FROM schedule s
                LEFT JOIN vehicles v ON s.vehicle_id=v.id
                LEFT JOIN users u ON s.user_id=u.id
                LEFT JOIN routes r on s.route_id=r.id 
                WHERE
                (:id IS NULL OR s.id=:id)
                AND (:routeId IS NULL OR s.route_id = :routeId)                
                AND s.status=1 ";
         
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect.= ($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "";
        $querySelect.=" LIMIT ".$rowsPerPage.' OFFSET '.$offset;
 
        $querys = $querySelect.";".$queryCount;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
 
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":routeId",$routeId);
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$totalRecords));
    }

    public function getScheduledRouteWithActivity($scheduleId){

        $em = $this->getEntityManager();        
        $dql = "SELECT DISTINCT sr.id  FROM DefaultDb_Entities_RoutePointActivity rpa 
                    INNER JOIN rpa.scheduledRoute sr
                    WHERE sr.schedule = :scheduleId";

        $query = $em->createQuery($dql);
        $query->setParameter("scheduleId", $scheduleId);
        $schedRoutes = $query->getArrayResult();
        return $schedRoutes;        
    }

    public function delete($scheduleId){
        
        $em = $this->getEntityManager();
        
        $scheduleWithActivity = $this->getScheduledRouteWithActivity($scheduleId);
        $hasActivity = count($scheduleWithActivity)>0;
        
        if($hasActivity)
            DBUtil::generateUserExceptionWithMessage("No es posible eliminar la programación debido a que tiene fechas programadas con actividad.");
        
        //Buscar las fechas programadas que dependen de la programación.        
        $dql = "SELECT partial sr.{id} FROM DefaultDb_Entities_ScheduledRoute sr WHERE sr.schedule = :scheduleId";
        $query = $em->createQuery($dql);
        $query->setParameter("scheduleId", $scheduleId);
        $scheduledDates = $query->getResult();
     
        //Si las fechas programadas no tienen actividad, se eliminan antes que la programación.
        foreach ($scheduledDates as $key => $sd) {
            $em->remove($sd);
        }
        $schedule = $this->find($scheduleId);
        $em->remove($schedule);

        $em->flush();

        return 1;
    }

    public function getScheduledDatesList($page,$rowsPerPage,$sortField,$sortDir,$scheduleId){
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
         
        $offset = ($page-1)*$rowsPerPage;
        $em = $this->getEntityManager();
 
        $selectFields = "sr.id,sr.scheduled_date, sr.driver_id driverid,CONCAT(u.username,' ',u.first_name,' ',u.last_name) driver, 
                        vehicle_id vehicleid, v.name vehicle,
                        route_id routeid, CONCAT('[',r.code,'] ',r.name) route";
        $sql = " SELECT [FIELDS] FROM scheduled_route sr
                 LEFT JOIN vehicles v ON sr.vehicle_id = v.id
                 LEFT JOIN users u ON sr.driver_id = u.id
                 LEFT JOIN routes r ON sr.route_id = r.id 
                WHERE (:scheduleId IS NULL OR sr.schedule_id=:scheduleId)";
         
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect.= ($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "";
        $querySelect.=" LIMIT ".$rowsPerPage.' OFFSET '.$offset;
 
        $querys = $querySelect.";".$queryCount;
 
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
 
        $stmt->bindValue(":scheduleId",$scheduleId);       
 
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$totalRecords));
    }

    public function getNextScheduledDate($routeId, $date){
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $dt = null;
        $query = "SELECT scheduled_date FROM scheduled_route 
                    WHERE route_id=:routeId AND scheduled_date>=:dt ORDER BY scheduled_date ASC LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(":routeId",$routeId);
        $stmt->bindValue(":dt",$date->format('Y-m-d H:i:s'));
        $stmt->execute();        
        $result = $stmt->fetchAll(PDO::FETCH_NAMED);
        if(count($result)>0){
            $dt = $result[0]['scheduled_date'];
            $dt = $dt ? DateTime::createFromFormat('Y-m-d H:i:s',$dt) : null;
        }        
        return $dt;
    }

    public function getScheduledDate($scheduledDateId){
        $result = array();
        $em = $this->getEntityManager();
        $dql = 'SELECT sr,partial r.{id,code,name},partial v.{id,name},partial d.{id,firstName,lastName,username},partial s.{id} FROM DefaultDb_Entities_ScheduledRoute sr 
                LEFT JOIN sr.route r
                LEFT JOIN sr.vehicle v
                LEFT JOIN sr.driver d
                LEFT JOIN sr.schedule s
        WHERE sr.id = :scheduledDateId';
        $query = $em->createQuery($dql);
        $query->setParameter('scheduledDateId',$scheduledDateId);
        $schedule = $query->getOneOrNullResult();
        
        if($schedule!=null){
            $result["id"]=$schedule->getId();
            $result["routeid"]=$schedule->getRoute()->getId();
            $result["vehicleid"]=$schedule->getVehicle()->getId();
            $result["driverid"]=$schedule->getDriver()->getId();
            $result["scheduled_date"]=$schedule->getScheduledDate() ? $schedule->getScheduledDate()->format('Y-m-d H:i:s') : null;
            $result["scheduleid"]=$schedule->getSchedule()->getId();
            $result["schedulenum"]=$schedule->getScheduleNum();
            $result["status"]=$schedule->getStatus();
            $result["progress"]=$schedule->getProgress();
            $result["startDate"]=$schedule->getStartDate();
            $result["endDate"]=$schedule->getEndDate();
            $result["statusRoute"]=$schedule->getStatusRoute();
            $result["route"]=$schedule->getRoute()->getName();
            $result["vehicle"]=$schedule->getVehicle()->getName();
            $result["driver"]=$schedule->getDriver()->getUsername().' '. $schedule->getDriver()->getFirstName().' '.$schedule->getDriver()->getLastName();
        }
        return $result;
    }

    public function updateScheduledDate($params){
        $em = $this->getEntityManager();
        $result = 0;
        //Fecha programada
        $dql = 'SELECT partial sr.{id} FROM DefaultDb_Entities_ScheduledRoute sr
        WHERE sr.id = :scheduledDateId';
        $query = $em->createQuery($dql);
        $query->setParameter('scheduledDateId',$params["id"]);
        $schedule = $query->getOneOrNullResult();

        //Conductor
        $dql = ' SELECT partial u.{id} FROM DefaultDb_Entities_User u WHERE u.id = :userid';
        $query = $em->createQuery($dql);
        $query->setParameter('userid',$params["driverid"]);
        $driver = $query->getOneOrNullResult();

        //Vehículo
        $dql = ' SELECT partial v.{id} FROM DefaultDb_Entities_Vehicle v WHERE v.id = :vehicleid';
        $query = $em->createQuery($dql);
        $query->setParameter('vehicleid',$params["vehicleid"]);
        $vehicle = $query->getOneOrNullResult();

        if($schedule!=null){
            $schedule->setDriver($driver);
            $schedule->setVehicle($vehicle);
            $em->persist($schedule);
            $em->flush();
            $result = 1;
        }
        return $result;
    }
}