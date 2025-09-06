<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_CausesRepository extends EntityRepository
{
    
    public function getCausesListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$name,$description){
        $em = $this->getEntityManager();
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
        $offset = ($page-1)*$rowsPerPage;
        
        $dql = $em->createQueryBuilder();
        $test='m.name';
        if($sortField == null){
            //$test = 'm.name';
        }else{
            $test = 'm.'+$sortField;
        }

        $dql->select('c')
            ->from('DefaultDb_Entities_Causes', 'c');
                
        $query=$em->createQuery($dql);

        $causes = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($causes);
        
        $x=0;
        foreach ($causes as $cause){
            $resultCause[$x]=array(
                'id' => $cause->getId(),
                'name' => $cause->getName(),
                'description' => $cause->getDescription());
            $x++;
        }
        $result[0]=$resultCause;
        $result[1][0]=array('records' => $totalRecords, 'page' =>$page, 'totalpages' => $offset);
        return $result;
    }
    
    
    public function getCauses($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_Causes m WHERE m.id = :id ";
        //$dql.=' ORDER BY '.$sortField.' '.$sortDir;
        
        //$query=$em->createQuery($dql)->setFirstResult($offset)->setMaxResults($rowsPerPage);
        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);
        /*$query->setParameter('name', $name);
        $query->setParameter('description',$description);*/
        //$causes = $query->getResult();
        $causes = $query->getFirstResult();
        return $causes;
        //return array('data'=>$result,'meta'=>array('totalRecords'=>$totalRecords));
    }
    
    public function addCauses($id,$name,$description)
    {   
        $em = $this->getEntityManager();
        if ($id == null){
            $causes = new DefaultDb_Entities_Causes();
        }else{
            $causes=$this->find($id);
        }
        $causes->setName($name);
        $causes->setDescription($description);
        
        $em->persist($causes);
        $em->flush();
        return;
    }
    
    public function deleteCauses ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $causes = $this->find($id);
            $em->remove($causes);
            $em->flush();
            return;
        }
    }
}