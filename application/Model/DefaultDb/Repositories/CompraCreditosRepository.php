<?php

use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_CompraCreditosRepository extends EntityRepository {
    
    private $compraTipo = 0;
    private $pago = null;
 
    /*
     * Cambiar texto de acuerdo el nombre de los campos en el catálogo de Estatus
     */
    const ESTATUS_APROBADO_STRING = 'Aprobado';
    const ESTATUS_PENDIENTE_STRING = 'Pendiente';
    
    private $ESTATUS_APROBADO = 0;
    private $ESTATUS_PENDIENTE = 0;
    
    


    public function getCompraCreditosListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultCompraCreditos = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());
        
        $result[0] = $resultCompraCreditos;

        $result[1][0] = array(
            'records' => $total_rows, 
            'page' => $parametros["pagina"], 
            'totalpages' => $total_pages
        );
        
        return $result;  
    }
    
    
    private function fncGetUsuario()
    {
        $userSessionId = Model3_Auth::getCredentials('id');
        return $userSessionId;
    }
    
    private function fncGetRole()
    {
        $role = Model3_Auth::getCredentials('role');
        $ROLE_CONTROLADOR = 7;
        
        $esControlador = TRUE;
        
        if( $role != $ROLE_CONTROLADOR ) 
        {
            $esControlador = FALSE;
        } 
        
        return $esControlador;
    }
    
    private function fncGetRoleCliente()
    {
        $role = Model3_Auth::getCredentials('role');
        $ROLE_CLIENTE = 3;
        
        $esCliente= TRUE;
        
        if( $role != $ROLE_CLIENTE ) 
        {
            $esCliente = FALSE;
        } 
        
        return $esCliente;
    }
    
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_CompraCreditos')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query ->innerJoin('m.cliente', 'c')
                ->innerJoin('m.tipoPago', 't')
                ->innerJoin('m.cuenta', 'a')
                ->innerJoin('a.moneda', 'b')
                ->innerJoin('a.banco', 'd')
                ->innerJoin('m.estatus', 'e');
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);       
        
        if( $this->fncGetRole() == FALSE ) 
        {
            if($this->fncGetRoleCliente() == TRUE)
            {
                $query->where('m.cliente = :usuario'); 
            }
            else
            {
                $query->where('m.usuario = :usuario'); 
            }
        } 

        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            if( $this->fncGetRole() == FALSE ) 
            { 
                $query->andWhere('m.fecha LIKE :fecha');            
            }
            else 
            {   
                $query->where('m.fecha LIKE :fecha');
            }
            
            $query->andWhere('c.commercialName LIKE :cliente')
            ->andWhere('t.tipoPago LIKE :tipoPago')
            ->andWhere('m.montoCompra LIKE :montoCompra')
            ->andWhere('b.moneda LIKE :moneda')
            ->andWhere('d.name LIKE :banco')                
            ->andWhere('a.cuenta LIKE :cuenta')         
            ->andWhere('m.creditos LIKE :creditos')
            ->andWhere('m.referencia LIKE :referencia')
            ->andWhere('e.estatus LIKE :estatus')
            ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
            ->setParameter('cliente', '%'.$parametros["filtro"]["cliente"].'%')
            ->setParameter('tipoPago', '%'.$parametros["filtro"]["tipoPago"].'%')
            ->setParameter('montoCompra', '%'.$parametros["filtro"]["montoCompra"].'%')
            ->setParameter('moneda', '%'.$parametros["filtro"]["moneda"].'%')
            ->setParameter('banco', '%'.$parametros["filtro"]["banco"].'%')
            ->setParameter('cuenta', '%'.$parametros["filtro"]["cuenta"].'%')
            ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%')
            ->setParameter('referencia', '%'.$parametros["filtro"]["referencia"].'%')
            ->setParameter('estatus', '%'.$parametros["filtro"]["estatus"].'%');
        }  
        if( $this->fncGetRole() == FALSE ) { $query->setParameter('usuario', $this->fncGetUsuario()); } 
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
    
    private function mapResultToArray( $compraCreditos ) {
        
        $resultcompraCreditos = array();

        foreach ( $compraCreditos as $row ) {
            
            $cuenta = $row->getCuenta();
            $cliente = $row->getCliente();
            $tipoPago = $row->getTipoPago();
            $estatus = $row->getEstatus();
            
            $moneda = $cuenta ? $cuenta->getMoneda() : null;
            $banco = $cuenta ? $cuenta->getBanco() : null;
            
            $categoria = $cliente ? $cliente->getCategory() : null;
            
            $commercialName = $cliente ? $cliente->getCommercialName(): '';
            $clienteId = $cliente ? $cliente->getId(): '';
            
            $nombreCategoria = $categoria ? $categoria->getName() : '';
            $categoriaId = $categoria ? $categoria->getId() : '';
            
            $nombreTipoPago = $tipoPago ? $tipoPago->getTipoPago(): '';
            $tipoPagoId = $tipoPago ? $tipoPago->getId(): '';
            
            $nombreMoneda = $moneda ? $moneda->getMoneda(): '';
            $monedaId = $moneda ? $moneda->getId(): '';
            $currencyCode = ($moneda->getCurrencyCode()) ? $moneda->getCurrencyCode() : "MXN";
            
            $nombreBanco = $banco ? $banco->getName(): '';
            $bancoId = $banco ? $banco->getId(): '';
            
            $nombreEstatus = $estatus ? $estatus->getEstatus(): '';
            $estatusId = $estatus ? $estatus->getId(): '';
            
            
            $nombreCuenta = $cuenta ? $cuenta->getCuenta() : '';
            $cuentaId = $cuenta ? $cuenta->getId() : '';
            
            $creditos = floatval( $row->getCreditos() );
            $fecha = null;
            if($row->getFecha() != null){
                $fecha = $row->getFecha()->format('d-m-Y');
            }

            $resultcompraCreditos[ ] = array(
                'id' => $row->getId(), 
                'usuario' => $row->getUsuario(), 
                'tipoPago' => $nombreTipoPago, 
                'tipoPagoId' => $tipoPagoId, 
                'montoCompra' => $row->getMontoCompra(), 
                'fecha' => $fecha,  
                'moneda' => $nombreMoneda,
                'monedaId' => $monedaId,
                'banco' => $nombreBanco,
                'bancoId' => $bancoId,
                'referencia' => $row->getReferencia(), 
                'cuenta' => $nombreCuenta, 
                'cuentaId' => $cuentaId, 
                'estatus' => $nombreEstatus,
                'estatusId' => $estatusId,
                'cliente' => $commercialName,
                'clienteId' => $clienteId,
                'categoria' => $nombreCategoria,
                'categoriaId' => $categoriaId,
                'creditos' => number_format( $creditos, 3 ),
                'currencyCode' => $currencyCode,
                'path' => $row->getPath(),
                'comentario' => ($row->getComentario()) ? $row->getComentario() : "---"
            );

        }
        
        return $resultcompraCreditos;
    }

    public function getCompraCreditos($id) {
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_CompraCreditos m WHERE m.id = :id ";

        $query = $em->createQuery($dql);
        $query->setParameter('id', $id);

        $compraCreditos = $query->getFirstResult();
        return $compraCreditos;
    }
    
    public function addCompraCreditos( $compraJSON ) 
    {
        $compraCreditos = $this->fncAgregarCompraCreditos($compraJSON); 
        return $compraCreditos->getId();
    }
    
    private function fncEditaEstatus($tipo, $estatusAnterior, $compraCreditos)
    {
//        $ESTATUS_ANTERIOR = 2;
//        $ESTATUS_APROBADO = 1;
        $this->initEstatus();

        $em = $this->getEntityManager("DefaultDb");
        $cliente = $compraCreditos->getCliente();
        
        if($tipo==0)
        {
            $idEstatusAnterior = $estatusAnterior ? $estatusAnterior->getId() : 0;
            if($compraCreditos->getEstatus()->getId() == $this->ESTATUS_APROBADO && $idEstatusAnterior == $this->ESTATUS_PENDIENTE)
            {
                $cliente->setCredito($cliente->getCredito() + $compraCreditos->getCreditos());
                $em->persist($cliente);
                $this->addAdministracionLog($compraCreditos, $cliente->getId());
                $this->fncEditaPago($compraCreditos);
                $this->editBalanceGeneral($compraCreditos);   
            }
        }
        else
        {
            if($compraCreditos->getEstatus()->getId() == $this->ESTATUS_APROBADO)
            {
                $cliente->setCredito($cliente->getCredito() + $compraCreditos->getCreditos());
                $em->persist($cliente);
                $this->addAdministracionLog($compraCreditos, $cliente->getId());
                $this->fncEditaPago($compraCreditos);
                $this->editBalanceGeneral($compraCreditos);
            }
            
        }
        
        $em->flush();
    }

    private function fncAgregarCompraCreditos($compraJSON)
    {
        $em = $this->getEntityManager();
        $cuenta = $em->getRepository('DefaultDb_Entities_Cuentas')->find( $compraJSON["cuenta"] );
        $cliente = $em->getRepository('DefaultDb_Entities_User')->find( $compraJSON["cliente"] );
        $tipoPago = $em->getRepository('DefaultDb_Entities_TipoPagos')->find( $compraJSON["tipoPago"] );
        $estatus = $em->getRepository('DefaultDb_Entities_Estatus')->find( $compraJSON["estatus"] );

        $moneda = $cuenta->getMoneda();
        $creditos = $this->calculaCreditos( $moneda, $compraJSON["montoCompra"] );
        $compraCreditos = $this->getObjectToSave( $compraJSON["id"] );
        $comentario = isset($compraJSON["comentario"]) ? $compraJSON["comentario"] : "";
        $estatusAnterior = $compraCreditos->getEstatus();
        $fecha = ($compraCreditos->getFecha() == null) ? new DateTime() : $compraCreditos->getFecha(); 
        $compraCreditos->setUsuario( $compraJSON["usuario"] );
        $compraCreditos->setMontoCompra( $compraJSON["montoCompra"] );
        $compraJSON["fecha"] = 
        $compraCreditos->setFecha( $fecha );
        $compraCreditos->setReferencia( $compraJSON["referencia"] );
        $compraCreditos->setTipoPago( $tipoPago );
        $compraCreditos->setCreditos( $creditos );
        $compraCreditos->setCuenta( $cuenta );
        $compraCreditos->setEstatus( $estatus );
        $compraCreditos->setCliente( $cliente );
        $compraCreditos->setComentario( $comentario );
        

        $em->persist( $compraCreditos );
        $em->flush();
        
        //verifica si es compra nueva (1) o ya existe (0)
        if($this->compraTipo == 0)
        {
            $this->fncEditaEstatus(0, $estatusAnterior, $compraCreditos);
        }
        if($this->compraTipo == 1)
        {
            $this->fncGuardarPago($compraCreditos, false);
            $this->fncEditaEstatus(1, $estatusAnterior, $compraCreditos);
        }
        return $compraCreditos;
    }


    private function fncGuardarPago($orden, $estado) 
    {
        $em = $this->getEntityManager('DefaultDb');

        $pagosRepo = $em->getRepository('DefaultDb_Entities_Pagos');

        $this->pago = $pagosRepo->fncGuardarPagoCreditos($orden, $estado);
    }
    
    private function fncEditaPago($compraCredito)
    {        
        $em = $this->getEntityManager("DefaultDb"); 
        
        $pago = $em->getRepository('DefaultDb_Entities_Pagos')->findOneBy(array("compraCreditos" => $compraCredito));
        $pago->setEstatus(1);
        $pago->setTimestamp(new DateTime());
        
        $em->persist( $pago );
        $em->flush();
        
        return $pago;
        
    }
    
    
    public function addAdministracionLog($compraCreditos, $usuario)
    {
        $em = $this->getEntityManager(); 
        
        $administracionJSON = array();
        
        $administracionJSON["idConcepto"] =  $compraCreditos->getId();  
        $administracionJSON["tipoConcepto"] =  2; 
        $administracionJSON["cliente"] = $usuario;
        $administracionJSON["concepto"] = "Compra de Créditos";
        $administracionJSON["fecha"] = $compraCreditos->getFecha();
        $administracionJSON["referencia"] = $compraCreditos->getReferencia();
        $administracionJSON["banco"] = $compraCreditos->getCuenta()->getBanco()->getName();
        $administracionJSON["tipoPago"] = $compraCreditos->getTipoPago()->getTipoPago();
        $administracionJSON["monto"] = $compraCreditos->getMontoCompra();
        $administracionJSON["creditos"] = $compraCreditos->getCreditos();
        $administracionJSON["transferencia"] = NULL;
        $administracionJSON["compraCreditos"] = $compraCreditos;
       
        $administracionLogRepo = $em->getRepository('DefaultDb_Entities_AdministracionLogCliente');

        $administracionLogRepo->addAdministracionLogCliente($administracionJSON);     
    }
    
    public function addBalanceGeneral($id, $creditos)
    {
        $role = Model3_Auth::getCredentials( 'role' );
        $ROLE_CONTROLADOR = 7;
        
        $em = $this->getEntityManager(); 
        
        if( $role != $ROLE_CONTROLADOR ) 
        {
            $balanceGeneralRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
            
            $balanceGeneralRepo->guardarBalanceCompraCreditos($id, $creditos);
        }      
    }
    
    public function editBalanceGeneral($orden)
    {
        //$COMPRA_CREDITOS = 8;
        
        $em = $this->getEntityManager("DefaultDb"); 
        
        $pagos = $em->getRepository('DefaultDb_Entities_Pagos')->findBy(array('compraCreditos'  => $orden));

        foreach ( $pagos as $row ) 
        {
            $balance = $em->getRepository('DefaultDb_Entities_BalanceGeneral')->findBy(array('pagos'  => $row));
            
            foreach ( $balance as $row1 ) 
            {
                $row1->setEstatus(1);
                $row1->setCreditos($orden->getCliente()->getCredito()-$row1->getMonto());
                $row1->setIngresos($row1->getMonto());
                $row1->setBalance($orden->getCliente()->getCredito());
                $row1->setTimestamp(new DateTime());
                $em->persist( $row1 );
                $em->flush();
            }
            

        }
    }
    
    
    
    private function calculaCreditos( $moneda, $montoCompra ) {
        
        $em = $this->getEntityManager();
        
        $conversion = $em->getRepository('DefaultDb_Entities_Conversion')
                ->createQueryBuilder( 'c' )
                ->where( "c.moneda = :moneda AND c.fecha <= :today " )
                ->setParameter( "moneda", $moneda )
                ->setParameter( "today", new DateTime() )
                ->addOrderBy('c.fecha', 'DESC')
                ->setMaxResults( 1 )
                ->getQuery()
                ->getOneOrNullResult();
        
        $creditos = 0;
        
        if( $conversion ) {
            $precioCompra = floatval($conversion->getCompra());
            $cantidadCreditos = floatval($conversion->getCreditos());
            
            $creditos = ( $montoCompra / $precioCompra ) * $cantidadCreditos;
        }
        
        return $creditos;
    }
    
    private function getObjectToSave( $id ) {
        
        $compraCreditos = null;
        
        if ($id == null) {
            $compraCreditos = new DefaultDb_Entities_CompraCreditos();
            $this->compraTipo = 1;
            
        } else {
            $compraCreditos = $this->find($id);
             $this->compraTipo = 0;
        }
        return $compraCreditos;
    }
    
    public function deleteCompraCreditos($id) {
        $em = $this->getEntityManager();
        if ($id == null) {
            return;
        } else {
            $compraCreditos = $this->find($id);
            $em->remove($compraCreditos);
            $em->flush();
            return;
        }
    }
    
     //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $comprasCreditos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $comprasCreditos as $row ) 
        {
            $fecha = null;
            if($row->getFecha()){
                $fecha = $row->getFecha()->format("d-m-Y");
            }
            $resultCompraCreditos[] = array
            ( 
                'fecha' => $fecha,      
                'cliente' => $row->getCliente()->getCommercialName(),
                'tipoPago' => $row->getTipoPago()->getTipoPago(), 
                'montoCompra' => number_format($row->getMontoCompra(),3), 
                'moneda' => $row->getCuenta()->getMoneda()->getMoneda(),
                'banco' => $row->getCuenta()->getBanco()->getName(),
                'cuenta' => $row->getCuenta()->getCuenta(), 
                'creditos' => number_format($row->getCreditos(),3),
                'referencia' => $row->getReferencia(), 
                'estatus' => $row->getEstatus()->getEstatus()
            );
        }
        
        return $resultCompraCreditos;  
    }
    
    public function fncGuardarPaypalCompra($datosPaypal, $baseUrl)
    {
//        $ESTATUS_APROBADO = 1;
        $this->initEstatus();
        $compraJSON = array();

        $compraJSON["id"] = null;
        $compraJSON["usuario"]  = $this->fncGetUsuario();
        $compraJSON["tipoPago"] = $datosPaypal["tipoPago"];
        $compraJSON["montoCompra"] = $datosPaypal["montoCompra"];
        $compraJSON["fecha"] = new DateTime();
        $compraJSON["referencia"] = $datosPaypal["referencia"];
        $compraJSON["cuenta"] = $datosPaypal["cuenta"];
        $compraJSON["estatus"] = $this->ESTATUS_APROBADO;
        $compraJSON["cliente"] = $datosPaypal["cliente"];
        
        $this->fncAgregarCompraCreditos($compraJSON);  
        $this->fncGuardarPaypalPago($this->pago, $datosPaypal);
        
        $urlReturn = $baseUrl.'/App/#!/compraCreditos';
        header('Location:'.$urlReturn);    
    }
    
    private function fncGuardarPaypalPago($pago, $paypal) 
    {
        $em = $this->getEntityManager('DefaultDb');

        $paypalRepo = $em->getRepository('DefaultDb_Entities_Paypal');

        $paypalRepo->fncGuardarPaypalPago($pago, $paypal);
    }
    
    
    public function fncGuardarTerminalCompra($datosTerminal)
    {
//        $ESTATUS_APROBADO = 1;
        $this->initEstatus();
        $compraJSON = array();

        $compraJSON["id"] = null;
        $compraJSON["usuario"]  = $this->fncGetUsuario();
        $compraJSON["tipoPago"] = $datosTerminal["compra"]["tipoPago"]['id'];
        $compraJSON["montoCompra"] = $datosTerminal["compra"]["montoCompra"];
        $compraJSON["fecha"] = new DateTime();
        $compraJSON["referencia"] = $datosTerminal["compra"]["referencia"];
        $compraJSON["cuenta"] = $datosTerminal["compra"]["cuenta"]['id'];
        $compraJSON["estatus"] = $this->ESTATUS_APROBADO;
        $compraJSON["cliente"] = $datosTerminal["compra"]["cliente"]['id'];
        
        $this->fncAgregarCompraCreditos($compraJSON);  
        $pagoTerminal = $this->fncGuardarTerminalPago($this->pago, $datosTerminal);
        return $pagoTerminal;
    }
    
    private function fncGuardarTerminalPago($pago, $datosTerminal) 
    {
        $em = $this->getEntityManager('DefaultDb');

        $terminalRepo = $em->getRepository('DefaultDb_Entities_TerminalBancaria');

        $pagoTerminal = $terminalRepo->fncGuardarTerminalBancariaPago($pago, $datosTerminal);
        return $pagoTerminal;
    }
    
    /**
     * Inicializa las variables de estatus
     */
    function initEstatus() {
        $this->ESTATUS_APROBADO = $this->getStatusId(self::ESTATUS_APROBADO_STRING);
        $this->ESTATUS_PENDIENTE = $this->getStatusId(self::ESTATUS_PENDIENTE_STRING);
    }
    
    /**
     * Obtiene los id's de los estatus de la tabla tblestatus
     * @param string $chrStatus Nombre del estatus a buscar
     * @return int Retorna el id del estatus, 0 si no encuentra el estatus
     */
    private function getStatusId($chrStatus){
        $em = $this->getEntityManager('DefaultDb');
        $params = array();
        $response ;
        try 
        {
            $response = $em->getRepository('DefaultDb_Entities_Estatus')
                    ->createQueryBuilder('m')
                    ->select('m.id')
                    ->where("m.estatus = '$chrStatus'")
                    ->getQuery()->getOneOrNullResult();
        }
        catch (Exception $exc) 
        {
           $this->logAndResolveException($exc,$params);
        }
        return !is_null($response) ? $response['id'] : 0;
    }

}
