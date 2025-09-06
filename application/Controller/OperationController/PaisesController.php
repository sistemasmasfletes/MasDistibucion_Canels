<?php
use com\masfletes\db\DBUtil;

class OperationController_PaisesController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }
    
    public function getPaisesAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $paisesRepo = $em->getRepository('DefaultDb_Entities_Paises');
            $paises = $paisesRepo->getPaisesListDQL($this->getParametros($params));  
        } 
        catch (Exception $exc) 
        {
            $paises["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($paises);
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
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        //si el registro es nuevo busca que no exista algun registro repetido
        try 
        {
            $id = (isset($params['pais']['id'])) ? $params['pais']['id'] : NULL;
            $name = $params['pais']['nombre'];
            if($id == NULL)
            {
            $estatus= $em->getRepository('DefaultDb_Entities_Paises')->findOneBy(array('nombre' => $name));
            $result['existe'] = ($estatus === null) ? $NO_EXISTE : $SI_EXISTE;

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
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $id = $this->getArrayValue('id', $params);
            $nombre = $this->getArrayValue('nombre', $params);
            $estado = $this->getArrayValue('estado', $params);
            $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
            
            $paisesRepo = $em->getRepository('DefaultDb_Entities_Paises');
            $paisesRepo->addPaises($id,$nombre,$client,$estado);
            $paises = $paisesRepo->getPaisesListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $paises["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($paises);
    }
    
    public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $id = $this->getArrayValue('id', $params);
            $paisesRepo = $em->getRepository('DefaultDb_Entities_Paises');
            $paisesRepo->deletePaises($id);
            $paises = $paisesRepo->getPaisesListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $paises["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($paises);
    }
 
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $paisesRepo = $em->getRepository('DefaultDb_Entities_Paises');
            $paises = $paisesRepo->fncGetListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $paises["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($paises);
    }
    
}