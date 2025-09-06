<?php

use com\masfletes\db\DBUtil;

class OperationController_TransferenciaCreditosController extends JController {

    private $userSessionId;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function indexAction() {
        
    }

    public function getTransferenciaCreditosAction() 
    {
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $transferenciaCreditosRepo = $em->getRepository('DefaultDb_Entities_TransferenciaCreditos');
            $TransferenciaCreditos = $transferenciaCreditosRepo->getTransferenciaCreditosListDQL($this->getParametros($params));
        } 
        catch (Exception $exc) 
        {
            $TransferenciaCreditos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($TransferenciaCreditos);
    }
    
    
    private function getParametros($params)
    {
        $parametros = array();
        $parametros["pagina"] = $this->getPageFromParams( $params );
        $parametros["registrosPorPagina"] = $this->getRowsPerPageFromParams( $params );
        $parametros["ordenarTipo"] = $this->getOrdenarTipo( $params);
        $parametros["ordenarCampo"] = $this->getOrdenarCampo($params);
        $parametros["filtro"] = $this->getFiltro( $params);
        return $parametros;
    }

    private function getPageFromParams( $params ) {
        $page = $this->getArrayValue( 'page', $params );
        if( !$page ) {
            $page = 1;
        }
       return $page;
    }
    
    private function getRowsPerPageFromParams( $params ) {
        $rowsPerPage = $this->getArrayValue( 'rowsPerPage', $params );
        if( !$rowsPerPage ) {
            $rowsPerPage = 10;
        }
       return $rowsPerPage;
    }
    
    private function getOrdenarTipo( $params ) {
        $ordenarTipo = $this->getArrayValue( 'sortDir', $params );
        if( !$ordenarTipo ) {
            $ordenarTipo = "asc";
        }
       return $ordenarTipo;
    }
    
    private function getOrdenarCampo( $params ) {
        $ordenarCampo = $this->getArrayValue( 'sortField', $params );
        if( !$ordenarCampo ) 
        {
            $ordenarCampo = "m.fecha";
        }
        else
        {
            switch ($ordenarCampo) 
            {
                case "client":
                    $ordenarCampo = "u.commercialName";
                    break;
                case "category":
                    $ordenarCampo = "c.name";
                    break;
                default:
                    $ordenarCampo = "m.".$ordenarCampo;
                    break;
            }
        }
       return $ordenarCampo;
    }
    
    private function getFiltro( $params ) {
        $filtro = $this->getArrayValue( 'filtro', $params );
        if( !$filtro ) {
            $filtro = null;
        }
       return $filtro;
    }
    
    public function saveAction() 
    {
        $em = $this->getEntityManager('DefaultDb');
        $params = $this->getRequest()->getPostJson();
        $transferenciaJSON = array();
        try 
        {
            $transferenciaJSON["id"] = $this->getArrayValue('id', $params);
            $transferenciaJSON["usuario"] = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
            $transferenciaJSON["fecha"] = new DateTime( );
            $transferenciaJSON["monto"] = $this->getArrayValue('monto', $params);
            $transferenciaJSON["descripcion"] = $this->getArrayValue('descripcion', $params);
            $transferenciaJSON["client"] = $this->getArrayValue('cliente', $params);
        
            $transferenciaCreditosRepo = $em->getRepository('DefaultDb_Entities_TransferenciaCreditos');
            $transferenciaCreditosRepo->addTransferenciaCreditos($transferenciaJSON);
            $TransferenciaCreditos = $transferenciaCreditosRepo->getTransferenciaCreditosListDQL($this->getParametros($params));
        } 
        catch (Exception $exc) 
        {
            $TransferenciaCreditos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($TransferenciaCreditos);
    }

     public function categoriasAction() {

        $em = $this->getEntityManager('DefaultDb');
        $response = array();
        $params = array();
        try 
        {
            $query = $em->getRepository('DefaultDb_Entities_Category')
                ->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getResult();
        
            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'nombre' => $q->getName()
                );
            }
            $response["categorias"] = $result;
        } 
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode( $response );
    }
    
    public function usuariosAction() 
    {
        $parameters = json_decode(file_get_contents("php://input"));
        $idCategoria = $parameters->idCategoria;
        $cliente = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $response = array();
        $params = array();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $categoria = $em->getRepository('DefaultDb_Entities_Category')
                ->findById($idCategoria);
        
            $query = $em->getRepository('DefaultDb_Entities_User')
                    ->createQueryBuilder('m')
                    ->where("m.category = :categoria AND m.id != :cliente" )
                    ->setParameter( "categoria", $categoria )
                    ->setParameter( "cliente", $cliente )
                    ->getQuery()
                    ->getResult();

            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'commercialName' => $q->getCommercialName()
                );
            }
            $response["clientes"] = $result;
        } 
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        
        echo json_encode( $response ) ;
    }

    public function deleteAction() 
    {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $transferenciaCreditosRepo = $em->getRepository('DefaultDb_Entities_TransferenciaCreditos');
            $transferenciaCreditosRepo->deleteTransferenciaCreditos($id);
            $TransferenciaCreditos = $transferenciaCreditosRepo->getTransferenciaCreditosListDQL($this->getParametros($params));
        } 
        catch (Exception $exc) 
        {
            $TransferenciaCreditos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($TransferenciaCreditos);
    }
    
    public function creditosAction() 
    {
        $cliente = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        $response = array();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $credito = $em->getRepository('DefaultDb_Entities_User')
                ->findById($cliente);
       
            foreach ($credito as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'creditos' => $q->getCredito()
                );
            }
            $response["clientes"] = $result;
        } 
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode( $response ) ;
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $transferenciaCreditosRepo = $em->getRepository('DefaultDb_Entities_TransferenciaCreditos');
            $transferenciaCreditos = $transferenciaCreditosRepo->fncGetListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $transferenciaCreditos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($transferenciaCreditos);
    }
    
    public function clientesAction() 
    {
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        $response = array();
        $result = array();
        $ROLE_CLIENTE = 3;
        try 
        {
            $textoCliente = $this->getArrayValue('param1', $params);
            $idCliente = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
            $role = $em->getRepository('DefaultDb_Entities_Role')->findById($ROLE_CLIENTE);
            $query = $em->getRepository('DefaultDb_Entities_User')
                    ->createQueryBuilder('m')
                    ->where("m.commercialName LIKE :commercialName" )
                    ->andWhere('m.id != :cliente')
                    ->andWhere('m.role = :role')
                    ->setParameter( "commercialName", '%'.$textoCliente.'%' )
                    ->setParameter( "cliente", $idCliente )
                    ->setParameter( "role", $role )
                    ->getQuery()
                    ->getResult();

            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'commercialName' => $q->getCommercialName()
                );
            }
            $response[0] = $result;
        } 
        catch (Exception $exc) 
        {
            $response[1] = $this->logAndResolveException($exc,$params);
        }
        
        echo json_encode( $response ) ;
    }
    
    
    
      
    //    public function creditosDisponiblesAction() 
//    {
//        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
//        $em = $this->getEntityManager('DefaultDb');
//        try 
//        {
//            
//        } 
//        catch (Exception $exc) 
//        {
//            $TransferenciaCreditos["error"] = $this->logAndResolveException($exc,$params);
//        }
//        $query = $em->getRepository('DefaultDb_Entities_CompraCreditos')->createQueryBuilder('m')->select('m')->where($client)->getQuery()->getResult();
//        $x = 0;
//        foreach ($query as $q) {
//            $result[] = array('id' => $q->getId(), 'creditos' => $q->getCreditos());
//            $datos = $result[$x];
//            $x++;
//        }echo '{"creditos": ' . json_encode($result) . '}';
//    }
//
//    public function creditosSQLAction() 
//    {
//        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['username'];
//
//        $em = $this->getEntityManager('DefaultDb');
//        $q = $em->createQuery("select COUNT( u.creditos ) from DefaultDb_Entities_CompraCreditos u  WHERE  u.client = :client");
//        $q->setParameter("client", $client);
//        // print_r($q);
//        $resultado = $q->getResult();
//
//        if (count($resultado) > 0) {
//            echo json_encode($resultado[0]);
//        } else {
//            echo '{"id": "0"}';
//        }
//    }
}
