<?php

use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_BalanceGeneralRepository extends EntityRepository {

    public function getBalanceGeneralListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultBalanceGeneral = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());
        
        $result[0] = $resultBalanceGeneral;

        $result[1][0] = array(
            'records' => $total_rows, 
            'page' => $parametros["pagina"], 
            'totalpages' => $total_pages
        );
        
        return $result;  
    }
    
    private function fncGetUsuario()
    {
        $em = $this->getEntityManager('DefaultDb');
        $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($userSessionId);
        return $usuario;
    }
    
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_BalanceGeneral')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.tipoConcepto', 'a');
        $query->leftJoin('m.pagos', 'p');
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);
        $query->where("m.cliente = :usuario"); 

        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->andWhere('m.fecha LIKE :fecha')
            ->andWhere('a.tipoConcepto LIKE :tipoConcepto')
            ->andWhere('m.referencia LIKE :referencia')
            ->andWhere('m.concepto LIKE :concepto')
            ->andWhere('m.estatus LIKE :estatus')
            ->andWhere('m.creditos LIKE :creditos')
            ->andWhere('m.ingresos LIKE :ingresos')
            ->andWhere('m.egresos LIKE :egresos')
            ->andWhere('m.balance LIKE :balance') 
            ->andWhere('p.pagos LIKE :pagos')
            ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
            ->setParameter('tipoConcepto', '%'.$parametros["filtro"]["tipoConcepto"].'%')
            ->setParameter('referencia', '%'.$parametros["filtro"]["referencia"].'%')
            ->setParameter('concepto', '%'.$parametros["filtro"]["concepto"].'%')
            ->setParameter('estatus', '%'.$this->fncGetEstatus($parametros["filtro"]["estatus"]).'%')
            ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%')
            ->setParameter('ingresos', '%'.$parametros["filtro"]["ingresos"].'%')
            ->setParameter('egresos', '%'.$parametros["filtro"]["egresos"].'%')
            ->setParameter('balance', '%'.$parametros["filtro"]["balance"].'%')
            ->setParameter('pagos', '%'.$parametros["filtro"]["orden"].'%');
        }  
       // $query->setParameter('idArray', $idArray );
        $query->setParameter('usuario', $this->fncGetUsuario());  
        return $query;
    }
 
    private function fncGetFecha($fecha)
    {
        if($fecha)
        {
            $date = new DateTime($fecha);
            return $date->format("Y-m-d");
        }
        else 
        {
            return null;
        }
    }
    
    private function fncGetEstatus($texto)
    {
        $STR_PAGADO = "pagado";
        $STR_PENDIENTE = "pendiente";
        
        $pagado = ((strcasecmp($texto, $STR_PAGADO) == 0) ? 1 : null);
        
        $pendiente = ((strcasecmp($texto, $STR_PENDIENTE) == 0) ? 2 : null);
        
        $result = ($pagado == 1) ? 1 : $pendiente;
        
        return $result;
    }
    
    private function fncObtenerQuery($parametros, $tipo, $case)
    {
        $DATOS_POR_PAGINA = 0;
        //$TODOS_DATOS = 1;

        $query = $this->fncGetQueryBuilder($parametros, $case);
        
        //si los datos se necesitan paginados entra aqui, sino retorna todos los datos
        if($tipo == $DATOS_POR_PAGINA)
        {
            $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
            $query->setMaxResults($parametros["registrosPorPagina"]);
            $query->setFirstResult($offset);   
        }
        
        return $query->getQuery();
    }
    
    private function getTotalRows($parametros) 
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - count
        $qb = $this->fncObtenerQuery($parametros, 1, 0);
        $total_rows = $qb->getSingleScalarResult();
        return $total_rows;
    }

    private function getConversion($idmoneda)
    {
    	$em = $this->getEntityManager('DefaultDb');
    	$conversion = $em->getRepository('DefaultDb_Entities_Conversion')->findOneBy(array('moneda' => $idmoneda));
    	return $conversion;
    }

    private function mapResultToArray( $balanceGeneral ) {
        
        $resultBalanceGeneral = array();
        $COMPRA_PRODUCTO = 4;
        $VENTA_PRODUCTO = 3;
        $TRANFERENCIA_CREDITOS = 1;
        $COMPRA_CREDITOS = 2;
        $COMPRA_PROMOCION = 7;
        $PAGO_CONGELADO = 1;
        $PAGADO = 1;
        $saldoCongelado = 0;
        foreach ( $balanceGeneral as $row ) 
        {   
            $saldoCongelado = 0;
            if($row->getPagos() !== null && 
               ($row->getPagos()->getTipoConcepto()->getId() == $COMPRA_PRODUCTO || 
                $row->getPagos()->getTipoConcepto()->getId() == $COMPRA_PROMOCION || 
                $row->getPagos()->getTipoConcepto()->getId() == $VENTA_PRODUCTO) && 
               $row->getPagos()->getTipoDebito()->getId() == $PAGO_CONGELADO &&
               $row->getEstatus() != $PAGADO)
            {
                $saldoCongelado = $row->getMonto();
            }            

            $cv = $this->getConversion($row->getCliente()->getMoneda()->getId());

            $resultBalanceGeneral[] = array(
                'id' => $row->getId(), 
                'fecha'=> $row->getFecha()->format("d-m-Y"),
                'referencia' => $row->getReferencia(),
                'concepto' => $row->getConcepto(),
                'tipoConcepto' => $row->getTipoConcepto()->getTipoConcepto(),
                'conversion' => "$ ".number_format(floatval(floatval($row->getBalance() / $cv->getCreditos())),3),
                'estatus' => ($row->getEstatus()==$PAGADO)? "Pagado" : "Pendiente",
                'monto' => number_format( $row->getMonto(), 3 ), 
                'creditos' => number_format( $row->getCreditos(), 3 ), 
                'ingresos' => number_format($row->getIngresos(),3),
                'egresos' => number_format($row->getEgresos(),3),
                'balance' => number_format($row->getBalance(),3),
                'congelado' => number_format($saldoCongelado,3),
                'orden' =>  ($row->getPagos() !== null ? (
                                    (
                                        $row->getPagos()->getTipoConcepto()->getId() == $COMPRA_PROMOCION ? 
                                        $row->getPagos()->getPromocion()->getId() 
                                        :   (
                                            ($row->getPagos()->getTipoConcepto()->getId() !== $TRANFERENCIA_CREDITOS && 
                                            $row->getPagos()->getTipoConcepto()->getId() !== $COMPRA_CREDITOS) ? $row->getPagos()->getCompraVenta()->getId() : "" 
                                        )
                                    )                                
                                )                 
                            : "" 
                            )
                //se agrego el numero de orden de compra 'orden' => $row->getPagos()->getCompraVenta()->getId()
            );
        }
        
        return $resultBalanceGeneral;
    }

    private function addBalanceGeneral($balanceJSON) 
    {
        $em = $this->getEntityManager();

        $balanceGeneral = new DefaultDb_Entities_BalanceGeneral();
        
        $balanceGeneral->setFecha($balanceJSON["fecha"]);
        $balanceGeneral->setReferencia($balanceJSON["referencia"]);
        $balanceGeneral->setConcepto($balanceJSON["concepto"]);
        $balanceGeneral->setCliente($balanceJSON["cliente"]);
        $balanceGeneral->setEstatus($balanceJSON["estatus"]);
        $balanceGeneral->setMonto(floatval($balanceJSON["monto"]));
        $balanceGeneral->setCreditos(floatval($balanceJSON["creditos"]));
        $balanceGeneral->setIngresos(floatval($balanceJSON["ingresos"]));
        $balanceGeneral->setEgresos(floatval($balanceJSON["egresos"]));
        $balanceGeneral->setBalance($balanceJSON["balance"]);
        $balanceGeneral->setPagos($balanceJSON["pagos"]);
        $balanceGeneral->setTransferencia($balanceJSON["transferencia"]);
        $balanceGeneral->setTipoConcepto($balanceJSON["tipoConcepto"]);
         
        $em->persist($balanceGeneral);
        
        $em->flush();
        
        return $balanceGeneral ;
    }
    
    public function fncAgregarBalanceTransferencia($transferenciaCreditos)
    {
        $TRANSFERENCIA_CREDITOS = 1;
        
        $em = $this->getEntityManager('DefaultDb'); 
        
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($TRANSFERENCIA_CREDITOS);
         
        $concepto = "Transferencia de créditos a ".$transferenciaCreditos->getClient()->getCommercialName();
        
        $balanceJSON = array();

        $balanceJSON["fecha"] =  new DateTime();   
        $balanceJSON["referencia"] = $transferenciaCreditos->getId();
        $balanceJSON["concepto"] = $concepto;
        $balanceJSON["cliente"] = $this->fncObtenerUsuario();
        $balanceJSON["estatus"] = 1;
        $balanceJSON["monto"] = $transferenciaCreditos->getMonto();
        $balanceJSON["creditos"] = $this->fncObtenerUsuario()->getCredito() + $transferenciaCreditos->getMonto();
        $balanceJSON["ingresos"] = 0;
        $balanceJSON["egresos"] = $transferenciaCreditos->getMonto(); 
        $balanceJSON["balance"] = $transferenciaCreditos->getCreditos();
        $balanceJSON["pagos"] = NULL;
        $balanceJSON["transferencia"] = $transferenciaCreditos;
        $balanceJSON["tipoConcepto"] = $tipoConcepto;
        
        $this->addBalanceGeneral($balanceJSON);
        $this->fncAgregarBalanceTransferenciaCliente($transferenciaCreditos);
        
    }
    
    public function fncAgregarBalanceTransferenciaCliente($transferenciaCreditos)
    {
        $TRANSFERENCIA_CREDITOS = 1;
        
        $em = $this->getEntityManager('DefaultDb'); 
        
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find($TRANSFERENCIA_CREDITOS);
   
        $concepto = "Transferencia de créditos de ".$this->fncObtenerUsuario()->getCommercialName();
        
        $balanceJSON = array();

        $balanceJSON["fecha"] =  new DateTime();   
        $balanceJSON["referencia"] = $transferenciaCreditos->getId();
        $balanceJSON["concepto"] = $concepto;
        $balanceJSON["cliente"] = $transferenciaCreditos->getClient();
        $balanceJSON["estatus"] = 1;
        $balanceJSON["monto"] = $transferenciaCreditos->getMonto();
        $balanceJSON["creditos"] = $transferenciaCreditos->getClient()->getCredito() - $transferenciaCreditos->getMonto();
        $balanceJSON["ingresos"] = $transferenciaCreditos->getMonto();
        $balanceJSON["egresos"] = 0; 
        $balanceJSON["balance"] = $transferenciaCreditos->getClient()->getCredito();
        $balanceJSON["pagos"] = NULL;
        $balanceJSON["transferencia"] = $transferenciaCreditos;
        $balanceJSON["tipoConcepto"] = $tipoConcepto;
        
        $this->addBalanceGeneral($balanceJSON);       
        
    }
    
    public function fncAgregarBalancePagos($pagos)
    {
        $COMPRA_CREDITOS = 2;
        $VENTA = 3;
        $COMPRA = 4;
        $ESTATUS_PAGADO = 1;
        $ESTATUS_PENDIENTE = 2;
        $COMPRA_PROMOCION = 7;

        if($pagos->getTipoConcepto()->getId() == $COMPRA_CREDITOS)
        {
            $cliente = $pagos->getCliente();
        }
        else 
        {
            $cliente = $pagos->getUsuario();
        }
        $balanceJSON = array();

        $balanceJSON["fecha"] =  new DateTime();   
        $balanceJSON["referencia"] = $pagos->getId();
        $balanceJSON["concepto"] = $pagos->getDescripcion();
        $balanceJSON["cliente"] = $cliente;
        $balanceJSON["estatus"] = $pagos->getEstatus();
        $balanceJSON["monto"] = $pagos->getMontoCreditos();
        
        if($pagos->getEstatus()==$ESTATUS_PAGADO)
        {
            if($pagos->getTipoConcepto()->getId() == $VENTA || $pagos->getTipoConcepto()->getId() == $COMPRA_CREDITOS)
            {
                $balanceJSON["creditos"] = $cliente->getCredito() - $pagos->getMontoCreditos();
                $balanceJSON["ingresos"] = $pagos->getMontoCreditos();
                $balanceJSON["egresos"] = 0; 
            }
            else
            {
                $balanceJSON["creditos"] = $cliente->getCredito() + $pagos->getMontoCreditos();
                $balanceJSON["ingresos"] = 0;
                $balanceJSON["egresos"] = $pagos->getMontoCreditos(); 
            }
        }
        else 
        {
            $balanceJSON["creditos"] = $cliente->getCredito();
            $balanceJSON["ingresos"] = 0;
            $balanceJSON["egresos"] = 0; 
        }
        if ($pagos->getEstatus() == $ESTATUS_PAGADO) {
            $balanceJSON["balance"] = $cliente->getCredito();
        }
        if ($pagos->getEstatus() == $ESTATUS_PENDIENTE && ($pagos->getTipoConcepto()->getId() == $VENTA || $pagos->getTipoConcepto()->getId() == $COMPRA || $pagos->getTipoConcepto()->getId() == $COMPRA_CREDITOS || $pagos->getTipoConcepto()->getId() == $COMPRA_PROMOCION)) {
            $balanceJSON["balance"] = $cliente->getCredito();
        }
       

        $balanceJSON["pagos"] = $pagos;
        $balanceJSON["transferencia"] = NULL;
        $balanceJSON["tipoConcepto"] = $pagos->getTipoConcepto();
        
        $this->addBalanceGeneral($balanceJSON);       
        
    }
    
    private function fncObtenerUsuario()
    {
        $em = $this->getEntityManager('DefaultDb'); 

        $usuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        
        $cliente = $em->getRepository('DefaultDb_Entities_User')->find($usuario);
        
        return $cliente;
    }
//
//        public function guardarBalance($orden, $total, $creditos)
//    {
//        
//        $em = $this->getEntityManager('DefaultDb'); 
//        
//        $PAGO_FLETE_COMPRA = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find(5);
//        $PAGO_FLETE_VENTA = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find(6);
//        $usuario = $this->usuario = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
//
//        $movimiento = $PAGO_FLETE_COMPRA;
//        $concepto = "Retiro por pago de flete a ".$orden->getSeller()->getCommercialName();
//        
//        if($usuario === $orden->getSeller()->getId())
//        {
//            $movimiento = $PAGO_FLETE_VENTA;
//            $concepto = "Retiro por pago de flete a ".$orden->getBuyer()->getCommercialName();
//        }
//        
//        $balanceJSON = array();
//        
//        $balanceJSON["fecha"] =  new DateTime();  
//        $balanceJSON["tipoMovimiento"] = $movimiento; 
//        $balanceJSON["referencia"] = $orden->getId();
//        $balanceJSON["concepto"] = $concepto;
//        $balanceJSON["cliente"] = $usuario;
//        $balanceJSON["estatus"] = "Pagado";
//        $balanceJSON["monto"] = $total;
//        $balanceJSON["creditos"] = $creditos;
//        $balanceJSON["ingresos"] = 0;
//        $balanceJSON["egresos"] = $total;
//       
//        $this->addBalanceGeneral($balanceJSON);
//    }
    
//    public function fncGuardarPago($orden, $total) 
//    {
//        $PAGO_CREDITOS = 7;
//        $ESTATUS_PAGADO = 1;
//        $ACREDITAR_CREDITOS = 3;
//
//        $em = $this->getEntityManager('DefaultDb');
//
//        $pagosJSON = array();
//
//        $pagosJSON["usuario"] = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
//        $pagosJSON["orden"] = $orden->getId();
//        $pagosJSON["tipoPago"] = $PAGO_CREDITOS;
//        $pagosJSON["moneda"] = null;
//        $pagosJSON["monto"] = $total;
//        $pagosJSON["fecha"] = new DateTime();
//        $pagosJSON["estatus"] = $ESTATUS_PAGADO;
//        $pagosJSON["formaDebito"] = $ACREDITAR_CREDITOS;
//
//        $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');
//
//        $pagosRepo->fncAgregarPagoCreditos($pagosJSON);
//    }
    
    
    
    
    public function guardarBalanceCompraCreditos($orden, $creditos)
    {
        $COMPRA_CREDITOS = 8;
        $em = $this->getEntityManager('DefaultDb'); 

        $usuario = $orden->getCliente()->getId();

        $compraCreditos = $em->getRepository('DefaultDb_Entities_CompraCreditos')->find($orden->getId());
        
        $movimiento = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find($COMPRA_CREDITOS);

        $concepto = "Compra de créditos a Más Distribución";

        $balanceJSON = array();
        
        $balanceJSON["fecha"] =  new DateTime();  
        $balanceJSON["tipoMovimiento"] = $movimiento; 
        $balanceJSON["referencia"] = $compraCreditos->getId();
        $balanceJSON["concepto"] = $concepto;
        $balanceJSON["cliente"] = $usuario;
        $balanceJSON["estatus"] = "Pendiente";
        $balanceJSON["monto"] = $compraCreditos->getCreditos();
        $balanceJSON["creditos"] = $creditos;
        $balanceJSON["ingresos"] = 0;
        $balanceJSON["egresos"] = 0;
       
        $this->addBalanceGeneral($balanceJSON);
        
    }
    
//    public function guardarBalanceCompraVenta($pago)
//    {
//        $em = $this->getEntityManager('DefaultDb'); 
//
//        $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($pago->getOrden());
//        
//        $usuario = $orden->getBuyer();
//       
//        $tipo = 1;
//                
//        $movimiento = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find($tipo);
//        
//        $concepto = "Retiro por pago de compra a ".$orden->getSeller()->getCommercialName();
//
//        $estatus = ($pago->getEstatus()==1) ? "Pagado" : "Pendiente";
//        
//        $ingreso = 0;
//        $egreso = 0;
//        if($pago->getEstatus()==1)
//        {
//            $egreso = $pago->getMontoCompra();
//            $ingreso = 0;
//        }
//
//        $balanceJSON = array();
//        
//        $balanceJSON["fecha"] =  new DateTime();  
//        $balanceJSON["tipoMovimiento"] = $movimiento; 
//        $balanceJSON["referencia"] = $pago->getId();
//        $balanceJSON["concepto"] = $concepto;
//        $balanceJSON["cliente"] = $orden->getBuyer()->getId();
//        $balanceJSON["estatus"] = $estatus;
//        $balanceJSON["monto"] = $pago->getMontoCompra();
//        $balanceJSON["creditos"] = $usuario->getCredito();
//        $balanceJSON["ingresos"] = $ingreso;
//        $balanceJSON["egresos"] = $egreso;
//        $balance = $this->addBalanceGeneral($balanceJSON);
//       
//        
//        if($pago->getEstatus()==1)
//        {
//            $this->editBalanceGeneral($balance, 1, $usuario);
//        }
//        
//        $this->guardarBalanceVenta($pago);
//        
//    }
//    
//    public function guardarBalanceVenta($pago)
//    {      
//        $em = $this->getEntityManager('DefaultDb');   
//       $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($pago->getOrden());      
//        $usuario = $orden->getSeller(); 
//        $tipo = 2;        
//        $movimiento = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find($tipo);       
//        $concepto = "Ingreso por pago de venta a ".$orden->getBuyer()->getCommercialName() ;
//
//        $estatus = ($pago->getEstatus()==1) ? "Pagado" : "Pendiente";
//
//        $ingreso = 0;
//        $egreso = 0;
//        
//        if($pago->getEstatus()==1)
//        {
//            $ingreso = $pago->getMontoCompra();
//            $egreso = 0;
//        }
//        
//        $balanceJSON = array();
//        
//        $balanceJSON["fecha"] =  new DateTime();  
//        $balanceJSON["tipoMovimiento"] = $movimiento; 
//        $balanceJSON["referencia"] = $pago->getId();
//        $balanceJSON["concepto"] = $concepto;
//        $balanceJSON["cliente"] = $orden->getSeller()->getId();
//        $balanceJSON["estatus"] = $estatus;
//        $balanceJSON["monto"] = $pago->getMontoCompra();
//        $balanceJSON["creditos"] = $usuario->getCredito();
//        $balanceJSON["ingresos"] = $ingreso;
//        $balanceJSON["egresos"] = $egreso;
//
//        $balance = $this->addBalanceGeneral($balanceJSON);
//
//        if($pago->getEstatus()==1)
//        {
//            $this->editBalanceGeneral($balance, 2, $usuario);
//        }
//        
//    }
//    
//    public function guardarBalanceCompra($pago)
//    {      
//        $em = $this->getEntityManager('DefaultDb');   
//        $orden = $em->getRepository('DefaultDb_Entities_M3CommerceOrder')->find($pago->getOrden());      
//        $usuario = $orden->getBuyer(); 
//        $tipo = 1;        
//        $movimiento = $em->getRepository('DefaultDb_Entities_TipoMovimientos')->find($tipo);       
//        $concepto = "Retiro por pago de compra a ".$orden->getSeller()->getCommercialName();
//
//        $estatus = ($pago->getEstatus()==1) ? "Pagado" : "Pendiente";
//
//        if($pago->getEstatus()==1)
//        {
//            $ingreso = 0;
//            $egreso = $pago->getMontoCompra();
//        }
//        //venta
//        $balanceJSON = array();
//        
//        $balanceJSON["fecha"] =  new DateTime();  
//        $balanceJSON["tipoMovimiento"] = $movimiento; 
//        $balanceJSON["referencia"] = $pago->getId();
//        $balanceJSON["concepto"] = $concepto;
//        $balanceJSON["cliente"] = $usuario->getId();
//        $balanceJSON["estatus"] = $estatus;
//        $balanceJSON["monto"] = $pago->getMontoCompra();
//        $balanceJSON["creditos"] = $usuario->getCredito();
//        $balanceJSON["ingresos"] = $ingreso;
//        $balanceJSON["egresos"] = $egreso;
//
//        $balance = $this->addBalanceGeneral($balanceJSON);
//
//        if($pago->getEstatus()==1)
//        {
//            $this->editBalanceGeneral($balance, 1, $usuario);
//        }
//        
//    }
//    
//     
    
    
    public function editBalanceGeneral($balance, $edit, $cliente)
    {
        $em = $this->getEntityManager("DefaultDb"); 
        
        if($edit==1)
        {
            $balance->setBalance($cliente->getCredito()-$balance->getMonto());
        }
        else
        {
            $balance->setBalance($cliente->getCredito()+$balance->getMonto());
        }
        
        $balance->setTimestamp(new DateTime());
        $em->persist( $balance );
        $em->flush();
        
    }
    
    //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $balanceGeneral = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $balanceGeneral as $row ) 
        {

            $resultBalanceGeneral[] = array
            ( 
                'fecha'=> $row->getFecha()->format("d-m-Y"),
                'tipoConcepto' => $row->getTipoConcepto()->getTipoConcepto(),
                'referencia' => $row->getReferencia(),
                'concepto' => $row->getConcepto(),
                'estatus' => ($row->getEstatus()==1)? "Pagado" : "Pendiente",
                'creditos' => number_format( $row->getCreditos(), 3 ), 
                'ingresos' => number_format($row->getIngresos(),3),
                'egresos' => number_format($row->getEgresos(),3),
                'balance' => number_format($row->getBalance(),3)
            );
        }
        
        return $resultBalanceGeneral;  
    }

    public function updateSaldoCongelado($parametros){
        $COBRAR_TOTAL = 1;
        $COBRAR_50_POR_CIENTO=2;
        $ESTATUS_PAGADO = 1;
        $CONGELA_CREDITOS = 1;
        $idpago = intval($parametros["idpago"]);
        $tipoCobro = $parametros["tipoCobro"];

        $em = $this->getEntityManager('DefaultDb');       
        $dql = "SELECT partial bg.{id,estatus,monto,ingresos,egresos},partial p.{id},partial td.{id},partial cl.{id,credito,creditoCongelado,creditoNegativo}
                FROM DefaultDb_Entities_BalanceGeneral bg LEFT JOIN bg.pagos p
                LEFT JOIN bg.cliente cl
                LEFT JOIN p.tipoDebito td
                WHERE bg.pagos = :idpago";                                
        $query = $em->createQuery($dql);
        $query->setParameter("idpago", $idpago);
        $balance = $query->getOneOrNullResult();

        if($balance!=null){
            $monto = $balance->getMonto();
            if($tipoCobro==$COBRAR_50_POR_CIENTO)
                $monto = $monto*0.5;
             
            $balance->setEgresos($monto); //FALTA. Cómo se maneja cuando no se cobra la totalidad.
            $balance->setEstatus($ESTATUS_PAGADO);

            if($balance->getPagos()->getTipoDebito()->getId()==$CONGELA_CREDITOS){
                //Disminuir créditos congelados
                $balance->getCliente()->setCreditoCongelado($balance->getCliente()->getCreditoCongelado() - ($tipoCobro==$COBRAR_50_POR_CIENTO ? ($monto*2) : $monto));
                
                //Si es cobro del 50%, devolver la otra parte de los créditos congelados.
                if($tipoCobro==$COBRAR_50_POR_CIENTO)
                    $balance->getCliente()->setCredito($balance->getCliente()->getCredito()+$monto);
            }
            
            $balance->getPagos()->setEstatus($ESTATUS_PAGADO);
            $em->flush();
            //FALTA. Manejar créditos negativos
        }
    }
   
}
