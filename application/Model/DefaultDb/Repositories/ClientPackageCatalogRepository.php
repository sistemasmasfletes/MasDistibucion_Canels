<?php
use Doctrine\ORM\EntityRepository;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ClientPackageCatalogRepository extends EntityRepository
{    
	public function getPackages($page,$rowsPerPage,$sortField,$sortDir,$userId,$packageId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = " id, user_id, name, weight, width, height, depth, price, size, description ";
     
        $query="
            SELECT [FIELDS]
            FROM
            	client_package_catalog cp
            WHERE
            	user_id=:userId OR user_id=58
                AND (:packageId IS NULL OR id=:packageId)
            ORDER BY user_id,name ASC        		
            [LIMT]
        ";
               
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
     
        //echo $queryCount;
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        
        $stmt->bindValue(":userId",$userId);
        $stmt->bindValue(":packageId",$packageId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
         
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"],'idusr'=>$userId));
    }

    public function save($id,$userId,$name,$weight,$width,$height,$depth,$price,$size,$description){
        $em = $this->getEntityManager();

        $clientPackage=null;
        if($id==null)
            $clientPackage=new DefaultDb_Entities_ClientPackageCatalog();
        else{
            $clientPackage = $this->find($id);
        }

        $userRepo = $em->getRepository("DefaultDb_Entities_User");
        $user = $userRepo->find($userId);

        if($clientPackage instanceof DefaultDb_Entities_ClientPackageCatalog){
            $clientPackage->setUser($user);
            $clientPackage->setName($name);
            $clientPackage->setWeight($weight);
            $clientPackage->setWidth($width);
            $clientPackage->setHeight($height);
            $clientPackage->setDepth($depth);
            $clientPackage->setPrice($price);
            $clientPackage->setSize($size);
            $clientPackage->setDescription($description);

            if($id==null)
                $em->persist($clientPackage);
            $em->flush();
        }
        //if($clientPackage!=null)
        //    return array($clientPackage);
        //else
        //    return array();
    }

    public function delete($id){
        $em = $this->getEntityManager();
        $clientPackage = $this->find($id);

        if($clientPackage instanceof DefaultDb_Entities_ClientPackageCatalog){
           $em->remove($clientPackage);
           $em->flush(); 
        }
    }

    public function getProductsFromPackage($page,$rowsPerPage,$sortField,$sortDir,$packageId){
        $em = $this->getEntityManager();
 
        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;
 
        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect = " pp.id, package_id, product_id, p.name product,quantity, default_package ";
     
        $query="
            SELECT [FIELDS]
            FROM
                package_products pp LEFT JOIN product p on pp.product_id=p.id

            WHERE                
                package_id=:packageId
            [ORDERBY]
            [LIMT]
        ";
               
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
     
        //echo $queryCount;
        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);
 
 
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[ORDERBY]',($sortField && $sortDir) ? " ORDER BY ".$sortField." ".$sortDir : "", $querySelect);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);
        
        $querys = $querySelect."; ".$queryCount;
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);        
        
        $stmt->bindValue(":packageId",$packageId);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
         
        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
}