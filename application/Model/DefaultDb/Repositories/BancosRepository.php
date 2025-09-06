<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_BancosRepository extends EntityRepository
{
    
    public function getBancosListDQL($parametros)
    {
        
        $total_rows = $this->getTotalRows($parametros);
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultBancos = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());

        $result[0] = $resultBancos;

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
        $query = $em->getRepository('DefaultDb_Entities_Bancos')
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
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);  
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.name LIKE :name')
                  ->andWhere('m.estado LIKE :estado')
                  ->setParameter('name', '%'.$parametros["filtro"]["banco"].'%')
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
    
    private function mapResultToArray( $bancos ) {
        
        $resultBancos = array();
        foreach ( $bancos as $row ) 
        {
            $resultBancos[] = array(
                'id' => $row->getId(),
                'name' => $row->getName(),
                'estado' => $row->getEstado()
            );
        }
        return $resultBancos;
    }
    
    public function getBancos($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_Bancos m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $bancos = $query->getFirstResult();
        return $bancos;
    }
    
    public function addBancos ($id,$name,$estado,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $bancos = new DefaultDb_Entities_Bancos();
        }else{
            $bancos=$this->find($id);
        }
        $bancos->setName($name);
        $bancos->setEstado($estado);
        $bancos->setClient($client);
        
        $em->persist($bancos);
        $em->flush();
        return;
    }
    
    public function deleteBancos ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $bancos = $this->find($id);
            $em->remove($bancos);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function getBancosListExport($parametros)
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $bancos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $bancos as $row ) 
        {
            $resultBancos[] = array
            (
                'name' => $row->getName(),
                'estado' => $row->getEstado()
            );
        }
        
        return $resultBancos;  
    }

}