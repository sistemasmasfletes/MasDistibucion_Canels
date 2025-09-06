<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_LogRepository extends EntityRepository
{

	public function addLog($date,$action,$params,$error){		
		$em = $this->getEntityManager();		

		$log = new DefaultDb_Entities_Log();
		$log->setDate($date);
		$log->setAction($action);
		$log->setParams($params);
		$log->setError($error);

		$em->persist($log);
		$em->flush();
	}
}