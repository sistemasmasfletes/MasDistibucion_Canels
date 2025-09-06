<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_LogUsersVisitedRepository extends EntityRepository
{
    public function addLog($driverId, $clientId, $action)
    {
        $em = $this->getEntityManager();
        $res = false;
        
        $driver = $em->find('DefaultDb_Entities_User', $driverId);
        $client = $em->find('DefaultDb_Entities_User', $clientId);

        $logUsersVisited = new DefaultDb_Entities_LogUsersVisited();
        $logUsersVisited->setUserDriver($driver);
        $logUsersVisited->setUserClient($client);
        $logUsersVisited->setCreationDate(new DateTime());
        $logUsersVisited->setAction($action);

        $em->persist($logUsersVisited);
        $em->flush();

        $res = true;
        
        return $res;
    }
}