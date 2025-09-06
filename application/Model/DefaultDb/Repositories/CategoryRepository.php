<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_CategoryRepository extends EntityRepository
{    
    public function getCategoryWithClients()
    {
        $em = $this->getEntityManager();
        $dql = 'SELECT c FROM DefaultDb_Entities_Category c 
                WHERE EXISTS 
                (
                    SELECT p FROM DefaultDb_Entities_User p 
                    WHERE p.category = c.id
                    AND p.type= :type
                    AND p.status = :status
                )';
        $query = $em->createQuery($dql);
        $query->setParameter('type', DefaultDb_Entities_User::USER_CLIENT);
        $query->setParameter('status', DefaultDb_Entities_User::STATUS_ACTIVE);
        $result = $query->execute();
        return $result;
    }
}