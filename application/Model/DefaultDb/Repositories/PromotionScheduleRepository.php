<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_PromotionScheduleRepository extends EntityRepository{
    
    const QUERY_TYPE_COUNT = 0;
    const QUERY_TYPE_SELECT = 1;
    const NOT_PAGINATE = false;
    const PAGINATE = true;    

    public function getPromotionSchedule($params){

       $totalRows =                         $this->getQuery($params,self::NOT_PAGINATE,self::QUERY_TYPE_COUNT)->getSingleScalarResult();
       $resultset = $this->mapResultToArray($this->getQuery($params,self::PAGINATE,    self::QUERY_TYPE_SELECT)->getResult());

       return array('data'=>$resultset,'meta'=>array('totalRecords'=>$totalRows));
    }

    public function getPromotionReceived($params){
    
    	$totalRows = $this->getQuery($params,self::NOT_PAGINATE,self::QUERY_TYPE_COUNT)->getSingleScalarResult();
    	$resultset = $this->mapResultToArray($this->getQuery($params,self::PAGINATE,self::QUERY_TYPE_SELECT)->getResult());
    
    	return array('data'=>$resultset,'meta'=>array('totalRecords'=>$totalRows));
    }
        
    private function getQueryBuilder($params,$queryType){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        if($queryType==self::QUERY_TYPE_SELECT)
            $qb->select('p','partial pr.{id,name}','partial cl.{id,commercialName}','partial pt.{id,name}')
        ->from('DefaultDb_Entities_PromotionSchedule','p');
        else
            $qb->select('COUNT(p.id)')->from('DefaultDb_Entities_PromotionSchedule','p');
        
        $id = $params["filter"]["id"];
        $clientid = $params["filter"]["clientid"];
        $userid = $params["filter"]["userid"];
        
        $qb->leftJoin('p.promotion', 'pr');
        $qb->leftJoin('p.client', 'cl');
        $qb->leftJoin('p.point', 'pt');
        $qb->where(':id IS NULL OR p.id=:id'); 
        $qb->andWhere(':clientid IS NULL OR p.client=:clientid');
        $qb->andWhere(':userid IS NULL OR p.user=:userid');
        $qb->orderBy($params["sortField"], $params["sortDir"]);
        
        $qb->setParameter('id', $id);
        $qb->setParameter('clientid', $clientid);
        $qb->setParameter('userid', $userid);
        return $qb;
    }

    private function getQuery($params,$paginate,$queryType){        
        $queryBuilder = $this->getQueryBuilder($params, $queryType);
        
        if($paginate)
        {
            $offset = ( $params["page"] - 1 ) * $params["rowsPerPage"];
            $queryBuilder->setMaxResults($params["rowsPerPage"]);
            $queryBuilder->setFirstResult($offset);   
        }

        return $queryBuilder->getQuery();
    }

    private function mapResultToArray($rowObject){
        $resultPromotion = array();
        foreach ($rowObject as $promotion) {
             $resultPromotion[] = array(
                "id"=>$promotion->getId(),
                "promotionId"=>$promotion->getPromotion()->getId(),
             	"promotionName"=>$promotion->getPromotion()->getName(),
                "client"=>$promotion->getClient() ? ($promotion->getClient()->getCommercialName() ? $promotion->getClient()->getCommercialName() : '-') : 'No registrado ('.($promotion->getPoint() ? $promotion->getPoint()->getName() : '-').')',
                "buyer"=>$promotion->getUser()->getCommercialName(),
             	"idactivitypoint"=>($promotion->getActivityPoint())?$promotion->getActivityPoint()->getId():'', 
                "package"=>$promotion->getPackageOrder() ? $promotion->getPackageOrder()->getNamePackage() : '-',
                "dateshipping"=>$promotion->getPromotionDate()->format('Y-m-d H:i:s'),
                "paymentStatus"=>$promotion->getPaymentStatus(),
                "shippingStatus"=>$promotion->getShippingStatus(),
                "consumerType"=>$promotion->getConsumerType(),
                "interestLevel"=>$promotion->getInterestLevel(),
                "request"=>$promotion->getRequest(),
                "receivingUser"=>$promotion->getReceivingUser(),
                "telephone"=>$promotion->getTelephone(),
                "comments"=>$promotion->getComments()
            );
        }
        return $resultPromotion;
    }

    public function saveSurvey($params){
        $id = $params["id"];
        $consumerType = $params["consumerType"];
        $interestLevel = $params["interestLevel"];
        $receivingUser = $params["receivingUser"];
        $comments = $params["comments"];
        $request = $params["request"];
        $telephone = $params["telephone"];
       
        $em = $this->getEntityManager();
        $promotionSchedule = $this->find($id);
        if($promotionSchedule){
            $promotionSchedule->setConsumerType($consumerType);
            $promotionSchedule->setInterestLevel($interestLevel);
            $promotionSchedule->setReceivingUser($receivingUser);
            $promotionSchedule->setComments($comments);            
            $promotionSchedule->setShippingStatus(DefaultDb_Entities_PromotionSchedule::STATUS_ENTREGADO);
            $promotionSchedule->setRequest($request);
            $promotionSchedule->setTelephone($telephone);
            $em->persist($promotionSchedule);
            $em->flush();
        }

    }

    public function updateStatusPromotion($params){
        $routePointActivityId = $params["routePointActivityId"];
        $promotionScheduleId = $params["promotionScheduleId"];
        $tipoCobro = $params["tipoCobro"];
        $comments = $params["comentarios"];
        $shippingStatus = intval($params["status"]);

        $em = $this->getEntityManager();
        $result = 0;       
        $TRANSACTION_TYPE_PROMOTION = 2;
        $STATUS_PROMOCION_ENTREGADO = 3;       
        $ESTATUS_PAGADO = 1;

        $em->getConnection()->beginTransaction();
        try{
            $promotionSchedule = $this->find($promotionScheduleId);
            if($promotionSchedule){
                $id = null;
                $status = $shippingStatus;
                $receptor = null;
                $comentarios = $comments;
                $uploadFile = null;
                $date = new DateTime();
                $causeId = null;

                //Si el estatus de la promoción no es Entregado, se guarda el detalle de la actividad
                //Una promoción con estatus de Entregado previamente ya está guardada; es por ello que en este punto solo requiere afectar el balance
                $promotionSchedule->setPaymentStatus($ESTATUS_PAGADO);
                $promotionSchedule->setShippingStatus($shippingStatus);
                if($shippingStatus!=$STATUS_PROMOCION_ENTREGADO){
                    $promotionSchedule->setComments($comments);                    

                    //Guardar detalle de la actividad               
                    $activityDetailRepo = $em->getRepository('DefaultDb_Entities_ActivityDetail');                
                    $activityDetailId = $activityDetailRepo->saveEvidence($id,$status,$receptor,$comentarios,$uploadFile,$routePointActivityId,$date,$causeId);
                    
                    //Guardar estatus en la tabla Transactions
                    $transRepo = $em->getRepository('DefaultDb_Entities_Transactions');
                    $transRepo->saveStatus($promotionScheduleId,$status,$routePointActivityId,$TRANSACTION_TYPE_PROMOTION);
                }
                $em->persist($promotionSchedule);
                //Afectar balance.
                $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
                $pago = $pagosRepo->findBy(array("promocion"=>$promotionScheduleId));

                if($pago!=null && count($pago)==1){
                    $pago = $pago[0];
                    $params = array("tipoCobro"=>$tipoCobro,"idpago"=>$pago->getId());
                    $balanceRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
                    $balanceRepo->updateSaldoCongelado($params);
                }
                $em->flush();
                $result = 1;
            }
            $em->getConnection()->commit();             
            //echo json_encode(array('res' => TRUE));
                
        }catch(Exception $ex){
            $em->getConnection()->rollback();
            throw $ex;
        }
        return $result;
    }
}