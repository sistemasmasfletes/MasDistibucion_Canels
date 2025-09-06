<?php
use com\masfletes\db\DBUtil;

class OperationController_TipoMonedasController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getTipoMonedasAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMonedasRepo = $em->getRepository('DefaultDb_Entities_TipoMonedas');
            $tipoMonedas = $tipoMonedasRepo->getTipoMonedasListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMonedas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMonedas);

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
            $ordenarCampo = "m.moneda";
        }
        else
        {
            switch ($ordenarCampo) 
            {
                case "codigoMoneda":
                    $ordenarCampo = "m.currencyCode";
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
        $id = (isset($params['tipoMonedas']['id'])) ? $params['tipoMonedas']['id'] : NULL;
        $name = $params['tipoMonedas']['moneda'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        if($id == NULL)
        {
            try 
            {
                $tipoMonedas = $em->getRepository('DefaultDb_Entities_TipoMonedas')->findOneBy(array('moneda' => $name));
                $result['existe'] = ($tipoMonedas === null) ? $NO_EXISTE : $SI_EXISTE;
            } 
            catch (Exception $exc) 
            {
                $result["error"] = $this->logAndResolveException($exc,$params);
            }
        }
        //retorna un array
        echo json_encode($result);
    }

    public function saveAction (){
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $moneda = $this->getArrayValue('moneda', $params);
        $codigoMoneda = $this->getArrayValue('codigoMoneda', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMonedasRepo = $em->getRepository('DefaultDb_Entities_TipoMonedas');
            $tipoMonedasRepo->addTipoMonedas($id,$moneda,$client, $codigoMoneda);
            $tipoMonedas = $tipoMonedasRepo->getTipoMonedasListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMonedas["error"] = $this->logAndResolveException($exc,$params);
        }
       echo json_encode($tipoMonedas);
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMonedasRepo = $em->getRepository('DefaultDb_Entities_TipoMonedas');
            $tipoMonedasRepo->deleteTipoMonedas($id);
            $tipoMonedas = $tipoMonedasRepo->getTipoMonedasListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMonedas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMonedas);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMonedasRepo = $em->getRepository('DefaultDb_Entities_TipoMonedas');
            $tipoMonedas = $tipoMonedasRepo->fncGetTipoMonedasListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMonedas["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMonedas);
    }
}