<?php
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_TipoPagosRepository extends EntityRepository
{

    public function getTipoPagosListDQL($parametros){

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
        $query = $em->getRepository('DefaultDb_Entities_TipoPagos')
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
        $query->orderBy("m.tipoPago", $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('m.tipoPago LIKE :tipoPago')
                  ->setParameter('tipoPago', '%'.$parametros["filtro"].'%');         
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
                'tipoPago' => $row->getTipoPago(),
            );
        }
        return $resultBancos;
    }
    
    public function getTipoPagos($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_TipoPagos m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $tipoPagos = $query->getFirstResult();
        return $tipoPagos;
    }
    
    public function addTipoPagos ($id,$tipoPago,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $tipoPagos = new DefaultDb_Entities_TipoPagos();
        }else{
            $tipoPagos=$this->find($id);
        }
        $tipoPagos->setTipoPago($tipoPago);
        $tipoPagos->setClient($client);
        
        $em->persist($tipoPagos);
        $em->flush();
        return;
    }
    
    public function deleteTipoPagos ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $tipoPagos = $this->find($id);
            $em->remove($tipoPagos);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetTipoPagosListExport($parametros)
    {
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $tipoPagos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $tipoPagos as $row ) 
        {
            $resultTipoPagos[] = array
            (
                'tipoPago' => $row->getTipoPago()
            );
        }
        
        return $resultTipoPagos;  
    }
}