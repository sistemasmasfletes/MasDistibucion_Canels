<?php

use com\masfletes\db\DBUtil;

class OperationController_EstadosController extends JController {

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->createResponse401();
        }
    }

    public function indexAction() {
        
    }

    public function getEstadosAction() {
        $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $limit = $this->getArrayValue('rowsPerPage', $params);
        $sidx = $this->getArrayValue('sortField', $params);
        $sord = $this->getArrayValue('sortDir', $params);
        $id = (int) $this->getArrayValue('id', $params);
        $filter = $this->getArrayValue('filter', $params);
        $estadoName = $this->getArrayValue('name', $filter);
        $paisName = $this->getArrayValue('country', $filter);
        $abbreviation = $this->getArrayValue('abbreviation', $filter);

        if (!$sidx) {
            $sidx = 1;
        }

        try {
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'listar');

            $em = $this->getEntityManager('DefaultDb');
            $estadosRepo = $em->getRepository('DefaultDb_Entities_State');
            $resultSets = $estadosRepo->getEstados($page, $limit, $sidx, $sord, $id, $estadoName, $paisName, $abbreviation, $this->currentUserId);

            echo json_encode($resultSets);
        } catch (Exception $ex) {
            $params = compact('page', 'limit', 'sidx', 'sord', 'id');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function getEstadoByIdAction() {
        $params = $this->getRequest()->getPostJson();
        $id = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'Listar');

            $em = $this->getEntityManager('DefaultDb');
            $estadosIdRepo = $em->getRepository('DefaultDb_Entities_State');
            $estadoId = $estadosIdRepo->getEstadoById($id);

            echo json_encode($estadoId);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function verificarAction() {
        $SI_EXISTE = 1;
        $NO_EXISTE = 0;

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        $result = array();

        try {
            $id = (isset($params['estado']['id'])) ? $params['estado']['id'] : NULL;
            $countryId = (isset($params['estado']['countryId']['id'])) ? $params['estado']['countryId']['id'] : NULL;
            $name = $params['estado']['name'];
            $estatus = $em->getRepository('DefaultDb_Entities_State')->findOneBy(array('name' => $name, 'country' => $countryId));
            $result['existe'] = ($estatus === null) ? $NO_EXISTE : $SI_EXISTE;
        } catch (Exception $ex) {
            $result["error"] = $this->logAndResolveException($ex, $params);
        }

        echo json_encode($result);
    }

    public function saveAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $abbreviation = $this->getArrayValue('abbreviation', $params);
        $countryId = $this->getArrayValue('countryId', $params);
        $name = $this->getArrayValue('name', $params);
        $chrEstatus = $this->getArrayValue('chrEstatus', $params);

        try {
            $em = $this->getEntityManager('DefaultDb');
            $estadosRepo = $em->getRepository('DefaultDb_Entities_State');

            $estadosEntity = $estadosRepo->saveEstado($id, $abbreviation, $countryId, $name,$chrEstatus);

            echo json_encode($estadosEntity);
        } catch (Exception $ex) {
            $params = compact('id', 'abbreviation', 'countryId', 'name');
            $this->logAndResolveException($ex, $params);
        }
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);

        try {
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'Eliminar');
            $em = $this->getEntityManager('DefaultDb');
            $estadoRepo = $em->getRepository('DefaultDb_Entities_State');
            $estado = $estadoRepo->delete($id);

            echo json_encode($estado);
        } catch (Exception $ex) {
            $params = compact('id');
            $this->logAndResolveException($ex, $params);
        }
    }
    
    public function getEstadosByCountryIdAction(){
        $params = $this->getRequest()->getPost();
        $countryId = $this->getArrayValue('countryId', $params);
        $ACTIVO = 1;
 
        try{
            $this->hasPermission($this->getUserSessionId(), 'Estados', 'Listar');
            $em = $this->getEntityManager('DefaultDb');
            $estadoRepo = $em->getRepository('DefaultDb_Entities_State');
            $estados = $estadoRepo->findBy(array('country' => $countryId, 'estatus' => $ACTIVO));
            $respuesta = array();
            foreach($estados as $estado){
                $respuesta[] = array('id' => $estado->getId(),
                    'name' => $estado->getName() );
            }
            echo json_encode($respuesta);
        } catch (Exception $ex) {
            $params = compact('countryId');
            $this->logAndResolveException($ex, $params);
        }
    }

}
