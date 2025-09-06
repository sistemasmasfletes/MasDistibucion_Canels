<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_EstadosRepository extends EntityRepository {

    public function getEstados($page, $rowsPerPage, $sortField, $sortDir, $id, $estadoName, $paisName, $abbreviation, $userId) {
        if ($page == null) {
            $page = 1;
        }

        if ($rowsPerPage == null) {
            $rowsPerPage = 10;
        }

        $offset = ($page - 1) * $rowsPerPage;
        $em = $this->getEntityManager();

        $selectFields = " s.id id, s.name name, s.abbreviation abbreviation, p.id countryId, p.chrNombre paisName ";
        $sql = "SELECT [FIELDS] FROM states s "
                . " LEFT JOIN tblpaises p on s.country_id = p.id "
                . " WHERE (:paisName IS NULL OR p.chrNombre LIKE :paisName) "
                . " AND (:estadoName IS NULL OR s.name LIKE :estadoName) "
                . " AND (:abbreviation IS NULL OR s.abbreviation LIKE :abbreviation) ";

        $queryCount = str_replace('[FIELDS]', ' COUNT(*) totalRecords ', $sql);
        $querySelect = str_replace('[FIELDS]', $selectFields, $sql);
        $querySelect .= ($sortField && $sortDir) ? " ORDER BY " . $sortField . " " . $sortDir : "";
        $querySelect .= " LIMIT " . $rowsPerPage . ' OFFSET ' . $offset;

        $querys = $querySelect . ";" . $queryCount;

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($querys);

        $stmt->bindValue(":estadoName", $estadoName ? "%$estadoName%" : null);
        $stmt->bindValue(":paisName", $paisName ? "%$paisName%" : null);
        $stmt->bindValue(":abbreviation", $abbreviation ? "%$abbreviation%" : null);

        $stmt->execute();

        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $totalRecords = $result[1][0]["totalRecords"];
        unset($result[1]);

        return array('data' => $result[0], 'meta' => array('totalRecords' => $totalRecords));
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
    
    public function saveEstado($id, $abbreviation, $countryId, $name){
        $em = $this->getEntityManager();
        
        if ($id == null){
            $nuevoEstado = new DefaultDb_Entities_Estados();
        } else {
            $nuevoEstado = $this->find($id);
        }
        
        if($countryId){
            $countryRepo = $em->find('DefaultDb_Entities_Paises', $countryId);
            $nuevoEstado->setCountry($countryRepo);
        }
        
        if($abbreviation){
            $nuevoEstado->setAbbreviation($abbreviation);
        }
        
        $nuevoEstado->setName($name);
        
        $em->persist($nuevoEstado);
        $em->flush();
        return;
        
    }
    
    public function delete($id){
        $em = $this->getEntityManager();
        
        if($id == null){
            return;
        } else {
            $estado = $this->find($id);
            $em->remove($estado);
            $em->flush();
            return;
        }
    }
    
 
}
