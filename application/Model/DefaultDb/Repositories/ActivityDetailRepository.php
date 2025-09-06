<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

class DefaultDb_Repositories_ActivityDetailRepository extends EntityRepository
{
    //SALVAR EVIDENCIA DEL CHOFER EN PUNTO DE VENTA
    public function saveEvidence($id,$status,$receptor,$comentarios,$uploadFile,$routePointActivityId,$date,$causeId)
    {
        $em = $this->getEntityManager();
        
        if ($id == null){
            $evidence = new DefaultDb_Entities_ActivityDetail();
        }else{
            $evidence=$this->find($id);
        }
        
        if ($routePointActivityId == null){
        }else{
            $shedule=$em->find("DefaultDb_Entities_RoutePointActivity", $routePointActivityId);
            $evidence->setRoutePointActivityId($shedule);
        }
        if ($causeId == null){
        }else{
            $cause=$em->find("DefaultDb_Entities_Causes", $causeId);
            $evidence->setCauseId($cause);
        }
        $evidence->setStatus($status);
        $evidence->setReceptor($receptor);
        $evidence->setComentarios($comentarios);
        $evidence->setUploadFile($uploadFile);
        $evidence->setDate($date);
                
        $em->persist($evidence);
        $em->flush();
        return $evidence->getId();
    }
    
    //SALVAR EVIDENCIA EN CENTRO DE INTERCAMBIO SIN TOMAR FOTOGRAFIA
    public function saveEvidenceCI($receptor,$routePointActivityId,$status,$comentarios,$date){
        $em = $this->getEntityManager();
        $evidence = new DefaultDb_Entities_ActivityDetail();
        
        if ($routePointActivityId == null){
        }else{
            $shedule=$em->find("DefaultDb_Entities_RoutePointActivity", $routePointActivityId);
            $evidence->setRoutePointActivityId($shedule);
        }
        $evidence->setReceptor($receptor);
        $evidence->setStatus($status);
        $evidence->setComentarios($comentarios);
        $evidence->setDate($date);
        
        $em->persist($evidence);
        $em->flush();
        return;
    }


    //Salvar evidencia de puntos sin actividad
    public function saveNoActivityPoint($receptor,$comentarios,$scheduledRouteId,$pointId,$date){
        $em = $this->getEntityManager();
        $evidence = new DefaultDb_Entities_ActivityDetail();

 
        $evidence->setReceptor($receptor);
        $evidence->setComentarios($comentarios);
        if ($scheduledRouteId == null){
        }else{
            $sheduleRoute=$em->find("DefaultDb_Entities_ScheduledRoute", $scheduledRouteId);
            $evidence->setScheduledRouteDetailId($sheduleRoute);
        }
        if ($pointId == null){
        }else{
            $point=$em->find("DefaultDb_Entities_Point", $pointId);
            $evidence->setPointId($point);
        }
     
        $evidence->setDate($date);
        
        $em->persist($evidence);
        $em->flush();
        return;
    }
    
    //obtener orden de compra
    public function getEvidenceId($schedule){
        $em = $this->getEntityManager();

        $shedule2 = new DefaultDb_Entities_RoutePointActivity();        
        if ($schedule == null){

        }else{
            $shedule2=$em->find("DefaultDb_Entities_RoutePointActivity", $schedule);
        }
        $dql = '
            SELECT m FROM DefaultDb_Entities_ActivityDetail m WHERE m.routePointActivityId = 
            '.$shedule2->getId().' ORDER BY m.id DESC';
        
        $query = $em->createQuery($dql)->setMaxResults(1);
        $orders = $query->getResult();
        $order = $orders[0];

        return $order->getId();
    }

    //obtener operación del paquete, entrega o recolección.
    public function getActivityName ($schedule){
        $em = $this->getEntityManager();

        $rpAct = new DefaultDb_Entities_RoutePointActivity();        

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('DefaultDb_Entities_ActivityType', 'a');
        $rsm->addFieldResult('a', 'name', 'name');
        $rsm->addFieldResult('a', 'id', 'id');
        $rsm->addFieldResult('a', 'description', 'description');
        $em = $this->getEntityManager();        
        $dql = '
                SELECT
                    act.id,act.name, act.description
                from routepoint_activity rpa
                inner join activity_type act on rpa.activityType_id=act.id
                WHERE rpa.id='.$schedule;
        $query = $em->createNativeQuery($dql,$rsm);
        
        $orders = $query->getResult();

        return $orders[0]->getName();
    }
    
    //INFORMACIÓN DEL PAQUETE
    public function getEvidenceListDQL($page,$rowsPerPage,$sortField,$sortDir,$ocId,$sraId,$comentarios,$foto,$puntoVenta){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = " 
                    distinct
                        ord.id as ocId,
                        r.id as rpaId,
                        ord.comments as comentarios,
                        concat(ad.id,ad.uploadFile) as evidence,
                        po.name as puntoVenta
                ";
        
        $query="
            SELECT [FIELDS]
                from package_to_order p 
                    inner join m3_commerce_order ord on p.order_id=ord.id
                    inner join transactions trans on trans.transaction_id = ord.id 
                    inner join routepoint_activity r on trans.id=r.transaction_id
                    inner join activity_type typ on typ.id = r.activityType_id
                    inner join route_points rp on rp.id = r.routePoint_id
                    inner join activity_detail ad on r.id=ad.routePointActivity_id
                    inner join points po on rp.point_id=po.id
                where ord.id='".$ocId."'
        ";
        //echo $ocId;
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
}