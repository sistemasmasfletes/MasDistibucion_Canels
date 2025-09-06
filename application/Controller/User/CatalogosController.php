<?php



class User_CatalogosController extends Model3_Scaffold_Controller

{



    public function __construct($request)

    {

        $this->_sc = new Scaffold_DefaultDb_Catalog();

        parent::__construct($request);

    }

    

    public function init()

    {

        if (!Model3_Auth::isAuth())

        {

            $this->redirect('Index/index');

        }

    }

    public function indexAction()

    {

        $credentials = Model3_Auth::getCredentials();
        $dbs = Model3_Registry::getInstance()->get('databases');
        $em = $dbs['DefaultDb'];
         
        $userdata = $em->getRepository('DefaultDb_Entities_User');
        $user =$userdata->find($credentials['id']);
        
        $catalogsHelper = new Helper_Catalogs();
        $this->view->catalogs = $catalogsHelper->getCatalogsByUserId($credentials['id']);
        $this->view->msgtoclient =  $user->getMsg();
        $this->view->msgtoclient2 =  $user->getMsg2();
        $this->view->msgtoclient3 =  $user->getLink();
        
        if ($this->getRequest()->isPost()){
            $post = $this->_post = $this->getRequest()->getPost();
            if(isset($post['msgtoclients'])){
                $user->setMsg($post['msgtoclients']);
            }

            if(isset($post['msgtoclients2'])){
                $user->setMsg2($post['msgtoclients2']);
            }            

            if(isset($post['msgtoclients3'])){
                $user->setLink($post['msgtoclients3']);
            }            

        	$em->persist($user);
        	$em->flush();
        	$this->redirect('User/Catalogos/index');        	
        }
    }



    public function  addAction()

    {

        // obtencion de los datos de la base

        $credentials = Model3_Auth::getCredentials();

        $this->view->catalogs = $this->getUserCatalogs($credentials['id']);



        if ($this->getRequest()->isPost())

        {

            $this->_post = $this->getRequest()->getPost();

            $this->_post['client'] = $credentials['id']; 

        }

        parent::addAction();

    }



    public function  editAction()

    {

        // obtencion de los datos de la base

        $credentials = Model3_Auth::getCredentials();

        $this->view->catalogs = $this->getUserCatalogs($credentials['id']);

        $catalog = null;



        $idCatalog = $this->getRequest()->getParam('id');

        $this->view->catalogId = $idCatalog;

        if($idCatalog != null && $idCatalog != '' && is_numeric($idCatalog))

        {

            // obtenemos el catalogo

            $dbs = Model3_Registry::getInstance()->get('databases');

            $em = $dbs['DefaultDb'];

            /* @var $em Doctrine\ORM\EntityManager */

            $catalogAdapter = $em->getRepository('DefaultDb_Entities_Catalog');

            $catalog =$catalogAdapter->find($idCatalog);

        }



        if ($this->getRequest()->isPost())

        {

            $this->_post = $this->getRequest()->getPost();

            $this->_post['client'] = $credentials['id'];

        }

        $this->view->catalog = $catalog;

        parent::editAction();

    }

    

    public function deleteAction()

    {

        $idCatalog = $this->getRequest()->getParam('id');

        $em = $this->getEntityManager('DefaultDb');

        $catalogRepo = $em->getRepository('DefaultDb_Entities_Catalog');

            

        $catalog = $catalogRepo->find($idCatalog);

        $products = $catalog->getProducts();

        

        // Before deleting a catalog, check if it has associated catalogs

        $isFather = $catalogRepo->findBy(array('catalogFather' => $idCatalog));

 

      

        if (count($products) > 0)

            $this->view->products = true;

        if (count($isFather) ==  0) {

            $this->view->isFather = false;

            if ($this->getRequest()->isPost()) {

                $post = $this->getRequest()->getPost();



                if ($this->view->products == true) { //El catalogo tiene productos que se deberan poner en un estatus de eliminados

                    foreach ($products as $product) {

                        $product->setStatus('2');

                        $em->persist($product);

                        $em->flush();

                    }

                }

                if ($post['delete'] == 1) {

                    parent::deleteAction();

                    $this->view->result = true;

                } else

                if ($post['delete'] == 0)

                    $this->redirect('User/Catalogos/index');

            }

        } else {

            $this->view->isFather = true;

        }

    }



    private function getUserCatalogs($userId)

    {

        $dbs = Model3_Registry::getInstance()->get('databases');

        $em = $dbs['DefaultDb'];

        /* @var $em Doctrine\ORM\EntityManager */

        $userAdapter = $em->getRepository('DefaultDb_Entities_User');

        $user =$userAdapter->find($userId);

        $catalogs = $user->getCatalogs();

        return $catalogs;

    }





}

