<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use com\masfletes\db\DBUtil;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultDb_Repositories_TransferenciaCreditosRepository extends EntityRepository {

    public function getTransferenciaCreditosListDQL($parametros){

        $total_rows = $this->getTotalRows($parametros);
        
        $total_pages = ceil($total_rows / $parametros["registrosPorPagina"]);
        
        //obtiene el array de datos, 0 = tipo - datos por pagina; 1 = case - select
        $resultTransferenciaCreditos = $this->mapResultToArray($this->fncObtenerQuery($parametros, 0, 1)->getResult());
      
        $result[0] = $resultTransferenciaCreditos;

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
        $query = $em->getRepository('DefaultDb_Entities_TransferenciaCreditos')
                ->createQueryBuilder('m');
        
        //verifica si es una sentencia select o un count
        if($case == $SELECCIONAR){ $query->select('m'); }
        else { $query->select('count(m.id)'); }
        
        //realiza el ordenamiento asc o desc
        $query->innerJoin('m.client', 'u')
              ->innerJoin('u.category', 'c');
 
        $query->orderBy($parametros["ordenarCampo"], $parametros["ordenarTipo"]); 
        $query->where('m.usuario = :usuario');
        
        //si existe el filtro de busqueda hace el where
        if( $parametros["filtro"] != NULL ) 
        {
            $query->andWhere('u.commercialName LIKE :cliente')
            ->andWhere('m.fecha LIKE :fecha')
            ->andWhere('m.creditos LIKE :creditos')
            ->andWhere('m.monto LIKE :monto')
            ->andWhere('c.name LIKE :categoria')
            ->andWhere('m.descripcion LIKE :comentarios')    
            ->setParameter('fecha', '%'.$this->fncGetFecha($parametros["filtro"]["fecha"]).'%')
            ->setParameter('cliente', '%'.$parametros["filtro"]["cliente"].'%')
            ->setParameter('creditos', '%'.$parametros["filtro"]["creditos"].'%')
            ->setParameter('monto', '%'.$parametros["filtro"]["monto"].'%')
            ->setParameter('categoria', '%'.$parametros["filtro"]["categoria"].'%')
            ->setParameter('comentarios', '%'.$parametros["filtro"]["comentarios"].'%');
        }  
        
        $query->setParameter('usuario', $this->fncGetUsuario()); 
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

    public function getTransferenciaCreditos($id) {
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_TransferenciaCreditos m WHERE m.id = :id ";

        $query = $em->createQuery($dql);
        $query->setParameter('id', $id);

        $transferenciaCreditos = $query->getFirstResult();
        return $transferenciaCreditos;
    }
    
    private function mapResultToArray( $transferenciaCreditos ) {
        
        $resultTransferenciaCreditos = array();

        foreach ( $transferenciaCreditos as $row ) {
            
            
            $cliente = $row->getClient();
            $usuario = $row->getUsuario();
            $monto = $row->getMonto();

            $categoria = $cliente ? $cliente->getCategory() : null;
            
            $commercialNameCliente = $cliente ? $cliente->getCommercialName(): '';
            
            $commercialNameUsuario = $usuario ? $usuario->getCommercialName(): '';
            
            $nombreCategoria = $categoria ? $categoria->getName() : '';

            $resultTransferenciaCreditos[ ] = array(
                'id' => $row->getId(), 
                'usuario' => $commercialNameUsuario,
                'fecha'=> $row->getFecha()->format("d-m-Y"),
                'creditos' => number_format( $row->getCreditos(), 3), 
                'monto'=> number_format( $monto, 3 ), 
                'client' => $commercialNameCliente, 
                'category' => $nombreCategoria,
                'descripcion' => $row->getDescripcion()
            );

        }
        
        return $resultTransferenciaCreditos;
    }
    
    
    public function addTransferenciaCreditos($transferenciaJSON) 
    {
        $em = $this->getEntityManager();

        $transferenciaCreditos = new DefaultDb_Entities_TransferenciaCreditos();
        
        $usuario = $em->getRepository('DefaultDb_Entities_User')->find( $transferenciaJSON["usuario"] );
        $cliente = $em->getRepository('DefaultDb_Entities_User')->find( $transferenciaJSON["client"] );
        
        $transferenciaCreditos->setUsuario($usuario);
        $transferenciaCreditos->setFecha($transferenciaJSON["fecha"]);
        $transferenciaCreditos->setCreditos($usuario->getCredito()-$transferenciaJSON["monto"]);
        $transferenciaCreditos->setMonto($transferenciaJSON["monto"]);
        $transferenciaCreditos->setDescripcion($transferenciaJSON["descripcion"]);
        $transferenciaCreditos->setClient($cliente);
        $transferenciaCreditos->setCategory($cliente->getCategory());
        
        $usuario->setCredito($usuario->getCredito()-$transferenciaJSON["monto"]); 
        $cliente->setCredito($cliente->getCredito()+$transferenciaJSON["monto"]); 

        $em->persist($usuario);
        $em->persist($cliente);
        $em->persist($transferenciaCreditos);
        
        $em->flush();
        
        $this->addAdministracionLog($transferenciaCreditos, $transferenciaJSON["usuario"]);
        $this->fncAgregarBalanceGeneral($transferenciaCreditos);
        
        return $transferenciaCreditos->getId();
    }
    
    //agrega los datos de la tranferencia a la tabla de administracionLogCliente
    public function addAdministracionLog($transferenciaCreditos, $usuario)
    {
        $em = $this->getEntityManager(); 
        
        $administracionJSON = array();
        
        $administracionJSON["idConcepto"] =  $transferenciaCreditos->getId();  
        $administracionJSON["tipoConcepto"] =  1; 
        $administracionJSON["cliente"] = $usuario;
        $administracionJSON["concepto"] = "Transferencia de Créditos";
        $administracionJSON["fecha"] = $transferenciaCreditos->getFecha();
        $administracionJSON["referencia"] = $transferenciaCreditos->getClient()->getCommercialName();
        $administracionJSON["banco"] = "Más Distribución";
        $administracionJSON["tipoPago"] = "Transferencia";
        $administracionJSON["monto"] = 0;
        $administracionJSON["creditos"] = $transferenciaCreditos->getMonto();
        $administracionJSON["transferencia"] = $transferenciaCreditos;
        $administracionJSON["compraCreditos"] = null;
       
        $administracionLogRepo = $em->getRepository('DefaultDb_Entities_AdministracionLogCliente');

        $administracionLogRepo->addAdministracionLogCliente($administracionJSON);
        
        
    }
     //agrega los datos de la tranferencia a la tabla de balance General
    public function fncAgregarBalanceGeneral($transferenciaCreditos)
    {
        $em = $this->getEntityManager(); 
        $balanceGeneralRepo = $em->getRepository('DefaultDb_Entities_BalanceGeneral');
        $balanceGeneralRepo->fncAgregarBalanceTransferencia($transferenciaCreditos);
    }

    public function deleteTransferenciaCreditos($id) {
        $em = $this->getEntityManager();
        if ($id == null) {
            return;
        } else {
            $transferenciaCreditos = $this->find($id);
            $em->remove($transferenciaCreditos);
            $em->flush();
            return;
        }
    }
    
    //retorna la lista para exportar
    public function fncGetListExport($parametros)
    {
        
        //obtiene el query count. 0 = tipo - todos los datos; 1 = case - select
        $transferenciaCreditos = $this->fncObtenerQuery($parametros, 1, 1)->getResult();
        
        foreach ( $transferenciaCreditos as $row ) 
        {
            $cliente = $row->getClient();
            $monto = $row->getMonto();

            $categoria = $cliente ? $cliente->getCategory() : null;
            $commercialNameCliente = $cliente ? $cliente->getCommercialName(): '';
            $nombreCategoria = $categoria ? $categoria->getName() : '';
            
            $resultTransferenciaCreditos[] = array
            ( 
                'cliente' => $commercialNameCliente, 
                'fecha'=> $row->getFecha()->format("d-m-Y"),
                'creditos' => number_format( $row->getCreditos(), 3), 
                'monto'=> number_format( $monto, 3 ), 
                'category' => $nombreCategoria,
                'descripcion' => $row->getDescripcion()          
            );
        }
        
        return $resultTransferenciaCreditos;  
    }

}
