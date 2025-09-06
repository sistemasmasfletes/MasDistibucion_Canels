<?php

use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_PagosRepository extends EntityRepository {

    public function getPagosListDQL($parametros) {

        $total_rows = $this->getTotalRows($parametros);

        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);

        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultPagos = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());

        $result[0] = $resultPagos;

        $result[1][0] = array(
            'records' => $total_rows,
            'page' => $parametros["pagina"],
            'totalpages' => $total_pages
        );

        return $result;
    }

    private function fncGetUsuario() {
        $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        return $userSessionId;
    }

    private function fncGetQueryBuilder($parametros, $case) {
        $SELECCIONAR = 1;
        //$CONTAR = 0;

        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Pagos')
                ->createQueryBuilder('m');

        //verifica si es una sentencia select o un count
        if ($case == $SELECCIONAR) {
            $query->select('m');
        } else {
            $query->select('count(m.id)');
        }

        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.tipoConcepto', 'a')
                ->innerJoin('m.tipoDebito', 'b');

        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);
        $query->where('m.usuario = :usuario');

        //si existe el filtro de busqueda hace el where
        if ($parametros["filtro"] != NULL) {
            $query->andWhere('m.fecha LIKE :fecha')
                    ->andWhere('a.tipoConcepto LIKE :tipoConcepto')
                    ->andWhere('b.tipoDebito LIKE :tipoDebito')
                    ->andWhere('m.orden LIKE :orden')
                    ->andWhere('m.descripcion LIKE :descripcion')
                    ->andWhere('m.estatus LIKE :estatus')
                    ->andWhere('m.montoCreditos LIKE :montoCreditos')
                    ->setParameter('fecha', '%' . $this->fncGetFecha($parametros["filtro"]["fecha"]) . '%')
                    ->setParameter('tipoConcepto', '%' . $parametros["filtro"]["tipoConcepto"] . '%')
                    ->setParameter('tipoDebito', '%' . $parametros["filtro"]["tipoDebito"] . '%')
                    ->setParameter('orden', '%' . $parametros["filtro"]["orden"] . '%')
                    ->setParameter('descripcion', '%' . $parametros["filtro"]["descripcion"] . '%')
                    ->setParameter('estatus', '%' . $this->fncGetEstatus($parametros["filtro"]["estatus"]) . '%')
                    ->setParameter('montoCreditos', '%' . $parametros["filtro"]["montoCreditos"] . '%');
        }

        $query->setParameter('usuario', $this->fncGetUsuario());
        return $query;
    }

    private function fncGetFecha($fecha) {
        if ($fecha) {
            $date = new DateTime($fecha);
            return $date->format("Y-m-d");
        } else {
            return null;
        }
    }

    private function fncGetEstatus($texto) {
        $STR_PAGADO = "pagado";
        $STR_PENDIENTE = "pendiente";

        $pagado = ((strcasecmp($texto, $STR_PAGADO) == 0) ? 1 : null);

        $pendiente = ((strcasecmp($texto, $STR_PENDIENTE) == 0) ? 2 : null);

        $result = ($pagado == 1) ? 1 : $pendiente;

        return $result;
    }

    private function fncObtenerQuery($parametros, $tipo, $case) {
        $DATOS_POR_PAGINA = 0;
        //$TODOS_DATOS = 1;

        $query = $this->fncGetQueryBuilder($parametros, $case);

        //si los datos se necesitan paginados entra aqui, sino retorna todos los datos
        if ($tipo == $DATOS_POR_PAGINA) {
            $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
            $query->setMaxResults($parametros["registrosPorPagina"]);
            $query->setFirstResult($offset);
        }

        return $query->getQuery();
    }

    private function getTotalRows($parametros) {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - count
        $qb = $this->fncObtenerQuery($parametros, 1, 0);
        $total_rows = $qb->getSingleScalarResult();
        return $total_rows;
    }

    /**
     * crea el arreglo de datos para retornar en la lista de pagos
     */
    private function mapResultToArray($pagos) {

        $resultPagos = array();
        foreach ($pagos as $row) {
            $resultPagos[] = array(
                'id' => $row->getId(),
                'fecha' => $row->getFecha()->format("d-m-Y"),
                'tipoConcepto' => $row->getTipoConcepto()->getTipoConcepto(),
                'tipoDebito' => $row->getTipoDebito()->getTipoDebito(),
                'orden' => $row->getOrden(),
                'estatus' => ($row->getEstatus() == 1) ? "Pagado" : "Pendiente",
                'descripcion' => $row->getDescripcion(),
                'montoCreditos' => number_format($row->getMontoCreditos(), 3)
            );
        }
        return $resultPagos;
    }

    /**
     * Agrega el pago 
     */
    private function fncAddPago($pagosJSON) {
        $em = $this->getEntityManager();

        $pagos = new DefaultDb_Entities_Pagos();

        $pagos->setUsuario($pagosJSON["usuario"]);
        $pagos->setCliente($pagosJSON["cliente"]);
        $pagos->setOrden($pagosJSON["orden"]);
        $pagos->setMontoCompra($pagosJSON["montoCompra"]);
        $pagos->setMontoCreditos($pagosJSON["montoCreditos"]);
        $pagos->setFecha($pagosJSON["fecha"]);
        $pagos->setEstatus($pagosJSON["estatus"]);
        $pagos->setCompraCreditos($pagosJSON["compraCreditos"]);
        $pagos->setCompraVenta($pagosJSON["compraVenta"]);
        $pagos->setTipoConcepto($pagosJSON["tipoConcepto"]);
        $pagos->setTipoDebito($pagosJSON["tipoDebito"]);
        $pagos->setDescripcion($pagosJSON["descripcion"]);
        $pagos->setPromocion(isset($pagosJSON["promocion"]) ? $pagosJSON["promocion"] : null);

        $em->persist($pagos);
        $em->flush();

        return $pagos;
    }

    /**
     * obtiene los datos de la compra de productos y llama al metodo de agregar pago
     */
    public function fncAgregarPago($pagosJSON)
    {	
        $PAGO_MOMENTO = 3;
        $PAGO_FUERA = 3;
        $extra = $this->fncAddPago($pagosJSON);
        $pago = $this->fncAddPago($pagosJSON);

        if($pagosJSON["tipoDebito"]->getId()==$PAGO_MOMENTO)
        {
            $this->fncActualizarSaldo($pago);
        }
        if ($pagosJSON["tipoDebito"]->getId() != $PAGO_FUERA)
        {
            $this->fncCongelaSaldo($pago);
        }
        $this->fncInvertir($extra, $pago );
    }

    public function fncAgregarPagoPromocion($pagosJSON){
        $CONGELAR_CREDITOS = 1;
        $ESTATUS_PENDIENTE = 2;

        //Crear pago
        $pago = $this->fncAddPago($pagosJSON);

        //Congelar créditos en el usuario
        $cliente = $pago->getUsuario();
        if($pago->getTipoDebito()->getId() == $CONGELAR_CREDITOS)
        {
            $cliente->setCredito($cliente->getCredito()-$pago->getMontoCreditos());
            $cliente->setCreditoCongelado($cliente->getCreditoCongelado()+$pago->getMontoCreditos());
        }
        else
        {
            $cliente->setCreditoNegativo($cliente->getCreditoNegativo()+$pago->getMontoCreditos());
        }

        //Actualizar estatus Promoción
        $pago->getPromocion()->setPaymentStatus($ESTATUS_PENDIENTE);

        //Actualizar Balance General
        $em = $this->getEntityManager();
        $pagosRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
        $pagosRepo->fncAgregarBalancePagos($pago);
    }

    /**
     * Si es compra, crea una venta y viceversa, para que ambos usuarios puedan verla 
     */
    private function fncInvertir($extra, $pago )
    {
        $VENTA = 3;
        $COMPRA = 4;
        $PAGO_FUERA = 4;

        $em = $this->getEntityManager();

        $concepto = ($pago->getTipoConcepto()->getId()==$VENTA) ? $COMPRA : $VENTA;

        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($concepto);

        $descripcion = ($concepto==$VENTA) ? "Venta a ".$pago->getUsuario()->getCommercialName()
                        :"Compra a ".$pago->getUsuario()->getCommercialName() ;

        $extra->setUsuario($pago->getCliente());
        $extra->setCliente($pago->getUsuario());
        $extra->setTipoConcepto($tipoConcepto);
        $extra->setDescripcion($descripcion);

        $em->persist( $extra );
        $em->flush( );

        if($pago->getTipoDebito()->getId() != $PAGO_FUERA){
            $this->fncAgregarBalanceGeneral($pago);
        }
        
        if($extra->getTipoDebito()->getId() != $PAGO_FUERA){
            $this->fncAgregarBalanceGeneral($extra);
        }
        
        $this->fncAgregarActividadesChofer($pago, $extra);
    }

    /*
     * si la orden es una compra de productos la agregar a las actividades del chofer 
     */

    private function fncAgregarActividadesChofer($pago, $extra) {
        $COMPRA = 4;
        if ($pago->getTipoConcepto()->getId() == $COMPRA) {
            $this->fncAgregarActividadChofer($pago);
        } else {
            $this->fncAgregarActividadChofer($extra);
        }
    }

    /**
     * obtiene los datos de la compra de creditos y llama al metodo de agregar pago
     */
    public function fncAgregarPagoCreditos($pagosJSON) {
        $COMPRA_CREDITOS = 2;
        $em = $this->getEntityManager();

        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($pagosJSON["usuario"]);

        $pagos = new DefaultDb_Entities_Pagos();

        $pagos->setUsuario($usuario);
        $pagos->setCliente(NULL);
        $pagos->setOrden($pagosJSON["orden"]);
        $pagos->setTipoPago($pagosJSON["tipoPago"]);
        $pagos->setMontoCompra($pagosJSON["monto"]);
        $pagos->setFecha($pagosJSON["fecha"]);
        $pagos->setEstatus($pagosJSON["estatus"]);
        $pagos->setFormaDebito($pagosJSON["formaDebito"]);
        $pagos->setTipoMovimiento($COMPRA_CREDITOS);

        $em->persist($pagos);
        $em->flush();
    }

    /**
     * obtiene el total de la compra en moneda y en creditos
     */
    public function fncObtenerTotal($orden, $moneda) {
        $productos = $orden->getProducts();

        $totalMonedas = 0;
        $totalCreditos = 0;

        $result = array();

        foreach ($productos as $productoCompra) {
            $producto = $productoCompra->getProduct();

            $subtotalCreditos = $producto->getPrice() * $productoCompra->getQuantity();
            $totalCreditos += $subtotalCreditos;

            $subtotalMonedas = $this->fncCalculaCreditos($moneda, $subtotalCreditos);
            $totalMonedas += $subtotalMonedas;
        }

        $result["total"] = $totalMonedas;
        $result["totalCreditos"] = $totalCreditos;

        return $result;
    }

    /**
     * realiza la conversion de creditos a terminos monetarios
     */
    public function fncCalculaCreditos($moneda, $montoCompra) {

        $em = $this->getEntityManager('DefaultDb');

        $monedas = $em->getRepository('DefaultDb_Entities_TipoMonedas')->find($moneda);

        $conversion = $em->getRepository('DefaultDb_Entities_Conversion')
                ->createQueryBuilder('c')
                ->where("c.moneda = :moneda AND c.fecha <= :today ")
                ->setParameter("moneda", $monedas)
                ->setParameter("today", new DateTime())
                ->addOrderBy('c.fecha', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        $creditos = 0;

        if ($conversion) {
            $precioVenta = floatval($conversion->getCompra());
            $cantidadCreditos = floatval($conversion->getCreditos());

            $creditos = ( $montoCompra * $precioVenta ) / $cantidadCreditos;
        }

        return $creditos;
    }

    /**
     * actualiza el saldo de los usuarios, al comprador le descuenta y al vendedor los aumenta
     */
    private function fncActualizarSaldo($pagos) {
        $VENTA = 3;
        //$COMPRA = 4;

        $em = $this->getEntityManager();

        $cliente = $pagos->getCliente();
        $usuario = $pagos->getUsuario();

        if($pagos->getTipoConcepto()->getId() == $VENTA)
        {
            $cliente->setCredito($cliente->getCredito()-$pagos->getMontoCreditos());
            $usuario->setCredito($usuario->getCredito()+$pagos->getMontoCreditos());
        }
        else
        {
            $cliente->setCredito($cliente->getCredito()+$pagos->getMontoCreditos());
            $usuario->setCredito($usuario->getCredito()-$pagos->getMontoCreditos());
        }

        $em->persist($cliente);
        $em->persist($usuario);

        $em->flush();

        $this->fncActualizaEstatusOrden($pagos->getCompraVenta(), 1);
    }

    /**
     * si el pago es contra entrega, se congela o se manejan creditos negativos
     */
    private function fncCongelaSaldo($pagos) {
        $VENTA = 3;
        $CONGELAR_CREDITOS = 1;

        $em = $this->getEntityManager();

        $cliente = ($pagos->getTipoConcepto()->getId() == $VENTA) ? $pagos->getCliente() : $pagos->getUsuario();
        $vendedor = ($pagos->getTipoConcepto()->getId() == $VENTA) ? $pagos->getUsuario() : $pagos->getCliente();
        

        if ($pagos->getTipoDebito()->getId() == $CONGELAR_CREDITOS) {
            $creditoCongelado = $cliente->getCredito() - $pagos->getMontoCreditos();

            $cliente->setCredito($creditoCongelado);
            $cliente->setCreditoCongelado($cliente->getCreditoCongelado() + $creditoCongelado);
            $vendedor->setCreditoCongelado($vendedor->getCreditoCongelado() + $pagos->getMontoCreditos());
        } else {

            $cliente->setCreditoNegativo($cliente->getCreditoNegativo() + $pagos->getMontoCreditos());
        }

        $em->persist($cliente);
        $em->persist($vendedor);
        $this->fncActualizaEstatusOrden($pagos->getCompraVenta(), 2);
        $em->flush();
    }

    /**
     * actualiza el estatus de la orden de sin pagar a pendiente o pagado 
     */
    public function fncActualizaEstatusOrden($orden, $tipo) {
        $em = $this->getEntityManager();
        $orden->setPaymentStatus($tipo);
        $em->persist($orden);
        $em->flush();
    }

    /**
     * Guarda los datos del pago en el balance general
     */
    public function addBalance($orden) {
        $em = $this->getEntityManager('DefaultDb');

        $balanceGeneralRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');

        $balanceGeneralRepo->guardarBalanceCompraVenta($orden);
    }

    /**
     * Guarda el pago del flete ya sea como comprador o vendedor
     */
    public function fncGuardarPagoFlete($orden, $total) {

        $tipoConcepto = $this->fncTipoConcepto($orden);

        $pagosJSON = array();

        $pagosJSON["usuario"] = $tipoConcepto["usuario"];
        $pagosJSON["cliente"] = $tipoConcepto["cliente"];
        $pagosJSON["orden"] = $orden->getId();
        $pagosJSON["montoCompra"] = null;
        $pagosJSON["montoCreditos"] = $total;
        $pagosJSON["fecha"] = new DateTime( );
        $pagosJSON["estatus"] = 1;
        $pagosJSON["compraCreditos"] = null;
        $pagosJSON["compraVenta"] = $orden;
        $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
        $pagosJSON["tipoDebito"] = $tipoConcepto["debito"];
        $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];

        $pago = $this->fncAddPago($pagosJSON);
        $this->fncAgregarBalanceGeneral($pago);
    }

    /**
     * Guarda el pago de la compra de creditos y se actualiza el estatus cuando 
     * la compra se aprueba
     */
    public function fncGuardarPagoCreditos($compraCreditos, $estado) {
        $ESTATUS_PAGADO = 1;
        $ESTATUS_PENDIENTE = 2;

        $tipoConcepto = $this->fncTipoConceptoCreditos($compraCreditos);

        $pagosJSON = array();

        $pagosJSON["usuario"] = $compraCreditos->getCliente();
        $pagosJSON["cliente"] = $tipoConcepto["usuario"];
        $pagosJSON["orden"] = $compraCreditos->getId();
        $pagosJSON["montoCompra"] = $compraCreditos->getMontoCompra();
        $pagosJSON["montoCreditos"] = $compraCreditos->getCreditos();
        $pagosJSON["fecha"] = new DateTime();
        $pagosJSON["estatus"] = ($estado == true) ? $ESTATUS_PAGADO : $ESTATUS_PENDIENTE;
        $pagosJSON["compraCreditos"] = $compraCreditos;
        $pagosJSON["compraVenta"] = null;
        $pagosJSON["tipoConcepto"] = $tipoConcepto["tipoConcepto"];
        $pagosJSON["tipoDebito"] = $tipoConcepto["debito"];
        $pagosJSON["descripcion"] = $tipoConcepto["descripcion"];

        $pago = $this->fncAddPago($pagosJSON);
        $this->fncAgregarBalanceGeneral($pago);
        return $pago;
    }

    /**
     * Obtiene el concepto, es decir si es compra o es venta, con sus respectivos valores
     */
    private function fncTipoConcepto($orden) {
        $FLETE_VENTA = 6;
        $FLETE_COMPRA = 5;
        $CREDITOS = 8;

        $em = $this->getEntityManager('DefaultDb');

        $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);

        $cliente = ($usuario == $orden->getSeller()) ? $orden->getBuyer() : $orden->getSeller();

        $concepto = ($usuario == $orden->getSeller()) ? $FLETE_VENTA : $FLETE_COMPRA;
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($concepto);
        $compra = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($FLETE_COMPRA);
        $venta = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($FLETE_VENTA);
        $debito = $em->getRepository('DefaultDb_Entities_TipoDebito')->find($CREDITOS);

        $datos = array();
        $datos["usuario"] = $usuario;
        $datos["cliente"] = $cliente;
        $datos["tipoConcepto"] = $tipoConcepto;
        $datos["compra"] = $compra;
        $datos["venta"] = $venta;
        $datos["debito"] = $debito;
        $datos["descripcion"] = "Pago de Flete a " . $cliente->getCommercialName();

        return $datos;
    }

    /**
     * Obtiene los datos como compra de creditos
     */
    private function fncTipoConceptoCreditos($compra) {
        $COMPRA_CREDITOS = 2;

        $em = $this->getEntityManager('DefaultDb');

        $idUsuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id'];
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($idUsuario);
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($COMPRA_CREDITOS);

        $debito = $em->getRepository('DefaultDb_Entities_TipoDebito')->findOneBy(array("tipoDebito" => $compra->getTipoPago()->getTipoPago()));

        $datos = array();
        $datos["usuario"] = $usuario;
        $datos["tipoConcepto"] = $tipoConcepto;
        $datos["debito"] = $debito;
        $datos["descripcion"] = "Compra de Créditos a Más Distribución";

        return $datos;
    }

    //agrega los datos de la tranferencia a la tabla de balance General
    public function fncAgregarBalanceGeneral($pagos) {
        $em = $this->getEntityManager();
        $pagosRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
        $pagosRepo->fncAgregarBalancePagos($pagos);
    }

    //agrega los datos de la compra a la tabla de actividad chofer log
    public function fncAgregarActividadChofer($pagos) {
        $em = $this->getEntityManager();
        $actividadRepo = $em->getRepository('DefaultDb_Entities_ActividaChoferLog');
        $actividadRepo->fncAgregarActividadChoferLog($pagos);
    }

    //retorna la lista para exportar
    public function fncGetListExport($parametros) {

        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $pagos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();

        foreach ($pagos as $row) {
            $resultPagos[] = array
                (
                'fecha' => $row->getFecha()->format("d-m-Y"),
                'tipoConcepto' => $row->getTipoConcepto()->getTipoConcepto(),
                'tipoDebito' => $row->getTipoDebito()->getTipoDebito(),
                'orden' => $row->getOrden(),
                'descripcion' => $row->getDescripcion(),
                'estatus' => ($row->getEstatus() == 1) ? "Pagado" : "Pendiente",
                'montoCreditos' => number_format($row->getMontoCreditos(), 3)
            );
        }

        return $resultPagos;
    }

}
