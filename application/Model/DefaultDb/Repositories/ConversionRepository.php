<?php
use Doctrine\ORM\EntityRepository;

class DefaultDb_Repositories_ConversionRepository extends EntityRepository
{
    
    public function getConversionListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);

        $resultConversion = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());

        $result[0] = $resultConversion;

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
        $query = $em->getRepository('DefaultDb_Entities_Conversion')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.moneda', 'a');
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]);       
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->where('a.moneda LIKE :moneda')->andWhere('m.compra LIKE :compra')
                ->andWhere('m.venta LIKE :venta') ->andWhere('m.fecha LIKE :fecha')
                ->andWhere('m.creditos LIKE :creditos')
                ->setParameter('moneda', '%'.$parametros["filtro"]["moneda"].'%')
                ->setParameter('compra', '%'.$parametros["filtro"]["compra"].'%')
                ->setParameter('venta', '%'.$parametros["filtro"]["venta"].'%')
                ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
                ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%');   
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

    private function mapResultToArray( $conversion ) {
        
        $resultConversion = array();
        foreach ( $conversion as $row ) 
        {
            $resultConversion[] = array(
                'id' => $row->getId(),
                'moneda' => $row->getMoneda()->getMoneda(),
                'compra' => $row->getCompra(),
                'venta' => $row->getVenta(),
                'fecha' => $row->getFecha()->format("d-m-Y"),
                'creditos' => $row->getCreditos(),
                'idMoneda' => $row->getMoneda()->getId()
            );

        }
        return $resultConversion;
    }
    
    
    public function getConversion($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_Conversion m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $conversion = $query->getFirstResult();
        return $conversion;
    }
    
    public function addConversion ($id,$idMoneda,$compra,$venta,$fecha,$creditos,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $conversion = new DefaultDb_Entities_Conversion();
        }else{
            $conversion=$this->find($id);
        }
        
        $moneda = $em->find('DefaultDb_Entities_TipoMonedas', $idMoneda);
        
        $conversion->setMoneda( $moneda );
        $conversion->setCompra($compra);
        $conversion->setVenta($venta);
        $conversion->setFecha(new DateTime($fecha));
        $conversion->setCreditos($creditos);
        $conversion->setClient($client);
        
        
        $em->persist($conversion);
        $em->flush();
        return;
    }
    
    public function deleteConversion ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $conversion = $this->find($id);
            $em->remove($conversion);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetConversionListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $tipoPagos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $tipoPagos as $row ) 
        {
            $resultTipoPagos[] = array
            (
                'moneda' => $row->getMoneda()->getMoneda(),
                'compra' => $row->getCompra(),
                'venta' => $row->getVenta(),
                'fecha' => $row->getFecha()->format("d-m-Y"),
                'creditos' => $row->getCreditos()
            );
        }
        
        return $resultTipoPagos;  
    }
}


