<?php
use com\masfletes\db\DBUtil;

class OperationController_EstatusController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
     public function getEstatusAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $estatusRepo = $em->getRepository('DefaultDb_Entities_Estatus');
            $estatus = $estatusRepo->getEstatusListDQL($this->getParametros($params));     
        } 
        catch (Exception $exc) 
        {
            $estatus["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($estatus);

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
        $id = (isset($params['estado']['id'])) ? $params['estado']['id'] : NULL;
        $name = $params['estado']['estatu'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        if($id == NULL)
        {
            try 
            {
                $estatus= $em->getRepository('DefaultDb_Entities_Estatus')->findOneBy(array('estatus' => $name));
                $result['existe'] = ($estatus === null) ? $NO_EXISTE : $SI_EXISTE;
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
        $estatu = $this->getArrayValue('estatu', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $estatusRepo = $em->getRepository('DefaultDb_Entities_Estatus');
            $estatusRepo->addEstatus($id,$estatu,$client);
            $estatus = $estatusRepo->getEstatusListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $estatus["error"] = $this->logAndResolveException($exc,$params);
        }
        
        echo json_encode($estatus);
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        
        try 
        {
            $estatusRepo = $em->getRepository('DefaultDb_Entities_Estatus');
            $estatusRepo->deleteEstatus($id);
            $estatus = $estatusRepo->getEstatusListDQL($this->getParametros($params));        
        } 
        catch (Exception $exc) 
        {
            $estatus["error"] = $this->logAndResolveException($exc,$params);
        }
        
        echo json_encode($estatus);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $estatusRepo = $em->getRepository('DefaultDb_Entities_Estatus');
            $estatus = $estatusRepo->fncGetEstatusListExport($this->getParametros($params));       
        } 
        catch (Exception $exc) 
        {
            $estatus["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($estatus);
    }
}