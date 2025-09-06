<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_RouteSummaryRepository extends EntityRepository
{
    //Rutas asignadas al chofer
    public function getRouteSummaryListDQL($page,$rowsPerPage,$sortField,$sortDir,$sidx,$id,$Fecha,$Hora,$Nombre, $Formato,$HoraActual,$Codigo,$Vehiculo, $Estado)
    {

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
                    s.id as id,
                    DATE_FORMAT(s.scheduled_date,' %d-%M-%Y') as Fecha,
                    DATE_FORMAT(s.scheduled_date,'%H:%i:%s') as Hora,
                    DATE_FORMAT(s.scheduled_date,'%m %d %Y %H:%i:%s') as Formato,
                    DATE_FORMAT(s.start_date,'%m %d %Y %H:%i:%s') as HoraActual,
                    s.end_date as endDate,
                    r.name as Nombre,
                    r.code as Codigo,
                    v.name as Vehiculo,
                    s.statusRoute as Estado,
                    u.first_name as driver
                ";
        
        $query="
            SELECT [FIELDS]
                FROM scheduled_route s
                        inner join routes r on s.route_id =r.id
                        inner join vehicles v on s.vehicle_id = v.id
                        inner join users u on s.driver_id = u.id
                WHERE DATE_FORMAT(s.scheduled_date, '%Y-%m-%d') <= CURDATE() and u.id = '".$usuario."'
            AND (:Fecha IS NULL OR s.scheduled_date LIKE :Fecha)
            AND (:Name IS NULL OR r.name LIKE :Name)
            AND (:Vehiculo IS NULL OR v.name LIKE :Vehiculo)
            #AND s.statusRoute IS NULL #NO INICIADAS
			#AND s.statusRoute = 2 # SOLO FINALIZADAS
			#AND s.statusRoute IS NOT NULL # EN PROCESO Y FINALIZADAS
			     [ORDERBY]
            [LIMT]
        ";
        //  CONDICIÃ“N 1:  SE USARA DURANTE LAS PRUEBAS ESTO ES PARA EL LISTADO DE RUTAS DEL CHOFER
        //  WHERE s.scheduled_date >= CURDATE() and u.id = '".$usuario."'
        //  CONDICIÃ“N 2: SE USARA AL IMPLEMENTAR
        //  WHERE DATE_FORMAT(s.scheduled_date, '%Y-%m-%d') = CURDATE() and u.id = '".$usuario."'
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        
        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue(':Fecha', ($Fecha==null) ? null : '%'.$Fecha.'%');
        $stmt->bindValue(':Name', ($Nombre==null) ? null : '%'.$Nombre.'%');
        $stmt->bindValue(':Vehiculo', ($Vehiculo==null) ? null : '%'.$Vehiculo.'%');
 
        $stmt->execute();
 
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    //Puntos de la ruta
    public function getRouteSummaryPointsListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$Codigo,$Hora,$Formato,$Nombre,$Direccion,$Actividad,$Comentarios,$HoraActual,$Progreso,$idrow,$idp,$stateParams,$id,$id2){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;

       // $query ="call sp_pointsDriver_listar(".$idrow.", '$sortField', '$sortDir', '%$Nombre%', '%$Codigo%');";

        $query = "
		SET @routeId:= (SELECT  max(route_id) FROM scheduled_route WHERE id =".$idrow.");
		SET @p_start:= (SELECT  max(scheduled_date) FROM scheduled_route WHERE start_date IS NOT NULL AND id =".$idrow.");
		SET @retVal = @p_start;
				
			DROP TABLE IF EXISTS _pointsCor;
			CREATE TEMPORARY TABLE IF NOT EXISTS _pointsCor AS 
			(
		SELECT DISTINCT
			/*scheduleRouteId AS Identificador,*/
			rp.route_id AS Route_ID,
			p.id AS Point_Id,	
				
		DATE_FORMAT(
		(SELECT ADDTIME(@retVal, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=@routeId and rp1.order_number <rp.order_number)))
		,GET_FORMAT(TIME,'ISO')) AS Hora,
						
		DATE_FORMAT(
		(SELECT ADDTIME(@retVal, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=@routeId and rp1.order_number <rp.order_number)))
		,'%m %d %Y %H:%i:%s') AS Formato,
				
		DATE_FORMAT(
		(SELECT ADDTIME(@retVal, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=@routeId and rp1.order_number <rp.order_number)))
		,'%d %M %Y') AS hourProgram,
				
			rp.arrival_time AS arrTime,
			p.urlGoogleMaps as url,
			p.name AS Nombre,
			CONCAT(a.address, p.extNumber, ', ', a.neighborhood, a.zipcode) AS Direccion,
			rp.order_number AS order_number,
			p.code AS Codigo,
			func_validarPunto(".$idrow.",rp.id,func_time_list(@routeId,rp.order_number,@p_start)) AS validar,
			DATE_FORMAT(cast(hora_actual_punto(".$idrow.",rp.id,p.id,func_time_list(@routeId,rp.order_number,@p_start)) AS DATETIME),GET_FORMAT(TIME,'ISO')) AS HoraActual,
			r.name AS ruta,
			rp.id AS routePoint_id,
			rp.status
		FROM points p
		INNER JOIN route_points rp ON p.id=rp.point_id
		INNER JOIN routepoint_activity rpa  ON rp.id = rpa.routePoint_id /*Desde qu� cambie left por inner*/
		LEFT JOIN scheduled_route sr ON rpa.scheduledRoute_id=sr.id
		LEFT JOIN activity_type act ON act.id = rpa.activityType_id
		LEFT JOIN transactions trans ON trans.id = rpa.transaction_id
		LEFT JOIN routes r ON rp.route_id=r.id
		LEFT JOIN address a ON p.address_id=a.id
		WHERE rp.route_id=@routeId AND rp.status=1
                    AND func_validarPunto(".$idrow.",rp.id,func_time_list(@routeId,rp.order_number,@p_start))>0
                    AND sr.id=".$idrow." 
                    AND trans.status NOT IN(5,6) -- Detenido, rechazado
		ORDER BY  rpa.hora_actual , rpa.date ASC  , rpa.routePoint_id ASC);
		
			SELECT * FROM _pointsCor 
			WHERE      ('%".$Nombre."%' IS NULL OR Nombre LIKE '%".$Nombre."%')
		    AND ('%".$Codigo."%' IS NULL OR Codigo LIKE '%".$Codigo."%') 
		    ORDER BY  HoraActual , Hora ASC  , hourProgram ;
					
			SELECT 
		    COUNT(*) totalRecords,
		    1 page,
		    CEIL(COUNT(*) / 100) totalpages
		FROM
		    _pointsCor;        		
        		";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    public function getRouteSummaryPointsListDQL1($page,$rowsPerPage,$sortField,$sortDir,$id,$Codigo,$Hora,$Formato,$Nombre,$Direccion,$Actividad,$Comentarios,$HoraActual,$Progreso,$idrow,$idp,$stateParams,$id,$id2){
    	$em = $this->getEntityManager();
    
    	if($page==null) $page=1;
    	if($rowsPerPage==null) $rowsPerPage=10;
    
    	$offset = ($page-1)*$rowsPerPage;
    
    	// $query ="call sp_pointsDriver_listar(".$idrow.", '$sortField', '$sortDir', '%$Nombre%', '%$Codigo%');";
    
    	$query = "
			 
			SELECT
				rp.route_id AS Route_ID,
				p.id AS Point_Id,
				sr.id,
			DATE_FORMAT(
					(SELECT ADDTIME(sr.scheduled_date, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=sr.route_id and rp1.order_number <rp.order_number)))
					,GET_FORMAT(TIME,'ISO')) AS Hora,
			DATE_FORMAT(
					(SELECT ADDTIME(sr.scheduled_date, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=sr.route_id and rp1.order_number <rp.order_number)))
					,'%m %d %Y %H:%i:%s') AS Formato,
			DATE_FORMAT(
					(SELECT ADDTIME(sr.scheduled_date, (select CAST(SEC_TO_TIME(SUM(rp1.arrival_time)) as CHAR) from route_points rp1 where rp1.route_id=sr.route_id and rp1.order_number <rp.order_number)))
				,'%d %M %Y') AS hourProgram,
			rp.arrival_time AS arrTime,
			p.urlGoogleMaps as url,p.name AS Nombre,CONCAT(a.address, p.extNumber, ', ', a.neighborhood, a.zipcode) AS Direccion, p.code AS Codigo,
			rp.order_number AS order_number,
			func_validarPunto(".$idrow.",rp.id,func_time_list(sr.route_id,rp.order_number,sr.scheduled_date)) AS validar,
			r.name AS ruta,
			rp.id AS routePoint_id,
			rp.status,
			DATE_FORMAT(cast(hora_actual_punto(".$idrow.",rp.id,p.id,func_time_list(sr.route_id,rp.order_number,sr.scheduled_date)) AS DATETIME),GET_FORMAT(TIME,'ISO')) AS HoraActual
			
			FROM scheduled_route sr
			left join routepoint_activity rpa on sr.id = rpa.scheduledRoute_id
			left join route_points rp on rp.id = rpa.routePoint_id
			left join points p on p.id = rp.point_id
			left join address a ON p.address_id=a.id
			left join routes r on rp.route_id = r.id
			left join transactions trans on trans.id = rpa.transaction_id
			where sr.id =".$idrow."
            and func_validarPunto(".$idrow.",rp.id,func_time_list(@routeId,rp.order_number,sr.scheduled_date))>0
            and trans.status NOT IN(5,6) -- Detenido, rechazado
			group by rp.order_number
			#group by p.id
			ORDER BY HoraActual,rp.order_number ASC;
            #ORDER BY HoraActual , Hora ASC  , hourProgram;
    
			SELECT
			COUNT(*) totalRecords,
			1 page,
			CEIL(COUNT(*) / 100) totalpages
			FROM scheduled_route sr
			left join routepoint_activity rpa on sr.id = rpa.scheduledRoute_id
			left join route_points rp on rp.id = rpa.routePoint_id
			left join points p on p.id = rp.point_id
			   
			where sr.id =".$idrow."
			group by rp.order_number
			#group by p.id
			;
		";
    
    	$conn = $em->getConnection()->getWrappedConnection();
    	$stmt = $conn->prepare($query);
    
    	$stmt->execute();
    	$result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
    
    	$totalrecords = (count($result[0] ) === 0)?0:count($result[1]);
    	
    	return array('data'=>$result[0],'meta'=>array('totalRecords'=>$totalrecords));
    	//return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }    
    
    //OBTENER PAQUETES A ENTREGAR O RECOLECTAR DEL CHOFER
    public function getRouteSummaryPackageListDQL ($page,$rowsPerPage,$sortField,$sortDir,$id2,$id,$idSra,$Paquete,$Estado,$Actividad,$idrow,$idrow2,$routePointActivityId){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = "
            routePointActivityId,
            scheduleId,
            routePoint_id,

            id,
            Paquete,
            Actividad,
            horaReal,
            Estado,
            name,
            pointType,
            receptor,
            status,    
            curpoint,    
            endpoint,
            packagePromotion,
            promotionid,
            numfiles,
            promotionpath,
            promotionscheduleid,
            ttype,
            userSending
        ";
        
        $query="
            SELECT [FIELDS] FROM (
            #Paquetes
                SELECT 
                    distinct
                    r.id as routePointActivityId,
                    r.scheduledRoute_id as scheduleId,
                    rp.id as routePoint_id,

                    ord.id,
                    p.namePackage as Paquete,
                    typ.name as Actividad,
                    DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                    ad.status as Estado,
                    po.name as name,
                    po.type as pointType,
                    u.first_name as receptor,
                    CASE WHEN typ.id=1 THEN 1
                        WHEN typ.id=2 THEN 3
                    END as status,    
                    NULL curpoint,    
                    NULL endpoint,
                    NULL packagePromotion,
                    0 promotionid,
                    0 numfiles,
                    NULL promotionpath,
                    NULL promotionscheduleid,
                    trans.transactionType_id as ttype,
                    ptseller.name userSending
                FROM
                    routepoint_activity r 
                    LEFT JOIN transactions trans ON r.transaction_id=trans.id
                    LEFT JOIN m3_commerce_order ord ON trans.transaction_id = ord.id
                    LEFT JOIN package_to_order p ON ord.id=p.order_id
                    LEFT JOIN activity_type typ ON r.activityType_id = typ.id
                    LEFT JOIN route_points rp ON r.routePoint_id = rp.id
                    LEFT JOIN activity_detail ad ON r.id=ad.routePointActivity_id
                    LEFT JOIN points po ON rp.point_id=po.id
                    LEFT JOIN users u ON po.id=u.point_id
                    LEFT JOIN points ptseller on ord.pointSeller_id = ptseller.id
                WHERE 
                (:routePointActivityId IS NULL OR r.id = :routePointActivityId)
                AND (:schedulerouteid  IS NULL OR r.scheduledRoute_id=:schedulerouteid )
                AND (:routepointid IS NULL OR rp.id=:routepointid)
                AND trans.transactionType_id=1 AND p.promotion_id IS NULL
                AND trans.status NOT IN(5,6)  -- Detenido, rechazado

                UNION
            #Paquetes con promociÃ³n
                SELECT 
                    distinct
                    r.id as routePointActivityId,
                    r.scheduledRoute_id as scheduleId,
                    rp.id as routePoint_id,

                    ord.id,
                    p.namePackage as Paquete,
                    typ.name as Actividad,
                    DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                    ad.status as Estado,
                    po.name as name,
                    po.type as pointType,
                    u.first_name as receptor,
                    CASE WHEN typ.id=1 THEN 1
                        WHEN typ.id=2 THEN 3
                    END as status,
                    rp.point_id curpoint,
                    ptbuyer.id endpoint,
                    promoOrder.name packagePromotion,
                    p.promotion_id promotionid,
                    (SELECT IFNULL(COUNT(id),0) FROM promotion_resources WHERE promotion_id=p.promotion_id) numfiles,
                    (SELECT path FROM promotion_resources WHERE promotion_id=p.promotion_id LIMIT 1) promotionpath,
                    (SELECT id FROM promotion_schedule WHERE activitypoint_id=r.id) promotionscheduleid,
                    trans.transactionType_id as ttype,
                    ptseller.name userSending
                FROM
                    routepoint_activity r 
                    LEFT JOIN transactions trans ON r.transaction_id=trans.id
                    LEFT JOIN m3_commerce_order ord ON trans.transaction_id = ord.id
                    LEFT JOIN package_to_order p ON ord.id=p.order_id
                    LEFT JOIN activity_type typ ON r.activityType_id = typ.id
                    LEFT JOIN route_points rp ON r.routePoint_id = rp.id
                    LEFT JOIN activity_detail ad ON r.id=ad.routePointActivity_id
                    LEFT JOIN points po ON rp.point_id=po.id
                    LEFT JOIN users u ON po.id=u.point_id
                    
                    LEFT JOIN promotion promoOrder on p.promotion_id=promoOrder.id
                    LEFT JOIN points ptbuyer on ord.pointBuyer_id = ptbuyer.id
                    LEFT JOIN points ptseller on ord.pointSeller_id = ptseller.id

                WHERE 
                (:routePointActivityId IS NULL OR r.id = :routePointActivityId)
                AND (:schedulerouteid  IS NULL OR r.scheduledRoute_id=:schedulerouteid )
                AND (:routepointid IS NULL OR rp.id=:routepointid)
                AND trans.transactionType_id=1 AND p.promotion_id IS NOT NULL
                AND trans.status NOT IN(5,6)  -- Detenido, rechazado

                UNION
            #Promociones    
                SELECT
                    distinct
                    r.id as routePointActivityId,
                    r.scheduledRoute_id as scheduleId,
                    rp.id as routePoint_id,

                    ps.id,
                    p.name as Paquete,
                    typ.name as Actividad,
                    DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                    ad.status as Estado,
                    po.name as name,
                    po.type as pointType,
                    u.first_name as receptor,
                    CASE WHEN typ.id=1 THEN 1
                        WHEN typ.id=2 THEN 3
                    END as status,    
                    NULL curpoint,    
                    NULL endpoint,
                    NULL packagePromotion,
                    p.id promotionid,
                    (SELECT IFNULL(COUNT(id),0) FROM promotion_resources WHERE promotion_id=p.id) numfiles,
                    (SELECT path FROM promotion_resources WHERE promotion_id=p.id LIMIT 1) promotionpath,
                    ps.id promotionscheduleid,
                    trans.transactionType_id as ttype,
                    IFNULL(userSeller.commercial_name,CONCAT(userSeller.first_name,' ',userSeller.last_name)) userSending
                FROM
                    routepoint_activity r 
                    LEFT JOIN transactions trans ON r.transaction_id=trans.id
                    LEFT JOIN promotion_schedule ps on trans.transaction_id = ps.id
                    LEFT JOIN promotion p on ps.promotion_id=p.id
                    LEFT JOIN activity_type typ on r.activityType_id = typ.id
                    LEFT JOIN route_points rp on r.routePoint_id = rp.id
                    LEFT JOIN points po on rp.point_id=po.id
                    LEFT JOIN activity_detail ad on r.id=ad.routePointActivity_id
                    LEFT JOIN users u ON po.id=u.point_id
                    LEFT JOIN users userSeller on ps.user_id=userSeller.id
                WHERE 
                (:routePointActivityId IS NULL OR r.id = :routePointActivityId)
                AND (:schedulerouteid  IS NULL OR r.scheduledRoute_id=:schedulerouteid )
                AND (:routepointid IS NULL OR rp.id=:routepointid)
                AND trans.transactionType_id=2
                AND trans.status NOT IN(5,6)  -- Detenido, rechazado
            ) a
            ORDER BY a.ttype
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(':routePointActivityId',$routePointActivityId);
        $stmt->bindValue(':schedulerouteid',$id);
        $stmt->bindValue(':routepointid',$id2);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));        
    }
    
    //Paquetes informativos sin scanner
    public function getActivityPackageListDQL ($page,$rowsPerPage,$sortField,$sortDir,
                        $idrow,
                        $idrow2,
                        
                        $stateParams,
                        $id2,
                        $id,
                        
                        $Paquete,
                        $Estado,
                        $Actividad){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = "  id,
                        Paquete,
                        Actividad,
                        horaReal,
                        Estado,
                        name,
                        comments1,
                        comments2,
                        evidence,
                        curpoint,    
                        endpoint,
                        packagePromotion,
                        promotionid,
                        ttype
                     ";

        $query = "                
                SELECT [FIELDS] FROM (
                #Paquetes   
                    SELECT DISTINCT    
                        ord.id,
                        p.namePackage as Paquete,
                        typ.name as Actividad,
                        DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                        ad.status as Estado,
                        po.name as name,
                        ord.comments as comments1,
                        ad.comentarios as comments2,
                        concat('../images/evidencias/',ad.id,ad.uploadFile) as evidence,
                        NULL curpoint,    
                        NULL endpoint,
                        NULL packagePromotion,
                        0 promotionid,
                        trans.transactionType_id as ttype
                    FROM routepoint_activity r
                        LEFT JOIN transactions trans on r.transaction_id=trans.id
                        LEFT JOIN m3_commerce_order ord on trans.transaction_id = ord.id
                        LEFT JOIN package_to_order p on ord.id=p.order_id
                        LEFT JOIN activity_type typ on r.activityType_id = typ.id
                        LEFT JOIN route_points rp on r.routePoint_id = rp.id
                        LEFT JOIN points po on rp.point_id=po.id
                        LEFT JOIN activity_detail ad on r.id=ad.routePointActivity_id
                        WHERE 
                            r.scheduledRoute_id= :schedulerouteid
                            AND rp.id = :routepointid
                            AND trans.transactionType_id=1 AND p.promotion_id IS NULL
                            AND trans.status NOT IN(5,6)  -- Detenido, rechazado
                        AND (:Paquete IS NULL OR  p.namePackage LIKE :Paquete)
                        AND (:Actividad IS NULL OR  typ.name LIKE :Actividad)

                    UNION

                    #Paquetes con promociÃ³n 
                    SELECT DISTINCT 
                        ord.id,
                        p.namePackage as Paquete,
                        typ.name as Actividad,
                        DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                        ad.status as Estado,
                        po.name as name,
                        ord.comments as comments1,
                        ad.comentarios as comments2,
                        concat('../images/evidencias/',ad.id,ad.uploadFile) as evidence,
                        rp.point_id curpoint,
                        ptbuyer.id endpoint,
                        promoOrder.name packagePromotion,
                        p.promotion_id promotionid,
                        trans.transactionType_id as transType
                    FROM routepoint_activity r
                        LEFT JOIN transactions trans on r.transaction_id=trans.id
                        LEFT JOIN m3_commerce_order ord on trans.transaction_id = ord.id
                        LEFT JOIN package_to_order p on ord.id=p.order_id
                        LEFT JOIN activity_type typ on r.activityType_id = typ.id
                        LEFT JOIN route_points rp on r.routePoint_id = rp.id
                        LEFT JOIN points po on rp.point_id=po.id
                        LEFT JOIN activity_detail ad on r.id=ad.routePointActivity_id
                        
                        LEFT JOIN promotion promoOrder on p.promotion_id=promoOrder.id
                        LEFT JOIN points ptbuyer on ord.pointBuyer_id = ptbuyer.id
                        LEFT JOIN points ptseller on ord.pointSeller_id = ptseller.id
                        WHERE 
                            r.scheduledRoute_id = :schedulerouteid
                            AND rp.id = :routepointid
                            AND trans.transactionType_id=1 AND p.promotion_id IS NOT NULL
                            AND trans.status NOT IN(5,6)  -- Detenido, rechazado
                        AND (:Paquete IS NULL OR  p.namePackage LIKE :Paquete)
                        AND (:Actividad IS NULL OR  typ.name LIKE :Actividad)

                    UNION

                #Promociones
                    SELECT DISTINCT
                        ps.id,
                        p.name as Paquete,
                        typ.name as Actividad,
                        DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                        ad.status as Estado,
                        po.name as name,
                        null as comments1,
                        ad.comentarios as comments2,
                        concat('../images/evidencias/',ad.id,ad.uploadFile) as evidence,
                        NULL curpoint,    
                        NULL endpoint,
                        NULL packagePromotion,
                        0 promotionid,
                        trans.transactionType_id as ttype
                    FROM routepoint_activity r
                        LEFT JOIN transactions trans on r.transaction_id=trans.id
                        LEFT JOIN promotion_schedule ps on trans.transaction_id = ps.id
                        LEFT JOIN promotion p on ps.promotion_id=p.id
                        LEFT JOIN activity_type typ on r.activityType_id = typ.id
                        LEFT JOIN route_points rp on r.routePoint_id = rp.id
                        LEFT JOIN points po on rp.point_id=po.id
                        LEFT JOIN activity_detail ad on r.id=ad.routePointActivity_id
                        WHERE 
                            r.scheduledRoute_id= :schedulerouteid 
                            AND rp.id = :routepointid 
                            AND trans.transactionType_id=2
                            AND trans.status NOT IN(5,6)  -- Detenido, rechazado
                            AND (:Paquete IS NULL OR  p.name LIKE :Paquete)
                            AND (:Actividad IS NULL OR  typ.name LIKE :Actividad)
                ) a
                ORDER BY a.ttype
                [LIMIT]
                ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(':schedulerouteid',$id2);
        $stmt->bindValue(':routepointid',$id);
        $stmt->bindValue(':Paquete', ($Paquete==null) ? null : '%'.$Paquete.'%');
        $stmt->bindValue(':Actividad', ($Actividad==null) ? null : '%'.$Actividad.'%');
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    //FUNCION QUE CUENTA LOS PAQUETES MODIFICADOS PARA SU NAVEGACION ENTRE PANTALLAS
    public function getCountPacks ($idschedule,$routePointId){
        $em = $this->getEntityManager();

        $sqlSelect = "
            SELECT count(r.id) totalRecords FROM 
                routepoint_activity r
                INNER JOIN route_points rp ON rp.id = r.routePoint_id
                LEFT JOIN activity_detail ad ON ad.routePointActivity_id=r.id    
            WHERE r.scheduledRoute_id  = :schedulerouteid AND rp.id = :routepointid AND ad.date IS NULL
        ";

        $querys = $sqlSelect;
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);        
        $stmt->bindValue(':schedulerouteid',$idschedule);
        $stmt->bindValue(':routepointid',$routePointId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('meta'=>array('totalRecords'=>$result[0][0]["totalRecords"]));
    }
            
    // obtener ir rpa
    public function  getActivityDetail($page,$rowsPerPage,$sortField,$sortDir,$id2){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = "
                    r.id
                ";
        
        $query="
            FROM logistic_masfletes.routepoint_activity r
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
    
    //REPORTE DE ACTIVIDADES POR CHOFER
    public function getReportListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$name,$progHour,$currHour,$comments){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = "
                    p.id,
                    p.name as Nombre,
                    DATE_FORMAT(rpa.date,GET_FORMAT(TIME,'ISO')) as HoraProgramada,
                    DATE_FORMAT(rpa.hora_actual,GET_FORMAT(TIME,'ISO')) as HoraReal,
                    ad.comentarios as Comentarios
            ";

        $this->_cr = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em1 = $dbs['DefaultDb'];
        $user = $em1->find('DefaultDb_Entities_User', $this->_cr['id']);
        $usuario = "" + $this->_cr['id'];

        $query="
            SELECT [FIELDS]
                from points p
                    inner join routepoint_activity rpa on p.id=rpa.id
                    left join activity_detail ad on rpa.id=ad.id
                    left join users u on ad.id = u.id
                    where u.id='".$usuario."'
                    ORDER BY rpa.date ASC
                ";
                //where u.id='".$usuario."'
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    //FUNCIÃ“N 1 DEL INVENTARIO POR VEHÃ�CULO DEL CHOFER
    public function getInventoryListDQL($page,$rowsPerPage,$sortField,$sortDir){        
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
                        ord.id as OC,
                        p.namePackage as Paquete,
                        count(ad.id) as count,
                        trans.status as estado,
                        u.first_name as driverName,
                        concat(v.name,' [',v.volume, ' ftÂ³','] ', '[',v.capacity,']') as vehicle
                     ";
        $query ="
                SELECT [FIELDS]
                    from package_to_order p 
                        inner join m3_commerce_order ord on p.order_id=ord.id
                        inner join transactions trans on trans.transaction_id = ord.id 
                        inner join routepoint_activity r on trans.id=r.transaction_id
                        inner join activity_type typ on typ.id = r.activityType_id
                        inner join route_points rp on rp.id = r.routePoint_id
                        left join activity_detail ad on r.id=ad.routePointActivity_id
                        left join points po on rp.point_id=po.id
                        inner join scheduled_route sr on r.scheduledRoute_id=sr.id
                        inner join users u on sr.driver_id=u.id
                        inner join vehicles v on sr.vehicle_id=v.id
                    where sr.driver_id=:usuario and (ad.status=1 or ad.status=3)
                    group by ord.id
                    having mod(count(ad.id),2)<>0
                ";
        // USAR ESTA CONDICIÃ“N
        // WHERE u.id = '".$usuario."'
        // WHERE DATE_FORMAT(s.scheduled_date, '%Y-%m-%d') = CURDATE() and u.id = '".$usuario."'
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":usuario",$usuario);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    //FUNCIÃ“N 2 DEL INVENTARIO POR VEHÃ�CULO DEL CHOFER
    public function getInventoryPacksListDQL ($page,$rowsPerPage,$sortField,$sortDir,$id,$idrow){
        $em = $this->getEntityManager();
        
    if($page==null) {$page=1;}
        if($rowsPerPage==null) {$rowsPerPage=10;}

        $offset = ($page-1)*$rowsPerPage;
        $sqlSelect = "
                     distinct
                        ord.id as OC,
                        p.namePackage as Paquete,
                        count(ad.id) as count,
                        trans.status as estado,
                        sr.vehicle_id as vehicle
                     ";
        $query ="
                SELECT [FIELDS]
                from package_to_order p 
                    inner join m3_commerce_order ord on p.order_id=ord.id
                    inner join transactions trans on trans.transaction_id = ord.id 
                    inner join routepoint_activity r on trans.id=r.transaction_id
                    inner join scheduled_route_activity s on s.routePointActivity_id = r.id
                    inner join activity_type typ on typ.id = r.activityType_id
                    inner join route_points rp on rp.id = r.routePoint_id
                    left join activity_detail ad on s.id=ad.scheduledRouteActivityId_id
                    left join points po on rp.point_id=po.id
                    inner join scheduled_route sr on s.scheduledRoute_id=sr.id
                where sr.vehicle_id = '".$idrow."' and (ad.status=1 or ad.status=3)
                group by ord.id
                having mod(count(ad.id),2)<>0
                ";
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect."; ".$queryCount;
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        //$stmt->bindValue(":scheduleId",$scheduleId);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],
                     'meta'=>array(
                         'totalRecords'=>$result[1][0]["totalRecords"],
                         'offset'=>$offset));
    }
    
    //PANTALLA INFORMATIVA DE PAQUETES POR RUTA
    public function getPacksRouteListDQL ($page,$rowsPerPage,$sortField,$sortDir,$id,$idrow,$stateParams,$id2, $ptoVenta, $Paquete){
        $em = $this->getEntityManager();
        
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        $sqlSelect="
                distinct
                    ord.id as OC,
                    p.namePackage as Paquete,
                    typ.name as Actividad,
                    DATE_FORMAT(ad.date,'%H:%i:%s') as horaReal,
                    ad.status as Estado,
                    po.name as ptoVenta,
                    ad.comentarios as comentarios,
                    ad.date as hourActivity,
                    r.date as scheduleDate
        ";
        $query="
            SELECT [FIELDS]
            from package_to_order p 
                inner join m3_commerce_order ord on p.order_id=ord.id
                inner join transactions trans on trans.transaction_id = ord.id 
                inner join routepoint_activity r on trans.id=r.transaction_id
                inner join activity_type typ on typ.id = r.activityType_id
                inner join route_points rp on rp.id = r.routePoint_id
                left join activity_detail ad on r.id=ad.routePointActivity_id
                left join points po on rp.point_id=po.id
            where r.scheduledRoute_id='".$idrow."'
            AND (:Paquete IS NULL OR  p.namePackage LIKE :Paquete)
            AND (:ptoVenta IS NULL OR  po.name LIKE :ptoVenta)
            AND trans.status NOT IN(5,6)  -- Detenido, rechazado
            [ORDERBY]
            [LIMIT]
        ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);
        
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(':Paquete', ($Paquete==null) ? null : '%'.$Paquete.'%');
        $stmt->bindValue(':ptoVenta', ($ptoVenta==null) ? null : '%'.$ptoVenta.'%');
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    
    
    //OBTENER FAVORITOS DEL CLIENTE
    public function getRouteSummaryFavoritesListDQL($page,$rowsPerPage,$sortField,$sortDir,$id2,$id){
        $em = $this->getEntityManager();

        if($page==null) {$page=1;}
        if($rowsPerPage==null) {$rowsPerPage=10;}

        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect = "  Producto,
                        Proveedor,idProducto ";
        
        $query="
                SELECT [FIELDS]
                FROM 
                (   SELECT DISTINCT 
                        p.id idProducto,
                        p.name AS Producto,
                        p.Proveedor
                    FROM favorite_users fu
                    INNER JOIN users u ON fu.client_id = u.id
                    INNER JOIN branches_user bu ON bu.client_id = u.id
                    INNER JOIN points po ON po.id=bu.point_id  
                    INNER JOIN route_points rp ON rp.point_id=po.id 
                    INNER JOIN (SELECT
                                    DISTINCT 
                                    product.id,
                                    product.name,
                                    users.id AS userId,
                                    CASE WHEN trim(users.commercial_name) != '' 
                                    THEN trim(users.commercial_name) 
                                    ELSE 
                                    concat(users.first_name, ' ', users.last_name) 
                                    END AS Proveedor
                                FROM product 
                                INNER JOIN users 
                                ON users.id = product.client_id) p 
                        ON p.id = producto_id  
                        AND p.userId = fu.favoriteClient_id   
                    WHERE rp.id='".$id2."'
                    ) AS tblFavorites
                       [ORDERBY]
                       [LIMIT]
             ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMIT]', " ", $queryCount);
        
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMIT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);
        
        return array('data' => $result[0], 'meta' => array('totalRecords' => $totalRecords));
        
    }
}
