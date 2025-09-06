<?php
use com\masfletes\db\DBUtil;

class OperationController_TipoMovimientosController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getTipoMovimientosAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMovimientosRepo = $em->getRepository('DefaultDb_Entities_TipoMovimientos');
            $tipoMovimientos = $tipoMovimientosRepo->getTipoMovimientosListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMovimientos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMovimientos);

    }
    
    private function getParametros($params)
    {
        $parametros = array();
        $parametros["pagina"] = $this->getPageFromParams( $params );
        $parametros["registrosPorPagina"] = $this->getRowsPerPageFromParams( $params );
        $parametros["ordenarTipo"] = $this->getOrdenarTipo( $params);
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
        $id = (isset($params['tipoMovimiento']['id'])) ? $params['tipoMovimiento']['id'] : NULL;
        $name = $params['tipoMovimiento']['tipoMovimiento'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        if($id == NULL)
        {
            try 
            {
                $tipoMovimientos = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->findOneBy(array('tipoMovimiento' => $name));
                $result['existe'] = ($tipoMovimientos === null) ? $NO_EXISTE : $SI_EXISTE;
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
        $tipoMovimiento = $this->getArrayValue('tipoMovimiento', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMovimientosRepo = $em->getRepository('DefaultDb_Entities_TipoMovimientos');
            $tipoMovimientosRepo->addTipoMovimientos($id,$tipoMovimiento,$client);
            $tipoMovimientos = $tipoMovimientosRepo->getTipoMovimientosListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMovimientos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMovimientos);
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMovimientosRepo = $em->getRepository('DefaultDb_Entities_TipoMovimientos');
            $tipoMovimientosRepo->deleteTipoMovimientos($id);
            $tipoMovimientos = $tipoMovimientosRepo->getTipoMovimientosListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMovimientos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMovimientos);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoMovimientosRepo = $em->getRepository('DefaultDb_Entities_TipoMovimientos');
            $tipoMovimientos = $tipoMovimientosRepo->fncGetTipoMovimientosListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoMovimientos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoMovimientos);
    }
}