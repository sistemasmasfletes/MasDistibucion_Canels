<?php

use com\masfletes\db\DBUtil;

/**
 * El propósito del controlador es realizar operaciones CRUD con el modelo vehículos
 *
 * @author Andrés Hdz
 */
class OperationController_VehiclesController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth())
            $this->createResponse401();
    }

    public function indexAction() {}

    public function getVehiclesAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = $this->getArrayValue('id', $params);

        if (!$sidx)
            $sidx = 1;

        try {            
            $this->hasPermission($this->getUserSessionId(), 'vehiculos', 'listar');
            $conn = $this->getConnection();

            $parameters = array($this->getUserSessionId(), $page, $limit, $sidx, $sord, $id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_vehicles_listar', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('page','limit','sidx','sord','id');           
            $this->logAndResolveException($ex,$params);
        }        
    }

    public function saveAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        $name = $this->getArrayValue('name', $params);
        $type = $this->getArrayValue('type', $params);
        $volume = $this->getArrayValue('volume', $params);
        $economic_number = $this->getArrayValue('economic_number', $params);
        $capacity = $this->getArrayValue('capacity', $params);
        $status = $this->getArrayValue('status', $params);
        $trade_mark = $this->getArrayValue('trade_mark', $params);
        $plate = $this->getArrayValue('plate', $params);
        $color = $this->getArrayValue('color', $params);
        $gps = $this->getArrayValue('gps', $params);
        $model = $this->getArrayValue('model', $params);
        $width = $this->getArrayValue('width', $params);
        $height = $this->getArrayValue('height', $params);
        $deep = $this->getArrayValue('deep', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'vehiculos', ($id == null) ? 'agregar' : 'editar');

            $conn = $this->getConnection();
            $parameters = array($id, $type, $volume, $economic_number, $name, $capacity, $status, $trade_mark, $plate, $color, $gps, $model, $width, $height, $deep, Model3_Auth::getCredentials("id"));
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_vehicles_guardar', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('id','name','type','volume','economic_number','capacity','status','trade_mark','plate','color'
                ,'gps','model','width','height','deep');
            $this->logAndResolveException($ex,$params);
        }
        
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $params['id'];
        if ($id == null)
            return;
        try {
            $this->hasPermission($this->getUserSessionId(), 'vehiculos', 'eliminar');

            $conn = $this->getConnection();
            $parameters = array($id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_vehicles_borrar', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex,$params);
        }        
    }
    
    function getVehicleCatalogAction(){
        $params = $this->getRequest()->getPostJson();
        $vehicleName = $this->getArrayValue('param1', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'vehiculos', 'listar');
            $conn = $this->getConnection();
            $parameters = array($this->getUserSessionId(),$vehicleName);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_vehicleCatalog_list', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);

        } catch (PDOException $ex) {
            $params = compact('vehicleName');
            $this->logAndResolveException($ex,$params);
        }        
    }

}
