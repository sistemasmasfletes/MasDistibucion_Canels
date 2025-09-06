<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_ZoneRepository extends EntityRepository{
const QUERY_TYPE_COUNT = 0;
    const QUERY_TYPE_SELECT = 1;
    const NOT_PAGINATE = false;
    const PAGINATE = true;    

    public function getZone($params){
       $totalRows =                         $this->getQuery($params,self::NOT_PAGINATE,self::QUERY_TYPE_COUNT)->getSingleScalarResult();
       $resultset = $this->mapResultToArray($this->getQuery($params,self::PAGINATE,    self::QUERY_TYPE_SELECT)->getResult());

       return array('data'=>$resultset,'meta'=>array('totalRecords'=>$totalRows));
    }


    private function getQueryBuilder($params,$queryType){
        $em = $this->getEntityManager(); 
        $qb = $em->getRepository('DefaultDb_Entities_Zone')->createQueryBuilder('p');

        if($queryType==self::QUERY_TYPE_SELECT)
            $qb->select('p');
        else
            $qb->select('COUNT(p.id)');
        
        $zoneId = $params["filter"]["zoneId"];
        $name = $params["filter"]["name"];
        
        $qb->where(':name IS NULL OR p.name LIKE :name'); 
        $qb->andWhere(':zoneId IS NULL OR p.id=:zoneId');
        $qb->orderBy($params["sortField"], $params["sortDir"]);

        $qb->setParameter('name', '%'.$name.'%');
        $qb->setParameter('zoneId', $zoneId);

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
        $resultZone = array();
        foreach ($rowObject as $zone) {
             $resultZone[] = array(
                "id"=>$zone->getId(),
                "name"=>$zone->getName()
            );
        }
        return $resultZone;
    }

    public function save($aZone){
        $em = $this->getEntityManager();

        $zoneId = $aZone["id"];
        $em->getConnection()->beginTransaction();
        try {
            if(is_null($zoneId)){
                $zone = new DefaultDb_Entities_Zone();
                $zone->setName($aZone["name"]);
                $em->persist($zone);
            }else{
                $zone = $em->getRepository('DefaultDb_Entities_Zone')->find($zoneId);
                $zone->setName($aZone["name"]);
                $em->persist($zone);
            }
            $em->flush();
            $em->getConnection()->commit();

        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function delete($zoneId){
        $em = $this->getEntityManager();
        
        
        $zone = $em->getRepository('DefaultDb_Entities_Zone')->find($zoneId);
        if($zone){
            $em->remove($zone);
            $em->flush();
        }
            
        return ;
    }

}