<?php

use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_CardOperatorsRepository extends EntityRepository {

    const QUERY_TYPE_COUNT = 0;
    const QUERY_TYPE_SELECT = 1;
    const NOT_PAGINATE = false;
    const PAGINATE = true;

    public function getCardOperators($params) {
        $totalRows = $this->getQuery($params, self::NOT_PAGINATE, self::QUERY_TYPE_COUNT)->getSingleScalarResult();
        $resultset = $this->mapResultToArray($this->getQuery($params, self::PAGINATE, self::QUERY_TYPE_SELECT)->getResult());

        return array('data' => $resultset, 'meta' => array('totalRecords' => $totalRows));
    }

    private function getQueryBuilder($params, $queryType) {
        $em = $this->getEntityManager();
        $qb = $em->getRepository('DefaultDb_Entities_CardOperators')->createQueryBuilder('p');

        if ($queryType == self::QUERY_TYPE_SELECT)
            $qb->select('p');
        else
            $qb->select('COUNT(p.id)');

        $cardOperatorId = $params["filter"]["cardOperatorId"];
        $chrOperator = $params["filter"]["chrOperator"];

        $qb->where(':chrOperator IS NULL OR p.chrOperator LIKE :chrOperator');
        $qb->andWhere(':cardOperatorId IS NULL OR p.id=:cardOperatorId');
        $qb->orderBy($params["sortField"], $params["sortDir"]);

        $qb->setParameter('chrOperator', '%' . $chrOperator . '%');
        $qb->setParameter('cardOperatorId', $cardOperatorId);

        return $qb;
    }

    private function getQuery($params, $paginate, $queryType) {
        $queryBuilder = $this->getQueryBuilder($params, $queryType);

        if ($paginate) {
            $offset = ( $params["page"] - 1 ) * $params["rowsPerPage"];
            $queryBuilder->setMaxResults($params["rowsPerPage"]);
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery();
    }

    private function mapResultToArray($rowObject) {
        $resultCardOperators = array();
        foreach ($rowObject as $cardOperator) {
            $resultCardOperators[] = array(
                "id" => $cardOperator->getId(),
                "chrOperator" => $cardOperator->getName()
            );
        }
        return $resultCardOperators;
    }

    public function save($aCardOperator, $userId) {
        $em = $this->getEntityManager();

        $cardOperatorId = $aCardOperator["id"];
        $em->getConnection()->beginTransaction();
        try {
            if (is_null($cardOperatorId)) {
                $cardOperator = new DefaultDb_Entities_CardOperators();
                $cardOperator->setName($aCardOperator["chrOperator"]);
                $cardOperator->setUser($userId);
                $em->persist($cardOperator);
            } else {
                $cardOperator = $em->getRepository('DefaultDb_Entities_CardOperators')->find($cardOperatorId);
                $cardOperator->setName($aCardOperator["chrOperator"]);
                $cardOperator->setUser($userId);
                $em->persist($cardOperator);
            }
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }

    public function delete($cardOperatorId) {
        $em = $this->getEntityManager();


        $cardOperator = $em->getRepository('DefaultDb_Entities_CardOperators')->find($cardOperatorId);
        if ($cardOperator) {
            $em->remove($cardOperator);
            $em->flush();
        }

        return;
    }

}
