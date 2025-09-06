<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

class DefaultDb_Repositories_ActividaChoferLogRepository extends EntityRepository {

    /* Obtiene la lista de datos*/
    public function getActividaChoferLogListDQL($parametros)
    {
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultActividadChoferLog = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());
        $result[0] = $resultActividadChoferLog;
        
        $total_rows = $this->getTotalRows($parametros);
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        $result[1][0] = array(
            'records' => $total_rows, 
            'page' => $parametros["pagina"], 
            'totalpages' => $total_pages
        );
        
        return $result;  
    }
    
    /* Obtiene los datos del usuario logueado */
    private function fncGetUsuario()
    {
        $em = $this->getEntityManager('DefaultDb');
        $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find($userSessionId);
        return $usuario;
    }
    
    /* Crea el query para obtener los datos del repositorio */
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
         
        $query = $em->getRepository('DefaultDb_Entities_ActividaChoferLog')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m');}
        else { $query->select('count(m.id)'); }

        //realiza la union de las tablas relacionadas
        $query->innerJoin('m.pago', 'p');
        $query->innerJoin('p.tipoDebito', 'td');
        $query->innerJoin('m.actividadTipo', 'at');
        $query->innerJoin('m.puntoActividad', 'pa');
        $query->innerJoin('p.compraVenta', 'cv');

        //busca los ids que se obtuvieron de la funcion fncGetSelectQuery
        $idArray = $this->fncGetArrayIds() ? $this->fncGetArrayIds() : NULL;
        $query->where('m.id IN (:idArray)');
        
        //realiza el ordenamiento  asc o desc
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->andWhere('m.fecha LIKE :fecha')
            ->andWhere('td.tipoDebito LIKE :tipoDebito')
            ->andWhere('at.name LIKE :name')
            ->andWhere('p.estatus LIKE :estatus')
            ->andWhere('p.montoCreditos LIKE :montoCreditos')
            ->andWhere('pa.name LIKE :puntoActividad')
            ->andWhere('cv.id LIKE :compraId')
            ->andWhere('m.estatus LIKE :estatusActividad')
            ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
            ->setParameter('tipoDebito', '%'.$parametros["filtro"]["tipoDebito"].'%')
            ->setParameter('name', '%'.$parametros["filtro"]["actividadTipo"].'%')
            ->setParameter('estatus', '%'.$this->fncGetEstatus($parametros["filtro"]["estatus"]).'%')
            ->setParameter('montoCreditos', '%'.$parametros["filtro"]["montoCreditos"].'%')
            ->setParameter('puntoActividad', '%'.$parametros["filtro"]["puntoActividad"].'%')
            ->setParameter('compraId', '%'.$parametros["filtro"]["compraId"].'%')
            ->setParameter('estatusActividad', '%'.$this->fncGetEstatusRealizado($parametros["filtro"]["estatusActividad"]).'%');
        } 

        $query->setParameter('idArray', $idArray );
        
        return $query;
    }
    
    /* Obtiene la fecha para filtar y la convierte en formato de año-mes-dia */
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
    
    /* Obtiene el estatus para filtrar y verifica si son mayuscula o minusculas */
    private function fncGetEstatus($texto)
    {
        $STR_PAGADO = "pagado";
        $STR_PENDIENTE = "pendiente";
        
        $pagado = ((strcasecmp($texto, $STR_PAGADO) == 0) ? 1 : null);
        
        $pendiente = ((strcasecmp($texto, $STR_PENDIENTE) == 0) ? 2 : null);
        
        $result = ($pagado == 1) ? 1 : $pendiente;

        return $result;
    }
    
    /* Obtiene el estatus de realizacion */
    private function fncGetEstatusRealizado($texto)
    {
        $STR_REALIZADO = "s";
        $STR_NO_REALIZADO = "n";
        
        $realizado = ((strcasecmp($texto, $STR_REALIZADO) == 0) ? 1 : null);
        
        $noRealizado = ((strcasecmp($texto, $STR_NO_REALIZADO) == 0) ? 0 : null);
        
        $result = ($realizado == 1) ? 1 : $noRealizado;

        return $result;
    }
    
    /* Obtiene el array de Ids que fueron encontrados para el chofer logueado*/
    private function fncGetArrayIds()
    {
        $data = $this->fncGetSelectQuery();
        
        $idsArray = array();
        foreach ($data as $dato)
        {
            $idsArray[] = $dato['id'];
        }
        return $idsArray;
    }

    /* Crea un query directamente en la base de datos, donde une las diferentes tablas 
     * para saber las rutas del chofer logueado y que paquetes debe de entregar */
    private function fncGetSelectQuery()
    {
        $em = $this->getEntityManager();
        $ENTREGA = 2;
        $sqlSelect = "SELECT DISTINCT
                    tblactividachoferlog.id
                FROM
                        package_to_order p
                            INNER JOIN
                        m3_commerce_order ord ON p.order_id = ord.id
                            INNER JOIN
                        transactions trans ON trans.transaction_id = ord.id
                            INNER JOIN
                        routepoint_activity r ON trans.id = r.transaction_id
                            INNER JOIN
                        activity_type typ ON typ.id = r.activityType_id
                            INNER JOIN
                        route_points rp ON rp.id = r.routePoint_id
                            LEFT JOIN
                        activity_detail ad ON r.id = ad.routePointActivity_id
                            LEFT JOIN
                        points po ON rp.point_id = po.id
                            INNER JOIN
                        scheduled_route sr ON r.scheduledRoute_id = sr.id
                            INNER JOIN
                        tblpagos ON ord.id = tblpagos.intIdCompraVenta
                            INNER JOIN
                        tblactividachoferlog ON tblpagos.id = tblactividachoferlog.intIdPagos
                            AND tblactividachoferlog.intIdPuntoActividad = po.id
                WHERE
                    sr.driver_id  = ".$this->fncGetUsuario()->getId()."
                    and r.activityType_id = $ENTREGA
                    and ord.pointSeller_id is not null;";
        
        $conn = $em->getConnection()->getWrappedConnection();
        $stmt = $conn->prepare($sqlSelect);
        $stmt->execute();
        $result = DBUtil::getResultsetFromStatement($stmt, \PDO::FETCH_NAMED);
        
        return $result[0];
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

    private function mapResultToArray( $actividadChoferLog ) {
        $em = $this->getEntityManager();
        $resultActividadChoferLog = array();
        foreach ( $actividadChoferLog as $row ) 
        {
            $origen = ($row->getPago()->getCompraVenta()->getPointSeller() ? $row->getPago()->getCompraVenta()->getPointSeller()->getName() : "Sin definir");
            $puntoActividad = $this->fncActualizarPuntoActividad($row);

            $resultActividadChoferLog[] = array(
                'id' => $row->getId(), 
                'pago'=> $row->getPago(),
                'fecha' => $row->getPago()->getFecha()->format("d-m-Y"),
                'tipoConcepto' => $row->getPago()->getTipoConcepto()->getTipoConcepto(),
                'tipoDebito' => $row->getPago()->getTipoDebito()->getTipoDebito(),
                'actividadTipoId' => $row->getActividadTipo()->getId(),
                'actividadTipo' => $row->getActividadTipo()->getName(),
                'estatus' => ($row->getPago()->getEstatus()==1) ? "Pagado" : "Pendiente",
                'montoCreditos' => number_format($row->getPago()->getMontoCreditos(), 3),
                'montoMoneda' => number_format($row->getPago()->getMontoCompra(), 3),
                'puntoVentaOrigen' => $origen,
                'puntoVentaDestino' => $row->getPago()->getCompraVenta()->getPointBuyer()->getName(),
                'puntoActividad' => $puntoActividad,
                'comprador' => $row->getPago()->getCompraVenta()->getBuyer()->getCommercialName(),
                'compradorId' => $row->getPago()->getCompraVenta()->getBuyer()->getId(),
                'vendedor' => $row->getPago()->getCompraVenta()->getSeller()->getCommercialName(),
                'vendedorId' => $row->getPago()->getCompraVenta()->getSeller()->getId(),
                'compraId' => $row->getPago()->getCompraVenta()->getId(),
                'estatusActividad' => ($row->getEstatus()) ? $row->getEstatus() : 0,
                'estatusCheck' => ($row->getEstatus()) ? TRUE : FALSE

            );

        }
        
        return $resultActividadChoferLog;
    }
    
    private function fncGetChoferRuta()
    {
        $em = $this->getEntityManager('DefaultDb');
        //se crea el query que obtiene a los choferes que manejan las rutas
        $query = $em->createQueryBuilder()
                ->select('b.id compra, h.id rutePo, f.id scheduleRo, g.id tipo')
                ->from('DefaultDb_Entities_PackageToOrder', 'a')
                ->from('DefaultDb_Entities_M3CommerceOrder', 'b')
                ->from('DefaultDb_Entities_Transactions', 'c')
                ->from('DefaultDb_Entities_RoutePointActivity', 'd')
                ->from('DefaultDb_Entities_ScheduledRouteActivity', 'e')
                ->from('DefaultDb_Entities_ScheduledRoute', 'f')
                ->from('DefaultDb_Entities_ActivityType', 'g')
                ->from('DefaultDb_Entities_RoutePoint', 'h');
        
        $query->where('a.order = b.id')
              ->andWhere('b.id = c.transactionId')
              ->andWhere('c.id = d.transaction')
              ->andWhere('d.id = e.routePointActivity')
              ->andWhere('f.id = e.scheduledRoute')
              ->andWhere('g.id = d.activityType')
              ->andWhere('h.id = d.routePoint')
              ->andWhere('f.driver = :usuario')
              ->setParameter('usuario', $this->fncGetUsuario());
        return $query->getQuery()->getResult();
    }
    
    private function fncActualizarPuntoActividad($actividad)
    {
        $em = $this->getEntityManager();
        
        if($actividad->getPago()->getCompraVenta()->getPointSeller())
        {
            if($actividad->getPuntoActividad() == NULL)
            {
                $actividad->setPuntoActividad($actividad->getPago()->getCompraVenta()->getPointSeller());
                $em->persist($actividad);
                $em->flush();       
            } 
        }
        $puntoActividad = $actividad->getPuntoActividad();
        return ($puntoActividad) ? "[".$puntoActividad->getCode()."] ".$puntoActividad->getName() : "Sin definir" ;
    }

    public function fncSave($parametros) 
    {
        $REALIZADO = 1;
        $SIN_REALIZAR = 0;
        $em = $this->getEntityManager();
        
        $actividadChoferLog = $em->getRepository('DefaultDb_Entities_ActividaChoferLog')->find( $parametros["id"] );
        $estatusPaquete =  ($actividadChoferLog->getEstatus() == $REALIZADO) ? $REALIZADO : $SIN_REALIZAR;
        
        if($estatusPaquete === $REALIZADO)
        {
            $result["error"] = "La actividad ya esta realizada";
        }
        else 
        {
            $validar = $this->fncValidarSaldos($actividadChoferLog);
            if($validar == FALSE)
            {
                $result["error"] = "El cliente no cuenta con créditos suficientes";
            }
            else
            {
                $actividadChoferLog->setEstatus(1);
                $actividadChoferLog->setUsuario($this->fncGetUsuario());
                $em->persist( $actividadChoferLog );
                $em->flush();
                $result["exito"] = $actividadChoferLog;
            }
        }
        return $result;
    }
    
    private function fncValidarSaldos($actividadChoferLog) 
    {
        $em = $this->getEntityManager();
        /*PCE = Pago Contra Entrega*/
        $PCE_CONGELANDO_CREDITOS = 1;
        $PCE_CREDITOS_NEGATIVOS = 2;
        $PAGADO = 1;
        $VENTA = 3;
        $result = TRUE;
        
        $venta = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find( $VENTA );
        $comprador = $actividadChoferLog->getPago()->getCompraVenta()->getBuyer();
        $vendedor = $actividadChoferLog->getPago()->getCompraVenta()->getSeller();
        $monto = $actividadChoferLog->getPago()->getMontoCreditos();
        $pagoCompra =  $actividadChoferLog->getPago();
        $pagoVenta =  $em->getRepository('DefaultDb_Entities_Pagos')->findOneBy(array(
                        'compraVenta' => $actividadChoferLog->getPago()->getCompraVenta(),
                        'tipoConcepto' => $venta));
        $compraVenta =  $actividadChoferLog->getPago()->getCompraVenta();
        $balanceCompra =  $em->getRepository('DefaultDb_Entities_BalanceGeneral')->findOneBy(array(
                        'pagos' => $pagoCompra));
        $balanceVenta =  $em->getRepository('DefaultDb_Entities_BalanceGeneral')->findOneBy(array(
                        'pagos' => $pagoVenta));
        
        
        if( $actividadChoferLog->getPago()->getTipoDebito()->getId() == $PCE_CONGELANDO_CREDITOS)
        {
      
            $comprador->setCreditoCongelado($comprador->getCreditoCongelado() -  $monto);
            $vendedor->setCredito($vendedor->getCredito() +  $monto);

        }       
        if( $actividadChoferLog->getPago()->getTipoDebito()->getId() == $PCE_CREDITOS_NEGATIVOS)
        {
            if($comprador->getCredito() < $monto)
            {
                $result = FALSE;
            }
            else
            {
                $comprador->setCreditoNegativo($comprador->getCreditoNegativo() -  $monto);
                $vendedor->setCredito($vendedor->getCredito() +  $monto);
                
            }
        }
        
        $pagoCompra->setEstatus($PAGADO);
        $pagoVenta->setEstatus($PAGADO);
        
        $compraVenta->setPaymentStatus($PAGADO);
        
        $balanceCompra->setEstatus($PAGADO);
        $balanceCompra->setEgresos(floatval($monto));
        
        $balanceVenta->setEstatus($PAGADO);
        $balanceVenta->setIngresos(floatval($monto));
        $balanceVenta->setBalance($balanceVenta->getBalance() + $monto);
                
        $em->persist( $comprador );
        $em->persist( $vendedor );
        $em->persist( $pagoCompra );
        $em->persist( $pagoVenta );
        $em->persist( $compraVenta );
        $em->persist( $balanceCompra );
        $em->persist( $balanceVenta );
        $em->flush();
        return $result;
    }
    
    public function fncAgregarActividadChoferLog($pago)
    {
        /*PCE = Pago Contra Entrega*/
        $PCE_CONGELANDO_CREDITOS = 1;
        $PCE_CREDITOS_NEGATIVOS = 2;
        
        $em = $this->getEntityManager();
        
        $origen = ($pago->getCompraVenta()->getPointBuyer()) ? $pago->getCompraVenta()->getPointBuyer() : NULL; 
               
        if( $pago->getTipoDebito()->getId() == $PCE_CONGELANDO_CREDITOS || $pago->getTipoDebito()->getId() == $PCE_CREDITOS_NEGATIVOS)
        {
            $actividadTipo = $em->getRepository('DefaultDb_Entities_ActivityType')->find(4);
        }
        else 
        { 
            $actividadTipo = $em->getRepository('DefaultDb_Entities_ActivityType')->find(2);
        }
        
        $actividad = new DefaultDb_Entities_ActividaChoferLog();
        $actividad->setFecha(new DateTime());
        $actividad->setPago($pago);
        $actividad->setActividadTipo($actividadTipo);
        $actividad->setPuntoActividad($origen);

        $em->persist($actividad);
        $em->flush();       
                
        return $actividad;
    }
    
    //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $actividadChoferLog = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $actividadChoferLog as $row ) 
        {
            $origen = ($row->getPago()->getCompraVenta()->getPointSeller() ? $row->getPago()->getCompraVenta()->getPointSeller()->getName() : "Sin definir");
            $puntoActividad = $this->fncActualizarPuntoActividad($row);

            $resultActividadChoferLog[] = array
            ( 
                'fecha' => $row->getPago()->getFecha()->format("d-m-Y"),
                'tipoDebito' => $row->getPago()->getTipoDebito()->getTipoDebito(),
                'tipoConcepto' => $row->getPago()->getTipoConcepto()->getTipoConcepto(),
                'estatus' => ($row->getPago()->getEstatus()==1) ? "Pagado" : "Pendiente",
                'montoCreditos' => number_format($row->getPago()->getMontoCreditos(), 3),
                'puntoActividad' => $puntoActividad,
                'compraId' => $row->getPago()->getCompraVenta()->getId(),
                'estatusActividad' => ($row->getEstatus()) ? "Realizado" : "Sin Realizar"
                
            );
        }
        
        return $resultActividadChoferLog;  
    }

}
