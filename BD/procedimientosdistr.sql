
DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_ListarPuntosCor`(
IN
		scheduleRouteId INT )
BEGIN
	declare routeId int default 0;
	declare p_start datetime default now();
declare p_sidx varchar(256) default 'order_number';
set routeId	:= (select  max(route_id) from scheduled_route where id =scheduleRouteId);
set p_start:= (select  max(scheduled_date) from scheduled_route where start_date is not null and id =scheduleRouteId);
	DROP TABLE IF EXISTS _pointsCor;
	CREATE TEMPORARY TABLE IF NOT EXISTS _pointsCor AS 
	(
select distinct
	scheduleRouteId as Identificador,
	rp.route_id as Route_ID,
	p.id as Point_Id,
	DATE_FORMAT((func_time_list(routeId,rp.order_number,p_start)),GET_FORMAT(TIME,'ISO')) as Hora,
	DATE_FORMAT(func_time_list(routeId,rp.order_number,p_start),'%m %d %Y %H:%i:%s') as Formato,
	p.name as Nombre,
	p.address as Direccion,
	rp.order_number as order_number,
	p.code as Codigo,
	func_validarPunto(scheduleRouteId,p.id,func_time_list(routeId,rp.order_number,p_start)) as validar,
	cast(hora_actual_punto(scheduleRouteId,p.id,func_time_list(routeId,rp.order_number,p_start)) as datetime) as HoraActual
from points p inner join route_points rp on p.id=rp.point_id
left join routepoint_activity rpa  on rp.id = rpa.routePoint_id
left join scheduled_route_activity sra on rpa.id = sra.routePointActivity_id
left join scheduled_route sr  on sr.id = sra.scheduledRoute_id
left join activity_type act on act.id = rpa.activityType_id
left join transactions trans on trans.id = rpa.transaction_id
where rp.route_id=routeId
order by order_number asc);
	SELECT * FROM _pointsCor;
	SELECT count(*) totalRecords,  1 page, ceil(count(*)/100) totalpages from _pointsCor;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_count_points`(IN
		scheduleRouteId INT, pointId int)
BEGIN
select distinct
	count(rp.point_id)
from package_to_order p 
	left join m3_commerce_order ord on p.order_id=ord.id
	left join transactions trans on trans.transaction_id = ord.id 
	left join routepoint_activity r on trans.id=r.transaction_id
	left join scheduled_route_activity s on s.routePointActivity_id = r.id
	left join activity_type typ on typ.id = r.activityType_id
	left join route_points rp on rp.id = r.routePoint_id
	left join activity_detail ad on ord.id=ad.scheduledRouteActivityId_id
	where s.scheduledRoute_id  = scheduleRouteId and rp.point_id = pointId and ad.date  is null
order by ord.id desc;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_error`(p_tipo char(1), p_mensaje varchar(250))
BEGIN
		declare l_version varchar(3) default version();
		declare l_nversion int default null;
		DECLARE l_tError VARCHAR(25) DEFAULT 'EXCEPCION_USUARIO: ';
		DECLARE l_msgError varchar(250) default null;
		set l_nversion = left(l_version,3)*1.0; 
		IF (p_tipo is null) OR (p_tipo <> 'U') THEN
			SET l_tError =  'EXCEPCION_SISTEMA: ';
		END IF;
		SET l_msgError = concat(l_tError, p_mensaje);
		if l_nversion < 5.5 then
			SET @sql=CONCAT('UPDATE `',l_msgError, '` SET X=1');
			PREPARE l_signal_stmt FROM @sql;
			EXECUTE l_signal_stmt;
			DEALLOCATE PREPARE l_signal_stmt;
		else			
			SIGNAL SQLSTATE 'ERROR'
			SET 
				MESSAGE_TEXT = l_msgError,
				MYSQL_ERRNO = 50000;
		end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_generateScheduledRoute`( IN pscheduleId Int )
BEGIN
    declare lrouteId int;
    declare luserId int;
    declare lvehicleId int;
    declare lstartDate datetime;
    declare lendDate datetime;    
    declare lsunday int;
    declare lmonday int;
    declare ltuesday int;
    declare lwednesday int;
    declare lthursday int;
    declare lfriday int;
    declare lsaturday int;
    declare lrecurrent int;
    declare lweekDay int;    
    declare lnumRecord int;
    declare lmaxScheduleNum int;
    select route_id,user_id,vehicle_id,start_date,end_date,sunday,monday,tuesday,wednesday,thursday,friday,saturday, recurrent
    into lrouteId,luserId,lvehicleId,lstartDate, lendDate,lsunday,lmonday,ltuesday,lwednesday,lthursday,lfriday,lsaturday,lrecurrent
    from `schedule` where id=pscheduleId;
    set lweekDay = DAYOFWEEK(lstartDate)-1; -- Porque devuelve domingo=1 y se requiere domingo=0
    set lnumRecord = 1;
    DROP TABLE IF EXISTS _scheduledRoute;
    CREATE TEMPORARY TABLE _scheduledRoute AS (SELECT * FROM scheduled_route WHERE id <0);
    if lrecurrent=1 then
        while lstartDate < DATE_ADD(lendDate, INTERVAL 1 day) do
            if (lsunday = 1 and lweekDay=0 ) or (lmonday = 1 and lweekDay=1) or (ltuesday = 1 and lweekDay=2)
            or (lwednesday = 1 and lweekDay=3) or (lthursday = 1 and lweekDay=4) or (lfriday = 1 and lweekDay=5)
            or (lsaturday = 1 and lweekDay=6) then           
                insert into _scheduledRoute(schedule_id,schedule_num,scheduled_date,route_id,vehicle_id,driver_id)
                values (pscheduleId, lnumRecord, lstartDate, lrouteId, lvehicleId, luserId);
                set lnumRecord = lnumRecord+1;
            end if;
            set lstartDate = DATE_ADD(lstartDate, INTERVAL 1 day);
            set lweekDay = DAYOFWEEK(lstartDate)-1;
        end while;
    ELSE
        insert into _scheduledRoute(schedule_id,schedule_num,scheduled_date,route_id,vehicle_id,driver_id)
        values (pscheduleId, 1, lstartDate, lrouteId, lvehicleId, luserId);
    end if;
    select max(schedule_num) into lmaxScheduleNum from scheduled_route where schedule_id=pscheduleId;
    set @maxSched = lmaxScheduleNum;
    insert into scheduled_route(schedule_id,schedule_num,scheduled_date,route_id,vehicle_id,driver_id,`status`)    
    SELECT temp.schedule_id,@maxSched:=@maxSched+1,temp.scheduled_date,temp.route_id,temp.vehicle_id,temp.driver_id,0
            FROM _scheduledRoute temp LEFT JOIN scheduled_route sr 
                on temp.schedule_id = sr.schedule_id 
                and DATE(temp.scheduled_date) = DATE(sr.scheduled_date)
            WHERE sr.id IS NULL;
    SELECT ROW_COUNT() schedulesCreated;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_getPointByName`(IN
		p_name varchar(20),
        p_controller INT(11))
BEGIN		
		select id, CONCAT('[',p.code,'] ',p.name ) point, p.type from points p 
		where (p_name is null or 
			CONCAT(p.code,' ', p.name ) like  CONCAT('%',p_name,'%')) AND controller_id = p_controller or p.type = 2;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_getPoints`(IN
		p_id int,
		p_name varchar(20))
BEGIN
		SELECT id, CONCAT('[',p.code,'] ',p.name ) POINT, p.type FROM points p 
		WHERE 
			(p_id IS NULL OR p.id=p_id)
			and (p_name IS NULL OR CONCAT(p.code,' ', p.name ) LIKE  CONCAT('%',p_name,'%'))
			order by CONCAT('[',p.code,'] ',p.name );
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_get_zone_by_user`(IN
	p_id int
	)
BEGIN	
if p_id  = 0 then
select id, name from zone where id in (select zone_id from user_zone) order by name;
else 
select id, name from zone where id in (select zone_id from user_zone where user_id = p_id) order by name;
end if;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_listarRutas`(
IN
		routeId INT )
BEGIN
	declare p_start datetime;
set p_start:= (select  max(start_date) from scheduled_route where start_date is not null);
select 
	rp.route_id as Route_ID,
	p.id as Point_Id,
	cast((select sum(rp.arrival_time) from route_points rp where rp.route_id=1 and order_number <10) as datetime) as ca,
	DATE_FORMAT((rp.arrival_time),GET_FORMAT(TIME,'ISO')) as Hora,
	DATE_FORMAT(rp.arrival_time,'%m %d %Y %H:%i:%s') as Formato,
	p.name as Nombre,
	p.address as Direccion,
	p.code as Codigo
from points p inner join route_points rp on p.id=rp.point_id
left join routepoint_activity rpa  on rp.id = rpa.routePoint_id
left join scheduled_route_activity sra on rpa.id = sra.routePointActivity_id
left join scheduled_route sr  on sr.id = sra.scheduledRoute_id
left join activity_type act on act.id = rpa.activityType_id
left join transactions trans on trans.id = rpa.transaction_id
where rp.route_id=routeId
order by order_number asc;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_permiso_validar`(IN
		p_idsesion varchar(20),
		p_element varchar(50),
		p_action varchar(20)
    )
BEGIN
		declare l_idusuario int default 0;
		select idusuario into l_idusuario FROM sesiones WHERE idsesion = p_idsesion;
		select count(us.id) permisos from users us inner join role ro on us.role_id=ro.id
			inner join role_action ra on ro.id=ra.role_id
			inner join element_action ea on ra.eaction_id=ea.id
			inner join elements el on ea.element_id=el.id 
			inner join action ac on ea.action_id=ac.id
		where us.id=l_idusuario 
		and lcase(ltrim(rtrim(el.name))) = lcase(ltrim(rtrim(p_element)))
		and lcase(ltrim(rtrim(ac.name))) = lcase(ltrim(rtrim(p_action)));
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_pointsDriver_listar`(
IN
		scheduleRouteId INT,
        pSortField VARCHAR(255),
        pOrden VARCHAR(255), 
        pNombre VARCHAR(255), 
        pCodigo VARCHAR(255))
BEGIN
	DECLARE routeId INT DEFAULT 0;
	DECLARE p_start DATETIME DEFAULT now();
DECLARE p_sidx VARCHAR(256) DEFAULT 'order_number';
SET routeId	:= (SELECT  max(route_id) FROM scheduled_route WHERE id =scheduleRouteId);
SET p_start:= (SELECT  max(scheduled_date) FROM scheduled_route WHERE start_date IS NOT NULL AND id =scheduleRouteId);
	DROP TABLE IF EXISTS _pointsCor;
	CREATE TEMPORARY TABLE IF NOT EXISTS _pointsCor AS 
	(
SELECT DISTINCT
	scheduleRouteId AS Identificador,
	rp.route_id AS Route_ID,
	p.id AS Point_Id,	
	DATE_FORMAT((func_time_list(routeId,rp.order_number,p_start)),GET_FORMAT(TIME,'ISO')) AS Hora,
	rp.arrival_time AS arrTime,
	DATE_FORMAT(func_time_list(routeId,rp.order_number,p_start),'%m %d %Y %H:%i:%s') AS Formato,
	DATE_FORMAT(func_time_list(routeId,rp.order_number,p_start),'%d %M %Y') AS hourProgram,
	p.name AS Nombre,
	CONCAT(a.address, p.extNumber, ', ', a.neighborhood, a.zipcode) AS Direccion,
	rp.order_number AS order_number,
	p.code AS Codigo,
	func_validarPunto(scheduleRouteId,rp.id,func_time_list(routeId,rp.order_number,p_start)) AS validar,
	DATE_FORMAT(cast(hora_actual_punto(scheduleRouteId,rp.id,p.id,func_time_list(routeId,rp.order_number,p_start)) AS DATETIME),GET_FORMAT(TIME,'ISO')) AS HoraActual,
	r.name AS ruta,
	rp.id AS routePoint_id,
	rp.status
FROM points p
INNER JOIN route_points rp ON p.id=rp.point_id
INNER JOIN routepoint_activity rpa  ON rp.id = rpa.routePoint_id /*Desde qu? cambie left por inner*/
LEFT JOIN scheduled_route sr ON rpa.scheduledRoute_id=sr.id
LEFT JOIN activity_type act ON act.id = rpa.activityType_id
LEFT JOIN transactions trans ON trans.id = rpa.transaction_id
LEFT JOIN routes r ON rp.route_id=r.id
LEFT JOIN address a ON p.address_id=a.id
WHERE rp.route_id=routeId AND rp.status=1
	AND func_validarPunto(scheduleRouteId,rp.id,func_time_list(routeId,rp.order_number,p_start))>0
ORDER BY  rpa.hora_actual , rpa.date ASC  , rpa.routePoint_id ASC);
	SELECT * FROM _pointsCor 
	WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) 
    ORDER BY  HoraActual , Hora ASC  , hourProgram ;
	SELECT 
    COUNT(*) totalRecords,
    1 page,
    CEIL(COUNT(*) / 100) totalpages
FROM
    _pointsCor;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_pointsDriver_listar_BCK`(
IN
		scheduleRouteId INT,
        pSortField varchar(255),
        pOrden varchar(255), 
        pNombre varchar(255), 
        pCodigo varchar(255))
BEGIN
	declare routeId int default 0;
	declare p_start datetime default now();
declare p_sidx varchar(256) default 'order_number';
set routeId	:= (select  max(route_id) from scheduled_route where id =scheduleRouteId);
set p_start:= (select  max(scheduled_date) from scheduled_route where start_date is not null and id =scheduleRouteId);
	DROP TABLE IF EXISTS _pointsCor;
	CREATE TEMPORARY TABLE IF NOT EXISTS _pointsCor AS 
	(
select distinct
	scheduleRouteId as Identificador,
	rp.route_id as Route_ID,
	p.id as Point_Id,	
	DATE_FORMAT((func_time_list(routeId,rp.order_number,p_start)),GET_FORMAT(TIME,'ISO')) as Hora,
	rp.arrival_time as arrTime,
	DATE_FORMAT(func_time_list(routeId,rp.order_number,p_start),'%m %d %Y %H:%i:%s') as Formato,
	DATE_FORMAT(func_time_list(routeId,rp.order_number,p_start),'%d %M %Y') as hourProgram,
	p.name as Nombre,
	CONCAT(a.address, p.extNumber, ', ', a.neighborhood, a.zipcode) as Direccion,
	rp.order_number as order_number,
	p.code as Codigo,
	func_validarPunto(scheduleRouteId,rp.id,func_time_list(routeId,rp.order_number,p_start)) as validar,
	DATE_FORMAT(cast(hora_actual_punto(scheduleRouteId,rp.id,p.id,func_time_list(routeId,rp.order_number,p_start)) as datetime),GET_FORMAT(TIME,'ISO')) as HoraActual,
	r.name as ruta,
	rp.id as routePoint_id,
	rp.status
from points p
inner join route_points rp on p.id=rp.point_id
inner join routepoint_activity rpa  on rp.id = rpa.routePoint_id /*Desde quí cambie left por inner*/
left join scheduled_route sr on rpa.scheduledRoute_id=sr.id
left join activity_type act on act.id = rpa.activityType_id
left join transactions trans on trans.id = rpa.transaction_id
left join routes r on rp.route_id=r.id
LEFT JOIN address a ON p.address_id=a.id
where rp.route_id=routeId and rp.status=1
	AND func_validarPunto(scheduleRouteId,rp.id,func_time_list(routeId,rp.order_number,p_start))>0
order by order_number asc);
if pOrden like '%desc%' then
IF pSortField like '%Hora%'  then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Hora DESC;
ELSEIF pSortField like '%Nombre%' then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Nombre DESC;
 ELSEIF pSortField like '%Codigo%'  then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Codigo DESC;
 ELSEIF pSortField like '%validar%'  then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY validar DESC;
ELSE 
SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY HoraActual desc;
END IF;
else
IF pSortField like '%Hora%'  then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Hora ASC;
 ELSEIF pSortField like '%Nombre%' then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Nombre ASC;
 ELSEIF pSortField like '%Codigo%' then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY Codigo ASC;
 ELSEIF pSortField like '%validar%'  then
 SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY validar ASC;
ELSE 
SELECT * FROM _pointsCor WHERE      (pNombre IS NULL OR Nombre LIKE pNombre)
    AND (pCodigo IS NULL OR Codigo LIKE pCodigo) ORDER BY HoraActual ASC;
END IF;
end if;
	SELECT 
    COUNT(*) totalRecords,
    1 page,
    CEIL(COUNT(*) / 100) totalpages
FROM
    _pointsCor;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_points_delete`(IN p_id int)
BEGIN
	IF p_id is not null then
		delete from points where id=p_id;
	end if;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_points_listar`(
	p_idsesion VARCHAR(20),
	p_page INT, 
	p_limit int,
	p_sidx varchar(256),
	p_sord varchar(4),
	p_id int)
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	set p_start=(p_page-1)*p_limit;
	DROP TABLE IF EXISTS _points;
	CREATE TEMPORARY TABLE IF NOT EXISTS _points AS 
	(SELECT p.*,case p.status when 1 then 'Normal'  when 2 then 'Pausado' ELSE 'Cancelado' end estatus, 
		CASE p.type WHEN 1 THEN 'Punto de venta' ELSE 'Centro de Intercambio' END tipo,
		s.name state
	from points p left join states s on p.state_id = s.id
		where (p_id is null or p.id = p_id)		
		);
	SELECT * FROM _points order by 
	Case p_sidx
		When 'code' then
			code
		When 'name' THEN
			name
		WHEN 'estatus' THEN
			estatus
		WHEN 'tipo' THEN
			tipo
		Else
			id
	end
	LIMIT p_start, p_limit;
	SELECT count(*) records,  p_page page, ceil(count(*)/p_limit) totalpages from _points;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_points_save`(IN p_id INT(10), IN p_code VARCHAR(255), IN p_name VARCHAR(255), IN p_type INT(10), IN p_status INT(10), IN p_address VARCHAR(255), IN p_state_id INT(10), IN p_controller_id INT(10))
BEGIN
	If(p_id is null) then
		insert into points(id, code, name, type, status, address, state_id, controller_id)
		values (null, p_code, p_name, p_type, p_status, p_address, p_state_id, p_controller_id);
	else
		update points set
			code=p_code, 
			name=p_name, 
			type=p_type, 
			status=p_status, 
			address=p_address, 
			state_id=p_state_id, 
			controller_id=p_controller_id
		where id=p_id;
	end if;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routeCatalog_list`(p_idsesion varchar(20), p_name varchar(50))
BEGIN
	DECLARE p_idusuario int DEFAULT 0 ;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	SELECT id,name route FROM routes r
			where (p_name is null or r.name LIKE CONCAT('%',p_name,'%'))
			and r.controller_id = p_idusuario;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routePoint_changeOrder`(IN
		p_id INT, 
		new_position INT
		)
BEGIN		
		declare l_route_id int default 0;
		declare l_oldOrder int DEFAULT 0; 
		select route_id, order_number into l_route_id, l_oldOrder  from route_points 
		where id = p_id;
        if new_position <> l_oldOrder then
			update route_points set order_number = new_position where id = p_id;
            if new_position > l_oldOrder then
				update route_points set order_number = order_number -1 
                where route_id = l_route_id
                and order_number between l_oldOrder and new_position
                and id <> p_id;
            else    
   				update route_points set order_number = order_number +1 
                where route_id = l_route_id
                and order_number between new_position and l_oldOrder
                and id <> p_id;
			end if;
        end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routePoint_delete`(In
		p_id int)
BEGIN
		declare l_Id int default 0; 
		declare l_route_id int default 0;
		declare l_oldOrder INT DEFAULT 0; 
		select id, route_id, order_number into l_Id, l_route_id, l_oldOrder  from route_points 
		where id = p_id;
		delete from route_points where id = p_id;
        update route_points set order_number = order_number - 1 where route_id = l_route_id and order_number > l_oldOrder;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routePoint_list`(IN
		p_page INT, 
		p_limit INT,
		p_sidx VARCHAR(256),
		p_sord VARCHAR(4),
		p_route_id int
		)
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	SET p_start=(p_page-1)*p_limit;
	DROP TABLE IF EXISTS _routePoints;
	CREATE TEMPORARY TABLE IF NOT EXISTS _routePoints AS 
	(
		SELECT rp.id, rp.route_id, rp.point_id, rp.order_number, rp.status, date_format(rp.arrival_time,'%i') arrival_time, rp.required, concat('[',p.code,'] ',p.name ) nombre, p.type pointType, case p.type when 1 then 'Punto de venta' else 'Centro de intercambio' end tipo FROM route_points rp
			INNER JOIN points p ON rp.point_id = p.id
		WHERE rp.route_id = p_route_id
		and rp.status = 1
		and rp.required = 1
		order by order_number		
	);
	SELECT * FROM _routePoints LIMIT p_start, p_limit;
	SELECT COUNT(*) records,  p_page page, CEIL(COUNT(*)/p_limit) totalpages FROM _routePoints;
		IF p_route_id IS NOT NULL THEN
			SELECT r.name,
				(SELECT COUNT(*) FROM route_points WHERE route_id = p_route_id AND required=1 AND STATUS = 1 )cantPoints,
				(SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = p_route_id AND p.type = 1 AND rp.required = 1 AND rp.STATUS = 1 )pointSales,
				(SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = p_route_id AND p.type = 2 AND rp.required = 1 AND rp.STATUS = 1 )exchangeCenters,
				(SELECT COUNT(*) FROM route_points WHERE route_id = p_route_id AND required = 1  AND STATUS = 1 )required,
				(SELECT IFNULL(SUM(DATE_FORMAT(arrival_time,'%i')),0) FROM route_points WHERE route_id = p_route_id AND required = 1  AND STATUS = 1 )totalTime,
				CASE CLOSE WHEN 0 THEN 'ABIERTA' WHEN 1 THEN 'CERRADA' END estatus
			FROM routes r 
			WHERE r.id = p_route_id;
		end if;	
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routePoint_save`(IN
		p_id int,
		p_route_id int,
		p_point_id int,
		p_order_number int,
		p_status int,
		p_arrival_time int,
		p_required int,
        p_arrival_seg int
		)
BEGIN
		declare l_max_order_number int default 0;
		declare l_arrival_time time;
		set l_arrival_time = MAKETIME(0,p_arrival_time,p_arrival_seg);
		if p_id is null then
			SELECT MAX(order_number) into l_max_order_number FROM route_points WHERE route_id = p_route_id;			
			 IF l_max_order_number is null then
				set l_max_order_number = 0;
			 END IF;
			insert into route_points (id, route_id, point_id, order_number, status, arrival_time, required)
				values(null, p_route_id, p_point_id, l_max_order_number+1, p_status, l_arrival_time, p_required);
		else
			UPDATE route_points
				set route_id = p_route_id, 
					point_id = p_point_id, 
					order_number = p_order_number, 
					status = p_status, 
					arrival_time = l_arrival_time, 
					required = p_required
				where id = 	p_id;	
		end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routePoints_listar`(IN
		p_page INT, 
		p_limit INT,
		p_sidx VARCHAR(256),
		p_sord VARCHAR(4),
		p_route_id int
		)
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	SET p_start=(p_page-1)*p_limit;
	DROP TABLE IF EXISTS _routePoints;
	CREATE TEMPORARY TABLE IF NOT EXISTS _routePoints AS 
	(
		SELECT rp.id, rp.route_id, rp.point_id, rp.order_number, rp.status, date_format(rp.arrival_time,'%i') arrival_time, rp.required, concat('[',p.code,'] ',p.name ) nombre, case p.type when 1 then 'Punto de venta' else 'Centro de intercambio' end tipo FROM route_points rp
			INNER JOIN points p ON rp.point_id = p.id
		WHERE rp.route_id = p_route_id
		order by order_number		
	);
	SELECT * FROM _routePoints LIMIT p_start, p_limit;
	SELECT COUNT(*) records,  p_page page, CEIL(COUNT(*)/p_limit) totalpages FROM _routePoints;
	IF p_route_id IS NOT NULL THEN
		SELECT r.name,
			(SELECT COUNT(*) FROM route_points WHERE route_id = p_route_id AND required=1 AND STATUS = 1 )cantPoints,
			(SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = p_route_id AND p.type = 1 AND rp.required = 1 AND rp.STATUS = 1 )pointSales,
			(SELECT COUNT(*) FROM points p INNER JOIN  route_points  rp ON p.id = rp.point_id WHERE rp.route_id = p_route_id AND p.type = 2 AND rp.required = 1 AND rp.STATUS = 1 )exchangeCenters,
			(SELECT COUNT(*) FROM route_points WHERE route_id = p_route_id AND required = 1  AND STATUS = 1 )required,
			(SELECT SUM(DATE_FORMAT(arrival_time,'%i')) FROM route_points WHERE route_id = p_route_id AND required = 1  AND STATUS = 1 )totalTime,
			CASE CLOSE WHEN 0 THEN 'ABIERTA' WHEN 1 THEN 'CERRADA' END estatus
		FROM routes r 
		WHERE r.id = p_route_id;
	END IF;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routes_borrar`(IN
		p_id int)
BEGIN
		delete from routes where id = p_id;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routes_guardar`(IN `p_id` int, IN `p_code` varchar(255), IN `p_name` VARCHAR(255), IN `p_status` int, IN `p_capacity` varchar(50), IN `p_factor` double, IN `p_close` tinyint, IN `p_controller_id` int, IN `p_zone_id` int, IN `p_franchisee_id` int)
BEGIN
		if(p_id is null) then
			insert into routes(id, CODE,NAME, STATUS,capacity, factor,CLOSE, controller_id, zone_id, franchisee_id)
				values(null,p_code, p_name, p_status, p_capacity, p_factor, p_close, p_controller_id, p_zone_id, p_franchisee_id);
		else
			update routes 
				set code = p_code,
				NAME = p_name, 
				STATUS = p_status,
				capacity = p_capacity, 
				factor = p_factor,
				CLOSE = p_close, 
				controller_id = p_controller_id, 
                zone_id = p_zone_id,
                franchisee_id = p_franchisee_id
			WHERE id = p_id;
		end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routes_listar`(IN
	p_idsesion VARCHAR(20),
	p_page INT, 
	p_limit int,
	p_sidx varchar(256),
	p_sord varchar(4),
	p_id int
	)
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	DECLARE p_idusuario INT DEFAULT 0 ;
	set p_start=(p_page-1)*p_limit;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	DROP TABLE IF EXISTS _routes;
	CREATE TEMPORARY TABLE IF NOT EXISTS _routes AS 
	(SELECT routes.*,CONCAT_WS(' ',users.first_name,users.last_name) franchisee,case routes.status when 1 then 'Activa' ELSE 'Inactiva' end estatus, CASE CLOSE WHEN 1 THEN 'Cerrada' ELSE 'Abierta' END estado from routes 
    LEFT JOIN users ON routes.franchisee_id = users.id 
        where (p_id is null or routes.id = p_id)
		AND controller_id = p_idusuario
		);
    if p_sord LIKE 'desc' THEN
	SELECT * FROM _routes order by 
	Case p_sidx
		When 'code' then
			upper(code)
		When 'name' THEN
			upper(name)
		WHEN 'estatus' THEN
			upper(estatus)
		WHEN 'factor' THEN
			factor
            WHEN 'capacity' THEN
			capacity
		WHEN 'estado' THEN
			upper(estado)
		Else
			id
	end
	desc LIMIT p_start, p_limit;
    else 
    SELECT * FROM _routes order by 
	Case p_sidx
		When 'code' then
			upper(code)
		When 'name' THEN
			upper(name)
		WHEN 'estatus' THEN
			upper(estatus)
		WHEN 'factor' THEN
			factor
            WHEN 'capacity' THEN
			capacity
		WHEN 'estado' THEN
			upper(estado)
		Else
			id
	end
	ASC LIMIT p_start, p_limit;
    END IF;
	SELECT count(*) records,  p_page page, ceil(count(*)/p_limit) totalpages from _routes;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_routes_toggleOpenClose`(IN
		p_routeId int,
		p_openClose InT)
BEGIN
	declare p_initialPointId int;
	DECLARE p_initialPointType INT;
	DECLARE p_finalPointId INT;
	DECLARE p_finalPointType INt;
	if p_openClose = 1 then
		SELECT rp.point_id, p.type into p_initialPointId, p_initialPointType FROM route_points rp
			INNER JOIN points p ON  rp.point_id = p.id
			WHERE rp.route_id = p_routeId AND rp.status =1 AND rp.required = 1
			ORDER BY order_number
			LIMIT 0,1;
		SELECT rp.point_id, p.type INTO p_finalPointId, p_finalPointType FROM route_points rp
			INNER JOIN points p ON  rp.point_id = p.id
			WHERE rp.route_id = p_routeId AND rp.status =1 AND rp.required = 1
			ORDER BY order_number desc
			LIMIT 0,1;
		if not (p_initialPointType = 2 and p_finalPointType = 2) then
			call sp_error('U','Para poder cerrar una ruta, el punto inicial y final deben ser de tipo centro de intercambio.');
		end if;
	end if;
	UPDATE routes  set close = p_openClose where id = p_routeId;		
	SELECT *,CASE STATUS WHEN 1 THEN 'Activa' ELSE 'Inactiva' END estatus, CASE CLOSE WHEN 1 THEN 'Cerrada' ELSE 'Abierta' END estado FROM routes WHERE id=p_routeId;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_schedule_delete`(IN p_id INT)
BEGIN
	DELETE FROM schedule where id = p_id;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_schedule_list`(in
		p_idsesion VARCHAR(20),
		p_page INT, 
		p_limit INT,
		p_sidx VARCHAR(256),
		p_sord VARCHAR(4),
		p_id INT)
BEGIN
		DECLARE p_start INT DEFAULT 0 ;
		DECLARE p_idusuario INT DEFAULT 0 ;
		SET p_start=(p_page-1)*p_limit;
		CALL sp_sesion_datos(p_idsesion, p_idusuario);
		DROP TABLE IF EXISTS _schedules;
		CREATE TEMPORARY TABLE IF NOT EXISTS _schedules AS 
		(
		SELECT sc.id,sc.route_id,sc.vehicle_id,sc.user_id,sc.status,sc.monday,sc.tuesday,sc.wednesday,sc.thursday,sc.friday,sc.saturday,sc.sunday,sc.recurrent,sc.week,sc.scheduleParent_id,sc.end_date,ro.name route, us.first_name driver, ve.name vehicle, sc.start_date,
			case sc.status when 1 then 'Activa' else 'Inactiva' end estatus,
			case sc.recurrent when 1 then 'Si' ELSE 'No' end periodica,
			CASE sc.week WHEN 1 THEN 'Si' ELSE 'No' end semanal
		FROM schedule sc
			LEFT JOIN vehicles ve ON sc.vehicle_id = ve.id
			LEFT JOIN users us ON sc.user_id = us.id
			LEFT JOIN routes ro ON sc.route_id = ro.id
			INNER JOIN (SELECT DISTINCT sc.route_id,MAX(sc.start_date) start_date
				FROM schedule sc LEFT JOIN routes ro ON sc.route_id = ro.id 
				WHERE ro.controller_id = p_idusuario GROUP BY sc.route_id) maxDates
			ON sc.route_id = maxDates.route_id AND sc.start_date =  maxDates.start_date
			where (p_id is null or sc.id=p_id)
		);		
		if p_sord = 'desc' then
			SELECT * FROM _schedules  
			ORDER BY 
				CASE p_sidx
					WHEN 'start_date' THEN
						start_date
					ELSE id 
				END
			desc	
			LIMIT p_start, p_limit;
		ELSE
			SELECT * FROM _schedules  
			ORDER BY 
				CASE p_sidx
					WHEN 'start_date' THEN
						start_date
					ELSE id 
				END		
			LIMIT p_start, p_limit;
		end if;
		SELECT COUNT(*) records,  p_page page, CEIL(COUNT(*)/p_limit) totalpages FROM _schedules;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_schedule_save`(
p_id int,
p_route_id int,
p_vehicle_id int,
p_user_id int,
p_start_date datetime,
p_status int,
p_monday int,
p_tuesday int,
p_wednesday int,
p_thursday int,
p_friday int,
p_saturday int,
p_sunday int,
p_recurrent int,
p_week int,
p_scheduleParent_id int,
p_end_date datetime
)
BEGIN
	IF p_id is null then
		INSERT INTO schedule(id,route_id,vehicle_id,user_id,start_date,status,monday,tuesday,wednesday,thursday,friday,saturday,sunday,recurrent,week,scheduleParent_id,end_date)
			values(null,p_route_id,p_vehicle_id,p_user_id,p_start_date,p_status,p_monday,p_tuesday,p_wednesday,p_thursday,p_friday,p_saturday,p_sunday,p_recurrent,p_week,p_scheduleParent_id,p_end_date);
	ELSE
		UPDATE schedule
			set   route_id = p_route_id,
			vehicle_id = p_vehicle_id,
			user_id = p_user_id,
			start_date = p_start_date,
			status = p_status,
			monday = p_monday,
			tuesday = p_tuesday,
			wednesday = p_wednesday,
			thursday = p_thursday,
			friday =  p_friday,
			saturday = p_saturday,
			sunday = p_sunday,
			recurrent = p_recurrent,
			week = p_week,
			scheduleParent_id = p_scheduleParent_id,
            end_date = p_end_date
            WHERE id = p_id;
	END IF ;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_sesion_crear`(
		IN p_idusuario int
    )
BEGIN
		declare l_idsesion varchar(20) default null;
		set l_idsesion = concat(DATE_FORMAT(NOW(),'%Y%m%d%H%i%s'),LPAD(p_idusuario,4,'0'));
		insert into sesiones values(l_idsesion, p_idusuario, now());
		select * from sesiones where idsesion = l_idsesion;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_sesion_datos`(
    IN
    p_idsesion varchar(20),
    OUT
    p_idusuario int)
BEGIN
		select idusuario into p_idusuario from sesiones where idsesion = p_idsesion;	
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_sesion_validar`(IN
	p_idsesion varchar(20),
	out 
	p_idusuario int)
BEGIN
		select idusuario into p_idusuario from sesiones where idsesion = p_idsesion;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_stateCatalog_list`(
p_id int)
BEGIN
	select * from states where (p_id is null or id = p_id) order by name;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_testhandler`()
BEGIN
		declare TABLE_NOT_FOUND inT default 0;
		declare continue handler for 1051 SET TABLE_NOT_FOUND = 1;
		 begin
			drop table test;
		 end;
		IF TABLE_NOT_FOUND = 1 then
			SELECT 'Tabla no existe' result;
		END if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_userCatalog_list`(p_idsesion varchar(20), p_name varchar(50))
BEGIN
	DECLARE p_idusuario int DEFAULT 0 ;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	SELECT id,first_name driver FROM users u
			where (p_name is null or u.first_name LIKE CONCAT('%',p_name,'%'))
			and u.type = 2;
			/*and u.parent_id = p_idusuario;*/
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_user_zone_borrar`(IN
	p_id int
	)
BEGIN	
	if(p_id is not null) then
    delete from user_zone where user_id = p_id;
    end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_user_zone_guardar`(IN
	p_id int,
	p_zone_id int,
    p_eliminar varchar(16)
	)
BEGIN	
	declare l_id INT(11) ;
    if(p_eliminar is not null and p_id is not null) then
		delete from user_zone where user_id = p_id;
	elseif(p_id is null) then 
        select MAX(id) FROM users into l_id;
			insert into user_zone(user_id, zone_id)
				values(l_id, p_zone_id);
		else 
        BEGIN
        delete from user_zone where user_id = p_id and zone_id = p_zone_id;
        insert into user_zone(user_id, zone_id) values (p_id, p_zone_id);
        END;
		End if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_user_zone_obtener`(IN
	p_id int
	)
BEGIN	
SELECT zone_id FROM user_zone WHERE user_id = p_id;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_users_borrar`(IN
    p_id int)
BEGIN
		DELETE from users where id = p_id;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_users_guardar`(IN
	p_id int,
	p_category_id int,
	p_parent_id int,
	p_point_id int,
	p_code varchar(20),
	p_status int,
	p_first_name varchar(50),
	p_last_name VARCHAR(50),
	p_username varchar(30),
	p_password varchar(32),
	p_title varchar(100),
	p_commercial_name varchar(100),
	p_cell_phone varchar(30),
	p_local_number varchar(10),
	p_type int,
	p_dayInvoice int,
    p_moneda_id int
	)
BEGIN
	declare l_password varchar(32) default '';
		if(p_id is null) then 
			insert into users(id, category_id, parent_id, point_id, CODE, STATUS, first_name, last_name, username, password, title, commercial_name, cell_phone, local_number, TYPE, role_id,dayInvoice, moneda_id)
				values(null, p_category_id, p_parent_id, p_point_id, p_code, p_status, p_first_name, p_last_name, p_username, md5(p_PASSWORD), p_title, p_commercial_name, p_cell_phone, p_local_number, p_type, p_type, p_dayInvoice, p_moneda_id);
		else
			BEGIN
				select password into l_password from users where id=p_id;
				if (l_password <> p_password) then
					set p_password = md5(p_password);
				End if;
				UPDATE users 
					set	category_id = p_category_id, 
						parent_id = p_parent_id, 
						point_id = p_point_id,
						CODE = p_code,
						STATUS = p_status,
						first_name = p_first_name, 
						last_name = p_last_name, 
						username = p_username, 
						PASSWORD = p_password,
						title = p_title, 
						commercial_name = p_commercial_name, 
						cell_phone = p_cell_phone, 
						local_number = p_local_number, 
						type = p_type, 
						dayInvoice = p_dayInvoice,
						role_id = p_type,
                        moneda_id = p_moneda_id
				WHERE id = p_id;
			END;
		End if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_users_listar`(IN
	p_idsesion VARCHAR(20),
	p_page INT, 
	p_limit INT,
	p_sidx VARCHAR(256),
	p_sord VARCHAR(4),
	p_id INT,
	p_userType Int,
    pClave varchar(255),
    pNombre varchar(255),
    pApellidos varchar(255),
    pUsuario varchar(255)
    )
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	DECLARE p_idusuario INT DEFAULT 0 ;
	SET p_start=(p_page-1)*p_limit;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	drop table IF EXISTS _users;
	CREATE TEMPORARY TABLE IF NOT EXISTS _users AS 
	(
		SELECT * FROM users
			WHERE (p_id IS NULL OR id = p_id)
			and (p_userType is null or type = p_userType)
            and (pClave is null or code like pClave)
            and (pNombre is null or first_name like pNombre)
            and (pApellidos is null or last_name like pApellidos)
            and (pUsuario is null or username like pUsuario)
			AND parent_id = p_idusuario
		);
	IF p_sord = 'asc' then
		SELECT us.*,ca.title category, 
			CASE type 
				WHEN 1 then 'Administrador'
				WHEN 2 THEN 'Conductor'
				WHEN 3 THEN 'Cliente'
				WHEN 4 THEN 'Secretaria'
				WHEN 6 THEN 'Almacenista'
				WHEN 7 THEN 'Controlador de Operaciones'
				WHEN 8 THEN 'Franquisiatario'
				End tipo
			FROM _users us 
			left join categories ca on us.category_id = ca.id
			order by 
				case p_sidx
					when 'code' then code
					when 'commercial_name' then  commercial_name
					when 'category' then category
					when 'first_name' then first_name
					when 'last_name' then last_name
					when 'username' then username
					when 'tipo' then tipo
					else us.id
				end ASC
			LIMIT p_start, p_limit;		
		else
			SELECT us.*,ca.title category, 
			CASE type 
				WHEN 1 then 'Administrador'
				WHEN 2 THEN 'Conductor'
				WHEN 3 THEN 'Cliente'
				WHEN 4 THEN 'Secretaria'
				WHEN 6 THEN 'Almacenista'
				WHEN 7 THEN 'Controlador de Operaciones'
				WHEN 8 THEN 'Franquisiatario'
				End tipo
			FROM _users us 
			left join categories ca on us.category_id = ca.id
			order by 
				case p_sidx
					when 'code' then code
					when 'commercial_name' then  commercial_name
					when 'category' then category
					when 'first_name' then first_name
					when 'last_name' then last_name
					when 'username' then username
					when 'tipo' then tipo
					else us.id
				end DESC
			LIMIT p_start, p_limit;
		end if;
	SELECT COUNT(*) records,  p_page page, CEIL(COUNT(*)/p_limit) totalpages FROM _users;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_vehicleCatalog_list`(p_idsesion varchar(20), p_name varchar(50))
BEGIN
	DECLARE p_idusuario int DEFAULT 0 ;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	SELECT id,name FROM vehicles v
			where (p_name is null or v.name LIKE CONCAT('%',p_name,'%'))
			and v.driver_id = p_idusuario;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_vehicles_borrar`(
    p_id int)
BEGIN
		delete from vehicles where id=p_id;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_vehicles_guardar`(
    p_id int,
    p_type int,
    p_volume FLOAT,
	p_economic_number varchar(100),
	p_name varchar(100),
	p_capacity FLOAT,
	p_status INT,
	p_trade_mark varchar(100),
	p_plate varchar(50),
	p_color varchar(20),
	p_gps varchar(30),
	p_model varchar(20),
	p_width FLOAT,
	p_height FLOAT,
	p_deep FLOAT,
	p_driver_id INT
    )
BEGIN
		if p_id is null then
			insert into vehicles( id, type, volume, economic_number, name, capacity, status, trade_mark, plate, color, gps, model, width, height, deep,driver_id)
			values(null, p_TYPE, p_volume, p_economic_number, p_NAME, p_capacity, p_STATUS, p_trade_mark, p_plate, p_color, p_gps, p_model, p_width, p_height, p_deep,p_driver_id);
		else
			Update vehicles
				set type=p_TYPE, volume=p_volume, economic_number=p_economic_number, NAME=p_NAME, capacity=p_capacity, STATUS=p_STATUS, 
				trade_mark=p_trade_mark, plate=p_plate, color=p_color, gps=p_gps, model=p_model, width=p_width, height=p_height, deep=p_deep, driver_id=p_driver_id
				where id=p_id;
		end if;
    END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` PROCEDURE `sp_vehicles_listar`(IN
	p_idsesion varchar(20),
	p_page INT, 
	p_limit INT,
	p_sidx VARCHAR(256),
	p_sord VARCHAR(4),
	p_id int)
BEGIN
	DECLARE p_start INT DEFAULT 0 ;
	Declare p_idusuario int DEFAULT 0 ;
	SET p_start=(p_page-1)*p_limit;
	CALL sp_sesion_datos(p_idsesion, p_idusuario);
	CREATE TEMPORARY TABLE IF NOT EXISTS _vehicles AS 
	(
		SELECT * FROM vehicles v
			where (p_id is null or v.id = p_id)
			and v.driver_id = p_idusuario
		);
	if p_sord LIKE 'desc' THEN
    SELECT *, case when type=1 then 'Caja Seca' else 'Desconocido' End tipo  FROM _vehicles 
		ORDER BY 
		CASE p_sidx
			WHEN 'name' THEN name
			WHEN 'tipo' THEN type
			WHEN 'volume' THEN volume
			WHEN 'economic_number' THEN economic_number
			WHEN 'capacity' THEN capacity
			WHEN 'trade_mark' THEN trade_mark
			WHEN 'plate' THEN plate
			WHEN 'color' THEN color
			WHEN 'gps' THEN gps
			WHEN 'model' THEN model
			WHEN 'width' THEN width
			WHEN 'height' THEN height
			WHEN 'deep' THEN deep
			ELSE id
		END
	 DESC LIMIT p_start, p_limit;
    ELSE
    SELECT *, case when type=1 then 'Caja Seca' else 'Desconocido' End tipo  FROM _vehicles 
		ORDER BY 
		CASE p_sidx
			WHEN 'name' THEN name
			WHEN 'tipo' THEN type
			WHEN 'volume' THEN volume
			WHEN 'economic_number' THEN economic_number
			WHEN 'capacity' THEN capacity
			WHEN 'trade_mark' THEN trade_mark
			WHEN 'plate' THEN plate
			WHEN 'color' THEN color
			WHEN 'gps' THEN gps
			WHEN 'model' THEN model
			WHEN 'width' THEN width
			WHEN 'height' THEN height
			WHEN 'deep' THEN deep
			ELSE id
		END
	 ASC LIMIT p_start, p_limit;
    END IF;
	SELECT COUNT(*) records,  p_page page, CEIL(COUNT(*)/p_limit) totalpages FROM _vehicles;
    END$$
DELIMITER ;
