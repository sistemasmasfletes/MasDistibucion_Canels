<?php

use com\masfletes\db\DBUtil;

class OperationController_PagosController extends JController {

    private $userSessionId;

    const MONEDA_MXN = 'MXN';

    public function init() {
        parent::init();
        if (!Model3_Auth::isAuth()) {
            $this->redirect('Index/index');
        }
        $this->userSessionId = $_SESSION['USERSESSIONID'];
    }

    public function indexAction() {
        
    }

    /**
     * Llama al metodo que obtiene el listado de pagos que se han realizado
     */
    public function getPagosAction() {

        $params = $this->getRequest()->getPostJson();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pagos = $pagosRepo->getPagosListDQL($this->getParametros($params));
        } catch (Exception $exc) {
            $pagos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($pagos);
    }

    /**
     * Obtiene los datos para el ordenamiento de tablas
     */
    private function getParametros($params) {
        $parametros = array();
        $parametros["pagina"] = $this->getPageFromParams($params);
        $parametros["registrosPorPagina"] = $this->getRowsPerPageFromParams($params);
        $parametros["ordenarTipo"] = $this->getOrdenarTipo($params);
        $parametros["ordenarCampo"] = $this->getOrdenarCampo($params);
        $parametros["filtro"] = $this->getFiltro($params);
        return $parametros;
    }

    /**
     * Obtiene el numero de pagina que se esta llamand, si no existe retorna 1
     */
    private function getPageFromParams($params) {
        $page = $this->getArrayValue('page', $params);
        if (!$page) {
            $page = 1;
        }

        return $page;
    }

    /**
     * Obtiene el numero de filas por pagina, si no existe retorna 10
     */
    private function getRowsPerPageFromParams($params) {
        $rowsPerPage = $this->getArrayValue('rowsPerPage', $params);
        if (!$rowsPerPage) {
            $rowsPerPage = 10;
        }

        return $rowsPerPage;
    }

    /**
     * Obtiene el tipo de ordenamiento, puede ser ascendente o descendente
     */
    private function getOrdenarTipo($params) {
        $ordenarTipo = $this->getArrayValue('sortDir', $params);
        if (!$ordenarTipo) {
            $ordenarTipo = "asc";
        }
        return $ordenarTipo;
    }

    /**
     * Obtiene el campo que se va a ordenar, ni no existe se ordenan por fecha
     * En el case se le agrega el alias del inner join
     */
    private function getOrdenarCampo($params) {

        $ordenarCampo = $this->getArrayValue('sortField', $params);
        if (!$ordenarCampo) {
            $ordenarCampo = "m.fecha";
        } else {
            switch ($ordenarCampo) {
                case "tipoConcepto":
                    $ordenarCampo = "a.tipoConcepto";
                    break;
                case "tipoDebito":
                    $ordenarCampo = "b.tipoDebito";
                    break;
                default:
                    $ordenarCampo = "m." . $ordenarCampo;
                    break;
            }
        }

        return $ordenarCampo;
    }

    private function getFiltro($params) {
        $filtro = $this->getArrayValue('filtro', $params);
        if (!$filtro) {
            $filtro = null;
        }
        return $filtro;
    }

    /**
     * Metodo para obtener los creditos del usuario 
     * si es compra retorna los creditos del usuario logueado
     * si es venta retorna los creditos del cliente logueado
     * Se llama desde el archivo viewEfectuarPago.js
     */
    public function getCreditosAction() {
        $post = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try {
            /* $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['orden']);
              $comprador = $em->getRepository('DefaultDb_Entities_User')->find($orden->getSeller()->getId());
              $vendedor = $em->getRepository('DefaultDb_Entities_User')->find($orden->getBuyer()->getId());
              $response["creditos"] = ($post['tipo']===FALSE) ? $comprador->getCredito() : $vendedor->getCredito(); */
            $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
            if(isset($post['routePointId'])){
            if ($post['routePointId'] > 0) {
                 $branchesRepos = $em->getRepository('DefaultDb_Entities_BranchesUser');
                $idRoutePoint = $post['routePointId'];
                if (isset($idRoutePoint)) {
                    $q = $em->getRepository('DefaultDb_Entities_RoutePoint')->createQueryBuilder('rp')->select('IDENTITY(rp.point)')->Where("rp.id='" . $idRoutePoint . "'")->getQuery()->getResult();
                    //$bu = $branchesRepos->findOneBy(array('point' => $q[0][1]), array('id' => 'desc'));
                    $bu = $branchesRepos->createQueryBuilder('bu')->select('IDENTITY(bu.client)')->Where("bu.client != '24' and bu.point = '".$q[0][1]."'")->getQuery()->getResult();                   

                    if ($bu) {
                        $idUsuario = $bu[0][1];
                        //$idUsuario = $bu->getClient()->getId();
                    }
                }
            }}
            $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);
            $response["creditos"] = $usuario->getCredito();
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($response);
    }

    /**
     * Metodo para obtener los datos de los productos que se van a comprar 
     * retorna un array para llenar la tabla 
     * Se llama desde el archivo viewEfectuarPago.js
     */
    public function getProductosAction() {
        $post = $this->getRequest()->getPost();
        $params = array();
        $em = $this->getEntityManager('DefaultDb');
        $result = array();
        try {
            $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['orden']);
            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');

            $productos = $orden->getProducts();

            $totalCreditos = 0;
            $totalMonedas = 0;

            foreach ($productos as $productoCompra) {
                $producto = $productoCompra->getProduct();

                if ($productoCompra->getVariant()) {
                    $productoNombre = $producto->getName() . ' - ' . $productoCompra->getVariant()->getDescription();
                } else {
                    $productoNombre = $producto->getName();
                }

                $subtotalCreditos = $producto->getPrice() * $productoCompra->getQuantity();
                $totalCreditos += $subtotalCreditos;

                $subtotalMonedas = $pagosRepo->fncCalculaCreditos($post['moneda'], $subtotalCreditos);
                $totalMonedas += $subtotalMonedas;

                $result[] = array(
                    'cantidad' => $productoCompra->getQuantity(),
                    'producto' => $productoNombre,
                    'sku' => $producto->getSku(),
                    'precioUnitario' => $producto->getPrice(),
                    'precioSubtotal' => $subtotalCreditos,
                    'precioMonedas' => $subtotalMonedas,
                    'totalCreditos' => $totalCreditos,
                    'totalMonedas' => $totalMonedas
                );
            }
        } catch (Exception $exc) {
            $result["error"] = $this->logAndResolveException($exc, $params);
        }

        echo json_encode($result);
    }

    /**
     * Metodo para recibir los parametros post de la vista y 
     * enviarlos al repositorio para guardarlos en la base
     * Se llama desde el archivo viewEfectuarPago.js
     */
    public function fncGuardarPagoAction() {
        $post = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        try {
            $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['orden']);
            $tipoConcepto = $this->fncTipoConcepto($orden);

            $pagosJSON = array();

            $pagosJSON["usuario"] = $tipoConcepto["usuario"];
            $pagosJSON["cliente"] = $tipoConcepto["cliente"];
            $pagosJSON["orden"] = $post["orden"];
            $pagosJSON["montoCompra"] = $post["montoMoneda"];
            $pagosJSON["montoCreditos"] = $post["monto"];
            $pagosJSON["fecha"] = new DateTime( );
            $pagosJSON["estatus"] = ($post["tipoPago"] == 2) ? 1 : 2;
            $pagosJSON["compraCreditos"] = null;
            $pagosJSON["compraVenta"] = $orden;
            $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
            $pagosJSON["tipoDebito"] = $this->fncObtenerTipoDebito($post["formaDebito"]);
            $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];

            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pago = $pagosRepo->fncAgregarPago($pagosJSON);
        } catch (Exception $exc) {
            $pago["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($pago);
    }

    /**
     * Obtiene el objeto de tipoDebito
     * depende del tipo de pago que son: Pago contra entrega, Pago al Momento 
     * y pago fuera de más distribución.
     * En pago contra entrega existen dos formas de debitar: congelar creditos y creditos negativos 
     * Son datos de la tabla tipo de debito
     */
    public function fncObtenerTipoDebito($formaDebito) {

        $CONGELAR_CREDITOS = 1;
        $NEGAR_CREDITOS = 2;
        $PAGO_MOMENTO = 3;
        $PAGO_FUERA = 4;

        $em = $this->getEntityManager('DefaultDb');
        if ($formaDebito == 0) {
            $tipo = $PAGO_MOMENTO;
        } else {
            $tipo = ($formaDebito == 1) ? $CONGELAR_CREDITOS : $NEGAR_CREDITOS;
        }
        if ($formaDebito == 4) {
            $tipo = $PAGO_FUERA;
        }

        $tipoDebito = $em->getRepository('DefaultDb_Entities_TipoDebito')->find($tipo);

        return $tipoDebito;
    }

    /**
     * Carga el combo de tipo de modnedas para el pago de productos
     * Se llama desde el archivo viewEfectuarPago.js
     */
    public function fncTipoMonedasAction() {
        $em = $this->getEntityManager('DefaultDb');
        try {
            $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')->findAll();

            foreach ($query as $q) {
                $result[] = array(
                    'id' => $q->getId(),
                    'moneda' => $q->getMoneda()
                );
            }
        } catch (Exception $exc) {
            $result["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($result);
    }

    /**
     * Metodo para verificar si existe un pago realizado para esa compra-venta 
     * Se llama desde el archivo viewOrder.js
     */
    public function fncExistePagoAction() {
        $post = $this->getRequest()->getPost();
        $params = array();
        $result = array();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['orden']);

            $tipoConcepto = $this->fncTipoConcepto($orden);

            $pago = $em->getRepository('DefaultDb_Entities_Pagos')
                            ->createQueryBuilder('m')
                            ->where("m.orden = :orden and m.tipoConcepto = :compra")
                            ->setParameters(array("orden" => $orden, "compra" => $tipoConcepto["compra"]))
                            ->setMaxResults(1)
                            ->getQuery()->getOneOrNullResult();

            if ($pago == NULL) {
                $result["id"] = null;
            } else {
                $result["id"] = $pago->getId();
                $result["tipoPago"] = $pago->getTipoDebito()->getTipoDebito();
            }
        } catch (Exception $exc) {
            $result["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($result);
    }

    /**
     * Metodo para verificar si es compra o es venta dependiendo del usuario logeado 
     * Se llama desde la funcion fncExistePagoAction linea 236
     */
    private function fncTipoConcepto($orden) {
        $VENTA = 3;
        $COMPRA = 4;

        $em = $this->getEntityManager('DefaultDb');
        $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);

        $cliente = ($usuario == $orden->getSeller()) ? $orden->getBuyer() : $orden->getSeller();

        $concepto = ($usuario == $orden->getSeller()) ? $VENTA : $COMPRA;
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($concepto);
        $compra = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($COMPRA);
        $venta = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($VENTA);

        $datos = array();
        $datos["usuario"] = $usuario;
        $datos["cliente"] = $cliente;
        $datos["tipoConcepto"] = $tipoConcepto;
        $datos["compra"] = $compra;
        $datos["venta"] = $venta;
        $datos["descripcion"] = ($concepto == $VENTA) ? "Venta a " . $cliente->getCommercialName() : "Compra a " . $cliente->getCommercialName();

        return $datos;
    }

    /**
     * Metodo para recibir los parametros post de la vista y 
     * enviarlos al repositorio para guardarlos en la base
     * Se llama desde el archivo viewEfectuarPago.js
     */
    public function fncGuardarPagoFueraAction() {
        $post = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($post['orden']);
            $tipoConcepto = $this->fncTipoConcepto($orden);

            $pagosJSON = array();

            $pagosJSON["usuario"] = $tipoConcepto["usuario"];
            $pagosJSON["cliente"] = $tipoConcepto["cliente"];
            $pagosJSON["orden"] = $post["orden"];
            $pagosJSON["montoCompra"] = 0;
            $pagosJSON["montoCreditos"] = $this->fncObtenerTotal($orden);
            $pagosJSON["fecha"] = new DateTime( );
            $pagosJSON["estatus"] = ($post["tipoPago"] == 2) ? 1 : 2;
            $pagosJSON["compraCreditos"] = null;
            $pagosJSON["compraVenta"] = $orden;
            $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
            $pagosJSON["tipoDebito"] = $this->fncObtenerTipoDebito($post["formaDebito"]);
            $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];

            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pago = $pagosRepo->fncAgregarPago($pagosJSON);
        } catch (Exception $exc) {
            $pago["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($pago);
    }

    /**
     * Obtiene el monto total en creditos de la compra de productos 
     */
    public function fncObtenerTotal($orden) {
        $productos = $orden->getProducts();
        $totalCreditos = 0;

        foreach ($productos as $productoCompra) {
            $producto = $productoCompra->getProduct();

            $subtotalCreditos = $producto->getPrice() * $productoCompra->getQuantity();
            $totalCreditos += $subtotalCreditos;
        }

        return $totalCreditos;
    }

    /**
     */
    public function fncExportarAction() {
        $params = $this->getRequest()->getPost();
        $em = $this->getEntityManager('DefaultDb');
        try {
            $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
            $pagos = $pagosRepo->fncGetListExport($this->getParametros($params));
        } catch (Exception $exc) {
            $pagos["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($pagos);
    }

    public function getUserCurrencyAction() {
        $em = $this->getEntityManager('DefaultDb');
        $conn = $em->getConnection();
        $response = array();
        $moneda_id;
        try {
            $query = 'SELECT moneda_id FROM users WHERE id = ' . $this->currentUserId . '; ';
            $res = $conn->executeQuery($query);
            $res = $res->fetchAll();
            $moneda_id = $res[0]['moneda_id'];

            $queryMXN = 'SELECT id FROM tbltipomonedas WHERE chrCurrencyCode LIKE "%' . self::MONEDA_MXN . '%";';
            $res = $conn->executeQuery($queryMXN);
            $res = $res->fetchAll();
            $monedaMXN = $res[0]['id'] ? $res[0]['id'] : 1;
        } catch (Exception $exc) {
            $response["error"] = $this->logAndResolveException($exc, $params);
        }
        echo json_encode($moneda_id ? $moneda_id : $monedaMXN);
    }

}
