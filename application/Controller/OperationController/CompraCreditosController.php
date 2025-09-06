<?php

use com\masfletes\db\DBUtil;

class OperationController_CompraCreditosController extends JController {

    private $userSessionId;
    private $envio;
    private $opcion = 0;

    public function init() {

        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = Model3_Auth::getCredentials('id');
        $this->verificarEnvio();
    }

    public function indexAction() {
        
    }

    /* Metodo que verifica si se creo la variable 
      de envio en el embalaje de productos */

    public function verificarEnvio() {
        if (!isset($_SESSION['opcion'])) {
            $this->envio = FALSE;
        } else {
            switch ($_SESSION['opcion']) {
                case 1:
                    $this->envio = TRUE;
                    break;
                case 2:
                    $this->envio = TRUE;
                    break;
            }
        }
    }

    public function recibiParametrosAction() {
        $total = $_POST['total'];
        $creditos = $_POST['creditos'];
        if ($creditos == 0) {
            $_SESSION['opcion'] = 1;
        } else if ($creditos < $total) {
            $_SESSION['opcion'] = 2;
        }
    }

    public function anularAction() {
        $_SESSION['opcion'] = NULL;
    }

    public function getCompraCreditosAction() {
        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');
            $CompraCreditos = $compraCreditosRepo->getCompraCreditosListDQL($this->getParametros($params));
        } catch (Exception $exc) {
            $CompraCreditos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($CompraCreditos);
    }

    private function getParametros($params) {
        $parametros = array();
        $parametros["pagina"] = $this->getPageFromParams($params);
        $parametros["registrosPorPagina"] = $this->getRowsPerPageFromParams($params);
        $parametros["ordenarTipo"] = $this->getOrdenarTipo($params);
        $parametros["ordenarCampo"] = $this->getOrdenarCampo($params);
        $parametros["filtro"] = $this->getFiltro($params);
        return $parametros;
    }

    private function getPageFromParams($params) {
        $page = $this->getArrayValue('page', $params);
        if (!$page) {
            $page = 1;
        }

        return $page;
    }

    private function getRowsPerPageFromParams($params) {
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        if (!$rowsPerPage) {
            $rowsPerPage = 10;
        }

        return $rowsPerPage;
    }

    private function getOrdenarCampo($params) {
        $ordenarCampo = $this->getArrayValue('sortField', $params);
        if (!$ordenarCampo) {
            $ordenarCampo = "m.fecha";
        } else {
            switch ($ordenarCampo) {
                case "tipoPago":
                    $ordenarCampo = "t.tipoPago";
                    break;
                case "moneda":
                    $ordenarCampo = "b.moneda";
                    break;
                case "banco":
                    $ordenarCampo = "d.name";
                    break;
                case "cuenta":
                    $ordenarCampo = "a.cuenta";
                    break;
                case "estatus":
                    $ordenarCampo = "e.estatus";
                    break;
                default:
                    $ordenarCampo = "m." . $ordenarCampo;
                    break;
            }
        }
        return $ordenarCampo;
    }

    private function getOrdenarTipo($params) {
        $ordenarTipo = $this->getArrayValue('sortDir', $params);
        if (!$ordenarTipo) {
            $ordenarTipo = "asc";
        }
        return $ordenarTipo;
    }

    private function getFiltro($params) {
        $filtro = $this->getArrayValue('filtro', $params);
        if (!$filtro) {
            $filtro = null;
        }
        return $filtro;
    }

    public function saveAction() {
        if ($this->envio == TRUE) {
            $this->anularAction();
        }

        $em = $this->getEntityManager('DefaultDb');
        $params = $this->getRequest()->getPostJson();

        $compraJSON = array();
        try {
            $compraJSON["id"] = $this->getArrayValue('id', $params);
            $compraJSON["usuario"] = $this->userSessionId = Model3_Auth::getCredentials('id');
            $compraJSON["tipoPago"] = $this->getArrayValue('tipoPago', $params);
            $compraJSON["montoCompra"] = $this->getArrayValue('montoCompra', $params);
            $compraJSON["fecha"] = new DateTime( );
            $compraJSON["referencia"] = $this->getArrayValue('referencia', $params);
            $compraJSON["cuenta"] = $this->getArrayValue('cuenta', $params);
            $compraJSON["estatus"] = $this->getArrayValue('estatus', $params);
            $compraJSON["cliente"] = $this->getArrayValue('cliente', $params);
            $compraJSON["comentario"] = $this->getArrayValue('comentario', $params);
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');

            $idCompra = $compraCreditosRepo->addCompraCreditos($compraJSON);
            $CompraCreditos = $compraCreditosRepo->getCompraCreditosListDQL($this->getParametros($params));
            $CompraCreditos[1]["idCompra"] = $idCompra;
        } catch (Exception $exc) {
            $CompraCreditos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($CompraCreditos);
    }

    public function fncBancoMasDistribucionAction() {
        $BANCO_MASDISTRIBUCION = "Más Distribución";
        $em = $this->getEntityManager("DefaultDb");
        $params = array();
        try {
            $this->fncBuscaBancoMasDistribucion();
            $query = $em->getRepository('DefaultDb_Entities_Bancos')
                            ->createQueryBuilder('m')
                            ->select('m')
                            ->where("m.name = :name")
                            ->setParameter("name", $BANCO_MASDISTRIBUCION)
                            ->getQuery()->getResult();
            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(),
                    'name' => $q->getName()
                );
            }
            $response = array();
            $response["bancos"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    private function fncBuscaBancoMasDistribucion() {
        $BANCO_MASDISTRIBUCION = "Más Distribución";
        $em = $this->getEntityManager("DefaultDb");
        $params = array();
        $banco = $em->getRepository('DefaultDb_Entities_Bancos')->findOneBy(array('name' => $BANCO_MASDISTRIBUCION));
        try {
            if ($banco === NULL) {
                $bancosRepo = $em->getRepository('DefaultDb_Entities_Bancos');
                $id = NULL;
                $name = $BANCO_MASDISTRIBUCION;
                $estado = 'Activo';
                $client = $this->fncGetUsuario()->getId();
                $banco = $bancosRepo->addBancos($id, $name, $estado, $client);
            }
        } catch (Exception $exc) {
            $banco["error"] = $this->logAndResolveException($exc, $params);
        }
    }

//    public function bancosSQLAction() {
//
//        $em = $this->getEntityManager('DefaultDb');
//        $q = $em->createQuery("select u.id, u.name from DefaultDb_Entities_Cuentas u WHERE u.estado='Activo' ");
//        $users = $q->getResult();
//          echo '{"bancos": ' . json_encode($users) . '}';
//    }

    /*
     * combo categorias
     */
    public function categoriasAction() {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $result = array();
        try {
            $query = $em->getRepository('DefaultDb_Entities_Category')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->getQuery()
                    ->getResult();
            foreach ($query as $q) {
                $result[] = array
                    (
                    'id' => $q->getId(),
                    'nombre' => $q->getName(),
                );
            }
            $response = array("categorias" => $result);
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    /*
     * combo bancos
     */

    public function bancosAction() {
        
        
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        try {
            $query = $em->getRepository('DefaultDb_Entities_Bancos')
                            ->createQueryBuilder('m')
                            ->select('m')
                            ->where("m.estado = 'Activo'")
                            ->getQuery()->getResult();

            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(),
                    'name' => $q->getName()
                );
            }
            $response["bancos"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function bancosEnCuentaAction() {
        $em = $this->getEntityManager('DefaultDb');
        $conn = $em->getConnection();
        $params = array();
        $response = array();
        try {
            $query = 'select distinct intIDBanco, tblbancos.chrName from tblcuentas left join tblbancos on tblbancos.id = tblcuentas.intIDBanco; ';
            $res = $conn->executeQuery($query);
            $res = $res->fetchAll();

            foreach ($res as $q) {
                $result[] = array(
                    'id' => $q['intIDBanco'],
                    'name' => $q['chrName']
                );
            }
            $response["bancos"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    /*
     * combo monedas
     */

    public function monedasAction() {
        $params = $this->getRequest()->getPostJson();
        $bancoId = $this->getArrayValue('banco', $params);

        $em = $this->getEntityManager('DefaultDb');
        $conn = $em->getConnection();
        $params = array();
        $response = array();
        try {
            if (!is_null($bancoId)) {
                $query = 'select distinct intIDTipoMoneda, chrMoneda, chrCurrencyCode, '
                        . '(select chrCompra from tblconversion where intIDMoneda = intIDTipoMoneda limit 1) as chrCompra, '
                        . ' (select chrCreditos from tblconversion where intIDMoneda = intIDTipoMoneda limit 1) as chrCreditos '
                        . 'from tblcuentas left join tbltipomonedas on tbltipomonedas.id = intIDTipoMoneda ; ';
                $res = $conn->executeQuery($query);
                $res = $res->fetchAll();

                foreach ($res as $q) {
                    $result[] = array(
                        'id' => $q['intIDTipoMoneda'],
                        'moneda' => $q['chrMoneda'],
                        'currencyCode' => ($q['chrCurrencyCode']) ? $q['chrCurrencyCode'] : "MXN",
                        'compra' => ($q['chrCompra']) ? $q['chrCompra'] : "0.0",
                        'creditos' => ($q['chrCreditos']) ? $q['chrCreditos'] : "0.0"
                    );
                }
            } else {
                $result = array();
            }

            $response["monedas"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    /*
     * combo tipo de pagos
     */

    public function tipoPagosAction() {

        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $ROLE_CHOFER = 2;
        $PAGO_TERMINAL = 4;
        try {
            //si esta pagando por el envio del paquete solo puede pagar con pago por internet
            //o con pago en sitio
            if ($this->envio == true) {
                $query = $em->getRepository('DefaultDb_Entities_TipoPagos')
                        ->createQueryBuilder('m')
                        ->select('m')
                        ->where("m.id != :id and m.id != :id2")
                        ->setParameter("id", 1)
                        ->setParameter("id2", $PAGO_TERMINAL)
                        ->getQuery()
                        ->getResult();
            } else {
                //sino puede ver todos los pagos excepto el de pago con terminal
                $query = $em->getRepository('DefaultDb_Entities_TipoPagos')
                        ->createQueryBuilder('m')
                        ->select('m')
                        ->where("m.id != :id")
                        ->setParameter("id", $PAGO_TERMINAL)
                        ->getQuery()
                        ->getResult();
            }

            //si el usuario es chofer puede ver todos los metodos de pago
            if ($this->fncGetUsuario()->getRole()->getId() == $ROLE_CHOFER) {
                $query = $em->getRepository('DefaultDb_Entities_TipoPagos')
                        ->createQueryBuilder('m')
                        ->select('m')
                        ->getQuery()
                        ->getResult();
            }

            foreach ($query as $q) {
                $result[] = array
                    (
                    'id' => $q->getId(),
                    'tipoPago' => $q->getTipoPago()
                );
            }
            $response["tipoPagos"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function usuariosAction() {
        $parameters = json_decode(file_get_contents("php://input"));
        $idCategoria = $parameters->idCategoria;
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        try {
            $categoria = $em->getRepository('DefaultDb_Entities_Category')
                    ->findById($idCategoria);

            $query = $em->getRepository('DefaultDb_Entities_User')
                    ->createQueryBuilder('m')
                    ->where("m.category = :categoria")
                    ->setParameter("categoria", $categoria)
                    ->getQuery()
                    ->getResult();

            $result = array();
            foreach ($query as $q) {

                $result[] = array(
                    'id' => $q->getId(),
                    'code' => $q->getCode(),
                    'commercialName' => $q->getCommercialName(),
                    'firstName' => $q->getFirstName(),
                    'lastName' => $q->getLastName()
                );
            }
            $response = array("usuarios" => $result);
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function cuentaBancoAction() {

        $idBanco = $_POST["idBanco"];

        $query = $this->getEntityManager('DefaultDb')
                ->createQuery(
                "SELECT c.id, c.moneda, c.numeroCuenta, c.name, c.clabeInterbancaria FROM DefaultDb_Entities_Cuentas c WHERE c.estado='Activo' AND c.id = :idBanco"
        );
        $query->setParameter("idBanco", $idBanco);

        $resultado = $query->getResult();

        if (count($resultado) > 0) {
            echo json_encode($resultado[0]);
        } else {
            echo '{"id": "0"}';
        }
    }

    public function subirAction() {


        $target_dir = dirname('..\public\documents') . DIRECTORY_SEPARATOR . 'PDF' . DIRECTORY_SEPARATOR;
        
        $this->view->getJsManager()->addJsVar('PDFPATH',$target_dir);
        if (!file_exists($target_dir)) {
            mkdir($target_dir);
        }
        $file_name = uniqid() . basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $file_name;
        $idCompra = $_POST["idCompra"];
        $em = $this->getEntityManager('DefaultDb');
        $compra = $em->getRepository('DefaultDb_Entities_CompraCreditos')->find($idCompra);

        $respuesta = array();

        if (!$compra) {
            $respuesta["respuesta"] = "0";
        } else {

            $compra->setPath($file_name);
            $em->flush();


            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $respuesta["respuesta"] = "1";
            } else {
                $respuesta["respuesta"] = "0";
            }

            echo json_encode($respuesta);
        }
    }

    public function cuentasAction() {

        $params = $this->getRequest()->getPostJson();
        $idBanco = $this->getArrayValue('idBanco', $params);
        $idMoneda = $this->getArrayValue('idMoneda', $params);
        $idTipoPago = $this->getArrayValue('idTipoPago', $params);

        $em = $this->getEntityManager('DefaultDb');
        $conn = $em->getConnection();
        $params = array();
        $response = array();
        try {
            $query = "select id, chrNumeroCuenta ,chrCuenta,intIdTipoMoneda,intIDBanco,intIDTipoPago from tblcuentas  where  chrEstado like 'Activo' AND (intIDTipoPago = '$idTipoPago' OR '$idTipoPago' = 0);";
            $res = $conn->executeQuery($query);
            $res = $res->fetchAll();

            $result = array();
            foreach ($res as $q) {
                $result[] = array(
                    'id' => $q['id'],
                    'numeroCuenta' => $q['chrNumeroCuenta'],
                    'cuenta' => $q['chrCuenta']." ".$q['chrNumeroCuenta'],
                    'intIdTipoMoneda' => $q['intIdTipoMoneda'],
                    'intIDBanco' => $q['intIDBanco'],
                    'intIDTipoPago' => $q['intIDTipoPago']
                        
                );
            }

            $response = array("cuentas" => $result);
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function fncCuentaMasDistribucionAction() {
        $parameters = json_decode(file_get_contents("php://input"));
        $idBanco = $parameters->idBanco;
        $idMoneda = $parameters->idMoneda;
        $em = $this->getEntityManager('DefaultDb');
        $CUENTA_MASDISTRIBUCION = "Más Distribución";
        $params = array();
        $response = array();
        try {
            $banco = $em->getRepository('DefaultDb_Entities_Bancos')
                    ->findById($idBanco);
            $moneda = $em->getRepository('DefaultDb_Entities_TipoMonedas')
                    ->findById($idMoneda);
            $query = $em->getRepository('DefaultDb_Entities_Cuentas')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->where("m.cuenta = :cuenta AND m.banco = :banco AND m.moneda = :moneda AND m.estado='Activo'")
                    ->setParameter("banco", $banco)
                    ->setParameter("moneda", $moneda)
                    ->setParameter("cuenta", $CUENTA_MASDISTRIBUCION)
                    ->getQuery()
                    ->getResult();
            $result = array();
            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(),
                    'cuenta' => ($q->getCuenta()." ".$q->getNumeroCuenta())
                );
            }
            $response["cuentas"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function estatusAction() {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        try {
            $query = $em->getRepository('DefaultDb_Entities_Estatus')
                    ->createQueryBuilder('m')
                    ->select('m')
                    ->getQuery()
                    ->getResult();

            $result = array();
            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(),
                    'estatu' => $q->getEstatus()
                );
            }

            $response["estatus"] = $result;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    private function fncGetUsuario() {
        $params = array();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
            $usuario = $em->getRepository('DefaultDb_Entities_User')->find($userSessionId);
        } catch (Exception $exc) {
            $usuario["error"] = $this->logAndResolveException($exc, $params);
        }
        return $usuario;
    }

    public function getLogClientesAction() {
        $query = $this->getEntityManager('DefaultDb')
                ->createQuery(
                "SELECT c.id, c.fecha, c.referencia, c.name, c.descripcion, c.creditos, c.tipoPago,  c.montoCompra FROM DefaultDb_Entities_CompraCreditos c"
        );

        $resultado = $query->getResult();

        if (count($resultado) > 0) {
            echo json_encode($resultado[0]);
        } else {
            echo '{"id": "0"}';
        }
    }

    public function deleteAction() {
        $params = $this->getRequest()->getPostJson();

        $id = $this->getArrayValue('id', $params);

        $em = $this->getEntityManager('DefaultDb');
        try {
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');
            $compraCreditosRepo->deleteCompraCreditos($id);
            $CompraCreditos = $compraCreditosRepo->getCompraCreditosListDQL($page, $rowsPerPage, $sortField, $sortDir, $id);
        } catch (Exception $exc) {
            $CompraCreditos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($CompraCreditos);
    }

    /*
     * obtiene los datos del usuario logueado, 
     * para hablilitar las funciones o campos que puede utilizar cada rol
     */

    public function obtenerConfiguracionAction() {
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response = array();
        try {
            $user = $em->find('DefaultDb_Entities_User', $this->userSessionId);
            $categoria = $user->getCategory();
            $response["role"] = Model3_Auth::getCredentials('role');
            $response["idUser"] = $user->getId();
            if ($categoria != null) {
                $response["idCategoria"] = $categoria->getId();
            }
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    public function fncExportarAction() {

        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');
            $compraCreditos = $compraCreditosRepo->fncGetListExport($this->getParametros($params));
        } catch (Exception $exc) {
            $compraCreditos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($compraCreditos);
    }

    public function fncPayPalAction() {
        $em = $this->getEntityManager('DefaultDb');
        try {
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');
            $compraCreditosRepo->fncGuardarPaypalCompra($_SESSION["paypal"], $this->view->getBaseUrl());
        } catch (Exception $exc) {
            $this->logAndResolveException($exc, $params);
        }
    }

    public function fncTerminalAction() {
        $params = $this->getRequest()->getPost();
        try {
            $em = $this->getEntityManager('DefaultDb');
            $compraCreditosRepo = $em->getRepository('DefaultDb_Entities_CompraCreditos');
            $compraCreditos = $compraCreditosRepo->fncGuardarTerminalCompra($params);
        } catch (Exception $exc) {
            $compraCreditos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($compraCreditos);
    }

}
