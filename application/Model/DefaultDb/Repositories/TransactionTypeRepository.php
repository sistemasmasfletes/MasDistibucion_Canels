<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_TransactionTypeRepository extends EntityRepository
{
    public function getTransactionTypeListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$name){
        $em = $this->getEntityManager();
        $offset = ($page-1)*$rowsPerPage;
        $dql = $em->createQueryBuilder();
        $test='m.name';
        if($sortField == null){

        }else{
            $test = 'm.'+$sortField;
        }
        
        $dql->select('m')
            ->from('DefaultDb_Entities_TransactionType', 'm')
                ->orderBy('m.name', $sortDir);

        $query=$em->createQuery($dql);

        $transactionType = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($transactionType);
        
        $x=0;
        foreach ($transactionType as $tratype){
            $resultTransaction[$x]=array('id' => $tratype->getId(), 'name' => $tratype->getName());
            
            $x++;
        }  
        $result[0]=$resultTransaction;
        $result[1][0]=array('records' => $x, 'page' =>$page, 'totalpages' => null);
        return $result;
    }
    
    
    
    public function getTransactionType($id){
        $em = $this->getEntityManager();
        
        $dql = $em->createQueryBuilder();
                 
        $dql->select('m')
            ->from('DefaultDb_Entities_TransactionType', 'm')
                ->where("m.id = $id");

        $query=$em->createQuery($dql);

        $transactionType = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($transactionType);
        
        $x=0;
        foreach ($transactionType as $tratype){
            $resultTransaction[$x]=array('id' => $tratype->getId(), 'name' => $tratype->getName());
            
            $x++;
        }  
        $result[0]=$resultTransaction;

        return $result;
    }
    
    public function addTransactionType ($id,$name)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $transactionType = new DefaultDb_Entities_TransactionType();
        }else{
            $transactionType=$this->find($id);
        }
        $transactionType->setName($name);
        
        $em->persist($transactionType);
        $em->flush();
        return;
    }
    
    public function deleteTransactionType ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $transactionType = $this->find($id);
            $em->remove($transactionType);
            $em->flush();
            return;
        }
    }
    
}
