<?php

use com\masfletes\db\DBUtil;

class Driver_RouteSummaryController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }

    public function indexAction() {
        
    }

    //FUNCIÓN RUTAS DEL CHOFER
    public function getRouteSummaryAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $sidx = $this->getArrayValue('sortField', $params);

        $id = $this->getArrayValue('id', $filter);
        $Fecha = $this->getArrayValue('Fecha', $filter);
        $Hora = $this->getArrayValue('Hora', $filter);
        $Nombre = $this->getArrayValue('Nombre', $filter);
        $Formato = $this->getArrayValue('Formato', $filter);
        $HoraActual = $this->getArrayValue('HoraActual', $filter);
        $Codigo = $this->getArrayValue('Codigo', $filter);
        $Vehiculo = $this->getArrayValue('Vehiculo', $filter);
        $Estado = $this->getArrayValue('Estado', $filter);

        if (!$sidx)
            $sidx = 1;


        try {

            $Fecha = ($Fecha != null) ? substr(str_replace('T', ' ', $Fecha), 0, 10) : null;
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $routeSummaryRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
            $rsummary = $routeSummaryRepo->getRouteSummaryListDQL($page, $rowsPerPage, $sortField, $sortDir, $sidx, $id, $Fecha, $Hora, $Nombre, $Formato, $HoraActual, $Codigo, $Vehiculo, $Estado);

            echo json_encode($rsummary);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'id', 'Fecha', 'Hora', 'Nombre', 'Formato', 'HoraActual', 'Codigo', 'Vehiculo');
            $this->logAndResolveException($ex, $params);
        }
    }

    //FUNCION PUNTOS DE LA RUTA
    public function getRouteSummaryPointsAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $stateParams = $this->getArrayValue('stateParams', $params);
        $idp = $this->getArrayValue('idrow2', $params);
        $id2 = $this->getArrayValue('id2', $stateParams);
        $idrow = $this->getArrayValue('idrow', $params);
        $id = $this->getArrayValue('id', $stateParams);
        $sidx = $this->getArrayValue('sortField', $params);

        $id = $this->getArrayValue('id', $filter);
        $Codigo = $this->getArrayValue('Codigo', $filter);
        $Hora = $this->getArrayValue('Hora', $filter);
        $Formato = $this->getArrayValue('Formato', $filter);
        $Nombre = $this->getArrayValue('Nombre', $filter);
        $Direccion = $this->getArrayValue('Direccion', $filter);
        $Actividad = $this->getArrayValue('Actividad', $filter);
        $Comentarios = $this->getArrayValue('Comentarios', $filter);
        $HoraActual = $this->getArrayValue('HoraActual', $filter);
        $Progreso = $this->getArrayValue('Progreso', $filter);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

            $rpoints = $routePointsRepo->getRouteSummaryPointsListDQL($page, $rowsPerPage, $sortField, $sortDir, $id, $Codigo, $Hora, $Formato, $Nombre, $Direccion, $Actividad, $Comentarios, $HoraActual, $Progreso, $idrow, $idp, $stateParams, $id, $id2);

            echo json_encode($rpoints);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'id', 'Codigo', 'Hora', 'Formato', 'Nombre', 'Direccion', 'Actividad', 'Comentarios', 'HoraActual', 'Progreso', 'idrow2', 'idp', 'stateParams', 'id', 'id2');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getRouteSummaryPoints1Action() {
    	$params = $this->getRequest()->getPostJson();
    
    	$page = $this->getArrayValue('page', $params);
    	$rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
    	$sortField = $this->getArrayValue('sortField', $params);
    	$sortDir = $this->getArrayValue('sortDir', $params);
    	$filter = $this->getArrayValue('filter', $params);
    	$stateParams = $this->getArrayValue('stateParams', $params);
    	$idp = $this->getArrayValue('idrow2', $params);
    	$id2 = $this->getArrayValue('id2', $stateParams);
    	$idrow = $this->getArrayValue('idrow', $params);
    	$id = $this->getArrayValue('id', $stateParams);
    	$sidx = $this->getArrayValue('sortField', $params);
    
    	$id = $this->getArrayValue('id', $filter);
    	$Codigo = $this->getArrayValue('Codigo', $filter);
    	$Hora = $this->getArrayValue('Hora', $filter);
    	$Formato = $this->getArrayValue('Formato', $filter);
    	$Nombre = $this->getArrayValue('Nombre', $filter);
    	$Direccion = $this->getArrayValue('Direccion', $filter);
    	$Actividad = $this->getArrayValue('Actividad', $filter);
    	$Comentarios = $this->getArrayValue('Comentarios', $filter);
    	$HoraActual = $this->getArrayValue('HoraActual', $filter);
    	$Progreso = $this->getArrayValue('Progreso', $filter);
    
    	if (!$sidx)
    		$sidx = 1;
    
    	try {
    		$this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');
    
    		$em = $this->getEntityManager('DefaultDb');
    		$routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
    
    		$rpoints = $routePointsRepo->getRouteSummaryPointsListDQL1($page, $rowsPerPage, $sortField, $sortDir, $id, $Codigo, $Hora, $Formato, $Nombre, $Direccion, $Actividad, $Comentarios, $HoraActual, $Progreso, $idrow, $idp, $stateParams, $id, $id2);
    
    		echo json_encode($rpoints);
    	} catch (Exception $ex) {
    		$params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'id', 'Codigo', 'Hora', 'Formato', 'Nombre', 'Direccion', 'Actividad', 'Comentarios', 'HoraActual', 'Progreso', 'idrow2', 'idp', 'stateParams', 'id', 'id2');
    		$this->logAndResolveException($ex, $params);
    	}
    }
    
    //FUNCIÓN DE PAQUETES POR PUNTO
    public function getRouteSummaryPackageAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);

        $idrow = $this->getArrayValue('idrow', $params);
        $idrow2 = $this->getArrayValue('idrow2', $params);

        $stateParams = $this->getArrayValue('stateParams', $params);
        $id2 = $this->getArrayValue('id2', $stateParams);
        $id = $this->getArrayValue('id', $stateParams);

        $sidx = $this->getArrayValue('sortField', $params);

        $Paquete = $this->getArrayValue('Paquete', $filter);
        $Estado = $this->getArrayValue('Estado', $filter);
        $Actividad = $this->getArrayValue('Actividad', $filter);
        $routePointActivityId = $this->getArrayValue('routePointActivityId', $params);
        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

            $rpoints = $routePointsRepo->getRouteSummaryPackageListDQL($page, $rowsPerPage, $sortField, $sortDir, $idrow, $idrow2, $stateParams, $id2, $id, $Paquete, $Estado, $Actividad, $routePointActivityId);

            echo json_encode($rpoints);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'idrow', 'idrow2', 'stateParams', 'id2', 'id', 'Paquete', 'Estado', 'Actividad');
            $this->logAndResolveException($ex, $params);
        }
    }

    //FUNCIÓN DE PAQUETES POR PUNTO
    public function getActivityPackageAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);

        $idrow = $this->getArrayValue('idrow', $params);
        $idrow2 = $this->getArrayValue('idrow2', $params);

        $stateParams = $this->getArrayValue('stateParams', $params);
        $scheduledRouteId = $this->getArrayValue('id', $stateParams);
        $routepointid = $this->getArrayValue('id1', $stateParams);

        $sidx = $this->getArrayValue('sortField', $params);

        $Paquete = $this->getArrayValue('Paquete', $filter);
        $Estado = $this->getArrayValue('Estado', $filter);
        $Actividad = $this->getArrayValue('Actividad', $filter);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

            $rpoints = $routePointsRepo->getActivityPackageListDQL($page, $rowsPerPage, $sortField, $sortDir, $idrow, $idrow2, $stateParams, $scheduledRouteId, $routepointid, $Paquete, $Estado, $Actividad);

            echo json_encode($rpoints);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'idrow', 'idrow2', 'stateParams', 'id2', 'id', 'Paquete', 'Estado', 'Actividad');
            $this->logAndResolveException($ex, $params);
        }
    }

    //FUNCION COUNT DE PAQUETES PARA NAVEGACION DE PANTALLAS
    public function getCountPacksAction() {
        $params = $this->getRequest()->getPostJson();

        $idschedule = $this->getArrayValue('id2', $params);
        $routePointId = $this->getArrayValue('id', $params);


        $em = $this->getEntityManager('DefaultDb');
        $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

        $rpoints = $routePointsRepo->getCountPacks($idschedule, $routePointId);

        echo json_encode($rpoints);
    }

    //FUNCION SALVAR EVIDENCIA DE PAQUETES EN PUNTO DE VENTA
    //2017-05-20 Se agregó el guardado del estatus en la tabla Transactions
    public function saveAction() {

        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $status = $this->getArrayValue('status', $params);
        $receptor = $this->getArrayValue('receptor', $params);
        $comentarios = $this->getArrayValue('comentarios', $params);
        $routePointActivityId = $this->getArrayValue('routePointActivityId', $params);
        $uploadFile = '.png';
        //$uploadFile =$scheduledRouteActivityId.'.png';
        $date = new DateTime();
        $causeId = $this->getArrayValue('causeId', $params);
        $OCId = $this->getArrayValue('transactionid', $params);
        $ttype = $this->getArrayValue('ttype', $params);
        try {

            if ($causeId != NULL) {
                $transaction = $this->sendToCI($OCId, $routePointActivityId);
            }

            //$this->hasPermission($this->getUserSessionId(), 'Route Summary', 'agregar');

            $em = $this->getEntityManager('DefaultDb');
            $causesRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');
            $activityDetailId = $causesRepo->saveEvidence($id, $status, $receptor, $comentarios, $uploadFile, $routePointActivityId, $date, $causeId);

            //Guardar estatus en la tabla Transactions
            $transRepo = $em->getRepository('DefaultDb_Entities_Transactions');
            $transRepo->saveStatus(
                    $OCId, $status, $routePointActivityId, $ttype);

            if ($causeId == NULL) {
                $this->CheckUpdatePago($routePointActivityId);
                if($ttype == 1){
                	$orderdat = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' => $OCId));
                	$correo = "<html><body>
            			<span>Hola ".$orderdat->getBuyer()->getFirstName()." ".$orderdat->getBuyer()->getLastName().",<br />
            			te informamos que tu paquete con folio:".$orderdat->getId()."<br />
            			esta en camino a tu domicilio.<br >
            			Que tenga un excelente d&iactue;a.<br >
            		</body></html>";
                	$mailsend = $this->sendMail($correo,"notificacionesmasdistribucion@gmail.com",$orderdat->getBuyer()->getMail()/*donde llega*/,"Aviso paquete en camino");
                	$data = array(
                			'title' => 'Paquete en camino',
                			'body' => 'Tu paquete con folio: '.$orderdat->getId().' esta en camino'
                	);
                	$this->sendPushNotification($orderdat->getBuyer()->getToken(),  $data);
                }
            }
            echo json_encode($activityDetailId);
        } catch (Exception $ex) {
        	echo $ex->getMessage();
            $params = compact('id', 'status', 'receptor', 'comentarios', 'routePointActivityId', 'uploadFile', 'causeId');
            $this->logAndResolveException($ex, $params);
        }
    }

    /*     * *****************************FUNCION PARA ENVIAR PAQUETE RECHAZADO EN PV DESTINO A CI MAS CERCANO************************************* */

    public function sendToCI($OCId, $routePointActivityId) {

        $em = $this->getEntityManager('DefaultDb');

        //$datatrans = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId' => $OCId));
        $dataRPA = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->findOneBy(array('id' => $routePointActivityId));
        $routeId = $dataRPA->getRoutePoint()->getRoute()->getId();
        $maxpoint = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->getMaxPoints($routeId);
        $maxpointid = $maxpoint[0]['id'];
        $transactionid = $dataRPA->getTransaction()->getId();
        $scheduledid = $dataRPA->getScheduledRoute()->getId();
        $routePointId = $dataRPA->getRoutePoint()->getId();
        $nuevafecha = strtotime('+13 minute', strtotime($dataRPA->getDate()->format('Y-m-d H:i:s')));
        $nuevafecha = date('Y-m-j H:i:s', $nuevafecha);
        $sequentialdata = $em->getRepository('DefaultDb_Entities_SequentialActivities')->findOneBy(array('order' => $OCId, 'routePoint' => $routePointId));
        $routedate = strtotime($sequentialdata->getRouteDate()->format('Y-m-d H:i:s'));
        $routedate = date('Y-m-j H:i:s', $routedate);

        $newRPA = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->saveNewRPA($nuevafecha, $maxpointid, $transactionid, $scheduledid, $OCId, $routedate);

        return $newRPA;
    }

    /*     * *****************************FUNCION PARA ENVIAR PAQUETE RECHAZADO EN PV DESTINO A CI MAS CERCANO************************************* */

    //SALVAR EVIDENCIA SIN FOTOGRAFIA EN CENTRO DE INTERCAMBIO
    public function saveEvidenceCIAction() {
        $params = $this->getRequest()->getPostJson();

        $receptor = $this->getArrayValue('receptor', $params);
        $routePointActivityId = $this->getArrayValue('routePointActivityId', $params);
        $status = $this->getArrayValue('status', $params);
        $comentarios = 'Evidencia salvada en CI';
        $date = new DateTime();
        $OCId = $this->getArrayValue('id', $params); ///////////////////EAAL////////////////

        try {
            $em = $this->getEntityManager('DefaultDb');

            /*             * ***************************RECHAZO DE PAQUETES EAAL*************************************** */
            $dataTrns = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId' => $OCId, 'transactionType' => 1));
            /* $transaction = $em->getRepository('DefaultDb_Entities_Transactions');
              $dataTrns = $transaction->createQueryBuilder('f')///OBTIENE LOS PUNTOS ENTRE RECOLECCION Y ENTREGA DEL PAQUETE PARA CADA RUTA EN LA FECHA POR LA QUE PASA EL PAQUETE
              ->where("f.transactionId = :transactionId AND f.transactionType IS NOT NULL")
              ->setParameter('transactionId',$OCId)
              ->setMaxResults(1)
              ->getQuery()->getSingleResult(); */

            $dataOrder = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' => $OCId));

            $dataOrder->setShippingStatus(4);
            $em->persist($dataOrder);
            $em->flush();

            if ($dataTrns->getStatus() == 6) {
                $dataRPA = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->findOneBy(array('id' => $routePointActivityId));
                $dataRPA->setStatus(2);
                $dataRPA->setStatusReason($comentarios);
                $em->persist($dataRPA);
                $em->flush();
            }
            /*             * ***************************RECHAZO DE PAQUETES EAAL*************************************** */

            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'agregar');

            $em = $this->getEntityManager('DefaultDb');
            $causesRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');
            $causesRepo->saveEvidenceCI(
                    $receptor, $routePointActivityId, $status, $comentarios, $date
            );

            echo json_encode($causesRepo);
        } catch (Exception $ex) {
            $params = compact('receptor', 'routePointActivityId', 'status', 'comentarios', 'date');
            $this->logAndResolveException($ex, $params);
        }
    }

    /*
     * FUNCION QUE ALMACENA EN TABLA DE ACTIVITY_DETAIL
     * LOS PUNTOS QUE NO TIENEN ACTIVIDAD
     * SE DEBEN VISITAR OBLIGATORIAMENTE
     */

    public function savePointEvidenceAction() {
        $params = $this->getRequest()->getPostJson();

        $scheRouteId = $this->getArrayValue('Identificador', $params);
        $pointId = $this->getArrayValue('Point_Id', $params);
        $receptor = 'Sin receptor';
        $comentarios = 'Punto sin actividades';
        $date = new DateTime();

        $em = $this->getEntityManager('DefaultDb');
        $causesRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');
        $causesRepo->saveNoActivityPoint($receptor, $comentarios, $scheRouteId, $pointId, $date);

        echo json_encode($causesRepo);
    }

    //SALVAR HORA ACTUAL DE INICIO DE RUTA
    public function saveCurrentHourAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $startDate = new DateTime();
        $statusRoute = 1;

        $em = $this->getEntityManager('DefaultDb');
        $hourRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');

        $hourRepo->getHourRouteSummary(
                $id, $startDate, $statusRoute);

        echo json_encode($hourRepo);
    }

    //SALVAR HORA ACTUAL DE FINALIZACIÓN DE LA RUTA
    public function saveEndHourRouteAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $endDate = new DateTime();
        $statusRoute = 2;

        $em = $this->getEntityManager('DefaultDb');
        $endHourRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');

        $endHourRepo->getEndHour(
                $id, $endDate, $statusRoute);

        echo json_encode($endHourRepo);
    }

    //OBTENER ID DE SCHEDULEROUTE
    public function getScheduleRouteIdAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);

        $em = $this->getEntityManager('DefaultDb');
        $scheduleRouteIdRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');

        $scheduleRouteIdRepo->getScheduleRouteIdListDQL(
                $id);

        echo json_encode($scheduleRouteIdRepo);
    }

    //SALVAR PROGRESO DE LA RUTA
    public function saveProgressAction() {
        $params = $this->getRequest()->getPostJson();
        $progress = 0;
        $id = $this->getArrayValue('Identificador', $params);
        $progress1 = $this->getArrayValue('progress', $params);
        if ($progress1 != null) {
            $progress = $progress1;
        }

        $progress2 = $this->getArrayValue('Progreso', $params);
        if ($progress2 != null) {
            $progress = $progress2;
        }
        $em = $this->getEntityManager('DefaultDb');
        $progresoRepo = $em->getRepository('DefaultDb_Entities_ScheduledRoute');

        $progresoRepo->addProgress(
                $id, $progress);

        echo json_encode($progresoRepo);
    }

    //SALVAR HORA ACTUAL DE PUNTO CON ACTIVIDADES
    public function saveHourPointAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('routePointActivityId', $params) ? $this->getArrayValue('routePointActivityId', $params):$this->getArrayValue('idactivitypoint', $params);
        $horaActual = new DateTime();

        $em = $this->getEntityManager('DefaultDb');
        $HoraRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity');

        $HoraRepo->saveCurrentHourPoint(
                $id, $horaActual);

        echo json_encode($HoraRepo);
    }

    //CAMBIAR status Y PointId EN TABLA DE TRANSACTIONS
    public function saveStatusAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $status = $this->getArrayValue('status', $params);
        $statusPoint = $this->getArrayValue('pointStatus', $params);
        $ttype = $this->getArrayValue('ttype', $params);

        $em = $this->getEntityManager('DefaultDb');
        $estadoRepo = $em->getRepository('DefaultDb_Entities_Transactions');

        $estadoRepo->saveStatus(
                $id, $status, $statusPoint, $ttype);

        echo json_encode($estadoRepo);
    }

    public function getActivityDetailAction() {

        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);

        $stateParams = $this->getArrayValue('stateParams', $params);

        $id2 = $this->getArrayValue('id2', $stateParams);

        $em = $this->getEntityManager('DefaultDb');
        $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

        $rpoints = $routePointsRepo->getRouteSummaryPackageListDQL($page, $rowsPerPage, $sortField, $sortDir, $id2, $stateParams);

        echo json_encode($rpoints);
    }

    //FUNCIÓN OBTENER ACTIVIDAD DE PAQUETE Y ENVIARLA
    //A ETIQUETA label1 EN PANTALLA SALVAR EVIDENCIA
    public function getActivityNameAction() {
        $params = $this->getRequest()->getPostJson();

        $em = $this->getEntityManager('DefaultDb');
        $nameRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');

        $actname = $nameRepo->getActivityName($params);

        echo json_encode(array('activity'=> $actname));
    }

    //FUNCION INFORMACIÓN DEL PAQUETE
    public function getEvidenceAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $ocId = $this->getArrayValue('ocId', $params);

        //$ocId = $this->getArrayValue('ocId', $filter);
        $sraId = $this->getArrayValue('sraId', $filter);
        $comentarios = $this->getArrayValue('comentarios', $filter);
        $foto = $this->getArrayValue('foto', $filter);
        $puntoVenta = $this->getArrayValue('puntoVenta', $filter);

        $em = $this->getEntityManager('DefaultDb');
        $evRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');

        $evidence = $evRepo->getEvidenceListDQL($page, $rowsPerPage, $sortField, $sortDir, $ocId, $sraId, $comentarios, $foto, $puntoVenta);

        echo json_encode($evidence);
    }

    //Funcion actividades de la ruta; pantalla informativa
    public function getPacksRouteAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $sidx = $this->getArrayValue('sortField', $params);

        $idrow = $this->getArrayValue('idrow', $params);
        $stateParams = $this->getArrayValue('stateParams', $params);
        $id = $this->getArrayValue('id', $stateParams);
        $Paquete = $this->getArrayValue('Paquete', $filter);
        $ptoVenta = $this->getArrayValue('ptoVenta', $filter);


        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $inventoryPackRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');
            $inventory = $inventoryPackRepo->getPacksRouteListDQL($page, $rowsPerPage, $sortField, $sortDir, $sidx, $idrow, $stateParams, $id, $ptoVenta, $Paquete);

            echo json_encode($inventory);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'idrow', 'stateParams', 'id');
            $this->logAndResolveException($ex, $params);
        }
    }

    /*     * ********************PAQUETES NO ENCONTRADOS*************************************** */

    public function packnofoundAction() {
        $params = $this->getRequest()->getPostJson();

        $em = $this->getEntityManager('DefaultDb');

        $OCId = $this->getArrayValue('id', $params);

        $TrnsId = $em->getRepository('DefaultDb_Entities_Transactions')->findOneBy(array('transactionId' => $OCId, 'transactionType' => 1));
        /* $transaction = $em->getRepository('DefaultDb_Entities_Transactions');
          $TrnsId = $transaction->createQueryBuilder('f')
          ->where("f.transactionId = :transactionId AND f.transactionType IS NOT NULL")
          ->setParameter('transactionId',$OCId)
          ->setMaxResults(1)
          ->getQuery()->getSingleResult(); */

        $SecAct = $em->getRepository('DefaultDb_Entities_SequentialActivities')->findBy(array('order' => $OCId));
        $RPAct = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->findBy(array('transaction' => $TrnsId->getId()));
        $PtOrd = $em->getRepository('DefaultDb_Entities_PackageToOrder')->findBy(array('order' => $OCId));
        $Pgs = $em->getRepository('DefaultDb_Entities_Pagos')->findBy(array('compraVenta' => $OCId));
        $Order = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' => $OCId));
        $client = $em->getRepository('DefaultDb_Entities_User')->findOneBy(array('id' => $Order->getSeller()));

        $tcreditos = 0;

        $em->getConnection()->beginTransaction();
        try {
            foreach ($Pgs as $d) {

                $BlPgs = $em->getRepository('DefaultDb_Entities_BalanceGeneral')->findBy(array('pagos' => $d->getId()));
                $LogPgs = $em->getRepository('DefaultDb_Entities_ActividaChoferLog')->findBy(array('pago' => $d->getId()));

                foreach ($LogPgs as $l) {
                	$em->remove($l);
                }
                
                foreach ($BlPgs as $e) {

                    if ((int) $e->getEstatus() == 1) {//Si el monto fue pagado se suma a la variable para el reembolos de los creditos
                        $tcreditos = $tcreditos + (int) $e->getMonto();
                    }
                    $em->remove($e);
                }

                $em->remove($d);
            }

            foreach ($PtOrd as $c) {
                $em->remove($c);
            }

            foreach ($RPAct as $a) {
                $em->remove($a);
            }

            foreach ($SecAct as $b) {
                $em->remove($b);
            }

            $em->flush();

            $Order->setSchedule(NULL);
            $Order->setShippingDate(NULL);
            $Order->setShippingStatus(0);
            $Order->setPointSeller(NULL);
            $Order->setComments('El paquete no estaba listo al momento de la recoleccion');
            $em->persist($Order);

            $client->setCredito($client->getCredito() + $tcreditos); //Se devuelven los creditos referentes a los pagos hechos a la orden cancelada
            $em->persist($client);

            $em->remove($TrnsId);

            $em->flush();

            $em->getConnection()->commit();

            echo json_encode(array('res' => TRUE));
        } catch (Exception $ex) {

            $em->getConnection()->rollback();
            throw $ex;

            $params = compact('id', 'userDelivery', 'userReceiving', 'status', 'userAbsence', 'statusReason', 'entityFrom', 'entityTo');
            $this->logAndResolveException($ex, $params);
        }
    }

    /*     * ********************PAQUETES NO ENCONTRADOS*************************************** */

    //FUNCIÓN DE FAVORITOS POR PUNTO
    public function getRouteSummaryFavoritesAction() {
        $this->view->getJsManager()->addJs('view/helper/element/product.js');
        $this->view->getJsManager()->addJsVar('urlAddToCart', '"' . $this->view->url(array('module' => 'User', 'controller' => 'AjaxCart', 'action' => 'addToCart')) . '"');
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);

        $idrow = $this->getArrayValue('idrow', $params);
        $idrow2 = $this->getArrayValue('idrow2', $params);

        $stateParams = $this->getArrayValue('stateParams', $params);
        $id2 = $this->getArrayValue('id2', $stateParams);
        $id = $this->getArrayValue('id', $stateParams);

        $sidx = $this->getArrayValue('sortField', $params);

        $Paquete = $this->getArrayValue('Paquete', $filter);
        $Estado = $this->getArrayValue('Estado', $filter);
        $Actividad = $this->getArrayValue('Actividad', $filter);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'Route Summary', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $routePointsRepo = $em->getRepository('DefaultDb_Entities_RouteSummary');

            $rpoints = $routePointsRepo->getRouteSummaryFavoritesListDQL($page, $rowsPerPage, $sortField, $sortDir, $idrow, $idrow2);

            echo json_encode($rpoints);
        } catch (Exception $ex) {
            $params = compact('page', 'rowsPerPage', 'sortField', 'sortDir', 'sidx', 'idrow', 'idrow2', 'stateParams', 'id2', 'id');
            $this->logAndResolveException($ex, $params);
        }
    }

    private function CheckUpdatePago($routePointActivityId) {
        $COMPRA = 4;
        $VENTA = 3;
        $PAGO_FLETE = 6;
        $CONGELAR_CREDITOS = 1;
        $RECOLECCION = 1;
        
        $em = $this->getEntityManager('DefaultDb');
        $routePointActivityRepo = $em->getRepository('DefaultDb_Entities_RoutePointActivity')->find($routePointActivityId);
        $pagosRepos = $em->getRepository('DefaultDb_Entities_Pagos');

		if($routePointActivityRepo->getActivityType()->getId() != $RECOLECCION){

        $transactionId = $routePointActivityRepo->getTransaction()->getTransactionId();
        //error_log("TRANSACTION ID " . $transactionId);

        $PagoCompra = $pagosRepos->findOneBy(array('compraVenta' => $transactionId, 'tipoConcepto' => $COMPRA));
        //error_log("PAGO COMPRA ID " . $PagoCompra->getId());

        $PagoVenta = $pagosRepos->findOneBy(array('compraVenta' => $transactionId, 'tipoConcepto' => $VENTA));
        //error_log("PAGO VENTA ID " . $PagoVenta->getId());

        $PagoFlete = $pagosRepos->findOneBy(array('compraVenta' => $transactionId, 'tipoConcepto' => $PAGO_FLETE));
        //error_log("PAGO VENTA ID " . $PagoFlete->getId());

        $balanceRepos = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
        if($PagoCompra){
        $balanceCompra = $balanceRepos->findOneBy(array('pagos' => $PagoCompra->getId()), array('id' => 'desc'));
        //error_log("BALANCE COMPRA ID " . json_encode($balanceCompra->getId()));
        //error_log("ESTATUS BALANCE COMPRA " . $balanceCompra->getEstatus());
		}
		
		if($PagoVenta){
        $balanceVenta = $balanceRepos->findOneBy(array('pagos' => $PagoVenta->getId()), array('id' => 'desc'));
        //error_log("BALANCE COMPRA ID " . json_encode($balanceVenta->getId()));
        //error_log("ESTATUS BALANCE VENTA " . $balanceVenta->getEstatus());
		}
		
        $balancePagoFlete = $balanceRepos->findOneBy(array('pagos' => $PagoFlete->getId()), array('id' => 'desc'));
        //error_log("BALANCE FLETE ID " . json_encode($balanceVenta->getId()));
        //error_log("ESTATUS BALANCE FLETE " . $balanceVenta->getEstatus());

        if(isset($balanceVenta)){
        if ($balanceVenta->getEstatus() == 2) {

            //SET PAGADO VENTA
            $balanceVenta->setEstatus(1);
            $balanceVenta->setIngresos($balanceVenta->getMonto());
            $montoBalanceAnteorior = $balanceVenta->getBalance();
            $balanceVenta->setBalance($montoBalanceAnteorior + $balanceVenta->getMonto());
            $em->persist($balanceVenta);
            error_log("MONTO BALANCE VENTA ACTUALIZADO ".$balanceVenta->getBalance());
             error_log("MONTO CREDITOS PAGO FLETE ". $balancePagoFlete->getEgresos());
            $balanceFlete =  $balanceVenta->getBalance() - $balancePagoFlete->getEgresos() ;
            error_log("MONTO BALANCE FLETE ACTUALIZADO ".$balanceFlete);
            $balancePagoFlete->setBalance($balanceFlete);
            $em->persist($balancePagoFlete);
            
            $cliente = $balancePagoFlete->getCliente();
            $cliente->setCredito($balancePagoFlete->getBalance());
            $em->persist($cliente);
            

            //error_log("ACTUALIZA PAGO DE VENTA");
        }
        }
        
        if(isset($balanceCompra)){
        if ($balanceCompra->getEstatus() == 2) {
            //SET PAGADO COMPRA
            $balanceCompra->setEstatus(1);
            $balanceCompra->setEgresos($balanceCompra->getMonto());
            $montoBalanceAnteorior = $balanceCompra->getBalance();
            if($montoBalanceAnteorior >= 0){
                $balanceCompra->setBalance($montoBalanceAnteorior - $balanceCompra->getMonto());   
            }else{
                 $balanceCompra->setBalance($montoBalanceAnteorior + $balanceCompra->getMonto()); 
            }
            
            $em->persist($balanceCompra);
                        
            //error_log("ACTUALIZA PAGO DE COMPRA");
        }
        $em->flush();
        }
        $dataOrder = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->findOneBy(array('id' => $transactionId));
        
        $dataOrder->setShippingStatus(3);
        $em->persist($dataOrder);
        $em->flush();
		}
    }

}
