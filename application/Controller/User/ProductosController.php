<?php

class User_ProductosController extends Model3_Scaffold_Controller
{

    public function __construct($request)
    {
        $this->_sc = new Scaffold_DefaultDb_Product();
        parent::__construct($request);
    }

    public function init()
    {
        if (!Model3_Auth::isAuth())
        {
            $this->redirect('Index/index');
        }
        
        $actualCatalog = null;
        $idCatalog = $this->getRequest()->getParam('id');
        if ($idCatalog != null && $idCatalog != '' && is_numeric($idCatalog))
        {
            // obtenemos el catalogo
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $em Doctrine\ORM\EntityManager */
            $catalogAdapter = $em->getRepository('DefaultDb_Entities_Catalog');
            $actualCatalog = $catalogAdapter->find($idCatalog);
        }
        $this->view->catalog = $actualCatalog;
    }

    /**
     * Listado de los productos del catalogo actual
     */
    public function indexAction()
    {
        $productos = null;

        if ($this->view->catalog instanceof DefaultDb_Entities_Catalog)
        {
            $productos = $this->view->catalog->getProducts();
        }

        $this->view->products = $productos;
    }

    /** este medoto no usa el esquema de escafold, esto se hizo asi para manejar correctamente los ajax  */
    public function addAction()
    {
        
        $credentials = Model3_Auth::getCredentials();
        // obtenemos todos los catalogos de la persona
        $catalogsHelper = new Helper_Catalogs();
        $this->view->catalogs = $catalogsHelper->getCatalogsByUserId($credentials['id']);

        $this->view->toSave = $this->view->url(array('module' => false, 'controller' => 'Ajax_UserProducts', 'action' => 'save'), true);
        
        //Buscamos y asignamos la cantidad de creditos para la conversion de acuerdo a la moneda del usuario
        $this->view->creditos = $this->getUserCreditosConversion();
        
        // cargamos los archivos de vista necesarios
        $this->view->getJsManager()->addJs('application/User/Productos/add' . VERSION_JS . '.js');
        $this->view->getJsManager()->addJs('jquery/jquery.form.js');
        $this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('jquery/jquery.number.min.js');
        //$this->view->getJsManager()->addJs('bootstrap/bootstrap.js');
        $this->view->getJsManager()->addJs('sheepIt/jquery.sheepItPlugin-1.0.0.min.js');
        $this->view->getJsManager()->addJsVar('urlGetImages', '"'.$this->view->url(array('module' => 'Ajax','controller' => 'ProductImages', 'action' => 'index' )).'"');
        $this->view->getJsManager()->addJsVar('urlDeleteImage', '"'.$this->view->url(array('module' => 'Ajax','controller' => 'ProductImages', 'action' => 'deleteImage' )).'"');        
//$this->view->getJsManager()->addJs('bootstrap/datepicker/bootstrap-datepicker.js');
    }

    public function editAction()
    {
        $credentials = Model3_Auth::getCredentials();
        $product = null;
        // obtenemos todos los catalogos de la persona
        $catalogsHelper = new Helper_Catalogs();
        $this->view->catalogs = $catalogsHelper->getCatalogsByUserId($credentials['id']);

        $idProduct = $this->getRequest()->getParam('idProduct');
        if ($idProduct != null && $idProduct != '' && is_numeric($idProduct))
        {
            // obtenemos el catalogo
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            
            //Buscamos y asignamos la cantidad de creditos para la conversion de acuerdo a la moneda del usuario
            $this->view->creditos = $this->getUserCreditosConversion();
            
            /* @var $em Doctrine\ORM\EntityManager */
            $productAdapter = $em->getRepository('DefaultDb_Entities_Product');
            $product = $productAdapter->find($idProduct);
            /**
             * @todo Verificar que el producto me pertenezca
             */
        }

        $this->view->product = $product;
        $this->view->toSave = $this->view->url(array('module' => false, 'controller' => 'Ajax_UserProducts', 'action' => 'save'), true);

        // cargamos los archivos de vista necesarios
        $this->view->getJsManager()->addJs('application/User/Productos/add' . VERSION_JS . '.js');
        $this->view->getJsManager()->addJs('jquery/jquery.form.310.js');
        //$this->view->getJsManager()->addJs('jquery/jquery-ui-1.8.12.custom.min.js');
        $this->view->getJsManager()->addJs('jquery/jquery.number.min.js'); 
        //$this->view->getJsManager()->addJs('bootstrap/bootstrap.js');

        $this->view->getJsManager()->addJs('sheepIt/jquery.sheepItPlugin-1.0.0.min.js');
        $this->view->getJsManager()->addJsVar('urlGetImages', '"'.$this->view->url(array('module' => 'Ajax','controller' => 'ProductImages', 'action' => 'index' )).'"');
        $this->view->getJsManager()->addJsVar('urlDeleteImage', '"'.$this->view->url(array('module' => 'Ajax','controller' => 'ProductImages', 'action' => 'deleteImage' )).'"');
        //$this->view->getJsManager()->addJs('bootstrap/datepicker/bootstrap-datepicker.js');
    }

    public function deleteAction()
    {

        $product = null;
        $idProduct = $this->getRequest()->getParam('idProduct');
        if ($idProduct != null && $idProduct != '' && is_numeric($idProduct))
        {
            // obtenemos el catalogo
            $dbs = Model3_Registry::getInstance()->get('databases');
            $em = $dbs['DefaultDb'];
            /* @var $em Doctrine\ORM\EntityManager */
            $productAdapter = $em->getRepository('DefaultDb_Entities_Product');
            $product = $productAdapter->find($idProduct);
            /**
             * @todo Verificar que el producto me pertenezca
             * Hacemos el Borrado logico 
             */
            $product->setStatus(2);
            $em->persist($product);
            $em->flush();
        }

        $this->view->product = $product;
    }
    
    
    private function getUserCreditosConversion() {
         
        $em = $this->getEntityManager('DefaultDb');
        $conn = $em->getConnection();
        $response = array();
        $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);
            
        $moneda_id;
        try {
            $query = 'SELECT moneda_id FROM users WHERE id = ' . $idUsuario . '; ';
            $res = $conn->executeQuery($query);
            $res = $res->fetchAll();
            $moneda_id = $res[0]['moneda_id'];
            
            $queryMXN = 'SELECT id FROM tbltipomonedas WHERE chrCurrencyCode LIKE "%MXn%";';
            $res = $conn->executeQuery($queryMXN);
            $res = $res->fetchAll();
            $monedaMXN = $res[0]['id'] ? $res[0]['id'] : 1;
            
            $monedaUsuario = $moneda_id ? $moneda_id : $monedaMXN;
            
            $queryConversion = 'SELECT chrCreditos FROM tblconversion WHERE intIDMoneda = ' . $monedaUsuario . '; ';
            $res = $conn->executeQuery($queryConversion);
            $res = $res->fetchAll();
            $chrCreditosConversion = $res[0]['chrCreditos'] ? $res[0]['chrCreditos'] : 0;
     

        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        return $chrCreditosConversion;
    }

}
