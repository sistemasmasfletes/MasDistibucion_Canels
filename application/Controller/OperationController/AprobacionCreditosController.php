<?php

use com\masfletes\db\DBUtil;

class OperationController_AprobacionCreditosController extends JController {

    private $userSessionId;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getAprobacionCreditosAction() {
       $params = $this->getRequest()->getPostJson();

        $page = $this->getArrayValue('page', $params);
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        $sortField = $this->getArrayValue('sortField', $params);
        $sortDir = $this->getArrayValue('sortDir', $params);
        $filter = $this->getArrayValue('filter', $params);
        $id = $this->getArrayValue('id', $filter);

        $usuario = $this->getArrayValue('usuario', $filter);
        $tipoPago = $this->getArrayValue('tipoPago', $filter);
        $montoCompra = $this->getArrayValue('montoCompra', $filter);
        $creditos = $this->getArrayValue('creditos', $params);
        $fecha = $this->getArrayValue('fecha', $filter);
        $nombreImg = $this->getArrayValue('nombreImg', $filter);
        $path = $this->getArrayValue('path', $filter);
        // $path = $this->path="MAS_FLETES\public\Documents\PDF";
        $moneda = $this->getArrayValue('moneda', $filter);
        $name = $this->getArrayValue('name', $filter);
        $referencia = $this->getArrayValue('referencia', $filter);
        $cuenta = $this->getArrayValue('cuenta', $filter);
       // $estado = $this->estado = "HOLD OFF";
        $estado = $this->getArrayValue('estado', $filter);
        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['username'];
        $descripcion = $this->descripcion = "Compra  de Creditos";



        $em = $this->getEntityManager('DefaultDb');
        $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $status = null;

        $CompraCreditos = $compraCreditosRepo->getCompraCreditosListDQL($page, $rowsPerPage, $sortField, $sortDir, $id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta, $estado, $client, $descripcion);

        echo json_encode($CompraCreditos);
    }

    public function saveAction() {
          $em = $this->getEntityManager('DefaultDb');
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $usuario = $this->getArrayValue('usuario', $params);
        print_r($params);
        $tipoPago = $this->getArrayValue('tipoPago', $params);
        $montoCompra = $this->getArrayValue('montoCompra', $params);
        $moneda = $this->getArrayValue('moneda', $params);
        $creditos = $this->getArrayValue('creditos', $params);
        $fecha = $this->getArrayValue('fecha', $params);
        $nombreImg = $this->getArrayValue('nombreImg', $params);
        $path = $this->getArrayValue('path', $params);
        //$path = $this->path="MAS_FLETES\public\Documents\PDF";
        //$path = 'MAS_FLETES\public\Documents\PDF\\'.$this->getArrayValue('path',$params);
        $name = $this->getArrayValue('name', $params);
        $referencia = $this->getArrayValue('referencia', $params);
        $cuenta = $this->getArrayValue('cuenta', $params);
       // $estado = $this->estado = "Hold off";
         $estado = $this->getArrayValue('estado', $params);
        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['username'];
        $descripcion = $this->descripcion = "Compra  de Creditos";
        $page = 1;
        $rowsPerPage = 10;
        $sortField = '';
        $sortDir = '';


        $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $compraCreditosRepo->addCompraCreditos($id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta, $estado, $client, $descripcion);

        $CompraCreditos = $compraCreditosRepo->getCompraCreditosListDQL($page, $rowsPerPage, $sortField, $sortDir, $id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta, $estado, $client, $descripcion);

        echo json_encode($CompraCreditos);
    }

    public function estatusAction() {
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Estatus')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'estatu' => $q->getEstatus());
            $datos = $result[$x];
            $x++;
        }echo '{"estatus": ' . json_encode($result) . '}';
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);

        $em = $this->getEntityManager('DefaultDb');
        $aprobacionCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $aprobacionCreditosRepo->deleteAprobacionCreditos($id);

        $aproCreditos = $aprobacionCreditosRepo->getAprobacionCreditosListDQL($page,$rowsPerPage,$sortField,$sortDir,$id);

        echo json_encode($aproCreditos);
    }

}
