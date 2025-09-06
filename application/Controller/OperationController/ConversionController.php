<?php
use com\masfletes\db\DBUtil;

class OperationController_ConversionController extends JController {
    private $userSessionId;
    
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getConversionAction() 
    {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $conversionRepo = $em->getRepository('DefaultDb_Entities_Conversion');
            $conversion = $conversionRepo->getConversionListDQL($this->getParametros($params)); 
        } 
        catch (Exception $exc) 
        {
            $conversion["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($conversion);

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
            $ordenarCampo = "a.moneda";
        }
        else
        {
            $ordenarCampo = ($ordenarCampo=="moneda") ? "a.moneda" : "m.".$ordenarCampo;
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
    
    public function saveAction ()
    {

        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $idMoneda = $this->getArrayValue('idMoneda', $params);
        $compra = $this->getArrayValue('compra', $params);
        $venta = $this->getArrayValue('venta', $params);
        $fecha = $this->getArrayValue('fecha', $params);
        $creditos = $this->getArrayValue('creditos', $params);
        $client=$this->client=$_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        
        $em = $this->getEntityManager('DefaultDb');
        
        $conversionRepo = $em->getRepository('DefaultDb_Entities_Conversion');
        try 
        {
            $conversionRepo->addConversion($id,$idMoneda,$compra,$venta,$fecha,$creditos,$client);
            $conversion = $conversionRepo->getConversionListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $conversion["error"] = $this->logAndResolveException($exc,$params);
        }
        
        echo json_encode($conversion);
    }
    
    public function monedasAction()
    {
        
        $em = $this->getEntityManager('DefaultDb');
        $query= $em->getRepository('DefaultDb_Entities_TipoMonedas')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x=0;
        foreach ($query as $q){
            $result[]=array('id' => $q->getId(), 'moneda' => $q->getMoneda());
            $datos= $result[$x];
            $x++;
        }echo '{"monedas": ' .json_encode($result). '}';
    }

        public function deleteAction()
    {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $conversionRepo = $em->getRepository('DefaultDb_Entities_Conversion');
            $conversionRepo->deleteConversion($id);
            $conversion = $conversionRepo->getConversionListDQL($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $conversion["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($conversion);
    }
    
    public function fncExportarAction() 
    {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
           $conversionRepo = $em->getRepository('DefaultDb_Entities_Conversion');
            $conversion = $conversionRepo->fncGetConversionListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $conversion["error"] = $this->logAndResolveException($exc,$params);
        }
        echo json_encode($conversion);
    }
}