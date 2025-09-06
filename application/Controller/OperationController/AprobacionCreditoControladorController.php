<?php

use com\masfletes\db\DBUtil;

class OperationController_AprobacionCreditoControladorController extends JController {

    private $userSessionId;

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function getAprobacionCreditoControladorControllerAction() {
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
        $path = $this->path = "MAS_FLETES\public\Documents\PDF";
        $moneda = $this->getArrayValue('moneda', $filter);
        $name = $this->getArrayValue('name', $filter);
        $referencia = $this->getArrayValue('referencia', $filter);
        $cuenta = $this->getArrayValue('cuenta', $filter);
        $estado = $this->estado = "Hold off";
        //$estado = $this->getArrayValue('estado', $filter);
        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['username'];



        $em = $this->getEntityManager('DefaultDb');
        $AprobacionCreditoControladorCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $status = null;

        $AprobacionCreditoControladorCreditos = $AprobacionCreditoControladorCreditosRepo->getAprobacionCreditoControladorListDQL($page, $rowsPerPage, $sortField, $sortDir, $id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta, $estado, $client);

        echo json_encode($AprobacionCreditoControladorCreditos);
    }

    public function saveAction() {
        $em = $this->getEntityManager('DefaultDb');
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);
        $usuario = $this->getArrayValue('usuario', $params);
         $filter = $this->getArrayValue('filter', $params);
//        print_r($usuario);
        $tipoPago = $this->getArrayValue('tipoPago', $params);
        $montoCompra = $this->getArrayValue('montoCompra', $params);
        $moneda = $this->getArrayValue('moneda', $params);
        $creditos = $this->getArrayValue('creditos', $params);
        $fecha = $this->getArrayValue('fecha', $params);
        $nombreImg = $this->getArrayValue('nombreImg', $params);
        $path = $this->path = "MAS_FLETES/public/Documents/PDF/".$nombreImg;
        $name = $this->getArrayValue('name', $params);
        $referencia = $this->getArrayValue('referencia', $params);
        $cuenta = $this->getArrayValue('cuenta', $params);
        //$estado = $this->estado = "Hold off";
       $estado = $this->getArrayValue('estado', $filter);
        $client = $this->client = $_SESSION['__M3']['MasDistribucion']['Credentials']['username'];
        $page = 1;
        $rowsPerPage = 10;
        $sortField = '';
        $sortDir = '';


        $AprobacionCreditoControladorCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $AprobacionCreditoControladorCreditosRepo->addAprobacionCreditoControlador($id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta, $client);
        
        $AprobacionCreditoControladorCreditos = $AprobacionCreditoControladorCreditosRepo->getAprobacionCreditoControladorListDQL($page, $rowsPerPage, $sortField, $sortDir, $id, $usuario, $tipoPago, $montoCompra, $creditos, $fecha, $nombreImg, $path, $moneda, $name, $referencia, $cuenta,  $client);

        echo json_encode($AprobacionCreditoControladorCreditos);
    }

    public function monedasAction() {

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Conversion')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'moneda' => $q->getMoneda());
            $datos = $result[$x];
            $x++;
        }echo '{"monedas": ' . json_encode($result) . '}';
    }

    public function bancosAction() {

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Cuentas')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'name' => $q->getName());
            $datos = $result[$x];
            $x++;
        }echo '{"bancos": ' . json_encode($result) . '}';
    }
   public function categoriasAction() {
       $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Category')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'name' => $q->getName());
            $datos = $result[$x];
            $x++;
        }echo '{"categorias": ' . json_encode($result) . '}';
   }
    

    public function usuariosAction() {

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_User')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'commercialName' => $q->getCommercialName());
            $datos = $result[$x];
            $x++;
        }echo '{"usuarios": ' . json_encode($result) . '}';
    }

    public function cuentaBancoAction() {
        
       
        $idBanco = $_POST["idBanco"];

        
        $query = $this->getEntityManager('DefaultDb')
                        ->createQuery(
                                "SELECT c.id, c.numeroCuenta, c.name, c.clabeInterbancaria FROM DefaultDb_Entities_Cuentas c WHERE c.estado='Activo' AND c.id = :idBanco"
                        );
        $query->setParameter("idBanco", $idBanco);
             
        $resultado = $query->getResult();
        
        if( count( $resultado ) > 0 ) {
            echo json_encode($resultado[0]);
        } else {
            echo '{"id": "0"}';
        }
        
    }

    public function cuentasBancosEntAction() {
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Cuentas')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'numeroCuenta' => $q->getNumeroCuenta(), 'clabeInterbancaria'=>getClabeInterbancaria());
            $datos = $result[$x];
            $x++;
        }echo '{"cuentasBancosEnt": ' . json_encode($result) . '}';
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
    public function tipoPagosAction() {

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_TipoPagos')->createQueryBuilder('m')->select('m')->getQuery()->getResult();
        $x = 0;
        foreach ($query as $q) {
            $result[] = array('id' => $q->getId(), 'tipoPago' => $q->getTipoPago());
            $datos = $result[$x];
            $x++;
        }echo '{"tipoPagos": ' . json_encode($result) . '}';
    }
    public function aprobacionAction(){
        $query = $this->getEntityManager('DefaultDb')
                        ->createQuery(
                                "SELECT c.id, c.fecha,  c.usuario, c.tipoPago, c.montoCompra, c.moneda,  c.name,  c.cuenta, c.creditos, c.nombreImg, c.estado FROM DefaultDb_Entities_CompraCreditos c"
                        );
         $resultado = $query->getResult();
         echo  json_encode($resultado);
         
    }
    
  
    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);

        $em = $this->getEntityManager('DefaultDb');
        $AprobacionCreditoControladorRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

        $AprobacionCreditoControladorRepo->deleteAprobacionCreditoControlador($id);

        $AprobacionCreditoControladorCreditos = $AprobacionCreditoControladorRepo->getAprobacionCreditoControladorListDQL($page, $rowsPerPage, $sortField, $sortDir, $id);

        echo json_encode($AprobacionCreditoControladorCreditos);
    }

}
