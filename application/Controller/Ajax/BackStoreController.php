<?php

class Ajax_BackStoreController extends Model3_Controller
{

    public function init()
    {
        $this->view->setUseTemplate(false);
    }

    public function getPackageUserAction()
    {

        $em = $this->getEntityManager('DefaultDb');
        $clientPackageCatalogAdapter = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $idClient = $post['id'];
            $packages = $clientPackageCatalogAdapter->findBy(array('user' => $idClient));
//            $this->view->package = $packages;
        }
        $this->view->package = $packages;
    }

    public function addPackageAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $pack = new DefaultDb_Entities_ClientPackageCatalog();
            $userRepos = $em->getRepository('DefaultDb_Entities_User');
            $packagesAdapter = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            $user = $userRepos->find($post['id']);

            if ($post['peso'] != "" && $post['alto'] != "" && $post['ancho'] != "" && $post['profundidad'] != "" && $post['nombre'] != "")
            {

                $peso = $post['peso'];
                $alto = $post['alto'];
                $ancho = $post['ancho'];
                $profundo = $post['profundidad'];
                $nombre = $post['nombre'];
                $precio = 27+((($profundo * $ancho * $alto)*(1))*(0.00023979));

                $pack->setWeight($peso);
                $pack->setHeight($alto);
                $pack->setWidth($ancho);
                $pack->setDepth($profundo);
                $pack->setPrice($precio);
                $pack->setName($nombre);
                $pack->setUser($user);

                $em->persist($pack);
                $em->flush();
            }
            $packages = $packagesAdapter->findBy(array('user' => $user));
            $this->view->packages = $packages;
        }
    }

    public function deletePackageAction()
    {
        $em = $this->getEntityManager('DefaultDb');

        if ($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $packagesAdapter = $em->getRepository('DefaultDb_Entities_ClientPackageCatalog');
            // $userRespos = $em->getRepository('DefaultDb_Entities_User');

            $idUser = $post['id'];
            $idPack = $post['idPackage'];
            $pack = $packagesAdapter->find($idPack);
            $em->remove($pack);
            $em->flush();

            $packages = $packagesAdapter->findBy(array('user' => $idUser));
            $this->view->packages = $packages;
        }
    }
    
    public function getDataOrderAction()
    {
        
    }
    
    public function searchClientsAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        if($this->getRequest()->isPost())
        {
            $pointRepository = $em->getRepository('DefaultDb_Entities_Point');
            $post = $this->getRequest()->getPost();
            $data = (isset($post['paramString'])) ? $post['paramString']:'';
            $state = (isset($post['state'])) ? $post['state']:'';
            $state = $em->getRepository('DefaultDb_Entities_State')->find($state);
            $conSucursal = (isset($post['conSucursal'])) ? (($post['conSucursal'] == 'true')?TRUE:FALSE) :FALSE;
            $city = (isset($post['city'])) ? $post['city']:'';
            $isbuy = ($post['isbuy'] !== "")?TRUE:FALSE;
			$isbranche = ($post['isbranche'] !== "")?TRUE:FALSE;
			
            /* @var $pointRepository DefaultDb_Repositories_PointRepository */
            $points = $pointRepository->getPointByNameOrAddress($data,$state,$conSucursal,$city,$isbranche);
            $this->view->points = $points;
        }
    }
    
    public function getPointsByCategoryAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $category = $em->getRepository('DefaultDb_Entities_Category')->find($post['idCategory']);
            //$users = $em->getRepository('DefaultDb_Entities_User')->findByCategory($category);
            $users = $em->getRepository('DefaultDb_Entities_User')->findBy(array('category' => $post['idCategory']),array('firstName' => 'ASC'));
			
            $this->view->users = $users;
        }
    }

    public function getUserPackagesAction(){
        $em = $this->getEntityManager('DefaultDb');
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $clientId = $post['userId'];
            $package = $post['package'];
            $buyer = $post['buyerId'];
            $seller = $post['sellerId'];

            $sql = "SELECT 
            			id,
            		 	name, 
            			weight, 
            			width, 
            			height, 
            			depth, 
            			price
                FROM client_package_catalog
                WHERE user_id=:clientId OR user_id=58 OR user_id=:buyerId AND name LIKE :package
                ";

            /*$sql = "SELECT 
                        client_package_catalog.id, 
                        client_package_catalog.name, 
                        weight, 
                        width, 
                        height, 
                        depth, 
                        price
                    FROM client_package_catalog 
                    INNER JOIN branches_user ON client_package_catalog.user_id = branches_user.client_id
                    WHERE branches_user.client_id=:clientId  OR branches_user.client_id=58 AND client_package_catalog.name LIKE :package                
                ";*/
            $conn = $em->getConnection();
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":clientId",$seller);
            $stmt->bindValue(":buyerId",$buyer);
            //$stmt->bindValue(":clientId",$clientId);
            $stmt->bindValue(":package","%".$package."%");
            $stmt->execute(); 

            $array = $stmt->fetchAll(PDO::FETCH_NAMED);
            $this->view->response = $array;
        }
    }

    public function getProductsToOrderAction(){        
        $result = array();
        if($this->getRequest()->isPost())
        {
            $post = $this->getRequest()->getPost();
            $orderId = $post['orderId'];

            if($orderId){
                $em = $this->getEntityManager('DefaultDb');
                $dql = "SELECT (pr.id*-1)id,pto.quantity unity, pto.price packagePrice,pr.weight,pr.width,pr.height,pr.depth,
                        CONCAT(CONCAT('Embalaje para (',pr.name),')') name
                        FROM DefaultDb_Entities_M3CommerceProductToOrder pto 
                            LEFT JOIN pto.product pr
                        WHERE pto.order = :orderId
                ";

                $query = $em->createQuery($dql);
                $query->setParameter('orderId', $orderId);

                $productsToOrder = $query->getResult();                
                $this->view->response = $productsToOrder;              
            }
        }       
    }
}
