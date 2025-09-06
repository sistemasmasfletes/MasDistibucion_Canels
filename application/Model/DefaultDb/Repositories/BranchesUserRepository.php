<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_BranchesUserRepository extends EntityRepository {

    const ROL_ADMIN_CODE = 1;

    public function getBranchesPending() {
        $em = $this->getEntityManager();
        if($_SESSION['__M3']['MasDistribucion']['Credentials']['role'] != self::ROL_ADMIN_CODE){
            $controllerId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
            $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_BranchesUser m LEFT JOIN m.client c WHERE m.point IS NULL AND c.parent = '.$controllerId .' ORDER BY m.id DESC');
        } else {
            $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_BranchesUser m  WHERE m.point IS NULL');
        }
        
        return $query->getResult();
    }
    public function getBranchesAll() {    	$em = $this->getEntityManager();    	$_SESSION['__M3']['MasDistribucion']['Credentials']['role'] != self::ROL_ADMIN_CODE;    	$controllerId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];    	$query = $em->createQuery('SELECT m FROM DefaultDb_Entities_BranchesUser m LEFT JOIN m.client c WHERE m.point IS NOT NULL AND c.parent = '.$controllerId .' GROUP BY m.client ORDER BY m.client DESC');    	     	return $query->getResult();    }        public function getBranchesDistinct() {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT bu.id FROM DefaultDb_Entities_BranchesUser bu JOIN bu.client u');
        //      var_dump($query->getSQL()); die;
        return $query->getResult();
    }

    public function getBrachesPerUser($userId) {
        $em = $this->getEntityManager();
        $dql = "SELECT b FROM DefaultDb_Entities_BranchesUser b WHERE b.client = :userId AND b.point IS NOT NULL";
        $query = $em->createQuery($dql);
        $query->setParameter("userId", $userId);
        $branchesUser = $query->getResult();
        return $branchesUser;
    }

    /*
      Devuelve un Array con la siguiente estructura
      0)Total branches por usuario
      1)Branches pendientes de aprobar
      2)Branches aprobados
     */

    public function getBranchStatusPerUser($userId) {

        $em = $this->getEntityManager();
        $sql = "SELECT 
                (SELECT COUNT(b.id)a FROM branches_user b WHERE b.client_id = :userId) totalBranches
                ,
                (SELECT COUNT(b.id)a FROM branches_user b WHERE b.client_id = :userId AND b.point_id IS NULL) branchesPending
                ,
                (SELECT COUNT(b.id)a FROM branches_user b WHERE b.client_id = :userId AND b.point_id IS NOT NULL) branchesValidated
        		,
                (SELECT IF(d.terms_acp = '0' OR d.service_acp='0' OR d.privacy_acp = '0',0,1)a FROM users d WHERE d.id = :userId) contractsValidated
                ";
        $conn = $em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue("userId", $userId);
        $stmt->execute();
        $branchStat = $stmt->fetch(\PDO::FETCH_NAMED);
        return $branchStat;
    }
    
    public function getBrachesRoutePerUser($userId) {
    	$em = $this->getEntityManager();
    	$conn = $em->getConnection();
    	$query = '
    			SELECT bu.id, bu.direction,rp.route_id as rid FROM branches_user bu
				left join route_points rp on bu.point_id =  rp.point_id
    			where client_id = '.$userId.' group by bu.point_id ';
    	$res = $conn->executeQuery($query);
    	$array = $res->fetchAll();
    	return $array;
    }
    

}

?>
