<?php
use com\masfletes\db\DBUtil;

class OperationController_CuentasController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getCuentasAction() 
    {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $cuentasRepo = $em->getRepository('DefaultDb_Entities_Cuentas');
            $cuentas = $cuentasRepo->getCuentasListDQL($this->getParametros($params));       
        } 
        catch (Exception $exc) 
        {
            $cuentas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($cuentas);

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
            $ordenarCampo = "m.cuenta";
        }
        else
        {
            switch ($ordenarCampo) 
            {
                case "moneda":
                    $ordenarCampo = "a.moneda";
                    break;
                case "pais":
                    $ordenarCampo = "b.nombre";
                    break;
                case "banco":
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

    public function fncVerificarAction() 
    {
        //variables estaticas
        $SI_EXISTE = 1;
        $NO_EXISTE = 0;
        //obtiene los parametros enviados por post
        $params = $this->getRequest()->getPostJson();
        $id = (isset($params['cuenta']['id'])) ? $params['cuenta']['id'] : NULL;
        $name = $params['cuenta']['numeroCuenta'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        try 
        {
            if($id == NULL)
            {
                $cuentas = $em->getRepository('DefaultDb_Entities_Cuentas')->findOneBy(array('numeroCuenta' => $name));
                $result['existe'] = ($cuentas === null) ? $NO_EXISTE : $SI_EXISTE;
            } 
        }
        catch (Exception $exc) 
        {
            $result["error"] = $this->logAndResolveException($exc,$params);
        }
        //retorna un array
        echo json_encode($result);
    }
    
    public function saveAction (){
        
        $params = $this->getRequest()->getPostJson();
        
        $cuentaArray = array();
        $cuentaArray["id"] = $this->getArrayValue( 'id', $params );
        $cuentaArray["numeroCuenta"] = $this->getArrayValue( 'numeroCuenta', $params );
        $cuentaArray["cuenta"] = $this->getArrayValue( 'cuenta', $params );
        $cuentaArray["clabeInterbancaria"] = $this->getArrayValue( 'clabeInterbancaria', $params );
        $cuentaArray["idMoneda"] = $this->getArrayValue( 'idMoneda', $params );
        $cuentaArray["idBanco"] = $this->getArrayValue( 'idBanco', $params );
        $cuentaArray["tipoOperador"] = $this->getArrayValue( 'tipoOperador', $params );
        $cuentaArray["estado"] = $this->getArrayValue( 'estado', $params );
        $cuentaArray["idPais"] = $this->getArrayValue( 'idPais', $params );
        $cuentaArray["idOperador"] = $this->getArrayValue( 'idOperador', $params );
        $cuentaArray["cliente"] = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $cuentaArray["tipoPago"] = $this->getArrayValue('tipoPago', $params);
                
        
        try 
        {
            $entityManager = $this->getEntityManager('DefaultDb');
            $cuentasRepo = $entityManager->getRepository('DefaultDb_Entities_Cuentas');
            $cuentasRepo->addCuenta( $cuentaArray );
            $cuentas = $cuentasRepo->getCuentasListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $cuentas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($cuentas);
        
    }
    
    public function monedasAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        try 
        {
            $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->getQuery()
                    ->getResult();

            foreach ($query as $q) 
            {
                $result[] = array
                (
                    'id' => $q->getId(), 
                    'moneda' => $q->getMoneda()
                );
            }
            $response["monedas"] = $result;
        }
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode( $response );
    }
    
    public function bancosAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        try 
        {
            $query = $em->getRepository('DefaultDb_Entities_Bancos')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->getQuery()->getResult();

            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'name' => $q->getName()
                );
            }
            $response["bancos"] = $result;
        }
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($response);
    }
    
    public function paisesAction()
    {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        $ACTIVO=1;
        try 
        {
           /*  $query = $em->getRepository('DefaultDb_Entities_Paises')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->getQuery()->getResult();*/
           $countries = $em->getRepository('DefaultDb_Entities_Paises')->findBy(array('estado' => $ACTIVO));

            foreach ($countries as $q) {
                $result[] = array(
                    'id' => $q->getId(), 
                    'name' => $q->getNombre()
                );
            }
            $response["paises"] = $result;        
        }
        catch (Exception $exc) 
        {
            $response["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($response);
        
//        $em = $this->getEntityManager('DefaultDb');
//        $query= $em->getRepository('DefaultDb_Entities_Paises')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
//        $x=0;
//        foreach ($query as $q){
//            $result[]=array('id' => $q->getId(), 'nombre' => $q->getNombre());
//            $datos= $result[$x];
//            $x++;
//        }echo '{"paises": ' .json_encode($result). '}';
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $cuentasRepo = $em->getRepository('DefaultDb_Entities_Cuentas');
            $cuentasRepo->deleteCuentas($id);
            $cuentas = $cuentasRepo->getCuentasListDQL($this->getParametros($params)); 
        } 
        catch (Exception $exc) 
        {
            $cuentas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($cuentas);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        try 
        {
            $em = $this->getEntityManager('DefaultDb');
            $cuentasRepo = $em->getRepository('DefaultDb_Entities_Cuentas');
            $cuentas = $cuentasRepo->fncGetListExport($this->getParametros($params));   
        } 
        catch (Exception $exc) 
        {
            $cuentas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($cuentas);
    }
    
      public function tipoPagosAction() {

        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        try {
                $query = $em->getRepository('DefaultDb_Entities_TipoPagos')
                        ->createQueryBuilder('m')
                        ->select('m')
                        ->getQuery()
                        ->getResult();
                
            foreach ($query as $q) {
                $result[] = array
                    (
                    'id' => $q->getId(),
                    'tipoPago' => $q->getTipoPago()
                );
            }
            $response["tipoPagos"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }
}