<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_PaisesRepository extends EntityRepository
{
    public function getPaisesListDQL($parametros){

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
        $query = $em->getRepository('DefaultDb_Entities_Paises')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR) 
        { $query->select('m'); }
        else 
        { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->orderBy("m.nombre", $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.nombre LIKE :nombre')
                  ->setParameter('nombre', '%'.$parametros["filtro"].'%');         
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
    
    private function mapResultToArray( $paises ) {
        
        $resultPaises = array();
        foreach ( $paises as $row ) 
        {
            $resultPaises[] = array(
                'id' => $row->getId(),
                'nombre' => $row->getNombre(),
                'estado' => ($row->getEstado() == TRUE )? 'Activo' : 'Inactivo',
                'client'=>$row->getClient()    
            );
        }
        return $resultPaises;
    }
    
    
    public function getPaises($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_Paises m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $paises = $query->getFirstResult();
        return $paises;
    }
    
    public function addPaises ($id,$nombre,$client,$estado)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $paises = new DefaultDb_Entities_Paises();
        }else{
            $paises=$this->find($id);
        }
        $paises->setNombre($nombre);
        $paises->setClient($client);
        $paises->setEstado($estado);
        
        
        $em->persist($paises);
        $em->flush();
        return;
    }
    
    public function deletePaises ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $paises = $this->find($id);
            $em->remove($paises);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $paises = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $paises as $row ) 
        {
            $resultTipoPagos[] = array
            (
                'pais' => $row->getNombre()
            );
        }
        
        return $resultTipoPagos;  
    }
}
