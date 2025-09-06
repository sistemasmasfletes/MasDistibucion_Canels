<?php

use com\masfletes\db\DBUtil;

/**
 * El propósito del controlador es realizar operaciones CRUD con el modelo vehículos
 *
 * @author Andrés Hdz
 */
class OperationController_UsersController extends JController {
    const ROL_ADMIN_CODE = 1;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }

    public function indexAction() {}

    public function getUsersAction() {
        $params = $this->getRequest()->getPostJson();
        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = $this->getArrayValue('id', $params);
        $userType =  $this->getArrayValue('type', $params);
        $filtro = $this->getArrayValue('filtro', $params);
        
        $Clave = $this->getArrayValue('Clave', $filtro);
        $Nombre = $this->getArrayValue('Nombre', $filtro);
        $Apellidos = $this->getArrayValue('Apellidos', $filtro);
        $Usuario = $this->getArrayValue('Usuario', $filtro);
        $Tipo = $this->getArrayValue('Tipo', $filtro);

        if (!$sidx)
            $sidx = 1;

        try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            $conn = $this->getConnection();
            $parameters = array($this->getUserSessionId(), $page, $limit, $sidx, $sord, $id, $userType, "%$Clave%","%$Nombre%","%$Apellidos%","%$Usuario%" );
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_users_listar', $parameters, PDO::FETCH_NAMED);
            
        } catch (Exception $ex) {
            $params = compact('page','limit','sidx','sord','id');
            $this->logAndResolveException($ex,$params);
        }
        
        // Obtener las zonas a las que pertenece cada usuario
        $users = array();
        if(!is_null($resultsets[0])){
            foreach ($resultsets[0] as $user){
                $parameters = array($user['id']);
                $zoneResults = DBUtil::executeStoredProc($conn, 'sp_user_zone_obtener', $parameters, PDO::FETCH_NAMED);
                $zone_id = array();
                if(!is_null($zoneResults[0])){
                    foreach($zoneResults[0] as $zone){
                        array_push($zone_id, $zone['zone_id']);
                    }
                }
                $user['zonas_id'] = $zone_id;
                array_push($users, $user);
            }
            $resultsets[0] = $users;
        }
        unset($conn);
        echo json_encode($resultsets);
    }

    public function saveAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $category_id = $this->getArrayValue('category_id',$params);
        $parent_id =  $this->getArrayValue('parent_id', $params);
        $point_id = $this->getArrayValue('point_id', $params);
        $code = $this->getArrayValue('code', $params);
        $status = $this->getArrayValue('status', $params);
        $first_name = $this->getArrayValue('first_name', $params);
        $last_name = $this->getArrayValue('last_name', $params);
        $type = $this->getArrayValue('type', $params);        
        $username = $this->getArrayValue('username', $params);
        $password = $this->getArrayValue('password', $params);
        $title = $this->getArrayValue('title', $params);
        $commercial_name = $this->getArrayValue('commercial_name', $params);
        $cell_phone = $this->getArrayValue('cell_phone', $params);
        $local_number = $this->getArrayValue('local_number', $params);
        $type = $this->getArrayValue('type', $params);        
        $dayInvoice  = $this->getArrayValue('dayInvoice', $params);
        //Se establece el día de pago por defecto en 0
        $dayInvoice = 0;
        $moneda_id = $this->getArrayValue('moneda_id', $params);
        $zonas_id = $this->getArrayValue('zonas_id', $params);
            
        try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', ($id==null) ? 'agregar' : 'editar');
            
            $conn = $this->getConnection();
            $parameters = array($id, $category_id, Model3_Auth::getCredentials("id"), $point_id, $code, $status, $first_name, $last_name, $username, $password
                    ,$title, $commercial_name, $cell_phone,$local_number, $type, $dayInvoice, $moneda_id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_users_guardar', $parameters, PDO::FETCH_NAMED);
            if(!is_null($id)){
                $parameters = array($id,null,'eliminar');
                $resultsets = DBUtil::executeStoredProc($conn, 'sp_user_zone_guardar', $parameters, PDO::FETCH_NAMED);
            }
            if ($zonas_id) {
                foreach ($zonas_id as $zona_id) {
                    $parameters = array($id, $zona_id, null);
                    $resultsets = DBUtil::executeStoredProc($conn, 'sp_user_zone_guardar', $parameters, PDO::FETCH_NAMED);
                }
            }


            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('id','category_id','parent_id','point_id','code','status','first_name','last_name','type','username'
                ,'password','title','commercial_name','cell_phone','local_number','type','dayInvoice', 'moneda_id');
            $this->logAndResolveException($ex,$params);
        }
        
    }
    
     public function deleteAction() {
          $params = $this->getRequest()->getPostJson();
          $id = $params['id'];
          if($id==null)
                return;
          try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'eliminar');
            
            $conn = $this->getConnection();
            $parameters = array($id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_users_borrar', $parameters, PDO::FETCH_NAMED);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_user_zone_borrar', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex,$params);
        }
        
    }
    
    function getUserCatalogAction(){
        $params = $this->getRequest()->getPostJson();
        $userName = $this->getArrayValue('param1', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            $conn = $this->getConnection();
            $parameters = array($this->getUserSessionId(),$userName);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_userCatalog_list', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('userName');
            $this->logAndResolveException($ex,$params);
        }
        
    }

    function getCategoriesAction(){
        try{
           // $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $em = $this->getEntityManager('DefaultDb');
            $categoryRepo = $em->getRepository('DefaultDb_Entities_Category');

            $rows = $categoryRepo->findBy(array(),array('name' => 'ASC'));
            $arrCategories = array();
            foreach ($rows as $category) {
                $arrCategories[] = array("id"=>$category->getId(),"name"=>$category->getName());
            }
            
            echo json_encode($arrCategories);
        }catch(Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);        
        }
    }
    
    //FUNCIÓN CATALOGO DE USUARIOS EN PANTALA SALVAR TRANFERENCIA MODULO CHOFER
    public function getUserDeliveryAction(){
        try {
            $this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
            
            $em = $this->getEntityManager('DefaultDb');
            $userDeliveryRepo = $em->getRepository('DefaultDb_Entities_Category');
            
            $rows= $userDeliveryRepo->findAll();
            $arrUserDelivery = array();
            foreach($rows as $uDelivery){
                $arrUserDelivery[] = array(
                    "id" => $uDelivery->getId(),
                    "firstDate" => $uDelivery->getFirstName());
            }
            
            echo json_encode($arrUserDelivery);
        } catch (Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //FUNCIÓN QUE OBTIENE LOS CENTROS DE INTERCAMBIO AL EDITAR O REGISTRAR UN USUARIO
    function getPointAction(){
        try{
//            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('p')
                ->from('DefaultDb_Entities_Point', 'p')
                ->where('p.type=2');
            
            $query = $em->createQuery($dql);
            $points = $query->getResult();
            
            $arrPoints = array();
            foreach ($points as $point) {
                $arrPoints[] = array(
                    "id"=>$point->getId(),
                    "name"=>$point->getName());
            }
            
            echo json_encode($arrPoints);
        }  catch (Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);
        }
    }
    
    // Obtiene los tipos de moneda
    function getMonedaAction(){
        try{
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('m')
                ->from('DefaultDb_Entities_TipoMonedas', 'm');
            
            $query = $em->createQuery($dql);

            $monedas = $query->getResult();
            
            $arrMonedas = array();
            foreach ($monedas as $moneda) {
                $arrMonedas[] = array(
                    "id"=>$moneda->getId(),
                    "chrMoneda"=>$moneda->getMoneda());
            }
            
            echo json_encode($arrMonedas);
        }  catch (Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);
        }
    }
    
    function getRoleCatalogAction(){
    	$userol = Model3_Auth::getCredentials('role');
    
    	try {
    		//$this->hasPermission($this->getUserSessionId(), 'Warehouseman', 'LISTAR');
    		$em = $this->getEntityManager('DefaultDb');
    		$dql = $em->createQueryBuilder();
    		$dql->select('u')
    		->from('DefaultDb_Entities_Role', 'u');
    
    		/*if($userol != 1){
    			$dql->where('u.id NOT IN (1,5,7,8)');
    		}*/

    		if($userol == 1){
    			$dql->where('u.id IN (1,5,7)');
    		}
    		
    		if($userol == 7){
    			$dql->where('u.id IN (3,4,8)');
    		}
    		
    		if($userol == 8){
    			$dql->where('u.id IN (2,6)');
    		}
    		
    		$query=$em->createQuery($dql);
    		$rolist = $query->getResult();
    		 
    		$catRol = array();
    		foreach($rolist as $rol){
    			$catRol[] = array(
    					"id" => $rol->getId(),
    					"tipo" => $rol->getName());
    		}
    		 
            echo json_encode($catRol);
    		} catch (Exception $ex){
    		$params = array();
    		$this->logAndResolveException($ex,$params);
    	}
    	 
    }
    
    //Obtiene Usuario franquisiatarios
    function getFranchiseeAction(){
    	try{
    		//$this->hasPermission($this->getUserSessionId(), 'usuarios', 'editar');
    	            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('u')
                ->from('DefaultDb_Entities_User', 'u')
                ->where('u.role=8');
            $query=$em->createQuery($dql);
            $users = $query->getResult();

            $arrUserFranchisee = array();
            foreach($users as $uFranshisee){
                $arrUserFranchisee[] = array(
                    "id" => $uFranshisee->getId(),
                    "firstName" => $uFranshisee->getFirstName(),
                    "role" => $uFranshisee->getType());
            }
    		            
            echo json_encode(array($arrUserFranchisee));
        }  catch (Exception $ex){
            $params = array();
            $this->logAndResolveException($ex,$params);
        }
    }
    
    //Obtiene las zonas
    function getZonaAction(){
        try{
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'editar');
            $em = $this->getEntityManager('DefaultDb');
            $dql = $em->createQueryBuilder();
            $dql->select('p')->from('DefaultDb_Entities_Zone', 'p')->orderBy("p.name") ;
            
            $query = $em->createQuery($dql);
            $zonas = $query->getResult();
            $arrZonas = array();
            
            foreach ($zonas as $zona){
                $arrZonas[] = array(
                    "id"=>$zona->getId(),
                    "name"=>$zona->getName()
                );
            }
            echo json_encode($arrZonas);
        } catch (Exception $ex) {
            $params = array();
            $this->logAndResolveException($ex, $params);
        }
    }
    
     //Obtiene las zonas por controlador
    function getZonaByControllerAction(){
        try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            $conn = $this->getConnection();
            $parameters = array($this->currentUserId);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_get_zone_by_user', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('userName');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    function getZonaByUserAction(){
        try {
            $this->hasPermission($this->getUserSessionId(), 'usuarios', 'listar');
            $conn = $this->getConnection();
            $parameters = $_SESSION['__M3']['MasDistribucion']['Credentials']['role'] == self::ROL_ADMIN_CODE ? array(0) : array($this->currentUserId);
 
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_get_zone_by_user', $parameters, PDO::FETCH_NAMED);
            unset($conn);
           $arrZonas = array();
            
            foreach ($resultsets[0] as $zona){
                $arrZonas[] = array(
                    "id"=>$zona['id'],
                    "name"=>$zona['name']
                );
            }
            echo json_encode($arrZonas);

        } catch (Exception $ex) {
            $params = compact('userName');
            $this->logAndResolveException($ex,$params);
        }
    }
    
    function getUserRoleAction(){
        echo json_encode(Model3_Auth::getCredentials('role'));
    }    function getMaxpointAction(){    	try{    		$em = $this->getEntityManager('DefaultDb');        		$query = $em->createQuery('SELECT MAX(m.id) FROM DefaultDb_Entities_Point m');    		$maxid = $query->getResult();    		echo json_encode($maxid);    	} catch (Exception $ex) {    		$params = array();    		$this->logAndResolveException($ex, $params);    	}    }        
}
