<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;

class DefaultDb_Repositories_ConfigurationRepository extends EntityRepository
{
    public function getConfiguration(){
        $em = $this->getEntityManager();
        $query = "
            SELECT id,minutes_per_point minutesPerPoint, costing_base_package_size basePackageSize,costing_power_factor powerFactor FROM configurations WHERE id=:confId
        ";

        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($query);
        $stmt->bindValue("confId",1);

        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        $arrConf = $result[0];

        return $arrConf;
    }

    public function save($confId,$minutesPerPoint,$basePackageSize,$powerFactor){
        $em = $this->getEntityManager();
        $obConfiguration = null;
        if($confId!=null){
            $obConfiguration = $this->find($confId);
            if($obConfiguration!=null){
                $obConfiguration->setMinutesPerPoint($minutesPerPoint);
                $obConfiguration->setBasePackageSize($basePackageSize);
                $obConfiguration->setPowerFactor($powerFactor);
            }
        }

        if($obConfiguration!=null) $em->flush();
    }
}