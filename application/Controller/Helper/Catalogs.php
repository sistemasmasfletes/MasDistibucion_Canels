<?php
/**
 *  Este helper nos ayudara con el manejo de los catalogos en el sistema
 */
class Helper_Catalogs
{
    public function getCatalogsByUserId($userId)
    {
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
        /* @var $em Doctrine\ORM\EntityManager */
        $userAdapter = $em->getRepository('DefaultDb_Entities_User');
        /**
         *  @todo Como ya se manejan subcatalogos arreglar esta parte para que los traiga ordenados
         */
        $user =$userAdapter->find($userId);
        $catalogs = $user->getCatalogs();
        return $catalogs;
    }
}
?>
