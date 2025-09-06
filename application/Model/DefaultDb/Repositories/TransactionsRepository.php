<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

class DefaultDb_Repositories_TransactionsRepository extends EntityRepository
{
    public function saveStatus ($id, $status, $statusPoint,$ttype = 1){
 
        $em = $this->getEntityManager();
        if ($id == null){
            $estado = new DefaultDb_Entities_Transactions();
        }else{
            $estado=($statusPoint ==="")? $this->findOneBy(array('transactionId'=>$id, 'transactionType'=>$ttype)):$this->findOneBy(array('transactionId'=>$id, 'transactionType'=>2,"statusPoint" => NULL));
        }
        if ($statusPoint != null && $estado!=null){
            $shedule=$em->find("DefaultDb_Entities_RoutePointActivity", $statusPoint);
            $estado->setStatusPoint($shedule);
        }
        if($estado!=null){
            $estado->setStatus($status);  
            $em->persist($estado);
            $em->flush();
        }
        return;
    }
}
