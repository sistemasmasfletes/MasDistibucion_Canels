<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_MenuRepository extends EntityRepository
{
    public function getMenu($userId=null, $roleId=null){
        $em = $this->getEntityManager();

        $sql = "SELECT m.id, m.parent_id, m.title, m.position, m.url,
                (SELECT count(us.id)>0  FROM users us 
                    INNER JOIN role ro on us.role_id=ro.id
                    INNER JOIN role_action ra on ro.id=ra.role_id           
                WHERE us.id=:userId
                and ra.eaction_id=m.element_action_id)  enabled
                FROM menu m
                WHERE role_id = :roleId
                ORDER BY m.parent_id,m.position";
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":userId",$userId);
        $stmt->bindValue(":roleId",$roleId);
        $stmt->execute();
        $menu = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
       
        if($menu)
            return $menu[0];
        else
            return null;
    }
}