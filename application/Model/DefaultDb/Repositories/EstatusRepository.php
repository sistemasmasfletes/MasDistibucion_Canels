<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_EstatusRepository extends EntityRepository
{

    public function getEstatusListDQL($parametros){

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
        $query = $em->getRepository('DefaultDb_Entities_Estatus')
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
        $query->orderBy("m.estatus", $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.estatus LIKE :estatus')
                  ->setParameter('estatus', '%'.$parametros["filtro"].'%');         
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
    

    
    private function mapResultToArray( $estatus ) {
        
        $resultEstatus = array();
        foreach ( $estatus as $row ) 
        {
            $resultEstatus[] = array(
                'id' => $row->getId(),
                'estatu' => $row->getEstatus(),
                'client'=>$row->getClient()    
            );
        }
        return $resultEstatus;
    }
    
    
    public function getEstatus($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_Estatus m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $estatus = $query->getFirstResult();
        return $estatus;
    }
    
    public function addEstatus ($id,$estatu,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $estatus = new DefaultDb_Entities_Estatus();
        }else{
            $estatus=$this->find($id);
        }
        $estatus->setEstatus($estatu);
        $estatus->setClient($client);
        
        $em->persist($estatus);
        $em->flush();
        return;
    }
    
    public function deleteEstatus ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $estatus = $this->find($id);
            $em->remove($estatus);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetEstatusListExport($parametros)
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $estatus = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $estatus as $row ) 
        {
            $resultEstatus[] = array
            (
                'estatu' => $row->getEstatus()
            );
        }
        
        return $resultEstatus;  
    }

}
