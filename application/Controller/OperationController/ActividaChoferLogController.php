<?php
use com\masfletes\db\DBUtil;

class OperationController_ActividaChoferLogController extends JController {
    private $userSessionId;
    
    /* Inicia el controlador */
    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

     /* Obtiene la el listado de datos del repositorio para mostrarlos en la pantalla
      * Se manda a llamar de  ActividadChoferLogIndexController.js */
    public function getActividaChoferLogAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $actividadChoferLogRepo = $em->getRepository('DefaultDb_Entities_ActividaChoferLog');
            $actividadChoferLog = $actividadChoferLogRepo->getActividaChoferLogListDQL($this->getParametros($params));
        } 
        catch (Exception $exc) 
        {
            $actividadChoferLog["error"] = $this->logAndResolveException($exc,$params);
        }  
        echo json_encode($actividadChoferLog);
    }
    
     /* Obtienen el array de parametros que se enviaron */
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

    /* Si el número de pagina no existe, la pagina es 1 */
    private function getPageFromParams( $params ) {
        $page = $this->getArrayValue( 'page', $params );
        if( !$page ) {
            $page = 1;
        }
       return $page;
    }
    
    /* Si el número de registros por pagina no existe, muestra 10 registros */
    private function getRowsPerPageFromParams( $params ) {
        $rowsPerPage = $this->getArrayValue( 'rowsPerPage', $params );
        if( !$rowsPerPage ) {
            $rowsPerPage = 10;
        }
       return $rowsPerPage;
    }
    
    /* Obtiene el tipo de ordenamiento (Ascendente o Descendente) */
    private function getOrdenarTipo( $params ) {
        $ordenarTipo = $this->getArrayValue( 'sortDir', $params );
        if( !$ordenarTipo ) {
            $ordenarTipo = "asc";
        }
       return $ordenarTipo;
    }
    
    /* Obtiene el campo para ordenar, y le suma el alias de la tabla a la que hace el join */
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
                case "tipoDebito":
                    $ordenarCampo = "td.tipoDebito";
                    break;
                case "actividadTipo":
                    $ordenarCampo = "at.name";
                    break;
                case "estatus":
                    $ordenarCampo = "p.estatus";
                    break;
                case "montoCreditos":
                    $ordenarCampo = "p.montoCreditos";
                    break;
                case "puntoActividad":
                    $ordenarCampo = "pa.name";
                    break;
                case "compraId":
                    $ordenarCampo = "cv.id";
                    break;
                case "estatusActividad":
                    $ordenarCampo = "m.estatus";
                    break;
                default:
                    $ordenarCampo = "m.".$ordenarCampo;
                    break;
            }
        }
       return $ordenarCampo;
    }
    
    /* Obtiene el texto que desea buscar para realizar el filtro */
    private function getFiltro( $params ) {
        $filtro = $this->getArrayValue( 'filtro', $params );
        if( !$filtro ) {
            $filtro = null;
        }
       return $filtro;
    }
    
    /* Recibe los parametros para enviarlos al repositorio y guardarlos */
    public function saveAction() 
    {      
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
             $actividadChoferLogRepo = $em->getRepository('DefaultDb_Entities_ActividaChoferLog');
            $actividadChoferLog = $actividadChoferLogRepo->fncSave($params);    
        } 
        catch (Exception $exc) 
        {
            $actividadChoferLog["error"] = $this->logAndResolveException($exc,$params);
        }  
        echo json_encode($actividadChoferLog);
    }
    
    /* Obtiene los datos para exportar */
    public function fncExportarAction() 
    {        
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try 
        {
            $actividadChoferLogRepo = $em->getRepository('DefaultDb_Entities_ActividaChoferLog');       
            $actividadChoferLog = $actividadChoferLogRepo->fncGetListExport($this->getParametros($params));    
        } 
        catch (Exception $exc) 
        {
            $actividadChoferLog["error"] = $this->logAndResolveException($exc,$params);
        }   
         echo json_encode($actividadChoferLog);
    }
    
    
    
    
}