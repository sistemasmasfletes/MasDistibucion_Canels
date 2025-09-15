DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` FUNCTION `func_ValidarPunto`(`scheduled_route` INT, `routePointId` INT, `stardate` DATETIME) RETURNS int(11)
BEGIN
DECLARE verRet int default 0;
set verRet :=(SELECT count(rpa.id) FROM routepoint_activity rpa
inner join route_points rp on rp.id =rpa.routepoint_id
where rpa.scheduledRoute_id = scheduled_route and rp.id=routePointId);
RETURN verRet;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` FUNCTION `func_time_list`(`routeId` INT, `orderNumber` INT, `startdate` DATETIME) RETURNS datetime
BEGIN
DECLARE factorial int;
DECLARE hours int;
DECLARE minutes int;
DECLARE secondes int;
DECLARE retVal datetime;
set factorial:=(select sum(rp.arrival_time) from route_points rp where rp.route_id=routeId and order_number <orderNumber);
set minutes:= factorial/100;
set hours:= factorial/10000;
set secondes:= factorial%100;
set retVal:= startdate;
if(minutes > 0) then
	set retVal:=  DATE_ADD(retVal, INTERVAL minutes minute);
end if;
if(secondes > 0) then
	set retVal:=  DATE_ADD(retVal, INTERVAL secondes second);
end if;
RETURN retVal;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`adminhub`@`localhost` FUNCTION `hora_actual_punto`(`scheduled_route` INT, `routePointId` INT, `pointId` INT, `stardate` DATETIME) RETURNS datetime
BEGIN
DECLARE verRet datetime default now();
DECLARE intRet int default 0;
DECLARE countRet int default 0;
set countRet :=(SELECT count(rpa.id) FROM routepoint_activity rpa
inner join route_points rp on rp.id =rpa.routepoint_id
where rpa.scheduledRoute_id = scheduled_route and rp.id=routePointId and rpa.date<=stardate and rpa.hora_actual is not null);
set intRet :=(SELECT count(rpa.id) FROM routepoint_activity rpa
inner join route_points rp on rp.id =rpa.routepoint_id
where rpa.scheduledRoute_id = scheduled_route and rp.id=routePointId and rpa.date<=stardate);
if(intRet >0) then
	if(intRet = countRet)then
		set verRet:=(
		SELECT MIN(rpa.hora_actual) FROM routepoint_activity rpa
		inner join route_points rp on rp.id =rpa.routepoint_id
		where rpa.scheduledRoute_id = scheduled_route and rp.id=routePointId  limit 1);
	else
		set verRet:=null;
	end if;
else
set verRet:=(
		SELECT MIN(rpa.hora_actual) FROM routepoint_activity rpa
		inner join route_points rp on rp.id =rpa.routepoint_id
		where rpa.scheduledRoute_id = scheduled_route and rp.id=routePointId  limit 1);
end if;
RETURN verRet;
END$$
DELIMITER ;
