<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ClassificationRepository extends EntityRepository {
    
    public function getClassificationByIdListDQL($id){
        $em = $this->getEntityManager();
        
        $sqlSelect="c.*, p.id as pointId";
        $query="
            SELECT [FIELDS]
                FROM classification c
                LEFT JOIN points p ON c.id=p.classificationId_id
                WHERE p.id=:id
        ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":id", $id);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

        public function saveClasiffication($id,$size,$activity,$consumption, $idPoint){
        $em = $this->getEntityManager();
        
        if($id==null){
            $classification = new DefaultDb_Entities_Classification();
            $classification->setSize($size);
            $classification->setActivity($activity);
            $classification->setConsumption($consumption);
            $em->persist($classification);
            
            $pointId = ($idPoint) ? $em->getRepository('DefaultDb_Entities_Point')->findOneBy(array("id"=>$idPoint)) : new DefaultDb_Entities_Point();

            if($pointId){    
                $pointId->setClassificationId($classification);
                $em->persist($pointId);
            }
            
            $em->flush();
        } else {
            $classification = $this->find($id);
            
            if($classification){
                $classification->setSize($size);
                $classification->setActivity($activity);
                $classification->setConsumption($consumption);
                $em->flush();
            }
        }
    }
}