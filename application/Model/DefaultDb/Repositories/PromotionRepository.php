<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_PromotionRepository extends EntityRepository{
    
    const QUERY_TYPE_COUNT = 0;
    const QUERY_TYPE_SELECT = 1;
    const NOT_PAGINATE = false;
    const PAGINATE = true;    

    public function getPromotion($params){

       $totalRows =                         $this->getQuery($params,self::NOT_PAGINATE,self::QUERY_TYPE_COUNT)->getSingleScalarResult();
       $resultset = $this->mapResultToArray($this->getQuery($params,self::PAGINATE,    self::QUERY_TYPE_SELECT)->getResult());

       return array('data'=>$resultset,'meta'=>array('totalRecords'=>$totalRows,"promotionCosting"=>$this->getPromotionCosting(1)));
    }

    public function getPromotionDetail($params){
        $em = $this->getEntityManager();

        $promotionResources = $em->getRepository('DefaultDb_Entities_PromotionResources')->findBy(array("promotion"=>$params["filter"]["promotionId"]));
        
        $resultset = $this->mapResultDetailToArray($promotionResources);
        return array('data'=>$resultset);
    }

    private function getQueryBuilder($params,$queryType){
        $em = $this->getEntityManager(); 
        $qb = $em->getRepository('DefaultDb_Entities_Promotion')->createQueryBuilder('p');

        if($queryType==self::QUERY_TYPE_SELECT)
            $qb->select('p');
        else
            $qb->select('COUNT(p.id)');
        
        $promotionId = $params["filter"]["promotionId"];
        $name = $params["filter"]["name"];
        $userid = $params["filter"]["userid"];
        
        $qb->where(':name IS NULL OR p.name LIKE :name'); 
        $qb->andWhere(':promotionId IS NULL OR p.id=:promotionId');
        $qb->andWhere(':userid IS NULL OR p.user=:userid');
        $qb->orderBy($params["sortField"], $params["sortDir"]);

        $qb->setParameter('name', '%'.$name.'%');
        $qb->setParameter('promotionId', $promotionId);
        $qb->setParameter('userid', $userid);

        return $qb;
    }
    
    private function getQueryBuilderDetail($params,$queryType){
        $em = $this->getEntityManager();
        $qb = $em->getRepository('DefaultDb_Entities_PromotionResources')->createQueryBuilder('p');
        if($queryType==self::QUERY_TYPE_SELECT)
            $qb->select('p');
        else
            $qb->select('COUNT(p.id)');

        $qb->where(':promotionId IS NULL OR p.promotion = :promotionId');     
        $qb->setParameter('promotionId', $params["filter"]["promotionId"]);

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
                "name"=>$promotion->getName(),
                "numResources"=>$promotion->getNumResources()
            );
        }
        return $resultPromotion;
    }

    private function mapResultDetailToArray($rowObject){
        $resultPromotionDetail = array();
        if($rowObject !=null){
            foreach ($rowObject as $promotion) {
                 $resultPromotionDetail[] = array(
                    "id"=>$promotion->getId(),
                    "promotionId"=>$promotion->getPromotion()->getId(),
                    "resourceType"=>$promotion->getResourceType(),
                    "name"=>$promotion->getName(),
                    "path"=>$promotion->getPath()
                );
            }
        }
        return $resultPromotionDetail;
    }

    private function addPromotion(){

    }

    public function save($aPromotion,$aItems,$targetDir,$userDocumentsPublic){
        $em = $this->getEntityManager();

        $promotionId = $aPromotion["id"];
        $em->getConnection()->beginTransaction();
        try {
            if(is_null($promotionId)){
                $promotion = new DefaultDb_Entities_Promotion();
                $promotion->setName($aPromotion["name"]);
                $user = $em->getRepository('DefaultDb_Entities_User')->find($aPromotion["userid"]);
                $promotion->setUser($user);                
            }else{
                $promotion = $em->getRepository('DefaultDb_Entities_Promotion')->find($promotionId);
                $promotion->setName($aPromotion["name"]);                

                //Eliminar promociones
                $dql = "SELECT pr from DefaultDb_Entities_PromotionResources pr WHERE pr.promotion = :promotionId";
                $query = $em->createQuery($dql);
                $query->setParameter(':promotionId',$promotionId);
                $resources = $query->getResult();
                
                $resourcesAll = array();
                // Lista diferencial entre recursos en BD y recursos en la lista que el usuario envía.
                foreach ($resources as $k=>$resource) {
                    $id = $resource->getId();
                    $resourcesAll[] = array("id"=>$id, "path"=>$resource->getPath(),"resourceType"=>$resource->getResourceType(),"delete"=>true);
                    foreach ($aItems as $key => $item) {
                        $currentid = intval($item["id"]);
                        if($id==$currentid) {
                            $resourcesAll[$k]["delete"] = false;
                            continue;
                        }
                    }
                }
                
                //Lista de los IDs que se va a eliminar
                $resourceToDelete = array();
                foreach ($resourcesAll as $key => $res) {
                    if($res["delete"]==true)
                        $resourceToDelete[]=$res["id"];
                }

                if(count($resourceToDelete)>0){
                   $dql = "SELECT pr from DefaultDb_Entities_PromotionResources pr WHERE pr.id IN (:ids)";
                    $query = $em->createQuery($dql);
                    $query->setParameter(':ids',$resourceToDelete);
                    $resources = $query->getResult();
                    foreach ($resources as $k=>$resource) {
                        $filepath = $targetDir."/".basename($resource->getPath());
                        if($resource->getResourceType()==1 and  file_exists($filepath)){
                            unlink($filepath);
                        }
                        $em->remove($resource);
                    } 
                }
                

            }
            
            if($promotion){
                foreach ($aItems as $key => $item) {
                    $currentid = $item["id"];
                    $resourceType = $item["resourceType"];

                    if($resourceType==1){
                        $tmpfile = $item["tmpfile"];
                        if(file_exists($tmpfile["tmpname"])){
                            $filename = uniqid() . basename($tmpfile["name"]);
                            $targetFile = $targetDir ."/". $filename;
                            $targetFilePublic = $userDocumentsPublic . "/" . $filename;
                            move_uploaded_file($tmpfile["tmpname"], $targetFile);
                        }
                    }

                    if($currentid<0){
                        $promotionResource = new DefaultDb_Entities_PromotionResources();
                        $promotionResource->setPromotion($promotion);
                        $promotionResource->setName($item["name"]);
                        $promotionResource->setResourceType($resourceType);
                        $promotionResource->setPath($resourceType==1 ? $targetFilePublic : $item["path"]);
                        $em->persist($promotionResource);
                    }
                }
                $promotion->setNumResources(count($aItems));
                $em->persist($promotion);
            }
            $em->flush();
            $em->getConnection()->commit();

        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function delete($promotionId,$targetDir){
        $em = $this->getEntityManager();

        //Obtener detalle de promoción.
        $dql = "SELECT partial pr.{id,path,resourceType} from DefaultDb_Entities_PromotionResources pr WHERE pr.promotion = :promotionId";
        $query = $em->createQuery($dql);
        $query->setParameter(':promotionId',$promotionId);
        $resources = $query->getResult();
    
        //Borrar archivos y detalle de promoción.
        $arrayDeleteIds = array();
        foreach ($resources as $key => $resource) {
            $arrayDeleteIds[] = $resource->getId();
            $filepath = $targetDir."/".basename($resource->getPath());
            if($resource->getResourceType()==1 and  file_exists($filepath))
                unlink($filepath);            
        }

        $deleteQuery = $em->createQuery("DELETE FROM DefaultDb_Entities_PromotionResources pr WHERE pr.id IN(:ids)");
        $deleteQuery->setParameter(':ids',$arrayDeleteIds);
        $numDeleted = $deleteQuery->execute();

        $promotion = $em->getRepository('DefaultDb_Entities_Promotion')->find($promotionId);
        if($promotion){
            $em->remove($promotion);
            $em->flush();
        }
            
        return $numDeleted ;
    }

    public function getPromotionCosting($configId){
        $promotionCosting = null;
        $em = $this->getEntityManager();
        $config = $em->getRepository('DefaultDb_Entities_Configuration')->find($configId);
        if($config)
            $promotionCosting = $config->getPromotionCost();
        return $promotionCosting;
    }

    public function getPromotionResourceCount($promotionId){
        $em = $this->getEntityManager();
        $dql = "SELECT COUNT(pr.id) from DefaultDb_Entities_PromotionResources pr WHERE pr.promotion= :promotionId";
        $query = $em->createQuery($dql);
        $query->setParameter(':promotionId',$promotionId);
        $count = $query->getSingleScalarResult();
        return $count;
    }

}