<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_UserRepository extends EntityRepository
{
    /**
     *
     * @param DefaultDb_Entities_User $user 
     */
    public function delete($user)
    {
        $em = $this->getEntityManager(); 
        $em->remove($user);         
        $em->flush();
    }
    
    public function searchUserByEmail($email)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_User m WHERE m.username = ?1');
        $query->setParameter(1, $email);
        $res=null;
        try {
            $res=$query->getSingleResult();
        }
        catch (Exception $e){
            $res=null;
        }
        return $res;
    }
    
    public function searchUsersByAll($value)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_User m WHERE
            (m.lastName LIKE ?1 OR m.firstName LIKE ?1 OR m.title LIKE ?1 OR m.company LIKE ?1 ) AND (m.typeLoginUser = 2 OR m.typeLoginUser = 3)');
        $query->setParameter(1,'%'.$value.'%');
        return $query->getResult();
    }
    
    public function findCommonUsers($limit, $page)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT m FROM DefaultDb_Entities_User m WHERE m.typeLoginUser = 2 OR m.typeLoginUser = 3');        
        $query->setFirstResult((($page-1)*$limit));
        $query->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    public function countUsers()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(m) AS total FROM DefaultDb_Entities_User m WHERE m.typeLoginUser = 2 OR m.typeLoginUser = 3');        
        return $query->getResult(Query::HYDRATE_SCALAR);
    }
    
    public function countRegisteredUsers()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(m) AS total FROM DefaultDb_Entities_User m WHERE m.typeLoginUser = 2');        
        return $query->getResult(Query::HYDRATE_SCALAR);
    }
    
    public function findAllCompany()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT DISTINCT(c.company) FROM DefaultDb_Entities_User c GROUP BY c.company');
        return $query->getResult(Query::HYDRATE_ARRAY);
    }
}