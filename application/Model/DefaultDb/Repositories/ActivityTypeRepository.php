<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_ActivityTypeRepository extends EntityRepository
{
     public function getActivityTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$name,$description){
        $em = $this->getEntityManager();
        $offset = ($page-1)*$rowsPerPage;
        $dql = $em->createQueryBuilder();
        $test='m.name';
        if($sortField == null){

        }else{
            $test = 'm.'+$sortField;
        }
        
        $dql->select('m')
            ->from('DefaultDb_Entities_ActivityType', 'm');

        $query=$em->createQuery($dql);

        $activityType = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($activityType);
        
        $x=0;
        foreach ($activityType as $atype){
            $resultActivity[$x]=array('id' => $atype->getId(), 'name' => $atype->getName(), 'description' => $atype->getDescription());
            
            $x++;
        }  
        $result[0]=$resultActivity;
        $result[1][0]=array('records' => $x, 'page' =>$page, 'totalpages' => null);
        return $result;
    }
    
    public function getActivityType($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_ActivityType m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $activityType = $query->getFirstResult();
        return $activityType;
    }
    
    public function addActivityType ($id,$name,$description)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $activityType = new DefaultDb_Entities_ActivityType();
        }else{
            $activityType=$this->find($id);
        }
        $activityType->setName($name);
        $activityType->setDescription($description);
        
        $em->persist($activityType);
        $em->flush();
        return;
    }
    
    public function deleteActivityType ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $activityType = $this->find($id);
            $em->remove($activityType);
            $em->flush();
            return;
        }
    }
}