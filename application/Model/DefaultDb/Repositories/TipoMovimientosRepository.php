<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_TipoMovimientosRepository extends EntityRepository
{
  
    
    public function getTipoMovimientosListDQL($parametros){

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
        $query = $em->getRepository('DefaultDb_Entities_TipoMovimientos')
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
        $query->orderBy("m.tipoMovimiento", $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.tipoMovimiento LIKE :tipoMovimiento')
                  ->setParameter('tipoMovimiento', '%'.$parametros["filtro"].'%');         
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
    
    private function mapResultToArray( $tipoMovimientos ) {
        
        $resultTipoMovimientos = array();
        foreach ( $tipoMovimientos as $row ) 
        {
            $resultTipoMovimientos[] = array(
                'id' => $row->getId(),
                'tipoMovimiento' => $row->getTipoMovimiento(),
                'client'=>$row->getClient()
                    
            );
        }
        return $resultTipoMovimientos;
    }
    
    public function getTipoMovimientos($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_TipoMovimientos m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $tipoMovimientos = $query->getFirstResult();
        return $tipoMovimientos;
    }
    
    public function addTipoMovimientos ($id,$tipoMovimiento,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $tipoMovimientos = new DefaultDb_Entities_TipoMovimientos();
        }else{
            $tipoMovimientos=$this->find($id);
        }
        $tipoMovimientos->setTipoMovimiento($tipoMovimiento);
        $tipoMovimientos->setClient($client);
        
        $em->persist($tipoMovimientos);
        $em->flush();
        return;
    }
    
    public function deleteTipoMovimientos ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $tipoMovimientos = $this->find($id);
            $em->remove($tipoMovimientos);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetTipoMovimientosListExport($parametros)
    {
       //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $tipoMovimiento = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $tipoMovimiento as $row ) 
        {
            $resultTipoMovimiento[] = array
            (
                'tipoMovimiento' => $row->getTipoMovimiento()
            );
        }
        
        return $resultTipoMovimiento;  
    }
 
}
