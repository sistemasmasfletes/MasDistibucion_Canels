<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class DefaultDb_Repositories_AprobacionCreditosRepository extends EntityRepository {
    
    public function getAprobacionCreditosListDQL($page,$rowsPerPage,$sortField,$sortDir,$id,$usuario,$tipoPago,$montoCompra,$creditos,$fecha,$nombreImg,$path,$moneda,$name,$referencia,$cuenta,$estado){
        $em = $this->getEntityManager();
        $offset = ($page-1)*$rowsPerPage;
        $dql = $em->createQueryBuilder();
        $test='m.name';
        if($sortField == null){

        }else{
            $test = 'm.'+$sortField;
        }
        
        $dql->select('m')
            ->from('DefaultDb_Entities_CompraCreditos', 'm');

        $query=$em->createQuery($dql);

        $aprobacionCreditos = $query->getResult();
        $paginator = new Paginator($query, $fetchJoinCollection = false);
        $totalRecords = count($aprobacionCreditos);
        
        $x=0;
        foreach ($aprobacionCreditos as $cc){
            $resultadoAprobacionCreditos[$x]=array('id' => $cc->getId(), 'usuario' => $cc->getUsuario(),'fecha' => $cc->getFecha(), 'tipoPago' => $cc->getTipoPago(), 'montoCompra' => $cc->getMontoCompra(), 'moneda' => $cc->getMoneda(),'name' => $cc->getName(), 'cuenta' => $cc->getCuenta(), 'creditos' => $cc->getCreditos(),'referencia' => $cc->getReferencia(),   'nombreImg' => $cc->getNombreImg(), 'path' => $cc->getPath(),  'estado' => $cc->getEstado(),'client'=>$cc->getClient());
            $x++;
        }  
        $result[0]=$resultadoAprobacionCreditos;
        $result[1][0]=array('records' => $x, 'page' =>$page, 'totalpages' => null);
        return $result;
    }
    
      public function getAprobacionCreditos($id){
        $em = $this->getEntityManager();

        $dql = "SELECT m FROM DefaultDb_Entities_CompraCreditos m WHERE m.id = :id ";

        $query=$em->createQuery($dql);
        $query->setParameter('id',$id);

        $aproCreditos = $query->getFirstResult();
        return $aproCreditos;
    }
    
     public function addAprobacionCreditos ($id,$usuario,$tipoPago,$montoCompra,$creditos,$fecha,$nombreImg,$path,$moneda,$name,$referencia,$cuenta,$estado,$client)
    {
        $em = $this->getEntityManager();
        if ($id == null){
            $aprobacionCreditos = new DefaultDb_Entities_CompraCreditos();
        }else{
            $aprobacionCreditos=$this->find($id);
        }   
        $aprobacionCreditos->setUsuario($usuario);
        $aprobacionCreditos->setTipoPago($tipoPago);
        $aprobacionCreditos->setMontoCompra($montoCompra);
        $aprobacionCreditos->setCreditos($creditos);
        $aprobacionCreditos->setFecha($fecha);
        $aprobacionCreditos->setNombreImg($nombreImg);
        $aprobacionCreditos->setPath($path);
        $aprobacionCreditos->setMoneda($moneda);
        $aprobacionCreditos->setName($name);
        $aprobacionCreditos->setReferencia($referencia);
        $aprobacionCreditos->setCuenta($cuenta);
        $aprobacionCreditos->setEstado($estado);
        $aprobacionCreditos->setClient($client);
        
        $em->persist($aprobacionCreditos);
        
        $em->flush();
        
        return;
        
        
    }
    
     public function deleteAprobacionCreditos ($id)
    {
        $em = $this->getEntityManager();
        if($id == null)
        {
            return;
        }else
        {
            $aprobacionCreditos = $this->find($id);
            $em->remove($aprobacionCreditos);
            $em->flush();
            return;
        }
    }
}

