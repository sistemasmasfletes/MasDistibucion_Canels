<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_TipoMonedasRepository extends EntityRepository
{    
    /**
     * funcion que llena la tabla en el grid de la vista, es llamado desde el controlador 
     * y retorna un array 
     */
    public function getTipoMonedasListDQL($parametros){

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
    
    private function fncGetQueryBuilder($parametros, $case)
    {
        $SELECCIONAR = 1;
        //$CONTAR = 0;
        
        $em = $this->getEntityManager('DefaultDb');
        $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR) 
        { 
            $query->select('m'); 
        }
        else 
        { 
            $query->select('count(m.id)'); 
        }
        
        //realiza el ordenamiento asc o desc
//        $query->orderBy("m.moneda", $parametros["ordenarTipo"]);       
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]); 
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.moneda LIKE :moneda');
            if($parametros["filtro"]["codigoMoneda"]!= NULL)
            {
                $query->andWhere('m.currencyCode LIKE :currencyCode');
            }
            $query->setParameter('moneda', '%'.$parametros["filtro"]["moneda"].'%');
            if($parametros["filtro"]["codigoMoneda"]!= NULL)
            {
                $query->setParameter('currencyCode', '%'.$parametros["filtro"]["codigoMoneda"].'%');
            }
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
    
    
    
    /**
     * funcion que genera el query de los datos con paginacio (offset) 
     * y ordenamiento (Ordenar tipo - Asendente o Desendente)
     */
//    private function fncObtenerQuery($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//        
//        $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')
//                ->createQueryBuilder('m')
//                ->orderBy("m.moneda", $parametros["ordenarTipo"]);       
//        
//        if( $parametros["filtro"] != NULL ) 
//        {
//            $query->where('m.moneda LIKE :moneda')
//                  ->setParameter('moneda', '%'.$parametros["filtro"].'%');         
//        }    
//        
//        $query->setMaxResults($parametros["registrosPorPagina"]);
//        $query->setFirstResult($offset);
//        
//        return $query->getQuery();
//    }
    
    /**
     * funcion que genera el query de los datos con paginacio (offset) 
     * y ordenamiento (Ordenar tipo - Asendente o Desendente), 
     * asi como tambien hace el fitro de busqueda (filtro)
     */
//    private function fncObtenerQueryFiltro($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $offset = $offset = ( $parametros["pagina"] - 1 ) * $parametros["registrosPorPagina"];
//        
//        $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')
//                ->createQueryBuilder('m')
//                ->orderBy("m.moneda", $parametros["ordenarTipo"])
//                ->where('m.moneda LIKE :moneda')
//                ->setParameter('moneda', '%'.$parametros["filtro"].'%')
//                ->setMaxResults($parametros["registrosPorPagina"])
//                ->setFirstResult( $offset )
//                ->getQuery();
//
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
//            $qb->select('count(m.id)')
//           ->from('DefaultDb_Entities_TipoMonedas', 'm')
//           ->where('m.moneda LIKE :moneda')
//           ->setParameter('moneda', '%'.$parametros["filtro"].'%');
//        } 
//        else
//        {
//            $qb->select('count(m.id)')
//           ->from('DefaultDb_Entities_TipoMonedas', 'm');
//        }
//        
//        $total_rows = $qb->getQuery()->getSingleScalarResult();
//        return $total_rows;
//    }
    
    private function mapResultToArray( $tipoMovimientos ) {
        
        $resultTipoMovimientos = array();
        foreach ( $tipoMovimientos as $row ) 
        {
            $resultTipoMovimientos[] = array(
                'id' => $row->getId(),
                'moneda' => $row->getMoneda(),
                'client'=>$row->getClient(),
                'codigoMoneda'=> ($row->getCurrencyCode()) ? $row->getCurrencyCode() : "---"
                    
            );
        }
        return $resultTipoMovimientos;
    }
    
    
    public function getTipoMonedas($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_TipoMonedas m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $tipoMonedas = $query->getFirstResult();
        return $tipoMonedas;
    }
    
    public function addTipoMonedas ($id,$moneda,$client,$codigoMoneda)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $tipoMonedas = new DefaultDb_Entities_TipoMonedas();
        }else{
            $tipoMonedas=$this->find($id);
        }
        $tipoMonedas->setMoneda($moneda);
        $tipoMonedas->setClient($client);
        $tipoMonedas->setCurrencyCode($codigoMoneda);
        
        $em->persist($tipoMonedas);
        $em->flush();
        return;
    }
    
    public function deleteTipoMonedas ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $tipoMonedas = $this->find($id);
            $em->remove($tipoMonedas);
            $em->flush();
            return;
        }
    }
    //retorna la lista para exportar
    public function fncGetTipoMonedasListExport($parametros)
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $tipoMonedas = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $tipoMonedas as $row ) 
        {
            $resultTipoMonedas[] = array
            (
                'moneda' => $row->getMoneda(),
                'codigoMoneda'=> ($row->getCurrencyCode()) ? $row->getCurrencyCode() : "---"
            );
        }
        
        return $resultTipoMonedas;  
    }
    /**
     * funcion para obtener el tipo de pagos de la base
     */
//    public function fncGetTipoMonedasListExport($parametros)
//    {
//        $tipoMonedas = $this->fncGetDatosExportar($parametros);
//        
//        foreach ( $tipoMonedas as $row ) 
//        {
//            $resultTipoMonedas[] = array
//            (
//                'moneda' => $row->getMoneda()
//            );
//        }
//        
//        return $resultTipoMonedas;  
//    }
    
    /**
     * funcion que crea el result de los datos, se crea otro query porque si se utiliza la funcion
     * getTipoMonedasListDQL obtiene los registros por pagina y este query obtiene todos los registros 
     * sin paginacion 
     */
//    private function fncGetDatosExportar($parametros)
//    {
//        $em = $this->getEntityManager('DefaultDb');
//        
//        $query = $em->getRepository('DefaultDb_Entities_TipoMonedas')
//                    ->createQueryBuilder('m')
//                    ->orderBy("m.moneda", $parametros["ordenarTipo"]);
//        
//        if( $parametros["filtro"] != NULL ) 
//        {
//            $query->where('m.moneda LIKE :moneda')
//                  ->setParameter('moneda', '%'.$parametros["filtro"].'%');           
//        } 
//        
//        return $query->getQuery()->getResult();
//    }
}
