<?php
use com\masfletes\db\DBUtil;

class OperationController_BancosController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID']; 
    }

    
    public function getBancosAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
            $bancos = $bancosRepo->getBancosListDQL($this->getParametros($params)); 
        } 
        catch (Exception $exc) 
        {
            $bancos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($bancos);

    }

    private function getParametros($params)
    {
        $parametros = array();
        $parametros["pagina"] = $this->getPageFromParams( $params );
        $parametros["registrosPorPagina"] = $this->getRowsPerPageFromParams( $params );
        $parametros["ordenarTipo"] = $this->getOrdenarTipo( $params);
        $parametros["filtro"] = $this->getFiltro( $params);
        $parametros["ordenarCampo"] = $this->getOrdenarCampo($params);
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
    
    private function getOrdenarCampo( $params ) {
        $ordenarCampo = $this->getArrayValue( 'sortField', $params );
        if( !$ordenarCampo ) 
        {
            $ordenarCampo = "m.name";
        }
        else
        {
            $ordenarCampo = "m.".$ordenarCampo;
        }
       return $ordenarCampo;
    }
    
    public function fncVerificarAction() 
    {
        //variables estaticas
        $SI_EXISTE = 1;
        $NO_EXISTE = 0;
        //obtiene los parametros enviados por post
        $params = $this->getRequest()->getPostJson();
        $id = (isset($params['banco']['id'])) ? $params['banco']['id'] : NULL;
        $name = $params['banco']['name'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        if($id == NULL)
        {
            try 
            {
                $bancos = $em->getRepository('DefaultDb_Entities_Bancos')->findOneBy(array('name' => $name));
                $result['existe'] = ($bancos === null) ? $NO_EXISTE : $SI_EXISTE;
            } 
            catch (Exception $exc) 
            {
                $result["error"] = $this->logAndResolveException($exc,$params);
            }
        }
        //retorna un array
        echo json_encode($result);
    }
    
    public function saveAction ()
    {
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
        $id = $this->getArrayValue('id', $params);
        $name = $this->getArrayValue('name', $params);
        $estado  = $this->getArrayValue('estado', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        try 
        {
            $bancosRepo->addBancos($id,$name,$estado,$client);
            $bancos = $bancosRepo->getBancosListDQL($this->getParametros($params));
        } 
        catch (Exception $exc) 
        {
            $bancos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($bancos);
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $em = $this->getEntityManager('DefaultDb');
        $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
        try 
        {
            $bancosRepo->deleteBancos($id);
            $bancos = $bancosRepo->getBancosListDQL($this->getParametros($params)); 
            
        } 
        catch (Exception $exc) 
        {
            $bancos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($bancos);
    }
    
    public function getBancoCatalogAction() 
    {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
            $bancos = $bancosRepo->getFiltroBancosDQL($this->getParametros($params));   
        } 
        catch (Exception $exc) 
        {
            $bancos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($bancos);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
            $bancos = $bancosRepo->getBancosListExport($this->getParametros($params));     
        } 
        catch (Exception $exc) 
        {
            $bancos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($bancos);
    }
    
    
}