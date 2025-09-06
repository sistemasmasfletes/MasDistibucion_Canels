<?php
use com\masfletes\db\DBUtil;

class OperationController_TipoPagosController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getTipoPagosAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $pagosRepo = $em->getRepository('DefaultDb_Entities_TipoPagos');
            $pagos = $pagosRepo->getTipoPagosListDQL($this->getParametros($params));  
        } 
        catch (Exception $exc) 
        {
            $pagos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($pagos);

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
        $id = (isset($params['tipoPago']['id'])) ? $params['tipoPago']['id'] : NULL;
        $name = $params['tipoPago']['tipoPago'];
        
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        if($id == NULL)
        {
            try 
            {
                $tipoPagos = $em->getRepository('DefaultDb_Entities_TipoPagos')->findOneBy(array('tipoPago' => $name));
                $result['existe'] = ($tipoPagos === null) ? $NO_EXISTE : $SI_EXISTE;
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
        $tipoPago = $this->getArrayValue('tipoPago', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoPagosRepo = $em->getRepository('DefaultDb_Entities_TipoPagos');
            $tipoPagosRepo->addTipoPagos($id,$tipoPago,$client);
            $pagos = $tipoPagosRepo->getTipoPagosListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $pagos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($pagos); 
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoPagosRepo = $em->getRepository('DefaultDb_Entities_TipoPagos');
            $tipoPagosRepo->deleteTipoPagos($id);
            $pagos = $tipoPagosRepo->getTipoPagosListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $pagos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($pagos); 
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $tipoPagosRepo = $em->getRepository('DefaultDb_Entities_TipoPagos');
            $tipoPagos = $tipoPagosRepo->fncGetTipoPagosListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $tipoPagos["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($tipoPagos);
    }
}