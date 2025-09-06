<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_CuentasRepository extends EntityRepository
{
    
    public function getCuentasListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultCuentas = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());

        $result[0] = $resultCuentas;

        $result[1][0] = array(
            'records' => $total_rows, 
            'page' => $parametros["pagina"], 
            'totalpages' => $total_pages
        );
        
        return $result;  
    }
    
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_Cuentas')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.moneda', 'a')
              ->innerJoin('m.pais', 'b')
              ->innerJoin('m.banco', 'c');
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.numeroCuenta LIKE :numeroCuenta')
                ->andWhere('m.cuenta LIKE :cuenta')
                ->andWhere('m.clabeInterbancaria LIKE :clabeInterbancaria')
                ->andWhere('a.moneda LIKE :moneda')
                ->andWhere('m.tipoOperador LIKE :tipoOperador')
                ->andWhere('b.nombre LIKE :pais')
                ->andWhere('c.name LIKE :banco')
                ->andWhere('m.estado LIKE :estado')
                ->setParameter('numeroCuenta', '%'.$parametros["filtro"]["numeroCuenta"].'%')
                ->setParameter('cuenta', '%'.$parametros["filtro"]["cuenta"].'%')
                ->setParameter('clabeInterbancaria', '%'.$parametros["filtro"]["clabeInterbancaria"].'%')
                ->setParameter('moneda', '%'.$parametros["filtro"]["moneda"].'%')
                ->setParameter('tipoOperador', '%'.$parametros["filtro"]["tipoOperador"].'%')
                ->setParameter('pais', '%'.$parametros["filtro"]["pais"].'%')
                ->setParameter('banco', '%'.$parametros["filtro"]["banco"].'%')
                ->setParameter('estado', $parametros["filtro"]["estado"].'%');
        }  
        
        return $query;
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
    
//    private function fncObtenerJoin($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        $query = $em->createQueryBuilder();
//        
//        $query->select('m, a, b, c')
//            ->from('DefaultDb_Entities_Cuentas', 'm')
//            ->innerJoin('m.moneda', 'a')
//            ->innerJoin('m.pais', 'b')
//            ->innerJoin('m.banco', 'c')
//            ->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);
//        return $query;
//    }
//    
//    private function fncObtenerQuery($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//        
//        $query = $em->createQuery($this->fncObtenerJoin($parametros)->getDQL());
//        
//        if( $parametros["filtro"] != NULL ) 
//        {
//            $query = $this->fncObtenerQueryFiltro($parametros, 1);
//        } 
//        
//        $query->setMaxResults($parametros["registrosPorPagina"]);
//        $query->setFirstResult( $offset );
//        
//        return $query;
//    }
//    
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
//                ->setParameter('numeroCuenta', '%'.$parametros["filtro"]["numeroCuenta"].'%')
//                ->setParameter('cuenta', '%'.$parametros["filtro"]["cuenta"].'%')
//                ->setParameter('clabeInterbancaria', '%'.$parametros["filtro"]["clabeInterbancaria"].'%')
//                ->setParameter('moneda', '%'.$parametros["filtro"]["moneda"].'%')
//                ->setParameter('tipoOperador', '%'.$parametros["filtro"]["tipoOperador"].'%')
//                ->setParameter('pais', '%'.$parametros["filtro"]["pais"].'%')
//                ->setParameter('banco', '%'.$parametros["filtro"]["banco"].'%')
//                ->setParameter('estado', '%'.$parametros["filtro"]["estado"].'%');
//        
//        return $query;
//    }
//    
//    private function fncObtenerQueryFiltroSelect($parametros, $case)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//        
//        $query = $em->createQueryBuilder();       
//        
//        if($case == 1) { $query->select('m, a, b, c'); }
//        else { $query->select('count(m.id)'); }
//        
//        $query->from('DefaultDb_Entities_Cuentas', 'm')
//        ->innerJoin('m.moneda', 'a')
//        ->innerJoin('m.pais', 'b')
//        ->innerJoin('m.banco', 'c')
//        ->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"])
//        ->where('m.numeroCuenta LIKE :numeroCuenta')
//        ->andWhere('m.cuenta LIKE :cuenta')
//        ->andWhere('m.clabeInterbancaria LIKE :clabeInterbancaria')
//        ->andWhere('a.moneda LIKE :moneda')
//        ->andWhere('m.tipoOperador LIKE :tipoOperador')
//        ->andWhere('b.nombre LIKE :pais')
//        ->andWhere('c.name LIKE :banco')
//        ->andWhere('m.estado LIKE :estado');
//        return $query;
//    }
// 
//    private function getTotalRows($parametros) 
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
//            ->from('DefaultDb_Entities_Cuentas', 'm');
//            $total_rows = $qb->getQuery()->getSingleScalarResult();
//        }
//        return $total_rows;
//    }
    
    private function mapResultToArray( $cuentas) {
        
        $resultCuentas = array();
        foreach ( $cuentas as $row ) 
        {
            $resultCuentas[] = array(
                'id' => $row->getId(),
                'numeroCuenta' => $row->getNumeroCuenta(),
                'cuenta'=> $row->getCuenta(),
                'clabeInterbancaria'=>$row->getClabeInterbancaria(), 
                'banco' =>  $row->getBanco()->getName(),
                'idBanco' => $row->getBanco()->getId(),
                'moneda' => $row->getMoneda()->getMoneda(),
                'idMoneda' => $row->getMoneda()->getId(),
                'pais' => $row->getPais()->getNombre(),
                'idPais' => $row->getPais()->getId(),
                'tipoOperador'=>$row->getTipoOperador(), 
                'estado'=> $row->getEstado(),
                'client'=>$row->getCliente() ,
                'tipoPago'=>($row->getTipoPago())?$row->getTipoPago()->getTipoPago():'',
                'idTipoPago'=>($row->getTipoPago())?$row->getTipoPago()->getId() : ''
            );
        }
        return $resultCuentas;
    }
    
    public function addCuenta ( $cuentaArray ) {
        
        $cuentaObject = $this->getCuenta( $cuentaArray["id"] );
        
        $entityManager = $this->getEntityManager();
        
        $banco = $entityManager->find('DefaultDb_Entities_Bancos', $cuentaArray["idBanco"] );
        $moneda = $entityManager->find('DefaultDb_Entities_TipoMonedas', $cuentaArray["idMoneda"] );
        $pais = $entityManager->find('DefaultDb_Entities_Paises', $cuentaArray["idPais"] );
        $tipoPago = $entityManager->getRepository('DefaultDb_Entities_TipoPagos')->find( $cuentaArray["tipoPago"] );
        
        $cuentaObject->setNumeroCuenta( $cuentaArray["numeroCuenta"] );
        $cuentaObject->setCuenta( $cuentaArray["cuenta"] );
        $cuentaObject->setClabeInterbancaria( $cuentaArray["clabeInterbancaria"] );
        $cuentaObject->setTipoOperador( $cuentaArray["idOperador"]['chrOperator'] );
        $cuentaObject->setEstado( $cuentaArray["estado"] );
        $cuentaObject->setCliente( $cuentaArray["cliente"] );
        $cuentaObject->setTipoPago( $tipoPago );
        
        $cuentaObject->setMoneda( $moneda );
        $cuentaObject->setBanco( $banco );
        $cuentaObject->setPais( $pais );
        
        $entityManager->persist( $cuentaObject );
        $entityManager->flush();
        
        return;
    }
    
    private function getCuenta( $id ) {
        
        $cuenta = null;
        if ($id == null){
            $cuenta = new DefaultDb_Entities_Cuentas();
        }else{
            $cuenta = $this->find($id);
        }
        return $cuenta;
    }
    
    public function deleteCuentas ($id) {
        $entityManager = $this->getEntityManager();
        
        if ( $id != null ) {
            $cuentas = $this->find( $id );
            $entityManager->remove($cuentas);
            $entityManager->flush();
        }
    }
    
     //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $cuentas = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $cuentas as $row ) 
        {
            $resultCuentas[] = array
            (
                'numeroCuenta' => $row->getNumeroCuenta(),
                'cuenta'=> $row->getCuenta(),
                'clabeInterbancaria'=>$row->getClabeInterbancaria(), 
                'moneda' => $row->getMoneda()->getMoneda(),
                'banco' =>  $row->getBanco()->getName(),
                'tipoOperador'=>$row->getTipoOperador(), 
                'pais' => $row->getPais()->getNombre(),
                'estado'=> $row->getEstado(),
                'tipoPago'=>$row->getTipoPago()->getTipoPago()
            );
        }
        
        return $resultCuentas;  
    }
}
