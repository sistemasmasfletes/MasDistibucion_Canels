<?php

use com\masfletes\db\DBUtil;

/**
 * El propósito del controlador es realizar operaciones CRUD con el modelo Client_Package_Catalog
 *
 * @author Andrés Hdz
 */
class User_PackageCatalogController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }

    public function getPackagesAction() {
        $params = $this->getRequest()->getPostJson();
        
        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $packageId = $this->getArrayValue('packageId', $params);
        $clientId=Model3_Auth::getCredentials("id");//$this->getArrayValue('clientId', $params);
        
        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'UserBackStore', 'createOrder');
            $em = $this->getEntityManager('DefaultDb');
            
            $packagesRepo = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            $resultsets = $packagesRepo->getPackages($page,$limit,$sidx,$sord,$clientId,$packageId);

            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('page','limit','sidx','sord','clientId');
            $this->logAndResolveException($ex,$params);
        }
        
    }

    public function saveAction(){
        //$id,$userId,$name,$weight,$width,$height,$depth,$price,$size,$description
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);        
        $name = $this->getArrayValue('name', $params);
        $weight = $this->getArrayValue('weight', $params);
        $width = $this->getArrayValue('width', $params);
        $height = $this->getArrayValue('height', $params);
        $depth = $this->getArrayValue('depth', $params);
        $price = $this->getArrayValue('price', $params);
        $description = $this->getArrayValue('description', $params);

        $clientId = Model3_Auth::getCredentials("id");

        try {
            if(!is_numeric($width)||!is_numeric($height)||!is_numeric($depth))
                $this->generateUserException("Medidas inválidas");

            $size = $width*$height*$depth;

            $this->hasPermission($this->getUserSessionId(), 'UserBackStore', 'createOrder');
            $em = $this->getEntityManager('DefaultDb');

            $packagesRepo = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            $packagesRepo->save($id,$clientId,$name,$weight,$width,$height,$depth,$price,$size,$description);


        }catch (Exception $ex) {
            $this->logAndResolveException($ex,$params);
        }
    }

    public function deleteAction(){
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'UserBackStore', 'createOrder');
            $em = $this->getEntityManager('DefaultDb');
            $packagesRepo = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            $packagesRepo->delete($id);
        }catch (Exception $ex) {
            $this->logAndResolveException($ex,$params);
        }

    }

    public function getProductsFromPackageAction(){
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $packageId = $this->getArrayValue('packageId', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'UserBackStore', 'createOrder');
            $em = $this->getEntityManager('DefaultDb');
            $packagesRepo = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            $products = $packagesRepo->getProductsFromPackage($page,$rowsPerPage,$sortField,$sortDir,$packageId);
            echo json_encode($products);
        }catch (Exception $ex) {
            $this->logAndResolveException($ex,$params);
        }

    }
}