<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query\ResultSetMapping;

class DefaultDb_Repositories_AdministracionLogClienteRepository extends EntityRepository {
    
    const ROLE_ADMIN_CODE = 1;
    const ROLE_CONTROLLER_CODE = 7;

    public function getAdministracionLogClienteListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);

        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultAdministracionLogCliente = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());
      

        $result[0] = $resultAdministracionLogCliente;

        $result[1][0] = array(
            'records' => $total_rows, 
            'page' => $parametros["pagina"], 
            'totalpages' => $total_pages
        );
        
        return $result;  
    }
    
    private function fncGetUsuario()
    {
        $userSessionId = $_SESSION['__M3']['MasDistribucion']['Credentials']['id']; 
        return $userSessionId;
    }
    
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_AdministracionLogCliente')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.cliente', 'c');
 
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]); 
        if($_SESSION['__M3']['MasDistribucion']['Credentials']['role'] != self::ROLE_CONTROLLER_CODE){
            $query->where("m.cliente = :usuario");
        }
        
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->andWhere('m.fecha LIKE :fecha')
            ->andWhere('m.referencia LIKE :referencia')
            ->andWhere('m.banco LIKE :banco')
            ->andWhere('m.concepto LIKE :concepto')
            ->andWhere('m.creditos LIKE :creditos')
            ->andWhere('m.tipoPago LIKE :tipoPago')
            ->andWhere('m.monto LIKE :monto')
            ->andWhere('m.saldo LIKE :saldo')    
            ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
            ->setParameter('referencia', '%'.$parametros["filtro"]["referencia"].'%')
            ->setParameter('banco', '%'.$parametros["filtro"]["banco"].'%')
            ->setParameter('concepto', '%'.$parametros["filtro"]["concepto"].'%')
            ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%')
            ->setParameter('tipoPago', '%'.$parametros["filtro"]["tipoPago"].'%')
            ->setParameter('monto', '%'.$parametros["filtro"]["monto"].'%')
            ->setParameter('saldo', '%'.$parametros["filtro"]["saldo"].'%');
        }  
        
        if($_SESSION['__M3']['MasDistribucion']['Credentials']['role'] != self::ROLE_CONTROLLER_CODE){
            $query->setParameter('usuario', $this->fncGetUsuario()); 
        }
        
        
 
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
    
//    private function fncObtenerQuery($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//
//        if( $parametros["filtro"] != NULL ) 
//        {
//            $query = $this->fncObtenerQueryFiltro($parametros, 1);
//        } 
//        else
//        {
//            $query = $em->createQuery($this->fncObtenerJoin($parametros)->getDQL());
//            $query->setParameter('usuario', $this->fncGetUsuario()); 
//        }
//        
//        $query->setMaxResults($parametros["registrosPorPagina"]);
//        $query->setFirstResult( $offset );
//        
//        return $query;
//    }
//    
//    private function fncObtenerJoin($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        $query = $em->createQueryBuilder();
//        
//        $query->select('m')
//                ->from('DefaultDb_Entities_AdministracionLogCliente', 'm')
//                ->innerJoin('m.cliente', 'c')
//                ->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"])
//                ->where("m.cliente = :usuario");  
//        
//        return $query;
//    }
//    
//    private function fncObtenerQueryFiltro($parametros, $case)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//        
//        $datos = $this->fncObtenerQueryFiltroSelect($parametros, $case);
//                
//        $query = $em->createQuery($datos->getDQL())
//                ->setParameter('fecha', '%'.substr(str_replace('T', ' ', $parametros["filtro"]["fecha"]),0,10).'%')
//                ->setParameter('referencia', '%'.$parametros["filtro"]["referencia"].'%')
//                ->setParameter('banco', '%'.$parametros["filtro"]["banco"].'%')
//                ->setParameter('concepto', '%'.$parametros["filtro"]["concepto"].'%')
//                ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%')
//                ->setParameter('tipoPago', '%'.$parametros["filtro"]["tipoPago"].'%')
//                ->setParameter('monto', '%'.$parametros["filtro"]["monto"].'%')
//                ->setParameter('saldo', '%'.$parametros["filtro"]["saldo"].'%')
//                ->setParameter('usuario', $this->fncGetUsuario());
//        
//        return $query;
//    }
//    
//    private function fncObtenerQueryFiltroSelect($parametros, $case)
//    {
//        $em = $this->getEntityManager('DefaultDb'); 
//        $query = $em->createQueryBuilder();       
//        
//        if($case == 1) { $query->select('m'); }
//        else { $query->select('count(m.id)'); }
//        
//        $query->from('DefaultDb_Entities_AdministracionLogCliente', 'm')
//        ->innerJoin('m.cliente', 'c')
//        ->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"])
//        ->where("m.cliente = :usuario")
//        ->andWhere('m.fecha LIKE :fecha')
//        ->andWhere('m.referencia LIKE :referencia')
//        ->andWhere('m.banco LIKE :banco')
//        ->andWhere('m.concepto LIKE :concepto')
//        ->andWhere('m.creditos LIKE :creditos')
//        ->andWhere('m.tipoPago LIKE :tipoPago')
//        ->andWhere('m.monto LIKE :monto')
//        ->andWhere('m.saldo LIKE :saldo');
//        
//        return $query;
//    }
//    
//    public function getTotalRows($parametros ) 
//    {
//        $em = $this->getEntityManager();
//        $qb = $em->createQueryBuilder();
//        
//        if( $parametros["filtro"] != NULL ) 
//        {
//            $query = $this->fncObtenerQueryFiltro($parametros, 2);
//            $total_rows = $query->getSingleScalarResult();
//        } 
//        else
//        {
//            $qb->select('count(m.id)')
//            ->from('DefaultDb_Entities_AdministracionLogCliente', 'm')
//            ->where("m.cliente = :usuario")
//            ->setParameter('usuario', $this->fncGetUsuario());   
//            $total_rows = $qb->getQuery()->getSingleScalarResult();
//        }
//        return $total_rows;
//    }
    
    
    
    
    private function mapResultToArray( $administracionLog ) 
    {
        $resultAdministracionLog = array();
        
        foreach ( $administracionLog as $row ) 
        {            
            $fecha = null;
            if($row->getFecha()){
                $fecha = $row->getFecha()->format("d-m-Y");
            }
            $resultAdministracionLog[ ] = array
            (
                'id' => $row->getId(), 
                'referencia' => $row->getReferencia(),
                'fecha' => $fecha,
                'monto' => number_format($row->getMonto(),3),
                'banco' => $row->getBanco(),
                'creditos' => number_format($row->getCreditos(),3),
                'tipoPago' => $row->getTipoPago(),
                'concepto' => $row->getConcepto(),
                'saldo' => number_format($row->getSaldo(), 3)
            );

        }
        return $resultAdministracionLog;
    }

    public function addAdministracionLogCliente($AdministracionJSON)
    {		
        $em = $this->getEntityManager();		

        $cliente = $em->getRepository('DefaultDb_Entities_User')->find( $AdministracionJSON["cliente"] );
        $tipoConcepto = $em->getRepository('DefaultDb_Entities_TipoConcepto')->find( $AdministracionJSON["tipoConcepto"] );

        $administracionLog = new DefaultDb_Entities_AdministracionLogCliente();
       
        $administracionLog->setIdConcepto($AdministracionJSON["idConcepto"]);
        $administracionLog->setTipoConcepto($tipoConcepto);
        $administracionLog->setConcepto($AdministracionJSON["concepto"]);
        $administracionLog->setSaldo($cliente->getCredito());
        $administracionLog->setCliente($cliente);
        $administracionLog->setFecha($AdministracionJSON["fecha"]);
        $administracionLog->setReferencia($AdministracionJSON["referencia"]);
        $administracionLog->setBanco($AdministracionJSON["banco"]);
        $administracionLog->setTipoPago($AdministracionJSON["tipoPago"]);
        $administracionLog->setMonto($AdministracionJSON["monto"]);
        $administracionLog->setCreditos($AdministracionJSON["creditos"]);
        $administracionLog->setTransferencia($AdministracionJSON["transferencia"]);
        $administracionLog->setCompraCreditos($AdministracionJSON["compraCreditos"]);

        $em->persist($administracionLog);
        $em->flush(); 
    }
    
    //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $administracionLogCliente = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $administracionLogCliente as $row ) 
        {
            $fecha = null;
            if($row->getFecha()){
                $fecha = $row->getFecha()->format("d-m-Y");
            }
            $resultAdministracionLogCliente[] = array
            ( 
                'fecha' => $fecha,
                'referencia' => $row->getReferencia(),
                'banco' => $row->getBanco(),
                'concepto' => $row->getConcepto(),
                'creditos' => number_format($row->getCreditos(),3),
                'tipoPago' => $row->getTipoPago(),
                'monto' => number_format($row->getMonto(),3),
                'saldo' => number_format($row->getSaldo(), 3)
            );
        }
        
        return $resultAdministracionLogCliente;  
    }


}
