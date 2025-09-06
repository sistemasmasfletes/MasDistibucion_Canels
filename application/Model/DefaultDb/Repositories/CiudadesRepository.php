<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_CiudadesRepository extends EntityRepository {

    public function getEstadosById($sortField, $sortDir, $id, $countryId) {
        $em = $this->getEntityManager();

        $selectFields = " s.id id, s.name name ";
        $sql = "SELECT [FIELDS] FROM states s where s.country_id = :countryId";

        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect .= ($sortField && $sortDir) ? " ORDER BY " . $sortField . " " . $sortDir : "";

        $querys = $querySelect . ";" ;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(":countryId", $countryId ? $countryId : null);

        $stmt->execute();

        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        unset($result[1]);

        return array('data' => $result[0]);
    }
    
    public function getCiudades($page, $rowsPerPage, $sortField, $sortDir, $id, $name,$state,$country, $userId) {
        if ($page == null) {
            $page = 1;
        }

        if ($rowsPerPage == null) {
            $rowsPerPage = 10;
        }

        $offset = ($page - 1) * $rowsPerPage;
        $em = $this->getEntityManager();

        $selectFields = " c.id id, c.name name, c.state_id stateId, s.name state, s.country_id countryId, p.chrNombre country ";
        $sql = " SELECT [FIELDS] FROM city c "
                . " LEFT JOIN states s on c.state_id = s.id "
                . " LEFT JOIN tblpaises p on p.id = s.country_id "
                . " WHERE (:paisName IS NULL OR p.chrNombre LIKE :paisName) "
                . " AND (:estadoName IS NULL OR s.name LIKE :estadoName) "
                . " AND (:ciudadName IS NULL OR c.name LIKE :ciudadName) ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect .= ($sortField && $sortDir) ? " ORDER BY " . $sortField . " " . $sortDir : "";
        $querySelect .= " LIMIT " . $rowsPerPage . ' OFFSET ' . $offset;

        $querys = $querySelect . ";" . $queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(":estadoName", $state ? "%$state%" : null);
        $stmt->bindValue(":paisName", $country ? "%$country%" : null);
        $stmt->bindValue(":ciudadName", $name ? "%$name%" : null);

        $stmt->execute();

        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);

        return array('data' => $result[0], 'meta' => array('totalRecords' => $totalRecords));
    }
    
    public function getCiudadById($id){
        $em = $this->getEntityManager();
        
        $sqlSelect = " c.id id, c.name name, c.state_id stateId, s.name state, s.country_id countryId, p.chrNombre country, c.chrEstatus ";
        $query = " SELECT [FIELDS] FROM city c "
                . " LEFT JOIN states s on c.state_id = s.id "
                . " LEFT JOIN tblpaises p on p.id = s.country_id "
                . " WHERE c.id = :id ";
        
        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect . "; " . $queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(":id", $id);

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        return array('data' => $result[0], 'meta' => array('totalRecords' => $result[1][0]["totalRecords"]));
    }

    public function getEstadoById($id) {
        $em = $this->getEntityManager();

        $sqlSelect = "  s.id id, s.name name, s.abbreviation abbreviation, s.country_id countryId ";
        $query = "SELECT [FIELDS] "
                . " FROM states s "
                . " WHERE s.id = :id";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $query);
        $querySelect = str_replace('[FIELDS]', $sqlSelect, $query);
        $querys = $querySelect . "; " . $queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(":id", $id);

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);

        return array('data' => $result[0], 'meta' => array('totalRecords' => $result[1][0]["totalRecords"]));
    }
    
    public function saveCiudad($id, $countryId, $stateId, $name,$chrEstatus){
        $em = $this->getEntityManager();
        
        if ($id == null){
            $nuevaCiudad = new DefaultDb_Entities_City();
        } else {
            $nuevaCiudad = $this->find($id);
        }
        
        if($stateId){
            $stateRepo = $em->find('DefaultDb_Entities_State', $stateId);
            $nuevaCiudad->setStatus($stateRepo);
        }
        
        $nuevaCiudad->setEstatus(($chrEstatus)? 1 : 0);
        $nuevaCiudad->setName($name);
        
        $em->persist($nuevaCiudad);
        $em->flush();
        return;
        
    }
    
    public function delete($id){
        $em = $this->getEntityManager();
        
        if($id == null){
            return;
        } else {
            $ciudad = $this->find($id);
            $em->remove($ciudad);
            $em->flush();
            return;
        }
    }

}
