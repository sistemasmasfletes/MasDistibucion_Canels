<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ContactRepository extends EntityRepository {

    public function getContactListDQL($page,$rowsPerPage,$sortField,$sortDir,$id){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;

        $sqlSelect="
            c.*,
            p.name as pointName
        ";
        $query="
            SELECT [FIELDS]
                FROM contact c
                INNER JOIN points p ON c.point_id=p.id
                WHERE c.point_id=:id
            [LIMT]
        ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);

        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }
    
    public function getContactByIdListDQL($page,$rowsPerPage,$sortField,$sortDir,$contactId){
        $em = $this->getEntityManager();

        if($page==null) $page=1;
        if($rowsPerPage==null) $rowsPerPage=10;

        $offset = ($page-1)*$rowsPerPage;
        
        $sqlSelect="*";
        $query="
            SELECT [FIELDS]
                FROM contact c
                WHERE c.id=:id
            [LIMT]
        ";
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);

        $queryCount = str_replace('[ORDERBY]', " ", $queryCount);
        $queryCount = str_replace('[LIMT]', " ", $queryCount);

        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querySelect = str_replace('[LIMT]', " LIMIT ".$rowsPerPage.' OFFSET '.$offset, $querySelect);

        $querys = $querySelect."; ".$queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);
        
        $stmt->bindValue(":id", $contactId);
        
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        return array('data'=>$result[0],'meta'=>array('totalRecords'=>$result[1][0]["totalRecords"]));
    }

    public function saveContact($id,$pointId,$name,$job,$email,$phoneNumber,$status/*,$default*/){
        $em = $this->getEntityManager();
        
        if($id == null){
            $contact = new DefaultDb_Entities_Contact();
        } else {
            $contact = $this->find($id);
        }
        
        if($pointId!=null){
            $pointIdRepo = $em->find('DefaultDb_Entities_Point',$pointId);
            $contact->setPointId($pointIdRepo);
        }
        
        $contact->setName($name);
        $contact->setJob($job);
        $contact->setEmail($email);
        $contact->setPhoneNumber($phoneNumber);
        $contact->setStatus($status);
        
        $em->persist($contact);
        $em->flush();
        return;
    }
    
    public function delete($id){
        $em = $this->getEntityManager();
        
        if($id == null){
            return;
        } else {
            $contact = $this->find($id);
            $em->remove($contact);
            $em->flush();
            return;
        }
    }
}