<?php
use com\masfletes\db\DBUtil;

class OperationController_StatesController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->createResponse401();
        }        
    }
    
    public function getStatesAction(){
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);
        
        try {
            $this->hasPermission($this->getUserSessionId(), 'rutas', 'listar');
            $conn = $this->getConnection();
            $parameters = array($id);
            $resultsets = DBUtil::executeStoredProc($conn, 'sp_stateCatalog_list', $parameters, PDO::FETCH_NAMED);
            unset($conn);

            echo json_encode($resultsets);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex,$params);
        }        
    }

}