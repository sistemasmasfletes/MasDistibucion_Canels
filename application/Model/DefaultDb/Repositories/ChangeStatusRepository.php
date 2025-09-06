<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_ChangeStatusRepository extends EntityRepository
{
 
    public function getLastFiveComment($user)
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT m FROM DefaultDb_Entities_ChangeStatus m WHERE m.user = '.$user->getId().' ORDER BY m.dateChange DESC ';
        $query = $em->createQuery($dql);
        $query->setMaxResults(5);
        return $query->getResult();
    }
}